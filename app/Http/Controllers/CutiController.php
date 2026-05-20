<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\Absensi;
use App\Models\CutiHistory;
use App\Models\CutiQuota;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CutiController extends Controller
{
    protected $user;
    protected $currentRole;
    protected $currentDivisiName;

    public function __construct()
    {
        // Remove generic middleware('auth') - let route middleware handle it
        // $this->middleware('auth');
        
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->currentRole = $this->user->role ?? null;
            
            // Ambil nama divisi dari relasi divisionDetail
            if ($this->user && $this->user->divisionDetail) {
                $this->currentDivisiName = $this->user->divisionDetail->divisi;
            } else {
                $this->currentDivisiName = null;
            }
            
            if (!$this->user) {
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'User tidak terautentikasi'], 401);
                }
                abort(401, 'User tidak terautentikasi');
            }
            
            return $next($request);
        });
    }

    // ============================================
    // INDEX ROUTING
    // ============================================

    public function index(Request $request)
    {
        try {
            Log::info('Cuti index accessed', ['role' => $this->currentRole]);

            if (!$this->currentRole) {
                return response()->json(['success' => false, 'message' => 'Role tidak ditemukan'], 403);
            }

            switch ($this->currentRole) {
                case 'karyawan':
                    return $this->indexKaryawan($request);
                case 'manager_divisi':
                    return $this->indexManagerDivisi($request);
                case 'general_manager':
                    return $this->indexGeneralManager($request);
                case 'admin':
                    return $this->indexAdmin($request);
                case 'owner':
                case 'finance':
                    return $this->indexOwner($request);
                default:
                    return response()->json(['success' => false, 'message' => 'Role tidak dikenali: ' . $this->currentRole], 403);
            }
        } catch (\Exception $e) {
            Log::error('Cuti index error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    public function indexKaryawan(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getData($request);
        }
        return view('karyawan.cuti');
    }

    public function indexManagerDivisi(Request $request)
    {
        try {
            if (!$this->currentDivisiName) {
                return response()->json(['success' => false, 'message' => 'User tidak memiliki divisi'], 400);
            }
            
            $divisiName = $this->currentDivisiName;
            $statusFilter = $request->get('status', 'all');
            $search = $request->get('search', '');
            
            $query = Cuti::whereHas('user.divisionDetail', function ($query) use ($divisiName) {
                $query->where('divisi', $divisiName);
            })->with(['user:id,name,divisi_id,sisa_cuti,email', 'user.divisionDetail']);
            
            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      });
                });
            }
            
            $cuti = $query->orderBy('created_at', 'desc')->get();
            
            $total = (clone $query)->count();
            $menunggu = (clone $query)->where('status', 'menunggu')->count();
            $disetujui = (clone $query)->where('status', 'disetujui')->count();
            $ditolak = (clone $query)->where('status', 'ditolak')->count();
            
            $karyawanDivisi = User::whereHas('divisionDetail', function($q) use ($divisiName) {
                                    $q->where('divisi', $divisiName);
                                })
                                ->where('role', 'karyawan')
                                ->orderBy('name')
                                ->get(['id', 'name', 'divisi_id', 'sisa_cuti', 'email']);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $cuti->map(function($item) {
                        return $this->formatCutiData($item);
                    }),
                    'stats' => [
                        'total' => $total,
                        'menunggu' => $menunggu,
                        'disetujui' => $disetujui,
                        'ditolak' => $ditolak
                    ],
                    'karyawan' => $karyawanDivisi
                ]);
            }
            
            return view('manager_divisi.cuti', compact(
                'cuti',
                'total',
                'menunggu',
                'disetujui',
                'ditolak',
                'divisiName',
                'karyawanDivisi'
            ));
        } catch (\Exception $e) {
            Log::error('Manager Index Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat data manager'], 500);
        }
    }

    public function indexGeneralManager(Request $request)
    {
        try {
            $statusFilter = $request->get('status', 'all');
            $divisiFilter = $request->get('divisi', 'all');
            $search = $request->get('search', '');
            
            $query = Cuti::with(['user:id,name,divisi_id,sisa_cuti,email', 'user.divisionDetail', 'disetujuiOleh:id,name']);
            
            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
            
            if ($divisiFilter !== 'all') {
                $query->whereHas('user.divisionDetail', function($q) use ($divisiFilter) {
                    $q->where('divisi', $divisiFilter);
                });
            }
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhere('jenis_cuti', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                          $q2->orWhereHas('divisionDetail', function($qDiv) use ($search) {
                              $qDiv->where('divisi', 'like', "%{$search}%");
                          });
                      });
                });
            }
            
            $cuti = $query->orderBy('created_at', 'desc')->paginate(10);
            
            $divisiList = Divisi::orderBy('divisi', 'asc')->pluck('divisi')->toArray();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $cuti->map(function($item) {
                        return $this->formatCutiData($item);
                    }),
                    'pagination' => [
                        'current_page' => $cuti->currentPage(),
                        'last_page' => $cuti->lastPage(),
                        'per_page' => $cuti->perPage(),
                        'total' => $cuti->total(),
                        'from' => $cuti->firstItem(),
                        'to' => $cuti->lastItem()
                    ],
                    'filters' => [
                        'divisi_list' => $divisiList
                    ]
                ]);
            }
            
            return view('general_manajer.acc_cuti', compact('cuti', 'divisiList'));
        } catch (\Exception $e) {
            Log::error('GM Index Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memuat data GM', 'error' => $e->getMessage()], 500);
            }
            abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function indexAdmin(Request $request) { 
        return $this->indexGeneralManager($request); 
    }
    
    public function indexOwner(Request $request) { 
        return $this->indexGeneralManager($request); 
    }

    // ============================================
    // CREATE METHOD
    // ============================================

    public function create()
    {
        try {
            if ($this->currentRole !== 'karyawan') {
                abort(403, 'Akses ditolak');
            }
            
            $user = $this->user;
            
            $quotaInfo = [];
            try {
                $quotaInfo = CutiQuota::getUserQuota($user->id, date('Y'));
            } catch (\Exception $e) {
                Log::warning('Failed to get quota info: ' . $e->getMessage());
                $quotaInfo = [
                    'tahun' => date('Y'),
                    'quota_tahunan' => 12,
                    'terpakai' => 0,
                    'sisa' => 12,
                    'quota_khusus' => 0,
                    'terpakai_khusus' => 0
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'divisi' => $user->divisionDetail ? $user->divisionDetail->divisi : '-',
                        'sisa_cuti' => (int)($user->sisa_cuti ?? 0)
                    ],
                    'quota_info' => $quotaInfo,
                    'jenis_cuti_options' => [
                        ['value' => 'tahunan', 'label' => 'Cuti Tahunan'],
                        ['value' => 'melahirkan', 'label' => 'Cuti Melahirkan'],
                        ['value' => 'duka', 'label' => 'Cuti Duka'],
                        ['value' => 'izin-khusus', 'label' => 'Cuti Izin Khusus'],
                        ['value' => 'tanpa-gaji', 'label' => 'Cuti Tanpa Gaji']
                    ],
                    'min_date' => Carbon::now()->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat form cuti'], 500);
        }
    }

    // ============================================
    // GET DATA API
    // ============================================

    public function getData(Request $request)
    {
        try {
            $query = Cuti::query();
            $query->with([
                'user:id,name,divisi_id,sisa_cuti,email',
                'user.divisionDetail',
                'disetujuiOleh:id,name'
            ]);

            switch ($this->currentRole) {
                case 'karyawan':
                    $query->where('user_id', $this->user->id);
                    break;
                case 'manager_divisi':
                    if (!$this->currentDivisiName) {
                        return response()->json(['success' => false, 'message' => 'User tidak memiliki divisi'], 400);
                    }
                    $query->whereHas('user.divisionDetail', function ($q) {
                         $q->where('divisi', $this->currentDivisiName);
                    });
                    break;
                case 'general_manager':
                case 'admin':
                case 'owner':
                case 'finance':
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Role tidak dikenali'], 403);
            }
            
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            if ($request->has('jenis_cuti') && $request->jenis_cuti !== 'all') {
                $query->where('jenis_cuti', $request->jenis_cuti);
            }
            
            if (in_array($this->currentRole, ['general_manager', 'admin', 'owner', 'finance']) && 
                $request->has('divisi') && $request->divisi !== 'all') {
                $query->whereHas('user.divisionDetail', function ($q) use ($request) {
                    $q->where('divisi', $request->divisi);
                });
            }
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhere('jenis_cuti', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                          $q->orWhereHas('divisionDetail', function($qDiv) use ($search) {
                              $qDiv->where('divisi', 'like', "%{$search}%");
                          });
                      });
                });
            }
            
            if ($request->has('tanggal_mulai')) {
                $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
            }
            if ($request->has('tanggal_selesai')) {
                $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
            }
            
            $query->orderBy('created_at', 'desc');
            $perPage = $request->get('per_page', 10);
            $cuti = $query->paginate($perPage);
            
            $formattedData = $cuti->map(function ($item) {
                return $this->formatCutiData($item);
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'pagination' => [
                    'total' => $cuti->total(),
                    'per_page' => $cuti->perPage(),
                    'current_page' => $cuti->currentPage(),
                    'last_page' => $cuti->lastPage(),
                    'from' => $cuti->firstItem(),
                    'to' => $cuti->lastItem()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getData Cuti: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengambil data.',
                'error' => env('APP_DEBUG') ? [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    // ============================================
    // HELPER: FORMAT CUTI DATA
    // ============================================

    private function formatCutiData($item)
    {
        try {
            $userName = 'Unknown';
            $userDivisi = '-';
            $userEmail = null;
            $sisaCuti = 0;
            
            if ($item->user) {
                $userName = $item->user->name ?? 'Unknown';
                if ($item->user->relationLoaded('divisionDetail') && $item->user->divisionDetail) {
                    $userDivisi = $item->user->divisionDetail->divisi;
                } else {
                    $userDivisi = $item->user->divisi ?? '-'; 
                }
                
                $userEmail = $item->user->email ?? null;
                $sisaCuti = (int)($item->user->sisa_cuti ?? 0);
            }
            
            $tMulai = '-';
            $tSelesai = '-';
            $periode = '-';
            
            if ($item->tanggal_mulai) {
                try {
                    $tMulai = Carbon::parse($item->tanggal_mulai)->translatedFormat('d F Y');
                } catch (\Exception $e) { $tMulai = '-'; }
            }
            
            if ($item->tanggal_selesai) {
                try {
                    $tSelesai = Carbon::parse($item->tanggal_selesai)->translatedFormat('d F Y');
                } catch (\Exception $e) { $tSelesai = '-'; }
            }
            
            if ($item->tanggal_mulai && $item->tanggal_selesai) {
                try {
                    $start = Carbon::parse($item->tanggal_mulai)->format('d/m/Y');
                    $end = Carbon::parse($item->tanggal_selesai)->format('d/m/Y');
                    $periode = $start . ' - ' . $end;
                } catch (\Exception $e) { $periode = '-'; }
            }
            
            $jenisMap = [
                'tahunan' => 'Cuti Tahunan',
                'melahirkan' => 'Cuti Melahirkan',
                'duka' => 'Cuti Duka',
                'izin-khusus' => 'Cuti Izin Khusus',
                'tanpa-gaji' => 'Cuti Tanpa Gaji',
                // Backward compatibility for legacy data
                'sakit' => 'Cuti Sakit',
                'penting' => 'Cuti Penting',
                'lainnya' => 'Cuti Lainnya',
            ];
            $jenisText = $jenisMap[$item->jenis_cuti] ?? 'Cuti Lainnya';
            
            $statusLabels = [
                'menunggu' => 'Menunggu Persetujuan',
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'dibatalkan' => 'Dibatalkan',
            ];
            $statusLabel = $statusLabels[$item->status] ?? ucfirst($item->status);
            
            $statusColors = [
                'menunggu' => 'warning',
                'disetujui' => 'success',
                'ditolak' => 'danger',
                'dibatalkan' => 'secondary',
            ];
            $statusColor = $statusColors[$item->status] ?? 'secondary';
            
            $disetujuiOleh = null;
            $disetujuiPada = null;
            
            if ($item->disetujuiOleh) {
                $disetujuiOleh = $item->disetujuiOleh->name;
            }
            
            if ($item->disetujui_pada) {
                try {
                    $disetujuiPada = Carbon::parse($item->disetujui_pada)->format('d F Y H:i');
                } catch (\Exception $e) {
                    $disetujuiPada = null;
                }
            }
            
            $createdAt = '-';
            if ($item->created_at) {
                try {
                    $createdAt = Carbon::parse($item->created_at)->format('d F Y H:i');
                } catch (\Exception $e) {
                    $createdAt = '-';
                }
            }
            
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'nama' => $userName,
                'email' => $userEmail,
                'divisi' => $userDivisi,
                'keterangan' => $item->keterangan ?? '-',
                'jenis_cuti' => $jenisText,
                'jenis_cuti_kode' => $item->jenis_cuti,
                'tanggal_mulai' => $item->tanggal_mulai ? Carbon::parse($item->tanggal_mulai)->format('Y-m-d') : '-',
                'tanggal_selesai' => $item->tanggal_selesai ? Carbon::parse($item->tanggal_selesai)->format('Y-m-d') : '-',
                'tanggal_mulai_formatted' => $tMulai,
                'tanggal_selesai_formatted' => $tSelesai,
                'periode' => $periode,
                'durasi' => $item->durasi ?? 0,
                'status' => $item->status,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'sisa_cuti_karyawan' => $sisaCuti,
                'sisa_cuti_sebelum' => $item->sisa_cuti_sebelum ?? null,
                'sisa_cuti_sesudah' => $item->sisa_cuti_sesudah ?? null,
                'disetujui_oleh' => $disetujuiOleh,
                'disetujui_pada' => $disetujuiPada,
                'catatan_penolakan' => $item->catatan_penolakan ?? null,
                'catatan_pembatalan' => $item->catatan_pembatalan ?? null,
                'created_at' => $createdAt,
                'dapat_disetujui' => $item->status === 'menunggu',
                'dapat_diubah' => $item->status === 'menunggu' && 
                                  ($this->currentRole === 'karyawan' ? $item->user_id === $this->user->id : true),
                'dapat_dihapus' => $item->status === 'menunggu' && 
                                   ($this->currentRole === 'karyawan' ? $item->user_id === $this->user->id : true),
                'dapat_lihat' => true,
                'dapat_batalkan' => $item->status === 'disetujui' && 
                                    ($this->currentRole !== 'karyawan' || $item->user_id === $this->user->id),
                'is_overlapping' => method_exists($item, 'isOverlapping') ? $item->isOverlapping() : false,
                'overlap_warning' => (method_exists($item, 'isOverlapping') && $item->isOverlapping()) ? 'Cuti ini bertabrakan dengan cuti lain yang sudah disetujui' : null
            ];
        } catch (\Exception $e) {
            Log::error('Error formatCutiData: ' . $e->getMessage());
            return [
                'id' => $item->id ?? 0,
                'user_id' => $item->user_id ?? 0,
                'nama' => 'Error',
                'divisi' => '-',
                'status' => 'error',
                'status_label' => 'Error',
                'status_color' => 'secondary',
                'created_at' => '-',
                'dapat_disetujui' => false,
                'dapat_diubah' => false,
                'dapat_dihapus' => false,
                'dapat_lihat' => true,
                'dapat_batalkan' => false
            ];
        }
    }

    // ============================================
    // STATS
    // ============================================

    public function stats()
    {
        try {
            $data = [];
            if (!$this->user || !$this->currentRole) {
                return response()->json(['success' => false, 'message' => 'User tidak terautentikasi'], 401);
            }

            switch ($this->currentRole) {
                case 'karyawan':
                    if (!$this->user) {
                        throw new \Exception('User tidak ditemukan');
                    }
                    
                    $userId = $this->user->id;
                    
                    $menunggu = Cuti::where('user_id', $userId)->where('status', 'menunggu')->count();
                    $disetujui = Cuti::where('user_id', $userId)->where('status', 'disetujui')->count();
                    $ditolak = Cuti::where('user_id', $userId)->where('status', 'ditolak')->count();
                    $dibatalkan = Cuti::where('user_id', $userId)->where('status', 'dibatalkan')->count();
                    $total = $menunggu + $disetujui + $ditolak + $dibatalkan;
                    
                    $sisaCuti = (int)($this->user->sisa_cuti ?? 0);
                    $cutiTahunan = 12;
                    $cutiTerpakai = 0;
                    
                    try {
                        $quota = CutiQuota::getUserQuota($userId, date('Y'));
                        $cutiTahunan = (int)($quota->quota_tahunan ?? 12);
                        $cutiTerpakai = (int)($quota->terpakai ?? 0);
                        $sisaCuti = (int)($quota->sisa ?? max(0, $cutiTahunan - $cutiTerpakai));

                        $quotaInfo = [
                            'tahun' => $quota->tahun,
                            'quota_tahunan' => $quota->quota_tahunan,
                            'terpakai' => $quota->terpakai,
                            'sisa' => $quota->sisa,
                            'quota_khusus' => $quota->quota_khusus ?? 0,
                            'terpakai_khusus' => $quota->terpakai_khusus ?? 0
                        ];
                    } catch (\Exception $e) {
                        Log::warning('Failed to get quota info: ' . $e->getMessage());
                        $cutiTerpakai = max(0, $cutiTahunan - $sisaCuti);
                        $quotaInfo = [
                            'tahun' => date('Y'),
                            'quota_tahunan' => 12,
                            'terpakai' => $cutiTerpakai,
                            'sisa' => $sisaCuti,
                            'quota_khusus' => 0,
                            'terpakai_khusus' => 0
                        ];
                    }
                    
                    $data = [
                        'menunggu' => $menunggu,
                        'disetujui' => $disetujui,
                        'ditolak' => $ditolak,
                        'dibatalkan' => $dibatalkan,
                        'total' => $total,
                        'sisa_cuti' => $sisaCuti,
                        'cuti_terpakai' => $cutiTerpakai,
                        'total_cuti_tahunan' => $cutiTahunan,
                        'quota_info' => $quotaInfo
                    ];
                    break;
                    
                case 'manager_divisi':
                    if (!$this->currentDivisiName) {
                        throw new \Exception('User tidak memiliki divisi');
                    }
                    
                    $baseQuery = Cuti::whereHas('user.divisionDetail', function ($query) {
                         $query->where('divisi', $this->currentDivisiName);
                    });
                    
                    $total = (clone $baseQuery)->count();
                    $menunggu = (clone $baseQuery)->where('status', 'menunggu')->count();
                    $disetujui = (clone $baseQuery)->where('status', 'disetujui')->count();
                    $ditolak = (clone $baseQuery)->where('status', 'ditolak')->count();
                    $dibatalkan = (clone $baseQuery)->where('status', 'dibatalkan')->count();
                    
                    $totalKaryawan = User::whereHas('divisionDetail', function($q) {
                                        $q->where('divisi', $this->currentDivisiName);
                                    })
                                    ->where('role', 'karyawan')
                                    ->count();
                    
                    $data = [
                        'total_pengajuan' => $total,
                        'menunggu' => $menunggu,
                        'disetujui' => $disetujui,
                        'ditolak' => $ditolak,
                        'total_karyawan' => $totalKaryawan
                    ];
                    break;
                    
                case 'general_manager':
                case 'admin':
                case 'owner':
                case 'finance':
                    $menunggu = Cuti::where('status', 'menunggu')->count();
                    $disetujui = Cuti::where('status', 'disetujui')->count();
                    $ditolak = Cuti::where('status', 'ditolak')->count();
                    $dibatalkan = Cuti::where('status', 'dibatalkan')->count();
                    $total = $menunggu + $disetujui + $ditolak + $dibatalkan;
                    
                    $data = [
                        'total' => $total,
                        'menunggu' => $menunggu,
                        'disetujui' => $disetujui,
                        'ditolak' => $ditolak,
                        'dibatalkan' => $dibatalkan
                    ];
                    break;
                    
                default:
                    return response()->json(['success' => false, 'message' => 'Role tidak didukung'], 403);
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengambil statistik',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ============================================
    // GET QUOTA INFO
    // ============================================

    public function getQuotaInfo(Request $request)
    {
        try {
            $userId = $request->get('user_id') ?? $this->user->id;
            $year = $request->get('year') ?? date('Y');
            
            if ($this->currentRole === 'karyawan' && $userId !== $this->user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }
            
            $quota = CutiQuota::getUserQuota($userId, $year);
            
            $cutiTahunan = Cuti::where('user_id', $userId)
                ->where('jenis_cuti', 'tahunan')
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $year)
                ->sum('durasi');
            
            $cutiSakit = Cuti::where('user_id', $userId)
                ->where('jenis_cuti', 'duka')
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $year)
                ->count();
            
            $cutiPenting = Cuti::where('user_id', $userId)
                ->where('jenis_cuti', 'izin-khusus')
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $year)
                ->count();
            
            $cutiMelahirkan = Cuti::where('user_id', $userId)
                ->where('jenis_cuti', 'melahirkan')
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $year)
                ->count();
            
            $cutiLainnya = Cuti::where('user_id', $userId)
                ->where('jenis_cuti', 'tanpa-gaji')
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $year)
                ->count();
            
            $cutiMenunggu = Cuti::where('user_id', $userId)
                ->where('status', 'menunggu')
                ->whereYear('tanggal_mulai', $year)
                ->count();
            
            $userDivisiName = $user->divisionDetail ? $user->divisionDetail->divisi : '-';

            $data = [
                'quota' => [
                    'id' => $quota->id,
                    'tahun' => $quota->tahun,
                    'quota_tahunan' => $quota->quota_tahunan,
                    'terpakai' => $quota->terpakai,
                    'sisa' => $quota->sisa,
                    'quota_khusus' => $quota->quota_khusus ?? 0,
                    'terpakai_khusus' => $quota->terpakai_khusus ?? 0,
                    'sisa_khusus' => ($quota->quota_khusus ?? 0) - ($quota->terpakai_khusus ?? 0),
                    'total_terpakai' => $quota->terpakai + ($quota->terpakai_khusus ?? 0),
                    'persentase_penggunaan' => $quota->quota_tahunan > 0 
                        ? round(($quota->terpakai / $quota->quota_tahunan) * 100, 1) 
                        : 0,
                    'is_active' => $quota->is_active ?? true,
                    'is_reset' => $quota->is_reset ?? false,
                    'reset_at' => $quota->reset_at ? $quota->reset_at->format('d F Y H:i') : null,
                    'reset_by_name' => (isset($quota->resetBy) && $quota->resetBy) ? $quota->resetBy->name : null
                ],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'divisi' => $userDivisiName,
                    'sisa_cuti' => $user->sisa_cuti ?? 0,
                    'cuti_terpakai_tahun_ini' => $user->cuti_terpakai_tahun_ini ?? 0,
                    'cuti_reset_date' => $user->cuti_reset_date
                ],
                'statistics' => [
                    'cuti_tahunan_disetujui' => $cutiTahunan,
                    'cuti_sakit_disetujui' => $cutiSakit,
                    'cuti_penting_disetujui' => $cutiPenting,
                    'cuti_melahirkan_disetujui' => $cutiMelahirkan,
                    'cuti_lainnya_disetujui' => $cutiLainnya,
                    'cuti_menunggu' => $cutiMenunggu,
                    'total_disetujui' => $cutiTahunan + $cutiSakit + $cutiPenting + $cutiMelahirkan + $cutiLainnya,
                    'total_pengajuan' => $cutiTahunan + $cutiSakit + $cutiPenting + $cutiMelahirkan + $cutiLainnya + $cutiMenunggu
                ],
                'quota_usage_percentage' => $quota->quota_tahunan > 0 
                    ? round(($quota->terpakai / $quota->quota_tahunan) * 100, 1) 
                    : 0
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting quota info: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengambil informasi quota',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ============================================
    // STORE (CREATE)
    // ============================================

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->filled('jenis_cuti')) {
                $request->merge([
                    'jenis_cuti' => $this->normalizeJenisCutiInput($request->input('jenis_cuti'))
                ]);
            }

            $validated = $request->validate([
                'keterangan' => 'required|string|max:255',
                'jenis_cuti' => 'required|in:tahunan,melahirkan,duka,izin-khusus,tanpa-gaji',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'durasi' => 'required|integer|min:1'
            ]);
            
            if (!$this->user) {
                return response()->json(['success' => false, 'message' => 'Anda harus login'], 401);
            }
            
            $today = Carbon::today();
            $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
            
            if ($validated['jenis_cuti'] !== 'duka' && $tanggalMulai->lt($today)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cuti tidak bisa diajukan untuk tanggal yang sudah lewat'
                ], 400);
            }
            
            if ($validated['jenis_cuti'] === 'tahunan') {
                $currentYear = date('Y');
                $quota = CutiQuota::getUserQuota($this->user->id, $currentYear);
                
                if ($quota->sisa < $validated['durasi']) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Sisa cuti tidak mencukupi. Sisa: ' . $quota->sisa . ' hari'
                    ], 400);
                }
            }
            
            $overlapCuti = Cuti::where('user_id', $this->user->id)
                ->where('status', 'disetujui')
                ->where(function($query) use ($validated) {
                    $query->whereBetween('tanggal_mulai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                          ->orWhereBetween('tanggal_selesai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                          ->orWhere(function($q) use ($validated) {
                              $q->where('tanggal_mulai', '<=', $validated['tanggal_mulai'])
                                ->where('tanggal_selesai', '>=', $validated['tanggal_selesai']);
                          });
                })
                ->exists();
            
            if ($overlapCuti) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cuti ini bertabrakan dengan cuti lain yang sudah disetujui'
                ], 400);
            }
            
            $cuti = Cuti::create([
                'user_id' => $this->user->id,
                'keterangan' => $validated['keterangan'],
                'jenis_cuti' => $validated['jenis_cuti'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'durasi' => $validated['durasi'],
                'status' => 'menunggu'
            ]);
            
            CutiHistory::create([
                'cuti_id' => $cuti->id,
                'action' => 'created',
                'user_id' => $this->user->id,
                'changes' => json_encode($validated),
                'note' => 'Pengajuan cuti baru - ' . $validated['jenis_cuti']
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan cuti berhasil dikirim',
                'data' => $this->formatCutiData($cuti)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengajukan cuti: ' . $e->getMessage()], 500);
        }
    }

    // ============================================
    // SHOW METHOD
    // ============================================

    public function show($id) 
    {
        try {
            $cuti = Cuti::with(['user:id,name,divisi_id,sisa_cuti,email', 'user.divisionDetail', 'disetujuiOleh:id,name', 'dibatalkanOleh:id,name', 'histories.user:id,name'])->findOrFail($id);
            
            if ($this->currentRole === 'karyawan' && $cuti->user_id !== $this->user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            if ($this->currentRole === 'manager_divisi') {
                $userDivisiName = $cuti->user && $cuti->user->divisionDetail ? $cuti->user->divisionDetail->divisi : null;
                if (!$cuti->user || $userDivisiName !== $this->currentDivisiName) {
                    return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
                }
            }
            
            $histories = $cuti->histories->map(function($history) {
                return [
                    'action' => $history->action,
                    'action_label' => method_exists($history, 'getActionLabelAttribute') ? $history->action_label : ucfirst($history->action),
                    'note' => $history->note,
                    'user_name' => $history->user ? $history->user->name : 'System',
                    'created_at' => $history->created_at->format('d F Y H:i')
                ];
            });
            
            return response()->json([
                'success' => true, 
                'data' => array_merge(
                    $this->formatCutiData($cuti),
                    [
                        'histories' => $histories
                    ]
                )
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data cuti'], 500);
        }
    }

    // ============================================
    // EDIT METHOD
    // ============================================

    public function edit($id) 
    {
        try {
            $cuti = Cuti::with(['user:id,name,divisi_id,sisa_cuti,email', 'user.divisionDetail'])->findOrFail($id);
            
            if ($cuti->user_id !== $this->user->id && !in_array($this->currentRole, ['admin', 'general_manager'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            if ($cuti->status !== 'menunggu') {
                return response()->json(['success' => false, 'message' => 'Cuti tidak dapat diubah karena sudah diproses'], 400);
            }
            
            $quotaInfo = [];
            try {
                $quotaInfo = CutiQuota::getUserQuota($cuti->user_id, date('Y'));
            } catch (\Exception $e) {
                Log::warning('Failed to get quota info: ' . $e->getMessage());
                $quotaInfo = [
                    'tahun' => date('Y'),
                    'quota_tahunan' => 12,
                    'terpakai' => 0,
                    'sisa' => 12,
                    'quota_khusus' => 0,
                    'terpakai_khusus' => 0
                ];
            }
            
            $divisiName = $cuti->user->divisionDetail ? $cuti->user->divisionDetail->divisi : '-';

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $cuti->id,
                    'user_id' => $cuti->user_id,
                    'nama' => $cuti->user->name ?? 'Unknown',
                    'email' => $cuti->user->email ?? '',
                    'divisi' => $divisiName,
                    'keterangan' => $cuti->keterangan,
                    'jenis_cuti' => $cuti->jenis_cuti,
                    'jenis_cuti_kode' => $cuti->jenis_cuti,
                    'tanggal_mulai' => $cuti->tanggal_mulai ? Carbon::parse($cuti->tanggal_mulai)->format('Y-m-d') : '',
                    'tanggal_selesai' => $cuti->tanggal_selesai ? Carbon::parse($cuti->tanggal_selesai)->format('Y-m-d') : '',
                    'tanggal_mulai_formatted' => $cuti->tanggal_mulai ? Carbon::parse($cuti->tanggal_mulai)->format('d F Y') : '',
                    'tanggal_selesai_formatted' => $cuti->tanggal_selesai ? Carbon::parse($cuti->tanggal_selesai)->format('d F Y') : '',
                    'periode' => $cuti->tanggal_mulai ? Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') . ' - ' . Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') : '',
                    'durasi' => $cuti->durasi,
                    'status' => $cuti->status,
                    'status_label' => $this->getStatusLabel($cuti->status),
                    'status_color' => $this->getStatusColor($cuti->status),
                    'sisa_cuti_karyawan' => $cuti->user->sisa_cuti ?? 0,
                    'sisa_cuti_sebelum' => $cuti->sisa_cuti_sebelum ?? null,
                    'sisa_cuti_sesudah' => $cuti->sisa_cuti_sesudah ?? null,
                    'disetujui_oleh' => null,
                    'disetujui_pada' => null,
                    'dibatalkan_oleh' => null,
                    'dibatalkan_pada' => null,
                    'catatan_penolakan' => $cuti->catatan_penolakan ?? null,
                    'catatan_pembatalan' => $cuti->catatan_pembatalan ?? null,
                    'created_at' => $cuti->created_at ? Carbon::parse($cuti->created_at)->format('d F Y H:i') : '',
                    'dapat_disetujui' => false,
                    'dapat_diubah' => true,
                    'dapat_dihapus' => true,
                    'dapat_lihat' => true,
                    'dapat_batalkan' => false,
                    'is_overlapping' => false,
                    'overlap_warning' => null,
                    'quota_info' => $quotaInfo,
                    'jenis_cuti_options' => [
                        ['value' => 'tahunan', 'label' => 'Cuti Tahunan'],
                        ['value' => 'melahirkan', 'label' => 'Cuti Melahirkan'],
                        ['value' => 'duka', 'label' => 'Cuti Duka'],
                        ['value' => 'izin-khusus', 'label' => 'Cuti Izin Khusus'],
                        ['value' => 'tanpa-gaji', 'label' => 'Cuti Tanpa Gaji']
                    ],
                    'min_date' => Carbon::now()->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error edit cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data edit cuti'], 500);
        }
    }

    // ============================================
    // UPDATE METHOD (FLEKSIBEL)
    // ============================================

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cuti = Cuti::with('user')->findOrFail($id);
            
            // Cek hak akses
            if ($cuti->user_id !== $this->user->id && !in_array($this->currentRole, ['admin', 'general_manager'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            if ($cuti->status !== 'menunggu') {
                return response()->json(['success' => false, 'message' => 'Cuti tidak dapat diubah karena sudah diproses'], 400);
            }

            if ($request->filled('jenis_cuti')) {
                $request->merge([
                    'jenis_cuti' => $this->normalizeJenisCutiInput($request->input('jenis_cuti'))
                ]);
            }
            
            // VALIDASI FLEKSIBEL
            // Gunakan 'sometimes' agar field tidak wajib diisi
            $validated = $request->validate([
                'keterangan' => 'sometimes|string|max:255',
                'jenis_cuti' => 'sometimes|in:tahunan,melahirkan,duka,izin-khusus,tanpa-gaji',
                'tanggal_mulai' => 'sometimes|date',
                'tanggal_selesai' => 'sometimes|date|after_or_equal:tanggal_mulai',
                'durasi' => 'sometimes|integer|min:1'
            ]);
            
            // GABUNGKAN DATA INPUT DENGAN DATA LAMA
            // Jika user tidak mengirim field tertentu, gunakan nilai lama
            $finalData = [
                'keterangan' => $request->filled('keterangan') ? $validated['keterangan'] : $cuti->keterangan,
                'jenis_cuti' => $request->filled('jenis_cuti') ? $validated['jenis_cuti'] : $cuti->jenis_cuti,
                'tanggal_mulai' => $request->filled('tanggal_mulai') ? $validated['tanggal_mulai'] : $cuti->tanggal_mulai,
                'tanggal_selesai' => $request->filled('tanggal_selesai') ? $validated['tanggal_selesai'] : $cuti->tanggal_selesai,
                'durasi' => $request->filled('durasi') ? $validated['durasi'] : $cuti->durasi,
            ];
            
            // Hitung selisih hari untuk penyesuaian kuota
            $selisihHari = 0;
            $oldDurasi = $cuti->durasi;
            $newDurasi = $finalData['durasi'];
            
            // Logika perubahan jenis cuti terkait kuota tahunan
            $oldJenis = $cuti->jenis_cuti;
            $newJenis = $finalData['jenis_cuti'];
            
            if ($oldJenis === 'tahunan' || $newJenis === 'tahunan') {
                if ($oldJenis === 'tahunan' && $newJenis === 'tahunan') {
                    $selisihHari = $newDurasi - $oldDurasi;
                } else if ($oldJenis !== 'tahunan' && $newJenis === 'tahunan') {
                    $selisihHari = $newDurasi;
                } else if ($oldJenis === 'tahunan' && $newJenis !== 'tahunan') {
                    $selisihHari = -$oldDurasi;
                }
            }
            
            // Cek kuota jika ada penambahan hari cuti tahunan
            if ($selisihHari > 0) {
                $currentYear = date('Y');
                $quota = CutiQuota::getUserQuota($cuti->user_id, $currentYear);
                
                if ($quota->sisa < $selisihHari) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Sisa cuti tidak mencukupi untuk perubahan ini. Sisa: ' . $quota->sisa . ' hari, Dibutuhkan: ' . $selisihHari . ' hari'
                    ], 400);
                }
            }
            
            // Cek tabrakan tanggal
            $overlapCuti = Cuti::where('user_id', $cuti->user_id)
                ->where('status', 'disetujui')
                ->where('id', '!=', $cuti->id)
                ->where(function($query) use ($finalData) {
                    $query->whereBetween('tanggal_mulai', [$finalData['tanggal_mulai'], $finalData['tanggal_selesai']])
                          ->orWhereBetween('tanggal_selesai', [$finalData['tanggal_mulai'], $finalData['tanggal_selesai']])
                          ->orWhere(function($q) use ($finalData) {
                              $q->where('tanggal_mulai', '<=', $finalData['tanggal_mulai'])
                                ->where('tanggal_selesai', '>=', $finalData['tanggal_selesai']);
                          });
                })
                ->exists();
            
            if ($overlapCuti) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cuti ini bertabrakan dengan cuti lain yang sudah disetujui'
                ], 400);
            }
            
            $oldData = $cuti->toArray();
            
            // Update Kuota jika ada perubahan durasi/tipe
            if ($selisihHari != 0) {
                $currentYear = date('Y');
                $quota = CutiQuota::getUserQuota($cuti->user_id, $currentYear);
                
                if ($selisihHari > 0) {
                    $quota->addTerpakai($selisihHari);
                    $cuti->sisa_cuti_sesudah = $quota->sisa;
                } else {
                    $quota->reduceTerpakai(abs($selisihHari));
                    $cuti->sisa_cuti_sesudah = $quota->sisa;
                }
            }
            
            // Update data cuti
            $cuti->update($finalData);
            
            // Catat history
            $changes = [];
            foreach ($finalData as $key => $value) {
                // Hanya catat jika ada perubahan
                // Kita bandingkan string/nilai untuk memastikan akurasi
                if (isset($oldData[$key]) && $oldData[$key] != $value) {
                    $changes[$key] = [
                        'from' => $oldData[$key],
                        'to' => $value
                    ];
                }
            }
            
            if (!empty($changes)) {
                CutiHistory::create([
                    'cuti_id' => $cuti->id,
                    'action' => 'updated',
                    'user_id' => $this->user->id,
                    'changes' => json_encode($changes),
                    'note' => 'Data cuti diperbarui' . ($selisihHari != 0 ? ' (Selisih hari: ' . $selisihHari . ')' : '')
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'Cuti berhasil diperbarui',
                'data' => $this->formatCutiData($cuti->fresh())
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui cuti'], 500);
        }
    }

    // ============================================
    // APPROVE CUTI
    // ============================================

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cuti = Cuti::with(['user', 'user.divisionDetail'])->findOrFail($id);
            
            if ($this->currentRole === 'karyawan') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            if ($this->currentRole === 'manager_divisi') {
                $userDivisiName = $cuti->user->divisionDetail ? $cuti->user->divisionDetail->divisi : null;
                if (!$cuti->user || $userDivisiName !== $this->currentDivisiName) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke divisi ini'], 403);
                }
            }

            if ($cuti->status !== 'menunggu') {
                return response()->json(['success' => false, 'message' => 'Cuti sudah diproses'], 400);
            }
            
            $currentYear = date('Y');
            $quota = CutiQuota::getUserQuota($cuti->user_id, $currentYear);
            
            if ($cuti->jenis_cuti === 'tahunan') {
                if ($quota->sisa < $cuti->durasi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sisa cuti tidak mencukupi. Sisa: ' . $quota->sisa . ' hari'
                    ], 400);
                }
                
                $cuti->sisa_cuti_sebelum = $quota->sisa;
                $quota->addTerpakai($cuti->durasi);
                $cuti->sisa_cuti_sesudah = $quota->sisa;
            } else {
                $quota->addTerpakaiKhusus($cuti->durasi);
            }
            
            if (method_exists($cuti, 'isOverlapping') && $cuti->isOverlapping()) {
                return response()->json(['success' => false, 'message' => 'Terdapat bentrok tanggal dengan cuti yang sudah disetujui'], 400);
            }
            
            $cuti->status = 'disetujui';
            $cuti->disetujui_oleh = $this->user->id;
            $cuti->disetujui_pada = Carbon::now();
            $cuti->save();
            
            CutiHistory::create([
                'cuti_id' => $cuti->id,
                'action' => 'approved',
                'user_id' => $this->user->id,
                'changes' => json_encode(['status' => 'disetujui']),
                'note' => 'Disetujui oleh ' . $this->user->name . ' (' . $this->currentRole . ')'
            ]);
            
            $startDate = Carbon::parse($cuti->tanggal_mulai);
            $endDate = Carbon::parse($cuti->tanggal_selesai);
            
            // Map cuti jenis to absensi jenis_ketidakhadiran
            $jenisMap = [
                'tahunan' => 'cuti',
                'duka' => 'sakit',
                'izin-khusus' => 'izin',
                'melahirkan' => 'cuti',
                'tanpa-gaji' => 'cuti'
            ];

            $absensiType = $jenisMap[$cuti->jenis_cuti] ?? 'cuti';

            // First, bulk-update any existing Absensi rows in the date range to approved
            Absensi::where('user_id', $cuti->user_id)
                ->whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->update([
                    'jenis_ketidakhadiran' => $absensiType,
                    'keterangan' => $cuti->keterangan,
                    'approval_status' => 'approved',
                    'approved_by' => $this->user->id,
                    'approved_at' => Carbon::now()
                ]);

            // Ensure an Absensi row exists for each non-weekend date; create if missing
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if ($date->isWeekend()) continue;

                Absensi::updateOrCreate(
                    [
                        'user_id' => $cuti->user_id,
                        'tanggal' => $date->format('Y-m-d')
                    ],
                    [
                        'jenis_ketidakhadiran' => $absensiType,
                        'keterangan' => $cuti->keterangan,
                        'approval_status' => 'approved',
                        'approved_by' => $this->user->id,
                        'approved_at' => Carbon::now()
                    ]
                );
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'Cuti disetujui',
                'quota_info' => [
                    'sisa_sebelum' => $cuti->sisa_cuti_sebelum ?? null,
                    'sisa_sesudah' => $cuti->sisa_cuti_sesudah ?? null,
                    'durasi' => $cuti->durasi,
                    'jenis_cuti' => $cuti->jenis_cuti
                ],
                'data' => $this->formatCutiData($cuti)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui'], 500);
        }
    }

    // ============================================
    // REJECT CUTI
    // ============================================

    public function reject(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(['alasan_penolakan' => 'required|string|max:255']);
            $cuti = Cuti::with(['user', 'user.divisionDetail'])->findOrFail($id);
            
            if ($this->currentRole === 'karyawan') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            if ($this->currentRole === 'manager_divisi') {
                $userDivisiName = $cuti->user->divisionDetail ? $cuti->user->divisionDetail->divisi : null;
                if (!$cuti->user || $userDivisiName !== $this->currentDivisiName) {
                    return response()->json(['success' => false, 'message' => 'Akses divisi ditolak'], 403);
                }
            }
            
            if ($cuti->status !== 'menunggu') {
                return response()->json(['success' => false, 'message' => 'Status bukan menunggu'], 400);
            }

            $cuti->status = 'ditolak';
            $cuti->disetujui_oleh = $this->user->id; 
            $cuti->disetujui_pada = Carbon::now();
            $cuti->catatan_penolakan = $validated['alasan_penolakan'];
            $cuti->save();

            CutiHistory::create([
                'cuti_id' => $cuti->id,
                'action' => 'rejected',
                'user_id' => $this->user->id,
                'changes' => json_encode(['alasan' => $validated['alasan_penolakan']]),
                'note' => 'Ditolak oleh ' . $this->user->name
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cuti ditolak', 'data' => $this->formatCutiData($cuti)]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menolak cuti'], 500);
        }
    }

    public function apiVerify(Request $request, $id)
    {
        $validated = $request->validate([
            'approval_status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:255',
            'alasan_penolakan' => 'nullable|string|max:255',
        ]);

        if ($validated['approval_status'] === 'approved') {
            return $this->approve($request, $id);
        }

        // Backward compatibility: frontend lama mengirim rejection_reason.
        $reason = $validated['alasan_penolakan']
            ?? $validated['rejection_reason']
            ?? 'Pengajuan ditolak';

        $request->merge(['alasan_penolakan' => $reason]);
        return $this->reject($request, $id);
    }

    // ============================================
    // CANCEL CUTI (Karyawan)
    // ============================================

    public function cancel(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cuti = Cuti::findOrFail($id);
            
            if ($cuti->user_id !== $this->user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            if ($cuti->status !== 'menunggu') {
                return response()->json(['success' => false, 'message' => 'Cuti tidak dapat dibatalkan karena sudah diproses'], 400);
            }
            
            // Karyawan membatalkan cuti: hapus permanen dari database cuti
            $cuti->forceDelete();
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Cuti berhasil dibatalkan']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan cuti'], 500);
        }
    }

    // ============================================
    // CANCEL CUTI (With Refund)
    // ============================================

    public function cancelWithRefund(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cuti = Cuti::with('user')->findOrFail($id);
            
            if ($this->currentRole === 'karyawan' && $cuti->user_id !== $this->user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            if ($cuti->status !== 'disetujui') {
                return response()->json(['success' => false, 'message' => 'Hanya cuti yang sudah disetujui yang dapat dibatalkan'], 400);
            }
            
            $validated = $request->validate([
                'catatan_pembatalan' => 'nullable|string|max:255'
            ]);
            
            $currentYear = date('Y');
            $quota = CutiQuota::getUserQuota($cuti->user_id, $currentYear);
            
            $refundAmount = $cuti->durasi;
            $refundType = 'none';

            $today = Carbon::today();
            $startDate = Carbon::parse($cuti->tanggal_mulai);

            if ($startDate->gte($today)) {
                if ($cuti->jenis_cuti === 'tahunan') {
                    $quota->reduceTerpakai($refundAmount);
                    $refundType = 'tahunan';
                } else {
                    $quota->reduceTerpakaiKhusus($refundAmount);
                    $refundType = 'khusus';
                }
            } else {
                // Tidak refund
            }

            // Delete related absensi entries during the cancelled leave range
            // Use a broader cleanup to avoid stale approved absences blocking check-in
            Absensi::where('user_id', $cuti->user_id)
                ->whereBetween('tanggal', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->whereNotNull('jenis_ketidakhadiran')
                ->whereIn('approval_status', ['pending', 'approved'])
                ->forceDelete();

            // Pembatalan cuti oleh role apa pun: hapus permanen dari database cuti
            $cuti->forceDelete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Cuti dibatalkan' . ($startDate->gte($today) ? ' dan quota dikembalikan' : ''),
                'data' => null
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling cuti with refund: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan cuti'], 500);
        }
    }

    // ============================================
    // DELETE METHOD
    // ============================================

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $cuti = Cuti::findOrFail($id);
            
            if ($cuti->user_id !== $this->user->id && !in_array($this->currentRole, ['admin', 'general_manager'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            if ($cuti->status !== 'menunggu') {
                return response()->json(['success' => false, 'message' => 'Cuti tidak dapat dihapus karena sudah diproses. Gunakan fitur pembatalan.'], 400);
            }
            
            // Hapus permanen dari database cuti (bukan soft delete)
            $cuti->forceDelete();
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Cuti dihapus']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus cuti'], 500);
        }
    }

    // ============================================
    // UTILITY METHODS
    // ============================================
    
    public function calculateDuration(Request $request) 
    {
        try {
            $validated = $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai'
            ]);
            
            $start = Carbon::parse($validated['tanggal_mulai']);
            $end = Carbon::parse($validated['tanggal_selesai']);
            
            $totalDays = 0;
            $current = $start->copy();
            
            while ($current->lte($end)) {
                if (!$current->isWeekend()) {
                    $totalDays++;
                }
                $current->addDay();
            }
            
            return response()->json([
                'success' => true, 
                'data' => [
                    'jumlah_hari' => $totalDays,
                    'tanggal_mulai' => $start->format('d F Y'),
                    'tanggal_selesai' => $end->format('d F Y'),
                    'hari_kerja' => $totalDays . ' hari kerja'
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error calculating duration: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghitung durasi'], 500);
        }
    }
    
    public function getKaryawanByDivisi() 
    {
        try {
            if ($this->currentRole !== 'manager_divisi') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            if (!$this->currentDivisiName) {
                return response()->json(['success' => false, 'message' => 'User tidak memiliki divisi'], 400);
            }
            
            $query = User::where('role', 'karyawan')
                         ->with('divisionDetail')
                         ->whereHas('divisionDetail', function($q) {
                             $q->where('divisi', $this->currentDivisiName);
                         });
            
            $karyawan = $query->orderBy('name')
                            ->get(['id', 'name', 'divisi_id', 'sisa_cuti', 'email'])
                            ->map(function($user) {
                                $quotaInfo = [];
                                try {
                                    $quotaInfo = CutiQuota::getUserQuota($user->id, date('Y'));
                                } catch (\Exception $e) {
                                    Log::warning('Failed to get quota for user ' . $user->id . ': ' . $e->getMessage());
                                }
                                
                                $divisiName = $user->divisionDetail ? $user->divisionDetail->divisi : '-';

                                return [
                                    'id' => $user->id,
                                    'name' => $user->name,
                                    'divisi' => $divisiName,
                                    'sisa_cuti' => (int)($user->sisa_cuti ?? 0),
                                    'email' => $user->email,
                                    'cuti_terpakai' => 12 - (int)($user->sisa_cuti ?? 0),
                                    'quota_info' => $quotaInfo
                                ];
                            });
            
            return response()->json(['success' => true, 'data' => $karyawan]);
            
        } catch (\Exception $e) {
            Log::error('Error getting karyawan by divisi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data karyawan'], 500);
        }
    }

    public function getHistory($id)
    {
        try {
            $cuti = Cuti::findOrFail($id);
            
            if ($cuti->user_id !== $this->user->id && !in_array($this->currentRole, ['admin', 'general_manager', 'manager_divisi'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $histories = CutiHistory::with('user:id,name')
                ->where('cuti_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($history) {
                    return [
                        'action' => $history->action,
                        'action_label' => $this->getActionLabel($history->action),
                        'note' => $history->note,
                        'user_name' => $history->user ? $history->user->name : 'System',
                        'created_at' => $history->created_at->format('d F Y H:i'),
                        'changes' => $history->changes ? json_decode($history->changes, true) : null
                    ];
                });
            
            return response()->json(['success' => true, 'data' => $histories]);
            
        } catch (\Exception $e) {
            Log::error('Error getting history: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil history'], 500);
        }
    }

    // ============================================
    // RESET QUOTA (ADMIN ONLY)
    // ============================================

    public function resetQuota(Request $request)
    {
        try {
            if (!in_array($this->currentRole, ['admin', 'general_manager', 'owner'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $year = $request->get('year') ?? date('Y');
            $userId = $request->get('user_id');
            $quotaKhusus = $request->get('quota_khusus', 0);
            
            if ($userId) {
                $quota = CutiQuota::where('user_id', $userId)
                    ->where('tahun', $year)
                    ->first();
                
                if (!$quota) {
                    return response()->json(['success' => false, 'message' => 'Quota tidak ditemukan'], 404);
                }
                
                $oldTerpakai = $quota->terpakai;
                $oldSisa = $quota->sisa;
                $oldTerpakaiKhusus = $quota->terpakai_khusus;
                
                $quota->update([
                    'terpakai' => 0,
                    'sisa' => $quota->quota_tahunan,
                    'quota_khusus' => $quotaKhusus,
                    'terpakai_khusus' => 0,
                    'is_reset' => true,
                    'reset_at' => now(),
                    'reset_by' => $this->user->id
                ]);
                
                $user = User::find($userId);
                if ($user) {
                    $user->update([
                        'sisa_cuti' => $quota->quota_tahunan,
                        'cuti_terpakai_tahun_ini' => 0,
                        'cuti_reset_date' => now()
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Quota berhasil direset untuk user ' . $user->name,
                    'data' => [
                        'before' => [
                            'terpakai' => $oldTerpakai, 
                            'sisa' => $oldSisa,
                            'terpakai_khusus' => $oldTerpakaiKhusus
                        ],
                        'after' => [
                            'terpakai' => 0, 
                            'sisa' => $quota->quota_tahunan,
                            'quota_khusus' => $quotaKhusus,
                            'terpakai_khusus' => 0
                        ],
                        'reset_at' => now()->format('d F Y H:i'),
                        'reset_by' => $this->user->name
                    ]
                ]);
            } else {
                $quotas = CutiQuota::where('tahun', $year)->get();
                $resetCount = 0;
                
                foreach ($quotas as $quota) {
                    if ($quota instanceof \App\Models\CutiQuota) {
                        $quota->update([
                            'terpakai' => 0,
                            'sisa' => $quota->quota_tahunan,
                            'quota_khusus' => $quotaKhusus,
                            'terpakai_khusus' => 0,
                            'is_reset' => true,
                            'reset_at' => now(),
                            'reset_by' => $this->user->id
                        ]);
                        
                        $user = User::find($quota->user_id);
                        if ($user) {
                            $user->update([
                                'sisa_cuti' => $quota->quota_tahunan,
                                'cuti_terpakai_tahun_ini' => 0,
                                'cuti_reset_date' => now()
                            ]);
                        }
                        
                        $resetCount++;
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Quota berhasil direset untuk ' . $resetCount . ' user',
                    'data' => [
                        'total_reset' => $resetCount,
                        'year' => $year,
                        'quota_khusus' => $quotaKhusus,
                        'reset_at' => now()->format('d F Y H:i'),
                        'reset_by' => $this->user->name
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error resetting quota: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mereset quota'], 500);
        }
    }

    // ============================================
    // EXPORT & REPORT
    // ============================================

    public function export(Request $request)
    {
        try {
            if (!in_array($this->currentRole, ['admin', 'general_manager', 'owner', 'finance'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $query = Cuti::with(['user:id,name,divisi_id,sisa_cuti,email', 'user.divisionDetail', 'disetujuiOleh:id,name']);
            
            if ($request->has('year')) {
                $year = $request->year;
                $query->whereYear('tanggal_mulai', $year);
            }
            
            if ($request->has('divisi') && $request->divisi !== 'all') {
                $query->whereHas('user.divisionDetail', function($q) use ($request) {
                    $q->where('divisi', $request->divisi);
                });
            }
            
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            $cuti = $query->orderBy('tanggal_mulai', 'desc')->get();
            
            $data = $cuti->map(function($item) {
                $divisiName = $item->user && $item->user->divisionDetail ? $item->user->divisionDetail->divisi : '-';
                return [
                    'ID' => $item->id,
                    'Nama Karyawan' => $item->user->name ?? 'Unknown',
                    'Divisi' => $divisiName,
                    'Jenis Cuti' => $this->getJenisCutiLabel($item->jenis_cuti),
                    'Tanggal Mulai' => $item->tanggal_mulai ? Carbon::parse($item->tanggal_mulai)->format('d/m/Y') : '-',
                    'Tanggal Selesai' => $item->tanggal_selesai ? Carbon::parse($item->tanggal_selesai)->format('d/m/Y') : '-',
                    'Durasi (Hari)' => $item->durasi,
                    'Status' => $this->getStatusLabel($item->status),
                    'Disetujui Oleh' => $item->disetujuiOleh->name ?? '-',
                    'Tanggal Disetujui' => $item->disetujui_pada ? Carbon::parse($item->disetujui_pada)->format('d/m/Y H:i') : '-',
                    'Keterangan' => $item->keterangan ?? '-'
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'total_records' => $cuti->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error exporting cuti: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor data'], 500);
        }
    }

    public function report(Request $request)
    {
        try {
            if (!in_array($this->currentRole, ['admin', 'general_manager', 'owner', 'finance'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $year = $request->get('year', date('Y'));
            
            $monthlyStats = [];
            for ($month = 1; $month <= 12; $month++) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                
                $total = Cuti::whereBetween('tanggal_mulai', [$startDate, $endDate])->count();
                $disetujui = Cuti::whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->where('status', 'disetujui')->count();
                $menunggu = Cuti::whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->where('status', 'menunggu')->count();
                $ditolak = Cuti::whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->where('status', 'ditolak')->count();
                
                $monthlyStats[] = [
                    'month' => Carbon::create($year, $month, 1)->translatedFormat('F'),
                    'total' => $total,
                    'disetujui' => $disetujui,
                    'menunggu' => $menunggu,
                    'ditolak' => $ditolak
                ];
            }
            
            $jenisStats = Cuti::whereYear('tanggal_mulai', $year)
                ->select('jenis_cuti', DB::raw('count(*) as total'), DB::raw('sum(durasi) as total_hari'))
                ->groupBy('jenis_cuti')
                ->get()
                ->map(function($item) {
                    return [
                        'jenis_cuti' => $this->getJenisCutiLabel($item->jenis_cuti),
                        'total' => $item->total,
                        'total_hari' => $item->total_hari
                    ];
                });
            
            $divisiStats = Cuti::whereYear('tanggal_mulai', $year)
                ->join('users', 'cutis.user_id', '=', 'users.id')
                ->join('divisis', 'users.divisi_id', '=', 'divisis.id')
                ->select('divisis.divisi', DB::raw('count(*) as total'))
                ->groupBy('divisis.divisi')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'monthly_stats' => $monthlyStats,
                    'jenis_stats' => $jenisStats,
                    'divisi_stats' => $divisiStats,
                    'year' => $year
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membuat laporan'], 500);
        }
    }

    public function checkLeaveStatusApi(Request $request)
    {
        try {
            $userId = $request->get('user_id') ?? $this->user->id;
            $date = $request->get('date') ?? Carbon::today()->format('Y-m-d');
            
            if ($this->currentRole === 'karyawan' && $userId !== $this->user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $cuti = Cuti::where('user_id', $userId)
                ->where('status', 'disetujui')
                ->whereDate('tanggal_mulai', '<=', $date)
                ->whereDate('tanggal_selesai', '>=', $date)
                ->first();
            
            $onLeave = $cuti ? true : false;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'on_leave' => $onLeave,
                    'cuti_details' => $onLeave ? [
                        'id' => $cuti->id,
                        'jenis_cuti' => $cuti->jenis_cuti,
                        'jenis_cuti_label' => $this->getJenisCutiLabel($cuti->jenis_cuti),
                        'keterangan' => $cuti->keterangan,
                        'tanggal_mulai' => $cuti->tanggal_mulai ? $cuti->tanggal_mulai->format('d F Y') : '-',
                        'tanggal_selesai' => $cuti->tanggal_selesai ? $cuti->tanggal_selesai->format('d F Y') : '-',
                        'durasi' => $cuti->durasi
                    ] : null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking leave status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memeriksa status cuti'], 500);
        }
    }

    public function getSummary(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            if (!in_array($this->currentRole, ['admin', 'general_manager', 'owner', 'manager_divisi'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $query = Cuti::whereYear('tanggal_mulai', $year);
            
            if ($this->currentRole === 'manager_divisi' && $this->currentDivisiName) {
                $query->whereHas('user.divisionDetail', function($q) {
                     $q->where('divisi', $this->currentDivisiName);
                });
            }
            
            $total = $query->count();
            $disetujui = (clone $query)->where('status', 'disetujui')->count();
            $menunggu = (clone $query)->where('status', 'menunggu')->count();
            $ditolak = (clone $query)->where('status', 'ditolak')->count();
            $dibatalkan = (clone $query)->where('status', 'dibatalkan')->count();
            
            $totalHariCuti = (clone $query)->where('status', 'disetujui')->sum('durasi');
            
            $topKaryawan = Cuti::whereYear('tanggal_mulai', $year)
                ->where('status', 'disetujui')
                ->join('users', 'cutis.user_id', '=', 'users.id')
                ->leftJoin('divisis', 'users.divisi_id', '=', 'divisis.id')
                ->select('users.name', 'divisis.divisi', DB::raw('sum(cutis.durasi) as total_hari'))
                ->groupBy('users.id', 'users.name', 'divisis.divisi')
                ->orderBy('total_hari', 'desc')
                ->limit(5)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'year' => $year,
                    'total_pengajuan' => $total,
                    'disetujui' => $disetujui,
                    'menunggu' => $menunggu,
                    'ditolak' => $ditolak,
                    'dibatalkan' => $dibatalkan,
                    'total_hari_cuti' => $totalHariCuti,
                    'top_karyawan' => $topKaryawan,
                    'persentase_disetujui' => $total > 0 ? round(($disetujui / $total) * 100, 1) : 0,
                    'persentase_ditolak' => $total > 0 ? round(($ditolak / $total) * 100, 1) : 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting summary: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil summary'], 500);
        }
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    private function getStatusLabel($status)
    {
        $labels = [
            'menunggu' => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
        ];
        return $labels[$status] ?? ucfirst($status);
    }

    private function getStatusColor($status)
    {
        $colors = [
            'menunggu' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'dibatalkan' => 'secondary',
        ];
        return $colors[$status] ?? 'secondary';
    }

    private function getJenisCutiLabel($jenis)
    {
        $labels = [
            'tahunan' => 'Cuti Tahunan',
            'melahirkan' => 'Cuti Melahirkan',
            'duka' => 'Cuti Duka',
            'izin-khusus' => 'Cuti Izin Khusus',
            'tanpa-gaji' => 'Cuti Tanpa Gaji',
            // Backward compatibility old values
            'sakit' => 'Cuti Duka',
            'penting' => 'Cuti Izin Khusus',
            'lainnya' => 'Cuti Tanpa Gaji',
        ];
        return $labels[$jenis] ?? 'Cuti Lainnya';
    }

    private function normalizeJenisCutiInput(?string $jenis): ?string
    {
        if (!$jenis) {
            return $jenis;
        }

        $mapping = [
            // old -> new
            'sakit' => 'duka',
            'penting' => 'izin-khusus',
            'lainnya' => 'tanpa-gaji',
        ];

        return $mapping[$jenis] ?? $jenis;
    }

    private function getActionLabel($action)
    {
        $labels = [
            'created' => 'Diajukan',
            'updated' => 'Diperbarui',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'deleted' => 'Dihapus',
        ];
        return $labels[$action] ?? ucfirst($action);
    }

    public function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            
            $userCount = User::count();
            $cutiCount = Cuti::count();
            $quotaCount = CutiQuota::count();
            
            return response()->json([
                'success' => true,
                'database' => 'Connected',
                'counts' => [
                    'users' => $userCount,
                    'cuti' => $cutiCount,
                    'quota' => $quotaCount
                ],
                'user_info' => $this->user ? [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'role' => $this->user->role,
                    'divisi' => $this->user->divisionDetail ? $this->user->divisionDetail->divisi : '-'
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


