<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpaSetting extends Model
{
    protected $table = 'kpa_settings';

    protected $fillable = [
        'aspek',
        'nama_aspek',
        'bobot',
        'is_active',
        'keterangan'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getActiveBobot()
    {
        return self::where('is_active', true)->get();
    }

    public static function getBobotArray()
    {
        $bobot = [];
        $settings = self::where('is_active', true)->get();
        foreach ($settings as $setting) {
            $bobot[$setting->aspek] = $setting->bobot;
        }
        return $bobot;
    }

    public static function getTotalBobot()
    {
        return self::where('is_active', true)->sum('bobot');
    }

    public static function isValidBobot()
    {
        return self::getTotalBobot() == 100;
    }
}