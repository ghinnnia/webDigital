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

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id');
    }

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
     * 🔥 RELASI KE DIVISI (HANYA SATU, TIDAK BOLEH DUPLIKAT)
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
public function tim()
{
    return $this->hasOneThrough(Tim::class, Karyawan::class, 'user_id', 'id', 'id', 'tim_id');
}

public function getDivisiIdAttribute($value)
{
    return $value;
}
}