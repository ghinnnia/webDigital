<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKpa extends Model
{
    use HasFactory;

    protected $table = 'indikator_kpa';
    protected $fillable = ['aspek_id', 'nama', 'bobot', 'tipe', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function aspek()
    {
        return $this->belongsTo(AspekKpa::class, 'aspek_id');
    }

    public function penilaian()
    {
        return $this->hasMany(PenilaianKpa::class, 'indikator_id');
    }

    public function getPenilaianByBulanTahun($karyawanId, $bulan, $tahun)
    {
        return $this->penilaian()
            ->where('karyawan_id', $karyawanId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
    }
}