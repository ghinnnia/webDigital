<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Rekap Absensi - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
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
        /* ... (semua style CSS kamu tetap sama, tidak perlu diubah) ... */
        body {
            font-family: 'Poppins', sans-serif;
        }

        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .order-table {
            transition: all 0.2s ease;
        }

        .order-table tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

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

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-hadir {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-terlambat {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .status-izin {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-cuti {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        .status-sakit {
            background-color: rgba(251, 146, 60, 0.15);
            color: #9a3412;
        }

        .status-dinas {
            background-color: rgba(139, 92, 246, 0.15);
            color: #5b21b6;
        }

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
        }

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

        .sidebar-fixed {
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
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

        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

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

        .desktop-pagination {
            display: none;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
        }

        @media (min-width: 768px) {
            .desktop-pagination {
                display: flex;
            }
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

        .table-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

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

        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
        }

        .mobile-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
        }

        @media (min-width: 768px) {
            .mobile-pagination {
                display: none;
            }
        }

        .mobile-page-btn {
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

        .mobile-page-btn.active {
            background-color: #3b82f6;
            color: white;
        }

        .mobile-page-btn:not(.active) {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .mobile-page-btn:not(.active):hover {
            background-color: #e2e8f0;
        }

        .mobile-nav-btn {
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

        .mobile-nav-btn:hover:not(:disabled) {
            background-color: #e2e8f0;
        }

        .mobile-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
     @include('pemilik/template/header') 
    <!-- Asumsikan kamu punya file header, kalau tidak, kamu bisa buat sederhana -->
    <header class="bg-white shadow-sm border-b border-border-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-text-light">Dashboard Pemilik</h1>
                </div>
                <!-- ... navigasi user lainnya ... -->
            </div>
        </div>
    </header>

    <main class="flex-1 flex flex-col bg-background-light">
        <div class="flex-1 p-3 sm:p-8">

            <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Rekap Absensi</h2>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
                <!-- Total Kehadiran Card -->
                <div class="card bg-white p-4 md:p-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="icon-container bg-green-100 mr-3 md:mr-4">
                            <span class="material-icons-outlined text-green-600 text-lg md:text-xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Total Kehadiran</p>
                            <p class="text-xl md:text-2xl font-bold text-green-600">
                                {{ $stats['total_tepat_waktu'] + $stats['total_terlambat'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tidak Hadir Card -->
                <div class="card bg-white p-4 md:p-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="icon-container bg-red-100 mr-3 md:mr-4">
                            <span class="material-icons-outlined text-red-600 text-lg md:text-xl">cancel</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Tidak Hadir</p>
                            <p class="text-xl md:text-2xl font-bold text-red-600">{{ $stats['total_tidak_masuk'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Izin Card -->
                <div class="card bg-white p-4 md:p-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="icon-container bg-blue-100 mr-3 md:mr-4">
                            <span class="material-icons-outlined text-blue-600 text-lg md:text-xl">error</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Izin</p>
                            <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $stats['total_izin'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cuti Card -->
                <div class="card bg-white p-4 md:p-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="icon-container bg-yellow-100 mr-3 md:mr-4">
                            <span class="material-icons-outlined text-yellow-600 text-lg md:text-xl">event_busy</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Cuti</p>
                            <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['total_cuti'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Dinas Luar Card -->
                <div class="card bg-white p-4 md:p-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="icon-container bg-purple-100 mr-3 md:mr-4">
                            <span class="material-icons-outlined text-purple-600 text-lg md:text-xl">directions_car</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Dinas Luar</p>
                            <p class="text-xl md:text-2xl font-bold text-purple-600">
                                {{ $stats['total_dinas_luar'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sakit Card -->
                <div class="card bg-white p-4 md:p-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="icon-container bg-orange-100 mr-3 md:mr-4">
                            <span class="material-icons-outlined text-orange-600 text-lg md:text-xl">healing</span>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Sakit</p>
                            <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $stats['total_sakit'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-md mb-6 md:mb-8">
                <form method="GET" action="{{ route('owner.rekap_absen') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="date" value="{{ old('date', $startDate ?? now()->format('Y-m-d')) }}" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <!-- Tanggal Akhir -->
                    <div>
                        <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ old('tanggal_akhir', $endDate ?? now()->format('Y-m-d')) }}" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" disabled>
                    </div>

                    <!-- Divisi -->
                    <div>
                        <label for="divisi" class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                        <select id="divisi" name="divisi" class="w-full px-3 py-2 bg-white border border-border-light text-text-muted-light rounded-lg form-input focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Semua Divisi</option>
                            @php
                                $divisionsDropdown = \App\Models\Divisi::orderBy('divisi')->pluck('divisi');
                            @endphp
                            @foreach($divisionsDropdown as $div)
                                <option value="{{ $div }}" {{ old('divisi', $selectedDivision ?? '') == $div ? 'selected' : '' }}>
                                    {{ $div }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary flex-1 px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                            <span class="material-icons-outlined align-middle mr-1 text-sm">filter_list</span>
                            Filter
                        </button>
                        <a href="{{ route('owner.rekap_absen') }}" class="btn-secondary flex-1 text-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                            <span class="material-icons-outlined align-middle mr-1 text-sm">refresh</span>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-nav">
                <button id="absensiTab" class="tab-button active" onclick="switchTab('absensi')">
                    <span class="material-icons-outlined align-middle mr-2">fact_check</span>
                    Data Absensi
                </button>
                <button id="ketidakhadiranTab" class="tab-button" onclick="switchTab('ketidakhadiran')">
                    <span class="material-icons-outlined align-middle mr-2">assignment_late</span>
                    Daftar Ketidakhadiran
                </button>
            </div>

            <!-- Data Absensi Panel -->
            <div id="absensiPanel" class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <span class="material-icons-outlined text-primary">fact_check</span>
                        Data Absensi
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-text-muted-light">Total: <span class="font-semibold text-text-light" id="absensiCount">{{ $attendances->count() }}</span> data</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="scrollable-table-container table-shadow" id="scrollableTable">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 60px;">No</th>
                                    <th style="min-width: 200px;">Nama</th>
                                    <th style="min-width: 150px;">Divisi</th>
                                    <th style="min-width: 120px;">Tanggal</th>
                                    <th style="min-width: 120px;">Jam Masuk</th>
                                    <th style="min-width: 120px;">Jam Keluar</th>
                                    <th style="min-width: 150px;">Keterangan</th>
                                    <th style="min-width: 120px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="absensiTableBody">
                                <!-- Data rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Desktop Pagination -->
                    <div id="absensiPaginationContainer" class="desktop-pagination">
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

                    <!-- Mobile Pagination -->
                    <div class="mobile-pagination">
                        <button id="absensiPrevPageMobile" class="mobile-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_left</span>
                        </button>
                        <div id="absensiPageNumbersMobile" class="flex gap-1">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="absensiNextPageMobile" class="mobile-nav-btn">
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
                        <span class="text-sm text-text-muted-light">Total: <span class="font-semibold text-text-light" id="ketidakhadiranCount">{{ $ketidakhadiran->count() }}</span> data</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="scrollable-table-container table-shadow" id="scrollableTableKetidakhadiran">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 60px;">No</th>
                                    <th style="min-width: 200px;">Nama</th>
                                    <th style="min-width: 150px;">Divisi</th>
                                    <th style="min-width: 120px;">Tanggal Mulai</th>
                                    <th style="min-width: 120px;">Tanggal Akhir</th>
                                    <th style="min-width: 200px;">Alasan</th>
                                    <th style="min-width: 120px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="ketidakhadiranTableBody">
                                <!-- Data rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Desktop Pagination -->
                    <div id="ketidakhadiranPaginationContainer" class="desktop-pagination">
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

                    <!-- Mobile Pagination -->
                    <div class="mobile-pagination">
                        <button id="ketidakhadiranPrevPageMobile" class="mobile-nav-btn">
                            <span class="material-icons-outlined text-sm">chevron_left</span>
                        </button>
                        <div id="ketidakhadiranPageNumbersMobile" class="flex gap-1">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="ketidakhadiranNextPageMobile" class="mobile-nav-btn">
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

    <script>
        // Data dari controller
        const absensiData = @json(
            $formattedAbsensi instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $formattedAbsensi->items()
                : $formattedAbsensi
        );
        const ketidakhadiranData = @json($ketidakhadiran);

        // Debug logging
        console.log('Absensi Data:', absensiData);
        console.log('Ketidakhadiran Data:', ketidakhadiranData);
        console.log('Absensi Count:', absensiData.length);
        console.log('Ketidakhadiran Count:', ketidakhadiranData.length);

        // Pagination variables for absensi
        const absensiItemsPerPage = 10;
        let absensiCurrentPage = 1;
        const absensiTotalPages = Math.ceil(absensiData.length / absensiItemsPerPage);

        // Pagination variables for ketidakhadiran
        const ketidakhadiranItemsPerPage = 10;
        let ketidakhadiranCurrentPage = 1;
        const ketidakhadiranTotalPages = Math.ceil(ketidakhadiranData.length / ketidakhadiranItemsPerPage);

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM Content Loaded - Initializing pagination...');
            initializeAbsensiPagination();
            initializeKetidakhadiranPagination();
        });

        // Function to switch between tabs
        function switchTab(tabName) {
            const absensiTab = document.getElementById('absensiTab');
            const ketidakhadiranTab = document.getElementById('ketidakhadiranTab');
            const absensiPanel = document.getElementById('absensiPanel');
            const ketidakhadiranPanel = document.getElementById('ketidakhadiranPanel');

            absensiPanel.classList.add('hidden');
            ketidakhadiranPanel.classList.add('hidden');
            absensiTab.classList.remove('active');
            ketidakhadiranTab.classList.remove('active');

            if (tabName === 'absensi') {
                absensiPanel.classList.remove('hidden');
                absensiTab.classList.add('active');
            } else if (tabName === 'ketidakhadiran') {
                ketidakhadiranPanel.classList.remove('hidden');
                ketidakhadiranTab.classList.add('active');
            }
        }

        // Initialize pagination for absensi
        function initializeAbsensiPagination() {
            document.getElementById('absensiCount').textContent = absensiData.length;
            initAbsensiDesktopPagination();
            initAbsensiMobilePagination();
            renderAbsensiTable(1);
        }

        // Initialize pagination for ketidakhadiran
        function initializeKetidakhadiranPagination() {
            document.getElementById('ketidakhadiranCount').textContent = ketidakhadiranData.length;
            initKetidakhadiranDesktopPagination();
            initKetidakhadiranMobilePagination();
            renderKetidakhadiranTable(1);
        }

        // ... (semua fungsi JavaScript lainnya tetap sama, tidak perlu diubah) ...
        function initAbsensiDesktopPagination() {
            const pageNumbersContainer = document.getElementById('absensiPageNumbers');
            const prevButton = document.getElementById('absensiPrevPage');
            const nextButton = document.getElementById('absensiNextPage');

            pageNumbersContainer.innerHTML = '';

            for (let i = 1; i <= absensiTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `desktop-page-btn ${i === absensiCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToAbsensiDesktopPage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            prevButton.addEventListener('click', () => {
                if (absensiCurrentPage > 1) goToAbsensiDesktopPage(absensiCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (absensiCurrentPage < absensiTotalPages) goToAbsensiDesktopPage(absensiCurrentPage + 1);
            });
        }

        function initAbsensiMobilePagination() {
            const pageNumbersContainer = document.getElementById('absensiPageNumbersMobile');
            const prevButton = document.getElementById('absensiPrevPageMobile');
            const nextButton = document.getElementById('absensiNextPageMobile');

            pageNumbersContainer.innerHTML = '';

            for (let i = 1; i <= absensiTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `mobile-page-btn ${i === absensiCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToAbsensiMobilePage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            prevButton.addEventListener('click', () => {
                if (absensiCurrentPage > 1) goToAbsensiMobilePage(absensiCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (absensiCurrentPage < absensiTotalPages) goToAbsensiMobilePage(absensiCurrentPage + 1);
            });
        }

        function initKetidakhadiranDesktopPagination() {
            const pageNumbersContainer = document.getElementById('ketidakhadiranPageNumbers');
            const prevButton = document.getElementById('ketidakhadiranPrevPage');
            const nextButton = document.getElementById('ketidakhadiranNextPage');

            pageNumbersContainer.innerHTML = '';

            for (let i = 1; i <= ketidakhadiranTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `desktop-page-btn ${i === ketidakhadiranCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToKetidakhadiranDesktopPage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            prevButton.addEventListener('click', () => {
                if (ketidakhadiranCurrentPage > 1) goToKetidakhadiranDesktopPage(ketidakhadiranCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (ketidakhadiranCurrentPage < ketidakhadiranTotalPages) goToKetidakhadiranDesktopPage(ketidakhadiranCurrentPage + 1);
            });
        }

        function initKetidakhadiranMobilePagination() {
            const pageNumbersContainer = document.getElementById('ketidakhadiranPageNumbersMobile');
            const prevButton = document.getElementById('ketidakhadiranPrevPageMobile');
            const nextButton = document.getElementById('ketidakhadiranNextPageMobile');

            pageNumbersContainer.innerHTML = '';

            for (let i = 1; i <= ketidakhadiranTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `mobile-page-btn ${i === ketidakhadiranCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToKetidakhadiranMobilePage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            prevButton.addEventListener('click', () => {
                if (ketidakhadiranCurrentPage > 1) goToKetidakhadiranMobilePage(ketidakhadiranCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (ketidakhadiranCurrentPage < ketidakhadiranTotalPages) goToKetidakhadiranMobilePage(ketidakhadiranCurrentPage + 1);
            });
        }

        function goToAbsensiDesktopPage(page) {
            absensiCurrentPage = page;
            renderAbsensiTable(page);
            updateAbsensiDesktopPaginationButtons();
            updateAbsensiMobilePaginationButtons();
        }

        function goToAbsensiMobilePage(page) {
            absensiCurrentPage = page;
            renderAbsensiTable(page);
            updateAbsensiDesktopPaginationButtons();
            updateAbsensiMobilePaginationButtons();
        }

        function goToKetidakhadiranDesktopPage(page) {
            ketidakhadiranCurrentPage = page;
            renderKetidakhadiranTable(page);
            updateKetidakhadiranDesktopPaginationButtons();
            updateKetidakhadiranMobilePaginationButtons();
        }

        function goToKetidakhadiranMobilePage(page) {
            ketidakhadiranCurrentPage = page;
            renderKetidakhadiranTable(page);
            updateKetidakhadiranDesktopPaginationButtons();
            updateKetidakhadiranMobilePaginationButtons();
        }

        function renderAbsensiTable(page) {
            const tbody = document.getElementById('absensiTableBody');
            tbody.innerHTML = '';

            const startIndex = (page - 1) * absensiItemsPerPage;
            const endIndex = Math.min(startIndex + absensiItemsPerPage, absensiData.length);

            console.log('Rendering absensi table - Page:', page, 'Start:', startIndex, 'End:', endIndex, 'Total:', absensiData.length);

            const statusClassMap = {
                'Tepat Waktu': 'status-hadir',
                'Terlambat': 'status-terlambat',
                'Tidak Masuk': 'status-tidak-hadir',
                'Cuti': 'status-cuti',
                'Sakit': 'status-sakit',
                'Izin': 'status-izin',
                'Dinas Luar': 'status-dinas'
            };

            function resolveDivisi(user) {
                if (!user) return '-';
                if (typeof user.divisi === 'string') return user.divisi || '-';
                if (user.divisi && typeof user.divisi === 'object') return user.divisi.divisi || '-';
                if (user.division_detail && typeof user.division_detail === 'object') return user.division_detail.divisi || '-';
                return '-';
            }

            function resolveStatus(absensi) {
                if (absensi.jenis_ketidakhadiran) {
                    switch (String(absensi.jenis_ketidakhadiran).toLowerCase()) {
                        case 'cuti':
                            return 'Cuti';
                        case 'sakit':
                            return 'Sakit';
                        case 'izin':
                            return 'Izin';
                        case 'dinas-luar':
                            return 'Dinas Luar';
                        default:
                            return 'Tidak Masuk';
                    }
                }

                if (absensi.jam_masuk) {
                    const lateMinutes = Number(absensi.late_minutes ?? 0);
                    return lateMinutes > 0 ? 'Terlambat' : 'Tepat Waktu';
                }

                return 'Tidak Masuk';
            }

            if (absensiData.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data absensi</td>`;
                tbody.appendChild(row);
                console.warn('No absensi data to display');
                return;
            }

            for (let i = startIndex; i < endIndex; i++) {
                const absensi = absensiData[i];
                console.log('Processing row:', i, absensi);

                const nama = absensi.user_name || absensi.user?.name || '-';
                const divisi = absensi.divisi || resolveDivisi(absensi.user);
                const tanggal = absensi.tanggal ? new Date(absensi.tanggal).toLocaleDateString('id-ID') : '-';
                const jamMasuk = absensi.jam_masuk ? String(absensi.jam_masuk).substring(0, 5) : '-';
                const jamKeluar = absensi.jam_pulang ? String(absensi.jam_pulang).substring(0, 5) : '-';
                const menitTerlambat = Number(absensi.late_minutes ?? 0);
                
                // Tentukan keterangan berdasarkan catatan
                let keterangan = '-';
                let keteranganClass = '';
                if (absensi.keterangan) {
                    keterangan = absensi.keterangan;
                    if (String(absensi.keterangan).toLowerCase().includes('sakit')) {
                        keteranganClass = 'status-sakit';
                    } else if (String(absensi.keterangan).toLowerCase().includes('izin')) {
                        keteranganClass = 'status-izin';
                    } else if (String(absensi.keterangan).toLowerCase().includes('cuti')) {
                        keteranganClass = 'status-cuti';
                    } else if (String(absensi.keterangan).toLowerCase().includes('dinas')) {
                        keteranganClass = 'status-dinas';
                    }
                } else if (absensi.catatan) {
                    keterangan = absensi.catatan;
                    // Map keterangan ke status class
                    if (absensi.catatan.toLowerCase().includes('sakit')) {
                        keteranganClass = 'status-sakit';
                    } else if (absensi.catatan.toLowerCase().includes('izin')) {
                        keteranganClass = 'status-izin';
                    } else if (absensi.catatan.toLowerCase().includes('cuti')) {
                        keteranganClass = 'status-cuti';
                    } else if (absensi.catatan.toLowerCase().includes('dinas')) {
                        keteranganClass = 'status-dinas';
                    }
                }
                
                // Status mengikuti data riwayat (late_minutes/jenis ketidakhadiran)
                const status = absensi.status_kehadiran || resolveStatus(absensi);
                const keterlambatanHH = String(Math.floor(menitTerlambat / 60)).padStart(2, '0');
                const keterlambatanMM = String(menitTerlambat % 60).padStart(2, '0');
                const statusLabel = status === 'Terlambat' && menitTerlambat > 0
                    ? `Terlambat (${keterlambatanHH}:${keterlambatanMM} menit)`
                    : status;

                const statusClass = statusClassMap[status] || 'status-default';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="min-width: 60px;">${i + 1}</td>
                    <td style="min-width: 200px;">${nama}</td>
                    <td style="min-width: 150px;">${divisi}</td>
                    <td style="min-width: 120px;">${tanggal}</td>
                    <td style="min-width: 120px;">${jamMasuk}</td>
                    <td style="min-width: 120px;">${jamKeluar}</td>
                    <td style="min-width: 150px;">
                        ${keterangan !== '-' ? `<span class="status-badge ${keteranganClass}">${keterangan}</span>` : '<span>-</span>'}
                    </td>
                    <td style="min-width: 120px;">
                        <span class="status-badge ${statusClass}">
                            ${statusLabel}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            }
        }

        function renderKetidakhadiranTable(page) {
            const tbody = document.getElementById('ketidakhadiranTableBody');
            tbody.innerHTML = '';

            const startIndex = (page - 1) * ketidakhadiranItemsPerPage;
            const endIndex = Math.min(startIndex + ketidakhadiranItemsPerPage, ketidakhadiranData.length);

            for (let i = startIndex; i < endIndex; i++) {
                const ketidakhadiran = ketidakhadiranData[i];
                const row = document.createElement('tr');

                const tanggalMulai = new Date(ketidakhadiran.tanggal).toLocaleDateString('id-ID');
                const tanggalAkhir = ketidakhadiran.tanggal_akhir ? new Date(ketidakhadiran.tanggal_akhir).toLocaleDateString('id-ID') : tanggalMulai;

                const jenis = String(ketidakhadiran.jenis_ketidakhadiran || '').toLowerCase();
                const alasanPengajuan = ketidakhadiran.reason || ketidakhadiran.keterangan || '';
                let alasan = alasanPengajuan || '-';
                if (!alasanPengajuan) {
                    if (jenis === 'cuti') alasan = 'Cuti';
                    else if (jenis === 'sakit') alasan = 'Sakit';
                    else if (jenis === 'izin') alasan = 'Izin';
                    else if (jenis === 'dinas-luar') alasan = 'Dinas Luar';
                }

                let statusClass = '';
                if (jenis === 'izin') {
                    statusClass = 'status-izin';
                } else if (jenis === 'cuti') {
                    statusClass = 'status-cuti';
                } else if (jenis === 'sakit') {
                    statusClass = 'status-sakit';
                }

                const jenisLabelMap = {
                    'izin': 'Izin',
                    'cuti': 'Cuti',
                    'sakit': 'Sakit',
                    'dinas-luar': 'Dinas Luar',
                };
                let statusBadge = `<span class="status-badge ${statusClass}">${jenisLabelMap[jenis] || ketidakhadiran.jenis_ketidakhadiran}</span>`;
                if (ketidakhadiran.approval_status === 'pending') {
                    statusBadge += ` <span class="status-badge" style="background-color: rgba(245, 158, 11, 0.15); color: #92400e;">Menunggu Persetujuan</span>`;
                }

                row.innerHTML = `
                    <td style="min-width: 60px;">${i + 1}</td>
                    <td style="min-width: 200px;">${ketidakhadiran.user ? ketidakhadiran.user.name : ketidakhadiran.name}</td>
                    <td style="min-width: 150px;">${ketidakhadiran.user ? (typeof ketidakhadiran.user.divisi === 'string' ? ketidakhadiran.user.divisi : (ketidakhadiran.user.divisi?.divisi || '-')) : '-'}</td>
                    <td style="min-width: 120px;">${tanggalMulai}</td>
                    <td style="min-width: 120px;">${tanggalAkhir}</td>
                    <td style="min-width: 200px;">${alasan}</td>
                    <td style="min-width: 120px;">${statusBadge}</td>
                `;
                tbody.appendChild(row);
            }
        }

        function updateAbsensiDesktopPaginationButtons() {
            const prevButton = document.getElementById('absensiPrevPage');
            const nextButton = document.getElementById('absensiNextPage');
            const pageButtons = document.querySelectorAll('#absensiPageNumbers button');

            prevButton.disabled = absensiCurrentPage === 1;
            nextButton.disabled = absensiCurrentPage === absensiTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === absensiCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function updateAbsensiMobilePaginationButtons() {
            const prevButton = document.getElementById('absensiPrevPageMobile');
            const nextButton = document.getElementById('absensiNextPageMobile');
            const pageButtons = document.querySelectorAll('#absensiPageNumbersMobile button');

            prevButton.disabled = absensiCurrentPage === 1;
            nextButton.disabled = absensiCurrentPage === absensiTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === absensiCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function updateKetidakhadiranDesktopPaginationButtons() {
            const prevButton = document.getElementById('ketidakhadiranPrevPage');
            const nextButton = document.getElementById('ketidakhadiranNextPage');
            const pageButtons = document.querySelectorAll('#ketidakhadiranPageNumbers button');

            prevButton.disabled = ketidakhadiranCurrentPage === 1;
            nextButton.disabled = ketidakhadiranCurrentPage === ketidakhadiranTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === ketidakhadiranCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function updateKetidakhadiranMobilePaginationButtons() {
            const prevButton = document.getElementById('ketidakhadiranPrevPageMobile');
            const nextButton = document.getElementById('ketidakhadiranNextPageMobile');
            const pageButtons = document.querySelectorAll('#ketidakhadiranPageNumbersMobile button');

            prevButton.disabled = ketidakhadiranCurrentPage === 1;
            nextButton.disabled = ketidakhadiranCurrentPage === ketidakhadiranTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === ketidakhadiranCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
    </script>
</body>

</html>
