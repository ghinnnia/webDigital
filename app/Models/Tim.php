<?php
// app/Models/Tim.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tim extends Model
{
    use HasFactory;

    protected $table = 'tims';
    
    protected $fillable = [
        'tim',
        'divisi_id',
        'deskripsi'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'tim_id');
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Karyawan::class, 'tim_id', 'id', 'id', 'user_id');
    }
}