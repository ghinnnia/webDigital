<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Cuti;
use Carbon\Carbon;

class CheckCutiStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Skip jika tidak login
        if (!auth()->check()) {
            return $next($request);
        }
        
        $user = auth()->user();
        
        // Cek role dengan cara yang benar (tanpa hasRole())
        if ($user->role !== 'karyawan') {
            return $next($request);
        }
        
        $today = Carbon::today();

        // Cek apakah user sedang cuti hari ini
        $sedangCuti = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();

        if ($sedangCuti) {
            // Simpan status di session
            session()->flash('cuti_status', 'sedang_cuti');
            
            // Block akses ke halaman absensi
            if ($request->routeIs('absensi.*') || 
                str_contains($request->path(), 'absensi') ||
                $request->routeIs('presensi.*') || 
                str_contains($request->path(), 'presensi')) {
                return redirect()->route('karyawan.home')
                    ->with('error', 'Anda sedang cuti. Tidak dapat melakukan absensi.');
            }
        }

        return $next($request);
    }
}