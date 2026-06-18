<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Tim & Divisi - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#ffffff",
                        "background-dark": "#f8fafc",
                        "sidebar-light": "#f3f4f6",
                        "sidebar-dark": "#1e293b",
                        "card-light": "#ffffff",
                        "card-dark": "#1e293b",
                        "text-light": "#1e293b",
                        "text-dark": "#f8fafc",
                        "text-muted-light": "#64748b",
                        "text-muted-dark": "#94a3b8",
                        "border-light": "#e2e8f0",
                        "border-dark": "#334155",
                        "success": "#10b981",
                        "warning": "#f59e0b",
                        "danger": "#ef4444"
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                    boxShadow: {
                        card: "0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)",
                        "card-hover": "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)"
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }

        /* Card hover effects */
        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Button styles */
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        /* Status Badge Styles */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-aktif {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-tidak-aktif {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        /* Tab Navigation Styles */
        .tab-nav {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .tab-button {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: #64748b;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-button:hover {
            color: #3b82f6;
        }

        .tab-button.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        /* Table mobile adjustments */
        @media (max-width: 639px) {
            .desktop-table {
                display: none;
            }

            .mobile-cards {
                display: block;
            }

            .desktop-pagination {
                display: none !important;
            }
        }

        @media (min-width: 640px) {
            .desktop-table {
                display: block;
            }

            .mobile-cards {
                display: none;
            }

            .mobile-pagination {
                display: none !important;
            }
        }

        /* Form input styles */
        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Panel Styles */
        .panel {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .panel-header {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .panel-body {
            padding: 1.5rem;
        }

        /* SCROLLABLE TABLE */
        .scrollable-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
        }

        .scrollable-table-container {
            scrollbar-width: auto;
            -webkit-overflow-scrolling: touch;
        }

        .scrollable-table-container::-webkit-scrollbar {
            height: 12px;
            width: 12px;
        }

        .scrollable-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }

        .scrollable-table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
            border: 2px solid #f1f5f9;
        }

        .scrollable-table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .data-table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .data-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .data-table tbody tr:hover {
            background: #f3f4f6;
        }

        .table-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Minimalist Popup Styles */
        .minimal-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 350px;
            border-left: 4px solid #10b981;
        }

        .minimal-popup.show {
            transform: translateX(0);
        }

        .minimal-popup.error {
            border-left-color: #ef4444;
        }

        .minimal-popup.warning {
            border-left-color: #f59e0b;
        }

        .minimal-popup-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .minimal-popup.success .minimal-popup-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .minimal-popup.error .minimal-popup-icon {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .minimal-popup.warning .minimal-popup-icon {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .minimal-popup-content {
            flex-grow: 1;
        }

        .minimal-popup-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .minimal-popup-message {
            font-size: 14px;
            color: #64748b;
        }

        .minimal-popup-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .minimal-popup-close:hover {
            background-color: #f1f5f9;
            color: #64748b;
        }

        /* Filter Dropdown Styles */
        .filter-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px;
            min-width: 200px;
            z-index: 100;
            display: none;
        }

        .filter-dropdown.show {
            display: block;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-option:hover {
            color: #3b82f6;
        }

        .filter-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .filter-option label {
            cursor: pointer;
            user-select: none;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        .filter-actions button {
            flex: 1;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .filter-apply {
            background-color: #3b82f6;
            color: white;
        }

        .filter-apply:hover {
            background-color: #2563eb;
        }

        .filter-reset {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .filter-reset:hover {
            background-color: #e2e8f0;
        }

        .hidden-by-filter {
            display: none !important;
        }

        /* Pagination */
        .desktop-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
        }

        .desktop-page-btn {
            min-width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .desktop-page-btn.active {
            background-color: #3b82f6;
            color: white;
        }

        .desktop-page-btn:not(.active) {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .desktop-page-btn:not(.active):hover {
            background-color: #e2e8f0;
        }

        .desktop-nav-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .desktop-nav-btn:hover:not(:disabled) {
            background-color: #e2e8f0;
        }

        .desktop-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Modal */
        .modal {
            transition: opacity 0.25s ease;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        /* Main Content Layout */
        .main-content {
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
                /* Lebar sidebar */
            }
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <!-- =========== SIDEBAR/HEADER DI ATAS =========== -->
    @include('general_manajer.templet.header')

    <!-- =========== MAIN CONTENT DI SAMPING SIDEBAR =========== -->
    <div class="main-content">
        <main class="flex-1 flex flex-col">
            <div class="flex-1 p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Manajemen Tim & Divisi</h2>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                    <div class="stat-card bg-white p-4 md:p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm text-gray-500 mb-1">Total Tim</p>
                                <p class="text-xl md:text-2xl font-bold text-blue-600" id="stat-total-tim">
                                    {{ $totalTim }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-outlined text-blue-600 text-lg md:text-xl">groups</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white p-4 md:p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm text-gray-500 mb-1">Total Divisi</p>
                                <p class="text-xl md:text-2xl font-bold text-green-600" id="stat-total-divisi">
                                    {{ $totalDivisi }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-outlined text-green-600 text-lg md:text-xl">business</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white p-4 md:p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm text-gray-500 mb-1">Tim Aktif</p>
                                <p class="text-xl md:text-2xl font-bold text-purple-600" id="stat-tim-aktif">
                                    {{ $timAktif }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-outlined text-purple-600 text-lg md:text-xl">group_work</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white p-4 md:p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm text-gray-500 mb-1">Total Anggota</p>
                                <p class="text-xl md:text-2xl font-bold text-orange-600" id="stat-total-anggota">
                                    {{ $totalAnggota }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons-outlined text-orange-600 text-lg md:text-xl">people</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="tab-nav">
                    <button id="divisiTab" class="tab-button active" data-tab="divisi">
                        <span class="material-icons-outlined align-middle mr-2">business</span>
                        Data Divisi
                    </button>
                    <button id="timTab" class="tab-button" data-tab="tim">
                        <span class="material-icons-outlined align-middle mr-2">groups</span>
                        Data Tim
                    </button>

                </div>
                
                <!-- Data Divisi Panel (Initially Hidden) -->
                <div id="divisiPanel" class="panel tab-panel ">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">business</span>
                            Data Divisi
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span
                                    class="font-semibold text-text-light" id="divisiCount">3</span> divisi</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Search Section -->
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                            <div class="relative w-full md:w-1/3">
                                <span
                                    class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                <input id="searchDivisiInput"
                                    class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                                    placeholder="Cari nama divisi..." type="text" />
                            </div>
                            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                                <button id="tambahDivisiBtn"
                                    class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                                    <span class="material-icons-outlined">add</span>
                                    <span class="hidden sm:inline">Tambah Divisi</span>
                                    <span class="sm:hidden">Tambah</span>
                                </button>
                            </div>
                        </div>

                        <!-- Desktop Table -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container table-shadow" id="scrollableDivisiTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 200px;">Nama Divisi</th>
                                            <th style="min-width: 150px;">Jumlah Tim</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="divisiTableBody">
                                        @foreach ($divisis as $item)
                                            <tr data-id="{{ $item->id }}" data-nama="{{ $item->divisi }}">
                                                <td style="min-width: 60px;">{{ $loop->iteration }}</td>
                                                <td style="min-width: 200px;">{{ $item->divisi }}</td>
                                                <td style="min-width: 150px;">{{ $item->jumlah_tim }}</td>
                                                <td style="min-width: 100px; text-align: center;">
                                                    <div class="flex justify-center gap-2">
                                                        <button
                                                            class="edit-divisi-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                            data-id='{{ $item->id }}' data-nama='{{ $item->divisi }}'>
                                                            <span class="material-icons-outlined">edit</span>
                                                        </button>
                                                        <button
                                                            class="delete-divisi-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                            data-id='{{ $item->id }}'>
                                                            <span class="material-icons-outlined">delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4" id="divisi-mobile-cards">
                            <!-- Cards will be populated by JavaScript -->
                        </div>

                        <!-- Pagination -->
                        <div id="divisiPaginationContainer" class="desktop-pagination">
                            <button id="divisiPrevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="divisiPageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="divisiNextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Data Tim Panel -->
                <div id="timPanel" class="panel tab-panel hidden">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">groups</span>
                            Data Tim
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span
                                    class="font-semibold text-text-light" id="timCount">6</span> tim</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Search and Filter Section -->
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                            <div class="relative w-full md:w-1/3">
                                <span
                                    class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                <input id="searchTimInput"
                                    class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                                    placeholder="Cari nama tim atau divisi..." type="text" />
                            </div>
                            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                                <button id="tambahTimBtn"
                                    class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                                    <span class="material-icons-outlined">add</span>
                                    <span class="hidden sm:inline">Tambah Tim</span>
                                    <span class="sm:hidden">Tambah</span>
                                </button>
                            </div>
                        </div>

                        <!-- Desktop Table -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container table-shadow" id="scrollableTimTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 200px;">Nama Tim</th>
                                            <th style="min-width: 200px;">Divisi</th>
                                            <th style="min-width: 120px; text-align: center;">Jumlah Anggota</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="timTableBody">
                                        @foreach ($tims as $index => $item)
                                            <tr data-id="{{ $item->id }}" data-nama="{{ $item->tim }}" data-divisi="{{ $item->divisi }}">
                                                <td style="min-width: 60px;">{{ $loop->iteration }}</td>
                                                <td style="min-width: 200px;">{{ $item->tim }}</td>
                                                <td style="min-width: 200px;">{{ $item->divisi }}</td>
                                                <td style="min-width: 120px; text-align: center;">{{ $item->jumlah_anggota ?? 0 }}</td>
                                                <td style="min-width: 100px; text-align: center;">
                                                    <div class="flex justify-center gap-2">
                                                        <button
                                                            class="edit-tim-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                                data-id='{{ $item->id }}' data-nama='{{ $item->tim }}' data-divisi='{{ $item->divisi }}'>
                                                            <span class="material-icons-outlined">edit</span>
                                                        </button>
                                                        <button
                                                            class="delete-tim-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                            data-id='{{ $item->id }}'>
                                                            <span class="material-icons-outlined">delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4" id="tim-mobile-cards">
                            <!-- Cards will be populated by JavaScript -->
                        </div>

                        <!-- Pagination -->
                        <div id="timPaginationContainer" class="desktop-pagination">
                            <button id="timPrevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="timPageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="timNextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>


                <footer
                    class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light mt-8">
                    Copyright ©2025 by digicity.id
                </footer>
            </div>
        </main>
    </div>

    <!-- Tambah Tim Modal -->
    <div id="tambahTimModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Tim Baru</h3>
                    <button type="button" class="close-modal text-gray-800 hover:text-gray-500"
                        data-target="tambahTimModal">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="tambahTimForm" class="space-y-4">
                    @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tim</label>
                            <input type="text" name="tim" required
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Masukkan nama tim">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <select name="divisi" required
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisis as $divisi)
                                    <option value="{{ $divisi->divisi }}">{{ $divisi->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" class="cancel-modal px-4 py-2 btn-secondary rounded-lg"
                            data-target="tambahTimModal">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Tim Modal -->
    <div id="editTimModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Tim</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500" data-target="editTimModal">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editTimForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editTimId" name="id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tim</label>
                            <input type="text" id="editNamaTim" name="tim" required
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Masukkan nama tim">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <select id="editDivisiSelect" name="divisi" required
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisis as $divisi)
                                    <option value="{{ $divisi->divisi }}">{{ $divisi->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" class="cancel-modal px-4 py-2 btn-secondary rounded-lg"
                            data-target="editTimModal">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Tambah Divisi Modal -->
<div id="tambahDivisiModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Tambah Divisi Baru</h3>
                <button type="button" class="close-modal text-gray-800 hover:text-gray-500" 
                        data-target="tambahDivisiModal">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="tambahDivisiForm" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Divisi</label>
                        <input type="text" name="divisi" id="namaDivisi" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                               placeholder="Masukkan nama divisi">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="cancel-modal px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" 
                            data-target="tambahDivisiModal">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Edit Divisi Modal -->
    <div id="editDivisiModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Divisi</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500" data-target="editDivisiModal">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editDivisiForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editDivisiId" name="id">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Divisi</label>
                            <input type="text" id="editNamaDivisi" name="divisi" required
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Masukkan nama divisi">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" class="cancel-modal px-4 py-2 btn-secondary rounded-lg"
                            data-target="editDivisiModal">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Tim Modal -->
    <div id="deleteTimModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500" data-target="deleteTimModal">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="deleteTimForm">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus data tim ini?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteTimId" name="id">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="cancel-modal px-4 py-2 btn-secondary rounded-lg"
                            data-target="deleteTimModal">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Divisi Modal -->
    <div id="deleteDivisiModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500" data-target="deleteDivisiModal">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="deleteDivisiForm">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus data divisi ini?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteDivisiId" name="id">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="cancel-modal px-4 py-2 btn-secondary rounded-lg"
                            data-target="deleteDivisiModal">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Minimalist Popup -->
    <div id="minimalPopup" class="minimal-popup">
        <div class="minimal-popup-icon">
            <span class="material-icons-outlined">check</span>
        </div>
        <div class="minimal-popup-content">
            <div class="minimal-popup-title">Berhasil</div>
            <div class="minimal-popup-message">Operasi berhasil dilakukan</div>
        </div>
        <button class="minimal-popup-close">
            <span class="material-icons-outlined text-sm">close</span>
        </button>
    </div>
<script>
    // ==================== GLOBAL FUNCTIONS ====================
    function switchTab(tabName) {
        const panels = document.querySelectorAll('.tab-panel');
        const buttons = document.querySelectorAll('.tab-button');
        
        panels.forEach(panel => panel.classList.add('hidden'));
        buttons.forEach(button => button.classList.remove('active'));

        document.getElementById(`${tabName}Panel`).classList.remove('hidden');
        document.getElementById(`${tabName}Tab`).classList.add('active');
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
            document.body.style.overflow = '';
        }
    }

    function forceCloseAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.style.overflow = '';
    }

    function showMinimalPopup(title, message, type = 'success') {
        const popup = document.getElementById('minimalPopup');
        if (!popup) {
            alert(`${title}: ${message}`);
            return;
        }

        const popupTitle = popup.querySelector('.minimal-popup-title');
        const popupMessage = popup.querySelector('.minimal-popup-message');
        const popupIcon = popup.querySelector('.minimal-popup-icon span');

        if (popupTitle) popupTitle.textContent = title;
        if (popupMessage) popupMessage.textContent = message;
        
        popup.className = `minimal-popup show ${type}`;

        if (popupIcon) {
            if (type === 'success') popupIcon.textContent = 'check';
            else if (type === 'error') popupIcon.textContent = 'error';
            else if (type === 'warning') popupIcon.textContent = 'warning';
        }

        setTimeout(() => {
            popup.classList.remove('show');
        }, 3000);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function refreshPage() {
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // ==================== SEARCH FUNCTIONS ====================
    function searchDivisi(searchTerm) {
        const rows = document.querySelectorAll('#divisiTableBody tr');
        rows.forEach(row => {
            const namaDivisi = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            if (searchTerm === '' || namaDivisi.includes(searchTerm.toLowerCase())) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update count display
        const visibleRows = document.querySelectorAll('#divisiTableBody tr:not([style*="display: none"])').length;
        const countSpan = document.getElementById('divisiCount');
        if (countSpan) countSpan.textContent = visibleRows;
    }

    function searchTim(searchTerm) {
        const rows = document.querySelectorAll('#timTableBody tr');
        rows.forEach(row => {
            const namaTim = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            const divisi = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
            if (searchTerm === '' || namaTim.includes(searchTerm.toLowerCase()) || divisi.includes(searchTerm.toLowerCase())) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update count display
        const visibleRows = document.querySelectorAll('#timTableBody tr:not([style*="display: none"])').length;
        const countSpan = document.getElementById('timCount');
        if (countSpan) countSpan.textContent = visibleRows;
    }

    // ==================== CRUD HANDLERS ====================
    function handleAddTim(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        }

        if (!data.tim || !data.divisi) {
            showMinimalPopup('Error', 'Nama tim dan divisi harus diisi', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
            return;
        }

        fetch('/general_manajer/tim', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMinimalPopup('Berhasil', data.message || 'Tim berhasil ditambahkan', 'success');
                e.target.reset();
                closeModal('tambahTimModal');
                refreshPage();
            } else {
                showMinimalPopup('Error', data.message || 'Terjadi kesalahan', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMinimalPopup('Error', 'Gagal menambahkan tim. Error: ' + error.message, 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    function handleEditTim(e) {
        e.preventDefault();
        const id = document.getElementById('editTimId').value;
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memperbarui...';
        }

        fetch(`/general_manajer/tim/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('editTimModal');
                showMinimalPopup('Berhasil', 'Data tim berhasil diperbarui', 'success');
                refreshPage();
            } else {
                showMinimalPopup('Error', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan pada server: ' + error.message, 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    function handleDeleteTim(e) {
        e.preventDefault();
        const id = document.getElementById('deleteTimId').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menghapus...';
        }

        fetch(`/general_manajer/tim/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('deleteTimModal');
                showMinimalPopup('Berhasil', 'Data tim berhasil dihapus', 'success');
                refreshPage();
            } else {
                showMinimalPopup('Error', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan pada server: ' + error.message, 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    function handleAddDivisi(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        }

        if (!data.divisi || data.divisi.trim() === '') {
            showMinimalPopup('Error', 'Nama divisi harus diisi', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
            return;
        }

        fetch('/general_manajer/divisi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMinimalPopup('Berhasil', data.message || 'Divisi berhasil ditambahkan', 'success');
                e.target.reset();
                closeModal('tambahDivisiModal');
                refreshPage();
            } else {
                showMinimalPopup('Error', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMinimalPopup('Error', 'Gagal menambahkan divisi: ' + error.message, 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    function handleEditDivisi(e) {
        e.preventDefault();
        const id = document.getElementById('editDivisiId').value;
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memperbarui...';
        }

        fetch(`/general_manajer/divisi/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('editDivisiModal');
                showMinimalPopup('Berhasil', 'Data divisi berhasil diperbarui', 'success');
                refreshPage();
            } else {
                showMinimalPopup('Error', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan pada server: ' + error.message, 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    function handleDeleteDivisi(e) {
        e.preventDefault();
        const id = document.getElementById('deleteDivisiId').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menghapus...';
        }

        fetch(`/general_manajer/divisi/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('deleteDivisiModal');
                showMinimalPopup('Berhasil', 'Data divisi berhasil dihapus', 'success');
                refreshPage();
            } else {
                showMinimalPopup('Error', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan pada server: ' + error.message, 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // ==================== EVENT LISTENERS SETUP ====================
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DOM Loaded ===');

        // Tab Switching
        const divisiTab = document.getElementById('divisiTab');
        const timTab = document.getElementById('timTab');
        
        if (divisiTab) {
            divisiTab.addEventListener('click', () => switchTab('divisi'));
        }
        if (timTab) {
            timTab.addEventListener('click', () => switchTab('tim'));
        }

        // Modal Controls
        document.querySelectorAll('.close-modal, .cancel-modal').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                if (targetId) closeModal(targetId);
            });
        });

        // Form Submissions
        const tambahTimForm = document.getElementById('tambahTimForm');
        if (tambahTimForm) tambahTimForm.addEventListener('submit', handleAddTim);

        const tambahDivisiForm = document.getElementById('tambahDivisiForm');
        if (tambahDivisiForm) tambahDivisiForm.addEventListener('submit', handleAddDivisi);

        const editTimForm = document.getElementById('editTimForm');
        if (editTimForm) editTimForm.addEventListener('submit', handleEditTim);

        const deleteTimForm = document.getElementById('deleteTimForm');
        if (deleteTimForm) deleteTimForm.addEventListener('submit', handleDeleteTim);

        const editDivisiForm = document.getElementById('editDivisiForm');
        if (editDivisiForm) editDivisiForm.addEventListener('submit', handleEditDivisi);

        const deleteDivisiForm = document.getElementById('deleteDivisiForm');
        if (deleteDivisiForm) deleteDivisiForm.addEventListener('submit', handleDeleteDivisi);

        // Button Click Handlers
        const tambahTimBtn = document.getElementById('tambahTimBtn');
        if (tambahTimBtn) {
            tambahTimBtn.addEventListener('click', () => {
                const form = document.getElementById('tambahTimForm');
                if (form) form.reset();
                openModal('tambahTimModal');
            });
        }

        const tambahDivisiBtn = document.getElementById('tambahDivisiBtn');
        if (tambahDivisiBtn) {
            tambahDivisiBtn.addEventListener('click', () => {
                const form = document.getElementById('tambahDivisiForm');
                if (form) form.reset();
                openModal('tambahDivisiModal');
            });
        }

        // Search Functionality
        const searchTimInput = document.getElementById('searchTimInput');
        if (searchTimInput) {
            searchTimInput.addEventListener('input', debounce(function(e) {
                searchTim(e.target.value.trim());
            }, 300));
        }

        const searchDivisiInput = document.getElementById('searchDivisiInput');
        if (searchDivisiInput) {
            searchDivisiInput.addEventListener('input', debounce(function(e) {
                searchDivisi(e.target.value.trim());
            }, 300));
        }

        // Popup Close
        const popupClose = document.querySelector('.minimal-popup-close');
        if (popupClose) {
            popupClose.addEventListener('click', () => {
                document.getElementById('minimalPopup')?.classList.remove('show');
            });
        }

        // Close popup when clicking outside
        document.addEventListener('click', function(e) {
            const popup = document.getElementById('minimalPopup');
            if (popup && !popup.contains(e.target) && popup.classList.contains('show')) {
                popup.classList.remove('show');
            }
        });

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                forceCloseAllModals();
            }
        });

        // Dynamic Button Handlers (Edit/Delete buttons in tables)
        document.body.addEventListener('click', function(e) {
            // Edit Tim
            if (e.target.closest('.edit-tim-btn')) {
                const btn = e.target.closest('.edit-tim-btn');
                const id = btn.dataset.id;
                const nama = btn.dataset.nama;
                const divisi = btn.dataset.divisi;
                
                document.getElementById('editTimId').value = id;
                document.getElementById('editNamaTim').value = nama;
                document.getElementById('editDivisiSelect').value = divisi;
                openModal('editTimModal');
            }
            
            // Delete Tim
            if (e.target.closest('.delete-tim-btn')) {
                const id = e.target.closest('.delete-tim-btn').dataset.id;
                document.getElementById('deleteTimId').value = id;
                openModal('deleteTimModal');
            }
            
            // Edit Divisi
            if (e.target.closest('.edit-divisi-btn')) {
                const btn = e.target.closest('.edit-divisi-btn');
                const id = btn.dataset.id;
                const nama = btn.dataset.nama;
                
                document.getElementById('editDivisiId').value = id;
                document.getElementById('editNamaDivisi').value = nama;
                openModal('editDivisiModal');
            }
            
            // Delete Divisi
            if (e.target.closest('.delete-divisi-btn')) {
                const id = e.target.closest('.delete-divisi-btn').dataset.id;
                document.getElementById('deleteDivisiId').value = id;
                openModal('deleteDivisiModal');
            }
        });

        console.log('=== Event Listeners Attached ===');
    });
</script>
</body>

</html>