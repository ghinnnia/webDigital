<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role;
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        // Tambahan untuk statistik notifikasi per role
        $stats = [];
        
        if ($userRole == 'karyawan') {
            $stats = [
                'total' => $notifications->total(),
                'unread' => $unreadCount,
                'read' => $notifications->total() - $unreadCount,
                'new_tasks' => Notification::where('user_id', $user->id)
                    ->where('type', 'new_task')
                    ->where('is_read', false)
                    ->count(),
                'payroll' => Notification::where('user_id', $user->id)
                    ->where('type', 'payroll')
                    ->where('is_read', false)
                    ->count(),
                'deadline_warning' => Notification::where('user_id', $user->id)
                    ->where('type', 'deadline_warning')
                    ->where('is_read', false)
                    ->count()
            ];
        } elseif ($userRole == 'hr') {
            $stats = [
                'total' => $notifications->total(),
                'unread' => $unreadCount,
                'read' => $notifications->total() - $unreadCount,
                'deadline_warning' => Notification::where('user_id', $user->id)
                    ->where('type', 'deadline_warning')
                    ->where('is_read', false)
                    ->count(),
                'task_submitted' => Notification::where('user_id', $user->id)
                    ->where('type', 'task_submitted')
                    ->where('is_read', false)
                    ->count()
            ];
        } elseif ($userRole == 'manager_divisi') {
            $stats = [
                'total' => $notifications->total(),
                'unread' => $unreadCount,
                'read' => $notifications->total() - $unreadCount,
                'task_submitted' => Notification::where('user_id', $user->id)
                    ->where('type', 'task_submitted')
                    ->where('is_read', false)
                    ->count(),
                'task_revision' => Notification::where('user_id', $user->id)
                    ->where('type', 'task_revision')
                    ->where('is_read', false)
                    ->count()
            ];
        }
        
        return view('notifications.index', compact('notifications', 'unreadCount', 'stats', 'userRole'));
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->update(['is_read' => true]);
        
        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca']);
    }
    
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca']);
        }
        
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }
    
    public function destroy($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->delete();
        
        return response()->json(['success' => true, 'message' => 'Notifikasi dihapus']);
    }
    
    /**
     * Create notification for new task (dipanggil saat tugas dibuat)
     */
    public static function createNewTaskNotification($taskId, $assignedTo)
    {
        $task = Task::find($taskId);
        if (!$task) return;
        
        $user = User::find($assignedTo);
        if (!$user) return;
        
        $creator = auth()->user();
        $creatorName = $creator ? $creator->name : 'Admin';
        $creatorRole = $creator ? ($creator->role == 'hr' ? 'HRD' : 'Manager') : 'Admin';
        
        // Pengaman format deadline agar tidak crash jika berupa string dari DB
        $deadlineText = 'Tidak ada deadline';
        if ($task->deadline) {
            try {
                $deadlineText = $task->deadline instanceof Carbon 
                    ? $task->deadline->format('d M Y H:i') 
                    : Carbon::parse($task->deadline)->format('d M Y H:i');
            } catch (\Exception $e) {
                $deadlineText = $task->deadline;
            }
        }
        
        Notification::create([
            'user_id' => $assignedTo,
            'title' => '📋 Tugas Baru: ' . $task->judul,
            'message' => "Anda mendapatkan tugas baru dari {$creatorName} ({$creatorRole}): {$task->judul}. Deadline: {$deadlineText}",
            'type' => 'new_task',
            'task_id' => $taskId,
            'is_read' => false,
            'link' => route('karyawan.tugas.show', $taskId),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Create notification for payroll (dipanggil saat slip gaji dibuat)
     */
    public static function createPayrollNotification($userId, $payrollId, $period, $total)
    {
        Notification::create([
            'user_id' => $userId,
            'title' => '📄 Slip Gaji ' . $period,
            'message' => "Slip gaji untuk periode {$period} telah tersedia. Total: Rp " . number_format($total, 0, ',', '.'),
            'type' => 'payroll',
            'is_read' => false,
            'link' => route('karyawan.payroll.show', $payrollId),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Create notification for task reminder (deadline approaching)
     */
    public static function createDeadlineReminderNotification($taskId)
    {
        $task = Task::find($taskId);
        if (!$task) return;
        
        // Konversi aman untuk deadline ke Carbon objek
        $deadlineObj = null;
        if ($task->deadline) {
            $deadlineObj = $task->deadline instanceof Carbon ? $task->deadline : Carbon::parse($task->deadline);
        }
        
        $daysLeft = $deadlineObj ? now()->diffInDays($deadlineObj, false) : null;
        
        if ($daysLeft !== null && $daysLeft >= 0) {
            $message = "Tugas \"{$task->judul}\" deadline dalam {$daysLeft} hari lagi. Jangan lupa untuk menyelesaikannya!";
            if ($daysLeft == 0) {
                $message = "⚠️ Tugas \"{$task->judul}\" deadline HARI INI! Segera selesaikan!";
            } elseif ($daysLeft == 1) {
                $message = "⏰ Tugas \"{$task->judul}\" deadline BESOK! Segera selesaikan!";
            }
            
            Notification::create([
                'user_id' => $task->assigned_to,
                'title' => '⏰ Pengingat Deadline',
                'message' => $message,
                'type' => 'deadline_reminder',
                'task_id' => $taskId,
                'is_read' => false,
                'link' => route('karyawan.tugas.show', $taskId),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    
    /**
     * Create notification for task submission (dipanggil saat tugas diupload)
     */
    public static function createTaskSubmittedNotification($taskId, $userId)
    {
        $task = Task::find($taskId);
        if (!$task) return;
        
        $user = User::find($userId);
        $taskCreator = User::find($task->created_by);
        
        if (!$taskCreator) return;
        
        Notification::create([
            'user_id' => $task->created_by,
            'title' => '📤 Tugas Dikumpulkan',
            'message' => "Karyawan {$user->name} telah mengumpulkan tugas \"{$task->judul}\". Silakan review.",
            'type' => 'task_submitted',
            'task_id' => $taskId,
            'is_read' => false,
            'link' => route($taskCreator->role == 'hr' ? 'hr.tasks.show' : 'manager.tasks.show', $taskId),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Create notification for task revision
     */
    public static function createTaskRevisionNotification($taskId, $userId)
    {
        $task = Task::find($taskId);
        if (!$task) return;
        
        $user = User::find($userId);
        $creator = User::find($task->created_by);
        
        Notification::create([
            'user_id' => $task->assigned_to,
            'title' => '📝 Tugas Perlu Revisi',
            'message' => "Tugas \"{$task->judul}\" perlu direvisi. Silakan cek komentar dari {$creator->name}.",
            'type' => 'task_revision',
            'task_id' => $taskId,
            'is_read' => false,
            'link' => route('karyawan.tugas.show', $taskId),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Create notification for task approval
     */
    public static function createTaskApprovedNotification($taskId, $userId)
    {
        $task = Task::find($taskId);
        if (!$task) return;
        
        $user = User::find($userId);
        $creator = User::find($task->created_by);
        
        Notification::create([
            'user_id' => $task->assigned_to,
            'title' => '✅ Tugas Disetujui',
            'message' => "Tugas \"{$task->judul}\" telah disetujui oleh {$creator->name}. Selamat!",
            'type' => 'task_approved',
            'task_id' => $taskId,
            'is_read' => false,
            'link' => route('karyawan.tugas.show', $taskId),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Get unread count for API (for auto refresh)
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        $payrollCount = Notification::where('user_id', auth()->id())
            ->where('type', 'payroll')
            ->where('is_read', false)
            ->count();
        
        $newTaskCount = Notification::where('user_id', auth()->id())
            ->where('type', 'new_task')
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'count' => $count,
            'payroll_count' => $payrollCount,
            'new_task_count' => $newTaskCount
        ]);
    }
}