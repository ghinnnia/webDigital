<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Layanan;
use App\Models\Invoice;
use App\Models\Divisi;
use App\Models\ProjectNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataProjectController extends Controller
{
    /**
     * Display a listing of the resource for General Manager.
     */
    public function index(Request $request)
    {
        $query = Project::with(['layanan', 'penanggungJawab.divisi', 'karyawanPenanggungJawab.divisi']);

        // Search Logic (Search by Nama Project atau Nama Penanggung Jawab)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('penanggungJawab', function($subQ) use ($searchTerm) {
                      $subQ->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Filter by Penanggung Jawab
        if ($request->has('penanggung_jawab_id') && $request->penanggung_jawab_id != '') {
            $managerId = (int) $request->penanggung_jawab_id;
            $query->where(function ($subQuery) use ($managerId) {
                $subQuery->where('penanggung_jawab_id', $managerId);

                if (Schema::hasColumn('project', 'penanggung_jawab_ids')) {
                    $subQuery
                        ->orWhereJsonContains('penanggung_jawab_ids', $managerId)
                        ->orWhereJsonContains('penanggung_jawab_ids', (string) $managerId);
                }
            });
        }

        $projects = $query->orderBy('id', 'desc')->paginate(3)->withQueryString();

        // Ambil daftar manager divisi untuk dropdown filter
        $managers = User::with('divisi')
            ->where('role', 'manager_divisi')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'divisi_id']);

        $karyawans = User::with('divisi')
            ->where('role', 'karyawan')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'divisi_id']);

        return view('general_manajer.data_project', compact('projects', 'managers', 'karyawans'));
    }

    /**
     * Display a listing of the resource for Admin.
     */
    public function admin(Request $request)
    {
        // Auto update status kerjasama yang sudah expired
        $this->autoUpdateStatusKerjasama();

        $query = Project::query();

        // SEARCH (nama & deskripsi)
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->q . '%');
            });
        }

        // FILTER STATUS PENGERJAAN
        if ($request->filled('status_pengerjaan')) {
            $query->where('status_pengerjaan', $request->status_pengerjaan);
        }

        // FILTER STATUS KERJASAMA
        if ($request->filled('status_kerjasama')) {
            $query->where('status_kerjasama', $request->status_kerjasama);
        }

        // FILTER TANGGAL MULAI PENGERJAAN
        if ($request->filled('tanggal_mulai_pengerjaan')) {
            $query->whereDate('tanggal_mulai_pengerjaan', $request->tanggal_mulai_pengerjaan);
        }

        // FILTER TANGGAL SELESAI PENGERJAAN
        if ($request->filled('tanggal_selesai_pengerjaan')) {
            $query->whereDate('tanggal_selesai_pengerjaan', $request->tanggal_selesai_pengerjaan);
        }

        $project = $query
            ->with(['invoice', 'penanggungJawab', 'layanan', 'divisi'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $invoices = Invoice::orderBy('id', 'desc')->get();
        $layanans = Layanan::all();
        $divisis = Divisi::all();

        return view('admin.data_project', compact('project', 'invoices', 'layanans', 'divisis'));
    }

    /**
     * Auto update status kerjasama yang sudah expired
     */
    private function autoUpdateStatusKerjasama()
    {
        $today = \Carbon\Carbon::now()->startOfDay();
        
        // Cari project dengan status kerjasama 'aktif' dan tanggal selesai sudah lewat
        $expiredProjects = Project::where('status_kerjasama', 'aktif')
            ->whereNotNull('tanggal_selesai_kerjasama')
            ->where('tanggal_selesai_kerjasama', '<', $today)
            ->get();
        
        foreach ($expiredProjects as $project) {
            $project->status_kerjasama = 'selesai';
            $project->save();
            
            // Buat notifikasi
            ProjectNotification::create([
                'project_id' => $project->id,
                'message' => "⚠️ Periode KERJA SAMA dengan '{$project->nama}' telah berakhir pada " . 
                            \Carbon\Carbon::parse($project->tanggal_selesai_kerjasama)->format('d-m-Y'),
                'type' => 'expired_kerjasama',
                'is_read' => false
            ]);
        }
    }

    /**
     * Display a listing of the resource for Manager Divisi.
     */
    public function managerDivisi(Request $request)
    {
        $user = auth()->user();

        Log::info('Manager Divisi accessing projects', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'user_divisi' => $user->divisi,
            'user_divisi_id' => $user->divisi_id
        ]);

        $query = Project::with(['layanan', 'penanggungJawab'])
            ->assignedToUser($user->id);

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $projects = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        Log::info('Projects found for manager divisi', [
            'count' => $projects->count(),
            'user_id' => $user->id
        ]);

        if ($projects->count() === 0) {
            Log::warning('No projects found for manager divisi', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
        }

        return view('manager_divisi.data_project', compact('projects'));
    }

    /**
     * API: Get all projects for authenticated user
     */
    public function getAllProjects(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            if ($user->role === 'manager_divisi') {
                $projects = Project::assignedToUser($user->id)
                    ->whereNull('deleted_at')
                    ->orderBy('nama', 'asc')
                    ->get();
            } else {
                $projects = Project::whereNull('deleted_at')
                    ->orderBy('nama', 'asc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $projects
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllProjects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get Projects Dropdown untuk Manager Divisi
     */
    public function getManagerProjectsDropdown()
    {
        $user = auth()->user();
        
        $projects = Project::assignedToUser($user->id)
            ->select(['id', 'nama', 'status', 'deadline'])
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * API: Get karyawan list by manager divisi
     */
    public function getKaryawanByManager($managerId)
    {
        try {
            $manager = User::with('divisi')->where('role', 'manager_divisi')->findOrFail($managerId);
            $divisiId = $manager->divisi_id;

            if (empty($divisiId)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Manager divisi tidak memiliki divisi'
                ]);
            }

            $karyawans = User::where('role', 'karyawan')
                ->where('divisi_id', $divisiId)
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'email', 'divisi_id']);

            return response()->json([
                'success' => true,
                'data' => $karyawans
            ]);
        } catch (\Exception $e) {
            Log::error('Error getKaryawanByManager: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data karyawan'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'invoice_id' => 'required|exists:invoices,id',
        'nama' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'harga' => 'required|numeric|min:0',
        'tanggal_mulai_pengerjaan' => 'nullable|date',
        'tanggal_selesai_pengerjaan' => 'nullable|date|after_or_equal:tanggal_mulai_pengerjaan',
        'tanggal_mulai_kerjasama' => 'nullable|date',
        'tanggal_selesai_kerjasama' => 'nullable|date|after_or_equal:tanggal_mulai_kerjasama',
        'status_pengerjaan' => 'required|in:pending,dalam_pengerjaan,selesai,dibatalkan',
        'status_kerjasama' => 'required|in:aktif,selesai,ditangguhkan',
        'progres' => 'required|integer|min:0|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $data = [
            'invoice_id' => $request->invoice_id,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi ?? '',
            'harga' => $request->harga,
            'tanggal_mulai_pengerjaan' => $request->tanggal_mulai_pengerjaan,
            'tanggal_selesai_pengerjaan' => $request->tanggal_selesai_pengerjaan,
            'tanggal_mulai_kerjasama' => $request->tanggal_mulai_kerjasama,
            'tanggal_selesai_kerjasama' => $request->tanggal_selesai_kerjasama,
            'status_pengerjaan' => $request->status_pengerjaan,
            'status_kerjasama' => $request->status_kerjasama,
            'progres' => $request->progres,
            'penanggung_jawab_id' => auth()->id(),
            // 'created_by' => auth()->id(), // HAPUS atau comment
        ];

        // Hanya tambahkan layanan_id jika ada
        if ($request->has('layanan_id') && !empty($request->layanan_id)) {
            $data['layanan_id'] = $request->layanan_id;
        }

        // Hanya tambahkan divisi_id jika ada
        if ($request->has('divisi_id') && !empty($request->divisi_id)) {
            $data['divisi_id'] = $request->divisi_id;
        }

        $project = Project::create($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Project berhasil ditambahkan',
            'data' => $project
        ]);
        
    } catch (\Exception $e) {
        Log::error('Store Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan project: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $project = Project::with(['invoice', 'layanan', 'penanggungJawab', 'karyawanPenanggungJawab', 'divisi'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $project
            ]);
        } catch (\Exception $e) {
            Log::error('Show Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Project tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('DataProjectController update method called', [
            'id' => $id, 
            'request_data' => $request->all(),
            'method' => $request->method()
        ]);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status_kerjasama' => 'required|in:aktif,selesai,ditangguhkan',
            'penanggung_jawab_id' => 'nullable|exists:users,id',
            'penanggung_jawab_ids' => 'nullable|array|min:1',
            'penanggung_jawab_ids.*' => 'integer|exists:users,id',
            'karyawan_penanggung_jawab_id' => 'nullable|exists:users,id',
            'karyawan_penanggung_jawab_ids' => 'nullable|array|min:1',
            'karyawan_penanggung_jawab_ids.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in update method', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
                'input_received' => $request->all()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $project = Project::findOrFail($id);
            
            $managerIds = collect($request->input('penanggung_jawab_ids', []))
                ->filter(fn ($id) => !is_null($id) && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($managerIds->isEmpty() && $request->filled('penanggung_jawab_id')) {
                $managerIds = collect([(int) $request->penanggung_jawab_id]);
            }

            $primaryManagerId = $managerIds->first();

            $karyawanIds = collect($request->input('karyawan_penanggung_jawab_ids', []))
                ->filter(fn ($id) => !is_null($id) && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($karyawanIds->isEmpty() && $request->filled('karyawan_penanggung_jawab_id')) {
                $karyawanIds = collect([(int) $request->karyawan_penanggung_jawab_id]);
            }

            $primaryKaryawanId = $karyawanIds->first();

            // Cek jika tanggal selesai kerjasama sudah lewat
            $statusKerjasama = $request->status_kerjasama;
            if ($request->tanggal_selesai_kerjasama) {
                $tglSelesai = \Carbon\Carbon::parse($request->tanggal_selesai_kerjasama);
                if ($tglSelesai->isPast() && $statusKerjasama == 'aktif') {
                    $statusKerjasama = 'selesai';
                }
            }

            $updateData = [
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'penanggung_jawab_id' => $primaryManagerId ?: null,
                'karyawan_penanggung_jawab_id' => $primaryKaryawanId ?: null,
                'tanggal_mulai_pengerjaan' => $request->tanggal_mulai_pengerjaan ?: null,
                'tanggal_selesai_pengerjaan' => $request->tanggal_selesai_pengerjaan ?: null,
                'tanggal_mulai_kerjasama' => $request->tanggal_mulai_kerjasama ?: null,
                'tanggal_selesai_kerjasama' => $request->tanggal_selesai_kerjasama ?: null,
                'status_kerjasama' => $statusKerjasama,
            ];

            if (Schema::hasColumn('project', 'penanggung_jawab_ids') && $managerIds->isNotEmpty()) {
                $updateData['penanggung_jawab_ids'] = $managerIds->all();
            }

            if (Schema::hasColumn('project', 'karyawan_penanggung_jawab_ids') && $karyawanIds->isNotEmpty()) {
                $updateData['karyawan_penanggung_jawab_ids'] = $karyawanIds->all();
            }

            // Hapus null values
            foreach ($updateData as $key => $value) {
                if ($value === null) {
                    unset($updateData[$key]);
                }
            }

            Log::info('Updating project with data', $updateData);
            
            $project->update($updateData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil diupdate!',
                'data' => $project->fresh(['layanan', 'penanggungJawab', 'karyawanPenanggungJawab'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Progress & Status dari Manager Divisi.
     */
    public function updateManager(Request $request, string $id)
    {
        Log::info('UpdateManager method called', [
            'id' => $id,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        $project = Project::where('id', $id)
            ->assignedToUser(auth()->id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'progres' => 'required|integer|min:0|max:100',
            'status' => 'required|string|in:pending,dalam_pengerjaan,selesai,dibatalkan',
            'tanggal_mulai_pengerjaan' => 'nullable|date',
            'tanggal_selesai_pengerjaan' => 'nullable|date|after_or_equal:tanggal_mulai_pengerjaan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $status = strtolower($request->status);
        $progres = (int) $request->progres;
        
        if ($progres == 100) {
            $status = 'selesai';
        } elseif ($progres > 0 && $status === 'pending') {
            $status = 'dalam_pengerjaan';
        }
        
        $updateData = [
            'progres' => $progres,
            'status_pengerjaan' => $status,
        ];
        
        if ($request->has('tanggal_mulai_pengerjaan') && $request->tanggal_mulai_pengerjaan) {
            $updateData['tanggal_mulai_pengerjaan'] = $request->tanggal_mulai_pengerjaan;
        }
        
        if ($request->has('tanggal_selesai_pengerjaan') && $request->tanggal_selesai_pengerjaan) {
            $updateData['tanggal_selesai_pengerjaan'] = $request->tanggal_selesai_pengerjaan;
        }
        
        $project->update($updateData);

        Log::info('Project updated by manager divisi', [
            'project_id' => $project->id,
            'new_progres' => $progres,
            'new_status' => $status,
            'updated_by' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Progres dan status berhasil diupdate!',
            'data' => $project
        ]);
    }

    /**
     * Update General Manager (Full Update - untuk method updategeneral).
     */
    public function updategeneral(Request $request, string $id)
    {
        Log::info('Updategeneral method called', ['id' => $id, 'request_data' => $request->all()]);
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'penanggung_jawab_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            Log::error('Updategeneral validation failed', ['errors' => $validator->errors()->toArray()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $project = Project::findOrFail($id);
            
            $project->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'tanggal_mulai_pengerjaan' => $request->tanggal_mulai_pengerjaan,
                'tanggal_selesai_pengerjaan' => $request->tanggal_selesai_pengerjaan,
                'tanggal_mulai_kerjasama' => $request->tanggal_mulai_kerjasama,
                'tanggal_selesai_kerjasama' => $request->tanggal_selesai_kerjasama,
                'status_kerjasama' => $request->status_kerjasama,
            ]);
            
            Log::info('Project updated via updategeneral', ['project' => $project->toArray()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil diupdate!',
                'data' => $project
            ]);
            
        } catch (\Exception $e) {
            Log::error('Updategeneral Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project berhasil dihapus!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter/Refresh Table via AJAX.
     */
    public function filterByUser(Request $request)
    {
        $user = auth()->user();
        $projects = Project::query()->with(['layanan', 'penanggungJawab']);

        if ($user->role === 'manager_divisi') {
            $projects->assignedToUser($user->id);
        }

        if ($request->has('user_id') && $request->user_id) {
            if ($user->role !== 'manager_divisi') {
                $targetUserId = (int) $request->user_id;
                $projects->where(function ($subQuery) use ($targetUserId) {
                    $subQuery->where('penanggung_jawab_id', $targetUserId);

                    if (Schema::hasColumn('project', 'penanggung_jawab_ids')) {
                        $subQuery
                            ->orWhereJsonContains('penanggung_jawab_ids', $targetUserId)
                            ->orWhereJsonContains('penanggung_jawab_ids', (string) $targetUserId);
                    }
                });
            }
        }

        $projects = $projects->orderBy('id', 'desc')->paginate(3);

        return response()->json([
            'html' => view('manager_divisi.partials.project_table', compact('projects'))->render(),
            'total' => $projects->total()
        ]);
    }
    
    /**
     * Synchronize projects from layanan
     */
    public function syncFromLayanan($layananId)
    {
        try {
            $layanan = Layanan::findOrFail($layananId);
            $projects = Project::where('layanan_id', $layananId)->get();
            
            $updatedCount = 0;
            
            foreach ($projects as $project) {
                if ($project instanceof Project) {
                    $project->update([
                        'nama' => $layanan->nama_layanan,
                        'deskripsi' => $layanan->deskripsi,
                        'harga' => $layanan->harga,
                    ]);
                    $updatedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} project berhasil disinkronisasi dari layanan.",
                'data' => [
                    'layanan' => $layanan,
                    'updated_projects_count' => $updatedCount
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Sync Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Assign Penanggung Jawab saja
     */
    public function assignResponsible(Request $request, string $id)
    {
        Log::info('Assign responsible method called', [
            'id' => $id, 
            'request_data' => $request->all()
        ]);
        
        $validator = Validator::make($request->all(), [
            'penanggung_jawab_id' => 'nullable|exists:users,id',
            'penanggung_jawab_ids' => 'nullable|array|min:1',
            'penanggung_jawab_ids.*' => 'integer|exists:users,id',
            'karyawan_penanggung_jawab_id' => 'nullable|exists:users,id',
            'karyawan_penanggung_jawab_ids' => 'nullable|array|min:1',
            'karyawan_penanggung_jawab_ids.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            Log::error('Assign responsible validation failed', ['errors' => $validator->errors()->toArray()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $project = Project::findOrFail($id);
            
            $managerIds = collect($request->input('penanggung_jawab_ids', []))
                ->filter(fn ($id) => !is_null($id) && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($managerIds->isEmpty() && $request->filled('penanggung_jawab_id')) {
                $managerIds = collect([(int) $request->penanggung_jawab_id]);
            }

            $karyawanIds = collect($request->input('karyawan_penanggung_jawab_ids', []))
                ->filter(fn ($id) => !is_null($id) && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($karyawanIds->isEmpty() && $request->filled('karyawan_penanggung_jawab_id')) {
                $karyawanIds = collect([(int) $request->karyawan_penanggung_jawab_id]);
            }

            $updateData = [
                'penanggung_jawab_id' => $managerIds->first() ?: null,
                'karyawan_penanggung_jawab_id' => $karyawanIds->first() ?: null,
            ];

            if (Schema::hasColumn('project', 'penanggung_jawab_ids') && $managerIds->isNotEmpty()) {
                $updateData['penanggung_jawab_ids'] = $managerIds->all();
            }

            if (Schema::hasColumn('project', 'karyawan_penanggung_jawab_ids') && $karyawanIds->isNotEmpty()) {
                $updateData['karyawan_penanggung_jawab_ids'] = $karyawanIds->all();
            }

            $project->update($updateData);
            
            DB::commit();
            
            Log::info('Responsible assigned successfully', [
                'project_id' => $project->id,
                'new_responsible_id' => $project->penanggung_jawab_id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Penanggung jawab berhasil ditetapkan!',
                'data' => $project->fresh(['layanan', 'penanggungJawab'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Project not found in assignResponsible: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Project tidak ditemukan!'
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assign Responsible Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan penanggung jawab: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Simple update method for just assigning responsible person
     */
    public function simpleUpdate(Request $request, string $id)
    {
        Log::info('Simple update called', ['id' => $id, 'data' => $request->all()]);
        
        try {
            $project = Project::findOrFail($id);
            
            $managerIds = collect($request->input('penanggung_jawab_ids', []))
                ->filter(fn ($id) => !is_null($id) && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($managerIds->isEmpty() && $request->filled('penanggung_jawab_id')) {
                $managerIds = collect([(int) $request->penanggung_jawab_id]);
            }

            $karyawanIds = collect($request->input('karyawan_penanggung_jawab_ids', []))
                ->filter(fn ($id) => !is_null($id) && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($karyawanIds->isEmpty() && $request->filled('karyawan_penanggung_jawab_id')) {
                $karyawanIds = collect([(int) $request->karyawan_penanggung_jawab_id]);
            }

            $updateData = [
                'penanggung_jawab_id' => $managerIds->first() ?: null,
                'karyawan_penanggung_jawab_id' => $karyawanIds->first() ?: null,
            ];

            if (Schema::hasColumn('project', 'penanggung_jawab_ids') && $managerIds->isNotEmpty()) {
                $updateData['penanggung_jawab_ids'] = $managerIds->all();
            }

            if (Schema::hasColumn('project', 'karyawan_penanggung_jawab_ids') && $karyawanIds->isNotEmpty()) {
                $updateData['karyawan_penanggung_jawab_ids'] = $karyawanIds->all();
            }

            $project->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil update penanggung jawab',
                'data' => $project
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice details untuk admin
     */
    public function getInvoiceDetails($id)
    {
        try {
            $invoice = Invoice::with(['perusahaan', 'layanan'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $invoice
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getInvoiceDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Sync project dari invoice
     */
    public function syncFromInvoice($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Cek apakah sudah ada project untuk invoice ini
            $existingProject = Project::where('invoice_id', $id)->first();
            
            if ($existingProject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project untuk invoice ini sudah ada'
                ], 422);
            }
            
            // Buat project dari invoice
            $project = Project::create([
                'invoice_id' => $invoice->id,
                'nama' => $invoice->judul ?? 'Project dari Invoice #' . $invoice->id,
                'deskripsi' => $invoice->deskripsi ?? '',
                'harga' => $invoice->total ?? 0,
                'tanggal_mulai_kerjasama' => $invoice->tanggal_mulai ?? now(),
                'tanggal_selesai_kerjasama' => $invoice->tanggal_selesai ?? now()->addMonth(),
                'status_kerjasama' => 'aktif',
                'status_pengerjaan' => 'pending',
                'progres' => 0,
                'created_by' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil dibuat dari invoice',
                'data' => $project
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error syncFromInvoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================================
    // NOTIFICATION METHODS
    // ============================================================

    /**
     * Get notifications untuk admin
     */
    public function getNotifications()
    {
        try {
            $notifications = ProjectNotification::with('project')
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Error getNotifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil notifikasi'
            ], 500);
        }
    }

    /**
     * Get notifications (alias untuk getNotifications)
     */
    public function notifications()
    {
        return $this->getNotifications();
    }

    /**
     * Mark notifikasi sebagai sudah dibaca
     */
    public function markNotificationAsRead($id)
    {
        try {
            $notification = ProjectNotification::findOrFail($id);
            $notification->is_read = true;
            $notification->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca'
            ]);
        } catch (\Exception $e) {
            Log::error('Error markNotificationAsRead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi'
            ], 500);
        }
    }

    /**
     * Mark notifikasi sebagai sudah dibaca (alias)
     */
    public function markAsRead($id)
    {
        return $this->markNotificationAsRead($id);
    }

    /**
     * Mark semua notifikasi sebagai sudah dibaca
     */
    public function markAllNotificationsAsRead()
    {
        try {
            ProjectNotification::where('is_read', false)->update(['is_read' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sudah dibaca'
            ]);
        } catch (\Exception $e) {
            Log::error('Error markAllNotificationsAsRead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi'
            ], 500);
        }
    }

    /**
     * Mark semua notifikasi sebagai sudah dibaca (alias)
     */
    public function markAllAsRead()
    {
        return $this->markAllNotificationsAsRead();
    }
}