<?php

namespace App\Http\Controllers;

use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FinanceController extends Controller
{
    /**
     * Menampilkan halaman dan mengirim data ke View
     */
    public function index()
    {
        // Ambil semua data dari database
        $transactions = FinanceTransaction::orderBy('tanggal', 'desc')->get();

        // Format data agar sama persis dengan struktur 'financeData' di JavaScript Anda
        // Ini penting agar JavaScript (filter, tabel, pagination) bisa membacanya
        $financeData = $transactions->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal_transaksi' => $item->tanggal->format('Y-m-d'),
                'nama_transaksi' => $item->nama,
                'kategori' => $item->kategori,
                'deskripsi' => $item->deskripsi,
                'jumlah' => $item->jumlah,
                'tipe_transaksi' => $item->tipe === 'income' ? 'pemasukan' : 'pengeluaran',
                'nomor_transaksi' => $item->nama,
            ];
        });

        // Extract kategori unik dari financeData
        $kategoriList = $transactions->pluck('kategori')->unique()->values();
        $allKategori = $kategoriList->map(function ($kategori) {
            return (object)['nama_kategori' => $kategori];
        });

        // Kirim data ke view
        return view('finance.pemasukan', compact('financeData', 'allKategori'));
    }

    /**
     * Menyimpan data transaksi baru (POST Request)
     */
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:income,expense',
            'kategori' => 'required',
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric',
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan
        FinanceTransaction::create([
            'tanggal' => $request->tanggal,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'nama' => $request->nama,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
        ]);

        // Redirect kembali ke halaman pemasukan dengan pesan sukses
        return redirect()->to('/pemasukan')->with('success', 'Data berhasil disimpan!');
    }
}