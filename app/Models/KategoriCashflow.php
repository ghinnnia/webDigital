<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriCashflow extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'kategori_cashflow';

    /**
     * Atribut yang dapat diisi secara massal (mass assignment).
     *
     * @var array
     */
    protected $fillable = [
        'nama_kategori',
        'tipe_kategori',
    ];

    /**
     * Relasi: Satu kategori bisa dimiliki oleh banyak transaksi cashflow.
     */
    public function cashflows()
    {
        return $this->hasMany(Cashflow::class, 'kategori_id');
    }
}
