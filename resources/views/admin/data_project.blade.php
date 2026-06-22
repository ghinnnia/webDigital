@php
    use App\Models\ProjectNotification;
    use Carbon\Carbon;

    // Ambil notifikasi dari database
    $notifikasiFromDb = ProjectNotification::with('project')
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get();

    // Hitung notifikasi dari deadline project (manual)
    $notifProyek = [];
    $hariIni = Carbon::now();

    if(isset($project)) {
        foreach ($project as $item) {
            // Cek sisa hari Periode Pengerjaan
            if ($item->tanggal_selesai_pengerjaan) {
                $tglSelesaiPengerjaan = Carbon::parse($item->tanggal_selesai_pengerjaan);
                $sisaHariPengerjaan = floor($hariIni->diffInDays($tglSelesaiPengerjaan, false));

                // Notifikasi jika <= 30 hari (termasuk yang sudah lewat)
                if ($sisaHariPengerjaan <= 30) {
                    if ($sisaHariPengerjaan < 0) {
                        $pesan = "⚠️ Periode PENGERJAAN proyek '{$item->nama}' telah berakhir " . abs($sisaHariPengerjaan) . " hari yang lalu";
                        $jenisNotif = 'expired_pengerjaan';
                    } else {
                        $pesan = "⚠️ Periode PENGERJAAN proyek '{$item->nama}' akan berakhir dalam {$sisaHariPengerjaan} hari";
                        $jenisNotif = 'pengerjaan';
                    }
                    
                    $notifProyek[] = [
                        'pesan' => $pesan,
                        'jenis' => $jenisNotif,
                        'tanggal' => $tglSelesaiPengerjaan->format('d-m-Y'),
                        'sisa_hari' => $sisaHariPengerjaan,
                        'is_read' => false,
                        'kategori' => 'Pengerjaan'
                    ];
                }
            }
            
            // Cek Periode Kerjasama
            if ($item->tanggal_selesai_kerjasama) {
                $tglSelesaiKerjasama = Carbon::parse($item->tanggal_selesai_kerjasama);
                $sisaHariKerjasama = floor($hariIni->diffInDays($tglSelesaiKerjasama, false));
                
                // Notifikasi jika <= 30 hari (termasuk yang sudah lewat)
                if ($sisaHariKerjasama <= 30) {
                    if ($sisaHariKerjasama < 0) {
                        $pesan = "⚠️ Periode KERJA SAMA dengan '{$item->nama}' telah berakhir " . abs($sisaHariKerjasama) . " hari yang lalu";
                        $jenisNotif = 'expired_kerjasama';
                    } else {
                        $pesan = "⚠️ Periode KERJA SAMA dengan '{$item->nama}' akan berakhir dalam {$sisaHariKerjasama} hari";
                        $jenisNotif = 'kerjasama';
                    }
                    
                    $notifProyek[] = [
                        'pesan' => $pesan,
                        'jenis' => $jenisNotif,
                        'tanggal' => $tglSelesaiKerjasama->format('d-m-Y'),
                        'sisa_hari' => $sisaHariKerjasama,
                        'is_read' => false,
                        'kategori' => 'Kerja Sama'
                    ];
                }
            }
        }
    }

    // Gabungkan notifikasi dari database dan manual
    $semuaNotifikasi = [];
    
    foreach ($notifikasiFromDb as $notif) {
        $semuaNotifikasi[] = [
            'id' => $notif->id,
            'pesan' => $notif->message,
            'jenis' => $notif->type,
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'is_read' => $notif->is_read
        ];
    }
    
    foreach ($notifProyek as $notif) {
        $semuaNotifikasi[] = [
            'id' => null,
            'pesan' => $notif['pesan'],
            'jenis' => $notif['jenis'],
            'tanggal' => $notif['tanggal'],
            'is_read' => $notif['is_read']
        ];
    }
    
    // Urutkan berdasarkan tanggal terbaru
    usort($semuaNotifikasi, function($a, $b) {
        return strtotime($b['tanggal']) - strtotime($a['tanggal']);
    });
    
    $totalNotif = count($semuaNotifikasi);
    $unreadNotif = count(array_filter($semuaNotifikasi, function($n) { return !$n['is_read']; }));
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Project</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Notyf -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#ffffff",
                        "background-dark": "#f8fafc",
                        "sidebar-light": "#f3f4f6",
                        "sidebar-dark": "#1e293b",
                        "card-light": "#ffffff",
                        "card-dark": "#1e293b",
                        "text-light": "#1e293b",
                        "text-dark": "#f8fafc",
                        "text-muted-light": "#64748b",
                        "text-muted-dark": "#94a3b8",
                        "border-light": "#e2e8f0",
                        "border-dark": "#334155",
                        "success": "#10b981",
                        "warning": "#f59e0b",
                        "danger": "#ef4444"
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                    boxShadow: {
                        card: "0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)",
                        "card-hover": "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)"
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }

        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .order-table {
            transition: all 0.2s ease;
        }

        .order-table tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .modal {
            transition: opacity 0.25s ease;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-dalam-pengerjaan {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .status-selesai {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-dibatalkan {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        .status-aktif {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-selesai-kerjasama {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-ditangguhkan {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        .hamburger-line {
            transition: all 0.3s ease-in-out;
        }

        .hamburger-active .line1 {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger-active .line2 {
            opacity: 0;
        }

        .hamburger-active .line3 {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        .nav-item {
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background-color: #3b82f6;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        @media (min-width: 768px) {
            .nav-item::before {
                right: auto;
                left: 0;
                transform: translateX(-100%);
            }
        }

        .nav-item:hover::before,
        .nav-item.active::before {
            transform: translateX(0);
        }

        .sidebar-fixed {
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
        }

        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
            }
        }

        .sidebar-fixed::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-fixed::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .sidebar-fixed::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .sidebar-fixed::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 639px) {
            .desktop-table {
                display: none;
            }

            .mobile-cards {
                display: block;
            }

            .desktop-pagination {
                display: none !important;
            }
        }

        @media (min-width: 640px) {
            .desktop-table {
                display: block;
            }

            .mobile-cards {
                display: none;
            }

            .mobile-pagination {
                display: none !important;
            }
        }

        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .page-btn {
            transition: all 0.2s ease;
        }

        .page-btn:hover:not(:disabled) {
            transform: scale(1.1);
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .desktop-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
        }

        .desktop-page-btn {
            min-width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .desktop-page-btn.active {
            background-color: #3b82f6;
            color: white;
        }

        .desktop-page-btn:not(.active) {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .desktop-page-btn:not(.active):hover {
            background-color: #e2e8f0;
        }

        .desktop-nav-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .desktop-nav-btn:hover:not(:disabled) {
            background-color: #e2e8f0;
        }

        .desktop-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .panel {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .panel-header {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .panel-body {
            padding: 1.5rem;
        }

        .scrollable-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
        }

        .scrollable-table-container::-webkit-scrollbar {
            height: 12px;
            width: 12px;
        }

        .scrollable-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }

        .scrollable-table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
            border: 2px solid #f1f5f9;
        }

        .scrollable-table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .data-table {
            width: 100%;
            min-width: 2000px;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .data-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .data-table tbody tr:hover {
            background: #f3f4f6;
        }

        .table-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .progress-bar {
            width: 100%;
            background-color: #e2e8f0;
            border-radius: 9999px;
            height: 8px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 9999px;
        }

        .truncate-text {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .notification-item {
            transition: background-color 0.2s ease;
        }
        
        .notification-item:hover {
            background-color: #f3f4f6;
        }

        /* NOTYF CUSTOM - LEBIH KECIL DAN RAPI */
        .notyf {
            max-width: 380px !important;
        }
        
        .notyf__toast {
            max-width: 380px !important;
            padding: 8px 12px !important;
            font-size: 12px !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12) !important;
            min-height: 44px !important;
        }
        
        .notyf__toast .notyf__message {
            font-size: 12px !important;
            line-height: 1.3 !important;
        }
        
        .notyf__toast .notyf__icon {
            font-size: 14px !important;
            width: 18px !important;
            height: 18px !important;
            margin-right: 6px !important;
        }
        
        .notyf__toast--success {
            background: #10b981 !important;
        }
        
        .notyf__toast--error {
            background: #ef4444 !important;
        }
        
        .notyf__toast--warning {
            background: #f59e0b !important;
        }
        
        .notyf__toast--info {
            background: #3b82f6 !important;
        }
        
        .notyf-announcer {
            display: none !important;
        }

        .text-expired {
            color: #ef4444 !important;
        }
        
        .text-pengerjaan {
            color: #f59e0b !important;
        }
        
        .text-kerjasama {
            color: #10b981 !important;
        }
        
        /* Notification Bell Badge */
        .notif-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ef4444;
            color: white;
            font-size: 9px;
            font-weight: 600;
            min-width: 16px;
            height: 16px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            border: 2px solid white;
        }
        
        /* NOTIFICATION DROPDOWN - LEBIH RINGKAS */
        .notif-dropdown {
            width: 320px;
            max-height: 380px;
        }
        
        .notif-dropdown .notif-item {
            padding: 6px 10px;
            font-size: 12px;
        }
        
        .notif-dropdown .notif-item .notif-icon {
            font-size: 16px !important;
        }
        
        .notif-dropdown .notif-item .notif-text {
            font-size: 12px;
            line-height: 1.3;
        }
        
        .notif-dropdown .notif-item .notif-time {
            font-size: 10px;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin/templet.sider')

        <!-- Main Content Container -->
        <div class="main-content flex-1 flex flex-col overflow-y-auto bg-background-light">
            <main class="flex-1 flex flex-col bg-background-light">
                <div class="flex-1 p-3 sm:p-8">
                    <!-- Header with Notification Bell -->
                    <div class="flex justify-between items-center mb-4 sm:mb-8">
                        <h2 class="text-xl sm:text-3xl font-bold">Data Project</h2>
                        
                        <!-- NOTIFICATION BELL -->
                        <div class="relative">
                            <button id="notificationBell" class="relative p-2 rounded-full hover:bg-gray-100 transition">
                                <span class="material-icons-outlined text-gray-600 text-2xl">notifications_none</span>
                                <span id="notifBadge" class="notif-badge hidden">
                                    {{ $unreadNotif > 0 ? ($unreadNotif > 99 ? '99+' : $unreadNotif) : 0 }}
                                </span>
                            </button>
                            
                            <div id="notifDropdown" class="notif-dropdown absolute right-0 mt-2 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50 overflow-hidden">
                                <div class="p-2 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                    <h3 class="font-semibold text-gray-800 text-xs">Notifikasi</h3>
                                    <button onclick="markAllNotifAsRead()" class="text-[10px] text-blue-600 hover:text-blue-800">Tandai semua dibaca</button>
                                </div>
                                <div id="notifList" class="max-h-72 overflow-y-auto">
                                    @if(count($semuaNotifikasi) > 0)
                                        @foreach($semuaNotifikasi as $notif)
                                        <div class="notif-item border-b border-gray-100 hover:bg-gray-50 transition cursor-pointer {{ !$notif['is_read'] ? 'bg-blue-50' : '' }}" 
                                             onclick="markNotifAsRead({{ $notif['id'] ?? 0 }})">
                                            <div class="flex gap-2 items-start">
                                                <span class="notif-icon material-icons-outlined text-sm mt-0.5
                                                    @if(str_contains($notif['jenis'], 'expired')) text-red-500
                                                    @elseif(str_contains($notif['jenis'], 'pengerjaan')) text-orange-500
                                                    @elseif(str_contains($notif['jenis'], 'kerjasama')) text-green-500
                                                    @else text-gray-500 @endif">
                                                    @if(str_contains($notif['jenis'], 'pengerjaan')) construction
                                                    @elseif(str_contains($notif['jenis'], 'kerjasama')) handshake
                                                    @else warning @endif
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="notif-text text-gray-800 leading-relaxed">{{ $notif['pesan'] }}</p>
                                                    <p class="notif-time text-gray-400 mt-0.5">{{ $notif['tanggal'] }}</p>
                                                    @if(!$notif['is_read'])
                                                        <span class="text-[9px] text-blue-500 mt-0.5 inline-block font-medium">● Baru</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="p-4 text-center text-gray-500 text-xs">Belum ada notifikasi</div>
                                    @endif
                                </div>
                                <div class="p-1.5 border-t border-gray-200 text-center bg-gray-50">
                                    <button onclick="refreshNotifications()" class="text-[10px] text-blue-600 hover:text-blue-800">Refresh</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.data_project') }}"
                        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div class="relative w-full md:w-1/3">
                            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                search
                            </span>
                            <input name="q" value="{{ request('q') }}"
                                class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg
                                       focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Cari nama / deskripsi project..." type="text">
                        </div>

                        <div class="flex flex-wrap gap-3 w-full md:w-auto">
                            <select name="status_pengerjaan"
                                class="px-3 py-2 bg-white border border-border-light rounded-lg">
                                <option value="">Semua Status Pengerjaan</option>
                                <option value="pending" {{ request('status_pengerjaan') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="dalam_pengerjaan" {{ request('status_pengerjaan') == 'dalam_pengerjaan' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                                <option value="selesai" {{ request('status_pengerjaan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ request('status_pengerjaan') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>

                            <select name="status_kerjasama"
                                class="px-3 py-2 bg-white border border-border-light rounded-lg">
                                <option value="">Semua Status Kerjasama</option>
                                <option value="aktif" {{ request('status_kerjasama') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ request('status_kerjasama') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditangguhkan" {{ request('status_kerjasama') == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                            </select>
                            
                            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">
                                Filter
                            </button>
                            
                            <a href="{{ route('admin.data_project') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                                Reset
                            </a>
                        </div>
                    </form>

                    <!-- Tambah Project Button -->
                    <div class="mb-4 text-right">
                        <button id="tambahProjectBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            + Tambah Project
                        </button>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg border border-border-light shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <span class="material-icons-outlined">folder</span>
                            </div>
                            <div>
                                <p class="text-xs text-text-muted-light font-medium">Total Project</p>
                                <p class="text-xl font-bold text-text-light">{{ $project->total() }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-border-light shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <span class="material-icons-outlined">handshake</span>
                            </div>
                            <div>
                                <p class="text-xs text-text-muted-light font-medium">Kerjasama Aktif</p>
                                <p class="text-xl font-bold text-text-light">{{ $project->where('status_kerjasama', 'aktif')->count() }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">*Halaman ini</p>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-border-light shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                <span class="material-icons-outlined">receipt</span>
                            </div>
                            <div>
                                <p class="text-xs text-text-muted-light font-medium">Total Kerjasama (Invoice)</p>
                                <p class="text-xl font-bold text-text-light">{{ $invoices->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table Panel -->
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">view_list</span>
                                Data Project
                            </h3>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-text-muted-light">Total: <span
                                        class="font-semibold text-text-light">{{ $project->total() }}</span>
                                    project</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <!-- SCROLLABLE TABLE -->
                            <div class="desktop-table">
                                <div class="scrollable-table-container table-shadow" id="scrollableTable">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 60px;">No</th>
                                                <th style="min-width: 150px;">Invoice</th>
                                                <th style="min-width: 200px;">Nama Project</th>
                                                <th style="min-width: 200px;">Deskripsi</th>
                                                <th style="min-width: 120px;">Harga</th>
                                                <th style="min-width: 150px;">Penanggung Jawab</th>
                                                <th style="min-width: 200px;">Periode Pengerjaan</th>
                                                <th style="min-width: 200px;">Periode Kerjasama</th>
                                                <th style="min-width: 120px;">Status Pengerjaan</th>
                                                <th style="min-width: 120px;">Status Kerjasama</th>
                                                <th style="min-width: 150px;">Progres</th>
                                                <th style="min-width: 180px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="desktopTableBody">
                                            @foreach ($project as $index => $item)
                                            @php
                                                $isPengerjaanExpired = false;
                                                $isKerjasamaExpired = false;
                                                $warnaPengerjaan = 'text-orange-600';
                                                $warnaKerjasama = 'text-green-600';
                                                
                                                if($item->tanggal_selesai_pengerjaan) {
                                                    $tglPengerjaan = Carbon::parse($item->tanggal_selesai_pengerjaan);
                                                    if($tglPengerjaan->isPast()) {
                                                        $isPengerjaanExpired = true;
                                                        $warnaPengerjaan = 'text-red-600';
                                                    }
                                                }
                                                
                                                if($item->tanggal_selesai_kerjasama) {
                                                    $tglKerjasama = Carbon::parse($item->tanggal_selesai_kerjasama);
                                                    if($tglKerjasama->isPast()) {
                                                        $isKerjasamaExpired = true;
                                                        $warnaKerjasama = 'text-red-600';
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td style="min-width: 60px;">
                                                    {{ ($project->currentPage() - 1) * $project->perPage() + $index + 1 }}
                                                </td>
                                                <td style="min-width: 150px;">
                                                    @if ($item->invoice)
                                                        Invoice #{{ $item->invoice->id }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 200px;">{{ $item->nama }}</td>
                                                <td style="min-width: 200px;" class="truncate-text"
                                                    title="{{ $item->deskripsi }}">
                                                    {{ Str::limit($item->deskripsi, 50) }}
                                                </td>
                                                <td style="min-width: 120px;">Rp
                                                    {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td style="min-width: 150px;">
                                                    @if ($item->penanggungJawab)
                                                        {{ $item->penanggungJawab->name }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 200px;">
                                                    <div class="text-xs">
                                                        @if ($item->tanggal_mulai_pengerjaan && $item->tanggal_selesai_pengerjaan)
                                                            <span class="font-semibold text-blue-600">Mulai:</span> 
                                                            <span>{{ $item->tanggal_mulai_pengerjaan->format('d-m-Y') }}</span><br>
                                                            <span class="font-semibold {{ $warnaPengerjaan }}">Selesai:</span> 
                                                            <span class="{{ $warnaPengerjaan }}">{{ $item->tanggal_selesai_pengerjaan->format('d-m-Y') }}</span>
                                                            @if($isPengerjaanExpired)
                                                                <span class="text-red-500 text-xs ml-1">(LEWAT)</span>
                                                            @endif
                                                        @elseif ($item->tanggal_mulai_pengerjaan)
                                                            <span class="font-semibold text-blue-600">Mulai:</span> 
                                                            <span>{{ $item->tanggal_mulai_pengerjaan->format('d-m-Y') }}</span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td style="min-width: 200px;">
                                                    <div class="text-xs">
                                                        @if ($item->tanggal_mulai_kerjasama && $item->tanggal_selesai_kerjasama)
                                                            <span class="font-semibold text-blue-600">Mulai:</span> 
                                                            <span>{{ $item->tanggal_mulai_kerjasama->format('d-m-Y') }}</span><br>
                                                            <span class="font-semibold {{ $warnaKerjasama }}">Selesai:</span> 
                                                            <span class="{{ $warnaKerjasama }}">{{ $item->tanggal_selesai_kerjasama->format('d-m-Y') }}</span>
                                                            @if($isKerjasamaExpired)
                                                                <span class="text-red-500 text-xs ml-1">(LEWAT)</span>
                                                            @endif
                                                        @elseif ($item->tanggal_mulai_kerjasama)
                                                            <span class="font-semibold text-blue-600">Mulai:</span> 
                                                            <span>{{ $item->tanggal_mulai_kerjasama->format('d-m-Y') }}</span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td style="min-width: 120px;">
                                                    <span class="status-badge status-{{ str_replace('_', '-', $item->status_pengerjaan) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $item->status_pengerjaan)) }}
                                                    </span>
                                                </td>
                                                <td style="min-width: 120px;">
                                                    @php
                                                        $tglSelesaiKerjasama = $item->tanggal_selesai_kerjasama ? \Carbon\Carbon::parse($item->tanggal_selesai_kerjasama) : null;
                                                        $isCloseToDeadline = $tglSelesaiKerjasama && now()->diffInDays($tglSelesaiKerjasama, false) <= 30 && now()->diffInDays($tglSelesaiKerjasama, false) >= 0 && !$tglSelesaiKerjasama->isPast();
                                                    @endphp
                                                    <span class="status-badge status-{{ $item->status_kerjasama }} flex items-center gap-1 justify-center">
                                                        @if($isCloseToDeadline)
                                                            <span class="relative flex h-2 w-2">
                                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                                            </span>
                                                        @endif
                                                        {{ ucfirst($item->status_kerjasama) }}
                                                    </span>
                                                </td>
                                                <td style="min-width: 150px;">
                                                    <div class="progress-bar">
                                                        <div class="progress-fill {{ $item->progres < 50 ? 'bg-red-500' : ($item->progres < 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                                            style="width: {{ $item->progres }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-600 mt-1 block">{{ $item->progres }}%</span>
                                                </td>
                                                <td style="min-width: 180px; text-align: center;">
                                                    <div class="flex justify-center gap-2">
                                                        <button class="detail-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                            onclick="openDetailModal({{ $item->id }})" title="Lihat Detail">
                                                            <span class="material-icons-outlined">visibility</span>
                                                        </button>
                                                        <button class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                            onclick="openEditModal({{ $item->id }})" title="Edit">
                                                            <span class="material-icons-outlined">edit</span>
                                                        </button>
                                                        <button class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                            onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama) }}')" title="Hapus">
                                                            <span class="material-icons-outlined">delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Mobile Card View -->
                            <div class="mobile-cards space-y-4" id="mobile-cards">
                                @foreach ($project as $item)
                                @php
                                    $isPengerjaanExpired = false;
                                    $isKerjasamaExpired = false;
                                    $warnaPengerjaan = 'text-orange-600';
                                    $warnaKerjasama = 'text-green-600';
                                    
                                    if($item->tanggal_selesai_pengerjaan) {
                                        $tglPengerjaan = Carbon::parse($item->tanggal_selesai_pengerjaan);
                                        if($tglPengerjaan->isPast()) {
                                            $isPengerjaanExpired = true;
                                            $warnaPengerjaan = 'text-red-600';
                                        }
                                    }
                                    
                                    if($item->tanggal_selesai_kerjasama) {
                                        $tglKerjasama = Carbon::parse($item->tanggal_selesai_kerjasama);
                                        if($tglKerjasama->isPast()) {
                                            $isKerjasamaExpired = true;
                                            $warnaKerjasama = 'text-red-600';
                                        }
                                    }
                                @endphp
                                    <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-base">{{ $item->nama }}</h4>
                                                <p class="text-sm text-text-muted-light">
                                                    @if ($item->invoice)
                                                        Invoice #{{ $item->invoice->id }}<br>
                                                    @endif
                                                    Mulai: {{ optional($item->tanggal_mulai_pengerjaan)->format('d-m-Y') ?? '-' }}
                                                </p>
                                            </div>
                                            <div class="flex gap-2">
                                                <button class="detail-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                    onclick="openDetailModal({{ $item->id }})" title="Lihat Detail">
                                                    <span class="material-icons-outlined">visibility</span>
                                                </button>
                                                <button class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                    onclick="openEditModal({{ $item->id }})" title="Edit">
                                                    <span class="material-icons-outlined">edit</span>
                                                </button>
                                                <button class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                    onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama) }}')" title="Hapus">
                                                    <span class="material-icons-outlined">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                                            <div>
                                                <p class="text-text-muted-light">Status Pengerjaan</p>
                                                <p>
                                                    <span class="status-badge status-{{ str_replace('_', '-', $item->status_pengerjaan) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $item->status_pengerjaan)) }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-text-muted-light">Status Kerjasama</p>
                                                <p>
                                                    <span class="status-badge status-{{ $item->status_kerjasama }}">
                                                        {{ ucfirst($item->status_kerjasama) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <p class="text-text-muted-light">Progres</p>
                                            <div class="progress-bar mt-1">
                                                <div class="progress-fill {{ $item->progres < 50 ? 'bg-red-500' : ($item->progres < 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                                    style="width: {{ $item->progres }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1">{{ $item->progres }}%</p>
                                        </div>
                                        <div class="mt-3 text-xs">
                                            <p class="text-text-muted-light">Periode Pengerjaan:</p>
                                            <p class="{{ $warnaPengerjaan }}">
                                                {{ optional($item->tanggal_mulai_pengerjaan)->format('d-m-Y') ?? '-' }} → 
                                                {{ optional($item->tanggal_selesai_pengerjaan)->format('d-m-Y') ?? '-' }}
                                                @if($isPengerjaanExpired) <span class="text-red-500">(LEWAT)</span> @endif
                                            </p>
                                            <p class="text-text-muted-light mt-1">Periode Kerjasama:</p>
                                            <p class="{{ $warnaKerjasama }}">
                                                {{ optional($item->tanggal_mulai_kerjasama)->format('d-m-Y') ?? '-' }} → 
                                                {{ optional($item->tanggal_selesai_kerjasama)->format('d-m-Y') ?? '-' }}
                                                @if($isKerjasamaExpired) <span class="text-red-500">(LEWAT)</span> @endif
                                            </p>
                                        </div>
                                        <div class="mt-3">
                                            <p class="text-text-muted-light">Harga</p>
                                            <p class="font-medium">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Desktop Pagination -->
                            <div class="desktop-pagination">
                                <button class="desktop-nav-btn" @if ($project->currentPage() == 1) disabled @endif
                                    onclick="window.location.href='{{ $project->previousPageUrl() }}'">
                                    <span class="material-icons-outlined text-sm">chevron_left</span>
                                </button>
                                <div class="flex gap-1">
                                    @for ($i = 1; $i <= $project->lastPage(); $i++)
                                        <button class="desktop-page-btn {{ $i == $project->currentPage() ? 'active' : '' }}"
                                            onclick="window.location.href='{{ $project->url($i) }}'">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <button class="desktop-nav-btn" @if ($project->currentPage() == $project->lastPage()) disabled @endif
                                    onclick="window.location.href='{{ $project->nextPageUrl() }}'">
                                    <span class="material-icons-outlined text-sm">chevron_right</span>
                                </button>
                            </div>

                            <!-- Mobile Pagination -->
                            <div class="mobile-pagination md:hidden flex justify-center items-center gap-2 mt-4">
                                <button class="page-btn w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                    @if ($project->currentPage() == 1) disabled @endif
                                    onclick="window.location.href='{{ $project->previousPageUrl() }}'">
                                    <span class="material-icons-outlined text-sm">chevron_left</span>
                                </button>
                                <div class="flex gap-1">
                                    @for ($i = 1; $i <= $project->lastPage(); $i++)
                                        <button class="page-btn w-8 h-8 rounded-full flex items-center justify-center text-sm {{ $i == $project->currentPage() ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }}"
                                            onclick="window.location.href='{{ $project->url($i) }}'">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <button class="page-btn w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                    @if ($project->currentPage() == $project->lastPage()) disabled @endif
                                    onclick="window.location.href='{{ $project->nextPageUrl() }}'">
                                    <span class="material-icons-outlined text-sm">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                    Copyright ©2025 by digicity.id
                </footer>
            </main>
        </div>
    </div>

    <!-- MODAL TAMBAH PROJECT -->
    <div id="tambahModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Project Baru</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="tambahForm" action="{{ route('admin.project.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Invoice</label>
                            <select name="invoice_id" id="tambahInvoice" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
                                <option value="">-- Pilih Invoice --</option>
                                @foreach ($invoices as $invoice)
                                    <option value="{{ $invoice->id }}"
                                        data-nama="{{ $invoice->judul ?? 'Project dari Invoice #' . $invoice->id }}"
                                        data-deskripsi="{{ $invoice->deskripsi ?? '' }}"
                                        data-harga="{{ $invoice->total ?? 0 }}"
                                        data-tanggal-mulai="{{ $invoice->tanggal_mulai ? $invoice->tanggal_mulai->format('Y-m-d') : '' }}"
                                        data-tanggal-selesai="{{ $invoice->tanggal_selesai ? $invoice->tanggal_selesai->format('Y-m-d') : '' }}">
                                        Invoice #{{ $invoice->id }} - {{ $invoice->judul ?? 'Tanpa Judul' }} (Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project</label>
                            <input type="text" name="nama" id="tambahNama" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" readonly required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" id="tambahDeskripsi" rows="3" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" readonly required></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                            <input type="number" name="harga" id="tambahHarga" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" readonly required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Pengerjaan</label>
                                <input type="date" name="tanggal_mulai_pengerjaan" id="tambahTanggalMulaiPengerjaan" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Pengerjaan</label>
                                <input type="date" name="tanggal_selesai_pengerjaan" id="tambahTanggalSelesaiPengerjaan" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Kerjasama</label>
                                <input type="date" name="tanggal_mulai_kerjasama" id="tambahTanggalMulaiKerjasama" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Kerjasama</label>
                                <input type="date" name="tanggal_selesai_kerjasama" id="tambahTanggalSelesaiKerjasama" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pengerjaan</label>
                                <select name="status_pengerjaan" id="tambahStatusPengerjaan" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg">
                                    <option value="pending">Pending</option>
                                    <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerjasama</label>
                                <select name="status_kerjasama" id="tambahStatusKerjasama" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg">
                                    <option value="aktif">Aktif</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="ditangguhkan">Ditangguhkan</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Progres (%)</label>
                            <input type="range" name="progres" id="tambahProgres" min="0" max="100" value="0" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                oninput="document.getElementById('tambahProgresValue').textContent = this.value + '%'">
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-sm text-gray-600">0%</span>
                                <span id="tambahProgresValue" class="text-sm font-medium">0%</span>
                                <span class="text-sm text-gray-600">100%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL PROJECT -->
    <div id="detailModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Detail Project</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <div id="detailContent" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><strong>ID Project:</strong> <span id="detailId"></span></div>
                        <div><strong>Invoice:</strong> <span id="detailInvoice"></span></div>
                        <div class="col-span-2"><strong>Nama Project:</strong> <span id="detailNama"></span></div>
                        <div class="col-span-2"><strong>Deskripsi:</strong> <p id="detailDeskripsi" class="mt-1"></p></div>
                        <div><strong>Harga:</strong> <span id="detailHarga"></span></div>
                        <div><strong>Progres:</strong> <span id="detailProgres"></span></div>
                        <div><strong>Status Pengerjaan:</strong> <span id="detailStatusPengerjaan"></span></div>
                        <div><strong>Status Kerjasama:</strong> <span id="detailStatusKerjasama"></span></div>
                        <div><strong>Tanggal Mulai Pengerjaan:</strong> <span id="detailTanggalMulaiPengerjaan"></span></div>
                        <div><strong>Tanggal Selesai Pengerjaan:</strong> <span id="detailTanggalSelesaiPengerjaan"></span></div>
                        <div><strong>Tanggal Mulai Kerjasama:</strong> <span id="detailTanggalMulaiKerjasama"></span></div>
                        <div><strong>Tanggal Selesai Kerjasama:</strong> <span id="detailTanggalSelesaiKerjasama"></span></div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PROJECT -->
    <div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Project</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editId">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project</label>
                            <input type="text" name="nama" id="editNama" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" id="editDeskripsi" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Pengerjaan</label>
                                <input type="date" name="tanggal_mulai_pengerjaan" id="editTanggalMulaiPengerjaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Pengerjaan</label>
                                <input type="date" name="tanggal_selesai_pengerjaan" id="editTanggalSelesaiPengerjaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Kerjasama</label>
                                <input type="date" name="tanggal_mulai_kerjasama" id="editTanggalMulaiKerjasama" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Kerjasama</label>
                                <input type="date" name="tanggal_selesai_kerjasama" id="editTanggalSelesaiKerjasama" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerjasama</label>
                            <select name="status_kerjasama" id="editStatusKerjasama" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="aktif">Aktif</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditangguhkan">Ditangguhkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE PROJECT -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <div class="mb-6">
                    <p class="text-gray-700">Apakah Anda yakin ingin menghapus project <span id="deleteNama" class="font-semibold"></span>?</p>
                    <p class="text-sm text-gray-500 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" id="deleteId">
                    <div class="flex justify-end gap-2">
                        <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-20 opacity-0 flex items-center z-50">
        <span id="toastMessage" class="mr-2"></span>
        <button id="closeToast" class="ml-2 text-white hover:text-gray-200">
            <span class="material-icons-outlined">close</span>
        </button>
    </div>

    @if (session('success'))
        <div id="successToast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center z-50">
            <span class="mr-2">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div id="errorToast" class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center z-50">
            <span class="mr-2">{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // ============================
        // TOAST NOTIFICATION SYSTEM
        // ============================
        
        // Inisialisasi Notyf - Bottom Right & Smaller
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'bottom' },
            ripple: false,
            dismissible: true,
            types: [
                {
                    type: 'warning',
                    background: '#f59e0b',
                    icon: '<i class="fas fa-exclamation-triangle" style="font-size:12px;"></i>',
                    duration: 5000
                },
                {
                    type: 'danger',
                    background: '#ef4444',
                    icon: '<i class="fas fa-circle-exclamation" style="font-size:12px;"></i>',
                    duration: 5000
                },
                {
                    type: 'info',
                    background: '#3b82f6',
                    icon: '<i class="fas fa-bell" style="font-size:12px;"></i>',
                    duration: 4000
                }
            ]
        });

        // Flag untuk mencegah notifikasi muncul berulang
        var initialNotificationsShown = false;
        var notificationShown = false;

        // Fungsi untuk memainkan suara notifikasi (opsional)
        function playNotificationSound() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 880;
                gainNode.gain.value = 0.1;
                
                oscillator.start();
                gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 0.2);
                oscillator.stop(audioContext.currentTime + 0.2);
                
                audioContext.resume();
            } catch(e) {
                // Silent fail
            }
        }

        // Queue untuk notifikasi berurutan
        let notifQueue = [];
        let isPlaying = false;

        function showToastNotification(message, type) {
            if (type === 'expired_pengerjaan' || type === 'expired_kerjasama' || type === 'danger') {
                notyf.error({
                    message: message,
                    icon: '<i class="fas fa-circle-exclamation"></i>'
                });
            } else if (type === 'warning' || type === 'pengerjaan' || type === 'kerjasama') {
                notyf.open({
                    type: 'warning',
                    message: message
                });
            } else {
                notyf.success({
                    message: message,
                    icon: '<i class="fas fa-info-circle"></i>'
                });
            }
            
            playNotificationSound();
        }

        function processNotificationQueue() {
            if (notifQueue.length === 0) {
                isPlaying = false;
                return;
            }
            
            isPlaying = true;
            const notif = notifQueue.shift();
            showToastNotification(notif.message, notif.type);
            
            setTimeout(processNotificationQueue, 1500);
        }

        function addNotificationToQueue(message, type) {
            notifQueue.push({ message: message, type: type });
            if (!isPlaying) {
                processNotificationQueue();
            }
        }

        // Data notifikasi dari server
        var serverNotifications = [];
        @if(count($notifProyek) > 0)
            serverNotifications = @json($notifProyek);
        @endif
        
        function showInitialNotifications() {
            // Cegah notifikasi muncul berulang kali
            if (initialNotificationsShown || notificationShown) return;
            
            var warningNotifs = [];
            var expiredNotifs = [];
            
            for(var i = 0; i < serverNotifications.length; i++) {
                var notif = serverNotifications[i];
                if(notif.sisa_hari >= 0 && notif.sisa_hari <= 30) {
                    warningNotifs.push(notif);
                } else if(notif.sisa_hari < 0) {
                    expiredNotifs.push(notif);
                }
            }
            
            warningNotifs.sort(function(a, b) {
                return a.sisa_hari - b.sisa_hari;
            });
            
            // Tampilkan notifikasi expired terlebih dahulu (lebih urgent)
            for(var e = 0; e < expiredNotifs.length; e++) {
                var notif = expiredNotifs[e];
                addNotificationToQueue(notif.pesan, notif.jenis);
            }
            
            for(var w = 0; w < warningNotifs.length; w++) {
                var notif = warningNotifs[w];
                addNotificationToQueue(notif.pesan, notif.jenis);
            }
            
            initialNotificationsShown = true;
            notificationShown = true;
            
            // Simpan ke session storage agar tidak muncul lagi
            try {
                sessionStorage.setItem('notifications_shown', 'true');
            } catch(e) {}
        }

        function formatNotifDate(dateString) {
            var date = new Date(dateString);
            return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        function getNotifIcon(type) {
            if(type === 'pengerjaan' || type === 'expired_pengerjaan') return 'construction';
            if(type === 'kerjasama' || type === 'expired_kerjasama') return 'handshake';
            return 'warning';
        }

        function getNotifColor(type) {
            if(type === 'pengerjaan') return 'text-orange-500';
            if(type === 'kerjasama') return 'text-green-500';
            if(type === 'expired_pengerjaan' || type === 'expired_kerjasama') return 'text-red-500';
            return 'text-gray-500';
        }

        function fetchProjectNotifications() {
            fetch('/admin/project/notifications', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.data) {
                    updateNotifUI(data.data);
                }
            })
            .catch(function(error) {
                console.error('Error fetching notifications:', error);
            });
        }

        function updateNotifUI(notifications) {
            var unreadCount = 0;
            for(var i = 0; i < notifications.length; i++) {
                if(!notifications[i].is_read) unreadCount++;
            }
            
            var badge = document.getElementById('notifBadge');
            var listContainer = document.getElementById('notifList');
            
            if (unreadCount > 0) {
                badge.classList.remove('hidden');
                badge.style.display = 'flex';
                badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
            } else {
                badge.classList.add('hidden');
                badge.style.display = 'none';
            }
            
            var allNotifs = [];
            for(var n = 0; n < notifications.length; n++) {
                allNotifs.push(notifications[n]);
            }
            for(var s = 0; s < serverNotifications.length; s++) {
                allNotifs.push({
                    id: null,
                    message: serverNotifications[s].pesan,
                    type: serverNotifications[s].jenis,
                    created_at: serverNotifications[s].tanggal,
                    is_read: false
                });
            }
            
            if (allNotifs.length === 0) {
                listContainer.innerHTML = '<div class="p-4 text-center text-gray-500 text-xs">Belum ada notifikasi</div>';
                return;
            }
            
            var html = '';
            var maxItems = Math.min(8, allNotifs.length);
            for(var item = 0; item < maxItems; item++) {
                var notif = allNotifs[item];
                var isUnread = !notif.is_read;
                var bgClass = isUnread ? 'bg-blue-50' : '';
                var icon = getNotifIcon(notif.type);
                var color = getNotifColor(notif.type);
                var notifId = notif.id ? notif.id : 0;
                
                html += '<div class="notif-item border-b border-gray-100 ' + bgClass + ' cursor-pointer hover:bg-gray-50 transition" onclick="markNotifAsRead(' + notifId + ')">';
                html += '<div class="flex gap-2 items-start">';
                html += '<span class="notif-icon material-icons-outlined text-sm mt-0.5 ' + color + '">' + icon + '</span>';
                html += '<div class="flex-1 min-w-0">';
                html += '<p class="notif-text text-gray-800 leading-relaxed">' + escapeNotifHtml(notif.message) + '</p>';
                html += '<p class="notif-time text-gray-400 mt-0.5">' + formatNotifDate(notif.created_at) + '</p>';
                if(isUnread) {
                    html += '<span class="text-[9px] text-blue-500 mt-0.5 inline-block font-medium">● Baru</span>';
                }
                html += '</div></div></div>';
            }
            
            if (allNotifs.length > 8) {
                html += '<div class="p-1.5 text-center border-t border-gray-100">';
                html += '<span class="text-[10px] text-gray-400">+ ' + (allNotifs.length - 8) + ' notifikasi lainnya</span>';
                html += '</div>';
            }
            
            listContainer.innerHTML = html;
        }

        function escapeNotifHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function markNotifAsRead(id) {
            if (!id || id === 0) {
                location.reload();
                return;
            }
            
            fetch('/admin/project/notifications/' + id + '/read', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function() {
                location.reload();
            })
            .catch(function(error) {
                console.error('Error marking as read:', error);
            });
        }

        function markAllNotifAsRead() {
            fetch('/admin/project/notifications/mark-all-read', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function() {
                location.reload();
            })
            .catch(function(error) {
                console.error('Error marking all as read:', error);
            });
        }

        function refreshNotifications() {
            location.reload();
        }

        // ============================
        // MODAL FUNCTIONS
        // ============================
        
        function closeAllModals() {
            var tambahModal = document.getElementById('tambahModal');
            var detailModal = document.getElementById('detailModal');
            var editModal = document.getElementById('editModal');
            var deleteModal = document.getElementById('deleteModal');
            
            if(tambahModal) tambahModal.classList.add('hidden');
            if(detailModal) detailModal.classList.add('hidden');
            if(editModal) editModal.classList.add('hidden');
            if(deleteModal) deleteModal.classList.add('hidden');
        }

        function openDetailModal(id) {
            var modal = document.getElementById('detailModal');
            if (!modal) return;
            
            fetch('/admin/project/' + id, {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.data) {
                    var p = data.data;
                    document.getElementById('detailId').textContent = '#' + p.id;
                    document.getElementById('detailNama').textContent = p.nama || '-';
                    document.getElementById('detailDeskripsi').textContent = p.deskripsi || '-';
                    document.getElementById('detailHarga').textContent = p.harga ? 'Rp ' + new Intl.NumberFormat('id-ID').format(p.harga) : '-';
                    document.getElementById('detailProgres').textContent = (p.progres || 0) + '%';
                    document.getElementById('detailInvoice').textContent = p.invoice ? '#' + p.invoice.id : '-';
                    document.getElementById('detailTanggalMulaiPengerjaan').textContent = p.tanggal_mulai_pengerjaan || '-';
                    document.getElementById('detailTanggalSelesaiPengerjaan').textContent = p.tanggal_selesai_pengerjaan || '-';
                    document.getElementById('detailTanggalMulaiKerjasama').textContent = p.tanggal_mulai_kerjasama || '-';
                    document.getElementById('detailTanggalSelesaiKerjasama').textContent = p.tanggal_selesai_kerjasama || '-';
                    
                    var statusP = p.status_pengerjaan || 'pending';
                    var statusK = p.status_kerjasama || 'aktif';
                    document.getElementById('detailStatusPengerjaan').innerHTML = '<span class="status-badge status-' + statusP.replace('_', '-') + '">' + statusP.replace('_', ' ') + '</span>';
                    document.getElementById('detailStatusKerjasama').innerHTML = '<span class="status-badge status-' + statusK + '">' + statusK + '</span>';
                    
                    modal.classList.remove('hidden');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
        }

        function openEditModal(id) {
            var modal = document.getElementById('editModal');
            if (!modal) return;
            
            fetch('/admin/project/' + id, {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.data) {
                    var p = data.data;
                    document.getElementById('editId').value = p.id;
                    document.getElementById('editNama').value = p.nama || '';
                    document.getElementById('editDeskripsi').value = p.deskripsi || '';
                    document.getElementById('editTanggalMulaiPengerjaan').value = p.tanggal_mulai_pengerjaan || '';
                    document.getElementById('editTanggalSelesaiPengerjaan').value = p.tanggal_selesai_pengerjaan || '';
                    document.getElementById('editTanggalMulaiKerjasama').value = p.tanggal_mulai_kerjasama || '';
                    document.getElementById('editTanggalSelesaiKerjasama').value = p.tanggal_selesai_kerjasama || '';
                    document.getElementById('editStatusKerjasama').value = p.status_kerjasama || 'aktif';
                    
                    var editForm = document.getElementById('editForm');
                    editForm.action = '/admin/project/' + id;
                    modal.classList.remove('hidden');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
        }

        function openDeleteModal(id, nama) {
            var modal = document.getElementById('deleteModal');
            if (!modal) return;
            
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteNama').textContent = nama;
            var deleteForm = document.getElementById('deleteForm');
            deleteForm.action = '/admin/project/' + id;
            modal.classList.remove('hidden');
        }

        function showToast(message, type) {
            var toast = document.getElementById('toast');
            var toastMessage = document.getElementById('toastMessage');
            
            if (!toast) return;
            
            toastMessage.textContent = message;
            if(type === 'success') {
                toast.style.backgroundColor = '#10b981';
            } else if(type === 'error') {
                toast.style.backgroundColor = '#ef4444';
            } else {
                toast.style.backgroundColor = '#f59e0b';
            }
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(function() {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        // ============================
        // INVOICE AUTO-FILL
        // ============================
        
        var tambahInvoice = document.getElementById('tambahInvoice');
        if(tambahInvoice) {
            tambahInvoice.addEventListener('change', function() {
                var selected = this.options[this.selectedIndex];
                if (selected.value) {
                    var namaInput = document.getElementById('tambahNama');
                    var deskripsiInput = document.getElementById('tambahDeskripsi');
                    var hargaInput = document.getElementById('tambahHarga');
                    var tanggalMulaiInput = document.getElementById('tambahTanggalMulaiKerjasama');
                    var tanggalSelesaiInput = document.getElementById('tambahTanggalSelesaiKerjasama');
                    
                    if(namaInput) namaInput.value = selected.getAttribute('data-nama') || '';
                    if(deskripsiInput) deskripsiInput.value = selected.getAttribute('data-deskripsi') || '';
                    if(hargaInput) hargaInput.value = selected.getAttribute('data-harga') || '';
                    if(tanggalMulaiInput) tanggalMulaiInput.value = selected.getAttribute('data-tanggal-mulai') || '';
                    if(tanggalSelesaiInput) tanggalSelesaiInput.value = selected.getAttribute('data-tanggal-selesai') || '';
                }
            });
        }
        
        // ============================
        // FORM SUBMISSIONS
        // ============================
        
        var tambahForm = document.getElementById('tambahForm');
        if(tambahForm) {
            tambahForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                
                // Validasi tambahan di sisi client
                var invoiceId = document.getElementById('tambahInvoice').value;
                if (!invoiceId) {
                    notyf.error('Silakan pilih invoice terlebih dahulu');
                    return;
                }
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        notyf.success(data.message || 'Project berhasil ditambahkan');
                        closeAllModals();
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        var errorMsg = data.message || 'Gagal menyimpan';
                        if (data.errors) {
                            var errors = Object.values(data.errors).flat();
                            errorMsg = errors.join('\n');
                        }
                        notyf.error(errorMsg);
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    notyf.error('Terjadi kesalahan pada server');
                });
            });
        }
        
        var editForm = document.getElementById('editForm');
        if(editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var id = document.getElementById('editId').value;
                var formData = new FormData(this);
                
                fetch('/admin/project/' + id, {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        notyf.success(data.message || 'Project berhasil diupdate');
                        closeAllModals();
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        var errorMsg = data.message || 'Gagal update';
                        if (data.errors) {
                            var errors = Object.values(data.errors).flat();
                            errorMsg = errors.join('\n');
                        }
                        notyf.error(errorMsg);
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    notyf.error('Terjadi kesalahan pada server');
                });
            });
        }
        
        var deleteForm = document.getElementById('deleteForm');
        if(deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var id = document.getElementById('deleteId').value;
                
                fetch('/admin/project/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        notyf.success(data.message || 'Project berhasil dihapus');
                        closeAllModals();
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        notyf.error(data.message || 'Gagal hapus');
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    notyf.error('Terjadi kesalahan pada server');
                });
            });
        }
        
        // ============================
        // DOM EVENT LISTENERS
        // ============================
        
        document.addEventListener('DOMContentLoaded', function() {
            // Cek session storage untuk mencegah notifikasi ganda
            try {
                if (sessionStorage.getItem('notifications_shown')) {
                    notificationShown = true;
                }
            } catch(e) {}
            
            // Tampilkan notifikasi hanya sekali saat load
            setTimeout(function() {
                showInitialNotifications();
            }, 1500);
            
            var tambahBtn = document.getElementById('tambahProjectBtn');
            var tambahModal = document.getElementById('tambahModal');
            if(tambahBtn) {
                tambahBtn.addEventListener('click', function() {
                    if(tambahModal) tambahModal.classList.remove('hidden');
                    var tambahFormEl = document.getElementById('tambahForm');
                    if(tambahFormEl) tambahFormEl.reset();
                    var progresValueSpan = document.getElementById('tambahProgresValue');
                    if(progresValueSpan) progresValueSpan.textContent = '0%';
                });
            }
            
            var progresSlider = document.getElementById('tambahProgres');
            var progresValue = document.getElementById('tambahProgresValue');
            if(progresSlider) {
                progresSlider.addEventListener('input', function() {
                    if(progresValue) progresValue.textContent = this.value + '%';
                });
            }
            
            var closeButtons = document.querySelectorAll('.close-modal');
            for(var c = 0; c < closeButtons.length; c++) {
                closeButtons[c].addEventListener('click', closeAllModals);
            }
            
            window.addEventListener('click', function(event) {
                if (event.target.classList && event.target.classList.contains('modal')) {
                    event.target.classList.add('hidden');
                }
            });
            
            var bell = document.getElementById('notificationBell');
            var dropdown = document.getElementById('notifDropdown');
            
            if(bell) {
                bell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if(dropdown) dropdown.classList.toggle('hidden');
                    fetchProjectNotifications();
                });
            }
            
            document.addEventListener('click', function(e) {
                if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Update notifikasi secara berkala (tanpa menampilkan toast)
            setInterval(function() {
                fetchProjectNotifications();
            }, 60000);
        });
        
        window.openDetailModal = openDetailModal;
        window.openEditModal = openEditModal;
        window.openDeleteModal = openDeleteModal;
        window.closeAllModals = closeAllModals;
        window.markNotifAsRead = markNotifAsRead;
        window.markAllNotifAsRead = markAllNotifAsRead;
        window.refreshNotifications = refreshNotifications;
    </script>
</body>

</html>