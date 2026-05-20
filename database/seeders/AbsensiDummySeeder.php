<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbsensiDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan semua user karyawan memiliki divisi_id
        $divisiList = ['Umum', 'IT', 'Marketing', 'Finance', 'HR', 'Operations'];
        $divisiIds = \App\Models\Divisi::whereIn('divisi', $divisiList)->pluck('id', 'divisi')->toArray();

        // Ambil beberapa user dengan role karyawan dan assign divisi jika belum ada
        $users = User::where('role', 'karyawan')->limit(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user dengan role karyawan. Silakan buat user terlebih dahulu.');
            return;
        }

        // Assign divisi_id untuk users yang belum punya
        foreach ($users as $index => $user) {
            if (!$user->divisi_id) {
                $divisiName = $divisiList[$index % count($divisiList)];
                $divisiId = $divisiIds[$divisiName] ?? null;
                
                if ($divisiId) {
                    User::where('id', $user->id)->update(['divisi_id' => $divisiId]);
                }
            }
        }

        // Re-fetch users dengan divisi_id yang sudah ter-assign
        $users = User::where('role', 'karyawan')->limit(10)->with('divisi')->get();

        $today = Carbon::now();
        
        foreach ($users as $user) {
            // Buat data absensi untuk 30 hari terakhir
            for ($i = 0; $i < 30; $i++) {
                $date = $today->copy()->subDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                $rand = rand(1, 100);

                if ($rand <= 70) {
                    // 70% hadir tepat waktu
                    Absensi::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'jam_masuk' => $date->copy()->setTime(8, rand(0, 59))->format('H:i:s'),
                            'jam_pulang' => $date->copy()->setTime(17, rand(0, 59))->format('H:i:s'),
                            'jenis_ketidakhadiran' => null,
                            'approval_status' => 'approved',
                            'approved_by' => 1,
                            'approved_at' => now(),
                        ]
                    );
                } elseif ($rand <= 85) {
                    // 15% terlambat
                    Absensi::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'jam_masuk' => $date->copy()->setTime(9, rand(10, 59))->format('H:i:s'),
                            'jam_pulang' => $date->copy()->setTime(17, rand(0, 59))->format('H:i:s'),
                            'jenis_ketidakhadiran' => null,
                            'approval_status' => 'approved',
                            'approved_by' => 1,
                            'approved_at' => now(),
                        ]
                    );
                } elseif ($rand <= 92) {
                    // 7% izin
                    Absensi::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'jam_masuk' => null,
                            'jam_pulang' => null,
                            'jenis_ketidakhadiran' => 'izin',
                            'keterangan' => 'Izin pribadi',
                            'approval_status' => 'approved',
                            'approved_by' => 1,
                            'approved_at' => now(),
                        ]
                    );
                } elseif ($rand <= 97) {
                    // 5% sakit
                    Absensi::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'jam_masuk' => null,
                            'jam_pulang' => null,
                            'jenis_ketidakhadiran' => 'sakit',
                            'keterangan' => 'Sakit',
                            'approval_status' => 'approved',
                            'approved_by' => 1,
                            'approved_at' => now(),
                        ]
                    );
                } else {
                    // 3% tidak masuk
                    Absensi::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'jam_masuk' => null,
                            'jam_pulang' => null,
                            'jenis_ketidakhadiran' => null,
                            'approval_status' => 'approved',
                            'approved_by' => 1,
                            'approved_at' => now(),
                        ]
                    );
                }
            }
        }

        $this->command->info('✓ Dummy absensi data berhasil dibuat!');
    }
}
