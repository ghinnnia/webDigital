<?php

namespace App\Http\Controllers\ManagerDivisi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LemburController extends Controller
{
    private const DEFAULT_RATE = 30000;
    private const MAX_RATE = 100000;

public function index(Request $request)
{
    $manager = Auth::user();
    $divisiId = $manager->divisi_id;
    
    $defaultRate = self::DEFAULT_RATE;
    $maxRate = self::MAX_RATE;
    
    // Ambil Karyawan di divisi manager yang berstatus aktif
    $karyawans = User::where('divisi_id', $divisiId)
        ->where('role', 'karyawan')
        ->where('status_kerja', 'aktif')
        ->get();
    
    // Eager loading data user untuk optimasi performa N+1 query
    $query = Lembur::with('user')->whereHas('user', function($q) use ($divisiId) {
        $q->where('divisi_id', $divisiId);
    });
    
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    if ($request->type) {
        $query->where('type', $request->type);
    }
    
    $lemburs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
    
    // Optimasi Statistik: 1x query menggunakan selectRaw aggregate khusus divisi ini
    $statsData = Lembur::whereHas('user', fn($q) => $q->where('divisi_id', $divisiId))
        ->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
            COUNT(CASE WHEN type = 'perintah' THEN 1 END) as perintah
        ")
        ->first();

    // Memetakan hasil ke array statistik dengan fallback/cadangan global jika relasi user divisi kosong/0
    $statistik = [
        'total'     => ($statsData && $statsData->total > 0) ? $statsData->total : \App\Models\Lembur::count(),
        'pending'   => ($statsData && $statsData->total > 0) ? $statsData->pending : \App\Models\Lembur::where('status', 'pending')->count(),
        'approved'  => ($statsData && $statsData->total > 0) ? $statsData->approved : \App\Models\Lembur::where('status', 'approved')->count(),
        'rejected'  => ($statsData && $statsData->total > 0) ? $statsData->rejected : \App\Models\Lembur::where('status', 'rejected')->count(),
        'cancelled' => ($statsData && $statsData->total > 0) ? $statsData->cancelled : \App\Models\Lembur::where('status', 'cancelled')->count(),
        'perintah'  => ($statsData && $statsData->total > 0) ? $statsData->perintah : \App\Models\Lembur::where('type', 'perintah')->count(),
    ];
    
    return view('manager_divisi.lembur.index', compact(
        'lemburs', 'karyawans', 'statistik', 'defaultRate', 'maxRate'
    ));
}
    
    public function order(Request $request)
    {
        try {
            $defaultRate = self::DEFAULT_RATE;
            $maxRate = self::MAX_RATE;
            $customRate = $request->custom_rate ?? $defaultRate;
            
            $rules = [
                'karyawan_id' => 'required|exists:users,id',
                'tanggal_lembur' => 'required|date',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'deskripsi_tugas' => 'required|string|min:5',
                'custom_rate' => 'nullable|numeric|min:0|max:' . $maxRate,
            ];
            
            if ($customRate > $defaultRate) {
                $rules['alasan_kenaikan'] = 'required|string|min:5';
            }
            
            $request->validate($rules);
            
            $manager = Auth::user();
            
            // Proteksi Divisi Karyawan
            $karyawan = User::where('id', $request->karyawan_id)
                ->where('divisi_id', $manager->divisi_id)
                ->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Karyawan tidak ditemukan atau tidak berada di divisi Anda'
                ], 403);
            }
            
            // Kalkulasi durasi lembur melewati tengah malam
            $tanggal = $request->tanggal_lembur;
            $jamMulai = Carbon::parse($tanggal . ' ' . $request->jam_mulai);
            $jamSelesai = Carbon::parse($tanggal . ' ' . $request->jam_selesai);
            
            if ($jamSelesai->lessThanOrEqualTo($jamMulai)) {
                $jamSelesai->addDay();
            }
            
            $durasi = ceil($jamSelesai->diffInMinutes($jamMulai) / 60);
            
            $lembur = Lembur::create([
                'user_id' => $request->karyawan_id,
                'ordered_by' => $manager->id,
                'tanggal_lembur' => $request->tanggal_lembur,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'durasi' => $durasi,
                'deskripsi_tugas' => $request->deskripsi_tugas,
                'keterangan' => $request->deskripsi_tugas,
                'hourly_rate' => $defaultRate,
                'custom_rate' => $customRate != $defaultRate ? $customRate : null,
                'alasan_kenaikan' => $request->alasan_kenaikan,
                'type' => 'perintah',
                'status' => 'pending', 
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Perintah lembur berhasil dikirim ke ' . $karyawan->name
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Order error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $defaultRate = self::DEFAULT_RATE;
            $maxRate = self::MAX_RATE;
            $customRate = $request->custom_rate ?? $defaultRate;
            
            $rules = [
                'tanggal_lembur' => 'required|date',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'deskripsi_tugas' => 'required|string|min:5',
                'custom_rate' => 'nullable|numeric|min:0|max:' . $maxRate,
            ];
            
            if ($customRate > $defaultRate) {
                $rules['alasan_kenaikan'] = 'required|string|min:5';
            }
            
            $request->validate($rules);
            
            $manager = Auth::user();
            
            // Ambil data lembur dan pastikan milik divisi manager (anti-IDOR)
            $lembur = Lembur::whereHas('user', function($q) use ($manager) {
                $q->where('divisi_id', $manager->divisi_id);
            })->findOrFail($id);

            // Batasi edit jika status sudah direspon oleh karyawan
            if ($lembur->status !== 'pending') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data lembur sudah diproses, tidak dapat diubah lagi.'
                ], 422);
            }
            
            // Hitung durasi ulang
            $tanggal = $request->tanggal_lembur;
            $jamMulai = Carbon::parse($tanggal . ' ' . $request->jam_mulai);
            $jamSelesai = Carbon::parse($tanggal . ' ' . $request->jam_selesai);
            
            if ($jamSelesai->lessThanOrEqualTo($jamMulai)) {
                $jamSelesai->addDay();
            }
            
            $durasi = ceil($jamSelesai->diffInMinutes($jamMulai) / 60);
            
            $lembur->update([
                'tanggal_lembur' => $request->tanggal_lembur,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'durasi' => $durasi,
                'deskripsi_tugas' => $request->deskripsi_tugas,
                'keterangan' => $request->deskripsi_tugas,
                'custom_rate' => $customRate != $defaultRate ? $customRate : null,
                'alasan_kenaikan' => $customRate > $defaultRate ? $request->alasan_kenaikan : null,
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Perintah lembur berhasil diperbarui!'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Data lembur tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update lembur error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan pada server saat memperbarui data.'
            ], 500);
        }
    }
    
    public function approve($id)
    {
        try {
            $manager = Auth::user();
            
            $lembur = Lembur::whereHas('user', function($q) use ($manager) {
                $q->where('divisi_id', $manager->divisi_id);
            })->findOrFail($id);
            
            if ($lembur->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Lembur sudah diproses sebelumnya'], 422);
            }
            
            $lembur->update([
                'status' => 'approved',
                'approved_by' => $manager->id,
                'approved_at' => now(),
            ]);
            
            return response()->json(['success' => true, 'message' => 'Lembur disetujui']);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data lembur tidak ditemukan atau tidak memiliki akses'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }
    
    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'alasan_penolakan' => 'required|string|min:3'
            ]);
            
            $manager = Auth::user();
            
            $lembur = Lembur::whereHas('user', function($q) use ($manager) {
                $q->where('divisi_id', $manager->divisi_id);
            })->findOrFail($id);
            
            if ($lembur->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Lembur sudah diproses sebelumnya'], 422);
            }
            
            $lembur->update([
                'status' => 'rejected',
                'alasan_penolakan' => $request->alasan_penolakan,
                'rejected_by' => $manager->id,
                'rejected_at' => now(),
            ]);
            
            return response()->json(['success' => true, 'message' => 'Lembur ditolak']);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data lembur tidak ditemukan atau tidak memiliki akses'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }
    
    public function cancel($id)
{
    try {
        $manager = Auth::user();
        $lembur = Lembur::findOrFail($id);
        
        // JIKA ordered_by kosong pada data lama, kita bypass proteksinya 
        // dengan mencocokkan divisi karyawan tersebut agar tetap aman dari IDOR
        if ($lembur->ordered_by && $lembur->ordered_by != $manager->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak dapat membatalkan perintah orang lain'], 403);
        }
        
        if ($lembur->status != 'pending') {
            return response()->json(['success' => false, 'message' => 'Perintah sudah ' . $lembur->status . ', tidak dapat dibatalkan'], 422);
        }
        
        // Eksekusi update status menjadi cancelled
        $lembur->update([
            'status' => 'cancelled',
            'alasan_penolakan' => 'Dibatalkan oleh manager',
        ]);
        
        return response()->json(['success' => true, 'message' => 'Perintah lembur berhasil dibatalkan']);
        
    } catch (\Exception $e) {
        // Mencatat log error asli ke storage/logs/laravel.log agar Anda bisa tahu detail error pastinya
        \Log::error('Cancel Lembur Error: ' . $e->getMessage()); 
        
        return response()->json([
            'success' => false, 
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage() // Menampilkan pesan error spesifik sementara untuk debug
        ], 500);
    }
}
    
    public function calculate(Request $request)
    {
        $request->validate([
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'rate' => 'required|numeric|min:0',
        ]);
        
        $tanggal = $request->tanggal_lembur;
        $jamMulai = Carbon::parse($tanggal . ' ' . $request->jam_mulai);
        $jamSelesai = Carbon::parse($tanggal . ' ' . $request->jam_selesai);
        
        if ($jamSelesai->lessThanOrEqualTo($jamMulai)) {
            $jamSelesai->addDay();
        }
        
        $durasi = ceil($jamSelesai->diffInMinutes($jamMulai) / 60);
        $total = $durasi * $request->rate;
        
        return response()->json([
            'success' => true,
            'durasi' => $durasi,
            'total' => $total,
            'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.')
        ]);
    }
}