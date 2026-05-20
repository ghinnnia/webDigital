<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PayrollPeriod;
use App\Models\PayrollLog;
use App\Services\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcessPayroll extends Command
{
    protected $signature = 'payroll:process {bulan} {tahun} {--user=}';
    protected $description = 'Proses gaji untuk bulan dan tahun tertentu';

    public function handle()
    {
        $bulan = (int) $this->argument('bulan');
        $tahun = (int) $this->argument('tahun');
        $userId = $this->option('user');
        
        // Validasi bulan
        if ($bulan < 1 || $bulan > 12) {
            $this->error('Bulan harus antara 1-12');
            return Command::FAILURE;
        }
        
        // Generate tanggal mulai dan selesai
        $tanggalMulai = Carbon::create($tahun, $bulan, 1);
        $tanggalSelesai = $tanggalMulai->copy()->endOfMonth();
        
        // Cek apakah sudah ada periode untuk bulan/tahun ini
        $existingPeriod = PayrollPeriod::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
        
        if ($existingPeriod) {
            $this->warn("Periode {$existingPeriod->nama_periode} sudah ada!");
            if (!$this->confirm('Ingin memproses ulang?', false)) {
                return Command::FAILURE;
            }
            $period = $existingPeriod;
        } else {
            // Buat periode baru
            $period = PayrollPeriod::create([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => 'draft'
            ]);
            $this->info("Periode baru dibuat: {$period->nama_periode}");
        }
        
        $this->info("Memproses gaji periode: {$period->nama_periode}");
        
        // Proses gaji
        $calculator = new PayrollCalculator($period);
        
        if ($userId) {
            // Proses satu karyawan
            $user = \App\Models\User::find($userId);
            if (!$user) {
                $this->error("User ID {$userId} tidak ditemukan");
                return Command::FAILURE;
            }
            $result = $calculator->prosesGajiKaryawan($user);
            $this->info("Berhasil memproses gaji untuk: {$user->name}");
        } else {
            // Proses semua karyawan
            $results = $calculator->prosesGajiSemuaKaryawan();
            $this->info("Berhasil memproses " . count($results) . " karyawan");
        }
        
        // Update status periode
        $period->update(['status' => 'processed']);
        
        // Catat log
        PayrollLog::create([
            'payroll_period_id' => $period->id,
            'user_id' => $userId ?? 1,
            'aksi' => 'processed',
            'keterangan' => 'Proses gaji via command line'
        ]);
        
        $this->info("✓ Selesai! Status periode: processed");
        
        return Command::SUCCESS;
    }
}