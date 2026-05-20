<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use App\Models\KinerjaPegawai;
use Illuminate\Http\Request;

class ManagerDivisiController extends Controller
{
    /**
     * Dashboard Manager Divisi dengan Top & Low Grade karyawan
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // Ambil semua karyawan di divisi manager ini
        $karyawan = User::where('role', 'karyawan')
            ->where('divisi_id', $user->divisi_id)
            ->with(['kinerja' => function($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            }])
            ->get()
            ->map(function($karyawan) use ($user) {
                $kinerja = $karyawan->kinerja->first();
                return [
                    'id' => $karyawan->id,
                    'name' => $karyawan->name,
                    'divisi' => $user->divisi->divisi ?? '-',
                    'nilai' => $kinerja->nilai_rata_rata ?? 0,
                    'grade' => $kinerja->grade ?? '-'
                ];
            })
            ->filter(function($item) {
                return $item['nilai'] > 0;
            });

        $topKaryawan = $karyawan->sortByDesc('nilai')->take(5);
        $lowKaryawan = $karyawan->sortBy('nilai')->take(5);

        $statistik = [
            'total_karyawan' => $karyawan->count(),
            'rata_rata' => $karyawan->avg('nilai'),
            'top_nilai' => $topKaryawan->first()['nilai'] ?? 0,
            'low_nilai' => $lowKaryawan->last()['nilai'] ?? 0,
            'grade_a' => $karyawan->where('grade', 'A')->count(),
            'grade_b' => $karyawan->where('grade', 'B')->count(),
            'grade_c' => $karyawan->where('grade', 'C')->count(),
            'grade_d' => $karyawan->where('grade', 'D')->count(),
        ];

        $divisi = $user->divisi;

        return view('manager_divisi.dashboard', compact(
            'topKaryawan', 'lowKaryawan', 'statistik', 
            'divisi', 'bulan', 'tahun'
        ));
    }

    /**
     * API: Detail peringkat karyawan di divisi manager
     */
    public function karyawanRanking(Request $request)
    {
        $user = auth()->user();
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $karyawan = User::where('role', 'karyawan')
            ->where('divisi_id', $user->divisi_id)
            ->with(['kinerja' => function($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            }])
            ->get()
            ->map(function($karyawan) use ($user) {
                $kinerja = $karyawan->kinerja->first();
                return [
                    'id' => $karyawan->id,
                    'name' => $karyawan->name,
                    'divisi' => $user->divisi->divisi ?? '-',
                    'nilai' => $kinerja->nilai_rata_rata ?? 0,
                    'grade' => $kinerja->grade ?? '-'
                ];
            })
            ->sortByDesc('nilai')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $karyawan
        ]);
    }
}