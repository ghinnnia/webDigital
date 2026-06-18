<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    protected $table = 'gaji';
    
    protected $fillable = [
        'karyawan_id',
        'bulan',
        'tahun',
        'gaji_pokok',
        'total_tunjangan',
        'tunjangan_detail',
        'tunjangan_tetap',
        'tunjangan_kinerja',
        'bonus',
        'potongan_bpjs',
        'potongan_lain',
        'total_gaji',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'total_tunjangan' => 'decimal:2',
        'tunjangan_tetap' => 'decimal:2',
        'tunjangan_kinerja' => 'decimal:2',
        'bonus' => 'decimal:2',
        'potongan_bpjs' => 'decimal:2',
        'potongan_lain' => 'decimal:2',
        'total_gaji' => 'decimal:2',
        'tunjangan_detail' => 'array',
    ];

    // Relasi ke User (karyawan) - karyawan_id = user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    // Relasi ke Karyawan (jika ada model Karyawan terpisah)
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}