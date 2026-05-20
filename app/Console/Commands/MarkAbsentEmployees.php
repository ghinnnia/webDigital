<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Cuti;
use Carbon\Carbon;

class MarkAbsentEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Automatically mark employees as absent if they did not check in all day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        
        // Get all active karyawan users
        $employees = User::where('role', 'karyawan')
            ->where('status_kerja', 'aktif')
            ->get();

        $markedAbsent = 0;

        foreach ($employees as $employee) {
            // Check if employee already has an attendance record for today
            $existingRecord = Absensi::where('user_id', $employee->id)
                ->where('tanggal', $today)
                ->first();

            if (!$existingRecord) {
                // Check if employee is on leave today
                $onLeaveToday = Cuti::where('user_id', $employee->id)
                    ->whereDate('tanggal_mulai', '<=', $today)
                    ->whereDate('tanggal_selesai', '>=', $today)
                    ->where('status', 'approved')
                    ->exists();

                // If not on leave, create absence record
                if (!$onLeaveToday) {
                    Absensi::create([
                        'user_id' => $employee->id,
                        'tanggal' => $today,
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'jenis_ketidakhadiran' => 'lainnya',
                        'keterangan' => 'Tidak Masuk',
                        'approval_status' => 'approved',
                        'is_early_checkout' => false,
                    ]);
                    $markedAbsent++;
                }
            }
        }

        $this->info("Marked {$markedAbsent} employees as absent for {$today}");
        return Command::SUCCESS;
    }
}
