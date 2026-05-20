<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetKuantitas extends Model
{
    use HasFactory;

    protected $table = 'target_kuantitas';
    protected $fillable = ['karyawan_id', 'bulan', 'tahun', 'target', 'realisasi'];

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    // Hitung nilai kuantitas (Realisasi ÷ Target × 100)
    public function getNilaiAttribute()
    {
        if ($this->target <= 0) return 100;
        $nilai = ($this->realisasi / $this->target) * 100;
        return min($nilai, 100); // Maksimal 100
    }
}