<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Task;
use App\Models\PenilaianKpa;
use App\Models\IndikatorKpa;
use App\Models\TargetKuantitas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManagerDivisiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah manager punya divisi
        if (!$user->divisi_id) {
            return view('manager_divisi.top_low_grade', [
                'topKaryawan' => collect([]),
                'lowKaryawan' => collect([]),
                'statistik' => [
                    'total_karyawan' => 0,
                    'grade_a_count' => 0,
                    'rata_rata_nilai' => 0,
                    'total_tugas' => 0,
                ],
                'namaDivisi' => 'Belum ada divisi',
                'bulan' => now()->month,
                'tahun' => now()->year,
            ]);
        }
        
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        
        // Ambil divisi
        $divisi = Divisi::find($user->divisi_id);
        $namaDivisi = $divisi ? $divisi->divisi : 'Divisi Anda';
        
        // Ambil semua karyawan di divisi manager
        $karyawanIds = Karyawan::where('divisi_id', $user->divisi_id)
            ->where('status_kerja', 'aktif')
            ->pluck('user_id')
            ->toArray();
        
        if (empty($karyawanIds)) {
            return view('manager_divisi.top_low_grade', [
                'topKaryawan' => collect([]),
                'lowKaryawan' => collect([]),
                'statistik' => [
                    'total_karyawan' => 0,
                    'grade_a_count' => 0,
                    'rata_rata_nilai' => 0,
                    'total_tugas' => 0,
                ],
                'namaDivisi' => $namaDivisi,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);
        }
        
        // ========== AMBIL DATA KPA DARI HR (tabel penilaian_kpa) ==========
        // Ambil semua indikator untuk menghitung total nilai
        $indikators = IndikatorKpa::with('aspek')->where('is_active', true)->get();
        
        // Ambil penilaian per karyawan
        $penilaianList = PenilaianKpa::with('indikator.aspek')
            ->whereIn('karyawan_id', $karyawanIds)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->groupBy('karyawan_id');
        
        // ========== AMBIL DATA TUGAS ==========
        $tasks = Task::whereIn('assigned_to', $karyawanIds)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->get();
        
        // Hitung tugas selesai per karyawan
        $taskCompleted = Task::whereIn('assigned_to', $karyawanIds)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->where('status', 'selesai')
            ->get()
            ->groupBy('assigned_to')
            ->map(fn($t) => $t->count());
        
        $totalTasks = $tasks->groupBy('assigned_to')
            ->map(fn($t) => $t->count());
        
        // Ambil data target kuantitas
        $targetKuantitas = TargetKuantitas::whereIn('karyawan_id', $karyawanIds)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('karyawan_id');
        
        // Ambil data users
        $users = User::whereIn('id', $karyawanIds)
            ->where('role', 'karyawan')
            ->get()
            ->keyBy('id');
        
        // Hitung per karyawan
        $dataKaryawan = collect();
        $gradeCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        $totalNilai = 0;
        
        foreach ($karyawanIds as $karyawanId) {
            $userData = $users[$karyawanId] ?? null;
            if (!$userData) continue;
            
            // Hitung total nilai KPA dari semua indikator
            $totalNilaiKPA = 0;
            $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
            
            foreach ($indikators as $indikator) {
                $penilaian = $karyawanPenilaian->firstWhere('indikator_id', $indikator->id);
                $nilai = $penilaian ? $penilaian->nilai : 0;
                
                if ($indikator->aspek && $nilai > 0) {
                    // Kontribusi = (Nilai/100) × Bobot Indikator × (Bobot Aspek/100)
                    $kontribusi = ($nilai / 100) * $indikator->bobot * ($indikator->aspek->bobot / 100);
                    $totalNilaiKPA += $kontribusi;
                }
            }
            $totalNilaiKPA = min(100, round($totalNilaiKPA, 2));
            
            // Nilai dari Target Kuantitas
            $tk = $targetKuantitas[$karyawanId] ?? null;
            $nilaiTarget = 100;
            if ($tk && $tk->target > 0) {
                $nilaiTarget = min(100, ($tk->realisasi / $tk->target) * 100);
            }
            
            // Nilai Tugas (berdasarkan penyelesaian)
            $totalTugas = $totalTasks[$karyawanId] ?? 0;
            $tugasSelesai = $taskCompleted[$karyawanId] ?? 0;
            $nilaiTugas = $totalTugas > 0 ? ($tugasSelesai / $totalTugas) * 100 : 100;
            
            // Nilai akhir (70% KPA, 30% Tugas)
            if ($totalNilaiKPA > 0 && $nilaiTugas > 0) {
                $nilaiAkhir = ($totalNilaiKPA * 0.7) + ($nilaiTugas * 0.3);
            } elseif ($totalNilaiKPA > 0) {
                $nilaiAkhir = $totalNilaiKPA;
            } else {
                $nilaiAkhir = $nilaiTugas;
            }
            
            // Tentukan grade
            $grade = $this->getGrade($nilaiAkhir);
            $gradeCount[$grade]++;
            $totalNilai += $nilaiAkhir;
            
            $dataKaryawan->push([
                'id' => $userData->id,
                'name' => $userData->name,
                'role' => $userData->role,
                'nilai_kpa' => round($totalNilaiKPA, 1),
                'nilai_tugas' => round($nilaiTugas, 1),
                'nilai' => round($nilaiAkhir, 1),
                'grade' => $grade,
                'total_tugas' => $totalTugas,
            ]);
        }
        
        // Urutkan berdasarkan nilai
        $sortedData = $dataKaryawan->sortByDesc('nilai')->values();
        
        // Ambil TOP 5 dan LOW 5
        $topKaryawan = $sortedData->take(5);
        $lowKaryawan = $sortedData->reverse()->take(5)->values();
        
        // Statistik
        $totalKaryawan = $dataKaryawan->count();
        $rataRata = $totalKaryawan > 0 ? $totalNilai / $totalKaryawan : 0;
        
        $statistik = [
            'total_karyawan' => $totalKaryawan,
            'grade_a_count' => $gradeCount['A'],
            'grade_b_count' => $gradeCount['B'],
            'grade_c_count' => $gradeCount['C'],
            'grade_d_count' => $gradeCount['D'],
            'rata_rata_nilai' => round($rataRata, 1),
            'total_tugas' => $tasks->count(),
            'grade_a' => $gradeCount['A'],
            'grade_b' => $gradeCount['B'],
            'grade_c' => $gradeCount['C'],
            'grade_d' => $gradeCount['D'],
            'rata_rata' => round($rataRata, 1),
            'top_nilai' => $topKaryawan->first()['nilai'] ?? 0,
            'low_nilai' => $lowKaryawan->last()['nilai'] ?? 0,
        ];
        
        return view('manager_divisi.top_low_grade', compact(
            'topKaryawan',
            'lowKaryawan',
            'statistik',
            'namaDivisi',
            'bulan',
            'tahun'
        ));
    }
    
    private function getGrade($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        return 'D';
    }
}