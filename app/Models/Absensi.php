<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class Absensi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with model.
     *
     * @var string
     */
    protected $table = 'absensis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tanggal',
        'tanggal_akhir',
        'jam_masuk',
        'jam_pulang',
        'late_minutes',
        'is_early_checkout',
        'early_checkout_reason',
        'jenis_ketidakhadiran',
        'reason',
        'keterangan',
        'location',
        'purpose',
        'approval_status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date',
        'tanggal_akhir' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
        'late_minutes' => 'integer',
        'is_early_checkout' => 'boolean',
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'approval_status' => 'approved',
        'is_early_checkout' => false,
    ];

    /**
     * Mendapatkan user yang memiliki data absensi ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan user yang menyetujui.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /* =====================================================
     |  SCOPES
     ===================================================== */

    /**
     * Scope untuk filter data absensi (kehadiran)
     * BERBASIS: ada jam masuk = kehadiran
     */
    public function scopeKehadiran($query)
    {
        return $query->whereNotNull('jam_masuk');
    }

    /**
     * Scope untuk filter data ketidakhadiran
     * BERBASIS: ada jenis ketidakhadiran = ketidakhadiran
     */
    public function scopeKetidakhadiran($query)
    {
        return $query->whereNotNull('jenis_ketidakhadiran');
    }

    /**
     * Scope untuk filter data cuti
     */
    public function scopeCuti($query)
    {
        return $query->where('jenis_ketidakhadiran', 'cuti');
    }

    /**
     * Scope untuk filter data izin
     */
    public function scopeIzin($query)
    {
        return $query->where('jenis_ketidakhadiran', 'izin');
    }

    /**
     * Scope untuk filter data sakit
     */
    public function scopeSakit($query)
    {
        return $query->where('jenis_ketidakhadiran', 'sakit');
    }

    /**
     * Scope untuk filter data dinas luar
     */
    public function scopeDinasLuar($query)
    {
        return $query->where('jenis_ketidakhadiran', 'dinas-luar');
    }

    /**
     * Scope untuk filter data yang disetujui
     */
    public function scopeDisetujui($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope untuk filter data pending
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope untuk filter data ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    /**
     * Scope untuk filter bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopeRentangTanggal($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /* =====================================================
     |  ACCESSORS & MUTATORS
     ===================================================== */

    /**
     * Accessor untuk mendapatkan tipe data
     * SEKARANG BERBASIS: ada jam masuk atau jenis ketidakhadiran
     */
    public function getTipeAttribute()
    {
        if ($this->jam_masuk) {
            return 'kehadiran';
        } elseif ($this->jenis_ketidakhadiran) {
            return 'ketidakhadiran';
        }
        
        return 'lainnya';
    }

    /**
     * Accessor untuk mendapatkan durasi cuti/izin
     */
    public function getDurasiHariAttribute()
    {
        if (!$this->tanggal_akhir) {
            return 1;
        }
        
        $start = Carbon::parse($this->tanggal);
        $end = Carbon::parse($this->tanggal_akhir);
        
        return $start->diffInDays($end) + 1; // +1 untuk inklusif
    }

    /**
     * Accessor untuk mendapatkan durasi kerja
     */
    public function getDurasiKerjaAttribute()
    {
        if (!$this->jam_masuk || !$this->jam_pulang) {
            return null;
        }
        
        $masuk = Carbon::parse($this->jam_masuk);
        $pulang = Carbon::parse($this->jam_pulang);
        
        $diff = $masuk->diff($pulang);
        return $diff->format('%h jam %i menit');
    }

    /**
     * Accessor untuk mendapatkan warna berdasarkan jenis ketidakhadiran
     */
    public function getStatusColorAttribute()
    {
        if ($this->jam_masuk && $this->jam_pulang) {
            return 'success'; // Kehadiran lengkap
        } elseif ($this->jam_masuk && !$this->jam_pulang) {
            return 'warning'; // Belum pulang
        } elseif (!$this->jam_masuk && !$this->jam_pulang && !$this->jenis_ketidakhadiran) {
            return 'danger'; // Tidak hadir tanpa keterangan
        }
        
        // Warna berdasarkan jenis ketidakhadiran
        return match($this->jenis_ketidakhadiran) {
            'cuti' => 'info',
            'sakit' => 'orange',
            'izin' => 'blue',
            'dinas-luar' => 'purple',
            'lainnya' => 'gray',
            default => 'secondary',
        };
    }

    /**
     * Accessor untuk mendapatkan icon berdasarkan jenis ketidakhadiran
     */
    public function getStatusIconAttribute()
    {
        if ($this->jam_masuk && $this->jam_pulang) {
            return 'check_circle'; // Kehadiran lengkap
        } elseif ($this->jam_masuk && !$this->jam_pulang) {
            return 'schedule'; // Belum pulang
        } elseif (!$this->jam_masuk && !$this->jam_pulang && !$this->jenis_ketidakhadiran) {
            return 'cancel'; // Tidak hadir tanpa keterangan
        }
        
        // Icon berdasarkan jenis ketidakhadiran
        return match($this->jenis_ketidakhadiran) {
            'cuti' => 'beach_access',
            'sakit' => 'medical_services',
            'izin' => 'event_available',
            'dinas-luar' => 'business',
            'lainnya' => 'help',
            default => 'question_mark',
        };
    }

    /**
     * Accessor untuk mendapatkan keterlambatan dalam menit
     */
    public function getLateMinutesAttribute()
    {
        if (!$this->jam_masuk) {
            return 0;
        }
        // Ensure we have a Carbon instance in the application timezone
        if ($this->jam_masuk instanceof Carbon) {
            $jamMasuk = $this->jam_masuk->copy()->setTimezone(config('app.timezone'));
        } else {
            $jamMasuk = Carbon::parse($this->jam_masuk)->setTimezone(config('app.timezone'));
        }

        // Ambil pengaturan jam operasional jika tersedia
        $operational = Setting::getValue('operational_hours', null);
        if ($operational && is_array($operational)) {
            $hour = isset($operational['late_limit_hour']) ? intval($operational['late_limit_hour']) : 9;
            $minute = isset($operational['late_limit_minute']) ? intval($operational['late_limit_minute']) : 5;
        } else {
            // default sama seperti AbsensiController::getOperationalHours
            $hour = 9; $minute = 5;
        }

        // Bandingkan hanya time-of-day untuk menghindari masalah timezone/tanggal
        $jamMasukSeconds = $jamMasuk->hour * 3600 + $jamMasuk->minute * 60 + $jamMasuk->second;
        $batasSeconds = $hour * 3600 + $minute * 60;

        // Debug log to help diagnose timezone/format issues
        Log::debug('Absensi lateness calc', [
            'raw_jam_masuk' => $this->attributes['jam_masuk'] ?? null,
            'parsed_jam_masuk' => $jamMasuk->toIso8601String(),
            'jamMasuk_seconds' => $jamMasukSeconds,
            'batas_seconds' => $batasSeconds,
            'app_timezone' => config('app.timezone'),
            'limit_hour' => $hour,
            'limit_minute' => $minute,
        ]);

        if ($jamMasukSeconds <= $batasSeconds) {
            return 0;
        }

        // Kembalikan selisih dalam menit (pembulatan ke bawah)
        return intdiv($jamMasukSeconds - $batasSeconds, 60);
    }

    /**
     * Accessor untuk cek apakah terlambat
     */
    public function getIsTerlambatAttribute()
    {
        return $this->late_minutes > 0;
    }

    /**
     * Format tanggal untuk display
     */
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    /**
     * Format tanggal akhir untuk display
     */
    public function getTanggalAkhirFormattedAttribute()
    {
        if (!$this->tanggal_akhir) {
            return $this->tanggal_formatted;
        }
        
        return Carbon::parse($this->tanggal_akhir)->translatedFormat('d F Y');
    }

    /**
     * Format jam masuk untuk display
     */
    public function getJamMasukFormattedAttribute()
    {
        if (!$this->jam_masuk) {
            return '-';
        }
        
        return Carbon::parse($this->jam_masuk)->format('H:i');
    }

    /**
     * Format jam pulang untuk display
     */
    public function getJamPulangFormattedAttribute()
    {
        if (!$this->jam_pulang) {
            return '-';
        }
        
        return Carbon::parse($this->jam_pulang)->format('H:i');
    }

    /**
     * Get label untuk jenis ketidakhadiran
     */
    public function getJenisKetidakhadiranLabelAttribute()
    {
        return match($this->jenis_ketidakhadiran) {
            'cuti' => 'Cuti',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'dinas-luar' => 'Dinas Luar',
            'lainnya' => 'Lainnya',
            default => 'Tidak Hadir',
        };
    }

    /**
     * Get label untuk approval status
     */
    public function getApprovalStatusLabelAttribute()
    {
        return match($this->approval_status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }

    /* =====================================================
     |  METHODS
     ===================================================== */

    /**
     * Check apakah data ini adalah kehadiran
     */
    public function isKehadiran()
    {
        return !is_null($this->jam_masuk);
    }

    /**
     * Check apakah data ini adalah ketidakhadiran
     */
    public function isKetidakhadiran()
    {
        return !is_null($this->jenis_ketidakhadiran);
    }

    /**
     * Check apakah butuh approval
     */
    public function needsApproval()
    {
        return $this->jenis_ketidakhadiran && $this->approval_status === 'pending';
    }

    /**
     * Check apakah sudah disetujui
     */
    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check apakah ditolak
     */
    public function isRejected()
    {
        return $this->approval_status === 'rejected';
    }
}