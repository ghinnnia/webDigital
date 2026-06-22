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
            $namaDivisi = $divisi->divisi ?? $divisi->nama_divisi ?? $divisi->name ?? 'Divisi ' . $divisiId;
        } else {
            $namaDivisi = $manager->divisi ?? 'Divisi ' . $divisiId;
        }
        
        // =============================================
        // AMBIL BULAN DAN TAHUN DARI REQUEST
        // =============================================
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        
        // =============================================
        // AMBIL SEMUA KARYAWAN DI DIVISI YANG SAMA
        // =============================================
        $employees = User::where('divisi_id', $divisiId)
            ->where(function($q) {
                $q->where('role', 'karyawan')
                  ->orWhere('role', 'staff')
                  ->orWhere('role', 'employee')
                  ->orWhere('status_karyawan', 'tetap')
                  ->orWhere('status_karyawan', 'kontrak');
            })
            ->where('id', '!=', $manager->id)
            // Memuat relasi tugas yang difilter berdasarkan bulan & tahun (menggunakan created_at atau keaslian tanggal tugas)
            ->with(['tasks' => function($query) use ($bulan, $tahun) {
                $query->whereMonth('created_at', $bulan)
                      ->whereYear('created_at', $tahun);
            }])
            ->get();
        
        // =============================================
        // PROSES DATA KARYAWAN
        // =============================================
        $dataKaryawan = [];
        $totalTugasSelesaiSemua = 0;
        $totalNilaiSemua = 0;
        $gradeCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        
        foreach ($employees as $employee) {
            // Ambil semua tugas karyawan di bulan & tahun ini
            $allTasks = $employee->tasks;
            
            // Hitung total semua tugas (target dinamis)
            $totalTasksCount = $allTasks->count();
            
            // Hitung jumlah tugas yang berstatus 'selesai'
            $completedTasks = $allTasks->where('status', 'selesai')->count();
            $totalTugasSelesaiSemua += $completedTasks;
            
            // RUMUS BARU: (tugas_selesai / total_tugas) * 100%
            if ($totalTasksCount > 0) {
                $rawScore = ($completedTasks / $totalTasksCount) * 100;
                $score = min(100, round($rawScore, 1));
            } else {
                // Jika belum diberikan tugas sama sekali bulan ini, beri nilai default 0 atau disesuaikan kebijakan
                $score = 0; 
            }
            
            $totalNilaiSemua += $score;
            
            // Tentukan GRADE berdasarkan SKOR BARU
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
            
            $role = $employee->role ?? 'Karyawan';
            $jabatan = $employee->jabatan ?? $employee->position ?? $employee->status_karyawan ?? 'Staff';
            
            $dataKaryawan[] = [
                'id' => $employee->id,
                'name' => $employee->name,
                'role' => $role,
                'jabatan' => $jabatan,
                'total_tugas' => $completedTasks, // Yang dikirim ke view tetap jumlah tugas selesai
                'nilai' => $score,
                'grade' => $grade,
            ];
        }
        
        // =============================================
        // URUTKAN DAN AMBIL TOP 5 & LOW 5
        // =============================================
        usort($dataKaryawan, function($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });
        
        $topKaryawan = array_slice($dataKaryawan, 0, 5);
        
        $lowData = array_filter($dataKaryawan, function($k) {
            return $k['nilai'] < 75;
        });
        
        usort($lowData, function($a, $b) {
            return $a['nilai'] <=> $b['nilai'];
        });
        
        $lowKaryawan = array_slice($lowData, 0, 5);
        
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
            'total_tugas' => $totalTugasSelesaiSemua,
        ];
        
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
     * Detail karyawan (Ikut diperbaiki agar sinkron)
     */
    public function show($id)
    {
        $manager = Auth::user();
        
        $employee = User::findOrFail($id);
        
        if ($employee->divisi_id !== $manager->divisi_id) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan ini');
        }
        
        $bulan = request()->input('bulan', now()->month);
        $tahun = request()->input('tahun', now()->year);
        
        // Ambil semua tugas di bulan & tahun ini
        $allTasks = $employee->tasks()
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();
            
        $totalTasksCount = $allTasks->count();
        $completedTasks = $allTasks->where('status', 'selesai')->count();
        
        // Hitung skor dinamis
        if ($totalTasksCount > 0) {
            $score = min(100, round(($completedTasks / $totalTasksCount) * 100, 1));
        } else {
            $score = 0;
        }
        
        if ($score >= 90) $grade = 'A';
        elseif ($score >= 75) $grade = 'B';
        elseif ($score >= 60) $grade = 'C';
        else $grade = 'D';
        
        // Agar view detail tidak error, kita samakan variabel targetTasks diisi total tugas bulan ini
        $targetTasks = $totalTasksCount;
        
        return view('manager_divisi.karyawan_detail', compact(
            'employee', 'score', 'grade', 'completedTasks', 'targetTasks', 'bulan', 'tahun'
        ));
    }
}