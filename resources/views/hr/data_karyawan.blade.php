<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Daftar Karyawan - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('logo1.jpeg') }}">
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

        /* Table styles */
        .order-table {
            transition: all 0.2s ease;
        }

        .order-table tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
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

        /* Modal styles */
        .modal {
            transition: opacity 0.25s ease;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        /* Status Badge Styles */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-manager {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-staff {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-intern {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

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

        /* Style untuk efek hover yang lebih menonjol */
        .nav-item {
            position: relative;
            overflow: hidden;
        }

        /* Gaya untuk indikator aktif/hover */
        .nav-item::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background-color: #3b82f6;
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

        /* Memastikan sidebar tetap di posisinya saat scroll */
        .sidebar-fixed {
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
        }

        /* Menyesuaikan konten utama agar tidak tertutup sidebar */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
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

        .input-readonly {
            background-color: #f3f4f6 !important;
            color: #6b7280 !important;
            cursor: not-allowed !important;
            border-color: #d1d5db !important;
            opacity: 0.7;
        }

        /* Pagination styles */
        .page-btn {
            transition: all 0.2s ease;
        }

        .page-btn:hover:not(:disabled) {
            transform: scale(1.1);
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Desktop pagination styles */
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

        /* SIMPLIFIED SCROLLABLE TABLE */
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

        /* Table with fixed width to ensure scrolling */
        .data-table {
            width: 100%;
            min-width: 1200px;
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

        /* Shadow effect */
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

        /* Hidden class for filtering */
        .hidden-by-filter {
            display: none !important;
        }

        /* Responsive table improvements */
        @media (max-width: 1024px) {
            .data-table {
                min-width: 1000px;
            }
        }

        @media (max-width: 768px) {
            .data-table {
                min-width: 800px;
            }
        }

        /* Better mobile card layout */
        @media (max-width: 640px) {
            .karyawan-card .grid {
                grid-template-columns: 1fr;
            }

            .karyawan-card .flex {
                flex-direction: column;
                gap: 10px;
            }
        }

        /* Loading states */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* Form validation styles */
        .input-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        
        /* Gaji field disabled style */
        .gaji-disabled {
            position: relative;
        }
        
        .gaji-disabled .lock-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(249, 250, 251, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .gaji-disabled .lock-overlay .material-icons-outlined {
            font-size: 18px;
            margin-right: 6px;
        }

        /* Allowance card styles */
        .allowance-card {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        .allowance-card.selected {
            background: #eff6ff;
            border-color: #3b82f6;
        }
        .allowance-nominal {
            font-size: 0.875rem;
            font-weight: 600;
            color: #059669;
        }
        .btn-allowance-edit {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            background: #e2e8f0;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-allowance-edit:hover {
            background: #cbd5e1;
        }
    </style>
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('hr.templet.sider')

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Daftar Karyawan</h2>

                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span
                            class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                            placeholder="Cari nama, role, atau alamat..." type="text" />
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative">
                            <button id="filterBtn"
                                class="px-4 py-2 bg-white border border-border-light text-text-muted-light rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">filter_list</span>
                                Filter
                            </button>
                            <div id="filterDropdown" class="filter-dropdown">
                                <!-- ROLE FILTER -->
                                <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
                                    <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; color: #1e293b;">Filter Role</div>
                                    <div class="filter-option">
                                        <input type="checkbox" id="filterAll" value="all" checked>
                                        <label for="filterAll">Semua Role</label>
                                    </div>
                                    <div id="roleFilterContainer">
                                        <!-- Role options akan diload dari database -->
                                    </div>
                                </div>

                                <!-- DIVISI FILTER -->
                                <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
                                    <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; color: #1e293b;">Filter Divisi</div>
                                    <div class="filter-option">
                                        <input type="checkbox" id="filterAllDivisi" value="all" checked>
                                        <label for="filterAllDivisi">Semua Divisi</label>
                                    </div>
                                    <div id="divisiFilterContainer">
                                        <!-- Divisi options akan diload dari database -->
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button id="applyFilter" class="filter-apply">Terapkan</button>
                                    <button id="resetFilter" class="filter-reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <button id="tambahKaryawanBtn"
                            class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Tambah Karyawan</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>
                </div>

                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">people</span>
                            Daftar Karyawan
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount"
                                    class="font-semibold text-text-light">{{ count($karyawan) }}</span> karyawan</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- SCROLLABLE TABLE -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 200px;">Nama</th>
                                            <th style="min-width: 200px;">Email</th>
                                            <th style="min-width: 150px;">Role</th>
                                            <th style="min-width: 150px;">Divisi</th>
                                            <th style="min-width: 150px;">Tim</th>
                                            <th style="min-width: 250px;">Alamat</th>
                                            <th style="min-width: 150px;">Kontak</th>
                                            <th style="min-width: 180px;">Kontrak</th>
                                            <th style="min-width: 120px;">Status Kerja</th>
                                            <th style="min-width: 150px;">Status Karyawan</th>
                                            <th style="min-width: 150px;">Tunjangan Tetap</th>
                                            <th style="min-width: 150px;">Tunjangan Tidak Tetap</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        @if (isset($karyawan) && count($karyawan) > 0)
                                            @php 
                                                $no = 1; 
                                                $currentUserRole = auth()->user()->role;
                                            @endphp
                                            @foreach ($karyawan as $item)
                                                @php
                                                    $tetapNames = $item->tunjangan_tetap_list ? $item->tunjangan_tetap_list->pluck('nama')->toArray() : [];
                                                    $tidakTetapNames = $item->tunjangan_tidak_tetap_list ? $item->tunjangan_tidak_tetap_list->pluck('nama')->toArray() : [];
                                                @endphp
                                                <tr class="karyawan-row" data-id="{{ $item->user_id }}"
                                                    data-nama="{{ $item->nama }}" data-email="{{ $item->email }}"
                                                    data-role="{{ $item->role }}" data-divisi="{{ $item->divisi }}"
                                                    data-divisi-id="{{ $item->divisi_id ?? '' }}"
                                                    data-alamat="{{ $item->alamat }}" data-kontak="{{ $item->kontak }}"
                                                    data-foto="{{ $item->foto ?? '' }}"
                                                    data-gaji="{{ $item->gaji }}"
                                                    data-status_kerja="{{ $item->status_kerja }}"
                                                    data-status_karyawan="{{ $item->status_karyawan }}"
                                                    data-tunjangan_tetap_ids='{{ json_encode($item->tunjangan_tetap_ids) }}'
                                                    data-tunjangan_tidak_tetap_ids='{{ json_encode($item->tunjangan_tidak_tetap_ids) }}'>
                                                    <td style="min-width: 60px;">{{ $no++ }}</td>
                                                    <td style="min-width: 200px;">
                                                        <div class="flex items-center gap-3">
                                                            @if (!empty($item->foto))
                                                                <img src="{{ asset('storage/' . $item->foto) }}"
                                                                    alt="{{ $item->nama }}"
                                                                    class="h-10 w-10 rounded-full object-cover"
                                                                    onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                            @else
                                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                                    <span class="material-icons-outlined text-gray-500">person</span>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div class="font-medium">{{ $item->nama }}</div>
                                                                <div class="text-xs text-gray-500">ID: {{ $item->user_id }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="min-width: 200px;">
                                                        <div class="text-sm">{{ $item->email }}</div>
                                                        <div class="text-xs text-gray-500">Role: {{ $item->role }}</div>
                                                    </td>
                                                    <td style="min-width: 150px;">
                                                        <span class="status-badge 
                                                            @if (in_array(strtolower($item->role), ['manager', 'general_manager', 'manager_divisi', 'admin', 'owner'])) status-manager
                                                            @elseif(strtolower($item->role) == 'staff' || $item->role == 'karyawan') status-staff
                                                            @elseif(strtolower($item->role) == 'intern' || $item->role == 'magang') status-intern
                                                            @else status-staff @endif">
                                                            {{ $item->role }}
                                                        </span>
                                                    </td>
                                                    <td style="min-width: 150px;">{{ $item->divisi ?? '-' }}</td>
                                                    <td style="min-width: 150px;">{{ $item->tim ? $item->tim->tim : '-' }}</td>
                                                    <td style="min-width: 250px;">{{ $item->alamat }}</td>
                                                    <td style="min-width: 150px;">{{ $item->kontak }}</td>
                                                    <td style="min-width: 180px;">
                                                        @php
                                                            $kontrakText = '-';
                                                            if (($item->status_karyawan ?? null) === 'kontrak' && (!empty($item->kontrak_mulai) || !empty($item->kontrak_selesai))) {
                                                                $mulai = $item->kontrak_mulai ? \Carbon\Carbon::parse($item->kontrak_mulai)->format('d M Y') : '-';
                                                                $selesai = $item->kontrak_selesai ? \Carbon\Carbon::parse($item->kontrak_selesai)->format('d M Y') : '-';
                                                                $kontrakText = "$mulai - $selesai";
                                                            }
                                                        @endphp
                                                        <div class="text-sm">{{ $kontrakText }}</div>
                                                    </td>
                                                    <td style="min-width: 120px;">
                                                        @php
                                                            $statusClass = 'bg-gray-100 text-gray-800';
                                                            if ($item->status_kerja === 'aktif') {
                                                                $statusClass = 'bg-green-100 text-green-800';
                                                            } elseif ($item->status_kerja === 'resign') {
                                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                            } elseif ($item->status_kerja === 'phk') {
                                                                $statusClass = 'bg-red-100 text-red-800';
                                                            } elseif ($item->status_kerja === 'nonaktif') {
                                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                            }
                                                        @endphp
                                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                                            {{ ucfirst($item->status_kerja) }}
                                                        </span>
                                                    </td>
                                                    <td style="min-width: 150px;">
                                                        @php
                                                            $statusKaryawanClass = 'bg-gray-100 text-gray-800';
                                                            if ($item->status_karyawan === 'tetap') {
                                                                $statusKaryawanClass = 'bg-blue-100 text-blue-800';
                                                            } elseif ($item->status_karyawan === 'kontrak') {
                                                                $statusKaryawanClass = 'bg-orange-100 text-orange-800';
                                                            } elseif ($item->status_karyawan === 'magang') {
                                                                $statusKaryawanClass = 'bg-purple-100 text-purple-800';
                                                            }
                                                        @endphp
                                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusKaryawanClass }}">
                                                            {{ ucfirst($item->status_karyawan ?? 'tetap') }}
                                                        </span>
                                                    </td>
                                                    <td style="min-width: 150px;">
                                                        {{ implode(', ', $tetapNames) ?: '-' }}
                                                    </td>
                                                    <td style="min-width: 150px;">
                                                        {{ implode(', ', $tidakTetapNames) ?: '-' }}
                                                    </td>
                                                    <td style="min-width: 100px; text-align: center;">
                                                        <div class="flex justify-center gap-2">
                                                            <button class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                                data-id="{{ $item->user_id }}"
                                                                data-nama="{{ $item->nama }}"
                                                                data-email="{{ $item->email }}"
                                                                data-role="{{ $item->role }}"
                                                                data-divisi_id="{{ $item->divisi_id }}"
                                                                data-divisi="{{ $item->divisi ?? '' }}"
                                                                data-alamat="{{ $item->alamat }}"
                                                                data-kontak="{{ $item->kontak }}"
                                                                data-status_kerja="{{ $item->status_kerja }}"
                                                                data-status_karyawan="{{ $item->status_karyawan }}"
                                                                data-gaji="{{ $item->gaji }}"
                                                                data-kontrak_mulai="{{ $item->kontrak_mulai ?? '' }}"
                                                                data-kontrak_selesai="{{ $item->kontrak_selesai ?? '' }}"
                                                                data-foto="{{ $item->foto ?? '' }}"
                                                                data-tunjangan_tetap_ids='{{ json_encode($item->tunjangan_tetap_list ? $item->tunjangan_tetap_list->pluck('id')->toArray() : []) }}'
                                                                data-tunjangan_tidak_tetap_ids='{{ json_encode($item->tunjangan_tidak_tetap_list ? $item->tunjangan_tidak_tetap_list->pluck('id')->toArray() : []) }}'>
                                                                <span class="material-icons-outlined">edit</span>
                                                            </button>
                                                            <button class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                                data-id="{{ $item->user_id }}"
                                                                data-nama="{{ $item->nama }}">
                                                                <span class="material-icons-outlined">delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="14" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    Tidak ada data karyawan
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4" id="mobile-cards">
                            @if (isset($karyawan) && count($karyawan) > 0)
                                @php 
                                    $no = 1; 
                                @endphp
                                @foreach ($karyawan as $item)
                                    @php
                                        $tetapNames = $item->tunjangan_tetap_list ? $item->tunjangan_tetap_list->pluck('nama')->toArray() : [];
                                        $tidakTetapNames = $item->tunjangan_tidak_tetap_list ? $item->tunjangan_tidak_tetap_list->pluck('nama')->toArray() : [];
                                    @endphp
                                    <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm karyawan-card"
                                        data-id="{{ $item->user_id }}" data-nama="{{ $item->nama }}"
                                        data-role="{{ $item->role }}" data-divisi="{{ $item->divisi }}"
                                        data-divisi-id="{{ $item->divisi_id ?? '' }}"
                                        data-alamat="{{ $item->alamat }}" data-kontak="{{ $item->kontak }}"
                                        data-foto="{{ $item->foto ?? '' }}">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center gap-3">
                                                @if ($item->foto)
                                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                                        alt="{{ $item->nama }}"
                                                        class="h-12 w-12 rounded-full object-cover"
                                                        onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                @else
                                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="material-icons-outlined text-gray-500">person</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="font-semibold text-base">{{ $item->nama }}</h4>
                                                    <p class="text-sm text-text-muted-light">{{ $item->kontak }}</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <button class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                    data-id="{{ $item->user_id }}"
                                                    data-nama="{{ $item->nama }}"
                                                    data-email="{{ $item->email }}"
                                                    data-role="{{ $item->role }}"
                                                    data-divisi_id="{{ $item->divisi_id }}"
                                                    data-alamat="{{ $item->alamat }}"
                                                    data-kontak="{{ $item->kontak }}"
                                                    data-status_kerja="{{ $item->status_kerja }}"
                                                    data-status_karyawan="{{ $item->status_karyawan }}"
                                                    data-gaji="{{ $item->gaji }}"
                                                    data-foto="{{ $item->foto ?? '' }}"
                                                    data-tunjangan_tetap_ids='{{ json_encode($item->tunjangan_tetap_list ? $item->tunjangan_tetap_list->pluck('id')->toArray() : []) }}'
                                                    data-tunjangan_tidak_tetap_ids='{{ json_encode($item->tunjangan_tidak_tetap_list ? $item->tunjangan_tidak_tetap_list->pluck('id')->toArray() : []) }}'>
                                                    <span class="material-icons-outlined">edit</span>
                                                </button>
                                                <button class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                    data-id="{{ $item->user_id }}" data-nama="{{ $item->nama }}">
                                                    <span class="material-icons-outlined">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div><p class="text-text-muted-light">No</p><p class="font-medium">{{ $no++ }}</p></div>
                                            <div><p class="text-text-muted-light">Role</p><p><span class="status-badge">{{ $item->role }}</span></p></div>
                                            <div><p class="text-text-muted-light">Status Kerja</p><p>{{ ucfirst($item->status_kerja) }}</p></div>
                                            <div><p class="text-text-muted-light">Divisi</p><p class="font-medium">{{ $item->divisi ?? '-' }}</p></div>
                                            <div><p class="text-text-muted-light">Alamat</p><p class="font-medium truncate">{{ $item->alamat }}</p></div>
                                            <div><p class="text-text-muted-light">Tunjangan Tetap</p><p>{{ implode(', ', $tetapNames) ?: '-' }}</p></div>
                                            <div><p class="text-text-muted-light">Tunjangan Tidak Tetap</p><p>{{ implode(', ', $tidakTetapNames) ?: '-' }}</p></div>
                                        </div>
                                        @if($item->gaji)
                                            <div class="mt-3 pt-3 border-t border-gray-100">
                                                <p class="text-text-muted-light">Gaji</p>
                                                <p class="font-medium">Rp {{ number_format($item->gaji, 0, ',', '.') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="bg-white rounded-lg border border-border-light p-8 text-center">
                                    <span class="material-icons-outlined text-4xl text-gray-300 mb-2">people</span>
                                    <p class="text-gray-500">Tidak ada data karyawan</p>
                                </div>
                            @endif
                        </div>

                        <!-- Pagination -->
                        <div id="paginationContainer" class="desktop-pagination">
                            <button id="prevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="pageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="nextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Popup Modal untuk Tambah/Edit Karyawan dengan Tunjangan -->
    <div id="karyawanModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Tambah Karyawan Baru</h3>
                    <button id="closeModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="karyawanForm" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="formId" name="id">
                    <input type="hidden" id="formUserId" name="user_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                            <input type="text" name="name" id="formNama" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nama karyawan">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="formEmail" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan email">
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" id="formPassword"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Kosongkan jika tidak diubah">
                            <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter (isi hanya jika ingin mengubah)</p>
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select name="role" id="formRole" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Role</option>
                                <option value="general_manager">General Manager</option>
                                <option value="manager_divisi">Manager Divisi</option>
                                <option value="karyawan">Karyawan</option>
                                <option value="finance">Finance</option>
                                <option value="hr">HR</option>
                            </select>
                        </div>

                        <!-- Divisi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <select name="divisi_id" id="formDivisi"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Divisi</option>
                                @if (isset($divisis) && count($divisis) > 0)
                                    @foreach ($divisis as $divisi)
                                        <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Tim -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tim</label>
                            <select name="tim_id" id="formTim"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Tim (opsional)</option>
                            </select>
                        </div>

                        <!-- Gaji -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gaji</label>
                            <input type="number" name="gaji" id="formGaji"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan gaji (angka tanpa titik/koma)">
                            <p class="text-xs text-gray-500 mt-1">Isi dengan angka, contoh: 5000000</p>
                        </div>

                        <!-- Kontak -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
                            <input type="text" name="kontak" id="formKontak"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nomor telepon">
                        </div>

                        <!-- Status Kerja -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerja</label>
                            <select name="status_kerja" id="formStatusKerja"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="aktif">Aktif</option>
                                <option value="resign">Resign</option>
                                <option value="phk">PHK</option>
                            </select>
                        </div>

                        <!-- Status Karyawan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                            <select name="status_karyawan" id="formStatusKaryawan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="tetap">Tetap</option>
                                <option value="kontrak">Kontrak</option>
                                <option value="freelance">Freelance</option>
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="alamat" id="formAlamat" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan alamat lengkap"></textarea>
                        </div>
                    </div>

                    <!-- Tunjangan Tetap Section -->
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-medium text-gray-700">Tunjangan Tetap (Bulanan)</label>
                            <button type="button" id="addFixedAllowanceBtn" class="text-xs text-primary hover:underline flex items-center gap-1">
                                <span class="material-icons-outlined text-sm">add_circle</span> Tambah Tunjangan Baru
                            </button>
                        </div>
                        <div id="fixedAllowanceContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($tunjanganMaster->where('tipe', 'bulanan') as $tunjangan)
                            <div class="allowance-card" data-id="{{ $tunjangan->id }}" data-tipe="fixed" data-nama="{{ $tunjangan->nama }}" data-nominal="{{ $tunjangan->nominal }}">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">{{ $tunjangan->nama }}</div>
                                        <div class="allowance-nominal text-xs">Rp {{ number_format($tunjangan->nominal, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" class="fixed-allowance-checkbox w-5 h-5 rounded border-gray-300" value="{{ $tunjangan->id }}">
                                        <button type="button" class="btn-allowance-edit" data-id="{{ $tunjangan->id }}" data-nama="{{ $tunjangan->nama }}" data-tipe="bulanan" data-nominal="{{ $tunjangan->nominal }}">
                                            <span class="material-icons-outlined text-sm">edit</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($tunjanganMaster->where('tipe', 'bulanan')->count() == 0)
                            <p class="text-sm text-gray-400 text-center py-4">Belum ada tunjangan tetap. Klik tombol "Tambah Tunjangan Baru" untuk menambahkan.</p>
                        @endif
                    </div>

                    <!-- Tunjangan Tidak Tetap Section -->
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-medium text-gray-700">Tunjangan Tidak Tetap (Bonus/Insentif)</label>
                            <button type="button" id="addVariableAllowanceBtn" class="text-xs text-primary hover:underline flex items-center gap-1">
                                <span class="material-icons-outlined text-sm">add_circle</span> Tambah Tunjangan Baru
                            </button>
                        </div>
                        <div id="variableAllowanceContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($tunjanganMaster->whereIn('tipe', ['bonus', 'insentif']) as $tunjangan)
                            <div class="allowance-card" data-id="{{ $tunjangan->id }}" data-tipe="variable" data-nama="{{ $tunjangan->nama }}" data-nominal="{{ $tunjangan->nominal }}">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">{{ $tunjangan->nama }}</div>
                                        <div class="allowance-nominal text-xs">Rp {{ number_format($tunjangan->nominal, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" class="variable-allowance-checkbox w-5 h-5 rounded border-gray-300" value="{{ $tunjangan->id }}">
                                        <button type="button" class="btn-allowance-edit" data-id="{{ $tunjangan->id }}" data-nama="{{ $tunjangan->nama }}" data-tipe="{{ $tunjangan->tipe }}" data-nominal="{{ $tunjangan->nominal }}">
                                            <span class="material-icons-outlined text-sm">edit</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($tunjanganMaster->whereIn('tipe', ['bonus', 'insentif'])->count() == 0)
                            <p class="text-sm text-gray-400 text-center py-4">Belum ada tunjangan tidak tetap. Klik tombol "Tambah Tunjangan Baru" untuk menambahkan.</p>
                        @endif
                    </div>

                    <!-- Foto -->
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                        <div class="flex items-center space-x-4">
                            <div id="fotoPreview" class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="material-icons-outlined text-gray-500 text-2xl">person</span>
                            </div>
                            <div>
                                <input type="file" name="foto" id="fotoInput" class="hidden" accept="image/*">
                                <button type="button" id="pilihFotoBtn" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                    Pilih Foto
                                </button>
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG maks. 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelModalBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan Karyawan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit/Add Tunjangan Master -->
    <div id="allowanceMasterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[100] p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="allowanceModalTitle" class="text-lg font-bold text-gray-800">Tambah Tunjangan</h3>
                    <button id="closeAllowanceModal" class="text-gray-500 hover:text-gray-700">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="allowanceMasterForm">
                    @csrf
                    <input type="hidden" name="id" id="allowanceId">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tunjangan *</label>
                            <input type="text" name="nama" id="allowanceNama" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Contoh: Transportasi, Makan, Bonus Tahunan">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                            <select name="tipe" id="allowanceTipe" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="bulanan">Tetap (Diberikan Setiap Bulan)</option>
                                <option value="bonus">Tidak Tetap (Bonus)</option>
                                <option value="insentif">Tidak Tetap (Insentif)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Default (Rp) *</label>
                            <input type="number" name="nominal" id="allowanceNominal" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Contoh: 500000">
                        </div>
                        <div class="flex justify-end gap-2 mt-6">
                            <button type="button" id="cancelAllowanceBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan Master</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Popup Modal untuk Konfirmasi Hapus -->
    <div id="deleteKaryawanModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button id="closeDeleteModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>

                <div class="mb-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-icons-outlined text-red-500 text-3xl">warning</span>
                    </div>
                    <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus data karyawan ini?</p>
                    <p class="text-sm text-gray-500 mb-4" id="deleteKaryawanName"></p>
                    <p class="text-xs text-gray-400">Tindakan ini tidak dapat dibatalkan dan data akan dihapus permanen.</p>
                    <input type="hidden" id="deleteId" name="id">
                </div>

                <div class="flex justify-center gap-3">
                    <button type="button" id="cancelDeleteBtn" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">Batal</button>
                    <button type="button" id="confirmDeleteBtn" class="px-5 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">Hapus Data</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimalist Popup -->
    <div id="minimalPopup" class="minimal-popup">
        <div class="minimal-popup-icon"><span class="material-icons-outlined">check</span></div>
        <div class="minimal-popup-content">
            <div class="minimal-popup-title">Berhasil</div>
            <div class="minimal-popup-message">Operasi berhasil dilakukan</div>
        </div>
        <button class="minimal-popup-close"><span class="material-icons-outlined text-sm">close</span></button>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay hidden">
        <div class="loading-spinner"></div>
    </div>

<script>
    // Inisialisasi variabel untuk pagination, filter, dan search
    let currentPage = 1;
    const itemsPerPage = 5;
    let activeFilters = ['all'];
    let activeDivisiFilters = ['all'];
    let searchTerm = '';

    // Dapatkan semua elemen karyawan
    let karyawanRows = document.querySelectorAll('.karyawan-row');
    let karyawanCards = document.querySelectorAll('.karyawan-card');
    
    // Variabel untuk tunjangan
    let fixedAllowanceSelections = new Set();
    let variableAllowanceSelections = new Set();
    let currentModalMode = 'create'; // create or edit

    // === UTILITY FUNCTIONS ===
    function getCsrfToken() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function showLoading(show = true) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            if (show) {
                loadingOverlay.classList.remove('hidden');
            } else {
                loadingOverlay.classList.add('hidden');
            }
        }
    }
    // Saat edit karyawan, ambil gaji dari data gaji HR
async function loadGajiFromHR(userId) {
    try {
        const response = await fetch(`/admin/karyawan/get-gaji/${userId}`);
        const data = await response.json();
        
        if (data.success && data.gaji_pokok) {
            document.getElementById('formGaji').value = data.gaji_pokok;
            showMinimalPopup('Info', `Gaji terupdate dari HR: Rp ${new Intl.NumberFormat('id-ID').format(data.gaji_pokok)}`, 'success');
        }
    } catch (error) {
        console.error('Error loading gaji:', error);
    }
}

// Panggil fungsi ini saat open edit modal
function openKaryawanModal(mode = 'create', data = null) {
    // ... kode yang sudah ada ...
    
    if (mode === 'edit' && data && data.id) {
        // ... isi form ...
        
        // Ambil gaji dari data gaji HR
        loadGajiFromHR(data.id);
    }
}

    function showMinimalPopup(title, message, type = 'success') {
        const popup = document.getElementById('minimalPopup');
        if (!popup) {
            console.log(`${title}: ${message}`);
            return;
        }

        const popupTitle = popup.querySelector('.minimal-popup-title');
        const popupMessage = popup.querySelector('.minimal-popup-message');
        const popupIcon = popup.querySelector('.minimal-popup-icon span');

        if (popupTitle) popupTitle.textContent = title;
        if (popupMessage) popupMessage.textContent = message;

        popup.className = 'minimal-popup show ' + type;

        if (popupIcon) {
            if (type === 'success') {
                popupIcon.textContent = 'check';
            } else if (type === 'error') {
                popupIcon.textContent = 'error';
            } else if (type === 'warning') {
                popupIcon.textContent = 'warning';
            }
        }

        setTimeout(() => {
            popup.classList.remove('show');
        }, 3000);
    }

    // === PAGINATION ===
    function initializePagination() {
        renderPagination();
        updateVisibleItems();
    }

    function getFilteredRows() {
        return Array.from(karyawanRows).filter(row => !row.classList.contains('hidden-by-filter'));
    }

    function getFilteredCards() {
        return Array.from(karyawanCards).filter(card => !card.classList.contains('hidden-by-filter'));
    }

    function renderPagination() {
        const visibleRows = getFilteredRows();
        const totalPages = Math.ceil(visibleRows.length / itemsPerPage);
        const pageNumbersContainer = document.getElementById('pageNumbers');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');

        if (!pageNumbersContainer) return;

        pageNumbersContainer.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const pageNumber = document.createElement('button');
            pageNumber.textContent = i;
            pageNumber.className = `desktop-page-btn ${i === currentPage ? 'active' : ''}`;
            pageNumber.addEventListener('click', () => goToPage(i));
            pageNumbersContainer.appendChild(pageNumber);
        }

        if (prevButton) prevButton.disabled = currentPage === 1;
        if (nextButton) nextButton.disabled = currentPage === totalPages || totalPages === 0;

        if (prevButton) {
            prevButton.onclick = () => {
                if (currentPage > 1) goToPage(currentPage - 1);
            };
        }

        if (nextButton) {
            nextButton.onclick = () => {
                if (currentPage < totalPages) goToPage(currentPage + 1);
            };
        }
    }

    function goToPage(page) {
        currentPage = page;
        renderPagination();
        updateVisibleItems();

        const scrollableTable = document.getElementById('scrollableTable');
        if (scrollableTable) {
            scrollableTable.scrollLeft = 0;
        }
    }

    function updateVisibleItems() {
        const visibleRows = getFilteredRows();
        const visibleCards = getFilteredCards();

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        karyawanRows.forEach(row => row.style.display = 'none');
        karyawanCards.forEach(card => card.style.display = 'none');

        let displayNumber = startIndex + 1;
        visibleRows.forEach((row, index) => {
            if (index >= startIndex && index < endIndex) {
                row.style.display = '';
                const noCell = row.querySelector('td:first-child');
                if (noCell) {
                    noCell.textContent = displayNumber;
                }
                displayNumber++;
            }
        });

        let cardNumber = startIndex + 1;
        visibleCards.forEach((card, index) => {
            if (index >= startIndex && index < endIndex) {
                card.style.display = 'block';
                const noElement = card.querySelector('.grid > div:first-child p:last-child');
                if (noElement) {
                    noElement.textContent = cardNumber;
                }
                cardNumber++;
            }
        });

        const totalCountElement = document.getElementById('totalCount');
        if (totalCountElement) {
            totalCountElement.textContent = visibleRows.length;
        }
    }

    // === FILTER ===
    async function loadRoleFilters() {
        try {
            const response = await fetch('{{ url('/roles/list') }}', {
                method: 'GET',
                credentials: 'include',
                headers: { "Accept": "application/json" }
            });
            if (!response.ok) return;
            const data = await response.json();
            const roleContainer = document.getElementById('roleFilterContainer');
            if (!roleContainer) return;
            roleContainer.innerHTML = '';
            const roles = Array.isArray(data) ? data : (data.data || []);
            if (Array.isArray(roles)) {
                roles.forEach(role => {
                    const option = document.createElement('div');
                    option.className = 'filter-option';
                    option.innerHTML = `<input type="checkbox" id="filterRole_${role.id}" value="${role.id}" data-role-id="${role.id}"><label for="filterRole_${role.id}">${role.role}</label>`;
                    roleContainer.appendChild(option);
                });
                document.querySelectorAll('#roleFilterContainer input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('change', function() {
                        if (this.checked) {
                            const filterAll = document.getElementById('filterAll');
                            if (filterAll) filterAll.checked = false;
                        }
                    });
                });
            }
        } catch (error) { console.error('Error loading role filters:', error); }
    }

    async function loadDivisionFilters() {
        try {
            const response = await fetch('{{ url('/divisis/list') }}', {
                method: 'GET',
                credentials: 'include',
                headers: { "Accept": "application/json" }
            });
            if (!response.ok) return;
            const data = await response.json();
            const divisiContainer = document.getElementById('divisiFilterContainer');
            if (!divisiContainer) return;
            divisiContainer.innerHTML = '';
            const divisis = Array.isArray(data) ? data : (data.data || []);
            if (Array.isArray(divisis)) {
                divisis.forEach(divisi => {
                    const option = document.createElement('div');
                    option.className = 'filter-option';
                    option.innerHTML = `<input type="checkbox" id="filterDivisi_${divisi.id}" value="${divisi.id}" data-divisi-id="${divisi.id}"><label for="filterDivisi_${divisi.id}">${divisi.divisi}</label>`;
                    divisiContainer.appendChild(option);
                });
                document.querySelectorAll('#divisiFilterContainer input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('change', function() {
                        if (this.checked) {
                            const filterAllDivisi = document.getElementById('filterAllDivisi');
                            if (filterAllDivisi) filterAllDivisi.checked = false;
                        }
                    });
                });
            }
        } catch (error) { console.error('Error loading division filters:', error); }
    }

    function initializeFilter() {
        const filterBtn = document.getElementById('filterBtn');
        const filterDropdown = document.getElementById('filterDropdown');
        const applyFilterBtn = document.getElementById('applyFilter');
        const resetFilterBtn = document.getElementById('resetFilter');
        const filterAll = document.getElementById('filterAll');
        const filterAllDivisi = document.getElementById('filterAllDivisi');

        if (!filterBtn || !filterDropdown) return;

        filterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            filterDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function() { filterDropdown.classList.remove('show'); });
        filterDropdown.addEventListener('click', function(e) { e.stopPropagation(); });

        if (filterAll) {
            filterAll.addEventListener('change', function() {
                if (this.checked) {
                    document.querySelectorAll('#roleFilterContainer input[type="checkbox"]').forEach(cb => { cb.checked = false; });
                }
            });
        }
        
        if (filterAllDivisi) {
            filterAllDivisi.addEventListener('change', function() {
                if (this.checked) {
                    document.querySelectorAll('#divisiFilterContainer input[type="checkbox"]').forEach(cb => { cb.checked = false; });
                }
            });
        }

        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', function() {
                activeFilters = [];
                if (filterAll && filterAll.checked) {
                    activeFilters.push('all');
                } else {
                    document.querySelectorAll('#roleFilterContainer input[type="checkbox"]:checked').forEach(cb => {
                        activeFilters.push(cb.getAttribute('data-role-id'));
                    });
                }
                activeDivisiFilters = [];
                if (filterAllDivisi && filterAllDivisi.checked) {
                    activeDivisiFilters.push('all');
                } else {
                    document.querySelectorAll('#divisiFilterContainer input[type="checkbox"]:checked').forEach(cb => {
                        activeDivisiFilters.push(cb.getAttribute('data-divisi-id'));
                    });
                }
                applyFilters();
                filterDropdown.classList.remove('show');
                showMinimalPopup('Filter Diterapkan', `Menampilkan ${getFilteredRows().length} karyawan`, 'success');
            });
        }

        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', function() {
                if (filterAll) filterAll.checked = true;
                document.querySelectorAll('#roleFilterContainer input[type="checkbox"]').forEach(cb => { cb.checked = false; });
                if (filterAllDivisi) filterAllDivisi.checked = true;
                document.querySelectorAll('#divisiFilterContainer input[type="checkbox"]').forEach(cb => { cb.checked = false; });
                activeFilters = ['all'];
                activeDivisiFilters = ['all'];
                applyFilters();
                filterDropdown.classList.remove('show');
                showMinimalPopup('Filter Direset', 'Menampilkan semua karyawan', 'success');
            });
        }
    }

    function applyFilters() {
        currentPage = 1;
        karyawanRows.forEach(row => {
            const role = row.getAttribute('data-role')?.toLowerCase() || '';
            const nama = row.getAttribute('data-nama')?.toLowerCase() || '';
            const alamat = row.getAttribute('data-alamat')?.toLowerCase() || '';
            const divisiId = row.getAttribute('data-divisi-id') || '';
            let roleMatches = activeFilters.includes('all') ? true : activeFilters.some(filter => role === filter.toLowerCase());
            let divisiMatches = activeDivisiFilters.includes('all') ? true : activeDivisiFilters.includes(divisiId);
            let searchMatches = true;
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                searchMatches = nama.includes(searchLower) || alamat.includes(searchLower) || role.includes(searchLower);
            }
            if (roleMatches && divisiMatches && searchMatches) row.classList.remove('hidden-by-filter');
            else row.classList.add('hidden-by-filter');
        });
        karyawanCards.forEach(card => {
            const role = card.getAttribute('data-role')?.toLowerCase() || '';
            const nama = card.getAttribute('data-nama')?.toLowerCase() || '';
            const alamat = card.getAttribute('data-alamat')?.toLowerCase() || '';
            const divisiId = card.getAttribute('data-divisi-id') || '';
            let roleMatches = activeFilters.includes('all') ? true : activeFilters.some(filter => role === filter.toLowerCase());
            let divisiMatches = activeDivisiFilters.includes('all') ? true : activeDivisiFilters.includes(divisiId);
            let searchMatches = true;
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                searchMatches = nama.includes(searchLower) || alamat.includes(searchLower) || role.includes(searchLower);
            }
            if (roleMatches && divisiMatches && searchMatches) card.classList.remove('hidden-by-filter');
            else card.classList.add('hidden-by-filter');
        });
        renderPagination();
        updateVisibleItems();
    }

    // === SEARCH ===
    function initializeSearch() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchTerm = searchInput.value.trim();
                applyFilters();
            }, 300);
        });
    }

    // === LOAD DIVISIS ===
    async function loadDivisis(selectElementId = 'formDivisi') {
        try {
            const response = await fetch('{{ url('/divisis/list') }}', {
                method: 'GET',
                credentials: 'include',
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return;
            const data = await response.json();
            const selectElement = document.getElementById(selectElementId);
            if (!selectElement) return;
            while (selectElement.options.length > 1) selectElement.remove(1);
            const divisis = Array.isArray(data) ? data : (data.data || []);
            divisis.forEach(divisi => {
                const option = document.createElement('option');
                option.value = divisi.id;
                option.textContent = divisi.divisi || divisi.name;
                selectElement.appendChild(option);
            });
        } catch (error) { console.error('Error loading divisis:', error); }
    }

    async function loadTims(selectElementId = 'formTim', divisiId, selectedTimId = null) {
        try {
            const selectElement = document.getElementById(selectElementId);
            if (!selectElement) return;
            while (selectElement.options.length > 1) selectElement.remove(1);
            if (!divisiId) return;
            const response = await fetch('{{ url('/tims/by-divisi') }}/' + divisiId, {
                method: 'GET',
                credentials: 'include',
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return;
            const payload = await response.json();
            const tims = payload && payload.data ? payload.data : payload;
            if (Array.isArray(tims)) {
                tims.forEach(t => {
                    const option = document.createElement('option');
                    option.value = t.id || t.ID || t.id_tim || '';
                    option.textContent = t.tim || t.name || t.tim_name || '';
                    if (selectedTimId && selectedTimId == option.value) option.selected = true;
                    selectElement.appendChild(option);
                });
            }
        } catch (error) { console.error('Error loading tims:', error); }
    }

    // === ALLOWANCE FUNCTIONS ===
    function initAllowanceSelections(fixedIds = [], variableIds = []) {
        fixedAllowanceSelections.clear();
        variableAllowanceSelections.clear();
        document.querySelectorAll('.fixed-allowance-checkbox').forEach(cb => {
            cb.checked = false;
            const val = parseInt(cb.value);
            if (fixedIds.includes(val)) {
                cb.checked = true;
                fixedAllowanceSelections.add(val);
                cb.closest('.allowance-card')?.classList.add('selected');
            } else {
                cb.closest('.allowance-card')?.classList.remove('selected');
            }
        });
        document.querySelectorAll('.variable-allowance-checkbox').forEach(cb => {
            cb.checked = false;
            const val = parseInt(cb.value);
            if (variableIds.includes(val)) {
                cb.checked = true;
                variableAllowanceSelections.add(val);
                cb.closest('.allowance-card')?.classList.add('selected');
            } else {
                cb.closest('.allowance-card')?.classList.remove('selected');
            }
        });
    }

    // Modal Karyawan
 function openKaryawanModal(mode = 'create', data = null) {
    currentModalMode = mode;
    const modal = document.getElementById('karyawanModal');
    const form = document.getElementById('karyawanForm');
    form.reset();
    document.getElementById('formId').value = '';
    document.getElementById('formUserId').value = '';
    document.getElementById('fotoPreview').innerHTML = '<span class="material-icons-outlined text-gray-500 text-2xl">person</span>';
    initAllowanceSelections([], []);
    
    if (mode === 'edit' && data) {
        document.getElementById('modalTitle').textContent = 'Edit Karyawan';
        document.getElementById('formId').value = data.id;
        document.getElementById('formUserId').value = data.user_id || '';
        document.getElementById('formNama').value = data.nama || '';
        document.getElementById('formEmail').value = data.email || '';
        document.getElementById('formRole').value = data.role || '';
        document.getElementById('formDivisi').value = data.divisi_id || '';
        
        // Set gaji - jika ada data gaji dari database, gunakan itu
        if (data.gaji && data.gaji > 0) {
            document.getElementById('formGaji').value = data.gaji;
        } else {
            // Jika tidak ada, coba auto-fill
            document.getElementById('formGaji').value = '';
            autoFillGaji(); // Panggil auto-fill setelah role dan divisi di-set
        }
        
        document.getElementById('formKontak').value = data.kontak || '';
        document.getElementById('formAlamat').value = data.alamat || '';
        document.getElementById('formStatusKerja').value = data.status_kerja || 'aktif';
        document.getElementById('formStatusKaryawan').value = data.status_karyawan || 'tetap';
        
        if (data.divisi_id) loadTims('formTim', data.divisi_id, data.tim_id);
        
        // Parse allowance ids
        let fixedIds = [], variableIds = [];
        try {
            if (data.tunjangan_tetap_ids) {
                let ft = typeof data.tunjangan_tetap_ids === 'string' ? JSON.parse(data.tunjangan_tetap_ids) : data.tunjangan_tetap_ids;
                if (Array.isArray(ft)) fixedIds = ft.map(v => typeof v === 'object' ? v.id : v);
            }
            if (data.tunjangan_tidak_tetap_ids) {
                let vt = typeof data.tunjangan_tidak_tetap_ids === 'string' ? JSON.parse(data.tunjangan_tidak_tetap_ids) : data.tunjangan_tidak_tetap_ids;
                if (Array.isArray(vt)) variableIds = vt.map(v => typeof v === 'object' ? v.id : v);
            }
        } catch(e) { console.error('Parse error:', e); }
        initAllowanceSelections(fixedIds, variableIds);
    } else {
        document.getElementById('modalTitle').textContent = 'Tambah Karyawan Baru';
        // Reset gaji saat tambah baru
        document.getElementById('formGaji').value = '';
    }
    modal.classList.remove('hidden');
}


    function closeKaryawanModal() {
        document.getElementById('karyawanModal').classList.add('hidden');
    }

    // Modal Allowance Master
    const allowanceModal = document.getElementById('allowanceMasterModal');
    function closeAllowanceModal() { allowanceModal.classList.add('hidden'); allowanceModal.style.display = 'none'; }
    function openAllowanceModal(id = null, nama = '', tipe = 'bulanan', nominal = '') {
        document.getElementById('allowanceId').value = id || '';
        document.getElementById('allowanceNama').value = nama;
        document.getElementById('allowanceTipe').value = tipe;
        document.getElementById('allowanceNominal').value = nominal;
        document.getElementById('allowanceModalTitle').textContent = id ? 'Edit Tunjangan' : 'Tambah Tunjangan';
        allowanceModal.classList.remove('hidden');
        allowanceModal.style.display = 'flex';
    }

    // Delete handler
    async function handleDeleteKaryawan(id) {
        if (!id) { showMinimalPopup('Error', 'ID karyawan tidak ditemukan', 'error'); return; }
        showLoading(true);
        try {
            const response = await fetch(`/admin/karyawan/delete/${id}`, {
                method: 'DELETE',
                credentials: 'include',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' }
            });
            const data = await response.json();
            if (data.success) {
                showMinimalPopup('Berhasil', data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showMinimalPopup('Error', data.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan saat menghapus data', 'error');
        } finally { showLoading(false); closeDeleteModal(); }
    }

    function openDeleteModal(id, nama) {
        const modal = document.getElementById('deleteKaryawanModal');
        if (modal) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteKaryawanName').textContent = `"${nama}"`;
            modal.classList.remove('hidden');
        }
    }
    function closeDeleteModal() { document.getElementById('deleteKaryawanModal').classList.add('hidden'); }

    // === FORM SUBMISSION ===
    document.getElementById('karyawanForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const fixedIds = Array.from(fixedAllowanceSelections);
        const variableIds = Array.from(variableAllowanceSelections);
        formData.append('tunjangan_tetap_ids', JSON.stringify(fixedIds));
        formData.append('tunjangan_tidak_tetap_ids', JSON.stringify(variableIds));
        
        const id = document.getElementById('formId').value;
        let url = '/admin/karyawan/store';
        if (currentModalMode === 'edit' && id) {
            url = `/admin/karyawan/update/${id}`;
            formData.append('_method', 'PUT');
        }
        
        showLoading(true);
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                showMinimalPopup('Berhasil', result.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                if (response.status === 422 && result.errors) {
                    let errorMsg = Object.values(result.errors)[0];
                    showMinimalPopup('Validasi Gagal', errorMsg, 'warning');
                } else {
                    showMinimalPopup('Error', result.message || 'Gagal menyimpan', 'error');
                }
            }
        } catch (error) {
            console.error('Submit error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
        } finally { showLoading(false); closeKaryawanModal(); }
    });

    // Allowance Master Form Submit
    document.getElementById('allowanceMasterForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('allowanceId').value;
        const formData = new FormData(this);
        let url = '/hr/tunjangan/add';
        if (id) {
            url = `/hr/tunjangan/${id}`;
            formData.append('_method', 'PUT');
        }
        showLoading(true);
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                showMinimalPopup('Berhasil', result.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showMinimalPopup('Error', result.message || 'Gagal menyimpan master tunjangan', 'error');
            }
        } catch (error) {
            console.error('Allowance master error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
        } finally { showLoading(false); closeAllowanceModal(); }
    });

    // Event Listeners for Checkboxes selection styling
    function setupAllowanceCheckboxListeners() {
        document.querySelectorAll('.fixed-allowance-checkbox').forEach(cb => {
            cb.removeEventListener('change', allowanceCheckboxChangeHandler);
            cb.addEventListener('change', allowanceCheckboxChangeHandler);
        });
        document.querySelectorAll('.variable-allowance-checkbox').forEach(cb => {
            cb.removeEventListener('change', allowanceCheckboxChangeHandler);
            cb.addEventListener('change', allowanceCheckboxChangeHandler);
        });
    }
    
    function allowanceCheckboxChangeHandler(e) {
        const card = e.target.closest('.allowance-card');
        if (e.target.checked) {
            card?.classList.add('selected');
            const val = parseInt(e.target.value);
            if (e.target.classList.contains('fixed-allowance-checkbox')) fixedAllowanceSelections.add(val);
            else variableAllowanceSelections.add(val);
        } else {
            card?.classList.remove('selected');
            const val = parseInt(e.target.value);
            if (e.target.classList.contains('fixed-allowance-checkbox')) fixedAllowanceSelections.delete(val);
            else variableAllowanceSelections.delete(val);
        }
    }

    // === INITIALIZE ALL EVENT LISTENERS ===
    function initializeEventListeners() {
        // Modal buttons
        document.getElementById('tambahKaryawanBtn').addEventListener('click', () => openKaryawanModal('create'));
        document.getElementById('closeModalBtn').addEventListener('click', closeKaryawanModal);
        document.getElementById('cancelModalBtn').addEventListener('click', closeKaryawanModal);
        document.getElementById('closeDeleteModalBtn').addEventListener('click', closeDeleteModal);
        document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            const id = document.getElementById('deleteId').value;
            if (id) handleDeleteKaryawan(id);
        // Auto-fill gaji berdasarkan role (untuk modal tambah)
const formRole = document.getElementById('formRole');
const formDivisi = document.getElementById('formDivisi');
if (formRole) {
    formRole.addEventListener('change', autoFillGaji);
}
if (formDivisi) {
    formDivisi.addEventListener('change', autoFillGaji);
}

// Untuk modal edit (jika ada editRoleSelect)
const editRoleSelect = document.getElementById('editRoleSelect');
const editDivisiSelect = document.getElementById('editDivisiSelect');
if (editRoleSelect) {
    editRoleSelect.addEventListener('change', autoFillEditGaji);
}
if (editDivisiSelect) {
    editDivisiSelect.addEventListener('change', autoFillEditGaji);
}
        });
        
        // Edit and Delete buttons (delegated)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-btn')) {
                const button = e.target.closest('.edit-btn');
                const data = {
                    id: button.dataset.id,
                    user_id: button.dataset.user_id,
                    nama: button.dataset.nama,
                    email: button.dataset.email,
                    role: button.dataset.role,
                    divisi_id: button.dataset.divisi_id,
                    alamat: button.dataset.alamat,
                    kontak: button.dataset.kontak,
                    gaji: button.dataset.gaji,
                    status_kerja: button.dataset.status_kerja,
                    status_karyawan: button.dataset.status_karyawan,
                    tunjangan_tetap_ids: button.dataset.tunjangan_tetap_ids,
                    tunjangan_tidak_tetap_ids: button.dataset.tunjangan_tidak_tetap_ids
                };
                openKaryawanModal('edit', data);
            }
            if (e.target.closest('.delete-btn')) {
                const button = e.target.closest('.delete-btn');
                openDeleteModal(button.dataset.id, button.dataset.nama);
            }
        });
        
        // Divisi change -> load tims
        document.getElementById('formDivisi').addEventListener('change', function() {
            loadTims('formTim', this.value);
        });
        
        // Foto preview
        const fotoInput = document.getElementById('fotoInput');
        const pilihFotoBtn = document.getElementById('pilihFotoBtn');
        if (pilihFotoBtn) pilihFotoBtn.addEventListener('click', () => fotoInput?.click());
        if (fotoInput) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const fotoPreview = document.getElementById('fotoPreview');
                if (file && fotoPreview) {
                    const reader = new FileReader();
                    reader.onload = function(e) { fotoPreview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-16 w-16 rounded-full object-cover">`; };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Allowance buttons
        document.getElementById('addFixedAllowanceBtn').addEventListener('click', () => openAllowanceModal(null, '', 'bulanan', ''));
        document.getElementById('addVariableAllowanceBtn').addEventListener('click', () => openAllowanceModal(null, '', 'bonus', ''));
        document.getElementById('closeAllowanceModal').addEventListener('click', closeAllowanceModal);
        document.getElementById('cancelAllowanceBtn').addEventListener('click', closeAllowanceModal);
        
        // Allowance edit buttons (delegated)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-allowance-edit')) {
                const btn = e.target.closest('.btn-allowance-edit');
                openAllowanceModal(btn.dataset.id, btn.dataset.nama, btn.dataset.tipe, btn.dataset.nominal);
            }
        });
        
        // Close modal on outside click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) e.target.classList.add('hidden');
            if (e.target === allowanceModal) closeAllowanceModal();
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => modal.classList.add('hidden'));
                closeAllowanceModal();
            }
        });
        
        // Popup close
        document.querySelector('.minimal-popup-close')?.addEventListener('click', function() {
            document.getElementById('minimalPopup')?.classList.remove('show');
        });
        
        setupAllowanceCheckboxListeners();
    }

    // === INITIALIZE ALL ===
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Data karyawan page loaded');
        initializePagination();
        Promise.all([loadRoleFilters(), loadDivisionFilters()]).then(() => { initializeFilter(); });
        initializeSearch();
        initializeEventListeners();
        loadDivisis('formDivisi');
        setupAllowanceCheckboxListeners();
    });
    // Auto-fill gaji berdasarkan role yang dipilih
async function autoFillGaji() {
    const role = document.getElementById('formRole').value;
    const divisiId = document.getElementById('formDivisi').value;
    const gajiInput = document.getElementById('formGaji');
    
    if (!role || !gajiInput) return;
    
    try {
        // Panggil API untuk mendapatkan template gaji
        let url = `/api/gaji-template?role=${role}`;
        if (divisiId) url += `&divisi_id=${divisiId}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
        });
        const result = await response.json();
        
        if (result.success && result.data && result.data.gaji_pokok) {
            gajiInput.value = result.data.gaji_pokok;
            showMinimalPopup('Info', `Gaji default untuk ${role}: Rp ${new Intl.NumberFormat('id-ID').format(result.data.gaji_pokok)}`, 'success');
        } else {
            // Default jika tidak ada template
            const defaultGaji = {
                'general_manager': 15000000,
                'manager_divisi': 10000000,
                'finance': 8000000,
                'hr': 7000000,
                'karyawan': 5000000
            };
            if (defaultGaji[role]) {
                gajiInput.value = defaultGaji[role];
                showMinimalPopup('Info', `Gaji default untuk ${role}: Rp ${new Intl.NumberFormat('id-ID').format(defaultGaji[role])}`, 'success');
            }
        }
    } catch (error) {
        console.error('Auto-fill gaji error:', error);
    }
}

// Event listener untuk role dan divisi
document.getElementById('formRole')?.addEventListener('change', autoFillGaji);
document.getElementById('formDivisi')?.addEventListener('change', autoFillGaji);


// ============================================================
// AUTO-FILL GAJI BERDASARKAN ROLE
// ============================================================
async function autoFillGaji() {
    const roleSelect = document.getElementById('formRole');
    const divisiSelect = document.getElementById('formDivisi');
    const gajiInput = document.getElementById('formGaji');
    
    if (!roleSelect || !gajiInput) return;
    
    const role = roleSelect.value;
    const divisiId = divisiSelect ? divisiSelect.value : '';
    
    if (!role) {
        gajiInput.value = '';
        return;
    }
    
    try {
        let url = `/api/gaji-template?role=${encodeURIComponent(role)}`;
        if (divisiId) {
            url += `&divisi_id=${encodeURIComponent(divisiId)}`;
        }
        
        console.log('Fetching gaji template from:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });
        
        const result = await response.json();
        console.log('Gaji template response:', result);
        
        if (result.success && result.data && result.data.gaji_pokok) {
            gajiInput.value = result.data.gaji_pokok;
            const roleName = role.replace('_', ' ').toUpperCase();
            showMinimalPopup(
                'Info', 
                `💼 Gaji default untuk ${roleName}: ${result.data.gaji_formatted || 'Rp ' + new Intl.NumberFormat('id-ID').format(result.data.gaji_pokok)}`, 
                'success'
            );
        } else {
            // Fallback ke default
            const defaultGaji = {
                'general_manager': 15000000,
                'manager_divisi': 10000000,
                'finance': 8000000,
                'hr': 7000000,
                'karyawan': 5000000
            };
            if (defaultGaji[role]) {
                gajiInput.value = defaultGaji[role];
                showMinimalPopup('Info', `Gaji default untuk ${role}: Rp ${new Intl.NumberFormat('id-ID').format(defaultGaji[role])}`, 'info');
            }
        }
    } catch (error) {
        console.error('Auto-fill gaji error:', error);
        // Fallback jika API error
        const defaultGaji = {
            'general_manager': 15000000,
            'manager_divisi': 10000000,
            'finance': 8000000,
            'hr': 7000000,
            'karyawan': 5000000
        };
        if (defaultGaji[role]) {
            gajiInput.value = defaultGaji[role];
        }
    }
}

// Fungsi untuk auto-fill gaji di edit modal
async function autoFillEditGaji() {
    const roleSelect = document.getElementById('editRoleSelect');
    const divisiSelect = document.getElementById('editDivisiSelect');
    const gajiInput = document.getElementById('editGaji');
    
    if (!roleSelect || !gajiInput) return;
    
    const role = roleSelect.value;
    const divisiId = divisiSelect ? divisiSelect.value : '';
    
    if (!role) {
        if (gajiInput) gajiInput.value = '';
        return;
    }
    
    try {
        let url = `/api/gaji-template?role=${encodeURIComponent(role)}`;
        if (divisiId) {
            url += `&divisi_id=${encodeURIComponent(divisiId)}`;
        }
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.data && result.data.gaji_pokok && gajiInput) {
            // Hanya set jika field gaji masih kosong atau belum diisi manual
            if (!gajiInput.value || gajiInput.value === '0' || gajiInput.value === '') {
                gajiInput.value = result.data.gaji_pokok;
            }
        }
    } catch (error) {
        console.error('Auto-fill edit gaji error:', error);
    }
}
</script>

</body>
</html>