<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspekKpa extends Model
{
    use HasFactory;

    protected $table = 'aspek_kpa';
    protected $fillable = ['nama', 'bobot', 'urutan', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function indikator()
    {
        return $this->hasMany(IndikatorKpa::class, 'aspek_id');
    }

    public function indikatorAktif()
    {
        return $this->hasMany(IndikatorKpa::class, 'aspek_id')->where('is_active', true);
    }
}