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
        
        // 1. Ambil nama divisi dari manager yang sedang login
        $divisi = $user->divisi_id ? Divisi::find($user->divisi_id) : null;
        $namaDivisi = $divisi ? $divisi->divisi : null;

        if (!$user->divisi_id && !$namaDivisi) {
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
        $namaDivisiTeks = $namaDivisi ?? 'Divisi Anda';
        
        // 2. Ambil User ID bawahan (Karyawan) secara langsung dari tabel Users dan Karyawan
        // Query ini digabung agar jika divisi_id NULL tapi teks 'divisi'-nya sesuai, datanya tetap masuk.
        $karyawanIds = User::where('role', 'karyawan')
            ->where('id', '!=', $user->id) // Proteksi agar manager tidak masuk list
            ->whereHas('karyawan', function($query) use ($user, $namaDivisi) {
                $query->where('status_kerja', 'aktif')
                      ->where(function($q) use ($user, $namaDivisi) {
                          if ($user->divisi_id) {
                              $q->where('divisi_id', $user->divisi_id);
                          }
                          if ($namaDivisi) {
                              $q->orWhere('divisi', 'like', '%' . $namaDivisi . '%');
                          }
                      });
            })
            ->pluck('id')
            ->toArray();
        
        // Fallback Query: Jika relasi di atas kosong karena masalah setup model Eloquent,
        // Ambil mentah dari table karyawan berdasarkan user_id.
        if (empty($karyawanIds)) {
            $karyawanIds = Karyawan::where('status_kerja', 'aktif')
                ->where('user_id', '!=', $user->id)
                ->where(function($query) use ($user, $namaDivisi) {
                    if ($user->divisi_id) {
                        $query->where('divisi_id', $user->divisi_id);
                    }
                    if ($namaDivisi) {
                        $query->orWhere('divisi', 'like', '%' . $namaDivisi . '%');
                    }
                })
                ->pluck('user_id')
                ->filter()
                ->toArray();
        }
        
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
                'namaDivisi' => $namaDivisiTeks,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);
        }
        
        // 3. Ambil data User lengkap untuk nama & role display
        $users = User::whereIn('id', $karyawanIds)->get()->keyBy('id');
        
        // ========== AMBIL DATA KPA DARI HR ==========
        $indikators = IndikatorKpa::with('aspek')->where('is_active', true)->get();
        
        // Antisipasi: Query Penilaian menggunakan user_id atau karyawan_id 
        // Kita cari yang COCOK dengan array $karyawanIds di kedua kemungkinan nama kolomnya
        $penilaianList = PenilaianKpa::with('indikator.aspek')
            ->where(function($q) use ($karyawanIds) {
                $q->whereIn('karyawan_id', $karyawanIds)
                  ->orWhereIn('user_id', $karyawanIds);
            })
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
            
        // Grouping yang fleksibel (berdasarkan kolom mana yang terisi data id-nya)
        $penilaianGrouped = $penilaianList->groupBy(function($item) {
            return $item->karyawan_id ?? $item->user_id;
        });
        
        // ========== AMBIL DATA TUGAS ==========
        $tasks = Task::whereIn('assigned_to', $karyawanIds)
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->get();
        
        $taskCompleted = $tasks->where('status', 'selesai')->groupBy('assigned_to')->map(fn($t) => $t->count());
        $totalTasks = $tasks->groupBy('assigned_to')->map(fn($t) => $t->count());
        
        // Ambil data target kuantitas (antisipasi kolom karyawan_id / user_id)
        $targetKuantitasRaw = TargetKuantitas::where(function($q) use ($karyawanIds) {
                $q->whereIn('karyawan_id', $karyawanIds)
                  ->orWhereIn('user_id', $karyawanIds);
            })
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $targetKuantitas = $targetKuantitasRaw->keyBy(function($item) {
            return $item->karyawan_id ?? $item->user_id;
        });
        
        // Hitung nilai akhir per karyawan
        $dataKaryawan = collect();
        $gradeCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        $totalNilai = 0;
        
        foreach ($karyawanIds as $karyawanId) {
            $userData = $users[$karyawanId] ?? null;
            if (!$userData) continue;
            
            // Hitung total nilai KPA dari semua indikator jika ada
            $totalNilaiKPA = 0;
            $karyawanPenilaian = $penilaianGrouped[$karyawanId] ?? collect();
            
            foreach ($indikators as $indikator) {
                $penilaian = $karyawanPenilaian->firstWhere('indikator_id', $indikator->id);
                $nilai = $penilaian ? $penilaian->nilai : 0;
                
                if ($indikator->aspek && $nilai > 0) {
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
            
            // Nilai Tugas
            $totalTugas = $totalTasks[$karyawanId] ?? 0;
            $tugasSelesai = $taskCompleted[$karyawanId] ?? 0;
            $nilaiTugas = $totalTugas > 0 ? ($tugasSelesai / $totalTugas) * 100 : 100;
            
            // Formula Nilai akhir (70% KPA, 30% Tugas)
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
        
        // Urutkan berdasarkan nilai tertinggi ke terendah
        $sortedData = $dataKaryawan->sortByDesc('nilai')->values();
        
        $topKaryawan = $sortedData->take(5);
        $lowKaryawan = $sortedData->reverse()->take(5)->values();
        
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
            'namaDivisiTeks',
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