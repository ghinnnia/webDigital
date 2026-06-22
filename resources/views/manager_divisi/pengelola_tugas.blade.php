<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Kelola Tugas - Manager Divisi</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        programmer: "#3b82f6",
                        denainer: "#8b5cf6",
                        marketing: "#10b981",
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <style>
        /* =========================================
           GLOBAL RESET & TYPOGRAPHY
           ========================================= */
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; background-color: #f9fafb; color: #1f2937; overflow-x: hidden; }
        .material-icons-outlined { font-size: 24px; vertical-align: middle; width: 1em; height: 1em; }

        /* =========================================
           LAYOUT CONTAINER
           ========================================= */
        .app-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* =========================================
           SIDEBAR STYLES
           ========================================= */
        .sidebar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 256px;
            background-color: white;
            border-right: 1px solid #e5e7eb;
            z-index: 40;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            .sidebar-fixed { transform: translateX(0); }
        }

        .sidebar-fixed.translate-x-0 { transform: translateX(0) !important; }

        .sidebar-header { height: 5rem; min-height: 5rem; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
        .sidebar-header img { max-height: 3rem; width: auto; object-fit: contain; }
    
        .sidebar-nav { flex: 1; padding: 1.5rem 1rem; overflow-y: auto; }
        
        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            color: #4b5563;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
            overflow: hidden;
        }
        .nav-item:hover { background-color: #f3f4f6; color: #111827; }

        .nav-item::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
            background-color: #3b82f6; transform: scaleY(0); transition: transform 0.2s ease;
            border-top-right-radius: 4px; border-bottom-right-radius: 4px;
        }
        .nav-item.active { background-color: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .nav-item.active::before { transform: scaleY(1); }

        .sidebar-footer { padding: 1.5rem; border-top: 1px solid #e5e7eb; }

        /* =========================================
           MAIN CONTENT AREA
           ========================================= */
        .main-content {
            width: 100%;
            min-height: 100vh;
            margin-left: 0;
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 10;
        }

        @media (min-width: 768px) {
            .main-content { margin-left: 256px; width: calc(100% - 256px); }
        }

        .sidebar-overlay {
            position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 30;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }

        /* =========================================
           UI COMPONENTS
           ========================================= */
        .stat-card {
            background-color: white; border: 1px solid #e5e7eb; border-radius: 0.75rem;
            padding: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        .btn-primary { background-color: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 500; transition: background-color 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary:hover { background-color: #2563eb; }
        
        .btn-secondary { background-color: #f3f4f6; color: #4b5563; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #d1d5db; cursor: pointer; font-weight:500; transition: all 0.2s; }
        .btn-secondary:hover { background-color: #e5e7eb; color: #1f2937; }

        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; line-height: 1; }
        
        .status-pending { background-color: #dbeafe; color: #1e40af; }
        .status-proses { background-color: #fef3c7; color: #92400e; }
        .status-selesai { background-color: #d1fae5; color: #065f46; }
        .status-dibatalkan { background-color: #fee2e2; color: #991b1b; }
        
        .badge-programmer { background-color: #dbeafe; color: #1e40af; }
        .badge-desainer { background-color: #ede9fe; color: #5b21b6; }
        .badge-marketing { background-color: #d1fae5; color: #065f46; }
        .badge-default { background-color: #f3f4f6; color: #4b5563; }
        
        /* Badge untuk jenis tugas */
        .badge-task-from-karyawan { background-color: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        .badge-task-regular { background-color: #dbeafe; color: #1e40af; border: 1px solid #60a5fa; }

        .form-input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: white; transition: border-color 0.2s, box-shadow 0.2s; font-size: 0.875rem; }
        .form-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

        /* =========================================
           TABLE & RESPONSIVE TABLE
           ========================================= */
        .panel { background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem; }
        .panel-header { background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .panel-body { padding: 1.5rem; }

        .scrollable-table-container { width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
        .scrollable-table-container::-webkit-scrollbar { height: 8px; }
        .scrollable-table-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .scrollable-table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .scrollable-table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .data-table { width: 100%; min-width: 900px; border-collapse: collapse; }
        .data-table th { position: sticky; top: 0; z-index: 10; background-color: #f9fafb; }
        
        .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
        .data-table th { background-color: #f9fafb; font-weight: 600; color: #374151; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb; }
        .data-table tbody tr:nth-child(even) { background-color: #f9fafb; }
        .data-table tbody tr:hover { background-color: #f3f4f6; }
        .truncate-text { max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; vertical-align: middle; }

        /* Tab Navigation */
        .tabs-container { border-bottom: 2px solid #e5e7eb; margin-bottom: 1.5rem; }
        .tabs-nav { display: flex; gap: 0.5rem; }
        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tab-btn:hover {
            color: #374151;
            background-color: #f9fafb;
        }
        .tab-btn.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
            background-color: #eff6ff;
        }
        .tab-badge {
            background-color: #e5e7eb;
            color: #374151;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .tab-btn.active .tab-badge {
            background-color: #3b82f6;
            color: white;
        }

        /* Pagination */
        .page-btn { transition: all 0.2s ease; }
        .page-btn:hover:not(:disabled) { transform: scale(1.1); }
        .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .desktop-pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 24px; }
        .desktop-page-btn { min-width: 32px; height: 32px; display: flex; justify-content: center; align-items: center; border-radius: 50%; font-size: 14px; font-weight: 500; transition: all 0.2s ease; cursor: pointer; }
        .desktop-page-btn.active { background-color: #3b82f6; color: white; }
        .desktop-page-btn:not(.active) { background-color: #f1f5f9; color: #64748b; }
        .desktop-nav-btn { display: flex; justify-content: center; align-items: center; width: 32px; height: 32px; border-radius: 50%; background-color: #f1f5f9; color: #64748b; transition: all 0.2s ease; cursor: pointer; }
        .desktop-nav-btn:hover:not(:disabled) { background-color: #e2e8f0; }
        .desktop-nav-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        @media (max-width: 767px) { 
            .desktop-only { display: none !important; } 
            .mobile-cards { display: block !important; } 
            .desktop-table { display: none !important; } 
            .desktop-pagination { display: none !important; } 
        }
        @media (min-width: 768px) { 
            .mobile-only { display: none !important; } 
            .mobile-cards { display: none !important; } 
            .desktop-table { display: block !important; } 
        }

        .hamburger-line { transition: all 0.3s ease-in-out; transform-origin: center; }
        .hamburger-active .line1 { transform: rotate(45deg) translate(5px, 6px); }
        .hamburger-active .line2 { opacity: 0; }
        .hamburger-active .line3 { transform: rotate(-45deg) translate(5px, -6px); }

        /* Animations */
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .slide-up { animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        /* Loading */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Project Filter Dropdown */
        .project-filter-container {
            position: relative;
            width: 100%;
        }
        .project-filter-dropdown {
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            z-index: 20;
        }
        .project-option {
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .project-option:hover {
            background-color: #f3f4f6;
        }
        
        /* Task Type Indicator */
        .task-type-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .task-type-karyawan { background-color: #f59e0b; }
        .task-type-regular { background-color: #3b82f6; }
    </style>
</head>

<body class="font-display bg-gray-50 text-gray-800">
    
    <!-- Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- APP CONTAINER -->
    <div class="app-container">
        
        <!-- SIDEBAR SECTION -->
        @include('manager_divisi.templet.sider')

        <!-- MAIN CONTENT -->
        <div class="main-content">
            
            <!-- Hamburger -->
            <button id="hamburgerBtn" class="md:hidden fixed top-4 right-4 z-50 p-2 bg-white rounded-md shadow-md">
                <div class="w-6 h-6 flex flex-col justify-center space-y-1.5" id="hamburgerIcon">
                    <div class="hamburger-line line1 w-6 h-0.5 bg-gray-800"></div>
                    <div class="hamburger-line line2 w-6 h-0.5 bg-gray-800"></div>
                    <div class="hamburger-line line3 w-6 h-0.5 bg-gray-800"></div>
                </div>
            </button>

            <main class="flex-1 flex flex-col">
                <div class="flex-1 p-3 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-8 gap-4">
                        <div>
                            <h2 class="text-xl sm:text-3xl font-bold text-gray-900">
                                Daftar Tugas & Project
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">Kelola tugas berdasarkan project</p>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            @if(in_array(auth()->user()->role, ['general_manager', 'manager_divisi', 'admin']))
                            <button id="buatTugasBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined text-lg">add</span>
                                <span class="hidden sm:inline">Tambah Tugas</span>
                                <span class="sm:hidden">Tambah</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-600">Total Tugas</p>
                                    <p class="text-2xl font-bold text-gray-800" id="totalTasks">0</p>
                                </div>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <span class="material-icons-outlined text-blue-600">task_alt</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500">Divisi Anda</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-600">Dalam Proses</p>
                                    <p class="text-2xl font-bold text-yellow-600" id="inProgressTasks">0</p>
                                </div>
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <span class="material-icons-outlined text-yellow-600">hourglass_empty</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500">Belum selesai</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-600">Selesai</p>
                                    <p class="text-2xl font-bold text-green-600" id="completedTasks">0</p>
                                </div>
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <span class="material-icons-outlined text-green-600">check_circle</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500">Telah diselesaikan</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-600">Terlambat</p>
                                    <p class="text-2xl font-bold text-red-600" id="overdueTasks">0</p>
                                </div>
                                <div class="p-2 bg-red-100 rounded-lg">
                                    <span class="material-icons-outlined text-red-600">warning</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500">Lewat deadline</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PROJECT FILTER (DI ATAS) -->
                    <div class="mb-6">
                        <div class="project-filter-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Berdasarkan Project</label>
                            <div class="relative">
                                <select id="projectFilter" class="w-full px-4 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors form-input text-base">
                                    <option value="all">Semua Project</option>
                                    <!-- Options akan diisi oleh JavaScript -->
                                </select>
                                <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabs Navigation -->
                    <div class="tabs-container mb-6">
                        <div class="tabs-nav">
                            <button id="tabRegular" class="tab-btn active" data-tab="regular">
                                <span class="material-icons-outlined">assignment</span>
                                Tugas Regular
                                <span id="tabRegularCount" class="tab-badge">0</span>
                            </button>
                            <button id="tabKaryawan" class="tab-btn" data-tab="karyawan">
                                <span class="material-icons-outlined">upload_file</span>
                                Dari Karyawan
                                <span id="tabKaryawanCount" class="tab-badge">0</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filters (BAWAH) -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div class="relative w-full md:w-1/3">
                            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari nama tugas atau deskripsi..." type="text" />
                        </div>
                        <div class="flex flex-wrap gap-3 w-full md:w-auto">
                            <select id="statusFilter" class="px-4 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors flex-1 md:flex-none">
                                <option value="all">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="proses">Dalam Proses</option>
                                <option value="selesai">Selesai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                            
                            <button id="refreshBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors flex-1 md:flex-none flex items-center gap-2">
                                <span class="material-icons-outlined">refresh</span>
                                <span class="hidden sm:inline">Refresh</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Panel untuk Tugas Regular -->
                    <div id="panelRegular" class="panel fade-in">
                        <div class="panel-header">
                            <h3 class="flex items-center gap-2 font-bold text-gray-800">
                                <span class="material-icons-outlined text-primary">assignment</span>
                                <span id="panelTitleRegular">Tugas Regular</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">Total: <span class="font-semibold text-gray-800" id="totalCountRegular">0</span> tugas</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <!-- Loading -->
                            <div id="loadingIndicatorRegular" class="text-center py-8">
                                <div class="loading-spinner mx-auto"></div>
                                <p class="mt-2 text-gray-600">Memuat data...</p>
                            </div>

                            <!-- Desktop Table -->
                            <div class="desktop-table" id="desktopTableRegular" style="display: none;">
                                <div class="scrollable-table-container">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 60px;">No</th>
                                                <th style="min-width: 200px;">Nama Project</th>
                                                <th style="min-width: 200px;">Nama Tugas</th>
                                                <th style="min-width: 250px;">Deskripsi</th>
                                                <th style="min-width: 150px;">Deadline</th>
                                                <th style="min-width: 200px;">Ditugaskan Kepada</th>
                                                <th style="min-width: 100px;">Status</th>
                                                <th style="min-width: 180px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="desktopTableBodyRegular"></tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Mobile Cards -->
                            <div class="mobile-cards space-y-4" id="mobileCardsRegular" style="display: none;"></div>
                            
                            <!-- No Data -->
                            <div id="noDataMessageRegular" class="text-center py-8" style="display: none;">
                                <span class="material-icons-outlined text-gray-400 text-4xl mb-2">assignment</span>
                                <p class="text-gray-600">Tidak ada data tugas regular</p>
                                @if(in_array(auth()->user()->role, ['general_manager', 'manager_divisi', 'admin']))
                                <button id="buatTugasBtnMobileRegular" class="btn-primary mt-4">
                                    <span class="material-icons-outlined">add</span>
                                    Tambah Tugas Pertama
                                </button>
                                @endif
                            </div>
                            
                            <!-- Pagination -->
                            <div id="desktopPaginationContainerRegular" class="desktop-pagination" style="display: none;">
                                <button id="desktopPrevPageRegular" class="desktop-nav-btn"><span class="material-icons-outlined text-sm">chevron_left</span></button>
                                <div id="desktopPageNumbersRegular" class="flex gap-1"></div>
                                <button id="desktopNextPageRegular" class="desktop-nav-btn"><span class="material-icons-outlined text-sm">chevron_right</span></button>
                            </div>
                            
                            <div id="mobilePaginationContainerRegular" class="mobile-pagination md:hidden flex justify-center items-center gap-2 mt-4" style="display: none;">
                                <button id="prevPageRegular" class="page-btn w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"><span class="material-icons-outlined text-sm">chevron_left</span></button>
                                <div id="pageNumbersRegular" class="flex gap-1"></div>
                                <button id="nextPageRegular" class="page-btn w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"><span class="material-icons-outlined text-sm">chevron_right</span></button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel untuk Tugas dari Karyawan -->
                    <div id="panelKaryawan" class="panel fade-in" style="display: none;">
                        <div class="panel-header">
                            <h3 class="flex items-center gap-2 font-bold text-gray-800">
                                <span class="material-icons-outlined text-amber-600">upload_file</span>
                                <span id="panelTitleKaryawan">Tugas dari Karyawan</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">Total: <span class="font-semibold text-gray-800" id="totalCountKaryawan">0</span> tugas</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <!-- Loading -->
                            <div id="loadingIndicatorKaryawan" class="text-center py-8">
                                <div class="loading-spinner mx-auto"></div>
                                <p class="mt-2 text-gray-600">Memuat data...</p>
                            </div>

                            <!-- Desktop Table -->
                            <div class="desktop-table" id="desktopTableKaryawan" style="display: none;">
                                <div class="scrollable-table-container">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 60px;">No</th>
                                                <th style="min-width: 200px;">Nama Project</th>
                                                <th style="min-width: 200px;">Nama Tugas</th>
                                                <th style="min-width: 250px;">Deskripsi</th>
                                                <th style="min-width: 150px;">Deadline</th>
                                                <th style="min-width: 200px;">Dibuat Oleh</th>
                                                <th style="min-width: 100px;">Status</th>
                                                <th style="min-width: 180px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="desktopTableBodyKaryawan"></tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Mobile Cards -->
                            <div class="mobile-cards space-y-4" id="mobileCardsKaryawan" style="display: none;"></div>
                            
                            <!-- No Data -->
                            <div id="noDataMessageKaryawan" class="text-center py-8" style="display: none;">
                                <span class="material-icons-outlined text-gray-400 text-4xl mb-2">upload_file</span>
                                <p class="text-gray-600">Tidak ada data tugas dari karyawan</p>
                            </div>
                            
                            <!-- Pagination -->
                            <div id="desktopPaginationContainerKaryawan" class="desktop-pagination" style="display: none;">
                                <button id="desktopPrevPageKaryawan" class="desktop-nav-btn"><span class="material-icons-outlined text-sm">chevron_left</span></button>
                                <div id="desktopPageNumbersKaryawan" class="flex gap-1"></div>
                                <button id="desktopNextPageKaryawan" class="desktop-nav-btn"><span class="material-icons-outlined text-sm">chevron_right</span></button>
                            </div>
                            
                            <div id="mobilePaginationContainerKaryawan" class="mobile-pagination md:hidden flex justify-center items-center gap-2 mt-4" style="display: none;">
                                <button id="prevPageKaryawan" class="page-btn w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"><span class="material-icons-outlined text-sm">chevron_left</span></button>
                                <div id="pageNumbersKaryawan" class="flex gap-1"></div>
                                <button id="nextPageKaryawan" class="page-btn w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"><span class="material-icons-outlined text-sm">chevron_right</span></button>
                            </div>
                        </div>
                    </div>

                </div>
                <footer class="text-center p-4 bg-gray-100 text-gray-600 text-sm border-t border-gray-300">
                    Copyright ©{{ date('Y') }} oleh digicity.id
                </footer>
            </main>
        </div>
    </div>

    <!-- Modal Template -->
    <div id="modalTemplate" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4 sticky top-0 bg-white py-2">
                    <h3 class="text-xl font-bold text-gray-800 modal-title"></h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500"><span class="material-icons-outlined">close</span></button>
                </div>
                <div class="modal-content"></div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-20 opacity-0 flex items-center z-50">
        <span id="toastMessage" class="mr-2"></span>
        <button id="closeToast" class="ml-2 text-white hover:text-gray-200"><span class="material-icons-outlined">close</span></button>
    </div>

    <script>
    // ============================================================
    // CSRF TOKEN
    // ============================================================
    let csrfToken = '';
    try {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            csrfToken = metaTag.getAttribute('content');
        }
    } catch (e) {
        console.error('Failed to get CSRF token:', e);
    }

    // ============================================================
    // STATE MANAGEMENT
    // ============================================================
    const state = {
        currentPageRegular: 1,
        currentPageKaryawan: 1,
        itemsPerPage: 10,
        totalPagesRegular: 1,
        totalPagesKaryawan: 1,
        allTasks: [],
        tasksRegular: [],
        tasksKaryawan: [],
        filteredTasksRegular: [],
        filteredTasksKaryawan: [],
        currentUser: {
            id: {{ auth()->id() }},
            name: '{{ auth()->user()->name }}',
            role: '{{ auth()->user()->role }}',
            divisi: '{{ auth()->user()->divisi ?? "" }}',
            divisi_id: {{ auth()->user()->divisi_id ? auth()->user()->divisi_id : 'null' }}
        },
        karyawanList: [],
        projectList: [],
        projectDetails: {},
        isLoading: false,
        sortField: 'created_at',
        sortDirection: 'desc',
        selectedProjectId: 'all',
        activeTab: 'regular'
    };

    // ============================================================
    // UTILITY FUNCTIONS
    // ============================================================
    const utils = {
        showToast: (message, type = 'success') => {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            
            toast.style.backgroundColor = colors[type] || colors.success;
            toastMessage.textContent = message;
            
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        },
        
        showLoading: (show, type = 'all') => {
            if (type === 'regular' || type === 'all') {
                const loadingIndicator = document.getElementById('loadingIndicatorRegular');
                const desktopTable = document.getElementById('desktopTableRegular');
                const mobileCards = document.getElementById('mobileCardsRegular');
                const noDataMessage = document.getElementById('noDataMessageRegular');
                const desktopPagination = document.getElementById('desktopPaginationContainerRegular');
                const mobilePagination = document.getElementById('mobilePaginationContainerRegular');
                
                if (show) {
                    loadingIndicator.style.display = 'block';
                    desktopTable.style.display = 'none';
                    mobileCards.style.display = 'none';
                    noDataMessage.style.display = 'none';
                    desktopPagination.style.display = 'none';
                    mobilePagination.style.display = 'none';
                } else {
                    loadingIndicator.style.display = 'none';
                }
            }
            
            if (type === 'karyawan' || type === 'all') {
                const loadingIndicator = document.getElementById('loadingIndicatorKaryawan');
                const desktopTable = document.getElementById('desktopTableKaryawan');
                const mobileCards = document.getElementById('mobileCardsKaryawan');
                const noDataMessage = document.getElementById('noDataMessageKaryawan');
                const desktopPagination = document.getElementById('desktopPaginationContainerKaryawan');
                const mobilePagination = document.getElementById('mobilePaginationContainerKaryawan');
                
                if (show) {
                    loadingIndicator.style.display = 'block';
                    desktopTable.style.display = 'none';
                    mobileCards.style.display = 'none';
                    noDataMessage.style.display = 'none';
                    desktopPagination.style.display = 'none';
                    mobilePagination.style.display = 'none';
                } else {
                    loadingIndicator.style.display = 'none';
                }
            }
        },
        
        createModal: (title, content, onSubmit = null) => {
            const modalTemplate = document.getElementById('modalTemplate');
            const modalClone = modalTemplate.cloneNode(true);
            modalClone.id = 'activeModal';
            modalClone.classList.remove('hidden');
            modalClone.querySelector('.modal-title').textContent = title;
            modalClone.querySelector('.modal-content').innerHTML = content;
            
            const closeModal = () => {
                document.body.removeChild(modalClone);
            };
            
            modalClone.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', closeModal);
            });
            
            modalClone.addEventListener('click', (e) => {
                if (e.target === modalClone) {
                    closeModal();
                }
            });
            
            if (onSubmit) {
                const form = modalClone.querySelector('form');
                if (form) {
                    form.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        
                        const allAssigneeInputs = form.querySelectorAll('input[name="assigned_to[]"]');
                        const checkedAssigneeInputs = form.querySelectorAll('input[name="assigned_to[]"]:checked');
                        if (allAssigneeInputs.length > 0 && checkedAssigneeInputs.length === 0) {
                            utils.showToast('Pilih minimal satu karyawan untuk ditugaskan', 'warning');
                            return;
                        }
                        
                        const formData = new FormData(form);
                        
                        try {
                            await onSubmit(formData);
                            closeModal();
                        } catch (error) {
                            console.error('Modal submit error:', error);
                            utils.showToast(error.message || 'Terjadi kesalahan', 'error');
                        }
                    });
                }
            }
            
            document.body.appendChild(modalClone);
            
            const firstInput = modalClone.querySelector('input, textarea, select');
            if (firstInput) firstInput.focus();
            
            return modalClone;
        },
        
        formatDate: (dateString) => {
            if (!dateString) return '-';
            
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '-';
                
                const options = { 
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };
                
                return date.toLocaleDateString('id-ID', options);
            } catch (e) {
                return '-';
            }
        },
        
        formatDateForInput: (dateString) => {
            if (!dateString) return null;
            
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return null;
                
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                
                return `${year}-${month}-${day}`;
            } catch (e) {
                return null;
            }
        },
        
        getStatusClass: (status) => {
            return `status-${status}`;
        },
        
        getStatusText: (status) => {
            const statusMap = {
                'pending': 'Pending',
                'proses': 'Dalam Proses',
                'selesai': 'Selesai',
                'dibatalkan': 'Dibatalkan',
                'submitted': 'Menunggu',
                'menunggu': 'Menunggu'
            };
            return statusMap[status] || status;
        },
        
        escapeHtml: (text) => {
            if (text === null || text === undefined) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        
        truncateText: (text, length = 50) => {
            if (!text) return '';
            return text.length > length ? text.substring(0, length) + '...' : text;
        },
        
        checkOverdue: (deadline, status) => {
            if (!deadline || status === 'selesai' || status === 'dibatalkan') return false;
            
            try {
                const now = new Date();
                const deadlineDate = new Date(deadline);
                if (isNaN(deadlineDate.getTime())) return false;
                
                const nowDateOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                const deadlineDateOnly = new Date(deadlineDate.getFullYear(), deadlineDate.getMonth(), deadlineDate.getDate());
                
                return deadlineDateOnly < nowDateOnly;
            } catch (e) {
                return false;
            }
        },

        getAssigneeName: (task) => {
            // 1. Cek assigned_names dari API
            if (task.assigned_names && typeof task.assigned_names === 'string') {
                return task.assigned_names;
            }
            
            // 2. Cek assigned_to_ids array
            if (task.assigned_to_ids && Array.isArray(task.assigned_to_ids) && task.assigned_to_ids.length > 0) {
                let names = [];
                task.assigned_to_ids.forEach(id => {
                    const karyawan = state.karyawanList.find(k => k.id == id);
                    if (karyawan) {
                        names.push(karyawan.name || karyawan.nama);
                    }
                });
                if (names.length > 0) {
                    return names.join(', ');
                }
            }
            
            // 3. Cek assignee relasi
            if (task.assignee && task.assignee.name) {
                return task.assignee.name;
            }
            
            // 4. Cek field string
            if (task.assignee_name) return task.assignee_name;
            if (task.assigned_to_name) return task.assigned_to_name;
            
            // 5. Cek single assigned_to di local state
            if (task.assigned_to && state.karyawanList.length > 0) {
                const karyawan = state.karyawanList.find(k => k.id == task.assigned_to);
                if (karyawan) {
                    return karyawan.name || karyawan.nama;
                }
            }
            
            return 'Belum ditugaskan';
        },
        
        getCreatedByName: (task) => {
            if (task.created_by_name) return task.created_by_name;
            if (task.creator_name) return task.creator_name;
            
            if (task.creator && task.creator.name) {
                return task.creator.name;
            }
            
            if (task.created_by && state.karyawanList.length > 0) {
                const creator = state.karyawanList.find(k => k.id == task.created_by);
                if (creator) {
                    return creator.name || creator.nama;
                }
            }
            
            return `Karyawan`;
        },
        
        getTaskTypeLabel: (task) => {
            if (task.type === 'task_from_karyawan') {
                return { 
                    type: 'Dari Karyawan', 
                    color: 'badge-task-from-karyawan', 
                    icon: 'upload_file',
                    indicator: 'task-type-karyawan'
                };
            }
            return { 
                type: 'Tugas Regular',
                color: 'badge-task-regular', 
                icon: 'assignment',
                indicator: 'task-type-regular'
            };
        }
    };

    // ============================================================
    // API FUNCTIONS
    // ============================================================
    const api = {
        getApiEndpoint: () => {
            const userRole = state.currentUser.role;
            if (userRole === 'manager_divisi') {
                return '/manager-divisi/api/tasks-api';
            } else if (userRole === 'admin') {
                return '/admin/api/tasks';
            } else if (userRole === 'general_manager') {
                return '/api/general-manager/tasks';
            }
            return null;
        },
        
        getStatisticsEndpoint: () => {
            const userRole = state.currentUser.role;
            if (userRole === 'manager_divisi') {
                return '/manager-divisi/api/tasks/statistics';
            } else if (userRole === 'admin') {
                return '/admin/api/tasks/statistics';
            } else if (userRole === 'general_manager') {
                return '/api/general-manager/tasks/statistics';
            }
            return '/api/tasks/statistics';
        },
        
        getCreateTaskEndpoint: () => {
            const userRole = state.currentUser.role;
            if (userRole === 'manager_divisi') {
                return '/manager-divisi/tasks/createTask';
            } else if (userRole === 'admin') {
                return '/admin/tasks/createTask';
            } else if (userRole === 'general_manager') {
                return '/general_manager/tasks/createTask';
            }
            return null;
        },

        request: async (url, options = {}) => {
            const defaultOptions = {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                },
                credentials: 'same-origin'
            };
            
            const mergedOptions = { ...defaultOptions, ...options };
            
            if (!(options.body instanceof FormData)) {
                mergedOptions.headers['Content-Type'] = 'application/json';
                if (typeof options.body === 'object' && options.body !== null) {
                    mergedOptions.body = JSON.stringify(options.body);
                }
            } else {
                delete mergedOptions.headers['Content-Type'];
            }
            
            try {
                const response = await fetch(url, mergedOptions);
                
                if (!response.ok) {
                    const contentType = response.headers.get('content-type');
                    let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                    
                    if (contentType && contentType.includes('application/json')) {
                        try {
                            const errorData = await response.json();
                            if (response.status === 422 && errorData.errors) {
                                const fieldErrors = Object.entries(errorData.errors)
                                    .map(([field, messages]) => `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`)
                                    .join('\n');
                                errorMessage = `Validasi gagal:\n${fieldErrors}`;
                            } else if (errorData.message) {
                                errorMessage = errorData.message;
                            }
                        } catch (parseError) {}
                    }
                    
                    const error = new Error(errorMessage);
                    error.status = response.status;
                    error.response = response;
                    throw error;
                }
                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                }
                return await response.text();
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        },
        
        fetchProjects: async () => {
            try {
                const response = await fetch('/manager-divisi/api/projects-dropdown', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && Array.isArray(data.data)) {
                        state.projectList = data.data;
                    }
                }
                
                // Update dropdown
                render.updateProjectFilterDropdown();
                
                // Cache project details
                state.projectList.forEach((project) => {
                    if (project && project.id) {
                        state.projectDetails[project.id] = {
                            id: project.id,
                            nama: project.nama || project.name || `Project ${project.id}`,
                            deskripsi: project.deskripsi || project.description || '',
                            deadline: project.deadline || project.tanggal_selesai || '',
                        };
                    }
                });
                
                return state.projectList;
            } catch (error) {
                console.error('Error fetching projects:', error);
                return [];
            }
        },
        
        fetchKaryawan: async () => {
            try {
                const response = await fetch('/manager-divisi/api/karyawan-dropdown', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && Array.isArray(data.data)) {
                        state.karyawanList = data.data;
                    }
                }
                return state.karyawanList;
            } catch (error) {
                console.error('Error fetching karyawan:', error);
                state.karyawanList = [];
                return [];
            }
        },
        
        fetchTasks: async () => {
            state.isLoading = true;
            utils.showLoading(true, 'all');
            
            try {
                const endpoint = api.getApiEndpoint();
                if (!endpoint) {
                    throw new Error('Endpoint tidak tersedia');
                }
                
                const data = await api.request(endpoint);
                
                let tasks = [];
                if (data.success && Array.isArray(data.data)) {
                    tasks = data.data;
                } else if (Array.isArray(data)) {
                    tasks = data;
                }
                
                // Process tasks
                tasks.forEach((task) => {
                    task.is_overdue = utils.checkOverdue(task.deadline, task.status);
                    
                    let projectName = task.project_name || task.project_nama;
                    if (!projectName && task.project_id && state.projectDetails[task.project_id]) {
                        projectName = state.projectDetails[task.project_id].nama;
                    }
                    if (!projectName) {
                        projectName = 'Tidak ada Project';
                    }
                    task.project_name = projectName;
                });
                
                state.allTasks = tasks;
                state.tasksRegular = tasks.filter(task => task.type !== 'task_from_karyawan');
                state.tasksKaryawan = tasks.filter(task => task.type === 'task_from_karyawan');
                
                state.filteredTasksRegular = [...state.tasksRegular];
                state.filteredTasksKaryawan = [...state.tasksKaryawan];
                
                // Update counts
                document.getElementById('tabRegularCount').textContent = state.tasksRegular.length;
                document.getElementById('tabKaryawanCount').textContent = state.tasksKaryawan.length;
                
                render.renderTable();
                api.calculateStatsFromTasks();
                
            } catch (error) {
                console.error('Error fetching tasks:', error);
                utils.showToast('Gagal memuat data tugas', 'error');
            } finally {
                state.isLoading = false;
                utils.showLoading(false, 'all');
            }
        },
        
        calculateStatsFromTasks: () => {
            const stats = {
                total: state.allTasks.length,
                in_progress: state.allTasks.filter(t => t.status === 'proses' || t.status === 'pending').length,
                completed: state.allTasks.filter(t => t.status === 'selesai').length,
                overdue: state.allTasks.filter(t => utils.checkOverdue(t.deadline, t.status)).length
            };
            
            document.getElementById('totalTasks').textContent = stats.total;
            document.getElementById('inProgressTasks').textContent = stats.in_progress;
            document.getElementById('completedTasks').textContent = stats.completed;
            document.getElementById('overdueTasks').textContent = stats.overdue;
        },
        
        // ============================================================
        // UPDATE TASK - FIXED
        // ============================================================
        updateTask: async (id, formData) => {
            try {
                console.log('=== UPDATING TASK ===');
                console.log('Task ID:', id);
                
                // Convert FormData to object
                const data = {};
                const assignedToArray = [];
                
                formData.forEach((value, key) => {
                    if (key === 'assigned_to[]') {
                        assignedToArray.push(value);
                    } else {
                        data[key] = value;
                    }
                });
                
                if (assignedToArray.length > 0) {
                    data.assigned_to = assignedToArray;
                }
                
                console.log('Data to send:', data);
                
                // ENDPOINT YANG BENAR
                const endpoint = `/manager-divisi/tasks/${id}`;
                
                const response = await api.request(endpoint, {
                    method: 'POST',
                    body: JSON.stringify({
                        ...data,
                        _method: 'PUT'
                    })
                });
                
                console.log('Update response:', response);
                
                if (response.success !== false) {
                    utils.showToast('✅ Tugas berhasil diperbarui', 'success');
                    await api.fetchTasks();
                    return response;
                } else {
                    throw new Error(response.message || 'Gagal mengupdate tugas');
                }
                
            } catch (error) {
                console.error('Error updating task:', error);
                utils.showToast('❌ Gagal mengupdate tugas: ' + error.message, 'error');
                throw error;
            }
        },
        
        createTask: async (formData) => {
            try {
                const data = {};
                const assignedToArray = [];
                
                formData.forEach((value, key) => {
                    if (key === 'assigned_to[]') {
                        assignedToArray.push(value);
                    } else {
                        data[key] = value;
                    }
                });
                
                if (assignedToArray.length > 0) {
                    data.assigned_to = assignedToArray;
                }
                
                if (!data.target_divisi_id || data.target_divisi_id === '') {
                    data.target_divisi_id = state.currentUser.divisi_id;
                }
                
                const endpoint = api.getCreateTaskEndpoint();
                if (!endpoint) {
                    throw new Error('Anda tidak memiliki izin untuk membuat tugas');
                }
                
                const response = await api.request(endpoint, {
                    method: 'POST',
                    body: data
                });
                
                utils.showToast('✅ Tugas berhasil dibuat', 'success');
                await api.fetchTasks();
                return response;
            } catch (error) {
                console.error('Error creating task:', error);
                throw error;
            }
        },
        
        deleteTask: async (id) => {
            try {
                const endpoint = `/manager-divisi/tasks/${id}`;
                const response = await api.request(endpoint, {
                    method: 'DELETE'
                });
                
                utils.showToast('✅ Tugas berhasil dihapus', 'success');
                await api.fetchTasks();
                return response;
            } catch (error) {
                throw error;
            }
        },
        
        getTaskDetail: async (id) => {
            try {
                const taskFromState = state.allTasks.find(t => t.id === id);
                if (taskFromState) {
                    return taskFromState;
                }
                
                const endpoint = `/manager-divisi/api/tasks/${id}`;
                const data = await api.request(endpoint);
                
                if (data.success && data.data) {
                    return data.data;
                } else if (data.task) {
                    return data.task;
                }
                throw new Error('Data tugas tidak ditemukan');
            } catch (error) {
                console.error('Error in getTaskDetail:', error);
                throw error;
            }
        }
    };

    // ============================================================
    // RENDER FUNCTIONS
    // ============================================================
    const render = {
        updateProjectFilterDropdown: () => {
            const projectFilter = document.getElementById('projectFilter');
            if (!projectFilter) return;
            
            const currentValue = projectFilter.value;
            while (projectFilter.options.length > 1) {
                projectFilter.remove(1);
            }
            
            state.projectList.forEach((project) => {
                const projectName = project.nama || project.name || `Project ${project.id}`;
                const option = document.createElement('option');
                option.value = project.id;
                option.textContent = utils.escapeHtml(projectName);
                projectFilter.appendChild(option);
            });
            
            if (currentValue && Array.from(projectFilter.options).some(opt => opt.value === currentValue)) {
                projectFilter.value = currentValue;
            } else {
                projectFilter.value = 'all';
                state.selectedProjectId = 'all';
            }
        },
        
        filterTasks: () => {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const projectFilter = document.getElementById('projectFilter');
            const selectedProjectId = projectFilter ? projectFilter.value : 'all';
            
            state.selectedProjectId = selectedProjectId;
            
            state.filteredTasksRegular = state.tasksRegular.filter(task => {
                const searchMatch = !searchTerm || 
                    ((task.nama_tugas || '').toLowerCase().includes(searchTerm) ||
                     (task.deskripsi || '').toLowerCase().includes(searchTerm) ||
                     (utils.getAssigneeName(task) || '').toLowerCase().includes(searchTerm) ||
                     (task.project_name || '').toLowerCase().includes(searchTerm));
                
                const statusMatch = statusFilter === 'all' || task.status === statusFilter;
                
                let projectMatch = true;
                if (selectedProjectId !== 'all') {
                    projectMatch = task.project_id == selectedProjectId;
                }
                
                return searchMatch && statusMatch && projectMatch;
            });
            
            state.filteredTasksKaryawan = state.tasksKaryawan.filter(task => {
                const searchMatch = !searchTerm || 
                    ((task.nama_tugas || '').toLowerCase().includes(searchTerm) ||
                     (task.deskripsi || '').toLowerCase().includes(searchTerm) ||
                     (utils.getAssigneeName(task) || '').toLowerCase().includes(searchTerm) ||
                     (utils.getCreatedByName(task) || '').toLowerCase().includes(searchTerm) ||
                     (task.project_name || '').toLowerCase().includes(searchTerm));
                
                const statusMatch = statusFilter === 'all' || task.status === statusFilter;
                
                let projectMatch = true;
                if (selectedProjectId !== 'all') {
                    projectMatch = task.project_id == selectedProjectId;
                }
                
                return searchMatch && statusMatch && projectMatch;
            });
            
            if (state.activeTab === 'regular') {
                state.currentPageRegular = 1;
            } else {
                state.currentPageKaryawan = 1;
            }
            
            render.renderTable();
            
            // Update panel titles
            let panelTitleRegular = 'Tugas Regular';
            let panelTitleKaryawan = 'Tugas dari Karyawan';
            
            if (selectedProjectId !== 'all') {
                const selectedProject = state.projectList.find(p => p.id == selectedProjectId);
                if (selectedProject) {
                    const projectName = selectedProject.nama || selectedProject.name || `Project ${selectedProjectId}`;
                    panelTitleRegular = `Tugas Regular: ${projectName}`;
                    panelTitleKaryawan = `Tugas dari Karyawan: ${projectName}`;
                }
            }
            
            document.getElementById('panelTitleRegular').textContent = `${panelTitleRegular} (${state.filteredTasksRegular.length})`;
            document.getElementById('panelTitleKaryawan').textContent = `${panelTitleKaryawan} (${state.filteredTasksKaryawan.length})`;
            
            document.getElementById('totalCountRegular').textContent = state.filteredTasksRegular.length;
            document.getElementById('totalCountKaryawan').textContent = state.filteredTasksKaryawan.length;
        },
        
        switchTab: (tabName) => {
            state.activeTab = tabName;
            
            document.getElementById('tabRegular').classList.toggle('active', tabName === 'regular');
            document.getElementById('tabKaryawan').classList.toggle('active', tabName === 'karyawan');
            
            document.getElementById('panelRegular').style.display = tabName === 'regular' ? 'block' : 'none';
            document.getElementById('panelKaryawan').style.display = tabName === 'karyawan' ? 'block' : 'none';
            
            render.renderTable();
        },
        
        renderTable: () => {
            if (state.activeTab === 'regular') {
                render.renderRegularTable();
            } else {
                render.renderKaryawanTable();
            }
        },
        
        renderRegularTable: () => {
            const startIndex = (state.currentPageRegular - 1) * state.itemsPerPage;
            const endIndex = Math.min(startIndex + state.itemsPerPage, state.filteredTasksRegular.length);
            const currentTasks = state.filteredTasksRegular.slice(startIndex, endIndex);
            
            if (state.filteredTasksRegular.length === 0) {
                document.getElementById('noDataMessageRegular').style.display = 'block';
                document.getElementById('desktopTableRegular').style.display = 'none';
                document.getElementById('mobileCardsRegular').style.display = 'none';
                document.getElementById('desktopPaginationContainerRegular').style.display = 'none';
                document.getElementById('mobilePaginationContainerRegular').style.display = 'none';
                return;
            }
            
            document.getElementById('noDataMessageRegular').style.display = 'none';
            document.getElementById('desktopTableRegular').style.display = 'block';
            
            const desktopTableBody = document.getElementById('desktopTableBodyRegular');
            desktopTableBody.innerHTML = '';
            
            currentTasks.forEach((task, index) => {
                const rowNumber = startIndex + index + 1;
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                const namaTugas = task.nama_tugas || task.judul || '';
                const assigneeName = utils.getAssigneeName(task);
                
                row.innerHTML = `
                    <td class="text-center">${rowNumber}</td>
                    <td class="font-medium text-gray-900">
                        <div class="truncate-text" title="${utils.escapeHtml(task.project_name || 'Tidak ada Project')}">
                            ${utils.escapeHtml(task.project_name || 'Tidak ada Project')}
                        </div>
                    </td>
                    <td class="font-medium text-gray-900">${utils.escapeHtml(namaTugas)}</td>
                    <td title="${utils.escapeHtml(task.deskripsi || '')}">
                        <div class="truncate-text">${utils.truncateText(task.deskripsi || '', 50)}</div>
                    </td>
                    <td class="${task.is_overdue ? 'text-red-600 font-semibold' : 'text-gray-700'}">
                        ${utils.formatDate(task.deadline)}
                        ${task.is_overdue ? '<br><span class="text-xs text-red-500">Terlambat</span>' : ''}
                    </td>
                    <td class="text-gray-700">${utils.escapeHtml(assigneeName)}</td>
                    <td>
                        <span class="badge ${utils.getStatusClass(task.status)}">
                            ${utils.getStatusText(task.status)}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="modal.showDetail(${task.id})" class="p-2 rounded-full hover:bg-blue-50 transition-colors" title="Detail">
                                <span class="material-icons-outlined text-blue-600 text-lg">visibility</span>
                            </button>
                            <button onclick="modal.showEdit(${task.id})" class="p-2 rounded-full hover:bg-green-50 transition-colors" title="Edit">
                                <span class="material-icons-outlined text-green-600 text-lg">edit</span>
                            </button>
                            <button onclick="confirmDelete(${task.id})" class="p-2 rounded-full hover:bg-red-50 transition-colors" title="Hapus">
                                <span class="material-icons-outlined text-red-600 text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                `;
                
                desktopTableBody.appendChild(row);
            });
            
            // Mobile Cards
            const mobileCards = document.getElementById('mobileCardsRegular');
            mobileCards.innerHTML = '';
            mobileCards.style.display = 'block';
            
            currentTasks.forEach((task) => {
                const assigneeName = utils.getAssigneeName(task);
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg border border-gray-200 p-4 shadow-sm';
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="text-xs text-primary font-medium mb-1">
                                ${utils.escapeHtml(task.project_name || 'Tidak ada Project')}
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">${utils.escapeHtml(task.nama_tugas || task.judul || '')}</h4>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="badge ${utils.getStatusClass(task.status)}">
                                    ${utils.getStatusText(task.status)}
                                </span>
                                <span class="text-xs text-gray-500">
                                    ${utils.formatDate(task.deadline)}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button onclick="modal.showDetail(${task.id})" class="p-1 hover:bg-blue-50 rounded">
                                <span class="material-icons-outlined text-blue-600">visibility</span>
                            </button>
                            <button onclick="modal.showEdit(${task.id})" class="p-1 hover:bg-green-50 rounded">
                                <span class="material-icons-outlined text-green-600">edit</span>
                            </button>
                            <button onclick="confirmDelete(${task.id})" class="p-1 hover:bg-red-50 rounded">
                                <span class="material-icons-outlined text-red-600">delete</span>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">${utils.truncateText(task.deskripsi || '', 80)}</p>
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <span class="text-gray-700 font-medium">${utils.escapeHtml(assigneeName)}</span>
                        </div>
                        ${task.is_overdue ? '<span class="text-red-600 text-xs font-semibold">Terlambat</span>' : ''}
                    </div>
                `;
                mobileCards.appendChild(card);
            });
            
            render.updatePagination('regular');
        },
        
        renderKaryawanTable: () => {
            const startIndex = (state.currentPageKaryawan - 1) * state.itemsPerPage;
            const endIndex = Math.min(startIndex + state.itemsPerPage, state.filteredTasksKaryawan.length);
            const currentTasks = state.filteredTasksKaryawan.slice(startIndex, endIndex);
            
            if (state.filteredTasksKaryawan.length === 0) {
                document.getElementById('noDataMessageKaryawan').style.display = 'block';
                document.getElementById('desktopTableKaryawan').style.display = 'none';
                document.getElementById('mobileCardsKaryawan').style.display = 'none';
                document.getElementById('desktopPaginationContainerKaryawan').style.display = 'none';
                document.getElementById('mobilePaginationContainerKaryawan').style.display = 'none';
                return;
            }
            
            document.getElementById('noDataMessageKaryawan').style.display = 'none';
            document.getElementById('desktopTableKaryawan').style.display = 'block';
            
            const desktopTableBody = document.getElementById('desktopTableBodyKaryawan');
            desktopTableBody.innerHTML = '';
            
            currentTasks.forEach((task, index) => {
                const rowNumber = startIndex + index + 1;
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                const namaTugas = task.nama_tugas || task.judul || '';
                const assigneeName = utils.getAssigneeName(task);
                const createdByName = utils.getCreatedByName(task);
                const taskTypeInfo = utils.getTaskTypeLabel(task);
                
                row.innerHTML = `
                    <td class="text-center">${rowNumber}</td>
                    <td class="font-medium text-gray-900">
                        <div class="truncate-text" title="${utils.escapeHtml(task.project_name || 'Tidak ada Project')}">
                            ${utils.escapeHtml(task.project_name || 'Tidak ada Project')}
                        </div>
                    </td>
                    <td class="font-medium text-gray-900">${utils.escapeHtml(namaTugas)}</td>
                    <td title="${utils.escapeHtml(task.deskripsi || '')}">
                        <div class="truncate-text">${utils.truncateText(task.deskripsi || '', 50)}</div>
                    </td>
                    <td class="${task.is_overdue ? 'text-red-600 font-semibold' : 'text-gray-700'}">
                        ${utils.formatDate(task.deadline)}
                        ${task.is_overdue ? '<br><span class="text-xs text-red-500">Terlambat</span>' : ''}
                    </td>
                    <td class="text-gray-700">
                        <div class="flex items-center">
                            <span class="task-type-indicator ${taskTypeInfo.indicator}"></span>
                            ${utils.escapeHtml(createdByName)}
                        </div>
                    </td>
                    <td>
                        <span class="badge ${utils.getStatusClass(task.status)}">
                            ${utils.getStatusText(task.status)}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="modal.showDetail(${task.id})" class="p-2 rounded-full hover:bg-blue-50 transition-colors" title="Detail">
                                <span class="material-icons-outlined text-blue-600 text-lg">visibility</span>
                            </button>
                            <button onclick="approveTask(${task.id})" class="p-2 rounded-full hover:bg-green-50 transition-colors" title="Approve" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <span class="material-icons-outlined text-green-700 text-lg">check_circle</span>
                            </button>
                            <button onclick="openRevisionModal(${task.id})" class="p-2 rounded-full hover:bg-amber-50 transition-colors" title="Revisi" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <span class="material-icons-outlined text-amber-700 text-lg">edit_note</span>
                            </button>
                        </div>
                    </td>
                `;
                
                desktopTableBody.appendChild(row);
            });
            
            // Mobile Cards
            const mobileCards = document.getElementById('mobileCardsKaryawan');
            mobileCards.innerHTML = '';
            mobileCards.style.display = 'block';
            
            currentTasks.forEach((task) => {
                const assigneeName = utils.getAssigneeName(task);
                const createdByName = utils.getCreatedByName(task);
                const taskTypeInfo = utils.getTaskTypeLabel(task);
                
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg border border-gray-200 p-4 shadow-sm';
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="text-xs text-amber-600 font-medium mb-1">
                                ${utils.escapeHtml(task.project_name || 'Tidak ada Project')}
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">${utils.escapeHtml(task.nama_tugas || task.judul || '')}</h4>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="badge ${utils.getStatusClass(task.status)}">
                                    ${utils.getStatusText(task.status)}
                                </span>
                                <span class="text-xs text-gray-500">
                                    ${utils.formatDate(task.deadline)}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button onclick="modal.showDetail(${task.id})" class="p-1 hover:bg-blue-50 rounded">
                                <span class="material-icons-outlined text-blue-600">visibility</span>
                            </button>
                            <button onclick="approveTask(${task.id})" class="p-1 hover:bg-green-50 rounded" title="Approve" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <span class="material-icons-outlined text-green-700">check_circle</span>
                            </button>
                            <button onclick="openRevisionModal(${task.id})" class="p-1 hover:bg-amber-50 rounded" title="Revisi" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <span class="material-icons-outlined text-amber-700">edit_note</span>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">${utils.truncateText(task.deskripsi || '', 80)}</p>
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <div class="flex items-center">
                                <span class="task-type-indicator ${taskTypeInfo.indicator}"></span>
                                <span class="text-gray-700 font-medium">Dibuat oleh: ${utils.escapeHtml(createdByName)}</span>
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                Ditugaskan ke: ${utils.escapeHtml(assigneeName)}
                            </div>
                        </div>
                        ${task.is_overdue ? '<span class="text-red-600 text-xs font-semibold">Terlambat</span>' : ''}
                    </div>
                `;
                mobileCards.appendChild(card);
            });
            
            render.updatePagination('karyawan');
        },
        
        updatePagination: (type) => {
            if (type === 'regular') {
                state.totalPagesRegular = Math.ceil(state.filteredTasksRegular.length / state.itemsPerPage);
                
                const desktopPageNumbers = document.getElementById('desktopPageNumbersRegular');
                desktopPageNumbers.innerHTML = '';
                
                for (let i = 1; i <= state.totalPagesRegular; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.className = `desktop-page-btn ${i === state.currentPageRegular ? 'active' : ''}`;
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => {
                        state.currentPageRegular = i;
                        render.renderRegularTable();
                    });
                    desktopPageNumbers.appendChild(pageButton);
                }
                
                document.getElementById('desktopPrevPageRegular').disabled = state.currentPageRegular === 1;
                document.getElementById('desktopNextPageRegular').disabled = state.currentPageRegular === state.totalPagesRegular;
                
                const mobilePageNumbers = document.getElementById('pageNumbersRegular');
                mobilePageNumbers.innerHTML = '';
                
                for (let i = 1; i <= state.totalPagesRegular; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.className = `w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium ${i === state.currentPageRegular ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300'}`;
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => {
                        state.currentPageRegular = i;
                        render.renderRegularTable();
                    });
                    mobilePageNumbers.appendChild(pageButton);
                }
                
                document.getElementById('prevPageRegular').disabled = state.currentPageRegular === 1;
                document.getElementById('nextPageRegular').disabled = state.currentPageRegular === state.totalPagesRegular;
                
                const showPagination = state.totalPagesRegular > 1;
                document.getElementById('desktopPaginationContainerRegular').style.display = showPagination ? 'flex' : 'none';
                document.getElementById('mobilePaginationContainerRegular').style.display = (showPagination && window.innerWidth < 768) ? 'flex' : 'none';
                
            } else {
                state.totalPagesKaryawan = Math.ceil(state.filteredTasksKaryawan.length / state.itemsPerPage);
                
                const desktopPageNumbers = document.getElementById('desktopPageNumbersKaryawan');
                desktopPageNumbers.innerHTML = '';
                
                for (let i = 1; i <= state.totalPagesKaryawan; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.className = `desktop-page-btn ${i === state.currentPageKaryawan ? 'active' : ''}`;
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => {
                        state.currentPageKaryawan = i;
                        render.renderKaryawanTable();
                    });
                    desktopPageNumbers.appendChild(pageButton);
                }
                
                document.getElementById('desktopPrevPageKaryawan').disabled = state.currentPageKaryawan === 1;
                document.getElementById('desktopNextPageKaryawan').disabled = state.currentPageKaryawan === state.totalPagesKaryawan;
                
                const mobilePageNumbers = document.getElementById('pageNumbersKaryawan');
                mobilePageNumbers.innerHTML = '';
                
                for (let i = 1; i <= state.totalPagesKaryawan; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.className = `w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium ${i === state.currentPageKaryawan ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300'}`;
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => {
                        state.currentPageKaryawan = i;
                        render.renderKaryawanTable();
                    });
                    mobilePageNumbers.appendChild(pageButton);
                }
                
                document.getElementById('prevPageKaryawan').disabled = state.currentPageKaryawan === 1;
                document.getElementById('nextPageKaryawan').disabled = state.currentPageKaryawan === state.totalPagesKaryawan;
                
                const showPagination = state.totalPagesKaryawan > 1;
                document.getElementById('desktopPaginationContainerKaryawan').style.display = showPagination ? 'flex' : 'none';
                document.getElementById('mobilePaginationContainerKaryawan').style.display = (showPagination && window.innerWidth < 768) ? 'flex' : 'none';
            }
        }
    };

    // ============================================================
    // MODAL FUNCTIONS
    // ============================================================
    const modal = {
        showDetail: async (id) => {
            try {
                const task = await api.getTaskDetail(id);
                
                let projectName = task.project_name || task.project_nama;
                if (!projectName && task.project_id && state.projectDetails[task.project_id]) {
                    projectName = state.projectDetails[task.project_id].nama;
                }
                
                const namaTugas = task.nama_tugas || task.judul || '';
                const assigneeName = utils.getAssigneeName(task);
                const createdByName = utils.getCreatedByName(task);
                const isFromKaryawan = task.type === 'task_from_karyawan';
                
                const modalContent = `
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Detail Tugas</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Project</h4>
                                <p class="font-medium text-gray-900">${utils.escapeHtml(projectName || 'Tidak ada Project')}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Tugas</h4>
                                <p class="font-medium text-gray-900">${utils.escapeHtml(namaTugas)}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deadline</h4>
                                <p class="${task.is_overdue ? 'text-red-600 font-semibold' : 'text-gray-900'}">
                                    ${utils.formatDate(task.deadline)}
                                    ${task.is_overdue ? '<br><span class="text-xs text-red-500">Terlambat</span>' : ''}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</h4>
                                <span class="badge ${utils.getStatusClass(task.status)}">
                                    ${utils.getStatusText(task.status)}
                                </span>
                            </div>
                            ${isFromKaryawan ? `
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Dibuat Oleh</h4>
                                <p class="text-gray-900">${utils.escapeHtml(createdByName || '-')}</p>
                            </div>
                            ` : ''}
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Ditugaskan Kepada</h4>
                                <p class="text-gray-900">${utils.escapeHtml(assigneeName || '-')}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi</h4>
                            <div class="bg-gray-50 p-3 rounded-lg mt-1">
                                <p class="text-gray-700 whitespace-pre-line">${utils.escapeHtml(task.deskripsi || 'Tidak ada deskripsi')}</p>
                            </div>
                        </div>
                        
                        ${task.catatan ? `
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catatan</h4>
                            <div class="bg-gray-50 p-3 rounded-lg mt-1">
                                <p class="text-gray-700 whitespace-pre-line">${utils.escapeHtml(task.catatan)}</p>
                            </div>
                        </div>
                        ` : ''}
                        
                        <div class="pt-4 border-t">
                            <button class="close-modal btn-secondary w-full py-2">Tutup</button>
                        </div>
                    </div>
                `;
                
                utils.createModal('Detail Tugas', modalContent);
                
            } catch (error) {
                console.error('Error showing detail:', error);
                utils.showToast('Gagal memuat detail tugas: ' + error.message, 'error');
            }
        },
        
        // ============================================================
        // SHOW EDIT - FIXED
        // ============================================================
        showEdit: async (id) => {
            try {
                console.log('=== OPENING EDIT MODAL ===');
                console.log('Task ID:', id);
                
                const task = await api.getTaskDetail(id);
                console.log('Task data:', task);
                
                // Cek apakah task dari karyawan
                if (task.type === 'task_from_karyawan') {
                    utils.showToast('Tugas dari karyawan tidak dapat diedit langsung. Gunakan tombol Revisi.', 'warning');
                    return;
                }
                
                // Siapkan data untuk form
                let hasKaryawanInDivisi = state.karyawanList.length > 0;
                
                let projectOptions = '<option value="">-- Pilih Project --</option>';
                if (state.projectList.length > 0) {
                    state.projectList.forEach(p => {
                        const projectName = p.nama || p.name || `Project ${p.id}`;
                        const isSelected = task.project_id == p.id ? 'selected' : '';
                        projectOptions += `<option value="${p.id}" ${isSelected}>${utils.escapeHtml(projectName)}</option>`;
                    });
                }
                
                const formattedDeadline = task.deadline ? utils.formatDateForInput(task.deadline) : '';
                const namaTugasValue = task.nama_tugas || '';
                
                // Dapatkan assigned_to untuk checkbox
                let assignedIds = [];
                if (task.assigned_to) {
                    if (Array.isArray(task.assigned_to)) {
                        assignedIds = task.assigned_to.map(id => String(id));
                    } else {
                        assignedIds = [String(task.assigned_to)];
                    }
                }
                
                let karyawanCheckboxes = '';
                if (hasKaryawanInDivisi) {
                    state.karyawanList.forEach((k) => {
                        const karyawanName = k.name || k.nama || 'Tanpa Nama';
                        const karyawanId = String(k.id || k.user_id);
                        const isChecked = assignedIds.includes(karyawanId) ? 'checked' : '';
                        
                        karyawanCheckboxes += `
                            <div class="flex items-center">
                                <input type="checkbox" name="assigned_to[]" value="${karyawanId}" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       id="karyawan_edit_${karyawanId}" ${isChecked}>
                                <label for="karyawan_edit_${karyawanId}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                    ${utils.escapeHtml(karyawanName)}
                                </label>
                            </div>
                        `;
                    });
                }
                
                const modalContent = `
                    <form id="editTaskForm">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project</label>
                                <select name="project_id" class="form-input w-full">
                                    ${projectOptions}
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tugas <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_tugas" value="${utils.escapeHtml(namaTugasValue)}" 
                                       class="form-input" required placeholder="Masukkan nama tugas">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                                <textarea name="deskripsi" rows="3" class="form-input" required>${utils.escapeHtml(task.deskripsi || '')}</textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                                <input type="date" name="deadline" value="${formattedDeadline}" class="form-input" required>
                            </div>
                            
                            ${task.status === 'pending' ? (hasKaryawanInDivisi ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ditugaskan Kepada <span class="text-red-500">*</span></label>
                                <div class="space-y-2 bg-gray-50 p-3 rounded-lg border border-gray-200 max-h-64 overflow-y-auto">
                                    ${karyawanCheckboxes}
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Pilih satu atau lebih karyawan</p>
                            </div>
                            ` : `
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-sm text-yellow-700">Tidak ada karyawan di divisi ini.</p>
                                <input type="hidden" name="assigned_to" value="">
                            </div>
                            `) : `
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-700">Status tugas adalah "${task.status}", tidak dapat mengubah penugasan.</p>
                            </div>
                            `}
                            
                            <input type="hidden" name="status" value="${utils.escapeHtml(task.status || 'pending')}">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                                <select name="priority" class="form-input">
                                    <option value="low" ${task.priority === 'low' ? 'selected' : ''}>Rendah</option>
                                    <option value="medium" ${task.priority === 'medium' ? 'selected' : ''}>Sedang</option>
                                    <option value="high" ${task.priority === 'high' ? 'selected' : ''}>Tinggi</option>
                                    <option value="urgent" ${task.priority === 'urgent' ? 'selected' : ''}>Sangat Mendesak</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                <textarea name="catatan" rows="2" class="form-input">${utils.escapeHtml(task.catatan || '')}</textarea>
                            </div>
                            
                            <div class="flex gap-2 pt-4">
                                <button type="button" class="close-modal btn-secondary flex-1 py-2">Batal</button>
                                <button type="submit" class="btn-primary flex-1 py-2">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                `;
                
                utils.createModal('Edit Tugas', modalContent, async (formData) => {
                    // Validasi
                    const namaTugas = formData.get('nama_tugas');
                    if (!namaTugas || namaTugas.trim() === '') {
                        utils.showToast('Nama tugas wajib diisi', 'warning');
                        return;
                    }
                    
                    const deskripsi = formData.get('deskripsi');
                    if (!deskripsi || deskripsi.trim() === '') {
                        utils.showToast('Deskripsi wajib diisi', 'warning');
                        return;
                    }
                    
                    const deadline = formData.get('deadline');
                    if (!deadline) {
                        utils.showToast('Deadline wajib diisi', 'warning');
                        return;
                    }
                    
                    // Panggil API update
                    await api.updateTask(id, formData);
                });
                
            } catch (error) {
                console.error('Error showing edit form:', error);
                utils.showToast('Gagal memuat form edit: ' + error.message, 'error');
            }
        },
        
        showCreate: () => {
            try {
                let hasKaryawanInDivisi = state.karyawanList.length > 0;
                
                let projectOptions = '<option value="">-- Pilih Project --</option>';
                if (state.projectList.length > 0) {
                    state.projectList.forEach(p => {
                        const projectName = p.nama || p.name || `Project ${p.id}`;
                        projectOptions += `<option value="${p.id}">${utils.escapeHtml(projectName)}</option>`;
                    });
                }
                
                let karyawanCheckboxes = '';
                if (hasKaryawanInDivisi) {
                    state.karyawanList.forEach((k) => {
                        const karyawanName = k.name || k.nama || 'Tanpa Nama';
                        const karyawanId = String(k.id || k.user_id);
                        
                        karyawanCheckboxes += `
                            <div class="flex items-center">
                                <input type="checkbox" name="assigned_to[]" value="${karyawanId}" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       id="karyawan_create_${karyawanId}">
                                <label for="karyawan_create_${karyawanId}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                    ${utils.escapeHtml(karyawanName)}
                                </label>
                            </div>
                        `;
                    });
                }
                
                const modalContent = `
                    <form>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project</label>
                                <select name="project_id" class="form-input w-full">
                                    ${projectOptions}
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tugas <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_tugas" class="form-input" required placeholder="Masukkan nama tugas">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                                <textarea name="deskripsi" rows="3" class="form-input" required placeholder="Deskripsi lengkap tugas"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                                <input type="date" name="deadline" class="form-input" required>
                            </div>
                            
                            ${hasKaryawanInDivisi ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ditugaskan Kepada <span class="text-red-500">*</span></label>
                                <div class="space-y-2 bg-gray-50 p-3 rounded-lg border border-gray-200 max-h-64 overflow-y-auto">
                                    ${karyawanCheckboxes}
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Pilih satu atau lebih karyawan</p>
                            </div>
                            ` : `
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-sm text-yellow-700">Tidak ada karyawan di divisi ini.</p>
                                <input type="hidden" name="assigned_to" value="">
                            </div>
                            `}
                            
                            <input type="hidden" name="status" value="pending">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                                <select name="priority" class="form-input">
                                    <option value="low">Rendah</option>
                                    <option value="medium" selected>Sedang</option>
                                    <option value="high">Tinggi</option>
                                    <option value="urgent">Sangat Mendesak</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                <textarea name="catatan" rows="2" class="form-input" placeholder="Tambahkan catatan"></textarea>
                            </div>
                            
                            <input type="hidden" name="target_divisi_id" value="${state.currentUser.divisi_id}">
                            
                            <div class="flex gap-2 pt-4">
                                <button type="button" class="close-modal btn-secondary flex-1 py-2">Batal</button>
                                <button type="submit" class="btn-primary flex-1 py-2">Tambah Tugas</button>
                            </div>
                        </div>
                    </form>
                `;
                
                utils.createModal('Tambah Tugas Baru', modalContent, async (formData) => {
                    await api.createTask(formData);
                });
                
            } catch (error) {
                console.error('Error showing create form:', error);
                utils.showToast('Gagal memuat form tambah tugas', 'error');
            }
        }
    };

    // ============================================================
    // GLOBAL FUNCTIONS
    // ============================================================
    window.approveTask = async (id) => {
        if (!confirm('Setujui tugas ini? Tugas akan ditandai SELESAI.')) return;
        
        try {
            const response = await fetch(`/manager-divisi/api/tugas-karyawan/${id}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ action: 'approved', status: 'selesai' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                utils.showToast('✅ Tugas berhasil disetujui', 'success');
                await api.fetchTasks();
            } else {
                utils.showToast(data.message || 'Gagal menyetujui tugas', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            utils.showToast('Terjadi kesalahan', 'error');
        }
    };

    window.openRevisionModal = (id) => {
        const modalContent = `
            <form id="revisionForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan Revisi <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="revision_notes" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                            required 
                            placeholder="Tuliskan bagian yang harus diperbaiki karyawan..."
                        ></textarea>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" class="close-modal px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex-1">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex-1">
                            Kirim Revisi
                        </button>
                    </div>
                </div>
            </form>
        `;

        utils.createModal('Kirim Revisi', modalContent, async (formData) => {
            const notes = formData.get('revision_notes')?.toString().trim();
            
            if (!notes) {
                utils.showToast('Keterangan revisi wajib diisi', 'warning');
                return;
            }

            try {
                const response = await fetch(`/manager-divisi/api/tugas-karyawan/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ action: 'returned', notes: notes })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    utils.showToast('✅ Revisi berhasil dikirim ke karyawan', 'success');
                    await api.fetchTasks();
                } else {
                    utils.showToast(data.message || 'Gagal mengirim revisi', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                utils.showToast('Terjadi kesalahan', 'error');
            }
        });
    };

    window.confirmDelete = async (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
            try {
                await api.deleteTask(id);
            } catch (error) {
                console.error('Error deleting task:', error);
                utils.showToast('Gagal menghapus tugas: ' + error.message, 'error');
            }
        }
    };

    // ============================================================
    // INITIALIZATION
    // ============================================================
    document.addEventListener('DOMContentLoaded', () => {
        // Sidebar
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const hamburger = document.getElementById('hamburgerBtn');
        const hamburgerIcon = document.getElementById('hamburgerIcon');

        if (sidebar && overlay && hamburger && hamburgerIcon) {
            function toggleSidebar() {
                sidebar.classList.toggle('translate-x-0');
                overlay.classList.toggle('active');
                hamburgerIcon.classList.toggle('hamburger-active');
            }
            hamburger.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        }

        // Event Listeners
        document.getElementById('searchInput').addEventListener('input', () => render.filterTasks());
        document.getElementById('statusFilter').addEventListener('change', () => render.filterTasks());
        document.getElementById('projectFilter').addEventListener('change', () => render.filterTasks());
        
        document.getElementById('refreshBtn').addEventListener('click', () => {
            api.fetchProjects().then(() => api.fetchTasks());
            utils.showToast('Data tugas diperbarui', 'info');
        });
        
        // Tab navigation
        document.getElementById('tabRegular').addEventListener('click', () => render.switchTab('regular'));
        document.getElementById('tabKaryawan').addEventListener('click', () => render.switchTab('karyawan'));
        
        document.getElementById('closeToast').addEventListener('click', () => {
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        });
        
        // Pagination
        document.getElementById('desktopPrevPageRegular').addEventListener('click', () => {
            if (state.currentPageRegular > 1) {
                state.currentPageRegular--;
                render.renderRegularTable();
            }
        });
        document.getElementById('desktopNextPageRegular').addEventListener('click', () => {
            if (state.currentPageRegular < state.totalPagesRegular) {
                state.currentPageRegular++;
                render.renderRegularTable();
            }
        });
        document.getElementById('prevPageRegular').addEventListener('click', () => {
            if (state.currentPageRegular > 1) {
                state.currentPageRegular--;
                render.renderRegularTable();
            }
        });
        document.getElementById('nextPageRegular').addEventListener('click', () => {
            if (state.currentPageRegular < state.totalPagesRegular) {
                state.currentPageRegular++;
                render.renderRegularTable();
            }
        });
        
        document.getElementById('desktopPrevPageKaryawan').addEventListener('click', () => {
            if (state.currentPageKaryawan > 1) {
                state.currentPageKaryawan--;
                render.renderKaryawanTable();
            }
        });
        document.getElementById('desktopNextPageKaryawan').addEventListener('click', () => {
            if (state.currentPageKaryawan < state.totalPagesKaryawan) {
                state.currentPageKaryawan++;
                render.renderKaryawanTable();
            }
        });
        document.getElementById('prevPageKaryawan').addEventListener('click', () => {
            if (state.currentPageKaryawan > 1) {
                state.currentPageKaryawan--;
                render.renderKaryawanTable();
            }
        });
        document.getElementById('nextPageKaryawan').addEventListener('click', () => {
            if (state.currentPageKaryawan < state.totalPagesKaryawan) {
                state.currentPageKaryawan++;
                render.renderKaryawanTable();
            }
        });
        
        document.getElementById('buatTugasBtn').addEventListener('click', modal.showCreate);
        document.getElementById('buatTugasBtnMobileRegular').addEventListener('click', modal.showCreate);
        
        // Initialize
        const init = async () => {
            try {
                await api.fetchProjects();
                await api.fetchKaryawan();
                await api.fetchTasks();
                render.switchTab('regular');
            } catch (error) {
                console.error('Error in initialization:', error);
                utils.showToast('Gagal memuat data awal', 'error');
            }
        };
        
        init();
    });

    window.modal = modal;
    window.state = state;
    window.api = api;
    window.utils = utils;
    </script>
</body>
</html>