<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Kelola Absensi - Dashboard Manajer</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    {{-- MATERIAL SYMBOLS untuk Sidebar (WAJIB) --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .material-icons-outlined { font-size: 24px; vertical-align: middle; }
        
        /* Custom Scrollbar for Tables */
        .scrollable-table-container::-webkit-scrollbar { height: 8px; width: 8px; }
        .scrollable-table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .scrollable-table-container::-webkit-scrollbar-track { background: #f1f5f9; }

        /* Card & Table Styling */
        .card { transition: all 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
        
        .status-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        
        /* Status Colors */
        .bg-hadir { background-color: rgba(16, 185, 129, 0.15); color: #065f46; }
        .bg-terlambat { background-color: rgba(245, 158, 11, 0.15); color: #92400e; }
        .bg-izin { background-color: rgba(59, 130, 246, 0.15); color: #1e40af; }
        .bg-cuti { background-color: rgba(239, 68, 68, 0.15); color: #991b1b; }
        .bg-sakit { background-color: rgba(251, 146, 60, 0.15); color: #9a3412; }
        .bg-dinas-luar { background-color: rgba(139, 92, 246, 0.15); color: #5b21b6; }
        .bg-tidak-masuk { background-color: rgba(107, 114, 128, 0.15); color: #374151; }
        
        /* Approval Status Colors */
        .bg-pending { background-color: rgba(245, 158, 11, 0.15); color: #92400e; }
        .bg-approved { background-color: rgba(16, 185, 129, 0.15); color: #065f46; }
        .bg-rejected { background-color: rgba(239, 68, 68, 0.15); color: #991b1b; }

        .icon-container { display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; }
        
        /* Table */
        .data-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .data-table th { background: #f8fafc; font-weight: 600; color: #374151; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .data-table tbody tr:hover { background: #f3f4f6; }
        .form-input { border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: all 0.2s ease; width: 100%; box-sizing: border-box;}
        .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
        
        /* Tabs */
        .tab-nav { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 1.5rem; }
        .tab-button { padding: 0.75rem 1.5rem; background: none; border: none; font-size: 0.875rem; font-weight: 500; color: #6b7280; cursor: pointer; position: relative; transition: color 0.2s; }
        .tab-button:hover { color: #3b82f6; }
        .tab-button.active { color: #3b82f6; font-weight: 600; }
        .tab-button.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background-color: #3b82f6; }
        
        /* Panel */
        .panel { background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); overflow: hidden; margin-bottom: 1.5rem; }
        .panel-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .panel-title { font-size: 1.125rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
        .panel-body { padding: 1.5rem; }
        
        /* Filter Dropdown */
        .filter-dropdown { display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 20; min-width: 220px; padding: 1rem; margin-top: 0.5rem; }
        .filter-dropdown.show { display: block; }
        .filter-option { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; cursor: pointer; }
        .filter-option:hover { background-color: #f8fafc; }
        .filter-actions { display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; }
        
        /* Mobile Responsive */
        .desktop-table { display: block; }
        .mobile-cards { display: none; }
        @media (max-width: 768px) {
            .desktop-table { display: none; }
            .mobile-cards { display: block; }
        }
        .mobile-card { background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; }
        .mobile-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .mobile-card-title { font-weight: 600; color: #111827; }
        .mobile-card-body { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .mobile-card-item { display: flex; flex-direction: column; }
        .mobile-card-label { font-size: 0.75rem; color: #6b7280; }
        .mobile-card-value { font-weight: 500; font-size: 0.875rem; color: #374151; }
        
        /* Pagination */
        .pagination-container { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; }
        .nav-btn { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; }
        .nav-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .page-btn { padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white; cursor: pointer; font-size: 0.875rem; color: #374151; transition: all 0.2s;}
        .page-btn:hover { background-color: #f8fafc; }
        .page-btn.active { background-color: #3b82f6; color: white; border-color: #3b82f6; }
        
        /* Modal */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 50; backdrop-filter: blur(2px); }
        .modal.show { display: flex; }
        .modal-content { background: white; border-radius: 0.75rem; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); animation: slideDown 0.3s ease-out; }
        
        /* Sidebar Styles */
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
            white-space: nowrap;
        }

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

        .nav-item.active {
            background-color: #e5e7eb;
            color: #111827 !important;
            font-weight: 600 !important;
        }

        .sidebar-fixed {
            position: fixed;
            top: 0;
            width: 256px !important;
            min-width: 256px !important;
            max-width: 256px !important;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
            flex-shrink: 0 !important;
        }

        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
            width: 100%;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px !important;
                width: calc(100% - 256px) !important;
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

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar-wrapper {
            width: 256px !important;
            min-width: 256px !important;
            max-width: 256px !important;
            flex-shrink: 0 !important;
        }

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
            width: 1.25rem !important;
            height: 1.25rem !important;
        }

        .sidebar-nav-item {
            padding: 0.625rem 1rem !important;
        }

        .sidebar-header {
            height: 5rem !important;
            min-height: 5rem !important;
            max-height: 5rem !important;
        }

        .sidebar-footer {
            padding: 1.5rem 1rem !important;
        }

        [x-cloak] {
            display: none !important;
        }

        .sidebar-header img {
            max-height: 3rem;
            width: auto;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .sidebar-header:hover img {
            transform: scale(1.05);
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Utility */
        .hidden { display: none !important; }
        .cursor-pointer { cursor: pointer; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: false }">
    <!-- Tombol Hamburger untuk Mobile (sekarang di kanan) -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="md:hidden fixed top-4 right-4 z-50 p-2 rounded-md bg-white shadow-md">
        <div class="w-6 h-6 flex flex-col justify-center space-y-1" :class="sidebarOpen ? 'hamburger-active' : ''">
            <div class="hamburger-line line1 w-6 h-0.5 bg-gray-800"></div>
            <div class="hamburger-line line2 w-6 h-0.5 bg-gray-800"></div>
            <div class="hamburger-line line3 w-6 h-0.5 bg-gray-800"></div>
        </div>
    </button>

    <!-- Overlay untuk Mobile -->
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"></div>

    <!-- Container utama aplikasi -->
    <div class="app-container flex">
        <!-- Sidebar dari template -->
        <aside id="sidebar"
            class="sidebar-fixed bg-white flex flex-col sidebar-transition transform translate-x-full md:translate-x-0 right-0 md:left-0 md:right-auto shadow-lg"
            :class="sidebarOpen ? 'translate-x-0' : ''">

            <!-- BRAND -->
            <div class="sidebar-header flex items-center justify-center border-b border-gray-200 flex-shrink-0">
                <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Inovindo Logo"
                    class="h-12 w-auto object-contain">
            </div>

            <!-- MENU -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

                <!-- BERANDA -->
                <a href="/manager-divisi/home"
                    class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    :class="window.location.pathname === '/manager-divisi/home' ? 'active' : ''">
                    <span class="material-symbols-outlined sidebar-icon">home</span>
                    <span class="sidebar-text">Beranda</span>
                </a>

                <!-- DATA PROJECT -->
                <a href="/manager-divisi/data_project"
                    class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    :class="window.location.pathname === '/manager-divisi/data_project' ? 'active' : ''">
                    <span class="material-symbols-outlined sidebar-icon">assignment</span>
                    <span class="sidebar-text">Data Project</span>
                </a>

                <!-- KELOLA TUGAS -->
                <a href="/manager-divisi/pengelola_tugas"
                    class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    :class="window.location.pathname === '/manager-divisi/pengelola_tugas' ? 'active' : ''">
                    <span class="material-symbols-outlined sidebar-icon">assignment</span>
                    <span class="sidebar-text">Kelola Tugas</span>
                </a>

                <!-- DATA KARYAWAN -->
                <a href="/manager-divisi/daftar_karyawan"
                    class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    :class="window.location.pathname === '/manager-divisi/daftar_karyawan' ? 'active' : ''">
                    <span class="material-symbols-outlined sidebar-icon">groups</span>
                    <span class="sidebar-text">Data Karyawan</span>
                </a>

                <!-- LAPORAN ABSENSI -->
                <a href="/manager-divisi/kelola_absensi"
                    class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    :class="window.location.pathname === '/manager-divisi/kelola_absensi' ? 'active' : ''">
                    <span class="material-symbols-outlined sidebar-icon">fact_check</span>
                    <span class="sidebar-text">Laporan Absensi</span>
                </a>

            </nav>

            <!-- LOGOUT -->
            <div class="sidebar-footer border-t border-gray-200 flex-shrink-0">
                <a href="#" @click="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="nav-item flex items-center gap-3 sidebar-nav-item text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined sidebar-icon">logout</span>
                    <span class="sidebar-text">Log Out</span>
                </a>

                <form id="logout-form" action="/logout" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="main-content p-4 sm:p-6 lg:p-8 min-h-screen">
            <div class="max-w-7xl mx-auto">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Laporan Absensi</h1>
                    <p class="text-sm text-gray-500 mt-1">Laporan kehadiran karyawan Divisi <span class="font-semibold text-gray-700">{{ $selectedDivision }}</span> (View Only)</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-200">
                    <span class="material-icons-outlined text-base text-blue-500">calendar_today</span>
                    <span id="currentDateDisplay"></span>
                </div>
            </div>

            <!-- Statistics Cards (Auto-updated) -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <!-- Cards generated by JS -->
                <div id="statsContainer" class="contents"></div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-nav">
                <button id="tabAbsensi" class="tab-button active" onclick="switchTab('absensi')">
                    <span class="material-icons-outlined align-middle mr-2 text-sm">fact_check</span>
                    Data Absensi
                </button>
                <button id="tabKetidakhadiran" class="tab-button" onclick="switchTab('ketidakhadiran')">
                    <span class="material-icons-outlined align-middle mr-2 text-sm">assignment_late</span>
                    Daftar Ketidakhadiran
                </button>
            </div>

            <!-- Search and Filter Controls -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div class="relative w-full md:w-1/3">
                    <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                    <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 form-input text-sm" placeholder="Cari nama karyawan..." type="text" />
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <!-- Date Filter -->
                    <div class="relative">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">date_range</span>
                        <input id="dateFilter" class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 form-input text-sm cursor-pointer" placeholder="Filter Tanggal" type="date" />
                    </div>
                    
                    <!-- Status Filter Dropdown -->
                    <div class="relative">
                        <button id="filterBtn" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2 text-sm font-medium shadow-sm">
                            <span class="material-icons-outlined text-sm">filter_list</span>
                            Filter Status
                        </button>
                        <div id="filterDropdown" class="filter-dropdown">
                            <div class="filter-option" onclick="toggleFilter('all')">
                                <input type="radio" name="filter" id="filterAll" value="all" checked>
                                <label for="filterAll" class="cursor-pointer w-full">Semua Status</label>
                            </div>
                            <div class="h-px bg-gray-100 my-1"></div>
                            <div class="filter-option" onclick="toggleFilter('Hadir')"><input type="radio" name="filter" value="Hadir"> <label class="cursor-pointer w-full">Hadir</label></div>
                            <div class="filter-option" onclick="toggleFilter('Terlambat')"><input type="radio" name="filter" value="Terlambat"> <label class="cursor-pointer w-full">Terlambat</label></div>
                            <div class="filter-option" onclick="toggleFilter('Izin')"><input type="radio" name="filter" value="Izin"> <label class="cursor-pointer w-full">Izin</label></div>
                            <div class="filter-option" onclick="toggleFilter('Sakit')"><input type="radio" name="filter" value="Sakit"> <label class="cursor-pointer w-full">Sakit</label></div>
                            <div class="filter-option" onclick="toggleFilter('Cuti')"><input type="radio" name="filter" value="Cuti"> <label class="cursor-pointer w-full">Cuti</label></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL 1: DATA ABSENSI -->
            <div id="panelAbsensi" class="panel">
                <div class="panel-header">
                    <h3 class="panel-title text-gray-800">
                        <span class="material-icons-outlined text-blue-500">fact_check</span>
                        Riwayat Absensi Harian
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Total: <span id="totalCountAbsensi" class="font-bold text-gray-800">{{ isset($formattedAbsensi) ? count($formattedAbsensi) : (isset($absensiPaginator) ? $absensiPaginator->total() : 0) }}</span> data</span>
                    </div>
                </div>
                <div class="panel-body p-0">
                    <!-- Desktop Table -->
                    <div class="desktop-table overflow-x-auto">
                        <table class="data-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="min-w-[50px]">No</th>
                                    <th class="min-w-[180px]">Nama Karyawan</th>
                                    <th class="min-w-[120px]">Tanggal</th>
                                    <th class="min-w-[120px]">Jam Masuk</th>
                                    <th class="min-w-[120px]">Jam Pulang</th>
                                    <th class="min-w-[120px]">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody id="absensiTableBody" class="text-sm text-gray-600">
                                @if(isset($formattedAbsensi) && count($formattedAbsensi) > 0)
                                    @foreach($formattedAbsensi as $i => $item)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                            <td class="text-center text-gray-400">{{ ($absensiPaginator && method_exists($absensiPaginator, 'firstItem')) ? $absensiPaginator->firstItem() + $i : $i+1 }}</td>
                                            <td class="font-medium text-gray-800">{{ $item['user_name'] ?? $item->user_name ?? ($item->user_name ?? '') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item['tanggal'] ?? ($item->tanggal ?? ''))->translatedFormat('d M Y') }}</td>
                                            <td><span class="text-gray-700 font-medium">{{ $item['jam_masuk'] ?? ($item->jam_masuk ?? '-') }}</span></td>
                                            <td><span class="{{ ( ($item['jam_pulang'] ?? ($item->jam_pulang ?? '-')) === '-' ) ? 'text-gray-400 italic' : 'text-gray-700 font-medium' }}">{{ $item['jam_pulang'] ?? ($item->jam_pulang ?? '-') }}</span></td>
                                            <td>
                                                @php
                                                    $lateMinutes = (int) ($item['late_minutes'] ?? ($item->late_minutes ?? 0));
                                                    $statusLabel = $item['status_kehadiran'] ?? ($item->status_kehadiran ?? '');
                                                    $statusClass = $item['status_class'] ?? ($item->status_class ?? '');
                                                    $lateHours = intdiv($lateMinutes, 60);
                                                    $lateRemain = $lateMinutes % 60;
                                                    $lateParts = [];
                                                    if ($lateHours > 0) $lateParts[] = $lateHours . ' jam';
                                                    if ($lateRemain > 0 || empty($lateParts)) $lateParts[] = $lateRemain . ' menit';
                                                    $lateText = implode(' ', $lateParts);
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    {{ $statusLabel }}{{ ($statusLabel === 'Terlambat' && $lateMinutes > 0) ? ' (' . $lateText . ')' : '' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-sm text-gray-500 py-6">Tidak ada data absensi.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="mobile-cards p-4" id="absensiMobileCards">
                        @if(isset($formattedAbsensi) && count($formattedAbsensi) > 0)
                            @foreach($formattedAbsensi as $i => $item)
                                <div class="mobile-card border-l-4 {{ (($item['status_kehadiran'] ?? ($item->status_kehadiran ?? '')) === 'Tepat Waktu') ? 'border-l-green-500' : 'border-l-yellow-500' }}">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ $item['user_name'] ?? ($item->user_name ?? '') }}</h4>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item['tanggal'] ?? ($item->tanggal ?? ''))->translatedFormat('d M Y') }}</p>
                                        </div>
                                        <span class="status-badge {{ $item['status_class'] ?? ($item->status_class ?? '') }} text-xs">{{ $item['status_kehadiran'] ?? ($item->status_kehadiran ?? '') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                        <div class="bg-gray-50 p-2 rounded">
                                            <p class="text-xs text-gray-400">Masuk</p>
                                            <p class="font-semibold text-gray-700">{{ $item['jam_masuk'] ?? ($item->jam_masuk ?? '-') }}</p>
                                        </div>
                                        <div class="bg-gray-50 p-2 rounded">
                                            <p class="text-xs text-gray-400">Pulang</p>
                                            <p class="font-semibold text-gray-700">{{ $item['jam_pulang'] ?? ($item->jam_pulang ?? '-') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Pagination -->
                    <div id="paginationAbsensi" class="pagination-container p-4 border-t border-gray-100">
                        @if(isset($absensiPaginator))
                            {{ $absensiPaginator->links() }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- PANEL 2: DAFTAR KETIDAKHADIRAN (IZIN/SAKIT/CUTI) -->
            <div id="panelKetidakhadiran" class="panel hidden">
                <div class="panel-header">
                    <h3 class="panel-title text-gray-800">
                        <span class="material-icons-outlined text-orange-500">assignment_late</span>
                        Pengajuan Izin & Cuti
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Total: <span id="totalCountKetidakhadiran" class="font-bold text-gray-800">{{ isset($ketidakhadiran) ? count($ketidakhadiran) : 0 }}</span> data</span>
                    </div>
                </div>
                <div class="panel-body p-0">
                    <!-- Desktop Table -->
                    <div class="desktop-table overflow-x-auto">
                        <table class="data-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="min-w-[50px]">No</th>
                                    <th class="min-w-[200px]">Nama Karyawan</th>
                                    <th class="min-w-[120px]">Jenis</th>
                                    <th class="min-w-[150px]">Tanggal</th>
                                    <th class="min-w-[200px]">Keterangan</th>
                                    <th class="min-w-[120px]">Status Approval</th>
                                </tr>
                            </thead>
                            <tbody id="ketidakhadiranTableBody" class="text-sm text-gray-600">
                                @if(isset($ketidakhadiran) && count($ketidakhadiran) > 0)
                                    @foreach($ketidakhadiran as $j => $k)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                            <td class="text-center text-gray-400">{{ $j + 1 }}</td>
                                            <td class="font-medium text-gray-800">{{ $k->user_name ?? ($k->user->name ?? '') }}</td>
                                            <td><span class="font-semibold text-gray-700">{{ $k->jenis_ketidakhadiran ?? ($k->type ?? '-') }}</span></td>
                                            <td>
                                                @php
                                                    $startTanggal = $k->tanggal_mulai ?? ($k->tanggal ?? ($k->dateStart ?? null));
                                                    $endTanggal = $k->tanggal_selesai ?? ($k->tanggal_akhir ?? ($k->dateEnd ?? $startTanggal));
                                                @endphp
                                                {{ $startTanggal ? \Carbon\Carbon::parse($startTanggal)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($endTanggal ?? $startTanggal)->translatedFormat('d M Y') : '-' }}
                                            </td>
                                            <td class="max-w-xs truncate text-gray-500" title="{{ preg_replace('/\s*\(Disetujui\)\s*$/i', '', ($k->reason ?? ($k->keterangan ?? ''))) }}">{{ preg_replace('/\s*\(Disetujui\)\s*$/i', '', ($k->reason ?? ($k->keterangan ?? ''))) }}</td>
                                            <td><span class="status-badge {{ ($k->approval_status ?? '') === 'approved' ? 'bg-approved' : (($k->approval_status ?? '') === 'rejected' ? 'bg-rejected' : 'bg-pending') }}">{{ ucfirst($k->approval_status ?? 'pending') }}</span></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-sm text-gray-500 py-6">Tidak ada data ketidakhadiran.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="mobile-cards p-4" id="ketidakhadiranMobileCards">
                        @if(isset($ketidakhadiran) && count($ketidakhadiran) > 0)
                            @foreach($ketidakhadiran as $k)
                                <div class="mobile-card border-l-4 border-gray-400">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ $k->user_name ?? ($k->user->name ?? '') }}</h4>
                                            <p class="text-xs text-gray-500 font-medium">{{ $k->jenis_ketidakhadiran ?? ($k->type ?? '-') }}</p>
                                        </div>
                                        <span class="status-badge {{ ($k->approval_status ?? '') === 'approved' ? 'bg-approved' : (($k->approval_status ?? '') === 'rejected' ? 'bg-rejected' : 'bg-pending') }}">{{ ucfirst($k->approval_status ?? 'pending') }}</span>
                                    </div>
                                    <div class="text-sm mb-3">
                                        <p class="text-gray-400 text-xs mb-1">Tanggal</p>
                                        <p class="font-medium text-gray-700">
                                            @php
                                                $startTanggal = $k->tanggal_mulai ?? ($k->tanggal ?? ($k->dateStart ?? null));
                                                $endTanggal = $k->tanggal_selesai ?? ($k->tanggal_akhir ?? ($k->dateEnd ?? $startTanggal));
                                            @endphp
                                            {{ $startTanggal ? \Carbon\Carbon::parse($startTanggal)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($endTanggal ?? $startTanggal)->translatedFormat('d M Y') : '-' }}
                                        </p>
                                    </div>
                                    <div class="text-sm mb-4">
                                        <p class="text-gray-400 text-xs mb-1">Keterangan</p>
                                        <p class="text-gray-600">{{ preg_replace('/\s*\(Disetujui\)\s*$/i', '', ($k->reason ?? ($k->keterangan ?? ''))) }}</p>
                                        @if(($k->approval_status ?? '') === 'rejected')
                                            <p class="text-red-500 text-xs mt-1 italic">Alasan Ditolak: {{ $k->rejection_reason ?? '-' }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Pagination -->
                    <div id="paginationKetidakhadiran" class="pagination-container p-4 border-t border-gray-100">
                        {{-- If you have separate paginator for ketidakhadiran, render links here --}}
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-2"></div>

    <!-- MODAL: Verifikasi (Approve/Reject) -->
    <div id="verifyModal" class="modal">
        <div class="modal-content">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="material-icons-outlined text-blue-500">verified_user</span>
                    Verifikasi Pengajuan
                </h3>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal('verifyModal')">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="verifyForm" class="p-6">
                <input type="hidden" id="verifyId">
                
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Karyawan</p>
                    <p id="verifyEmployeeName" class="text-sm font-bold text-gray-800 mb-1">-</p>
                    <div class="flex gap-2">
                        <span id="verifyLeaveType" class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">-</span>
                        <span id="verifyDateRange" class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">-</span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan Manajer</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="decision" value="approved" class="peer sr-only" checked onchange="toggleRejectionReason(false)">
                            <div class="p-3 text-center rounded-lg border border-gray-200 peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-700 hover:bg-gray-50 transition-all">
                                <span class="material-icons-outlined text-sm align-middle mr-1">check_circle</span> Setujui
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="decision" value="rejected" class="peer sr-only" onchange="toggleRejectionReason(true)">
                            <div class="p-3 text-center rounded-lg border border-gray-200 peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-700 hover:bg-gray-50 transition-all">
                                <span class="material-icons-outlined text-sm align-middle mr-1">cancel</span> Tolak
                            </div>
                        </label>
                    </div>
                </div>

                <div id="rejectionReasonContainer" class="mb-5 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea id="rejectionReason" rows="3" class="w-full bg-white border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all" placeholder="Tuliskan alasan mengapa pengajuan ini ditolak..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors" onclick="closeModal('verifyModal')">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-md shadow-blue-200 transition-all">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // === 1. SERVER DATA (from Controller) ===
        const users = @json(isset($users) ? $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values() : []);
        const serverAbsensi = @json(isset($formattedAbsensi) ? $formattedAbsensi : []);
        const serverKetidakhadiran = @json(isset($ketidakhadiran) ? $ketidakhadiran : []);

        // If controller passed a paginator object, its items are under `data` (or `items`).
        const serverAbsensiArray = Array.isArray(serverAbsensi) ? serverAbsensi : (serverAbsensi && serverAbsensi.data ? serverAbsensi.data : (serverAbsensi && serverAbsensi.items ? serverAbsensi.items : []));
        const serverKetidakhadiranArray = Array.isArray(serverKetidakhadiran) ? serverKetidakhadiran : (serverKetidakhadiran && serverKetidakhadiran.data ? serverKetidakhadiran.data : (serverKetidakhadiran && serverKetidakhadiran.items ? serverKetidakhadiran.items : []));

        // Normalize server data into the shapes expected by the client renderers
        let dataAbsensi = (serverAbsensiArray || []).map(item => ({
            id: item.id,
            userId: item.user_id ?? item.userId ?? (item.user && item.user.id) ?? null,
            name: item.user_name ?? (item.user && item.user.name) ?? item.name ?? '',
            date: item.tanggal ?? item.date ?? item.created_at ?? null,
            checkIn: item.jam_masuk ?? item.checkIn ?? item.check_in ?? '-',
            checkOut: item.jam_pulang ?? item.checkOut ?? item.check_out ?? '-',
            status: item.status_kehadiran ?? item.jenis_ketidakhadiran ?? item.status ?? 'Hadir',
            lateMinutes: Number(item.late_minutes ?? item.lateMinutes ?? 0)
        }));

        const cleanReasonText = (value) => String(value ?? '').replace(/\s*\(Disetujui\)\s*$/i, '').trim();

        let dataKetidakhadiran = (serverKetidakhadiranArray || []).map(item => ({
            id: item.id,
            userId: item.user_id ?? item.userId ?? (item.user && item.user.id) ?? null,
            name: item.user_name ?? (item.user && item.user.name) ?? item.name ?? '',
            type: item.jenis_ketidakhadiran ?? item.type ?? item.kategori ?? 'Izin',
            dateStart: item.tanggal_mulai ?? item.start_date ?? item.tanggal ?? item.dateStart ?? null,
            dateEnd: item.tanggal_selesai ?? item.end_date ?? item.tanggal_akhir ?? item.dateEnd ?? item.tanggal ?? item.dateStart ?? null,
            reason: cleanReasonText(item.reason ?? item.keterangan ?? item.alasan ?? ''),
            status: item.approval_status ?? item.status ?? 'pending',
            rejectionNote: item.rejection_reason ?? item.rejectionNote ?? item.alasan_penolakan ?? null
        }));

        // === 2. STATE MANAGEMENT ===
        let state = {
            activeTab: 'absensi', // 'absensi' or 'ketidakhadiran'
            currentPage: 1,
            itemsPerPage: 5,
            search: '',
            filterStatus: 'all', // 'all', 'Hadir', 'Terlambat', 'Sakit', etc.
            filterDate: ''
        };

        // === 3. INITIALIZATION ===
        document.addEventListener('DOMContentLoaded', () => {
            // Set current date in header
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('currentDateDisplay').textContent = new Date().toLocaleDateString('id-ID', options);

            // Initial Render: skip client-side table rendering to preserve server-side HTML
            // updateStats();
            // renderAbsensiTable();
            // renderKetidakhadiranTable();
            
            // Event Listeners
            document.getElementById('searchInput').addEventListener('input', (e) => {
                state.search = e.target.value.toLowerCase();
                state.currentPage = 1;
                refreshCurrentView();
            });

            document.getElementById('dateFilter').addEventListener('change', (e) => {
                state.filterDate = e.target.value;
                state.currentPage = 1;
                refreshCurrentView();
            });

            // Toggle Filter Dropdown
            document.getElementById('filterBtn').addEventListener('click', (e) => {
                e.stopPropagation();
                document.getElementById('filterDropdown').classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.relative')) {
                    document.getElementById('filterDropdown').classList.remove('show');
                }
            });

            // Verification Form Submit
            document.getElementById('verifyForm').addEventListener('submit', handleVerificationSubmit);
        });

        // === 4. CORE FUNCTIONS ===

        function switchTab(tabName) {
            state.activeTab = tabName;
            state.currentPage = 1; // Reset page on switch
            
            // UI Toggle
            const btnAbsensi = document.getElementById('tabAbsensi');
            const btnKetid = document.getElementById('tabKetidakhadiran');
            const panelAbsensi = document.getElementById('panelAbsensi');
            const panelKetid = document.getElementById('panelKetidakhadiran');

            if (tabName === 'absensi') {
                btnAbsensi.classList.add('active');
                btnKetid.classList.remove('active');
                panelAbsensi.classList.remove('hidden');
                panelKetid.classList.add('hidden');
                renderAbsensiTable();
            } else {
                btnKetid.classList.add('active');
                btnAbsensi.classList.remove('active');
                panelKetid.classList.remove('hidden');
                panelAbsensi.classList.add('hidden');
                renderKetidakhadiranTable();
            }
        }

        function toggleFilter(status) {
            state.filterStatus = status;
            // Update Radio UI
            const radios = document.querySelectorAll('input[name="filter"]');
            radios.forEach(r => {
                if(r.value === status) r.checked = true;
            });
            state.currentPage = 1;
            refreshCurrentView();
            document.getElementById('filterDropdown').classList.remove('show');
        }

        function refreshCurrentView() {
            if (state.activeTab === 'absensi') renderAbsensiTable();
            else renderKetidakhadiranTable();
        }

        function getUserName(userId) {
            const user = users.find(u => u.id === userId);
            return user ? user.name : 'Unknown';
        }

        // === 5. RENDER LOGIC: DATA ABSENSI ===

        function renderAbsensiTable() {
            const tbody = document.getElementById('absensiTableBody');
            const mobileCards = document.getElementById('absensiMobileCards');
            const pagination = document.getElementById('paginationAbsensi');
            
            tbody.innerHTML = '';
            mobileCards.innerHTML = '';
            pagination.innerHTML = '';

            // Filter Logic
            let filtered = dataAbsensi.filter(item => {
                const name = (item.name || getUserName(item.userId)).toLowerCase();
                const matchesSearch = name.includes(state.search);
                const matchesDate = state.filterDate ? item.date === state.filterDate : true;
                const matchesStatus = state.filterStatus === 'all' ? true : item.status === state.filterStatus;
                return matchesSearch && matchesDate && matchesStatus;
            });

            // Update Count
            document.getElementById('totalCountAbsensi').textContent = filtered.length;

            // Pagination Logic
            const totalPages = Math.ceil(filtered.length / state.itemsPerPage) || 1;
            if (state.currentPage > totalPages) state.currentPage = totalPages;
            
            const start = (state.currentPage - 1) * state.itemsPerPage;
            const end = start + state.itemsPerPage;
            const pageData = filtered.slice(start, end);

            // Generate Desktop Rows
            pageData.forEach((item, index) => {
                const globalIndex = start + index + 1;
                const statusClass = item.status === 'Tepat Waktu'
                    ? 'bg-hadir'
                    : (item.status === 'Terlambat' ? 'bg-terlambat' : 'bg-pending');
                const name = item.name || getUserName(item.userId);
                const lateLabel = formatLateDuration(item.lateMinutes);
                const statusLabel = item.status === 'Terlambat' && item.lateMinutes > 0
                    ? `${item.status} (${lateLabel})`
                    : item.status;
                
                const row = `
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="text-center text-gray-400">${globalIndex}</td>
                        <td class="font-medium text-gray-800">${name}</td>
                        <td>${formatDateIndo(item.date)}</td>
                        <td><span class="text-gray-700 font-medium">${displayRaw(item.checkIn)}</span></td>
                        <td><span class="${item.checkOut === '-' ? 'text-gray-400 italic' : 'text-gray-700 font-medium'}">${displayRaw(item.checkOut)}</span></td>
                        <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);

                // Generate Mobile Card
                const card = `
                    <div class="mobile-card border-l-4 ${item.status === 'Hadir' ? 'border-l-green-500' : 'border-l-yellow-500'}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-gray-800">${name}</h4>
                                <p class="text-xs text-gray-500">${formatDateIndo(item.date)}</p>
                            </div>
                            <span class="status-badge ${statusClass} text-xs">${statusLabel}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div class="bg-gray-50 p-2 rounded">
                                <p class="text-xs text-gray-400">Masuk</p>
                                <p class="font-semibold text-gray-700">${displayRaw(item.checkIn)}</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <p class="text-xs text-gray-400">Pulang</p>
                                <p class="font-semibold text-gray-700">${displayRaw(item.checkOut)}</p>
                            </div>
                        </div>
                    </div>
                `;
                mobileCards.insertAdjacentHTML('beforeend', card);
            });

            // Render Pagination
            renderPaginationControls(pagination, totalPages, 'absensi');
        }

        // === 6. RENDER LOGIC: KETIDAKHADIRAN (Approvals) ===

        function renderKetidakhadiranTable() {
            const tbody = document.getElementById('ketidakhadiranTableBody');
            const mobileCards = document.getElementById('ketidakhadiranMobileCards');
            const pagination = document.getElementById('paginationKetidakhadiran');

            tbody.innerHTML = '';
            mobileCards.innerHTML = '';
            pagination.innerHTML = '';

            // Filter Logic
            let filtered = dataKetidakhadiran.filter(item => {
                const name = (item.name || getUserName(item.userId)).toLowerCase();
                const matchesSearch = name.includes(state.search);
                const matchesDate = state.filterDate ? item.dateStart === state.filterDate : true;
                
                let matchesStatus = true;
                if (state.filterStatus !== 'all') {
                    // Filter by Type (Sakit, Cuti, etc) OR Status (Pending, Approved, Rejected)
                    // For this demo, we assume Filter Dropdown selects the TYPE of leave
                    matchesStatus = item.type === state.filterStatus;
                }

                return matchesSearch && matchesDate && matchesStatus;
            });

            document.getElementById('totalCountKetidakhadiran').textContent = filtered.length;

            const totalPages = Math.ceil(filtered.length / state.itemsPerPage) || 1;
            if (state.currentPage > totalPages) state.currentPage = totalPages;
            
            const start = (state.currentPage - 1) * state.itemsPerPage;
            const end = start + state.itemsPerPage;
            const pageData = filtered.slice(start, end);

            pageData.forEach((item, index) => {
                const globalIndex = start + index + 1;
                const name = item.name || getUserName(item.userId);
                
                // Styling based on approval status
                let statusBadge = '';
                let actionButton = '';
                
                if (item.status === 'pending') {
                    statusBadge = '<span class="status-badge bg-pending">Menunggu</span>';
                    actionButton = `<button onclick="openVerifyModal(${item.id})" class="flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-semibold hover:bg-blue-100 transition-colors">
                                        <span class="material-icons-outlined text-sm">check_circle</span> Verifikasi
                                    </button>`;
                } else if (item.status === 'approved') {
                    statusBadge = '<span class="status-badge bg-approved">Disetujui</span>';
                    actionButton = `<span class="text-gray-400 text-xs italic">Selesai</span>`;
                } else if (item.status === 'rejected') {
                    statusBadge = '<span class="status-badge bg-rejected">Ditolak</span>';
                    actionButton = `<button onclick="openVerifyModal(${item.id})" class="text-blue-500 hover:text-blue-700 text-xs font-medium">Review</button>`;
                }

                // Color type for left border badge
                const typeColorMap = {
                    'Sakit': 'border-orange-400',
                    'Izin': 'border-blue-400',
                    'Cuti': 'border-red-400',
                };
                const typeClass = typeColorMap[item.type] || 'border-gray-400';

                // Desktop Row
                const cleanedReason = cleanReasonText(item.reason);

                const row = `
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="text-center text-gray-400">${globalIndex}</td>
                        <td class="font-medium text-gray-800">${name}</td>
                        <td><span class="font-semibold text-gray-700">${item.type}</span></td>
                        <td>${formatDateRange(item.dateStart, item.dateEnd)}</td>
                        <td class="max-w-xs truncate text-gray-500" title="${cleanedReason}">${cleanedReason}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">${actionButton}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);

                // Mobile Card
                const card = `
                    <div class="mobile-card border-l-4 ${typeClass}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-gray-800">${name}</h4>
                                <p class="text-xs text-gray-500 font-medium">${item.type}</p>
                            </div>
                            ${statusBadge}
                        </div>
                        <div class="text-sm mb-3">
                            <p class="text-gray-400 text-xs mb-1">Tanggal</p>
                            <p class="font-medium text-gray-700">${formatDateRange(item.dateStart, item.dateEnd)}</p>
                        </div>
                        <div class="text-sm mb-4">
                            <p class="text-gray-400 text-xs mb-1">Keterangan</p>
                            <p class="text-gray-600">${cleanedReason}</p>
                            ${item.status === 'rejected' ? `<p class="text-red-500 text-xs mt-1 italic">Alasan Ditolak: ${item.rejectionNote || '-'}</p>` : ''}
                        </div>
                        <div class="flex justify-end pt-2 border-t border-gray-100">
                            ${actionButton}
                        </div>
                    </div>
                `;
                mobileCards.insertAdjacentHTML('beforeend', card);
            });

            renderPaginationControls(pagination, totalPages, 'ketidakhadiran');
        }

        // === 7. PAGINATION HELPER ===

        function renderPaginationControls(container, totalPages, context) {
            if (totalPages <= 1) return;

            const prevBtn = `<button class="nav-btn" onclick="changePage(-1, '${context}')" ${state.currentPage === 1 ? 'disabled' : ''}><span class="material-icons-outlined text-sm">chevron_left</span></button>`;
            const nextBtn = `<button class="nav-btn" onclick="changePage(1, '${context}')" ${state.currentPage === totalPages ? 'disabled' : ''}><span class="material-icons-outlined text-sm">chevron_right</span></button>`;

            let pagesHtml = '';
            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === state.currentPage ? 'active' : '';
                pagesHtml += `<button class="page-btn ${activeClass}" onclick="goToPage(${i}, '${context}')">${i}</button>`;
            }

            container.innerHTML = prevBtn + pagesHtml + nextBtn;
        }

        function changePage(delta, context) {
            state.currentPage += delta;
            refreshCurrentView();
        }

        function goToPage(page, context) {
            state.currentPage = page;
            refreshCurrentView();
        }

        // === 8. STATS UPDATE HELPER ===

        function updateStats() {
            // Calculate stats dynamically from data
            const stats = {
                hadir: dataAbsensi.filter(i => i.status === 'Hadir').length,
                terlambat: dataAbsensi.filter(i => i.status === 'Terlambat').length,
                izin: dataKetidakhadiran.filter(i => i.type === 'Izin').length,
                cuti: dataKetidakhadiran.filter(i => i.type === 'Cuti').length,
                sakit: dataKetidakhadiran.filter(i => i.type === 'Sakit').length,
            };

            const statsData = [
                { label: 'Total Hadir', val: stats.hadir, color: 'bg-green-100 text-green-600', icon: 'check_circle' },
                { label: 'Terlambat', val: stats.terlambat, color: 'bg-yellow-100 text-yellow-600', icon: 'schedule' },
                { label: 'Izin', val: stats.izin, color: 'bg-blue-100 text-blue-600', icon: 'info' },
                { label: 'Cuti', val: stats.cuti, color: 'bg-red-100 text-red-600', icon: 'flight_takeoff' },
                { label: 'Sakit', val: stats.sakit, color: 'bg-orange-100 text-orange-600', icon: 'healing' },
            ];

            const container = document.getElementById('statsContainer');
            container.innerHTML = statsData.map(s => `
                <div class="card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">${s.label}</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">${s.val}</p>
                        </div>
                        <div class="icon-container ${s.color} bg-opacity-20">
                            <span class="material-icons-outlined text-xl">${s.icon}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // === 9. MODAL & ACTIONS ===

        function openVerifyModal(id) {
            const item = dataKetidakhadiran.find(i => i.id === id);
            if (!item) return;

            document.getElementById('verifyId').value = id;
            document.getElementById('verifyEmployeeName').textContent = item.name || getUserName(item.userId);
            document.getElementById('verifyLeaveType').textContent = item.type;
            document.getElementById('verifyDateRange').textContent = formatDateRange(item.dateStart, item.dateEnd);

            // Reset Form State
            const radios = document.getElementsByName('decision');
            radios[0].checked = true; // Default Approved
            toggleRejectionReason(false);
            document.getElementById('rejectionReason').value = '';

            const modal = document.getElementById('verifyModal');
            modal.classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        function toggleRejectionReason(show) {
            const container = document.getElementById('rejectionReasonContainer');
            const textarea = document.getElementById('rejectionReason');
            if (show) {
                container.classList.remove('hidden');
                textarea.setAttribute('required', 'true');
            } else {
                container.classList.add('hidden');
                textarea.removeAttribute('required');
            }
        }

        function handleVerificationSubmit(e) {
            e.preventDefault();
            const id = parseInt(document.getElementById('verifyId').value);
            const decision = document.querySelector('input[name="decision"]:checked').value;
            const reason = document.getElementById('rejectionReason').value;

            // Update Data
            const index = dataKetidakhadiran.findIndex(i => i.id === id);
            if (index !== -1) {
                dataKetidakhadiran[index].status = decision;
                dataKetidakhadiran[index].rejectionNote = decision === 'rejected' ? reason : null;
                
                // Show Notification
                const msg = decision === 'approved' ? 'Pengajuan berhasil disetujui' : 'Pengajuan ditolak';
                const type = decision === 'approved' ? 'success' : 'error';
                showNotification(msg, type);

                closeModal('verifyModal');
                renderKetidakhadiranTable();
                updateStats(); // Update numbers at top
            }
        }

        function deleteAbsensi(id) {
            showNotification('Fitur hapus data tidak tersedia. Manager Divisi hanya dapat melihat laporan absensi.', 'warning');
        }

        // === 10. UTILITIES ===

        function formatDateIndo(dateString) {
            if (!dateString) return '-';
            const raw = String(dateString).trim();
            if (!raw) return '-';

            const options = { day: 'numeric', month: 'short', year: 'numeric' };

            // Prevent timezone shift for DB date-only values (YYYY-MM-DD)
            const dateOnly = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (dateOnly) {
                const year = Number(dateOnly[1]);
                const month = Number(dateOnly[2]) - 1;
                const day = Number(dateOnly[3]);
                return new Date(year, month, day).toLocaleDateString('id-ID', options);
            }

            // Handle common SQL datetime by extracting the date part first
            const sqlDate = raw.match(/^(\d{4})-(\d{2})-(\d{2})\s+/);
            if (sqlDate) {
                const year = Number(sqlDate[1]);
                const month = Number(sqlDate[2]) - 1;
                const day = Number(sqlDate[3]);
                return new Date(year, month, day).toLocaleDateString('id-ID', options);
            }

            const parsed = new Date(raw);
            if (isNaN(parsed.getTime())) return raw;
            return parsed.toLocaleDateString('id-ID', options);
        }

        function formatDateRange(startDate, endDate) {
            if (!startDate) return '-';
            const start = formatDateIndo(startDate);
            const end = formatDateIndo(endDate || startDate);
            return `${start} - ${end}`;
        }

        function formatTime(value) {
            if (value === null || value === undefined) return '-';
            const s = String(value).trim();
            if (s === '' || s === '-' || s.toLowerCase() === 'null') return '-';

            // Treat all-zero placeholders as empty (e.g. '0000', '000000', '00:00')
            if (/^0{4}$/.test(s) || /^0{6}$/.test(s) || s === '00:00' || s === '00:00:00') return '-';

            // Common formats:
            // HH:MM or HH:MM:SS => capture HH:MM
            const colonMatch = s.match(/^(\d{2}:\d{2})(:\d{2})?$/);
            if (colonMatch) return colonMatch[1];

            // 4-digit HHMM => convert to HH:MM (e.g., 0800 -> 08:00, 0000 -> 00:00)
            const fourDigit = s.match(/^\d{4}$/);
            if (fourDigit) return s.slice(0,2) + ':' + s.slice(2,4);

            // 6-digit HHMMSS => take first four digits
            const sixDigit = s.match(/^\d{6}$/);
            if (sixDigit) return s.slice(0,2) + ':' + s.slice(2,4);

            // ISO or datetime with time part: try to extract time with regex
            const datetimeMatch = s.match(/(\d{2}:\d{2})(:\d{2})?/);
            if (datetimeMatch) return datetimeMatch[1];

            // Fallback: attempt Date parsing
            const d = new Date(s);
            if (!isNaN(d.getTime())) {
                const hh = String(d.getHours()).padStart(2, '0');
                const mm = String(d.getMinutes()).padStart(2, '0');
                return `${hh}:${mm}`;
            }

            // As last resort, return first 5 chars (may normalize something like "0000" -> "0000" so ensure format)
            const fallback = s.slice(0,5);
            if (/^\d{4}$/.test(s)) return s.slice(0,2) + ':' + s.slice(2,4);
            return fallback || '-';
        }

        function displayRaw(value) {
            if (value === null || value === undefined) return '-';
            const s = String(value).trim();
            if (s === '' || s === '-') return '-';

            // Prefer extracting HH:MM if any time portion exists in the string
            const timeMatch = s.match(/(\d{1,2}):(\d{2})(?::\d{2})?/);
            if (timeMatch) {
                const hh = String(timeMatch[1]).padStart(2, '0');
                const mm = timeMatch[2];
                return `${hh}:${mm}`;
            }

            // Handle compact HHMM or HHMMSS anywhere in the string (e.g., '0800' or '080000')
            const compactMatch = s.match(/(\d{2})(\d{2})(\d{2})?/);
            if (compactMatch && s.length <= 6) {
                return compactMatch[1] + ':' + compactMatch[2];
            }

            // No time found — return the raw DB value (date or other) so it's preserved
            return s;
        }

        function formatLateDuration(totalMinutes) {
            const minutes = Number(totalMinutes || 0);
            if (!minutes || minutes <= 0) return '0 menit';

            const hours = Math.floor(minutes / 60);
            const remain = minutes % 60;
            const parts = [];

            if (hours > 0) parts.push(`${hours} jam`);
            if (remain > 0 || parts.length === 0) parts.push(`${remain} menit`);

            return parts.join(' ');
        }


        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            const icons = {
                success: 'check_circle',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            const notification = document.createElement('div');
            notification.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-full opacity-0`;
            notification.innerHTML = `
                <span class="material-icons-outlined text-xl">${icons[type]}</span>
                <span class="font-medium text-sm">${message}</span>
            `;

            container.appendChild(notification);

            // Animate in
            requestAnimationFrame(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
            });

            // Remove after 3s
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
    
    <!-- ALPINE JS -->
    <script src="//unpkg.com/alpinejs" defer></script>
            </div>
        </main>
    </div>
</body>
</html>
