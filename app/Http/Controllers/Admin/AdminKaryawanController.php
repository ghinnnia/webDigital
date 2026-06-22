<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\Tim;
use App\Models\TunjanganMaster;
use App\Models\Gaji; // Tambahkan model Gaji
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdminKaryawanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get gaji terbaru karyawan dari data HR
     */
    public function getGajiTerbaru($userId)
    {
        try {
            // Ambil bulan dan tahun sekarang
            $bulanSekarang = date('m');
            $tahunSekarang = date('Y');
            
            // Cari data gaji terbaru
            $gaji = Gaji::where('karyawan_id', $userId)
                ->where('bulan', $bulanSekarang)
                ->where('tahun', $tahunSekarang)
                ->first();
            
            // Jika tidak ada, cari bulan sebelumnya
            if (!$gaji) {
                $bulanLalu = date('m', strtotime('-1 month'));
                $tahunLalu = date('Y', strtotime('-1 month'));
                
                $gaji = Gaji::where('karyawan_id', $userId)
                    ->where('bulan', $bulanLalu)
                    ->where('tahun', $tahunLalu)
                    ->first();
            }
            
            if ($gaji) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'gaji_pokok' => $gaji->gaji_pokok,
                        'total_tunjangan' => $gaji->total_tunjangan ?? 0,
                        'total_gaji' => $gaji->total_gaji,
                        'bulan' => $gaji->bulan,
                        'tahun' => $gaji->tahun,
                        'gaji_formatted' => 'Rp ' . number_format($gaji->gaji_pokok, 0, ',', '.')
                    ]
                ]);
            }
            
            // Jika belum ada data gaji, kembalikan default berdasarkan role
            $user = User::find($userId);
            $defaultGaji = 5000000;
            if ($user) {
                if ($user->role == 'general_manager') $defaultGaji = 15000000;
                elseif ($user->role == 'manager_divisi') $defaultGaji = 10000000;
                elseif ($user->role == 'finance') $defaultGaji = 8000000;
                elseif ($user->role == 'hr') $defaultGaji = 7000000;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'gaji_pokok' => $defaultGaji,
                    'total_tunjangan' => 0,
                    'total_gaji' => $defaultGaji,
                    'is_default' => true,
                    'gaji_formatted' => 'Rp ' . number_format($defaultGaji, 0, ',', '.')
                ],
                'message' => 'Belum ada data gaji dari HR, menggunakan gaji default'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get gaji terbaru error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data gaji: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get semua riwayat gaji karyawan
     */
    public function getRiwayatGaji($userId)
    {
        try {
            $gajiList = Gaji::where('karyawan_id', $userId)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->get();
            
            $riwayat = $gajiList->map(function($item) {
                $namaBulan = \Carbon\Carbon::create()->month($item->bulan)->format('F');
                return [
                    'id' => $item->id,
                    'periode' => $namaBulan . ' ' . $item->tahun,
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'gaji_pokok' => $item->gaji_pokok,
                    'gaji_pokok_formatted' => 'Rp ' . number_format($item->gaji_pokok, 0, ',', '.'),
                    'total_tunjangan' => $item->total_tunjangan ?? 0,
                    'total_tunjangan_formatted' => 'Rp ' . number_format($item->total_tunjangan ?? 0, 0, ',', '.'),
                    'potongan' => $item->potongan_bpjs,
                    'total_gaji' => $item->total_gaji,
                    'total_gaji_formatted' => 'Rp ' . number_format($item->total_gaji, 0, ',', '.'),
                    'status' => $item->status ?? 'draft'
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $riwayat,
                'total' => $riwayat->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get riwayat gaji error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat gaji'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            \Log::info('=== STORE KARYAWAN ===');
            \Log::info('Request data:', $request->all());
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role' => 'required|string',
                'divisi_id' => 'nullable|exists:divisi,id',
                'tim_id' => 'nullable|exists:tims,id',
                'alamat' => 'nullable|string',
                'kontak' => 'nullable|string',
                'gaji' => 'nullable|numeric',
                'status_kerja' => 'nullable|string',
                'status_karyawan' => 'nullable|string',
                'tunjangan_tetap_ids' => 'nullable',
                'tunjangan_tidak_tetap_ids' => 'nullable',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            DB::beginTransaction();

            // Buat user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'divisi_id' => $validated['divisi_id'] ?? null,
                'gaji' => $validated['gaji'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'kontak' => $validated['kontak'] ?? null,
            ]);

            // Handle foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto_karyawan', 'public');
            }

            // Proses tunjangan
            $tunjanganTetapIds = $request->tunjangan_tetap_ids;
            $tunjanganTidakTetapIds = $request->tunjangan_tidak_tetap_ids;
            
            \Log::info('Raw tunjangan input:', [
                'tetap_raw' => $tunjanganTetapIds,
                'tidak_tetap_raw' => $tunjanganTidakTetapIds,
                'tetap_type' => gettype($tunjanganTetapIds),
                'tidak_tetap_type' => gettype($tunjanganTidakTetapIds)
            ]);
            
            if (is_string($tunjanganTetapIds)) {
                $tunjanganTetapIds = json_decode($tunjanganTetapIds, true);
            }
            if (is_string($tunjanganTidakTetapIds)) {
                $tunjanganTidakTetapIds = json_decode($tunjanganTidakTetapIds, true);
            }
            
            $allTunjanganIds = array_merge((array)$tunjanganTetapIds, (array)$tunjanganTidakTetapIds);
            
            \Log::info('Saving tunjangan IDs to pivot:', [
                'tetap_decoded' => $tunjanganTetapIds,
                'tidak_tetap_decoded' => $tunjanganTidakTetapIds,
                'all_ids' => $allTunjanganIds
            ]);

            // Buat karyawan
            $karyawan = Karyawan::create([
                'user_id' => $user->id,
                'nama' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'divisi_id' => $validated['divisi_id'] ?? null,
                'tim_id' => $validated['tim_id'] ?? null,
                'alamat' => $validated['alamat'] ?? '',
                'kontak' => $validated['kontak'] ?? '',
                'gaji' => $validated['gaji'] ?? null,
                'status_kerja' => $validated['status_kerja'] ?? 'aktif',
                'status_karyawan' => $validated['status_karyawan'] ?? 'tetap',
                'foto' => $fotoPath
            ]);

            // Sync tunjangan ke pivot table
            \Log::info('Before sync - karyawan ID:', ['id' => $karyawan->id, 'tunjangan_ids' => $allTunjanganIds]);
            $syncResult = $karyawan->tunjanganMaster()->sync($allTunjanganIds);
            \Log::info('After sync - result:', $syncResult);
            
            // Verify sync worked
            $verifyCount = $karyawan->tunjanganMaster()->count();
            \Log::info('Verify count after sync:', ['count' => $verifyCount]);

            DB::commit();

            \Log::info('Karyawan saved successfully:', ['id' => $karyawan->id]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $karyawan
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Store karyawan error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            \Log::info('=== UPDATE KARYAWAN ===');
            \Log::info('Update ID:', ['id' => $id]);
            \Log::info('Request data:', $request->all());
            
            $karyawan = Karyawan::findOrFail($id);
            $user = User::findOrFail($karyawan->user_id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6',
                'role' => 'required|string',
                'divisi_id' => 'nullable|exists:divisi,id',
                'tim_id' => 'nullable|exists:tims,id',
                'alamat' => 'nullable|string',
                'kontak' => 'nullable|string',
                'gaji' => 'nullable|numeric',
                'status_kerja' => 'nullable|string',
                'status_karyawan' => 'nullable|string',
                'tunjangan_tetap_ids' => 'nullable',
                'tunjangan_tidak_tetap_ids' => 'nullable',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            DB::beginTransaction();

            // Update user
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'divisi_id' => $validated['divisi_id'] ?? null,
                'gaji' => $validated['gaji'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'kontak' => $validated['kontak'] ?? null,
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $user->update($userData);

            // Handle foto
            $fotoPath = $karyawan->foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                    Storage::disk('public')->delete($fotoPath);
                }
                $fotoPath = $request->file('foto')->store('foto_karyawan', 'public');
            }

            // Proses tunjangan
            $tunjanganTetapIds = $request->tunjangan_tetap_ids;
            $tunjanganTidakTetapIds = $request->tunjangan_tidak_tetap_ids;
            
            if (is_string($tunjanganTetapIds)) {
                $tunjanganTetapIds = json_decode($tunjanganTetapIds, true);
            }
            if (is_string($tunjanganTidakTetapIds)) {
                $tunjanganTidakTetapIds = json_decode($tunjanganTidakTetapIds, true);
            }
            
            $allTunjanganIds = array_merge((array)$tunjanganTetapIds, (array)$tunjanganTidakTetapIds);
            
            \Log::info('Updating tunjangan IDs in pivot:', [
                'ids' => $allTunjanganIds
            ]);

            // Update karyawan
            $karyawan->update([
                'nama' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'divisi_id' => $validated['divisi_id'] ?? null,
                'tim_id' => $validated['tim_id'] ?? null,
                'alamat' => $validated['alamat'] ?? '',
                'kontak' => $validated['kontak'] ?? '',
                'gaji' => $validated['gaji'] ?? null,
                'status_kerja' => $validated['status_kerja'] ?? 'aktif',
                'status_karyawan' => $validated['status_karyawan'] ?? 'tetap',
                'foto' => $fotoPath
            ]);

            // Sync tunjangan ke pivot table
            $karyawan->tunjanganMaster()->sync($allTunjanganIds);

            DB::commit();

            \Log::info('Karyawan updated successfully:', ['id' => $karyawan->id]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diupdate',
                'data' => $karyawan
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update karyawan error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            $userId = $karyawan->user_id;

            // Hapus foto jika ada
            if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            $karyawan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete karyawan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKaryawan($id)
    {
        try {
            $karyawan = Karyawan::with(['user', 'divisiRelation', 'tim'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $karyawan->id,
                    'user_id' => $karyawan->user_id,
                    'nama' => $karyawan->nama,
                    'email' => $karyawan->email,
                    'role' => $karyawan->role,
                    'divisi_id' => $karyawan->divisi_id,
                    'tim_id' => $karyawan->tim_id,
                    'alamat' => $karyawan->alamat,
                    'kontak' => $karyawan->kontak,
                    'gaji' => $karyawan->gaji,
                    'status_kerja' => $karyawan->status_kerja,
                    'status_karyawan' => $karyawan->status_karyawan,
                    'foto' => $karyawan->foto ? asset('storage/' . $karyawan->foto) : null,
                    'tunjangan_tetap_ids' => $karyawan->tunjanganTetap->pluck('id')->toArray(),
                    'tunjangan_tidak_tetap_ids' => $karyawan->tunjanganTidakTetap->pluck('id')->toArray()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get karyawan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        }
    }
    
       /**
     * Get karyawan data for general manager view
     */
    public function karyawanGeneral(Request $request)
    {
        $query = Karyawan::with(['user', 'divisiRelation', 'tim']);

        // Logika Search (mendukung parameter 'search' atau 'keyword')
        if ($request->filled('search') || $request->filled('keyword')) {
            $keyword = $request->input('search') ?? $request->input('keyword');
            
            $query->where(function($q) use ($keyword) {
                $q->where('nama', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%")
                  ->orWhere('role', 'like', "%$keyword%")
                  ->orWhereHas('user', function($qu) use ($keyword) {
                      $qu->where('email', 'like', "%$keyword%")
                        ->orWhere('role', 'like', "%$keyword%");
                  });
            });
        }

        // Logika Filter Divisi (mendukung parameter 'divisi' atau 'divisi_id')
        if ($request->filled('divisi') || $request->filled('divisi_id')) {
            $divisiId = $request->input('divisi') ?? $request->input('divisi_id');
            $query->where('divisi_id', $divisiId);
        }

        $karyawan = $query->paginate(10);
        
        return view('general_manajer.data_karyawan', compact('karyawan'));
    }
    
    /**
     * Get karyawan data for finance view
     */
    public function karyawanFinance(Request $request)
    {
        $karyawans = Karyawan::with(['user', 'divisiRelation', 'tim'])->get();
        return view('finance.daftar_karyawan', compact('karyawans'));
    }
    
       /**
     * Get daftar karyawan view for manager divisi
     */
    public function daftarKaryawanView(Request $request)
    {
        $user = Auth::user();
        $divisiId = $user->divisi_id;
        
        $divisi = Divisi::find($divisiId);
        $namaDivisiManager = $divisi ? $divisi->divisi : null;
        
        $query = Karyawan::with(['user', 'divisi', 'tim'])
            ->where('divisi_id', $divisiId);
            
        // Gunakan $request->filled() agar pencarian kosong tidak merusak query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // 1. Cari berdasarkan data langsung di tabel karyawan
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('role', 'like', "%$search%")
                  
                  // 2. Tambahan: Cari berdasarkan nama divisi di tabel divisi
                  ->orWhereHas('divisi', function($qd) use ($search) {
                      $qd->where('divisi', 'like', "%$search%");
                  })
                  
                  // 3. Tambahan: Cari berdasarkan email dan role di tabel users (jika di tabel karyawan kosong)
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'like', "%$search%")
                        ->orWhere('role', 'like', "%$search%");
                  });
            });
        }
        
        $karyawan = $query->get();
        
        return view('manager_divisi.daftar_karyawan', compact('karyawan', 'namaDivisiManager'));
    }
    /**
     * Get karyawan by divisi for manager
     */
    public function karyawanDivisi($divisiId = null)
    {
        $query = Karyawan::with(['user', 'tim']);
        
        if ($divisiId) {
            $query->where('divisi_id', $divisiId);
        }
        
        $karyawan = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $karyawan
        ]);
    }
}