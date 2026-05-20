<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CutiQuota extends Model
{
    use HasFactory;

    protected $table = 'cuti_quotas';
    
    protected $fillable = [
        'user_id',
        'tahun',
        'quota_tahunan',
        'terpakai',
        'sisa',
        'quota_khusus',
        'terpakai_khusus',
        'is_active',
        'is_reset',
        'reset_at',
        'reset_by'
    ];

    protected $casts = [
        'quota_tahunan' => 'integer',
        'terpakai' => 'integer',
        'sisa' => 'integer',
        'quota_khusus' => 'integer',
        'terpakai_khusus' => 'integer',
        'is_active' => 'boolean',
        'is_reset' => 'boolean',
        'reset_at' => 'datetime',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke user yang mereset
    public function resetBy()
    {
        return $this->belongsTo(User::class, 'reset_by');
    }

    // Mendapatkan quota user untuk tahun tertentu
    public static function getUserQuota($userId, $year)
    {
        $quota = self::where('user_id', $userId)
                    ->where('tahun', $year)
                    ->first();

        if (!$quota) {
            // Cek apakah user ada
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }
            
            // Buat quota default jika belum ada
            $quota = self::create([
                'user_id' => $userId,
                'tahun' => $year,
                'quota_tahunan' => 12, // Default 12 hari
                'terpakai' => 0,
                'sisa' => 12,
                'quota_khusus' => 0,
                'terpakai_khusus' => 0,
                'is_active' => true,
                'is_reset' => false,
            ]);
        }

        return $quota;
    }

    // Menambah cuti terpakai
    public function addTerpakai($days)
    {
        if ($days <= 0) {
            return $this;
        }

        $this->terpakai += $days;
        $this->sisa = $this->quota_tahunan - $this->terpakai;
        
        if ($this->sisa < 0) {
            $this->sisa = 0;
        }
        
        $this->save();
        
        // Update user's sisa_cuti
        $user = User::find($this->user_id);
        if ($user) {
            $user->sisa_cuti = $this->sisa;
            $user->cuti_terpakai_tahun_ini = $this->terpakai;
            $user->save();
        }
        
        return $this;
    }

    // Mengurangi cuti terpakai (untuk pembatalan)
    public function reduceTerpakai($days)
    {
        if ($days <= 0) {
            return $this;
        }

        $this->terpakai -= $days;
        
        if ($this->terpakai < 0) {
            $this->terpakai = 0;
        }
        
        $this->sisa = $this->quota_tahunan - $this->terpakai;
        
        if ($this->sisa > $this->quota_tahunan) {
            $this->sisa = $this->quota_tahunan;
        }
        
        $this->save();
        
        // Update user's sisa_cuti
        $user = User::find($this->user_id);
        if ($user) {
            $user->sisa_cuti = $this->sisa;
            $user->cuti_terpakai_tahun_ini = $this->terpakai;
            $user->save();
        }
        
        return $this;
    }

    // Menambah cuti khusus terpakai
    public function addTerpakaiKhusus($days)
    {
        if ($days <= 0) {
            return $this;
        }

        $this->terpakai_khusus += $days;
        $this->save();
        
        return $this;
    }

    // Mengurangi cuti khusus terpakai
    public function reduceTerpakaiKhusus($days)
    {
        if ($days <= 0) {
            return $this;
        }

        $this->terpakai_khusus -= $days;
        
        if ($this->terpakai_khusus < 0) {
            $this->terpakai_khusus = 0;
        }
        
        $this->save();
        
        return $this;
    }

    // Reset quota untuk tahun berikutnya
    public function resetForNextYear($newYear)
    {
        $newQuota = self::create([
            'user_id' => $this->user_id,
            'tahun' => $newYear,
            'quota_tahunan' => $this->quota_tahunan,
            'terpakai' => 0,
            'sisa' => $this->quota_tahunan,
            'quota_khusus' => $this->quota_khusus,
            'terpakai_khusus' => 0,
            'is_active' => true,
            'is_reset' => false,
            'reset_by' => auth()->id(),
            'reset_at' => now(),
        ]);

        // Tandai quota lama sebagai sudah direset
        $this->is_reset = true;
        $this->is_active = false;
        $this->save();

        return $newQuota;
    }

    // Scope untuk quota aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk tahun tertentu
    public function scopeYear($query, $year)
    {
        return $query->where('tahun', $year);
    }

    // Mendapatkan total terpakai semua jenis
    public function getTotalTerpakaiAttribute()
    {
        return $this->terpakai + $this->terpakai_khusus;
    }

    // Mendapatkan persentase penggunaan
    public function getPersentasePenggunaanAttribute()
    {
        if ($this->quota_tahunan == 0) {
            return 0;
        }
        
        return round(($this->terpakai / $this->quota_tahunan) * 100, 1);
    }

    // Cek apakah quota mencukupi
    public function isQuotaSufficient($days)
    {
        return $this->sisa >= $days;
    }

    // Mendapatkan quota khusus tersisa
    public function getSisaKhususAttribute()
    {
        return $this->quota_khusus - $this->terpakai_khusus;
    }
}