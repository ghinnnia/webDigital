<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinanceTransaction;

class FinanceTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dari JavaScript financeData Anda
        $data = [
            [
                'tanggal' => '2023-01-05',
                'nama' => 'Gaji Bulanan',
                'kategori' => 'salary',
                'deskripsi' => 'Gaji bulan Januari 2023',
                'jumlah' => 15000000.00, // Input angka murni
                'tipe' => 'income',
            ],
            [
                'tanggal' => '2023-01-10',
                'nama' => 'Proyek Website',
                'kategori' => 'project',
                'deskripsi' => 'Pembuatan website untuk PT. Teknologi Maju',
                'jumlah' => 8500000.00,
                'tipe' => 'income',
            ],
            [
                'tanggal' => '2023-01-12',
                'nama' => 'Sewa Kantor',
                'kategori' => 'office',
                'deskripsi' => 'Pembayaran sewa kantor bulan Januari',
                'jumlah' => 5000000.00,
                'tipe' => 'expense',
            ],
            [
                'tanggal' => '2023-01-15',
                'nama' => 'Investasi Saham',
                'kategori' => 'investment',
                'deskripsi' => 'Dividen saham PT. Bank Central Asia',
                'jumlah' => 2500000.00,
                'tipe' => 'income',
            ],
            [
                'tanggal' => '2023-01-18',
                'nama' => 'Iklan Online',
                'kategori' => 'marketing',
                'deskripsi' => 'Biaya iklan Google Ads dan Facebook Ads',
                'jumlah' => 3200000.00,
                'tipe' => 'expense',
            ],
            // ... Lanjutkan data sisanya sesuai JavaScript Anda ...
        ];

        foreach ($data as $item) {
            FinanceTransaction::create($item);
        }
    }
}