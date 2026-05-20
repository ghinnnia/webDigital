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
                $tglSelesai = Carbon::parse($item->tanggal_selesai_pengerjaan);
                // 🔥 PAKAI diffInDays dengan floor (bulatkan ke bawah)
                $sisaHari = floor($hariIni->diffInDays($tglSelesai, false));

                // Jika H-3 sampai hari-H dan belum lewat
                if ($sisaHari >= 0 && $sisaHari <= 3) {
                    $pesan = ($sisaHari == 0) 
                        ? "Project '{$item->nama}' deadline HARI INI untuk pengerjaan!" 
                        : "Project '{$item->nama}' tersisa {$sisaHari} hari menuju deadline pengerjaan!";
                    
                    $notifProyek[] = [
                        'pesan' => $pesan,
                        'jenis' => 'pengerjaan',
                        'tanggal' => $tglSelesai->format('d-m-Y'),
                        'is_read' => false
                    ];
                }
            }
            
            // Cek Periode Kerjasama
            if ($item->tanggal_selesai_kerjasama) {
                $tglSelesaiKerjasama = Carbon::parse($item->tanggal_selesai_kerjasama);
                // 🔥 PAKAI diffInDays dengan floor (bulatkan ke bawah)
                $sisaHariKerjasama = floor($hariIni->diffInDays($tglSelesaiKerjasama, false));
                
                if ($sisaHariKerjasama >= 0 && $sisaHariKerjasama <= 3) {
                    $pesan = ($sisaHariKerjasama == 0)
                        ? "Project '{$item->nama}' deadline HARI INI untuk kerjasama!"
                        : "Project '{$item->nama}' tersisa {$sisaHariKerjasama} hari menuju deadline kerjasama!";
                    
                    $notifProyek[] = [
                        'pesan' => $pesan,
                        'jenis' => 'kerjasama',
                        'tanggal' => $tglSelesaiKerjasama->format('d-m-Y'),
                        'is_read' => false
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
    <script>
        // ============================
// POP UP NOTIFICATION (Toast)
// ============================

let lastNotifCount = 0;
let popupShownForIds = [];

function showPopupNotification(message, type = 'warning') {
    // Buat elemen popup
    const popup = document.createElement('div');
    popup.className = 'fixed bottom-20 right-4 bg-white rounded-lg shadow-xl border-l-4 border-orange-500 p-4 z-50 transform transition-all duration-300 translate-x-full opacity-0';
    popup.style.minWidth = '300px';
    popup.style.maxWidth = '400px';
    
    const icon = type === 'warning' ? '<i class="fa-solid fa-triangle-exclamation text-orange-500"></i>' : (type === 'danger' ? '<i class="fa-solid fa-circle-exclamation text-red-500"></i>' : '<i class="fa-solid fa-bell text-blue-500"></i>');
    const bgColor = type === 'warning' ? 'border-orange-500' : (type === 'danger' ? 'border-red-500' : 'border-blue-500');
    
    popup.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="text-xl">${icon}</div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800">Notifikasi Deadline</p>
                <p class="text-sm text-gray-600 mt-1">${message}</p>
                <p class="text-xs text-gray-400 mt-2">${new Date().toLocaleTimeString('id-ID')}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons-outlined text-sm">close</span>
            </button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Animasi masuk
    setTimeout(() => {
        popup.classList.remove('translate-x-full', 'opacity-0');
        popup.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    // Auto hilang setelah 8 detik
    setTimeout(() => {
        popup.classList.remove('translate-x-0', 'opacity-100');
        popup.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (popup.parentNode) popup.remove();
        }, 300);
    }, 8000);
}

function checkForNewNotifications(notifications) {
    // Hitung notifikasi yang belum dibaca dan belum muncul popupnya
    const newNotifs = notifications.filter(n => !n.is_read && !popupShownForIds.includes(n.id));
    
    if (newNotifs.length > 0) {
        // Tampilkan popup untuk setiap notifikasi baru
        newNotifs.forEach(notif => {
            const icon = notif.type === 'pengerjaan' ? '<i class="fa-solid fa-triangle-exclamation text-orange-500"></i>' : '<i class="fa-solid fa-bell text-blue-500"></i>';
            const title = notif.type === 'pengerjaan' ? 'Deadline Pengerjaan' : 'Deadline Kerjasama';
            showPopupNotification(`${icon} ${title}: ${notif.message}`, 'warning');
            popupShownForIds.push(notif.id);
        });
        
        // Mainkan suara notifikasi (opsional)
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(e => console.log('Audio not supported'));
        } catch(e) {}
        
        // Tampilkan toast browser jika diizinkan
        if (Notification.permission === 'granted') {
            newNotifs.forEach(notif => {
                new Notification('Notifikasi Deadline Project', {
                    body: notif.message,
                    icon: '/favicon.ico'
                });
            });
        }
    }
    
    lastNotifCount = notifications.filter(n => !n.is_read).length;
}

// Minta izin notifikasi browser
if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
    Notification.requestPermission();
}

// Update fungsi fetchProjectNotifications
const originalFetchNotif = fetchProjectNotifications;
fetchProjectNotifications = function() {
    originalFetchNotif();
    // Tambahan: cek notifikasi baru setiap 30 detik
    setTimeout(() => {
        originalFetchNotif();
    }, 30000);
};

// Update fungsi updateNotifUI untuk mengecek notifikasi baru
const originalUpdateUI = updateNotifUI;
updateNotifUI = function(notifications) {
    originalUpdateUI(notifications);
    checkForNewNotifications(notifications);
};
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
                                <span class="material-icons-outlined text-gray-600">notifications_none</span>
                                <span id="notifBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 hidden items-center justify-center">
                                    {{ $totalNotif > 0 ? ($totalNotif > 99 ? '99+' : $totalNotif) : 0 }}
                                </span>
                            </button>
                            
                            <div id="notifDropdown" class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                                    <button onclick="markAllNotifAsRead()" class="text-xs text-blue-600 hover:text-blue-800">Tandai semua dibaca</button>
                                </div>
                                <div id="notifList" class="max-h-96 overflow-y-auto">
                                    @if(count($semuaNotifikasi) > 0)
                                        @foreach($semuaNotifikasi as $notif)
                                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50 transition notification-item {{ !$notif['is_read'] ? 'bg-blue-50' : '' }}" 
                                             onclick="markNotifAsRead({{ $notif['id'] ?? 0 }})">
                                            <div class="flex gap-2">
                                                <span class="material-icons-outlined text-{{ $notif['jenis'] == 'pengerjaan' ? 'orange' : 'green' }}-500">
                                                    {{ $notif['jenis'] == 'pengerjaan' ? 'construction' : 'handshake' }}
                                                </span>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-800">{{ $notif['pesan'] }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">{{ $notif['tanggal'] }}</p>
                                                    @if(!$notif['is_read'])
                                                        <span class="text-xs text-blue-500 mt-1 inline-block">● Baru</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="p-4 text-center text-gray-500">Belum ada notifikasi</div>
                                    @endif
                                </div>
                                <div class="p-2 border-t border-gray-200 text-center">
                                    <button onclick="refreshNotifications()" class="text-xs text-blue-600 hover:text-blue-800">Refresh</button>
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
                                                    @php
                                                        $startPengerjaan = $item->tanggal_mulai_pengerjaan ? $item->tanggal_mulai_pengerjaan->format('Y-m-d') : null;
                                                        $endPengerjaan = $item->tanggal_selesai_pengerjaan ? $item->tanggal_selesai_pengerjaan->format('Y-m-d') : null;
                                                    @endphp
                                                    @if ($startPengerjaan && $endPengerjaan)
                                                        {{ $startPengerjaan }} &mdash; {{ $endPengerjaan }}
                                                    @elseif ($startPengerjaan)
                                                        {{ $startPengerjaan }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 200px;">
                                                    @php
                                                        $startKerjasama = $item->tanggal_mulai_kerjasama ? $item->tanggal_mulai_kerjasama->format('Y-m-d') : null;
                                                        $endKerjasama = $item->tanggal_selesai_kerjasama ? $item->tanggal_selesai_kerjasama->format('Y-m-d') : null;
                                                    @endphp
                                                    @if ($startKerjasama && $endKerjasama)
                                                        {{ $startKerjasama }} &mdash; {{ $endKerjasama }}
                                                    @elseif ($startKerjasama)
                                                        {{ $startKerjasama }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 120px;">
                                                    <span class="status-badge status-{{ str_replace('_', '-', $item->status_pengerjaan) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $item->status_pengerjaan)) }}
                                                    </span>
                                                </td>
                                                <td style="min-width: 120px;">
                                                    @php
                                                        $tglSelesaiKerjasama = $item->tanggal_selesai_kerjasama ? \Carbon\Carbon::parse($item->tanggal_selesai_kerjasama) : null;
                                                        $isCloseToDeadline = $tglSelesaiKerjasama && now()->diffInDays($tglSelesaiKerjasama, false) <= 3 && now()->diffInDays($tglSelesaiKerjasama, false) >= 0;
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
                                    <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-base">{{ $item->nama }}</h4>
                                                <p class="text-sm text-text-muted-light">
                                                    @if ($item->invoice)
                                                        Invoice #{{ $item->invoice->id }}<br>
                                                    @endif
                                                    Mulai: {{ optional($item->tanggal_mulai_pengerjaan)->format('Y-m-d') ?? '-' }}
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
        // NOTIFICATION FUNCTIONS
        // ============================
        
        function formatNotifDate(dateString) {
            let date = new Date(dateString);
            return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        function getNotifIcon(type) {
            return type === 'pengerjaan' ? 'construction' : 'handshake';
        }

        function getNotifColor(type) {
            return type === 'pengerjaan' ? 'text-orange-500' : 'text-green-500';
        }

        function fetchProjectNotifications() {
            fetch('/admin/project/notifications', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    updateNotifUI(data.data);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
        }

        function updateNotifUI(notifications) {
            const unreadCount = notifications.filter(n => !n.is_read).length;
            const badge = document.getElementById('notifBadge');
            const listContainer = document.getElementById('notifList');
            
            if (unreadCount > 0) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
                badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
            
            if (notifications.length === 0) {
                listContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Belum ada notifikasi</div>';
                return;
            }
            
            let html = '';
            notifications.slice(0, 10).forEach(notif => {
                const isUnread = !notif.is_read;
                const bgClass = isUnread ? 'bg-blue-50' : '';
                const icon = getNotifIcon(notif.type);
                const color = getNotifColor(notif.type);
                
                html += `
                    <div class="p-3 border-b border-gray-100 ${bgClass} cursor-pointer hover:bg-gray-50 transition notification-item" onclick="markNotifAsRead(${notif.id})">
                        <div class="flex gap-2">
                            <span class="material-icons-outlined ${color}">${icon}</span>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">${escapeNotifHtml(notif.message)}</p>
                                <p class="text-xs text-gray-400 mt-1">${formatNotifDate(notif.created_at)}</p>
                                ${isUnread ? '<span class="text-xs text-blue-500 mt-1 inline-block">● Baru</span>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            if (notifications.length > 10) {
                html += `<div class="p-2 text-center border-t border-gray-100">
                            <span class="text-xs text-gray-400">+ ${notifications.length - 10} notifikasi lainnya</span>
                        </div>`;
            }
            
            listContainer.innerHTML = html;
        }

        function escapeNotifHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function markNotifAsRead(id) {
            if (!id || id === 0) {
                // Jika id null atau 0 (notifikasi manual), refresh saja
                location.reload();
                return;
            }
            
            fetch(`/admin/project/notifications/${id}/read`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(() => {
                location.reload();
            })
            .catch(error => console.error('Error marking as read:', error));
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
            .then(response => response.json())
            .then(() => {
                location.reload();
            })
            .catch(error => console.error('Error marking all as read:', error));
        }

        function refreshNotifications() {
            location.reload();
        }

        // ============================
        // MODAL FUNCTIONS
        // ============================
        
        function closeAllModals() {
            document.getElementById('tambahModal')?.classList.add('hidden');
            document.getElementById('detailModal')?.classList.add('hidden');
            document.getElementById('editModal')?.classList.add('hidden');
            document.getElementById('deleteModal')?.classList.add('hidden');
        }

        function openDetailModal(id) {
            const modal = document.getElementById('detailModal');
            if (!modal) return;
            
            fetch(`/admin/project/${id}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const p = data.data;
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
                    
                    const statusP = p.status_pengerjaan || 'pending';
                    const statusK = p.status_kerjasama || 'aktif';
                    document.getElementById('detailStatusPengerjaan').innerHTML = `<span class="status-badge status-${statusP.replace('_', '-')}">${statusP.replace('_', ' ')}</span>`;
                    document.getElementById('detailStatusKerjasama').innerHTML = `<span class="status-badge status-${statusK}">${statusK}</span>`;
                    
                    modal.classList.remove('hidden');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function openEditModal(id) {
            const modal = document.getElementById('editModal');
            if (!modal) return;
            
            fetch(`/admin/project/${id}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const p = data.data;
                    document.getElementById('editId').value = p.id;
                    document.getElementById('editNama').value = p.nama || '';
                    document.getElementById('editDeskripsi').value = p.deskripsi || '';
                    document.getElementById('editTanggalMulaiPengerjaan').value = p.tanggal_mulai_pengerjaan || '';
                    document.getElementById('editTanggalSelesaiPengerjaan').value = p.tanggal_selesai_pengerjaan || '';
                    document.getElementById('editTanggalMulaiKerjasama').value = p.tanggal_mulai_kerjasama || '';
                    document.getElementById('editTanggalSelesaiKerjasama').value = p.tanggal_selesai_kerjasama || '';
                    document.getElementById('editStatusKerjasama').value = p.status_kerjasama || 'aktif';
                    
                    const editForm = document.getElementById('editForm');
                    editForm.action = `/admin/project/${id}`;
                    modal.classList.remove('hidden');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function openDeleteModal(id, nama) {
            const modal = document.getElementById('deleteModal');
            if (!modal) return;
            
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteNama').textContent = nama;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/admin/project/${id}`;
            modal.classList.remove('hidden');
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            
            if (!toast) return;
            
            toastMessage.textContent = message;
            toast.style.backgroundColor = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#f59e0b');
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        // ============================
        // INVOICE AUTO-FILL
        // ============================
        
        document.getElementById('tambahInvoice')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('tambahNama').value = selected.getAttribute('data-nama') || '';
                document.getElementById('tambahDeskripsi').value = selected.getAttribute('data-deskripsi') || '';
                document.getElementById('tambahHarga').value = selected.getAttribute('data-harga') || '';
                document.getElementById('tambahTanggalMulaiKerjasama').value = selected.getAttribute('data-tanggal-mulai') || '';
                document.getElementById('tambahTanggalSelesaiKerjasama').value = selected.getAttribute('data-tanggal-selesai') || '';
            }
        });
        
        // ============================
        // FORM SUBMISSIONS
        // ============================
        
        document.getElementById('tambahForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeAllModals();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Gagal menyimpan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            });
        });
        
        document.getElementById('editForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editId').value;
            const formData = new FormData(this);
            
            fetch(`/admin/project/${id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeAllModals();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Gagal update', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            });
        });
        
        document.getElementById('deleteForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('deleteId').value;
            
            fetch(`/admin/project/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeAllModals();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Gagal hapus', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            });
        });
        
        // ============================
        // DOM EVENT LISTENERS
        // ============================
        
        document.addEventListener('DOMContentLoaded', function() {
            // Tambah Project button
            const tambahBtn = document.getElementById('tambahProjectBtn');
            const tambahModal = document.getElementById('tambahModal');
            tambahBtn?.addEventListener('click', () => {
                tambahModal.classList.remove('hidden');
                document.getElementById('tambahForm')?.reset();
                document.getElementById('tambahProgresValue').textContent = '0%';
            });
            
            // Progres slider
            const progresSlider = document.getElementById('tambahProgres');
            const progresValue = document.getElementById('tambahProgresValue');
            progresSlider?.addEventListener('input', function() {
                if (progresValue) progresValue.textContent = this.value + '%';
            });
            
            // Close modals
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', closeAllModals);
            });
            
            // Click outside modal
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.classList.add('hidden');
                }
            });
            
            // Notification bell
            const bell = document.getElementById('notificationBell');
            const dropdown = document.getElementById('notifDropdown');
            
            bell?.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
                fetchProjectNotifications();
            });
            
            // Close notification dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Initial fetch notifications
            fetchProjectNotifications();
            
            // Refresh notifications every 60 seconds
            setInterval(fetchProjectNotifications, 60000);
        });
        
        // Make functions global
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