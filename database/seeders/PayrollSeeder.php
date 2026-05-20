<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // =============================================
        // 1. Tunjangan tetap & tarif lembur (default)
        // =============================================
        // Sesuaikan nilai ini dengan kebijakan perusahaan
        DB::table('payroll_allowances')->insertOrIgnore([
            [
                'nama'        => 'Tunjangan Transport',
                'tipe'        => 'tunjangan_tetap',
                'nilai'       => 200000.00,
                'is_active'   => true,
                'keterangan'  => 'Tunjangan transportasi per bulan',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'nama'        => 'Tunjangan Makan',
                'tipe'        => 'tunjangan_tetap',
                'nilai'       => 300000.00,
                'is_active'   => true,
                'keterangan'  => 'Tunjangan makan per bulan',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'nama'        => 'Tarif Lembur',
                'tipe'        => 'tarif_lembur',
                'nilai'       => 25000.00,
                'is_active'   => true,
                'keterangan'  => 'Tarif lembur per jam',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        // =============================================
        // 2. Mapping KPA → Persentase Tunjangan Kinerja
        // =============================================
        // Berdasarkan field nilai_akhir di tabel kpa
        DB::table('kpa_tunjangan_rules')->insertOrIgnore([
            [
                'nilai_min'   => 90.00,
                'nilai_max'   => 100.00,
                'persentase'  => 15.00,
                'label'       => 'Sangat Baik',
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'nilai_min'   => 75.00,
                'nilai_max'   => 89.99,
                'persentase'  => 10.00,
                'label'       => 'Baik',
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'nilai_min'   => 60.00,
                'nilai_max'   => 74.99,
                'persentase'  => 7.00,
                'label'       => 'Cukup',
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'nilai_min'   => 0.00,
                'nilai_max'   => 59.99,
                'persentase'  => 0.00,
                'label'       => 'Kurang / Sangat Kurang',
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }
}
