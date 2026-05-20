<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Component</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FIX ICON (PAKAI SATU JENIS SAJA) -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar-fixed {
            position: fixed;
            top: 0;
            width: 256px;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
        }

        .main-content {
            margin-left: 256px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            transition: 0.2s;
            text-decoration: none;
        }

        .nav-item:hover {
            background: #f3f4f6;
        }

        .nav-item.active {
            background: #e5e7eb;
            font-weight: 600;
        }

        .sidebar-icon {
            font-size: 20px;
            min-width: 20px;
        }

        .sidebar-text {
            white-space: nowrap;
            font-size: 14px;
            color: #374151;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background: red;
            color: white;
            font-size: 10px;
            border-radius: 9999px;
            padding: 2px 6px;
            min-width: 18px;
            text-align: center;
        }

        .notification-dropdown {
            position: absolute;
            top: 40px;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: none;
            z-index: 100;
        }

        .notification-dropdown.show {
            display: block;
        }

        /* Scrollbar untuk dropdown */
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-100">

<!-- SIDEBAR -->
<aside class="sidebar-fixed bg-white shadow-lg">

    <!-- HEADER -->
    <div class="flex items-center justify-between p-4 border-b">
        <img src="{{ asset('images/logo_inovindo.jpg') }}" class="h-10" alt="Logo">

        <!-- NOTIF -->
        <div class="notification-bell" id="notificationBell">
            <span class="material-icons-outlined">notifications</span>

            @php
                $unreadCount = App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
            @endphp

            @if($unreadCount > 0)
                <span class="notification-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </div>

        <!-- DROPDOWN -->
        <div id="notificationDropdown" class="notification-dropdown">
            <div class="p-3 border-b font-semibold text-gray-800">Notifikasi</div>
            <div id="notificationList" class="notification-list p-3 text-sm text-gray-500">
                Memuat...
            </div>
            <div class="p-2 border-t text-center">
                <a href="{{ route('notifications.index') }}" class="text-blue-500 text-sm hover:underline">Lihat Semua</a>
            </div>
        </div>
    </div>

    <!-- MENU -->
    <nav class="p-4 space-y-2">

        <a href="{{ route('hr.home') }}" class="nav-item {{ request()->routeIs('hr.home') ? 'active' : '' }}">
            <span class="material-icons-outlined sidebar-icon">home</span>
            <span class="sidebar-text">Beranda</span>
        </a>

        <a href="{{ route('hr.data_karyawan') }}" class="nav-item {{ request()->routeIs('hr.data_karyawan*') ? 'active' : '' }}">
            <span class="material-icons-outlined sidebar-icon">group</span>
            <span class="sidebar-text">Data Karyawan</span>
        </a>

        <a href="{{ route('hr.kelola_absensi') }}" class="nav-item {{ request()->routeIs('hr.kelola_absensi*') ? 'active' : '' }}">
            <span class="material-icons-outlined sidebar-icon">event_available</span>
            <span class="sidebar-text">Kelola Absensi</span>
        </a>

        <a href="{{ route('hr.tasks.index') }}" class="nav-item {{ request()->routeIs('hr.tasks*') ? 'active' : '' }}">
            <span class="material-icons-outlined sidebar-icon">assignment</span>
            <span class="sidebar-text">Monitoring Tugas</span>
        </a>

        <a href="{{ route('hr.kpa.index') }}" class="nav-item {{ request()->routeIs('hr.kpa*') ? 'active' : '' }}">
            <span class="material-icons-outlined sidebar-icon">assessment</span>
            <span class="sidebar-text">Kinerja Pegawai (KPA)</span>
        </a>

        <a href="{{ route('hr.tunjangan.index') }}" class="nav-item {{ request()->routeIs('hr.tunjangan*') ? 'active' : '' }}">
            <span class="material-icons-outlined sidebar-icon">card_giftcard</span>
            <span class="sidebar-text">Tunjangan Pegawai</span>
        </a>

        <a href="{{ route('hr.gaji.index') }}" class="nav-item {{ request()->routeIs('hr.gaji*') ? 'active' : '' }}">
            <span class="material-icons-outlined sidebar-icon">payments</span>
            <span class="sidebar-text">Gaji Pegawai</span>
        </a>

        <!--PENGUMUMAN -->
      <a href="{{ route('pengumuman.index') }}" class="nav-item {{ request()->routeIs('pengumuman*') ? 'active' : '' }}">
    <span class="material-icons-outlined sidebar-icon">campaign</span>
    <span class="sidebar-text">Pengumuman</span>
</a>
<!-- Surat Sakit -->
<a href="{{ route('hr.absensi.surat-sakit') }}" 
   class="nav-item {{ request()->routeIs('hr.absensi.surat-sakit*') ? 'active' : '' }}">
    <div class="w-8 h-8 flex items-center justify-center">
        <span class="material-icons-outlined text-xl">medication</span>
    </div>
    <span class="text-sm font-medium">Surat Sakit</span>
</a>

    </nav>

    <!-- LOGOUT -->
    <div class="p-4 border-t">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item w-full text-left">
                <span class="material-icons-outlined sidebar-icon">logout</span>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>

</aside>

<script>
const bell = document.getElementById('notificationBell');
const dropdown = document.getElementById('notificationDropdown');

if (bell) {
    bell.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        loadNotifications();
    });
}

document.addEventListener('click', () => {
    if (dropdown) dropdown.classList.remove('show');
});

function loadNotifications() {
    fetch('/api/notifications')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('notificationList');
            if (!list) return;

            if (data.notifications && data.notifications.length > 0) {
                list.innerHTML = data.notifications.slice(0, 5).map(n => `
                    <div class="border-b py-2 last:border-0">
                        <p class="font-semibold text-gray-800 text-sm">${escapeHtml(n.title)}</p>
                        <p class="text-xs text-gray-500 mt-1">${escapeHtml(n.message)}</p>
                        <p class="text-xs text-gray-400 mt-1">${new Date(n.created_at).toLocaleDateString('id-ID')}</p>
                    </div>
                `).join('');
                if (data.notifications.length > 5) {
                    list.innerHTML += `<div class="pt-2 text-center"><span class="text-xs text-gray-400">+ ${data.notifications.length - 5} notifikasi lainnya</span></div>`;
                }
            } else {
                list.innerHTML = '<div class="text-center py-4 text-gray-500">Tidak ada notifikasi</div>';
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            const list = document.getElementById('notificationList');
            if (list) list.innerHTML = '<div class="text-center py-4 text-gray-500">Gagal memuat notifikasi</div>';
        });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Aktifkan menu berdasarkan URL saat ini
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath === href) {
            item.classList.add('active');
        }
    });
});
</script>

</body>
</html>