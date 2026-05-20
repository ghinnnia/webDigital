<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAcceptance;
use App\Models\User;
use App\Models\Project;
use App\Models\Divisi;
use App\Models\TaskFile;
use App\Models\TugasKaryawanToManager;
use App\Models\Notification;
use App\Models\TugasApprovalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ManagerDivisiTaskController extends Controller
{
    /**
     * Menampilkan halaman kelola tugas (View HTML)
     */
    public function index()
    {
        $user = Auth::user();
        return view('manager_divisi.kelola_tugas', [
            'user' => $user
        ]);
    }

    /**
     * Menampilkan halaman tugas dari karyawan
     */
    public function tugasDariKaryawan()
    {
        $user = Auth::user();
        return view('manager_divisi.tugas-dari-karyawan', [
            'user' => $user
        ]);
    }

    /**
     * ==============================================
     * API UNTUK TUGAS DARI KARYAWAN KE MANAGER
     * ==============================================
     */

    /**
     * API: Mendapatkan daftar tugas dari karyawan untuk Manager Divisi
     */
    public function getTasksFromKaryawan(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'manager_divisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya manager divisi yang dapat mengakses'
                ], 403);
            }

            Log::info('API: Get tasks from karyawan for manager', [
                'user_id' => $user->id,
                'divisi_id' => $user->divisi_id
            ]);

            $query = TugasKaryawanToManager::with(['karyawan:id,name,email', 'project:id,nama'])
                ->where('manager_divisi_id', $user->id)
                ->orderBy('created_at', 'desc');

            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->has('project_id') && $request->project_id !== 'all') {
                $query->where('project_id', $request->project_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('nama_tugas', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhereHas('karyawan', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('project', function($q) use ($search) {
                          $q->where('nama', 'like', "%{$search}%");
                      });
                });
            }

            $tasks = $query->get()->map(function($task) {
                $isOverdue = false;
                if ($task->deadline && !in_array($task->status, ['selesai', 'dibatalkan'])) {
                    try {
                        $isOverdue = \Carbon\Carbon::parse($task->deadline)->isPast();
                    } catch (\Exception $e) {
                        $isOverdue = false;
                    }
                }

                return [
                    'id' => $task->id,
                    'karyawan_id' => $task->karyawan_id,
                    'karyawan_name' => $task->karyawan->name ?? 'Unknown',
                    'project_id' => $task->project_id,
                    'project_name' => $task->project->nama ?? 'Tidak ada Project',
                    'judul' => $task->judul,
                    'nama_tugas' => $task->nama_tugas,
                    'deskripsi' => $task->deskripsi,
                    'deadline' => $task->deadline ? $task->deadline->format('Y-m-d H:i:s') : null,
                    'status' => $task->status,
                    'catatan' => $task->catatan,
                    'lampiran' => $task->lampiran,
                    'lampiran_url' => $task->lampiran ? Storage::url($task->lampiran) : null,
                    'is_overdue' => $isOverdue,
                    'created_at' => $task->created_at ? $task->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $task->updated_at ? $task->updated_at->format('Y-m-d H:i:s') : null,
                ];
            });

            Log::info('Tasks from karyawan loaded', ['count' => $tasks->count()]);

            return response()->json([
                'success' => true,
                'data' => $tasks,
                'count' => $tasks->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getTasksFromKaryawan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tugas dari karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Statistik tugas dari karyawan
     */
    public function getTasksFromKaryawanStatistics(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'manager_divisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $total = TugasKaryawanToManager::where('manager_divisi_id', $user->id)->count();
            $pending = TugasKaryawanToManager::where('manager_divisi_id', $user->id)
                ->where('status', 'pending')
                ->count();
            $proses = TugasKaryawanToManager::where('manager_divisi_id', $user->id)
                ->where('status', 'proses')
                ->count();
            $selesai = TugasKaryawanToManager::where('manager_divisi_id', $user->id)
                ->where('status', 'selesai')
                ->count();
            $dibatalkan = TugasKaryawanToManager::where('manager_divisi_id', $user->id)
                ->where('status', 'dibatalkan')
                ->count();
            
            $overdue = TugasKaryawanToManager::where('manager_divisi_id', $user->id)
                ->where('status', '!=', 'selesai')
                ->where('status', '!=', 'dibatalkan')
                ->whereDate('deadline', '<', now())
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'pending' => $pending,
                    'proses' => $proses,
                    'selesai' => $selesai,
                    'dibatalkan' => $dibatalkan,
                    'overdue' => $overdue,
                    'in_progress' => $proses
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getTasksFromKaryawanStatistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Approve tugas dari karyawan (DIPERBAIKI)
     */
   public function approveTaskFromKaryawan(Request $request, $id)
{
    try {
        $user = Auth::user();
        
        if ($user->role !== 'manager_divisi') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya manager divisi yang dapat menyetujui tugas'
            ], 403);
        }

        $task = Task::find($id);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'action' => 'required|in:approved,rejected,returned',
            'notes' => 'nullable|string|required_if:action,returned',
            'status' => 'required_if:action,approved|in:proses,selesai,dibatalkan'
        ]);

        // Inisialisasi variabel message agar tidak undefined
        $message = '';

        if ($validated['action'] === 'approved') {
            // ========== APPROVE TUGAS ==========
            $task->status = $validated['status'];
            $task->completed_at = now();
            
            $approveNote = "[APPROVED] " . now()->format('d/m/Y H:i') . " - Disetujui oleh " . $user->name . "\n";
            $task->catatan_update = ($task->catatan_update ?? '') . $approveNote;
            
            $task->save();
            $message = 'Tugas berhasil disetujui';
        } 
        elseif ($validated['action'] === 'rejected') {
            // ========== REJECT TUGAS ==========
            $task->status = 'dibatalkan';
            $rejectNote = "[REJECTED] " . now()->format('d/m/Y H:i') . " - Ditolak oleh " . $user->name . "\n";
            $task->catatan_update = ($task->catatan_update ?? '') . $rejectNote;
            $task->save();
            $message = 'Tugas ditolak';
        } 
        elseif ($validated['action'] === 'returned') {
            // ========== RETURN/REVISI TUGAS ==========
            $catatanLama = $task->catatan_update ?? '';
            
            // Gunakan helper countRevisi (pastikan fungsi ini ada di controller Anda)
            $jumlahRevisiSebelum = method_exists($this, 'countRevisi') ? $this->countRevisi($catatanLama) : 0;
            $revisiKe = $jumlahRevisiSebelum + 1;
            
            $revisiBaru = sprintf(
                "REVISI %d: %s - %s\n",
                $revisiKe,
                now()->format('d/m/Y H:i'),
                $validated['notes']
            );
            
            $task->catatan_update = $catatanLama . $revisiBaru;
            
            // Kembalikan status ke 'proses' agar karyawan bisa mengupload ulang
            $task->status = 'proses';
            $task->save();
            
            $message = 'Tugas berhasil dikembalikan untuk revisi';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $task
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Tangani error validasi agar pesan errornya jelas (notes kosong dll)
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error approveTaskFromKaryawan: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Hitung jumlah revisi dari catatan_update
     */
    private function countRevisi($catatan)
    {
        if (empty($catatan)) return 0;
        preg_match_all('/REVISI \d+[: -]/', $catatan, $matches);
        return count($matches[0] ?? []);
    }

    /**
     * API: Detail tugas dari karyawan
     */
    public function getTaskFromKaryawanDetail($id)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'manager_divisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $task = TugasKaryawanToManager::with(['karyawan:id,name,email', 'project:id,nama,deskripsi', 'approvalHistory.approver:id,name'])
                ->where('manager_divisi_id', $user->id)
                ->findOrFail($id);

            $isOverdue = false;
            if ($task->deadline && !in_array($task->status, ['selesai', 'dibatalkan'])) {
                try {
                    $isOverdue = \Carbon\Carbon::parse($task->deadline)->isPast();
                } catch (\Exception $e) {
                    $isOverdue = false;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'karyawan' => $task->karyawan,
                    'project' => $task->project,
                    'judul' => $task->judul,
                    'nama_tugas' => $task->nama_tugas,
                    'deskripsi' => $task->deskripsi,
                    'deadline' => isset($task->deadline) ? (string)$task->deadline : null,
                    'status' => $task->status,
                    'catatan' => $task->catatan,
                    'lampiran' => $task->lampiran,
                    'lampiran_url' => $task->lampiran ? Storage::url($task->lampiran) : null,
                    'is_overdue' => $isOverdue,
                    'approval_history' => $task->approvalHistory,
                    'created_at' => $task->created_at ? $task->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $task->updated_at ? $task->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getTaskFromKaryawanDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Daftar karyawan dalam divisi
     */
    public function getKaryawanInDivisi()
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'manager_divisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $karyawan = User::where('role', 'karyawan')
                ->where('divisi_id', $user->divisi_id)
                ->select('id', 'name', 'email', 'divisi')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $karyawan
            ]);

        } catch (\Exception $e) {
            Log::error('Error getKaryawanInDivisi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ==============================================
     * API UNTUK TUGAS BIASA (Manager ke Karyawan)
     * ==============================================
     */

    /**
     * API: Mendapatkan daftar tugas untuk Manager Divisi
     */
    public function getTasksApi(Request $request)
    {
        try {
            $user = Auth::user();
            
            Log::info('API getTasksApi called', ['user_id' => $user->id]);
            
            $tasks = Task::where('target_divisi_id', $user->divisi_id)
                ->orWhere('created_by', $user->id)
                ->orWhere('assigned_by_manager', $user->id)
                ->select('id', 'judul', 'nama_tugas', 'deskripsi', 'deadline', 'status', 'priority', 'project_id', 'assigned_to', 'assigned_to_ids', 'submission_file', 'submitted_at', 'catatan_update', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $tasksFromKaryawan = TugasKaryawanToManager::where('manager_divisi_id', $user->id)
                ->select('id', 'karyawan_id', 'judul', 'nama_tugas', 'deskripsi', 'deadline', 'status', 'lampiran', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $transformedTasks = $tasks->map(function($task) {
                // Dapatkan nama assignee dari assigned_to_ids
                $assigneeNames = $this->getAssigneeNames($task);
                
                return [
                    'id' => $task->id,
                    'type' => 'regular',
                    'judul' => $task->judul,
                    'nama_tugas' => $task->nama_tugas,
                    'deskripsi' => $task->deskripsi,
                    'deadline' => $task->deadline,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'project_name' => $task->project ? $task->project->nama : null,
                    'assignee_name' => $assigneeNames,
                    'assigned_names' => $assigneeNames,
                    'assigned_to_ids' => $task->assigned_to_ids,
                    'submission_file' => $task->submission_file,
                    'submitted_at' => $task->submitted_at,
                    'catatan_update' => $task->catatan_update,
                    'created_at' => $task->created_at,
                ];
            });
            
            $transformedFromKaryawan = $tasksFromKaryawan->map(function($task) {
                return [
                    'id' => $task->id,
                    'type' => 'task_from_karyawan',
                    'judul' => $task->judul,
                    'nama_tugas' => $task->nama_tugas,
                    'deskripsi' => $task->deskripsi,
                    'deadline' => $task->deadline,
                    'status' => $task->status,
                    'project_name' => $task->project ? $task->project->nama : null,
                    'created_by_name' => $task->karyawan ? $task->karyawan->name : null,
                    'submission_file' => $task->lampiran,
                    'created_at' => $task->created_at,
                ];
            });
            
            $allTasks = array_merge($transformedTasks->toArray(), $transformedFromKaryawan->toArray());
            
            return response()->json([
                'success' => true,
                'data' => $allTasks,
                'total' => count($allTasks)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getTasksApi: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Dapatkan nama-nama assignee dari assigned_to_ids
     */
    private function getAssigneeNames($task)
    {
        try {
            $assignedIds = $task->assigned_to_ids;
            
            if (!$assignedIds) {
                return $task->assignee ? $task->assignee->name : 'Belum ditugaskan';
            }
            
            if (is_string($assignedIds)) {
                $assignedIds = json_decode($assignedIds, true) ?? [];
            }
            
            if (!is_array($assignedIds) || empty($assignedIds)) {
                return $task->assignee ? $task->assignee->name : 'Belum ditugaskan';
            }
            
            $users = User::whereIn('id', $assignedIds)->pluck('name', 'id')->toArray();
            
            $names = [];
            foreach ($assignedIds as $id) {
                if (isset($users[$id])) {
                    $names[] = $users[$id];
                }
            }
            
            return !empty($names) ? implode(', ', $names) : 'Belum ditugaskan';
        } catch (\Exception $e) {
            return $task->assignee ? $task->assignee->name : 'Belum ditugaskan';
        }
    }

    /**
     * API: Dropdown Projects
     */
    public function getProjectsDropdown(Request $request)
    {
        try {
            $user = Auth::user();
            
            $projects = Project::assignedToUser($user->id)
                ->whereNull('deleted_at')
                ->select(['id', 'nama', 'deskripsi', 'tanggal_selesai_pengerjaan', 'status_pengerjaan', 'harga', 'progres'])
                ->orderBy('nama', 'asc')
                ->get();
            
            $mappedProjects = $projects->map(function($project) {
                return [
                    'id' => $project->id,
                    'nama' => $project->nama,
                    'name' => $project->nama,
                    'nama_project' => $project->nama,
                    'deskripsi' => $project->deskripsi ?? '',
                    'description' => $project->deskripsi ?? '',
                    'deskripsi_project' => $project->deskripsi ?? '',
                    'deadline' => $project->tanggal_selesai_pengerjaan ? $project->tanggal_selesai_pengerjaan->format('Y-m-d H:i:s') : null,
                    'tanggal_selesai' => $project->tanggal_selesai_pengerjaan ? $project->tanggal_selesai_pengerjaan->format('Y-m-d H:i:s') : null,
                    'harga' => $project->harga,
                    'budget' => $project->harga,
                    'progres' => $project->progres,
                    'progress' => $project->progres,
                    'status' => $project->status_pengerjaan,
                    'status_pengerjaan' => $project->status_pengerjaan
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $mappedProjects
            ]);

        } catch (\Exception $e) {
            Log::error('Error getProjectsDropdown: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengambil data project: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * API: Dropdown Karyawan
     */
    public function getKaryawanDropdown(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated',
                    'data' => []
                ], 401);
            }
            
            if (empty($user->divisi_id)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'User tidak terhubung ke divisi'
                ]);
            }
            
            $karyawan = User::where('role', 'karyawan')
                ->where('divisi_id', (int)$user->divisi_id)
                ->orderBy('name')
                ->select('id', 'name', 'email', 'divisi_id')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $karyawan
            ]);
        } catch (\Throwable $e) {
            Log::error('ERROR in getKaryawanDropdown', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store: Membuat Tugas Baru
     */
    public function createTask(Request $request)
    {
        try {
            $user = Auth::user();
            
            $assignedToInput = $request->input('assigned_to');
            
            $assignedToValues = [];
            if (is_array($assignedToInput)) {
                $assignedToValues = $assignedToInput;
            } elseif (is_string($assignedToInput) && !empty($assignedToInput)) {
                $decoded = json_decode($assignedToInput, true);
                if (is_array($decoded)) {
                    $assignedToValues = $decoded;
                } else {
                    $assignedToValues = [$assignedToInput];
                }
            }
            
            if (empty($assignedToValues)) {
                throw new \Exception('Pilih minimal satu karyawan untuk ditugaskan');
            }
            
            $validator = Validator::make($request->all(), [
                'project_id'        => 'nullable|exists:project,id',
                'judul'             => 'nullable|string|max:255',
                'nama_tugas'        => 'required|string|max:255',
                'deskripsi'         => 'required|string',
                'deadline'          => 'required|date',
                'target_divisi_id'  => 'nullable|exists:divisi,id',
                'status'            => 'nullable|in:pending,proses,selesai,dibatalkan',
                'priority'          => 'nullable|in:low,medium,high,urgent',
                'catatan'           => 'nullable|string',
                'attachment'        => 'nullable|file|max:10240',
            ]);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $validated = $validator->validated();
            
            $targetDivisiId = $validated['target_divisi_id'] ?? $user->divisi_id;
            
            // Validasi setiap karyawan
            foreach ($assignedToValues as $karyawanId) {
                $karyawan = User::where('id', (int)$karyawanId)->where('role', 'karyawan')->first();
                if (!$karyawan) {
                    throw new \Exception("Karyawan dengan ID {$karyawanId} tidak ditemukan");
                }
                if ((int)$karyawan->divisi_id !== (int)$targetDivisiId) {
                    throw new \Exception("Karyawan {$karyawan->name} bukan bagian dari divisi tujuan");
                }
            }
            
            $baseTaskData = [
                'project_id'          => $validated['project_id'] ?? null,
                'judul'               => $validated['judul'] ?? $validated['nama_tugas'],
                'nama_tugas'          => $validated['nama_tugas'],
                'deskripsi'           => $validated['deskripsi'],
                'deadline'            => $validated['deadline'],
                'target_divisi_id'    => $targetDivisiId,
                'status'              => $validated['status'] ?? 'pending',
                'priority'            => $validated['priority'] ?? 'medium',
                'catatan'             => $validated['catatan'] ?? null,
                'created_by'          => $user->id,
                'assigned_by_manager' => $user->id,
                'target_type'         => 'karyawan',
                'is_broadcast'        => false,
            ];

            $attachmentMeta = null;
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $file = $request->file('attachment');
                $attachmentMeta = [
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $file->store('tasks', 'public'),
                ];
            }

            DB::beginTransaction();
            $createdTasks = [];
            foreach ($assignedToValues as $assigneeId) {
                $taskData = $baseTaskData;
                $taskData['assigned_to'] = (int)$assigneeId;
                $taskData['assigned_to_ids'] = $assignedToValues;

                $task = Task::create($taskData);
                $createdTasks[] = $task;

                if ($attachmentMeta) {
                    TaskFile::create([
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'filename' => $attachmentMeta['filename'],
                        'original_name' => $attachmentMeta['filename'],
                        'path' => $attachmentMeta['path'],
                        'size' => $attachmentMeta['size'],
                        'mime_type' => $attachmentMeta['mime_type'],
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($createdTasks) > 1
                    ? 'Tugas berhasil dibuat untuk ' . count($createdTasks) . ' karyawan'
                    : 'Tugas berhasil dibuat',
                'count' => count($createdTasks)
            ]);
            
        } catch (ValidationException $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Gagal membuat tugas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update: Mengupdate Tugas
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            
            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'nama_tugas' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'deadline' => 'required|date',
                'status' => 'required|in:pending,proses,selesai,dibatalkan',
                'project_id' => 'nullable|exists:project,id',
                'catatan' => 'nullable|string'
            ]);
            
            if (!isset($validated['judul']) || empty($validated['judul'])) {
                $validated['judul'] = $validated['nama_tugas'];
            }
            
            $task->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil diupdate',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengupdate tugas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Destroy: Menghapus Tugas
     */
    public function destroy($id)
    {
        try {
            $task = Task::withTrashed()->find($id);
            
            if (!$task) {
                throw new \Exception("Task dengan ID {$id} tidak ditemukan");
            }
            
            if ($task->trashed()) {
                $task->forceDelete();
                $message = 'Tugas berhasil dihapus permanen';
            } else {
                $task->delete();
                $message = 'Tugas berhasil dihapus';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus tugas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show: Detail Tugas (JSON)
     */
    public function show($id)
    {
        try {
            $task = Task::with(['assignee:id,name,email,divisi_id', 'project:id,nama,deskripsi', 'creator:id,name', 'targetDivisi:id,divisi'])
                ->find($id);
            
            if (!$task) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Tugas tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengambil detail tugas'
            ], 500);
        }
    }
    
    /**
     * API: Statistik Tugas
     */
    public function getStatistics()
    {
        try {
            $user = Auth::user();
            
            $query = Task::where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_by_manager', $user->id);
                
                if (!empty($user->divisi_id)) {
                    $q->orWhere('target_divisi_id', $user->divisi_id);
                }
            });

            $total = (clone $query)->count();
            $completed = (clone $query)->where('status', 'selesai')->count();
            $pending = (clone $query)->where('status', 'pending')->count();
            $proses = (clone $query)->where('status', 'proses')->count();
            $dibatalkan = (clone $query)->where('status', 'dibatalkan')->count();
            
            $overdue = (clone $query)
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'completed' => $completed,
                    'pending' => $pending,
                    'proses' => $proses,
                    'dibatalkan' => $dibatalkan,
                    'overdue' => $overdue,
                    'in_progress' => $pending + $proses
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }
}