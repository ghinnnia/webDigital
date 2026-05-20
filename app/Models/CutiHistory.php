<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CutiHistory extends Model
{
    use HasFactory;

    protected $table = 'cuti_histories';

    /**
     * MASS ASSIGNMENT
     */
    protected $fillable = [
        'cuti_id',
        'action',
        'user_id',
        'changes',
        'note',
    ];

    /**
     * CASTS
     */
    protected $casts = [
        'changes' => 'array', // Otomatis decode JSON ke Array PHP
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Relasi ke Cuti
     */
    public function cuti()
    {
        return $this->belongsTo(Cuti::class, 'cuti_id');
    }

    /**
     * Relasi ke User
     * User yang melakukan aksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ============================================
    // ACCESSORS (GETTERS)
    // ============================================

    /**
     * Mendapatkan label teks untuk action
     * Contoh: 'approved' -> 'Disetujui'
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'created' => 'Diajukan',
            'updated' => 'Diperbarui',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'deleted' => 'Dihapus',
            'restored' => 'Dipulihkan',
            'cancelled_with_refund' => 'Dibatalkan (Refund)',
        ];

        // Default jika action tidak dikenali, gunakan ucfirst
        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Mendapatkan nama user yang melakukan aksi
     * Mengambil dari relasi user, jika null kembalikan ke 'System'
     */
    public function getNamaUserAttribute()
    {
        return $this->user ? $this->user->name : 'System';
    }

    /**
     * Mendapatkan role user yang melakukan aksi
     * Mengambil dari relasi user, jika null kembalikan ke 'unknown'
     */
    public function getRoleUserAttribute()
    {
        return $this->user ? $this->user->role : 'unknown';
    }

    /**
     * Mendapatkan tanggal pembuatan diformat Indonesia
     * Contoh: '25 Oktober 2023, 14:00'
     */
    public function getTanggalAttribute()
    {
        return $this->created_at ? $this->created_at->translatedFormat('d F Y H:i') : '-';
    }

    /**
     * Format perubahan untuk ditampilkan di timeline
     * Contoh: "Status: menunggu → Disetujui"
     */
    public function getChangesFormattedAttribute()
    {
        if (empty($this->changes) || !is_array($this->changes)) {
            return null;
        }

        $formatted = [];
        foreach ($this->changes as $field => $change) {
            if (is_array($change) && isset($change['from'], $change['to'])) {
                $from = $this->formatChangeValue($field, $change['from']);
                $to = $this->formatChangeValue($field, $change['to']);
                
                if ($from !== $to) {
                    $formatted[] = [
                        'field' => $this->getFieldLabel($field),
                        'from' => $from,
                        'to' => $to
                    ];
                }
            }
        }

        return $formatted;
    }

    /**
     * Format perubahan menjadi teks sederhana
     * Contoh: "Status: menunggu | Alasan: Sakit"
     */
    public function getChangesTextAttribute()
    {
        $formatted = $this->getChangesFormatted();
        if (empty($formatted)) {
            return 'Tidak ada perubahan';
        }

        $lines = [];
        foreach ($formatted as $change) {
            $lines[] = "{$change['field']}: {$change['from']} → {$change['to']}";
        }

        return implode("\n", $lines);
    }

    // ============================================
    // SCOPES
    // ============================================
    
    /**
     * Scope untuk filter berdasarkan action
     * Contoh: CutiHistory::action('approved')->get();
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan user
     * Contoh: CutiHistory::byUser(5)->get();
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopePeriode($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?: now()->format('Y-m-d');
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk history dari cuti tertentu
     */
    public function scopeForCuti($query, $cutiId)
    {
        return $query->where('cuti_id', $cutiId);
    }

    /**
     * Scope untuk aksi tertentu pada cuti tertentu
     * Contoh: CutiHistory::actionForCuti(10, 'approved')->get();
     */
    public function scopeActionForCuti($query, $cutiId, $action)
    {
        return $query->where('cuti_id', $cutiId)->where('action', $action);
    }

    // ============================================
    // BUSINESS LOGIC
    // ============================================

    /**
     * Cek apakah history ini adalah approval
     * 'approved' atau 'rejected'
     */
    public function isApprovalAction()
    {
        return in_array($this->action, ['approved', 'rejected']);
    }

    /**
     * Cek apakah history ini adalah pembuatan
     */
    public function isCreationAction()
    {
        return $this->action === 'created';
    }

    /**
     * Cek apakah history ini adalah update
     */
    public function isUpdateAction()
    {
        return $this->action === 'updated';
    }

    // ============================================
    // HELPERS (PRIVATE)
    // ============================================

    /**
     * Format nilai perubahan berdasarkan tipe data
     */
    private function formatChangeValue($field, $value)
    {
        if (is_null($value)) {
            return '-';
        }

        // Format tanggal
        if (str_contains($field, 'tanggal') || str_contains($field, 'date')) {
            try {
                return Carbon::parse($value)->translatedFormat('d F Y');
            } catch (\Exception $e) {
                return $value;
            }
        }

        // Format status
        if ($field === 'status') {
            $statusLabels = [
                'menunggu' => 'Menunggu',
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
            ];
            return $statusLabels[$value] ?? $value;
        }

        // Format jenis cuti
        if ($field === 'jenis_cuti') {
            $jenisLabels = [
                'tahunan' => 'Cuti Tahunan',
                'sakit' => 'Cuti Sakit',
                'penting' => 'Cuti Penting',
                'melahirkan' => 'Cuti Melahirkan',
                'lainnya' => 'Cuti Lainnya',
            ];
            return $jenisLabels[$value] ?? $value;
        }

        // Format boolean (optional)
        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        return $value;
    }

    /**
     * Mendapatkan label untuk nama kolom
     * Mengubah 'user_id' -> 'User ID'
     */
    private function getFieldLabel($field)
    {
        $labels = [
            'status' => 'Status',
            'jenis_cuti' => 'Jenis Cuti',
            'tanggal_mulai' => 'Tanggal Mulai',
            'tanggal_selesai' => 'Tanggal Selesai',
            'durasi' => 'Durasi',
            'keterangan' => 'Keterangan',
            'disetujui_oleh' => 'Disetujui Oleh',
            'catatan_penolakan' => 'Catatan Penolakan',
            'disetujui_pada' => 'Disetujui Pada',
            'catatan_pembatalan' => 'Catatan Pembatalan',
            'disetujui_ada' => 'Disetujui Ada',
        ];

        return $labels[$field] ?? str_replace('_', ' ', ucfirst($field));
    }

    // ============================================
    // ICON & COLOR
    // ============================================

    /**
     * Mendapatkan icon Material Icons berdasarkan action
     */
    public function getActionIconAttribute()
    {
        $icons = [
            'created' => 'add_circle',
            'updated' => 'edit',
            'approved' => 'check_circle',
            'rejected' => 'cancel',
            'deleted' => 'delete',
            'restored' => 'settings_backup', // Menggunakan icon backup/restore
        ];

        return $icons[$this->action] ?? 'history';
    }

    /**
     * Mendapatkan warna warna berdasarkan action
     * created: Blue, updated: Orange, approved: Green, rejected: Red, deleted: Gray
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'created' => 'blue',
            'updated' => 'orange',
            'approved' => 'green',
            'rejected' => 'red',
            'deleted' => 'gray',
            'restored' => 'purple', // Dipulihkan: purple
        ];

        return $colors[$this->action] ?? 'gray';
    }

    // ============================================
    // API FORMATTER
    // ============================================

    /**
     * Mengembalikan data history ke dalam format siap untuk API response
     */
    public function toApiFormat()
    {
        return [
            'id' => $this->id,
            'cuti_id' => $this->cuti_id,
            'action' => $this->action,
            'action_label' => $this->action_label,
            'action_icon' => $this->action_icon,
            'action_color' => $this->action_color,
            'changes' => $this->changes_formatted,
            'changes_text' => $this->changes_text,
            'user_id' => $this->user_id,
            'user_name' => $this->nama_user,
            'user_role' => $this->role_user,
            'note' => $this->note,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->tanggal,
            'is_approval' => $this->isApprovalAction(),
            'is_creation' => $this->isCreationAction(),
            'is_update' => $this->isUpdateAction(),
        ];
    }

    // ============================================
    // STATICS & TIMELINE METHODS
    // ============================================

    /**
     * Method untuk membuat history record baru
     * Static agar bisa dipanggil tanpa instance object
     */
    public static function createHistory($cutiId, $action, $userId, $changes = null, $note = null)
    {
        return self::create([
            'cuti_id' => $cutiId,
            'action' => $action,
            'changes' => $changes,
            'user_id' => $userId,
            'note' => $note,
        ]);
    }

    /**
     * Mendapatkan semua history untuk cuti tertentu
     * Urutkan dari yang baru ke lama (Descending)
     */
    public static function getHistoryForCuti($cutiId)
    {
        return self::forCuti($cutiId)
            ->with('user:id,name,email,role')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mendapatkan timeline history untuk cuti tertentu
     * Urutkan dari yang lama ke baru (Ascending) untuk tampilan timeline
     */
    public static function getTimelineForCuti($cutiId)
    {
        return self::forCuti($cutiId)
            ->with('user:id,name,email,role')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'action' => $history->action_label,
                    'icon' => $history->action_icon,
                    'color' => $history->action_color,
                    'user' => $history->nama_user,
                    'role' => $history->role_user,
                    'note' => $history->note,
                    'changes' => $history->changes_formatted,
                    'timestamp' => $history->created_at->format('Y-m-d H:i:s'),
                    'time_ago' => $history->created_at->diffForHumans(),
                    'date' => $history->created_at->translatedFormat('d F Y'),
                    'time' => $history->created_at->format('H:i'),
                ];
            });
    }

    /**
     * Cek apakah cuti pernah direject sebelumnya
     * Cek aksi 'rejected' di history
     */
    public static function hasRejectionHistory($cutiId)
    {
        return self::actionForCuti($cutiId, 'rejected')->exists();
    }

    /**
     * Mendapatkan catatan penolakan terakhir
     * Mencari catatan penolakan pada aksi rejected terakhir
     */
    public static function getLastRejectionNote($cutiId)
    {
        $rejection = self::actionForCuti($cutiId, 'rejected')
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $rejection ? $rejection->note : null;
    }

    /**
     * Mendapatkan approver terakhir
     * Mencari aksi 'approved' terakhir untuk melihat siapa yang disetujui terakhir
     */
    public static function getLastApprover($cutiId)
    {
        $approval = self::whereIn('action', ['approved', 'rejected'])
            ->where('cuti_id', $cutiId)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $approval ? [
            'user_id' => $approval->user_id,
            'user_name' => $approval->nama_user,
            'action' => $approval->action,
            'timestamp' => $approval->created_at
        ] : null;
    }

}