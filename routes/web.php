<?php

use App\Http\Controllers\CashflowController;
use App\Http\Controllers\OwnerBerandaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin\PerusahaanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminKaryawanController;
use App\Http\Controllers\CatatanRapatController;
use App\Http\Controllers\DataProjectController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\SuratKerjasamaController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GeneralManagerTaskController;
use App\Http\Controllers\KaryawanProfileController;
use App\Http\Controllers\ManagerDivisiTaskController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\TimDivisiController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\BerandaFinanceController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\GMPerusahaanController;
use App\Http\Controllers\HRTaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HRKPAController;
use App\Http\Controllers\TunjanganController;
use App\Http\Controllers\ManagerDivisiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HR\GajiHrController;
use App\Http\Controllers\HR\TunjanganHrController;
use App\Http\Controllers\Finance\PayrollController;
use App\Http\Controllers\HR\AbsensiHrController;
use App\Http\Controllers\GeneralManajerController;
use App\Http\Controllers\GeneralManagerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\TimController;
use App\Http\Controllers\GajiTemplateController;
use App\Models\GajiTemplate;
use App\Http\Controllers\LemburController;



/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

if (!function_exists('redirectToRolePage')) {
    function redirectToRolePage($user)
    {
        return match ($user->role) {
            'admin', 'finance' => redirect()->route("{$user->role}.beranda"),
            'general_manager' => redirect()->route('general_manajer.home'),
            'hr' => redirect()->route('hr.home'),
            'karyawan', 'manager_divisi', 'owner' => redirect()->route("{$user->role}.home"),
            default => redirect('/login')
        };
    }
}


/*
|--------------------------------------------------------------------------
| Public Routes (Tidak Perlu Login)
|--------------------------------------------------------------------------
*/

// Hanya Admin dan Finance yang bisa akses Order
Route::middleware(['auth', 'role:admin,finance'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::put('/{id}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
});


// Landing Page
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Route untuk HR
Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    
    // FITUR 1: Monitoring Tugas
    Route::get('tasks', [HRTaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/create', [HRTaskController::class, 'create'])->name('tasks.create');
    Route::post('tasks', [HRTaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{id}', [HRTaskController::class, 'show'])->name('tasks.show');
    Route::get('tasks/{id}/edit', [HRTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{id}', [HRTaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{id}', [HRTaskController::class, 'destroy'])->name('tasks.destroy');
    Route::put('tasks/{id}/status', [HRTaskController::class, 'updateStatus'])->name('tasks.update-status');
});

// Notifikasi Project
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/project/notifications', [DataProjectController::class, 'getNotifications']);
    Route::put('/project/notifications/{id}/read', [DataProjectController::class, 'markNotificationAsRead']);
    Route::put('/project/notifications/mark-all-read', [DataProjectController::class, 'markAllNotificationsAsRead']);
    Route::get('/admin/project/notifications', [ProjectController::class, 'getNotifications']);
    Route::put('/admin/project/notifications/{id}/read', [ProjectController::class, 'markAsRead']);
    Route::put('/admin/project/notifications/mark-all-read', [ProjectController::class, 'markAllRead']);
});

// ROUTE UNTUK HR - KPA
Route::middleware(['auth', 'role:hr'])->prefix('hr')->group(function () {
    
    // KPA
    Route::get('/kpa', [HRKPAController::class, 'index'])->name('hr.kpa.index');
    Route::get('/kpa/penilaian', [HRKPAController::class, 'formPenilaian'])->name('hr.kpa.penilaian');
    Route::post('/kpa/penilaian', [HRKPAController::class, 'simpanPenilaian'])->name('hr.kpa.penilaian.store');
    Route::get('/kpa/detail/{id}', [HRKPAController::class, 'detail'])->name('hr.kpa.detail');
    Route::get('/kpa/show/{id}', [HRKPAController::class, 'show'])->name('hr.kpa.show');
    Route::get('/kpa/export-pdf', [HRKPAController::class, 'exportPDF'])->name('hr.kpa.export-pdf');
    Route::post('/kpa/hitung-semua', [HRKPAController::class, 'hitungSemua'])->name('hr.kpa.hitung-semua');
    Route::put('/kpa/update-bobot', [HRKPAController::class, 'updateBobot'])->name('hr.kpa.update-bobot');
    Route::get('/kpa/preview-nilai', [HRKPAController::class, 'previewNilai'])->name('hr.kpa.preview-nilai');
});

// Target Kuantitas
Route::get('/kpa/target-kuantitas', [HRKPAController::class, 'targetKuantitasIndex'])->name('hr.kpa.target-kuantitas');
Route::post('/kpa/target-kuantitas', [HRKPAController::class, 'targetKuantitasStore'])->name('hr.kpa.target-kuantitas.store');

// Aspek & Indikator
Route::get('/kpa/aspek-indikator', [HRKPAController::class, 'aspekIndikatorIndex'])->name('hr.kpa.aspek-indikator');
Route::post('/kpa/aspek', [HRKPAController::class, 'aspekStore'])->name('hr.kpa.aspek.store');
Route::post('/kpa/indikator', [HRKPAController::class, 'indikatorStore'])->name('hr.kpa.indikator.store');
Route::get('/hr/kpa/preview-nilai', [HRKPAController::class, 'previewNilai'])->name('hr.kpa.preview-nilai');
// Hapus aspek & indikator
Route::delete('/kpa/aspek/{id}', [HRKPAController::class, 'aspekDestroy'])->name('hr.kpa.aspek.destroy');
Route::delete('/kpa/indikator/{id}', [HRKPAController::class, 'indikatorDestroy'])->name('hr.kpa.indikator.destroy');
// Tunjangan
Route::get('/tunjangan', [HRKPAController::class, 'tunjanganIndex'])->name('hr.tunjangan.index');
Route::post('/tunjangan', [HRKPAController::class, 'simpanTunjangan'])->name('hr.tunjangan.store');
    
// ========== ABSENSI HR ==========
Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    
    // Halaman utama kelola absensi
    Route::get('/kelola-absensi', [AbsensiHrController::class, 'index'])->name('kelola_absensi');
    
    // Surat Sakit
    Route::get('/absensi/surat-sakit', [AbsensiHrController::class, 'suratSakit'])->name('absensi.surat-sakit');
    Route::post('/absensi/approve-sakit/{id}', [AbsensiHrController::class, 'approveSakit'])->name('absensi.approve-sakit');
    Route::post('/absensi/reject-sakit/{id}', [AbsensiHrController::class, 'rejectSakit'])->name('absensi.reject-sakit');
    Route::get('/absensi/detail-surat/{id}', [AbsensiHrController::class, 'detailSurat'])->name('absensi.detail-surat');
});

// ========== TUNJANGAN HR ==========
Route::get('/tunjangan', [TunjanganHrController::class, 'index'])->name('hr.tunjangan.index');
Route::post('/tunjangan', [TunjanganHrController::class, 'store'])->name('hr.tunjangan.store');
Route::post('/tunjangan/add', [TunjanganHrController::class, 'addTunjangan'])->name('hr.tunjangan.add');
Route::put('/tunjangan/{id}', [TunjanganHrController::class, 'update'])->name('hr.tunjangan.update');
Route::delete('/tunjangan/{id}', [TunjanganHrController::class, 'destroy'])->name('hr.tunjangan.destroy');

// Routes untuk Gaji Template
Route::middleware(['auth'])->prefix('hr/gaji-template')->group(function () {
    Route::get('/', [GajiTemplateController::class, 'index'])->name('hr.gaji-template.index');
    Route::post('/store', [GajiTemplateController::class, 'store'])->name('hr.gaji-template.store');
    Route::put('/{id}', [GajiTemplateController::class, 'update'])->name('hr.gaji-template.update');
    Route::delete('/{id}', [GajiTemplateController::class, 'destroy'])->name('hr.gaji-template.delete');
});

Route::get('/api/gaji-template', function (Request $request) {
    $role = $request->query('role');
    $divisiId = $request->query('divisi_id');
    
    if (!$role) {
        return response()->json(['success' => false, 'message' => 'Role required'], 400);
    }
    
    // Cari template (prioritaskan yang spesifik per divisi)
    $template = GajiTemplate::where('role', $role);
    
    if ($divisiId) {
        $template->where(function($q) use ($divisiId) {
            $q->where('divisi_id', $divisiId)->orWhereNull('divisi_id');
        });
    } else {
        $template->whereNull('divisi_id');
    }
    
    $template = $template->first();
    
    if ($template) {
        return response()->json([
            'success' => true,
            'data' => [
                'gaji_pokok' => $template->gaji_pokok,
                'tunjangan_tetap' => $template->tunjangan_tetap,
                'tunjangan_kinerja' => $template->tunjangan_kinerja,
                'gaji_formatted' => 'Rp ' . number_format($template->gaji_pokok, 0, ',', '.')
            ]
        ]);
    }
    
    // Default values
    $defaults = [
        'general_manager' => 15000000,
        'manager_divisi' => 10000000,
        'finance' => 8000000,
        'hr' => 7000000,
        'karyawan' => 5000000,
    ];
    
    return response()->json([
        'success' => true,
        'data' => [
            'gaji_pokok' => $defaults[$role] ?? 5000000,
            'gaji_formatted' => 'Rp ' . number_format($defaults[$role] ?? 5000000, 0, ',', '.')
        ],
        'is_default' => true
    ]);
})->name('api.gaji-template');

// API endpoint untuk get template (untuk auto-fill di form)
Route::get('/api/gaji-template', [GajiTemplateController::class, 'getTemplate']);

Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/gaji', [GajiHrController::class, 'index'])->name('gaji.index');
    Route::post('/gaji', [GajiHrController::class, 'store'])->name('gaji.store');
    Route::post('/gaji/apply-template', [GajiHrController::class, 'applyTemplate'])->name('gaji.apply-template');
    Route::post('/gaji/kirim-ke-finance', [GajiHrController::class, 'kirimKeFinance'])->name('gaji.kirim-ke-finance');
});
// ========== ROUTE LEMBUR ==========

// Untuk Karyawan
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::prefix('lembur')->name('lembur.')->group(function () {
        Route::get('/', [LemburController::class, 'index'])->name('index');
        Route::get('/create', [LemburController::class, 'create'])->name('create');
        Route::post('/', [LemburController::class, 'store'])->name('store');
    });
});

// Untuk HR 
Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::prefix('lembur')->name('lembur.')->group(function () {
        Route::get('/', [LemburController::class, 'hrIndex'])->name('index');
        Route::post('/{id}/approve', [LemburController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LemburController::class, 'reject'])->name('reject');
    });
});

// Untuk Finance
Route::middleware(['auth', 'role:finance'])->prefix('finance')->name('finance.')->group(function () {
    Route::prefix('lembur')->name('lembur.')->group(function () {
        Route::get('/', [LemburController::class, 'financeIndex'])->name('index');
        Route::post('/mark-paid', [LemburController::class, 'markAsPaid'])->name('mark-paid');
        // Di dalam group finance payroll
Route::post('/{periodId}/send-notification/{detailId}', [PayrollController::class, 'sendNotificationSlip'])->name('send-notification');
Route::post('/{periodId}/send-notification-mass', [PayrollController::class, 'sendNotificationMass'])->name('send-notification-mass');
    });
});

// Di dalam route api group
// Route untuk notifikasi
Route::get('/notifications', function() {
    $user = Auth::user();
    $notifications = \App\Models\Notification::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'link' => $n->link,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at,
                'time_ago' => $n->created_at->diffForHumans(),
            ];
        });
    
    $unreadCount = \App\Models\Notification::where('user_id', $user->id)
        ->where('is_read', false)
        ->count();
    
    return response()->json([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
        'total' => $notifications->count(),
    ]);
})->name('api.notifications');

Route::post('/notifications/{id}/read', function($id) {
    $notification = \App\Models\Notification::where('user_id', Auth::id())
        ->where('id', $id)
        ->first();
    
    if ($notification) {
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
    
    return response()->json(['success' => false], 404);
})->name('api.notifications.read');

Route::post('/notifications/read-all', function() {
    \App\Models\Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);
    
    return response()->json(['success' => true]);
})->name('api.notifications.read-all');

// Di dalam Route::prefix('api')->group(function () {
Route::get('/notifications', function() {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['notifications' => [], 'unread_count' => 0]);
    }
    
    $notifications = \App\Models\Notification::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'link' => $n->link,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at,
                'time_ago' => $n->created_at->diffForHumans(),
            ];
        });
    
    $unreadCount = \App\Models\Notification::where('user_id', $user->id)
        ->where('is_read', false)
        ->count();
    
    return response()->json([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
        'total' => $notifications->count(),
    ]);
})->name('api.notifications');

Route::post('/notifications/{id}/read', function($id) {
    $notification = \App\Models\Notification::where('user_id', Auth::id())
        ->where('id', $id)
        ->first();
    
    if ($notification) {
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
    
    return response()->json(['success' => false], 404);
})->name('api.notifications.read');

Route::post('/notifications/read-all', function() {
    \App\Models\Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);
    
    return response()->json(['success' => true]);
})->name('api.notifications.read-all');

// Route untuk karyawan - slip gaji
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::prefix('slip-gaji')->name('slip-gaji.')->group(function () {
        Route::get('/', [App\Http\Controllers\Karyawan\SlipGajiController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Karyawan\SlipGajiController::class, 'show'])->name('show');
    });
});

// ========== PENGUMUMAN (Admin & HR - Satu Route) ==========
Route::middleware(['auth'])->group(function () {
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/data', [PengumumanController::class, 'getData'])->name('pengumuman.data');
    Route::get('/pengumuman/{id}', [PengumumanController::class, 'show'])->name('pengumuman.show');
    Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
});

Route::get('/admin/karyawan/get-gaji/{userId}', [AdminKaryawanController::class, 'getGajiTerbaru']);

// ========== FINANCE - PENGGAJIAN ==========
Route::middleware(['auth', 'role:finance'])->prefix('finance')->name('finance.')->group(function () {
    
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/payroll/{id}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::get('/payroll/{periodId}/slip/{detailId}', [PayrollController::class, 'slip'])->name('payroll.slip');
    // Di dalam group finance payroll
Route::post('/{periodId}/send-notification/{detailId}', [PayrollController::class, 'sendNotificationSlip'])->name('send-notification');
    // TAMBAHKAN ROUTE INI
    Route::post('/payroll/{id}/hitung-potongan', [PayrollController::class, 'hitungSemuaPotongan'])->name('payroll.hitung-potongan');
    
    // Ambil data dari HR
    Route::get('/payroll-dari-hr', [PayrollController::class, 'daftarDariHR'])->name('payroll.dari-hr');
    Route::post('/payroll-ambil-dari-hr', [PayrollController::class, 'ambilDariHR'])->name('payroll.ambil-dari-hr');
    
    // Approve & Payment
    Route::post('/payroll/{id}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
    Route::post('/payroll/{id}/paid', [PayrollController::class, 'markAsPaid'])->name('payroll.paid');
});

// Routes for getting lists (no auth required for these specific endpoints)
Route::get('/roles/list', [RoleController::class, 'list'])->name('roles.list');
Route::get('/divisis/list', [DivisiController::class, 'list'])->name('divisis.list');
Route::get('/tims/by-divisi/{divisiId}', [TimController::class, 'getByDivisi'])->name('tims.by-divisi');

// Routes for admin karyawan (API endpoints)
Route::middleware(['auth'])->prefix('admin/karyawan')->group(function () {
    Route::post('/store', [AdminKaryawanController::class, 'store'])->name('admin.karyawan.store');
    Route::put('/update/{id}', [AdminKaryawanController::class, 'update'])->name('admin.karyawan.update');
    Route::delete('/delete/{id}', [AdminKaryawanController::class, 'destroy'])->name('admin.karyawan.delete');
    Route::get('/get/{id}', [AdminKaryawanController::class, 'getKaryawan'])->name('admin.karyawan.get');
});

// Routes for divisi management (if needed)
Route::middleware(['auth'])->prefix('divisi')->group(function () {
    Route::get('/', [DivisiController::class, 'index']);
    Route::post('/store', [DivisiController::class, 'store']);
    Route::put('/{id}', [DivisiController::class, 'update']);
    Route::delete('/{id}', [DivisiController::class, 'destroy']);
});

// Routes for tim management (if needed)
Route::middleware(['auth'])->prefix('tim')->group(function () {
    Route::get('/', [TimController::class, 'index']);
    Route::post('/store', [TimController::class, 'store']);
    Route::put('/{id}', [TimController::class, 'update']);
    Route::delete('/{id}', [TimController::class, 'destroy']);
    Route::get('/{id}/members', [TimController::class, 'getMembers']);
});

// Route untuk karyawan (API endpoints)
Route::prefix('admin/karyawan')->middleware(['auth'])->group(function () {
    Route::post('/store', [AdminKaryawanController::class, 'store'])->name('admin.karyawan.store');
    Route::put('/update/{id}', [AdminKaryawanController::class, 'update'])->name('admin.karyawan.update');
    Route::delete('/delete/{id}', [AdminKaryawanController::class, 'destroy'])->name('admin.karyawan.delete');
    Route::get('/get/{id}', [AdminKaryawanController::class, 'getKaryawan'])->name('admin.karyawan.get');
});

// ========== GENERAL MANAGER ==========
Route::middleware(['auth', 'role:general_manager'])->prefix('general_manager')->name('general_manajer.')->group(function () {
    
    // Halaman Beranda
    Route::get('/home', [GeneralManagerController::class, 'home'])->name('home');
    
    // Halaman Data Karyawan
    Route::get('/data_karyawan', [GeneralManagerController::class, 'data_karyawan'])->name('data_karyawan');
    
    // Halaman Layanan
    Route::get('/layanan', [GeneralManagerController::class, 'layanan'])->name('layanan');
    
    // Halaman Data Project
    Route::get('/data_project', [GeneralManagerController::class, 'data_project'])->name('data_project');
    Route::put('/data_project/{id}', [GeneralManagerController::class, 'update_project'])->name('data_project.update');
    
    // Halaman Tim & Divisi
    Route::get('/tim_divisi', [GeneralManagerController::class, 'tim_divisi'])->name('tim_divisi');
    
    // 🔥 HALAMAN TOP & LOW GRADE
    Route::get('/top-low-grade', [GeneralManagerController::class, 'index'])->name('top_low_grade');
    
    // API Endpoints
    Route::get('/api/manager-ranking', [GeneralManagerController::class, 'managerRanking'])->name('api.manager-ranking');
    Route::get('/api/divisi-ranking', [GeneralManagerController::class, 'divisiRanking'])->name('api.divisi-ranking');
});

// Route untuk Manager Divisi - Top Low Grade
Route::middleware(['auth', 'role:manager_divisi'])->prefix('manager-divisi')->group(function () {
    Route::get('/top-low-grade', [App\Http\Controllers\ManagerDivisiController::class, 'dashboard'])->name('manager_divisi.top_low_grade');
});

Route::prefix('manager/divisi')->name('manager.divisi.')->group(function () {
  
    // Halaman utama
    Route::get('/kelola-tugas', [ManagerDivisiTaskController::class, 'index'])->name('manager.kelola-tugas');
    Route::get('/tugas-dari-karyawan', [ManagerDivisiTaskController::class, 'tugasDariKaryawan'])->name('manager.tugas-dari-karyawan');
    
    // API Endpoints
    Route::get('/api/tasks', [ManagerDivisiTaskController::class, 'getTasksApi'])->name('manager.api.tasks');
    Route::get('/api/tasks/statistics', [ManagerDivisiTaskController::class, 'getStatistics'])->name('manager.api.statistics');
    Route::get('/api/projects-dropdown', [ManagerDivisiTaskController::class, 'getProjectsDropdown'])->name('manager.api.projects');
    Route::get('/api/karyawan-dropdown', [ManagerDivisiTaskController::class, 'getKaryawanDropdown'])->name('manager.api.karyawan');
    
    // CRUD Tasks
    Route::post('/tasks/createTask', [ManagerDivisiTaskController::class, 'createTask'])->name('manager.tasks.create');
    Route::put('/tasks/{id}', [ManagerDivisiTaskController::class, 'update'])->name('manager.tasks.update');
    Route::delete('/tasks/{id}', [ManagerDivisiTaskController::class, 'destroy'])->name('manager.tasks.destroy');
    Route::get('/tasks/{id}', [ManagerDivisiTaskController::class, 'show'])->name('manager.tasks.show');
    
    // ========== ENDPOINT PENTING UNTUK APPROVE/REVISI ==========
    Route::post('/api/tugas-karyawan/{id}/approve', [ManagerDivisiTaskController::class, 'approveTaskFromKaryawan'])
        ->name('manager.api.tugas-karyawan.approve');
    // ============================================================
    
    // Tugas dari karyawan
    Route::get('/api/tasks-from-karyawan', [ManagerDivisiTaskController::class, 'getTasksFromKaryawan'])->name('manager.api.tasks-from-karyawan');
    Route::get('/api/tasks-from-karyawan/statistics', [ManagerDivisiTaskController::class, 'getTasksFromKaryawanStatistics'])->name('manager.api.tasks-from-karyawan.statistics');
    Route::get('/api/tasks-from-karyawan/{id}', [ManagerDivisiTaskController::class, 'getTaskFromKaryawanDetail'])->name('manager.api.tasks-from-karyawan.detail');
    
    // Karyawan
    Route::get('/api/karyawan-in-divisi', [ManagerDivisiTaskController::class, 'getKaryawanInDivisi'])->name('manager.api.karyawan-in-divisi');
});

// Route notifikasi untuk semua user
Route::middleware(['auth'])->group(function () {
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Route untuk karyawan
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    
    // Dashboard/Beranda
    Route::get('/home', [KaryawanController::class, 'home'])->name('home');
    
    // Presensi
    Route::get('/absensi', [KaryawanController::class, 'absensi'])->name('absensi');
    
    // Manage Tugas
    Route::get('/tugas', [TaskController::class, 'karyawanTasks'])->name('tugas.index');
    Route::get('/tugas/{id}', [TaskController::class, 'karyawanShow'])->name('tugas.show');
    Route::post('/tugas/{id}/terima', [TaskController::class, 'terimaTugas'])->name('tugas.terima');
    
    // ========== TAMBAHKAN ROUTE INI ==========
    Route::post('/tugas/{id}/upload', [TaskController::class, 'uploadTaskFile'])->name('tugas.upload');
    // ========================================
    
    // Pengajuan Cuti
    Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
});

Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/absensi', function() {
        return view('karyawan.absensi');
    })->name('absensi');
});

// Public API endpoints
Route::prefix('api')->name('api.public.')->group(function () {
    Route::get('/contact', [SettingController::class, 'getContactData'])->name('contact');
    Route::get('/about', [SettingController::class, 'getAboutData'])->name('about');
    Route::get('/articles', [SettingController::class, 'getArticlesData'])->name('articles');
    Route::get('/portfolios', [SettingController::class, 'getPortfoliosData'])->name('portfolios');
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.public');
    Route::get('/operational-hours', [SettingController::class, 'getOperationalHours'])->name('operational.hours');

    // Test API untuk finance (temporary)
    Route::get('/finance/layanan-test', function () {
        return response()->json([
            'success' => true,
            'data' => [
                ['id' => 1, 'nama_layanan' => 'Web Development', 'harga' => 10000000, 'deskripsi' => 'Pembuatan website'],
                ['id' => 2, 'nama_layanan' => 'Mobile App Development', 'harga' => 15000000, 'deskripsi' => 'Pembuatan aplikasi mobile'],
                ['id' => 3, 'nama_layanan' => 'UI/UX Design', 'harga' => 5000000, 'deskripsi' => 'Desain antarmuka'],
            ],
            'message' => 'Data dummy untuk testing finance layanan'
        ]);
    })->name('api.finance.layanan.test');
});

// Auth routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login-process', [LoginController::class, 'login'])->name('login.process');

// DEBUG: Check auth and session state
Route::get('/debug/auth', function() {
    return [
        'authenticated' => Auth::check(),
        'user' => Auth::user() ? Auth::user()->only(['id', 'name', 'email', 'role']) : null,
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
    ];
});

// API Test routes (public)
Route::prefix('api')->group(function() {
    Route::get('/test', function() {
        return response()->json([
            'success' => true,
            'message' => 'API Works!',
            'timestamp' => now()->toDateTimeString()
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    Route::get('/logout-get', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout.get');

    // Profile Redirect
    Route::get('/profile', function () {
        $user = Auth::user();
        return match ($user->role) {
            'admin' => redirect()->route('admin.settings.index'),
            'karyawan' => redirect()->route('karyawan.profile'),
            'manager_divisi' => redirect()->route('manager_divisi.home'),
            'general_manager' => redirect()->route('general_manajer.home'),
            'owner' => redirect()->route('owner.home'),
            'finance' => redirect()->route('finance.beranda'),
            'hr' => redirect()->route('hr.home'),
            default => abort(404)
        };
    })->name('profile');

    // Orders & Invoices
    Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');

    // Pegawai Management
    Route::prefix('pegawai')->name('pegawai.')->group(function () {
        Route::get('/', [KaryawanController::class, 'indexPegawai'])->name('index');
        Route::post('/', [KaryawanController::class, 'storePegawai'])->name('store');
        Route::get('/{id}/edit', [KaryawanController::class, 'editPegawai'])->name('edit');
        Route::put('/{id}', [KaryawanController::class, 'updatePegawai'])->name('update');
        Route::delete('/{id}', [KaryawanController::class, 'destroyPegawai'])->name('destroy');
    });

    // HR specific routes (role middleware can be added where appropriate)
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/home', [KaryawanController::class, 'home'])->name('home');
        Route::get('/data-karyawan', [KaryawanController::class, 'indexPegawai'])->name('data_karyawan');
        Route::get('/kelola-absensi', [KaryawanController::class, 'absensiPage'])->name('kelola_absensi');
    });

    Route::prefix('catatan-rapat')->name('catatan_rapat.')->group(function () {
        Route::get('/', [CatatanRapatController::class, 'index'])->name('index');
        Route::post('/', [CatatanRapatController::class, 'store'])->name('store');
        Route::get('/data', [CatatanRapatController::class, 'getData'])->name('data');
        Route::put('/{id}', [CatatanRapatController::class, 'update'])->name('update')->whereNumber('id');
        Route::delete('/{id}', [CatatanRapatController::class, 'destroy'])->name('destroy')->whereNumber('id');
        Route::get('/{id}', [CatatanRapatController::class, 'show'])->name('show')->whereNumber('id');
    });

    // =========== GLOBAL API ROUTES ===========
    Route::prefix('api')->group(function () {
        // Users
        Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');

        // Projects API (Global - untuk semua role yang membutuhkan)
        Route::get('/projects', [DataProjectController::class, 'getAllProjects'])->name('projects.all');

        // Cuti API
        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/check-leave-status', [CutiController::class, 'checkLeaveStatusApi'])->name('check-leave-status');
            Route::get('/{id}/history', [CutiController::class, 'getHistory'])->name('history');
            Route::get('/summary', [CutiController::class, 'getSummary'])->name('summary');
            Route::post('/calculate-duration', [CutiController::class, 'calculateDuration'])->name('calculate-duration');
        });

        // Tasks API - Global
        Route::prefix('tasks')->name('tasks.')->group(function () {
            // Store task global
            Route::post('/', [TaskController::class, 'store'])->name('store');
            
            // Route khusus untuk manager divisi
            Route::post('/store-for-manager', [TaskController::class, 'storeForManager'])
                ->middleware(['role:manager_divisi'])
                ->name('store.for-manager');
            
            // Test endpoint untuk debugging
            Route::post('/test', [TaskController::class, 'testCreateTask'])->name('test');
            
            Route::get('/{id}', [TaskController::class, 'show'])->name('show');
            Route::get('/{id}/detail', [TaskController::class, 'getTaskDetailApi'])->name('detail');
            Route::post('/{id}/upload-file', [TaskController::class, 'uploadTaskFile'])->name('upload.file');
            Route::post('/{id}/complete', [TaskController::class, 'markAsComplete'])->name('complete');
            Route::post('/{id}/status', [TaskController::class, 'updateTaskStatus'])->name('status');
            Route::get('/{id}/comments', [TaskController::class, 'getComments'])->name('comments');
            Route::post('/{id}/comments', [TaskController::class, 'storeComment'])->name('comments.store');
            Route::get('/{id}/files', [TaskController::class, 'getTaskFiles'])->name('files');
            Route::get('/files/{file}/download', [TaskController::class, 'downloadFileById'])->name('files.download');
            Route::delete('/files/{file}', [TaskController::class, 'deleteFile'])->name('files.delete');
            Route::get('/{id}/download', [TaskController::class, 'downloadSubmission'])->name('download.submission');
            Route::get('/statistics', [TaskController::class, 'apiGetStatistics'])->name('statistics');
            Route::get('/karyawan/statistics', [TaskController::class, 'getKaryawanStatistics'])->name('karyawan.statistics');
        });

        // INVOICE API ROUTES
        Route::prefix('invoices')->name('api.invoices.')->group(function() {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/mark-printed', [InvoiceController::class, 'markPrinted'])->name('mark.printed');
        });

        // Admin/GM/HR Absensi API
        Route::prefix('admin')->middleware(['role:admin,general_manager,hr'])->name('admin.')->group(function () {
            Route::get('/absensi', [AbsensiController::class, 'apiIndex'])->name('absensi');
            Route::get('/absensi/ketidakhadiran', [AbsensiController::class, 'apiIndexKetidakhadiran'])->name('ketidakhadiran');
            Route::get('/absensi/stats', [AbsensiController::class, 'apiStatistics'])->name('stats');
            Route::get('/kehadiran-per-divisi', [AbsensiController::class, 'apiKehadiranPerDivisi'])->name('kehadiran.divisi');
            Route::post('/absensi', [AbsensiController::class, 'apiStore'])->name('absensi.store');
            Route::get('/absensi/{id}', [AbsensiController::class, 'apiShow'])->name('absensi.show');
            Route::put('/absensi/{id}', [AbsensiController::class, 'apiUpdate'])->name('absensi.update');
            Route::delete('/absensi/{id}', [AbsensiController::class, 'apiDestroy'])->name('absensi.destroy');
            Route::post('/absensi/cuti', [AbsensiController::class, 'apiStoreCuti'])->name('absensi.cuti.store');
            Route::put('/absensi/cuti/{id}', [AbsensiController::class, 'apiUpdateCuti'])->name('absensi.cuti.update');
            Route::post('/absensi/{id}/verify', [AbsensiController::class, 'apiVerify'])->name('absensi.verify');
            Route::post('/cuti/{id}/verify', [CutiController::class, 'apiVerify'])->name('cuti.verify');
        });

        // Owner API
        Route::prefix('owner')->middleware(['role:owner'])->name('owner.')->group(function () {
            Route::get('/data', [OwnerController::class, 'getData'])->name('data');
            Route::get('/service-count', [OwnerController::class, 'getServiceCount'])->name('service.count');
            Route::get('/attendance-percentage', [OwnerController::class, 'getAttendancePercentage'])->name('attendance.percentage');
            Route::get('/attendance-by-division', [OwnerController::class, 'getAttendanceByDivision'])->name('attendance.by-division');
            Route::get('/dashboard-stats', [OwnerController::class, 'getDashboardStats'])->name('dashboard.stats');
            // Owner Specific APIs for Meeting/Announcements
            Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingNotesDatesForOwner']);
            Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesByDateForOwner']);
            Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesForOwner']);
            Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsForOwner']);
        });
        
        // Manager Divisi Specific APIs (Global prefix)
        Route::prefix('manager-divisi')->middleware(['role:manager_divisi'])->name('manager.divisi.')->group(function () {
            Route::get('/tasks', [ManagerDivisiTaskController::class, 'getTasksApi'])->name('tasks');
            Route::get('/tasks/statistics', [ManagerDivisiTaskController::class, 'getStatistics'])->name('tasks.statistics');
            Route::get('/karyawan-by-divisi/{divisi}', [ManagerDivisiTaskController::class, 'getKaryawanByDivisi'])->name('karyawan.by_divisi');
            
            // Route khusus untuk projects-dropdown
            Route::get('/project-dropdown', [ManagerDivisiTaskController::class, 'getProjectsDropdown'])->name('project.dropdown');
            
            // Route alternatif untuk kompatibilitas
            Route::get('/projects', [ManagerDivisiTaskController::class, 'getProjectsDropdown'])->name('projects');
            
            // Manager Divisi Specific APIs for Meeting/Announcements
            Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingNotesDatesForManager']);
            Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesByDateForManager']);
            Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesForManager']);
            Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsForManager']);
        });

        // General Manager Specific APIs
        Route::prefix('general-manager')->middleware(['role:general_manager'])->name('general.manager.')->group(function () {
            Route::get('/tasks', [GeneralManagerTaskController::class, 'getTasksApi'])->name('tasks');
            Route::get('/tasks/statistics', [GeneralManagerTaskController::class, 'getStatistics'])->name('tasks.statistics');
            Route::get('/karyawan-by-divisi/{divisi}', [GeneralManagerTaskController::class, 'getKaryawanByDivisi'])->name('karyawan.by_divisi');
            // GM Specific APIs for Meeting/Announcements
            Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingNotesDatesForGM']);
            Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesByDateForGM']);
            Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesForGM']);
            Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsForGM']);
        });

        // Finance API
        Route::prefix('finance')->middleware(['role:finance'])->name('finance.')->group(function () {
            Route::get('/layanan', [LayananController::class, 'financeApi'])->name('layanan.api');
            Route::get('/invoices', [InvoiceController::class, 'getFinanceInvoices'])->name('invoices.api');
            // Kwitansi print data (used by finance kwitansi blade JS)
            Route::get('/kwitansi/{id}/cetak-data', [KwitansiController::class, 'getKwitansiForPrint'])->name('kwitansi.cetak.data');
        });

        // HR Specific APIs
        Route::prefix('hr')->middleware(['role:hr'])->name('hr.')->group(function () {
            Route::get('/karyawan/count', function () {
                return response()->json(['count' => \App\Models\User::where('role', 'karyawan')->count()]);
            })->name('karyawan.count');
            Route::get('/karyawan/active', function () {
                return response()->json(['count' => \App\Models\User::where('role', 'karyawan')->count()]);
            })->name('karyawan.active');
            Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingDatesApi'])->name('meeting.notes.dates');
            Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesApi'])->name('meeting.notes.get');
            Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesApi'])->name('announcements.dates');
            Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsApi'])->name('announcements');
        });

        // ============== PERBAIKAN: Tambahkan Kwitansi API Routes di sini ==============
        Route::prefix('kwitansi')->name('kwitansi.')->group(function () {
            Route::get('/', [KwitansiController::class, 'getKwitansiData'])->name('index');
            Route::get('/{id}', [KwitansiController::class, 'show'])->name('show');
            Route::post('/', [KwitansiController::class, 'store'])->name('store');
            Route::put('/{id}', [KwitansiController::class, 'update'])->name('update');
            Route::delete('/{id}', [KwitansiController::class, 'destroy'])->name('destroy');
        });

        // Invoice API untuk dropdown
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'getInvoicesForDropdown'])->name('dropdown');
            Route::get('/{id}', [InvoiceController::class, 'getInvoiceDetail'])->name('detail');
        });

        // Divisi API (Global)
        Route::get('/divisis/list', function () {
            try {
                $divisis = \App\Models\Divisi::select('id', 'divisi')->get();
                return response()->json($divisis);
            } catch (\Exception $e) {
                \Log::error('Divisi list error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to load divisis', 'message' => $e->getMessage()], 500);
            }
        })->name('divisis.list');
    });
});

/*
|--------------------------------------------------------------------------
| Role: ADMIN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,hr'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/beranda', [AdminController::class, 'beranda'])->name('beranda');
        Route::get('/home', function () {
            return redirect()->route('admin.beranda');
        });

        // Profile
        Route::get('/profile', function () {
            return view('admin.profile');
        })->name('profile');

        // API untuk data
        Route::get('/catatan_rapat/data', [CatatanRapatController::class, 'getData'])->name('catatan_rapat.data');
        Route::get('/users/data', [UserController::class, 'getData']);

        // USER MANAGEMENT
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::post('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
        Route::get('/data_user', function () {
            return redirect()->route('admin.user');
        });

        Route::get('/perusahaan', [PerusahaanController::class, 'index'])->name('perusahaan.index');
        Route::get('/perusahaan/create', [PerusahaanController::class, 'create'])->name('perusahaan.create');
        Route::post('/perusahaan', [PerusahaanController::class, 'store'])->name('perusahaan.store');
        Route::get('/perusahaan/{perusahaan}/edit', [PerusahaanController::class, 'edit'])->name('perusahaan.edit');
        Route::put('/perusahaan/{perusahaan}', [PerusahaanController::class, 'update'])->name('perusahaan.update');
        Route::delete('/perusahaan/{perusahaan}', [PerusahaanController::class, 'destroy'])->name('perusahaan.delete');
        Route::get('/perusahaan/data', [PerusahaanController::class, 'getDataForDropdown'])
            ->name('perusahaan.data');

        // KARYAWAN MANAGEMENT - ROUTE YANG DIPERBAIKI
        Route::get('/data_karyawan', [AdminKaryawanController::class, 'index'])->name('karyawan.index');
        Route::post('/karyawan/store', [AdminKaryawanController::class, 'store'])->name('karyawan.store');

        // Untuk update gunakan PUT dan POST untuk fallback
        Route::put('/karyawan/update/{id}', [AdminKaryawanController::class, 'update'])->name('karyawan.update');
        Route::post('/karyawan/update/{id}', [AdminKaryawanController::class, 'update'])->name('karyawan.update.post');

        // Untuk delete
        Route::delete('/karyawan/delete/{id}', [AdminKaryawanController::class, 'destroy'])->name('karyawan.delete');

        // Untuk get data
        Route::get('/karyawan/get/{id}', [AdminKaryawanController::class, 'getKaryawanData'])->name('karyawan.get.data');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');

        // KEUANGAN
        Route::get('/keuangan', function () {
            return view('admin.keuangan');
        })->name('keuangan.index');

        Route::get('/data_order', function () {
            return view('admin.data_order');
        })->name('data_order');

        // Task Management
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index');
            Route::get('/create', [TaskController::class, 'create'])->name('create');
            Route::post('/', [TaskController::class, 'store'])->name('store');
            Route::get('/{id}', [TaskController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [TaskController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TaskController::class, 'update'])->name('update');
            Route::delete('/{id}', [TaskController::class, 'destroy'])->name('destroy');

            // Upload & File Admin
            Route::post('/{id}/upload-file', [TaskController::class, 'uploadFileAdmin'])->name('upload.file');
            Route::get('/files/{file}/download', [TaskController::class, 'downloadFileById'])->name('files.download');
            Route::delete('/files/{file}', [TaskController::class, 'deleteFile'])->name('files.delete');

            // Comments
            Route::get('/{id}/comments', [TaskController::class, 'getComments'])->name('comments');
            Route::post('/{id}/comments', [TaskController::class, 'storeComment'])->name('comments.store');

            // Status updates
            Route::post('/{id}/status', [TaskController::class, 'updateTaskStatus'])->name('update.status');
            Route::post('/{id}/complete', [TaskController::class, 'markAsComplete'])->name('complete');
        });

        // Layanan
        Route::prefix('layanan')->name('layanan.')->group(function () {
            Route::get('/', [LayananController::class, 'index'])->name('index');
            Route::post('/', [LayananController::class, 'store'])->name('store');
            Route::put('/{id}', [LayananController::class, 'update'])->name('update');
            Route::delete('/{id}', [LayananController::class, 'destroy'])->name('delete');
            Route::get('/dropdown', [LayananController::class, 'getForInvoiceDropdown'])->name('dropdown');
            Route::get('/{id}', [LayananController::class, 'show'])->name('show');
        });

        // Surat Kerjasama
        Route::prefix('surat-kerjasama')->name('surat_kerjasama.')->group(function () {
            Route::get('/', [SuratKerjasamaController::class, 'index'])->name('index');
            Route::get('/create', [SuratKerjasamaController::class, 'create'])->name('create');
            Route::post('/', [SuratKerjasamaController::class, 'store'])->name('store');
            Route::get('/{id}', [SuratKerjasamaController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [SuratKerjasamaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [SuratKerjasamaController::class, 'update'])->name('update');
            Route::delete('/{id}', [SuratKerjasamaController::class, 'destroy'])->name('destroy');

            Route::get('/data/layanan', [InvoiceController::class, 'getLayananData'])->name('data.layanan');
            Route::get('/list/layanan', [InvoiceController::class, 'getLayananList'])->name('list.layanan');
            Route::get('/list/status-pembayaran', [InvoiceController::class, 'getStatusPembayaranList'])->name('list.status');
        });

        // Data Project
        Route::get('/data_project', [DataProjectController::class, 'admin'])->name('data_project');
        Route::get('/project/{id}', [DataProjectController::class, 'show'])->name('project.show');
        Route::post('/project', [DataProjectController::class, 'store'])->name('project.store');
        Route::put('/project/{id}', [DataProjectController::class, 'update'])->name('project.update');
        Route::delete('/project/{id}', [DataProjectController::class, 'destroy'])->name('project.destroy');
        Route::post('/project/sync-from-invoice/{id}', [DataProjectController::class, 'syncFromInvoice'])
            ->name('admin.project.sync');
        Route::get('/project/invoice/{id}/details', [DataProjectController::class, 'getInvoiceDetails']);

        Route::get('/surat_kerjasama', function () {
            return redirect()->route('admin.surat_kerjasama.index');
        });

        Route::get('/template_surat', function () {
            return view('admin.template_surat');
        })->name('template_surat');

        // INVOICE MANAGEMENT
        Route::prefix('invoice')->name('invoice.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/print', [InvoiceController::class, 'print'])->name('print');
        });
        Route::get('/layanan-data', [InvoiceController::class, 'getLayananForDropdown'])
            ->name('layanan-data');
        Route::get('/invoice/perusahaan-data', [InvoiceController::class, 'getPerusahaanData'])
            ->name('invoice.perusahaan.data');

        // KWITANSI MANAGEMENT
        Route::prefix('kwitansi')->name('kwitansi.')->group(function () {
            Route::get('/', [KwitansiController::class, 'index'])->name('index');
            Route::post('/', [KwitansiController::class, 'store'])->name('store');
            Route::get('/data', [KwitansiController::class, 'getKwitansiData'])->name('data');
            Route::get('/{id}/print', [KwitansiController::class, 'cetak'])->name('print');
            Route::get('/{id}/cetak-data', [KwitansiController::class, 'getKwitansiForPrint'])->name('cetak.data');
            Route::get('/{id}', [KwitansiController::class, 'show'])->name('show');
            Route::put('/{id}', [KwitansiController::class, 'update'])->name('update');
            Route::delete('/{id}', [KwitansiController::class, 'destroy'])->name('destroy');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/profile', [SettingController::class, 'updateProfile'])->name('profile.update');
            Route::post('/account', [SettingController::class, 'updateAccount'])->name('account.update');
            Route::post('/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
            Route::post('/password', [SettingController::class, 'updatePassword'])->name('password.update');
            Route::post('/logout-all', [SettingController::class, 'logoutAll'])->name('logout.all');

            Route::get('/contact', [SettingController::class, 'contact'])->name('contact');
            Route::post('/contact', [SettingController::class, 'updateContact'])->name('contact.update');

            Route::get('/about', [SettingController::class, 'about'])->name('about');
            Route::post('/about', [SettingController::class, 'updateAbout'])->name('about.update');
            Route::get('/operational-hours', [SettingController::class, 'getOperationalHours'])->name('operational.hours');
            Route::post('/operational-hours', [SettingController::class, 'saveOperationalHours'])->name('operational.hours.update');

            Route::get('/articles', [SettingController::class, 'articles'])->name('articles');
            Route::get('/articles/{id}', [SettingController::class, 'getArticle'])->name('articles.get');
            Route::post('/articles', [SettingController::class, 'storeArticle'])->name('articles.store');
            Route::put('/articles/{id}', [SettingController::class, 'updateArticle'])->name('articles.update');
            Route::delete('/articles/{id}', [SettingController::class, 'deleteArticle'])->name('articles.delete');

            // Portfolios
            Route::get('/portfolios', [SettingController::class, 'portfolios'])->name('portfolios');
            Route::get('/portfolios/{id}', [SettingController::class, 'getPortfolio'])->name('portfolios.get');
            Route::post('/portfolios', [SettingController::class, 'storePortfolio'])->name('portfolios.store');
            Route::put('/portfolios/{id}', [SettingController::class, 'updatePortfolio'])->name('portfolios.update');
            Route::delete('/portfolios/{id}', [SettingController::class, 'deletePortfolio'])->name('portfolios.delete');
        });

        // API ENDPOINTS UNTUK ADMIN
        Route::prefix('api')->name('api.')->group(function () {
            // API untuk invoices admin (JSON response)
            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.api');
            
            // API untuk perusahaan dropdown (untuk finance)
            Route::get('/perusahaan', [PerusahaanController::class, 'getDataForDropdown'])->name('perusahaan.api');
            
            // API untuk layanan dropdown
            Route::get('/layanan', [LayananController::class, 'getLayananData'])->name('layanan.api');

            Route::get('/invoices-for-kwitansi', [InvoiceController::class, 'getInvoicesForKwitansiAdmin']);
            Route::get('/invoice-for-kwitansi/{id}', [InvoiceController::class, 'getInvoiceDetailForKwitansiAdmin']);
        });

        Route::get('/catatan_rapat', function () {
            return redirect()->route('catatan_rapat.index');
        });
        Route::get('/pengumuman', function () {
            return redirect()->route('pengumuman.index');
        });
    });

/*
|--------------------------------------------------------------------------
| Role: KARYAWAN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        // Dashboard
        Route::get('/home', [KaryawanController::class, 'home'])->name('home');

        // Profile
        Route::get('/profile', [KaryawanProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [KaryawanProfileController::class, 'update'])->name('profile.update');

        // CUTI
        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/data', [CutiController::class, 'getData'])->name('data');
            Route::get('/stats', [CutiController::class, 'stats'])->name('stats');
            Route::get('/quota-info', [CutiController::class, 'getQuotaInfo'])->name('quota.info');
            Route::get('/create', [CutiController::class, 'create'])->name('create');
            Route::post('/calculate-duration', [CutiController::class, 'calculateDuration'])->name('calculate-duration');
            Route::post('/', [CutiController::class, 'store'])->name('store');
            Route::get('/{cuti}', [CutiController::class, 'show'])->name('show');
            Route::get('/{cuti}/edit', [CutiController::class, 'edit'])->name('edit');
            Route::put('/{cuti}', [CutiController::class, 'update'])->name('update');
            Route::delete('/{cuti}', [CutiController::class, 'destroy'])->name('destroy');
            Route::post('/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cancel');
            Route::post('/{cuti}/cancel-refund', [CutiController::class, 'cancelWithRefund'])->name('cancel.refund');
            Route::get('/summary', [CutiController::class, 'getSummary'])->name('summary');
            Route::get('/export', [CutiController::class, 'export'])->name('export');
            Route::get('/{cuti}/history', [CutiController::class, 'getHistory'])->name('history');
        });

        // ABSENSI
        Route::get('/absensi', [KaryawanController::class, 'absensiPage'])->name('absensi.page');
        Route::get('/absensi/list', [KaryawanController::class, 'absensiListPage'])->name('absensi.list');
        Route::get('/detail/{id}', [KaryawanController::class, 'detailPage'])->name('detail');

        // TUGAS
        Route::prefix('tugas')->name('tugas.')->group(function () {
            Route::get('/', [TaskController::class, 'karyawanTasks'])->name('index');
            Route::get('/list', [TaskController::class, 'karyawanTasks'])->name('list');
            Route::get('/{id}', [TaskController::class, 'karyawanShow'])->name('show');
            Route::post('/{id}/upload-file', [TaskController::class, 'uploadTaskFile'])->name('upload.file');
            Route::post('/{id}/complete', [TaskController::class, 'markAsComplete'])->name('complete');
            Route::post('/{id}/status', [TaskController::class, 'updateTaskStatus'])->name('update.status');
            Route::put('/{id}/accept', [KaryawanController::class, 'acceptTask'])->name('accept');
            Route::get('/{id}/acceptance-status', [KaryawanController::class, 'getAcceptanceStatus'])->name('acceptance.status');
            Route::get('/{id}/comments', [TaskController::class, 'getComments'])->name('comments');
            Route::post('/{id}/comments', [TaskController::class, 'storeComment'])->name('comments.store');
            Route::get('/{id}/files', [TaskController::class, 'getTaskFiles'])->name('files');
            Route::get('/files/{file}/download', [TaskController::class, 'downloadFileById'])->name('files.download');
        });

        // LIST PAGE
        Route::get('/list', [KaryawanController::class, 'listPage'])->name('list');

        // =========== API KHUSUS KARYAWAN ===========
        Route::prefix('api')->name('api.')->group(function () {
            // DASHBOARD DATA
            Route::get('/dashboard-data', [KaryawanController::class, 'getDashboardDataApi'])->name('dashboard.data');
            Route::get('/penanggung-projects', [KaryawanController::class, 'getPenanggungProjects'])->name('penanggung.projects');

            // MEETING NOTES
            Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingDatesApi'])->name('meeting.notes.dates');
            Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesApi'])->name('meeting.notes.get');

            // ANNOUNCEMENTS
            Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesApi'])->name('announcements.dates');
            Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsApi'])->name('announcements.get');
            
            // =========== ABSENSI API ROUTES ===========
            Route::get('/today-status', [KaryawanController::class, 'getTodayStatusApi'])->name('today.status');
            Route::get('/history', [KaryawanController::class, 'getHistoryApi'])->name('history');
            Route::get('/dashboard', [KaryawanController::class, 'getDashboardData'])->name('dashboard');
            Route::post('/absen-masuk', [KaryawanController::class, 'absenMasukApi'])->name('absen.masuk');
            Route::post('/absen-pulang', [KaryawanController::class, 'absenPulangApi'])->name('absen.pulang');
            Route::post('/submit-izin', [KaryawanController::class, 'submitIzinApi'])->name('submit.izin');
            Route::post('/submit-dinas', [KaryawanController::class, 'submitDinasApi'])->name('submit.dinas');
            Route::get('/pengajuan-status', [KaryawanController::class, 'getPengajuanStatus'])->name('pengajuan-status');
            Route::get('/tasks', [KaryawanController::class, 'getTasksApi'])->name('tasks');
        });

        Route::get('/pengajuan_cuti', function () {
            return redirect()->route('karyawan.cuti.index');
        });
    });

/*
|--------------------------------------------------------------------------
| Role: GENERAL MANAGER Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:general_manager'])
    ->prefix('general_manajer')
    ->name('general_manajer.')
    ->group(function () {
        // Dashboard
        Route::get('/home', function () {
            return view('general_manajer.home');
        })->name('home');

        // Profile
        Route::get('/profile', function () {
            return view('general_manajer.profile');
        })->name('profile');

        Route::get('/data_karyawan', [AdminKaryawanController::class, 'karyawanGeneral'])->name('data_karyawan');
        Route::get('/layanan', [LayananController::class, 'Generalindex'])->name('layanan');

        // DATA PROJECT - ROUTES
        Route::prefix('data_project')->name('data_project.')->group(function () {
            Route::get('/', [DataProjectController::class, 'index'])->name('index');
            Route::post('/', [DataProjectController::class, 'store'])->name('store');
            Route::get('/{id}', [DataProjectController::class, 'show'])->name('show');
            Route::put('/{id}', [DataProjectController::class, 'update'])->name('update');
            Route::post('/{id}/assign-responsible', [DataProjectController::class, 'assignResponsible'])->name('assign.responsible');
            Route::delete('/{id}', [DataProjectController::class, 'destroy'])->name('destroy');
            Route::post('/sync/{layananId}', [DataProjectController::class, 'syncFromLayanan'])->name('sync');
            Route::get('/karyawan-by-manager/{id}', [DataProjectController::class, 'getKaryawanByManager'])->name('karyawan.by-manager');
        });

        // === DATA PERUSAHAAN (DITAMBAHKAN) ===
        Route::get('/perusahaan', [GMPerusahaanController::class, 'index'])->name('perusahaan.index');
        Route::post('/perusahaan', [GMPerusahaanController::class, 'store'])->name('perusahaan.store');
        Route::put('/perusahaan/{id}', [GMPerusahaanController::class, 'update'])->name('perusahaan.update');
        Route::delete('/perusahaan/{id}', [GMPerusahaanController::class, 'destroy'])->name('perusahaan.delete');

        // CUTI MANAGEMENT
        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/data', [CutiController::class, 'getData'])->name('data');
            Route::get('/stats', [CutiController::class, 'stats'])->name('stats');
            Route::post('/{cuti}/approve', [CutiController::class, 'approve'])->name('approve');
            Route::post('/{cuti}/reject', [CutiController::class, 'reject'])->name('reject');
            Route::post('/{cuti}/cancel-refund', [CutiController::class, 'cancelWithRefund'])->name('cancel.refund');
            Route::get('/quota-info', [CutiController::class, 'getQuotaInfo'])->name('quota.info');
            Route::post('/reset-quota', [CutiController::class, 'resetQuota'])->name('reset.quota');
            Route::get('/report', [CutiController::class, 'report'])->name('report');
            Route::get('/{cuti}', [CutiController::class, 'show'])->name('show');
            Route::get('/{cuti}/edit', [CutiController::class, 'edit'])->name('edit');
            Route::put('/{cuti}', [CutiController::class, 'update'])->name('update');
            Route::get('/summary', [CutiController::class, 'getSummary'])->name('summary');
            Route::get('/export', [CutiController::class, 'export'])->name('export');
            Route::get('/karyawan-by-divisi', [CutiController::class, 'getKaryawanByDivisi'])->name('karyawan.by-divisi');
        });

        // TUGAS MANAGEMENT
        Route::get('/kelola-tugas', [GeneralManagerTaskController::class, 'index'])->name('kelola_tugas');

        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::post('/', [GeneralManagerTaskController::class, 'store'])->name('store');
            Route::get('/{id}', [GeneralManagerTaskController::class, 'show'])->name('show');
            Route::put('/{id}', [GeneralManagerTaskController::class, 'update'])->name('update');
            Route::put('/{id}/status', [GeneralManagerTaskController::class, 'updateStatus'])->name('update.status');
            Route::post('/{id}/assign', [GeneralManagerTaskController::class, 'assignToKaryawan'])->name('assign');
            Route::delete('/{id}', [GeneralManagerTaskController::class, 'destroy'])->name('destroy');
        });

        // Absensi Management
        Route::get('/kelola-absen', [AbsensiController::class, 'kelolaAbsenGeneral'])->name('kelola_absen');

        // Action untuk approve/reject
        Route::post('/absensi/{id}/approve', [AbsensiController::class, 'approveAbsensi'])
            ->name('absensi.approve');

        Route::post('/absensi/{id}/reject', [AbsensiController::class, 'rejectAbsensi'])
            ->name('absensi.reject');

        Route::get('/tim_dan_divisi', function () {
            return view('general_manajer.tim_dan_divisi');
        })->name('tim_dan_divisi');

        // Halaman utama tim divisi
        Route::get('/tim_divisi', [TimDivisiController::class, 'index'])->name('tim_divisi');

        // Tim routes
        Route::prefix('tim')->group(function () {
            Route::post('/', [TimDivisiController::class, 'storeTim'])->name('tim.store');
            Route::put('/{id}', [TimDivisiController::class, 'updateTim'])->name('tim.update');
            Route::delete('/{id}', [TimDivisiController::class, 'destroyTim'])->name('tim.destroy');
            Route::get('/search', [TimDivisiController::class, 'searchTim'])->name('tim.search');
        });

        // Divisi routes
        Route::prefix('divisi')->group(function () {
            Route::post('/', [TimDivisiController::class, 'storeDivisi'])->name('divisi.store');
            Route::put('/{id}', [TimDivisiController::class, 'updateDivisi'])->name('divisi.update');
            Route::delete('/{id}', [TimDivisiController::class, 'destroyDivisi'])->name('divisi.destroy');
            Route::get('/search', [TimDivisiController::class, 'searchDivisi'])->name('divisi.search');
        });

        // Utility route
        Route::get('/divisis/list', [TimDivisiController::class, 'getDivisis'])->name('divisis.list');
    });

// Convenience route: Rekap Absensi (auth-only)
Route::middleware(['auth'])->get('/rekap_absensi', [AbsensiController::class, 'rekapAbsensi'])->name('rekap_absensi');

/*
|--------------------------------------------------------------------------
| Role: OWNER Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {

        Route::get('home', [OwnerBerandaController::class, 'index'])->name('home');

        Route::get('/rekap_absen', [AbsensiController::class, 'rekapAbsensi'])->name('rekap_absen');
        Route::get('/laporan_pemasukan', [OwnerController::class, 'laporanPemasukan'])->name('laporan_pemasukan');
        Route::get('/laporan', [OwnerController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/pdf', [OwnerController::class, 'laporanPdf'])->name('laporan.pdf');

        // CUTI MANAGEMENT
        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/data', [CutiController::class, 'getData'])->name('data');
            Route::get('/stats', [CutiController::class, 'stats'])->name('stats');
            Route::get('/quota-info', [CutiController::class, 'getQuotaInfo'])->name('quota.info');
            Route::post('/reset-quota', [CutiController::class, 'resetQuota'])->name('reset.quota');
            Route::get('/report', [CutiController::class, 'report'])->name('report');
            Route::get('/{cuti}', [CutiController::class, 'show'])->name('show');
            Route::get('/{cuti}/edit', [CutiController::class, 'edit'])->name('edit');
            Route::put('/{cuti}', [CutiController::class, 'update'])->name('update');
            Route::get('/summary', [CutiController::class, 'getSummary'])->name('summary');
            Route::get('/export', [CutiController::class, 'export'])->name('export');
        });
    });

/*
|--------------------------------------------------------------------------
| Role: FINANCE Routes - DIPERBAIKI DENGAN API ENDPOINTS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:finance'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/beranda', [BerandaFinanceController::class, 'index'])->name('beranda');
        Route::get('/test', [BerandaFinanceController::class, 'index'])->withoutMiddleware(['auth', 'role:finance'])->name('test');

        Route::get('/data-layanan', function () {
            return view('finance.data_layanan');
        })->name('data_layanan');
        Route::get('/pembayaran', [OrderController::class, 'index'])->name('pembayaran');
        Route::get('/laporan-keuangan', function () {
            return view('finance.laporan_keuangan');
        })->name('laporan_keuangan');
        Route::get('/daftar_karyawan', [AdminKaryawanController::class, 'karyawanFinance'])->name('daftar_karyawan');

        // EDIT & DELETE KARYAWAN
        Route::put('/karyawan/{karyawan}', [AdminKaryawanController::class, 'update'])->name('karyawan.update');
        Route::delete('/karyawan/{karyawan}', [AdminKaryawanController::class, 'destroy'])->name('karyawan.destroy');
        Route::get('/layanan', [LayananController::class, 'financeIndex'])->name('layanan.index');

        // Update harga saja
        Route::put('/layanan/{id}/update-harga', [LayananController::class, 'updateHarga'])->name('layanan.update-harga');

        // CUTI VIEW ONLY untuk finance
        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/data', [CutiController::class, 'getData'])->name('data');
            Route::get('/stats', [CutiController::class, 'stats'])->name('stats');
            Route::get('/quota-info', [CutiController::class, 'getQuotaInfo'])->name('quota.info');
            Route::get('/report', [CutiController::class, 'report'])->name('report');
            Route::get('/{cuti}', [CutiController::class, 'show'])->name('show');
        });

        // INVOICE MANAGEMENT - FINANCE
        Route::prefix('invoice')->name('invoice.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/print', [InvoiceController::class, 'print'])->name('print');
        });

        // CASHFLOW MANAGEMENT
        Route::prefix('cashflow')->name('cashflow.')->group(function () {
            Route::get('/', [CashflowController::class, 'index'])->name('index');
            Route::post('/', [CashflowController::class, 'store'])->name('store');
            Route::put('/{id}', [CashflowController::class, 'update'])->name('update');
            Route::delete('/{id}', [CashflowController::class, 'destroy'])->name('destroy');
        });

        // KWITANSI MANAGEMENT - FINANCE
        Route::prefix('kwitansi')->name('kwitansi.')->group(function () {
            Route::get('/', [KwitansiController::class, 'financeIndex'])->name('finance.kwitansi.index');
            Route::post('/', [KwitansiController::class, 'store'])->name('store');
            Route::put('/{id}', [KwitansiController::class, 'update'])->name('update');
            Route::delete('/{id}', [KwitansiController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/cetak', [KwitansiController::class, 'cetak'])->name('cetak');
        });

        Route::prefix('api')->name('api.')->group(function () {
            // Route untuk invoice options kwitansi
            Route::get('/invoices-for-kwitansi', [InvoiceController::class, 'getInvoicesForKwitansi']);
            Route::get('/invoice-for-kwitansi/{id}', [InvoiceController::class, 'getInvoiceDetailForKwitansi']);
            // Test endpoint
            Route::get('/test-json', function() {
                return response()->json([
                    'success' => true,
                    'message' => 'Finance API is working',
                    'timestamp' => now()
                ], 200, [], JSON_UNESCAPED_UNICODE);
            })->name('test');

            // API untuk perusahaan dropdown
            Route::get('/perusahaan', [PerusahaanController::class, 'getDataForDropdown'])->name('perusahaan.api');
            
            // API untuk layanan finance (JSON response)
            Route::get('/layanan', [LayananController::class, 'financeApi'])->name('layanan.api');
            
            // API untuk layanan dropdown
            Route::get('/layanan-dropdown', [InvoiceController::class, 'getLayananForDropdown'])->name('layanan.dropdown');

            // API untuk invoices finance (JSON response)
            Route::get('/invoices', [InvoiceController::class, 'getFinanceInvoices'])->name('invoices.api');

            // API untuk kategori cashflow
            Route::get('/kategori/{tipe}', [CashflowController::class, 'getKategoriByType'])->name('kategori.by.type');

            // API untuk beranda finance: catatan rapat & pengumuman
            Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingDatesApi'])->name('meeting.notes.dates');
            Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesApi'])->name('meeting.notes.get');
            Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesApi'])->name('announcements.dates');
            Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsApi'])->name('announcements.get');
        });

        // ========== FINANCE - PENGGAJIAN ==========
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::get('/create', [PayrollController::class, 'create'])->name('create');
            Route::post('/', [PayrollController::class, 'store'])->name('store');
            Route::get('/dari-hr', [PayrollController::class, 'dariHr'])->name('dari-hr');
            Route::post('/ambil-dari-hr', [PayrollController::class, 'ambilDariHR'])->name('ambil-dari-hr');
            Route::get('/{id}', [PayrollController::class, 'show'])->name('show');
            Route::post('/{id}/hitung-potongan', [PayrollController::class, 'hitungPotongan'])->name('hitung-potongan');
            Route::post('/{id}/approve', [PayrollController::class, 'approve'])->name('approve');
            Route::post('/{id}/paid', [PayrollController::class, 'markAsPaid'])->name('paid');
            Route::get('/{periodId}/slip/{detailId}', [PayrollController::class, 'slip'])->name('slip');
            Route::get('/{id}/export', [PayrollController::class, 'export'])->name('export');
            Route::get('/settings', [PayrollController::class, 'settings'])->name('settings');
            Route::post('/settings', [PayrollController::class, 'updateSettings'])->name('update-settings');
            
            // TAMBAHKAN DI SINI
            Route::post('/{periodId}/send-slip/{detailId}', [PayrollController::class, 'sendSlipToEmail'])->name('send-slip');
        });
    });

/*
|--------------------------------------------------------------------------
| Role: MANAGER DIVISI Routes - DIPERBAIKI
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manager_divisi'])
    ->prefix('manager-divisi')
    ->name('manager_divisi.')
    ->group(function () {
        // Dashboard
        Route::get('/home', function () {
            return view('manager_divisi.home');
        })->name('home');

        Route::get('/kelola_absensi', [AbsensiController::class, 'kelolaAbsenManajer'])->name('kelola_absensi');

        // Profile
        Route::get('/profile', function () {
            return view('manager_divisi.profile');
        })->name('profile');

        // Halaman Kelola Tugas
        Route::get('/kelola-tugas', [ManagerDivisiTaskController::class, 'index'])
            ->name('kelola-tugas');
        
        // Halaman view untuk pengelolaan tugas (kompatibilitas)
        Route::get('/pengelola_tugas', function () {
            return view('manager_divisi.pengelola_tugas');
        })->name('pengelola_tugas');
        
        // =========== TUGAS MANAGEMENT ROUTES (CRUD) - DIPERBAIKI ===========
        Route::prefix('tasks')->name('tasks.')->group(function () {
            // ROUTE UTAMA untuk store task - MENGGUNAKAN createTask()
            Route::post('/createTask', [ManagerDivisiTaskController::class, 'createTask'])->name('createTask');
            
            // Route untuk halaman create (view)
            Route::get('/create', [TaskController::class, 'create'])->name('create');
            
            // Route untuk halaman index/view
            Route::get('/', [TaskController::class, 'managerTasks'])->name('index');
            
            // CRUD lainnya
            Route::get('/{id}', [TaskController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [TaskController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TaskController::class, 'update'])->name('update');
            Route::delete('/{id}', [TaskController::class, 'destroy'])->name('destroy');
            
            // Comments dan Files
            Route::get('/{id}/comments', [TaskController::class, 'getComments'])->name('comments');
            Route::post('/{id}/comments', [TaskController::class, 'storeComment'])->name('comments.store');
            Route::get('/{id}/files', [TaskController::class, 'getTaskFiles'])->name('files');
            Route::get('/files/{file}/download', [TaskController::class, 'downloadFileById'])->name('files.download');
            Route::delete('/files/{file}', [TaskController::class, 'deleteFile'])->name('files.delete');
            Route::post('/{id}/upload-file', [TaskController::class, 'uploadFileAdmin'])->name('upload.file');
            
            // Status update
            Route::put('/{id}/status', [TaskController::class, 'updateTaskStatus'])->name('update.status');
            Route::post('/{id}/complete', [TaskController::class, 'markAsComplete'])->name('complete');
        });

        // Data Karyawan
        Route::get('/data-karyawan', function() {
            $user = Auth::user();
            $karyawan = \App\Models\User::where('divisi_id', $user->divisi_id)
                ->where('role', 'karyawan')
                ->get();
            return view('manager_divisi.data_karyawan', compact('karyawan'));
        })->name('data-karyawan');

        /* ============================================
           API ENDPOINTS KHUSUS MANAGER DIVISI - DIPERBAIKI
           ============================================ */
        Route::prefix('api')->name('api.')->group(function () {
            // Data Project untuk Dropdown - ROUTE UTAMA yang dicari frontend
            Route::get('/projects-dropdown', [ManagerDivisiTaskController::class, 'getProjectsDropdown'])
                ->name('projects-dropdown');
            
            // Data Karyawan untuk Dropdown
            Route::get('/karyawan-dropdown', [ManagerDivisiTaskController::class, 'getKaryawanDropdown'])
                ->name('karyawan-dropdown');
            
            // Test karyawan endpoint
            Route::get('/karyawan-test', function() {
                return response()->json([
                    'success' => true,
                    'message' => 'Test endpoint working',
                    'user' => auth()->id()
                ]);
            })->name('karyawan-test');
            
            // Data Tasks utama - MENGGUNAKAN TaskController::apiGetManagerTasks
            Route::get('/tasks-api', [TaskController::class, 'apiGetManagerTasks'])
                ->name('tasks-api');
            
            // Statistik tugas - MENGGUNAKAN TaskController::apiGetStatistics
            Route::get('/tasks/statistics', [TaskController::class, 'apiGetStatistics'])
                ->name('tasks.statistics');
            
            // API untuk create task - GUNAKAN createTask() dari ManagerDivisiTaskController
            Route::post('/tasks/create-task', [ManagerDivisiTaskController::class, 'createTask'])
                ->name('tasks.create-task');
            
            // Alternate route untuk compatibility
            Route::post('/tasks', [ManagerDivisiTaskController::class, 'store'])
                ->name('tasks.store.api');
            
            // Route khusus untuk mendapatkan detail task
            Route::get('/tasks/{id}', [ManagerDivisiTaskController::class, 'show'])
                ->name('tasks.show.api');
                
            // Update task
            Route::put('/tasks/{id}', [ManagerDivisiTaskController::class, 'update'])
                ->name('tasks.update.api');
            
            // Delete task
            Route::delete('/tasks/{id}', [ManagerDivisiTaskController::class, 'destroy'])
                ->name('tasks.destroy.api');

            // Approve tugas dari karyawan (oleh Manager Divisi)
            Route::post('/tugas-karyawan/{id}/approve', [ManagerDivisiTaskController::class, 'approveTaskFromKaryawan'])
                ->name('tugas-karyawan.approve');
            
            // Test endpoint untuk create task
            Route::post('/tasks/test-create', [TaskController::class, 'testCreateTask'])
                ->name('tasks.test-create');
        });

        // CUTI MANAGEMENT
        Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/data', [CutiController::class, 'getData'])->name('data');
            Route::get('/stats', [CutiController::class, 'stats'])->name('stats');
            Route::post('/{cuti}/approve', [CutiController::class, 'approve'])->name('approve');
            Route::post('/{cuti}/reject', [CutiController::class, 'reject'])->name('reject');
            Route::post('/{cuti}/cancel-refund', [CutiController::class, 'cancelWithRefund'])->name('cancel.refund');
            Route::get('/quota-info', [CutiController::class, 'getQuotaInfo'])->name('quota.info');
            Route::get('/{cuti}', [CutiController::class, 'show'])->name('show');
            Route::get('/{cuti}/edit', [CutiController::class, 'edit'])->name('edit');
            Route::put('/{cuti}', [CutiController::class, 'update'])->name('update');
            Route::get('/karyawan-by-divisi', [CutiController::class, 'getKaryawanByDivisi'])->name('karyawan.by-divisi');
            Route::get('/summary', [CutiController::class, 'getSummary'])->name('summary');
        });

        // DATA PROJECT
        Route::get('/data_project', [DataProjectController::class, 'managerDivisi'])->name('data_project');
        Route::post('/data_project/{id}/update', [DataProjectController::class, 'updateManager'])->name('data_project.update');
        Route::get('/data_project/filter', [DataProjectController::class, 'filterByUser'])->name('data_project.filter');
        
        // API untuk manager divisi
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/tasks', [ManagerDivisiTaskController::class, 'getTasksApi'])->name('tasks');
            Route::get('/tasks/statistics', [ManagerDivisiTaskController::class, 'getStatistics'])->name('tasks.statistics');
            Route::get('/karyawan-by-divisi/{divisi}', [ManagerDivisiTaskController::class, 'getKaryawanByDivisi'])->name('karyawan.by_divisi');
            Route::get('/daftar_karyawan/{divisi}', [AdminKaryawanController::class, 'karyawanDivisi'])->name('karyawan.divisi');
        });

        // Task management
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::post('/', [ManagerDivisiTaskController::class, 'store'])->name('store');
            Route::get('/{id}', [ManagerDivisiTaskController::class, 'show'])->name('show');
            Route::put('/{id}', [ManagerDivisiTaskController::class, 'update'])->name('update');
            Route::put('/{id}/status', [ManagerDivisiTaskController::class, 'updateStatus'])->name('update.status');
            Route::post('/{id}/assign', [ManagerDivisiTaskController::class, 'assignToKaryawan'])->name('assign');
            Route::delete('/{id}', [ManagerDivisiTaskController::class, 'destroy'])->name('destroy');
        });

        Route::get('/daftar_karyawan', [AdminKaryawanController::class, 'daftarKaryawanView'])->name('daftar_karyawan');

        // Tim Saya
        Route::get('/tim-saya', function () {
            $user = Auth::user();
            $tim = \App\Models\User::where('divisi_id', $user->divisi_id)
                ->where('role', 'karyawan')
                ->get();
            return view('manager_divisi.tim_saya', compact('tim'));
        })->name('tim_saya');
    });

/*
|--------------------------------------------------------------------------
| GLOBAL API ROUTES (Additional) - DIPERBAIKI
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Notification unread count
    Route::get('/notifications/unread-count', function() {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => true, 'count' => 0]);
        }
        $count = $user->notifications()
            ->where('is_read', false)
            ->count();
        return response()->json(['success' => true, 'count' => $count]);
    });

    // INVOICE API ROUTES
    Route::prefix('invoices')->name('api.invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/mark-printed', [InvoiceController::class, 'markPrinted'])->name('mark.printed');
    });

    // Karyawan API
    Route::get('/api/karyawan/history', [KaryawanController::class, 'getHistory'])->name('api.karyawan.history');
    Route::get('/api/karyawan/dashboard-data', [KaryawanController::class, 'getDashboardData'])->name('api.karyawan.dashboard-data');
    Route::get('/api/karyawan/today-status', [KaryawanController::class, 'getTodayStatus'])->name('api.karyawan.today-status');
    Route::get('/karyawan/{id}/detail', [KaryawanController::class, 'getDetailApi'])->name('api.karyawan.detail');

    // Absensi actions
    Route::post('/karyawan/absen-masuk', [KaryawanController::class, 'absenMasukApi'])->name('api.karyawan.absen-masuk');
    Route::post('/karyawan/absen-pulang', [KaryawanController::class, 'absenPulangApi'])->name('api.karyawan.absen-pulang');
    Route::post('/karyawan/submit-izin', [KaryawanController::class, 'submitIzinApi'])->name('api.karyawan.submit-izin');
    Route::post('/karyawan/submit-dinas', [KaryawanController::class, 'submitDinasApi'])->name('api.karyawan.submit-dinas');

    // Pengajuan status
    Route::get('/karyawan/pengajuan-status', [KaryawanController::class, 'getPengajuanStatus'])->name('api.karyawan.pengajuan-status');

    /* ABSENSI API */
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/today-status', [AbsensiController::class, 'apiTodayStatus'])->name('today-status');
        Route::get('/history', [AbsensiController::class, 'apiHistory'])->name('history');
        Route::post('/absen-masuk', [AbsensiController::class, 'apiAbsenMasuk'])->name('absen-masuk');
        Route::post('/absen-pulang', [AbsensiController::class, 'apiAbsenPulang'])->name('absen-pulang');
        Route::post('/submit-izin', [AbsensiController::class, 'apiSubmitIzin'])->name('submit-izin');
    });

    /* =====================================================
     |  API MEETING NOTES & ANNOUNCEMENTS
     ===================================================== */
    Route::get('api/karyawan/meeting-notes', [KaryawanController::class, 'getMeetingNotes']);
    Route::get('api/karyawan/meeting-notes-dates', [KaryawanController::class, 'getMeetingNotesDates']);
    Route::get('api/karyawan/announcements', [KaryawanController::class, 'getAnnouncements']);
    Route::get('karyawan/announcements-by-date', [KaryawanController::class, 'getAnnouncementsByDate']);
    Route::get('karyawan/announcements-dates', [KaryawanController::class, 'getAnnouncementsDates']);
    Route::get('/karyawan/calendar-dates', [KaryawanController::class, 'getCalendarDates']);

    /* TASKS API */
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/{id}', [TaskController::class, 'show'])->name('show');
        Route::get('/{id}/detail', [TaskController::class, 'getTaskDetailApi'])->name('detail');
        Route::post('/{id}/upload', [TaskController::class, 'uploadTaskFile'])->name('upload');
        Route::post('/{id}/upload-file', [TaskController::class, 'uploadTaskFile'])->name('upload.file');
        Route::post('/{id}/complete', [TaskController::class, 'markAsComplete'])->name('complete');
        Route::post('/{id}/status', [TaskController::class, 'updateTaskStatus'])->name('status');
        Route::get('/{id}/comments', [TaskController::class, 'getComments'])->name('comments');
        Route::post('/{id}/comments', [TaskController::class, 'storeComment'])->name('comments.store');
        Route::get('/{id}/files', [TaskController::class, 'getTaskFiles'])->name('files');
        Route::get('/files/{file}/download', [TaskController::class, 'downloadFile'])->name('files.download');
        Route::delete('/files/{file}', [TaskController::class, 'deleteFile'])->name('files.delete');
        Route::get('/{id}/download', [TaskController::class, 'downloadSubmission'])->name('download.submission');
        Route::get('/statistics', [TaskController::class, 'getStatistics'])->name('statistics');
        Route::get('/karyawan/statistics', [TaskController::class, 'getKaryawanStatistics'])->name('karyawan.statistics');
        
        // Route khusus untuk manager divisi store
        Route::post('/store-for-manager', [TaskController::class, 'storeForManager'])
            ->middleware(['role:manager_divisi'])
            ->name('store.for-manager');
            
        // Test endpoint
        Route::post('/test-create', [TaskController::class, 'testCreateTask'])->name('test-create');
    });

    /* TASKS UNTUK KARYAWAN */
    Route::prefix('karyawan-tasks')->name('karyawan.tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'getKaryawanTasks'])->name('index');
        Route::get('/{id}/detail', [TaskController::class, 'getTaskDetailForKaryawan'])->name('detail');
    });

    /* API DASHBOARD DATA */
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/karyawan', [KaryawanController::class, 'getDashboardData'])->name('karyawan');
        Route::get('/pengajuan-status', [KaryawanController::class, 'getPengajuanStatus'])->name('pengajuan.status');
        Route::post('/submit-dinas', [KaryawanController::class, 'submitDinasApi'])->name('submit.dinas');
    });

    // ============== PERBAIKAN: Tambahkan Kwitansi API Routes di sini ==============
    Route::prefix('kwitansi')->name('api.kwitansi.')->group(function () {
        Route::get('/', [KwitansiController::class, 'getKwitansiData'])->name('index');
        Route::get('/{id}', [KwitansiController::class, 'show'])->name('show');
        Route::post('/', [KwitansiController::class, 'store'])->name('store');
        Route::put('/{id}', [KwitansiController::class, 'update'])->name('update');
        Route::delete('/{id}', [KwitansiController::class, 'destroy'])->name('destroy');
    });

    // Invoice API untuk dropdown
    Route::prefix('invoices')->name('api.invoices.')->group(function () {
        Route::get('/dropdown', [InvoiceController::class, 'getInvoicesForDropdown'])->name('dropdown');
        Route::get('/{id}/detail', [InvoiceController::class, 'getInvoiceDetail'])->name('detail');
    });
});

/*
|--------------------------------------------------------------------------
| Shortcuts & Redirects
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Catatan Rapat
    Route::get('/catatan-rapat', [CatatanRapatController::class, 'index'])->name('catatan_rapat.index');
    Route::post('/catatan-rapat', [CatatanRapatController::class, 'store'])->name('catatan_rapat.store');
    Route::put('/catatan-rapat/{id}', [CatatanRapatController::class, 'update'])->name('catatan_rapat.update')->whereNumber('id');
    Route::delete('/catatan-rapat/{id}', [CatatanRapatController::class, 'destroy'])->name('catatan_rapat.destroy')->whereNumber('id');
    Route::get('/catatan-rapat/{id}', [CatatanRapatController::class, 'show'])->name('catatan_rapat.show')->whereNumber('id');

    // API endpoints
    Route::get('/users/data', [UserController::class, 'getData']);
    Route::get('/divisis/list', [UserController::class, 'getDivisis'])->name('divisis.list');
    Route::get('/roles/list', [UserController::class, 'getRoles'])->name('roles.list');
    Route::get('/tims/by-divisi/{id}', [TimDivisiController::class, 'getTimsByDivisi'])->name('tims.by_divisi');
});

Route::get('/redirect-login', function () {
    if (Auth::check()) {
        return redirectToRolePage(Auth::user());
    }
    return redirect('/login');
})->name('redirect.login');

Route::get('/tugas', function () {
    if (!Auth::check())
        return redirect('/login');
    return match (Auth::user()->role) {
        'karyawan' => redirect()->route('karyawan.tugas.index'),
        'admin' => redirect()->route('admin.tasks.index'),
        'general_manager' => redirect()->route('general_manajer.kelola_tugas'),
        'manager_divisi' => redirect()->route('manager_divisi.kelola-tugas'),
        default => redirect('/login')
    };
})->name('tugas.redirect');

Route::get('/absensi', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();

    try {
        return match ($user->role) {
            'admin' => redirect()->route('admin.absensi.index'),
            'general_manager' => redirect()->route('general_manajer.kelola_absen'),
            'owner' => redirect()->route('owner.rekap_absen'),
            'karyawan' => redirect()->route('karyawan.absensi.page'),
            'manager_divisi' => redirect()->route('manager_divisi.kelola_absensi'),
            'finance' => redirect()->route('finance.beranda'),
            default => redirect('/login')
        };
    } catch (\Exception $e) {
        return redirect('/redirect-login');
    }
})->name('absensi.redirect');

Route::get('/cuti', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return match ($user->role) {
            'karyawan' => redirect()->route('karyawan.cuti.index'),
            'admin' => redirect()->route('admin.cuti.index'),
            'general_manager' => redirect()->route('general_manajer.cuti.index'),
            'manager_divisi' => redirect()->route('manager_divisi.cuti.index'),
            'owner' => redirect()->route('owner.cuti.index'),
            'finance' => redirect()->route('finance.cuti.index'),
            default => redirect('/login')
        };
    }
    return redirect('/login');
})->name('cuti.redirect');

Route::get('/quota', function () {
    if (!Auth::check())
        return redirect('/login');
    return match (Auth::user()->role) {
        'karyawan' => redirect()->route('karyawan.cuti.quota.info'),
        'admin', 'general_manager', 'owner' => redirect()->route('admin.cuti.quota.info'),
        'manager_divisi' => redirect()->route('manager_divisi.cuti.quota.info'),
        'finance' => redirect()->route('finance.cuti.quota.info'),
        default => redirect('/login')
    };
})->name('quota.redirect');

/*
|--------------------------------------------------------------------------
| Legacy & Fallback Routes
|--------------------------------------------------------------------------
*/

// Legacy redirect untuk acc_cuti
Route::middleware(['auth', 'role:general_manager'])
    ->get('/general_manajer/acc_cuti', function () {
        return redirect()->route('general_manajer.cuti.index');
    })->name('general_manajer.acc_cuti');

// Finance legacy routes
Route::get('/data', [LayananController::class, 'financeIndex']);
Route::middleware(['auth', 'role:finance'])->get('/data_orderan', [OrderController::class, 'index']);
Route::get('/finance', function () {
    return view('finance.beranda');
});
Route::get('/pemasukan', [FinanceController::class, 'index']);
Route::post('/pemasukan', [FinanceController::class, 'store']);
Route::get('/pengeluaran', function () {
    return view('finance.pengeluaran');
});
Route::get('/finance/invoice', function () {
    return view('finance.invoice');
});

// General Manager legacy shortcuts
Route::get('/general_manajer', function () {
    return view('general_manajer.home');
});
Route::get('/kelola_tugas', function () {
    return redirect()->route('general_manajer.kelola_tugas');
});
Route::get('/kelola_absen', function () {
    return view('general_manajer.kelola_absen');
});

// Admin legacy
Route::get('/admin/templat', function () {
    return view('admin.templet_surat');
});

// Invoice print
Route::get('/invoices/{invoice}/print', function (\App\Models\Invoice $invoice) {
    return view('invoices.print', compact('invoice'));
})->name('invoices.print');

// Additional routes (re-ensuring coverage)
Route::get('/general_manajer', function () {
    return view('general_manajer/home');
});

Route::get('/tugas', [TaskController::class, 'index'])->name('tugas.page');

// API untuk jumlah layanan
Route::middleware(['auth'])->prefix('api/services')->name('api.services.')->group(function () {
    Route::get('/count', [LayananController::class, 'getCount'])->name('count');
});

// Admin Template
Route::get('/admin/templat', function () {
    return view('admin.templet_surat');
});

// GM API Routes
Route::prefix('general_manager/api')->middleware(['auth'])->group(function () {
    // Route untuk Catatan Rapat
    Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingNotesDatesForGM']);
    Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesByDateForGM']);

    // Route untuk Pengumuman
    Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesForGM']);
    Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsForGM']);
});

// Owner API Routes
Route::prefix('owner/api')->middleware(['auth', 'role:owner'])->group(function () {
    // Route untuk Catatan Rapat
    Route::get('/meeting-notes-dates', [CatatanRapatController::class, 'getMeetingNotesDatesForOwner']);
    Route::get('/meeting-notes', [CatatanRapatController::class, 'getMeetingNotesByDateForOwner']);

    // Route untuk Pengumuman
    Route::get('/announcements-dates', [PengumumanController::class, 'getAnnouncementDatesForOwner']);
    Route::get('/announcements', [PengumumanController::class, 'getAnnouncementsForOwner']);
});

/*
|--------------------------------------------------------------------------
| DEBUG ROUTES (Development Only)
|--------------------------------------------------------------------------
*/
if (env('APP_DEBUG', false)) {
    Route::middleware(['auth'])->group(function () {
        // Test route untuk TaskController
        Route::get('/test/task-controller', function () {
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'divisi_id' => $user->divisi_id
                ],
                'endpoints' => [
                    'createTask' => route('manager_divisi.tasks.createTask'),
                    'api_create_task' => route('manager_divisi.api.tasks.create-task'),
                    'api_tasks_store' => route('manager_divisi.api.tasks.store.api'),
                    'test_create' => route('manager_divisi.api.tasks.test-create'),
                ],
                'controller_methods' => [
                    'ManagerDivisiTaskController::createTask' => method_exists(ManagerDivisiTaskController::class, 'createTask'),
                    'TaskController::storeForManager' => method_exists(TaskController::class, 'storeForManager'),
                    'TaskController::testCreateTask' => method_exists(TaskController::class, 'testCreateTask'),
                    'TaskController::apiGetManagerTasks' => method_exists(TaskController::class, 'apiGetManagerTasks'),
                ]
            ]);
        });

        // Test untuk check divisi
        Route::get('/test/divisi-check', function() {
            $divisi = \App\Models\Divisi::find(1);
            
            return response()->json([
                'divisi_exists' => !is_null($divisi),
                'divisi_table' => (new \App\Models\Divisi())->getTable(),
                'divisi_model' => get_class($divisi),
                'all_divisis' => \App\Models\Divisi::all()->toArray()
            ]);
        });

        // Test untuk create task secara langsung
        Route::post('/test/create-task-direct', [TaskController::class, 'testCreateTask'])
            ->withoutMiddleware(['role:manager_divisi']);
            
        Route::get('/debug/cuti-fix', function () {
            try {
                $controller = new \App\Http\Controllers\CutiController();
                $request = new \Illuminate\Http\Request();

                // Test getData method
                $response = $controller->getData($request);
                $data = json_decode($response->getContent(), true);

                echo "<h1>Debug Cuti Controller</h1>";
                echo "Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "<br>";
                echo "Message: " . ($data['message'] ?? 'No message') . "<br>";

                if ($data['success'] && isset($data['data'])) {
                    echo "Jumlah Data: " . count($data['data']) . "<br>";
                    if (count($data['data']) > 0) {
                        $first = $data['data'][0];
                        echo "<h3>Data Pertama:</h3>";
                        echo "ID: " . $first['id'] . "<br>";
                        echo "Nama: " . ($first['nama'] ?? 'NULL') . "<br>";
                        echo "Divisi: " . ($first['divisi'] ?? 'NULL') . "<br>";
                        echo "Status: " . ($first['status'] ?? 'NULL') . "<br>";
                    }
                }

                echo "<hr>";

                // Test stats method
                $statsResponse = $controller->stats();
                $statsData = json_decode($statsResponse->getContent(), true);

                echo "<h2>Stats Test:</h2>";
                echo "Status: " . ($statsData['success'] ? 'SUCCESS' : 'FAILED') . "<br>";
                if ($statsData['success']) {
                    echo "<pre>" . print_r($statsData['data'], true) . "</pre>";
                }

                // Test quota info
                $quotaResponse = $controller->getQuotaInfo($request);
                $quotaData = json_decode($quotaResponse->getContent(), true);

                echo "<h2>Quota Info Test:</h2>";
                echo "Status: " . ($quotaData['success'] ? 'SUCCESS' : 'FAILED') . "<br>";
                if ($quotaData['success']) {
                    echo "<pre>" . print_r($quotaData['data'], true) . "</pre>";
                }
            } catch (\Exception $e) {
                echo "<h1>ERROR: " . $e->getMessage() . "</h1>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        });

        Route::get('/test/cuti-routes', function () {
            $user = auth::user();

            return response()->json([
                'user_role' => $user->role,
                'routes' => [
                    'karyawan_cuti_index' => route('karyawan.cuti.index'),
                    'karyawan_cuti_data' => route('karyawan.cuti.data'),
                    'karyawan_cuti_edit' => route('karyawan.cuti.edit', ['cuti' => 1]),
                    'karyawan_cuti_update' => route('karyawan.cuti.update', ['cuti' => 1]),
                    'karyawan_quota_info' => route('karyawan.cuti.quota.info'),
                    'general_manager_cuti_index' => route('general_manajer.cuti.index'),
                    'general_manager_cuti_data' => route('general_manajer.cuti.data'),
                    'admin_reset_quota' => route('admin.cuti.reset.quota'),
                ],
                'current_url' => url()->current()
            ]);
        });

        Route::get('/check-db', [CutiController::class, 'checkDatabase'])->name('check.db');
        
        Route::get('/debug/routes', function () {
            $routes = Route::getRoutes();

            echo "<h1>All Routes</h1>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th></table>";

            foreach ($routes as $route) {
                echo "<tr>";
                echo "<td>" . implode('|', $route->methods()) . "</td>";
                echo "<td>" . $route->uri() . "</td>";
                echo "<td>" . ($route->getName() ?? '-') . "</td>";
                echo "<td>" . ($route->getActionName() ?? '-') . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        });

        Route::get('/test/route/{name}', function ($name) {
            try {
                $url = route($name);
                return response()->json([
                    'success' => true,
                    'name' => $name,
                    'url' => $url,
                    'exists' => true
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'name' => $name,
                    'error' => $e->getMessage(),
                    'exists' => false
                ]);
            }
        });

        // Debug route untuk Manager Divisi Projects
        Route::get('/test/manager-divisi-projects', function () {
            $user = Auth::user();
            
            if ($user->role !== 'manager_divisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya untuk manager divisi'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'endpoints' => [
                    'projects-dropdown' => route('manager_divisi.api.projects-dropdown'),
                    'api_projects_dropdown' => route('manager.divisi.project.dropdown'),
                    'karyawan_dropdown' => route('manager_divisi.api.karyawan-dropdown'),
                    'tasks_api' => route('manager_divisi.api.tasks-api'),
                    'tasks_createTask' => route('manager_divisi.tasks.createTask'),
                    'api_tasks_create_task' => route('manager_divisi.api.tasks.create-task'),
                ],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'divisi_id' => $user->divisi_id
                ]
            ]);
        });

        Route::get('/test/my-cuti-status', function () {
            $user = Auth::user();
            $today = now()->format('Y-m-d');

            $cutiAktif = \App\Models\Cuti::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereDate('tanggal_mulai', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)
                ->get();

            return response()->json([
                'user_id' => $user->id,
                'user_role' => $user->role,
                'today' => $today,
                'on_leave' => $cutiAktif->count() > 0,
                'cuti_details' => $cutiAktif,
                'absensi_route' => route('karyawan.absensi.page'),
                'home_route' => route('karyawan.home')
            ]);
        });

        // DEBUG: Check latest task with multi-assign
        Route::get('/debug/latest-task', function () {
            try {
                $latestTask = \App\Models\Task::latest('id')->first();
                
                if (!$latestTask) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tasks found'
                    ]);
                }

                // Get raw database value
                $rawData = \DB::table('tasks')->where('id', $latestTask->id)->first();

                // Test query with both methods
                $testUser7Old = \App\Models\Task::where('id', $latestTask->id)
                    ->whereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [7])
                    ->count();

                $testUser7New = \App\Models\Task::where('id', $latestTask->id)
                    ->whereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [7])
                    ->count();

                $rawSql = \DB::select("
                    SELECT 
                        assigned_to_ids,
                        JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(7)) as contains_7,
                        JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(6)) as contains_6
                    FROM tasks
                    WHERE id = ?
                ", [$latestTask->id]);

                return response()->json([
                    'success' => true,
                    'task_id' => $latestTask->id,
                    'judul' => $latestTask->judul,
                    'nama_tugas' => $latestTask->nama_tugas,
                    'assigned_to' => $latestTask->assigned_to,
                    'assigned_to_ids' => $latestTask->assigned_to_ids,
                    'assigned_to_ids_count' => is_array($latestTask->assigned_to_ids) ? count($latestTask->assigned_to_ids) : 0,
                    'raw_assigned_to_ids' => $rawData ? $rawData->assigned_to_ids : null,
                    'is_broadcast' => $latestTask->is_broadcast ?? false,
                    'created_at' => $latestTask->created_at,
                    'query_tests' => [
                        'test_user_7_with_old_json_contains' => $testUser7Old,
                        'test_user_7_with_new_json_search' => $testUser7New,
                    ],
                    'raw_sql_debug' => $rawSql,
                    'message' => 'Latest task retrieved successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        // DEBUG: Check which users can see a specific task
        Route::get('/debug/task-visibility/{taskId}', function ($taskId) {
            try {
                $task = \App\Models\Task::find($taskId);
                
                if (!$task) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Task not found'
                    ]);
                }

                // Get all users
                $allUsers = \App\Models\User::whereIn('role', ['karyawan', 'manager_divisi', 'admin'])->get();
                
                $canSeeTask = [];
                $cannotSeeTask = [];

                foreach ($allUsers as $user) {
                    // Test the same query as karyawanTasks with NEW JSON_CONTAINS(JSON_ARRAY()) method
                    $taskCount = \App\Models\Task::where('id', $taskId)
                        ->where(function($q) use ($user) {
                            $q->where('assigned_to', $user->id)
                                ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$user->id]);
                        })
                        ->count();

                    if ($taskCount > 0) {
                        $canSeeTask[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'role' => $user->role
                        ];
                    } else {
                        $cannotSeeTask[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'role' => $user->role
                        ];
                    }
                }

                // Also test raw SQL to debug
                $rawSql = \DB::select("
                    SELECT 
                        id, 
                        assigned_to, 
                        assigned_to_ids,
                        JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(7)) as contains_7_new,
                        JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(6)) as contains_6_new
                    FROM tasks
                    WHERE id = ?
                ", [$taskId]);

                return response()->json([
                    'success' => true,
                    'task_id' => $taskId,
                    'task_assigned_to' => $task->assigned_to,
                    'task_assigned_to_ids' => $task->assigned_to_ids,
                    'can_see_count' => count($canSeeTask),
                    'can_see' => $canSeeTask,
                    'cannot_see_count' => count($cannotSeeTask),
                    'cannot_see' => $cannotSeeTask,
                    'raw_sql_debug' => $rawSql,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        // DEBUG: Manual test untuk karyawan 7 lihat task 17
        Route::get('/debug/karyawan-7-see-task-17', function () {
            try {
                $userId = 7;
                $taskId = 17;

                $task = \App\Models\Task::find($taskId);
                if (!$task) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Task tidak ditemukan'
                    ]);
                }

                // Test query 1: assigned_to check
                $canSee1 = $task->assigned_to == $userId;

                // Test query 2: JSON_CONTAINS
                $canSee2 = \DB::selectOne("
                    SELECT JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?)) as result
                    FROM tasks
                    WHERE id = ?
                ", [$userId, $taskId]);

                // Test query 3: Full where clause
                $count = \App\Models\Task::where('id', $taskId)
                    ->where(function($q) use ($userId) {
                        $q->where('assigned_to', $userId)
                            ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
                    })
                    ->count();

                return response()->json([
                    'success' => true,
                    'user_id' => $userId,
                    'task_id' => $taskId,
                    'task_data' => [
                        'assigned_to' => $task->assigned_to,
                        'assigned_to_ids' => $task->assigned_to_ids,
                    ],
                    'tests' => [
                        'assigned_to_match' => $canSee1,
                        'json_contains_result' => $canSee2->result ?? null,
                        'full_query_count' => $count,
                    ],
                    'should_see' => $canSee1 || ($canSee2 && $canSee2->result),
                    'message' => ($canSee1 || ($canSee2 && $canSee2->result)) 
                        ? 'User 7 SHOULD be able to see this task' 
                        : 'User 7 should NOT see this task'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        Route::get('/test/edit-cuti/{id}', function ($id) {
            try {
                $cuti = \App\Models\Cuti::find($id);

                if (!$cuti) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cuti tidak ditemukan'
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'cuti' => $cuti,
                    'edit_route' => route('karyawan.cuti.edit', $cuti->id),
                    'update_route' => route('karyawan.cuti.update', $cuti->id),
                    'can_edit' => $cuti->status === 'menunggu'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
        });
    });
}

// DEBUG: Test karyawanTasks for current user (protected by auth)
Route::middleware('auth')->group(function () {
    Route::get('/debug/current-user-tasks', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        $userId = $user->id;
        $tasks = \App\Models\Task::with(['creator', 'assigner', 'comments', 'files', 'project', 'targetDivisi'])
                                    ->where(function($q) use ($userId) {
                                            $q->where('assigned_to', $userId)
                                                ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
                                    })
                                    ->orderBy('deadline', 'asc')
                                    ->get();
        
        return response()->json([
            'current_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'query_result_count' => $tasks->count(),
            'tasks' => $tasks->map(function($t) {
                return [
                    'id' => $t->id,
                    'judul' => $t->judul,
                    'assigned_to' => $t->assigned_to,
                    'assigned_to_ids' => $t->assigned_to_ids,
                ];
            }),
        ]);
    });

    // DEBUG: Incrementally test to see where query breaks
    Route::get('/debug/test-tasks-step/{step}', function ($step) {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        $userId = $user->id;
        
        switch ($step) {
            case 1:
                $tasks = \App\Models\Task::where('assigned_to', $userId)->get();
                $label = "Only assigned_to = {$userId}";
                break;
                
            case 2:
                $tasks = \App\Models\Task::whereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId])->get();
                $label = "Only JSON_CONTAINS for {$userId}";
                break;
                
            case 3:
                $tasks = \App\Models\Task::where(function($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
                })->get();
                $label = "Combined where clause (assigned_to OR JSON_CONTAINS)";
                break;
                
            case 4:
                $tasks = \App\Models\Task::where(function($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
                })->with('creator')->get();
                $label = "Combined where + with creator";
                break;
                
            case 5:
                $tasks = \App\Models\Task::where(function($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhereRaw("JSON_CONTAINS(assigned_to_ids, JSON_ARRAY(?))", [$userId]);
                })->with(['creator', 'assigner', 'comments', 'files', 'project', 'targetDivisi'])->get();
                $label = "Full relationships";
                break;
                
            default:
                return response()->json(['error' => 'Invalid step'], 400);
        }
        
        return response()->json([
            'step' => $step,
            'label' => $label,
            'user_id' => $userId,
            'count' => $tasks->count(),
            'ids' => $tasks->pluck('id')->values()->toArray(),
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| MAIN FALLBACK ROUTE
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (Auth::check()) {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.beranda');
            case 'karyawan':
                return redirect()->route('karyawan.home');
            case 'general_manager':
                return redirect()->route('general_manajer.home');
            case 'manager_divisi':
                return redirect()->route('manager_divisi.home');
            case 'owner':
                return redirect()->route('owner.home');
            case 'finance':
                return redirect()->route('finance.beranda');
            default:
                return redirect('/login');
        }
    }

    return redirect('/login');
});