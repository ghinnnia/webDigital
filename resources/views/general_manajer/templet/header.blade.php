<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar General Manager</title>

    <!-- Meta tag CSRF untuk keamanan form (sangat penting di Laravel) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Custom styles untuk transisi */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        /* Animasi hamburger */
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

        /* Gaya untuk efek hover yang lebih menonjol */
        .nav-item {
            position: relative;
            overflow: hidden;
            white-space: nowrap;
            /* Mencegah teks wrap */
        }

        /* Gaya untuk indikator aktif/hover */
        /* Default untuk mobile: di sebelah kanan */
        .nav-item::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background-color: #000;
            /* Warna indikator hitam */
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        /* Override untuk desktop: di sebelah kiri */
        @media (min-width: 768px) {
            .nav-item::before {
                right: auto;
                left: 0;
                transform: translateX(-100%);
            }
        }

        /* Hanya tampilkan hover untuk item yang diizinkan */
        .nav-item.allowed-active:hover::before,
        .nav-item.allowed-active.active::before {
            transform: translateX(0);
        }

        /* Gaya untuk item navigasi yang sedang aktif */
        .nav-item.allowed-active.active {
            background-color: #e5e7eb;
            /* Warna latar yang sedikit lebih gelap dari hover */
            color: #111827 !important;
            /* Warna teks yang lebih gelap, dengan !important */
            font-weight: 600 !important;
            /* Menebalkan teks, dengan !important */
        }

        /* Gaya untuk navbar karyawan */
        .employee-navbar {
            background-color: #000;
            color: #fff;
        }

        /* Memastikan sidebar tetap di posisinya saat scroll dengan ukuran tetap */
        .sidebar-fixed {
            position: fixed;
            top: 0;
            width: 256px !important;
            /* Lebar tetap, dengan !important */
            min-width: 256px !important;
            /* Lebar minimum, dengan !important */
            max-width: 256px !important;
            /* Lebar maksimum, dengan !important */
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
            flex-shrink: 0 !important;
            /* Mencegah sidebar mengecil, dengan !important */
        }

        /* Menyesuaikan konten utama agar tidak tertutup sidebar */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
            width: 100%;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px !important;
                /* Lebar sidebar yang tetap, dengan !important */
                width: calc(100% - 256px) !important;
                /* Lebar konten utama disesuaikan, dengan !important */
            }
        }

        /* Scrollbar kustom untuk sidebar */
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

        /* Container untuk memastikan layout tetap */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Memastikan sidebar tidak berubah ukurannya */
        .sidebar-wrapper {
            width: 256px !important;
            /* Lebar tetap, dengan !important */
            min-width: 256px !important;
            /* Lebar minimum, dengan !important */
            max-width: 256px !important;
            /* Lebar maksimum, dengan !important */
            flex-shrink: 0 !important;
            /* Mencegah sidebar mengecil, dengan !important */
        }

        /* Memastikan teks di sidebar tidak berubah */
        .sidebar-text {
            font-size: 0.875rem !important;
            /* 14px, dengan !important */
            line-height: 1.25rem !important;
            /* 20px, dengan !important */
            font-weight: 500 !important;
            /* Medium, dengan !important */
            color: #374151 !important;
            /* Gray-700, dengan !important */
        }

        .sidebar-title {
            font-size: 1.5rem !important;
            /* 24px, dengan !important */
            line-height: 2rem !important;
            /* 32px, dengan !important */
            font-weight: 700 !important;
            /* Bold, dengan !important */
            color: #1f2937 !important;
            /* Gray-800, dengan !important */
        }

        /* Memastikan ikon tidak berubah ukurannya */
        .sidebar-icon {
            font-size: 1.25rem !important;
            /* 20px, dengan !important */
            width: 1.25rem !important;
            /* 20px, dengan !important */
            height: 1.25rem !important;
            /* 20px, dengan !important */
        }

        /* Memastikan padding dan margin tidak berubah */
        .sidebar-nav-item {
            padding: 0.625rem 1rem !important;
            /* 10px 16px, dengan !important */
        }

        /* Mengurangi tinggi header dari 5rem (80px) menjadi 3rem (48px) */
        .sidebar-header {
            height: 4rem !important;
            /* 48px, sebelumnya 80px */
            min-height: 4rem !important;
            /* 48px, sebelumnya 80px */
            max-height: 5rem !important;
            /* 48px, sebelumnya 80px */
            padding: 0.5rem !important;
            /* Menambahkan padding untuk memberikan ruang di sekitar logo */
        }

        /* Menyesuaikan ukuran logo agar pas dengan header yang lebih kecil */
        .sidebar-logo {
            max-height: 100% !important;
            /* Maksimal tinggi logo sama dengan tinggi header */
            width: auto !important;
            /* Menjaga aspek rasio logo */
            object-fit: contain !important;
            /* Memastikan logo terlihat penuh tanpa terpotong */
        }

        .sidebar-footer {
            padding: 1.5rem 1rem !important;
            /* 24px 16px, dengan !important */
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Tombol Hamburger untuk Mobile (sekarang di kanan) -->
    <button id="hamburger" class="md:hidden fixed top-4 right-4 z-50 p-2 rounded-md bg-white shadow-md">
        <div class="w-6 h-6 flex flex-col justify-center space-y-1">
            <div class="hamburger-line line1 w-6 h-0.5 bg-gray-800"></div>
            <div class="hamburger-line line2 w-6 h-0.5 bg-gray-800"></div>
            <div class="hamburger-line line3 w-6 h-0.5 bg-gray-800"></div>
        </div>
    </button>

    <!-- Overlay untuk Mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <!-- Container utama aplikasi -->

    <aside id="sidebar"
        class="sidebar-fixed bg-white flex flex-col sidebar-transition transform translate-x-full md:translate-x-0 right-0 md:left-0 md:right-auto shadow-lg">

        <!-- Mengurangi ukuran header dan menyesuaikan kelas logo -->
        <div class="sidebar-header flex items-center justify-center border-b border-gray-200 flex-shrink-0">
            <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Login Background" class="sidebar-logo">
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <!-- Menu Beranda -->
            <a class="nav-item allowed-active flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('general_manajer.home') }}" data-page="home" data-path="general_manajer/home">
                <span class="material-icons sidebar-icon">home</span>
                <span class="sidebar-text">Beranda</span>
            </a>

            <!-- Menu Data Karyawan -->
            <a class="nav-item allowed-active flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('general_manajer.data_karyawan') }}" data-page="data_karyawan"
                data-path="general_manajer/data_karyawan">
                <span class="material-icons sidebar-icon">group</span>
                <span class="sidebar-text">Data Karyawan</span>
            </a>

            <!-- Menu Data Layanan -->
            <a class="nav-item allowed-active flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('general_manajer.layanan') }}" data-page="layanan" data-path="general_manajer/layanan">
                <span class="material-icons sidebar-icon">miscellaneous_services</span>
                <span class="sidebar-text">Data Layanan</span>
            </a>

            <!-- Menu Data Project -->
            <a class="nav-item allowed-active flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('general_manajer.data_project') }}" data-page="data_project" data-path="general_manajer/data_project">
                <span class="material-icons sidebar-icon">dashboard</span>
                <span class="sidebar-text">Data Project</span>
            </a>

            <!-- Menu Tim dan Divisi -->
            <a class="nav-item allowed-active flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('general_manajer.tim_divisi') }}" data-page="tim_divisi" data-path="general_manajer/tim_divisi">
                <span class="material-icons sidebar-icon">groups</span>
                <span class="sidebar-text">Tim dan Divisi</span>
            </a>

            <!-- Menu Top & Low Grade -->
            <a class="nav-item allowed-active flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('general_manajer.top_low_grade') }}" data-page="top_low_grade" data-path="general_manajer/top-low-grade">
                <span class="material-icons sidebar-icon">leaderboard</span>
                <span class="sidebar-text">Top & Low Grade</span>
            </a>


          
        </nav>

        <div class="sidebar-footer border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="nav-item w-full flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-icons sidebar-icon">logout</span>
                    <span class="sidebar-text">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- ELEMEN DOM ---
            const hamburger = document.getElementById('hamburger');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            // --- CEK ELEMEN PENTING ---
            // Jika salah satu elemen utama tidak ditemukan, hentikan eksekusi
            if (!hamburger || !sidebar || !overlay) {
                console.error('Error: Elemen hamburger, sidebar, atau overlay tidak ditemukan.');
                return;
            }

            // --- FUNGSI SIDEBAR ---
            function openSidebar() {
                sidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                hamburger.classList.add('hamburger-active');
                document.body.style.overflow = 'hidden'; // Mencegah scroll background
            }

            function closeSidebar() {
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
                hamburger.classList.remove('hamburger-active');
                document.body.style.overflow = ''; // Kembalikan scroll
            }

            // --- EVENT LISTENER UNTUK HAMBURGER ---
            hamburger.addEventListener('click', () => {
                // Cek apakah sidebar sedang tersembunyi (memiliki class translate-x-full)
                if (sidebar.classList.contains('translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            // --- EVENT LISTENER UNTUK OVERLAY ---
            overlay.addEventListener('click', closeSidebar);

            // --- TUTUP SIDEBAR DENGAN TOMBOL ESC ---
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !sidebar.classList.contains('translate-x-full')) {
                    closeSidebar();
                }
            });

            // --- FUNGSI UNTUK MENU AKTIF (BERSIH DARI DEBUG) ---
            function setActiveNavItem() {
                const currentPath = window.location.pathname;

                // Hapus class 'active' dari semua item
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Tambahkan class 'active' ke item yang sesuai dengan URL saat ini
                document.querySelectorAll('.nav-item.allowed-active').forEach(item => {
                    const dataPath = item.getAttribute('data-path');
                    const href = item.getAttribute('href');
                    const pageName = item.getAttribute('data-page');

                    // Normalisasi path untuk perbandingan
                    const normalizedCurrentPath = currentPath.replace(/^\//, '').toLowerCase();

                    let isActive = false;

                    // Cek berdasarkan data-path terlebih dahulu
                    if (dataPath) {
                        const normalizedDataPath = dataPath.replace(/^\//, '').toLowerCase();

                        // Cocokkan eksak atau dengan sub-path
                        if (normalizedCurrentPath === normalizedDataPath ||
                            normalizedCurrentPath.startsWith(normalizedDataPath + '/')) {
                            isActive = true;
                        }
                    }

                    // Fallback ke href jika data-path tidak cocok
                    if (!isActive && href) {
                        const normalizedHref = href.replace(/^\//, '').toLowerCase();

                        if (normalizedCurrentPath === normalizedHref ||
                            normalizedCurrentPath.startsWith(normalizedHref + '/')) {
                            isActive = true;
                        }
                    }

                    // Pencocokan khusus untuk kasus yang sulit
                    if (!isActive) {
                        // Khusus untuk kelola_tugas
                        if (pageName === 'kelola_tugas' &&
                            (normalizedCurrentPath.includes('kelola_tugas') ||
                                normalizedCurrentPath.includes('tugas'))) {
                            isActive = true;
                        }

                        // Khusus untuk kelola_absen
                        if (pageName === 'kelola_absen' &&
                            (normalizedCurrentPath.includes('kelola_absen') ||
                                normalizedCurrentPath.includes('absen'))) {
                            isActive = true;
                        }
                    }

                    if (isActive) {
                        item.classList.add('active');
                    }
                });
            }

            // --- EVENT LISTENER UNTUK SETIAP ITEM NAVIGASI ---
            document.querySelectorAll('.nav-item.allowed-active').forEach(item => {
                item.addEventListener('click', function (e) {
                    // Simpan halaman yang diklik ke sessionStorage
                    const page = this.getAttribute('data-page');
                    const path = this.getAttribute('data-path');

                    if (page) {
                        sessionStorage.setItem('lastClickedPage', page);
                        sessionStorage.setItem('lastClickedPath', path);
                    }

                    // Force update active state immediately
                    setTimeout(() => {
                        setActiveNavItem();
                    }, 10);

                    // Biarkan navigasi default berlanjut
                });
            });

            // --- INISIALISASI ---
            setActiveNavItem();

            // --- UPDATE ACTIVE STATE SAAT PAGE DIMUAT ULANG ---
            // Cek apakah ada halaman yang tersimpan di sessionStorage
            const lastClickedPage = sessionStorage.getItem('lastClickedPage');
            const lastClickedPath = sessionStorage.getItem('lastClickedPath');

            if (lastClickedPath) {
                // Verifikasi apakah kita masih di halaman yang sama
                const currentPath = window.location.pathname.replace(/^\//, '');
                const normalizedStoredPath = lastClickedPath.replace(/^\//, '');

                if (currentPath === normalizedStoredPath || currentPath.startsWith(normalizedStoredPath + '/')) {
                    setActiveNavItem();
                }
            }

            // --- HANDLER LOGOUT YANG LEBIH AMAN ---
            const logoutForm = document.querySelector('form[action*="logout"]');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Hapus sessionStorage saat logout
                    sessionStorage.removeItem('lastClickedPage');
                    sessionStorage.removeItem('lastClickedPath');

                    // Cek keberadaan meta tag CSRF
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('Meta tag CSRF-Token tidak ditemukan!');
                        alert('Terjadi kesalahan konfigurasi. Logout tidak dapat diproses.');
                        return;
                    }

                    // Kirim form menggunakan fetch
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                        .then(response => {
                            // Jika response adalah redirect (bukan JSON), arahkan saja
                            if (response.redirected) {
                                window.location.href = response.url;
                                return;
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Ini hanya dijalankan jika response adalah JSON
                            if (data && data.success) {
                                window.location.href = data.redirect_to || '/login';
                            } else if (data && data.message) {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Logout Error:', error);
                            alert('Terjadi kesalahan saat mencoba logout.');
                        });
                });
            }

            // --- MONITOR PERUBAHAN URL (UNTUK SPA) ---
            // Jika menggunakan Single Page Application, monitor perubahan URL
            let currentUrl = window.location.href;
            setInterval(() => {
                if (window.location.href !== currentUrl) {
                    currentUrl = window.location.href;
                    setTimeout(() => {
                        setActiveNavItem();
                    }, 100); // Delay kecil untuk memastikan DOM sudah update
                }
            }, 500);

            // --- FORCE UPDATE ON PAGE LOAD ---
            // Pastikan active state diupdate saat halaman selesai dimuat
            window.addEventListener('load', function () {
                setTimeout(() => {
                    setActiveNavItem();
                }, 100);
            });
        });
    </script>

</body>

</html>