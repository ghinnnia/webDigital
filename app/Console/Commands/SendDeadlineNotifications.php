<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDeadlineNotifications extends Command
{
    protected $signature = 'notifications:send-deadline';
    protected $description = 'Kirim notifikasi deadline tugas ke karyawan';

    public function handle()
    {
        $this->info('Mengirim notifikasi deadline tugas...');

        // 1. NOTIFIKASI H-1 DEADLINE (ke karyawan)
        $tomorrow = Carbon::tomorrow();
        
        $upcomingTasks = Task::where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan')
            ->whereDate('deadline', $tomorrow)
            ->where('is_reminded', false)
            ->get();

        foreach ($upcomingTasks as $task) {
            // Cari user (karyawan) yang ditugasi
            $user = User::find($task->assigned_to);
            
            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => '⏰ Pengingat Deadline Tugas',
                    'message' => "Tugas '{$task->judul}' deadline besok, {$task->deadline->format('d M Y H:i')}. Segera selesaikan!",
                    'type' => 'deadline_reminder',
                    'task_id' => $task->id,
                    'is_read' => false
                ]);
                
                $task->update([
                    'is_reminded' => true,
                    'reminder_sent_at' => now(),
                ]);
                
                $this->info("Notifikasi H-1 dikirim untuk tugas: {$task->judul} ke {$user->name}");
            }
        }

        // 2. NOTIFIKASI DEADLINE LEWAT (ke karyawan)
        $overdueTasks = Task::where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan')
            ->where('deadline', '<', now())
            ->where('is_overdue_notified', false)
            ->get();

        foreach ($overdueTasks as $task) {
            $user = User::find($task->assigned_to);
            
            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => '⚠️ Tugas Terlambat',
                    'message' => "Tugas '{$task->judul}' sudah melewati deadline ({$task->deadline->format('d M Y H:i')}). Segera selesaikan!",
                    'type' => 'deadline_warning',
                    'task_id' => $task->id,
                    'is_read' => false
                ]);
                
                $task->update(['is_overdue_notified' => true]);
                $this->info("Notifikasi deadline lewat dikirim untuk tugas: {$task->judul} ke {$user->name}");
            }
        }

        $this->info('Selesai! ' . $upcomingTasks->count() . ' notifikasi H-1, ' . $overdueTasks->count() . ' notifikasi terlambat.');
    }
}