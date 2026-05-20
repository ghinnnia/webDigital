<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckProjectDeadlines extends Command
{
    protected $signature = 'project:check-deadlines';
    protected $description = 'Cek deadline proyek dan buat notifikasi';

    public function handle()
    {
        $projects = Project::all();
        $notificationsCreated = 0;

        foreach ($projects as $project) {
            // ========== PERIODE PENGERJAAN ==========
            if ($project->tanggal_mulai_pengerjaan) {
                $start = Carbon::parse($project->tanggal_mulai_pengerjaan);
                if ($start->copy()->subDays(7)->isToday()) {
                    $this->createNotif($project, 'pengerjaan', "⚠️ Project '{$project->nama}' akan dimulai dalam 7 hari ({$start->format('d-m-Y')})");
                    $notificationsCreated++;
                }
                if ($start->copy()->subDays(3)->isToday()) {
                    $this->createNotif($project, 'pengerjaan', "🔔 Project '{$project->nama}' akan dimulai dalam 3 hari!");
                    $notificationsCreated++;
                }
                if ($start->isToday()) {
                    $this->createNotif($project, 'pengerjaan', "🚀 Project '{$project->nama}' DIMULAI hari ini!");
                    $notificationsCreated++;
                }
            }

            if ($project->tanggal_selesai_pengerjaan) {
                $end = Carbon::parse($project->tanggal_selesai_pengerjaan);
                if ($end->copy()->subDays(7)->isToday()) {
                    $this->createNotif($project, 'pengerjaan', "⚠️ Deadline pengerjaan '{$project->nama}' tersisa 7 hari (Selesai: {$end->format('d-m-Y')})");
                    $notificationsCreated++;
                }
                if ($end->copy()->subDays(3)->isToday()) {
                    $this->createNotif($project, 'pengerjaan', "🔔 Project '{$project->nama}' tersisa 3 hari menuju deadline!");
                    $notificationsCreated++;
                }
                if ($end->isToday()) {
                    $this->createNotif($project, 'pengerjaan', "⏰ HARI INI deadline pengerjaan Project '{$project->nama}'!");
                    $notificationsCreated++;
                }
                if ($end->isPast() && $project->status_pengerjaan !== 'selesai') {
                    $this->createNotif($project, 'pengerjaan', "❌ Project '{$project->nama}' MELEWATI deadline! (Terlambat {$end->diffInDays(now())} hari)");
                    $notificationsCreated++;
                }
            }

            // ========== PERIODE KERJASAMA ==========
            if ($project->tanggal_selesai_kerjasama) {
                $end = Carbon::parse($project->tanggal_selesai_kerjasama);
                if ($end->copy()->subDays(30)->isToday()) {
                    $this->createNotif($project, 'kerjasama', "⚠️ Kerjasama '{$project->nama}' akan berakhir dalam 30 hari ({$end->format('d-m-Y')})");
                    $notificationsCreated++;
                }
                if ($end->copy()->subDays(7)->isToday()) {
                    $this->createNotif($project, 'kerjasama', "🔔 Kerjasama '{$project->nama}' tersisa 7 hari lagi!");
                    $notificationsCreated++;
                }
                if ($end->isToday()) {
                    $this->createNotif($project, 'kerjasama', "⏰ HARI INI hari terakhir kerjasama '{$project->nama}'!");
                    $notificationsCreated++;
                }
                if ($end->isPast() && $project->status_kerjasama !== 'selesai') {
                    $this->createNotif($project, 'kerjasama', "❌ Masa kerjasama '{$project->nama}' telah BERAKHIR! (Terlambat {$end->diffInDays(now())} hari)");
                    $notificationsCreated++;
                }
            }
        }

        $this->info("Selesai! {$notificationsCreated} notifikasi baru.");
    }

    private function createNotif($project, $type, $message)
    {
        $exists = ProjectNotification::where('project_id', $project->id)
            ->where('type', $type)
            ->whereDate('created_at', today())
            ->exists();

        if (!$exists) {
            ProjectNotification::create([
                'project_id' => $project->id,
                'type' => $type,
                'message' => $message,
                'trigger_date' => today(),
                'is_read' => false
            ]);
        }
    }
}