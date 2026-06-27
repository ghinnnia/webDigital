<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Task;
use App\Models\TaskAcceptance;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\Setting;
use App\Models\CatatanRapat;
use App\Models\Pengumuman;
use App\Models\Cuti;
use App\Models\Project;
use App\Models\CutiKuota;
use App\Models\TunjanganMaster;
use App\Models\TunjanganKaryawan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\CarbonPeriod;

class KaryawanController extends Controller
{
    /**
     * Menampilkan data karyawan untuk general manager (dengan filter & pagination).
     */
/**
 * Menampilkan data karyawan untuk general manager (dengan filter & pagination).
 */
public function indexPegawai(Request $request)
{
    $user = Auth::user();
    
    if ($user->role === 'hr') {
        // Ambil data user langsung tanpa eager loading yang bermasalah
        $query = User::with(['divisi', 'tim'])
            ->whereIn('role', ['general_manager', 'manager_divisi', 'karyawan', 'finance', 'hr']);
        
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('kontak', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }
        
        if ($divisi = $request->query('divisi')) {
            $query->whereHas('divisi', function ($sq) use ($divisi) {
                $sq->where('divisi', $divisi);
            });
        }
        
        $karyawanCollection = $query->orderBy('created_at', 'desc')->get();
        
        // Format data untuk view (tanpa eager loading yang bermasalah)
        $karyawan = $karyawanCollection->map(function ($userItem) {
            // dd($userItem->toArray()); // Debugging: Periksa struktur data userItem
            // Ambil data karyawan beserta tim (tim_id ada di karyawan, bukan users)
            $karyawanData = Karyawan::with('tim')->where('user_id', $userItem->id)->first();
            
            $tetapList = collect();
            $tidakTetapList = collect();
            $tetapIds = [];
            $tidakTetapIds = [];
            
            if ($karyawanData) {
                // Ambil tunjangan dari tabel karyawan_tunjangan (pivot default, tanpa bulan/tahun)
                $semuaTunjangan = $karyawanData->tunjanganDefault()->get();

                $tetapList = $semuaTunjangan->filter(fn($t) => $t->tipe === 'bulanan');
                $tidakTetapList = $semuaTunjangan->filter(fn($t) => in_array($t->tipe, ['bonus', 'insentif']));

                $tetapIds = $tetapList->pluck('id')->toArray();
                $tidakTetapIds = $tidakTetapList->pluck('id')->toArray();
            }
            
            // Tim diambil dari tabel karyawan (bukan users, karena tim_id ada di karyawan)
            $timObject = $karyawanData ? $karyawanData->tim : null;

            // dd($userItem); // Debugging: Periksa struktur data divisi)

            return (object) [
                'id' => $karyawanData ? $karyawanData->id : null, // karyawan.id untuk delete/edit
                'user_id' => $userItem->id,
                'nama' => $userItem->name,
                'email' => $userItem->email,
                'role' => $userItem->role,
                'divisi' => $userItem->divisi ? $userItem->divisi->divisi : '-',
                'divisi_id' => $userItem->divisi_id,
                'tim' => $timObject,
                'tim_id' => $karyawanData ? $karyawanData->tim_id : null,
                'alamat' => $userItem->alamat,
                'kontak' => $userItem->kontak,
                'foto' => $karyawanData ? $karyawanData->foto : $userItem->foto,
                'gaji' => $userItem->gaji,
                'kontrak_mulai' => $userItem->kontrak_mulai,
                'kontrak_selesai' => $userItem->kontrak_selesai,
                'status_kerja' => $karyawanData ? $karyawanData->status_kerja : ($userItem->status_kerja ?? 'aktif'),
                'status_karyawan' => $karyawanData ? $karyawanData->status_karyawan : ($userItem->status_karyawan ?? 'tetap'),
                'tunjanganTetapBulanIni' => $tetapList,
                'tunjanganTidakTetapBulanIni' => $tidakTetapList,
                'tunjangan_tetap_ids' => $tetapIds,
                'tunjangan_tidak_tetap_ids' => $tidakTetapIds,
            ];
        });
        
        $divisis = Divisi::orderBy('divisi', 'asc')->get();
        $tunjanganMaster = TunjanganMaster::orderBy('tipe')->orderBy('nama')->get();
        
        return view('hr.data_karyawan', compact('karyawan', 'divisis', 'tunjanganMaster'));
    }
    
    // Kode untuk role selain HR (general_manager, manager_divisi, dll)
    $query = Karyawan::with([
        'user',
        'user.divisi',
        'tim',
        'tunjanganTetapBulanIni',
        'tunjanganTidakTetapBulanIni'
    ]);
    
    if ($search = $request->query('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('alamat', 'like', "%{$search}%")
              ->orWhere('kontak', 'like', "%{$search}%")
              ->orWhereHas('user', function ($sq) use ($search) {
                  $sq->where('email', 'like', "%{$search}%")
                     ->orWhere('role', 'like', "%{$search}%");
              });
        });
    }
    
    if ($divisi = $request->query('divisi')) {
        $query->whereHas('user.divisi', function ($sq) use ($divisi) {
            $sq->where('divisi', $divisi);
        });
    }
    
    $karyawanCollection = $query->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->get();
    
    $karyawan = $karyawanCollection->map(function ($k) {
        $userData = $k->user;
        
        return (object) [
            'id' => $k->id,
            'user_id' => $k->user_id,
            'nama' => $k->nama,
            'email' => $userData?->email ?? $k->email ?? '',
            'role' => $userData?->role ?? 'karyawan',
            'divisi' => $userData?->divisi?->divisi ?? $k->divisi ?? '-',
            'divisi_id' => $userData?->divisi_id ?? $k->divisi_id,
            'tim' => $k->tim,
            'alamat' => $k->alamat,
            'kontak' => $k->kontak,
            'foto' => $k->foto,
            'gaji' => $k->gaji,
            'kontrak_mulai' => $k->kontrak_mulai,
            'kontrak_selesai' => $k->kontrak_selesai,
            'status_kerja' => $k->status_kerja,
            'status_karyawan' => $k->status_karyawan,
            'tunjangan_tetap_list' => $k->tunjanganTetapBulanIni ?? collect(),
            'tunjangan_tidak_tetap_list' => $k->tunjanganTidakTetapBulanIni ?? collect(),
            'tunjangan_tetap_ids' => ($k->tunjanganTetapBulanIni ?? collect())->pluck('id')->toArray(),
            'tunjangan_tidak_tetap_ids' => ($k->tunjanganTidakTetapBulanIni ?? collect())->pluck('id')->toArray(),
        ];
    });
    
    $divisis = Divisi::orderBy('divisi', 'asc')->get();
    $tunjanganMaster = TunjanganMaster::orderBy('tipe')->orderBy('nama')->get();
    
    return view('hr.data_karyawan', compact('karyawan', 'divisis', 'tunjanganMaster'));
}

    /**
     * Store karyawan baru (API untuk AJAX)
     */
    /**
 * Store karyawan baru (API untuk AJAX)
 */
public function storePegawai(Request $request)
{
    try {
        DB::beginTransaction();
        Log::info('storePegawai endpoint hit');

        
Log::info('storePegawai kontrak (raw)', [
            'kontrak_mulai' => $request->input('kontrak_mulai'),
            'kontrak_selesai' => $request->input('kontrak_selesai'),
        ]);
        Log::info('storePegawai kontrak (validated? akan dihitung setelah validate)');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|string',
            'divisi_id' => 'nullable|exists:divisis,id',
            'tim_id' => 'nullable|exists:tims,id',
            'gaji' => 'nullable|numeric',
            'kontrak_mulai' => 'nullable|date',
            'kontrak_selesai' => 'nullable|date|after_or_equal:kontrak_mulai',
            'kontak' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_kerja' => 'required|in:aktif,resign,phk',
            'status_karyawan' => 'required|in:tetap,kontrak,freelance',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tunjangan_tetap_ids' => 'nullable|string',
            'tunjangan_tidak_tetap_ids' => 'nullable|string'
        ]);
        
// Create user
        Log::info('storePegawai kontrak payload', [
            'kontrak_mulai' => $validated['kontrak_mulai'] ?? null,
            'kontrak_selesai' => $validated['kontrak_selesai'] ?? null,
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'divisi_id' => $validated['divisi_id'] ?? null,
            'tim_id' => $validated['tim_id'] ?? null,
            'gaji' => $validated['gaji'] ?? 0,
            'kontak' => $validated['kontak'] ?? '',
            'alamat' => $validated['alamat'] ?? '',
            'status_kerja' => $validated['status_kerja'],
            'status_karyawan' => $validated['status_karyawan'],
            'kontrak_mulai' => isset($validated['kontrak_mulai']) && $validated['kontrak_mulai'] ? Carbon::parse($validated['kontrak_mulai'])->format('Y-m-d') : null,
            'kontrak_selesai' => isset($validated['kontrak_selesai']) && $validated['kontrak_selesai'] ? Carbon::parse($validated['kontrak_selesai'])->format('Y-m-d') : null
        ]);

        // Reload untuk pastikan value benar-benar tersimpan di tabel users
        $user->refresh();
        Log::info('storePegawai users saved kontrak', [
            'user_id' => $user->id,
            'kontrak_mulai' => $user->kontrak_mulai,
            'kontrak_selesai' => $user->kontrak_selesai,
        ]);

        // Debug: cek nilai di DB paling akhir
        $userDb = User::select('kontrak_mulai','kontrak_selesai')->find($user->id);
        Log::info('storePegawai users DB kontrak', [
            'user_id' => $user->id,
            'kontrak_mulai' => $userDb?->kontrak_mulai,
            'kontrak_selesai' => $userDb?->kontrak_selesai,
        ]);


        
        // Handle foto
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto-karyawan', 'public');
            $user->foto = $fotoPath;
            $user->save();
        }
        
        // Handle tunjangan - Cari atau buat karyawan
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        
        if (!$karyawan) {
            // Jika karyawan tidak ditemukan, buat baru
            $divisiName = $user->divisi ? $user->divisi->divisi : null;
            $karyawan = Karyawan::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'divisi' => $divisiName,
                'divisi_id' => $user->divisi_id,
                'tim_id' => $user->tim_id,
                'gaji' => $user->gaji,
                'alamat' => $user->alamat,
                'kontak' => $user->kontak,
                'foto' => $user->foto,
                'status_kerja' => $user->status_kerja,
                'status_karyawan' => $user->status_karyawan,
            ]);
        }
        
        if ($karyawan) {
            $this->syncTunjanganKaryawan($karyawan->id, $request, false);
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil ditambahkan'
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error storing karyawan: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update karyawan (API untuk AJAX)
     */
   /**
 * Update karyawan (API untuk AJAX)
 */
/**
 * Update karyawan (API untuk AJAX)
 */
public function updatePegawai(Request $request, $id)
{
    try {
        DB::beginTransaction();
        Log::info('updatePegawai endpoint hit', ['id' => $id]);

        $user = User::findOrFail($id);

        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'required|string',
            'divisi_id' => 'nullable|exists:divisis,id',
            'tim_id' => 'nullable|exists:tims,id',
            'gaji' => 'nullable|numeric',
            'kontrak_mulai' => 'nullable|date',
            'kontrak_selesai' => 'nullable|date|after_or_equal:kontrak_mulai',
            'kontak' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_kerja' => 'required|in:aktif,resign,phk',
            'status_karyawan' => 'required|in:tetap,kontrak,freelance',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tunjangan_tetap_ids' => 'nullable|string',
            'tunjangan_tidak_tetap_ids' => 'nullable|string'
        ]);
        
        // Update user data
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->divisi_id = $validated['divisi_id'] ?? null;
        $user->tim_id = $validated['tim_id'] ?? null;
        $user->gaji = $validated['gaji'] ?? 0;
// Normalisasi agar tetap tersimpan sebagai date (YYYY-MM-DD) meski format dari frontend beragam
        $user->kontrak_mulai = !empty($validated['kontrak_mulai']) ? Carbon::parse($validated['kontrak_mulai'])->format('Y-m-d') : null;
        $user->kontrak_selesai = !empty($validated['kontrak_selesai']) ? Carbon::parse($validated['kontrak_selesai'])->format('Y-m-d') : null;

        $user->kontak = $validated['kontak'] ?? '';
        $user->alamat = $validated['alamat'] ?? '';
        $user->status_kerja = $validated['status_kerja'];
        $user->status_karyawan = $validated['status_karyawan'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        // Handle foto
        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $fotoPath = $request->file('foto')->store('foto-karyawan', 'public');
            $user->foto = $fotoPath;
        }
        
        $user->save();

        // Debug: cek nilai setelah save di DB
        $userDb = User::select('kontrak_mulai','kontrak_selesai')->find($user->id);
        Log::info('updatePegawai users DB kontrak', [
            'user_id' => $user->id,
            'kontrak_mulai' => $userDb?->kontrak_mulai,
            'kontrak_selesai' => $userDb?->kontrak_selesai,
        ]);

        // Debug: cek nilai di tabel karyawan juga
        $karyawanDb = Karyawan::where('user_id', $user->id)->first();
        Log::info('updatePegawai karyawan DB kontrak', [
            'user_id' => $user->id,
            'karyawan_kontrak_mulai' => $karyawanDb?->kontrak_mulai,
            'karyawan_kontrak_selesai' => $karyawanDb?->kontrak_selesai,
        ]);

        // Sync tunjangan - Cari atau buat karyawan

        $karyawan = Karyawan::where('user_id', $user->id)->first();
        
        if (!$karyawan) {
            // Jika karyawan tidak ditemukan, buat baru
            $divisiName = $user->divisi ? $user->divisi->divisi : null;
            $karyawan = Karyawan::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'divisi' => $divisiName,
                'divisi_id' => $user->divisi_id,
                'tim_id' => $user->tim_id,
                'gaji' => $user->gaji,
                'alamat' => $user->alamat,
                'kontak' => $user->kontak,
                'foto' => $user->foto,
                'status_kerja' => $user->status_kerja,
                'status_karyawan' => $user->status_karyawan,
            ]);
        }
        
        if ($karyawan) {
            $this->syncTunjanganKaryawan($karyawan->id, $request, true);
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil diupdate'
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating karyawan: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate karyawan: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Delete karyawan (API untuk AJAX)
     */
    public function destroyPegawai($id)
    {
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($id);
            
            // Hapus foto jika ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            
            // Hapus tunjangan karyawan
$karyawan = Karyawan::where('user_id', $user->id)->first();
if ($karyawan) {
    TunjanganKaryawan::where('karyawan_id', $karyawan->id)->delete();
}            
            // Hapus user
            $user->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting karyawan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    //**
//  * Helper sync tunjangan karyawan sesuai struktur database
//  * 
//  * @param int $karyawanId ID dari tabel karyawan (bukan user_id)
//  * @param Request $request
//  * @param bool $deleteOld Hapus data lama untuk bulan ini
//  */
private function syncTunjanganKaryawan($karyawanId, Request $request, $deleteOld = false)
{
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;
    
    if ($deleteOld) {
        // Hapus tunjangan untuk bulan ini saja (bukan semua data)
        TunjanganKaryawan::where('karyawan_id', $karyawanId)
            ->where('bulan', $currentMonth)
            ->where('tahun', $currentYear)
            ->delete();
    }
    
    // Handle tunjangan tetap
    $tetapIds = [];
    if ($request->filled('tunjangan_tetap_ids')) {
        $tetapIds = json_decode($request->tunjangan_tetap_ids, true);
        if (!is_array($tetapIds)) $tetapIds = [];
    }
    
    foreach ($tetapIds as $tunjanganId) {
        $tunjanganMaster = TunjanganMaster::find($tunjanganId);
        if ($tunjanganMaster) {
            // PASTIKAN SEMUA FIELD DIISI
            TunjanganKaryawan::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId,
                    'tunjangan_id' => $tunjanganId,
                    'bulan' => $currentMonth,
                    'tahun' => $currentYear,
                ],
                [
                    'nominal' => $tunjanganMaster->nominal,
                    'diberikan' => 1,
                ]
            );
        }
    }
    
    // Handle tunjangan tidak tetap
    $tidakTetapIds = [];
    if ($request->filled('tunjangan_tidak_tetap_ids')) {
        $tidakTetapIds = json_decode($request->tunjangan_tidak_tetap_ids, true);
        if (!is_array($tidakTetapIds)) $tidakTetapIds = [];
    }
    
    foreach ($tidakTetapIds as $tunjanganId) {
        $tunjanganMaster = TunjanganMaster::find($tunjanganId);
        if ($tunjanganMaster) {
            // PASTIKAN SEMUA FIELD DIISI
            TunjanganKaryawan::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId,
                    'tunjangan_id' => $tunjanganId,
                    'bulan' => $currentMonth,
                    'tahun' => $currentYear,
                ],
                [
                    'nominal' => $tunjanganMaster->nominal,
                    'diberikan' => 1,
                ]
            );
        }
    }
}
    /**
     * API untuk template gaji
     */
    public function getGajiTemplate(Request $request)
    {
        $role = $request->get('role');
        $divisiId = $request->get('divisi_id');
        
        $defaultGaji = [
            'general_manager' => 15000000,
            'manager_divisi' => 10000000,
            'finance' => 8000000,
            'hr' => 7000000,
            'karyawan' => 5000000
        ];
        
        $gajiPokok = $defaultGaji[$role] ?? null;
        
        return response()->json([
            'success' => true,
            'data' => ['gaji_pokok' => $gajiPokok]
        ]);
    }

    /**
     * API untuk mendapatkan tunjangan karyawan
     */
    public function getTunjanganKaryawan($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            $tetap = $user->tunjanganTetap->map(function($item) {
                return ['id' => $item->id, 'nama' => $item->nama, 'nominal' => $item->pivot->nominal];
            });
            
            $tidakTetap = $user->tunjanganTidakTetap->map(function($item) {
                return ['id' => $item->id, 'nama' => $item->nama, 'nominal' => $item->pivot->nominal];
            });
            
            return response()->json([
                'success' => true,
                'data' => ['tetap' => $tetap, 'tidak_tetap' => $tidakTetap]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tunjangan'
            ], 500);
        }
    }

    /**
     * Helper method untuk cek apakah user sedang cuti hari ini
     */
    private function checkIfOnLeaveToday($userId)
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        
        $cuti = Cuti::where('user_id', $userId)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();
        
        if ($cuti) {
            return [
                'on_leave' => true,
                'details' => $cuti
            ];
        }
        
        return [
            'on_leave' => false,
            'details' => null
        ];
    }

    private function isAttendanceLate(?Absensi $absen): bool
    {
        if (!$absen || !$absen->jam_masuk) {
            return false;
        }

        // Pakai nilai mentah database agar tidak dipengaruhi accessor model.
        $rawLateMinutes = $absen->getRawOriginal('late_minutes');
        if (is_numeric($rawLateMinutes) && (int) $rawLateMinutes > 0) {
            return true;
        }

        $operational = Setting::getValue('operational_hours', []);
        $lateLimitTime = is_array($operational)
            ? ($operational['late_limit_time']
                ?? sprintf(
                    '%02d:%02d',
                    (int) ($operational['late_limit_hour'] ?? 9),
                    (int) ($operational['late_limit_minute'] ?? 5)
                ))
            : '09:05';

        [$lateHour, $lateMinute] = array_map('intval', explode(':', $lateLimitTime));
        $lateLimitSeconds = ($lateHour * 3600) + ($lateMinute * 60);

        try {
            $jamMasuk = $absen->jam_masuk instanceof Carbon
                ? $absen->jam_masuk->copy()->setTimezone('Asia/Jakarta')
                : Carbon::parse((string) $absen->jam_masuk, 'Asia/Jakarta');
        } catch (\Exception $e) {
            return false;
        }

        $jamMasukSeconds = ($jamMasuk->hour * 3600) + ($jamMasuk->minute * 60) + $jamMasuk->second;
        return $jamMasukSeconds > $lateLimitSeconds;
    }

    /**
     * Menampilkan halaman beranda karyawan.
     */
    public function home()
    {
        $userId = Auth::id();
        $today = now('Asia/Jakarta')->toDateString();
        $user = Auth::user()->load('divisi');
        $userRole = $user->role; 
        
        // Determine divisi id and name
        $userDivisiId = $user->divisi_id ?: null;
        $userDivisi = trim((string) (optional($user->divisi)->divisi ?? '')) ?: null;
        $karyawanData = Karyawan::where('user_id', $userId)->first();

        // Fallback nama divisi dari tabel karyawan bila relasi users.divisi kosong/tidak sinkron
        if (!$userDivisi && $karyawanData && !empty($karyawanData->divisi)) {
            $karyawanDivisiRaw = trim((string) $karyawanData->divisi);

            if (is_numeric($karyawanDivisiRaw)) {
                $fallbackDivisi = Divisi::find((int) $karyawanDivisiRaw);
                if ($fallbackDivisi) {
                    $userDivisi = $fallbackDivisi->divisi;
                    $userDivisiId = $userDivisiId ?: $fallbackDivisi->id;
                }
            } else {
                $userDivisi = $karyawanDivisiRaw;
                if (!$userDivisiId) {
                    $fallbackDivisi = Divisi::where('divisi', $karyawanDivisiRaw)->first();
                    if ($fallbackDivisi) {
                        $userDivisiId = $fallbackDivisi->id;
                    }
                }
            }
        }

        // Jika id ada tapi nama masih kosong, resolve ulang dari tabel divisi
        if (!$userDivisi && $userDivisiId) {
            $userDivisi = optional(Divisi::find($userDivisiId))->divisi;
        }

        // 1. Ambil status absensi hari ini
        $absenToday = Absensi::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        $attendanceStatus = 'Belum Absen';
        if ($absenToday) {
            if ($absenToday->jam_masuk) {
                $attendanceStatus = $this->isAttendanceLate($absenToday) ? 'Terlambat' : 'Tepat Waktu';
            } elseif ($absenToday->jenis_ketidakhadiran) {
                $attendanceStatus = match($absenToday->jenis_ketidakhadiran) {
                    'cuti' => 'Cuti',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'dinas-luar' => 'Dinas Luar',
                    'lainnya' => 'Tidak Hadir',
                    default => 'Tidak Hadir',
                };
            }
        }

        // Hitung Jumlah Ketidakhadiran
        $ketidakhadiranCount = Absensi::where('user_id', $userId)
                                ->where('approval_status', 'approved')
                                ->whereIn('jenis_ketidakhadiran', ['cuti', 'sakit', 'izin'])
                                ->count();

        // Hitung Jumlah Tugas
        $tugasCount = Task::where(function ($query) use ($userId, $userDivisiId) {
            $query->where('assigned_to', $userId)
                  ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
            if (Schema::hasColumn('tasks', 'target_type') && $userDivisiId) {
                $query->orWhere(function ($q) use ($userDivisiId) {
                    $q->where('target_type', 'divisi');
                    if (Schema::hasColumn('tasks', 'target_divisi_id')) $q->where('target_divisi_id', $userDivisiId);
                    elseif (Schema::hasColumn('tasks', 'target_id')) $q->where('target_id', $userDivisiId);
                });
            } else {
                if ($userDivisiId && Schema::hasColumn('tasks', 'divisi')) {
                    $query->orWhere('divisi', $userDivisiId);
                }
            }
        })
        ->whereNotIn('status', ['selesai', 'dibatalkan'])
        ->count();

        // Cek apakah karyawan menjadi penanggung jawab project
        $penanggungProjectQuery = Project::assignedToKaryawan($userId);
        $penanggungProjectCount = (clone $penanggungProjectQuery)->count();
        $penanggungProjectAktifCount = (clone $penanggungProjectQuery)
            ->where('status_kerjasama', 'aktif')
            ->count();
        $penanggungProjectBerjalanCount = (clone $penanggungProjectQuery)
            ->whereIn('status_pengerjaan', ['pending', 'dalam_pengerjaan'])
            ->count();
        $penanggungProjectsPreview = (clone $penanggungProjectQuery)
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get([
                'id',
                'nama',
                'status_pengerjaan',
                'status_kerjasama',
                'progres',
            ]);

        $roleBasedData = [];

        if ($userRole === 'general_manager') {
            $roleBasedData['totalKaryawan'] = Karyawan::count();
            $roleBasedData['totalDivisi'] = Karyawan::distinct('divisi')->count('divisi');

            $countPendingManual = Absensi::where('approval_status', 'pending')
                ->whereNotNull('jenis_ketidakhadiran')
                ->count();

            $countPendingCuti = 0;
            if (Schema::hasTable('cutis')) {
                 $queryCuti = \App\Models\Cuti::query();
                 if (Schema::hasColumn('cutis', 'status')) {
                     $queryCuti->where('status', 'pending');
                 } elseif (Schema::hasColumn('cutis', 'status_pengajuan')) {
                     $queryCuti->where('status_pengajuan', 'pending');
                 }
                 $countPendingCuti = $queryCuti->count();
            }

            $roleBasedData['pendingApprovals'] = $countPendingManual + $countPendingCuti;

        } elseif ($userRole === 'manager_divisi') {
            if ($userDivisiId) {
                $roleBasedData['teamMembers'] = User::where('divisi_id', $userDivisiId)->count();
                $roleBasedData['teamPendingApprovals'] = Absensi::whereIn('user_id', function($query) use ($userDivisiId) {
                    $query->select('id')
                          ->from('users')
                          ->where('divisi_id', $userDivisiId);
                })
                ->where('approval_status', 'pending')
                ->count();
            } else {
                $roleBasedData['teamMembers'] = 0;
                $roleBasedData['teamPendingApprovals'] = 0;
            }
        }

        // Return appropriate view based on role
        if ($userRole === 'hr') {
            return view('hr.home', [
                'attendance_status' => $attendanceStatus,
                'ketidakhadiran_count' => $ketidakhadiranCount,
                'tugas_count' => $tugasCount,
                'user_role' => $userRole,
                'user_divisi' => $userDivisi,
                'user_divisi_id' => $userDivisiId,
                'role_based_data' => $roleBasedData,
            ]);
        }

        $announcements = \App\Models\Pengumuman::latest()->take(5)->get();
        $meetingNotes = \App\Models\CatatanRapat::latest()->take(5)->get();

        $highlightedDates = \App\Models\CatatanRapat::whereHas('penugasan', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->select('tanggal')
            ->distinct()
            ->get()
            ->pluck('tanggal')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $announcementDates = \App\Models\Pengumuman::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->pluck('date')
            ->toArray();
            
        $totalHadir = \App\Models\Absensi::where('user_id', $userId)
            ->whereNotNull('jam_masuk')
            ->where('approval_status', 'approved')
            ->count();

        $totalTerlambat = \App\Models\Absensi::where('user_id', $userId)
            ->whereNotNull('jam_masuk')
            ->where('approval_status', 'approved')
            ->get()
            ->filter(function ($record) {
                return $record->is_terlambat;
            })
            ->count();

        $totalIzin = \App\Models\Absensi::where('user_id', $userId)
            ->where('jenis_ketidakhadiran', 'izin')
            ->where('approval_status', 'approved')
            ->count();

        $totalSakit = \App\Models\Absensi::where('user_id', $userId)
            ->where('jenis_ketidakhadiran', 'sakit')
            ->where('approval_status', 'approved')
            ->count();

        $totalCuti = \App\Models\Cuti::where('user_id', $userId)
            ->where('status', 'approved')
            ->get()
            ->sum(function ($cuti) {
                return \Carbon\Carbon::parse($cuti->tanggal_mulai)
                    ->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1;
            });

        $tugasCount = \App\Models\Task::where(function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
            })
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->count();

            // NOTE: KPA tables may be split into new structure tables.
            // Avoid hard-failing when legacy table `kpa` is referenced elsewhere.
            return view('karyawan.home', [

            'attendance_status' => $attendanceStatus,
            'ketidakhadiran_count' => $ketidakhadiranCount,
            'total_hadir' => $totalHadir,
            'total_terlambat' => $totalTerlambat,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_cuti' => $totalCuti,
            'tugas_count' => $tugasCount,
            'announcements' => $announcements,
            'meeting_notes' => $meetingNotes,
            'highlighted_dates' => $highlightedDates,
            'announcement_dates' => $announcementDates,
            'penanggung_project_count' => $penanggungProjectCount,
            'penanggung_project_aktif_count' => $penanggungProjectAktifCount,
            'penanggung_project_berjalan_count' => $penanggungProjectBerjalanCount,
            'penanggung_projects_preview' => $penanggungProjectsPreview,
            'user_role' => $userRole,
            'user_divisi' => $userDivisi,
            'user_divisi_id' => $userDivisiId,
            'role_based_data' => $roleBasedData,
        ]);
    }

    /**
     * Menampilkan halaman absensi karyawan.
     */
    public function absensiPage(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Cek absensi hari ini
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $riwayatAbsensi = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Hitung statistik
        $totalHadir = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->whereNotNull('jam_masuk')
            ->where('approval_status', 'approved')
            ->count();

        $totalIzin = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'izin')
            ->where('approval_status', 'approved')
            ->count();

        $totalSakit = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'sakit')
            ->where('approval_status', 'approved')
            ->count();

        $totalCuti = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'cuti')
            ->where('approval_status', 'approved')
            ->count();

        $totalDinasLuar = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'dinas-luar')
            ->where('approval_status', 'approved')
            ->count();

        // Return appropriate view based on user role
        if ($user->role === 'hr') {
            $formattedAbsensi = collect();
            $ketidakhadiran = collect();
            $cuti = collect();
            $totalKaryawan = 0;
            $hadiranCount = 0;
            $sakitCount = 0;
            $izinCount = 0;
            $cutiCount = 0;
            $tidakHadirCount = 0;
            $hadiranUserIds = [];
            $sakit_UserIds = [];
            $izin_UserIds = [];
            $cutiUserIds = [];

            $todayStartUtc = Carbon::today('Asia/Jakarta')->startOfDay()->utc();
            $todayEndUtc = Carbon::today('Asia/Jakarta')->endOfDay()->utc();

            try {
                $users = User::where('role', 'karyawan')->get();
                $userIds = $users->pluck('id')->toArray();
                $totalKaryawan = count($userIds);

                $attendances = Absensi::with('user')
                    ->whereIn('user_id', $userIds)
                    ->whereBetween('tanggal', [$todayStartUtc, $todayEndUtc])
                    ->whereNotNull('jam_masuk')
                    ->orderBy('jam_masuk', 'desc')
                    ->get();

                $formattedAbsensi = collect();
                $attendanceUserIds = [];
                
                foreach ($attendances as $absen) {
                    $attendanceUserIds[] = $absen->user_id;

                    $jamMasukFormatted = null;
                    if (!empty($absen->jam_masuk)) {
                        try {
                            $jamMasukFormatted = Carbon::parse((string) $absen->jam_masuk)->format('H:i');
                        } catch (\Exception $e) {
                            $jamMasukFormatted = substr((string) $absen->jam_masuk, 0, 5);
                        }
                    }

                    $jamPulangFormatted = null;
                    if (!empty($absen->jam_pulang)) {
                        try {
                            $jamPulangFormatted = Carbon::parse((string) $absen->jam_pulang)->format('H:i');
                        } catch (\Exception $e) {
                            $jamPulangFormatted = substr((string) $absen->jam_pulang, 0, 5);
                        }
                    }

                    $keteranganValue = $absen->keterangan ?? $absen->reason ?? null;
                    if (empty($keteranganValue) && !empty($absen->is_early_checkout) && !empty($absen->early_checkout_reason)) {
                        $keteranganValue = $absen->early_checkout_reason;
                    }
                    
                    $rawLateMinutes = $absen->getRawOriginal('late_minutes');
                    $isTerlambat = (is_numeric($rawLateMinutes) && (int) $rawLateMinutes > 0)
                        ? true
                        : $this->isAttendanceLate($absen);
                    $statusLabel = $isTerlambat ? 'Terlambat' : 'Tepat Waktu';
                    $statusClass = $isTerlambat ? 'status-terlambat' : 'status-tepat-waktu';

                    $formattedAbsensi->push([
                        'id' => $absen->id,
                        'user_id' => $absen->user_id,
                        'user_name' => $absen->user ? $absen->user->name : '-',
                        'tanggal' => $absen->tanggal,
                        'jam_masuk' => $jamMasukFormatted,
                        'jam_pulang' => $jamPulangFormatted,
                        'late_minutes' => is_numeric($rawLateMinutes) ? (int) $rawLateMinutes : 0,
                        'jenis_ketidakhadiran' => $absen->jenis_ketidakhadiran,
                        'keterangan' => $keteranganValue,
                        'approval_status' => $absen->approval_status ?? null,
                        'status_kehadiran' => $statusLabel,
                        'status_class' => $statusClass,
                        'is_terlambat' => $isTerlambat,
                        'attendance' => $absen,
                    ]);
                }

                $ketidakhadiranRaw = Absensi::with('user')
                    ->whereIn('user_id', $userIds)
                    ->whereBetween('tanggal', [$todayStartUtc, $todayEndUtc])
                    ->whereIn('jenis_ketidakhadiran', ['sakit', 'izin'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $ketidakhadiran = collect();
                $sakit_UserIds = [];
                $izin_UserIds = [];
                
                foreach ($ketidakhadiranRaw as $item) {
                    $ketidakhadiran->push([
                        'id' => $item->id,
                        'user_id' => $item->user_id,
                        'user' => $item->user ? [
                            'id' => $item->user->id,
                            'name' => $item->user->name,
                        ] : null,
                        'tanggal' => $item->tanggal,
                        'tanggal_akhir' => $item->tanggal_akhir ?? null,
                        'jenis_ketidakhadiran' => $item->jenis_ketidakhadiran,
                        'keterangan' => $item->reason ?? $item->keterangan ?? null,
                        'approval_status' => $item->approval_status ?? 'pending',
                    ]);
                    
                    if ($item->jenis_ketidakhadiran === 'sakit') {
                        $sakit_UserIds[] = $item->user_id;
                    } elseif ($item->jenis_ketidakhadiran === 'izin') {
                        $izin_UserIds[] = $item->user_id;
                    }
                }
                
                $sakit_UserIds = array_unique($sakit_UserIds);
                $izin_UserIds = array_unique($izin_UserIds);

                $cutiRaw = Cuti::with(['user', 'user.divisionDetail'])
                    ->whereIn('user_id', $userIds)
                    ->whereDate('tanggal_mulai', '<=', $endOfMonth)
                    ->whereDate('tanggal_selesai', '>=', $startOfMonth)
                    ->whereIn('status', ['disetujui', 'menunggu', 'pending'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $cuti = collect();
                $cutiUserIds = [];
                
                foreach ($cutiRaw as $item) {
                    $cuti->push([
                        'id' => $item->id,
                        'user_id' => $item->user_id,
                        'user' => $item->user ? [
                            'id' => $item->user->id,
                            'name' => $item->user->name,
                            'divisionDetail' => $item->user->divisionDetail ? [
                                'divisi' => $item->user->divisionDetail->divisi,
                            ] : null,
                        ] : null,
                        'tanggal_mulai' => $item->tanggal_mulai,
                        'tanggal_selesai' => $item->tanggal_selesai,
                        'keterangan' => $item->keterangan ?? null,
                        'durasi' => $item->durasi ?? null,
                        'jenis_cuti' => $item->jenis_cuti ?? null,
                        'status' => $item->status ?? 'menunggu',
                    ]);
                    
                    $cutiUserIds[] = $item->user_id;
                }
                
                $cutiUserIds = array_unique($cutiUserIds);
                
                $hadiranUserIds = array_unique($attendanceUserIds);
                $hadiranCount = count($hadiranUserIds);
                $sakitCount = count($sakit_UserIds);
                $izinCount = count($izin_UserIds);
                $cutiCount = count($cutiUserIds);
                
                $allTrackedUserIds = array_unique(array_merge($hadiranUserIds, $sakit_UserIds, $izin_UserIds, $cutiUserIds));
                $tidakHadirCount = $totalKaryawan - count($allTrackedUserIds);

            } catch (\Exception $e) {
                $formattedAbsensi = collect();
                $ketidakhadiran = collect();
                $cuti = collect();
            }

            return view('hr.kelola_absensi', [
                'on_leave' => false,
                'cuti_details' => null,
                'absensiHariIni' => $absensiHariIni,
                'riwayatAbsensi' => $riwayatAbsensi,
                'totalHadir' => $totalHadir,
                'totalIzin' => $totalIzin,
                'totalSakit' => $totalSakit,
                'totalCuti' => $totalCuti,
                'totalDinasLuar' => $totalDinasLuar,
                'formattedAbsensi' => $formattedAbsensi,
                'ketidakhadiran' => $ketidakhadiran,
                'cuti' => $cuti,
                'totalKaryawan' => $totalKaryawan,
                'hadiranCount' => $hadiranCount,
                'sakitCount' => $sakitCount,
                'izinCount' => $izinCount,
                'cutiCount' => $cutiCount,
                'tidakHadirCount' => $tidakHadirCount,
                'presentIds' => $hadiranUserIds ?? [],
                'sakitIds' => $sakit_UserIds ?? [],
                'izinIds' => $izin_UserIds ?? [],
                'cutiIds' => $cutiUserIds ?? [],
                'allUsers' => isset($users) ? $users->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                    ];
                })->values() : collect(),
            ]);
        }

        return view('karyawan.absen', [
            'on_leave' => false,
            'cuti_details' => null,
            'absensiHariIni' => $absensiHariIni,
            'riwayatAbsensi' => $riwayatAbsensi,
            'totalHadir' => $totalHadir,
            'totalIzin' => $totalIzin,
            'totalSakit' => $totalSakit,
            'totalCuti' => $totalCuti,
            'totalDinasLuar' => $totalDinasLuar,
        ]);
    }

    /**
     * Menampilkan halaman daftar TUGAS karyawan (Web View).
     */
    public function listPage()
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();
            $userDivisiId = null;
            $userDivisi = null;
            if ($user->divisi_id) {
                $userDivisiId = $user->divisi_id;
                $userDivisi = optional($user->divisi)->divisi ?? null;
            } else {
                $karyawan = Karyawan::where('user_id', $userId)->first();
                $userDivisi = $karyawan ? $karyawan->divisi : null;
                if ($userDivisi) {
                    $divModel = Divisi::where('divisi', $userDivisi)->first();
                    if ($divModel) $userDivisiId = $divModel->id;
                }
            }
            
            $userName = $user->name;

            $tasks = Task::where(function ($query) use ($userId, $userDivisiId) {
                $query->where('assigned_to', $userId);
                if (Schema::hasColumn('tasks', 'target_type') && $userDivisiId) {
                    $query->orWhere(function ($q) use ($userDivisiId) {
                        $q->where('target_type', 'divisi');
                        if (Schema::hasColumn('tasks', 'target_divisi_id')) $q->where('target_divisi_id', $userDivisiId);
                        elseif (Schema::hasColumn('tasks', 'target_id')) $q->where('target_id', $userDivisiId);
                    });
                } elseif ($userDivisiId && Schema::hasColumn('tasks', 'divisi')) {
                     $query->orWhere('divisi', $userDivisiId);
                }
            })
            ->with(['creator:id,name', 'assignee:id,name', 'targetManager:id,name'])
            ->orderBy('deadline', 'asc')
            ->get();

            $tasks->transform(function ($task) {
                if ($task->assignee) {
                    $karyawanInfo = Karyawan::where('user_id', $task->assignee->id)->first(['divisi']);
                    $task->assignee_divisi = $karyawanInfo ? $karyawanInfo->divisi : null;
                } else {
                    $task->assignee_divisi = null;
                }
                return $task;
            });

            return view('karyawan.list', compact('tasks'));

        } catch (\Exception $e) {
            Log::error('CRITICAL ERROR in KaryawanController@listPage: ' . $e->getMessage());
            return view('karyawan.list', [
                'tasks' => collect([]),
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menampilkan halaman daftar ABSENSI karyawan.
     */
    public function absensiListPage()
    {
        $userId = Auth::id();

        $absensis = Absensi::where('user_id', $userId)
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('karyawan.absensi_list', compact('absensis'));
    }

    /**
     * Menampilkan halaman detail absensi karyawan.
     */
    public function detailPage($id)
    {
        $absensi = Absensi::findOrFail($id);

        if ($absensi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('karyawan.detail', compact('absensi'));
    }

    // =================================================================
    // API UNTUK HALAMAN ABSENSI (FRONTEND JAVASCRIPT)
    // =================================================================

    public function getTodayStatusApi()
    {
        try {
            $userId = Auth::id();
            $today = now('Asia/Jakarta')->toDateString();
            
            $absen = Absensi::where('user_id', $userId)
                ->whereDate('tanggal', $today)
                ->first();

            $data = [
                'jam_masuk' => null,
                'jam_pulang' => null,
                'status' => 'Belum Absen',
                'late_minutes' => 0,
                'approval_status' => 'approved',
                'is_on_leave' => false,
                'jenis_ketidakhadiran' => null,
                'jenis_ketidakhadiran_label' => null,
                'keterangan' => null
            ];

            if ($absen) {
                $data['jam_masuk'] = $absen->jam_masuk;
                $data['jam_pulang'] = $absen->jam_pulang;
                $data['approval_status'] = $absen->approval_status;
                $data['jenis_ketidakhadiran'] = $absen->jenis_ketidakhadiran;
                $data['jenis_ketidakhadiran_label'] = $absen->jenis_ketidakhadiran ? $absen->jenis_ketidakhadiran_label : null;
                $data['keterangan'] = $absen->keterangan;

                if ($absen->jam_masuk) {
                    $rawLateMin = $absen->getRawOriginal('late_minutes');
                    $isLate = $this->isAttendanceLate($absen);
                    $data['status'] = $isLate ? 'Terlambat' : 'Tepat Waktu';
                    $data['late_minutes'] = is_numeric($rawLateMin) ? (int) $rawLateMin : 0;
                    $data['is_terlambat'] = $isLate;
                } elseif ($absen->jenis_ketidakhadiran) {
                    $data['status'] = match($absen->jenis_ketidakhadiran) {
                        'cuti' => 'Cuti',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'dinas-luar' => 'Dinas Luar',
                        'lainnya' => 'Lainnya',
                        default => 'Lainnya',
                    };
                }
            }

            $leaveStatus = $this->checkIfOnLeaveToday(Auth::id());
            if ($leaveStatus['on_leave']) {
                $data['is_on_leave'] = true;
                $data['leave_type'] = $leaveStatus['details']->tipe_cuti;
                $data['leave_reason'] = $leaveStatus['details']->alasan;
                $data['leave_dates'] = [
                    'start' => $leaveStatus['details']->tanggal_mulai,
                    'end' => $leaveStatus['details']->tanggal_selesai
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Today Status API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load today status',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        $query = Absensi::where('user_id', Auth::id());
        
        $filterType = $request->get('filter', 'month');
        $month = $request->get('month');
        $year = $request->get('year');

        if ($filterType === 'custom' && $month && $year) {
            $query->whereMonth('tanggal', '=', $month)
                  ->whereYear('tanggal', '=', $year);
        } elseif ($filterType === 'week') {
            $query->whereBetween('tanggal', [
                Carbon::now()->startOfWeek()->toDateString(),
                Carbon::now()->endOfWeek()->toDateString()
            ]);
        } elseif ($filterType === 'year') {
            $query->whereYear('tanggal', '=', date('Y'));
        } else {
            $query->whereMonth('tanggal', '=', date('m'))
                  ->whereYear('tanggal', '=', date('Y'));
        }

        $history = $query->orderBy('tanggal', 'desc')->get();

        $formattedData = $history->map(function ($item) {
            $status = 'Tidak Hadir';
            $lateMinutes = 0;

            if ($item->jam_masuk) {
                $lateMinutes = $item->late_minutes !== null ? $item->late_minutes : 0;
                $status = $lateMinutes > 0 ? 'Terlambat' : 'Tepat Waktu';
            } elseif ($item->jenis_ketidakhadiran) {
                $status = match($item->jenis_ketidakhadiran) {
                    'cuti' => 'Cuti',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'dinas-luar' => 'Dinas Luar',
                    default => 'Tidak Hadir',
                };
            }

            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'date' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                'jam_masuk' => $item->jam_masuk,
                'jam_pulang' => $item->jam_pulang,
                'status' => $status,
                'lateMinutes' => $lateMinutes,
                'is_early_checkout' => $item->is_early_checkout,
                'early_checkout_reason' => $item->early_checkout_reason,
                'approval_status' => $item->approval_status,
                'reason' => $item->reason,
                'location' => $item->location,
                'purpose' => $item->purpose,
                'jenis_ketidakhadiran' => $item->jenis_ketidakhadiran,
                'keterangan' => $item->keterangan,
                'is_on_leave' => false
            ];
        })->all();

        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);
    }

    public function getDashboardData()
    {
        try {
            $userId = Auth::id();
            $today = now()->toDateString();

            $absenToday = Absensi::where('user_id', $userId)
                ->where('tanggal', $today)
                ->first();

            $attendanceStatus = 'Belum Absen';
            if ($absenToday) {
                if ($absenToday->jam_masuk) {
                    $attendanceStatus = $this->isAttendanceLate($absenToday) ? 'Terlambat' : 'Tepat Waktu';
                } elseif ($absenToday->jenis_ketidakhadiran) {
                    $attendanceStatus = match($absenToday->jenis_ketidakhadiran) {
                        'cuti' => 'Cuti',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'dinas-luar' => 'Dinas Luar',
                        'lainnya' => 'Tidak Hadir',
                        default => 'Tidak Hadir',
                    };
                }
            }

            $absenHariIni = Absensi::where('user_id', $userId)
                ->where('tanggal', $today)
                ->first();

            $totalHadir = Absensi::where('user_id', $userId)
                ->whereNotNull('jam_masuk')
                ->where('approval_status', 'approved')
                ->count();

            $totalTerlambat = Absensi::where('user_id', $userId)
                ->whereNotNull('jam_masuk')
                ->where('approval_status', 'approved')
                ->get()
                ->filter(function ($record) {
                    return $record->is_terlambat;
                })
                ->count();

            $totalIzin = Absensi::where('user_id', $userId)
                ->where('jenis_ketidakhadiran', 'izin')
                ->where('approval_status', 'approved')
                ->count();

            $totalSakit = Absensi::where('user_id', $userId)
                ->where('jenis_ketidakhadiran', 'sakit')
                ->where('approval_status', 'approved')
                ->count();

            $totalAbsen = Absensi::where('user_id', $userId)
                ->where('jenis_ketidakhadiran', 'lainnya')
                ->where('approval_status', 'approved')
                ->count();

            $totalCuti = Cuti::where('user_id', $userId)
                ->where('status', 'approved')
                ->get()
                ->sum(function ($cuti) {
                    return Carbon::parse($cuti->tanggal_mulai)
                        ->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
                });

            $tugasCount = Task::where(function($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
                })
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();

            return response()->json([
                'attendance_status' => $attendanceStatus,
                'attendance_today' => $absenHariIni ? [
                    'jam_masuk' => $absenHariIni->jam_masuk,
                    'jam_pulang' => $absenHariIni->jam_pulang,
                ] : null,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_izin' => $totalIzin,
                'total_sakit' => $totalSakit,
                'total_absen' => $totalAbsen,
                'total_cuti' => $totalCuti,
                'tugas_count' => $tugasCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDashboardData: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error loading dashboard data',
                'message' => $e->getMessage(),
                'attendance_status' => 'Error',
                'total_hadir' => 0,
                'total_terlambat' => 0,
                'total_izin' => 0,
                'total_sakit' => 0,
                'total_absen' => 0,
                'total_cuti' => 0,
                'tugas_count' => 0,
            ], 500);
        }
    }

    public function getDashboardDataApi()
    {
        try {
            $userId = Auth::id();
            $today = now()->toDateString();
            $todayRecord = Absensi::where('user_id', $userId)->where('tanggal', $today)->first();

            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();
            
            $monthlyRecords = Absensi::where('user_id', $userId)
                ->whereBetween('tanggal', [$monthStart, $monthEnd])
                ->get();

            $totalHadir = 0;
            $totalTerlambat = 0;
            $totalIzin = 0;
            $totalSakit = 0;
            $totalAbsen = 0;
            $totalCuti = 0;

            foreach ($monthlyRecords as $record) {
                if ($record->jam_masuk && !$record->jenis_ketidakhadiran) {
                    $totalHadir++;
                    if ($this->isAttendanceLate($record)) {
                        $totalTerlambat++;
                    }
                } elseif ($record->jenis_ketidakhadiran) {
                    switch ($record->jenis_ketidakhadiran) {
                        case 'izin':
                            $totalIzin++;
                            break;
                        case 'sakit':
                            $totalSakit++;
                            break;
                        case 'cuti':
                            $totalCuti++;
                            break;
                        default:
                            $totalAbsen++;
                            break;
                    }
                } elseif (!$record->jam_masuk && !$record->jenis_ketidakhadiran) {
                    $totalAbsen++;
                }
            }

            $weekStart = now()->startOfWeek()->toDateString();
            $weekEnd = now()->endOfWeek()->toDateString();
            
            $weeklyRecords = Absensi::where('user_id', $userId)
                ->whereBetween('tanggal', [$weekStart, $weekEnd])
                ->get();

            $weeklyHadir = 0;
            $weeklyTerlambat = 0;
            $weeklyIzin = 0;
            $weeklySakit = 0;
            $weeklyAbsen = 0;
            $weeklyCuti = 0;

            foreach ($weeklyRecords as $record) {
                if ($record->jam_masuk && !$record->jenis_ketidakhadiran) {
                    $weeklyHadir++;
                    if ($this->isAttendanceLate($record)) {
                        $weeklyTerlambat++;
                    }
                } elseif ($record->jenis_ketidakhadiran) {
                    switch ($record->jenis_ketidakhadiran) {
                        case 'izin':
                            $weeklyIzin++;
                            break;
                        case 'sakit':
                            $weeklySakit++;
                            break;
                        case 'cuti':
                            $weeklyCuti++;
                            break;
                        default:
                            $weeklyAbsen++;
                            break;
                    }
                } elseif (!$record->jam_masuk && !$record->jenis_ketidakhadiran) {
                    $weeklyAbsen++;
                }
            }

            $todayAttendanceStatus = 'Belum Absen';
            if ($todayRecord) {
                if ($todayRecord->jam_masuk) {
                    $todayAttendanceStatus = $this->isAttendanceLate($todayRecord) ? 'Terlambat' : 'Tepat Waktu';
                } elseif ($todayRecord->jenis_ketidakhadiran) {
                    $todayAttendanceStatus = 'Tidak Hadir';
                }
            }

            $cutiSaldo = $this->getCutiSaldo($userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'attendance_status' => $todayAttendanceStatus,
                    'today' => [
                        'jam_masuk' => $todayRecord ? $todayRecord->jam_masuk?->format('H:i:s') : null,
                        'jam_pulang' => $todayRecord ? $todayRecord->jam_pulang?->format('H:i:s') : null,
                        'status' => $todayAttendanceStatus,
                        'late_minutes' => $todayRecord ? $todayRecord->late_minutes : 0,
                        'is_late' => $todayRecord ? $this->isAttendanceLate($todayRecord) : false,
                        'is_early_checkout' => $todayRecord ? $todayRecord->is_early_checkout : false,
                        'early_checkout_reason' => $todayRecord ? $todayRecord->early_checkout_reason : null,
                    ],
                    'month' => [
                        'total_hadir' => $totalHadir,
                        'total_terlambat' => $totalTerlambat,
                        'total_izin' => $totalIzin,
                        'total_sakit' => $totalSakit,
                        'total_absen' => $totalAbsen,
                        'total_cuti' => $totalCuti,
                    ],
                    'weekly' => [
                        'total_hadir' => $weeklyHadir,
                        'total_terlambat' => $weeklyTerlambat,
                        'total_izin' => $weeklyIzin,
                        'total_sakit' => $weeklySakit,
                        'total_absen' => $weeklyAbsen,
                        'total_cuti' => $weeklyCuti,
                    ],
                    'cuti_saldo' => $cutiSaldo,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard API Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    private function getCutiSaldo($userId)
    {
        try {
            $karyawan = Karyawan::where('user_id', $userId)->first();
            if (!$karyawan) return 0;

            $cutiKuota = CutiKuota::where('karyawan_id', $karyawan->id)
                ->where('tahun', now()->year)
                ->first();

            $totalKuota = $cutiKuota ? $cutiKuota->jumlah_hari : 12;

            $cutiDiambil = Cuti::where('karyawan_id', $karyawan->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', now()->year)
                ->sum('jumlah_hari');

            return max(0, $totalKuota - $cutiDiambil);
        } catch (\Exception $e) {
            Log::error('Error calculating cuti saldo: ' . $e->getMessage());
            return 0;
        }
    }

    public function getHistoryApi(Request $request)
    {
        try {
            $userId = Auth::id();
            
            $filter = $request->query('filter', 'month');
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            
            $query = Absensi::where('user_id', $userId);
            
            if ($filter === 'custom' && $month && $year) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } elseif ($filter === 'week') {
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } elseif ($filter === 'month') {
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } elseif ($filter === 'year') {
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }
            
            $history = $query->orderBy('tanggal', 'desc')
                            ->get()
                            ->map(function ($item) {
                                $status = 'Tidak Hadir';
                                $lateMinutes = 0;
                                $isLate = false;
                                
                                if ($item->jam_masuk) {
                                    $jamMasuk = Carbon::parse($item->jam_masuk);
                                    $jamBatas = Carbon::parse('08:00');
                                    $lateMinutes = $jamMasuk->gt($jamBatas) ? $jamMasuk->diffInMinutes($jamBatas) : 0;
                                    $isLate = $lateMinutes > 0;
                                    $status = $isLate ? 'Terlambat' : 'Tepat Waktu';
                                } elseif ($item->jenis_ketidakhadiran) {
                                    $status = match($item->jenis_ketidakhadiran) {
                                        'cuti' => 'Cuti',
                                        'sakit' => 'Sakit',
                                        'izin' => 'Izin',
                                        'dinas-luar' => 'Dinas Luar',
                                        default => 'Tidak Hadir',
                                    };
                                }
                                
                                return [
                                    'id' => $item->id,
                                    'tanggal' => $item->tanggal,
                                    'jam_masuk' => $item->jam_masuk,
                                    'jam_pulang' => $item->jam_pulang,
                                    'status' => $status,
                                    'late_minutes' => $lateMinutes,
                                    'is_late' => $isLate,
                                    'jenis_ketidakhadiran' => $item->jenis_ketidakhadiran,
                                    'jenis_ketidakhadiran_label' => match($item->jenis_ketidakhadiran) {
                                        'cuti' => 'Cuti',
                                        'sakit' => 'Sakit',
                                        'izin' => 'Izin',
                                        'dinas-luar' => 'Dinas Luar',
                                        default => null,
                                    },
                                    'approval_status' => $item->approval_status,
                                    'reason' => $item->reason,
                                    'keterangan' => $item->keterangan,
                                ];
                            });

            return response()->json([
                'success' => true,
                'data' => $history,
                'filter' => $filter,
                'count' => $history->count()
            ]);
        } catch (\Exception $e) {
            Log::error('History API Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function getPengajuanStatus()
    {
        try {
            $userId = Auth::id();
            $pending = Absensi::where('user_id', $userId)
                ->where('approval_status', 'pending')
                ->whereNotNull('jenis_ketidakhadiran')
                ->orderBy('tanggal', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'tanggal' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                        'jenis' => $item->jenis_ketidakhadiran,
                        'status' => $item->approval_status,
                    ];
                });

            $recentSubmissions = Absensi::where('user_id', $userId)
                                        ->whereNotNull('jenis_ketidakhadiran')
                                        ->whereIn('approval_status', ['approved', 'rejected'])
                                        ->where('tanggal', '>=', now()->subDays(7)->toDateString())
                                        ->orderBy('tanggal', 'desc')
                                        ->get()
                                        ->map(function ($item) {
                                            return [
                                                'id' => $item->id,
                                                'tanggal' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                                                'jenis_ketidakhadiran' => $item->jenis_ketidakhadiran,
                                                'jenis_label' => match($item->jenis_ketidakhadiran) {
                                                    'cuti' => 'Cuti',
                                                    'sakit' => 'Sakit',
                                                    'izin' => 'Izin',
                                                    'dinas-luar' => 'Dinas Luar',
                                                    default => 'Ketidakhadiran',
                                                },
                                                'approval_status' => $item->approval_status,
                                                'reason' => $item->reason,
                                                'keterangan' => $item->keterangan,
                                            ];
                                        });

            return response()->json([
                'success' => true,
                'data' => [
                    'pending' => $pending,
                    'recent' => $recentSubmissions,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Pengajuan Status Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error'], 500);
        }
    }

    public function absenMasukApi(Request $request)
    {
        try {
            $user = Auth::user();
            $nowWIB = now('Asia/Jakarta');
            $today = $nowWIB->toDateString();

            $operationalHours = Setting::where('key', 'operational_hours')->first();
            $startTime = '08:00';
            $lateLimitTime = '09:05';
            if ($operationalHours) {
                $settings = json_decode($operationalHours->value, true);
                $startTime = $settings['start_time'] ?? '08:00';
                $lateLimitTime = $settings['late_limit_time']
                    ?? sprintf(
                        '%02d:%02d',
                        (int) ($settings['late_limit_hour'] ?? 9),
                        (int) ($settings['late_limit_minute'] ?? 5)
                    );
            }

            [$startHour, $startMin] = explode(':', $startTime);
            $startSeconds = (int)$startHour * 3600 + (int)$startMin * 60;

            $currentSeconds = $nowWIB->hour * 3600 + $nowWIB->minute * 60 + $nowWIB->second;

            if ($currentSeconds < $startSeconds) {
                $secondsUntilStart = $startSeconds - $currentSeconds;
                $hoursUntil = intdiv($secondsUntilStart, 3600);
                $minutesUntil = intdiv($secondsUntilStart % 3600, 60);

                return response()->json([
                    'success' => false,
                    'message' => "Absen masuk baru bisa dilakukan mulai pukul {$startTime} WIB. Sisa waktu: {$hoursUntil} jam {$minutesUntil} menit."
                ], 403);
            }

            $cutiCheck = $this->checkIfOnLeaveToday($user->id);
            if ($cutiCheck['on_leave']) {
                $cuti = $cutiCheck['details'];
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sedang cuti dari ' .
                        Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') .
                        ' sampai ' .
                        Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') .
                        '. Tidak dapat melakukan absensi.',
                ], 403);
            }

            $existingAbsence = Absensi::where('user_id', $user->id)
                                      ->where('tanggal', $today)
                                      ->whereNotNull('jenis_ketidakhadiran')
                                      ->where('approval_status', 'approved')
                                      ->first();

            if ($existingAbsence) {
                $jenisLabel = match($existingAbsence->jenis_ketidakhadiran) {
                    'cuti' => 'Cuti',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'dinas-luar' => 'Dinas Luar',
                    default => 'Ketidakhadiran',
                };

                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat melakukan absen masuk karena telah mengajukan ketidakhadiran pada hari ini.'
                ], 403);
            }

            $cek = Absensi::withTrashed()
                ->where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->first();

            if ($cek && !$cek->trashed() && $cek->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu sudah absen masuk hari ini'
                ], 409);
            }

            [$lateHour, $lateMin] = array_map('intval', explode(':', $lateLimitTime));
            $workStartTime = $nowWIB->copy()->setTime($lateHour, $lateMin, 0);
            $lateMinutes = $nowWIB->greaterThan($workStartTime) ? $workStartTime->diffInMinutes($nowWIB) : 0;

            if ($cek) {
                if ($cek->trashed()) {
                    $cek->restore();
                }
                $absensi = $cek;
                $absensi->jam_masuk = $nowWIB;
                $absensi->approval_status = 'approved';
                $absensi->late_minutes = $lateMinutes;
                $absensi->save();
            } else {
                $absensi = Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $today,
                    'jam_masuk' => $nowWIB,
                    'approval_status' => 'approved',
                    'late_minutes' => $lateMinutes,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil!',
                'data' => [
                    'id' => $absensi->id,
                    'time' => $nowWIB->toDateTimeString(),
                    'jam_masuk' => $nowWIB->toTimeString(),
                    'late_minutes' => $lateMinutes,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Absen Masuk Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    public function absenPulangApi(Request $request)
    {
        try {
            $user = Auth::user();
            $today = now()->toDateString();

            $cutiCheck = $this->checkIfOnLeaveToday($user->id);
            if ($cutiCheck['on_leave']) {
                $cuti = $cutiCheck['details'];
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sedang cuti dari ' .
                        Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') .
                        ' sampai ' .
                        Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') .
                        '. Tidak dapat melakukan absensi.',
                    'cuti_details' => [
                        'tanggal_mulai' => $cuti->tanggal_mulai,
                        'tanggal_selesai' => $cuti->tanggal_selesai,
                        'tipe_cuti' => $cuti->tipe_cuti,
                        'alasan' => $cuti->alasan
                    ]
                ], 403);
            }

            $absen = Absensi::where('user_id', $user->id)
                ->where('tanggal', $today)
                ->first();

            if (!$absen || !$absen->jam_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda belum absen masuk.'], 400);
            }

            if ($absen->jam_pulang) {
                return response()->json(['success' => false, 'message' => 'Anda sudah absen pulang.'], 409);
            }

            $nowLocal = now();
            $workEndTime = $nowLocal->copy()->setTime(17, 0, 0);
            $isEarlyCheckout = $nowLocal->lessThan($workEndTime);

            $reason = null;
            if ($isEarlyCheckout) {
                $request->validate(['reason' => 'required|string|max:255']);
                $reason = $request->input('reason');
            }

            $absen->update([
                'jam_pulang' => $nowLocal,
                'is_early_checkout' => $isEarlyCheckout,
                'early_checkout_reason' => $reason,
                'approval_status' => 'approved',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen pulang berhasil!',
                'data' => [
                    'id' => $absen->id,
                    'time' => $nowLocal->toDateTimeString(),
                    'jam_pulang' => $nowLocal->toTimeString(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Absen Pulang Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    public function submitIzinApi(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal',
            'keterangan' => 'required|string',
            'jenis' => 'required|string|in:sakit,izin',
        ]);

        $user = Auth::user();
        $period = CarbonPeriod::create($request->tanggal, $request->tanggal_akhir);

        foreach ($period as $date) {
            $dateStr = $date->toDateString();

            $existingCuti = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereDate('tanggal_mulai', '<=', $dateStr)
                ->whereDate('tanggal_selesai', '>=', $dateStr)
                ->exists();

            if ($existingCuti) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki cuti yang disetujui pada tanggal ' .
                        Carbon::parse($dateStr)->format('d/m/Y') .
                        '. Tidak dapat mengajukan izin.'
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($period as $date) {
                Absensi::updateOrCreate(
                    ['user_id' => Auth::id(), 'tanggal' => $date->toDateString()],
                    [
                        'jenis_ketidakhadiran' => $request->jenis,
                        'reason' => $request->keterangan,
                        'approval_status' => 'pending',
                        'tanggal_akhir' => $request->tanggal_akhir,
                        'keterangan' => 'Pengajuan ' . $request->jenis,
                        'status' => 'tidak_hadir'
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengajuan berhasil.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal mengajukan.'], 500);
        }
    }

    public function submitDinasApi(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string',
            'purpose' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = Auth::user();
        $period = CarbonPeriod::create($request->start_date, $request->end_date);

        foreach ($period as $date) {
            $dateStr = $date->toDateString();

            $existingCuti = Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereDate('tanggal_mulai', '<=', $dateStr)
                ->whereDate('tanggal_selesai', '>=', $dateStr)
                ->exists();

            if ($existingCuti) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki cuti yang disetujui pada tanggal ' .
                        Carbon::parse($dateStr)->format('d/m/Y') .
                        '. Tidak dapat mengajukan dinas luar.'
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($period as $date) {
                Absensi::updateOrCreate(
                    ['user_id' => Auth::id(), 'tanggal' => $date->toDateString()],
                    [
                        'jenis_ketidakhadiran' => 'dinas-luar',
                        'reason' => $request->description,
                        'location' => $request->location,
                        'purpose' => $request->purpose,
                        'approval_status' => 'pending',
                        'tanggal_akhir' => $request->end_date,
                        'keterangan' => 'Pengajuan dinas luar',
                        'status' => 'tidak_hadir'
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengajuan dinas luar berhasil.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal mengajukan dinas luar.'], 500);
        }
    }

    public function apiGetTasks()
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();
            $userDivisi = $user->divisi ?? null;
            if (!$userDivisi) {
                $k = Karyawan::where('user_id', $userId)->first();
                $userDivisi = $k ? $k->divisi : null;
            }

            $tasks = Task::where(function ($query) use ($userId, $userDivisi) {
                $query->where('assigned_to', $userId);
                
                if (Schema::hasColumn('tasks', 'target_type') && $userDivisi) {
                    $query->orWhere(function ($q) use ($userDivisi) {
                        $q->where('target_type', 'divisi');
                        if (Schema::hasColumn('tasks', 'target_divisi_id')) $q->where('target_divisi_id', $userDivisi);
                        elseif (Schema::hasColumn('tasks', 'target_id')) $q->where('target_id', $userDivisi);
                    });
                } else {
                    if ($userDivisi && Schema::hasColumn('tasks', 'divisi')) {
                        $query->orWhere('divisi', $userDivisi);
                    }
                }
            })
            ->with(['creator:id,name', 'assignee:id,name'])
            ->orderBy('deadline', 'asc')
            ->get();

            $transformedTasks = $tasks->map(function ($task) {
                $assigneeDivisi = null;
                if ($task->assignee) {
                    $k = Karyawan::where('user_id', $task->assignee->id)->first(['divisi']);
                    $assigneeDivisi = $k ? $k->divisi : null;
                }

                return [
                    'id' => $task->id,
                    'judul' => $task->judul ?: $task->nama_tugas,
                    'nama_tugas' => $task->nama_tugas ?: $task->judul,
                    'deskripsi' => $task->deskripsi,
                    'deadline' => $task->deadline ? $task->deadline->format('Y-m-d H:i:s') : null,
                    'status' => $task->status,
                    'target_type' => $task->target_type ?? 'unknown',
                    'assignee_text' => $task->assignee ? $task->assignee->name : ($task->target_type === 'divisi' ? 'Divisi' : 'Unknown'),
                    'assignee_divisi' => $assigneeDivisi,
                    'creator_name' => $task->creator ? $task->creator->name : 'System',
                ];
            });

            return response()->json(['success' => true, 'data' => $transformedTasks->toArray()]);
        } catch (\Exception $e) {
            Log::error('API Get Tasks Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal memuat tugas: ' . $e->getMessage()], 500);
        }
    }

    // =================================================================
    // METHOD UNTUK MEETING NOTES DAN PENGUMUMAN
    // =================================================================

    public function getMeetingNotesDatesApi()
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            
            if (!Schema::hasTable('catatan_rapats')) {
                return response()->json(['success' => true, 'dates' => []]);
            }
            
            $dates = CatatanRapat::select('tanggal')
                ->distinct()
                ->orderBy('tanggal', 'desc')
                ->pluck('tanggal')
                ->map(function($date) { return Carbon::parse($date)->format('Y-m-d'); })
                ->toArray();
            
            return response()->json(['success' => true, 'dates' => $dates]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getMeetingNotesApi(Request $request)
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            $date = $request->query('date');
            if (!$date) return response()->json(['success' => false, 'message' => 'Date required'], 400);
            
            if (!Schema::hasTable('catatan_rapats')) {
                return response()->json(['success' => true, 'data' => []]);
            }
            
            $meetingNotes = CatatanRapat::whereDate('tanggal', $date)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'topik', 'hasil_diskusi', 'keputusan', 'tanggal', 'created_at']);

            $formattedNotes = $meetingNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'topik' => $note->topik,
                    'hasil_diskusi' => $note->hasil_diskusi,
                    'keputusan' => $note->keputusan,
                    'tanggal' => $note->tanggal,
                    'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                    'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                    'formatted_created_at' => $note->created_at->translatedFormat('d F Y H:i'),
                ];
            });
            
            return response()->json(['success' => true, 'data' => $formattedNotes, 'date' => $date]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function getMeetingNotesDates()
    {
        try {
            $userId = Auth::id();
            
            if (!Schema::hasTable('catatan_rapats')) {
                return response()->json([]);
            }
            
            $dates = CatatanRapat::select('tanggal')
                ->distinct()
                ->orderBy('tanggal', 'desc')
                ->pluck('tanggal')
                ->map(function($date) { return Carbon::parse($date)->format('Y-m-d'); })
                ->toArray();
                
            return response()->json($dates);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getMeetingNotes(Request $request)
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            $date = $request->query('date');
            if (!$date) return response()->json(['success' => false, 'message' => 'Date required'], 400);
            
            if (!Schema::hasTable('catatan_rapats')) {
                return response()->json([]);
            }
            
            $meetingNotes = CatatanRapat::whereDate('tanggal', $date)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'topik', 'hasil_diskusi', 'keputusan', 'tanggal', 'created_at']);
            
            $formattedNotes = $meetingNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'topik' => $note->topik,
                    'hasil_diskusi' => $note->hasil_diskusi,
                    'keputusan' => $note->keputusan,
                    'tanggal' => $note->tanggal,
                    'formatted_tanggal' => Carbon::parse($note->tanggal)->translatedFormat('d F Y'),
                    'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                    'formatted_created_at' => $note->created_at->translatedFormat('d F Y H:i'),
                ];
            });
            
            return response()->json($formattedNotes->toArray());
            
        } catch (\Exception $e) {
            \Log::error('Error in getMeetingNotes: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function getAnnouncementDatesApi()
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            if (!Schema::hasTable('pengumuman')) return response()->json(['success' => true, 'dates' => []]);
            
            $dates = Pengumuman::select('created_at')
                ->distinct()
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->map(function($date) { return Carbon::parse($date)->format('Y-m-d'); })
                ->toArray();
            
            return response()->json(['success' => true, 'dates' => $dates]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function getAnnouncementsApi()
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            if (!Schema::hasTable('pengumuman')) return response()->json(['success' => true, 'data' => []]);
            
            $announcements = Pengumuman::orderBy('created_at', 'desc')
                ->limit(20)
                ->get(['id', 'judul', 'isi_pesan', 'lampiran', 'created_at']);

            $formattedAnnouncements = $announcements->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'judul' => $announcement->judul,
                    'isi' => $announcement->isi_pesan,
                    'tanggal' => $announcement->created_at->format('Y-m-d'),
                    'formatted_tanggal' => Carbon::parse($announcement->created_at)->translatedFormat('d F Y'),
                    'lampiran' => $announcement->lampiran,
                    'lampiran_url' => $announcement->lampiran ? asset('storage/' . $announcement->lampiran) : null,
                    'created_at' => $announcement->created_at->format('Y-m-d H:i:s'),
                    'formatted_created_at' => $announcement->created_at->translatedFormat('d F Y H:i'),
                ];
            });
            
            return response()->json(['success' => true, 'data' => $formattedAnnouncements]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function getAnnouncements()
    {
        try {
            $userId = Auth::id();
            
            if (!Schema::hasTable('pengumuman')) {
                return response()->json([]);
            }
            
            $announcements = Pengumuman::orderBy('created_at', 'desc')
                ->get();
                
            $formattedAnnouncements = $announcements->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'isi_pesan' => $item->isi_pesan,
                    'ringkasan' => $item->ringkasan ?? null,
                    'lampiran' => $item->lampiran,
                    'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'tanggal_indo' => $item->created_at->translatedFormat('d F Y'),
                ];
            });

            return response()->json($formattedAnnouncements);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getAnnouncementsDates()
    {
        try {
            if (!Schema::hasTable('pengumuman')) {
                return response()->json([]);
            }
            
            $dates = Pengumuman::select('created_at')
                ->distinct()
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->map(function($date) { return Carbon::parse($date)->format('Y-m-d'); })
                ->toArray();
            
            return response()->json($dates);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getAnnouncementsByDate(Request $request)
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            $date = $request->query('date');
            if (!$date) return response()->json(['success' => false, 'message' => 'Date required'], 400);
            
            if (!Schema::hasTable('pengumuman')) {
                return response()->json([]);
            }
            
            $announcements = Pengumuman::whereDate('created_at', $date)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $formattedAnnouncements = $announcements->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'isi_pesan' => $item->isi_pesan,
                    'ringkasan' => $item->ringkasan ?? null,
                    'lampiran' => $item->lampiran,
                    'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'tanggal_indo' => $item->created_at->translatedFormat('d F Y'),
                ];
            });

            return response()->json($formattedAnnouncements);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function debugGmDashboard()
    {
        $pendingAbsensi = Absensi::where('approval_status', 'pending')
            ->whereNotNull('jenis_ketidakhadiran')
            ->get(['id', 'user_id', 'jenis_ketidakhadiran', 'approval_status', 'tanggal']);
        
        $pendingCuti = Cuti::where('status', 'pending')->get(); 

        return response()->json([
            'status' => 'ok',
            'total_pending_absensi' => $pendingAbsensi->count(),
            'data_pending_absensi' => $pendingAbsensi,
            'total_pending_cuti' => $pendingCuti->count(),
            'data_pending_cuti' => $pendingCuti,
            'message' => 'Lihat output ini. Jika data sakit ada di sini, berarti query controller salah. Jika KOSONG, berarti status data di DB bukan PENDING.'
        ]);
    }

    public function testApiEndpoints()
    {
        return response()->json(['status' => 'ok', 'message' => 'API endpoints are working.']);
    }

    public function getDetailApi($id)
    {
        try {
            $karyawan = User::with(['files'])->find($id);
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }
            
            $authUser = Auth::user();
            if ($authUser->role === 'manager_divisi' && $karyawan->divisi_id !== $authUser->divisi_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke data karyawan ini'
                ], 403);
            }
            
            $data = [
                'id' => $karyawan->id,
                'nama' => $karyawan->name,
                'name' => $karyawan->name,
                'email' => $karyawan->email,
                'role' => $karyawan->role,
                'divisi' => $karyawan->divisi,
                'divisi_id' => $karyawan->divisi_id,
                'status_kerja' => $karyawan->status_kerja,
                'status_karyawan' => $karyawan->status_karyawan,
                'kontak' => $karyawan->kontak,
                'alamat' => $karyawan->alamat,
                'foto' => $karyawan->foto,
                'files' => []
            ];
            
            if ($karyawan->files && count($karyawan->files) > 0) {
                $data['files'] = $karyawan->files->map(function($file) {
                    return [
                        'id' => $file->id,
                        'name' => $file->nama ?? $file->name ?? 'File',
                        'nama' => $file->nama ?? $file->name ?? 'File',
                        'url' => $file->url ?? '/storage/' . $file->path
                    ];
                })->toArray();
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting karyawan detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail karyawan'
            ], 500);
        }
    }

    public function getAcceptanceStatus($taskId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $task = Task::find($taskId);
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas tidak ditemukan'
                ], 404);
            }

            $isAssigned = $task->assigned_to == $user->id || 
                         (is_array($task->assigned_to_ids) && in_array($user->id, $task->assigned_to_ids));
            
            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melihat status penerimaan tugas ini'
                ], 403);
            }

            if (!$task->assigned_to_ids || !is_array($task->assigned_to_ids) || count($task->assigned_to_ids) <= 1) {
                return response()->json([
                    'success' => true,
                    'acceptance_status' => [
                        'total' => 1,
                        'accepted' => $task->status === 'proses' ? 1 : 0,
                        'pending' => $task->status === 'pending' ? 1 : 0,
                        'rejected' => 0,
                        'percentage' => $task->status === 'proses' ? 100 : 0,
                        'is_fully_accepted' => $task->status === 'proses',
                        'is_any_accepted' => $task->status === 'proses',
                        'is_any_rejected' => false
                    ],
                    'acceptance_details' => [
                        [
                            'user_id' => $task->assigned_to,
                            'user_name' => $task->assignee->name ?? 'Unknown',
                            'user_email' => $task->assignee->email ?? 'Unknown',
                            'status' => $task->status === 'proses' ? 'accepted' : 'pending',
                            'accepted_at' => $task->status === 'proses' ? now() : null,
                            'notes' => null
                        ]
                    ]
                ]);
            }

            $this->initializeTaskAcceptances($task);

            return response()->json([
                'success' => true,
                'acceptance_status' => $task->getAcceptanceStatus(),
                'acceptance_details' => $task->getAcceptanceDetails()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting acceptance status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status penerimaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function acceptTask(Request $request, $taskId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $task = Task::find($taskId);
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas tidak ditemukan'
                ], 404);
            }

            $isAssigned = $task->assigned_to == $user->id || 
                         (is_array($task->assigned_to_ids) && in_array($user->id, $task->assigned_to_ids));
            
            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menerima tugas ini'
                ], 403);
            }

            $task->status = 'proses';
            $task->save();

            if ($task->assigned_to_ids && is_array($task->assigned_to_ids) && count($task->assigned_to_ids) > 1) {
                if (!$task->acceptances()->exists()) {
                    $this->initializeTaskAcceptances($task);
                }

                $acceptance = TaskAcceptance::updateOrCreate(
                    [
                        'task_id' => $taskId,
                        'user_id' => $user->id
                    ],
                    [
                        'status' => 'accepted',
                        'accepted_at' => now(),
                        'notes' => $request->input('notes')
                    ]
                );

                $acceptanceStatus = $task->getAcceptanceStatus();
                if (!$acceptanceStatus['is_fully_accepted']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Tugas berhasil diterima. Status sudah berubah menjadi Dalam Proses',
                        'data' => [
                            'task_id' => $task->id,
                            'task_status' => $task->status,
                            'acceptance_status' => $acceptanceStatus,
                            'acceptance_details' => $task->getAcceptanceDetails()
                        ]
                    ]);
                }
            }

            Log::info('Task accepted by karyawan', [
                'task_id' => $taskId,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'new_status' => 'proses'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil diterima. Status berubah menjadi Dalam Proses',
                'data' => [
                    'task_id' => $task->id,
                    'task_status' => $task->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error accepting task: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima tugas: ' . $e->getMessage()
            ], 500);
        }
    }

    private function initializeTaskAcceptances($task)
    {
        if ($task->acceptances()->exists()) {
            return;
        }

        $assignees = [];
        
        if ($task->assigned_to) {
            $assignees[] = $task->assigned_to;
        }
        
        if ($task->assigned_to_ids && is_array($task->assigned_to_ids)) {
            $assignees = array_merge($assignees, $task->assigned_to_ids);
        }

        $assignees = array_unique($assignees);

        foreach ($assignees as $userId) {
            TaskAcceptance::firstOrCreate(
                [
                    'task_id' => $task->id,
                    'user_id' => $userId
                ],
                [
                    'status' => 'pending',
                    'accepted_at' => null
                ]
            );
        }
    }
}