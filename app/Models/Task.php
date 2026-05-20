<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'judul',
        'nama_tugas',
        'deskripsi',
        'deadline',
        'status',
        'priority',
        'assigned_to',
        'assigned_to_ids',
        'created_by',
        'assigned_by_manager',
        'target_type',
        'target_divisi_id',
        'target_manager_id',
      
        'catatan',
        'catatan_update',
        'submission_file',
        'submission_notes',
        'submitted_at',
        'assigned_at',
        'completed_at',
        
        'parent_task_id',
        'project_id',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'assigned_to_ids' => 'array',
    ];

    protected $attributes = [
        'status' => 'pending',
      
        'nama_tugas' => '',
        // 'progress_percentage' => 0, <--- DIHAPUS
    ];

    // ========== RELATIONSHIPS ==========
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to')
                    ->withDefault([
                        'name' => 'Tidak Ditugaskan',
                        'email' => '-'
                    ]);
    }

    // Di dalam model Task, tambahkan method ini:

    public function assignedKaryawan()
    {
    return $this->belongsTo(Karyawan::class, 'assigned_to', 'user_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')
                    ->withDefault([
                        'name' => 'Tidak Diketahui',
                        'email' => '-'
                    ]);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by_manager')
                    ->withDefault([
                        'name' => 'Tidak Diketahui',
                        'email' => '-'
                    ]);
    }

    public function targetManager()
    {
        return $this->belongsTo(User::class, 'target_manager_id')
                    ->withDefault([
                        'name' => 'Tidak Diketahui',
                        'email' => '-'
                    ]);
    }

    public function targetDivisi()
    {
        return $this->belongsTo(Divisi::class, 'target_divisi_id')
                    ->withDefault([
                        'divisi' => 'Tidak Diketahui'
                    ]);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class)->orderBy('created_at', 'desc');
    }

    public function acceptances()
    {
        return $this->hasMany(TaskAcceptance::class)->orderBy('created_at', 'asc');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id')
                    ->withDefault([
                        'nama' => 'Tidak terkait proyek',
                        'layanan_id' => null
                    ]);
    }

    // ========== ACCESSORS ==========
    public function getIsOverdueAttribute()
    {
        return $this->deadline && 
               now()->gt($this->deadline) && 
               !in_array($this->status, ['selesai', 'dibatalkan']);
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->deadline || in_array($this->status, ['selesai', 'dibatalkan'])) {
            return null;
        }
        
        return now()->diffInDays($this->deadline, false);
    }

    public function getFormattedDeadlineAttribute()
    {
        if (!$this->deadline) {
            return '-';
        }
        
        $today = now()->startOfDay();
        $deadlineDate = $this->deadline->startOfDay();
        
        if ($deadlineDate->eq($today)) {
            return 'Hari ini ' . $this->deadline->format('H:i');
        } elseif ($deadlineDate->eq($today->copy()->addDay())) {
            return 'Besok ' . $this->deadline->format('H:i');
        } elseif ($deadlineDate->eq($today->copy()->subDay())) {
            return 'Kemarin ' . $this->deadline->format('H:i');
        }
        
        return $this->deadline->translatedFormat('d M Y H:i');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu',
            'proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
        
        return $labels[$this->status] ?? 'Tidak Diketahui';
    }

    public function getAssigneeNameAttribute()
    {
        if ($this->target_type === 'karyawan' && $this->assignee) {
            return $this->assignee->name;
        } elseif ($this->target_type === 'divisi') {
            $divisiName = $this->targetDivisi ? $this->targetDivisi->divisi : '-';
            return 'Seluruh Divisi ' . $divisiName;
        } elseif ($this->target_type === 'manager' && $this->targetManager) {
            return 'Manager: ' . $this->targetManager->name;
        } elseif ($this->assigned_to && $this->assignee) {
            return $this->assignee->name;
        }
        
        return 'Belum Ditugaskan';
    }

    public function getAssigneeDetailAttribute()
    {
        if ($this->target_type === 'karyawan' && $this->assignee) {
            return $this->assignee->name;
        } elseif ($this->target_type === 'divisi') {
            $divisiName = $this->targetDivisi ? $this->targetDivisi->divisi : '-';
            return 'Divisi: ' . $divisiName;
        } elseif ($this->target_type === 'manager' && $this->targetManager) {
            return 'Manager: ' . $this->targetManager->name;
        }
        
        return '-';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'proses' => 'info',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    // Accessor untuk nama_tugas fallback
    public function getNamaTugasAttribute($value)
    {
        return $value ?? $this->judul;
    }

    public function getFullTaskNameAttribute()
    {
        if ($this->nama_tugas && $this->nama_tugas !== $this->judul) {
            return $this->judul . ' - ' . $this->nama_tugas;
        }
        return $this->judul;
    }

    // Accessor untuk project
    public function getProjectNameAttribute()
    {
        return $this->project ? $this->project->nama : 'Tidak terkait proyek';
    }

    // Accessor untuk pengecekan apakah tugas ditugaskan ke user saat ini
    public function getIsAssignedToMeAttribute()
    {
        if (!auth()->check()) {
            return false;
        }
        
        $userId = auth()->id();
        
        if ($this->target_type === 'divisi' && 
            auth()->user()->divisi_id == $this->target_divisi_id) {
            return true;
        }
        
        if ($this->assigned_to == $userId) {
            return true;
        }
        
        if ($this->target_manager_id == $userId) {
            return true;
        }
        
        return false;
    }

    public function getCanEditAttribute()
    {
        if (!auth()->check()) {
            return false;
        }
        
        $userId = auth()->id();
        $userRole = auth()->user()->role;
        
        if (in_array($userRole, ['admin', 'general_manager'])) {
            return true;
        }
        
        if ($this->created_by == $userId) {
            return true;
        }
        
        if ($this->assigned_by_manager == $userId) {
            return true;
        }
        
        if ($userRole === 'manager_divisi' && 
            auth()->user()->divisi_id == $this->target_divisi_id) {
            return true;
        }
        
        return false;
    }

    public function getCanDeleteAttribute()
    {
        if (!auth()->check()) {
            return false;
        }
        
        $userId = auth()->id();
        $userRole = auth()->user()->role;
        
        if (in_array($userRole, ['admin', 'general_manager'])) {
            return true;
        }
        
        if ($this->created_by == $userId && 
            !$this->assigned_to && 
            $this->status === 'pending') {
            return true;
        }
        
        return false;
    }

    // ========== SCOPES ==========
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProses($query)
    {
        return $query->where('status', 'proses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereNotIn('status', ['selesai', 'dibatalkan']);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('deadline', today())
                    ->whereNotIn('status', ['selesai', 'dibatalkan']);
    }

    public function scopeForDivisi($query, $divisiId)
    {
        return $query->where('target_divisi_id', $divisiId)
                    ->orWhereHas('assignee', function($q) use ($divisiId) {
                        $q->where('divisi_id', $divisiId);
                    });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('assigned_to', $userId)
                    ->orWhere('target_manager_id', $userId)
                    ->orWhere('created_by', $userId);
    }

    public function scopeForManager($query, $managerId)
    {
        return $query->where(function($q) use ($managerId) {
            $q->where('created_by', $managerId)
              ->orWhere('assigned_by_manager', $managerId)
              ->orWhere('target_manager_id', $managerId);
        });
    }

  

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }
        
        return $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('nama_tugas', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%")
              ->orWhere('catatan', 'like', "%{$search}%")
             
              ->orWhereHas('assignee', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('creator', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('project', function($q) use ($search) {
                  $q->where('nama', 'like', "%{$search}%");
              });
        });
    }

    public function scopeFilterByStatus($query, $status)
    {
        if (empty($status) || $status === 'all') {
            return $query;
        }
        
        return $query->where('status', $status);
    }

    public function scopeOrderByDeadline($query, $direction = 'asc')
    {
        return $query->orderBy('deadline', $direction);
    }

    // ========== METHODS ==========
    public function markAsProses()
    {
        return $this->update([
            'status' => 'proses',
            'assigned_at' => now(),
        ]);
    }

    public function markAsSelesai($filePath = null, $notes = null)
    {
        return $this->update([
            'status' => 'selesai',
            'submission_file' => $filePath,
            'submission_notes' => $notes,
            'submitted_at' => now(),
            'completed_at' => now(),
            // 'progress_percentage' => 100, <--- DIHAPUS
        ]);
    }

    public function markAsDibatalkan($reason = null)
    {
        return $this->update([
            'status' => 'dibatalkan',
            'catatan_update' => $reason,
            'completed_at' => now(),
        ]);
    }

    // Fungsi updateProgress DIHAPUS TOTAL karena kolom database tidak ada
    // public function updateProgress(...) { ... }

    public function assignTo($userId, $managerId = null)
    {
        return $this->update([
            'assigned_to' => $userId,
            'assigned_by_manager' => $managerId ?? auth()->id(),
            'assigned_at' => now(),
            'target_type' => 'karyawan',
          
        ]);
    }

   

    // ========== METHOD BARU: FOR API RESPONSE ==========
    public function getApiData()
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'nama_tugas' => $this->nama_tugas,
            'deskripsi' => $this->deskripsi,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'target_type' => $this->target_type,
            'target_divisi_id' => $this->target_divisi_id,
            'target_divisi' => $this->targetDivisi ? $this->targetDivisi->divisi : null,
            'project_id' => $this->project_id,
            'project_name' => $this->project ? $this->project->nama : null,
            'assignee_name' => $this->assignee ? $this->assignee->name : null,
            'assigned_to_name' => $this->assignee ? $this->assignee->name : null,
            'creator_name' => $this->creator ? $this->creator->name : null,
            'is_overdue' => $this->is_overdue,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'catatan' => $this->catatan,
          
          
            'target_manager_id' => $this->target_manager_id,
            'formatted_deadline' => $this->formatted_deadline,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'can_edit' => $this->can_edit,
            'can_delete' => $this->can_delete,
            'is_assigned_to_me' => $this->is_assigned_to_me,
            'assignee_detail' => $this->assignee_detail,
            'full_task_name' => $this->full_task_name,
            // 'progress_percentage' => ..., <--- DIHAPUS
            // 'progress_label' => ..., <--- DIHAPUS
        ];
    }

    // ========== ACCEPTANCE TRACKING HELPERS ==========
    public function getAcceptanceStatus()
    {
        $acceptances = $this->acceptances()->get();
        
        if ($acceptances->isEmpty()) {
            return [
                'total' => 0,
                'accepted' => 0,
                'pending' => 0,
                'rejected' => 0,
                'percentage' => 0,
                'is_fully_accepted' => false
            ];
        }

        $total = $acceptances->count();
        $accepted = $acceptances->where('status', 'accepted')->count();
        $pending = $acceptances->where('status', 'pending')->count();
        $rejected = $acceptances->where('status', 'rejected')->count();
        $percentage = ($total > 0) ? round(($accepted / $total) * 100) : 0;

        return [
            'total' => $total,
            'accepted' => $accepted,
            'pending' => $pending,
            'rejected' => $rejected,
            'percentage' => $percentage,
            'is_fully_accepted' => $accepted === $total && $total > 0,
            'is_any_accepted' => $accepted > 0,
            'is_any_rejected' => $rejected > 0
        ];
    }

    public function getAcceptanceDetails()
    {
        return $this->acceptances()
                    ->with('user:id,name,email')
                    ->get()
                    ->map(function($acceptance) {
                        return [
                            'user_id' => $acceptance->user_id,
                            'user_name' => $acceptance->user->name,
                            'user_email' => $acceptance->user->email,
                            'status' => $acceptance->status,
                            'accepted_at' => $acceptance->accepted_at,
                            'notes' => $acceptance->notes
                        ];
                    });
    }

    // ========== Boot method untuk event handling ==========
      protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->status)) {
                $task->status = 'pending';
            }
            
            // Hanya isi kategori jika kolomnya ADA di database
            if (Schema::hasColumn('tasks', 'kategori') && $task->project_id && empty($task->kategori)) {
                $project = Project::find($task->project_id);
                if ($project && $project->layanan) {
                    $task->kategori = $project->layanan->nama;
                }
            }
        });
    }

    // ========== Appended attributes for API ==========
    protected $appends = [
        'is_overdue',
        'days_remaining',
        'status_label',
        'formatted_deadline',
        'assignee_name',
        'assignee_detail',
        'status_color',
        // 'progress_label', <--- DIHAPUS
        'is_assigned_to_me',
        'can_edit',
        'can_delete',
        'project_name',
        'full_task_name',
    ];
}