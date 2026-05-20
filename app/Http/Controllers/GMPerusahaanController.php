<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;

class GMPerusahaanController extends Controller
{
    /**
     * Tampilkan halaman utama data perusahaan.
     */
    public function index()
    {
        $perusahaans = Perusahaan::latest()->get();
        return view('general_manajer.perusahaan.index', compact('perusahaans'));
    }

    /**
     * Simpan data perusahaan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'klien'           => 'required|string|max:255',
            'kontak'          => 'required|string|max:255',
            'alamat'          => 'required|string',
            'jumlah_kerjasama'=> 'nullable|integer|min:0', 
        ]);

        Perusahaan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil ditambahkan'
        ]);
    }

    /**
     * Update data perusahaan.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'klien'           => 'required|string|max:255',
            'kontak'          => 'required|string|max:255',
            'alamat'          => 'required|string',
            'jumlah_kerjasama'=> 'nullable|integer|min:0',
        ]);

        $perusahaan = Perusahaan::findOrFail($id);
        $perusahaan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil diperbarui'
        ]);
    }

    /**
     * Hapus data perusahaan.
     */
    public function destroy($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $perusahaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil dihapus'
        ]);
    }
    // Di GMPerusahaanController.php tambahkan:

/**
 * API: Get perusahaan data for finance dropdown
 */
public function getPerusahaanForFinance(Request $request)
{
    try {
        \Log::info('API getPerusahaanForFinance called', ['user_id' => auth()->id()]);
        
        $perusahaans = Perusahaan::orderBy('nama_perusahaan', 'asc')
            ->get(['id', 'nama_perusahaan', 'klien', 'alamat', 'kontak']);
        
        \Log::info('Perusahaan data retrieved', [
            'count' => $perusahaans->count(),
            'first_item' => $perusahaans->first() ? $perusahaans->first()->toArray() : 'empty'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil diambil',
            'data' => $perusahaans,
            'total_count' => $perusahaans->count()
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getPerusahaanForFinance: ' . $e->getMessage(), [
            'exception' => $e
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data perusahaan',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}
}