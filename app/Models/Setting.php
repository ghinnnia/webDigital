<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description'
    ];

    protected $casts = [
        'value' => 'json'
    ];

    /**
     * Mendapatkan nilai setting berdasarkan key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Menyimpan nilai setting
     */
    public static function setValue($key, $value, $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description
            ]
        );
    }

    /**
     * Mendapatkan data kontak
     */
    public static function getContactData()
    {
        $setting = static::where('key', 'contact_info')->first();

        if ($setting) {
            return json_decode($setting->value, true);
        }

        // Default values
        return [
            'email' => 'inovindocorp@gmail.com',
            'phone' => '+62 817 - 251 - 196',
            'address' => 'Jl. Batusari Komplek Buana Citra Ciwastra No.D-3, Buahbatu, Kec. Bojongsoang, Kabupaten Bandung, Jawa Barat 40287',
            'whatsapp_message' => 'Halo, saya tertarik dengan layanan yang ditawarkan. Mohon informasi lebih lanjut.'
        ];
    }
    public static function getAboutData()
    {
        $setting = static::where('key', 'about_info')->first();

        if ($setting) {
            return json_decode($setting->value, true);
        }

        // Default values
        return [
            'title' => 'TENTANG',
            'description' => 'Kami digital agency adalah perusahaan yang membantu bisnis lain membawa ke produk atau jasanya secara online melalui berbagai layanan digital. Layanan yang ditawarkan meliputi strategi pemasaran digital, pembuatan dan pengelolaan situs web, manajemen media sosial, optimasi mesin pencari (SEO), serta kampanye iklan di Google Ads, iklan display, dan video.'
        ];
    }
}