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
     * API: Ambil tunjangan per karyawan (format sesuai frontend hr/data_karyawan.blade.php)
     * Response:
     * {
     *   success: true,
     *   data: { tetap: [{id:...}], tidak_tetap:[{id:...}] }
     * }
     */
    public function getTunjanganKaryawanApi($id)
    {
        try {
            $karyawan = Karyawan::with('user')->findOrFail($id);

            $all = $karyawan->tunjanganDefault()->get();

            $tetap = $all->where('tipe', 'bulanan')->values()->map(function ($t) {
                return ['id' => $t->id];
            })->all();

            $tidakTetap = $all->whereIn('tipe', ['bonus', 'insentif'])->values()->map(function ($t) {
                return ['id' => $t->id];
            })->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'tetap' => $tetap,
                    'tidak_tetap' => $tidakTetap,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('getTunjanganKaryawanApi error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tunjangan karyawan'
            ], 500);
        }
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
                    'status' => $item->status ?? 'draft',
                    'kontrak_mulai' => isset($validated['kontrak_mulai']) && $validated['kontrak_mulai'] ? \Carbon\Carbon::parse($validated['kontrak_mulai'])->format('Y-m-d') : null,
                    'kontrak_selesai' => isset($validated['kontrak_selesai']) && $validated['kontrak_selesai'] ? \Carbon\Carbon::parse($validated['kontrak_selesai'])->format('Y-m-d') : null
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
                'kontrak_mulai' => 'nullable|date',
                'kontrak_selesai' => 'nullable|date|after_or_equal:kontrak_mulai',
                'status_kerja' => 'nullable|string',
                'status_karyawan' => 'nullable|string',
                'tunjangan_tetap_ids' => 'nullable',
                'tunjangan_tidak_tetap_ids' => 'nullable',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            DB::beginTransaction();

            // Buat user (User::boot() otomatis membuat Karyawan)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'divisi_id' => $validated['divisi_id'] ?? null,
                'gaji' => $validated['gaji'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'kontak' => $validated['kontak'] ?? null,
                'kontrak_mulai' => isset($validated['kontrak_mulai']) && $validated['kontrak_mulai'] ? \Carbon\carbon::parse($validated['kontrak_mulai'])->format('Y-m-d') : null,
                'kontrak_selesai' => isset($validated['kontrak_selesai']) && $validated['kontrak_selesai'] ? \Carbon\carbon::parse($validated['kontrak_selesai'])->format('Y-m-d') : null
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
            
            $allTunjanganIds = array_filter(array_merge((array)$tunjanganTetapIds, (array)$tunjanganTidakTetapIds));
            
            \Log::info('Saving tunjangan IDs to pivot:', [
                'tetap_decoded' => $tunjanganTetapIds,
                'tidak_tetap_decoded' => $tunjanganTidakTetapIds,
                'all_ids' => $allTunjanganIds
            ]);

            // Ambil Karyawan yang sudah dibuat otomatis oleh User::boot()
            // PERBAIKAN: Tidak membuat Karyawan baru (sudah dibuat di User::boot) → mencegah double data
            $karyawan = Karyawan::where('user_id', $user->id)->first();

            if (!$karyawan) {
                // Fallback jika boot() tidak membuat karyawan
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
                    'foto' => $fotoPath,
                    'kontrak_mulai' => isset($validated['kontrak_mulai']) && $validated['kontrak_mulai'] ? \Carbon\Carbon::parse($validated['kontrak_mulai'])->format('Y-m-d') : null,
                    'kontrak_selesai' => isset($validated['kontrak_selesai']) && $validated['kontrak_selesai'] ? \Carbon\Carbon::parse($validated['kontrak_selesai'])->format('Y-m-d') : null
                ]);
            } else {
                // Update field tambahan yang tidak ada di User::boot()
                // tim_id hanya ada di tabel karyawan (bukan users)
                $updateData = [
                    'divisi_id' => $validated['divisi_id'] ?? null,
                    'tim_id' => $validated['tim_id'] ?? null,
                    'status_kerja' => $validated['status_kerja'] ?? 'aktif',
                    'status_karyawan' => $validated['status_karyawan'] ?? 'tetap',
                ];
                if ($fotoPath) {
                    $updateData['foto'] = $fotoPath;
                }
                $karyawan->update($updateData);
            }

            // Sync tunjangan ke tabel karyawan_tunjangan (pivot bersih tanpa bulan/tahun)
            \Log::info('Before sync - karyawan ID:', ['id' => $karyawan->id, 'tunjangan_ids' => $allTunjanganIds]);
            $syncResult = $karyawan->tunjanganDefault()->sync($allTunjanganIds);
            \Log::info('After sync - result:', $syncResult);
            
            // Verify sync worked
            $verifyCount = $karyawan->tunjanganDefault()->count();
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
                'kontrak_mulai' => 'nullable|date',
                'kontrak_selesai' => 'nullable|date|after_or_equal:kontrak_mulai',
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
            ];
            if ($request->has('divisi_id')) {
                $userData['divisi_id'] = $validated['divisi_id'] ?? null;
            }
            if ($request->has('gaji')) {
                $userData['gaji'] = $validated['gaji'] ?? null;
            }
            if ($request->has('alamat')) {
                $userData['alamat'] = $validated['alamat'] ?? null;
            }
            if ($request->has('kontak')) {
                $userData['kontak'] = $validated['kontak'] ?? null;
            }
            if ($request->has('kontrak_mulai')) {
                $userData['kontrak_mulai'] = isset($validated['kontrak_mulai']) && $validated['kontrak_mulai'] ? \Carbon\Carbon::parse($validated['kontrak_mulai'])->format('Y-m-d') : null;
            }
            if ($request->has('kontrak_selesai')) {
                $userData['kontrak_selesai'] = isset($validated['kontrak_selesai']) && $validated['kontrak_selesai'] ? \Carbon\Carbon::parse($validated['kontrak_selesai'])->format('Y-m-d') : null;
            }
            if (array_key_exists('status_kerja', $validated)) {
                $userData['status_kerja'] = $validated['status_kerja'];
            }
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
            $karyawanData = [
                'nama' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];
            if ($request->has('divisi_id')) {
                $karyawanData['divisi_id'] = $validated['divisi_id'] ?? null;
            }
            if ($request->has('tim_id')) {
                $karyawanData['tim_id'] = $validated['tim_id'] ?? null;
            }
            if ($request->has('alamat')) {
                $karyawanData['alamat'] = $validated['alamat'] ?? null;
            }
            if ($request->has('kontak')) {
                $karyawanData['kontak'] = $validated['kontak'] ?? null;
            }
            if ($request->has('gaji')) {
                $karyawanData['gaji'] = $validated['gaji'] ?? null;
            }
            if ($request->has('status_kerja')) {
                $karyawanData['status_kerja'] = $validated['status_kerja'];
            }
            if ($request->has('status_karyawan')) {
                $karyawanData['status_karyawan'] = $validated['status_karyawan'];
            }
            if ($fotoPath !== $karyawan->foto) {
                $karyawanData['foto'] = $fotoPath;
            }
            if ($request->has('kontrak_mulai')) {
                $karyawanData['kontrak_mulai'] = isset($validated['kontrak_mulai']) && $validated['kontrak_mulai'] ? \Carbon\Carbon::parse($validated['kontrak_mulai'])->format('Y-m-d') : null;
            }
            if ($request->has('kontrak_selesai')) {
                $karyawanData['kontrak_selesai'] = isset($validated['kontrak_selesai']) && $validated['kontrak_selesai'] ? \Carbon\Carbon::parse($validated['kontrak_selesai'])->format('Y-m-d') : null;
            }
            $karyawan->update($karyawanData);

            // Sync tunjangan ke tabel karyawan_tunjangan (pivot bersih tanpa bulan/tahun)
            $karyawan->tunjanganDefault()->sync($allTunjanganIds);

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

            // Hapus pivot tunjangan default
            $karyawan->tunjanganDefault()->detach();

            // Hapus record karyawan
            $karyawan->delete();

            // Hapus user juga agar tidak muncul lagi di daftar
            if ($userId) {
                User::where('id', $userId)->delete();
            }

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

            // Baca tunjangan dari karyawan_tunjangan (pivot bersih)
            $semuaTunjangan = $karyawan->tunjanganDefault()->get();
            $tetapIds = $semuaTunjangan->where('tipe', 'bulanan')->pluck('id')->toArray();
            $tidakTetapIds = $semuaTunjangan->whereIn('tipe', ['bonus', 'insentif'])->pluck('id')->toArray();

            // Ambil data dari user jika tersedia
            $user = $karyawan->user;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $karyawan->id,
                    'user_id' => $karyawan->user_id,
                    'nama' => $user->name ?? $karyawan->nama,
                    'email' => $user->email ?? $karyawan->email,
                    'role' => $user->role ?? $karyawan->role,
                    'divisi_id' => $user->divisi_id ?? $karyawan->divisi_id,
                    'tim_id' => $karyawan->tim_id,
                    'alamat' => $user->alamat ?? $karyawan->alamat,
                    'kontak' => $user->kontak ?? $karyawan->kontak,
                    'gaji' => $user->gaji ?? $karyawan->gaji,
                    'status_kerja' => $karyawan->status_kerja,
                    'status_karyawan' => $karyawan->status_karyawan,
                    'foto' => $karyawan->foto ? asset('storage/' . $karyawan->foto) : null,
                    'tunjangan_tetap_ids' => $tetapIds,
                    'tunjangan_tidak_tetap_ids' => $tidakTetapIds
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
        $karyawan = Karyawan::with(['user', 'divisiRelation', 'tim'])->paginate(10);
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
        
        $query = Karyawan::with(['user', 'tim'])
            ->where('divisi_id', $divisiId);
            
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('role', 'like', "%$search%");
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