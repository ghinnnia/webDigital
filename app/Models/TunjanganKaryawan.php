<?php

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
        'nominal',
        'bulan',
        'tahun',
        'diberikan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'diberikan' => 'boolean',
    ];

    // Relasi ke Karyawan (bukan User langsung)
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
    
    // Relasi ke User melalui Karyawan
    public function user()
    {
        return $this->hasOneThrough(User::class, Karyawan::class, 'id', 'id', 'karyawan_id', 'user_id');
    }

    // Relasi ke TunjanganMaster
    public function tunjanganMaster()
    {
        return $this->belongsTo(TunjanganMaster::class, 'tunjangan_id');
    }
    
    // Scope untuk filter bulan/tahun
    public function scopePeriode($query, $bulan, $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }
    
    // Scope untuk tunjangan yang diberikan
    public function scopeDiberikan($query)
    {
        return $query->where('diberikan', true);
    }
}