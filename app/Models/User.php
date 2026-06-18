<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Divisi; 
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\Pengumuman;
use App\Models\Absensi;
use App\Models\CatatanRapat;
use App\Models\CatatanRapatPenugasan;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'divisi_id',
        'tim_id',
        'gaji',
        'alamat',
        'kontak',
        'status_kerja',
        'status_karyawan',
        'foto',
        'email_verified_at',
        'sisa_cuti',
        'kontrak_mulai',
        'kontrak_selesai',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'gaji' => 'decimal:2'
    ];

    // ============================================
    // BOOT & EVENTS
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $divisiName = null;
            if ($user->divisi_id) {
                $divisi = Divisi::find($user->divisi_id);
                $divisiName = $divisi ? $divisi->divisi : null;
            }

            Karyawan::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'divisi' => $divisiName,
                'gaji' => $user->gaji,
                'alamat' => $user->alamat,
                'kontak' => $user->kontak,
                'foto' => $user->foto,
                'status_kerja' => $user->status_kerja ?? 'aktif',
                'status_karyawan' => $user->status_karyawan ?? 'tetap',
                'kontrak_mulai' => $user->kontrak_mulai,
                'kontrak_selesai' => $user->kontrak_selesai,
            ]);
        });

        static::updated(function ($user) {
            if ($user->karyawan) {
                $karyawan = $user->karyawan;
                
                $divisiName = null;
                if ($user->divisi_id) {
                    $divisi = Divisi::find($user->divisi_id);
                    $divisiName = $divisi ? $divisi->divisi : null;
                }

                $karyawan->update([
                    'nama' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'divisi' => $divisiName,
                    'gaji' => $user->gaji,
                    'alamat' => $user->alamat,
                    'kontak' => $user->kontak,
                    'foto' => $user->foto,
                    'status_kerja' => $user->status_kerja,
                    'status_karyawan' => $user->status_karyawan,
                    'kontrak_mulai' => $user->kontrak_mulai,
                    'kontrak_selesai' => $user->kontrak_selesai,
                ]);
            }
        });
    }

    // ============================================
    // RELASI
    // ============================================
/**
 * Relasi ke tabel lembur (pengajuan/perintah lembur)
 */
public function lembur()
{
    return $this->hasMany(Lembur::class, 'user_id');
}

/**
 * Relasi ke tabel lembur (perintah yang diberikan)
 */
public function lemburPerintah()
{
    return $this->hasMany(Lembur::class, 'ordered_by');
}

/**
 * Relasi ke tabel lembur (yang diapprove)
 */
public function lemburApproved()
{
    return $this->hasMany(Lembur::class, 'approved_by');
}

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id');
    }

    // ========== RELASI TASKS (TUGAS) ==========
    /**
     * Relasi ke tabel tasks (tugas yang ditugaskan ke user)
     * Menggunakan kolom 'assigned_to' di tabel tasks
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'id');
    }

    /**
     * Relasi ke tabel tasks (tugas yang dibuat oleh user)
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by', 'id');
    }

    /**
     * Relasi ke tabel tasks (tugas yang diberikan oleh user sebagai manager)
     */
    public function assignedByManagerTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by_manager', 'id');
    }

    /**
     * Relasi ke tabel tasks (tugas yang ditargetkan ke manager)
     */
    public function targetManagerTasks()
    {
        return $this->hasMany(Task::class, 'target_manager_id', 'id');
    }

    /**
     * Relasi ke tabel tasks (tugas yang sudah selesai)
     */
    public function completedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'id')
            ->where('status', 'selesai');
    }

    /**
     * Relasi ke tabel tasks (tugas yang sedang berjalan)
     */
    public function pendingTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'id')
            ->whereIn('status', ['pending', 'proses']);
    }

    /**
     * Relasi ke tabel tasks (tugas yang melebihi deadline)
     */
    public function overdueTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'id')
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['selesai', 'dibatalkan']);
    }

    /**
     * Hitung jumlah tugas selesai per bulan tertentu
     */
    public function getCompletedTasksCount($bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? now()->month;
        $tahun = $tahun ?? now()->year;

        return $this->hasMany(Task::class, 'assigned_to', 'id')
            ->where('status', 'selesai')
            ->whereMonth('completed_at', $bulan)
            ->whereYear('completed_at', $tahun)
            ->count();
    }

    /**
     * Hitung skor kinerja berdasarkan tugas selesai per bulan
     * Target default: 5 tugas = 100%
     */
    public function getPerformanceScore($bulan = null, $tahun = null, $target = 5)
    {
        $completedCount = $this->getCompletedTasksCount($bulan, $tahun);
        
        if ($target <= 0) {
            return 0;
        }
        
        $score = ($completedCount / $target) * 100;
        return min(100, round($score, 1));
    }

    /**
     * Dapatkan grade kinerja berdasarkan skor
     */
    public function getPerformanceGrade($bulan = null, $tahun = null, $target = 5)
    {
        $score = $this->getPerformanceScore($bulan, $tahun, $target);
        
        if ($score >= 90) return 'A';
        if ($score >= 75) return 'B';
        if ($score >= 60) return 'C';
        return 'D';
    }

    // ========== RELASI LAINNYA ==========

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'user_id');
    }

    public function cutiMenunggu()
    {
        return $this->cuti()->where('status', 'menunggu');
    }

    public function cutiDisetujui()
    {
        return $this->cuti()->where('status', 'disetujui');
    }

    public function cutiDitolak()
    {
        return $this->cuti()->where('status', 'ditolak');
    }

    public function pengumuman()
    {
        return $this->belongsToMany(Pengumuman::class, 'pengumuman_user');
    }

    public function createdPengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'user_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function catatanRapatPeserta()
    {
        return $this->belongsToMany(CatatanRapat::class, 'catatan_rapat_peserta');
    }

    public function catatanRapatPenugasan()
    {
        return $this->belongsToMany(CatatanRapat::class, 'catatan_rapat_penugasan');
    }

    public function catatanRapats()
    {
        return $this->belongsToMany(CatatanRapat::class, 'catatan_rapat_penugasan', 'user_id', 'catatan_rapat_id');
    }

    public function catatanRapatPenugasans()
    {
        return $this->hasMany(CatatanRapatPenugasan::class, 'user_id');
    }

    public function pengumumanDiterima()
    {
        return $this->belongsToMany(Pengumuman::class, 'pengumuman_user', 'user_id', 'pengumuman_id')
            ->withTimestamps();
    }

    /**
     * Relasi ke DIVISI
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    /**
     * Alias untuk relasi divisi() (untuk kompatibilitas)
     */
    public function divisionDetail()
    {
        return $this->divisi();
    }

    /**
     * Relasi ke Tim
     */
    public function tim()
    {
        return $this->belongsTo(Tim::class, 'tim_id');
    }

    /**
     * Relasi ke Tunjangan Tetap (melalui Karyawan)
     */
    public function tunjanganTetap()
    {
        $karyawan = $this->karyawan;
        if (!$karyawan) {
            return collect();
        }
        
        return $karyawan->tunjanganTetap();
    }

    /**
     * Relasi ke Tunjangan Tidak Tetap
     */
    public function tunjanganTidakTetap()
    {
        $karyawan = $this->karyawan;
        if (!$karyawan) {
            return collect();
        }
        
        return $karyawan->tunjanganTidakTetap();
    }

    /**
     * Relasi untuk tunjangan tetap bulan ini
     */
    public function tunjanganTetapBulanIni()
    {
        $karyawan = $this->karyawan;
        if (!$karyawan) return collect();
        return $karyawan->tunjanganTetapBulanIni();
    }

    /**
     * Relasi untuk tunjangan tidak tetap bulan ini
     */
    public function tunjanganTidakTetapBulanIni()
    {
        $karyawan = $this->karyawan;
        if (!$karyawan) return collect();
        return $karyawan->tunjanganTidakTetapBulanIni();
    }

    /**
     * Relasi ke tabel kinerja_pegawai
     */
    public function kinerja()
    {
        return $this->hasMany(KinerjaPegawai::class, 'karyawan_id');
    }

    /**
     * Relasi ke tabel kinerja_pegawai untuk periode tertentu
     */
    public function kinerjaPeriode($bulan, $tahun)
    {
        return $this->hasOne(KinerjaPegawai::class, 'karyawan_id')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);
    }

    // ============================================
    // HELPER METHODS & SCOPES
    // ============================================

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'finance']);
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    public function isGeneralManager(): bool
    {
        return $this->role === 'general_manager';
    }

    public function isManagerDivisi(): bool
    {
        return $this->role === 'manager_divisi';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByDivisiId($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    public function getFullNameWithRoleAttribute(): string
    {
        return "{$this->name} ({$this->role})";
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }

    public function syncToKaryawan(): void
    {
        if ($this->karyawan) {
            $karyawan = $this->karyawan;
            $karyawan->nama = $this->name;
            $karyawan->email = $this->email;
            
            if ($this->divisi) {
                $karyawan->divisi = $this->divisi->divisi;
            }
            
            if (!$karyawan->role || $karyawan->role === '') {
                $karyawan->role = $this->role;
            }
            
            $karyawan->save();
        }
    }

    public function getDivisiIdAttribute($value)
    {
        return $value;
    }
}