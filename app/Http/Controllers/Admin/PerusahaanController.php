<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource (View Halaman).
     */
    public function index()
    {
        // WAJIB: Ambil data dari database
        $perusahaans = Perusahaan::orderBy('created_at', 'desc')->get();
        
        // WAJIB: Kirim data ke view (blade)
        // 'perusahaans' harus sama dengan variabel $perusahaans di atas
        return view('admin.add_perusahaan', compact('perusahaans'));
    }

    /**
     * Get all companies data as JSON (Untuk AJAX).
     */
    public function getData()
    {
        $perusahaans = Perusahaan::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $perusahaans
        ]);
    }

    /**
     * Store a newly created resource in storage (Tambah Data).
     */
    public function store(Request $request)
    {
        $hasStatusColumn = Schema::hasColumn('perusahaans', 'status');

        // Validasi Input
        $rules = [
            'nama_perusahaan' => 'required|string|max:255',
            'klien'            => 'required|string|max:255',
            'kontak'           => 'required|string|max:255',
            'alamat'           => 'required|string',
            'jumlah_kerjasama' => 'nullable|integer', // Validasi harus angka bulat
        ];

        if ($hasStatusColumn) {
            $rules['status'] = 'nullable|in:aktif,nonaktif';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Ambil langsung input jumlah (tanpa pembersihan format rupiah)
            $jumlah = $request->input('jumlah_kerjasama');

            // Simpan Data Baru
            $payload = [
                'nama_perusahaan' => $request->input('nama_perusahaan'),
                'klien'            => $request->input('klien'),
                'kontak'           => $request->input('kontak'),
                'alamat'           => $request->input('alamat'),
                'jumlah_kerjasama' => $jumlah ? (int)$jumlah : null, // Cast ke integer
            ];

            if ($hasStatusColumn) {
                // Status awal selalu nonaktif, lalu otomatis aktif saat dipakai membuat invoice.
                $payload['status'] = 'nonaktif';
            }

            $perusahaan = Perusahaan::create($payload);

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil ditambahkan!',
                'data'    => $perusahaan
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage (Edit Data).
     */
    public function update(Request $request, $id)
    {
        $hasStatusColumn = Schema::hasColumn('perusahaans', 'status');

        // Validasi Input
        $rules = [
            'nama_perusahaan' => 'required|string|max:255',
            'klien'            => 'required|string|max:255',
            'kontak'           => 'required|string|max:255',
            'alamat'           => 'required|string',
            'jumlah_kerjasama' => 'nullable|integer', // Validasi harus angka bulat
        ];

        if ($hasStatusColumn) {
            $rules['status'] = 'required|in:aktif,nonaktif';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari data berdasarkan ID
            $perusahaan = Perusahaan::findOrFail($id);

            // Ambil langsung input jumlah (tanpa pembersihan format rupiah)
            $jumlah = $request->input('jumlah_kerjasama');

            // Update Data
            $payload = [
                'nama_perusahaan' => $request->input('nama_perusahaan'),
                'klien'            => $request->input('klien'),
                'kontak'           => $request->input('kontak'),
                'alamat'           => $request->input('alamat'),
                'jumlah_kerjasama' => $jumlah ? (int)$jumlah : null, // Cast ke integer
            ];

            if ($hasStatusColumn) {
                $payload['status'] = $request->input('status', 'nonaktif');
            }

            $perusahaan->update($payload);

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil diperbarui!',
                'data'    => $perusahaan
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (Hapus Data).
     */
    public function destroy($id)
    {
        try {
            $perusahaan = Perusahaan::findOrFail($id);
            $perusahaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil dihapus.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.'
            ], 500);
        }
    }

    public function getDataForDropdown(Request $request)
{
    try {
        $perusahaanList = Perusahaan::orderBy('nama_perusahaan', 'asc')
            ->get(['id', 'nama_perusahaan', 'klien', 'alamat', 'kontak']);

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil diambil',
            'data' => $perusahaanList
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data perusahaan',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
