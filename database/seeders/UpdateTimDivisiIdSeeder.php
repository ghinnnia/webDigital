<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tim;
use App\Models\Divisi;

class UpdateTimDivisiIdSeeder extends Seeder
{
    public function run(): void
    {
        $tims = Tim::all();
        
        foreach ($tims as $tim) {
            // Cari divisi berdasarkan nama
            $divisi = Divisi::where('divisi', $tim->divisi)->first();
            
            if ($divisi) {
                $tim->divisi_id = $divisi->id;
                $tim->save();
                $this->command->info("✅ Updated tim: {$tim->tim} with divisi_id: {$divisi->id}");
            } else {
                $this->command->warn("⚠️ Divisi not found for tim: {$tim->tim}");
            }
        }
        
        // Update jumlah_tim di semua divisi
        $divisis = Divisi::all();
        foreach ($divisis as $divisi) {
            $divisi->updateJumlahTim();
            $this->command->info("✅ Updated divisi: {$divisi->divisi} with jumlah_tim: {$divisi->jumlah_tim}");
        }
    }
}