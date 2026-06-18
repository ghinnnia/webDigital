<?php
// app/Models/Tim.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tim extends Model
{
    use HasFactory;

    protected $table = 'tim';
    
    protected $fillable = [
        'tim',
        'divisi',
        'divisi_id',
        'jumlah_anggota',
        'deskripsi'
    ];

    /**
     * Boot method untuk auto update jumlah_tim di divisi
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function($tim) {
            $divisi = Divisi::where('divisi', $tim->divisi)->first();
            if ($divisi) {
                $divisi->updateJumlahTim();
            }
        });
        
        static::updated(function($tim) {
            if ($tim->isDirty('divisi')) {
                // Kurangi di divisi lama
                $oldDivisi = Divisi::where('divisi', $tim->getOriginal('divisi'))->first();
                if ($oldDivisi) {
                    $oldDivisi->updateJumlahTim();
                }
                // Tambah di divisi baru
                $newDivisi = Divisi::where('divisi', $tim->divisi)->first();
                if ($newDivisi) {
                    $newDivisi->updateJumlahTim();
                }
            }
        });
        
        static::deleted(function($tim) {
            $divisi = Divisi::where('divisi', $tim->divisi)->first();
            if ($divisi) {
                $divisi->updateJumlahTim();
            }
        });
    }

    /**
     * Relasi ke Divisi
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi', 'divisi');
    }

    /**
     * Relasi ke Karyawan
     */
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'tim_id');
    }

    /**
     * Relasi ke User melalui Karyawan
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, Karyawan::class, 'tim_id', 'id', 'id', 'user_id');
    }
}