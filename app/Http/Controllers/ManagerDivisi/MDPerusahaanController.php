<?php

namespace App\Http\Controllers\ManagerDivisi;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class MDPerusahaanController extends Controller
{
    /**
     * Tampilkan halaman utama data perusahaan untuk Manager Divisi.
     */
    public function index()
    {
        $perusahaans = Perusahaan::latest()->get();
        
        // Pastikan path view ada di: resources/views/manager_divisi/perusahaan/index.blade.php
        return view('manager_divisi.perusahaan.index', compact('perusahaans'));
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
}