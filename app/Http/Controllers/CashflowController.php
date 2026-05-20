<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\KategoriCashflow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CashflowController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan halaman utama data keuangan.
     */
    public function index()
    {
        // Ambil semua data cashflow beserta relasi kategorinya, diurutkan dari yang terbaru
        $cashflowData = Cashflow::with('kategori')->orderBy('tanggal_transaksi', 'desc')->get();

        // Ambil semua kategori untuk dropdown
        $allKategori = KategoriCashflow::all();

        // Format data untuk dikirim ke JavaScript
        $formattedData = $cashflowData->map(function ($item) {
            return [
                'id' => $item->id, // Gunakan ID untuk identifikasi unik
                'nomor_transaksi' => $item->nomor_transaksi, // Tampilkan nomor transaksi
                'tanggal_transaksi' => $item->tanggal_transaksi->format('Y-m-d'),
                'nama_transaksi' => $item->nama_transaksi,
                // Jika subkategori ada (pengeluaran), tampilkan subkategori; jika tidak, tampilkan nama kategori dari DB
                'kategori' => $item->subkategori ?? ($item->kategori ? $item->kategori->nama_kategori : 'Tidak Diketahui'),
                'deskripsi' => $item->deskripsi,
                'jumlah' => $item->jumlah, // Kirim sebagai angka, format di JavaScript
                'tipe_transaksi' => $item->tipe_transaksi,
            ];
        });

        // Kirim data yang sudah diformat ke view
        return view('finance.pemasukan', [
            'financeData' => $formattedData,
            'allKategori' => $allKategori
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data dari form
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:income,expense',
            // Untuk tipe income: kategori_id harus ada di DB. Untuk expense: subkategori wajib.
            'kategori_id' => 'required_if:tipe,income|nullable|exists:kategori_cashflow,id',
            'subkategori' => 'required_if:tipe,expense|nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        // Siapkan data untuk disimpan, sesuaikan nama field form dengan nama kolom DB
        $dataToStore = [
            'tanggal_transaksi' => $validated['tanggal'],
            'tipe_transaksi' => $validated['tipe'] === 'income' ? 'pemasukan' : 'pengeluaran',
            'nama_transaksi' => $validated['nama'],
            'jumlah' => $validated['jumlah'],
            'deskripsi' => $validated['deskripsi'],
            // 'nomor_transaksi' akan diisi otomatis oleh model event
        ];

        // Untuk pemasukan, simpan kategori_id dari DB. Untuk pengeluaran, simpan subkategori teks.
        if ($validated['tipe'] === 'income') {
            $dataToStore['kategori_id'] = $validated['kategori_id'];
            $dataToStore['subkategori'] = null;
        } else {
            $dataToStore['kategori_id'] = null;
            $dataToStore['subkategori'] = $validated['subkategori'] ?? null;
        }

        // Simpan ke database
        Cashflow::create($dataToStore);

        // Redirect kembali ke halaman utama dengan pesan sukses
        return redirect()->back()
                         ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    /**
     * API Endpoint untuk mendapatkan kategori berdasarkan tipe.
     * Digunakan oleh JavaScript untuk mengisi dropdown kategori secara dinamis.
     */
    public function getKategoriByType($tipe)
    {
        // Mapping dari tipe di URL ('pemasukan'/'pengeluaran') ke tipe di database
        $tipeDatabase = $tipe;

        $kategoris = KategoriCashflow::where('tipe_kategori', $tipeDatabase)->get();

        // Kembalikan dalam format JSON
        return response()->json($kategoris);
    }

    /**
     * Update the specified cashflow entry.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:income,expense',
            'kategori_id' => 'required_if:tipe,income|nullable|exists:kategori_cashflow,id',
            'subkategori' => 'required_if:tipe,expense|nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $cashflow = Cashflow::findOrFail($id);

        $cashflow->tanggal_transaksi = $validated['tanggal'];
        $cashflow->tipe_transaksi = $validated['tipe'] === 'income' ? 'pemasukan' : 'pengeluaran';
        $cashflow->nama_transaksi = $validated['nama'];
        $cashflow->jumlah = $validated['jumlah'];
        $cashflow->deskripsi = $validated['deskripsi'] ?? null;

        if ($validated['tipe'] === 'income') {
            $cashflow->kategori_id = $validated['kategori_id'];
            $cashflow->subkategori = null;
        } else {
            $cashflow->kategori_id = null;
            $cashflow->subkategori = $validated['subkategori'] ?? null;
        }

        $cashflow->save();

        // Jika request AJAX ingin JSON, kembalikan JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaksi diperbarui', 'data' => $cashflow]);
        }

        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Remove the specified cashflow entry.
     */
    public function destroy(Request $request, $id)
    {
        $cashflow = Cashflow::find($id);
        if (!$cashflow) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        try {
            $cashflow->delete();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transaksi dihapus']);
            }
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus data'], 500);
            }
            return redirect()->back()->with('error', 'Gagal menghapus data');
        }
    }
}
