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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\CarbonPeriod;

class KaryawanController extends Controller
{
    /**
     * Menampilkan data karyawan untuk general manager (dengan filter & pagination).
     */
// Di dalam KaryawanController.php, method indexPegawai()

public function indexPegawai(Request $request)
{
    $user = Auth::user();
    if ($user->role === 'hr') {
        // HR: source data dari tabel users
        $query = User::with(['divisi', 'karyawan.tim', 'karyawan.tunjanganMaster'])
            ->whereRaw("LOWER(TRIM(role)) NOT IN ('admin','owner')");

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%");
            });
        }

        if ($divisi = $request->query('divisi')) {
            $query->whereHas('divisi', function ($sq) use ($divisi) {
                $sq->where('divisi', $divisi);
            });
        }

        $usersCollection = $query
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // PERBAIKAN: Ambil tunjangan dari tabel karyawan
        $karyawan = $usersCollection->map(function ($u) {
            $k = $u->karyawan;
            $karyawanAlamat = trim((string) optional($k)->alamat);
            $karyawanKontak = trim((string) optional($k)->kontak);
            $karyawanFoto = trim((string) optional($k)->foto);

            return (object) [
                'id' => $k ? $k->id : $u->id,
                'user_id' => $u->id,
                'nama' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'divisi' => optional($u->divisi)->divisi ?? optional($k)->divisi,
                'divisi_id' => $u->divisi_id,
                'tim' => optional($k)->tim,
                'alamat' => $karyawanAlamat !== '' ? optional($k)->alamat : $u->alamat,
                'kontak' => $karyawanKontak !== '' ? optional($k)->kontak : $u->kontak,
                'foto' => $karyawanFoto !== '' ? optional($k)->foto : $u->foto,
                'gaji' => $u->gaji,
                'kontrak_mulai' => optional($k)->kontrak_mulai,
                'kontrak_selesai' => optional($k)->kontrak_selesai,
                'status_kerja' => optional($k)->status_kerja ?? $u->status_kerja,
                'status_karyawan' => optional($k)->status_karyawan ?? $u->status_karyawan,
                'tunjangan_tetap_ids' => $k && optional($k)->tunjanganMaster ? collect(optional($k)->tunjanganMaster)->where('tipe', 'bulanan')->pluck('id')->toArray() : [],
                'tunjangan_tidak_tetap_ids' => $k && optional($k)->tunjanganMaster ? collect(optional($k)->tunjanganMaster)->whereIn('tipe', ['bonus', 'insentif'])->pluck('id')->toArray() : [],
                'tunjangan_tetap_list' => $k && optional($k)->tunjanganMaster ? collect(optional($k)->tunjanganMaster)->where('tipe', 'bulanan') : collect(),
                'tunjangan_tidak_tetap_list' => $k && optional($k)->tunjanganMaster ? collect(optional($k)->tunjanganMaster)->whereIn('tipe', ['bonus', 'insentif']) : collect(),
                'user' => $u,
                'name' => $u->name,
            ];
        });

        $divisis = Divisi::orderBy('divisi', 'asc')->get();
        $tunjanganMaster = TunjanganMaster::orderBy('tipe')->orderBy('nama')->get();

        return view('hr.data_karyawan', compact('karyawan', 'divisis', 'tunjanganMaster'));
    } else {
        // Non-HR role
        $query = Karyawan::with('user.divisi', 'tim');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($divisi = $request->query('divisi')) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->whereHas('divisi', function ($sq) use ($divisi) {
                    $sq->where('divisi', $divisi);
                });
            });
        }

        $karyawan = $query->orderBy('nama')->paginate(15)->withQueryString();
        $tunjanganMaster = TunjanganMaster::orderBy('tipe')->orderBy('nama')->get();

        return view('general_manajer.data_karyawan', compact('karyawan', 'tunjanganMaster'));
    }
}

    /**
     * Store karyawan baru (untuk form tambah karyawan).
     */
    public function storePegawai(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:255',
            'gaji' => 'nullable|string|max:100',
            'kontrak_mulai' => 'nullable|date',
            'kontrak_selesai' => 'nullable|date|after_or_equal:kontrak_mulai',
            'status_karyawan' => 'nullable|in:tetap,kontrak,freelance',
            'tunjangan_tetap_ids' => 'nullable|json',
            'tunjangan_tidak_tetap_ids' => 'nullable|json',
        ]);

        if (($validated['status_karyawan'] ?? null) !== 'kontrak') {
            $validated['kontrak_mulai'] = null;
            $validated['kontrak_selesai'] = null;
        }

        // Jika karyawan tipe kontrak dan tanggal selesai kontrak sudah lewat, set status_kerja nonaktif
        if (($validated['status_karyawan'] ?? null) === 'kontrak' && !empty($validated['kontrak_selesai'])) {
            try {
                if (Carbon::parse($validated['kontrak_selesai'])->isPast()) {
                    $validated['status_kerja'] = 'nonaktif';
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        $karyawan = Karyawan::create($validated);

        $tunjanganTetapIds = $request->input('tunjangan_tetap_ids', []);
        $tunjanganTidakTetapIds = $request->input('tunjangan_tidak_tetap_ids', []);
        if (is_string($tunjanganTetapIds)) {
            $tunjanganTetapIds = json_decode($tunjanganTetapIds, true);
        }
        if (is_string($tunjanganTidakTetapIds)) {
            $tunjanganTidakTetapIds = json_decode($tunjanganTidakTetapIds, true);
        }
        $karyawan->tunjanganMaster()->sync(array_filter(array_merge((array) $tunjanganTetapIds, (array) $tunjanganTidakTetapIds)));

        return redirect()->route('pegawai.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Edit karyawan (return JSON untuk modal).
     */
    public function editPegawai($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return response()->json($karyawan);
    }

    /**
     * Update karyawan.
     */
    public function updatePegawai(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:255',
            'gaji' => 'nullable|string|max:100',
            'kontrak_mulai' => 'nullable|date',
            'kontrak_selesai' => 'nullable|date|after_or_equal:kontrak_mulai',
            'status_karyawan' => 'nullable|in:tetap,kontrak,freelance',
            'tunjangan_tetap_ids' => 'nullable|json',
            'tunjangan_tidak_tetap_ids' => 'nullable|json',
        ]);

        if (($validated['status_karyawan'] ?? $karyawan->status_karyawan) !== 'kontrak') {
            $validated['kontrak_mulai'] = null;
            $validated['kontrak_selesai'] = null;
        }

        if (($validated['status_karyawan'] ?? $karyawan->status_karyawan) === 'kontrak' && !empty($validated['kontrak_selesai'])) {
            try {
                if (Carbon::parse($validated['kontrak_selesai'])->isPast()) {
                    $validated['status_kerja'] = 'nonaktif';
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        $karyawan->update($validated);

        $tunjanganTetapIds = $request->input('tunjangan_tetap_ids', []);
        $tunjanganTidakTetapIds = $request->input('tunjangan_tidak_tetap_ids', []);
        if (is_string($tunjanganTetapIds)) {
            $tunjanganTetapIds = json_decode($tunjanganTetapIds, true);
        }
        if (is_string($tunjanganTidakTetapIds)) {
            $tunjanganTidakTetapIds = json_decode($tunjanganTidakTetapIds, true);
        }
        $karyawan->tunjanganMaster()->sync(array_filter(array_merge((array) $tunjanganTetapIds, (array) $tunjanganTidakTetapIds)));

        return redirect()->route('pegawai.index')->with('success', 'karyawan berhasil diperbarui.');
    }

    /**
     * Delete karyawan.
     */
    public function destroyPegawai($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('pegawai.index')->with('success', 'karyawan berhasil dihapus.');
    }

    /**
     * Helper method untuk cek apakah user sedang cuti hari ini
     */
    private function checkIfOnLeaveToday($userId)
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        
        $cuti = Cuti::where('user_id', $userId)
            ->where('status', 'disetujui') // Pastikan kolom status di tabel cuti menggunakan 'disetujui'
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
        $user = Auth::user()->load('divisi');  // Eager load the divisi relationship
        $userRole = $user->role; 
        
        // Determine divisi id and name (prefer users.divisi_id, but keep robust fallback)
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

        // Hitung Jumlah Ketidakhadiran (Sakit, Izin, Cuti yang disetujui)
        $ketidakhadiranCount = Absensi::where('user_id', $userId)
                                ->where('approval_status', 'approved')
                                ->whereIn('jenis_ketidakhadiran', ['cuti', 'sakit', 'izin'])
                                ->count();

        // Hitung Jumlah Tugas (use divisi_id when available)
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

        // Cek apakah karyawan menjadi penanggung jawab project + ringkasan detail
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

            Log::info('GM Dashboard Check', [
                'user_id' => $userId,
                'user_role' => $userRole,
                'user_divisi' => $userDivisi,
                'user_divisi_id' => $userDivisiId,
                'count_pending_manual' => $countPendingManual,
                'count_cuti_table' => $countPendingCuti,
                'final_total' => $roleBasedData['pendingApprovals']
            ]);

        } elseif ($userRole === 'manager') {
            if ($userDivisiId) {
                // Count team members by users.divisi_id
                $roleBasedData['teamMembers'] = Karyawan::whereHas('user', function($q) use ($userDivisiId) {
                    $q->where('divisi_id', $userDivisiId);
                })->count();

                // Pending approvals for users in the same divisi
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

        $userId = Auth::id();

        // Ambil tanggal untuk kalender (tidak pakai JSON fetch lagi)
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
     * API: Daftar project yang ditanggung jawab karyawan
     */
    public function getPenanggungProjects(Request $request)
    {
        try {
            $userId = Auth::id();
            $projects = Project::with(['penanggungJawab', 'karyawanPenanggungJawab'])
                ->assignedToKaryawan($userId)
                ->orderBy('id', 'desc')
                ->get([
                    'id',
                    'nama',
                    'deskripsi',
                    'status_pengerjaan',
                    'status_kerjasama',
                    'progres',
                    'tanggal_mulai_pengerjaan',
                    'tanggal_selesai_pengerjaan',
                    'tanggal_mulai_kerjasama',
                    'tanggal_selesai_kerjasama'
                ]);

            return response()->json([
                'success' => true,
                'data' => $projects
            ]);
        } catch (\Exception $e) {
            Log::error('Error getPenanggungProjects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data project'
            ], 500);
        }
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

        // Hitung statistik - SESUAIKAN DENGAN STRUKTUR TABEL

        // 1. Total Hadir = ada jam_masuk DAN approval_status = approved
        $totalHadir = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->whereNotNull('jam_masuk') // ADA CHECK-IN
            ->where('approval_status', 'approved') // DISETUJUI
            ->count();

        // 2. Total Izin = jenis_ketidakhadiran = 'izin' DAN approval_status = approved
        $totalIzin = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'izin')
            ->where('approval_status', 'approved')
            ->count();

        // 3. Total Sakit
        $totalSakit = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'sakit')
            ->where('approval_status', 'approved')
            ->count();

        // 4. Total Cuti
        $totalCuti = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'cuti')
            ->where('approval_status', 'approved')
            ->count();

        // 5. Total Dinas Luar
        $totalDinasLuar = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('jenis_ketidakhadiran', 'dinas-luar')
            ->where('approval_status', 'approved')
            ->count();

        // 6. Total Alpha = tidak ada data absensi sama sekali untuk hari kerja
        // (Perlu logika khusus)

        // Return appropriate view based on user role
        if ($user->role === 'hr') {
            // Prepare default values so view always renders even if query fails
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

            // Gunakan rentang hari WIB agar konsisten untuk kolom tanggal bertipe datetime (UTC di DB)
            $todayStartUtc = Carbon::today('Asia/Jakarta')->startOfDay()->utc();
            $todayEndUtc = Carbon::today('Asia/Jakarta')->endOfDay()->utc();

            // Prepare full lists for HR view (today's data only, confirmed submissions)
            try {
                $users = User::where('role', 'karyawan')->get();
                $userIds = $users->pluck('id')->toArray();
                $totalKaryawan = count($userIds);

                // Fetch all attendance records for today (must have jam_masuk)
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

                    // Prioritaskan keterangan manual, fallback ke alasan pulang duluan bila ada
                    $keteranganValue = $absen->keterangan ?? $absen->reason ?? null;
                    if (empty($keteranganValue) && !empty($absen->is_early_checkout) && !empty($absen->early_checkout_reason)) {
                        $keteranganValue = $absen->early_checkout_reason;
                    }
                    
                    // Determine status: terlambat or tepat waktu (samakan dengan riwayat karyawan)
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

                // Fetch ketidakhadiran (sakit + izin) for today - EXCLUDE pending - only confirmed
                $ketidakhadiranRaw = Absensi::with('user')
                    ->whereIn('user_id', $userIds)
                    ->whereBetween('tanggal', [$todayStartUtc, $todayEndUtc])
                    ->whereIn('jenis_ketidakhadiran', ['sakit', 'izin'])
                    // Include pending and approved so HR can see incoming requests
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Format ketidakhadiran data for view
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
                        // Untuk daftar ketidakhadiran HR, tampilkan alasan izin/sakit (field reason) sebagai prioritas
                        'keterangan' => $item->reason ?? $item->keterangan ?? null,
                        'approval_status' => $item->approval_status ?? 'pending',
                    ]);
                    
                    // Track user IDs by type
                    if ($item->jenis_ketidakhadiran === 'sakit') {
                        $sakit_UserIds[] = $item->user_id;
                    } elseif ($item->jenis_ketidakhadiran === 'izin') {
                        $izin_UserIds[] = $item->user_id;
                    }
                }
                
                // Get unique IDs
                $sakit_UserIds = array_unique($sakit_UserIds);
                $izin_UserIds = array_unique($izin_UserIds);

                // DEBUG: Log counts and sample items to help debugging missing 'izin'
                try {
                    \Log::info('DEBUG ketidakhadiran summary', [
                        'total_raw' => count($ketidakhadiranRaw),
                        'sakit_count' => count($sakit_UserIds),
                        'izin_count' => count($izin_UserIds),
                        'sample_raw_types' => array_map(function($it){ return $it->jenis_ketidakhadiran ?? null; }, array_slice($ketidakhadiranRaw->toArray(), 0, 10)),
                        'sample_raw_ids' => array_map(function($it){ return $it->id ?? null; }, array_slice($ketidakhadiranRaw->toArray(), 0, 10)),
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to log ketidakhadiran debug: ' . $e->getMessage());
                }

                // Fetch Cuti data overlapping this month (include pending and approved)
                $cutiRaw = Cuti::with(['user', 'user.divisionDetail'])
                    ->whereIn('user_id', $userIds)
                    // Overlap check: cuti that start before or on end of month AND end after or on start of month
                    ->whereDate('tanggal_mulai', '<=', $endOfMonth)
                    ->whereDate('tanggal_selesai', '>=', $startOfMonth)
                    ->whereIn('status', ['disetujui', 'menunggu', 'pending'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Format cuti data for view
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
                
                // Calculate statistics
                $hadiranUserIds = array_unique($attendanceUserIds);
                $hadiranCount = count($hadiranUserIds);
                $sakitCount = count($sakit_UserIds);
                $izinCount = count($izin_UserIds);
                $cutiCount = count($cutiUserIds);
                
                // Tidak Hadir = Total - (Hadir + Sakit + Izin + Cuti)
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
                // Pass today's statistics (pre-calculated in controller)
                'totalKaryawan' => $totalKaryawan,
                'hadiranCount' => $hadiranCount,
                'sakitCount' => $sakitCount,
                'izinCount' => $izinCount,
                'cutiCount' => $cutiCount,
                'tidakHadirCount' => $tidakHadirCount,
                // Also pass raw id lists to help client-side non-attendee computation
                'presentIds' => $hadiranUserIds,
                'sakitIds' => $sakit_UserIds,
                'izinIds' => $izin_UserIds,
                'cutiIds' => $cutiUserIds,
                'allUsers' => $users->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                    ];
                })->values(),
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
            // Determine divisi id and name
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

            Log::info('=== KARYAWAN TUGAS LIST ===', [
                'controller' => 'KaryawanController@listPage',
                'user_id' => $userId,
                'user_name' => $userName,
                'user_divisi' => $userDivisi,
                'user_divisi_id' => $userDivisiId,
                'role' => $user->role,
                'timestamp' => now()->toDateTimeString(),
            ]);

            // PERBAIKAN UTAMA: Query yang lebih komprehensif
            $tasks = Task::where(function ($query) use ($userId, $userDivisiId) {
                // 1. Tugas untuk user
                $query->where('assigned_to', $userId);

                // 2. Tugas untuk divisi (by id)
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
            // PERBAIKAN KRUSIAL: Hanya select id dan name dari relasi User
            // Jangan select 'divisi' karena tabel users tidak punya kolom itu
            ->with([
                'creator:id,name',
                'assignee:id,name', 
                'targetManager:id,name'
            ])
            ->orderBy('deadline', 'asc')
            ->get();

            // Jika view butuh nama divisi dari assignee, kita ambil manual
            // Option A: Loop tambah data (Cara ini aman untuk view blade)
            $tasks->transform(function ($task) {
                if ($task->assignee) {
                    // Cari data karyawan berdasarkan user_id assignee
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

    /**
     * Mengambil status absensi hari ini.
     * PERBAIKAN: Format response sesuai harapan Frontend { success: true, data: {...} }
     */
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
                Log::debug('getTodayStatusApi status check', [
                    'raw_late_minutes' => $rawLateMin,
                    'late_minutes' => $data['late_minutes'],
                    'is_terlambat' => $isLate,
                    'status' => $data['status']
                ]);
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

        // Cek status cuti
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

    /**
     * Mengambil riwayat absensi.
     * PERBAIKAN PENTING:
     * 1. Format JSON { success: true, data: [...] } agar frontend membaca.
     * 2. Nama Key disesuaikan (jam_masuk, jam_pulang) sesuai frontend.
     */
    public function getHistory(Request $request)
    {
        $query = Absensi::where('user_id', Auth::id());
        
        // Filter Logic sesuai frontend JS
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
            // Default: Bulan Ini
            $query->whereMonth('tanggal', '=', date('m'))
                  ->whereYear('tanggal', '=', date('Y'));
        }

        $history = $query->orderBy('tanggal', 'desc')->get();

        $formattedData = $history->map(function ($item) {
            // Tentukan status
            $status = 'Tidak Hadir';
            $lateMinutes = 0;

            if ($item->jam_masuk) {
                // Use late_minutes from database if available
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
                'tanggal' => $item->tanggal, // Tanggal string mentah
                'date' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'), // Format label
                'jam_masuk' => $item->jam_masuk, // Key disamakan dengan frontend
                'jam_pulang' => $item->jam_pulang, // Key disamakan dengan frontend
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
                'is_on_leave' => false // Akan dihandle di level view/logika jika perlu
            ];
        })->all();

        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);
    }

    /**
     * Mengambil data untuk dashboard karyawan via API.
     */
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

            // Get today's attendance status
            $absenHariIni = Absensi::where('user_id', $userId)
                ->where('tanggal', $today)
                ->first();

            // Count statistics
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
            Log::error('Stack trace: ' . $e->getTraceAsString());
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

    /**
     * API: Mengambil data dashboard untuk karyawan (endpoint baru untuk /api/karyawan/dashboard-data)
     * (Menggantikan duplikat method sebelumnya)
     */
    /**
 * API: Dashboard Data
 */
public function getDashboardDataApi()
{
    try {
        $userId = Auth::id();
        $today = now()->toDateString();
        $todayRecord = Absensi::where('user_id', $userId)->where('tanggal', $today)->first();

        // Menghitung data bulanan
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        
        $monthlyRecords = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$monthStart, $monthEnd])
            ->get();

        // Inisialisasi variabel bulanan
        $totalHadir = 0;
        $totalTerlambat = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAbsen = 0;
        $totalCuti = 0;

        // Hitung data bulanan
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

        // Menghitung data mingguan
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();
        
        $weeklyRecords = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$weekStart, $weekEnd])
            ->get();

        // Inisialisasi variabel mingguan
        $weeklyHadir = 0;
        $weeklyTerlambat = 0;
        $weeklyIzin = 0;
        $weeklySakit = 0;
        $weeklyAbsen = 0;
        $weeklyCuti = 0;

        // Hitung data mingguan
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

        // Menghitung sisa cuti
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

/**
 * Helper: Menghitung sisa cuti
 */
private function getCutiSaldo($userId)
{
    try {
        $karyawan = Karyawan::where('user_id', $userId)->first();
        if (!$karyawan) return 0;

        // Ambil kuota cuti tahunan
        $cutiKuota = CutiKuota::where('karyawan_id', $karyawan->id)
            ->where('tahun', now()->year)
            ->first();

        $totalKuota = $cutiKuota ? $cutiKuota->jumlah_hari : 12; // Default 12 hari

        // Hitung cuti yang sudah diambil
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

    /**
     * API: Mengambil riwayat absensi (untuk frontend)
     */
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

    /**
     * API: Mengambil status pengajuan (Pending & Recent)
     */
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

    /**
     * API: Proses absen masuk via AJAX
     */
    public function absenMasukApi(Request $request)
    {
        try {
            $user = Auth::user();
            $nowWIB = now('Asia/Jakarta');
            $today = $nowWIB->toDateString();

            // 0. Validasi: Cek apakah jam sekarang sudah mencapai jam kerja mulai (START_TIME)
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

            // Parse start time
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

            // 1. Cek apakah user sedang cuti hari ini
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

            // 2. Cek apakah sudah ada pengajuan ketidakhadiran yang disetujui
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
                    'jam_masuk' => $nowWIB->toTimeString(), // Tambahkan jam_masuk
                    'late_minutes' => $lateMinutes,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Absen Masuk Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    /**
     * API: Proses absen pulang via AJAX
     */
    public function absenPulangApi(Request $request)
    {
        try {
            $user = Auth::user();
            $today = now()->toDateString();

            // 1. Cek apakah user sedang cuti hari ini
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

    /**
     * API: Proses pengajuan izin
     */
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

            // Cek apakah sudah ada cuti yang disetujui di tanggal ini
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

    /**
     * API: Proses pengajuan dinas luar
     */
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

            // Cek apakah sudah ada cuti yang disetujui di tanggal ini
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

    /**
     * API: Mendapatkan Tugas (JSON)
     */
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
            // PERBAIKAN: Hanya select id & name, hindari kolom divisi di tabel users
            ->with(['creator:id,name', 'assignee:id,name'])
            ->orderBy('deadline', 'asc')
            ->get();

            $transformedTasks = $tasks->map(function ($task) {
                // Ambil divisi secara manual jika diperlukan
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
                    'assignee_divisi' => $assigneeDivisi, // Tambahan data divisi
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

    /**
     * API: Mengambil tanggal-tanggal yang memiliki meeting notes
     */
    public function getMeetingNotesDatesApi()
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            
            if (!Schema::hasTable('catatan_rapats')) {
                return response()->json(['success' => true, 'dates' => []]);
            }
            
            // Use `tanggal` column from CatatanRapat model
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

    /**
     * API: Mengambil meeting notes untuk tanggal tertentu
     */
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

    /**
     * API untuk mendapatkan daftar tanggal yang memiliki catatan rapat
     */
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

    /**
     * API untuk mendapatkan catatan rapat berdasarkan tanggal
     */
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

    /**
     * API: Mengambil tanggal-tanggal yang memiliki pengumuman
     */
    public function getAnnouncementDatesApi()
    {
        try {
            if (!auth()->check()) return response()->json(['success' => false], 401);
            if (!Schema::hasTable('pengumuman')) return response()->json(['success' => true, 'dates' => []]);
            
            // Use created_at for announcement dates
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

    /**
     * API: Mengambil daftar pengumuman
     */
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

    /**
     * API untuk mendapatkan pengumuman (Web View)
     */
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

    /**
     * API untuk mendapatkan daftar tanggal yang memiliki pengumuman
     */
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

    /**
     * API untuk mendapatkan pengumuman berdasarkan tanggal
     */
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

    /**
     * Get detail karyawan dengan file/dokumen
     */
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
            
            // Check if user is authorized to view this karyawan
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
            
            // Get files if relationship exists
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

    /**
     * Get task acceptance status
     * NOTE: Dengan design baru, setiap task hanya punya 1 assignee
     * Method ini untuk backward compatibility dengan old multi-assign tasks
     */
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

            // Validasi bahwa user adalah yang ditugaskan
            $isAssigned = $task->assigned_to == $user->id || 
                         (is_array($task->assigned_to_ids) && in_array($user->id, $task->assigned_to_ids));
            
            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melihat status penerimaan tugas ini'
                ], 403);
            }

            // Jika task hanya punya 1 assignee (new design), return simple response
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

            // Untuk old multi-assign tasks: initialize jika belum ada
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

    /**
     * Accept task - mengubah status dari pending menjadi proses
     */
    public function acceptTask(Request $request, $taskId)
    {
        try {
            $user = Auth::user();
            
            // Validasi user
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            // Cari task
            $task = Task::find($taskId);
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas tidak ditemukan'
                ], 404);
            }

            // Validasi bahwa user adalah yang ditugaskan
            $isAssigned = $task->assigned_to == $user->id || 
                         (is_array($task->assigned_to_ids) && in_array($user->id, $task->assigned_to_ids));
            
            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menerima tugas ini'
                ], 403);
            }

            // NEW DESIGN: Setiap task hanya punya 1 assignee
            // Jadi langsung ubah status menjadi proses tanpa menunggu assignee lain
            $task->status = 'proses';
            $task->save();

            // OPTIONAL: Jika ada assigned_to_ids (old multi-assign task), track dengan task_acceptances
            // Untuk backward compatibility
            if ($task->assigned_to_ids && is_array($task->assigned_to_ids) && count($task->assigned_to_ids) > 1) {
                // Initialize jika belum ada
                if (!$task->acceptances()->exists()) {
                    $this->initializeTaskAcceptances($task);
                }

                // Update status karyawan ini
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

                // Check apakah semua sudah accept
                $acceptanceStatus = $task->getAcceptanceStatus();
                if (!$acceptanceStatus['is_fully_accepted']) {
                    // Update message jika belum semua accept
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

    // Helper method untuk inisialisasi task acceptances
    private function initializeTaskAcceptances($task)
    {
        // Cek apakah sudah ada acceptance records
        if ($task->acceptances()->exists()) {
            return;
        }

        // Get list of assignees
        $assignees = [];
        
        if ($task->assigned_to) {
            $assignees[] = $task->assigned_to;
        }
        
        if ($task->assigned_to_ids && is_array($task->assigned_to_ids)) {
            $assignees = array_merge($assignees, $task->assigned_to_ids);
        }

        // Remove duplicates
        $assignees = array_unique($assignees);

        // Create acceptance records untuk setiap assignee
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