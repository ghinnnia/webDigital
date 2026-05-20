<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use App\Models\KinerjaPegawai;
use App\Models\Layanan;
use App\Models\Project;
use Illuminate\Http\Request;

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
                $q->where('nama', 'like', '%' . $request->search . '%')
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
     * Halaman Top & Low Grade
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        
        // Query managers
        $managers = User::where('role', 'manager_divisi')
            ->with(['divisi'])
            ->get()
            ->map(function($manager) use ($bulan, $tahun) {
                $kinerja = KinerjaPegawai::where('karyawan_id', $manager->id)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();
                return [
                    'name' => $manager->name,
                    'divisi' => $manager->divisi->divisi ?? '-',
                    'nilai' => $kinerja->nilai_rata_rata ?? 0,
                    'grade' => $kinerja->grade ?? '-'
                ];
            })
            ->filter(fn($item) => $item['nilai'] > 0);
        
        $topManagers = $managers->sortByDesc('nilai')->take(5)->values();
        $lowManagers = $managers->sortBy('nilai')->take(5)->values();
        
        // Query Divisi
        $divisions = Divisi::all()->map(function($divisi) use ($bulan, $tahun) {
            $karyawanIds = User::where('divisi_id', $divisi->id)
                ->where('role', 'karyawan')
                ->pluck('id');
                
            $kinerja = KinerjaPegawai::whereIn('karyawan_id', $karyawanIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get();
            
            $nilaiRataRata = $kinerja->avg('nilai_rata_rata') ?? 0;
            
            // Determine grade
            $grade = '-';
            if ($nilaiRataRata >= 90) $grade = 'A';
            elseif ($nilaiRataRata >= 75) $grade = 'B';
            elseif ($nilaiRataRata >= 60) $grade = 'C';
            elseif ($nilaiRataRata > 0) $grade = 'D';
            
            return [
                'nama' => $divisi->divisi,
                'jumlah_karyawan' => $karyawanIds->count(),
                'nilai_rata_rata' => $nilaiRataRata,
                'grade' => $grade
            ];
        })->filter(fn($item) => $item['nilai_rata_rata'] > 0);
        
        $topDivisi = $divisions->sortByDesc('nilai_rata_rata')->take(5)->values();
        $lowDivisi = $divisions->sortBy('nilai_rata_rata')->take(5)->values();
        
        // Statistik
        $statistik = [
            'total_manager' => User::where('role', 'manager_divisi')->count(),
            'total_divisi' => Divisi::count(),
            'rata_rata_manager' => $managers->avg('nilai') ?? 0,
            'rata_rata_divisi' => $divisions->avg('nilai_rata_rata') ?? 0,
        ];
        
        return view('general_manajer.top_low_grade', compact('topManagers', 'lowManagers', 'topDivisi', 'lowDivisi', 'statistik', 'bulan', 'tahun'));
    }
    
    public function managerRanking(Request $request)
    {
        // API
    }
    
    public function divisiRanking(Request $request)
    {
        // API
    }
}