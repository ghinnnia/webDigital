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
use App\Mail\SlipGajiMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function dariHr(Request $request)
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

    /**
     * Menampilkan daftar data gaji dari HR (GET)
     */
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

    /**
     * Mengambil data dari HR dan menyimpan ke payroll (POST)
     */
    public function ambilDariHR(Request $request)
    {
        try {
            // 1. Ambil data penggajian dari HR yang berstatus menunggu_finance
            $dataHrTerbaru = Gaji::where('status', 'menunggu_finance')->get();

            if ($dataHrTerbaru->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data penggajian terbaru dari HR untuk diambil.');
            }

            $successCount = 0;
            $failedCount = 0;
            $failedList = [];

            foreach ($dataHrTerbaru as $gaji) {
                // Validasi: pastikan karyawan_id ada
                if (!$gaji->karyawan_id) {
                    $failedCount++;
                    $failedList[] = 'Data gaji ID ' . $gaji->id . ' tidak memiliki karyawan_id';
                    continue;
                }

                // Validasi: pastikan user exists
                $user = \App\Models\User::find($gaji->karyawan_id);
                if (!$user) {
                    $failedCount++;
                    $failedList[] = 'User dengan ID ' . $gaji->karyawan_id . ' tidak ditemukan';
                    continue;
                }

                // Ambil nama divisi
                $namaDivisi = '-';
                if ($user->divisi) {
                    $namaDivisi = $user->divisi->divisi ?? $user->divisi->nama_divisi ?? '-';
                }

                // Cari atau buat periode
                $periode = PayrollPeriod::firstOrCreate(
                    [
                        'bulan' => $gaji->bulan,
                        'tahun' => $gaji->tahun,
                    ],
                    [
                        'nama_periode' => $this->getNamaBulan($gaji->bulan) . ' ' . $gaji->tahun,
                        'tanggal_mulai' => Carbon::create($gaji->tahun, $gaji->bulan, 1)->format('Y-m-d'),
                        'tanggal_selesai' => Carbon::create($gaji->tahun, $gaji->bulan, 1)->endOfMonth()->format('Y-m-d'),
                        'status' => 'processed',
                        'dibuat_oleh' => Auth::id(),
                    ]
                );

                // Hitung nilai dengan abs() untuk menghindari nilai negatif
                $gajiPokok = abs($gaji->gaji_pokok ?? 0);
                $tunjanganTetap = abs($gaji->tunjangan_tetap ?? 0);
                $tunjanganKinerja = abs($gaji->tunjangan_kinerja ?? 0);
                $bonus = abs($gaji->bonus ?? 0);
                $totalBersih = $gajiPokok + $tunjanganTetap + $tunjanganKinerja + $bonus;

                // INSERT KE PAYROLL_DETAILS sesuai struktur tabel
                $detail = PayrollDetail::updateOrCreate(
                    [
                        'payroll_period_id' => $periode->id,
                        'user_id' => $gaji->karyawan_id,
                        'karyawan_id' => $gaji->karyawan_id,
                    ],
                    [
                        'nama_karyawan' => $user->name,
                        'divisi' => $namaDivisi,
                        'gaji_per_bulan' => $gajiPokok,
                        'total_hari_kerja' => 25,
                        'hari_hadir' => 0,
                        'hari_alfa' => 0,
                        'hari_sakit_tanpa_surat' => 0,
                        'hari_izin_tanpa_ket' => 0,
                        'hari_cuti' => 0,
                        'hari_sakit_dengan_surat' => 0,
                        'jam_lembur' => 0,
                        'gaji_pokok' => $gajiPokok,
                        'potongan_tidak_hadir' => 0,
                        'potongan_bpjs' => 0,
                        'tunjangan_tetap' => $tunjanganTetap,
                        'nilai_kpa' => 0,
                        'persentase_tunjangan_kinerja' => 0,
                        'tunjangan_kinerja' => $tunjanganKinerja,
                        'tarif_lembur_per_jam' => 0,
                        'upah_lembur' => 0,
                        'total_gaji_bersih' => $totalBersih,
                        'tunjangan_lain' => 0,
                        'status' => 'draft',
                    ]
                );

                if ($detail) {
                    $successCount++;
                    // Update status di tabel HR
                    $gaji->update(['status' => 'terbaca_finance']);
                }
            }

            $message = "Berhasil menyinkronkan {$successCount} data dari HR!";
            if ($failedCount > 0) {
                $message .= " Gagal: {$failedCount} data. " . implode(', ', $failedList);
            }

            return redirect()->route('finance.payroll.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error ambil data HR: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getNamaBulan($bulan)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$bulan] ?? $bulan;
    }

    /**
     * HITUNG POTONGAN DAN LEMBUR
     */
    public function hitungPotongan($id)
    {
        $period = PayrollPeriod::with(['details.user'])->findOrFail($id);
        $potonganBPJS = 100000; 
        
        $defaultOvertime = \App\Models\OvertimeSetting::whereNull('division_id')->first();
        $defaultRate = $defaultOvertime ? $defaultOvertime->default_rate : 30000;
        
        DB::beginTransaction();
        try {
            foreach ($period->details as $detail) {
                if (!$detail->user) {
                    continue;
                }
                
                $karyawan = $detail->user;
                $userId = $detail->user_id;
                
                // ============================================
                // HITUNG LEMBUR
                // ============================================
                $lemburs = \App\Models\Lembur::where('user_id', $userId)
                    ->whereIn('status', ['approved'])
                    ->whereMonth('tanggal_lembur', $period->bulan)
                    ->whereYear('tanggal_lembur', $period->tahun)
                    ->where('durasi', '>', 0)
                    ->get();
                
                $totalJamLembur = $lemburs->sum('durasi');
                
                $tarifLembur = $defaultRate;
                if ($karyawan && $karyawan->divisi_id) {
                    $divisionOvertime = \App\Models\OvertimeSetting::where('division_id', $karyawan->divisi_id)->first();
                    if ($divisionOvertime) {
                        $tarifLembur = $divisionOvertime->default_rate;
                    }
                }
                
                $firstLembur = $lemburs->first();
                if ($firstLembur && $firstLembur->custom_rate) {
                    $tarifLembur = $firstLembur->custom_rate;
                }
                
                $upahLembur = $totalJamLembur * $tarifLembur;
                
                // ============================================
                // HITUNG POTONGAN KEHADIRAN
                // ============================================
                $absensi = Absensi::where('user_id', $userId)
                    ->whereMonth('tanggal', $period->bulan)
                    ->whereYear('tanggal', $period->tahun)
                    ->get();
                
                $gajiPerHari = $detail->gaji_pokok / 25;
                $totalPotonganHadir = 0;
                $hariAlfa = 0;
                $hariIzin = 0;
                $hariSakitTanpaSurat = 0;
                
                foreach ($absensi as $absen) {
                    if ($absen->jenis_ketidakhadiran === 'alpha') {
                        $totalPotonganHadir += $gajiPerHari;
                        $hariAlfa++;
                    } elseif ($absen->jenis_ketidakhadiran === 'izin') {
                        $totalPotonganHadir += $gajiPerHari;
                        $hariIzin++;
                    } elseif ($absen->jenis_ketidakhadiran === 'sakit') {
                        if (!$absen->ada_surat_dokter || $absen->status_surat != 'approved') {
                            $totalPotonganHadir += $gajiPerHari;
                            $hariSakitTanpaSurat++;
                        }
                    }
                }
                
                $telatLebih12Siang = Absensi::where('user_id', $userId)
                    ->whereMonth('tanggal', $period->bulan)
                    ->whereYear('tanggal', $period->tahun)
                    ->whereNotNull('jam_masuk')
                    ->where('jam_masuk', '>', '12:00:00')
                    ->count();
                
                $totalPotonganHadir += $telatLebih12Siang * $gajiPerHari;
                
                // ============================================
                // UPDATE DETAIL GAJI - SESUAI STRUKTUR TABEL
                // ============================================
                $detail->gaji_per_bulan = $detail->gaji_pokok;
                $detail->total_hari_kerja = 25;
                $detail->hari_hadir = max(0, 25 - ($hariAlfa + $hariIzin + $hariSakitTanpaSurat + $telatLebih12Siang));
                $detail->hari_alfa = $hariAlfa;
                $detail->hari_sakit_tanpa_surat = $hariSakitTanpaSurat;
                $detail->hari_izin_tanpa_ket = $hariIzin;
                $detail->jam_lembur = $totalJamLembur;
                $detail->potongan_tidak_hadir = min($totalPotonganHadir, $detail->gaji_pokok);
                $detail->potongan_bpjs = $potonganBPJS;
                $detail->tarif_lembur_per_jam = $tarifLembur;
                $detail->upah_lembur = $upahLembur;
                $detail->total_gaji_bersih = max(0, 
                    $detail->gaji_pokok 
                    + ($detail->tunjangan_tetap ?? 0) 
                    + ($detail->tunjangan_kinerja ?? 0) 
                    + ($detail->tunjangan_lain ?? 0)
                    + $upahLembur
                    - $detail->potongan_tidak_hadir 
                    - $detail->potongan_bpjs 
                );
                $detail->save();
                
                if ($lemburs->count() > 0) {
                    \App\Models\Lembur::whereIn('id', $lemburs->pluck('id'))->update(['is_paid' => true]);
                }
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Potongan dan upah lembur berhasil dihitung!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hitung potongan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghitung: ' . $e->getMessage());
        }
    }

    public function sendNotificationSlip($periodId, $detailId)
    {
        try {
            Log::info('=== MULAI KIRIM NOTIFIKASI ===');
            
            $period = PayrollPeriod::findOrFail($periodId);
            $detail = PayrollDetail::with('user')->findOrFail($detailId);
            
            if (!$detail->user || !$detail->user->email) {
                return response()->json(['success' => false, 'message' => 'Email karyawan tidak ditemukan'], 400);
            }
            
            $pdf = Pdf::loadView('finance.payroll.slip_pdf', compact('period', 'detail'));
            $pdfContent = $pdf->output();
            
            $fileName = 'slip_gaji_' . $detail->user->id . '_' . $period->bulan . '_' . $period->tahun . '.pdf';
            $filePath = 'slip_gaji/' . $fileName;
            
            if (!Storage::exists('public/slip_gaji')) {
                Storage::makeDirectory('public/slip_gaji');
            }
            
            Storage::put('public/' . $filePath, $pdfContent);
            
            $notification = \App\Models\Notification::create([
                'user_id' => $detail->user->id,
                'title' => '📄 Slip Gaji Tersedia',
                'message' => "Slip gaji untuk periode {$period->nama_periode} sudah tersedia. Klik untuk melihat detail.",
                'type' => 'payroll',
                'link' => route('karyawan.slip-gaji.show', $detail->id),
                'is_read' => false,
            ]);
            
            Log::info('Notifikasi berhasil dibuat, ID: ' . $notification->id);
            
            return response()->json(['success' => true, 'message' => "Slip gaji telah dikirim ke {$detail->user->name}"]);
            
        } catch (\Exception $e) {
            Log::error('Gagal kirim notifikasi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengirim: ' . $e->getMessage()], 500);
        }
    }

    public function sendNotificationMass(Request $request, $periodId)
    {
        try {
            $ids = $request->ids;
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data dipilih'], 400);
            }
            
            $period = PayrollPeriod::findOrFail($periodId);
            $details = PayrollDetail::with('user')->whereIn('id', $ids)->get();
            
            $successCount = 0;
            $failedList = [];
            
            foreach ($details as $detail) {
                if (!$detail->user) {
                    $failedList[] = 'User tidak ditemukan';
                    continue;
                }
                
                try {
                    $pdf = Pdf::loadView('finance.payroll.slip_pdf', compact('period', 'detail'));
                    $pdfContent = $pdf->output();
                    
                    $fileName = 'slip_gaji_' . $detail->user->id . '_' . $period->bulan . '_' . $period->tahun . '.pdf';
                    $filePath = 'slip_gaji/' . $fileName;
                    
                    if (!Storage::exists('public/slip_gaji')) {
                        Storage::makeDirectory('public/slip_gaji');
                    }
                    
                    Storage::put('public/' . $filePath, $pdfContent);
                    
                    \App\Models\Notification::create([
                        'user_id' => $detail->user->id,
                        'title' => '📄 Slip Gaji Tersedia',
                        'message' => "Slip gaji untuk periode {$period->nama_periode} sudah tersedia. Klik untuk melihat detail.",
                        'type' => 'payroll',
                        'link' => route('karyawan.slip-gaji.show', $detail->id),
                        'is_read' => false,
                    ]);
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $failedList[] = $detail->user->name . ': ' . $e->getMessage();
                }
            }
            
            $message = "Berhasil mengirim {$successCount} slip gaji";
            if (!empty($failedList)) {
                $message .= ", gagal: " . implode(', ', $failedList);
            }
            
            return response()->json(['success' => true, 'message' => $message]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $period = PayrollPeriod::with(['details.user.karyawan.divisi'])->findOrFail($id);
        
        $totalGaji = $period->details->sum('gaji_pokok');
        $totalTunjangan = $period->details->sum('tunjangan_tetap') + $period->details->sum('tunjangan_kinerja');
        $totalLembur = $period->details->sum('upah_lembur');
        $totalPotonganHadir = $period->details->sum('potongan_tidak_hadir');
        $totalPotonganBPJS = $period->details->sum('potongan_bpjs');
        
        $statistik = [
            'total_karyawan' => $period->details->count(),
            'total_gaji' => $totalGaji,
            'total_tunjangan' => $totalTunjangan,
            'total_lembur' => $totalLembur,
            'total_potongan_hadir' => $totalPotonganHadir,
            'total_potongan_bpjs' => $totalPotonganBPJS,
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

    public function sendSlipToEmail($periodId, $detailId)
    {
        try {
            $period = PayrollPeriod::findOrFail($periodId);
            $detail = PayrollDetail::with('user')->findOrFail($detailId);
            
            if (!$detail->user || !$detail->user->email) {
                return response()->json(['success' => false, 'message' => 'Email karyawan tidak ditemukan'], 400);
            }
            
            $pdf = Pdf::loadView('finance.payroll.slip_pdf', compact('period', 'detail'));
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
            ]);
            $pdfContent = $pdf->output();
            
            Mail::to($detail->user->email)->send(new SlipGajiMail($detail, $period, $pdfContent));
            
            return response()->json(['success' => true, 'message' => "Slip gaji berhasil dikirim ke {$detail->user->email}"]);
            
        } catch (\Exception $e) {
            Log::error('Gagal kirim slip gaji: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengirim slip gaji: ' . $e->getMessage()], 500);
        }
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
        $request->validate(['tanggal_pembayaran' => 'required|date']);
        
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

    public function export($id)
    {
        return redirect()->back()->with('info', 'Fitur export sedang dalam pengembangan');
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
        
        DB::beginTransaction();
        try {
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
            
            DB::commit();
            return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }
}