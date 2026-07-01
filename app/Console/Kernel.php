<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        \App\Console\Commands\SendTaskReminders::class,
        \App\Console\Commands\UpdateExpiredContractStatuses::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule -> commad('notifications:send-deadline')->hourly();   // Mark employees as absent at end of working day (23:55 / 11:55 PM)
        $schedule->command('attendance:mark-absent')
            ->dailyAt('23:55')
            ->timezone('Asia/Jakarta');
        
        // Kirim notifikasi pengingat deadline tugas (setiap 6 jam)
        $schedule->command('tasks:send-reminders')
            ->everySixHours()
            ->timezone('Asia/Jakarta');

        $schedule->command('project:check-deadlines')->dailyAt('08:00');

        $schedule->command('contracts:update-status')
            ->dailyAt('00:05')
            ->timezone('Asia/Jakarta');

    }


    
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
}