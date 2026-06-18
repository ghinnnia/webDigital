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
        white-space: nowrap; /* Mencegah teks wrap */
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
        background-color: #000; /* Warna indikator hitam */
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
        background-color: #e5e7eb; /* Warna latar yang sedikit lebih gelap dari hover */
        color: #111827 !important; /* Warna teks yang lebih gelap */
        font-weight: 600 !important; /* Menebalkan teks */
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
        min-width: 256px !important;
        max-width: 256px !important;
        height: 100vh;
        overflow-y: auto;
        z-index: 40;
        flex-shrink: 0 !important; /* Mencegah sidebar mengecil */
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
            width: calc(100% - 256px) !important; /* Lebar konten utama disesuaikan */
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
        min-width: 256px !important;
        max-width: 256px !important;
        flex-shrink: 0 !important;
    }

    /* Memastikan teks di sidebar tidak berubah */
    .sidebar-text {
        font-size: 0.875rem !important; /* 14px */
        line-height: 1.25rem !important; /* 20px */
        font-weight: 500 !important; /* Medium */
        color: #374151 !important; /* Gray-700 */
    }

    .sidebar-title {
        font-size: 1.5rem !important; /* 24px */
        line-height: 2rem !important; /* 32px */
        font-weight: 700 !important; /* Bold */
        color: #1f2937 !important; /* Gray-800 */
    }

    /* Memastikan ikon tidak berubah ukurannya */
    .sidebar-icon {
        font-size: 1.25rem !important; /* 20px */
        width: 1.25rem !important;
        height: 1.25rem !important;
    }

    /* Memastikan padding dan margin tidak berubah */
    .sidebar-nav-item {
        padding: 0.625rem 1rem !important; /* 10px 16px */
    }

    /* Mengurangi tinggi header dari 5rem (80px) menjadi 4rem */
    .sidebar-header {
        height: 4rem !important;
        min-height: 4rem !important;
        max-height: 5rem !important;
        padding: 0.5rem !important; /* Memberikan ruang di sekitar logo */
    }

    /* Menyesuaikan ukuran logo agar pas dengan header */
    .sidebar-logo {
        max-height: 100% !important;
        width: auto !important;
        object-fit: contain !important;
    }

    .sidebar-footer {
        padding: 1.5rem 1rem !important;
    }
</style>

<button id="hamburger" class="md:hidden fixed top-4 right-4 z-50 p-2 rounded-md bg-white shadow-md">
    <div class="w-6 h-6 flex flex-col justify-center space-y-1">
        <div class="hamburger-line line1 w-6 h-0.5 bg-gray-800"></div>
        <div class="hamburger-line line2 w-6 h-0.5 bg-gray-800"></div>
        <div class="hamburger-line line3 w-6 h-0.5 bg-gray-800"></div>
    </div>
</button>

<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

<aside id="sidebar" class="sidebar-fixed bg-white flex flex-col sidebar-transition transform translate-x-full md:translate-x-0 right-0 md:left-0 md:right-auto shadow-lg">

    <div class="sidebar-header flex items-center justify-center border-b border-gray-200 flex-shrink-0">
        <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Logo Inovindo" class="sidebar-logo">
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
           href="{{ route('admin.beranda') }}" data-page="beranda">
            <span class="material-icons sidebar-icon">home</span>
            <span class="sidebar-text">Beranda</span>
        </a>

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

        <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
           href="{{ route('admin.data_project') }}" data-page="data_project">
            <span class="material-icons sidebar-icon">dashboard</span>
            <span class="sidebar-text">Data Project</span>
        </a>

        <div class="relative">
            <button class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors w-full text-left dropdown-toggle"
                    data-dropdown="dokumen-dropdown" data-page="dokumen">
                <span class="material-icons sidebar-icon">description</span>
                <span class="sidebar-text">Dokumen</span>
                <span class="material-icons sidebar-icon ml-auto transition-transform duration-200" id="dokumen-icon">expand_more</span>
            </button>

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

        <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
           href="{{ route('catatan_rapat.index') }}" data-page="catatan_rapat">
            <span class="material-icons sidebar-icon">note</span>
            <span class="sidebar-text">Catatan Rapat</span>
        </a>

        <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
           href="{{ route('admin.ttd.index') }}" data-page="ttd">
            <span class="material-icons sidebar-icon">draw</span>
            <span class="sidebar-text">Tanda Tangan Digital</span>
        </a>

        <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
           href="{{ route('pengumuman.index') }}" data-page="pengumuman">
            <span class="material-icons sidebar-icon">campaign</span>
            <span class="sidebar-text">Pengumuman</span>
        </a>

        <a class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
           href="{{ route('admin.settings.contact') }}" data-page="settings_contact">
            <span class="material-icons sidebar-icon">settings</span>
            <span class="sidebar-text">Pengaturan Konten</span>
        </a>
    </nav>

    <div class="sidebar-footer border-t border-gray-200">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item w-full flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
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

        if (!hamburger || !sidebar || !overlay) {
            console.error('Error: Elemen hamburger, sidebar, atau overlay tidak ditemukan.');
            return;
        }

        // --- FUNGSI SIDEBAR MOBILE ---
        function openSidebar() {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            hamburger.classList.add('hamburger-active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            hamburger.classList.remove('hamburger-active');
            document.body.style.overflow = '';
        }

        hamburger.addEventListener('click', () => {
            if (sidebar.classList.contains('translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !sidebar.classList.contains('translate-x-full')) {
                closeSidebar();
            }
        });

        // --- FUNGSI HIGHLIGHT MENU AKTIF ---
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
                { match: '/admin/ttd', page: 'ttd' }, // Ditambahkan agar menu TTD bisa aktif otomatis
                { match: '/pengumuman', page: 'pengumuman' },
                { match: '/admin/pengumuman', page: 'pengumuman' },
                { match: '/admin/settings/contact', page: 'settings_contact' }
            ];

            const currentPath = window.location.pathname;
            const pageFromPath = (pathMap.find(item => currentPath.startsWith(item.match)) || {}).page;
            const activePage = pageFromPath || sessionStorage.getItem('activeSidebar');
            if (!activePage) return;

            // Bersihkan semua class active
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Beri class active ke menu saat ini
            const activeItem = document.querySelector(`.nav-item[data-page="${activePage}"]`);
            if (activeItem) {
                activeItem.classList.add('active');

                // Otomatis buka dropdown jika menu aktif berada di dalamnya
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

        // Event listener saat item navigasi diklik
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                const page = item.getAttribute('data-page');
                if (page) {
                    sessionStorage.setItem('activeSidebar', page);
                    setActiveNavItem();
                }
            });
        });

        // --- FUNGSI TOGGLE DROPDOWN ---
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const iconId = dropdownId.replace('-dropdown', '-icon');
            const icon = document.getElementById(iconId);

            if (dropdown && icon) {
                dropdown.classList.toggle('hidden');
                icon.textContent = dropdown.classList.contains('hidden') ? 'expand_more' : 'expand_less';
            }
        }

        window.toggleDropdown = toggleDropdown;

        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdownId = this.getAttribute('data-dropdown');
                if (dropdownId) {
                    toggleDropdown(dropdownId);
                }
            });
        });

        // Jalankan inisialisasi menu aktif
        setActiveNavItem();

        // --- HANDLER LOGOUT VIA FETCH ---
        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                if (!csrfToken) {
                    console.error('CSRF token tidak ditemukan');
                    this.submit();
                    return;
                }

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
                        window.location.href = '/login';
                    } else {
                        this.submit();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.submit();
                });
            });
        }
    });
</script>