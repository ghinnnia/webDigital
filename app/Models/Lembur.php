<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    protected $fillable = [
        'user_id', 'tanggal_lembur', 'jam_mulai', 'jam_selesai', 
        'durasi', 'upah_per_jam', 'total_upah', 'keterangan', 
        'status', 'approved_by', 'approved_at', 'alasan_penolakan', 'is_paid'
    ];

    protected $casts = [
        'tanggal_lembur' => 'date',
        'approved_at' => 'datetime',
        'upah_per_jam' => 'decimal:2',
        'total_upah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
