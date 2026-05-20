<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    protected $signature = 'tasks:send-reminders';
    protected $description = 'Kirim notifikasi untuk tugas yang deadline-nya mendekat atau sudah lewat';

    public function handle()
    {
        $this->info('Mengirim notifikasi tugas...');

        // 1. KIRIM NOTIFIKASI H-1 DEADLINE (KE KARYAWAN)
        $tomorrow = Carbon::tomorrow();
        
        $upcomingTasks = Task::where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan')
            ->whereDate('deadline', $tomorrow)
            ->where('is_reminded', false)
            ->get();

        foreach ($upcomingTasks as $task) {
            if ($task->assigned_to) {
                $karyawan = Karyawan::find($task->assigned_to);
                if ($karyawan && $karyawan->user_id) {
                    Notification::create([
                        'user_id' => $karyawan->user_id,
                        'title' => '⏰ Pengingat Deadline Tugas',
                        'message' => "Tugas '{$task->judul}' deadline besok, {$task->deadline->format('d M Y H:i')}. Segera selesaikan!",
                        'type' => 'task_reminder',
                        'task_id' => $task->id,
                    ]);
                    
                    $task->update([
                        'is_reminded' => true,
                        'reminder_sent_at' => now(),
                    ]);
                    
                    $this->info("Notifikasi H-1 dikirim untuk tugas: {$task->judul}");
                }
            }
        }

        // 2. KIRIM NOTIFIKASI DEADLINE LEWAT
        $overdueTasks = Task::where('status', '!=', 'selesai')
            ->where('status', '!=', 'dibatalkan')
            ->where('deadline', '<', now())
            ->where('is_overdue_notified', false)
            ->get();

        foreach ($overdueTasks as $task) {
            // Notifikasi ke karyawan
            if ($task->assigned_to) {
                $karyawan = Karyawan::find($task->assigned_to);
                if ($karyawan && $karyawan->user_id) {
                    Notification::create([
                        'user_id' => $karyawan->user_id,
                        'title' => '⚠️ Tugas Terlambat',
                        'message' => "Tugas '{$task->judul}' sudah melewati deadline ({$task->deadline->format('d M Y H:i')}). Segera selesaikan!",
                        'type' => 'deadline_warning',
                        'task_id' => $task->id,
                    ]);
                }
            }
            
            // Notifikasi ke HR
            $hrUsers = User::where('role', 'hr')->get();
            foreach ($hrUsers as $hr) {
                Notification::create([
                    'user_id' => $hr->id,
                    'title' => '⚠️ Tugas Karyawan Terlambat',
                    'message' => "Tugas '{$task->judul}' yang diberikan ke " . ($task->assignedKaryawan->nama ?? 'karyawan') . " sudah melewati deadline.",
                    'type' => 'deadline_warning',
                    'task_id' => $task->id,
                ]);
            }
            
            $task->update(['is_overdue_notified' => true]);
            $this->info("Notifikasi deadline lewat dikirim untuk tugas: {$task->judul}");
        }

        $this->info('Selesai! ' . $upcomingTasks->count() . ' notifikasi H-1, ' . $overdueTasks->count() . ' notifikasi terlambat.');
    }
}