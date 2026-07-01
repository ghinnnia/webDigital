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
    </style>
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('admin/templet/sider')

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
                                <div class="filter-option">
                                    <input type="checkbox" id="filterAll" value="all" checked>
                                    <label for="filterAll">Semua Role</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterManager" value="manager">
                                    <label for="filterManager">Manager</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterStaff" value="staff">
                                    <label for="filterStaff">Staff</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterIntern" value="intern">
                                    <label for="filterIntern">Intern</label>
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
                                            <th style="min-width: 250px;">Alamat</th>
                                            <th style="min-width: 150px;">Kontak</th>
                                            <th style="min-width: 100px;">Foto</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        @if (isset($karyawan) && count($karyawan) > 0)
                                            @php 
                                                $no = 1; 
                                                $currentUserRole = auth()->user()->role;
                                                $isFinance = $currentUserRole === 'finance';
                                            @endphp
                                            @foreach ($karyawan as $item)
                                                <tr class="karyawan-row" data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->name }}" data-email="{{ $item->email }}"
                                                    data-role="{{ $item->role }}" data-divisi="{{ $item->divisi ? $item->divisi->divisi : ($item->karyawan && $item->karyawan->divisi ? $item->karyawan->divisi : '') }}"
                                                    data-alamat="{{ $item->alamat }}" data-kontak="{{ $item->kontak }}"
                                                    data-foto="{{ $item->foto ?? '' }}">
                                                    <td style="min-width: 60px;">{{ $no++ }}</td>
                                                    <td style="min-width: 200px;">
                                                        <div class="flex items-center gap-2">
                                                            @if ($item->foto)
                                                                <img src="{{ asset('storage/' . $item->foto) }}"
                                                                    alt="{{ $item->name }}"
                                                                    class="h-8 w-8 rounded-full object-cover"
                                                                    onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                            @else
                                                                <div
                                                                    class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                                    <span
                                                                        class="material-icons-outlined text-gray-500 text-sm">person</span>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div class="font-medium">{{ $item->name }}</div>
                                                                <div class="text-xs text-gray-500">ID: {{ $item->id }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="min-width: 200px;">
                                                        <div class="text-sm">{{ $item->email }}</div>
                                                        <div class="text-xs text-gray-500">Role: {{ $item->role }}</div>
                                                    </td>
                                                    <td style="min-width: 150px;">
                                                        <span
                                                            class="status-badge 
                                                            @if (in_array(strtolower($item->role), ['manager', 'general_manager', 'manager_divisi', 'admin', 'owner'])) status-manager
                                                            @elseif(strtolower($item->role) == 'staff' || $item->role == 'karyawan') status-staff
                                                            @elseif(strtolower($item->role) == 'intern' || $item->role == 'magang') status-intern
                                                            @else status-staff @endif">
                                                            {{ $item->role }}
                                                        </span>
                                                    </td>
                                                    <td style="min-width: 150px;">{{ $item->divisi ? $item->divisi->divisi : ($item->karyawan && $item->karyawan->divisi ? $item->karyawan->divisi : '-') }}</td>
                                                    <td style="min-width: 250px;">{{ $item->alamat }}</td>
                                                    <td style="min-width: 150px;">{{ $item->kontak }}</td>
                                                    <td style="min-width: 100px;">
                                                        @if ($item->foto)
                                                            <img src="{{ asset('storage/' . $item->foto) }}"
                                                                alt="{{ $item->name }}"
                                                                class="h-10 w-10 rounded-full object-cover mx-auto"
                                                                onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                        @else
                                                            <div
                                                                class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mx-auto">
                                                                <span
                                                                    class="material-icons-outlined text-gray-500">person</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td style="min-width: 100px; text-align: center;">
                                                        <div class="flex justify-center gap-2">
                                                            <button
                                                                class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                                data-id="{{ $item->id }}"
                                                                data-nama="{{ $item->name }}"
                                                                data-email="{{ $item->email }}"
                                                                data-role="{{ $item->role }}"
                                                                data-divisi_id="{{ $item->divisi_id }}"
                                                                data-divisi="{{ $item->divisi ? $item->divisi->divisi : '' }}"
                                                                data-alamat="{{ $item->alamat }}"
                                                                data-kontak="{{ $item->kontak }}"
                                                                data-status_kerja="{{ $item->status_kerja }}"
                                                                data-status_karyawan="{{ $item->status_karyawan }}"
                                                                data-gaji="{{ $item->gaji }}"
                                                                data-foto="{{ $item->foto ?? '' }}">
                                                                <span class="material-icons-outlined">edit</span>
                                                            </button>
                                                            <button
                                                                class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                                data-id="{{ $item->id }}"
                                                                data-nama="{{ $item->name }}">
                                                                <span class="material-icons-outlined">delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="10"
                                                    class="px-6 py-4 text-center text-sm text-gray-500">
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
                                    $currentUserRole = auth()->user()->role;
                                    $isFinance = $currentUserRole === 'finance';
                                @endphp
                                @foreach ($karyawan as $item)
                                    <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm karyawan-card"
                                        data-id="{{ $item->id }}" data-nama="{{ $item->name }}"
                                        data-role="{{ $item->role }}" data-divisi="{{ $item->divisi ? $item->divisi->divisi : ($item->karyawan && $item->karyawan->divisi ? $item->karyawan->divisi : '') }}"
                                        data-alamat="{{ $item->alamat }}" data-kontak="{{ $item->kontak }}"
                                        data-foto="{{ $item->foto ?? '' }}">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center gap-3">
                                                @if ($item->foto)
                                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                                        alt="{{ $item->name }}"
                                                        class="h-12 w-12 rounded-full object-cover"
                                                        onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                                @else
                                                    <div
                                                        class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span
                                                            class="material-icons-outlined text-gray-500">person</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="font-semibold text-base">{{ $item->name }}</h4>
                                                    <p class="text-sm text-text-muted-light">{{ $item->kontak }}</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <button
                                                    class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                    data-karyawan='{"id": "{{ $item->id }}", "name": "{{ $item->name }}", "email": "{{ $item->email }}", "role": "{{ $item->role }}", "divisi_id": "{{ $item->divisi_id }}", "divisi": "{{ $item->divisi ? $item->divisi->divisi : ($item->karyawan && $item->karyawan->divisi ? $item->karyawan->divisi : '') }}", "alamat": "{{ $item->alamat }}", "kontak": "{{ $item->kontak }}", "status_kerja": "{{ $item->status_kerja }}", "status_karyawan": "{{ $item->status_karyawan }}", "gaji": "{{ $item->gaji }}", "foto": "{{ $item->foto ?? '' }}" }'>
                                                    <span class="material-icons-outlined">edit</span>
                                                </button>
                                                <button
                                                    class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                    data-id="{{ $item->id }}" data-nama="{{ $item->name }}">
                                                    <span class="material-icons-outlined">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div>
                                                <p class="text-text-muted-light">No</p>
                                                <p class="font-medium">{{ $no++ }}</p>
                                            </div>
                                            <div>
                                                <p class="text-text-muted-light">Role</p>
                                                <p>
                                                    <span
                                                        class="status-badge 
                                                            @if (strtolower($item->role) == 'manager') status-manager
                                                            @elseif(strtolower($item->role) == 'staff') status-staff
                                                            @else status-intern @endif">
                                                        {{ $item->role }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-text-muted-light">Divisi</p>
                                                <p class="font-medium">{{ $item->divisi ? $item->divisi->divisi : ($item->karyawan && $item->karyawan->divisi ? $item->karyawan->divisi : '-') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-text-muted-light">Alamat</p>
                                                <p class="font-medium truncate">{{ $item->alamat }}</p>
                                            </div>
                                        </div>
                                        @if($item->gaji)
                                            <div class="mt-3 pt-3 border-t border-gray-100">
                                                <p class="text-text-muted-light">Gaji</p>
                                                <div class="flex items-center justify-between">
                                                    <p class="font-medium">{{ $item->gaji }}</p>
                                                    @if(!$isFinance)
                                                        <span class="material-icons-outlined text-gray-400 text-sm" title="Hanya finance yang bisa mengedit">lock</span>
                                                    @endif
                                                </div>
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
                Copyright ©2025 by Digital Kolaborasi.id
            </footer>
        </main>
    </div>

    <!-- Popup Modal untuk Tambah Karyawan -->
    <div id="tambahKaryawanModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Karyawan Baru</h3>
                    <button id="closeModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="tambahKaryawanForm" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @php
                        $currentUserRole = auth()->user()->role;
                        $isFinance = $currentUserRole === 'finance';
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama (INPUT TEXT) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                            <input type="text" name="name" id="namaInput" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nama karyawan">
                        </div>

                        <!-- Email (INPUT TEXT) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="emailInput" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan email">
                        </div>

                        <!-- Password (INPUT PASSWORD) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                            <input type="password" name="password" id="passwordInput" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan password">
                            <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                        </div>

                        <!-- Role (DROPDOWN) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select name="role" id="roleSelect" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Role</option>
                                <option value="general_manager">General Manager</option>
                                <option value="manager_divisi">Manager Divisi</option>
                                <option value="karyawan">Karyawan</option>
                                <option value="finance">Finance</option>
                            </select>
                        </div>

                        <!-- Divisi (DROPDOWN dari database) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <select name="divisi_id" id="divisiSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Divisi</option>
                                @if (isset($divisis) && count($divisis) > 0)
                                    @foreach ($divisis as $divisi)
                                        <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Gaji (INPUT NUMBER) - Hanya bisa diisi oleh finance -->
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gaji</label>
                            <input type="number" name="gaji" id="gajiInput" 
                                @if(!$isFinance)
                                    readonly 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary input-readonly"
                                @else
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                @endif
                                placeholder="{{ $isFinance ? 'Masukkan gaji' : 'Kosongkan - hanya finance' }}">
                            @if(!$isFinance)
                                <div class="lock-overlay">
                                    <span class="material-icons-outlined">lock</span>
                                    <span>Hanya finance</span>
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">
                                @if(!$isFinance)
                                    <span class="text-orange-500 font-medium"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Gaji hanya dapat diatur oleh finance</span>
                                @else
                                    Isi dalam angka tanpa titik/koma
                                @endif
                            </p>
                        </div>

                        <!-- Kontak (INPUT TEXT) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak *</label>
                            <input type="text" name="kontak" id="kontakInput" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nomor telepon">
                        </div>

                        <!-- Status Kerja (DROPDOWN) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerja</label>
                            <select name="status_kerja" id="statusKerjaSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="aktif" selected>Aktif</option>
                                <option value="resign">Resign</option>
                                <option value="phk">PHK</option>
                            </select>
                        </div>

                        <!-- Status Karyawan (DROPDOWN) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                            <select name="status_karyawan" id="statusKaryawanSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="tetap" selected>Tetap</option>
                                <option value="kontrak">Kontrak</option>
                                <option value="freelance">Freelance</option>
                            </select>
                        </div>

                        <!-- Alamat (TEXTAREA) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat *</label>
                            <textarea name="alamat" id="alamatInput" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan alamat lengkap"></textarea>
                        </div>

                        <!-- Foto -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                            <div class="flex items-center space-x-4">
                                <div id="fotoPreview"
                                    class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="material-icons-outlined text-gray-500 text-2xl">person</span>
                                </div>
                                <div>
                                    <input type="file" name="foto" id="fotoInput" class="hidden"
                                        accept="image/*">
                                    <button type="button" id="pilihFotoBtn"
                                        class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                        Pilih Foto
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG maks. 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelBtn"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan
                            Karyawan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Popup Modal untuk Edit Karyawan -->
    <div id="editKaryawanModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Karyawan</h3>
                    <button id="closeEditModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editKaryawanForm" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @php
                        $currentUserRole = auth()->user()->role;
                        $isFinance = $currentUserRole === 'finance';
                    @endphp
                    <input type="hidden" id="editId" name="id">
                    <!-- Perhatikan bahwa form update menggunakan user_id, bukan karyawan_id -->
                    <input type="hidden" id="editUserId" name="user_id">
                    <!-- Simpan nilai gaji asli untuk validasi -->
                    <input type="hidden" id="editGajiOriginal" name="gaji_original">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama (INPUT TEXT - bisa diedit) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" id="editNama" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nama karyawan">
                        </div>

                        <!-- Email (INPUT TEXT - bisa diedit) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="editEmail" name="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan email">
                        </div>

                        <!-- Role (DROPDOWN - bisa dipilih) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select name="role" id="editRoleSelect" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Role</option>
                                <option value="general_manager">General Manager</option>
                                <option value="manager_divisi">Manager Divisi</option>
                                <option value="karyawan">Karyawan</option>
                                <option value="finance">Finance</option>
                            </select>
                        </div>

                        <!-- Divisi (DROPDOWN dari database) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <select name="divisi_id" id="editDivisiSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Divisi</option>
                                @if (isset($divisis) && count($divisis) > 0)
                                    @foreach ($divisis as $divisi)
                                        <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Gaji (INPUT NUMBER) - Hanya bisa diedit oleh finance -->
                        <div class="relative gaji-disabled">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gaji</label>
                            <input type="number" id="editGaji" name="gaji"
                                @if(!$isFinance)
                                    readonly 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary input-readonly"
                                @else
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                @endif
                                placeholder="{{ $isFinance ? 'Masukkan gaji' : 'Tidak dapat diedit' }}">
                            @if(!$isFinance)
                                <div class="lock-overlay">
                                    <span class="material-icons-outlined">lock</span>
                                    <span>Hanya finance yang bisa mengedit</span>
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">
                                @if(!$isFinance)
                                    <span class="text-orange-500 font-medium"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Hanya dapat diubah oleh finance</span>
                                @else
                                    Isi dalam angka tanpa titik/koma
                                @endif
                            </p>
                        </div>

                        <!-- Kontak (INPUT TEXT) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak *</label>
                            <input type="text" id="editKontak" name="kontak" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nomor telepon">
                        </div>

                        <!-- Status Kerja (DROPDOWN) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerja</label>
                            <select name="status_kerja" id="editStatusKerja"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="aktif">Aktif</option>
                                <option value="resign">Resign</option>
                                <option value="phk">PHK</option>
                            </select>
                        </div>

                        <!-- Status Karyawan (DROPDOWN) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                            <select name="status_karyawan" id="editStatusKaryawan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="tetap">Tetap</option>
                                <option value="kontrak">Kontrak</option>
                                <option value="freelance">Freelance</option>
                            </select>
                        </div>

                        <!-- Alamat (TEXTAREA) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat *</label>
                            <textarea id="editAlamat" name="alamat" required rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan alamat lengkap"></textarea>
                        </div>

                        <!-- Foto -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                            <div class="flex items-center space-x-4">
                                <div id="editFotoPreview"
                                    class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="material-icons-outlined text-gray-500 text-2xl">person</span>
                                </div>
                                <div>
                                    <input type="file" name="foto" id="editFotoInput" class="hidden"
                                        accept="image/*">
                                    <button type="button" id="pilihEditFotoBtn"
                                        class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                        Pilih Foto
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG maks. 2MB</p>
                                </div>
                            </div>
                            <!-- Input hidden untuk menyimpan foto lama -->
                            <input type="hidden" id="editFotoLama" name="foto_lama">
                        </div>

                        <!-- Password (Opsional) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" id="editPassword" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                            <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelEditBtn"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">Update
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Popup Modal untuk Konfirmasi Hapus -->
    <div id="deleteKaryawanModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
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
                    <p class="text-xs text-gray-400">Tindakan ini tidak dapat dibatalkan dan data akan dihapus
                        permanen.</p>

                    <input type="hidden" id="deleteId" name="id">
                </div>

                <div class="flex justify-center gap-3">
                    <button type="button" id="cancelDeleteBtn"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="button" id="confirmDeleteBtn"
                        class="px-5 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                        Hapus Data
                    </button>
                </div>
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

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay hidden">
        <div class="loading-spinner"></div>
    </div>

<script>
    // Inisialisasi variabel untuk pagination, filter, dan search
    let currentPage = 1;
    const itemsPerPage = 5;
    let activeFilters = ['all'];
    let searchTerm = '';

    // Dapatkan semua elemen karyawan
    let karyawanRows = document.querySelectorAll('.karyawan-row');
    let karyawanCards = document.querySelectorAll('.karyawan-card');

    // === UTILITY FUNCTIONS ===
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
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

        let displayNumber = 1;
        visibleRows.forEach((row, index) => {
            if (index >= startIndex && index < endIndex) {
                row.style.display = '';
                // Update nomor di kolom pertama
                const noCell = row.querySelector('td:first-child');
                if (noCell) {
                    noCell.textContent = displayNumber;
                }
                displayNumber++;
            }
        });

        let cardNumber = 1;
        visibleCards.forEach((card, index) => {
            if (index >= startIndex && index < endIndex) {
                card.style.display = 'block';
                // Update nomor di card mobile
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
    function initializeFilter() {
        const filterBtn = document.getElementById('filterBtn');
        const filterDropdown = document.getElementById('filterDropdown');
        const applyFilterBtn = document.getElementById('applyFilter');
        const resetFilterBtn = document.getElementById('resetFilter');
        const filterAll = document.getElementById('filterAll');

        if (!filterBtn || !filterDropdown) return;

        filterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            filterDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            filterDropdown.classList.remove('show');
        });

        filterDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        if (filterAll) {
            filterAll.addEventListener('change', function() {
                if (this.checked) {
                    document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(
                        cb => {
                            cb.checked = false;
                        });
                }
            });
        }

        document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    if (filterAll) filterAll.checked = false;
                }
            });
        });

        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', function() {
                const filterAll = document.getElementById('filterAll');
                const filterManager = document.getElementById('filterManager');
                const filterStaff = document.getElementById('filterStaff');
                const filterIntern = document.getElementById('filterIntern');

                activeFilters = [];
                if (filterAll && filterAll.checked) {
                    activeFilters.push('all');
                } else {
                    if (filterManager && filterManager.checked) activeFilters.push('manager');
                    if (filterStaff && filterStaff.checked) activeFilters.push('staff');
                    if (filterIntern && filterIntern.checked) activeFilters.push('intern');
                }

                applyFilters();
                filterDropdown.classList.remove('show');
                const visibleCount = getFilteredRows().length;
                showMinimalPopup('Filter Diterapkan', `Menampilkan ${visibleCount} karyawan`, 'success');
            });
        }

        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', function() {
                const filterAll = document.getElementById('filterAll');
                const filterManager = document.getElementById('filterManager');
                const filterStaff = document.getElementById('filterStaff');
                const filterIntern = document.getElementById('filterIntern');

                if (filterAll) filterAll.checked = true;
                if (filterManager) filterManager.checked = false;
                if (filterStaff) filterStaff.checked = false;
                if (filterIntern) filterIntern.checked = false;

                activeFilters = ['all'];
                applyFilters();
                filterDropdown.classList.remove('show');
                const visibleCount = getFilteredRows().length;
                showMinimalPopup('Filter Direset', 'Menampilkan semua karyawan', 'success');
            });
        }
    }

    function applyFilters() {
        currentPage = 1;

        karyawanRows.forEach(row => {
            const role = row.getAttribute('data-role').toLowerCase();
            const nama = row.getAttribute('data-nama').toLowerCase();
            const alamat = row.getAttribute('data-alamat').toLowerCase();

            let roleMatches = false;
            if (activeFilters.includes('all')) {
                roleMatches = true;
            } else {
                roleMatches = activeFilters.some(filter => role.includes(filter.toLowerCase()));
            }

            let searchMatches = true;
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                searchMatches = nama.includes(searchLower) ||
                    alamat.includes(searchLower) ||
                    role.includes(searchLower);
            }

            if (roleMatches && searchMatches) {
                row.classList.remove('hidden-by-filter');
            } else {
                row.classList.add('hidden-by-filter');
            }
        });

        karyawanCards.forEach(card => {
            const role = card.getAttribute('data-role').toLowerCase();
            const nama = card.getAttribute('data-nama').toLowerCase();
            const alamat = card.getAttribute('data-alamat').toLowerCase();

            let roleMatches = false;
            if (activeFilters.includes('all')) {
                roleMatches = true;
            } else {
                roleMatches = activeFilters.some(filter => role.includes(filter.toLowerCase()));
            }

            let searchMatches = true;
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                searchMatches = nama.includes(searchLower) ||
                    alamat.includes(searchLower) ||
                    role.includes(searchLower);
            }

            if (roleMatches && searchMatches) {
                card.classList.remove('hidden-by-filter');
            } else {
                card.classList.add('hidden-by-filter');
            }
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

    // === AUTO-FILL USER DATA ===
    function initializeAutoFill() {
        const userSelect = document.getElementById('userSelect');
        if (!userSelect) return;

        userSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {
                const nama = selectedOption.getAttribute('data-nama');
                const role = selectedOption.getAttribute('data-role');
                const divisi = selectedOption.getAttribute('data-divisi');
                const email = selectedOption.getAttribute('data-email');

                const namaInput = document.getElementById('namaInput');
                const emailInput = document.getElementById('emailInput');
                const roleInput = document.getElementById('roleInput');
                const divisiInput = document.getElementById('divisiInput');

                if (namaInput) namaInput.value = nama || '';
                if (emailInput) emailInput.value = email || '';
                if (roleInput && !roleInput.value.trim()) roleInput.value = role || '';
                if (divisiInput && !divisiInput.value.trim()) divisiInput.value = divisi || '';
            } else {
                clearAutoFillFields();
            }
        });
    }

    function clearAutoFillFields() {
        const inputs = ['namaInput', 'emailInput', 'roleInput', 'divisiInput'];

        inputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.value = '';
            }
        });
    }

    // Fungsi untuk mengambil data karyawan saat edit
    async function fetchKaryawanData(id) {
        try {
            showLoading(true);
            const response = await fetch(`/admin/karyawan/get/${id}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                return data.data;
            } else {
                showMinimalPopup('Error', data.message || 'Gagal mengambil data karyawan', 'error');
                return null;
            }
        } catch (error) {
            console.error('Fetch karyawan error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan saat mengambil data', 'error');
            return null;
        } finally {
            showLoading(false);
        }
    }

    // Cek apakah user adalah finance
    const isFinance = {{ auth()->user()->role === 'finance' ? 'true' : 'false' }};

    // Modal Edit
    async function openEditModal(data) {
        console.log('Opening edit modal with data:', data);

        if (!data || !data.id) {
            showMinimalPopup('Error', 'Data karyawan tidak valid', 'error');
            return;
        }

        showLoading(true);

        // Ambil data terbaru dari server
        const karyawanData = await fetchKaryawanData(data.id);

        if (!karyawanData) {
            showLoading(false);
            return;
        }

        // Set nilai form
        const editId = document.getElementById('editId');
        const editUserId = document.getElementById('editUserId');
        const editNama = document.getElementById('editNama');
        const editEmail = document.getElementById('editEmail');
        const editRoleSelect = document.getElementById('editRoleSelect');
        const editDivisiSelect = document.getElementById('editDivisiSelect');
        const editGaji = document.getElementById('editGaji');
        const editKontak = document.getElementById('editKontak');
        const editAlamat = document.getElementById('editAlamat');
        const editStatusKerja = document.getElementById('editStatusKerja');
        const editStatusKaryawan = document.getElementById('editStatusKaryawan');
        const editFotoPreview = document.getElementById('editFotoPreview');
        const editFotoLama = document.getElementById('editFotoLama');
        const editKaryawanForm = document.getElementById('editKaryawanForm');
        const editKaryawanModal = document.getElementById('editKaryawanModal');
        const editGajiOriginal = document.getElementById('editGajiOriginal');

        if (!editId || !editNama || !editKaryawanForm || !editKaryawanModal) {
            console.error('Required modal elements not found');
            showLoading(false);
            return;
        }

        console.log('Karyawan data from API:', karyawanData);

        // Set nilai form
        editId.value = karyawanData.id; // ID karyawan
        if (editUserId) {
            editUserId.value = karyawanData.user_id || '';
        }
        editNama.value = karyawanData.name || '';
        editEmail.value = karyawanData.email || '';

        // Set role dropdown (dari database)
        if (editRoleSelect) {
            editRoleSelect.value = karyawanData.role || '';
        }

        // Set divisi dropdown
        if (editDivisiSelect) {
            editDivisiSelect.value = karyawanData.divisi_id || '';
        }

        // Set gaji dan simpan nilai asli
        if (editGaji) {
            editGaji.value = karyawanData.gaji || '';
            // Simpan nilai gaji asli untuk validasi
            if (editGajiOriginal) {
                editGajiOriginal.value = karyawanData.gaji || '';
            }
            
            // Jika bukan finance, tambahkan pesan khusus
            if (!isFinance) {
                editGaji.title = "Gaji hanya dapat diubah oleh finance";
            }
        }

        // Set kontak
        if (editKontak) editKontak.value = karyawanData.kontak || '';

        // Set alamat
        if (editAlamat) editAlamat.value = karyawanData.alamat || '';

        // Set status kerja dropdown
        if (editStatusKerja) {
            editStatusKerja.value = karyawanData.status_kerja || 'aktif';
        }

        // Set status karyawan dropdown
        if (editStatusKaryawan) {
            editStatusKaryawan.value = karyawanData.status_karyawan || 'tetap';
        }

        // Tampilkan foto karyawan jika ada
        if (editFotoPreview) {
            if (karyawanData.foto) {
                editFotoPreview.innerHTML =
                    `<img src="${karyawanData.foto}" alt="${karyawanData.name}" class="h-16 w-16 rounded-full object-cover">`;
            } else {
                editFotoPreview.innerHTML =
                    '<span class="material-icons-outlined text-gray-500 text-2xl">person</span>';
            }
        }

        // PERBAIKAN PENTING: Gunakan ID karyawan untuk update URL
        editKaryawanForm.action = `/admin/karyawan/update/${karyawanData.id}`;
        
        console.log('Form action updated to:', editKaryawanForm.action);
        console.log('Karyawan ID:', karyawanData.id);
        console.log('User ID:', karyawanData.user_id);

        // Tampilkan modal
        editKaryawanModal.classList.remove('hidden');
        showLoading(false);
    }

    function closeEditModal() {
        const modal = document.getElementById('editKaryawanModal');
        const form = document.getElementById('editKaryawanForm');
        const fotoPreview = document.getElementById('editFotoPreview');

        if (modal) modal.classList.add('hidden');
        if (form) {
            form.reset();
            const editId = document.getElementById('editId');
            if (editId) editId.value = '';
            const editUserId = document.getElementById('editUserId');
            if (editUserId) editUserId.value = '';
            const editGajiOriginal = document.getElementById('editGajiOriginal');
            if (editGajiOriginal) editGajiOriginal.value = '';
        }
        if (fotoPreview) {
            fotoPreview.innerHTML = '<span class="material-icons-outlined text-gray-500 text-2xl">person</span>';
        }
    }

    // Modal Tambah
    function openTambahModal() {
        const modal = document.getElementById('tambahKaryawanModal');
        if (modal) {
            modal.classList.remove('hidden');
            clearAutoFillFields();

            const userSelect = document.getElementById('userSelect');
            if (userSelect) userSelect.selectedIndex = 0;
        }
    }

    function closeTambahModal() {
        const modal = document.getElementById('tambahKaryawanModal');
        const form = document.getElementById('tambahKaryawanForm');
        const fotoPreview = document.getElementById('fotoPreview');

        if (modal) modal.classList.add('hidden');
        if (form) form.reset();
        if (fotoPreview) {
            fotoPreview.innerHTML = '<span class="material-icons-outlined text-gray-500 text-2xl">person</span>';
        }

        clearAutoFillFields();

        const userSelect = document.getElementById('userSelect');
        if (userSelect) userSelect.selectedIndex = 0;
    }

    // Modal Delete
    function openDeleteModal(id, nama) {
        const modal = document.getElementById('deleteKaryawanModal');
        if (modal) {
            document.getElementById('deleteId').value = id;
            const deleteKaryawanName = document.getElementById('deleteKaryawanName');
            if (deleteKaryawanName) {
                deleteKaryawanName.textContent = `"${nama}"`;
            }
            modal.classList.remove('hidden');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteKaryawanModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // === FORM VALIDATION ===
    function validateForm(form) {
        if (!form) return false;

        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('input-error');
                isValid = false;

                const errorMsg = field.parentElement.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.textContent = 'Field ini wajib diisi';
                    errorMsg.classList.add('show');
                }
            } else {
                field.classList.remove('input-error');

                const errorMsg = field.parentElement.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.classList.remove('show');
                }
            }
        });

        const emailField = form.querySelector('input[type="email"]');
        if (emailField && emailField.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                emailField.classList.add('input-error');
                isValid = false;
            }
        }

        return isValid;
    }

    // === VALIDASI GAJI KHUSUS ===
    function validateGajiPermissions(form, isEdit = false) {
        if (isFinance) {
            return true; // Finance bisa melakukan apa saja
        }

        const gajiInput = form.querySelector('#gajiInput') || form.querySelector('#editGaji');
        
        if (!gajiInput) {
            return true;
        }

        // Untuk form tambah: non-finance tidak boleh mengisi gaji
        if (!isEdit) {
            if (gajiInput.value.trim() !== '') {
                showMinimalPopup(
                    'Akses Ditolak', 
                    'Anda tidak memiliki izin untuk mengatur gaji. Biarkan kosong atau hubungi finance.', 
                    'warning'
                );
                gajiInput.value = ''; // Reset nilai gaji
                return false;
            }
        } 
        // Untuk form edit: non-finance tidak boleh mengubah gaji yang sudah ada
        else {
            const originalGaji = document.getElementById('editGajiOriginal');
            if (originalGaji && originalGaji.value && gajiInput.value !== originalGaji.value) {
                showMinimalPopup(
                    'Akses Ditolak', 
                    'Anda tidak memiliki izin untuk mengubah gaji. Hubungi finance untuk perubahan gaji.', 
                    'error'
                );
                gajiInput.value = originalGaji.value; // Kembalikan ke nilai asli
                return false;
            }
        }

        return true;
    }

    // === FORM SUBMISSION ===
    // CREATE (POST)
    function initializeCreateForm() {
        const tambahKaryawanForm = document.getElementById('tambahKaryawanForm');
        if (!tambahKaryawanForm) return;

        tambahKaryawanForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                showMinimalPopup('Validasi Gagal', 'Harap periksa kembali form yang diisi', 'warning');
                return;
            }

            // Validasi permission gaji
            if (!validateGajiPermissions(this, false)) {
                return;
            }

            const submitBtn = tambahKaryawanForm.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent || 'Simpan';

            if (submitBtn) {
                submitBtn.textContent = 'Menyimpan...';
                submitBtn.disabled = true;
            }
            showLoading(true);

            const formData = new FormData(tambahKaryawanForm);

            try {
                const response = await fetch("/admin/karyawan/store", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const res = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && res.errors) {
                        const message = res.errors.foto ?
                            res.errors.foto[0] :
                            Object.values(res.errors)[0][0];

                        showMinimalPopup('Validasi Gagal', message, 'warning');
                        return;
                    }

                    showMinimalPopup('Error', res.message || 'Terjadi kesalahan', 'error');
                    return;
                }

                showMinimalPopup('Berhasil', res.message, 'success');
                closeTambahModal();

                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } catch (error) {
                console.error(error);
                showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
                showLoading(false);
            }
        });
    }

    // UPDATE (PUT/POST)
    function initializeUpdateForm() {
        const editKaryawanForm = document.getElementById('editKaryawanForm');
        if (!editKaryawanForm) return;

        editKaryawanForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                showMinimalPopup('Validasi Gagal', 'Harap periksa kembali form yang diisi', 'warning');
                return;
            }

            // Validasi permission gaji
            if (!validateGajiPermissions(this, true)) {
                return;
            }

            const submitBtn = editKaryawanForm.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent || 'Update';

            if (submitBtn) {
                submitBtn.textContent = 'Memperbarui...';
                submitBtn.disabled = true;
            }
            showLoading(true);

            const formData = new FormData(editKaryawanForm);
            const id = document.getElementById("editId").value;

            console.log('Submitting update for karyawan ID:', id);
            
            if (!id) {
                showMinimalPopup('Error', 'ID karyawan tidak ditemukan di form', 'error');
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
                showLoading(false);
                return;
            }

            // Gunakan URL yang benar dengan ID karyawan
            const updateUrl = `/admin/karyawan/update/${id}`;
            console.log('Sending update request to:', updateUrl);

            try {
                // Coba dengan method PUT terlebih dahulu
                let response = await fetch(updateUrl, {
                    method: "PUT",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        "Accept": "application/json"
                    },
                    body: formData
                });

                // Jika PUT gagal (misalnya 405), coba dengan POST
                if (response.status === 405) {
                    console.log('PUT method not allowed, trying POST...');
                    response = await fetch(updateUrl, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": getCsrfToken(),
                            "Accept": "application/json",
                            "X-HTTP-Method-Override": "PUT"
                        },
                        body: formData
                    });
                }

                const data = await response.json();

                if (data && data.success) {
                    showMinimalPopup('Berhasil', data.message || 'Data karyawan berhasil diperbarui', 'success');
                    closeEditModal();

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    const errorMessage = data && data.message ? data.message :
                        data && data.errors ? Object.values(data.errors)[0] :
                        'Terjadi kesalahan saat memperbarui data';
                    showMinimalPopup('Error', errorMessage, 'error');
                }
            } catch (error) {
                console.error('Edit error:', error);
                showMinimalPopup('Error', error.message || 'Terjadi kesalahan saat memperbarui data', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
                showLoading(false);
            }
        });
    }

    // DELETE HANDLER
    async function handleDeleteKaryawan(id) {
        if (!id) {
            showMinimalPopup('Error', 'ID karyawan tidak ditemukan', 'error');
            return;
        }

        showLoading(true);

        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const originalText = confirmDeleteBtn?.textContent || 'Hapus';

        if (confirmDeleteBtn) {
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.textContent = 'Menghapus...';
        }

        try {
            const response = await fetch(`/admin/karyawan/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showMinimalPopup('Berhasil', data.message, 'success');

                // Hapus row dari table
                const row = document.querySelector(`.karyawan-row[data-id="${id}"]`);
                const card = document.querySelector(`.karyawan-card[data-id="${id}"]`);

                if (row) row.remove();
                if (card) card.remove();

                // Update total count
                const totalCountElement = document.getElementById('totalCount');
                if (totalCountElement) {
                    const currentCount = parseInt(totalCountElement.textContent) || 0;
                    totalCountElement.textContent = Math.max(0, currentCount - 1);
                }

                // Re-query karyawanRows dan karyawanCards dari DOM terbaru
                karyawanRows = document.querySelectorAll('.karyawan-row');
                karyawanCards = document.querySelectorAll('.karyawan-card');

                // Reset pagination jika halaman saat ini kosong
                const visibleRows = getFilteredRows();
                const totalPages = Math.ceil(visibleRows.length / itemsPerPage);
                if (currentPage > totalPages && totalPages > 0) {
                    currentPage = totalPages;
                } else if (totalPages === 0) {
                    currentPage = 1;
                }

                // Reload pagination dan tampilkan data
                initializePagination();
                updateVisibleItems();

                closeDeleteModal();
            } else {
                showMinimalPopup('Error', data.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan saat menghapus data', 'error');
        } finally {
            if (confirmDeleteBtn) {
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.textContent = originalText;
            }
            showLoading(false);
        }
    }

    // === EVENT LISTENERS ===
    function initializeEventListeners() {
        // Button event listeners
        const tambahKaryawanBtn = document.getElementById('tambahKaryawanBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const closeEditModalBtn = document.getElementById('closeEditModalBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const closeDeleteModalBtn = document.getElementById('closeDeleteModalBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const pilihFotoBtn = document.getElementById('pilihFotoBtn');
        const fotoInput = document.getElementById('fotoInput');
        const pilihEditFotoBtn = document.getElementById('pilihEditFotoBtn');
        const editFotoInput = document.getElementById('editFotoInput');
        const popupCloseBtn = document.querySelector('.minimal-popup-close');

        // Modal buttons
        if (tambahKaryawanBtn) {
            tambahKaryawanBtn.addEventListener('click', openTambahModal);
        }
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeTambahModal);
        }
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeTambahModal);
        }
        if (closeEditModalBtn) {
            closeEditModalBtn.addEventListener('click', closeEditModal);
        }
        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', closeEditModal);
        }
        if (closeDeleteModalBtn) {
            closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
        }
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        }

        // Delete confirmation
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                const id = document.getElementById('deleteId').value;
                if (id) {
                    handleDeleteKaryawan(id);
                }
            });
        }

        // Foto buttons
        if (pilihFotoBtn) {
            pilihFotoBtn.addEventListener('click', () => {
                if (fotoInput) fotoInput.click();
            });
        }
        if (pilihEditFotoBtn) {
            pilihEditFotoBtn.addEventListener('click', () => {
                if (editFotoInput) editFotoInput.click();
            });
        }

        // Foto preview
        if (fotoInput) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const fotoPreview = document.getElementById('fotoPreview');
                if (file && fotoPreview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        fotoPreview.innerHTML =
                            `<img src="${e.target.result}" alt="Preview" class="h-16 w-16 rounded-full object-cover">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (editFotoInput) {
            editFotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const editFotoPreview = document.getElementById('editFotoPreview');
                if (file && editFotoPreview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editFotoPreview.innerHTML =
                            `<img src="${e.target.result}" alt="Preview" class="h-16 w-16 rounded-full object-cover">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Edit buttons (delegated event handling)
        document.addEventListener('click', function(e) {
            // Edit button
            if (e.target.closest('.edit-btn')) {
                const button = e.target.closest('.edit-btn');

                let data;

                if (button.hasAttribute('data-karyawan')) {
                    // Untuk mobile cards
                    try {
                        data = JSON.parse(button.getAttribute('data-karyawan'));
                    } catch (error) {
                        console.error('Error parsing karyawan data:', error);
                        showMinimalPopup('Error', 'Data karyawan tidak valid', 'error');
                        return;
                    }
                } else {
                    // Untuk desktop table - ambil dari parent row
                    const row = button.closest('tr');
                    if (!row) return;

                    data = {
                        id: row.getAttribute('data-id'),
                        nama: row.getAttribute('data-nama'),
                        email: row.getAttribute('data-email'),
                        role: row.getAttribute('data-role'),
                        divisi: row.getAttribute('data-divisi'),
                        alamat: row.getAttribute('data-alamat'),
                        kontak: row.getAttribute('data-kontak'),
                        foto: row.getAttribute('data-foto'),
                        gaji: row.getAttribute('data-gaji')
                    };
                }

                console.log('Edit button clicked with data:', data);
                openEditModal(data);
            }

            // Delete button
            if (e.target.closest('.delete-btn')) {
                const button = e.target.closest('.delete-btn');
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama') || 'karyawan ini';
                openDeleteModal(id, nama);
            }
        });

        // Close modal on outside click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.add('hidden');
            }
        });

        // Close modal on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });

        // Close popup
        if (popupCloseBtn) {
            popupCloseBtn.addEventListener('click', function() {
                const popup = document.getElementById('minimalPopup');
                if (popup) {
                    popup.classList.remove('show');
                }
            });
        }
    }

    // === INITIALIZE ALL ===
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Data karyawan page loaded');
        console.log('Is user finance?', isFinance);

        // Initialize pagination, filter, search
        initializePagination();
        initializeFilter();
        initializeSearch();
        initializeAutoFill();
        initializeEventListeners();
        initializeCreateForm();
        initializeUpdateForm();
    });
</script>

</body>

</html>
