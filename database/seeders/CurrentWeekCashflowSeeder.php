<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrentWeekCashflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        for ($i = 0; $i < 7; $i++) {
            $date = $now->copy()->subDays($i);
            \App\Models\Cashflow::create([
                'tanggal_transaksi' => $date,
                'nama_transaksi' => 'Test Minggu Ini ' . $i,
                'deskripsi' => 'Test',
                'jumlah' => rand(100000, 500000),
                'tipe_transaksi' => $i % 2 == 0 ? 'pemasukan' : 'pengeluaran',
                'kategori_id' => 1
            ]);
        }
    }
}
