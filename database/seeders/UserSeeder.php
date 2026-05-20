<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data users yang sudah ada
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Cek dan buat divisi jika belum ada
        $this->createDivisisIfNotExists();

        // Get divisi IDs
        $programmer = Divisi::where('divisi', 'programmer')->first();
        $digitalMarketing = Divisi::where('divisi', 'digital_marketing')->first();
        $desainer = Divisi::where('divisi', 'desainer')->first();

        // Pastikan divisi ditemukan
        if (!$programmer || !$digitalMarketing || !$desainer) {
            $this->command->error('Divisi tidak ditemukan!');
            return;
        }

        $users = [
            // User tanpa divisi (admin, owner, GM, finance)
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'divisi_id' => null,
                'gaji' => '5000000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000001',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Owner Agency',
                'email' => 'owner@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'owner',
                'divisi_id' => null,
                'gaji' => '10000000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000002',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'General Manager',
                'email' => 'gm@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'general_manager',
                'divisi_id' => null,
                'gaji' => '8000000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000003',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Finance Department',
                'email' => 'finance@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'finance',
                'divisi_id' => null,
                'gaji' => '4500000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000004',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'hr',
                'divisi_id' => null,
                'gaji' => '4500000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000015',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            
            // Programmer Divisi
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'manager_divisi',
                'divisi_id' => $programmer->id,
                'gaji' => '6000000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000005',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'karyawan',
                'divisi_id' => $programmer->id,
                'gaji' => '4000000',
                'alamat' => 'Depok',
                'kontak' => '082000000006',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Rizki Pratama',
                'email' => 'rizki.pratama@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'karyawan',
                'divisi_id' => $programmer->id,
                'gaji' => '4000000',
                'alamat' => 'Bogor',
                'kontak' => '082000000007',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            
            // Digital Marketing Divisi
            [
                'name' => 'Agus Wijaya',
                'email' => 'agus.wijaya@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'manager_divisi',
                'divisi_id' => $digitalMarketing->id,
                'gaji' => '5500000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000008',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Lisa Marlina',
                'email' => 'lisa.marlina@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'karyawan',
                'divisi_id' => $digitalMarketing->id,
                'gaji' => '3500000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000009',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'karyawan',
                'divisi_id' => $digitalMarketing->id,
                'gaji' => '3500000',
                'alamat' => 'Tangerang',
                'kontak' => '082000000010',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            
            // Desainer Divisi
            [
                'name' => 'Yuni Astuti',
                'email' => 'yuni.astuti@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'manager_divisi',
                'divisi_id' => $desainer->id,
                'gaji' => '5500000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000011',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Ferdy Kurniawan',
                'email' => 'ferdy.kurniawan@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'karyawan',
                'divisi_id' => $desainer->id,
                'gaji' => '3500000',
                'alamat' => 'Bekasi',
                'kontak' => '082000000012',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            [
                'name' => 'Sari Dewi',
                'email' => 'sari.dewi@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'karyawan',
                'divisi_id' => $desainer->id,
                'gaji' => '3500000',
                'alamat' => 'Bandung',
                'kontak' => '082000000013',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
            
            // Admin biasa
            [
                'name' => 'Admin Support',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'divisi_id' => null,
                'gaji' => '3000000',
                'alamat' => 'Jakarta',
                'kontak' => '082000000014',
                'status_kerja' => 'aktif',
                'status_karyawan' => 'tetap',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('User seeder berhasil dijalankan! Total: ' . count($users) . ' user');
        $this->command->info('Password semua user: 123');
        $this->command->info('Email format: nama@gmail.com');
    }

    /**
     * Membuat data divisi jika belum ada - TANPA DESKRIPSI
     */
    private function createDivisisIfNotExists(): void
    {
        $divisis = ['programmer', 'digital_marketing', 'desainer'];

        foreach ($divisis as $namaDivisi) {
            Divisi::firstOrCreate(
                ['divisi' => $namaDivisi],
                ['divisi' => $namaDivisi]
            );
        }
    }
}