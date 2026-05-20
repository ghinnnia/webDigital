<?php
// app/Models/TunjanganMaster.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TunjanganMaster extends Model
{
    use HasFactory;

    protected $table = 'tunjangan_master';
    
    protected $fillable = [
        'nama',
        'tipe',
        'nominal',
        'keterangan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2'
    ];

    public function getNominalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}