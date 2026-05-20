<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PreventAbsensiDuringCuti
{
    public function handle(Request $request, Closure $next)
    {
        // Skip jika tidak login
        if (!Auth::check()) {
            return $next($request);
        }
        
        $user = Auth::user();
        
        // Hanya berlaku untuk karyawan
        if ($user->role !== 'karyawan') {
            return $next($request);
        }
        
        $today = Carbon::today()->format('Y-m-d');
        
        // Cek apakah user sedang cuti hari ini
        $sedangCuti = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();
        
        // Jika sedang cuti dan mencoba akses absensi API
        if ($sedangCuti) {
            // Cek jika request ke API absensi
            if ($request->is('karyawan/api/*') && 
                ($request->routeIs('karyawan.api.absen.*') || 
                 $request->routeIs('karyawan.api.submit.*'))) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sedang cuti dari ' . 
                        Carbon::parse($sedangCuti->tanggal_mulai)->format('d/m/Y') . 
                        ' sampai ' . 
                        Carbon::parse($sedangCuti->tanggal_selesai)->format('d/m/Y') . 
                        '. Tidak dapat melakukan absensi.',
                    'cuti_details' => [
                        'tanggal_mulai' => $sedangCuti->tanggal_mulai,
                        'tanggal_selesai' => $sedangCuti->tanggal_selesai,
                        'tipe_cuti' => $sedangCuti->tipe_cuti,
                        'alasan' => $sedangCuti->alasan
                    ]
                ], 403);
            }
            
            // Jika akses halaman absensi via browser
            if ($request->routeIs('karyawan.absensi.*')) {
                return redirect()->route('karyawan.home')
                    ->with('warning', 
                        'Anda sedang cuti dari ' . 
                        Carbon::parse($sedangCuti->tanggal_mulai)->format('d/m/Y') . 
                        ' sampai ' . 
                        Carbon::parse($sedangCuti->tanggal_selesai)->format('d/m/Y') . 
                        '. Tidak dapat mengakses halaman absensi.');
            }
        }
        
        return $next($request);
    }
}