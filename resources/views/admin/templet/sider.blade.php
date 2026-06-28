<!-- Sidebar partial -->
<meta name="csrf-token" content="{{ csrf_token() }}">
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

        .nav-item:hover::before,
        .nav-item.active::before {
            transform: translateX(0);
        }

        /* Gaya untuk item navigasi yang sedang aktif */
        .nav-item.active {
            background-color: #e5e7eb;
            /* Warna latar yang sedikit lebih gelap dari hover */
            color: #111827 !important;
            /* Warna teks yang lebih gelap, dengan !important */
            font-weight: 600 !important;
            /* Menebalkan teks, dengan !important */
        }
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

        /* PERUBAHAN: Mengurangi tinggi header dari 5rem (80px) menjadi 3rem (48px) */
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

        /* PERUBAHAN: Menyesuaikan ukuran logo agar pas dengan header yang lebih kecil */
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

        <!-- PERUBAHAN: Mengurangi ukuran header dan menyesuaikan kelas logo -->
        <div class="sidebar-header flex items-center justify-center border-b border-gray-200 flex-shrink-0">
            <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Login Background" class="sidebar-logo">
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <!-- Menu Beranda -->
            <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('admin.beranda') }}" data-page="beranda">
                <span class="material-icons sidebar-icon">home</span>
                <span class="sidebar-text">Beranda</span>
            </a>

            <!-- Menu Data Layanan -->
            <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('admin.layanan.index') }}" data-page="layanan">
                <span class="material-icons sidebar-icon">handshake</span>
                <span class="sidebar-text">Data Layanan</span>
            </a>
            <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('admin.perusahaan.index') }}" data-page="perusahaan">
                <span class="material-icons sidebar-icon">business</span>
                <span class="sidebar-text">Data Perusahaan</span>
            </a>

            <!-- Menu Data Project -->
            <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('admin.data_project') }}" data-page="data_project">
                <span class="material-icons sidebar-icon">dashboard</span>
                <span class="sidebar-text">Data Project</span>
            </a>

            <!-- Menu Surat Kerjasama (Dropdown) -->
            <div class="relative">
                <button
                    class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors w-full text-left dropdown-toggle"
                    data-dropdown="dokumen-dropdown" data-page="dokumen">
                    <span class="material-icons sidebar-icon">description</span>
                    <span class="sidebar-text">Dokumen</span>
                    <span class="material-icons sidebar-icon ml-auto transition-transform duration-200"
                        id="dokumen-icon">expand_more</span>
                </button>

                <!-- Dropdown -->
                <div id="dokumen-dropdown" class="pl-6 mt-1 space-y-1 hidden">
                    <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        href="{{ route('admin.invoice.index') }}" data-page="invoice">
                        <span class="material-icons sidebar-icon">article</span>
                        <span class="sidebar-text">Invoice</span>
                    </a>

                    <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        href="{{ route('admin.kwitansi.index') }}" data-page="kwitansi">
                        <span class="material-icons sidebar-icon">list_alt</span>
                        <span class="sidebar-text">Kwitansi</span>
                    </a>
                </div>
            </div>
            <!-- Catatan Rapat -->
            <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('catatan_rapat.index') }}" data-page="catatan_rapat">
                <span class="material-icons sidebar-icon">note</span>
                <span class="sidebar-text">Catatan Rapat</span>
            </a>

            <!-- Pengumuman -->
            <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('pengumuman.index') }}" data-page="pengumuman">
                <span class="material-icons sidebar-icon">campaign</span>
                <span class="sidebar-text">Pengumuman</span>
            </a>

            <!-- Menu Pengaturan Kontak dan Tentang -->
            <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                href="{{ route('admin.settings.contact') }}" data-page="settings_contact">
                <span class="material-icons sidebar-icon">settings</span>
                <span class="sidebar-text">Pengaturan Konten</span>
            </a>
        </nav>

        <div class="sidebar-footer border-t border-gray-200">
            <!-- FORM LOGOUT YANG DIPERBAIKI -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
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
        document.addEventListener('DOMContentLoaded', function() {
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

            // --- FUNGSI UNTUK MENU AKTIF ---
            function setActiveNavItem() {
                const pathMap = [
                    { match: '/admin/beranda', page: 'beranda' },
                    { match: '/admin/home', page: 'beranda' },
                    { match: '/admin/layanan', page: 'layanan' },
                    { match: '/admin/data_project', page: 'data_project' },
                    { match: '/admin/perusahaan', page: 'perusahaan' },
                    { match: '/admin/invoice', page: 'invoice' },
                    { match: '/admin/kwitansi', page: 'kwitansi' },
                    { match: '/catatan-rapat', page: 'catatan_rapat' },
                    { match: '/admin/catatan_rapat', page: 'catatan_rapat' },
                    { match: '/pengumuman', page: 'pengumuman' },
                    { match: '/admin/pengumuman', page: 'pengumuman' },
                    { match: '/admin/settings/contact', page: 'settings_contact' }
                ];

                const currentPath = window.location.pathname;
                const pageFromPath = (pathMap.find(item => currentPath.startsWith(item.match)) || {}).page;
                const activePage = pageFromPath || sessionStorage.getItem('activeSidebar');
                if (!activePage) return;

                // Hapus class 'active' dari semua item
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Tambahkan class 'active' ke item yang sesuai
                const activeItem = document.querySelector(`.nav-item[data-page="${activePage}"]`);
                if (activeItem) {
                    activeItem.classList.add('active');

                    // Jika item berada di dalam dropdown, buka dropdown tersebut
                    const dropdownParent = activeItem.closest('[id$="-dropdown"]');
                    if (dropdownParent) {
                        dropdownParent.classList.remove('hidden');
                        const iconId = dropdownParent.id.replace('-dropdown', '-icon');
                        const icon = document.getElementById(iconId);
                        if (icon) {
                            icon.textContent = 'expand_less';
                        }
                    }
                }

                if (activePage) {
                    sessionStorage.setItem('activeSidebar', activePage);
                }
            }

            // --- EVENT LISTENER UNTUK SETIAP ITEM NAVIGASI ---
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', () => {
                    const page = item.getAttribute('data-page');
                    if (page) {
                        sessionStorage.setItem('activeSidebar', page);
                        // Set status aktif sebelum pindah halaman (untuk efek instan)
                        setActiveNavItem();
                    }
                });
            });

            // --- FUNGSI UNTUK DROPDOWN ---
            function toggleDropdown(dropdownId) {
                const dropdown = document.getElementById(dropdownId);
                const iconId = dropdownId.replace('-dropdown', '-icon');
                const icon = document.getElementById(iconId);

                if (dropdown && icon) {
                    dropdown.classList.toggle('hidden');
                    icon.textContent = dropdown.classList.contains('hidden') ? 'expand_more' : 'expand_less';
                }
            }

            // Membuat fungsi global agar bisa dipanggil dari onclick di HTML
            window.toggleDropdown = toggleDropdown;

            // --- EVENT LISTENER UNTUK DROPDOWN BUTTON ---
            document.querySelectorAll('.dropdown-toggle').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation(); // Mencegah event bubbling
                    const dropdownId = this.getAttribute('data-dropdown');
                    if (dropdownId) {
                        toggleDropdown(dropdownId);
                    }
                });
            });

            // --- INISIALISASI ---
            setActiveNavItem();

            // --- HANDLER LOGOUT YANG LEBIH AMAN ---
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Ambil CSRF token dari meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                    if (!csrfToken) {
                        console.error('CSRF token tidak ditemukan');
                        // Fallback: submit form biasa
                        this.submit();
                        return;
                    }

                    // Kirim request logout dengan fetch
                    fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => {
                            if (response.ok || response.redirected) {
                                // Redirect ke halaman login
                                window.location.href = '/login';
                            } else {
                                console.error('Logout gagal');
                                // Fallback: submit form biasa
                                this.submit();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Fallback: submit form biasa
                            this.submit();
                        });
                });
            }
        });
    </script>
