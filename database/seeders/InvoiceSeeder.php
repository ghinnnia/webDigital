<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoices = [
            [
                'nama_klien' => 'PT. Maju Jaya',
                'nomor_order' => 'ORD-2023-001',
                'detail_layanan' => 'Pengembangan Website Company Profile',
                'harga' => 15000000,
                'pajak' => 1500000,
                'metode_pembayaran' => 'Transfer Bank',
            ],
            [
                'nama_klien' => 'CV. Sukses Mandiri',
                'nomor_order' => 'ORD-2023-002',
                'detail_layanan' => 'Jasa SEO dan Digital Marketing',
                'harga' => 8000000,
                'pajak' => 800000,
                'metode_pembayaran' => 'E-Wallet',
            ],
            [
                'nama_klien' => 'PT. Teknologi Nusantara',
                'nomor_order' => 'ORD-2023-003',
                'detail_layanan' => 'Maintenance sistem aplikasi selama 3 bulan',
                'harga' => 12000000,
                'pajak' => 1200000,
                'metode_pembayaran' => 'Transfer Bank',
            ],
            [
                'nama_klien' => 'UD. Berkah Jaya',
                'nomor_order' => 'ORD-2023-004',
                'detail_layanan' => 'Pembuatan aplikasi kasir toko',
                'harga' => 20000000,
                'pajak' => 2000000,
                'metode_pembayaran' => 'Tunai',
            ],
            [
                'nama_klien' => 'PT. Inovasi Digital',
                'nomor_order' => 'ORD-2023-005',
                'detail_layanan' => 'Konsultasi dan desain sistem ERP',
                'harga' => 35000000,
                'pajak' => 3500000,
                'metode_pembayaran' => 'Transfer Bank',
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::create($invoice);
        }
    }
}