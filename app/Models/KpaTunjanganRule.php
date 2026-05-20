<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpaTunjanganRule extends Model
{
    protected $table = 'kpa_tunjangan_rules';
    
    protected $fillable = [
        'nilai_min', 'nilai_max', 'persentase', 'label', 'is_active'
    ];

    protected $casts = [
        'nilai_min' => 'decimal:2',
        'nilai_max' => 'decimal:2',
        'persentase' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}