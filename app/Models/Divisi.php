<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Divisi extends Model
{
    use HasFactory;
    
    protected $table = 'divisi';
    protected $fillable = ['divisi', 'keterangan', 'jumlah_tim'];
    
    /**
     * Boot method untuk auto update jumlah_tim
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function($divisi) {
            // Hapus semua tim yang terkait dengan divisi ini
            Tim::where('divisi', $divisi->divisi)->delete();
        });
    }
    
    /**
     * Relasi ke Tim
     */
    public function tim()
    {
        return $this->hasMany(Tim::class, 'divisi', 'divisi');
    }
    
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
    
    /**
     * Update jumlah tim di divisi
     */
    public function updateJumlahTim()
    {
        $this->jumlah_tim = $this->tim()->count();
        $this->saveQuietly();
    }
}