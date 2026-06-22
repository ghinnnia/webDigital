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
        'divisi',        // Nama divisi (string) - untuk keperluan display
        'divisi_id',     // ID divisi (integer) - untuk relasi
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
            // Update jumlah tim di divisi
            if ($tim->divisi_id) {
                $divisi = Divisi::find($tim->divisi_id);
                if ($divisi) {
                    $divisi->updateJumlahTim();
                }
            }
        });
        
        static::updated(function($tim) {
            // Jika divisi berubah, update kedua divisi
            if ($tim->isDirty('divisi_id')) {
                // Kurangi di divisi lama
                $oldDivisiId = $tim->getOriginal('divisi_id');
                if ($oldDivisiId) {
                    $oldDivisi = Divisi::find($oldDivisiId);
                    if ($oldDivisi) {
                        $oldDivisi->updateJumlahTim();
                    }
                }
                // Tambah di divisi baru
                if ($tim->divisi_id) {
                    $newDivisi = Divisi::find($tim->divisi_id);
                    if ($newDivisi) {
                        $newDivisi->updateJumlahTim();
                    }
                }
            }
        });
        
        static::deleted(function($tim) {
            // Update jumlah tim di divisi
            if ($tim->divisi_id) {
                $divisi = Divisi::find($tim->divisi_id);
                if ($divisi) {
                    $divisi->updateJumlahTim();
                }
            }
        });
    }

    /**
     * Relasi ke Divisi menggunakan divisi_id
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
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

    /**
     * Get nama divisi (accessor)
     */
    public function getNamaDivisiAttribute()
    {
        return $this->divisi ? $this->divisi->divisi : $this->divisi;
    }
}