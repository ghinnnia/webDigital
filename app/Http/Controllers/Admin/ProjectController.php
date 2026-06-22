<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\ProjectNotification;
use App\Models\User;
use App\Models\Layanan;
use App\Models\Divisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['invoice', 'penanggungJawab', 'layanan', 'divisi']);
        
        // Auto update status kerjasama yang sudah expired
        $this->autoUpdateStatusKerjasama();
        
        // Filter pencarian
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->q . '%');
            });
        }
        
        // Filter status pengerjaan
        if ($request->filled('status_pengerjaan')) {
            $query->where('status_pengerjaan', $request->status_pengerjaan);
        }
        
        // Filter status kerjasama
        if ($request->filled('status_kerjasama')) {
            $query->where('status_kerjasama', $request->status_kerjasama);
        }
        
        $project = $query->orderBy('created_at', 'desc')->paginate(10);
        $invoices = Invoice::all();
        $layanans = Layanan::all();
        $divisis = Divisi::all();
        
        return view('admin.data_project', compact('project', 'invoices', 'layanans', 'divisis'));
    }
    
    /**
     * Auto update status kerjasama yang sudah expired
     */
    private function autoUpdateStatusKerjasama()
    {
        $today = Carbon::now()->startOfDay();
        
        // Cari project dengan status kerjasama 'aktif' dan tanggal selesai sudah lewat
        $expiredProjects = Project::where('status_kerjasama', 'aktif')
            ->whereNotNull('tanggal_selesai_kerjasama')
            ->where('tanggal_selesai_kerjasama', '<', $today)
            ->get();
        
        foreach ($expiredProjects as $project) {
            $project->status_kerjasama = 'selesai';
            $project->save();
            
            // Buat notifikasi
            ProjectNotification::create([
                'project_id' => $project->id,
                'message' => "⚠️ Periode KERJA SAMA dengan '{$project->nama}' telah berakhir pada " . 
                            Carbon::parse($project->tanggal_selesai_kerjasama)->format('d-m-Y'),
                'type' => 'expired_kerjasama',
                'is_read' => false
            ]);
        }
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'layanan_id' => 'nullable|exists:layanans,id',
            'divisi_id' => 'nullable|exists:divisi,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'tanggal_mulai_pengerjaan' => 'nullable|date',
            'tanggal_selesai_pengerjaan' => 'nullable|date|after_or_equal:tanggal_mulai_pengerjaan',
            'tanggal_mulai_kerjasama' => 'nullable|date',
            'tanggal_selesai_kerjasama' => 'nullable|date|after_or_equal:tanggal_mulai_kerjasama',
            'status_pengerjaan' => 'required|in:pending,dalam_pengerjaan,selesai,dibatalkan',
            'status_kerjasama' => 'required|in:aktif,selesai,ditangguhkan',
            'progres' => 'required|integer|min:0|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $project = Project::create([
                'invoice_id' => $request->invoice_id,
                'layanan_id' => $request->layanan_id,
                'divisi_id' => $request->divisi_id,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'harga' => $request->harga,
                'tanggal_mulai_pengerjaan' => $request->tanggal_mulai_pengerjaan,
                'tanggal_selesai_pengerjaan' => $request->tanggal_selesai_pengerjaan,
                'tanggal_mulai_kerjasama' => $request->tanggal_mulai_kerjasama,
                'tanggal_selesai_kerjasama' => $request->tanggal_selesai_kerjasama,
                'status_pengerjaan' => $request->status_pengerjaan,
                'status_kerjasama' => $request->status_kerjasama,
                'progres' => $request->progres,
                'penanggung_jawab_id' => auth()->id(),
                'created_by' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil ditambahkan',
                'data' => $project
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan project: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai_pengerjaan' => 'nullable|date',
            'tanggal_selesai_pengerjaan' => 'nullable|date|after_or_equal:tanggal_mulai_pengerjaan',
            'tanggal_mulai_kerjasama' => 'nullable|date',
            'tanggal_selesai_kerjasama' => 'nullable|date|after_or_equal:tanggal_mulai_kerjasama',
            'status_kerjasama' => 'required|in:aktif,selesai,ditangguhkan',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $project = Project::findOrFail($id);
            
            $project->nama = $request->nama;
            $project->deskripsi = $request->deskripsi;
            $project->tanggal_mulai_pengerjaan = $request->tanggal_mulai_pengerjaan;
            $project->tanggal_selesai_pengerjaan = $request->tanggal_selesai_pengerjaan;
            $project->tanggal_mulai_kerjasama = $request->tanggal_mulai_kerjasama;
            $project->tanggal_selesai_kerjasama = $request->tanggal_selesai_kerjasama;
            
            // Cek jika tanggal selesai kerjasama sudah lewat
            if ($request->tanggal_selesai_kerjasama) {
                $tglSelesai = Carbon::parse($request->tanggal_selesai_kerjasama);
                if ($tglSelesai->isPast() && $request->status_kerjasama == 'aktif') {
                    $project->status_kerjasama = 'selesai';
                } else {
                    $project->status_kerjasama = $request->status_kerjasama;
                }
            } else {
                $project->status_kerjasama = $request->status_kerjasama;
            }
            
            $project->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil diupdate',
                'data' => $project
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate project: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus project: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $project = Project::with(['invoice', 'penanggungJawab', 'layanan', 'divisi'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $project
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project tidak ditemukan'
            ], 404);
        }
    }
    
    public function notifications()
    {
        $notifications = ProjectNotification::with('project')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
    
    public function markAsRead($id)
    {
        try {
            $notification = ProjectNotification::findOrFail($id);
            $notification->is_read = true;
            $notification->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai dibaca'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi'
            ], 500);
        }
    }
    
    public function markAllAsRead()
    {
        try {
            ProjectNotification::where('is_read', false)->update(['is_read' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sebagai dibaca'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi'
            ], 500);
        }
    }
}