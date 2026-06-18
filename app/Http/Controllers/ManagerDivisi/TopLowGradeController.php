<?php

namespace App\Http\Controllers\ManagerDivisi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopLowGradeController extends Controller
{
    public function index(Request $request)
    {
        // Ambil manager yang login
        $manager = Auth::user();
        
        // =============================================
        // VALIDASI: PASTIKAN MANAGER MEMILIKI DIVISI
        // =============================================
        $divisiId = $manager->divisi_id;
        
        if (!$divisiId) {
            // Jika manager tidak memiliki divisi, tampilkan pesan error
            return view('manager_divisi.top_low_grade', [
                'topKaryawan' => [],
                'lowKaryawan' => [],
                'statistik' => [
                    'total_karyawan' => 0,
                    'grade_a_count' => 0,
                    'grade_b_count' => 0,
                    'grade_c_count' => 0,
                    'grade_d_count' => 0,
                    'rata_rata_nilai' => 0,
                    'total_tugas' => 0,
                ],
                'bulan' => $request->input('bulan', now()->month),
                'tahun' => $request->input('tahun', now()->year),
                'namaDivisi' => 'Tidak ada divisi',
                'error' => 'Anda belum memiliki divisi yang terdaftar.'
            ]);
        }
        
        // =============================================
        // AMBIL NAMA DIVISI DARI TABEL DIVISI
        // =============================================
        $namaDivisi = 'Divisi Anda';
        $divisi = Divisi::find($divisiId);
        
        if ($divisi) {
            // Sesuaikan dengan kolom yang ada di tabel divisi
            $namaDivisi = $divisi->divisi ?? $divisi->nama_divisi ?? $divisi->name ?? 'Divisi ' . $divisiId;
        } else {
            // Jika tabel divisi tidak ditemukan, coba dari kolom divisi di user
            $namaDivisi = $manager->divisi ?? 'Divisi ' . $divisiId;
        }
        
        // =============================================
        // AMBIL BULAN DAN TAHUN DARI REQUEST
        // =============================================
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        
        // TARGET TUGAS per bulan (5 tugas selesai = skor 100%)
        $targetTasks = 5;
        
        // =============================================
        // AMBIL SEMUA KARYAWAN DI DIVISI YANG SAMA DENGAN MANAGER
        // =============================================
        // HANYAMenampilkan karyawan yang divisi_id nya sama dengan manager
        $employees = User::where('divisi_id', $divisiId)
            ->where(function($q) {
                $q->where('role', 'karyawan')
                  ->orWhere('role', 'staff')
                  ->orWhere('role', 'employee')
                  ->orWhere('status_karyawan', 'tetap')
                  ->orWhere('status_karyawan', 'kontrak');
            })
            // JANGAN tampilkan manager itu sendiri di daftar karyawan
            ->where('id', '!=', $manager->id)
            ->with(['tasks' => function($query) use ($bulan, $tahun) {
                $query->where('status', 'selesai')
                      ->whereMonth('completed_at', $bulan)
                      ->whereYear('completed_at', $tahun);
            }])
            ->get();
        
        // =============================================
        // PROSES DATA KARYAWAN
        // =============================================
        $dataKaryawan = [];
        $totalTugasSemua = 0;
        $totalNilaiSemua = 0;
        $gradeCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        
        foreach ($employees as $employee) {
            // Hitung jumlah tugas selesai dari relasi tasks
            $completedTasks = $employee->tasks->count();
            $totalTugasSemua += $completedTasks;
            
            // HITUNG SKOR: (tugas_selesai / target) * 100%
            if ($targetTasks > 0) {
                $rawScore = ($completedTasks / $targetTasks) * 100;
                $score = min(100, round($rawScore, 1));
            } else {
                $score = 0;
            }
            
            $totalNilaiSemua += $score;
            
            // Tentukan GRADE berdasarkan SKOR
            if ($score >= 90) {
                $grade = 'A';
                $gradeCount['A']++;
            } elseif ($score >= 75) {
                $grade = 'B';
                $gradeCount['B']++;
            } elseif ($score >= 60) {
                $grade = 'C';
                $gradeCount['C']++;
            } else {
                $grade = 'D';
                $gradeCount['D']++;
            }
            
            // Ambil role/jabatan
            $role = $employee->role ?? 'Karyawan';
            $jabatan = $employee->jabatan ?? $employee->position ?? $employee->status_karyawan ?? 'Staff';
            
            $dataKaryawan[] = [
                'id' => $employee->id,
                'name' => $employee->name,
                'role' => $role,
                'jabatan' => $jabatan,
                'total_tugas' => $completedTasks,
                'nilai' => $score,
                'grade' => $grade,
            ];
        }
        
        // =============================================
        // URUTKAN DAN AMBIL TOP 5 & LOW 5
        // =============================================
        
        // Urutkan berdasarkan NILAI (TERTINGGI ke TERENDAH)
        usort($dataKaryawan, function($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });
        
        // Ambil TOP 5 (nilai tertinggi) - karyawan terbaik
        $topKaryawan = array_slice($dataKaryawan, 0, 5);
        
        // Ambil LOW 5 (nilai terendah) - karyawan yang perlu dievaluasi
        // Filter dulu yang nilainya di bawah 75 (Grade C/D)
        $lowData = array_filter($dataKaryawan, function($k) {
            return $k['nilai'] < 75;
        });
        
        // Urutkan dari terendah ke tertinggi
        usort($lowData, function($a, $b) {
            return $a['nilai'] <=> $b['nilai'];
        });
        
        // Ambil 5 terendah
        $lowKaryawan = array_slice($lowData, 0, 5);
        
        // Jika lowKaryawan kurang dari 5, ambil dari nilai terendah keseluruhan
        if (count($lowKaryawan) < 5 && count($dataKaryawan) > 0) {
            $allLow = array_slice($dataKaryawan, -5);
            usort($allLow, function($a, $b) {
                return $a['nilai'] <=> $b['nilai'];
            });
            $lowKaryawan = array_slice($allLow, 0, 5);
        }
        
        // =============================================
        // STATISTIK
        // =============================================
        $statistik = [
            'total_karyawan' => $employees->count(),
            'grade_a_count' => $gradeCount['A'],
            'grade_b_count' => $gradeCount['B'],
            'grade_c_count' => $gradeCount['C'],
            'grade_d_count' => $gradeCount['D'],
            'rata_rata_nilai' => $employees->count() > 0 ? round($totalNilaiSemua / $employees->count(), 1) : 0,
            'total_tugas' => $totalTugasSemua,
        ];
        
        // =============================================
        // RETURN VIEW
        // =============================================
        return view('manager_divisi.top_low_grade', compact(
            'topKaryawan', 
            'lowKaryawan', 
            'statistik', 
            'bulan', 
            'tahun', 
            'namaDivisi'
        ));
    }
    
    /**
     * Detail karyawan (opsional)
     */
    public function show($id)
    {
        $manager = Auth::user();
        
        $employee = User::with(['tasks' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);
        
        // Pastikan karyawan satu divisi dengan manager
        if ($employee->divisi_id !== $manager->divisi_id) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan ini');
        }
        
        $bulan = request()->input('bulan', now()->month);
        $tahun = request()->input('tahun', now()->year);
        $targetTasks = 5;
        
        $completedTasks = $employee->tasks()
            ->where('status', 'selesai')
            ->whereMonth('completed_at', $bulan)
            ->whereYear('completed_at', $tahun)
            ->count();
        
        $score = min(100, round(($completedTasks / $targetTasks) * 100, 1));
        
        if ($score >= 90) $grade = 'A';
        elseif ($score >= 75) $grade = 'B';
        elseif ($score >= 60) $grade = 'C';
        else $grade = 'D';
        
        return view('manager_divisi.karyawan_detail', compact(
            'employee', 'score', 'grade', 'completedTasks', 'targetTasks', 'bulan', 'tahun'
        ));
    }
}