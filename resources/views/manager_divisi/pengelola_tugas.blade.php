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
                    Copyright ©{{ date('Y') }} oleh digital kolaborasi.id
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

    <!-- JavaScript -->
    <script>
    // CSRF Token untuk AJAX
let csrfToken = '';
try {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        csrfToken = metaTag.getAttribute('content');
    }
} catch (e) {
    console.error('Failed to get CSRF token:', e);
}

// State Management
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

// Utility Functions
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
        
        // Setup auto-fill untuk project
        const setupProjectAutoFill = () => {
            const projectSelect = modalClone.querySelector('select[name="project_id"]');
            const namaTugasInput = modalClone.querySelector('input[name="nama_tugas"]');
            const deskripsiTextarea = modalClone.querySelector('textarea[name="deskripsi"]');
            const deadlineInput = modalClone.querySelector('input[name="deadline"]');
            
            if (projectSelect) {
                projectSelect.addEventListener('change', function() {
                    const projectId = this.value;
                    
                    if (projectId && projectId !== '') {
                        const projectData = state.projectDetails[projectId];
                        
                        if (projectData) {
                            // Beri saran untuk field "Nama Tugas"
                            if (namaTugasInput && (!namaTugasInput.value || namaTugasInput.value.trim() === '')) {
                                namaTugasInput.placeholder = `Contoh: Melakukan analisis untuk ${projectData.nama}`;
                                namaTugasInput.setAttribute('data-project-name', projectData.nama);
                            }
                            
                            // Fill deskripsi jika kosong
                            if (deskripsiTextarea) {
                                if (!deskripsiTextarea.value || deskripsiTextarea.value.trim() === '') {
                                    if (projectData.deskripsi && projectData.deskripsi.trim() !== '') {
                                        deskripsiTextarea.value = projectData.deskripsi;
                                    } else {
                                        deskripsiTextarea.value = `Tugas terkait dengan project: ${projectData.nama}`;
                                    }
                                }
                            }
                            
                            // Fill deadline jika kosong dan tersedia
                            if (deadlineInput && (!deadlineInput.value || deadlineInput.value.trim() === '')) {
                                if (projectData.deadline) {
                                    try {
                                        const formattedDate = utils.formatDateForInput(projectData.deadline);
                                        if (formattedDate) {
                                            deadlineInput.value = formattedDate;
                                        }
                                    } catch (e) {
                                        console.error('Error parsing deadline:', e);
                                    }
                                }
                            }
                            
                            utils.showToast(`Data dari project "${projectData.nama}" telah diisi otomatis`, 'info');
                        } else {
                            utils.showToast('Memuat detail project...', 'info');
                            
                            api.fetchProjectDetail(projectId).then(data => {
                                if (data) {
                                    state.projectDetails[projectId] = data;
                                    setTimeout(() => {
                                        projectSelect.dispatchEvent(new Event('change'));
                                    }, 100);
                                } else {
                                    utils.showToast('Data project tidak ditemukan', 'warning');
                                }
                            }).catch(err => {
                                console.error('Error fetching project detail:', err);
                                utils.showToast('Gagal memuat detail project', 'error');
                            });
                        }
                    } else {
                        // Jika project tidak dipilih, reset placeholder
                        if (namaTugasInput) {
                            namaTugasInput.placeholder = 'Masukkan nama tugas';
                            namaTugasInput.removeAttribute('data-project-name');
                        }
                    }
                });
                
                // Trigger change event jika sudah ada value
                if (projectSelect.value) {
                    setTimeout(() => {
                        projectSelect.dispatchEvent(new Event('change'));
                    }, 200);
                }
            }
        };
        
        if (onSubmit) {
            const form = modalClone.querySelector('form');
            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    // Validasi karyawan hanya untuk form yang memang memiliki input assigned_to[]
                    const allAssigneeInputs = form.querySelectorAll('input[name="assigned_to[]"]');
                    const checkedAssigneeInputs = form.querySelectorAll('input[name="assigned_to[]"]:checked');
                    if (allAssigneeInputs.length > 0 && checkedAssigneeInputs.length === 0) {
                        utils.showToast('Pilih minimal satu karyawan untuk ditugaskan', 'warning');
                        return;
                    }
                    
                    const formData = new FormData(form);
                    
                    // Debug: Log all form data
                    console.log('=== FULL FORM DATA ===');
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}:`, value);
                    }
                    
                    // Get assigned_to[] values (form checkboxes are already using assigned_to[])
                    const assignedToValues = formData.getAll('assigned_to[]');
                    console.log('Assigned to values:', assignedToValues.length, assignedToValues);
                    
                    // Verify FormData has all correct fields
                    console.log('=== FINAL FORM DATA ===');
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}:`, value);
                    }
                    
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
        
        setTimeout(setupProjectAutoFill, 100);
        
        const firstInput = modalClone.querySelector('input, textarea, select');
        if (firstInput) firstInput.focus();
        
        return modalClone;
    },
    
    formatDate: (dateString) => {
        if (!dateString) return '-';
        
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';
            
            const timezoneOffset = 7 * 60 * 60 * 1000;
            const localDate = new Date(date.getTime() + timezoneOffset);
            
            const options = { 
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            
            return localDate.toLocaleDateString('id-ID', options);
        } catch (e) {
            console.error('Error formatting date:', e, 'dateString:', dateString);
            return '-';
        }
    },
    
    formatDateForInput: (dateString) => {
        if (!dateString) return null;
        
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return null;
            
            const timezoneOffset = 7 * 60 * 60 * 1000;
            const localDate = new Date(date.getTime() + timezoneOffset);
            
            const year = localDate.getFullYear();
            const month = String(localDate.getMonth() + 1).padStart(2, '0');
            const day = String(localDate.getDate()).padStart(2, '0');
            
            return `${year}-${month}-${day}`;
        } catch (e) {
            console.error('Error formatting date for input:', e, 'dateString:', dateString);
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
    
    cleanDivisiString: (divisiString) => {
        if (!divisiString) return '';
        if (typeof divisiString === 'string' && divisiString.includes('{"id":') && divisiString.includes('divisi":"')) {
            try {
                const parsed = JSON.parse(divisiString);
                return parsed.divisi || '';
            } catch (e) {
                return divisiString;
            }
        }
        return divisiString;
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
            
            const timezoneOffset = 7 * 60 * 60 * 1000;
            const adjustedNow = new Date(now.getTime() + timezoneOffset);
            const adjustedDeadline = new Date(deadlineDate.getTime() + timezoneOffset);
            
            const nowDateOnly = new Date(adjustedNow.getFullYear(), adjustedNow.getMonth(), adjustedNow.getDate());
            const deadlineDateOnly = new Date(adjustedDeadline.getFullYear(), adjustedDeadline.getMonth(), adjustedDeadline.getDate());
            
            return deadlineDateOnly < nowDateOnly;
        } catch (e) {
            console.error('Error checking overdue:', e);
            return false;
        }
    },

    // FUNGSI BARU: Pencarian Nama Assignee yang Cerdas
    getAssigneeName: (task) => {
        // 0. Direct field from API (backend computed names) - THIS IS THE FASTEST PATH
        if (task.assigned_names && typeof task.assigned_names === 'string') {
            console.log(`[getAssigneeName] Task ${task.id}: Using assigned_names field:`, task.assigned_names);
            return task.assigned_names;
        }
        
        // If we get here, assigned_names wasn't in API response
        if (task.assigned_to_ids && Array.isArray(task.assigned_to_ids) && task.assigned_to_ids.length > 1) {
            console.warn(`[getAssigneeName] Task ${task.id}: assigned_names NOT in API! Using fallback to parse assigned_to_ids`);
        }
        
        console.log('getAssigneeName called for task:', {
            id: task.id,
            assigned_to_ids: task.assigned_to_ids,
            assigned_to_ids_type: typeof task.assigned_to_ids,
            assigned_to: task.assigned_to,
            assigned_names: task.assigned_names,
            karyawanListCount: state.karyawanList.length
        });
        
        // Parse assigned_to_ids - bisa dari berbagai format
        let assignedIds = [];
        if (task.assigned_to_ids) {
            if (Array.isArray(task.assigned_to_ids)) {
                assignedIds = task.assigned_to_ids;
            } else if (typeof task.assigned_to_ids === 'string') {
                try {
                    assignedIds = JSON.parse(task.assigned_to_ids);
                    if (!Array.isArray(assignedIds)) assignedIds = [];
                } catch (e) {
                    console.log('Failed to parse assigned_to_ids as JSON:', e);
                    assignedIds = [];
                }
            }
        }
        
        console.log('Parsed assignedIds:', assignedIds);
        
        // 1. Jika ada multiple assigned_to_ids, cari di karyawanList
        if (assignedIds.length > 0) {
            console.log('Processing assignedIds:', assignedIds);
            let names = [];
            
            assignedIds.forEach(id => {
                // First try: Cari di karyawanList
                const karyawan = state.karyawanList.find(k => k.id == id);
                if (karyawan) {
                    names.push(karyawan.name || karyawan.nama);
                    console.log(`Found in karyawanList ${id}:`, karyawan.name);
                    return;
                }
                
                // If not found yet, skip for now - will try API data below
                console.log(`Karyawan ${id} not found in karyawanList (list has ${state.karyawanList.length} items)`);
            });
            
            // Jika berhasil find di karyawanList
            if (names.length > 0) {
                const result = names.join(', ');
                console.log('Multiple assignees from karyawanList:', result);
                return result;
            }
        }
        
        // 2. Jika assigned_to_ids tidak ditemukan di karyawanList, coba dari API relasi
        // Check if API returned assignees array langsung
        if (task.assignees && Array.isArray(task.assignees)) {
            const assigneeNames = task.assignees
                .map(a => a.name || a.nama)
                .filter(n => n);
            if (assigneeNames.length > 0) {
                console.log('Found from task.assignees array:', assigneeNames.join(', '));
                return assigneeNames.join(', ');
            }
        }
        
        // 3. Coba dari single assignee relasi (untuk task lama/single assignment)
        if (task.assignee && task.assignee.name) {
            console.log('Found from task.assignee:', task.assignee.name);
            return task.assignee.name;
        }
        
        // 4. Coba dari data API string fields
        if (task.assignee_name) return task.assignee_name;
        if (task.assigned_to_name) return task.assigned_to_name;
        
        // 5. Fallback: Cari assigned_to (single ID) di local state
        if (task.assigned_to && state.karyawanList.length > 0) {
            const karyawan = state.karyawanList.find(k => k.id == task.assigned_to);
            if (karyawan) {
                console.log('Found single assigned_to:', karyawan.name);
                return karyawan.name || karyawan.nama;
            }
        }
        
        // 6. Last resort - return debug string
        const debugId = assignedIds.length > 0 ? assignedIds.join(', ') : task.assigned_to || '?';
        console.log('Returning debug string:', debugId);
        return `Unknown (${debugId})`;
    },
    
    // Fungsi untuk mendapatkan nama pembuat tugas dari karyawan
    getCreatedByName: (task) => {
        // 1. Coba dari data task langsung
        if (task.created_by_name) return task.created_by_name;
        if (task.creator_name) return task.creator_name;
        
        // 2. Coba dari relasi
        if (task.creator && task.creator.name) {
            return task.creator.name;
        }
        
        // 3. Fallback: Cari di local state
        if (task.created_by && state.karyawanList.length > 0) {
            const creator = state.karyawanList.find(k => k.id == task.created_by);
            if (creator) {
                return creator.name || creator.nama;
            }
        }
        
        // 4. Jika tidak ketemu
        return `Karyawan (ID: ${task.created_by || '?'})`;
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

// API Functions
const api = {
    getApiEndpoint: () => {
        const userRole = state.currentUser.role;
        
        if (userRole === 'manager_divisi') {
            return '/manager-divisi/api/tasks-api';
        } else if (userRole === 'admin') {
            return '/admin/api/tasks';
        } else if (userRole === 'general_manager') {
            return '/api/general-manager/tasks';
        } else {
            return null;
        }
    },
    
    getStatisticsEndpoint: () => {
        const userRole = state.currentUser.role;
        
        if (userRole === 'manager_divisi') {
            return '/manager-divisi/api/tasks/statistics';
        } else if (userRole === 'admin') {
            return '/admin/api/tasks/statistics';
        } else if (userRole === 'general_manager') {
            return '/api/general-manager/tasks/statistics';
        } else {
            return '/api/tasks/statistics';
        }
    },
    
    getCreateTaskEndpoint: () => {
        const userRole = state.currentUser.role;
        
        if (userRole === 'manager_divisi') {
            return '/manager-divisi/tasks/createTask';
        } else if (userRole === 'admin') {
            return '/admin/tasks/createTask';
        } else if (userRole === 'general_manager') {
            return '/general_manager/tasks/createTask';
        } else {
            return null;
        }
    },

    fetchProjects: async () => {
        try {
            console.log('Fetching projects for manager divisi...');
            
            const endpoints = [
                '/manager-divisi/api/projects-dropdown',
                '/api/projects'
            ];
            
            let projectsData = [];
            
            for (const endpoint of endpoints) {
                try {
                    console.log('Trying endpoint:', endpoint);
                    const response = await fetch(endpoint, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        console.log('Response from ' + endpoint + ':', data);
                        
                        if (data.success === true && Array.isArray(data.data)) {
                            projectsData = data.data;
                            console.log('Successfully loaded ' + projectsData.length + ' projects from ' + endpoint);
                            break;
                        } else if (Array.isArray(data.data)) {
                            projectsData = data.data;
                            console.log('Successfully loaded ' + projectsData.length + ' projects from ' + endpoint);
                            break;
                        } else if (Array.isArray(data.projects)) {
                            projectsData = data.projects;
                            console.log('Successfully loaded ' + projectsData.length + ' projects from ' + endpoint);
                            break;
                        } else if (data.success === true && data.data && Array.isArray(data.data.data)) {
                            projectsData = data.data.data;
                            console.log('Successfully loaded ' + projectsData.length + ' projects from ' + endpoint);
                            break;
                        } else if (Array.isArray(data)) {
                            projectsData = data;
                            console.log('Successfully loaded ' + projectsData.length + ' projects from ' + endpoint);
                            break;
                        }
                    } else {
                        console.warn('Endpoint ' + endpoint + ' returned status ' + response.status);
                    }
                } catch (error) {
                    console.warn('Error trying endpoint ' + endpoint + ':', error.message);
                    continue;
                }
            }
            
            if (projectsData.length === 0) {
                console.warn('No projects found from any endpoint');
                utils.showToast('Tidak ada project yang tersedia', 'warning');
            } else {
                console.log(`Successfully loaded ${projectsData.length} projects`);
            }
            
            state.projectList = projectsData;
            
            // Update dropdown filter project
            render.updateProjectFilterDropdown();
            
            state.projectDetails = {};
            projectsData.forEach((project) => {
                if (project && project.id) {
                    let nama = '';
                    if (project.nama) nama = project.nama;
                    else if (project.name) nama = project.name;
                    else if (project.nama_project) nama = project.nama_project;
                    else if (project.project_name) nama = project.project_name;
                    else nama = `Project ${project.id}`;
                    
                    let deskripsi = '';
                    if (project.deskripsi) deskripsi = project.deskripsi;
                    else if (project.description) deskripsi = project.description;
                    else if (project.deskripsi_project) deskripsi = project.deskripsi_project;
                    else if (project.project_description) deskripsi = project.project_description;
                    
                    let deadline = '';
                    if (project.deadline) deadline = project.deadline;
                    else if (project.tanggal_selesai) deadline = project.tanggal_selesai;
                    else if (project.deadline_date) deadline = project.deadline_date;
                    
                    state.projectDetails[project.id] = {
                        id: project.id,
                        nama: nama,
                        deskripsi: deskripsi,
                        deadline: deadline,
                        harga: project.harga || project.budget || project.price || 0,
                        progres: project.progres || project.progress || 0,
                        status: project.status || 'pending',
                        divisi_id: project.divisi_id || project.divisi || project.division_id || null,
                        created_by: project.created_by || project.user_id || project.created_by_id || null
                    };
                }
            });
            
            console.log(`Total cached projects: ${Object.keys(state.projectDetails).length}`);
            
            return projectsData;
            
        } catch (error) {
            console.error('Error fetching projects:', error);
            utils.showToast('Gagal memuat daftar project: ' + error.message, 'error');
            state.projectList = [];
            state.projectDetails = {};
            return [];
        }
    },

    fetchProjectDetail: async (projectId) => {
        if (state.projectDetails[projectId]) {
            return state.projectDetails[projectId];
        }
        
        try {
            const endpoints = [
                `/api/projects/${projectId}`,
                `/manager-divisi/api/projects/${projectId}`,
                `/projects/${projectId}`
            ];
            
            for (const endpoint of endpoints) {
                try {
                    const response = await fetch(endpoint, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success && data.data) {
                            const project = data.data;
                            state.projectDetails[projectId] = {
                                id: project.id,
                                nama: project.nama || project.name || project.nama_project || `Project ${project.id}`,
                                deskripsi: project.deskripsi || project.description || project.deskripsi_project || '',
                                deadline: project.deadline || project.tanggal_selesai || '',
                                harga: project.harga || project.budget || 0,
                                progres: project.progres || project.progress || 0,
                                status: project.status || 'pending',
                                divisi_id: project.divisi_id || project.divisi || null,
                                created_by: project.created_by || project.user_id || null
                            };
                            return state.projectDetails[projectId];
                        }
                    }
                } catch (error) {
                    continue;
                }
            }
        } catch (error) {
            console.error('Error fetching project detail:', error);
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
        
        // Only set Content-Type for non-FormData requests
        if (!(options.body instanceof FormData)) {
            mergedOptions.headers['Content-Type'] = 'application/json';
            
            if (typeof options.body === 'object' && options.body !== null) {
                mergedOptions.body = JSON.stringify(options.body);
            }
        } else {
            // For FormData, let the browser set Content-Type with boundary
            delete mergedOptions.headers['Content-Type'];
        }
        
        try {
            const response = await fetch(url, mergedOptions);
            
            if (!response.ok) {
                const contentType = response.headers.get('content-type');
                let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                
                // Try to parse JSON error response
                if (contentType && contentType.includes('application/json')) {
                    try {
                        const errorData = await response.json();
                        console.error('Server validation errors:', errorData);
                        
                        // For 422 Validation errors, show field-specific errors
                        if (response.status === 422 && errorData.errors) {
                            const fieldErrors = Object.entries(errorData.errors)
                                .map(([field, messages]) => `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`)
                                .join('\n');
                            errorMessage = `Validasi gagal:\n${fieldErrors}`;
                        } else if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (parseError) {
                        console.error('Could not parse error JSON:', parseError);
                    }
                }
                
                const error = new Error(errorMessage);
                error.status = response.status;
                error.response = response;
                throw error;
            }
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                return data;
            }
            
            const text = await response.text();
            return text;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },
    
    fetchTasks: async () => {
        state.isLoading = true;
        utils.showLoading(true, 'all');
        
        try {
            const endpoint = api.getApiEndpoint();
            
            if (!endpoint) {
                throw new Error('Endpoint tidak tersedia untuk role Anda');
            }
            
            console.log('Fetching tasks from:', endpoint);
            const data = await api.request(endpoint);
            console.log('Tasks API response:', data);
            
            let tasks = [];
            if (data.success === true && Array.isArray(data.data)) {
                tasks = data.data;
            } else if (Array.isArray(data.data)) {
                tasks = data.data;
            } else if (Array.isArray(data)) {
                tasks = data;
            } else if (data.success === true && Array.isArray(data.tasks)) {
                tasks = data.tasks;
            }
            
            console.log('Loaded tasks:', tasks.length);
            
            // Debug: Log assigned_names for tasks with multiple assignees
            tasks.forEach((task, index) => {
                if (task.assigned_to_ids && Array.isArray(task.assigned_to_ids) && task.assigned_to_ids.length > 1) {
                    console.log(`Task ${index + 1} (ID: ${task.id}) - Multi-assigned:`, {
                        assigned_to_ids: task.assigned_to_ids,
                        assigned_names: task.assigned_names,
                        assignee_name: task.assignee_name
                    });
                }
            });
            
            // Process tasks
            tasks.forEach((task) => {
                task.is_overdue = utils.checkOverdue(task.deadline, task.status);
                
                let projectName = '';
                
                if (task.project_name) projectName = task.project_name;
                else if (task.project_nama) projectName = task.project_nama;
                
                if (!projectName && task.project_id && state.projectDetails[task.project_id]) {
                    projectName = state.projectDetails[task.project_id].nama;
                }
                
                if (!projectName && task.project_id) {
                    const project = state.projectList.find(p => p.id == task.project_id);
                    if (project) {
                        projectName = project.nama || project.name || project.nama_project || project.project_name || `Project ${project.id}`;
                    }
                }
                
                if (!projectName) {
                    projectName = task.project_nama || 'Tidak ada Project';
                }
                
                task.project_name = projectName;
            });
            
            state.allTasks = tasks;
            
            // Pisahkan tugas berdasarkan tipe
            state.tasksRegular = tasks.filter(task => task.type !== 'task_from_karyawan');
            state.tasksKaryawan = tasks.filter(task => task.type === 'task_from_karyawan');
            
            state.filteredTasksRegular = [...state.tasksRegular];
            state.filteredTasksKaryawan = [...state.tasksKaryawan];
            
            // Update tab counts
            document.getElementById('tabRegularCount').textContent = state.tasksRegular.length;
        document.getElementById('tabKaryawanCount').textContent = state.tasksKaryawan.length;
            
            // Render table dengan data terbaru
            render.renderTable();
            
            try {
                await api.fetchStatistics();
            } catch(e) {
                console.log('Using calculated statistics:', e);
                api.calculateStatsFromTasks();
            }
            
        } catch (error) {
            console.error('Error fetching tasks:', error);
            utils.showToast('Gagal memuat data tugas', 'error');
            state.allTasks = [];
            state.tasksRegular = [];
            state.tasksKaryawan = [];
            state.filteredTasksRegular = [];
            state.filteredTasksKaryawan = [];
            render.renderTable();
        } finally {
            state.isLoading = false;
            utils.showLoading(false, 'all');
        }
    },
    
    fetchStatistics: async () => {
        try {
            const endpoint = api.getStatisticsEndpoint();
            
            if (!endpoint) {
                api.calculateStatsFromTasks();
                return;
            }
            
            const data = await api.request(endpoint);
            
            if (data.success !== false) {
                const stats = data.data || data;
                document.getElementById('totalTasks').textContent = stats.total || 0;
                document.getElementById('inProgressTasks').textContent = stats.in_progress || stats.proses || 0;
                document.getElementById('completedTasks').textContent = stats.completed || stats.selesai || 0;
                document.getElementById('overdueTasks').textContent = stats.overdue || 0;
            } else {
                api.calculateStatsFromTasks();
            }
        } catch (error) {
            console.error('Error fetching statistics:', error);
            api.calculateStatsFromTasks();
        }
    },
    
    calculateStatsFromTasks: () => {
        const stats = {
            total: state.allTasks.length,
            in_progress: state.allTasks.filter(task => 
                task.status === 'proses' || task.status === 'pending'
            ).length,
            completed: state.allTasks.filter(task => 
                task.status === 'selesai'
            ).length,
            overdue: state.allTasks.filter(task => 
                utils.checkOverdue(task.deadline, task.status)
            ).length
        };
        
        document.getElementById('totalTasks').textContent = stats.total;
        document.getElementById('inProgressTasks').textContent = stats.in_progress;
        document.getElementById('completedTasks').textContent = stats.completed;
        document.getElementById('overdueTasks').textContent = stats.overdue;
    },
    
    fetchKaryawan: async () => {
        try {
            const userRole = state.currentUser.role;
            
            let endpoint;
            
            if (userRole === 'manager_divisi') {
                endpoint = '/manager-divisi/api/karyawan-dropdown';
            } else {
                endpoint = '/api/users/data?role=karyawan';
            }
            
            console.log('Fetching karyawan from endpoint:', endpoint);
            
            const response = await fetch(endpoint, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': "XMLHttpRequest"
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                console.error(`HTTP Error: ${response.status} ${response.statusText}`);
                throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
            }

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Response is not JSON, content-type:', contentType);
                const text = await response.text();
                console.error('Response text:', text.substring(0, 200));
                throw new Error('Response is not JSON. Server may have returned an error page.');
            }

            const data = await response.json();
            console.log('Karyawan raw response:', data);
            console.log('Response structure check:', {
                'data.success': data.success,
                'data.data type': Array.isArray(data.data) ? 'array' : typeof data.data,
                'data.data length': Array.isArray(data.data) ? data.data.length : 'N/A',
                'is Array': Array.isArray(data),
                'data keys': Object.keys(data)
            });
            
            if (data.success === true && data.data && Array.isArray(data.data)) {
                console.log('Matched: data.success && data.data array');
                state.karyawanList = data.data;
            } else if (Array.isArray(data)) {
                console.log('Matched: direct array');
                state.karyawanList = data;
            } else if (data.success === true && Array.isArray(data.karyawan)) {
                console.log('Matched: data.karyawan array');
                state.karyawanList = data.karyawan;
            } else if (data.data && Array.isArray(data.data)) {
                console.log('Matched: data.data array (no success check)');
                state.karyawanList = data.data;
            } else {
                console.warn('No matching array format found, setting empty list');
                state.karyawanList = [];
            }
            
            console.log('Karyawan before mapping:', state.karyawanList.length, state.karyawanList);
            
            state.karyawanList = state.karyawanList.map(karyawan => {
                const cleanKaryawan = { ...karyawan };
                if (karyawan.divisi && typeof karyawan.divisi === 'string') {
                    cleanKaryawan.divisi = utils.cleanDivisiString(karyawan.divisi);
                }
                return cleanKaryawan;
            });
            
            console.log('Karyawan loaded:', state.karyawanList.length, state.karyawanList);
            
        } catch (error) {
            console.error('Failed to fetch karyawan:', error);
            console.error('Error stack:', error.stack);
            utils.showToast('Gagal memuat daftar karyawan: ' + error.message, 'error');
            state.karyawanList = [];
        }
    },
    
    createTask: async (formData) => {
        try {
            // Check if there's a file attachment
            const attachment = formData.get('attachment');
            
            // Build final data - keep FormData if there's attachment, otherwise convert to object
            let finalData = formData;
            
            if (!attachment) {
                // No file attachment - convert FormData to object
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
                    data['assigned_to'] = assignedToArray;
                }
                
                if (!data.target_divisi_id || data.target_divisi_id === '') {
                    data.target_divisi_id = state.currentUser.divisi_id;
                }
                
                console.log('Sending task data (no attachment):', data);
                console.log('Assigned to count:', assignedToArray.length);
                finalData = data;
            } else {
                // Has file attachment - rebuild FormData with converted assigned_to array
                const newFormData = new FormData();
                const assignedToArray = [];
                
                formData.forEach((value, key) => {
                    if (key === 'assigned_to[]') {
                        assignedToArray.push(value);
                    } else {
                        newFormData.append(key, value);
                    }
                });
                
                if (assignedToArray.length > 0) {
                    newFormData.append('assigned_to', JSON.stringify(assignedToArray));
                }
                
                if (!formData.get('target_divisi_id') || formData.get('target_divisi_id') === '') {
                    newFormData.set('target_divisi_id', state.currentUser.divisi_id);
                }
                
                console.log('Sending task data (with attachment)');
                console.log('Assigned to count:', assignedToArray.length);
                finalData = newFormData;
            }
            
            const endpoint = api.getCreateTaskEndpoint();
            
            if (!endpoint) {
                throw new Error('Anda tidak memiliki izin untuk membuat tugas');
            }
            
            const response = await api.request(endpoint, {
                method: 'POST',
                body: finalData
            });
            
            const message = response.message || 'Tugas berhasil dibuat';
            utils.showToast(message, 'success');
            
            console.log('Task creation response:', response);
            await api.fetchTasks();
            
            return response;
        } catch (error) {
            console.error('Error creating task:', error);
            throw error;
        }
    },
    
    updateTask: async (id, formData) => {
        try {
            console.log('Updating task:', id);
            const userRole = state.currentUser.role;
            let endpoint;
            
            if (userRole === 'manager_divisi') {
                endpoint = `/manager-divisi/tasks/${id}`;
            } else if (userRole === 'admin') {
                endpoint = `/admin/tasks/${id}`;
            } else if (userRole === 'general_manager') {
                endpoint = `/general_manager/tasks/${id}`;
            } else {
                throw new Error('Anda tidak memiliki izin untuk mengedit tugas');
            }
            
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            console.log('Sending update request to:', endpoint);
            console.log('Data being sent:', data);
            
            const response = await api.request(endpoint, {
                method: 'PUT',
                body: data
            });
            
            console.log('Update response:', response);
            
            utils.showToast('Tugas berhasil diperbarui', 'success');
            await api.fetchTasks();
            
            return response;
        } catch (error) {
            console.error('Error updating task:', error);
            throw error;
        }
    },
    
    deleteTask: async (id) => {
        try {
            const userRole = state.currentUser.role;
            let endpoint;
            
            if (userRole === 'manager_divisi') {
                endpoint = `/manager-divisi/tasks/${id}`;
            } else if (userRole === 'admin') {
                endpoint = `/admin/tasks/${id}`;
            } else if (userRole === 'general_manager') {
                endpoint = `/general_manager/tasks/${id}`;
            } else {
                throw new Error('Anda tidak memiliki izin untuk menghapus tugas');
            }
            
            const response = await api.request(endpoint, {
                method: 'DELETE'
            });
            
            utils.showToast('Tugas berhasil dihapus', 'success');
            await api.fetchTasks();
            
            return response;
        } catch (error) {
            throw error;
        }
    },
    
    getTaskDetail: async (id) => {
        try {
            const userRole = state.currentUser.role;
            
            // Find the task from loaded tasks to check its type
            const taskFromState = state.allTasks.find(t => t.id === id);
            console.log('Task from state:', taskFromState);
            console.log('Task type:', taskFromState?.type);
            
            let endpoint;
            let isKaryawanTask = false;
            
            // Handle different task types
            if (taskFromState?.type === 'task_from_karyawan') {
                // Task dari karyawan - jangan gunakan endpoint spesifik, tapi return dari state
                console.log('Task dari karyawan - menggunakan data dari state:', taskFromState);
                return taskFromState;
            } else if (userRole === 'manager_divisi') {
                endpoint = `/manager-divisi/api/tasks/${id}`;
            } else if (userRole === 'admin') {
                endpoint = `/admin/tasks/${id}`;
            } else if (userRole === 'general_manager') {
                endpoint = `/general_manager/tasks/${id}`;
            } else {
                endpoint = `/api/tasks/${id}`;
            }
            
            console.log('Fetching task detail from:', endpoint);
            const data = await api.request(endpoint);
            console.log('Task detail response:', data);
            
            if (data.success === true && data.data) {
                console.log('Returning task data (success format):', data.data);
                return data.data;
            } else if (data.task) {
                console.log('Returning task data (task format):', data.task);
                return data.task;
            } else if (data) {
                console.log('Returning raw data:', data);
                return data;
            } else {
                throw new Error('Data tugas tidak ditemukan');
            }
        } catch (error) {
            console.error('Error in getTaskDetail:', error);
            throw error;
        }
    }
};

// Render Functions
const render = {
    updateProjectFilterDropdown: () => {
        const projectFilter = document.getElementById('projectFilter');
        if (!projectFilter) return;
        
        const currentValue = projectFilter.value;
        
        while (projectFilter.options.length > 1) {
            projectFilter.remove(1);
        }
        
        state.projectList.forEach((project) => {
            const projectName = project.nama || project.name || project.nama_project || `Project ${project.id}`;
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
        
        // Filter untuk tugas regular
        state.filteredTasksRegular = state.tasksRegular.filter(task => {
            const searchMatch = !searchTerm || 
                ((task.nama_tugas && task.nama_tugas.toLowerCase().includes(searchTerm)) ||
                 (task.judul && task.judul.toLowerCase().includes(searchTerm)) ||
                 (task.deskripsi && task.deskripsi.toLowerCase().includes(searchTerm)) ||
                 (utils.getAssigneeName(task) && utils.getAssigneeName(task).toLowerCase().includes(searchTerm)) ||
                 (task.project_name && task.project_name.toLowerCase().includes(searchTerm)));
            
            const statusMatch = statusFilter === 'all' || task.status === statusFilter;
            
            let projectMatch = true;
            if (selectedProjectId !== 'all') {
                if (task.project_id) {
                    projectMatch = task.project_id == selectedProjectId;
                } else {
                    const taskProjectName = task.project_name || task.project_nama || '';
                    const selectedProject = state.projectList.find(p => p.id == selectedProjectId);
                    if (selectedProject) {
                        const selectedProjectName = selectedProject.nama || selectedProject.name || selectedProject.nama_project || '';
                        projectMatch = taskProjectName.includes(selectedProjectName) || 
                                      selectedProjectName.includes(taskProjectName);
                    } else {
                        projectMatch = false;
                    }
                }
            }
            
            return searchMatch && statusMatch && projectMatch;
        });
        
        // Filter untuk tugas dari karyawan
        state.filteredTasksKaryawan = state.tasksKaryawan.filter(task => {
            const searchMatch = !searchTerm || 
                ((task.nama_tugas && task.nama_tugas.toLowerCase().includes(searchTerm)) ||
                 (task.judul && task.judul.toLowerCase().includes(searchTerm)) ||
                 (task.deskripsi && task.deskripsi.toLowerCase().includes(searchTerm)) ||
                 (utils.getAssigneeName(task) && utils.getAssigneeName(task).toLowerCase().includes(searchTerm)) ||
                 (utils.getCreatedByName(task) && utils.getCreatedByName(task).toLowerCase().includes(searchTerm)) ||
                 (task.project_name && task.project_name.toLowerCase().includes(searchTerm)));
            
            const statusMatch = statusFilter === 'all' || task.status === statusFilter;
            
            let projectMatch = true;
            if (selectedProjectId !== 'all') {
                if (task.project_id) {
                    projectMatch = task.project_id == selectedProjectId;
                } else {
                    const taskProjectName = task.project_name || task.project_nama || '';
                    const selectedProject = state.projectList.find(p => p.id == selectedProjectId);
                    if (selectedProject) {
                        const selectedProjectName = selectedProject.nama || selectedProject.name || selectedProject.nama_project || '';
                        projectMatch = taskProjectName.includes(selectedProjectName) || 
                                      selectedProjectName.includes(taskProjectName);
                    } else {
                        projectMatch = false;
                    }
                }
            }
            
            return searchMatch && statusMatch && projectMatch;
        });
        
        // Reset halaman untuk tab aktif
        if (state.activeTab === 'regular') {
            state.currentPageRegular = 1;
        } else {
            state.currentPageKaryawan = 1;
        }
        
        render.renderTable();
        
        // Update panel titles
     
        let panelTitleKaryawan = 'Tugas dari Karyawan';
        
        if (selectedProjectId !== 'all') {
            const selectedProject = state.projectList.find(p => p.id == selectedProjectId);
            if (selectedProject) {
                const projectName = selectedProject.nama || selectedProject.name || selectedProject.nama_project || `Project ${selectedProjectId}`;
                const truncatedName = utils.truncateText(projectName, 30);
        
                panelTitleKaryawan = `Tugas dari Karyawan: ${truncatedName}`;
            }
        }
        
        document.getElementById('panelTitleRegular').textContent = `${panelTitleRegular} (${state.filteredTasksRegular.length})`;
        document.getElementById('panelTitleKaryawan').textContent = `${panelTitleKaryawan} (${state.filteredTasksKaryawan.length})`;
        
        // Update total counts
        document.getElementById('totalCountRegular').textContent = state.filteredTasksRegular.length;
        document.getElementById('totalCountKaryawan').textContent = state.filteredTasksKaryawan.length;
    },
    
    switchTab: (tabName) => {
        state.activeTab = tabName;
        
        // Update tab buttons
        document.getElementById('tabRegular').classList.toggle('active', tabName === 'regular');
        document.getElementById('tabKaryawan').classList.toggle('active', tabName === 'karyawan');
        
        // Show/hide panels
        document.getElementById('panelRegular').style.display = tabName === 'regular' ? 'block' : 'none';
        document.getElementById('panelKaryawan').style.display = tabName === 'karyawan' ? 'block' : 'none';
        
        // Render table for active tab
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
        
        const desktopTableBody = document.getElementById('desktopTableBodyRegular');
        desktopTableBody.innerHTML = '';
        
        currentTasks.forEach((task, index) => {
            const rowNumber = startIndex + index + 1;
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            const namaTugas = task.nama_tugas || task.judul || '';
            
            // BARU: Build assignee name yang pasti handle multiple assignees
            let assigneeName = 'Belum ditugaskan';
            
            // 1. Jika ada assigned_names dari backend (sudah comma-separated)
            if (task.assigned_names && typeof task.assigned_names === 'string' && task.assigned_names.trim()) {
                assigneeName = task.assigned_names;
                console.log(`[Table] Task ${task.id}: Using assigned_names from backend:`, assigneeName);
            }
            // 2. Jika ada assigned_to_ids array dengan multiple entries
            else if (task.assigned_to_ids && Array.isArray(task.assigned_to_ids) && task.assigned_to_ids.length > 0) {
                let names = [];
                task.assigned_to_ids.forEach(id => {
                    // Cari nama di karyawanList
                    const karyawan = state.karyawanList.find(k => k.id == id);
                    if (karyawan) {
                        names.push(karyawan.name || karyawan.nama);
                    } else {
                        names.push(`ID: ${id}`);
                    }
                });
                if (names.length > 0) {
                    assigneeName = names.join(', ');
                    console.log(`[Table] Task ${task.id}: Built from assigned_to_ids array:`, assigneeName);
                }
            }
            // 3. Fallback ke assignee_name dari API
            else if (task.assignee_name && task.assignee_name.trim()) {
                assigneeName = task.assignee_name;
                console.log(`[Table] Task ${task.id}: Using assignee_name:`, assigneeName);
            }
            // 4. Fallback ke assigned_to lookup di karyawanList
            else if (task.assigned_to && state.karyawanList.length > 0) {
                const karyawan = state.karyawanList.find(k => k.id == task.assigned_to);
                if (karyawan) {
                    assigneeName = karyawan.name || karyawan.nama;
                    console.log(`[Table] Task ${task.id}: Using assigned_to lookup:`, assigneeName);
                }
            }
            
            const isAssigneeUnknown = assigneeName.includes('Unknown') || assigneeName === 'Belum ditugaskan';
            const taskTypeInfo = utils.getTaskTypeLabel(task);

            row.innerHTML = `
                <td class="text-center">
                    ${rowNumber}
                </td>
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
                <td class="${isAssigneeUnknown ? 'text-red-600 font-bold bg-red-50' : 'text-gray-700'}">
                    ${utils.escapeHtml(assigneeName)}
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
                        
                        ${state.currentUser.role !== 'karyawan' || task.assigned_to == state.currentUser.id ? `
                        <button onclick="modal.showEdit(${task.id})" class="p-2 rounded-full hover:bg-green-50 transition-colors" title="Edit">
                            <span class="material-icons-outlined text-green-600 text-lg">edit</span>
                        </button>
                        <button onclick="confirmDelete(${task.id})" class="p-2 rounded-full hover:bg-red-50 transition-colors" title="Hapus">
                            <span class="material-icons-outlined text-red-600 text-lg">delete</span>
                        </button>
                        ` : ''}
                    </div>
                </td>
            `;
            
            desktopTableBody.appendChild(row);
        });
        
        const mobileCards = document.getElementById('mobileCardsRegular');
        mobileCards.innerHTML = currentTasks.map((task) => {
            const assigneeName = utils.getAssigneeName(task);
            const isAssigneeUnknown = assigneeName.includes('Unknown');
            const taskTypeInfo = utils.getTaskTypeLabel(task);

            return `
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="text-xs text-primary font-medium">
                                ${utils.escapeHtml(task.project_name || 'Tidak ada Project')}
                            </div>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-1">
                            <div class="text-sm font-medium">Tugas: ${utils.escapeHtml(task.nama_tugas || task.judul || '')}</div>
                        </h4>
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
                        ${state.currentUser.role !== 'karyawan' || task.assigned_to == state.currentUser.id ? `
                        <button onclick="modal.showEdit(${task.id})" class="p-1 hover:bg-green-50 rounded">
                            <span class="material-icons-outlined text-green-600">edit</span>
                        </button>
                        <button onclick="confirmDelete(${task.id})" class="p-1 hover:bg-red-50 rounded">
                            <span class="material-icons-outlined text-red-600">delete</span>
                        </button>
                        ` : ''}
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 mb-3">${utils.truncateText(task.deskripsi || '', 80)}</p>
                
                <div class="flex justify-between items-center text-sm">
                    <div>
                        <span class="text-gray-700 font-medium ${isAssigneeUnknown ? 'text-red-600 bg-red-50 px-1 rounded' : ''}">${utils.escapeHtml(assigneeName)}</span>
                    </div>
                    ${task.is_overdue ? '<span class="text-red-600 text-xs font-semibold">Terlambat</span>' : ''}
                </div>
            </div>
        `}).join('');
        
        document.getElementById('noDataMessageRegular').style.display = 'none';
        document.getElementById('desktopTableRegular').style.display = 'block';
        document.getElementById('mobileCardsRegular').style.display = window.innerWidth < 768 ? 'block' : 'none';
        
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
                <td class="text-center">
                    ${rowNumber}
                </td>
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
                        
                        ${state.currentUser.role !== 'karyawan' ? `
                        <button onclick="approveTask(${task.id})" class="p-2 rounded-full hover:bg-green-50 transition-colors" title="Approve" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                            <span class="material-icons-outlined text-green-700 text-lg">check_circle</span>
                        </button>
                        <button onclick="openRevisionModal(${task.id})" class="p-2 rounded-full hover:bg-amber-50 transition-colors" title="Revisi" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                            <span class="material-icons-outlined text-amber-700 text-lg">edit_note</span>
                        </button>
                        ` : ''}
                    </div>
                </td>
            `;
            
            desktopTableBody.appendChild(row);
        });
        
        const mobileCards = document.getElementById('mobileCardsKaryawan');
        mobileCards.innerHTML = currentTasks.map((task) => {
            const assigneeName = utils.getAssigneeName(task);
            const createdByName = utils.getCreatedByName(task);
            const taskTypeInfo = utils.getTaskTypeLabel(task);

            return `
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="text-xs text-amber-600 font-medium">
                                ${utils.escapeHtml(task.project_name || 'Tidak ada Project')}
                            </div>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-1">
                            <div class="text-sm font-medium">Tugas: ${utils.escapeHtml(task.nama_tugas || task.judul || '')}</div>
                        </h4>
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
                        ${state.currentUser.role !== 'karyawan' ? `
                        <button onclick="approveTask(${task.id})" class="p-1 hover:bg-green-50 rounded" title="Approve" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                            <span class="material-icons-outlined text-green-700">check_circle</span>
                        </button>
                        <button onclick="openRevisionModal(${task.id})" class="p-1 hover:bg-amber-50 rounded" title="Revisi" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                            <span class="material-icons-outlined text-amber-700">edit_note</span>
                        </button>
                        ` : ''}
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
            </div>
        `}).join('');
        
        document.getElementById('noDataMessageKaryawan').style.display = 'none';
        document.getElementById('desktopTableKaryawan').style.display = 'block';
        document.getElementById('mobileCardsKaryawan').style.display = window.innerWidth < 768 ? 'block' : 'none';
        
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

// Modal Functions
const modal = {
    showDetail: async (id) => {
        try {
            console.log('Opening detail modal for task:', id);
            const task = await api.getTaskDetail(id);
            console.log('Task loaded for detail view:', task);
            
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
                    
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">File Unggahan</h4>
                        <div class="bg-gray-50 p-3 rounded-lg mt-1 max-h-64 overflow-y-auto" id="filesContainer">
                            <p class="text-gray-500 text-sm">Memuat file...</p>
                        </div>
                    </div>
                    
                    ${isFromKaryawan && state.currentUser.role !== 'karyawan' && task.status !== 'selesai' ? `
                    <div class="pt-4 border-t flex gap-2">
                        <button class="close-modal btn-secondary flex-1 py-2">Tutup</button>
                        <button onclick="openRevisionModal(${task.id})" class="btn-secondary flex-1 py-2">Revisi</button>
                        <button onclick="approveTask(${task.id})" class="btn-primary flex-1 py-2" ${task.status === 'selesai' ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>Approve</button>
                    </div>
                    ` : `
                    <div class="pt-4 border-t">
                        <button class="close-modal btn-secondary w-full py-2">Tutup</button>
                    </div>
                    `}
                </div>
            `;
            
            utils.createModal('Detail Tugas', modalContent);
            
            // Load and display task files
            setTimeout(async () => {
                try {
                    const filesContainer = document.getElementById('filesContainer');
                    if (!filesContainer) return;

                    let files = [];

                    // For tasks from karyawan, use submission file if available.
                    if (isFromKaryawan && task.submission_file) {
                        files = [{
                            path: task.submission_file,
                            url: task.submission_url,
                            filename: task.submission_file.split('/').pop() || 'submission_file'
                        }];
                    } else {
                        files = task.files || [];

                        // Fallback fetch if task detail does not include files.
                        if (!files || files.length === 0) {
                            const response = await fetch(`/api/tasks/${id}/files`);
                            if (!response.ok) {
                                filesContainer.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada file yang diunggah</p>';
                                return;
                            }

                            const data = await response.json();
                            files = data.files || [];
                        }
                    }
                    
                    if (files.length === 0) {
                        filesContainer.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada file yang diunggah</p>';
                        return;
                    }
                    
                    let filesHTML = '';
                    files.forEach(file => {
                        const fileName = file.filename || file.name || 'File';
                        let filePath = file.submission_url || file.download_url || file.url || file.path || '#';

                        // If path is relative, make it absolute.
                        if (filePath.startsWith('tugas_karyawan/') || filePath.startsWith('tasks/') || (!filePath.startsWith('http') && !filePath.startsWith('/'))) {
                            filePath = '/storage/' + filePath;
                        }
                        
                        const fileSize = file.size ? `(${typeof file.size === 'string' ? file.size : (file.size / 1024).toFixed(2) + ' KB'})` : '';
                        const mimeType = file.mime_type || '';
                        
                        // Determine if file is image based on extension or mime type.
                        const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp'];
                        const isImage = mimeType.startsWith('image/') || 
                                       imageExtensions.some(ext => fileName.toLowerCase().endsWith(ext));
                        
                        if (isImage) {
                            filesHTML += `
                            <div class="mb-3 p-2 bg-white rounded border border-gray-200">
                                <p class="text-xs font-medium text-gray-600 mb-2 flex items-center gap-1">
                                    <span class="material-icons-outlined text-sm">image</span>
                                    ${utils.escapeHtml(fileName)} ${fileSize}
                                </p>
                                <img src="${filePath}" alt="${utils.escapeHtml(fileName)}" class="max-w-full max-h-48 rounded cursor-pointer hover:opacity-90" onclick="window.open('${filePath}', '_blank')"
                                style="object-fit: contain;">
                            </div>
                            `;
                        } else {
                            filesHTML += `
                            <div class="mb-2 p-2 bg-white rounded border border-gray-200 flex items-center justify-between">
                                <a href="${filePath}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-2 flex-1 truncate">
                                    <span class="material-icons-outlined text-sm">attach_file</span>
                                    <span class="truncate">${utils.escapeHtml(fileName)} ${fileSize}</span>
                                </a>
                                <a href="${filePath}" download class="ml-2 p-1 text-gray-600 hover:bg-gray-100 rounded" title="Download">
                                    <span class="material-icons-outlined text-sm">download</span>
                                </a>
                            </div>
                            `;
                        }
                    });
                    
                    filesContainer.innerHTML = filesHTML;
                } catch (error) {
                    console.error('Error loading task files:', error);
                    const filesContainer = document.getElementById('filesContainer');
                    if (filesContainer) {
                        filesContainer.innerHTML = '<p class="text-gray-500 text-sm">Gagal memuat file</p>';
                    }
                }
            }, 100);
            
        } catch (error) {
            console.error('Error showing detail:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            utils.showToast('Gagal memuat detail tugas: ' + error.message, 'error');
        }
    },
    
    showEdit: async (id) => {
        try {
            console.log('Opening edit modal for task:', id);
            const task = await api.getTaskDetail(id);
            console.log('Task loaded for editing:', task);
            
            let karyawanOptions = '';
            let hasKaryawanInDivisi = false;
            
            if (state.karyawanList.length > 0) {
                // Handle both single value and array for assigned_to
                const assignedIds = Array.isArray(task.assigned_to) 
                    ? task.assigned_to 
                    : (task.assigned_to ? [task.assigned_to] : []);
                
                state.karyawanList.forEach((k) => {
                    const karyawanName = k.name || k.nama || 'Tanpa Nama';
                    const karyawanId = k.id || k.user_id;
                    const isSelected = assignedIds.includes(karyawanId.toString()) || assignedIds.includes(karyawanId) ? 'selected' : '';
                    
                    karyawanOptions += `
                        <option value="${karyawanId}" ${isSelected}>
                            ${utils.escapeHtml(karyawanName)}
                        </option>
                    `;
                    
                    hasKaryawanInDivisi = true;
                });
            }
            
            let projectOptions = '<option value="">-- Pilih Project --</option>';
            if (state.projectList.length > 0) {
                state.projectList.forEach(p => {
                    const projectName = p.nama || p.name || p.nama_project || `Project ${p.id}`;
                    const projectId = p.id;
                    const isSelected = task.project_id == projectId ? 'selected' : '';
                    projectOptions += `<option value="${projectId}" ${isSelected}>${utils.escapeHtml(projectName)}</option>`;
                });
            }
            
            const formattedDeadline = task.deadline ? utils.formatDateForInput(task.deadline) : '';
            const namaTugasValue = task.nama_tugas || '';
            
            const modalContent = `
                <form>
                    <div class="space-y-4">
                        ${task.type === 'task_from_karyawan' ? `
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-outlined text-amber-600">info</span>
                                <span class="text-sm text-amber-800 font-medium">Tugas dari Karyawan</span>
                            </div>
                        </div>
                        ` : ''}
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project</label>
                            <select name="project_id" class="form-input w-full">
                                ${projectOptions}
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih project terkait (Opsional)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tugas <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_tugas" value="${utils.escapeHtml(namaTugasValue)}" 
                                   class="form-input" required placeholder="Masukkan nama tugas spesifik">
                            <p class="text-xs text-gray-500 mt-1">Contoh: Analisis kebutuhan, Desain UI, Pengembangan fitur X</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                            <textarea name="deskripsi" rows="3" class="form-input" required>${utils.escapeHtml(task.deskripsi || '')}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                            <input type="date" name="deadline" 
                                   value="${formattedDeadline}" 
                                   class="form-input" required>
                        </div>
                        
                        ${task.status === 'pending' ? (hasKaryawanInDivisi ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ditugaskan Kepada <span class="text-red-500">*</span></label>
                            <div class="space-y-2 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                ${state.karyawanList.map(karyawan => `
                                <div class="flex items-center">
                                    <input type="checkbox" name="assigned_to[]" value="${karyawan.id}" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           id="karyawan_${karyawan.id}">
                                    <label for="karyawan_${karyawan.id}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                        ${utils.escapeHtml(karyawan.name)}
                                    </label>
                                </div>
                                `).join('')}
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Pilih satu atau lebih karyawan</p>
                        </div>
                        ` : `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <span class="material-icons-outlined text-yellow-600">warning</span>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Tidak Ada Karyawan</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Tidak ditemukan karyawan dalam divisi ini.</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="assigned_to" value="">
                        </div>
                        `) : ''}
                        
                        <input type="hidden" name="status" value="${utils.escapeHtml(task.status || 'pending')}">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas <span class="text-red-500">*</span></label>
                            <select name="priority" class="form-input" required>
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
                await api.updateTask(id, formData);
            });
            
        } catch (error) {
            console.error('Error showing edit form:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            utils.showToast('Gagal memuat form edit: ' + error.message, 'error');
        }
    },
    
    showCreate: () => {
        try {
            // Initialize assignedIds as empty array for new task creation
            const assignedIds = [];
            
            let karyawanOptions = '';
            let hasKaryawanInDivisi = false;
            
            if (state.karyawanList.length > 0) {
                state.karyawanList.forEach((k) => {
                    const karyawanName = k.name || k.nama || 'Tanpa Nama';
                    const karyawanId = k.id || k.user_id;
                    
                    karyawanOptions += `
                        <option value="${karyawanId}">
                            ${utils.escapeHtml(karyawanName)}
                        </option>
                    `;
                    
                    hasKaryawanInDivisi = true;
                });
            }
            
            let projectOptions = '<option value="">-- Pilih Project --</option>';
            if (state.projectList.length > 0) {
                state.projectList.forEach(p => {
                    const projectName = p.nama || p.name || p.nama_project || `Project ${p.id}`; 
                    const projectId = p.id;
                    
                    projectOptions += `<option value="${projectId}">${utils.escapeHtml(projectName)}</option>`;
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
                            <p class="text-xs text-gray-500 mt-1">Pilih project untuk mengisi otomatis judul tugas</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tugas <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_tugas" class="form-input" required 
                                   placeholder="Masukkan nama tugas spesifik">
                            <p class="text-xs text-gray-500 mt-1">Contoh: Analisis kebutuhan, Desain UI, Pengembangan fitur X, Testing</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                            <textarea name="deskripsi" rows="3" class="form-input" required 
                                      placeholder="Deskripsi lengkap tugas"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                            <input type="date" name="deadline" class="form-input" required>
                            <p class="text-xs text-gray-500 mt-1">Akan diisi otomatis dari deadline project jika tersedia</p>
                        </div>
                        
                        ${hasKaryawanInDivisi ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ditugaskan Kepada <span class="text-red-500">*</span></label>
                            <div class="space-y-2 bg-gray-50 p-3 rounded-lg border border-gray-200 max-h-64 overflow-y-auto">
                                ${state.karyawanList.map(karyawan => {
                                    const isChecked = assignedIds.includes(karyawan.id) ? 'checked' : '';
                                    return `
                                    <div class="flex items-center">
                                        <input type="checkbox" name="assigned_to[]" value="${karyawan.id}" 
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               id="karyawan_edit_${karyawan.id}" ${isChecked}>
                                        <label for="karyawan_edit_${karyawan.id}" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                            ${utils.escapeHtml(karyawan.name)}
                                        </label>
                                    </div>
                                    `;
                                }).join('')}
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Pilih satu atau lebih karyawan (<span class="font-medium">${state.karyawanList.length} karyawan</span> tersedia)</p>
                        </div>
                        ` : `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <span class="material-icons-outlined text-yellow-600">warning</span>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Tidak Ada Karyawan</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Tidak ditemukan karyawan di divisi ini.</p>
                                        <p class="mt-1">Hubungi administrator untuk menambahkan karyawan ke divisi Anda.</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="assigned_to" value="">
                        </div>
                        `}
                        
                        <input type="hidden" name="status" value="pending">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas <span class="text-red-500">*</span></label>
                            <select name="priority" class="form-input" required>
                                <option value="low">Rendah</option>
                                <option value="medium" selected>Sedang</option>
                                <option value="high">Tinggi</option>
                                <option value="urgent">Sangat Mendesak</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="2" class="form-input" 
                                      placeholder="Tambahkan catatan (opsional)"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File Lampiran (Opsional)</label>
                            <input type="file" name="attachment" class="form-input" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <p class="text-xs text-gray-500 mt-1">Format: Gambar, PDF, Word, Excel, PowerPoint | Maks: 10MB</p>
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
                formData.set('status', 'pending');
                await api.createTask(formData);
            });
            
        } catch (error) {
            console.error('Error showing create form:', error);
            utils.showToast('Gagal memuat form tambah tugas', 'error');
        }
    }
};

// Approve function untuk tugas dari karyawan
window.approveTask = async (id) => {
    if (!confirm('Setujui tugas ini? Tugas akan ditandai SELESAI.')) return;
    
    try {
        const response = await fetch(`/manager-divisi/api/tugas-karyawan/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: 'approved', status: 'selesai' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            utils.showToast('Tugas berhasil disetujui', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            utils.showToast(data.message || 'Gagal menyetujui tugas', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        utils.showToast('Terjadi kesalahan', 'error');
    }
};

/**
 * REVISION SYSTEM - UPDATED & CLEANED
 * Menghapus duplikasi fungsi dan memastikan penanganan error yang tepat.
 */

// 1. Fungsi Utama untuk Membuka Modal
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

    // Pastikan utils.createModal tersedia di script global kamu
    utils.createModal('Kirim Revisi', modalContent, async (formData) => {
        const notes = formData.get('revision_notes')?.toString().trim();
        
        if (!notes) {
            utils.showToast('Keterangan revisi wajib diisi', 'warning');
            return;
        }

        try {
            await window.reviseTask(id, notes);
        } catch (error) {
            // Error ditangani di dalam reviseTask
            throw error; 
        }
    });
};

// 2. Fungsi API untuk Mengirim Data ke Server
window.reviseTask = async (id, notes) => {
    try {
        const endpoint = `/manager-divisi/api/tugas-karyawan/${id}/approve`;
        const body = { 
            action: 'returned', 
            notes: notes 
        };

        // Menggunakan fetch manual jika object 'api' tidak memiliki method request
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success || response.ok) {
            utils.showToast('Revisi berhasil dikirim ke karyawan', 'success');
            
            // Reload atau panggil ulang data agar status berubah
            if (typeof api !== 'undefined' && typeof api.fetchTasks === 'function') {
                await api.fetchTasks();
            } else {
                setTimeout(() => location.reload(), 1000);
            }
        } else {
            throw new Error(data.message || 'Gagal mengirim revisi');
        }
    } catch (error) {
        console.error('Error returning task for revision:', error);
        utils.showToast(error.message || 'Terjadi kesalahan sistem', 'error');
        throw error;
    }
};

// Debug Functions
window.debugProjects = () => {
    console.log('=== PROJECTS DEBUG ===');
    console.log('Project list count:', state.projectList.length);
    console.log('Cached projects:', Object.keys(state.projectDetails).length);
    
    if (state.projectList.length > 0) {
        console.log('First project in list:', state.projectList[0]);
    }
};

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    
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
    document.getElementById('searchInput').addEventListener('input', () => {
        render.filterTasks();
    });
    
    document.getElementById('statusFilter').addEventListener('change', () => {
        render.filterTasks();
    });
    
    const projectFilter = document.getElementById('projectFilter');
    if (projectFilter) {
        projectFilter.addEventListener('change', () => {
            render.filterTasks();
        });
    }
    
    document.getElementById('refreshBtn').addEventListener('click', () => {
        api.fetchProjects().then(() => {
            api.fetchTasks();
            utils.showToast('Data tugas diperbarui', 'success');
        });
    });
    
    // Tab navigation
    document.getElementById('tabRegular').addEventListener('click', () => {
        render.switchTab('regular');
    });
    
    document.getElementById('tabKaryawan').addEventListener('click', () => {
        render.switchTab('karyawan');
    });
    
    document.getElementById('closeToast').addEventListener('click', () => {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        }
    });
    
    // Pagination event listeners for regular tasks
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
    
    // Pagination event listeners for karyawan tasks
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
    
    // Function to confirm and delete task
    window.confirmDelete = async (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
            try {
                await api.deleteTask(id);
                // api.deleteTask already shows success toast
            } catch (error) {
                console.error('Error deleting task:', error);
                utils.showToast('Gagal menghapus tugas: ' + error.message, 'error');
            }
        }
    };
    
    window.addEventListener('resize', () => {
        if (state.filteredTasksRegular.length > 0) {
            render.renderRegularTable();
        } else if (state.activeTab === 'karyawan' && state.filteredTasksKaryawan.length > 0) {
            render.renderKaryawanTable();
        }
    });
    
    // Initialize
    const init = async () => {
        try {
            console.log('Starting initialization...');
            
            await api.fetchProjects();
            console.log('After fetchProjects:', {
                projectList: state.projectList.length,
                projectListData: state.projectList
            });
            
            await api.fetchKaryawan();
            console.log('After fetchKaryawan:', {
                karyawanList: state.karyawanList.length,
                karyawanListData: state.karyawanList
            });
            
            await api.fetchTasks();
            console.log('After fetchTasks:', {
                tasksRegular: state.tasksRegular.length,
                tasksKaryawan: state.tasksKaryawan.length
            });
            
            if (state.karyawanList.length === 0) {
                console.warn('No karyawan found after fetch');
                utils.showToast('Tidak ada karyawan yang tersedia.', 'warning');
            }
            
            console.log('Initialization complete:', {
                projects: state.projectList.length,
                karyawan: state.karyawanList.length,
                tasksRegular: state.tasksRegular.length,
                tasksKaryawan: state.tasksKaryawan.length
            });
            
            // Set initial tab
            render.switchTab('regular');
            
        } catch (error) {
            console.error('Error in initialization:', error);
            console.error('Error details:', error.stack);
            utils.showToast('Gagal memuat data awal', 'error');
        }
    };
    
    init();
});

window.modal = modal;
window.state = state
window.api = api;
window.utils = utils;
    </script>
</body>
</html>
