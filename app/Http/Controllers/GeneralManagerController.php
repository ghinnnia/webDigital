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
     * Halaman Beranda (Dashboard Utama)
     */
    public function home()
    {
        $totalKaryawan = User::whereIn('role', ['karyawan', 'manager_divisi'])->count();
        $totalLayanan  = Layanan::count();
        $totalProject  = Project::count();

        $divisionsDropdown = DB::table('divisi')
                               ->select('id', 'divisi')
                               ->orderBy('divisi', 'asc')
                               ->get();

        return view('general_manajer.home', compact('totalKaryawan', 'totalLayanan', 'totalProject', 'divisionsDropdown'));
    }

    /**
     * Get data karyawan untuk dashboard (API)
     */
    public function getKaryawanData(Request $request)
    {
        try {
            $karyawan = User::whereIn('role', ['karyawan', 'manager_divisi'])->get();

            return response()->json([
                'success' => true,
                'data'    => $karyawan,
                'total'   => $karyawan->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get dashboard stats untuk GM (API)
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $stats = [
                'total_karyawan' => User::whereIn('role', ['karyawan', 'manager_divisi'])->count(),
                'total_layanan'  => Layanan::count(),
                'total_project'  => Project::count(),
                'total_divisi'   => DB::table('divisi')->count(),
                'total_manager'  => User::where('role', 'manager_divisi')->count(),
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ============================================================
     * Halaman Data Karyawan — FIXED
     * ============================================================
     */
    public function data_karyawan(Request $request)
    {
        $search   = trim($request->get('search', ''));
        $divisiId = $request->get('divisi', '');

        // Menggunakan Eloquent model User dengan leftJoin ke tabel divisi
        // untuk mendapatkan nama divisi dan menghindari konflik relasi.
        $query = User::leftJoin('divisi', 'users.divisi_id', '=', 'divisi.id')
            ->select(
                'users.*',
                'users.name          as nama',
                'divisi.divisi       as nama_divisi'  // alias berbeda agar tidak konflik
            )
            ->whereIn('users.role', ['karyawan', 'manager_divisi']);

        // Filter search: nama atau email
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('users.name',  'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%");
            });
        }

        // Filter divisi
        if ($divisiId !== '') {
            $query->where('users.divisi_id', $divisiId);
        }

        $karyawan = $query->orderBy('users.name', 'asc')
                          ->paginate(10)
                          ->withQueryString();

        // Ambil semua data divisi untuk dropdown filter
        $divisionsDropdown = Divisi::orderBy('divisi', 'asc')->get();

        return view('general_manajer.data_karyawan', compact('karyawan', 'divisionsDropdown'));
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
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $projects  = $query->paginate(10)->withQueryString();
        $managers  = User::where('role', 'manager_divisi')->get();
        $karyawans = User::where('role', 'karyawan')->get();

        return view('general_manajer.data_project', compact('projects', 'managers', 'karyawans'));
    }

    /**
     * Update Penanggung Jawab Project
     */
    public function update_project(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $project->update([
            'penanggung_jawab_id'           => $request->penanggung_jawab_id,
            'penanggung_jawab_ids'          => $request->penanggung_jawab_ids,
            'karyawan_penanggung_jawab_id'  => $request->karyawan_penanggung_jawab_id,
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
     * Hitung total nilai KPA dari indikator yang ADA nilainya
     */
    private function hitungTotalNilaiKPA($penilaianList, $indikators)
    {
        $totalNilai = 0;
        $totalBobot = 0;

        foreach ($indikators as $indikator) {
            $penilaian = $penilaianList->firstWhere('indikator_id', $indikator->id);
            $nilai     = $penilaian ? $penilaian->nilai : null;

            if ($indikator->aspek && $nilai !== null && $nilai >= 0) {
                $kontribusi  = ($nilai / 100) * $indikator->bobot * ($indikator->aspek->bobot / 100);
                $totalNilai += $kontribusi;
                $totalBobot += $indikator->bobot * ($indikator->aspek->bobot / 100);
            }
        }

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
     * Dapatkan nama divisi dari ID — pakai DB::table agar aman
     */
    private function getNamaDivisi($divisiId)
    {
        if (!$divisiId) return '-';
        $divisi = DB::table('divisi')->where('id', $divisiId)->first();
        return $divisi ? $divisi->divisi : '-';
    }

    /**
     * Halaman Top & Low Grade
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $indikators = IndikatorKpa::with('aspek')->where('is_active', true)->get();

        // 1. DATA MANAGER DIVISI
        $managerDivisi = User::where('role', 'manager_divisi')
            ->where('status_kerja', 'aktif')
            ->get();

        $managerData = [];

        foreach ($managerDivisi as $manager) {
            $karyawanIds = Karyawan::where('divisi_id', $manager->divisi_id)
                ->where('status_kerja', 'aktif')
                ->pluck('user_id')
                ->toArray();

            if (empty($karyawanIds)) continue;

            $penilaianList = PenilaianKpa::with('indikator.aspek')
                ->whereIn('karyawan_id', $karyawanIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()
                ->groupBy('karyawan_id');

            $totalNilaiDivisi          = 0;
            $jumlahKaryawanDenganNilai = 0;

            foreach ($karyawanIds as $karyawanId) {
                $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
                $nilaiKaryawan     = $this->hitungTotalNilaiKPA($karyawanPenilaian, $indikators);

                if ($nilaiKaryawan > 0) {
                    $totalNilaiDivisi          += $nilaiKaryawan;
                    $jumlahKaryawanDenganNilai++;
                }
            }

            $rataNilaiManager = $jumlahKaryawanDenganNilai > 0
                ? round($totalNilaiDivisi / $jumlahKaryawanDenganNilai, 1)
                : 0;

            if ($rataNilaiManager > 0) {
                $managerData[] = [
                    'name'            => $manager->name,
                    'divisi'          => $this->getNamaDivisi($manager->divisi_id),
                    'nilai'           => $rataNilaiManager,
                    'grade'           => $this->getGrade($rataNilaiManager),
                    'jumlah_karyawan' => count($karyawanIds),
                ];
            }
        }

        $sortedManagers = collect($managerData)->sortByDesc('nilai')->values();
        $topManagers    = $sortedManagers->take(5);
        $lowManagers    = $sortedManagers->reverse()->take(5)->values();

        // 2. DATA DIVISI
        $divisiList = DB::table('divisi')->get();
        $divisiData = [];

        foreach ($divisiList as $divisi) {
            $karyawanIds = Karyawan::where('divisi_id', $divisi->id)
                ->where('status_kerja', 'aktif')
                ->pluck('user_id')
                ->toArray();

            if (empty($karyawanIds)) continue;

            $penilaianList = PenilaianKpa::with('indikator.aspek')
                ->whereIn('karyawan_id', $karyawanIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()
                ->groupBy('karyawan_id');

            $totalNilaiDivisi          = 0;
            $jumlahKaryawanDenganNilai = 0;

            foreach ($karyawanIds as $karyawanId) {
                $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
                $nilaiKaryawan     = $this->hitungTotalNilaiKPA($karyawanPenilaian, $indikators);

                if ($nilaiKaryawan > 0) {
                    $totalNilaiDivisi          += $nilaiKaryawan;
                    $jumlahKaryawanDenganNilai++;
                }
            }

            $rataNilaiDivisi = $jumlahKaryawanDenganNilai > 0
                ? round($totalNilaiDivisi / $jumlahKaryawanDenganNilai, 1)
                : 0;

            if ($rataNilaiDivisi > 0) {
                $divisiData[] = [
                    'nama'            => $divisi->divisi,
                    'jumlah_karyawan' => count($karyawanIds),
                    'nilai_rata_rata' => $rataNilaiDivisi,
                    'grade'           => $this->getGrade($rataNilaiDivisi),
                ];
            }
        }

        $sortedDivisi = collect($divisiData)->sortByDesc('nilai_rata_rata')->values();
        $topDivisi    = $sortedDivisi->take(5);
        $lowDivisi    = $sortedDivisi->reverse()->take(5)->values();

        $statistik = [
            'total_manager'    => User::where('role', 'manager_divisi')->count(),
            'total_divisi'     => DB::table('divisi')->count(),
            'rata_rata_manager' => $managerData ? round(collect($managerData)->avg('nilai'), 1) : 0,
            'rata_rata_divisi'  => $divisiData  ? round(collect($divisiData)->avg('nilai_rata_rata'), 1) : 0,
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

        $indikators    = IndikatorKpa::with('aspek')->where('is_active', true)->get();
        $managerDivisi = User::where('role', 'manager_divisi')->where('status_kerja', 'aktif')->get();

        $result = [];

        foreach ($managerDivisi as $manager) {
            $karyawanIds = Karyawan::where('divisi_id', $manager->divisi_id)
                ->where('status_kerja', 'aktif')
                ->pluck('user_id')
                ->toArray();

            if (empty($karyawanIds)) continue;

            $penilaianList = PenilaianKpa::with('indikator.aspek')
                ->whereIn('karyawan_id', $karyawanIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()
                ->groupBy('karyawan_id');

            $totalNilaiDivisi = 0;
            $jumlahKaryawan   = 0;

            foreach ($karyawanIds as $karyawanId) {
                $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
                $nilaiKaryawan     = $this->hitungTotalNilaiKPA($karyawanPenilaian, $indikators);

                if ($nilaiKaryawan > 0) {
                    $totalNilaiDivisi += $nilaiKaryawan;
                    $jumlahKaryawan++;
                }
            }

            $rataNilai = $jumlahKaryawan > 0 ? $totalNilaiDivisi / $jumlahKaryawan : 0;

            if ($rataNilai > 0) {
                $result[] = [
                    'id'              => $manager->id,
                    'name'            => $manager->name,
                    'divisi'          => $this->getNamaDivisi($manager->divisi_id),
                    'nilai'           => round($rataNilai, 1),
                    'grade'           => $this->getGrade($rataNilai),
                    'jumlah_karyawan' => count($karyawanIds),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => collect($result)->sortByDesc('nilai')->values(),
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
        $divisiList = DB::table('divisi')->get();

        $result = [];

        foreach ($divisiList as $divisi) {
            $karyawanIds = Karyawan::where('divisi_id', $divisi->id)
                ->where('status_kerja', 'aktif')
                ->pluck('user_id')
                ->toArray();

            if (empty($karyawanIds)) continue;

            $penilaianList = PenilaianKpa::with('indikator.aspek')
                ->whereIn('karyawan_id', $karyawanIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()
                ->groupBy('karyawan_id');

            $totalNilai     = 0;
            $jumlahKaryawan = 0;

            foreach ($karyawanIds as $karyawanId) {
                $karyawanPenilaian = $penilaianList[$karyawanId] ?? collect();
                $nilaiKaryawan     = $this->hitungTotalNilaiKPA($karyawanPenilaian, $indikators);

                if ($nilaiKaryawan > 0) {
                    $totalNilai += $nilaiKaryawan;
                    $jumlahKaryawan++;
                }
            }

            $rataNilai = $jumlahKaryawan > 0 ? $totalNilai / $jumlahKaryawan : 0;

            if ($rataNilai > 0) {
                $result[] = [
                    'id'              => $divisi->id,
                    'nama'            => $divisi->divisi,
                    'nilai_rata_rata' => round($rataNilai, 1),
                    'grade'           => $this->getGrade($rataNilai),
                    'jumlah_karyawan' => count($karyawanIds),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => collect($result)->sortByDesc('nilai_rata_rata')->values(),
        ]);
    }
}