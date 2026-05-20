<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KinerjaPegawai extends Model
{
    protected $table = 'kpa';

    protected $fillable = [
        'karyawan_id',
        'bulan',
        'tahun',
        'nilai_kehadiran',
        'nilai_ketepatan_waktu',
        'nilai_penyelesaian_tugas',
        'nilai_kesesuaian_tugas',
        'nilai_rata_rata',
        'grade',
        'rekomendasi',
        'catatan'
    ];

    protected $casts = [
        'nilai_kehadiran' => 'decimal:2',
        'nilai_ketepatan_waktu' => 'decimal:2',
        'nilai_penyelesaian_tugas' => 'decimal:2',
        'nilai_kesesuaian_tugas' => 'decimal:2',
        'nilai_rata_rata' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function getGradeLabelAttribute()
    {
        $labels = [
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Cukup',
            'D' => 'Kurang',
            'E' => 'Sangat Kurang'
        ];
        return $labels[$this->grade] ?? '-';
    }

    public function getGradeColorAttribute()
    {
        $colors = [
            'A' => 'bg-green-100 text-green-700',
            'B' => 'bg-blue-100 text-blue-700',
            'C' => 'bg-yellow-100 text-yellow-700',
            'D' => 'bg-orange-100 text-orange-700',
            'E' => 'bg-red-100 text-red-700'
        ];
        return $colors[$this->grade] ?? 'bg-gray-100 text-gray-700';
    }
}