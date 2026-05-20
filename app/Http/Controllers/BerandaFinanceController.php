<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Cashflow;
use App\Models\KategoriCashflow;

class BerandaFinanceController extends Controller
{
    public function index()
    {
        // Ambil data layanan
        $layanans = Layanan::all();

        // Hitung total pemasukan, pengeluaran, dan total keuangan
        $totalPemasukan = Cashflow::where('tipe_transaksi', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = Cashflow::where('tipe_transaksi', 'pengeluaran')->sum('jumlah');
        
        // Tambahkan total gaji dari payroll_details sebagai pengeluaran
        $totalGaji = \Illuminate\Support\Facades\DB::table('payroll_details')->sum('total_gaji_bersih') ?? 0;
        $totalPengeluaran += $totalGaji;
        
        $totalKeuangan = $totalPemasukan - $totalPengeluaran;
        $jumlahLayanan = Layanan::count();

        // Ambil data keuangan (pemasukan dan pengeluaran)
        $financeData = Cashflow::orderBy('tanggal_transaksi', 'desc')->get()->map(function($item) {
            $item->tanggal_transaksi = $item->tanggal_transaksi->format('Y-m-d H:i:s');
            return $item;
        });

        // Ambil semua kategori cashflow
        $allKategori = KategoriCashflow::all();

        // Ambil data order untuk list
        $orders = \App\Models\Order::orderBy('created_at', 'desc')->get();

        $orderData = $layanans->map(function($layanan, $index) {
            return [
                'no' => $index + 1,
                'layanan' => $layanan->nama_layanan ?? 'N/A',
                'harga' => 'Rp ' . number_format($layanan->harga ?? 0, 0, ',', '.'),
                'klien' => 'N/A',
                'awal' => 'N/A',
                'lunas' => 'N/A',
                'status' => 'N/A',
                'date' => $layanan->created_at?->format('Y-m-d') ?? ''
            ];
        });

        return view('finance.beranda', compact('layanans', 'financeData', 'allKategori', 'orders', 'orderData', 'totalPemasukan', 'totalPengeluaran', 'totalKeuangan', 'jumlahLayanan'));
    }
}
