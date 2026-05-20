<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasKaryawanToManager extends Model
{
    use HasFactory;

    protected $table = 'tugas_karyawan_to_manager';

    protected $fillable = [
        'karyawan_id',
        'manager_divisi_id',
        'project_id',
        'judul',
        'nama_tugas',
        'deskripsi',
        'deadline',
        'status',
        'catatan',
        'lampiran',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_divisi_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function approvalHistory()
    {
        return $this->hasMany(TugasApprovalHistory::class, 'tugas_id');
    }

    // Scope untuk manager divisi
    public function scopeForManager($query, $managerId)
    {
        return $query->where('manager_divisi_id', $managerId);
    }

    // Scope untuk karyawan
    public function scopeForKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    // Helper methods
    public function isOverdue()
    {
        if (in_array($this->status, ['selesai', 'dibatalkan'])) {
            return false;
        }

        return now()->gt($this->deadline);
    }

    public function getStatusBadgeClass()
    {
        return [
            'pending' => 'badge bg-warning',
            'proses' => 'badge bg-info',
            'selesai' => 'badge bg-success',
            'dibatalkan' => 'badge bg-danger',
        ][$this->status] ?? 'badge bg-secondary';
    }
}