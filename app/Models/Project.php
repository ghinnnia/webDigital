<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project';

    protected $fillable = [
        'invoice_id',
        'layanan_id',
        'divisi_id',
        'penanggung_jawab_id',
        'penanggung_jawab_ids',
        'karyawan_penanggung_jawab_id',
        'karyawan_penanggung_jawab_ids',
        // 'created_by', // HAPUS atau comment jika kolom tidak ada
        'nama',
        'deskripsi',
        'harga',
        'tanggal_mulai_pengerjaan',
        'tanggal_selesai_pengerjaan',
        'tanggal_mulai_kerjasama',
        'tanggal_selesai_kerjasama',
        'status_kerjasama',
        'status_pengerjaan',
        'progres',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'harga' => 'decimal:2',
        'progres' => 'integer',
        'penanggung_jawab_ids' => 'array',
        'karyawan_penanggung_jawab_ids' => 'array',
        'tanggal_mulai_pengerjaan' => 'datetime',
        'tanggal_selesai_pengerjaan' => 'datetime',
        'tanggal_mulai_kerjasama' => 'datetime',
        'tanggal_selesai_kerjasama' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // =========================================================================
    // RELASI (RELATIONSHIPS)
    // =========================================================================

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id')
                    ->select(['id', 'name', 'email', 'divisi_id']);
    }

    public function karyawanPenanggungJawab()
    {
        return $this->belongsTo(User::class, 'karyawan_penanggung_jawab_id')
                    ->select(['id', 'name', 'email', 'divisi_id']);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function notifications()
    {
        return $this->hasMany(ProjectNotification::class, 'project_id');
    }

    // =========================================================================
    // MUTATORS & ACCESSORS
    // =========================================================================

    public function setStatusAttribute($value)
    {
        $lowerValue = strtolower(trim($value));
        
        $statusMap = [
            'pending' => 'Pending',
            'proses'  => 'Proses',
            'selesai' => 'Selesai',
            'process' => 'Proses', 
            'done'    => 'Selesai',
        ];

        $this->attributes['status'] = $statusMap[$lowerValue] ?? ucfirst($lowerValue);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (!$project->status_kerjasama) {
                $project->status_kerjasama = 'aktif';
            }

            if (!$project->status_pengerjaan) {
                $project->status_pengerjaan = 'pending';
            }

            if (!isset($project->progres) || $project->progres === null) {
                $project->progres = 0;
            }
        });

        static::retrieved(function ($project) {
            $project->updateStatusKerjasamaIfExpired();
        });
    }

    public function updateStatusKerjasamaIfExpired()
    {
        if ($this->status_kerjasama === 'aktif' && $this->tanggal_selesai_kerjasama) {
            $tglSelesai = \Carbon\Carbon::parse($this->tanggal_selesai_kerjasama);
            if ($tglSelesai->isPast()) {
                $this->status_kerjasama = 'selesai';
                $this->saveQuietly();
                
                ProjectNotification::create([
                    'project_id' => $this->id,
                    'message' => "⚠️ Periode KERJA SAMA dengan '{$this->nama}' telah berakhir pada " . 
                                $tglSelesai->format('d-m-Y'),
                    'type' => 'expired_kerjasama',
                    'is_read' => false,
                    'trigger_date' => now(),
                ]);
            }
        }
    }

    public function getStatusFormattedAttribute()
    {
        $rawStatus = $this->attributes['status'] ?? null;
        if (!$rawStatus) return '-';

        $lowerStatus = strtolower($rawStatus);

        if ($lowerStatus !== 'selesai' && $this->tanggal_selesai_pengerjaan && $this->tanggal_selesai_pengerjaan->isPast()) {
            return 'Terlambat';
        }
        
        $statusMap = [
            'pending' => 'Pending',
            'proses'  => 'Dalam Proses',
            'selesai' => 'Selesai',
        ];

        return $statusMap[$lowerStatus] ?? ucfirst($rawStatus);
    }

    public function getStatusPengerjaanFormattedAttribute()
    {
        $rawStatus = $this->status_pengerjaan ?? null;
        if (!$rawStatus) return '-';

        $lowerStatus = strtolower($rawStatus);

        if ($lowerStatus !== 'selesai' && $lowerStatus !== 'dibatalkan') {
            if ($this->tanggal_selesai_pengerjaan && $this->tanggal_selesai_pengerjaan->isPast()) {
                return 'Terlambat';
            }
        }

        $statusMap = [
            'pending' => 'Pending',
            'dalam_pengerjaan' => 'Dalam Pengerjaan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return $statusMap[$lowerStatus] ?? ucfirst(str_replace('_', ' ', $rawStatus));
    }

    public function getStatusKerjasamaFormattedAttribute()
    {
        $rawStatus = $this->status_kerjasama ?? null;

        if (!$rawStatus) return '-';

        $statusMap = [
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            'ditangguhkan' => 'Ditangguhkan',
        ];

        return $statusMap[$rawStatus] ?? ucfirst(str_replace('_', ' ', $rawStatus));
    }

    public function getIsOverdueAttribute()
    {
        return $this->tanggal_selesai_pengerjaan && 
               $this->tanggal_selesai_pengerjaan->isPast() && 
               strtolower($this->status_pengerjaan) !== 'selesai' && 
               strtolower($this->status_pengerjaan) !== 'dibatalkan';
    }

    public function isKerjasamaExpired()
    {
        if (!$this->tanggal_selesai_kerjasama) {
            return false;
        }
        return \Carbon\Carbon::now()->startOfDay()->gt($this->tanggal_selesai_kerjasama);
    }

    // =========================================================================
    // SCOPES (Query Helpers)
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Pending', 'Proses']);
    }

    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    public function scopeByPenanggungJawab($query, $userId)
    {
        return $query->assignedToUser($userId);
    }

    public function scopeAssignedToUser($query, $userId)
    {
        return $query->where(function ($subQuery) use ($userId) {
            $subQuery->where('penanggung_jawab_id', $userId);

            if (Schema::hasColumn($this->getTable(), 'penanggung_jawab_ids')) {
                $subQuery
                    ->orWhereJsonContains('penanggung_jawab_ids', (int) $userId)
                    ->orWhereJsonContains('penanggung_jawab_ids', (string) $userId);
            }
        });
    }

    public function scopeAssignedToKaryawan($query, $userId)
    {
        return $query->where(function ($subQuery) use ($userId) {
            $subQuery->where('karyawan_penanggung_jawab_id', $userId);

            if (Schema::hasColumn($this->getTable(), 'karyawan_penanggung_jawab_ids')) {
                $subQuery
                    ->orWhereJsonContains('karyawan_penanggung_jawab_ids', (int) $userId)
                    ->orWhereJsonContains('karyawan_penanggung_jawab_ids', (string) $userId);
            }
        });
    }
}