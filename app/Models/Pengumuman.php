<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    
    protected $fillable = [
        'user_id', 'judul', 'isi_pesan', 'lampiran', 
        'target', 'is_active', 'tanggal_mulai', 'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Relasi ke User (creator)
     * Menggunakan 'creator' sebagai nama relasi
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (creator) - alias untuk konsistensi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi many-to-many dengan User (untuk pembaca/penerima)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'pengumuman_user', 'pengumuman_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Scope untuk pengumuman yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('tanggal_mulai')
                  ->orWhere('tanggal_mulai', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now());
            });
    }

    /**
     * Scope untuk target tertentu
     */
    public function scopeForTarget($query, $target)
    {
        return $query->where('target', $target);
    }
}