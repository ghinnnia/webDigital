<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar divisi yang ingin dibuat
        $divisiList = ['Umum', 'IT', 'Marketing', 'Finance', 'HR', 'Operations'];
        
        // Buat divisi jika belum ada
        $createdDivisions = [];
        foreach ($divisiList as $divisiName) {
            $divisi = Divisi::firstOrCreate(
                ['divisi' => $divisiName]
            );
            $createdDivisions[] = $divisi->id;
        }
        
        $this->command->info('✓ Divisi berhasil dibuat: ' . implode(', ', $divisiList));
        
        // Update pengguna karyawan dengan divisi yang berbeda
        $karyawanUsers = User::where('role', 'karyawan')->get();
        
        foreach ($karyawanUsers as $index => $user) {
            // Assign divisi_id berdasarkan index, tanpa trigger event
            User::where('id', $user->id)->update(['divisi_id' => $createdDivisions[$index % count($createdDivisions)]]);
        }
        
        $this->command->info('✓ Divisi berhasil ditambahkan ke ' . $karyawanUsers->count() . ' pengguna karyawan!');
    }
}
