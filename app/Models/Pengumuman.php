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

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'pengumuman_user', 'pengumuman_id', 'user_id')
                    ->withTimestamps();
    }
}