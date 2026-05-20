<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianKpa extends Model
{
    use HasFactory;

    protected $table = 'penilaian_kpa';
    protected $fillable = ['karyawan_id', 'indikator_id', 'bulan', 'tahun', 'nilai', 'catatan'];

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function indikator()
    {
        return $this->belongsTo(IndikatorKpa::class, 'indikator_id');
    }
}