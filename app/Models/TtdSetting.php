<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TtdSetting extends Model
{
    protected $fillable = ['role', 'nama_pejabat', 'jabatan', 'file_path', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
}