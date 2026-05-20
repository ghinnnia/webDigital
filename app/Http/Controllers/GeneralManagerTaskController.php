<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class GeneralManagerTaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // FIX: Perbaiki syntax error di line ini
        // $karyawan = User::where('role', operator: 'karyawan')->get(); // SALAH
        $karyawan = User::where('role', 'karyawan')->get(); // BENAR
        
        // Get semua manager divisi
        $managers = User::where('role', 'manager_divisi')->get();
        
        // List of all divisions
        $divisi = [
            'Programmer',
            'Desainer',
            'Digital Marketing'
        ];
        
        return view('general_manajer.kelola_tugas', compact(
            'karyawan', 
            'divisi', 
            'managers'
        ));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'nama_tugas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'sometimes|in:pending,proses,selesai,dibatalkan',
            'target_type' => 'required|in:karyawan,divisi,manager',
            'catatan' => 'nullable|string',
            // TAMBAHKAN priority karena field ini ada di database
            'priority' => 'sometimes|in:low,medium,high',
        ]);
        
        $user = Auth::user();
        $validated['created_by'] = $user->id;
        $validated['assigned_by_manager'] = $user->id;
        $validated['assigned_at'] = now();
        
        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'pending';
        // Set default priority jika tidak disediakan
        $validated['priority'] = $validated['priority'] ?? 'medium';
        // Default judul to nama_tugas if not provided
        $validated['judul'] = $validated['judul'] ?? $validated['nama_tugas'];
        
        // Handle different target types
        if ($request->target_type === 'karyawan' && $request->filled('assigned_to')) {
            $validated['assigned_to'] = $request->assigned_to;
            $validated['target_type'] = 'karyawan';
            $validated['is_broadcast'] = false;
        } elseif ($request->target_type === 'divisi' && $request->filled('target_divisi')) {
            $validated['target_divisi_id'] = $request->target_divisi;
            $validated['target_type'] = 'divisi';
            $validated['is_broadcast'] = true;
        } elseif ($request->target_type === 'manager' && $request->filled('target_manager_id')) {
            $validated['target_manager_id'] = $request->target_manager_id;
            $validated['target_type'] = 'manager';
            $validated['is_broadcast'] = false;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Harap lengkapi data penerima tugas sesuai tipe yang dipilih'
            ], 422);
        }
        
        // FIX: Tambahkan logic untuk auto assign ke manager jika target_type = divisi
        if ($request->target_type === 'divisi' && $request->filled('target_divisi')) {
            // Cari manager divisi yang sesuai
            $manager = User::where('role', 'manager_divisi')
                          ->where('divisi', $request->target_divisi)
                          ->first();
            
            if ($manager) {
                $validated['target_manager_id'] = $manager->id;
                // Auto assign ke manager juga
                if (!isset($validated['assigned_to'])) {
                    $validated['assigned_to'] = $manager->id;
                }
            }
        }
        
        // For editing existing task
        if ($request->filled('id')) {
            $task = Task::findOrFail($request->id);
            
            // Check if user is authorized to edit
            if ($task->created_by != $user->id || $user->role !== 'general_manager') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengedit tugas ini'
                ], 403);
            }
            
            $task->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil diperbarui'
            ]);
        }
        
        Task::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat'
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'nama_tugas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:pending,proses,selesai,dibatalkan',
            'target_type' => 'required|in:karyawan,divisi,manager',
            'catatan' => 'nullable|string',
            // TAMBAHKAN priority
            'priority' => 'required|in:low,medium,high',
        ]);
        
        $task = Task::findOrFail($id);
        $user = Auth::user();
        
        // Default judul to nama_tugas if not provided
        $validated['judul'] = $validated['judul'] ?? $validated['nama_tugas'];
        
        // Check if user is authorized to edit
        if ($task->created_by != $user->id || $user->role !== 'general_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit tugas ini'
            ], 403);
        }
        
        // Handle different target types
        if ($request->target_type === 'karyawan' && $request->filled('assigned_to')) {
            $validated['assigned_to'] = $request->assigned_to;
        } elseif ($request->target_type === 'divisi' && $request->filled('target_divisi')) {
            $validated['target_divisi_id'] = $request->target_divisi;
            $validated['is_broadcast'] = true;
            
            // FIX: Auto assign ke manager divisi
            $manager = User::where('role', 'manager_divisi')
                          ->where('divisi', $request->target_divisi)
                          ->first();
            
            if ($manager) {
                $validated['target_manager_id'] = $manager->id;
                if (!isset($validated['assigned_to'])) {
                    $validated['assigned_to'] = $manager->id;
                }
            }
        } elseif ($request->target_type === 'manager' && $request->filled('target_manager_id')) {
            $validated['target_manager_id'] = $request->target_manager_id;
            $validated['assigned_to'] = $request->target_manager_id;
        }
        
        // Set completed_at if status is 'selesai'
        if ($request->status === 'selesai' && $task->status !== 'selesai') {
            $validated['completed_at'] = now();
        }
        
        $task->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui'
        ]);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,proses,selesai,dibatalkan',
            'catatan_update' => 'nullable|string'
        ]);
        
        $task = Task::findOrFail($id);
        $user = Auth::user();
        
        // FIX: Tambahkan authorization check
        if ($user->role !== 'general_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya General Manager yang dapat mengupdate status semua tugas'
            ], 403);
        }
        
        $task->status = $request->status;
        
        // Add catatan_update if provided
        if ($request->filled('catatan_update')) {
            $task->catatan_update = $request->catatan_update;
        }
        
        // Set completed_at if status is 'selesai'
        if ($request->status === 'selesai' && $task->status !== 'selesai') {
            $task->completed_at = now();
        }
        
        $task->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diupdate'
        ]);
    }
    
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();
        
        // Check if user is authorized to delete
        if ($task->created_by != $user->id || $user->role !== 'general_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus tugas ini'
            ], 403);
        }
        
        $task->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus'
        ]);
    }
    
    public function assignToKaryawan(Request $request, $id)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:users,id'
        ]);
        
        $task = Task::findOrFail($id);
        $user = Auth::user();
        
        // Check if task is broadcast to division
        if (!$task->is_broadcast || $task->target_type !== 'divisi') {
            return response()->json([
                'success' => false,
                'message' => 'Tugas ini bukan tugas broadcast ke divisi'
            ], 422);
        }
        
        // FIX: Tambahkan authorization check
        if ($user->role !== 'general_manager' && $task->created_by !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menugaskan tugas ini'
            ], 403);
        }
        
        // Check if karyawan is in the same division
        $karyawan = User::findOrFail($request->karyawan_id);
        if ($karyawan->divisi_id != $task->target_divisi_id) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak berada di divisi yang sama'
            ], 422);
        }
        
        $task->update([
            'assigned_to' => $request->karyawan_id,
            'assigned_by_manager' => $user->id,
            'assigned_at' => now(),
            'is_broadcast' => false, // No longer a broadcast task
            'target_type' => 'karyawan', // Ubah target type karena sekarang spesifik ke karyawan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil ditugaskan ke karyawan'
        ]);
    }
    
    public function getTasksApi(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'my-tasks');
        
        if ($type === 'my-tasks') {
            // Tasks created by this general manager
            $tasks = Task::where('created_by', $user->id)
                        ->with(['assignedUser', 'creator', 'targetManager'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            // All tasks (general manager bisa lihat semua)
            $tasks = Task::with(['assignedUser', 'creator', 'targetManager'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        }
        
        // Transform data for frontend
        $tasks->transform(function($task) {
            // Determine assignee text based on target_type
            if ($task->target_type === 'karyawan' && $task->assignedUser) {
                $task->assignee_text = $task->assignedUser->name;
                $task->assignee_divisi = $task->assignedUser->divisi ?? '-';
            } elseif ($task->target_type === 'divisi') {
                $divisiName = $task->targetDivisi ? $task->targetDivisi->divisi : '-';
                $task->assignee_text = 'Divisi ' . $divisiName;
                $task->assignee_divisi = $divisiName;
            } elseif ($task->target_type === 'manager' && $task->targetManager) {
                $task->assignee_text = 'Manager: ' . $task->targetManager->name;
                $task->assignee_divisi = $task->targetManager->divisi ?? '-';
            } else {
                $task->assignee_text = '-';
                $task->assignee_divisi = '-';
            }
            
            $task->creator_name = $task->creator ? $task->creator->name : '-';
            $task->is_overdue = $task->deadline && now()->gt($task->deadline) && $task->status !== 'selesai';
            $task->formatted_deadline = $task->deadline ? $task->deadline->format('d M Y H:i') : '-';
            
            return $task;
        });
        
        return response()->json($tasks);
    }
    
    public function getStatistics()
    {
        $user = Auth::user();
        
        // Statistics for all tasks (general manager bisa lihat semua)
        $total = Task::count();
        $completed = Task::where('status', 'selesai')->count();
        $inProgress = Task::where('status', 'proses')->count();
        $pending = Task::where('status', 'pending')->count();
        $cancelled = Task::where('status', 'dibatalkan')->count();
        
        // FIX: Tambahkan statistik overdue
        $overdue = Task::where('deadline', '<', now())
                      ->whereNotIn('status', ['selesai', 'dibatalkan'])
                      ->count();
        
        return response()->json([
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => $pending,
            'cancelled' => $cancelled,
            'overdue' => $overdue
        ]);
    }
    
    // Get task detail
    public function show($id)
    {
        $task = Task::with(['assignedUser', 'creator', 'targetManager', 'assignedByManager'])
                   ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }
    
    // Get karyawan by divisi
    public function getKaryawanByDivisi($divisi)
    {
        $karyawan = User::where('divisi', $divisi)
                       ->where('role', 'karyawan')
                       ->get(['id', 'name', 'email', 'divisi']);
        
        return response()->json($karyawan);
    }
}