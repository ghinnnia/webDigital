<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LayananController extends Controller
{
    public function financeIndex()
    {
        $layanans = Layanan::all();
        return view('finance.data_layanan', compact('layanans'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $layanans = Layanan::latest()->get();
        return view('admin.data_layanan', compact('layanans'));
    }

    public function Generalindex()
    {
        $layanan = Layanan::latest()->get();
        return view('general_manajer.data_layanan', compact('layanan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_layanan' => 'required|string|max:255',
            'harga'        => 'nullable|numeric|min:0',
            'hpp'          => 'nullable|numeric|min:0',
            'deskripsi'    => 'nullable|string',
            'foto'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['nama_layanan', 'harga', 'hpp', 'deskripsi']);

            $data['harga'] = $request->harga ?? 0;
            $data['hpp'] = $request->hpp ?? 0;

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoName = time() . '_' . Str::random(10) . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/layanan', $fotoName);
                $data['foto'] = 'layanan/' . $fotoName;
            }

            $layanan = Layanan::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil ditambahkan!',
                'data' => $layanan
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing layanan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage - VERSI SEDERHANA
     */
    public function update(Request $request, $id)
    {
        try {
            // Validasi sederhana
            $request->validate([
                'nama_layanan' => 'required|string|max:255',
                'deskripsi'    => 'nullable|string',
                'harga' => 'nullable|numeric|min:0',
                'hpp' => 'nullable|numeric|min:0',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            ]);

            $layanan = Layanan::findOrFail($id);
            
            // Update data dasar
            $layanan->nama_layanan = $request->nama_layanan;
            $layanan->deskripsi = $request->deskripsi;
            $layanan->harga = $request->harga ?? 0;
            $layanan->hpp = $request->hpp ?? 0;

            // Handle foto
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($layanan->foto && Storage::exists('public/' . $layanan->foto)) {
                    Storage::delete('public/' . $layanan->foto);
                }
                
                // Simpan foto baru
                $foto = $request->file('foto');
                $fotoName = time() . '_' . $foto->getClientOriginalName();
                $foto->storeAs('public/layanan', $fotoName);
                $layanan->foto = 'layanan/' . $fotoName;
            }
            // Jika tidak ada file baru tapi ada current_foto, tetap gunakan
            elseif ($request->has('current_foto')) {
                $layanan->foto = $request->current_foto;
            }

            $layanan->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil diperbarui!',
                'data' => $layanan
            ]);

        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        try {
            $layanan = Layanan::findOrFail($id);
            
            if ($layanan->foto && Storage::exists('public/' . $layanan->foto)) {
                Storage::delete('public/' . $layanan->foto);
            }
            
            $layanan->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting layanan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update harga layanan khusus role finance.
     */
    public function updateHarga(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'harga' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $layanan = Layanan::findOrFail($id);
            $layanan->harga = (float) $request->harga;
            $layanan->save();

            return response()->json([
                'success' => true,
                'message' => 'Harga layanan berhasil diperbarui.',
                'data' => $layanan,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error update harga layanan: ' . $e->getMessage(), [
                'id' => $id,
                'payload' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui harga.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // Method lainnya (tidak perlu diubah)
    public function indexLayanan(Request $request)
    {
        $query = Layanan::query();
        
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_layanan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        $pelayanan = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $search = $request->query('search');
        
        return view('general_manajer.data_layanan', compact('pelayanan', 'search'));
    }

    public function landingPage()
    {
        $layanans = Layanan::latest()->get(); 
        return view('home', compact('layanans'));
    }
    
    public function getCount()
    {
        try {
            $count = Layanan::count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'count' => $count
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting layanan count: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data layanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id, Request $request)
    {
        try {
            $layanan = Layanan::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $layanan
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing layanan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Get layanan data for invoice dropdown
     */
    public function getForInvoiceDropdown()
    {
        try {
            $layanan = Layanan::where('status', 'aktif')
                ->select('id', 'nama_layanan', 'harga', 'deskripsi')
                ->orderBy('nama_layanan')
                ->get();
                
            return response()->json([
                'success' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $layanan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching layanan dropdown: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data layanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // Di LayananController.php tambahkan jika belum ada:

/**
 * API: Get layanan for dropdown
 */
public function getLayananForDropdown(Request $request)
{
    try {
        \Log::info('API getLayananForDropdown called', ['user_id' => auth()->id()]);
        
        $layanans = Layanan::orderBy('nama_layanan', 'asc')
            ->get(['id', 'nama_layanan', 'harga', 'deskripsi']);
        
        \Log::info('Layanan data retrieved', [
            'count' => $layanans->count(),
            'first_item' => $layanans->first() ? $layanans->first()->toArray() : 'empty'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data layanan berhasil diambil',
            'data' => $layanans,
            'total_count' => $layanans->count()
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getLayananForDropdown: ' . $e->getMessage(), [
            'exception' => $e
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data layanan',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}

    public function financeApi()
    {
        try {
            $layanan = Layanan::where('status', 'aktif')
                ->select('id', 'nama_layanan', 'harga', 'deskripsi', 'created_at')
                ->orderBy('nama_layanan')
                ->get();
                
            return response()->json([
                'success' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $layanan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching finance layanan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data layanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
