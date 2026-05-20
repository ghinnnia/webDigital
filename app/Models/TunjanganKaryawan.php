<?php
// app/Models/TunjanganKaryawan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TunjanganKaryawan extends Model
{
    use HasFactory;

    protected $table = 'tunjangan_karyawan';
    
    protected $fillable = [
        'karyawan_id',
        'tunjangan_id',
        'bulan',
        'tahun',
        'nominal',
        'catatan',
        'diberikan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'diberikan' => 'boolean'
    ];

    // Relasi ke TunjanganMaster
    public function tunjanganMaster()
    {
        return $this->belongsTo(TunjanganMaster::class, 'tunjangan_id');
    }

    // Relasi ke User (Karyawan)
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }
}