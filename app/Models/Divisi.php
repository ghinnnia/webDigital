<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Divisi extends Model
{
    protected $table = 'divisi';
    protected $fillable = ['divisi', 'keterangan'];
    
    /**
     * Relasi ke User (karyawan)
     */
    public function karyawan()
    {
        return $this->hasMany(User::class, 'divisi_id');
    }
    
    /**
     * Relasi ke User (manager divisi)
     */
    public function manager()
    {
        return $this->hasOne(User::class, 'divisi_id')->where('role', 'manager_divisi');
    }
}