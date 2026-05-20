<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_perusahaan',
        'klien',
        'kontak',
        'alamat',
        'jumlah_kerjasama',
        'status',
    ];

    /**
     * Accessor untuk format Rupiah saat pemanggilan model
     */
    public function getJumlahKerjasamaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_kerjasama, 0, ',', '.');
    }
}
