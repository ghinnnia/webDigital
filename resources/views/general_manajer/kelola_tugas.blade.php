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
        body {
            font-family: 'Poppins', sans-serif;
        }

        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }

        /* Card hover effects */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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

        .status-tidak-masuk {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
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

        /* Table with fixed width to ensure scrolling */
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

        /* Shadow effect */
        .table-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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

        /* Icon styling */
        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
        }

        /* Filter styles */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }

        /* Form input styles */
        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            background-color: white;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        /* Pagination styles */
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

        /* Mobile pagination styles */
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
    @include('general_manajer/templet/header')

    <!-- Main Content Container -->
    <div class="main-content">
        <main class="flex-1 flex flex-col bg-background-light">
            <div class="flex-1 p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Rekap Absensi</h2>

                <!-- Filter Section -->
                <div class="panel mb-6 md:mb-8">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">filter_list</span>
                            Filter Data
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="filter-container">
                            <div class="filter-item">
                                <label class="filter-label" for="startDate">Tanggal Mulai</label>
                                <input type="date" id="startDate" class="form-input" value="{{ $tanggalMulai ?? date('Y-m-01') }}">
                            </div>
                            <div class="filter-item">
                                <label class="filter-label" for="endDate">Tanggal Akhir</label>
                                <input type="date" id="endDate" class="form-input" value="{{ $tanggalAkhir ?? date('Y-m-d') }}">
                            </div>
                            <div class="filter-item">
                                <label class="filter-label" for="divisionFilter">Divisi</label>
                                <select id="divisionFilter" class="form-input">
                                    <option value="">Semua Divisi</option>
                                    @foreach($divisions ?? [] as $division)
                                        <option value="{{ $division }}" {{ $divisiFilter === $division ? 'selected' : '' }}>{{ $division }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-item flex-end">
                                <label class="filter-label">&nbsp;</label>
                                <button id="applyFilterBtn" class="btn-primary px-4 py-2 rounded-md text-white">
                                    <span class="material-icons-outlined text-sm align-middle mr-1">search</span>
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards - Hanya 3 kartu: Cuti, Dinas Luar, Sakit -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
                    <!-- Cuti Card -->
                    <div class="card bg-white p-4 md:p-6 rounded-xl shadow-md">
                        <div class="flex items-center">
                            <div class="icon-container bg-yellow-100 mr-3 md:mr-4">
                                <span class="material-icons-outlined text-yellow-600 text-lg md:text-xl">event_busy</span>
                            </div>
                            <div>
                                <p class="text-xs md:text-sm text-gray-500">Cuti</p>
                                <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['total_cuti'] ?? 0 }}</p>
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
                                <p class="text-xl md:text-2xl font-bold text-purple-600">{{ $stats['total_dinas_luar'] ?? 0 }}</p>
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
                                <p class="text-xl md:text-2xl font-bold text-orange-600">{{ $stats['total_sakit'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="tab-nav">
                    <button id="attendanceTab" class="tab-button active" onclick="switchTab('attendance')">
                        <span class="material-icons-outlined align-middle mr-2">fact_check</span>
                        Data Absensi
                    </button>
                    <button id="absenceTab" class="tab-button" onclick="switchTab('absence')">
                        <span class="material-icons-outlined align-middle mr-2">assignment_late</span>
                        Daftar Ketidakhadiran
                    </button>
                </div>

                <!-- Data Absensi Panel -->
                <div id="attendancePanel" class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">fact_check</span>
                            Data Absensi
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span class="font-semibold text-text-light" id="attendanceCount">0</span> data</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- SCROLLABLE TABLE -->
                        <div class="scrollable-table-container table-shadow" id="scrollableTableAttendance">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 60px;">No</th>
                                        <th style="min-width: 200px;">Nama</th>
                                        <th style="min-width: 120px;">Tanggal</th>
                                        <th style="min-width: 120px;">Jam Masuk</th>
                                        <th style="min-width: 120px;">Jam Keluar</th>
                                        <th style="min-width: 120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceTableBody">
                                    <!-- Data rows will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Desktop Pagination -->
                        <div id="attendancePaginationContainer" class="desktop-pagination">
                            <button id="attendancePrevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="attendancePageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="attendanceNextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>

                        <!-- Mobile Pagination -->
                        <div class="mobile-pagination">
                            <button id="attendancePrevPageMobile" class="mobile-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="attendancePageNumbersMobile" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="attendanceNextPageMobile" class="mobile-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Daftar Ketidakhadiran Panel (Initially Hidden) -->
                <div id="absencePanel" class="panel hidden">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">assignment_late</span>
                            Daftar Ketidakhadiran
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span class="font-semibold text-text-light" id="absenceCount">0</span> data</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- SCROLLABLE TABLE -->
                        <div class="scrollable-table-container table-shadow" id="scrollableTableAbsence">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 60px;">No</th>
                                        <th style="min-width: 200px;">Nama</th>
                                        <th style="min-width: 120px;">Tanggal Mulai</th>
                                        <th style="min-width: 120px;">Tanggal Akhir</th>
                                        <th style="min-width: 200px;">Alasan</th>
                                        <th style="min-width: 120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="absenceTableBody">
                                    <!-- Data rows will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Desktop Pagination -->
                        <div id="absencePaginationContainer" class="desktop-pagination">
                            <button id="absencePrevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="absencePageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="absenceNextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>

                        <!-- Mobile Pagination -->
                        <div class="mobile-pagination">
                            <button id="absencePrevPageMobile" class="mobile-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="absencePageNumbersMobile" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="absenceNextPageMobile" class="mobile-nav-btn">
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

    <script>
        // Data akan diambil dari API
        let attendanceData = @json($attendances ?? []);
        let absenceData = @json($ketidakhadiran ?? []);

        // Pagination variables for attendance
        const attendanceItemsPerPage = 10;
        let attendanceCurrentPage = 1;
        let attendanceTotalPages = Math.ceil(attendanceData.length / attendanceItemsPerPage);

        // Pagination variables for absence
        const absenceItemsPerPage = 10;
        let absenceCurrentPage = 1;
        let absenceTotalPages = Math.ceil(absenceData.length / absenceItemsPerPage);

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize pagination
            initializeAttendancePagination();
            initializeAbsencePagination();

            // Render first page
            renderAttendanceTable(1);
            renderAbsenceTable(1);

            // Update counts
            document.getElementById('attendanceCount').textContent = attendanceData.length;
            document.getElementById('absenceCount').textContent = absenceData.length;

            // Event listener untuk tombol filter
            document.getElementById('applyFilterBtn').addEventListener('click', applyFilters);
        });

        // Function to switch between tabs
        function switchTab(tabName) {
            // Get tab buttons and panels
            const attendanceTab = document.getElementById('attendanceTab');
            const absenceTab = document.getElementById('absenceTab');
            const attendancePanel = document.getElementById('attendancePanel');
            const absencePanel = document.getElementById('absencePanel');

            // Hide all panels and remove active class from all tabs
            attendancePanel.classList.add('hidden');
            absencePanel.classList.add('hidden');
            attendanceTab.classList.remove('active');
            absenceTab.classList.remove('active');

            // Show selected panel and add active class to clicked tab
            if (tabName === 'attendance') {
                attendancePanel.classList.remove('hidden');
                attendanceTab.classList.add('active');
            } else if (tabName === 'absence') {
                absencePanel.classList.remove('hidden');
                absenceTab.classList.add('active');
            }
        }

        // Function to apply filters
        function applyFilters() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const division = document.getElementById('divisionFilter').value;

            // Build query string
            const params = new URLSearchParams();
            if (startDate) params.append('tanggal_mulai', startDate);
            if (endDate) params.append('tanggal_akhir', endDate);
            if (division) params.append('divisi', division);

            // Reload page with filters
            window.location.href = `?${params.toString()}`;
        }

        // Initialize pagination for attendance
        function initializeAttendancePagination() {
            initAttendanceDesktopPagination();
            initAttendanceMobilePagination();
        }

        // Initialize pagination for absence
        function initializeAbsencePagination() {
            initAbsenceDesktopPagination();
            initAbsenceMobilePagination();
        }

        // Desktop pagination for attendance
        function initAttendanceDesktopPagination() {
            const pageNumbersContainer = document.getElementById('attendancePageNumbers');
            const prevButton = document.getElementById('attendancePrevPage');
            const nextButton = document.getElementById('attendanceNextPage');

            // Clear existing page numbers
            pageNumbersContainer.innerHTML = '';

            // Generate page numbers
            for (let i = 1; i <= attendanceTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `desktop-page-btn ${i === attendanceCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToAttendanceDesktopPage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            // Event listeners for navigation buttons
            prevButton.addEventListener('click', () => {
                if (attendanceCurrentPage > 1) goToAttendanceDesktopPage(attendanceCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (attendanceCurrentPage < attendanceTotalPages) goToAttendanceDesktopPage(attendanceCurrentPage + 1);
            });
        }

        // Mobile pagination for attendance
        function initAttendanceMobilePagination() {
            const pageNumbersContainer = document.getElementById('attendancePageNumbersMobile');
            const prevButton = document.getElementById('attendancePrevPageMobile');
            const nextButton = document.getElementById('attendanceNextPageMobile');

            // Clear existing page numbers
            pageNumbersContainer.innerHTML = '';

            // Generate page numbers
            for (let i = 1; i <= attendanceTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `mobile-page-btn ${i === attendanceCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToAttendanceMobilePage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            // Event listeners for navigation buttons
            prevButton.addEventListener('click', () => {
                if (attendanceCurrentPage > 1) goToAttendanceMobilePage(attendanceCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (attendanceCurrentPage < attendanceTotalPages) goToAttendanceMobilePage(attendanceCurrentPage + 1);
            });
        }

        // Desktop pagination for absence
        function initAbsenceDesktopPagination() {
            const pageNumbersContainer = document.getElementById('absencePageNumbers');
            const prevButton = document.getElementById('absencePrevPage');
            const nextButton = document.getElementById('absenceNextPage');

            // Clear existing page numbers
            pageNumbersContainer.innerHTML = '';

            // Generate page numbers
            for (let i = 1; i <= absenceTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `desktop-page-btn ${i === absenceCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToAbsenceDesktopPage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            // Event listeners for navigation buttons
            prevButton.addEventListener('click', () => {
                if (absenceCurrentPage > 1) goToAbsenceDesktopPage(absenceCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (absenceCurrentPage < absenceTotalPages) goToAbsenceDesktopPage(absenceCurrentPage + 1);
            });
        }

        // Mobile pagination for absence
        function initAbsenceMobilePagination() {
            const pageNumbersContainer = document.getElementById('absencePageNumbersMobile');
            const prevButton = document.getElementById('absencePrevPageMobile');
            const nextButton = document.getElementById('absenceNextPageMobile');

            // Clear existing page numbers
            pageNumbersContainer.innerHTML = '';

            // Generate page numbers
            for (let i = 1; i <= absenceTotalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `mobile-page-btn ${i === absenceCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToAbsenceMobilePage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }

            // Event listeners for navigation buttons
            prevButton.addEventListener('click', () => {
                if (absenceCurrentPage > 1) goToAbsenceMobilePage(absenceCurrentPage - 1);
            });

            nextButton.addEventListener('click', () => {
                if (absenceCurrentPage < absenceTotalPages) goToAbsenceMobilePage(absenceCurrentPage + 1);
            });
        }

        // Go to specific desktop page for attendance
        function goToAttendanceDesktopPage(page) {
            attendanceCurrentPage = page;
            renderAttendanceTable(page);
            updateAttendanceDesktopPaginationButtons();
            updateAttendanceMobilePaginationButtons();
        }

        // Go to specific mobile page for attendance
        function goToAttendanceMobilePage(page) {
            attendanceCurrentPage = page;
            renderAttendanceTable(page);
            updateAttendanceDesktopPaginationButtons();
            updateAttendanceMobilePaginationButtons();
        }

        // Go to specific desktop page for absence
        function goToAbsenceDesktopPage(page) {
            absenceCurrentPage = page;
            renderAbsenceTable(page);
            updateAbsenceDesktopPaginationButtons();
            updateAbsenceMobilePaginationButtons();
        }

        // Go to specific mobile page for absence
        function goToAbsenceMobilePage(page) {
            absenceCurrentPage = page;
            renderAbsenceTable(page);
            updateAbsenceDesktopPaginationButtons();
            updateAbsenceMobilePaginationButtons();
        }

        // Render table for attendance
        function renderAttendanceTable(page) {
            const tbody = document.getElementById('attendanceTableBody');
            tbody.innerHTML = '';

            const startIndex = (page - 1) * attendanceItemsPerPage;
            const endIndex = Math.min(startIndex + attendanceItemsPerPage, attendanceData.length);

            for (let i = startIndex; i < endIndex; i++) {
                const attendance = attendanceData[i];
                const row = document.createElement('tr');

                // Determine status class
                let statusClass = '';
                if (attendance.jam_masuk) {
                    const jamMasuk = new Date(`1970-01-01T${attendance.jam_masuk}`);
                    const jamBatas = new Date(`1970-01-01T09:05:00`);
                    
                    if (jamMasuk > jamBatas) {
                        statusClass = 'status-terlambat';
                    } else {
                        statusClass = 'status-hadir';
                    }
                }

                row.innerHTML = `
                    <td style="min-width: 60px;">${i + 1}</td>
                    <td style="min-width: 200px;">${attendance.user ? attendance.user.name : '-'}</td>
                    <td style="min-width: 120px;">${new Date(attendance.tanggal).toLocaleDateString('id-ID')}</td>
                    <td style="min-width: 120px;">${attendance.jam_masuk ? attendance.jam_masuk.substring(0, 5) : '-'}</td>
                    <td style="min-width: 120px;">${attendance.jam_pulang ? attendance.jam_pulang.substring(0, 5) : '-'}</td>
                    <td style="min-width: 120px;"><span class="status-badge ${statusClass}">${statusClass ? 'Terlambat' : 'Hadir'}</span></td>
                `;
                tbody.appendChild(row);
            }
        }

        // Render table for absence
        function renderAbsenceTable(page) {
            const tbody = document.getElementById('absenceTableBody');
            tbody.innerHTML = '';

            const startIndex = (page - 1) * absenceItemsPerPage;
            const endIndex = Math.min(startIndex + absenceItemsPerPage, absenceData.length);

            for (let i = startIndex; i < endIndex; i++) {
                const absence = absenceData[i];
                const row = document.createElement('tr');

                // Format tanggal
                const tanggalMulai = new Date(absence.tanggal).toLocaleDateString('id-ID');
                const tanggalAkhir = absence.tanggal_akhir 
                    ? new Date(absence.tanggal_akhir).toLocaleDateString('id-ID')
                    : tanggalMulai;

                // Determine alasan
                let alasan = '-';
                if (absence.jenis_ketidakhadiran === 'Cuti') {
                    alasan = absence.keterangan || 'Cuti';
                } else if (absence.jenis_ketidakhadiran === 'Sakit') {
                    alasan = 'Sakit';
                } else if (absence.jenis_ketidakhadiran === 'Izin') {
                    alasan = absence.reason || 'Izin';
                }

                // Determine status class
                let statusClass = '';
                if (absence.jenis_ketidakhadiran === 'Izin') {
                    statusClass = 'status-izin';
                } else if (absence.jenis_ketidakhadiran === 'Cuti') {
                    statusClass = 'status-cuti';
                } else if (absence.jenis_ketidakhadiran === 'Sakit') {
                    statusClass = 'status-sakit';
                }

                row.innerHTML = `
                    <td style="min-width: 60px;">${i + 1}</td>
                    <td style="min-width: 200px;">${absence.user ? absence.user.name : '-'}</td>
                    <td style="min-width: 120px;">${tanggalMulai}</td>
                    <td style="min-width: 120px;">${tanggalAkhir}</td>
                    <td style="min-width: 200px;">${alasan}</td>
                    <td style="min-width: 120px;"><span class="status-badge ${statusClass}">${absence.jenis_ketidakhadiran}</span></td>
                `;
                tbody.appendChild(row);
            }
        }

        // Update desktop pagination buttons for attendance
        function updateAttendanceDesktopPaginationButtons() {
            const prevButton = document.getElementById('attendancePrevPage');
            const nextButton = document.getElementById('attendanceNextPage');
            const pageButtons = document.querySelectorAll('#attendancePageNumbers button');

            prevButton.disabled = attendanceCurrentPage === 1;
            nextButton.disabled = attendanceCurrentPage === attendanceTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === attendanceCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // Update mobile pagination buttons for attendance
        function updateAttendanceMobilePaginationButtons() {
            const prevButton = document.getElementById('attendancePrevPageMobile');
            const nextButton = document.getElementById('attendanceNextPageMobile');
            const pageButtons = document.querySelectorAll('#attendancePageNumbersMobile button');

            prevButton.disabled = attendanceCurrentPage === 1;
            nextButton.disabled = attendanceCurrentPage === attendanceTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === attendanceCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // Update desktop pagination buttons for absence
        function updateAbsenceDesktopPaginationButtons() {
            const prevButton = document.getElementById('absencePrevPage');
            const nextButton = document.getElementById('absenceNextPage');
            const pageButtons = document.querySelectorAll('#absencePageNumbers button');

            prevButton.disabled = absenceCurrentPage === 1;
            nextButton.disabled = absenceCurrentPage === absenceTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === absenceCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // Update mobile pagination buttons for absence
        function updateAbsenceMobilePaginationButtons() {
            const prevButton = document.getElementById('absencePrevPageMobile');
            const nextButton = document.getElementById('absenceNextPageMobile');
            const pageButtons = document.querySelectorAll('#absencePageNumbersMobile button');

            prevButton.disabled = absenceCurrentPage === 1;
            nextButton.disabled = absenceCurrentPage === absenceTotalPages;

            pageButtons.forEach((btn, index) => {
                if (index + 1 === absenceCurrentPage) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>