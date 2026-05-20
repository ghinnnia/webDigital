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
        .status-tepat-waktu { background-color: rgba(34, 197, 94, 0.15); color: #166534; }
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
    @include('hr.templet.sider')
    
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
                <div id="tidakHadirCard" class="card bg-white p-4 rounded-xl shadow-md border border-gray-100 cursor-pointer">
                    <div class="flex flex-col items-center text-center gap-2">
                        <div class="icon-container bg-green-100">
                            <span class="material-icons-outlined text-green-600 text-xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Total Kehadiran</p>
                            <p class="text-xl md:text-2xl font-bold text-green-600" id="totalKehadiran">0</p>
                        </div>
                    </div>
                </div>

                <!-- Tidak Hadir Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex flex-col items-center text-center gap-2">
                        <div class="icon-container bg-red-100">
                            <span class="material-icons-outlined text-red-600 text-xl">cancel</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Tidak Hadir</p>
                            <p class="text-xl md:text-2xl font-bold text-red-600" id="totalTidakHadir">0</p>
                        </div>
                    </div>
                </div>

                <!-- Izin Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex flex-col items-center text-center gap-2">
                        <div class="icon-container bg-blue-100">
                            <span class="material-icons-outlined text-blue-600 text-xl">error</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Izin</p>
                            <p class="text-xl md:text-2xl font-bold text-blue-600" id="totalIzin">0</p>
                        </div>
                    </div>
                </div>

                <!-- Cuti Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex flex-col items-center text-center gap-2">
                        <div class="icon-container bg-yellow-100">
                            <span class="material-icons-outlined text-yellow-600 text-xl">event_busy</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Cuti</p>
                            <p class="text-xl md:text-2xl font-bold text-yellow-600" id="totalCuti">0</p>
                        </div>
                    </div>
                </div>

                <!-- Sakit Card -->
                <div class="card bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <div class="flex flex-col items-center text-center gap-2">
                        <div class="icon-container bg-orange-100">
                            <span class="material-icons-outlined text-orange-600 text-xl">healing</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 mb-1">Sakit</p>
                            <p class="text-xl md:text-2xl font-bold text-orange-600" id="totalSakit">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-nav">
                <button id="absensiTab" class="tab-button active" onclick="switchTab('absensi')">
                    <span class="material-icons-outlined align-middle mr-2 text-base">fact_check</span>
                    Daftar Kehadiran
                </button>
                <button id="ketidakhadiranTab" class="tab-button" onclick="switchTab('ketidakhadiran')">
                    <span class="material-icons-outlined align-middle mr-2 text-base">assignment_late</span>
                    Daftar Ketidakhadiran
                </button>
                <button id="cutiTab" class="tab-button" onclick="switchTab('cuti')">
                    <span class="material-icons-outlined align-middle mr-2 text-base">event_busy</span>
                    Daftar Cuti
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
                        Daftar Kehadiran
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
                                        <th style="min-width: 150px;">Keterangan</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 60px; text-align: center;">Aksi</th>
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
                                                <span class="text-sm text-gray-600 truncate max-w-xs" title="{{ $absen['keterangan'] ?? '-' }}">
                                                    {{ $absen['keterangan'] ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusLabel = $absen['status_kehadiran'] ?? ($absen['status_label'] ?? 'Hadir');
                                                    $lateMinutes = (int) ($absen['late_minutes'] ?? 0);
                                                    $lateHours = intdiv($lateMinutes, 60);
                                                    $lateRemain = $lateMinutes % 60;
                                                    $lateParts = [];
                                                    if ($lateHours > 0) $lateParts[] = $lateHours . ' jam';
                                                    if ($lateRemain > 0 || empty($lateParts)) $lateParts[] = $lateRemain . ' menit';
                                                    $lateText = implode(' ', $lateParts);
                                                @endphp
                                                <span class="status-badge {{ $absen['status_class'] ?? 'status-hadir' }}">
                                                    {{ $statusLabel }}{{ ($statusLabel === 'Terlambat' && $lateMinutes > 0) ? ' (' . $lateText . ')' : '' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex justify-center space-x-1">
                                                    @if ($absen['approval_status'] === 'pending')
                                                        <button class="verify-btn-approve text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50" data-id="{{ $absen['id'] }}" data-status="approved" title="Setujui">
                                                            <span class="material-icons-outlined text-sm">check_circle</span>
                                                        </button>
                                                        <button class="verify-btn-reject text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50" data-id="{{ $absen['id'] }}" data-status="rejected" title="Tolak">
                                                            <span class="material-icons-outlined text-sm">cancel</span>
                                                        </button>
                                                    @else
                                                        <span class="text-xs text-gray-400">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-8 text-gray-500">Tidak ada data absensi ditemukan.</td>
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
                                        @php
                                            $statusLabelMobile = $absen['status_kehadiran'] ?? ($absen['status_label'] ?? 'Hadir');
                                            $lateMinutesMobile = (int) ($absen['late_minutes'] ?? 0);
                                            $lateHoursMobile = intdiv($lateMinutesMobile, 60);
                                            $lateRemainMobile = $lateMinutesMobile % 60;
                                            $latePartsMobile = [];
                                            if ($lateHoursMobile > 0) $latePartsMobile[] = $lateHoursMobile . ' jam';
                                            if ($lateRemainMobile > 0 || empty($latePartsMobile)) $latePartsMobile[] = $lateRemainMobile . ' menit';
                                            $lateTextMobile = implode(' ', $latePartsMobile);
                                        @endphp
                                        <span class="status-badge {{ $absen['status_class'] ?? 'status-hadir' }}">
                                            {{ $statusLabelMobile }}{{ ($statusLabelMobile === 'Terlambat' && $lateMinutesMobile > 0) ? ' (' . $lateTextMobile . ')' : '' }}
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
                                    <div class="mobile-card-item col-span-2">
                                        <span class="mobile-card-label">Keterangan</span>
                                        <span class="mobile-card-value text-sm text-gray-700">{{ $absen['keterangan'] ?? '-' }}</span>
                                    </div>
                                </div>
                                @if ($absen['approval_status'] === 'pending')
                                <div class="flex justify-end space-x-2 mt-3 pt-3 border-t border-gray-100">
                                    <button class="verify-btn-approve text-gray-500 hover:text-green-600 p-2 rounded-full hover:bg-green-50" data-id="{{ $absen['id'] }}" data-status="approved" title="Setujui">
                                        <span class="material-icons-outlined text-sm">check_circle</span>
                                    </button>
                                    <button class="verify-btn-reject text-gray-500 hover:text-red-600 p-2 rounded-full hover:bg-red-50" data-id="{{ $absen['id'] }}" data-status="rejected" title="Tolak">
                                        <span class="material-icons-outlined text-sm">cancel</span>
                                    </button>
                                </div>
                                @endif
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
                                        <th style="min-width: 60px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="ketidakhadiranTableBody">
                                    @forelse ($ketidakhadiran as $index => $absen)
                                        <tr class="ketidakhadiran-row" 
                                            data-id="{{ $absen['id'] }}"
                                            data-nama="{{ $absen['user']['name'] ?? '-' }}"
                                            data-tanggal="{{ $absen['tanggal'] }}"
                                            data-tanggal-formatted="{{ $absen['tanggal'] ? date('d/m/Y', strtotime($absen['tanggal'])) : '' }}"
                                            data-tanggal-akhir="{{ $absen['tanggal_akhir'] }}"
                                            data-tanggal-akhir-formatted="{{ $absen['tanggal_akhir'] ? date('d/m/Y', strtotime($absen['tanggal_akhir'])) : '' }}"
                                            data-reason="{{ $absen['keterangan'] ?? '-' }}" 
                                            data-status="{{ $absen['approval_status'] }}">

                                            <td>{{ $index + 1 }}</td>
                                            <td class="font-medium text-gray-900">{{ $absen['user']['name'] ?? '-' }}</td>
                                            <td>{{ $absen['tanggal'] ? date('d/m/Y', strtotime($absen['tanggal'])) : '-' }}</td>
                                            <td>
                                                {{ $absen['tanggal_akhir'] ? date('d/m/Y', strtotime($absen['tanggal_akhir'])) : '-' }}
                                            </td>
                                            <td>
                                                <span class="font-medium text-gray-700">
                                                    {{ ucfirst($absen['jenis_ketidakhadiran']) ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex flex-col">
                                                    <!-- PERBAIKAN: Menggunakan field 'keterangan' dari database -->
                                                    <span class="text-sm text-gray-600 truncate max-w-xs" title="{{ $absen['keterangan'] ?? '-' }}">
                                                        {{ $absen['keterangan'] ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ strtolower($absen['approval_status']) }}">
                                                    {{ strtoupper($absen['approval_status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex justify-center space-x-1">
                                                    @if ($absen['approval_status'] === 'approved')
                                                        <span class="text-xs text-green-600">Disetujui</span>
                                                    @elseif ($absen['approval_status'] === 'rejected')
                                                        <span class="text-xs text-red-600">Ditolak</span>
                                                    @else
                                                        <span class="text-xs text-gray-400">-</span>
                                                    @endif
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
                            <div class="mobile-card ketidakhadiran-card" data-id="{{ $absen['id'] }}">
                                <div class="mobile-card-header">
                                    <div class="mobile-card-title">{{ $absen['user']['name'] ?? '-' }}</div>
                                    <div>
                                        <span class="status-badge status-{{ strtolower($absen['approval_status']) }}">
                                            {{ strtoupper($absen['approval_status']) }}
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
                                            {{ $absen['tanggal'] ? date('d/m/Y', strtotime($absen['tanggal'])) : '-' }}
                                            @if ($absen['tanggal_akhir'])
                                                - {{ date('d/m/Y', strtotime($absen['tanggal_akhir'])) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Jenis</span>
                                        <span class="mobile-card-value">{{ ucfirst($absen['jenis_ketidakhadiran']) ?? '-' }}</span>
                                    </div>
                                    <div class="mobile-card-item">
                                        <span class="mobile-card-label">Status</span>
                                        <span class="mobile-card-value">{{ strtoupper($absen['approval_status']) }}</span>
                                    </div>
                                    <div class="mobile-card-item col-span-2">
                                        <span class="mobile-card-label">Keterangan</span>
                                        <span class="mobile-card-value text-sm text-gray-700 block">{{ $absen['keterangan'] ?? '-' }}</span>
                                    </div>
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

            <!-- Cuti Panel (Initially Hidden) -->
            <div id="cutiPanel" class="panel hidden">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <span class="material-icons-outlined text-primary">event_busy</span>
                        Daftar Cuti
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Total: <span id="totalCount3" class="font-semibold text-gray-800">{{ $cuti->count() ?? 0 }}</span> data</span>
                    </div>
                </div>
                <div class="panel-body">
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="border-bottom: 2px solid #e2e8f0;">
                                    <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">No</th>
                                    <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Nama Karyawan</th>
                                    <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Tanggal</th>
                                    <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Jenis Cuti</th>
                                    <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Alasan</th>
                                    <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Status</th>
                                    <th style="text-align: center; padding: 1rem; font-weight: 600; color: #374151;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cutiTableBody">
                                <!-- Data will be loaded via API -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Desktop Pagination -->
                    <div id="cutiPagination" class="desktop-pagination">
                        <button id="cutiPrevPage" class="desktop-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_left</span>
                        </button>
                        <div id="cutiPageNumbers" class="flex gap-1">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="cutiNextPage" class="desktop-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_right</span>
                        </button>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-cards" id="cutiMobileCards">
                        <!-- Data will be loaded via API -->
                    </div>

                    <!-- Mobile Pagination -->
                    <div id="cutiMobilePagination" class="mobile-pagination">
                        <button id="cutiMobilePrevPage" class="mobile-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_left</span>
                        </button>
                        <div id="cutiMobilePageNumbers" class="flex gap-1">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="cutiMobileNextPage" class="mobile-nav-btn">
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
    // Define critical functions at window level BEFORE DOMContentLoaded
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Helper function untuk mendapatkan label status yang konsisten
    const getStatusLabel = (status) => {
        const s = String(status).toLowerCase().trim();
        if (s === 'approved' || s === 'disetujui') return 'Disetujui';
        if (s === 'rejected' || s === 'ditolak') return 'Ditolak';
        if (s === 'pending' || s === 'menunggu') return 'Menunggu';
        return status || 'Menunggu';
    };
    
    // Helper function untuk mendapatkan CSS class untuk status
    const getStatusClass = (status) => {
        const s = String(status).toLowerCase().trim();
        if (s === 'approved' || s === 'disetujui') return 'status-approved';
        if (s === 'rejected' || s === 'ditolak') return 'status-rejected';
        if (s === 'pending' || s === 'menunggu') return 'status-pending';
        return 'status-pending';
    };
    
    // Helper function untuk check apakah status pending
    const isPending = (status) => {
        const s = String(status).toLowerCase().trim();
        return s === 'pending' || s === 'menunggu';
    };
    
    // Server-side fallback data (will be injected by Blade below)
    let serverCutiData = [];
    
    // Function to update statistics cards
    window.updateStatistics = function() {
        console.log('[updateStatistics] ========== STATISTICS CALC START ==========');
        
        // USE PRE-CALCULATED VALUES FROM CONTROLLER (PRIMARY SOURCE)
        let totalKaryawan = window.totalKaryawan || 0;
        let totalKehadiran = window.hadiranCount || 0;
        let totalSakit = window.sakitCount || 0;
        let totalIzin = window.izinCount || 0;
        let totalCuti = window.cutiCount || 0;
        let totalTidakHadir = window.tidakHadirCount || 0;
        
        console.log('[updateStatistics] Using PRE-CALCULATED VALUES from controller:');
        console.log('[updateStatistics]  -  Total karyawan:', totalKaryawan);
        console.log('[updateStatistics]  -  Hadir:', totalKehadiran);
        console.log('[updateStatistics]  -  Sakit:', totalSakit);
        console.log('[updateStatistics]  -  Izin:', totalIzin);
        console.log('[updateStatistics]  -  Cuti:', totalCuti);
        console.log('[updateStatistics]  -  Tidak Hadir:', totalTidakHadir);
        
        const absensiData = window.serverAbsensiData || [];
        const ketidakhadiranData = window.serverKetidakhadiranData || [];
        const cutiData = window.serverCutiData || [];
        
        console.log('[updateStatistics] absensiData:', absensiData);
        console.log('[updateStatistics] ketidakhadiranData:', ketidakhadiranData);
        console.log('[updateStatistics] cutiData:', cutiData);
        
        const searchTerm = (document.getElementById('searchInput')?.value || '').toLowerCase();
        const selectedFilters = Array.from(document.querySelectorAll('#filterDropdown input[type="checkbox"]:checked'))
            .map(cb => cb.value)
            .filter(v => v !== 'all');
        
        const allChecked = document.getElementById('filterAll')?.checked;
        const applyFilters = !allChecked && selectedFilters.length > 0;
        
        console.log('[updateStatistics] searchTerm:', searchTerm);
        console.log('[updateStatistics] applyFilters:', applyFilters);
        console.log('[updateStatistics] absensiData length:', absensiData.length);
        console.log('[updateStatistics] ketidakhadiranData length:', ketidakhadiranData.length);
        console.log('[updateStatistics] cutiData length:', cutiData.length);
        
        // Helper function to match search term
        const matchesSearch = (name) => {
            if (!searchTerm) return true;
            return String(name || '').toLowerCase().includes(searchTerm);
        };
        
        // Helper function to check if item matches selected filters
        const matchesFilters = (item, type) => {
            if (!applyFilters) return true;
            
            if (type === 'kehadiran') {
                const status = String(item.status_kehadiran || 'hadir').toLowerCase();
                return selectedFilters.some(f => status.includes(f.toLowerCase()));
            }
            
            if (type === 'ketidakhadiran') {
                const alasan = String(item.alasan || '').toLowerCase();
                return selectedFilters.some(f => alasan.includes(f.toLowerCase()) || 
                    f === 'tidak hadir' && alasan === '');
            }
            
            if (type === 'cuti') {
                const status = String(item.status || '').toLowerCase();
                return selectedFilters.some(f => f === 'cuti' || status.includes(f.toLowerCase()));
            }
            
            return true;
        };
        
        console.log('[updateStatistics] Calculation: TidakHadir = ' + totalKaryawan + ' - (' + totalKehadiran + ' + ' + totalSakit + ' + ' + totalIzin + ' + ' + totalCuti + ') = ' + totalTidakHadir);
        console.log('[updateStatistics] Final counts:', {
            totalKaryawan, totalKehadiran, totalTidakHadir, totalIzin, totalCuti, totalSakit
        });
        
        // Update DOM elements
        const elemKehadiran = document.getElementById('totalKehadiran');
        const elemTidakHadir = document.getElementById('totalTidakHadir');
        const elemIzin = document.getElementById('totalIzin');
        const elemCuti = document.getElementById('totalCuti');
        const elemSakit = document.getElementById('totalSakit');
        
        console.log('[updateStatistics] DOM Elements found:');
        console.log('  - totalKehadiran:', elemKehadiran);
        console.log('  - totalTidakHadir:', elemTidakHadir);
        console.log('  - totalIzin:', elemIzin);
        console.log('  - totalCuti:', elemCuti);
        console.log('  - totalSakit:', elemSakit);
        
        if (elemKehadiran) {
            elemKehadiran.textContent = totalKehadiran;
            console.log('[updateStatistics] Updated totalKehadiran to:', totalKehadiran);
        } else console.log('[updateStatistics] ERROR: totalKehadiran element not found!');
        
        if (elemTidakHadir) {
            elemTidakHadir.textContent = totalTidakHadir;
            console.log('[updateStatistics] Updated totalTidakHadir to:', totalTidakHadir);
        } else console.log('[updateStatistics] ERROR: totalTidakHadir element not found!');
        
        if (elemIzin) {
            elemIzin.textContent = totalIzin;
            console.log('[updateStatistics] Updated totalIzin to:', totalIzin);
        } else console.log('[updateStatistics] ERROR: totalIzin element not found!');
        
        if (elemCuti) {
            elemCuti.textContent = totalCuti;
            console.log('[updateStatistics] Updated totalCuti to:', totalCuti);
        } else console.log('[updateStatistics] ERROR: totalCuti element not found!');
        
        if (elemSakit) {
            elemSakit.textContent = totalSakit;
            console.log('[updateStatistics] Updated totalSakit to:', totalSakit);
        } else console.log('[updateStatistics] ERROR: totalSakit element not found!');
        
        console.log('[updateStatistics] ========== STATISTICS CALC END ==========');
    };
    
    // Define loadCutiDataFromAPI at window level
    window.loadCutiDataFromAPI = async function() {
        console.log('[loadCutiDataFromAPI] Function called');
        const tbody = document.getElementById('cutiTableBody');
        const cardsContainer = document.getElementById('cutiMobileCards');
        console.log('[loadCutiDataFromAPI] tbody element:', tbody);
        console.log('[loadCutiDataFromAPI] cardsContainer element:', cardsContainer);
        
        if (!tbody || !cardsContainer) {
            console.error('[loadCutiDataFromAPI] ERROR: cutiTableBody or cutiMobileCards element not found!');
            return;
        }
        
        // Use server-injected data directly (no API call)
        const data = serverCutiData;
        
        console.log('[loadCutiDataFromAPI] Using server data:', data.length, 'items');
        console.log('[loadCutiDataFromAPI] Data detail:', data);
        
        // Clear both table and cards
        tbody.innerHTML = '';
        cardsContainer.innerHTML = '';
        
        if (data.length === 0) {
            console.log('[loadCutiDataFromAPI] No data to display');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-500">Tidak ada data cuti ditemukan.</td></tr>';
            cardsContainer.innerHTML = '<div class="text-center py-8 text-gray-500">Tidak ada data cuti ditemukan.</div>';
            return;
        }

        data.forEach((cuti, index) => {
            console.log('[loadCutiDataFromAPI] Processing cuti item:', index, cuti);
            
            const statusLabel = getStatusLabel(cuti.status);
            const badgeClass = getStatusClass(cuti.status);
            const isPendingStatus = isPending(cuti.status);

            console.log('[loadCutiDataFromAPI] Item', index, '- statusLabel:', statusLabel, 'isPending:', isPendingStatus);

            // Table Row
            const tr = document.createElement('tr');
            tr.className = 'cuti-row border-b border-gray-200 hover:bg-gray-50';
            tr.innerHTML = `
                <td style="padding:1rem">${index + 1}</td>
                <td style="padding:1rem" class="font-medium text-gray-900">${cuti.nama}</td>
                <td style="padding:1rem">${cuti.periode}</td>
                <td style="padding:1rem">${cuti.jenis_cuti || '-'}</td>
                <td style="padding:1rem"><div class="text-sm text-gray-600 truncate max-w-xs" title="${cuti.keterangan}">${cuti.keterangan}</div></td>
                <td style="padding:1rem"><span class="status-badge ${badgeClass}">${statusLabel}</span></td>
                <td style="padding:1rem"><div class="flex justify-center space-x-1">${isPendingStatus ? `
                    <button class="verify-btn-approve text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50" data-id="${cuti.id}" title="Setujui"><span class="material-icons-outlined text-sm">check_circle</span></button>
                    <button class="verify-btn-reject text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50" data-id="${cuti.id}" title="Tolak"><span class="material-icons-outlined text-sm">cancel</span></button>
                ` : '<span class="text-xs text-gray-400">-</span>'}</div></td>
            `;
            tbody.appendChild(tr);
            console.log('[loadCutiDataFromAPI] Added table row for cuti', cuti.id);

            // Mobile Card
            const card = document.createElement('div');
            card.className = 'cuti-card card mobile-card';
            card.innerHTML = `
                <div class="card-body">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="font-semibold text-gray-900">${cuti.nama}</p>
                            <p class="text-xs text-gray-500">${cuti.divisi || '-'}</p>
                        </div>
                        <span class="status-badge ${badgeClass}">${statusLabel}</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-700 mb-4">
                        <p><strong>Periode:</strong> ${cuti.periode}</p>
                        <p><strong>Jenis Cuti:</strong> ${cuti.jenis_cuti || '-'}</p>
                        <p><strong>Durasi:</strong> ${cuti.durasi}</p>
                        <p><strong>Alasan:</strong> ${cuti.keterangan}</p>
                    </div>
                    ${isPendingStatus ? `
                    <div class="flex gap-2">
                        <button class="verify-btn-approve flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" data-id="${cuti.id}">
                            <span class="material-icons-outlined text-sm">check_circle</span>
                            Setujui
                        </button>
                        <button class="verify-btn-reject flex-1 px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700" data-id="${cuti.id}">
                            <span class="material-icons-outlined text-sm">cancel</span>
                            Tolak
                        </button>
                    </div>
                    ` : ''}
                </div>
            `;
            cardsContainer.appendChild(card);
            console.log('[loadCutiDataFromAPI] Added mobile card for cuti', cuti.id);
        });
        
        console.log('[loadCutiDataFromAPI] Finished rendering', data.length, 'items');
        
        // Count and log buttons
        const approveCount = cardsContainer.querySelectorAll('.verify-btn-approve').length + tbody.querySelectorAll('.verify-btn-approve').length;
        const rejectCount = cardsContainer.querySelectorAll('.verify-btn-reject').length + tbody.querySelectorAll('.verify-btn-reject').length;
        console.log('[loadCutiDataFromAPI] Created buttons - Approve:', approveCount, 'Reject:', rejectCount);
        
        if (typeof bindVerificationButtons === 'function') bindVerificationButtons();
    };
    
    function normalizeTimeOnly(value) {
        if (!value) return '-';
        const raw = String(value).trim();
        if (!raw) return '-';

        const timeMatch = raw.match(/(\d{2}:\d{2})(?::\d{2})?/);
        if (timeMatch && timeMatch[1]) return timeMatch[1];

        const parsed = new Date(raw);
        if (!isNaN(parsed.getTime())) {
            return `${String(parsed.getHours()).padStart(2, '0')}:${String(parsed.getMinutes()).padStart(2, '0')}`;
        }

        return raw;
    }

    function resolveKeteranganAbsensi(item) {
        const directKeterangan = item?.keterangan || item?.reason;
        if (directKeterangan) return directKeterangan;

        const isEarlyCheckout = Boolean(item?.is_early_checkout || item?.attendance?.is_early_checkout);
        const earlyReason = item?.early_checkout_reason || item?.attendance?.early_checkout_reason;
        if (isEarlyCheckout && earlyReason) return earlyReason;

        return '-';
    }

    // Define loadAbsensiDataFromAPI
    window.loadAbsensiDataFromAPI = function() {
        console.log('[loadAbsensiDataFromAPI] Function called');

        const tbody = document.getElementById('absensiTableBody') || document.querySelector('[id^="absensi"] tbody');
        const cardsContainer = document.getElementById('absensiMobileCards');

        if (!tbody) {
            console.error('[loadAbsensiDataFromAPI] tbody not found');
            return;
        }

        const rawData = window.serverAbsensiData || [];
        const searchTerm = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
        const selectedDate = document.getElementById('dateFilter')?.value || '';
        const selectedFilters = Array.from(document.querySelectorAll('#filterDropdown input[type="checkbox"]:checked'))
            .map(cb => String(cb.value || '').toLowerCase())
            .filter(v => v !== 'all');
        const applyFilters = selectedFilters.length > 0 && !document.getElementById('filterAll')?.checked;

        const today = new Date();
        const todayYmd = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        const trackedIds = new Set([
            ...(window.presentIds || []),
            ...(window.sakitIds || []),
            ...(window.izinIds || []),
            ...(window.cutiIds || []),
        ].map(id => Number(id)));

        // Generate virtual rows for karyawan yang belum absen masuk hari ini
        const belumAbsenData = (!selectedDate || selectedDate === todayYmd)
            ? (window.allUsers || [])
                .filter(user => user && user.id && !trackedIds.has(Number(user.id)))
                .map(user => ({
                    id: `belum-absen-${user.id}`,
                    user_id: user.id,
                    user_name: user.name || '-',
                    tanggal: todayYmd,
                    jam_masuk: '-',
                    jam_pulang: '-',
                    keterangan: 'Belum absen masuk',
                    status_kehadiran: 'Belum Absen',
                    status_class: 'status-belum-absen',
                    late_minutes: 0,
                    is_virtual_absence: true,
                }))
            : [];

        const mergedData = [...rawData, ...belumAbsenData];

        const data = mergedData.filter(item => {
            const name = String(item.user_name || item.nama || '').toLowerCase();
            const status = String(item.status_kehadiran || '').toLowerCase();
            const rawDate = item.tanggal ? new Date(item.tanggal) : null;
            const localYmd = rawDate && !isNaN(rawDate)
                ? `${rawDate.getFullYear()}-${String(rawDate.getMonth() + 1).padStart(2, '0')}-${String(rawDate.getDate()).padStart(2, '0')}`
                : '';

            const matchSearch = !searchTerm || name.includes(searchTerm);
            const matchDate = !selectedDate || localYmd === selectedDate;
            const matchFilter = !applyFilters || selectedFilters.some(f => {
                if (f === 'hadir') return status === 'tepat waktu' || status === 'hadir';
                if (f === 'terlambat') return status.includes('terlambat');
                if (f === 'belum absen') return status.includes('belum absen');
                if (f === 'tidak hadir') return status.includes('tidak hadir') || status.includes('belum absen');
                return status.includes(f);
            });

            return matchSearch && matchDate && matchFilter;
        });

        console.log('[loadAbsensiDataFromAPI] Filtered data:', data.length, 'items from', rawData.length);

        tbody.innerHTML = '';
        if (cardsContainer) cardsContainer.innerHTML = '';

        const totalCountEl = document.getElementById('totalCount');
        if (totalCountEl) totalCountEl.textContent = String(data.length);

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-500">Tidak ada data kehadiran.</td></tr>';
            if (cardsContainer) {
                cardsContainer.innerHTML = '<div class="text-center py-8 text-gray-500">Tidak ada data kehadiran.</div>';
            }
            return;
        }

        data.forEach((item, index) => {
            const statusClass = item.status_class || 'status-hadir';
            const baseStatus = item.status_kehadiran || 'Hadir';
            const lateMinutes = Number(item.late_minutes || item.lateMinutes || 0);
            const statusText = (String(baseStatus).toLowerCase() === 'terlambat' && lateMinutes > 0)
                ? `${baseStatus} (${formatLateDuration(lateMinutes)})`
                : baseStatus;
            const tanggalFormatted = item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-';
            const jamMasuk = normalizeTimeOnly(item.jam_masuk);
            const jamPulang = normalizeTimeOnly(item.jam_pulang);
            const keterangan = resolveKeteranganAbsensi(item);

            const tr = document.createElement('tr');
            tr.className = 'absensi-row border-b hover:bg-gray-50';
            tr.innerHTML = `
                <td style="padding:1rem">${index + 1}</td>
                <td style="padding:1rem">${item.user_name || '-'}</td>
                <td style="padding:1rem">${tanggalFormatted}</td>
                <td style="padding:1rem">${jamMasuk}</td>
                <td style="padding:1rem">${jamPulang}</td>
                <td style="padding:1rem"><div class="text-sm text-gray-600 truncate max-w-xs" title="${keterangan}">${keterangan}</div></td>
                <td style="padding:1rem"><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td style="padding:1rem text-align:center;">-</td>
            `;
            tbody.appendChild(tr);

            if (cardsContainer) {
                const card = document.createElement('div');
                card.className = 'mobile-card absensi-card';
                card.innerHTML = `
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">${item.user_name || '-'}</div>
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-item"><span class="mobile-card-label">No</span><span class="mobile-card-value">${index + 1}</span></div>
                        <div class="mobile-card-item"><span class="mobile-card-label">Tanggal</span><span class="mobile-card-value">${tanggalFormatted}</span></div>
                        <div class="mobile-card-item"><span class="mobile-card-label">Jam Masuk</span><span class="mobile-card-value">${jamMasuk}</span></div>
                        <div class="mobile-card-item"><span class="mobile-card-label">Jam Keluar</span><span class="mobile-card-value">${jamPulang}</span></div>
                        <div class="mobile-card-item" style="grid-column: span 2;"><span class="mobile-card-label">Keterangan</span><span class="mobile-card-value">${keterangan}</span></div>
                    </div>
                `;
                cardsContainer.appendChild(card);
            }
        });

        console.log('[loadAbsensiDataFromAPI] Rendered', data.length, 'items');
    };

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
    
    // Define loadKetidakhadiranDataFromAPI
    window.loadKetidakhadiranDataFromAPI = function() {
        console.log('[loadKetidakhadiranDataFromAPI] Function called');
        const tbody = document.getElementById('ketidakhadiranTableBody') || document.querySelector('[id^="ketidakhadiran"] tbody');
        console.log('[loadKetidakhadiranDataFromAPI] tbody element:', tbody);
        
        if (!tbody) {
            console.error('[loadKetidakhadiranDataFromAPI] tbody not found');
            return;
        }
        
        const data = window.serverKetidakhadiranData || [];
        console.log('[loadKetidakhadiranDataFromAPI] Data:', data.length, 'items');
        console.log('[loadKetidakhadiranDataFromAPI] Data detail:', data);
        
        tbody.innerHTML = '';
        if (data.length === 0) {
            console.log('[loadKetidakhadiranDataFromAPI] No data to display');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-500">Tidak ada data ketidakhadiran.</td></tr>';
            return;
        }
        
        data.forEach((item, index) => {
            console.log('[loadKetidakhadiranDataFromAPI] Processing item:', index, item);
            
            const statusLabel = getStatusLabel(item.status);
            const statusClass = getStatusClass(item.status);
            const isPendingStatus = isPending(item.status);
            
            console.log('[loadKetidakhadiranDataFromAPI] Item', index, '- RAW STATUS:', item.status, '-> statusLabel:', statusLabel, 'isPending:', isPendingStatus);
            
            const tr = document.createElement('tr');
            tr.className = 'ketidakhadiran-row border-b hover:bg-gray-50';
            tr.innerHTML = `
                <td style="padding:1rem">${index + 1}</td>
                <td style="padding:1rem">${item.nama || '-'}</td>
                <td style="padding:1rem">${item.tanggal || '-'}</td>
                <td style="padding:1rem">${item.tanggal_akhir || '-'}</td>
                <td style="padding:1rem">${item.alasan || '-'}</td>
                <td style="padding:1rem">${item.keterangan || '-'}</td>
                <td style="padding:1rem"><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td style="padding:1rem; text-align:center;">
                    ${isPendingStatus ? (`<div class="flex justify-center space-x-1">
                        <button class="verify-btn-approve text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50" data-id="${item.id}" data-status="approved" title="Setujui">
                            <span class="material-icons-outlined text-sm">check_circle</span>
                        </button>
                        <button class="verify-btn-reject text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50" data-id="${item.id}" data-status="rejected" title="Tolak">
                            <span class="material-icons-outlined text-sm">cancel</span>
                        </button>
                    </div>`) : '<span class="text-xs text-gray-400">-</span>'}
                </td>
            `;
            tbody.appendChild(tr);
            console.log('[loadKetidakhadiranDataFromAPI] Added row for item', item.id);
        });
        
        console.log('[loadKetidakhadiranDataFromAPI] Finished rendering', data.length, 'items');
        
        // Count and log buttons
        const approveCount = tbody.querySelectorAll('.verify-btn-approve').length;
        const rejectCount = tbody.querySelectorAll('.verify-btn-reject').length;
        console.log('[loadKetidakhadiranDataFromAPI] Created buttons - Approve:', approveCount, 'Reject:', rejectCount);
    };
    
    // Define switchTab at window level
    window.switchTab = function(tabName) {
        console.log('[switchTab] Switching to tab:', tabName);
        
        document.getElementById('absensiPanel').classList.add('hidden');
        document.getElementById('ketidakhadiranPanel').classList.add('hidden');
        document.getElementById('cutiPanel').classList.add('hidden');
        document.getElementById('absensiTab').classList.remove('active');
        document.getElementById('ketidakhadiranTab').classList.remove('active');
        document.getElementById('cutiTab').classList.remove('active');
        
        if (tabName === 'absensi') { 
            console.log('[switchTab] Loading absensi tab');
            document.getElementById('absensiPanel').classList.remove('hidden'); 
            document.getElementById('absensiTab').classList.add('active');
            loadAbsensiDataFromAPI();
        }
        else if (tabName === 'ketidakhadiran') { 
            console.log('[switchTab] Loading ketidakhadiran tab');
            document.getElementById('ketidakhadiranPanel').classList.remove('hidden'); 
            document.getElementById('ketidakhadiranTab').classList.add('active');
            loadKetidakhadiranDataFromAPI();
        }
        else if (tabName === 'cuti') { 
            console.log('[switchTab] Loading cuti tab');
            document.getElementById('cutiPanel').classList.remove('hidden'); 
            document.getElementById('cutiTab').classList.add('active'); 
            loadCutiDataFromAPI();
        }
    };

    // Initialize after DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[DOMREADY] DOM Content Loaded - Setting up handlers');
        
        // Inject server-side attendance data
        const absensiRawData = @json($formattedAbsensi ?? []);
        const ketidakhadiranRawData = @json($ketidakhadiran ?? []);
        const cutiRawData = @json($cuti ?? []);
        
        // Inject pre-calculated statistics from controller
        window.totalKaryawan = {{ $totalKaryawan ?? 0 }};
        window.hadiranCount = {{ $hadiranCount ?? 0 }};
        window.sakitCount = {{ $sakitCount ?? 0 }};
        window.izinCount = {{ $izinCount ?? 0 }};
        window.cutiCount = {{ $cutiCount ?? 0 }};
        window.tidakHadirCount = {{ $tidakHadirCount ?? 0 }};
        window.presentIds = @json($presentIds ?? []);
        window.sakitIds = @json($sakitIds ?? []);
        window.izinIds = @json($izinIds ?? []);
        window.cutiIds = @json($cutiIds ?? []);
        window.allUsers = @json($allUsers ?? []);
        
        console.log('[DOMREADY] Pre-calculated stats from controller:',{
            totalKaryawan: window.totalKaryawan,
            hadir: window.hadiranCount,
            sakit: window.sakitCount,
            izin: window.izinCount,
            cuti: window.cutiCount,
            tidakHadir: window.tidakHadirCount
        });
        
        console.log('========== RAW DATA FROM SERVER ==========');
        console.log('[DEBUG] absensiRawData type:', typeof absensiRawData, 'is array:', Array.isArray(absensiRawData));
        console.log('[DEBUG] absensiRawData:', absensiRawData);
        console.log('[DEBUG] absensiRawData length:', absensiRawData?.length || 0);
        
        console.log('[DEBUG] ketidakhadiranRawData type:', typeof ketidakhadiranRawData, 'is array:', Array.isArray(ketidakhadiranRawData));
        console.log('[DEBUG] ketidakhadiranRawData:', ketidakhadiranRawData);
        console.log('[DEBUG] ketidakhadiranRawData length:', ketidakhadiranRawData?.length || 0);
        
        console.log('[DEBUG] cutiRawData type:', typeof cutiRawData, 'is array:', Array.isArray(cutiRawData));
        console.log('[DEBUG] cutiRawData:', cutiRawData);
        console.log('[DEBUG] cutiRawData length:', cutiRawData?.length || 0);
        console.log('=========================================');
        
        // Transform and store absensi data
        window.serverAbsensiData = Array.isArray(absensiRawData) ? absensiRawData.map(item => ({
            ...item,
            jam_masuk: normalizeTimeOnly(item?.jam_masuk),
            jam_pulang: normalizeTimeOnly(item?.jam_pulang),
            keterangan: resolveKeteranganAbsensi(item),
        })) : [];
        console.log('[TRANSFORM] window.serverAbsensiData after assignment:', window.serverAbsensiData);
        
        // Transform and store ketidakhadiran data (format dates to d/m/Y)
        window.serverKetidakhadiranData = Array.isArray(ketidakhadiranRawData) ? ketidakhadiranRawData.map(item => ({
            id: item.id,
            nama: item.user && item.user.name ? item.user.name : 'Unknown',
            // Format start and end dates for display
            tanggal: item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-',
            tanggal_akhir: item.tanggal_akhir ? new Date(item.tanggal_akhir).toLocaleDateString('id-ID') : '-',
            alasan: item.jenis_ketidakhadiran || '-',
            keterangan: item.reason || item.keterangan || '-',
            status: item.approval_status || 'pending',
        })) : [];
        console.log('[TRANSFORM] window.serverKetidakhadiranData after transformation:', window.serverKetidakhadiranData);
        
        // Transform and store cuti data
        serverCutiData = Array.isArray(cutiRawData) ? cutiRawData.map(item => {
            const userName = item.user && item.user.name ? item.user.name : 'Unknown';
            const divisi = item.user && item.user.divisionDetail && item.user.divisionDetail.divisi ? item.user.divisionDetail.divisi : '-';
            const jenisCutiMap = {
                'tahunan': 'Cuti Tahunan',
                'melahirkan': 'Cuti Melahirkan',
                'duka': 'Cuti Duka',
                'izin-khusus': 'Cuti Izin Khusus',
                'tanpa-gaji': 'Cuti Tanpa Gaji',
                // Backward compatibility for old records
                'sakit': 'Cuti Sakit',
                'penting': 'Cuti Penting',
                'lainnya': 'Cuti Lainnya'
            };
            const jenisCutiKode = item.jenis_cuti || '';
            const jenisCutiText = jenisCutiMap[jenisCutiKode] || item.jenis_cuti_label || 'Cuti Lainnya';
            return {
                id: item.id,
                nama: userName,
                keterangan: item.keterangan || '-',
                periode: (item.tanggal_mulai && item.tanggal_selesai) ? 
                    new Date(item.tanggal_mulai).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}) + ' - ' + 
                    new Date(item.tanggal_selesai).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})
                    : '-',
                durasi: item.durasi || '-',
                jenis_cuti: jenisCutiText,
                status: item.status || 'menunggu',
                divisi: divisi
            };
        }) : [];
        console.log('[TRANSFORM] serverCutiData after transformation:', serverCutiData);
        
        console.log('[DEBUG] Transformed absensi data:', window.serverAbsensiData);
        console.log('[DEBUG] Transformed ketidakhadiran data:', window.serverKetidakhadiranData);
        console.log('[DEBUG] Transformed cuti data:', serverCutiData);
        
        // Update statistics with loaded data
        console.log('[DOMREADY] Calling updateStatistics after data injection');
        updateStatistics();
        
        window.showNotification = function(message, type = 'success') {
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
            setTimeout(() => { notification.classList.add('translate-x-10', 'opacity-0'); setTimeout(() => notification.remove(), 300); }, 3000);
        };

        window.openModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        };

        window.closeModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }
        };
        
        // Filter and Search Functionality
        window.applyFiltersAndSearch = function() {
            console.log('[Filter] Applying filters and search');
            
            const searchTerm = (document.getElementById('searchInput')?.value || '').toLowerCase();
            const selectedFilters = Array.from(document.querySelectorAll('#filterDropdown input[type="checkbox"]:checked'))
                .map(cb => cb.value)
                .filter(v => v !== 'all');
            
            const allChecked = document.getElementById('filterAll').checked;
            const applyFilters = !allChecked && selectedFilters.length > 0;
            
            console.log('[Filter] Search term:', searchTerm);
            console.log('[Filter] Selected filters:', selectedFilters);
            console.log('[Filter] Apply filters:', applyFilters);
            
            // Update statistics for filtered data
            updateStatistics();
            
            // Reload current tab with filters
            const activeTab = document.querySelector('.tab-button.active')?.id?.replace('Tab', '') || 'absensi';
            console.log('[Filter] Active tab:', activeTab);
            
            if (activeTab === 'absensi') {
                loadAbsensiDataFromAPI();
            } else if (activeTab === 'ketidakhadiran') {
                loadKetidakhadiranDataFromAPI();
            } else if (activeTab === 'cuti') {
                loadCutiDataFromAPI();
            }
        };
        
        // Filter button toggle
        const filterBtn = document.getElementById('filterBtn');
        const filterDropdown = document.getElementById('filterDropdown');
        if (filterBtn) {
            filterBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                filterDropdown.classList.toggle('show');
            });
        }
        
        // Close filter dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!filterBtn?.contains(e.target) && !filterDropdown?.contains(e.target)) {
                filterDropdown?.classList.remove('show');
            }
        });
        
        // Filter checkbox: "Semua Status" unchecks others
        const filterAllCheckbox = document.getElementById('filterAll');
        const otherFilterCheckboxes = document.querySelectorAll('#filterDropdown input[type="checkbox"]:not(#filterAll)');
        
        if (filterAllCheckbox) {
            filterAllCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    otherFilterCheckboxes.forEach(cb => cb.checked = false);
                }
            });
        }
        
        otherFilterCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked && filterAllCheckbox) {
                    filterAllCheckbox.checked = false;
                }
            });
        });
        
        // Apply filter button
        const applyFilterBtn = document.getElementById('applyFilter');
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', function() {
                applyFiltersAndSearch();
                filterDropdown?.classList.remove('show');
            });
        }
        
        // Reset filter button
        const resetFilterBtn = document.getElementById('resetFilter');
        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                filterAllCheckbox.checked = true;
                otherFilterCheckboxes.forEach(cb => cb.checked = false);
                document.getElementById('dateFilter').value = '';
                applyFiltersAndSearch();
                filterDropdown?.classList.remove('show');
            });
        }
        
        // Search input listener
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') {
                    applyFiltersAndSearch();
                }
            });
            
            // Debounced search as user types
            let searchTimeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFiltersAndSearch, 500);
            });
        }
        
        // Date filter listener
        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            dateFilter.addEventListener('change', applyFiltersAndSearch);
        }

        window.focusBelumAbsen = function() {
            switchTab('absensi');

            const filterAll = document.getElementById('filterAll');
            const filterBelumAbsen = document.getElementById('filterBelumAbsen');
            const filterTidakHadir = document.getElementById('filterTidakHadir');
            if (filterAll) filterAll.checked = false;
            if (filterTidakHadir) filterTidakHadir.checked = false;
            if (filterBelumAbsen) filterBelumAbsen.checked = true;

            applyFiltersAndSearch();

            const panel = document.getElementById('absensiPanel');
            if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        const tidakHadirCard = document.getElementById('tidakHadirCard');
        if (tidakHadirCard) {
            tidakHadirCard.addEventListener('click', function() {
                window.focusBelumAbsen();
            });
        }

        window.bindVerificationButtons = function() {
            const csrfToken = getCsrfToken();
            // legacy per-button binding retained for compatibility
            document.querySelectorAll('.verify-btn-approve').forEach(button => {
                button.onclick = async function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    if (!id) return showNotification('ID tidak ditemukan', 'error');
                    try {
                        const resp = await fetch(`/api/admin/cuti/${id}/verify`, {
                            method: 'POST',
                            credentials: 'include',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ approval_status: 'approved' })
                        });
                        const j = await resp.json();
                        if (j.success) {
                            showNotification('Pengajuan disetujui', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else showNotification(j.message || 'Gagal', 'error');
                    } catch (err) { showNotification('Kesalahan koneksi', 'error'); }
                };
            });
        };

        // Delegated click handler for approve/reject buttons (works for dynamic elements)
        document.addEventListener('click', function(ev) {
            const btn = ev.target.closest && ev.target.closest('.verify-btn-approve, .verify-btn-reject');
            if (!btn) return;
            ev.preventDefault();
            ev.stopPropagation();
            
            console.log('[Button Click] Detected button click:', btn.className);
            
            const csrfToken = getCsrfToken();
            const id = btn.getAttribute('data-id');
            
            console.log('[Button Click] ID:', id);
            
            if (!id) return showNotification('ID tidak ditemukan', 'error');

            const isApprove = btn.classList.contains('verify-btn-approve');
            
            console.log('[Button Click] Is Approve:', isApprove);
            
            // Determine endpoint based on parent container
            let endpoint = '/api/admin/cuti/' + id + '/verify';
            
            // Check if button is in a panel or card container
            let panel = btn.closest('.panel');
            let cutiSection = btn.closest('#cutiPanel');
            let absensiSection = btn.closest('#absensiPanel');
            let ketidakhadiranSection = btn.closest('#ketidakhadiranPanel');
            
            console.log('[Button Click] cutiSection:', !!cutiSection, 'absensiSection:', !!absensiSection, 'ketidakhadiranSection:', !!ketidakhadiranSection);
            
            if (absensiSection || ketidakhadiranSection) {
                endpoint = '/api/admin/absensi/' + id + '/verify';
            }
            // else default is cuti endpoint
            
            console.log('[Button Click] Endpoint:', endpoint);

            // For reject: submit directly without modal
            if (!isApprove) {
                (async () => {
                    try {
                        console.log('[Rejection] Sending reject request to:', endpoint);
                        const resp = await fetch(endpoint, {
                            method: 'POST',
                            credentials: 'include',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ approval_status: 'rejected' })
                        });
                        const j = await resp.json();
                        console.log('[Rejection] Response:', j);
                        if (j.success) {
                            showNotification('Pengajuan ditolak', 'success');
                            setTimeout(() => location.reload(), 1200);
                        } else showNotification(j.message || 'Gagal', 'error');
                    } catch (err) { 
                        console.error('Rejection error:', err);
                        showNotification('Kesalahan koneksi: ' + err.message, 'error'); 
                    }
                })();
                return;
            }

            // Approve directly
            (async () => {
                try {
                    console.log('[Approval] Sending approve request to:', endpoint);
                    const resp = await fetch(endpoint, {
                        method: 'POST',
                        credentials: 'include',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ approval_status: 'approved' })
                    });
                    const j = await resp.json();
                    console.log('[Approval] Response:', j);
                    if (j.success) {
                        showNotification('Pengajuan disetujui', 'success');
                        setTimeout(() => location.reload(), 1200);
                    } else showNotification(j.message || 'Gagal', 'error');
                } catch (err) { 
                    console.error('Approval error:', err);
                    showNotification('Kesalahan koneksi: ' + err.message, 'error'); 
                }
            })();
        }, true);

        // Close modal buttons (delegate)
        document.addEventListener('click', function(ev) {
            const closeBtn = ev.target.closest && ev.target.closest('.close-modal');
            const cancelBtn = ev.target.closest && ev.target.closest('.cancel-btn');
            if (closeBtn) {
                ev.preventDefault();
                // find nearest modal parent
                const modal = closeBtn.closest('.modal');
                if (modal) closeModal(modal.id);
            }
            if (cancelBtn) {
                ev.preventDefault();
                const modal = cancelBtn.closest('.modal');
                if (modal) closeModal(modal.id);
            }
        });

        // Verify modal form submission (approve/reject with reason)
        const verifyForm = document.getElementById('verifyForm');
        if (verifyForm) {
            verifyForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = document.getElementById('verifyId').value;
                const type = document.getElementById('verifyType').value || 'cuti';
                const status = document.getElementById('verifyStatus').value || 'rejected';
                const reason = document.getElementById('rejectionReason').value || '';
                if (!id) return showNotification('ID tidak ditemukan', 'error');
                const csrfToken = getCsrfToken();
                const endpoint = (type === 'absensi') ? `/api/admin/absensi/${id}/verify` : `/api/admin/cuti/${id}/verify`;
                try {
                    const resp = await fetch(endpoint, {
                        method: 'POST',
                        credentials: 'include',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ approval_status: status, rejection_reason: reason })
                    });
                    const j = await resp.json();
                    if (j.success) {
                        closeModal('verifyModal');
                        showNotification('Perubahan status tersimpan', 'success');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showNotification(j.message || 'Gagal menyimpan', 'error');
                    }
                } catch (err) { showNotification('Kesalahan koneksi', 'error'); }
            });
        }

        // Initialize page
        console.log('[INIT] Starting page initialization');
        
        // Log all available data
        console.log('[INIT] === DATA CHECK ===');
        console.log('[INIT] window.serverAbsensiData length:', window.serverAbsensiData?.length || 0);
        console.log('[INIT] window.serverKetidakhadiranData length:', window.serverKetidakhadiranData?.length || 0);
        console.log('[INIT] serverCutiData length:', serverCutiData?.length || 0);
        
        // Always show Daftar Kehadiran (Absensi) tab first
        let defaultTab = 'absensi';
        
        console.log('[INIT] === TAB SELECTION LOGIC ===');
        console.log('[INIT] -> Selected: absensi (ALWAYS show Daftar Kehadiran first)');
        
        console.log('[INIT] Switching to default tab:', defaultTab);
        switchTab(defaultTab);
        
        // Debug: Check for buttons on next tick
        setTimeout(() => {
            const allApproveButtons = document.querySelectorAll('.verify-btn-approve');
            const allRejectButtons = document.querySelectorAll('.verify-btn-reject');
            console.log('[INIT] After tab switch - Total approve buttons:', allApproveButtons.length);
            console.log('[INIT] After tab switch - Total reject buttons:', allRejectButtons.length);
            
            // Log first few buttons
            allApproveButtons.forEach((btn, i) => {
                if (i < 3) console.log(`[INIT] Approve button ${i}:`, btn.className, 'data-id:', btn.getAttribute('data-id'));
            });
            allRejectButtons.forEach((btn, i) => {
                if (i < 3) console.log(`[INIT] Reject button ${i}:`, btn.className, 'data-id:', btn.getAttribute('data-id'));
            });
        }, 500);
        
        console.log('[INIT] Page initialization complete');
    });
    </script>
</body>
</html>

