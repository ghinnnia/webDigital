<?php

namespace App\Http\Controllers;

use App\Models\Tim;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimDivisiController extends Controller
{
    /**
     * Display the management dashboard.
     */
    public function index()
    {
        // Hitung statistik dari database
        $totalTim = Tim::count();
        $totalDivisi = Divisi::count();
        
        // Untuk tim aktif, asumsikan semua tim aktif
        $timAktif = $totalTim;
        
        // Hitung total anggota
        $totalAnggota = Tim::sum('jumlah_anggota');
        
        // Ambil data dengan pagination
        $tims = Tim::orderBy('created_at', 'desc')->paginate(10);
        $divisis = Divisi::orderBy('created_at', 'desc')->paginate(10);
        
        return view('general_manajer.tim_dan_divisi', compact(
            'totalTim', 'totalDivisi', 'timAktif', 'totalAnggota', 'tims', 'divisis'
        ));
    }

    /**
     * Store a newly created tim.
     */
    public function storeTim(Request $request)
    {
        try {
            \Log::info('Store Tim Request:', $request->all());
            
            $validated = $request->validate([
                'tim' => 'required|string|max:255',
                'divisi' => 'required|string|max:255',
                'jumlah_anggota' => 'nullable|integer|min:0'
            ]);

            // Cek apakah divisi ada
            $divisiExists = Divisi::where('divisi', $validated['divisi'])->exists();

            if (!$divisiExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Divisi tidak ditemukan. Silakan pilih divisi yang tersedia.'
                ], 422);
            }

            // Dapatkan divisi_id
            $divisi = Divisi::where('divisi', $validated['divisi'])->first();
            
            // Buat tim
            $tim = Tim::create([
                'tim' => $validated['tim'],
                'divisi' => $validated['divisi'],
                'divisi_id' => $divisi->id,
                'jumlah_anggota' => $validated['jumlah_anggota'] ?? 0,
            ]);

            \Log::info('Tim created successfully:', ['id' => $tim->id]);

            return response()->json([
                'success' => true,
                'message' => 'Tim berhasil ditambahkan',
                'data' => $tim
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store tim error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified tim.
     */
    public function updateTim(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tim' => 'required|string|max:255',
                'divisi' => 'required|string|max:255',
                'jumlah_anggota' => 'nullable|integer|min:0'
            ]);

            $tim = Tim::findOrFail($id);
            
            // Cek apakah divisi baru ada
            $newDivisi = Divisi::where('divisi', $validated['divisi'])->first();
            if (!$newDivisi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Divisi tidak ditemukan'
                ], 422);
            }
            
            $tim->tim = $validated['tim'];
            $tim->divisi = $validated['divisi'];
            $tim->divisi_id = $newDivisi->id;
            if (isset($validated['jumlah_anggota'])) {
                $tim->jumlah_anggota = $validated['jumlah_anggota'];
            }
            $tim->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Tim berhasil diperbarui',
                'data' => $tim
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified tim.
     */
    public function destroyTim($id)
    {
        try {
            $tim = Tim::findOrFail($id);
            $tim->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tim berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created divisi.
     */
    public function storeDivisi(Request $request)
    {
        try {
            $validated = $request->validate([
                'divisi' => 'required|string|max:255|unique:divisi,divisi'
            ]);

            $divisi = Divisi::create([
                'divisi' => $validated['divisi'],
                'jumlah_tim' => 0
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Divisi berhasil ditambahkan',
                'data' => $divisi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified divisi.
     */
    public function updateDivisi(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'divisi' => 'required|string|max:255|unique:divisi,divisi,' . $id
            ]);

            $divisi = Divisi::findOrFail($id);
            $oldNamaDivisi = $divisi->divisi;
            
            $divisi->update(['divisi' => $validated['divisi']]);
            
            // Update nama divisi di semua tim yang terkait
            if ($oldNamaDivisi != $validated['divisi']) {
                Tim::where('divisi', $oldNamaDivisi)->update(['divisi' => $validated['divisi']]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Divisi berhasil diperbarui',
                'data' => $divisi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified divisi.
     */
    public function destroyDivisi($id)
    {
        try {
            $divisi = Divisi::findOrFail($id);
            
            // Check if divisi has tims
            if (Tim::where('divisi', $divisi->divisi)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus divisi yang memiliki tim'
                ], 400);
            }
            
            $divisi->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Divisi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search tim.
     */
    public function searchTim(Request $request)
    {
        $search = $request->get('search');
        
        $tims = Tim::where('tim', 'like', "%{$search}%")
            ->orWhere('divisi', 'like', "%{$search}%")
            ->latest()
            ->paginate(10);
            
        return response()->json([
            'success' => true,
            'data' => $tims
        ]);
    }

    /**
     * Search divisi.
     */
    public function searchDivisi(Request $request)
    {
        $search = $request->get('search');
        
        $divisis = Divisi::where('divisi', 'like', "%{$search}%")
            ->latest()
            ->paginate(10);
            
        return response()->json([
            'success' => true,
            'data' => $divisis
        ]);
    }

    /**
     * Get all divisis for dropdown.
     */
    public function getDivisis()
    {
        $divisis = Divisi::all(['id', 'divisi']);
        
        return response()->json([
            'success' => true,
            'data' => $divisis
        ]);
    }

    /**
     * Get tims by divisi id for dropdowns.
     */
    public function getTimsByDivisi($id)
    {
        try {
            $divisi = Divisi::find($id);
            if (!$divisi) {
                return response()->json(['success' => false, 'message' => 'Divisi tidak ditemukan'], 404);
            }

            $tims = Tim::where('divisi', $divisi->divisi)->get(['id', 'tim']);

            return response()->json(['success' => true, 'data' => $tims]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}