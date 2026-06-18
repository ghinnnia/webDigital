<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lemburs';

    protected $fillable = [
        'user_id',
        'ordered_by',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'durasi',
        'keterangan',
        'deskripsi_tugas',
        'hourly_rate',
        'custom_rate',
        'alasan_kenaikan',
        'status',
        'type',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'alasan_penolakan',
        'is_paid',
    ];

    protected $casts = [
        'tanggal_lembur' => 'date',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'custom_rate' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_paid' => 'boolean',
    ];

    // Relasi ke User (karyawan)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User (manager yang perintah)
    public function orderedBy()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    // Relasi ke User (approver)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke User (rejector)
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Hitung total upah (pakai custom_rate jika ada,否则 pakai hourly_rate)
    public function getTotalAttribute()
    {
        $rate = $this->custom_rate ?? $this->hourly_rate ?? 30000;
        return $rate * $this->durasi;
    }

    // Format total
    public function getTotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    // Rate yang dipakai (custom atau default)
    public function getEffectiveRateAttribute()
    {
        return $this->custom_rate ?? $this->hourly_rate ?? 30000;
    }

    // Format rate
    public function getRateFormattedAttribute()
    {
        return 'Rp ' . number_format($this->effective_rate, 0, ',', '.');
    }

    // Label status
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">⏳ Menunggu</span>',
            'approved' => '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">✅ Disetujui</span>',
            'rejected' => '<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">❌ Ditolak</span>',
        ];
        return $labels[$this->status] ?? '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">' . $this->status . '</span>';
    }

    // Label type
    public function getTypeLabelAttribute()
    {
        if ($this->type == 'perintah') {
            return '<span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs"><i class="fa-solid fa-crown"></i> Perintah Manager</span>';
        }
        return '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs"><i class="fa-solid fa-user"></i> Pengajuan</span>';
    }

    // Scope
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePengajuan($query)
    {
        return $query->where('type', 'pengajuan');
    }

    public function scopePerintah($query)
    {
        return $query->where('type', 'perintah');
    }
}