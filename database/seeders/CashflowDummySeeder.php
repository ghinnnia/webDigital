<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashflowDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $date = '2026-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01';
            // Pemasukan
            \App\Models\Cashflow::create([
                'tanggal_transaksi' => $date,
                'nama_transaksi' => 'Pemasukan Bulan ' . $i,
                'deskripsi' => 'Test data',
                'jumlah' => rand(500000, 2000000),
                'tipe_transaksi' => 'pemasukan',
                'kategori_id' => 1
            ]);
            // Pengeluaran
            \App\Models\Cashflow::create([
                'tanggal_transaksi' => $date,
                'nama_transaksi' => 'Pengeluaran Bulan ' . $i,
                'deskripsi' => 'Test data',
                'jumlah' => rand(200000, 1000000),
                'tipe_transaksi' => 'pengeluaran',
                'kategori_id' => 2
            ]);
        }
    }
}
