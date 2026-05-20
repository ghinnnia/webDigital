<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriCashflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            // Pemasukan
            ['nama_kategori' => 'Layanan', 'tipe_kategori' => 'pemasukan'],
            ['nama_kategori' => 'Produk', 'tipe_kategori' => 'pemasukan'],
            ['nama_kategori' => 'Fee/Komisi', 'tipe_kategori' => 'pemasukan'],

            // Pengeluaran
            ['nama_kategori' => 'Sewa Kantor', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Listrik', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Internet', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Air', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Biaya Perawatan', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Gaji', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Bonus', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Tunjangan', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Asuransi', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Biaya Pelatihan', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Iklan', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Langganan Software/Tools', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Infrastruktur IT', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Biaya Proyek & Variabel', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Biaya Administrasi & Legal', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Zakat', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Infaq', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Sedekah', 'tipe_kategori' => 'pengeluaran'],
            ['nama_kategori' => 'Wakaf', 'tipe_kategori' => 'pengeluaran'],
        ];

        DB::table('kategori_cashflow')->insert($kategoris);
    }
}
