<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    /**
     * Tampilan halaman pengumuman (untuk admin dan HR)
     */
    public function index()
    {
        $user = Auth::user();
        
        // Jika admin, pakai view admin
        if ($user->role == 'admin') {
            return view('admin.pengumuman');
        }
        
        // Jika HR atau lainnya, pakai view HR
        return view('hr.pengumuman.index');
    }

    /**
     * API: Get all announcements (JSON)
     */
    public function getData()
    {
        $pengumuman = Pengumuman::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Get single announcement
     */
    public function show($id)
    {
        $pengumuman = Pengumuman::with('creator')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Create announcement
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_pesan' => 'required|string',
            'target' => 'required|in:semua,hr,manager_divisi,general_manager,karyawan,finance,owner'
        ]);

        $data = [
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'isi_pesan' => $request->isi_pesan,
            'target' => $request->target,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'is_active' => true
        ];

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = 'pengumuman_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $data['lampiran'] = $path;
        }

        $pengumuman = Pengumuman::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat',
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Update announcement
     */
    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_pesan' => 'required|string',
            'target' => 'required|in:semua,hr,manager_divisi,general_manager,karyawan,finance,owner'
        ]);

        $data = [
            'judul' => $request->judul,
            'isi_pesan' => $request->isi_pesan,
            'target' => $request->target,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ];

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran && Storage::disk('public')->exists($pengumuman->lampiran)) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }
            
            $file = $request->file('lampiran');
            $filename = 'pengumuman_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $data['lampiran'] = $path;
        }

        $pengumuman->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diupdate',
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Delete announcement
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        
        if ($pengumuman->lampiran && Storage::disk('public')->exists($pengumuman->lampiran)) {
            Storage::disk('public')->delete($pengumuman->lampiran);
        }
        
        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus'
        ]);
    }

    /**
     * API: Get announcements for general view
     */
    public function getAnnouncementsApi()
    {
        $user = Auth::user();
        
        $pengumuman = Pengumuman::with('creator')
            ->active()
            ->where(function($q) use ($user) {
                $q->where('target', 'semua')
                  ->orWhere('target', $user->role);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Get announcement dates for calendar
     */
    public function getAnnouncementDatesApi()
    {
        $user = Auth::user();
        
        $dates = Pengumuman::active()
            ->where(function($q) use ($user) {
                $q->where('target', 'semua')
                  ->orWhere('target', $user->role);
            })
            ->selectRaw('DISTINCT DATE(created_at) as date')
            ->pluck('date')
            ->map(function($date) {
                return $date instanceof \DateTime ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date));
            })
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'dates' => $dates
        ]);
    }

    // ==================== GENERAL MANAGER METHODS ====================
    
    public function getAnnouncementDatesForGM()
    {
        try {
            $dates = Pengumuman::active()
                ->where(function($q) {
                    $q->where('target', 'semua')
                      ->orWhere('target', 'general_manager');
                })
                ->selectRaw('DISTINCT DATE(created_at) as tanggal')
                ->pluck('tanggal')
                ->map(function($date) {
                    return $date instanceof \DateTime ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date));
                })
                ->values()
                ->toArray();
            
            return response()->json($dates);
        } catch (\Exception $e) {
            \Log::error('Error get announcement dates for GM: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function getAnnouncementsForGM(Request $request)
    {
        try {
            $date = $request->get('date');
            
            $query = Pengumuman::with('creator')
                ->active()
                ->where(function($q) {
                    $q->where('target', 'semua')
                      ->orWhere('target', 'general_manager');
                });
            
            if ($date) {
                $query->whereDate('created_at', $date);
            }
            
            $announcements = $query->orderBy('created_at', 'desc')->get();
            
            $data = $announcements->map(function($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'isi_pesan' => $item->isi_pesan,
                    'ringkasan' => Str::limit(strip_tags($item->isi_pesan), 100),
                    'tanggal_indo' => $item->created_at->translatedFormat('d F Y'),
                    'created_at' => $item->created_at,
                    'creator' => $item->creator->name ?? 'System',
                    'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error get announcements for GM: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    // ==================== MANAGER DIVISI METHODS ====================
    
    public function getAnnouncementDatesForManager()
    {
        try {
            $dates = Pengumuman::active()
                ->where(function($q) {
                    $q->where('target', 'semua')
                      ->orWhere('target', 'manager_divisi');
                })
                ->selectRaw('DISTINCT DATE(created_at) as tanggal')
                ->pluck('tanggal')
                ->map(function($date) {
                    return $date instanceof \DateTime ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date));
                })
                ->values()
                ->toArray();
            
            return response()->json($dates);
        } catch (\Exception $e) {
            \Log::error('Error get announcement dates for Manager: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function getAnnouncementsForManager(Request $request)
    {
        try {
            $date = $request->get('date');
            
            $query = Pengumuman::with('creator')
                ->active()
                ->where(function($q) {
                    $q->where('target', 'semua')
                      ->orWhere('target', 'manager_divisi');
                });
            
            if ($date) {
                $query->whereDate('created_at', $date);
            }
            
            $announcements = $query->orderBy('created_at', 'desc')->get();
            
            $data = $announcements->map(function($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'isi_pesan' => $item->isi_pesan,
                    'ringkasan' => Str::limit(strip_tags($item->isi_pesan), 100),
                    'tanggal_indo' => $item->created_at->translatedFormat('d F Y'),
                    'created_at' => $item->created_at,
                    'creator' => $item->creator->name ?? 'System',
                    'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error get announcements for Manager: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    // ==================== OWNER METHODS ====================
    
    public function getAnnouncementDatesForOwner()
    {
        try {
            $dates = Pengumuman::active()
                ->selectRaw('DISTINCT DATE(created_at) as tanggal')
                ->pluck('tanggal')
                ->map(function($date) {
                    return $date instanceof \DateTime ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date));
                })
                ->values()
                ->toArray();
            
            return response()->json($dates);
        } catch (\Exception $e) {
            \Log::error('Error get announcement dates for Owner: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function getAnnouncementsForOwner(Request $request)
    {
        try {
            $date = $request->get('date');
            
            $query = Pengumuman::with('creator')->active();
            
            if ($date) {
                $query->whereDate('created_at', $date);
            }
            
            $announcements = $query->orderBy('created_at', 'desc')->get();
            
            $data = $announcements->map(function($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'isi_pesan' => $item->isi_pesan,
                    'ringkasan' => Str::limit(strip_tags($item->isi_pesan), 100),
                    'tanggal_indo' => $item->created_at->translatedFormat('d F Y'),
                    'created_at' => $item->created_at,
                    'creator' => $item->creator->name ?? 'System',
                    'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error get announcements for Owner: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    // ==================== MEETING NOTES DATES (dummy) ====================
    
    public function getMeetingNotesDatesForGM()
    {
        return response()->json([]);
    }

    public function getMeetingNotesByDateForGM(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}