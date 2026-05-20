<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'contact_info'],
            [
                'value' => json_encode([
                    'email' => 'inovindocorp@gmail.com',
                    'phone' => '+62 817 - 251 - 196',
                    'address' => 'Jl. Batusari Komplek Buana Citra Ciwastra No.D-3, Buahbatu, Kec. Bojongsoang, Kabupaten Bandung, Jawa Barat 40287',
                    'whatsapp_message' => 'Halo, saya tertarik dengan layanan yang ditawarkan. Mohon informasi lebih lanjut.'
                ]),
                'description' => 'Informasi kontak untuk landing page'
            ]
        );
    }
}