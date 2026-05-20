<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    use HasFactory;

    protected $table = 'tunjangan';
    protected $fillable = ['nama', 'nominal', 'tipe', 'deskripsi', 'is_active'];

    public function karyawan()
    {
        return $this->belongsToMany(User::class, 'tunjangan_karyawan', 'tunjangan_id', 'karyawan_id')
                    ->withPivot('bulan', 'tahun', 'diberikan')
                    ->withTimestamps();
    }
}