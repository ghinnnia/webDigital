<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Karyawan | Project Management</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
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
                        body: ["Poppins", "sans-serif"],
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

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

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
        /* Default untuk mobile: di sebelah kanan */
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

        /* Override untuk desktop: di sebelah kiri */
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
                /* Lebar sidebar */
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

            /* Hide desktop pagination on mobile */
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

            /* Hide mobile pagination on desktop */
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

        /* Force scrollbar to be visible */
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
            min-width: 1300px;
            /* Fixed minimum width - increased for divisi column */
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
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Container untuk sidebar yang akan dimuat -->
        @include('finance.templet.sider')


        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Data karyawan</h2>

                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span
                            class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                            placeholder="Cari nama, role, divisi, atau alamat..." type="text" />
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
                                    <input type="checkbox" id="filterGeneralManager" value="general_manager">
                                    <label for="filterGeneralManager">General Manager</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterManagerDivisi" value="manager_divisi">
                                    <label for="filterManagerDivisi">Manager Divisi</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterKaryawan" value="karyawan">
                                    <label for="filterKaryawan">Karyawan</label>
                                </div>
                                <div class="filter-actions">
                                    <button id="applyFilter" class="filter-apply">Terapkan</button>
                                    <button id="resetFilter" class="filter-reset">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">people</span>
                            Data Karyawan
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount"
                                    class="font-semibold text-text-light">0</span> karyawan</span>
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
                                            <th style="min-width: 150px;">Gaji</th>
                                            <th style="min-width: 250px;">Alamat</th>
                                            <th style="min-width: 150px;">Kontak</th>
                                            <th style="min-width: 100px;">Foto</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">

                                        @foreach ($karyawans as $karyawan)
                                            <tr class="karyawan-row">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $karyawan->nama }}</td>
                                                <td>{{ $karyawan->email }}</td>
                                                <td>
                                                    <span class="status-badge {{ in_array(strtolower($karyawan->role), ['manager_divisi','general_manager']) ? 'status-manager' : 'status-staff' }}">
                                                        {{ $karyawan->role ? str_replace('_', ' ', $karyawan->role) : '-' }}
                                                    </span>
                                                </td>
                                                <td>{{ $karyawan->divisi ?? '-' }}</td>
                                                <td>Rp {{ number_format((float) ($karyawan->gaji ?? 0), 0, ',', '.') }}</td>
                                                <td>{{ $karyawan->alamat }}</td>
                                                <td>{{ $karyawan->kontak }}</td>
                                                <td>
                                                    @if(!empty($karyawan->foto_url))
                                                        <img src="{{ $karyawan->foto_url }}"
                                                            class="h-10 w-10 rounded-full object-cover"
                                                            onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
                                                    @elseif(!empty($karyawan->foto))
                                                        <img src="{{ str_starts_with($karyawan->foto, 'http') ? $karyawan->foto : asset('storage/' . ltrim($karyawan->foto, '/')) }}"
                                                            class="h-10 w-10 rounded-full object-cover"
                                                            onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
                                                    @else
                                                        <span class="material-icons-outlined">person</span>
                                                    @endif
                                                </td>
                                                <td class="text-center flex gap-2 justify-center">
                                                    <!-- EDIT -->
                                                    <button onclick="openEditModal({{ $karyawan->id }})"
                                                        class="p-1 hover:bg-blue-100 rounded-full">
                                                        <span class="material-icons-outlined">edit</span>
                                                    </button>

                                                    <!-- DELETE -->
                                                    <form action="{{ route('finance.karyawan.destroy', $karyawan->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus data ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 hover:bg-red-100 rounded-full">
                                                            <span class="material-icons-outlined">delete</span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4" id="mobile-cards">
                            <!-- Card akan diisi dengan JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

<!-- Modal Edit Karyawan -->
<div id="editKaryawanModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Data Karyawan</h3>
                <button id="closeEditModalBtn" class="text-gray-800 hover:text-gray-500">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="editKaryawanForm" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="editId" name="id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" id="editNama"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <input type="text" id="editRole"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                        <input type="text" id="editDivisi"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                               readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gaji (bisa diubah)</label>
                        <input type="text" id="editGajiDisplay"
                               inputmode="numeric"
                               required
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="Rp 0">
                        <input type="hidden" id="editGaji" name="gaji" value="">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="editEmail"
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                               readonly>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" id="cancelEditBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Gaji</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Popup Modal untuk Konfirmasi Hapus -->
    <div id="deleteKaryawanModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button id="closeDeleteModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="deleteKaryawanForm">
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus data karyawan ini?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteId" name="id">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelDeleteBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
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
    <!-- Tambahkan di body sebelum script utama -->
<script>
    // Data karyawan dari PHP untuk JavaScript (untuk modal edit saja)
    const karyawanData = @json($karyawans);
    
    // Inisialisasi variabel untuk filter dan search
    let activeFilters = ['all'];
    let searchTerm = '';
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded');
        initializeScrollDetection();
        initializeModals();
        initializeFilter();
        initializeSearch();
        initSidebar();
        
        // Set data attributes pada rows yang sudah ada di Blade
        const rows = document.querySelectorAll('.karyawan-row');
        rows.forEach((row, index) => {
            const nama = row.querySelector('td:nth-child(2)').textContent;
            const email = row.querySelector('td:nth-child(3)').textContent;
            const role = row.querySelector('td:nth-child(4) span').textContent;
            const divisi = row.querySelector('td:nth-child(5)').textContent; // index 5 untuk divisi
            const gajiText = row.querySelector('td:nth-child(6)').textContent; // index 6 untuk gaji
            const gaji = parseInt(gajiText.replace(/[^0-9]/g, ''));
            const alamat = row.querySelector('td:nth-child(7)').textContent; // index 7 untuk alamat
            const kontak = row.querySelector('td:nth-child(8)').textContent; // index 8 untuk kontak
            const foto = row.querySelector('td:nth-child(9) img')?.src || '';
            
            row.setAttribute('data-nama', nama);
            row.setAttribute('data-email', email);
            row.setAttribute('data-role', role);
            row.setAttribute('data-divisi', divisi);
            row.setAttribute('data-gaji', gaji);
            row.setAttribute('data-alamat', alamat);
            row.setAttribute('data-kontak', kontak);
            row.setAttribute('data-foto', foto);
        });
        
        // Inisialisasi filter pertama kali
        applyFilters();
    });

    function formatRupiah(value) {
        const numeric = String(value ?? '').replace(/\D/g, '');
        if (!numeric) return 'Rp 0';
        return 'Rp ' + Number(numeric).toLocaleString('id-ID');
    }

    function parseRupiah(value) {
        const numeric = String(value ?? '').replace(/\D/g, '');
        return numeric ? Number(numeric) : 0;
    }

    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (!sidebar || !overlay) {
            console.log('Sidebar elements not found');
            return;
        }

        sidebar.style.transform = 'translateX(0)';
        overlay.style.display = 'block';
    }

    // === FILTER ===
    function initializeFilter() {
        const filterBtn = document.getElementById('filterBtn');
        const filterDropdown = document.getElementById('filterDropdown');
        const applyFilterBtn = document.getElementById('applyFilter');
        const resetFilterBtn = document.getElementById('resetFilter');
        const filterAll = document.getElementById('filterAll');
        
        if (!filterBtn || !filterDropdown) {
            console.log('Filter elements not found');
            return;
        }
        
        // Toggle filter dropdown
        filterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            filterDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            filterDropdown.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside
        filterDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Handle "All" checkbox
        if (filterAll) {
            filterAll.addEventListener('change', function() {
                if (this.checked) {
                    document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                        cb.checked = false;
                    });
                }
            });
        }
        
        // Handle other checkboxes
        document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    if (filterAll) filterAll.checked = false;
                }
            });
        });
        
        // Apply filter
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', function() {
                const filterAll = document.getElementById('filterAll');
                const filterGeneralManager = document.getElementById('filterGeneralManager');
                const filterManagerDivisi = document.getElementById('filterManagerDivisi');
                const filterKaryawan = document.getElementById('filterKaryawan');
                
                activeFilters = [];
                if (filterAll.checked) {
                    activeFilters.push('all');
                } else {
                    if (filterGeneralManager && filterGeneralManager.checked) activeFilters.push('general_manager');
                    if (filterManagerDivisi && filterManagerDivisi.checked) activeFilters.push('manager_divisi');
                    if (filterKaryawan && filterKaryawan.checked) activeFilters.push('karyawan');
                }
                
                applyFilters();
                filterDropdown.classList.remove('show');
            });
        }
        
        // Reset filter
        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', function() {
                const filterAll = document.getElementById('filterAll');
                const filterGeneralManager = document.getElementById('filterGeneralManager');
                const filterManagerDivisi = document.getElementById('filterManagerDivisi');
                const filterKaryawan = document.getElementById('filterKaryawan');
                
                if (filterAll) filterAll.checked = true;
                if (filterGeneralManager) filterGeneralManager.checked = false;
                if (filterManagerDivisi) filterManagerDivisi.checked = false;
                if (filterKaryawan) filterKaryawan.checked = false;
                
                activeFilters = ['all'];
                applyFilters();
                filterDropdown.classList.remove('show');
            });
        }
    }
    
    function applyFilters() {
        const rows = document.querySelectorAll('.karyawan-row');
        
        let visibleCount = 0;
        
        rows.forEach((row) => {
            const role = row.getAttribute('data-role') || '';
            const nama = row.getAttribute('data-nama') || '';
            const email = row.getAttribute('data-email') || '';
            const divisi = row.getAttribute('data-divisi') || '';
            const alamat = row.getAttribute('data-alamat') || '';
            
            let roleMatches = false;
            if (activeFilters.includes('all')) {
                roleMatches = true;
            } else {
                const roleLower = role.toLowerCase();
                roleMatches = activeFilters.some(filter =>
                    roleLower.includes(filter.toLowerCase())
                );
            }
            
            let searchMatches = true;
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                searchMatches = 
                    nama.toLowerCase().includes(searchLower) || 
                    alamat.toLowerCase().includes(searchLower) ||
                    role.toLowerCase().includes(searchLower) ||
                    divisi.toLowerCase().includes(searchLower) ||
                    email.toLowerCase().includes(searchLower);
            }
            
            if (roleMatches && searchMatches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update total count
        const totalCountElement = document.getElementById('totalCount');
        if (totalCountElement) {
            totalCountElement.textContent = visibleCount;
        }
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

    function initializeScrollDetection() {
        const scrollableTable = document.getElementById('scrollableTable');
        if (scrollableTable) {
            scrollableTable.addEventListener('scroll', function() {
                // Scroll detection logic jika diperlukan
            });
        }
    }

    // Modal functions
    function initializeModals() {
        const editKaryawanModal = document.getElementById('editKaryawanModal');
        const closeEditModalBtn = document.getElementById('closeEditModalBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const editKaryawanForm = document.getElementById('editKaryawanForm');
        const editGajiDisplay = document.getElementById('editGajiDisplay');
        const editGajiHidden = document.getElementById('editGaji');

        if (!editKaryawanModal || !closeEditModalBtn || !cancelEditBtn || !editKaryawanForm) {
            console.log('Modal elements not found');
            return;
        }

        // Modal Edit
        closeEditModalBtn.addEventListener('click', closeEditModal);
        cancelEditBtn.addEventListener('click', closeEditModal);

        if (editGajiDisplay && editGajiHidden) {
            editGajiDisplay.addEventListener('input', function() {
                const numericValue = parseRupiah(this.value);
                editGajiHidden.value = numericValue;
                this.value = formatRupiah(numericValue);
            });

            editGajiDisplay.addEventListener('blur', function() {
                const numericValue = parseRupiah(this.value);
                this.value = formatRupiah(numericValue);
            });
        }

        // Form submission
        editKaryawanForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const numericGaji = parseRupiah(editGajiDisplay ? editGajiDisplay.value : 0);
            if (editGajiHidden) {
                editGajiHidden.value = numericGaji;
            }
            
            const formData = new FormData(this);
            formData.set('_method', 'PUT');
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMinimalPopup('Berhasil', 'Data karyawan berhasil diperbarui', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMinimalPopup('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Update error:', error);
                showMinimalPopup('Error', 'Terjadi kesalahan saat mengupdate data', 'error');
            }
        });
    }

    // Fungsi ini HARUS dideklarasikan di scope global
    function openEditModal(karyawanId) {
        console.log('openEditModal called with ID:', karyawanId);
        const karyawan = karyawanData.find(k => k.id == karyawanId);

        if (!karyawan) {
            showMinimalPopup('Error', 'Data karyawan tidak ditemukan', 'error');
            return;
        }

        const editForm = document.getElementById('editKaryawanForm');
        if (!editForm) {
            console.error('Edit form not found');
            return;
        }

        const baseRoute = "{{ route('finance.karyawan.update', '') }}";
        editForm.action = baseRoute + '/' + karyawan.id;

        // Set nilai form
        document.getElementById('editId').value = karyawan.id;
        document.getElementById('editNama').value = karyawan.nama || '';
        document.getElementById('editEmail').value = karyawan.email || '';
        document.getElementById('editRole').value = karyawan.role || '';
        document.getElementById('editDivisi').value = karyawan.divisi || '-';
        const gajiNumeric = parseRupiah(karyawan.gaji || 0);
        document.getElementById('editGaji').value = gajiNumeric;
        const editGajiDisplay = document.getElementById('editGajiDisplay');
        if (editGajiDisplay) {
            editGajiDisplay.value = formatRupiah(gajiNumeric);
        }

        // Tampilkan modal
        const modal = document.getElementById('editKaryawanModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editKaryawanModal');
        const form = document.getElementById('editKaryawanForm');
        
        if (modal) modal.classList.add('hidden');
        if (form) form.reset();
    }

    async function confirmDelete(id) {
        if (!confirm('Yakin ingin menghapus data karyawan ini?')) return;
        
        try {
            const response = await fetch("{{ route('finance.karyawan.destroy', '') }}/" + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMinimalPopup('Berhasil', 'Data karyawan berhasil dihapus', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showMinimalPopup('Error', result.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showMinimalPopup('Error', 'Terjadi kesalahan saat menghapus data', 'error');
        }
    }

    // Minimalist Popup
    function showMinimalPopup(title, message, type = 'success') {
        const popup = document.getElementById('minimalPopup');
        if (!popup) return;
        
        const popupTitle = popup.querySelector('.minimal-popup-title');
        const popupMessage = popup.querySelector('.minimal-popup-message');
        const popupIcon = popup.querySelector('.minimal-popup-icon span');
        
        if (popupTitle) popupTitle.textContent = title;
        if (popupMessage) popupMessage.textContent = message;
        
        popup.className = 'minimal-popup show ' + type;
        
        if (type === 'success') {
            if (popupIcon) popupIcon.textContent = 'check';
        } else if (type === 'error') {
            if (popupIcon) popupIcon.textContent = 'error';
        } else if (type === 'warning') {
            if (popupIcon) popupIcon.textContent = 'warning';
        }
        
        setTimeout(() => {
            popup.classList.remove('show');
        }, 3000);
    }
    
    // Event listener untuk close popup
    const popupCloseBtn = document.querySelector('.minimal-popup-close');
    if (popupCloseBtn) {
        popupCloseBtn.addEventListener('click', function() {
            const popup = document.getElementById('minimalPopup');
            if (popup) popup.classList.remove('show');
        });
    }
</script>
</body>

</html>
