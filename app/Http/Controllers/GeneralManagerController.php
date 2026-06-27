<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Project;
use App\Models\PenilaianKpa;
use App\Models\IndikatorKpa;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GeneralManagerController extends Controller
{
    /**
     * Halaman Beranda
     */
    public function home()
    {
        $totalKaryawan = User::where('role', 'karyawan')->count();
        $totalLayanan = Layanan::count();
        $totalProject = Project::count();
        
        return view('general_manajer.home', compact('totalKaryawan', 'totalLayanan', 'totalProject'));
    }
    
    /**
     * Halaman Data Karyawan
     */
    public function data_karyawan(Request $request)
    {
        $query = User::where('role', 'karyawan')->with('divisi');
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('divisi')) {
            $query->where('divisi_id', $request->divisi);
        }
        
        $karyawan = $query->paginate(10)->withQueryString();
        
        return view('general_manajer.data_karyawan', compact('karyawan'));
    }
    
    /**
     * Halaman Layanan
     */
    public function layanan()
    {
        $layanan = Layanan::all();
        return view('general_manajer.data_layanan', compact('layanan'));
    }
    
    /**
     * Halaman Data Project
     */
    public function data_project(Request $request)
    {
        $query = Project::with(['layanan', 'penanggungJawab']);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }
        
        $projects = $query->paginate(10)->withQueryString();
        
        $managers = User::where('role', 'manager_divisi')->with('divisi')->get();
        $karyawans = User::where('role', 'karyawan')->with('divisi')->get();
        
        return view('general_manajer.data_project', compact('projects', 'managers', 'karyawans'));
    }
    
    /**
     * Update Penanggung Jawab Project
     */
    public function update_project(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $project->update([
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'penanggung_jawab_ids' => $request->penanggung_jawab_ids,
            'karyawan_penanggung_jawab_id' => $request->karyawan_penanggung_jawab_id,
            'karyawan_penanggung_jawab_ids' => $request->karyawan_penanggung_jawab_ids,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Penanggung jawab berhasil ditetapkan!']);
    }
    
    /**
     * Halaman Tim & Divisi
     */
    public function tim_divisi()
    {
        $divisi = Divisi::with(['karyawan'])->get();
        return view('general_manajer.tim_dan_divisi', compact('divisi'));
    }
    
    /**
     * Hitung total nilai KPA dari indikator
     */
    /**
 * Hitung total nilai KPA dari indikator yang ADA nilainya
 */
private function hitungTotalNilaiKPA($penilaianList, $indikators)
{
    $totalNilai = 0;
    $totalBobot = 0;
    
    foreach ($indikators as $indikator) {
        $penilaian = $penilaianList->firstWhere('indikator_id', $indikator->id);
        $nilai = $penilaian ? $penilaian->nilai : 0;
        
        // Hanya hitung jika ada nilai (termasuk nilai 0 itu tetap dihitung)
        if ($indikator->aspek && $nilai >= 0) {
            // Kontribusi = (Nilai/100) × Bobot Indikator × (Bobot Aspek/100)
            $kontribusi = ($nilai / 100) * $indikator->bobot * ($indikator->aspek->bobot / 100);
            $totalNilai += $kontribusi;
            $totalBobot += $indikator->bobot * ($indikator->aspek->bobot / 100);
        }
    }
    
    // Normalisasi ke 100
    if ($totalBobot > 0) {
        $totalNilai = ($totalNilai / $totalBobot) * 100;
    }
    
    return min(100, round($totalNilai, 2));
}
    
    /**
     * Tentukan grade berdasarkan nilai
     */
    private function getGrade($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        return 'D';
    }
    
    /**
     * Dapatkan nama divisi dari ID
     */
    private function getNamaDivisi($divisiId)
    {
        if (!$divisiId) return '-';
        $divisi = Divisi::find($divisiId);
        return $divisi ? $divisi->divisi : '-';
    }
    
    /**
     * Halaman Top & Low Grade (Menggunakan data dari tabel penilaian_kpa)
     */
  public function index(Request $request)
{
    $bulan = $request->get('bulan', now()->month);
    $tahun = $request->get('tahun', now()->year);
    
    // Ambil semua indikator
    $indikators = IndikatorKpa::with('aspek')->where('is_active', true)->get();
    
    // ============================================================
    // 1. DATA MANAGER DIVISI
    // ============================================================
    
    $managerDivisi = User::where('role', 'manager_divisi')
        ->where('status_kerja', 'aktif')
        ->get();
    
    $managerData = [];
    
    foreach ($managerDivisi as $manager) {
        // Ambil semua karyawan di divisi manager ini
        $karyawanIds = Karyawan::where('divisi_id', $manager->divisi_id)
            ->where('status_kerja', 'aktif')
            ->pluck('user_id')
            ->toArray();
        
        if (empty($karyawanIds)) {
            continue;
        }
        
        // Ambil penilaian KPA karyawan
        $penilaianList = PenilaianKpa::with('indikator.aspek')
            ->whereIn('karyawan_id', $karyawanIds)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->groupBy('karyawan_id');
        
        // Hitung rata-rata nilai seluruh karyawan di divisi
        $totalNilaiDivisi = 0;
        $jumlahKaryawanDenganNilai = 0;
        
        foreach ($karyawanIds as $karyawanId) {
            $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
            
            // Hitung nilai rata-rata karyawan dari semua indikator yang ADA
            $totalNilaiKaryawan = 0;
            $jumlahIndikator = 0;
            
            foreach ($indikators as $indikator) {
                $penilaian = $karyawanPenilaian->firstWhere('indikator_id', $indikator->id);
                $nilai = $penilaian ? $penilaian->nilai : null;
                
                if ($nilai !== null) {
                    $totalNilaiKaryawan += $nilai;
                    $jumlahIndikator++;
                }
            }
            
            $nilaiKaryawan = $jumlahIndikator > 0 ? round($totalNilaiKaryawan / $jumlahIndikator, 2) : 0;
            
            if ($nilaiKaryawan > 0) {
                $totalNilaiDivisi += $nilaiKaryawan;
                $jumlahKaryawanDenganNilai++;
            }
        }
        
        $rataNilaiManager = $jumlahKaryawanDenganNilai > 0 
            ? round($totalNilaiDivisi / $jumlahKaryawanDenganNilai, 1)
            : 0;
        
        if ($rataNilaiManager > 0) {
            $managerData[] = [
                'name' => $manager->name,
                'divisi' => $this->getNamaDivisi($manager->divisi_id),
                'nilai' => $rataNilaiManager,
                'grade' => $this->getGrade($rataNilaiManager),
                'jumlah_karyawan' => count($karyawanIds),
            ];
        }
    }
    
    // Urutkan manager
    $sortedManagers = collect($managerData)->sortByDesc('nilai')->values();
    $topManagers = $sortedManagers->take(5);
    $lowManagers = $sortedManagers->reverse()->take(5)->values();
    
    // ============================================================
    // 2. DATA DIVISI
    // ============================================================
    
    $divisiList = Divisi::all();
    $divisiData = [];
    
    foreach ($divisiList as $divisi) {
        $karyawanIds = Karyawan::where('divisi_id', $divisi->id)
            ->where('status_kerja', 'aktif')
            ->pluck('user_id')
            ->toArray();
        
        if (empty($karyawanIds)) {
            continue;
        }
        
        $penilaianList = PenilaianKpa::with('indikator.aspek')
            ->whereIn('karyawan_id', $karyawanIds)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->groupBy('karyawan_id');
        
        $totalNilaiDivisi = 0;
        $jumlahKaryawanDenganNilai = 0;
        
        foreach ($karyawanIds as $karyawanId) {
            $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
            
            $totalNilaiKaryawan = 0;
            $jumlahIndikator = 0;
            
            foreach ($indikators as $indikator) {
                $penilaian = $karyawanPenilaian->firstWhere('indikator_id', $indikator->id);
                $nilai = $penilaian ? $penilaian->nilai : null;
                
                if ($nilai !== null) {
                    $totalNilaiKaryawan += $nilai;
                    $jumlahIndikator++;
                }
            }
            
            $nilaiKaryawan = $jumlahIndikator > 0 ? round($totalNilaiKaryawan / $jumlahIndikator, 2) : 0;
            
            if ($nilaiKaryawan > 0) {
                $totalNilaiDivisi += $nilaiKaryawan;
                $jumlahKaryawanDenganNilai++;
            }
        }
        
        $rataNilaiDivisi = $jumlahKaryawanDenganNilai > 0 
            ? round($totalNilaiDivisi / $jumlahKaryawanDenganNilai, 1)
            : 0;
        
        if ($rataNilaiDivisi > 0) {
            $divisiData[] = [
                'nama' => $divisi->divisi,
                'jumlah_karyawan' => count($karyawanIds),
                'nilai_rata_rata' => $rataNilaiDivisi,
                'grade' => $this->getGrade($rataNilaiDivisi),
            ];
        }
    }
    
    $sortedDivisi = collect($divisiData)->sortByDesc('nilai_rata_rata')->values();
    $topDivisi = $sortedDivisi->take(5);
    $lowDivisi = $sortedDivisi->reverse()->take(5)->values();
    
    // Statistik
    $statistik = [
        'total_manager' => User::where('role', 'manager_divisi')->count(),
        'total_divisi' => Divisi::count(),
        'rata_rata_manager' => $managerData ? round(collect($managerData)->avg('nilai'), 1) : 0,
        'rata_rata_divisi' => $divisiData ? round(collect($divisiData)->avg('nilai_rata_rata'), 1) : 0,
    ];
    
    return view('general_manajer.top_low_grade', compact(
        'topManagers', 'lowManagers', 'topDivisi', 'lowDivisi', 'statistik', 'bulan', 'tahun'
    ));
}
    
    /**
     * API untuk ranking manager
     */
    public function managerRanking(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        
        $indikators = IndikatorKpa::with('aspek')->where('is_active', true)->get();
        
        $managerDivisi = User::where('role', 'manager_divisi')
            ->where('status_kerja', 'aktif')
            ->get();
        
        $result = [];
        
        foreach ($managerDivisi as $manager) {
            $karyawanIds = Karyawan::where('divisi_id', $manager->divisi_id)
                ->where('status_kerja', 'aktif')
                ->pluck('user_id')
                ->toArray();
            
            if (empty($karyawanIds)) {
                continue;
            }
            
            $penilaianList = PenilaianKpa::with('indikator.aspek')
                ->whereIn('karyawan_id', $karyawanIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()
                ->groupBy('karyawan_id');
            
            $totalNilaiDivisi = 0;
            $jumlahKaryawan = 0;
            
            foreach ($karyawanIds as $karyawanId) {
                $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
                $nilaiKaryawan = $this->hitungTotalNilaiKPA($karyawanPenilaian, $indikators);
                
                if ($nilaiKaryawan > 0) {
                    $totalNilaiDivisi += $nilaiKaryawan;
                    $jumlahKaryawan++;
                }
            }
            
            $rataNilai = $jumlahKaryawan > 0 ? $totalNilaiDivisi / $jumlahKaryawan : 0;
            
            $result[] = [
                'id' => $manager->id,
                'name' => $manager->name,
                'divisi' => $this->getNamaDivisi($manager->divisi_id),
                'nilai' => round($rataNilai, 1),
                'grade' => $this->getGrade($rataNilai),
                'jumlah_karyawan' => count($karyawanIds),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => collect($result)->sortByDesc('nilai')->values(),
        ]);
    }
    
    /**
     * API untuk ranking divisi
     */
    public function divisiRanking(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        
        $indikators = IndikatorKpa::with('aspek')->where('is_active', true)->get();
        $divisiList = Divisi::all();
        $result = [];
        
        foreach ($divisiList as $divisi) {
            $karyawanIds = Karyawan::where('divisi_id', $divisi->id)
                ->where('status_kerja', 'aktif')
                ->pluck('user_id')
                ->toArray();
            
            if (empty($karyawanIds)) {
                continue;
            }
            
            $penilaianList = PenilaianKpa::with('indikator.aspek')
                ->whereIn('karyawan_id', $karyawanIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()
                ->groupBy('karyawan_id');
            
            $totalNilai = 0;
            $jumlahKaryawan = 0;
            
            foreach ($karyawanIds as $karyawanId) {
                $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
                $nilaiKaryawan = $this->hitungTotalNilaiKPA($karyawanPenilaian, $indikators);
                
                if ($nilaiKaryawan > 0) {
                    $totalNilai += $nilaiKaryawan;
                    $jumlahKaryawan++;
                }
            }
            
            $rataNilai = $jumlahKaryawan > 0 ? $totalNilai / $jumlahKaryawan : 0;
            
            $result[] = [
                'id' => $divisi->id,
                'nama' => $divisi->divisi,
                'nilai_rata_rata' => round($rataNilai, 1),
                'grade' => $this->getGrade($rataNilai),
                'jumlah_karyawan' => count($karyawanIds),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => collect($result)->sortByDesc('nilai_rata_rata')->values(),
        ]);
    }
}