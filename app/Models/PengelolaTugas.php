<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengelolaTugas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengelola_tugas';

    protected $fillable = [
        'manager_id',
        'karyawan_id',
        'divisi_id',
        'judul',
        'deskripsi',
        'deadline',
        'status',
        'file_lampiran',
        'catatan_manager',
        'catatan_karyawan',
        'progress_percentage',
        'tanggal_mulai',
        'tanggal_submit',
        'tanggal_selesai',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'tanggal_mulai' => 'datetime',
        'tanggal_submit' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'progress_percentage' => 'integer',
    ];

    /**
     * Relasi ke Manager (User)
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relasi ke Karyawan (User)
     */
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    /**
     * Relasi ke Divisi
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    /**
     * Scope untuk filter berdasarkan manager
     */
    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    /**
     * Scope untuk filter berdasarkan karyawan
     */
    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    /**
     * Scope untuk filter status aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope untuk filter status selesai
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
