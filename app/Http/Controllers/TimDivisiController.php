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
        
        // Hitung total anggota (konversi string ke integer)
        $totalAnggota = Tim::sum(DB::raw('CAST(jumlah_anggota AS SIGNED)'));
        
        // Ambil data dengan pagination
        $tims = Tim::latest()->paginate(5);
        $divisis = Divisi::latest()->paginate(5);
        
        return view('general_manajer.tim_dan_divisi', compact(
            'totalTim', 'totalDivisi', 'timAktif', 'totalAnggota', 'tims', 'divisis'
        ));
    }

    /**
     * Store a newly created tim.
     */
/**
 * Store a newly created tim.
 */
public function storeTim(Request $request)
{
    try {
        \Log::info('Store Tim Request:', $request->all());
        
        // VALIDASI TANPA exists rule yang berat
        $validated = $request->validate([
            'tim' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'jumlah_anggota' => 'nullable|integer|min:0'
        ]);

        \Log::info('Validated data:', $validated);

        // Cek divisi dengan query langsung yang ringan
        $divisiExists = DB::table('divisi')
            ->where('divisi', $validated['divisi'])
            ->exists();

        if (!$divisiExists) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak ditemukan. Silakan pilih divisi yang tersedia.'
            ], 422);
        }

        // Buat tim dengan query langsung untuk menghindari Eloquent events
        $timId = DB::table('tim')->insertGetId([
            'tim' => $validated['tim'],
            'divisi' => $validated['divisi'],
            'jumlah_anggota' => $validated['jumlah_anggota'] ?? 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update jumlah_tim di divisi (tanpa trigger boot)
        $timCount = DB::table('tim')
            ->where('divisi', $validated['divisi'])
            ->count();
            
        DB::table('divisi')
            ->where('divisi', $validated['divisi'])
            ->update(['jumlah_tim' => $timCount]);

        \Log::info('Tim created successfully:', ['id' => $timId]);

        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil ditambahkan',
            'data' => ['id' => $timId]
        ], 201);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Store tim error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
        $validated = $request->validate([
            'tim' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'jumlah_anggota' => 'nullable|integer|min:0'
        ]);

        $tim = Tim::findOrFail($id);
        // Only update jumlah_anggota if provided and not null
        $tim->tim = $validated['tim'];
        $tim->divisi = $validated['divisi'];
        if (array_key_exists('jumlah_anggota', $validated) && $validated['jumlah_anggota'] !== null) {
            $tim->jumlah_anggota = $validated['jumlah_anggota'];
        }
        $tim->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil diperbarui',
            'data' => $tim
        ]);
    }

    /**
     * Remove the specified tim.
     */
    public function destroyTim($id)
    {
        $tim = Tim::findOrFail($id);
        $tim->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil dihapus'
        ]);
    }

    /**
     * Store a newly created divisi.
     */
    public function storeDivisi(Request $request)
    {
        $validated = $request->validate([
            'divisi' => 'required|string|max:255|unique:divisi'
        ]);

        // Jumlah tim akan di-set otomatis oleh model boot method
        $divisi = Divisi::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil ditambahkan',
            'data' => $divisi
        ]);
    }

    /**
     * Update the specified divisi.
     */
    public function updateDivisi(Request $request, $id)
    {
        $validated = $request->validate([
            'divisi' => 'required|string|max:255|unique:divisi,divisi,' . $id
        ]);

        $divisi = Divisi::findOrFail($id);
        $oldNamaDivisi = $divisi->divisi;
        
        $divisi->update($validated);
        
        // Update nama divisi di semua tim yang terkait
        if ($oldNamaDivisi != $validated['divisi']) {
            Tim::where('divisi', $oldNamaDivisi)
                ->update(['divisi' => $validated['divisi']]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil diperbarui',
            'data' => $divisi
        ]);
    }

    /**
     * Remove the specified divisi.
     */
    public function destroyDivisi($id)
    {
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
            ->paginate(5);
            
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
            ->paginate(5);
            
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
     * Route: /tims/by-divisi/{id}
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