<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashflow;
use App\Models\Layanan;
use App\Models\FinanceTransaction;
use Carbon\Carbon;

class OwnerBerandaController extends Controller
{
    public function index() {
        // Ambil data keuangan lengkap dari Cashflow (legacy)
        $financeData = Cashflow::orderBy('tanggal_transaksi', 'desc')->get()->map(function($item) {
            $item->tanggal_transaksi = $item->tanggal_transaksi->format('Y-m-d');
            return $item;
        });

        // Hitung total pemasukan, pengeluaran dari Cashflow
        $totalPemasukan = Cashflow::where('tipe_transaksi', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = Cashflow::where('tipe_transaksi', 'pengeluaran')->sum('jumlah');
        $totalKeuntungan = $totalPemasukan - $totalPengeluaran;
        $jumlahLayanan = Layanan::count();

        // Ambil data pemasukan dari FinanceTransaction (bulan ini)
        $bulanIni = Carbon::now();
        $pemasukanBulanIni = FinanceTransaction::where('tipe', 'income')
            ->whereYear('tanggal', $bulanIni->year)
            ->whereMonth('tanggal', $bulanIni->month)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPemasukanBulanIni = $pemasukanBulanIni->sum('jumlah');
        $jumlahTransaksiPemasukanBulanIni = $pemasukanBulanIni->count();

        // Pemasukan per kategori (bulan ini)
        $pemasukanPerKategori = $pemasukanBulanIni->groupBy('kategori')->map(function($items) {
            return [
                'total' => $items->sum('jumlah'),
                'jumlah' => $items->count()
            ];
        })->sortByDesc('total');

        // Data pemasukan terbaru (5 transaksi terakhir)
        $pemasukanTerbaru = $pemasukanBulanIni->take(5);

        return view('pemilik.home', compact(
            'financeData',
            'totalPemasukan',
            'totalPengeluaran',
            'totalKeuntungan',
            'jumlahLayanan',
            'pemasukanBulanIni',
            'totalPemasukanBulanIni',
            'jumlahTransaksiPemasukanBulanIni',
            'pemasukanPerKategori',
            'pemasukanTerbaru'
        ));
    }
}

