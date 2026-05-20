<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsensiHrController extends Controller
{
    /**
     * Halaman utama kelola absensi
     */
    public function index(Request $request)
    {
        $query = Absensi::with('user.divisi');

        // Filter berdasarkan tanggal
        if ($request->has('date') && $request->date) {
            $query->whereDate('tanggal', $request->date);
        }

        // Filter berdasarkan karyawan
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $absensi = $query->orderBy('tanggal', 'desc')->get();

        // Format data untuk view
        $formattedAbsensi = $absensi->map(function($item) {
            return [
                'id' => $item->id,
                'user_name' => $item->user->name ?? '-',
                'tanggal' => $item->tanggal,
                'jam_masuk' => $item->jam_masuk,
                'jam_pulang' => $item->jam_pulang,
                'keterangan' => $item->keterangan,
                'status_kehadiran' => $this->getStatusKehadiran($item),
                'status_class' => $this->getStatusClass($item),
                'late_minutes' => $item->late_minutes ?? 0,
                'approval_status' => $item->approval_status ?? 'approved'
            ];
        });

        // Data ketidakhadiran
        $ketidakhadiran = Absensi::whereNotNull('jenis_ketidakhadiran')
            ->with('user.divisi')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'user' => $item->user,
                    'tanggal' => $item->tanggal,
                    'tanggal_akhir' => $item->tanggal_akhir,
                    'jenis_ketidakhadiran' => $item->jenis_ketidakhadiran,
                    'keterangan' => $item->keterangan,
                    'approval_status' => $item->approval_status ?? 'pending'
                ];
            });

        // Data cuti
        $cuti = Absensi::where('jenis_ketidakhadiran', 'cuti')
            ->with('user.divisi')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function($item) {
                $user = $item->user;
                $start = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal) : null;
                $end = $item->tanggal_akhir ? \Carbon\Carbon::parse($item->tanggal_akhir) : $start;
                $periode = $start && $end ? $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y') : '-';
                $durasi = $start && $end ? $start->diffInDays($end) + 1 : 0;
                
                return [
                    'id' => $item->id,
                    'nama' => $user->name ?? '-',
                    'divisi' => $user->divisi->divisi ?? '-',
                    'periode' => $periode,
                    'durasi' => $durasi,
                    'jenis_cuti' => $item->jenis_cuti ?? 'tahunan',
                    'keterangan' => $item->keterangan ?? '-',
                    'status' => $item->approval_status ?? 'pending'
                ];
            });

        // Statistik
        $totalKaryawan = User::where('role', 'karyawan')->count();
        $hadiranCount = $absensi->whereNull('jenis_ketidakhadiran')->count();
        $sakitCount = $absensi->where('jenis_ketidakhadiran', 'sakit')->count();
        $izinCount = $absensi->where('jenis_ketidakhadiran', 'izin')->count();
        $cutiCount = $absensi->where('jenis_ketidakhadiran', 'cuti')->count();
        $tidakHadirCount = $totalKaryawan - $hadiranCount;

        return view('hr.kelola_absensi', compact(
            'formattedAbsensi', 'ketidakhadiran', 'cuti',
            'totalKaryawan', 'hadiranCount', 'sakitCount', 
            'izinCount', 'cutiCount', 'tidakHadirCount'
        ));
    }

    /**
     * Mendapatkan status kehadiran
     */
    private function getStatusKehadiran($absen)
    {
        if ($absen->jenis_ketidakhadiran) {
            return ucfirst($absen->jenis_ketidakhadiran);
        }
        
        if ($absen->jam_masuk && $absen->jam_masuk > '08:00:00') {
            return 'Terlambat';
        }
        
        return 'Hadir';
    }

    /**
     * Mendapatkan class CSS untuk status
     */
    private function getStatusClass($absen)
    {
        if ($absen->jenis_ketidakhadiran) {
            return 'status-' . strtolower($absen->jenis_ketidakhadiran);
        }
        
        if ($absen->jam_masuk && $absen->jam_masuk > '08:00:00') {
            return 'status-terlambat';
        }
        
        return 'status-hadir';
    }

    /**
     * Halaman daftar pengajuan surat sakit
     */
    public function suratSakit(Request $request)
    {
        $pengajuanSakit = Absensi::with('user.divisi')
            ->where('jenis_ketidakhadiran', 'sakit')
            ->whereNotNull('file_surat')
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('hr.absensi.surat_sakit', compact('pengajuanSakit'));
    }

    /**
     * Approve surat sakit (disetujui)
     */
    public function approveSakit($id)
    {
        try {
            $absen = Absensi::findOrFail($id);
            $absen->status_surat = 'approved';
            $absen->ada_surat_dokter = true;
            $absen->save();

            return redirect()->back()->with('success', '✅ Surat sakit disetujui. Karyawan tidak akan dipotong gaji.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * Reject surat sakit (ditolak)
     */
    public function rejectSakit($id)
    {
        try {
            $absen = Absensi::findOrFail($id);
            $absen->status_surat = 'rejected';
            $absen->ada_surat_dokter = false;
            $absen->save();

            return redirect()->back()->with('warning', '❌ Surat sakit ditolak. Karyawan akan dipotong gaji.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }

    /**
     * Detail surat sakit (JSON)
     */
    public function detailSurat($id)
    {
        try {
            $absen = Absensi::with('user.divisi')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $absen->id,
                    'karyawan' => $absen->user->name ?? '-',
                    'divisi' => $absen->user->divisi->divisi ?? '-',
                    'tanggal' => $absen->tanggal ? $absen->tanggal->format('d-m-Y') : '-',
                    'keterangan' => $absen->keterangan ?? '-',
                    'file_url' => $absen->file_surat ? asset('storage/' . $absen->file_surat) : null,
                    'status' => $absen->status_surat ?? 'pending'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}