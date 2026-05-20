<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKerjasama extends Model
{
    protected $fillable = [
        'judul',
        'nomor_surat',
        'para_pihak',
        'maksud_tujuan',
        'ruang_lingkup',
        'jangka_waktu',
        'biaya_pembayaran',
        'kerahasiaan',
        'penyelesaian_sengketa',
        'penutup',
        'tanggal',
        'tanda_tangan',
    ];
}

