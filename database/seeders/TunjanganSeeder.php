<?php
// database/seeders/TunjanganSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TunjanganMaster;

class TunjanganSeeder extends Seeder
{
    public function run()
    {
        $tunjangan = [
            ['nama' => 'Transportasi', 'tipe' => 'bulanan', 'nominal' => 500000],
            ['nama' => 'Makan', 'tipe' => 'bulanan', 'nominal' => 300000],
            ['nama' => 'Komunikasi', 'tipe' => 'bulanan', 'nominal' => 200000],
            ['nama' => 'Bonus Tahunan', 'tipe' => 'bonus', 'nominal' => 5000000],
            ['nama' => 'Insentif Project', 'tipe' => 'insentif', 'nominal' => 1000000],
            ['nama' => 'Tunjangan Kinerja', 'tipe' => 'bulanan', 'nominal' => 500000],
            ['nama' => 'Tunjangan Kesehatan', 'tipe' => 'bulanan', 'nominal' => 400000],
            ['nama' => 'Bonus Lebaran', 'tipe' => 'bonus', 'nominal' => 3000000],
        ];
        
        foreach ($tunjangan as $t) {
            TunjanganMaster::firstOrCreate(
                ['nama' => $t['nama']],
                $t
            );
        }
        
        $this->command->info('TunjanganSeeder berhasil dijalankan! Total: ' . count($tunjangan) . ' tunjangan');
    }
}