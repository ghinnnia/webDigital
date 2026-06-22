<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationController; // Tambahkan ini agar bisa memanggil notifikasi

class HRTaskController extends Controller
{
   public function index()
{
    $tasks = Task::with(['assignedKaryawan', 'creator'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Hitung statistik
    $totalTasks = $tasks->count();
    $completedTasks = $tasks->where('status', 'selesai')->count();
    $pendingTasks = $tasks->whereIn('status', ['pending', 'proses'])->count();
    $overdueTasks = $tasks->filter(function($task) {
        if (in_array($task->status, ['selesai', 'dibatalkan'])) return false;
        return $task->deadline && now()->gt($task->deadline);
    })->count();

    return view('hr.tasks.index', compact('tasks', 'totalTasks', 'completedTasks', 'pendingTasks', 'overdueTasks'));
}

    public function create()
    {
        $karyawan = Karyawan::where('status_kerja', 'aktif')->get();
        return view('hr.tasks.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'karyawan_id' => 'required|exists:karyawan,id',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        // 🔥 PERBAIKAN: Ambil user_id dari karyawan
        $karyawan = Karyawan::find($request->karyawan_id);
        
        if (!$karyawan || !$karyawan->user_id) {
            return redirect()->back()
                ->with('error', 'Karyawan tidak memiliki akun user!')
                ->withInput();
        }

        $divisiId = $karyawan->divisi_id; // asumsi karyawan punya kolom divisi_id
        $task = Task::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'created_by' => Auth::id(),
            'created_by_role' => 'hr',
            'target_type' => 'karyawan',
            'assigned_to' => $karyawan->user_id,  
            'target_divisi_id' => $divisiId,
            'status' => 'pending'
        ]);

        // 🔥 TAMBAHAN: Pemicu Notifikasi Tugas Baru dari HR
        NotificationController::createNewTaskNotification($task->id, $task->assigned_to);

        return redirect()->route('hr.tasks.index')
            ->with('success', 'Tugas berhasil dibuat!');
    }

    public function show($id)
    {
        $task = Task::with(['assignedKaryawan', 'creator'])
            ->findOrFail($id);

        return view('hr.tasks.show', compact('task'));
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $karyawan = Karyawan::where('status_kerja', 'aktif')->get();
        return view('hr.tasks.edit', compact('task', 'karyawan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'karyawan_id' => 'required|exists:karyawan,id',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        // 🔥 PERBAIKAN: Ambil user_id dari karyawan
        $karyawan = Karyawan::find($request->karyawan_id);

        $task = Task::findOrFail($id);
        $task->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'assigned_to' => $karyawan->user_id,  // 🔥 PAKAI USER_ID
        ]);

        return redirect()->route('hr.tasks.index')
            ->with('success', 'Tugas berhasil diupdate!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,proses,selesai,dibatalkan'
        ]);

        $task = Task::findOrFail($id);
        $task->status = $request->status;

        if ($request->status == 'selesai') {
            $task->completed_at = now();
        }

        $task->save();

        return redirect()->back()->with('success', 'Status berhasil diupdate!');
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('hr.tasks.index')
            ->with('success', 'Tugas berhasil dihapus!');
    }
}