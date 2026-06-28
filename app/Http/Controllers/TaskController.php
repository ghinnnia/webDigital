<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment; 
use App\Models\TaskFile;
use App\Models\User;
use App\Models\Project;
use App\Models\Layanan;
use App\Models\Divisi;
use App\Models\TugasKaryawanToManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display list of tasks for admin (dashboard)
     */
/**
 * API untuk Manager Divisi mendapatkan daftar tugas
 */
public function apiGetManagerTasks()
{
    try {
        $user = Auth::user();
        $userDivisiId = $user->divisi_id;
        
        Log::info('Manager Divisi API Get Tasks', [
            'user_id' => $user->id,
            'divisi_id' => $userDivisiId
        ]);
        
        $tasks = Task::with(['assignee', 'creator', 'project', 'targetDivisi'])
            ->where(function($query) use ($user, $userDivisiId) {
                $query->where('target_divisi_id', $userDivisiId)
                      ->orWhere('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id)
                      ->orWhere('assigned_by_manager', $user->id);
            })
            ->orderBy('deadline', 'asc')
            ->get();
        
        // Transform data untuk frontend
        $transformedTasks = $tasks->map(function($task) {
            // TENTUKAN TYPE BERDASARKAN SUBMISSION FILE
            // Jika sudah ada file upload, ini adalah "tugas dari karyawan"
            $type = $task->submission_file ? 'task_from_karyawan' : 'regular';
            
            return [
                'id' => $task->id,
                'judul' => $task->judul,
                'nama_tugas' => $task->nama_tugas,
                'deskripsi' => $task->deskripsi,
                'deadline' => $task->deadline,
                'status' => $task->status,
                'priority' => $task->priority,
                'project_id' => $task->project_id,
                'project_name' => $task->project ? $task->project->nama : null,
                'assigned_to' => $task->assigned_to,
                'assignee_name' => $task->assignee ? $task->assignee->name : null,
                'created_by' => $task->created_by,
                'creator_name' => $task->creator ? $task->creator->name : null,
                'submission_file' => $task->submission_file,
                'submission_url' => $task->submission_file ? Storage::url($task->submission_file) : null,
                'submission_notes' => $task->submission_notes,
                'submitted_at' => $task->submitted_at,
                'catatan' => $task->catatan,
                'is_overdue' => $task->deadline && now()->gt($task->deadline) && !in_array($task->status, ['selesai', 'dibatalkan']),
                'type' => $type,  // ← INI YANG DITAMBAHKAN
                'created_by_name' => $task->creator ? $task->creator->name : null,  // ← TAMBAHKAN JUGA INI
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $transformedTasks,
            'total' => $transformedTasks->count()
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in apiGetManagerTasks: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data tugas: ' . $e->getMessage()
        ], 500);
    }
}

    public function index()
    {
        try {
            $tasks = Task::with(['assignee', 'creator', 'targetManager', 'comments.user', 'files.uploader', 'project', 'targetDivisi'])
                ->whereIn('status', ['pending', 'proses'])
                ->orderBy('deadline', 'asc')
                ->get();
            
            Log::info('Loading all tasks for admin', ['count' => $tasks->count()]);
            
            return view('admin.tasks.index', compact('tasks'));
            
        } catch (\Exception $e) {
            Log::error('Error loading tasks: ' . $e->getMessage());
            return view('admin.tasks.index', ['tasks' => collect([])]);
        }
    }

    /**
 * Karyawan menerima tugas (ubah status dari pending menjadi proses)
 */
public function terimaTugas($id)
{
    try {
        $userId = Auth::id();
        
        // Cari tugas milik karyawan ini
        $task = Task::where('id', $id)
            ->where(function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
            })
            ->first();
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak ditemukan'
            ], 404);
        }
        
        // Update status menjadi 'proses'
        $task->status = 'proses';
        $task->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diterima'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Display tasks for manager divisi
     */
    public function managerTasks()
    {
        try {
            $user = Auth::user();
            $userDivisiId = $user->divisi_id;
            
            Log::info('Loading tasks for manager divisi', [
                'user_id' => $user->id,
                'divisi_id' => $userDivisiId
            ]);
            
            $tasks = Task::with(['assignee', 'creator', 'targetManager', 'comments.user', 'files.uploader', 'project', 'targetDivisi'])
                ->whereIn('status', ['pending', 'proses'])
                ->where(function($query) use ($user, $userDivisiId) {
                    $query->where('target_divisi_id', $userDivisiId)
                          ->orWhere('created_by', $user->id)
                          ->orWhere('assigned_to', $user->id)
                          ->orWhere('assigned_by_manager', $user->id);
                })
                ->orderBy('deadline', 'asc')
                ->get();
            
            return view('manager_divisi.pengelola_tugas', compact('tasks'));
            
        } catch (\Exception $e) {
            Log::error('Error loading manager tasks: ' . $e->getMessage());
            return view('manager_divisi.pengelola_tugas', ['tasks' => collect([])]);
        }
    }
    
    /**
     * Create task page
     */
    public function create()
    {
        $projects = Project::where('status_pengerjaan', '!=', 'selesai')
                          ->orderBy('nama', 'asc')
                          ->get(['id', 'nama', 'layanan_id', 'deadline']);
        
        $layanans = Layanan::orderBy('nama', 'asc')->get(['id', 'nama']);
        
        $divisis = Divisi::orderBy('divisi', 'asc')->get(['id', 'divisi']);
        
        return view('admin.tasks.create', compact('projects', 'layanans', 'divisis'));
    }
    
    /**
     * Edit task page
     */
    public function edit($id)
    {
        $task = Task::with(['assignee', 'creator', 'targetManager', 'project', 'targetDivisi'])->findOrFail($id);
        
        $projects = Project::where('status_pengerjaan', '!=', 'selesai')
                          ->orderBy('nama', 'asc')
                          ->get(['id', 'nama', 'layanan_id', 'deadline']);
        
        $divisis = Divisi::orderBy('divisi', 'asc')->get(['id', 'divisi']);
        
        return view('admin.tasks.edit', compact('task', 'projects', 'divisis'));
    }
    
    /**
     * Karyawan tasks - LIST TUGAS UNTUK KARYAWAN
     */
    public function karyawanTasks(Request $request)
    {
        try {
            $userId = Auth::id();
            
            Log::info('=== KARYAWAN TASKS ===', [
                'user_id' => $userId,
                'user_name' => Auth::user()->name ?? 'Unknown',
            ]);
            
            $tasks = Task::where('assigned_to', $userId)
                ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId])
                ->with(['creator', 'project', 'targetDivisi'])
                ->orderBy('deadline', 'asc')
                ->get();

            Log::info('Karyawan tasks loaded', [
                'user_id' => $userId,
                'count' => $tasks->count(),
            ]);

            return view('karyawan.list', compact('tasks'));
            
        } catch (\Exception $e) {
            Log::error('Error in karyawanTasks: ' . $e->getMessage());
            return view('karyawan.list', [
                'tasks' => collect([]),
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Show detail tugas untuk karyawan
     */
    public function karyawanShow($id)
    {
        try {
            $userId = Auth::id();
            
            $task = Task::where('id', $id)
                ->where(function($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
                })
                ->with(['creator', 'project', 'targetDivisi', 'comments.user', 'files.uploader'])
                ->firstOrFail();
            
            return view('karyawan.detail', compact('task'));
            
        } catch (\Exception $e) {
            // Log::error('Error in karyawanShow: ' . $e->getMessage());
            return redirect()->route('karyawan.tugas.index')
                ->with('error', 'Tugas tidak ditemukan');
        }
    }
    
    /**
     * UPLOAD TUGAS OLEH KARYAWAN - TANPA VALIDASI MANAGER
     */
  public function uploadTaskFile(Request $request, $id)
{
    try {
        $user = Auth::user();
        
        if ($user->role !== 'karyawan') {
            return redirect()->back()->with('error', 'Hanya karyawan yang dapat mengupload tugas');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar|max:10240',
            'notes' => 'nullable|string|max:1000'
        ]);

        $task = Task::find($id);
        if (!$task) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan');
        }

        // Cek apakah tugas milik karyawan ini
        $isAssignedToUser = ((int) $task->assigned_to === (int) $user->id);
        if (!$isAssignedToUser) {
            $assignedIds = $task->assigned_to_ids;
            if (is_string($assignedIds)) {
                $decoded = json_decode($assignedIds, true);
                $assignedIds = is_array($decoded) ? $decoded : [];
            }
            if (is_array($assignedIds)) {
                $isAssignedToUser = in_array((int) $user->id, array_map('intval', $assignedIds), true);
            }
        }

        if (!$isAssignedToUser) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengupload tugas ini');
        }

        // Cek status tugas
        if (in_array($task->status, ['selesai', 'dibatalkan'])) {
            return redirect()->back()->with('error', 'Tugas sudah selesai atau dibatalkan');
        }

        // Handle file upload
        $file = $request->file('file');
        $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $filePath = $file->storeAs('tugas_karyawan', $fileName, 'public');

        // Simpan submission
        $oldSubmission = $task->submission_file;
        $task->submission_file = $filePath;
        $task->submission_notes = $request->input('notes');
        $task->submitted_at = now();
        
        // 🔥 PERUBAHAN: Jangan langsung selesai, tapi "menunggu review"
        $task->status = 'menunggu';  // ← GANTI DARI 'selesai' JADI 'menunggu'
        
        $task->save();

        // Hapus file submission lama jika berbeda
        if ($oldSubmission && $oldSubmission !== $filePath && Storage::disk('public')->exists($oldSubmission)) {
            Storage::disk('public')->delete($oldSubmission);
        }

        return redirect()->route('karyawan.tugas.index')
            ->with('success', 'Tugas berhasil diupload! Menunggu review dari Manager/HR.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Gagal mengupload file: ' . $e->getMessage());
    }
}
    
    /**
     * Store a new task (ADMIN)
     */
    public function store(Request $request)
    {
        try {
            Log::info('=== STORE TASK REQUEST ===', $request->all());
            
            $validator = Validator::make($request->all(), [
                'judul' => 'nullable|string|max:255',
                'nama_tugas' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'deadline' => 'required|date',
                'status' => 'required|in:pending,proses,selesai,dibatalkan',
                'target_type' => 'required|in:karyawan,divisi,manager',
                'assigned_to' => 'nullable|exists:users,id',
                'target_manager_id' => 'nullable|exists:users,id',
                'catatan' => 'nullable|string',
                'project_id' => 'nullable|exists:project,id',
                'target_divisi_id' => 'nullable|integer|exists:divisi,id',
            ]);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $validated = $validator->validated();
            $validated['created_by'] = Auth::id();
            $validated['status'] = $validated['status'] ?? 'pending';
            $validated['judul'] = $validated['judul'] ?? $validated['nama_tugas'];
            
            // Logic divisi
            if ($validated['target_type'] === 'divisi' && !empty($validated['target_divisi_id'])) {
                $manager = User::where('role', 'manager_divisi')
                              ->where('divisi_id', $validated['target_divisi_id'])
                              ->first();
                
                if ($manager) {
                    $validated['target_manager_id'] = $manager->id;
                    $validated['assigned_to'] = $manager->id;
                }
            }
            
            unset($validated['kategori']);
            
            Log::info('Creating task with data:', $validated);
            
            $task = Task::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil dibuat',
                'task' => $task->load(['project', 'assignee', 'creator', 'targetDivisi'])
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error in store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tugas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update task
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'judul' => 'nullable|string|max:255',
                'nama_tugas' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'deadline' => 'required|date',
                'status' => 'required|in:pending,proses,selesai,dibatalkan',
                'assigned_to' => 'nullable|exists:users,id',
                'target_manager_id' => 'nullable|exists:users,id',
                'catatan' => 'nullable|string',
                'project_id' => 'nullable|exists:project,id',
            ]);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $validated = $validator->validated();
            unset($validated['kategori']);
            
            if (!isset($validated['judul']) || empty($validated['judul'])) {
                $validated['judul'] = $validated['nama_tugas'] ?? $task->nama_tugas;
            }

            if ($task->status !== $validated['status']) {
                $validated['catatan_update'] = "Status diubah dari {$task->status} menjadi {$validated['status']} oleh " . Auth::user()->name;
                
                if ($validated['status'] === 'selesai' && !$task->completed_at) {
                    $validated['completed_at'] = now();
                }
            }
            
            $task->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil diperbarui',
                'task' => $task->fresh(['project', 'assignee', 'creator', 'targetDivisi'])
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error in update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tugas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
 * Approve atau revisi tugas dari karyawan (untuk Manager/HR)
 */
public function approveOrRevisiTugasKaryawan(Request $request, $id)
{
    try {
        $task = Task::findOrFail($id);
        $action = $request->input('action');
        $notes = $request->input('notes');
        
        if ($action === 'approved') {
            // APPROVE TUGAS
            $task->status = 'selesai';
            $task->completed_at = now();
            $message = 'Tugas berhasil disetujui';
            
            // Tambahkan catatan approve ke catatan_update
            $approveNote = "[APPROVED] " . now()->format('d/m/Y H:i') . " - Tugas disetujui oleh " . Auth::user()->name . "\n";
            $task->catatan_update = ($task->catatan_update ?? '') . $approveNote;
            
        } elseif ($action === 'returned') {
            // REVISI TUGAS - INI YANG PENTING UNTUK PENILAIAN KESESUAIAN
            
            if (!$notes || trim($notes) === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Keterangan revisi wajib diisi'
                ], 422);
            }
            
            // Hitung jumlah revisi sebelumnya
            $jumlahRevisiSebelum = $this->countRevisi($task->catatan_update);
            $revisiKe = $jumlahRevisiSebelum + 1;
            
            // Format revisi sesuai dengan yang dibaca sistem penilaian
            // PENTING: Gunakan format "REVISI:" agar terbaca oleh sistem KPA
            $revisiText = sprintf(
                "REVISI %d: %s - %s\n",
                $revisiKe,
                now()->format('d/m/Y H:i'),
                $notes
            );
            
            // Simpan ke catatan_update
            $task->catatan_update = ($task->catatan_update ?? '') . $revisiText;
            
            // Ubah status menjadi 'proses' atau 'pending' agar karyawan bisa perbaiki
            $task->status = 'proses';
            
            // Reset submission file agar karyawan upload ulang
            $oldFile = $task->submission_file;
            $task->submission_file = null;
            $task->submission_notes = null;
            $task->submitted_at = null;
            
            // Hapus file lama jika ada
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
            
            $message = 'Revisi berhasil dikirim ke karyawan';
            
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aksi tidak valid'
            ], 400);
        }
        
        $task->save();
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'task' => $task
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in approveOrRevisiTugasKaryawan: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Hitung jumlah revisi dari catatan_update
 */
private function countRevisi($catatan)
{
    if (empty($catatan)) return 0;
    return preg_match_all('/REVISI \d+:/', $catatan, $matches);
}

    /**
     * Store task khusus untuk manager divisi
     */
    public function storeForManager(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            if ($user->role !== 'manager_divisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya manager divisi yang dapat membuat tugas'
                ], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'project_id'        => 'required|exists:project,id',
                'judul'             => 'nullable|string|max:255',
                'nama_tugas'        => 'required|string|max:255',
                'deskripsi'         => 'required|string',
                'deadline'          => 'required|date',
                'assigned_to'       => 'required|exists:users,id',
                'status'            => 'required|in:pending,proses,selesai,dibatalkan',
                'target_divisi_id'  => 'required|integer',
                'catatan'           => 'nullable|string',
            ]);
            
            if ($request->has('target_divisi_id') && $request->target_divisi_id) {
                if (!Divisi::where('id', $request->target_divisi_id)->exists()) {
                    $validator->errors()->add('target_divisi_id', 'Divisi yang dipilih tidak valid.');
                }
            }
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $validated = $validator->validated();
            
            $taskData = [
                'project_id'          => $validated['project_id'],
                'judul'               => $validated['judul'] ?? $validated['nama_tugas'],
                'nama_tugas'          => $validated['nama_tugas'],
                'deskripsi'           => $validated['deskripsi'],
                'deadline'            => $validated['deadline'],
                'assigned_to'         => $validated['assigned_to'],
                'status'              => $validated['status'],
                'target_divisi_id'    => $validated['target_divisi_id'],
                'catatan'             => $validated['catatan'] ?? null,
                'created_by'          => $user->id,
                'assigned_by_manager' => $user->id,
                'target_type'         => 'karyawan',
            ];
            
            $task = Task::create($taskData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil dibuat',
                'task' => $task->load(['project', 'assignee', 'creator', 'targetDivisi'])
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storeForManager: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tugas: ' . $e->getMessage()
            ], 500);
        }
    }
}