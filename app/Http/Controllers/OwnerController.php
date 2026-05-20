<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnerController extends Controller
{
    /**
     * Mendapatkan data owner yang sedang login
     */
    public function getData()
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'id' => $user->id,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data owner: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Persentase kehadiran karyawan
     */
    public function getAttendancePercentage()
    {
        try {
            $tanggal = \Carbon\Carbon::today()->format('Y-m-d');
            
            // Total karyawan aktif
            $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
            
            if ($totalKaryawan === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'persentase' => 0,
                        'hadir' => 0,
                        'total' => 0
                    ]
                ]);
            }
            
            // Kehadiran hari ini
            $hadirHariIni = \App\Models\Absensi::whereDate('tanggal', $tanggal)
                ->whereNotNull('jam_masuk')
                ->count();
            
            $persentase = round(($hadirHariIni / $totalKaryawan) * 100);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'persentase' => $persentase,
                    'hadir' => $hadirHariIni,
                    'total' => $totalKaryawan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kehadiran'
            ], 500);
        }
    }

    /**
     * API: Kehadiran per divisi (untuk modal popup)
     */
    public function getAttendanceByDivision()
    {
        try {
            $tanggal = \Carbon\Carbon::today()->format('Y-m-d');
            
            $divisions = \App\Models\User::select('divisi')
                ->whereNotNull('divisi')
                ->where('role', 'karyawan')
                ->distinct()
                ->pluck('divisi');

            $result = [];

            foreach ($divisions as $division) {
                $totalInDivision = \App\Models\User::where('divisi', $division)
                    ->where('role', 'karyawan')
                    ->count();

                if ($totalInDivision === 0) {
                    continue;
                }
                
                $hadirInDivision = \App\Models\Absensi::whereHas('user', function($q) use ($division) {
                        $q->where('divisi', $division);
                    })
                    ->whereDate('tanggal', $tanggal)
                    ->whereNotNull('jam_masuk')
                    ->count();

                $persentase = round(($hadirInDivision / $totalInDivision) * 100);

                $result[] = [
                    'divisi' => $division,
                    'total' => $totalInDivision,
                    'hadir' => $hadirInDivision,
                    'tidak_hadir' => $totalInDivision - $hadirInDivision,
                    'persentase' => $persentase
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kehadiran per divisi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Jumlah layanan
     */
    public function getServiceCount()
    {
        try {
            $jumlah = \App\Models\Layanan::count();
            
            return response()->json([
                'success' => true,
                'data' => $jumlah
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jumlah layanan'
            ], 500);
        }
    }

    /**
     * API: Statistik keuangan - Match dengan Finance Dashboard
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $period = $request->get('period', 'weekly'); // weekly, monthly, yearly
            
            // Ambil total pemasukan dan pengeluaran dari Cashflow
            $totalPemasukan = (int)\App\Models\Cashflow::where('tipe_transaksi', 'pemasukan')
                ->sum('jumlah');
            $totalPengeluaran = (int)\App\Models\Cashflow::where('tipe_transaksi', 'pengeluaran')
                ->sum('jumlah');
            $totalKeuntungan = $totalPemasukan - $totalPengeluaran;
            
            $pemasukanPerPeriod = [];
            $pengeluaranPerPeriod = [];
            $labels = [];
            
            if ($period === 'weekly') {
                // Minggu ini: Senin sampai Minggu (kembalikan label sebagai tanggal agar grafik cocok dengan jumlah)
                $pemasukanPerPeriod = array_fill(0, 7, 0);
                $pengeluaranPerPeriod = array_fill(0, 7, 0);

                $now = \Carbon\Carbon::now();
                // Gunakan startOfWeek/endOfWeek untuk memperjelas rentang (Senin sampai Minggu)
                $startOfWeek = $now->copy()->startOfWeek(); // default Monday
                $endOfWeek = $now->copy()->endOfWeek(); // Sunday

                // Buat label berupa tanggal singkat (contoh: 08 Feb)
                $labels = [];
                for ($i = 0; $i < 7; $i++) {
                    $labels[] = $startOfWeek->copy()->addDays($i)->format('d M');
                }

                // Ambil semua transaksi minggu ini
                $transactions = \App\Models\Cashflow::whereBetween('tanggal_transaksi', [$startOfWeek->startOfDay(), $endOfWeek->endOfDay()])->get();

                foreach ($transactions as $transaction) {
                    $date = \Carbon\Carbon::parse($transaction->tanggal_transaksi);
                    // Normalisasi indeks ke 0..6 (Senin=0 ... Minggu=6)
                    $dayOfWeek = $date->dayOfWeek; // 0=Sun,1=Mon,...6=Sat
                    $adjustedDay = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1; // convert to Mon=0

                    if ($adjustedDay >= 0 && $adjustedDay < 7) {
                        if ($transaction->tipe_transaksi === 'pemasukan') {
                            $pemasukanPerPeriod[$adjustedDay] += (int)$transaction->jumlah;
                        } else if ($transaction->tipe_transaksi === 'pengeluaran') {
                            $pengeluaranPerPeriod[$adjustedDay] += (int)$transaction->jumlah;
                        }
                    }
                }
            } elseif ($period === 'monthly') {
                // Bulan ini: Minggu 1 sampai 4
                $now = \Carbon\Carbon::now();
                $startOfMonth = $now->copy()->startOfMonth();
                $endOfMonth = $now->copy()->endOfMonth();
                $totalWeeks = ceil(($endOfMonth->day + $startOfMonth->dayOfWeek) / 7);
                
                $labels = [];
                for ($i = 1; $i <= $totalWeeks; $i++) {
                    $labels[] = "Minggu $i";
                }
                
                $pemasukanPerPeriod = array_fill(0, $totalWeeks, 0);
                $pengeluaranPerPeriod = array_fill(0, $totalWeeks, 0);
                
                // Ambil semua transaksi bulan ini
                $transactions = \App\Models\Cashflow::whereYear('tanggal_transaksi', $now->year)
                    ->whereMonth('tanggal_transaksi', $now->month)
                    ->get();
                
                foreach ($transactions as $transaction) {
                    $date = \Carbon\Carbon::parse($transaction->tanggal_transaksi);
                    $weekIndex = min(floor(($date->day - 1) / 7), $totalWeeks - 1);
                    
                    if ($transaction->tipe_transaksi === 'pemasukan') {
                        $pemasukanPerPeriod[$weekIndex] += (int)$transaction->jumlah;
                    } else if ($transaction->tipe_transaksi === 'pengeluaran') {
                        $pengeluaranPerPeriod[$weekIndex] += (int)$transaction->jumlah;
                    }
                }
            } elseif ($period === 'yearly') {
                // Tahun ini: Jan sampai Des
                $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $pemasukanPerPeriod = array_fill(0, 12, 0);
                $pengeluaranPerPeriod = array_fill(0, 12, 0);
                
                $now = \Carbon\Carbon::now();
                
                // Ambil semua transaksi tahun ini
                $transactions = \App\Models\Cashflow::whereYear('tanggal_transaksi', $now->year)->get();
                
                foreach ($transactions as $transaction) {
                    $date = \Carbon\Carbon::parse($transaction->tanggal_transaksi);
                    $monthIndex = $date->month - 1; // 0-11
                    
                    if ($transaction->tipe_transaksi === 'pemasukan') {
                        $pemasukanPerPeriod[$monthIndex] += (int)$transaction->jumlah;
                    } else if ($transaction->tipe_transaksi === 'pengeluaran') {
                        $pengeluaranPerPeriod[$monthIndex] += (int)$transaction->jumlah;
                    }
                }
            }
            
            Log::info('Dashboard Stats - Period: ' . $period . ', Total Pemasukan: ' . $totalPemasukan . ', Total Pengeluaran: ' . $totalPengeluaran);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_pengeluaran' => $totalPengeluaran,
                    'total_keuntungan' => $totalKeuntungan,
                    'pemasukan_per_bulan' => $pemasukanPerPeriod,
                    'pemasukan_per_periode' => $pemasukanPerPeriod,
                    'pengeluaran_per_bulan' => $pengeluaranPerPeriod,
                    'pengeluaran_per_periode' => $pengeluaranPerPeriod,
                    'labels' => $labels
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getDashboardStats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik keuangan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Tanggal-tanggal yang memiliki catatan rapat
     */
    public function getMeetingNotesDates()
    {
        try {
            $dates = \App\Models\CatatanRapat::select('tanggal')
                ->distinct()
                ->pluck('tanggal');
            
            return response()->json([
                'success' => true,
                'data' => $dates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tanggal catatan rapat'
            ], 500);
        }
    }

    /**
     * API: Tanggal-tanggal yang memiliki pengumuman
     */
    public function getAnnouncementsDates()
    {
        try {
            $dates = \App\Models\Pengumuman::select('created_at')
                ->distinct()
                ->pluck('created_at');
            
            return response()->json([
                'success' => true,
                'data' => $dates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tanggal pengumuman'
            ], 500);
        }
    }

    /**
     * API: Catatan rapat pada tanggal tertentu
     */
    public function getMeetingNotes(Request $request)
    {
        try {
            $tanggal = $request->get('date', \Carbon\Carbon::today()->format('Y-m-d'));
            
            $notes = \App\Models\CatatanRapat::whereDate('tanggal', $tanggal)
                ->get(['id', 'topik', 'hasil_diskusi', 'keputusan', 'tanggal']);
            
            return response()->json([
                'success' => true,
                'data' => $notes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil catatan rapat'
            ], 500);
        }
    }

    /**
     * API: Semua pengumuman
     */
    public function getAnnouncements()
    {
        try {
            $announcements = \App\Models\Pengumuman::orderBy('created_at', 'desc')
                ->limit(20)
                ->get(['id', 'judul', 'isi_pesan', 'created_at']);
            
            return response()->json([
                'success' => true,
                'data' => $announcements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil pengumuman'
            ], 500);
        }
    }

    /**
     * Menampilkan laporan umum (dengan data pemasukan dan keuangan)
     */
    public function laporan(Request $request)
    {
        // Ambil data Cashflow untuk statistik keseluruhan
        $totalPemasukan = \App\Models\Cashflow::where('tipe_transaksi', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = \App\Models\Cashflow::where('tipe_transaksi', 'pengeluaran')->sum('jumlah');
        $totalKeuntungan = $totalPemasukan - $totalPengeluaran;

        // Ambil semua data finance (Cashflow) untuk tabel data keuangan
        $financeDataRaw = \App\Models\Cashflow::with('kategori')->orderBy('tanggal_transaksi', 'desc')->get();
        $financeData = $financeDataRaw->map(function($item) {
            return [
                'id' => $item->id,
                'tanggal_transaksi' => $item->tanggal_transaksi->format('Y-m-d'),
                'nama_transaksi' => $item->nama_transaksi,
                'deskripsi' => $item->deskripsi,
                'jumlah' => (int)$item->jumlah,
                'tipe_transaksi' => $item->tipe_transaksi,
                'kategori' => $item->kategori ? $item->kategori->nama_kategori : ($item->subkategori ?? 'Lainnya'),
                'subkategori' => $item->subkategori
            ];
        })->toArray();

        // Ambil data pemasukan dari FinanceTransaction untuk bulan ini
        $bulanIni = \Carbon\Carbon::now();
        $pemasukan = \App\Models\FinanceTransaction::where('tipe', 'income')
            ->whereYear('tanggal', $bulanIni->year)
            ->whereMonth('tanggal', $bulanIni->month)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Hitung statistik pemasukan bulan ini
        $totalPemasukanBulanIni = $pemasukan->sum('jumlah');
        $jumlahTransaksiBulanIni = $pemasukan->count();

        // Pemasukan per kategori
        $pemasukanPerKategori = $pemasukan->groupBy('kategori')->map(function($items) {
            return [
                'total' => $items->sum('jumlah'),
                'jumlah' => $items->count()
            ];
        })->sortByDesc('total');

        // Ambil daftar kategori unik dari financeData (sudah dalam format array)
        $uniqueKategori = collect($financeData)->pluck('kategori')->unique()->values()->toArray();
        
        // Buat array kategori dengan format yang sama seperti KategoriCashflow
        $allKategori = array_map(function($kategori) {
            return (object)['nama_kategori' => $kategori];
        }, $uniqueKategori);

        return view('pemilik.laporan', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'totalKeuntungan',
            'financeData',
            'pemasukan',
            'totalPemasukanBulanIni',
            'jumlahTransaksiBulanIni',
            'pemasukanPerKategori',
            'allKategori'
        ));
    }

    /**
     * Export laporan ke PDF
     */
    public function laporanPdf(Request $request)
    {
        $type = $request->get('type', 'all'); // all|pemasukan|pengeluaran
        $search = trim((string) $request->get('search', ''));
        $kategoriParam = $request->get('kategori', '');
        $kategoriList = [];

        if (is_array($kategoriParam)) {
            $kategoriList = array_filter($kategoriParam);
        } elseif (is_string($kategoriParam) && $kategoriParam !== '') {
            $kategoriList = array_filter(array_map('trim', explode(',', $kategoriParam)));
        }

        $query = \App\Models\Cashflow::with('kategori')->orderBy('tanggal_transaksi', 'desc');

        if (in_array($type, ['pemasukan', 'pengeluaran'], true)) {
            $query->where('tipe_transaksi', $type);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_transaksi', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($k) use ($search) {
                        $k->where('nama_kategori', 'like', "%{$search}%");
                    })
                    ->orWhere('subkategori', 'like', "%{$search}%");
            });
        }

        if (!empty($kategoriList)) {
            $query->where(function ($q) use ($kategoriList) {
                $q->whereHas('kategori', function ($k) use ($kategoriList) {
                    $k->whereIn('nama_kategori', $kategoriList);
                })
                ->orWhereIn('subkategori', $kategoriList);
            });
        }

        $financeDataRaw = $query->get();
        $financeData = $financeDataRaw->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal_transaksi' => $item->tanggal_transaksi ? $item->tanggal_transaksi->format('Y-m-d') : null,
                'nama_transaksi' => $item->nama_transaksi,
                'deskripsi' => $item->deskripsi,
                'jumlah' => (int) $item->jumlah,
                'tipe_transaksi' => $item->tipe_transaksi,
                'kategori' => $item->kategori ? $item->kategori->nama_kategori : ($item->subkategori ?? 'Lainnya'),
                'subkategori' => $item->subkategori
            ];
        })->toArray();

        $totalPemasukan = (int) $financeDataRaw->where('tipe_transaksi', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = (int) $financeDataRaw->where('tipe_transaksi', 'pengeluaran')->sum('jumlah');
        $totalKeuntungan = $totalPemasukan - $totalPengeluaran;

        $generatedAt = \Carbon\Carbon::now()->format('d M Y H:i');

        $pdf = Pdf::loadView('pemilik.laporan_pdf', [
            'financeData' => $financeData,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalKeuntungan' => $totalKeuntungan,
            'generatedAt' => $generatedAt,
            'filterType' => $type,
            'filterKategori' => $kategoriList,
            'searchTerm' => $search
        ])->setPaper('A4', 'portrait');

        $filename = 'laporan-owner-' . date('Ymd-His') . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Menampilkan laporan pemasukan
     */
    public function laporanPemasukan(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));
        $kategoriFilter = $request->get('kategori', '');

        Log::info("Laporan Pemasukan - tanggalMulai: $tanggalMulai, tanggalAkhir: $tanggalAkhir, kategori: $kategoriFilter");

        // Query pemasukan dari finance transactions
        $pemasukan = \App\Models\FinanceTransaction::where('tipe', 'income')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);

        // Ambil daftar kategori unik
        $kategoriList = \App\Models\FinanceTransaction::where('tipe', 'income')
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (!empty($kategoriFilter)) {
            $pemasukan->where('kategori', $kategoriFilter);
        }

        $pemasukan = $pemasukan->orderBy('tanggal', 'desc')->get();

        // Hitung statistik
        $totalPemasukan = $pemasukan->sum('jumlah');
        $jumlahTransaksi = $pemasukan->count();
        $rataRataPemasukan = $jumlahTransaksi > 0 ? $totalPemasukan / $jumlahTransaksi : 0;

        // Kelompokkan berdasarkan kategori
        $pemasukanPerKategori = $pemasukan->groupBy('kategori')->map(function($items) {
            return [
                'total' => $items->sum('jumlah'),
                'jumlah' => $items->count()
            ];
        });

        Log::info("Laporan Pemasukan - Total: $totalPemasukan, Transaksi: $jumlahTransaksi");

        return view('pemilik.laporan_pemasukan', compact(
            'pemasukan', 
            'kategoriList', 
            'kategoriFilter', 
            'tanggalMulai', 
            'tanggalAkhir',
            'totalPemasukan',
            'jumlahTransaksi',
            'rataRataPemasukan',
            'pemasukanPerKategori'
        ));
    }
}
