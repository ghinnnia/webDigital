<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cuti';

    /**
     * MASS ASSIGNMENT
     * Ditambahkan kolom pembatalan untuk mendukung fitur Cancel With Refund
     */
    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'keterangan',
        'jenis_cuti',
        'status',
        'disetujui_oleh',        // ID Manager/GM yang menyetujui
        'catatan_penolakan',
        'disetujui_pada',         // Timestamp persetujuan
        'dibatalkan_oleh',        // ID User/Admin yang membatalkan (ADDED)
        'catatan_pembatalan',      // Alasan pembatalan (ADDED)
        'dibatalkan_pada',        // Timestamp pembatalan (ADDED)
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime:Y-m-d',
        'tanggal_selesai' => 'datetime:Y-m-d',
        'disetujui_pada' => 'datetime',
        'dibatalkan_pada' => 'datetime', // ADDED
    ];

    /**
     * RELASI
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function dibatalkanOleh()
    {
        // Relasi untuk melihat siapa yang membatalkan cuti (ADDED)
        return $this->belongsTo(User::class, 'dibatalkan_oleh');
    }

    public function histories()
    {
        return $this->hasMany(CutiHistory::class);
    }

    /**
     * ACCESSORS & MUTATORS
     */
    
    public function getNamaKaryawanAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown';
    }

    public function getDivisiKaryawanAttribute()
    {
        return $this->user ? $this->user->divisi : 'Unknown';
    }

    public function getSisaCutiKaryawanAttribute()
    {
        return $this->user ? $this->user->sisa_cuti : 0;
    }

    // ADDED: Accessor untuk mencegah error jika kolom belum ada di DB
    public function getDibatalkanOlehAttribute()
    {
        return $this->attributes['dibatalkan_oleh'] ?? null;
    }

    public function getDibatalkanPadaAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getCatatanPembatalanAttribute()
    {
        return $this->attributes['catatan_pembatalan'] ?? null;
    }

    /**
     * Format tanggal dengan fallback
     */
    public function getTanggalMulaiFormattedAttribute()
    {
        return $this->tanggal_mulai ? $this->tanggal_mulai->format('d F Y') : '-';
    }

    public function getTanggalSelesaiFormattedAttribute()
    {
        return $this->tanggal_selesai ? $this->tanggal_selesai->format('d F Y') : '-';
    }

    public function getPeriodeAttribute()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) return '-';
        return $this->tanggal_mulai->format('d/m/Y') . ' - ' . $this->tanggal_selesai->format('d/m/Y');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'menunggu' => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan', // ADDED
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getJenisCutiTextAttribute()
    {
        $jenis = [
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Cuti Sakit',
            'penting' => 'Cuti Penting',
            'melahirkan' => 'Cuti Melahirkan',
            'lainnya' => 'Cuti Lainnya',
        ];

        return $jenis[$this->jenis_cuti] ?? 'Cuti Lainnya';
    }

    /**
     * SCOPES
     */
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeDibatalkan($query)
    {
        // ADDED Scope
        return $query->where('status', 'dibatalkan');
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function scopeTahunIni($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    public function scopeUntukUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * SCOPE UNTUK VALIDASI ABSENSI & OVERLAP
     */
    
    public function scopeAktifPadaTanggal($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return $query->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $date)
            ->where('tanggal_selesai', '>=', $date);
    }

    public function scopeSedangBerlangsung($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today);
    }

    public function scopeOverlapDengan($query, $startDate, $endDate)
    {
        return $query->where('status', 'disetujui')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                  ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                  ->orWhere(function($innerQ) use ($startDate, $endDate) {
                      $innerQ->where('tanggal_mulai', '<=', $startDate)
                             ->where('tanggal_selesai', '>=', $endDate);
                  });
            });
    }

    /**
     * BUSINESS LOGIC
     */
    public function dapatDisetujui()
    {
        return $this->status === 'menunggu';
    }

    public function dapatDiubah()
    {
        return $this->status === 'menunggu';
    }

    public function dapatDihapus()
    {
        return $this->status === 'menunggu';
    }

    public function isOverlapping()
    {
        return self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->where('status', 'disetujui')
            ->where(function ($query) {
                $query->whereBetween('tanggal_mulai', [$this->tanggal_mulai, $this->tanggal_selesai])
                    ->orWhereBetween('tanggal_selesai', [$this->tanggal_mulai, $this->tanggal_selesai])
                    ->orWhere(function ($q) {
                        $q->where('tanggal_mulai', '<=', $this->tanggal_mulai)
                            ->where('tanggal_selesai', '>=', $this->tanggal_selesai);
                    });
            })
            ->exists();
    }

    /**
     * VALIDASI ABSENSI HELPERS
     */
    
    public function isSedangBerlangsung()
    {
        $today = now()->format('Y-m-d');
        return $this->status == 'disetujui' && 
               $this->tanggal_mulai <= $today && 
               $this->tanggal_selesai >= $today;
    }

    public function isTanggalDalamCuti($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return $this->status == 'disetujui' && 
               $date >= $this->tanggal_mulai && 
               $date <= $this->tanggal_selesai;
    }

    public function isOverlapDenganRentang($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');
        
        return $this->status == 'disetujui' && 
               (
                   ($this->tanggal_mulai >= $startDate && $this->tanggal_mulai <= $endDate) ||
                   ($this->tanggal_selesai >= $startDate && $this->tanggal_selesai <= $endDate) ||
                   ($this->tanggal_mulai <= $startDate && $this->tanggal_selesai >= $endDate)
               );
    }

    /**
     * API FORMAT
     */
    public function toApiFormat()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nama_karyawan' => $this->nama_karyawan,
            'divisi' => $this->divisi_karyawan,
            'tanggal_mulai' => $this->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $this->tanggal_selesai->format('Y-m-d'),
            'durasi' => $this->durasi,
            'keterangan' => $this->keterangan,
            'jenis_cuti' => $this->jenis_cuti,
            'jenis_cuti_text' => $this->jenis_cuti_text,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'disetujui_oleh' => $this->disetujui_oleh,
            'disetujui_oleh_nama' => $this->disetujuiOleh ? $this->disetujuiOleh->name : null,
            'disetujui_pada' => $this->disetujui_pada ? $this->disetujui_pada->format('Y-m-d H:i:s') : null,
            // Data pembatalan (ADDED)
            'dibatalkan_oleh' => $this->dibatalkan_oleh,
            'dibatalkan_oleh_nama' => $this->dibatalkanOleh ? $this->dibatalkanOleh->name : null,
            'dibatalkan_pada' => $this->dibatalkan_pada ? $this->dibatalkan_pada->format('Y-m-d H:i:s') : null,
            'catatan_penolakan' => $this->catatan_penolakan,
            'catatan_pembatalan' => $this->catatan_pembatalan,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'periode' => $this->periode,
            'tanggal_mulai_formatted' => $this->tanggal_mulai_formatted,
            'tanggal_selesai_formatted' => $this->tanggal_selesai_formatted,
        ];
    }

    /**
     * ACTIONS
     */
    public function approve($userId, $note = null)
    {
        $this->update([
            'status' => 'disetujui',
            'disetujui_oleh' => $userId,
            'disetujui_pada' => now(),
        ]);

        $this->histories()->create([
            'action' => 'approved',
            'user_id' => $userId,
            'note' => $note ?? 'Cuti disetujui',
            'changes' => json_encode([
                'status' => ['from' => $this->getOriginal('status'), 'to' => 'disetujui'],
                'disetujui_oleh' => ['from' => $this->getOriginal('disetujui_oleh'), 'to' => $userId],
            ]),
        ]);

        return $this;
    }

    public function reject($userId, $catatanPenolakan)
    {
        $this->update([
            'status' => 'ditolak',
            'disetujui_oleh' => $userId,
            'disetujui_pada' => now(),
            'catatan_penolakan' => $catatanPenolakan,
        ]);

        $this->histories()->create([
            'action' => 'rejected',
            'user_id' => $userId,
            'note' => $catatanPenolakan,
            'changes' => json_encode([
                'status' => ['from' => $this->getOriginal('status'), 'to' => 'ditolak'],
                'disetujui_oleh' => ['from' => $this->getOriginal('disetujui_oleh'), 'to' => $userId],
                'catatan_penolakan' => ['from' => $this->getOriginal('catatan_penolakan'), 'to' => $catatanPenolakan],
            ]),
        ]);

        return $this;
    }

    /**
     * Method Pembatalan (Support untuk Cancel With Refund)
     */
    public function cancel($userId, $catatan = null)
    {
        $this->update([
            'status' => 'dibatalkan',
            'dibatalkan_oleh' => $userId,
            'dibatalkan_pada' => now(),
            'catatan_pembatalan' => $catatan ?? 'Dibatalkan oleh user',
        ]);

        $this->histories()->create([
            'action' => 'cancelled',
            'user_id' => $userId,
            'note' => 'Cuti dibatalkan',
            'changes' => json_encode([
                'status' => ['from' => $this->getOriginal('status'), 'to' => 'dibatalkan'],
                'dibatalkan_oleh' => ['from' => $this->getOriginal('dibatalkan_oleh'), 'to' => $userId],
            ]),
        ]);

        return $this;
    }

    /**
     * STATIC HELPERS (Untuk Absensi Controller)
     */
    public static function getActiveLeaveForUser($userId, $date = null)
    {
        $date = $date ? Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');
        
        return self::where('user_id', $userId)
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $date)
            ->where('tanggal_selesai', '>=', $date)
            ->first();
    }

    public static function isUserOnLeave($userId, $date = null)
    {
        return self::getActiveLeaveForUser($userId, $date) !== null;
    }

    public static function getOverlappingLeavesForUser($userId, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');
        
        return self::where('user_id', $userId)
            ->where('status', 'disetujui')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                      ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('tanggal_mulai', '<=', $startDate)
                            ->where('tanggal_selesai', '>=', $endDate);
                      });
            })
            ->get();
    }
}