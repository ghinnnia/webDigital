<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi; // TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**p
     * Halaman utama user management
     */
public function index()
{
    // TAMPILKAN DATA TERBARU DI ATAS (DESCENDING)
    $users = User::with('divisi') // Load relationship divisi
                ->orderBy('created_at', 'desc')
                ->get();
    
    $divisis = Divisi::orderBy('divisi', 'asc')->get();
    
    return view('admin.user', compact('users', 'divisis'));
}

    /**
     * API endpoint untuk mendapatkan data users
     * Route: /admin/users/data
     */
    public function getData(): JsonResponse
    {
        try {
            $users = User::select('id', 'name', 'email', 'role', 'divisi_id')
                ->orderBy('name', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint untuk mendapatkan data divisi untuk dropdown
     * Route: /admin/divisis/list
     */
public function getDivisis(): JsonResponse
{
    try {
        $divisis = Divisi::select('id', 'divisi')->get();
        
        return response()->json([
            'success' => true,
            'data' => $divisis
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * API endpoint untuk get unique roles
 * Route: /roles/list
 */
public function getRoles(): JsonResponse
{
    try {
        $roles = User::select('role')
            ->distinct()
            ->whereNotNull('role')
            ->where('role', '!=', '')
            ->orderBy('role', 'asc')
            ->get()
            ->pluck('role')
            ->map(function ($role) {
                return [
                    'id' => $role,
                    'role' => ucfirst($role)
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * API endpoint untuk select dropdown (minimal data)
     * Route: /admin/users/data (alias untuk compatibility)
     */
    public function data(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => User::select('id', 'name', 'role')->orderBy('name')->get()
        ]);
    }

    /**
     * Simpan user baru
     */
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:5',
            'role'              => 'required|in:owner,admin,general_manager,manager_divisi,finance,karyawan',
            'divisi_id'         => 'nullable|exists:divisi,id',
            'alamat'            => 'nullable|string',
            'kontak'            => 'nullable|string|max:20',
            'status_kerja'      => 'nullable|in:aktif,resign,phk,tidak_aktif',
            'status_karyawan'   => 'nullable|in:tetap,kontrak,freelance',
            'gaji'              => 'nullable|numeric',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => bcrypt($validated['password']),
            'role'              => $validated['role'],
            'divisi_id'         => $validated['divisi_id'] ?? null,
            'alamat'            => $validated['alamat'] ?? null,
            'kontak'            => $validated['kontak'] ?? null,
            'status_kerja'      => $validated['status_kerja'] ?? 'aktif',
            'status_karyawan'   => $validated['status_karyawan'] ?? 'tetap',
            'gaji'              => $validated['gaji'] ?? null,
            'sisa_cuti'         => 12, // Default value sesuai migration
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data'    => $user->load('divisi', 'karyawan')
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('User Store Validation Error:', [
            'errors' => $e->errors(),
            'request_data' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors'  => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        \Log::error('User Store Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Update user
     */
public function update(Request $request, $id)
{
    try {
        \Log::info('=== USER UPDATE REQUEST ===');
        \Log::info('User ID: ' . $id);
        \Log::info('Request Data:', $request->all());
        
        // Validasi
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $id,
            'role'              => 'required|in:owner,admin,general_manager,manager_divisi,finance,karyawan',
            'divisi_id'         => 'nullable|exists:divisi,id',
            'password'          => 'nullable|min:5',
            'alamat'            => 'nullable|string',
            'kontak'            => 'nullable|string|max:20',
            'status_kerja'      => 'nullable|in:aktif,resign,phk,tidak_aktif',
            'status_karyawan'   => 'nullable|in:tetap,kontrak,freelance',
            'gaji'              => 'nullable|numeric',
        ]);

        // Cari user dengan relasi karyawan
        $user = User::with(['karyawan', 'divisi'])->findOrFail($id);
        
        if (!$user) {
            throw new \Exception('User tidak ditemukan');
        }
        
        \Log::info('User ditemukan: ' . $user->name);

        // Mulai transaksi database
        DB::beginTransaction(); // GUNAKAN DB:: bukan \DB::

        // Update data user
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->divisi_id = $validated['divisi_id'] ?? null;
        $user->alamat = $validated['alamat'] ?? null;
        $user->kontak = $validated['kontak'] ?? null;
        $user->status_kerja = $validated['status_kerja'] ?? 'aktif';
        $user->status_karyawan = $validated['status_karyawan'] ?? 'tetap';
        $user->gaji = $validated['gaji'] ?? null;
        
        // Update password jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        
        // **SINKRONKAN KE KARYAWAN JIKA ADA**
        if ($user->karyawan) {
            $karyawan = $user->karyawan;
            $karyawanUpdated = false;
            
            // Update nama karyawan jika berbeda
            if ($karyawan->nama !== $user->name) {
                $karyawan->nama = $user->name;
                $karyawanUpdated = true;
            }
            
            // Update email karyawan jika berbeda
            if ($karyawan->email !== $user->email) {
                $karyawan->email = $user->email;
                $karyawanUpdated = true;
            }
            
            // Update divisi di karyawan jika user memiliki divisi
            if ($user->divisi && $karyawan->divisi !== $user->divisi->divisi) {
                $karyawan->divisi = $user->divisi->divisi;
                $karyawanUpdated = true;
            }
            
            // Jika jabatan kosong dan role adalah karyawan, isi jabatan
            if ((!$karyawan->jabatan || $karyawan->jabatan === '') && $user->role === 'karyawan') {
                $karyawan->jabatan = $user->role;
                $karyawanUpdated = true;
            }
            
            if ($karyawanUpdated) {
                $karyawan->save();
                \Log::info('Manual sync: Karyawan updated from User controller', [
                    'user_id' => $user->id,
                    'karyawan_id' => $karyawan->id
                ]);
            }
        }

        DB::commit();
        
        \Log::info('User dan Karyawan berhasil diupdate:', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'divisi_id' => $user->divisi_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User dan data karyawan berhasil diperbarui',
            'data' => $user->load('divisi', 'karyawan')
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        \Log::error('User Update Validation Error:', $e->errors());
        
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        DB::rollBack();
        \Log::error('User not found: ' . $id);
        
        return response()->json([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ], 404);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('User Update Error: ' . $e->getMessage());
        \Log::error('Stack Trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Hapus user
     */
/**
 * Hapus user
 */
public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        
        // Cek jika user sedang login
        if ($user->id === auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus user yang sedang login'
                ], 400);
            }
            return redirect()->route('admin.user')->with('error', 'Tidak dapat menghapus user yang sedang login');
        }
        
        $userName = $user->name;
        $user->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User '{$userName}' berhasil dihapus"
            ]);
        }
        
        return redirect()->route('admin.user')->with('success', "User '{$userName}' berhasil dihapus");
        
    } catch (\Exception $e) {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->route('admin.user')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
    }
}

    /**
     * Get user untuk edit modal
     */
    public function getUser($id): JsonResponse // OPTIONAL: Tambah method ini
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}