<?php
// app/Models/GajiTemplate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiTemplate extends Model
{
    use HasFactory;

    protected $table = 'gaji_templates';
    
    protected $fillable = [
        'role',
        'divisi_id',
        'gaji_pokok',
        'tunjangan_tetap',
        'tunjangan_kinerja',
        'keterangan'
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'tunjangan_tetap' => 'decimal:2',
        'tunjangan_kinerja' => 'decimal:2',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }
}