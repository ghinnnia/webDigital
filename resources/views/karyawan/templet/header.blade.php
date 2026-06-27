{{-- karyawan/templet/header.blade.php --}}
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@php
    $currentPage = request()->path();
    $authUser = Auth::user();
    $profilePhotoUrl = !empty($authUser?->foto) ? asset('storage/' . $authUser->foto) : null;
@endphp

<style>
    /* CSS untuk membuat header fiks dan meningkatkan tampilan */
    .fixed-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 50;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Tambahkan padding ke body agar konten tidak tertutup header */
    body {
        padding-top: 80px;
    }

    @media (max-width: 767px) {
        body {
            padding-bottom: 80px;
        }
    }

    /* Mode gelap untuk header */
    .dark .fixed-header {
        background-color: rgba(31, 41, 55, 0.95);
        border-bottom: 1px solid rgba(75, 85, 99, 0.5);
    }

    /* Style untuk Brand */
    .brand-link {
        color: #1e293b;
        font-weight: 700;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        position: relative;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }

    .brand-link:hover {
        color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }

    .brand-link:active {
        transform: translateY(0);
        background-color: rgba(59, 130, 246, 0.2);
    }

    .brand-link.active {
        color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }

    .brand-link.active:hover {
        background-color: rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }

    .dark .brand-link {
        color: #f9fafb;
    }

    .dark .brand-link:hover {
        color: #60a5fa;
        background-color: rgba(96, 165, 250, 0.1);
    }

    .dark .brand-link.active {
        color: #60a5fa;
        background-color: rgba(96, 165, 250, 0.1);
        transform: translateY(-1px);
    }

    /* Efek hover untuk link navigasi */
    .nav-link {
        position: relative;
        transition: all 0.2s ease;
        color: #64748b;
        cursor: pointer;
    }

    .nav-link .nav-indicator {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        background-color: #3b82f6;
        width: 0;
        transition: width 0.3s ease;
    }

    .nav-link:hover {
        color: #3b82f6 !important;
    }

    .nav-link:hover .nav-indicator {
        width: 100% !important;
    }

    .nav-link.active {
        color: #3b82f6 !important;
        font-weight: 600;
    }

    .nav-link.active .nav-indicator {
        width: 100% !important;
    }

    .dark .nav-link {
        color: #d1d5db;
    }

    .dark .nav-link:hover {
        color: #60a5fa !important;
    }

    .dark .nav-link.active {
        color: #60a5fa !important;
    }

    .dark .nav-link .nav-indicator,
    .dark .nav-link:hover .nav-indicator,
    .dark .nav-link.active .nav-indicator {
        background-color: #60a5fa !important;
    }

    /* Profile Link */
    .profile-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        color: #1e293b;
    }

    .profile-link:hover {
        background-color: #f1f5f9;
    }

    .dark .profile-link {
        color: #d1d5db;
    }

    .dark .profile-link:hover {
        background-color: #1e293b;
    }

    /* Logout Button */
    .logout-button {
        background-color: #f1f5f9;
        color: #1e293b;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 600;
        transition: all 0.3s ease;
        transform: scale(1);
        border: none;
        cursor: pointer;
    }

    .logout-button:hover {
        background-color: #e2e8f0;
        transform: scale(1.05);
    }

    .dark .logout-button {
        background-color: #1e293b;
        color: #f9fafb;
    }

    .dark .logout-button:hover {
        background-color: #374151;
    }

    /* Notification Dropdown */
    .notification-dropdown {
        position: absolute;
        right: 0;
        top: 40px;
        width: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        z-index: 100;
        display: none;
    }
    .notification-dropdown.show {
        display: block;
    }
    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
    }
    .notification-item:hover {
        background: #f9fafb;
    }
    .notification-item.unread {
        background: #eff6ff;
    }
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: bold;
        min-width: 18px;
        height: 18px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
    }

    /* Mobile Menu Animation */
    .mobile-menu-enter {
        animation: slideDown 0.3s ease forwards;
    }

    .mobile-menu-exit {
        animation: slideUp 0.3s ease forwards;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
        }
        to {
            opacity: 1;
            max-height: 500px;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 1;
            max-height: 500px;
        }
        to {
            opacity: 0;
            max-height: 0;
        }
    }
</style>

<header class="fixed-header w-full">
    <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-4 sm:px-6 lg:px-8">
        <!-- Brand -->
        <div class="flex items-center">
            <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Inovindo Logo" class="h-10 w-auto object-contain">
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-4 sm:gap-8 font-medium">
            <a class="nav-link {{ $currentPage === 'karyawan/home' ? 'active' : '' }} px-1 py-2" href="{{ route('karyawan.home') }}">
                Beranda
                <span class="nav-indicator"></span>
            </a>
            <a class="nav-link {{ strpos($currentPage, 'absensi') !== false ? 'active' : '' }} px-1 py-2"
                href="{{ route('absensi.redirect') }}">
                Presensi
                <span class="nav-indicator"></span>
            </a>
            <a class="nav-link {{ strpos($currentPage, 'karyawan/tugas') !== false ? 'active' : '' }} px-1 py-2"
                href="{{ route('karyawan.tugas.index') }}">
                Manage Tugas
                <span class="nav-indicator"></span>
            </a>
            <a class="nav-link {{ strpos($currentPage, 'karyawan/cuti') !== false ? 'active' : '' }} px-1 py-2"
                href="{{ route('karyawan.cuti.index') }}">
                Pengajuan Cuti
                <span class="nav-indicator"></span>
            </a>
            <!-- Menu Pengajuan Lembur -->
            <a class="nav-link {{ strpos($currentPage, 'karyawan/lembur') !== false ? 'active' : '' }} px-1 py-2"
                href="{{ route('karyawan.lembur.index') }}">
                Pengajuan Lembur
                <span class="nav-indicator"></span>
            </a>
        </nav>

        <!-- Desktop Right Side Controls -->
        <div class="hidden md:flex items-center gap-3">
            <!-- Notification Bell -->
            <div class="relative">
                <button id="notificationBell" class="relative p-2 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                            a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                            C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595
                            1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @php
                        $unreadCount = App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span id="notificationBadge" class="notification-badge">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <div id="notificationDropdown" class="notification-dropdown">
                    <div class="p-3 border-b">
                        <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                    </div>
                    <div id="notificationList" class="max-h-80 overflow-y-auto">
                        <div class="p-4 text-center text-gray-400">Memuat...</div>
                    </div>
                    <div class="p-2 border-t">
                        <a href="{{ route('notifications.index') }}" class="text-blue-600 text-sm block text-center">Lihat Semua</a>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <a href="{{ route('karyawan.profile') }}" class="profile-link">
                @if($profilePhotoUrl)
                    <img src="{{ $profilePhotoUrl }}" alt="Foto Profil" class="h-7 w-7 rounded-full object-cover border border-gray-200">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A9 9 0 1118.88 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                @endif
                <span class="hidden sm:block text-sm font-medium">
                    {{ $authUser?->name ?? 'Profile' }}
                </span>
            </a>

            <!-- Dark Mode Toggle -->
            <button id="dark-mode-toggle"
                class="p-2 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-colors duration-300">
                <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <!-- Logout Button -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-button">
                    Logout
                </button>
            </form>
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden flex items-center gap-3">
            <button id="mobile-dark-mode-toggle"
                class="p-2 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none">
                <svg id="mobile-sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg id="mobile-moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <button onclick="toggleMobileMenu()" class="p-2 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <nav id="mobile-menu" class="md:hidden hidden">
        <div class="flex flex-col space-y-3 px-4 pb-4">
            <a class="mobile-nav-link block px-3 py-2 rounded-md transition-all duration-300 {{ $currentPage === 'karyawan/home' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800' }}"
                href="{{ route('karyawan.home') }}">
                Beranda
            </a>
            <a class="mobile-nav-link block px-3 py-2 rounded-md transition-all duration-300 {{ strpos($currentPage, 'absensi') !== false ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800' }}"
                href="{{ route('absensi.redirect') }}">
                Presensi
            </a>
            <a class="mobile-nav-link block px-3 py-2 rounded-md transition-all duration-300 {{ strpos($currentPage, 'karyawan/tugas') !== false ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800' }}"
                href="{{ route('karyawan.tugas.index') }}">
                Manage Tugas
            </a>
            <a class="mobile-nav-link block px-3 py-2 rounded-md transition-all duration-300 {{ strpos($currentPage, 'karyawan/cuti') !== false ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800' }}"
                href="{{ route('karyawan.cuti.index') }}">
                Pengajuan Cuti
            </a>
            <!-- Menu Pengajuan Lembur Mobile -->
            <a class="mobile-nav-link block px-3 py-2 rounded-md transition-all duration-300 {{ strpos($currentPage, 'karyawan/lembur') !== false ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800' }}"
                href="{{ route('karyawan.lembur.index') }}">
                Pengajuan Lembur
            </a>
            <a href="{{ route('karyawan.profile') }}"
                class="mobile-nav-link block px-3 py-2 rounded-md transition-all duration-300 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800">
                Profil Saya
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full text-left px-3 py-2 rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800 transition-all duration-300">
                    Logout
                </button>
            </form>
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi tema dari localStorage
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
        }

        // Fungsi toggle tema
        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');

            // Update ikon
            document.getElementById('sun-icon').style.display = isDark ? 'block' : 'none';
            document.getElementById('moon-icon').style.display = isDark ? 'none' : 'block';
            document.getElementById('mobile-sun-icon').style.display = isDark ? 'block' : 'none';
            document.getElementById('mobile-moon-icon').style.display = isDark ? 'none' : 'block';
        }

        // Event listener untuk tombol toggle
        document.getElementById('dark-mode-toggle').addEventListener('click', toggleTheme);
        document.getElementById('mobile-dark-mode-toggle').addEventListener('click', toggleTheme);

        // Set ikon awal
        const isDark = document.documentElement.classList.contains('dark');
        document.getElementById('sun-icon').style.display = isDark ? 'block' : 'none';
        document.getElementById('moon-icon').style.display = isDark ? 'none' : 'block';
        document.getElementById('mobile-sun-icon').style.display = isDark ? 'block' : 'none';
        document.getElementById('mobile-moon-icon').style.display = isDark ? 'none' : 'block';

        // Notification Bell
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        if (notificationBell) {
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
                loadNotifications();
            });
        }
        
        document.addEventListener('click', function() {
            notificationDropdown.classList.remove('show');
        });
        
   // Load notifications function
function loadNotifications() {
    fetch('/api/notifications')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('notificationList');
            const bell = document.getElementById('notificationBell');
            const badge = document.getElementById('notificationBadge');
            
            if (data.notifications && data.notifications.length > 0) {
                container.innerHTML = data.notifications.map(n => {
                    let icon = '';
                    switch(n.type) {
                        case 'payroll': icon = '💰'; break;
                        case 'task_revision': icon = '🔄'; break;
                        case 'deadline_warning': icon = '⚠️'; break;
                        default: icon = '📋';
                    }
                    
                    return `
                        <div class="notification-item ${!n.is_read ? 'unread' : ''}" onclick="markNotificationRead(${n.id}, '${n.link}')">
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 text-xl">${icon}</div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">${n.title}</p>
                                    <p class="text-xs text-gray-500">${n.message}</p>
                                    <p class="text-xs text-gray-400 mt-1">${n.time_ago}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                if (data.unread_count > 0) {
                    if (badge) {
                        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                        badge.style.display = 'flex';
                    }
                } else if (badge) {
                    badge.style.display = 'none';
                }
            } else {
                container.innerHTML = '<div class="p-4 text-center text-gray-400">Tidak ada notifikasi</div>';
            }
        })
        .catch(err => console.error('Error loading notifications:', err));
}

// Mark notification as read
function markNotificationRead(id, link) {
    fetch(`/api/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && link) {
            window.location.href = link;
        } else {
            loadNotifications();
            const badge = document.getElementById('notificationBadge');
            if (badge) badge.style.display = 'none';
        }
    })
    .catch(err => console.error('Error:', err));
}
// Fungsi untuk menandai notifikasi sudah dibaca
function markNotificationRead(id, link) {
    fetch(`/api/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && link) {
            window.location.href = link;
        } else {
            loadNotifications();
        }
    })
    .catch(err => console.error('Error:', err));
}

// Update unread count periodically
setInterval(() => {
    fetch('/api/notifications')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (data.unread_count > 0) {
                if (badge) {
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.style.display = 'flex';
                }
            } else if (badge) {
                badge.style.display = 'none';
            }
        })
        .catch(err => console.error('Error:', err));
}, 30000);

        // Mobile menu auto-close on link click
        document.querySelectorAll('#mobile-menu a, #mobile-menu button').forEach(link => {
            link.addEventListener('click', () => toggleMobileMenu());
        });
    });

    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
            mobileMenu.classList.add('mobile-menu-enter');
        } else {
            mobileMenu.classList.remove('mobile-menu-enter');
            mobileMenu.classList.add('mobile-menu-exit');
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('mobile-menu-exit');
            }, 300);
        }
    }
</script>

<!-- Bottom Navigation Bar for Mobile (DANA/GoPay style) -->
<div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 md:hidden shadow-lg">
    <div class="flex justify-around items-center h-16">
        <!-- Home -->
        <a href="{{ route('karyawan.home') }}" class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-primary transition-colors {{ $currentPage === 'karyawan/home' ? 'text-primary dark:text-blue-400' : '' }}">
            <span class="material-symbols-outlined text-2xl">home</span>
            <span class="text-xs mt-1">Beranda</span>
        </a>
        
        <!-- Cuti -->
        <a href="{{ route('karyawan.cuti.index') }}" class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-primary transition-colors {{ strpos($currentPage, 'karyawan/cuti') !== false ? 'text-primary dark:text-blue-400' : '' }}">
            <span class="material-symbols-outlined text-2xl">beach_access</span>
            <span class="text-xs mt-1">Cuti</span>
        </a>
        
        <!-- Lembur -->
        <a href="{{ route('karyawan.lembur.index') }}" class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-primary transition-colors {{ strpos($currentPage, 'karyawan/lembur') !== false ? 'text-primary dark:text-blue-400' : '' }}">
            <span class="material-symbols-outlined text-2xl">schedule</span>
            <span class="text-xs mt-1">Lembur</span>
        </a>
        
        <!-- Big Absen Button (Center) -->
        <div class="relative -mt-6">
            <a href="{{ route('absensi.redirect') }}" class="w-14 h-14 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 transform hover:scale-110 active:scale-95">
                <span class="material-symbols-outlined text-2xl">fingerprint</span>
            </a>
        </div>
        
        <!-- Tugas -->
        <a href="{{ route('karyawan.tugas.index') }}" class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-primary transition-colors {{ strpos($currentPage, 'karyawan/tugas') !== false ? 'text-primary dark:text-blue-400' : '' }}">
            <span class="material-symbols-outlined text-2xl">assignment</span>
            <span class="text-xs mt-1">Tugas</span>
        </a>
        
        <!-- Profile -->
        <a href="{{ route('karyawan.profile') }}" class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-primary transition-colors {{ $currentPage === 'karyawan/profile' ? 'text-primary dark:text-blue-400' : '' }}">
            <span class="material-symbols-outlined text-2xl">person</span>
            <span class="text-xs mt-1">Profil</span>
        </a>
    </div>
</div>