<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Task;
use App\Models\AspekKpa;
use App\Models\IndikatorKpa;
use App\Models\PenilaianKpa;
use App\Models\TargetKuantitas;
use App\Models\KpaSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HRKPAController extends Controller
{
    /**
     * Dashboard KPA HR
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $divisiId = $request->get('divisi_id');
        $viewType = $request->get('view', 'bulanan');

        $queryKaryawan = User::where('role', 'karyawan')->with('divisi');
        if ($divisiId && $divisiId != '') {
            $queryKaryawan->where('divisi_id', $divisiId);
        }
        $karyawan = $queryKaryawan->get();

        // Hitung KPA untuk setiap karyawan
        foreach ($karyawan as $k) {
            $this->hitungKpaOtomatis($k->id, $bulan, $tahun);
        }

        // Ambil data KPA
        $kinerjaList = $this->getKpaData($bulan, $tahun, $divisiId, $viewType);
        $stats = $this->getStatistics($kinerjaList);
        $divisiList = Divisi::orderBy('divisi')->get();
        
        // 🔥 TAMBAHKAN INI - Ambil semua aspek untuk ditampilkan di tabel
        $aspekList = AspekKpa::with('indikator')->where('is_active', true)->orderBy('urutan')->get();

        return view('hr.kpa.index', compact('kinerjaList', 'stats', 'bulan', 'tahun', 'divisiList', 'aspekList', 'viewType'));
    }

    /**
     * Hitung KPA Otomatis (Target Kerja, Ketepatan, Kuantitas)
     */
    public function hitungKpaOtomatis($karyawanId, $bulan, $tahun)
    {
        // Ambil tugas (deadline bulan ini ATAU selesai bulan ini)
        $tasks = Task::where(function($query) use ($karyawanId) {
                $query->where('assigned_to', $karyawanId)
                      ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [(string)$karyawanId]);
            })
            ->where(function($query) use ($bulan, $tahun) {
                $query->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun)
                      ->orWhere(function($q) use ($bulan, $tahun) {
                          $q->whereMonth('completed_at', $bulan)->whereYear('completed_at', $tahun);
                      });
            })->get();

        $totalTugas = $tasks->count();
        $tugasSelesai = $tasks->where('status', 'selesai')->count();

        // ============================================================
        // 1. KETEPATAN WAKTU
        // ============================================================
        $totalNilaiKetepatan = 0;
        if ($totalTugas > 0) {
            foreach ($tasks as $task) {
                $deadline = Carbon::parse($task->deadline);
                if ($task->status == 'selesai' && $task->completed_at) {
                    $completed = Carbon::parse($task->completed_at);
                    if ($completed->lte($deadline)) {
                        $totalNilaiKetepatan += 100;
                    } else {
                        $hariTelat = $deadline->diffInDays($completed);
                        $totalNilaiKetepatan += max(0, 100 - ($hariTelat * 10));
                    }
                } elseif ($deadline->isPast()) {
                    $totalNilaiKetepatan += 0;
                } else {
                    $totalNilaiKetepatan += 100;
                }
            }
            $nilaiKetepatan = $totalNilaiKetepatan / $totalTugas;
        } else {
            $nilaiKetepatan = 100;
        }

        // ============================================================
        // 2. TARGET KERJA (dari target_kuantitas)
        // ============================================================
        $tk = TargetKuantitas::where([
            'karyawan_id' => $karyawanId, 
            'bulan' => $bulan, 
            'tahun' => $tahun
        ])->first();
        
        if ($tk && $tk->target > 0) {
            // Update realisasi dengan tugas selesai
            $tk->update(['realisasi' => $tugasSelesai]);
            $nilaiTarget = min(100, ($tugasSelesai / $tk->target) * 100);
        } else {
            $nilaiTarget = 100; // Jika belum ada target, default 100
        }

        // ============================================================
        // 3. KUANTITAS KERJA
        // ============================================================
        $nilaiKuantitas = ($totalTugas > 0) ? ($tugasSelesai / $totalTugas) * 100 : 100;
        $nilaiKuantitas = min(100, round($nilaiKuantitas, 2));

        // Simpan ke database
        $this->savePenilaian($karyawanId, 'Ketepatan Waktu', $bulan, $tahun, $nilaiKetepatan);
        $this->savePenilaian($karyawanId, 'Target Kerja', $bulan, $tahun, $nilaiTarget);
        $this->savePenilaian($karyawanId, 'Kuantitas Kerja', $bulan, $tahun, $nilaiKuantitas);

        Log::info('KPA Otomatis', [
            'karyawan_id' => $karyawanId,
            'target_kerja' => round($nilaiTarget, 2),
            'ketepatan' => round($nilaiKetepatan, 2),
            'kuantitas' => $nilaiKuantitas
        ]);

        return true;
    }

    private function savePenilaian($karyawanId, $namaIndikator, $bulan, $tahun, $nilai)
    {
        $ind = IndikatorKpa::where('nama', $namaIndikator)->first();
        if ($ind) {
            PenilaianKpa::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId, 
                    'indikator_id' => $ind->id, 
                    'bulan' => $bulan, 
                    'tahun' => $tahun
                ],
                ['nilai' => round($nilai, 2), 'catatan' => 'Otomatis Sistem']
            );
        }
    }

    /**
     * Ambil data KPA (Dynamic Mapping)
     */
    private function getKpaData($bulan, $tahun, $divisiId, $viewType = 'bulanan')
    {
        $queryKaryawan = User::where('role', 'karyawan')->with('divisi');
        if ($divisiId && $divisiId != '') {
            $queryKaryawan->where('divisi_id', $divisiId);
        }
        $karyawanList = $queryKaryawan->get();
        
        // Ambil semua indikator aktif
        $allIndikatorNames = IndikatorKpa::where('is_active', true)->pluck('nama')->toArray();
        $result = [];
        
        foreach ($karyawanList as $karyawan) {
            // Ambil penilaian
            $penilaianQuery = PenilaianKpa::with('indikator.aspek')
                ->where('karyawan_id', $karyawan->id);
            
            if ($viewType == 'bulanan') {
                $penilaianQuery->where('bulan', $bulan)->where('tahun', $tahun);
            }
            
            $penilaianList = $penilaianQuery->get();
            
            // Inisialisasi semua indikator dengan null
            $penilaianDetail = array_fill_keys($allIndikatorNames, null);
            $totalAkhir = 0;
            
            foreach ($penilaianList as $p) {
                if (!$p->indikator || !$p->indikator->aspek) continue;
                
                $namaIndikator = $p->indikator->nama;
                $nilai = $p->nilai;
                $penilaianDetail[$namaIndikator] = $nilai;

                // 🔥 PERBAIKAN RUMUS KONTRIBUSI
                // Kontribusi = (Nilai / 100) × Bobot Indikator × (Bobot Aspek / 100)
                $kontribusi = ($nilai / 100) * $p->indikator->bobot * ($p->indikator->aspek->bobot / 100);
                $totalAkhir += $kontribusi;
            }
            
            $totalAkhir = min(100, round($totalAkhir, 2));
            
            $result[] = [
                'karyawan' => $karyawan,
                'penilaian' => $penilaianDetail,
                'total_nilai' => $totalAkhir,
                'grade' => $this->getGrade($totalAkhir)
            ];
        }
        return $result;
    }

    /**
     * Halaman Setting Target Kuantitas
     */
    public function targetKuantitasIndex(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $divisiId = $request->get('divisi_id');

        $queryKaryawan = User::where('role', 'karyawan')->with('divisi');
        if ($divisiId && $divisiId != '') {
            $queryKaryawan->where('divisi_id', $divisiId);
        }
        $karyawan = $queryKaryawan->get();

        $targetExisting = TargetKuantitas::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('karyawan_id');

        $divisiList = Divisi::orderBy('divisi')->get();

        return view('hr.kpa.target_kuantitas', compact('karyawan', 'targetExisting', 'bulan', 'tahun', 'divisiList'));
    }

    public function targetKuantitasStore(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'target' => 'required|array'
        ]);

        foreach ($request->target as $karyawanId => $target) {
            TargetKuantitas::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId, 
                    'bulan' => $request->bulan, 
                    'tahun' => $request->tahun
                ],
                ['target' => $target, 'realisasi' => $request->realisasi[$karyawanId] ?? 0]
            );
            $this->hitungKpaOtomatis($karyawanId, $request->bulan, $request->tahun);
        }

        return redirect()->back()->with('success', 'Target kuantitas berhasil disimpan');
    }

    /**
     * Form Penilaian Manual
     */
    public function formPenilaian(Request $request)
    {
        $karyawanId = $request->get('karyawan_id');
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $karyawan = User::findOrFail($karyawanId);
        
        $indikatorManual = IndikatorKpa::with('aspek')
            ->where('tipe', 'manual')
            ->where('is_active', true)
            ->get();
            
        $nilaiExisting = PenilaianKpa::where([
            'karyawan_id' => $karyawanId, 
            'bulan' => $bulan, 
            'tahun' => $tahun
        ])->get()->keyBy('indikator_id');

        return view('hr.kpa.penilaian_form', compact('karyawan', 'indikatorManual', 'nilaiExisting', 'bulan', 'tahun'));
    }

    public function simpanPenilaian(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:users,id',
            'nilai' => 'required|array',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer'
        ]);

        foreach ($request->nilai as $indikatorId => $nilai) {
            PenilaianKpa::updateOrCreate(
                [
                    'karyawan_id' => $request->karyawan_id, 
                    'indikator_id' => $indikatorId, 
                    'bulan' => $request->bulan, 
                    'tahun' => $request->tahun
                ],
                ['nilai' => $nilai, 'catatan' => $request->catatan[$indikatorId] ?? null]
            );
        }

        $this->hitungKpaOtomatis($request->karyawan_id, $request->bulan, $request->tahun);
        
        return redirect()->route('hr.kpa.index', [
            'bulan' => $request->bulan, 
            'tahun' => $request->tahun
        ])->with('success', 'Penilaian berhasil disimpan');
    }

    /**
     * ============================================================
     * MANAJEMEN ASPEK & INDIKATOR
     * ============================================================
     */

    public function aspekIndikatorIndex()
    {
        $aspekList = AspekKpa::with('indikator')->orderBy('urutan')->get();
        return view('hr.kpa.aspek_indikator', compact('aspekList'));
    }

    public function aspekStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'bobot' => 'required|integer|min:0|max:100'
        ]);
        
        $urutan = AspekKpa::max('urutan') + 1;
        
        AspekKpa::create([
            'nama' => $request->nama,
            'bobot' => $request->bobot,
            'urutan' => $urutan,
            'is_active' => true
        ]);
        
        return redirect()->back()->with('success', 'Aspek baru berhasil ditambahkan');
    }

    public function indikatorStore(Request $request)
    {
        $request->validate([
            'aspek_id' => 'required|exists:aspek_kpa,id',
            'nama' => 'required|string|max:255',
            'bobot' => 'required|integer|min:0|max:100',
            'tipe' => 'required|in:otomatis,manual'
        ]);

        IndikatorKpa::create([
            'aspek_id' => $request->aspek_id,
            'nama' => $request->nama,
            'bobot' => $request->bobot,
            'tipe' => $request->tipe,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Indikator baru berhasil ditambahkan');
    }

    public function aspekDestroy($id)
    {
        // Hapus semua indikator terkait
        IndikatorKpa::where('aspek_id', $id)->delete();
        // Hapus aspek
        AspekKpa::destroy($id);
        return redirect()->back()->with('success', 'Aspek berhasil dihapus');
    }

    public function indikatorDestroy($id)
    {
        // Hapus penilaian terkait
        PenilaianKpa::where('indikator_id', $id)->delete();
        // Hapus indikator
        IndikatorKpa::destroy($id);
        return redirect()->back()->with('success', 'Indikator berhasil dihapus');
    }

    /**
     * Hitung Ulang Semua Karyawan
     */
    public function hitungSemua(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $divisiId = $request->get('divisi_id');

        $query = User::where('role', 'karyawan')->with('divisi');
        if ($divisiId && $divisiId != '') {
            $query->where('divisi_id', $divisiId);
        }
        $karyawan = $query->get();
        
        foreach ($karyawan as $k) { 
            $this->hitungKpaOtomatis($k->id, $bulan, $tahun); 
        }
        
        return redirect()->back()->with('success', 'Berhasil menghitung ulang semua karyawan');
    }

    /**
     * Export PDF
     */
    public function exportPDF(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $divisiId = $request->get('divisi_id');
        $viewType = $request->get('view', 'bulanan');

        $kinerjaList = $this->getKpaData($bulan, $tahun, $divisiId, $viewType);
        
        $pdf = Pdf::loadView('hr.kpa.pdf', [
            'kinerjaList' => $kinerjaList, 
            'bulan' => $bulan, 
            'tahun' => $tahun,
            'viewType' => $viewType,
            'nama_bulan' => $this->getNamaBulan($bulan), 
            'tanggal_cetak' => now()->format('d-m-Y H:i:s')
        ])->setPaper('a4', 'landscape');
        
        $filename = $viewType == 'tahunan' 
            ? "Laporan_KPA_Tahunan_{$tahun}.pdf"
            : "Laporan_KPA_{$this->getNamaBulan($bulan)}_{$tahun}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Detail Riwayat Karyawan
     */
    public function detail($id)
    {
        $karyawan = User::with('divisi')->findOrFail($id);
        
        // Ambil semua riwayat penilaian
        $riwayatKinerja = PenilaianKpa::with('indikator.aspek')
            ->where('karyawan_id', $id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->tahun . '-' . $item->bulan;
            });

        return view('hr.kpa.detail', compact('karyawan', 'riwayatKinerja'));
    }

    /**
     * Show Detail Penilaian
     */
    public function show($id)
    {
        $kinerja = PenilaianKpa::with('karyawan.divisi', 'indikator.aspek')->findOrFail($id);
        return view('hr.kpa.show', compact('kinerja'));
    }

    /**
     * Preview Nilai (AJAX)
     */
    public function previewNilai(Request $request)
    {
        $karyawanId = $request->get('karyawan_id');
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        
        // Hitung nilai otomatis
        $tasks = Task::where(function($query) use ($karyawanId) {
                $query->where('assigned_to', $karyawanId)
                      ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [(string)$karyawanId]);
            })
            ->where(function($query) use ($bulan, $tahun) {
                $query->whereMonth('deadline', $bulan)->whereYear('deadline', $tahun)
                      ->orWhere(function($q) use ($bulan, $tahun) {
                          $q->whereMonth('completed_at', $bulan)->whereYear('completed_at', $tahun);
                      });
            })->get();

        $totalTugas = $tasks->count();
        $tugasSelesai = $tasks->where('status', 'selesai')->count();

        // Kuantitas Kerja
        $nilaiKuantitas = ($totalTugas > 0) ? ($tugasSelesai / $totalTugas) * 100 : 100;
        
        // Ketepatan Waktu
        $totalNilaiKetepatan = 0;
        foreach ($tasks as $task) {
            $deadline = Carbon::parse($task->deadline);
            if ($task->status == 'selesai' && $task->completed_at) {
                $completed = Carbon::parse($task->completed_at);
                if ($completed->lte($deadline)) {
                    $totalNilaiKetepatan += 100;
                } else {
                    $hariTelat = $deadline->diffInDays($completed);
                    $totalNilaiKetepatan += max(0, 100 - ($hariTelat * 10));
                }
            } elseif ($deadline->isPast()) {
                $totalNilaiKetepatan += 0;
            } else {
                $totalNilaiKetepatan += 100;
            }
        }
        $nilaiKetepatan = ($totalTugas > 0) ? $totalNilaiKetepatan / $totalTugas : 100;
        
        // Target Kerja
        $tk = TargetKuantitas::where(['karyawan_id' => $karyawanId, 'bulan' => $bulan, 'tahun' => $tahun])->first();
        if ($tk && $tk->target > 0) {
            $nilaiTarget = min(100, ($tugasSelesai / $tk->target) * 100);
        } else {
            $nilaiTarget = 100;
        }
        
        return response()->json([
            'success' => true,
            'kuantitas_kerja' => round($nilaiKuantitas, 2),
            'ketepatan_waktu' => round($nilaiKetepatan, 2),
            'target_kerja' => round($nilaiTarget, 2)
        ]);
    }

    // ============================================================
    // UTILITY FUNCTIONS
    // ============================================================

    private function getGrade($nilai) 
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        return 'D';
    }

    private function getStatistics($kinerjaList) 
    {
        $nilaiList = array_column($kinerjaList, 'total_nilai');
        return [
            'total' => count($kinerjaList),
            'rata_rata' => count($nilaiList) > 0 ? round(array_sum($nilaiList) / count($nilaiList), 2) : 0,
            'grade_a' => collect($kinerjaList)->where('grade', 'A')->count(),
            'grade_b' => collect($kinerjaList)->where('grade', 'B')->count(),
            'grade_c' => collect($kinerjaList)->where('grade', 'C')->count(),
            'grade_d' => collect($kinerjaList)->where('grade', 'D')->count(),
        ];
    }

    private function getNamaBulan($bulan) 
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $nama[(int)$bulan] ?? 'Januari';
    }
}