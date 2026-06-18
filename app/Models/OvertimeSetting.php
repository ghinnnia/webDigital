<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeSetting extends Model
{
    protected $table = 'overtime_settings';
    
    protected $fillable = [
        'division_id', 'default_rate', 'max_rate', 'is_active', 
        'created_by', 'updated_by'
    ];
    
    protected $casts = [
        'default_rate' => 'decimal:2',
        'max_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    public function division()
    {
        return $this->belongsTo(Divisi::class, 'division_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    // Get setting untuk divisi tertentu
    public static function getSetting($divisionId = null)
    {
        if ($divisionId) {
            $setting = self::where('division_id', $divisionId)->first();
            if ($setting) return $setting;
        }
        
        return self::whereNull('division_id')->first() ?? (object)[
            'default_rate' => 50000,
            'max_rate' => 100000,
        ];
    }
}