<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CatatanRapat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'topik',
        'hasil_diskusi',
        'keputusan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ✅ PESERTA RAPAT
    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'catatan_rapat_peserta'
        );
    }

    // ✅ PENUGASAN
    public function penugasan(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'catatan_rapat_penugasan'
        );
    }

    public function getFormattedTanggalAttribute(): string
    {
        return $this->tanggal->format('d/m/Y');
    }
}
