<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GajiTemplate;
use App\Models\Divisi;

class GajiTemplateSeeder extends Seeder
{
    public function run()
    {
        // Template berdasarkan ROLE saja (berlaku untuk semua divisi)
        $templates = [
            ['role' => 'general_manager', 'divisi_id' => null, 'gaji_pokok' => 15000000, 'tunjangan_tetap' => 2000000, 'tunjangan_kinerja' => 3000000],
            ['role' => 'manager_divisi', 'divisi_id' => null, 'gaji_pokok' => 10000000, 'tunjangan_tetap' => 1500000, 'tunjangan_kinerja' => 2000000],
            ['role' => 'finance', 'divisi_id' => null, 'gaji_pokok' => 8000000, 'tunjangan_tetap' => 1000000, 'tunjangan_kinerja' => 1000000],
            ['role' => 'hr', 'divisi_id' => null, 'gaji_pokok' => 7000000, 'tunjangan_tetap' => 1000000, 'tunjangan_kinerja' => 1000000],
            ['role' => 'karyawan', 'divisi_id' => null, 'gaji_pokok' => 5000000, 'tunjangan_tetap' => 500000, 'tunjangan_kinerja' => 500000],
        ];

        // Template khusus per DIVISI (opsional, lebih spesifik)
        $itDivisi = Divisi::where('divisi', 'IT')->first();
        if ($itDivisi) {
            $templates[] = ['role' => 'karyawan', 'divisi_id' => $itDivisi->id, 'gaji_pokok' => 6000000, 'tunjangan_tetap' => 1000000, 'tunjangan_kinerja' => 1000000];
        }

        foreach ($templates as $template) {
            GajiTemplate::updateOrCreate(
                ['role' => $template['role'], 'divisi_id' => $template['divisi_id']],
                $template
            );
        }

        $this->command->info('GajiTemplateSeeder berhasil! Total: ' . count($templates) . ' template');
    }
}