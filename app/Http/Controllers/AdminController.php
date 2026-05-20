<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Layanan;
use App\Models\CatatanRapat;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Project;
use App\Models\Task;

class AdminController extends Controller
{
public function beranda()
    {
        try {
            // Data statistik
            $jumlahKaryawan = Karyawan::count();
            $jumlahPerusahaan = Perusahaan::count(); // Tambahkan ini
            $jumlahLayanan = Layanan::count();
            $jumlahProject = Project::count();
            
            // Data catatan rapat dengan relasi
            $catatanRapat = CatatanRapat::with(['peserta', 'penugasan'])
                ->orderBy('tanggal', 'desc')
                ->take(10)
                ->get();
            
            // Data pengumuman terbaru
            $pengumumanTerbaru = Pengumuman::with('users')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            // Data untuk calendar events
            $events = [];
            $catatanRapat->each(function ($rapat) use (&$events) {
                $date = \Carbon\Carbon::parse($rapat->tanggal)->format('Y-m-d');
                if (!isset($events[$date])) {
                    $events[$date] = [];
                }
                $events[$date][] = [
                    'topik' => $rapat->topik,
                    'keputusan' => $rapat->keputusan,
                ];
            });
            
            return view('admin.home', compact(
                'jumlahKaryawan',
                'jumlahPerusahaan', // Tambahkan ini
                'jumlahLayanan',
                'jumlahProject',
                'catatanRapat',
                'pengumumanTerbaru',
                'events'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard.');
        }
    }
}