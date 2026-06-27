<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes; // Aktif karena migrasi Anda memiliki $table->softDeletes()

    /**
     * Nama tabel yang terkait dengan model.
     * Default Laravel adalah 'projects', tapi kita pakai 'project' sesuai request.
     */
    protected $table = 'project';

    /**
     * Kolom yang bisa diisi secara mass-assignment.
     */
    protected $fillable = [
        'invoice_id',         // Foreign key ke invoice
        'layanan_id',
        'divisi_id',          // Kolom baru dari revisi migrasi
        'penanggung_jawab_id',
        'penanggung_jawab_ids',
        'karyawan_penanggung_jawab_id',
        'karyawan_penanggung_jawab_ids',
        'created_by',         // User yang membuat project
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

    /**
     * Type Casting (Otomatis ubah tipe data)
     */
    protected $casts = [
        'deadline' => 'datetime',
        'harga' => 'decimal:2', // Penting: Decimal agar uang presisi (contoh: 100000.50)
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

    /**
     * Relasi ke Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Relasi ke Layanan (alias untuk invoice jika digunakan)
     */
    public function layanan()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Relasi ke Penanggung Jawab (User)
     */
    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id')
                    ->select(['id', 'name', 'email', 'divisi_id']);
    }

    /**
     * Relasi ke Penanggung Jawab Karyawan (User)
     */
    public function karyawanPenanggungJawab()
    {
        return $this->belongsTo(User::class, 'karyawan_penanggung_jawab_id')
                    ->select(['id', 'name', 'email', 'divisi_id']);
    }

    /**
     * Relasi ke Divisi
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    /**
     * Relasi ke User yang membuat Project (Creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Tasks (Tugas-tugas di dalam project ini)
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    // =========================================================================
    // MUTATORS & ACCESSORS
    // =========================================================================

    /**
     * Mutator: Menormalisasi input status sebelum simpan ke DB
     */
    public function setStatusAttribute($value)
    {
        // Logika: Ubah input jadi lowercase dulu, lalu map ke format standard
        $lowerValue = strtolower(trim($value));
        
        $statusMap = [
            'pending' => 'Pending',
            'proses'  => 'Proses',
            'selesai' => 'Selesai',
            'process' => 'Proses', 
            'done'    => 'Selesai',
        ];

        // Simpan status yang sudah diformat
        $this->attributes['status'] = $statusMap[$lowerValue] ?? ucfirst($lowerValue);
    }
    
    /**
     * Event ketika project dibuat (Auto-populate dari Layanan)
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika project dibuat dari invoice, Invoice model sudah menghandle data
        // Method boot ini hanya untuk fallback atau ketika project dibuat manual
        static::creating(function ($project) {
            // Jika status tidak diset, gunakan default
            if (!$project->status_kerjasama) {
                $project->status_kerjasama = 'aktif';
            }

            if (!$project->status_pengerjaan) {
                $project->status_pengerjaan = 'pending';
            }

            // Progres default 0 jika tidak diset
            if (!isset($project->progres) || $project->progres === null) {
                $project->progres = 0;
            }
        });
    }

    /**
     * Accessor: Mengambil status yang sudah diformat
     */
    public function getStatusFormattedAttribute()
    {
        $rawStatus = $this->attributes['status'] ?? null;

        if (!$rawStatus) return '-';

        $lowerStatus = strtolower($rawStatus);
        
        $statusMap = [
            'pending' => 'Pending',
            'proses'  => 'Dalam Proses',
            'selesai' => 'Selesai',
        ];

        return $statusMap[$lowerStatus] ?? ucfirst($rawStatus);
    }

    /**
     * Accessor: Format status_pengerjaan untuk display
     */
    public function getStatusPengerjaanFormattedAttribute()
    {
        $rawStatus = $this->status_pengerjaan ?? null;

        if (!$rawStatus) return '-';

        $statusMap = [
            'pending' => 'Pending',
            'dalam_pengerjaan' => 'Dalam Pengerjaan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return $statusMap[$rawStatus] ?? ucfirst(str_replace('_', ' ', $rawStatus));
    }

    /**
     * Accessor: Format status_kerjasama untuk display
     */
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

    /**
     * Accessor: Cek apakah project overdue
     */
    public function getIsOverdueAttribute()
    {
        // Cek jika ada deadline, tanggalnya sudah lewat, dan status bukan Selesai
        return $this->deadline && 
               $this->deadline->isPast() && 
               $this->status !== 'Selesai' && 
               $this->status !== 'Dibatalkan';
    }

    // =========================================================================
    // SCOPES (Query Helpers)
    // =========================================================================

    /**
     * Scope: Filter project aktif (bukan Selesai/Dibatalkan)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Pending', 'Proses']);
    }

    /**
     * Scope: Filter project berdasarkan divisi
     */
    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    /**
     * Scope: Filter project yang menjadi tanggung jawab user tertentu
     */
    public function scopeByPenanggungJawab($query, $userId)
    {
        return $query->assignedToUser($userId);
    }

    /**
     * Scope: Filter project yang ditugaskan ke user tertentu
     * (kolom tunggal + multi manager JSON).
     */
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

    /**
     * Scope: Filter project yang ditugaskan ke karyawan tertentu
     * (kolom tunggal + multi karyawan JSON).
     */
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
