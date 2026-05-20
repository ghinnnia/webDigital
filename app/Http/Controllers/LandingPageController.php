<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Layanan;

class LandingPageController extends Controller
{
    /**
     * Menampilkan halaman landing page
     */
    public function index()
    {
        // Ambil data kontak
        $contactInfo = Setting::getContactData();
        
        // Ambil data layanan
        $layanans = Layanan::all();
        
        // Mengarah ke view home.blade.php
        return view('home', compact('contactInfo', 'layanans'));
    }

    /**
     * API endpoint untuk mendapatkan info kontak
     */
    public function getContactInfo()
    {
        try {
            $setting = Setting::where('key', 'contact_info')->first();
            
            if ($setting) {
                $contactData = json_decode($setting->value, true);
            } else {
                // Default values
                $contactData = [
                    'email' => 'inovindocorp@gmail.com',
                    'phone' => '+62 817 - 251 - 196',
                    'address' => 'Jl. Batusari Komplek Buana Citra Ciwastra No.D-3, Buahbatu, Kec. Bojongsoang, Kabupaten Bandung, Jawa Barat 40287',
                    'whatsapp_message' => 'Halo, saya tertarik dengan layanan yang ditawarkan. Mohon informasi lebih lanjut.'
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $contactData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}