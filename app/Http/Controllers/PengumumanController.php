<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\User;
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
        
        // Ambil semua pengumuman dengan relasi users dan creator
        $pengumuman = Pengumuman::with(['creator', 'users'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Jika admin, pakai view admin
        if ($user->role == 'admin' || $user->role == 'hr') {
            return view('admin.pengumuman', compact('pengumuman'));
        }
        
        // Jika HR atau lainnya, pakai view HR
        return view('hr.pengumuman.index', compact('pengumuman'));
    }

    /**
     * API: Get all announcements (JSON)
     */
    public function getData()
    {
        $pengumuman = Pengumuman::with(['creator', 'users'])
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
        $pengumuman = Pengumuman::with(['creator', 'users'])->findOrFail($id);
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
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'isi_pesan' => 'required|string',
                'target' => 'required|in:semua,hr,manager_divisi,general_manager,karyawan,finance,owner',
                'lampiran' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

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

            // Sync users jika ada
            if ($request->has('users') && is_array($request->users)) {
                $pengumuman->users()->sync($request->users);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dibuat',
                'data' => $pengumuman->load(['creator', 'users'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating pengumuman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update announcement
     */
    public function update(Request $request, $id)
    {
        try {
            $pengumuman = Pengumuman::findOrFail($id);

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'isi_pesan' => 'required|string',
                'target' => 'required|in:semua,hr,manager_divisi,general_manager,karyawan,finance,owner',
                'lampiran' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

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

            // Sync users jika ada
            if ($request->has('users') && is_array($request->users)) {
                $pengumuman->users()->sync($request->users);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil diupdate',
                'data' => $pengumuman->load(['creator', 'users'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating pengumuman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete announcement
     */
    public function destroy($id)
    {
        try {
            $pengumuman = Pengumuman::findOrFail($id);
            
            if ($pengumuman->lampiran && Storage::disk('public')->exists($pengumuman->lampiran)) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }
            
            // Hapus relasi many-to-many
            $pengumuman->users()->detach();
            
            $pengumuman->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting pengumuman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get announcements for general view
     */
    public function getAnnouncementsApi()
    {
        $user = Auth::user();
        
        $pengumuman = Pengumuman::with(['creator', 'users'])
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

    /**
     * API: Get users data for dropdown
     */
    public function getUsersData()
    {
        try {
            $users = User::select('id', 'name', 'email', 'role')
                ->where('role', '!=', 'admin')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting users data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
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
            
            $query = Pengumuman::with(['creator', 'users'])
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
                    'users' => $item->users,
                    'target' => $item->target
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
            
            $query = Pengumuman::with(['creator', 'users'])
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
                    'users' => $item->users,
                    'target' => $item->target
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
            
            $query = Pengumuman::with(['creator', 'users'])->active();
            
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
                    'users' => $item->users,
                    'target' => $item->target
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

    // ==================== EMPLOYEE METHODS (BARU DITAMBAHKAN) ====================
    
    /**
     * API: Get announcements for regular employee
     */
    public function getAnnouncementsForEmployee(Request $request)
    {
        try {
            $user = Auth::user();
            $date = $request->get('date');
            
            $query = Pengumuman::with(['creator', 'users'])
                ->active()
                ->where(function($q) use ($user) {
                    $q->where('target', 'semua')
                      ->orWhere('target', $user->role)
                      ->orWhere('target', 'karyawan');
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
                    'tanggal' => $item->created_at->format('Y-m-d'),
                    'created_at' => $item->created_at,
                    'creator' => $item->creator->name ?? 'System',
                    'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                    'users' => $item->users,
                    'target' => $item->target
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error get announcements for Employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get announcement dates for regular employee
     */
    public function getAnnouncementDatesForEmployee()
    {
        try {
            $user = Auth::user();
            
            $dates = Pengumuman::active()
                ->where(function($q) use ($user) {
                    $q->where('target', 'semua')
                      ->orWhere('target', $user->role)
                      ->orWhere('target', 'karyawan');
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
            \Log::error('Error get announcement dates for Employee: ' . $e->getMessage());
            return response()->json([], 200);
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