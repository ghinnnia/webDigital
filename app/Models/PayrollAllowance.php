<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollAllowance extends Model
{
    protected $table = 'payroll_allowances';
    
    protected $fillable = [
        'nama', 'tipe', 'nilai', 'is_active', 'keterangan'
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}