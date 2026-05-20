<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Kelola Absensi - Dashboard Manajer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        .material-icons-outlined { font-size: 24px; vertical-align: middle; }
        .card { transition: all 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
        
        /* Badge Status */
        .status-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; }
        .status-hadir { background-color: rgba(16, 185, 129, 0.15); color: #065f46; }
        .status-terlambat { background-color: rgba(245, 158, 11, 0.15); color: #92400e; }
        .status-izin { background-color: rgba(59, 130, 246, 0.15); color: #1e40af; }
        .status-cuti { background-color: rgba(239, 68, 68, 0.15); color: #991b1b; }
        .status-sakit { background-color: rgba(251, 146, 60, 0.15); color: #9a3412; }
        .status-dinas-luar { background-color: rgba(139, 92, 246, 0.15); color: #5b21b6; }
        .status-tidak-masuk { background-color: rgba(239, 68, 68, 0.15); color: #991b1b; }
        .status-belum-absen { background-color: rgba(156, 163, 175, 0.15); color: #4b5563; }
        
        /* Status Approval */
        .status-pending { background-color: rgba(245, 158, 11, 0.15); color: #92400e; }
        .status-approved { background-color: rgba(16, 185, 129, 0.15); color: #065f46; }
        .status-rejected { background-color: rgba(239, 68, 68, 0.15); color: #991b1b; }
        
        .icon-container { display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; }
        
        /* Table Styles */
        .data-table { width: 100%; border-collapse: collapse; min-width: 800px; /* Force scroll on mobile */ }
        .data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom:1px solid #e2e8f0; }
        .data-table th { background: #f8fafc; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; }
        .data-table tbody tr:nth-child(even) { background: #f9fafb; }
        .data-table tbody tr:hover { background: #f3f4f6; }
        
        /* Scrollable Container */
        .scrollable-table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .form-input { border:1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: all 0.2s ease; width: 100%; }
        .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
        
        /* Tab Navigation */
        .tab-nav { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom:1.5rem; overflow-x: auto; white-space: nowrap; }
        .tab-button { padding: 0.75rem 1.5rem; background: none; border: none; font-size: 0.875rem; font-weight: 500; color: #6b7280; cursor: pointer; position: relative; }
        .tab-button.active { color: #3b82f6; font-weight: 600; }
        .tab-button.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background-color: #3b82f6; }
        
        /* Panel */
        .panel { background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); overflow: hidden; margin-bottom:1.5rem; }
        .panel-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .panel-title { font-size: 1.125rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
        .panel-body { padding: 1.5rem; }
        
        /* Filter Dropdown */
        .filter-dropdown { display: none; position: absolute; top: 100%; left: 0; right: 0; md:right: auto; background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 50; min-width: 200px; padding: 1rem; max-height: 400px; overflow-y: auto; }
        .filter-dropdown.show { display: block; }
        .filter-option { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; }
        .filter-actions { display: flex; gap: 0.5rem; margin-top: 1rem; }
        .filter-apply, .filter-reset { padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; cursor: pointer; }
        .filter-apply { background-color: #3b82f6; color: white; border: none; }
        .filter-reset { background-color: #e5e7eb; color: #374151; border: none; }
        
        /* Responsive Views */
        .desktop-table { display: block; }
        .mobile-cards { display: none; }
        
        .desktop-pagination { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top:1.5rem; }
        .mobile-pagination { display: none; align-items: center; justify-content: center; gap: 0.5rem; margin-top:1.5rem; }

        /* MEDIA QUERIES - Perbaikan Layout */
        @media (max-width: 768px) {
            .desktop-table { display: none; }
            .mobile-cards { display: block; }
            .desktop-pagination { display: none; }
            .mobile-pagination { display: flex; }
            
            /* Pada Mobile, hilangkan margin kiri agar lebar penuh */
            main { 
                margin-left: 0 !important; 
                padding-left: 1rem !important; 
                padding-right: 1rem !important; 
            }
        }
        
        /* Mobile Card Styles */
        .mobile-card { background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; }
        .mobile-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .mobile-card-title { font-weight: 600; font-size: 1rem; color: #1f2937; }
        .mobile-card-body { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .mobile-card-item { display: flex; flex-direction: column; overflow: hidden; }
        .mobile-card-label { font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem; }
        .mobile-card-value { font-weight: 500; font-size: 0.875rem; color: #374151; word-break: break-word; }
        
        /* Pagination Buttons */
        .desktop-nav-btn, .mobile-nav-btn { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .desktop-nav-btn:disabled, .mobile-nav-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .desktop-page-btn, .mobile-page-btn { padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white; cursor: pointer; font-size: 0.875rem; min-width: 2.5rem; }
        .desktop-page-btn.active, .mobile-page-btn.active { background-color: #3b82f6; color: white; border-color: #3b82f6; }
        
        /* Modal */
        .modal { display: none; }
        .modal.hidden { display: none; }
        
        /* Colors */
        .bg-primary { background-color: #3b82f6; }
        .bg-danger { background-color: #ef4444; }
        .text-primary { color: #3b82f6; }
        .border-border-light { border-color: #e2e8f0; }
        .text-text-muted-light { color: #6b7280; }
    </style>
</head>

<body class="text-gray-800">
    <!-- Jangan dihapus: Memanggil Sidebar/Header -->
    @include('general_manajer/templet/header')
    
    <!-- Main Content -->
    <!-- Margin-left diset 280px untuk Desktop. Akan di-override jadi 0 di Mobile oleh CSS di atas -->
    <main class="p-4 sm:p-6 lg:p-8 transition-all duration-300" style="margin-left: 280px;">
        <div class="max-w-[1920px] mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Kelola Absensi</h1>
                    @if(isset($selectedDivision) && $selectedDivision)
                        <p class="text-sm text-blue-600 mt-1 flex items-center gap-1">
                            <span class="material-icons-outlined text-sm">filter_list</span>
                            Menampilkan untuk divisi: <span class="font-semibold">{{ $selectedDivision }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <!-- Total Kehadiran Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex items-center">
                        <div class="icon-container bg-green-100 mr-3 shrink-0">
                            <span class="material-icons-outlined text-green-600 text-xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Total Kehadiran</p>
                            <p class="text-xl md:text-2xl font-bold text-green-600" id="totalKehadiran">0</p>
                        </div>
                        <div class="icon-container bg-green-100">
                            <span class="material-icons-outlined text-green-600 text-xl">check_circle</span>
                        </div>
                    </div>
                </div>

                <!-- Tidak Hadir Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex items-center">
                        <div class="icon-container bg-red-100 mr-3 shrink-0">
                            <span class="material-icons-outlined text-red-600 text-xl">cancel</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Tidak Hadir</p>
                            <p class="text-xl md:text-2xl font-bold text-red-600" id="totalTidakHadir">0</p>
                        </div>
                        <div class="icon-container bg-red-100">
                            <span class="material-icons-outlined text-red-600 text-xl">cancel</span>
                        </div>
                    </div>
                </div>

                <!-- Izin Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex items-center">
                        <div class="icon-container bg-blue-100 mr-3 shrink-0">
                            <span class="material-icons-outlined text-blue-600 text-xl">error</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Izin</p>
                            <p class="text-xl md:text-2xl font-bold text-blue-600" id="totalIzin">0</p>
                        </div>
                        <div class="icon-container bg-blue-100">
                            <span class="material-icons-outlined text-blue-600 text-xl">error</span>
                        </div>
                    </div>
                </div>

                <!-- Cuti Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex items-center">
                        <div class="icon-container bg-yellow-100 mr-3 shrink-0">
                            <span class="material-icons-outlined text-yellow-600 text-xl">event_busy</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Cuti</p>
                            <p class="text-xl md:text-2xl font-bold text-yellow-600" id="totalCuti">0</p>
                        </div>
                        <div class="icon-container bg-yellow-100">
                            <span class="material-icons-outlined text-yellow-600 text-xl">event_busy</span>
                        </div>
                    </div>
                </div>

                <!-- Sakit Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex items-center">
                        <div class="icon-container bg-orange-100 mr-3 shrink-0">
                            <span class="material-icons-outlined text-orange-600 text-xl">healing</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Sakit</p>
                            <p class="text-xl md:text-2xl font-bold text-orange-600" id="totalSakit">0</p>
                        </div>
                        <div class="icon-container bg-orange-100">
                            <span class="material-icons-outlined text-orange-600 text-xl">healing</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-nav">
                <button id="absensiTab" class="tab-button active" onclick="switchTab('absensi')">
                    <span class="material-icons-outlined align-middle mr-2 text-base">fact_check</span>
                    Data Absensi
                </button>
                <button id="ketidakhadiranTab" class="tab-button" onclick="switchTab('ketidakhadiran')">
                    <span class="material-icons-outlined align-middle mr-2 text-base">assignment_late</span>
                    Daftar Ketidakhadiran
                </button>
            </div>

            <!-- Search and Filter Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div class="relative w-full md:w-1/3 z-20">
                    <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                    <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari nama karyawan..." type="text" />
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto z-20">
                    <div class="relative w-full md:w-auto">
                        <button id="filterBtn" class="w-full md:w-auto px-4 py-2 bg-white border border-border-light text-text-muted-light rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                            <span class="material-icons-outlined text-sm">filter_list</span>
                            Filter
                        </button>
                        <div id="filterDropdown" class="filter-dropdown">
                            <div class="filter-option">
                                <input type="checkbox" id="filterAll" value="all" checked>
                                <label for="filterAll">Semua Status</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterHadir" value="hadir">
                                <label for="filterHadir">Hadir</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterTerlambat" value="terlambat">
                                <label for="filterTerlambat">Terlambat</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterTidakHadir" value="tidak hadir">
                                <label for="filterTidakHadir">Tidak Hadir</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterBelumAbsen" value="belum absen">
                                <label for="filterBelumAbsen">Belum Absen</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterIzin" value="izin">
                                <label for="filterIzin">Izin</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterCuti" value="cuti">
                                <label for="filterCuti">Cuti</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterDinasLuar" value="dinas luar">
                                <label for="filterDinasLuar">Dinas Luar</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterSakit" value="sakit">
                                <label for="filterSakit">Sakit</label>
                            </div>
                            <div class="filter-actions">
                                <button id="applyFilter" class="filter-apply w-full">Terapkan</button>
                                <button id="resetFilter" class="filter-reset w-full">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="relative w-full md:w-auto">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">date_range</span>
                        <input id="dateFilter" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Pilih tanggal" type="date" />
                    </div>
                </div>
            </div>

            <!-- Data Absensi Panel -->
            <div id="absensiPanel" class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <span class="material-icons-outlined text-primary">fact_check</span>
                        Data Absensi
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Total: <span id="totalCount" class="font-semibold text-gray-800">{{ $formattedAbsensi->count() ?? 0 }}</span> data</span>
                    </div>
                </div>
                <div class="panel-body">
                    <!-- Desktop Table View -->
                    <div class="desktop-table">
                        <div class="scrollable-table-container rounded-lg border border-gray-200">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 50px;">No</th>
                                        <th style="min-width: 150px;">Nama</th>
                                        <th style="min-width: 120px;">Tanggal</th>
                                        <th style="min-width: 120px;">Jam Masuk</th>
                                        <th style="min-width: 120px;">Jam Keluar</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 80px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="absensiTableBody">
                                    @forelse ($formattedAbsensi as $i => $absen)
                                        <tr class="absensi-row" 
                                            data-id="{{ $absen['id'] ?? '' }}"
                                            data-status="{{ strtolower($absen['status_kehadiran'] ?? ($absen['status_label'] ?? 'hadir')) }}">
                                            
                                            <td>{{ $i + 1 }}</td>
                                            <td class="font-medium text-gray-900">{{ $absen['user_name'] ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($absen['tanggal'] ?? now())->format('d/m/Y') }}</td>
                                            <td>{{ $absen['jam_masuk'] ?? '-' }}</td>
                                            <td>{{ $absen['jam_pulang'] ?? '-' }}</td>
                                            <td>
                                                <span class="status-badge {{ $absen['status_class'] ?? 'status-hadir' }}">
                                                    {{ $absen['status_kehadiran'] ?? ($absen['status_label'] ?? 'Hadir') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if(isset($absen['attendance']) && $absen['attendance'] !== null)
                                                    <button class="delete-absensi-btn text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50" data-id="{{ $absen['id'] }}" title="Hapus">
                                                        <span class="material-icons-outlined text-sm">delete</span>
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-8 text-gray-500">Tidak ada data absensi ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Desktop Pagination -->
                        <div id="absensiPagination" class="desktop-pagination">
                            <button id="absensiPrevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="absensiPageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="absensiNextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-cards" id="absensiMobileCards">
                        @forelse ($formattedAbsensi as $i => $absen)
                            <div class="mobile-card absensi-card" 
                                data-id="{{ $absen['id'] ?? '' }}"
                                data-status="{{ strtolower($absen['status_kehadiran'] ?? ($absen['status_label'] ?? 'hadir')) }}">
                                <div class="mobile-card-header">
                                    <div class="mobile-card-title">{{ $absen['user_name'] ?? '-' }}</div>
                                    <div>
                                        <span class="status-badge {{ $absen['status_class'] ?? 'status-hadir' }}">
                                            {{ $absen['status_kehadiran'] ?? ($absen['status_label'] ?? 'Hadir') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mobile-card-body">
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">No</span>
                                        <span class="mobile-card-value">{{ $i + 1 }}</span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Tanggal</span>
                                        <span class="mobile-card-value">{{ \Carbon\Carbon::parse($absen['tanggal'] ?? now())->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Jam Masuk</span>
                                        <span class="mobile-card-value">{{ $absen['jam_masuk'] ?? '-' }}</span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Jam Keluar</span>
                                        <span class="mobile-card-value">{{ $absen['jam_pulang'] ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-2 mt-3 pt-3 border-t border-gray-100">
                                    @if(isset($absen['attendance']) && $absen['attendance'] !== null)
                                        <button class="delete-absensi-btn text-gray-500 hover:text-red-600 p-2 rounded-full hover:bg-red-50" data-id="{{ $absen['id'] }}" title="Hapus">
                                            <span class="material-icons-outlined text-sm">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">Tidak ada data absensi ditemukan.</div>
                        @endforelse
                    </div>

                    <!-- Mobile Pagination -->
                    <div id="absensiMobilePagination" class="mobile-pagination">
                        <button id="absensiMobilePrevPage" class="mobile-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_left</span>
                        </button>
                        <div id="absensiMobilePageNumbers" class="flex gap-1">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="absensiMobileNextPage" class="mobile-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ketidakhadiran Panel (Initially Hidden) -->
            <div id="ketidakhadiranPanel" class="panel hidden">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <span class="material-icons-outlined text-primary">assignment_late</span>
                        Daftar Ketidakhadiran
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Total: <span id="totalCount2" class="font-semibold text-gray-800">{{ $ketidakhadiran->count() ?? 0 }}</span> data</span>
                    </div>
                </div>
                <div class="panel-body">
                    <!-- Desktop Table View -->
                    <div class="desktop-table">
                        <div class="scrollable-table-container rounded-lg border border-gray-200">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 50px;">No</th>
                                        <th style="min-width: 180px;">Nama</th>
                                        <th style="min-width: 120px;">Tanggal Mulai</th>
                                        <th style="min-width: 120px;">Tanggal Akhir</th>
                                        <th style="min-width: 140px;">Jenis Ketidakhadiran</th>
                                        <th style="min-width: 200px;">Keterangan</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 100px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="ketidakhadiranTableBody">
                                    @forelse ($ketidakhadiran as $index => $absen)
                                        <tr class="ketidakhadiran-row" 
                                            data-id="{{ $absen->id }}"
                                            data-nama="{{ $absen->user->name ?? '-' }}"
                                            data-tanggal="{{ $absen->tanggal }}"
                                            data-tanggal-akhir="{{ $absen->tanggal_akhir }}"
                                            data-reason="{{ $absen->reason ?? '-' }}" 
                                            data-status="{{ $absen->approval_status }}">

                                            <td>{{ $index + 1 }}</td>
                                            <td class="font-medium text-gray-900">{{ $absen->user->name ?? '-' }}</td>
                                            <td>{{ $absen->tanggal ? $absen->tanggal->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                {{ $absen->tanggal_akhir ? $absen->tanggal_akhir->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <span class="font-medium text-gray-700">
                                                    {{ $absen->jenis_ketidakhadiran_label ?? ucfirst($absen->jenis_ketidakhadiran) ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex flex-col">
                                                    <!-- PERBAIKAN: Menggunakan field 'reason' dari database -->
                                                    <span class="text-sm text-gray-600 truncate max-w-xs" title="{{ $absen->reason ?? '-' }}">
                                                        {{ $absen->reason ?? '-' }}
                                                    </span>
                                                    @if($absen->approval_status === 'rejected' && $absen->rejection_reason)
                                                        <span class="text-xs text-red-600 mt-1" title="Alasan Ditolak: {{ $absen->rejection_reason }}">
                                                            ❌ {{ $absen->rejection_reason }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ strtolower($absen->approval_status) }}">
                                                    {{ $absen->approval_status_label ?? strtoupper($absen->approval_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex justify-center space-x-2">
                                                    @if ($absen->approval_status === 'pending')
                                                        <button class="verify-btn text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50" data-id="{{ $absen->id }}" title="Verifikasi">
                                                            <span class="material-icons-outlined text-sm">check_circle</span>
                                                        </button>
                                                    @endif
                                                    <button class="delete-cuti-btn text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50" data-id="{{ $absen->id }}" title="Hapus">
                                                        <span class="material-icons-outlined text-sm">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-8 text-gray-500">Tidak ada data ketidakhadiran ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Desktop Pagination -->
                        <div id="ketidakhadiranPagination" class="desktop-pagination">
                            <button id="ketidakhadiranPrevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="ketidakhadiranPageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="ketidakhadiranNextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-cards" id="ketidakhadiranMobileCards">
                        @forelse ($ketidakhadiran as $index => $absen)
                            <div class="mobile-card ketidakhadiran-card" data-id="{{ $absen->id }}">
                                <div class="mobile-card-header">
                                    <div class="mobile-card-title">{{ $absen->user->name ?? '-' }}</div>
                                    <div>
                                        <span class="status-badge status-{{ strtolower($absen->approval_status) }}">
                                            {{ $absen->approval_status_label ?? strtoupper($absen->approval_status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mobile-card-body">
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">No</span>
                                        <span class="mobile-card-value">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Tanggal</span>
                                        <span class="mobile-card-value">
                                            {{ $absen->tanggal ? $absen->tanggal->format('d/m/Y') : '-' }}
                                            @if ($absen->tanggal_akhir)
                                                - {{ $absen->tanggal_akhir->format('d/m/Y') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Jenis</span>
                                        <span class="mobile-card-value">{{ $absen->jenis_ketidakhadiran_label ?? ucfirst($absen->jenis_ketidakhadiran) ?? '-' }}</span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Status</span>
                                        <span class="mobile-card-value">{{ $absen->approval_status_label ?? strtoupper($absen->approval_status) }}</span>
                                    </div>
                                    <!-- PERBAIKAN: Menggunakan field 'reason' -->
                                    <div class="mobile-card-item col-span-2">
                                        <span class="mobile-card-label">Keterangan</span>
                                        <span class="mobile-card-value text-sm text-gray-700 block">{{ $absen->reason ?? '-' }}</span>
                                        @if($absen->approval_status === 'rejected' && $absen->rejection_reason)
                                            <div class="text-xs text-red-600 mt-1 font-medium">
                                                Ditolak: {{ $absen->rejection_reason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-2 mt-3 pt-3 border-t border-gray-100">
                                    @if ($absen->approval_status === 'pending')
                                        <button class="verify-btn text-gray-500 hover:text-green-600 p-2 rounded-full hover:bg-green-50" data-id="{{ $absen->id }}" title="Verifikasi">
                                            <span class="material-icons-outlined text-sm">check_circle</span>
                                        </button>
                                    @endif
                                    <button class="delete-cuti-btn text-gray-500 hover:text-red-600 p-2 rounded-full hover:bg-red-50" data-id="{{ $absen->id }}" title="Hapus">
                                        <span class="material-icons-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">Tidak ada data ketidakhadiran ditemukan.</div>
                        @endforelse
                    </div>

                    <!-- Mobile Pagination -->
                    <div id="ketidakhadiranMobilePagination" class="mobile-pagination">
                        <button id="ketidakhadiranMobilePrevPage" class="mobile-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_left</span>
                        </button>
                        <div id="ketidakhadiranMobilePageNumbers" class="flex gap-1">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="ketidakhadiranMobileNextPage" class="mobile-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Verify Modal -->
    <div id="verifyModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto m-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Verifikasi Pengajuan</h3>
                    <button class="close-modal text-gray-500 hover:text-gray-700 transition-colors">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            </div>
            <form id="verifyForm" class="p-6">
                <input type="hidden" id="verifyId">
                <input type="hidden" id="verifyType">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 text-gray-700">Status Persetujuan</label>
                    <select id="verifyStatus" name="approval_status" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div class="mb-6" id="rejectionReasonContainer" style="display: none;">
                    <label class="block text-sm font-medium mb-2 text-gray-700">Alasan Penolakan</label>
                    <textarea id="rejectionReason" name="rejection_reason" rows="3" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none" placeholder="Masukkan alasan penolakan"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" class="cancel-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                        <span class="material-icons-outlined text-sm align-middle mr-2">check_circle</span>Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto m-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Konfirmasi Hapus</h3>
                    <button class="close-modal text-gray-500 hover:text-gray-700 transition-colors">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="icon-container bg-red-100 mr-4" style="width: 3.5rem; height: 3.5rem; border-radius: 50%;">
                        <span class="material-icons-outlined text-red-600 text-3xl">warning</span>
                    </div>
                    <div>
                        <p class="font-semibold text-lg text-gray-900">Apakah Anda yakin?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <input type="hidden" id="deleteId">
                <input type="hidden" id="deleteType">

                <div class="flex justify-end space-x-3">
                    <button class="cancel-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                    <button id="confirmDeleteBtn" class="px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600 transition-colors flex items-center">
                        <span class="material-icons-outlined text-sm align-middle mr-2">delete</span>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none"></div>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentPageAbsensi = 1;
            let currentPageKetidakhadiran = 1;
            const itemsPerPage = 5;
            let activeFilters = ['all'];
            let searchTerm = '';

            const absensiRows = document.querySelectorAll('.absensi-row');
            const absensiCards = document.querySelectorAll('.absensi-card');
            const ketidakhadiranRows = document.querySelectorAll('.ketidakhadiran-row');
            const ketidakhadiranCards = document.querySelectorAll('.ketidakhadiran-card');

            // Hitung statistik dari data yang ada
            calculateStatistics();
            initializePagination();
            initializeFilter();
            initializeSearch();

            function calculateStatistics() {
                let totalKehadiran = 0;
                let totalTidakHadir = 0;
                let totalIzin = 0;
                let totalCuti = 0;
                let totalSakit = 0;

                // Hitung dari data absensi
                absensiRows.forEach(row => {
                    const status = row.getAttribute('data-status')?.toLowerCase() || '';
                    // Hitung hadir dan terlambat sebagai kehadiran
                    if (status.includes('tepat waktu') || status.includes('hadir') || status.includes('terlambat')) {
                        totalKehadiran++;
                    } else if (status.includes('tidak hadir')) {
                        totalTidakHadir++;
                    } else if (status.includes('izin')) {
                        totalIzin++;
                    } else if (status.includes('cuti')) {
                        totalCuti++;
                    } else if (status.includes('sakit')) {
                        totalSakit++;
                    }
                });

                // Hitung dari data ketidakhadiran
                ketidakhadiranRows.forEach(row => {
                    const alasan = row.querySelector('td:nth-child(5)')?.textContent?.toLowerCase() || '';
                    if (alasan.includes('izin')) {
                        totalIzin++;
                    } else if (alasan.includes('cuti')) {
                        totalCuti++;
                    } else if (alasan.includes('sakit')) {
                        totalSakit++;
                    } else if (alasan.includes('tidak masuk')) {
                        totalTidakHadir++;
                    }
                });

                // Update tampilan statistik
                document.getElementById('totalKehadiran').textContent = totalKehadiran;
                document.getElementById('totalTidakHadir').textContent = totalTidakHadir;
                document.getElementById('totalIzin').textContent = totalIzin;
                document.getElementById('totalCuti').textContent = totalCuti;
                document.getElementById('totalSakit').textContent = totalSakit;
            }

            function showNotification(message, type = 'success') {
                const container = document.getElementById('notificationContainer');
                const notification = document.createElement('div');

                let icon, bgColor;
                switch (type) {
                    case 'success': icon = 'check_circle'; bgColor = 'bg-green-600'; break;
                    case 'error': icon = 'error'; bgColor = 'bg-red-600'; break;
                    case 'warning': icon = 'warning'; bgColor = 'bg-yellow-500'; break;
                    default: icon = 'info'; bgColor = 'bg-blue-600';
                }

                notification.className = `notification pointer-events-auto transform transition-all duration-300 translate-x-10 opacity-0 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg mb-2 flex items-center min-w-[300px]`;
                notification.innerHTML = `<span class="material-icons-outlined mr-3 text-xl">${icon}</span><span class="font-medium text-sm">${message}</span>`;
                container.appendChild(notification);

                requestAnimationFrame(() => notification.classList.remove('translate-x-10', 'opacity-0'));
                setTimeout(() => {
                    notification.classList.add('translate-x-10', 'opacity-0');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    const form = modal.querySelector('form');
                    if (form) form.reset();
                    const rejectionContainer = document.getElementById('rejectionReasonContainer');
                    if (rejectionContainer) rejectionContainer.style.display = 'none';
                }
            }

            document.querySelectorAll('.close-modal, .cancel-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const modal = e.target.closest('[id$="Modal"]');
                    if (modal) closeModal(modal.id);
                });
            });

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(modal.id); });
            });

            document.getElementById('verifyStatus')?.addEventListener('change', function() {
                const rejectionReasonContainer = document.getElementById('rejectionReasonContainer');
                if (this.value === 'rejected') rejectionReasonContainer.style.display = 'block';
                else {
                    rejectionReasonContainer.style.display = 'none';
                    document.getElementById('rejectionReason').value = '';
                }
            });

            function initializePagination() {
                renderPaginationAbsensi();
                renderPaginationKetidakhadiran();
                updateVisibleItemsAbsensi();
                updateVisibleItemsKetidakhadiran();

                const setupNav = (prevId, nextId, tableType, pageVarRef) => {
                    document.getElementById(prevId).addEventListener('click', () => {
                        if (pageVarRef.val > 1) {
                            pageVarRef.val--;
                            refreshPagination(tableType);
                        }
                    });
                    document.getElementById(nextId).addEventListener('click', () => {
                        const totalRows = tableType === 'absensi' ? getFilteredRowsAbsensi().length : getFilteredRowsKetidakhadiran().length;
                        const totalPages = Math.ceil(totalRows / itemsPerPage);
                        if (pageVarRef.val < totalPages) {
                            pageVarRef.val++;
                            refreshPagination(tableType);
                        }
                    });
                };

                // Using object wrapper for variables to pass by reference easily
                const absensiRef = { val: currentPageAbsensi };
                const ketidRef = { val: currentPageKetidakhadiran };

                setupNav('absensiPrevPage', 'absensiNextPage', 'absensi', absensiRef);
                setupNav('absensiMobilePrevPage', 'absensiMobileNextPage', 'absensi', absensiRef);
                setupNav('ketidakhadiranPrevPage', 'ketidakhadiranNextPage', 'ketidakhadiran', ketidRef);
                setupNav('ketidakhadiranMobilePrevPage', 'ketidakhadiranMobileNextPage', 'ketidakhadiran', ketidRef);

                // Update global variables from refs after click
                const refreshPagination = (type) => {
                    if(type === 'absensi') { currentPageAbsensi = absensiRef.val; renderPaginationAbsensi(); updateVisibleItemsAbsensi(); }
                    else { currentPageKetidakhadiran = ketidRef.val; renderPaginationKetidakhadiran(); updateVisibleItemsKetidakhadiran(); }
                };
            }

            function renderPaginationAbsensi() {
                const visibleRows = getFilteredRowsAbsensi();
                const totalPages = Math.ceil(visibleRows.length / itemsPerPage) || 1;
                const createButtons = (containerId) => {
                    const container = document.getElementById(containerId);
                    container.innerHTML = '';
                    for (let i = 1; i <= totalPages; i++) {
                        const btn = document.createElement('button');
                        btn.textContent = i;
                        btn.className = `desktop-page-btn ${i === currentPageAbsensi ? 'active' : ''}`; // Reuse class style
                        if(containerId.includes('mobile')) btn.className = `mobile-page-btn ${i === currentPageAbsensi ? 'active' : ''}`;
                        
                        btn.addEventListener('click', () => {
                            currentPageAbsensi = i;
                            renderPaginationAbsensi();
                            updateVisibleItemsAbsensi();
                        });
                        container.appendChild(btn);
                    }
                };
                createButtons('absensiPageNumbers');
                createButtons('absensiMobilePageNumbers');
                
                const setBtnState = (id, cond) => document.getElementById(id).disabled = cond;
                setBtnState('absensiPrevPage', currentPageAbsensi === 1);
                setBtnState('absensiNextPage', currentPageAbsensi === totalPages);
                setBtnState('absensiMobilePrevPage', currentPageAbsensi === 1);
                setBtnState('absensiMobileNextPage', currentPageAbsensi === totalPages);
            }

            function renderPaginationKetidakhadiran() {
                const visibleRows = getFilteredRowsKetidakhadiran();
                const totalPages = Math.ceil(visibleRows.length / itemsPerPage) || 1;
                const createButtons = (containerId) => {
                    const container = document.getElementById(containerId);
                    container.innerHTML = '';
                    for (let i = 1; i <= totalPages; i++) {
                        const btn = document.createElement('button');
                        btn.textContent = i;
                        btn.className = `desktop-page-btn ${i === currentPageKetidakhadiran ? 'active' : ''}`;
                        if(containerId.includes('mobile')) btn.className = `mobile-page-btn ${i === currentPageKetidakhadiran ? 'active' : ''}`;
                        
                        btn.addEventListener('click', () => {
                            currentPageKetidakhadiran = i;
                            renderPaginationKetidakhadiran();
                            updateVisibleItemsKetidakhadiran();
                        });
                        container.appendChild(btn);
                    }
                };
                createButtons('ketidakhadiranPageNumbers');
                createButtons('ketidakhadiranMobilePageNumbers');
                
                const setBtnState = (id, cond) => document.getElementById(id).disabled = cond;
                setBtnState('ketidakhadiranPrevPage', currentPageKetidakhadiran === 1);
                setBtnState('ketidakhadiranNextPage', currentPageKetidakhadiran === totalPages);
                setBtnState('ketidakhadiranMobilePrevPage', currentPageKetidakhadiran === 1);
                setBtnState('ketidakhadiranMobileNextPage', currentPageKetidakhadiran === totalPages);
            }

            function getFilteredRowsAbsensi() { return Array.from(absensiRows).filter(row => !row.classList.contains('hidden-by-filter')); }
            function getFilteredRowsKetidakhadiran() { return Array.from(ketidakhadiranRows).filter(row => !row.classList.contains('hidden-by-filter')); }
            function getFilteredCardsAbsensi() { return Array.from(absensiCards).filter(card => !card.classList.contains('hidden-by-filter')); }
            function getFilteredCardsKetidakhadiran() { return Array.from(ketidakhadiranCards).filter(card => !card.classList.contains('hidden-by-filter')); }

            function updateVisibleItemsAbsensi() {
                const visibleRows = getFilteredRowsAbsensi();
                const visibleCards = getFilteredCardsAbsensi();
                const start = (currentPageAbsensi - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                absensiRows.forEach(r => r.style.display = 'none');
                absensiCards.forEach(c => c.style.display = 'none');
                visibleRows.forEach((r, i) => { if(i >= start && i < end) r.style.display = ''; });
                visibleCards.forEach((c, i) => { if(i >= start && i < end) c.style.display = ''; });
                document.getElementById('totalCount').textContent = visibleRows.length;
            }

            function updateVisibleItemsKetidakhadiran() {
                const visibleRows = getFilteredRowsKetidakhadiran();
                const visibleCards = getFilteredCardsKetidakhadiran();
                const start = (currentPageKetidakhadiran - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                ketidakhadiranRows.forEach(r => r.style.display = 'none');
                ketidakhadiranCards.forEach(c => c.style.display = 'none');
                visibleRows.forEach((r, i) => { if(i >= start && i < end) r.style.display = ''; });
                visibleCards.forEach((c, i) => { if(i >= start && i < end) c.style.display = ''; });
                document.getElementById('totalCount2').textContent = visibleRows.length;
            }

            function initializeFilter() {
                const filterBtn = document.getElementById('filterBtn');
                const filterDropdown = document.getElementById('filterDropdown');
                
                filterBtn.addEventListener('click', (e) => { e.stopPropagation(); filterDropdown.classList.toggle('show'); });
                document.addEventListener('click', () => filterDropdown.classList.remove('show'));
                filterDropdown.addEventListener('click', (e) => e.stopPropagation());

                document.getElementById('filterAll').addEventListener('change', function() {
                    if (this.checked) document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => cb.checked = false);
                });
                document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                    cb.addEventListener('change', function() { if (this.checked) document.getElementById('filterAll').checked = false; });
                });

                document.getElementById('applyFilter').addEventListener('click', function() {
                    activeFilters = [];
                    if (document.getElementById('filterAll').checked) {
                        activeFilters.push('all');
                    } else {
                        ['filterHadir', 'filterTerlambat', 'filterTidakHadir', 'filterBelumAbsen', 'filterIzin', 'filterCuti', 'filterDinasLuar', 'filterSakit'].forEach(id => {
                            if (document.getElementById(id).checked) activeFilters.push(document.getElementById(id).value);
                        });
                    }
                    applyFilters();
                    filterDropdown.classList.remove('show');
                    const visibleCount = getFilteredRowsAbsensi().length + getFilteredRowsKetidakhadiran().length;
                    showNotification(`Filter Diterapkan: ${visibleCount} data ditemukan`, 'success');
                });

                document.getElementById('resetFilter').addEventListener('click', function() {
                    document.querySelectorAll('.filter-option input[type="checkbox"]').forEach(cb => cb.checked = false);
                    document.getElementById('filterAll').checked = true;
                    activeFilters = ['all'];
                    applyFilters();
                    filterDropdown.classList.remove('show');
                    showNotification('Filter Direset', 'success');
                });

                document.getElementById('dateFilter').addEventListener('change', function() { applyFilters(); });
            }

            function applyFilters() {
                currentPageAbsensi = 1; currentPageKetidakhadiran = 1;
                const dateFilterValue = document.getElementById('dateFilter').value;

                const processItem = (item, isCard, type) => {
                    let status, nama, tanggal, keterangan = '', jenis = '';
                    
                    if (type === 'absensi') {
                        status = item.getAttribute('data-status')?.toLowerCase() || '';
                        nama = isCard ? item.querySelector('.mobile-card-title')?.textContent?.toLowerCase() || '' : item.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
                        tanggal = isCard ? item.querySelector('.mobile-card-item:nth-child(2) .mobile-card-value')?.textContent || '' : item.querySelector('td:nth-child(3)')?.textContent || '';

                        let statusMatches = activeFilters.includes('all') || activeFilters.some(filter => {
                            if (filter === 'hadir' && (status.includes('tepat waktu') || status.includes('hadir'))) return true;
                            if (filter === 'terlambat' && status.includes('terlambat')) return true;
                            if (filter === 'tidak hadir' && status.includes('tidak hadir')) return true;
                            if (filter === 'belum absen' && status.includes('belum absen')) return true;
                            return status.includes(filter.toLowerCase());
                        });
                        
                        let dateMatches = !dateFilterValue || (tanggal.split('/').reverse().join('-') === dateFilterValue);
                        let searchMatches = !searchTerm || nama.includes(searchTerm.toLowerCase());
                        
                        if (statusMatches && dateMatches && searchMatches) item.classList.remove('hidden-by-filter');
                        else item.classList.add('hidden-by-filter');

                    } else { // ketidakhadiran
                        nama = isCard ? item.querySelector('.mobile-card-title')?.textContent?.toLowerCase() || '' : item.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
                        
                        // Ambil text dari elemen yang berisi 'reason' (PHP $absen->reason)
                        if(isCard) {
                            const ketEl = item.querySelector('.mobile-card-body .mobile-card-item:nth-child(5)');
                            keterangan = ketEl ? ketEl.textContent.toLowerCase() : '';
                            const jenisEl = item.querySelector('.mobile-card-body .mobile-card-item:nth-child(3)');
                            jenis = jenisEl ? jenisEl.textContent.toLowerCase() : '';
                        } else {
                            const ketTd = item.querySelector('td:nth-child(6)');
                            keterangan = ketTd ? ketTd.textContent.toLowerCase() : '';
                            const jenisTd = item.querySelector('td:nth-child(5)');
                            jenis = jenisTd ? jenisTd.textContent.toLowerCase() : '';
                        }

                        tanggal = isCard ? item.querySelector('.mobile-card-item:nth-child(2)')?.textContent || '' : item.querySelector('td:nth-child(3)')?.textContent || '';

                        let statusMatches = activeFilters.includes('all') || activeFilters.some(filter => {
                            if (filter === 'izin' && jenis.includes('izin')) return true;
                            if (filter === 'sakit' && jenis.includes('sakit')) return true;
                            if (filter === 'cuti' && jenis.includes('cuti')) return true;
                            if (filter === 'dinas luar' && jenis.includes('dinas')) return true;
                            if (filter === 'tidak hadir' && jenis.includes('tidak')) return true;
                            return false;
                        });

                        let dateMatches = !dateFilterValue || (tanggal.split(' - ')[0].split('/').reverse().join('-') === dateFilterValue);
                        let searchMatches = !searchTerm || (nama.includes(searchTerm.toLowerCase()) || keterangan.includes(searchTerm.toLowerCase()));

                        if (statusMatches && dateMatches && searchMatches) item.classList.remove('hidden-by-filter');
                        else item.classList.add('hidden-by-filter');
                    }
                };

                absensiRows.forEach(r => processItem(r, false, 'absensi'));
                absensiCards.forEach(c => processItem(c, true, 'absensi'));
                ketidakhadiranRows.forEach(r => processItem(r, false, 'ketidakhadiran'));
                ketidakhadiranCards.forEach(c => processItem(c, true, 'ketidakhadiran'));

                renderPaginationAbsensi(); updateVisibleItemsAbsensi();
                renderPaginationKetidakhadiran(); updateVisibleItemsKetidakhadiran();
            }

            function initializeSearch() {
                const searchInput = document.getElementById('searchInput');
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => { searchTerm = searchInput.value.trim(); applyFilters(); }, 300);
                });
            }

            document.querySelectorAll('.verify-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('verifyId').value = this.getAttribute('data-id');
                    document.getElementById('verifyStatus').value = 'approved';
                    document.getElementById('rejectionReason').value = '';
                    document.getElementById('rejectionReasonContainer').style.display = 'none';
                    openModal('verifyModal');
                });
            });

            document.getElementById('verifyForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = document.getElementById('verifyId').value;
                if (!id) return showNotification('ID verifikasi tidak ditemukan', 'error');

                const status = document.getElementById('verifyStatus').value;
                const rejectionReason = document.getElementById('rejectionReason').value;
                const formData = new FormData();
                formData.append('approval_status', status);
                
                if (status === 'rejected') {
                    if (!rejectionReason.trim()) return showNotification('Alasan penolakan harus diisi', 'error');
                    formData.append('rejection_reason', rejectionReason.trim());
                }

                try {
                    const endpoint = status === 'approved'
                        ? `/general_manajer/absensi/${id}/approve`
                        : `/general_manajer/absensi/${id}/reject`;

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        showNotification('Data berhasil diverifikasi', 'success');
                        closeModal('verifyModal');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(result.message || 'Gagal memverifikasi data', 'error');
                    }
                } catch (error) {
                    showNotification('Terjadi kesalahan', 'error');
                }
            });

            document.querySelectorAll('.delete-cuti-btn, .delete-absensi-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('deleteId').value = this.getAttribute('data-id');
                    document.getElementById('deleteType').value = this.classList.contains('delete-absensi-btn') ? 'absensi' : 'cuti';
                    openModal('deleteModal');
                });
            });

            document.getElementById('confirmDeleteBtn')?.addEventListener('click', async function() {
                const id = document.getElementById('deleteId').value;
                if (!id) return showNotification('ID hapus tidak ditemukan', 'error');
                try {
                    const response = await fetch(`/api/admin/absensi/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        showNotification('Data berhasil dihapus', 'success');
                        closeModal('deleteModal');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(result.message || 'Gagal menghapus data', 'error');
                    }
                } catch (error) {
                    showNotification('Terjadi kesalahan', 'error');
                }
            });

            window.switchTab = function(tabName) {
                document.getElementById('absensiPanel').classList.add('hidden');
                document.getElementById('ketidakhadiranPanel').classList.add('hidden');
                document.getElementById('absensiTab').classList.remove('active');
                document.getElementById('ketidakhadiranTab').classList.remove('active');

                if (tabName === 'absensi') {
                    document.getElementById('absensiPanel').classList.remove('hidden');
                    document.getElementById('absensiTab').classList.add('active');
                } else if (tabName === 'ketidakhadiran') {
                    document.getElementById('ketidakhadiranPanel').classList.remove('hidden');
                    document.getElementById('ketidakhadiranTab').classList.add('active');
                }
            };

            switchTab('absensi');
        });
    </script>
</body>
</html>
