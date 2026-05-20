<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollDetail;
use App\Models\PayrollAllowance;
use App\Models\PayrollLog;
use App\Models\Gaji;
use App\Models\TunjanganKaryawan;
use App\Models\KpaTunjanganRule;
use App\Models\Absensi;
use App\Services\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $allowedRoles = ['finance', 'admin', 'owner', 'general_manager'];
        $this->middleware(function ($request, $next) use ($allowedRoles) {
            $user = Auth::user();
            if (!in_array($user->role, $allowedRoles)) {
                abort(403, 'Tidak memiliki akses ke menu penggajian');
            }
            return $next($request);
        });
    }

    // ============================================================
    // INDEX & CREATE
    // ============================================================

    public function index()
    {
        $periods = PayrollPeriod::orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(15);
        
        return view('finance.payroll.index', compact('periods'));
    }

    public function create()
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        
        return view('finance.payroll.create', compact('months', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030',
        ]);
        
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        $existing = PayrollPeriod::where('bulan', $bulan)->where('tahun', $tahun)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Periode sudah ada!')->withInput();
        }
        
        $tanggalMulai = Carbon::create($tahun, $bulan, 1);
        $tanggalSelesai = $tanggalMulai->copy()->endOfMonth();
        
        DB::beginTransaction();
        try {
            $period = PayrollPeriod::create([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => 'draft',
                'dibuat_oleh' => Auth::id(),
            ]);
            
            $calculator = new PayrollCalculator($period);
            $calculator->prosesGajiSemuaKaryawan();
            
            $period->update(['status' => 'processed']);
            
            PayrollLog::create([
                'payroll_period_id' => $period->id,
                'user_id' => Auth::id(),
                'aksi' => 'created',
                'keterangan' => 'Periode baru dibuat dan diproses'
            ]);
            
            DB::commit();
            
            return redirect()->route('finance.payroll.show', $period->id)
                ->with('success', 'Penggajian berhasil diproses!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // AMBIL DATA DARI HR
    // ============================================================

    public function daftarDariHR(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $dataGaji = Gaji::with('karyawan.divisi')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'menunggu_finance')
            ->get();

        $totalGaji = $dataGaji->sum('total_gaji');

        return view('finance.payroll.dari_hr', compact('dataGaji', 'bulan', 'tahun', 'totalGaji'));
    }

    public function ambilDariHR(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $period = PayrollPeriod::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if (!$period) {
            $tanggalMulai = Carbon::create($tahun, $bulan, 1);
            $tanggalSelesai = $tanggalMulai->copy()->endOfMonth();
            
            $period = PayrollPeriod::create([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => 'draft',
                'dibuat_oleh' => Auth::id(),
            ]);
        }

        $dataGaji = Gaji::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'menunggu_finance')
            ->get();

        if ($dataGaji->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data gaji dari HR untuk periode ini.');
        }

        DB::beginTransaction();
        try {
            $selectedIds = $request->selected ?? $dataGaji->pluck('id')->toArray();
            $count = 0;
            
            foreach ($dataGaji as $gaji) {
                if (!in_array($gaji->id, $selectedIds)) continue;
                
                $exists = PayrollDetail::where('payroll_period_id', $period->id)
                    ->where('user_id', $gaji->karyawan_id)
                    ->exists();

                if (!$exists) {
                    $tunjanganLain = TunjanganKaryawan::where('karyawan_id', $gaji->karyawan_id)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->sum('nominal');

                    PayrollDetail::create([
                        'payroll_period_id' => $period->id,
                        'user_id' => $gaji->karyawan_id,
                        'gaji_pokok' => $gaji->gaji_pokok,
                        'tunjangan_tetap' => $gaji->tunjangan_tetap,
                        'tunjangan_kinerja' => $gaji->tunjangan_kinerja,
                        'bonus' => $gaji->bonus,
                        'tunjangan_lain' => $tunjanganLain,
                        'potongan_bpjs' => $gaji->potongan_bpjs,
                        'potongan_lain' => $gaji->potongan_lain,
                        'potongan_tidak_hadir' => 0,
                        'total_gaji_bersih' => $gaji->total_gaji + $tunjanganLain,
                        'keterangan' => 'Data dari HR'
                    ]);
                    $count++;
                }
            }

            $period->update(['status' => 'processed']);

            PayrollLog::create([
                'payroll_period_id' => $period->id,
                'user_id' => Auth::id(),
                'aksi' => 'import_from_hr',
                'keterangan' => "Mengambil {$count} data gaji dari HR"
            ]);

            DB::commit();
            return redirect()->route('finance.payroll.show', $period->id)
                ->with('success', "Berhasil mengambil {$count} data gaji dari HR.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengambil data: ' . $e->getMessage());
        }
    }

    // ============================================================
    // HITUNG POTONGAN (HANYA SATU METHOD, TIDAK BOLEH DUPLIKAT)
    // ============================================================

    /**
     * Hitung potongan untuk semua karyawan dalam satu periode
     */
    public function hitungSemuaPotongan(Request $request, $id)
{
    $period = PayrollPeriod::findOrFail($id);
    
    foreach ($period->details as $detail) {
        $absensi = Absensi::where('user_id', $detail->user_id)
            ->whereMonth('tanggal', $period->bulan)
            ->whereYear('tanggal', $period->tahun)
            ->get();
        
        $gajiPerHari = $detail->gaji_pokok / 25;
        $totalPotongan = 0;
        
        foreach ($absensi as $absen) {
            if ($absen->jenis_ketidakhadiran === 'alpha') {
                $totalPotongan += $gajiPerHari;
            } 
            elseif ($absen->jenis_ketidakhadiran === 'izin') {
                $totalPotongan += $gajiPerHari;
            }
            elseif ($absen->jenis_ketidakhadiran === 'sakit') {
                // 🔥 CEK APAKAH ADA SURAT DOKTER YANG SUDAH DIAPPROVE
                if (!$absen->ada_surat_dokter) {
                    $totalPotongan += $gajiPerHari;  // DIPOTONG
                }
                // Jika ada surat dokter (ada_surat_dokter = true), TIDAK DIPOTONG
            }
            elseif ($absen->jenis_ketidakhadiran === 'cuti' && ($absen->cuti_melebihi_jatah ?? false)) {
                $totalPotongan += $gajiPerHari;
            }
        }
        
        $detail->potongan_tidak_hadir = $totalPotongan;
        $detail->total_gaji_bersih = max(0, 
            $detail->gaji_pokok 
            + ($detail->tunjangan_tetap ?? 0) 
            + ($detail->tunjangan_kinerja ?? 0) 
            + ($detail->bonus ?? 0) 
            + ($detail->tunjangan_lain ?? 0) 
            - $totalPotongan 
            - ($detail->potongan_bpjs ?? 0) 
            - ($detail->potongan_lain ?? 0)
        );
        $detail->save();
    }
    
    return redirect()->back()->with('success', 'Potongan gaji berhasil dihitung.');
}
    // ============================================================
    // SHOW, SLIP, APPROVE, PAID
    // ============================================================

    public function show($id)
    {
        $period = PayrollPeriod::with(['details', 'details.user'])->findOrFail($id);
        
        $totalGaji = $period->details->sum('total_gaji_bersih');
        $totalTunjangan = $period->details->sum('tunjangan_tetap') + $period->details->sum('tunjangan_kinerja') + $period->details->sum('tunjangan_lain');
        $totalPotongan = $period->details->sum('potongan_tidak_hadir') + $period->details->sum('potongan_bpjs') + $period->details->sum('potongan_lain');
        
        $statistik = [
            'total_karyawan' => $period->details->count(),
            'total_gaji' => $totalGaji,
            'total_tunjangan' => $totalTunjangan,
            'total_potongan' => $totalPotongan,
        ];
        
        return view('finance.payroll.show', compact('period', 'statistik'));
    }

    public function slip($periodId, $detailId)
    {
        $period = PayrollPeriod::findOrFail($periodId);
        $detail = PayrollDetail::with(['user'])->findOrFail($detailId);
        
        if ($detail->payroll_period_id != $periodId) {
            abort(404);
        }
        
        return view('finance.payroll.slip', compact('period', 'detail'));
    }

    public function approve($id)
    {
        $period = PayrollPeriod::findOrFail($id);
        
        if ($period->status != 'processed') {
            return redirect()->back()->with('error', 'Periode belum diproses');
        }
        
        $period->update([
            'status' => 'approved',
            'disetujui_oleh' => Auth::id(),
            'disetujui_at' => now(),
        ]);
        
        PayrollLog::create([
            'payroll_period_id' => $period->id,
            'user_id' => Auth::id(),
            'aksi' => 'approved',
            'keterangan' => 'Penggajian disetujui'
        ]);
        
        return redirect()->back()->with('success', 'Penggajian disetujui');
    }

    public function markAsPaid($id, Request $request)
    {
        $period = PayrollPeriod::findOrFail($id);
        
        $request->validate([
            'tanggal_pembayaran' => 'required|date',
        ]);
        
        $period->update([
            'status' => 'paid',
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'dibayar_at' => now(),
        ]);
        
        PayrollLog::create([
            'payroll_period_id' => $period->id,
            'user_id' => Auth::id(),
            'aksi' => 'paid',
            'keterangan' => 'Gaji dibayarkan tanggal ' . $request->tanggal_pembayaran
        ]);
        
        return redirect()->back()->with('success', 'Status pembayaran diperbarui');
    }

    public function settings()
    {
        $allowances = PayrollAllowance::all();
        $kpaRules = KpaTunjanganRule::all();
        
        return view('finance.payroll.settings', compact('allowances', 'kpaRules'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'allowances' => 'array',
            'allowances.*.id' => 'exists:payroll_allowances,id',
            'allowances.*.nilai' => 'numeric|min:0',
            'kpa_rules' => 'array',
            'kpa_rules.*.id' => 'exists:kpa_tunjangan_rules,id',
            'kpa_rules.*.persentase' => 'numeric|min:0|max:100',
        ]);
        
        if ($request->has('allowances')) {
            foreach ($request->allowances as $allowance) {
                PayrollAllowance::where('id', $allowance['id'])->update([
                    'nilai' => $allowance['nilai'],
                    'is_active' => $allowance['is_active'] ?? false
                ]);
            }
        }
        
        if ($request->has('kpa_rules')) {
            foreach ($request->kpa_rules as $rule) {
                KpaTunjanganRule::where('id', $rule['id'])->update([
                    'persentase' => $rule['persentase'],
                    'is_active' => $rule['is_active'] ?? false
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }
}