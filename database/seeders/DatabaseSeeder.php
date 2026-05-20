<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Panggil seeder yang aman (tidak error)
        $this->call(UserSeeder::class);
        $this->call(PayrollSeeder::class);
        
        // Panggil seeder lain dengan try-catch
        try {
            $this->call(CommentSeeder::class);
        } catch (\Exception $e) {
            $this->command->error('CommentSeeder gagal: ' . $e->getMessage());
        }
        
        try {
            $this->call(GajiTemplateSeeder::class);
        } catch (\Exception $e) {
            $this->command->error('GajiTemplateSeeder gagal: ' . $e->getMessage());
        }
        
        try {
            $this->call(TunjanganSeeder::class);
        } catch (\Exception $e) {
            $this->command->error('TunjanganSeeder gagal: ' . $e->getMessage());
        }
        
        $this->command->info('Database seeding selesai!');
    }
}