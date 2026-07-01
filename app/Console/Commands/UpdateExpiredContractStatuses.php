<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Notification;
use Carbon\Carbon;

class UpdateExpiredContractStatuses extends Command
{
    protected $signature = 'contracts:update-status';

    protected $description = 'Update status kerja to tidak_aktif for expired contract employees';

    public function handle()
    {
        $today = Carbon::today();
        $updatedCount = 0;

        $users = User::where('status_karyawan', 'kontrak')
            ->whereNotNull('kontrak_selesai')
            ->where('status_kerja', 'aktif')
            ->get();

        foreach ($users as $user) {
            $contractEnd = Carbon::parse($user->kontrak_selesai);
            if ($contractEnd->lt($today)) {
                $user->forceFill(['status_kerja' => 'tidak_aktif'])->saveQuietly();

                $karyawan = $user->karyawan()->first();
                if ($karyawan && $karyawan->status_kerja !== 'tidak_aktif') {
                    $karyawan->forceFill(['status_kerja' => 'tidak_aktif'])->saveQuietly();
                }

                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Status kontrak berakhir',
                    'message' => 'Status kerja Anda otomatis diubah menjadi tidak aktif karena kontrak sudah melewati batas akhir.',
                    'type' => 'info',
                    'link' => '/karyawan/profile',
                    'is_read' => false,
                ]);

                $updatedCount++;
            }
        }

        $this->info("Updated {$updatedCount} expired contract statuses.");

        return Command::SUCCESS;
    }
}
