<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
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

    /* Gaya untuk indikator aktif/hover */
    .nav-item {
        position: relative;
        overflow: hidden;
        white-space: nowrap;
    }

    /* Default untuk mobile: di sebelah kanan */
    .nav-item::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background-color: #000;
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

    /* Hover & Active State Logic */
    .nav-item:hover::before,
    .nav-item.active::before {
        transform: translateX(0);
    }

    /* Tampilan Menu Aktif (Background Abu-abu, Teks Hitam Tebal) */
    .nav-item.active {
        background-color: #e5e7eb;
        color: #111827 !important;
        font-weight: 600 !important;
    }

    /* Memastikan sidebar tetap pas tinggi layar tanpa scroll luar */
    .sidebar-fixed {
        position: fixed;
        top: 0;
        bottom: 0;
        width: 256px !important;
        min-width: 256px !important;
        max-width: 256px !important;
        height: 100vh;
        z-index: 40;
        flex-shrink: 0 !important;
        display: flex;
        flex-direction: column;
    }

    /* Scrollbar kustom hanya aktif jika menu benar-benar membeludak */
    .sidebar-menu-nav::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar-menu-nav::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .sidebar-menu-nav::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .sidebar-menu-nav::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Memastikan teks di sidebar tidak berubah */
    .sidebar-text {
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
        font-weight: 500 !important;
        color: #374151 !important;
    }

    .sidebar-title {
        font-size: 1.5rem !important;
        line-height: 2rem !important;
        font-weight: 700 !important;
        color: #1f2937 !important;
    }

    .sidebar-icon {
        font-size: 1.25rem !important;
        display: inline-block;
    }

    /* Memastikan padding dan margin pas agar tidak memicu scroll berlebih */
    .sidebar-nav-item {
        padding: 0.5rem 0.875rem !important; 
    }

    .sidebar-header {
        height: 4.5rem !important;
        min-height: 4.5rem !important;
        max-height: 4.5rem !important;
    }

    .sidebar-footer {
        padding: 0.875rem 1rem !important;
    }

    /* Logo styling */
    .sidebar-header img {
        max-height: 2.5rem;
        width: auto;
        object-fit: contain;
        transition: all 0.3s ease;
    }

    /* Efek hover untuk logo */
    .sidebar-header:hover img {
        transform: scale(1.05);
    }
</style>

<button @click="sidebarOpen = !sidebarOpen"
    class="md:hidden fixed top-4 right-4 z-50 p-2 rounded-md bg-white shadow-md">
    <div class="w-6 h-6 flex flex-col justify-center space-y-1" :class="sidebarOpen ? 'hamburger-active' : ''">
        <div class="hamburger-line line1 w-6 h-0.5 bg-gray-800"></div>
        <div class="hamburger-line line2 w-6 h-0.5 bg-gray-800"></div>
        <div class="hamburger-line line3 w-6 h-0.5 bg-gray-800"></div>
    </div>
</button>

<div x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
    class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"></div>

<aside id="sidebar"
    class="sidebar-fixed bg-white sidebar-transition transform -translate-x-full md:translate-x-0 left-0 shadow-lg"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    <div class="sidebar-header flex items-center justify-center border-b border-gray-200 flex-shrink-0">
        <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Inovindo Logo">
    </div>

    <nav class="sidebar-menu-nav flex-1 px-4 py-3 space-y-1 overflow-y-auto">

        <a href="/manager-divisi/home"
            class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            :class="window.location.pathname === '/manager-divisi/home' ? 'active' : ''">
            <span class="material-symbols-outlined sidebar-icon">home</span>
            <span class="sidebar-text">Beranda</span>
        </a>

        <a href="/manager-divisi/data_project"
            class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            :class="window.location.pathname === '/manager-divisi/data_project' ? 'active' : ''">
            <span class="material-symbols-outlined sidebar-icon">assignment</span>
            <span class="sidebar-text">Data Project</span>
        </a>

        <a href="/manager-divisi/pengelola_tugas"
            class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            :class="window.location.pathname === '/manager-divisi/pengelola_tugas' ? 'active' : ''">
            <span class="material-symbols-outlined sidebar-icon">assignment</span>
            <span class="sidebar-text">Kelola Tugas</span>
        </a>

        <a href="/manager-divisi/daftar_karyawan"
            class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            :class="window.location.pathname === '/manager-divisi/daftar_karyawan' ? 'active' : ''">
            <span class="material-symbols-outlined sidebar-icon">groups</span>
            <span class="sidebar-text">Data Karyawan</span>
        </a>

        <a href="/manager-divisi/kelola_absensi"
            class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            :class="window.location.pathname === '/manager-divisi/kelola_absensi' ? 'active' : ''">
            <span class="material-symbols-outlined sidebar-icon">fact_check</span>
            <span class="sidebar-text">Laporan Absensi</span>
        </a>

        <a href="{{ route('manager_divisi.top_low_grade') }}"
            class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            :class="window.location.pathname.includes('top-low-grade') ? 'active' : ''">
            <span class="material-symbols-outlined sidebar-icon">leaderboard</span>
            <span class="sidebar-text">Top & Low Grade</span>
        </a>
        
        <a href="{{ route('manager_divisi.lembur.index') }}" 
           class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
           :class="window.location.pathname.includes('lembur') ? 'active' : ''">
            <span class="material-symbols-outlined sidebar-icon">schedule</span>
            <span class="sidebar-text">Approve Lembur</span>
        </a>

    </nav>

    <div class="sidebar-footer border-t border-gray-200 flex-shrink-0">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined sidebar-icon">logout</span>
            <span class="sidebar-text">Log Out</span>
        </a>

        <form id="logout-form" action="/logout" method="POST" class="hidden">
            @csrf
        </form>
    </div>

</aside>