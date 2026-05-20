<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar User - Dashboard</title>
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

        .status-admin {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-karyawan {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
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
            min-width: 800px;
            /* Fixed minimum width */
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

        /* Scroll indicator */
        .scroll-indicator {
            position: relative;
        }

        .scroll-hint {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #3b82f6;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 6px;
            animation: pulse 2s infinite;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .scroll-hint.hidden {
            display: none;
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

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Hidden class for filtering */
        .hidden-by-filter {
            display: none !important;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('admin/templet/sider')


        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Daftar User</h2>

                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span
                            class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                            placeholder="Cari nama atau email..." type="text" />
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <button id="tambahUserBtn"
                            class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Tambah User</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>
                </div>

                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">people</span>
                            Daftar User
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount"
                                    class="font-semibold text-text-light">{{ count($users) }}</span> user</span>
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
                                            <th style="min-width: 200px;">Username</th>
                                            <th style="min-width: 200px;">Divisi</th>
                                            <th style="min-width: 250px;">Email</th>
                                            <th style="min-width: 120px;">Role</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        @foreach ($users as $i => $u)
                                            <tr class="user-row" data-id="{{ $u->id }}"
                                                data-name="{{ $u->name }}" data-divisi-id="{{ $u->divisi_id }}"
                                                data-email="{{ $u->email }}" data-role="{{ $u->role }}">
                                                <!-- TETAP MENGGUNAKAN $i+1 untuk nomor urut -->
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $u->name }}</td>
                                               <td>
    @if($u->divisi) <!-- Akses via relationship -->
        {{ $u->divisi->divisi }}
    @else
        -
    @endif
</td>
                                                <td>{{ $u->email }}</td>
                                                <td>
                                                    <span
                                                        class="status-badge {{ $u->role == 'admin' ? 'status-admin' : 'status-karyawan' }}">
                                                        {{ $u->role }}
                                                    </span>
                                                </td>
                                                <!-- Di dalam desktop table (td aksi) -->
                                                <td style="min-width: 100px; text-align: center;">
                                                    <div class="flex justify-center gap-2">
                                                        <button
                                                            onclick="openModalEdit({{ $u->id }}, '{{ $u->name }}', {{ $u->divisi_id ?? 'null' }}, '{{ $u->email }}', '{{ $u->role }}')"
                                                            class="p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                            title="Edit">
                                                            <span class="material-icons-outlined">edit</span>
                                                        </button>

                                                        <!-- GANTI FORM DELETE INI -->
                                                        <button
                                                            onclick="confirmDeleteUser({{ $u->id }}, '{{ $u->name }}')"
                                                            class="delete-user-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700"
                                                            title="Hapus">
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

                        <!-- GANTI INI DI MOBILE CARD: -->
                        <div class="mobile-cards space-y-4" id="mobile-cards">
                            @foreach ($users as $i => $u)
                                <div class="user-card bg-white rounded-lg border border-border-light p-4 shadow-sm"
                                    data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                    data-email="{{ $u->email }}" data-role="{{ $u->role }}"
                                    data-index="{{ $i }}">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold text-base">{{ $u->name }}</h4>
                                            <p class="text-sm text-text-muted-light">{{ $u->email }}</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <!-- TAMBAHKAN PARAMETER DIVISI DI SINI -->
                                            <button
                                                onclick="openModalEdit({{ $u->id }}, '{{ $u->name }}', '{{ $u->divisi ?? '' }}', '{{ $u->email }}', '{{ $u->role }}')"
                                                class="p-1 rounded-full hover:bg-primary/20 text-gray-700">
                                                <span class="material-icons-outlined">edit</span>
                                            </button>

                                            <form action="{{ route('admin.user.delete', $u->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus user?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1 rounded-full hover:bg-red-500/20 text-gray-700">
                                                    <span class="material-icons-outlined">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <p class="text-text-muted-light">No</p>
                                            <p class="font-medium">{{ $i + 1 }}</p>
                                        </div>
<div>
    <p class="text-text-muted-light">Divisi</p>
    <p class="font-medium">
        @if($u->divisi)
            {{ $u->divisi->divisi }}
        @else
            -
        @endif
    </p>
</div>
                                        <div>
                                            <p class="text-text-muted-light">Role</p>
                                            <p>
                                                <span
                                                    class="status-badge {{ $u->role == 'admin' ? 'status-admin' : 'status-karyawan' }}">
                                                    {{ $u->role }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div id="paginationContainer" class="desktop-pagination">
                            <button id="prevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="pageNumbers" class="flex gap-1">
                                <!-- Nomor halaman akan dibuat dengan JavaScript -->
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
    <!-- Modal Konfirmasi Hapus User -->
    <div id="deleteUserModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus User</h3>
                    <button type="button" class="close-modal text-gray-800 hover:text-gray-500"
                        data-target="deleteUserModal">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="material-icons-outlined text-red-500 text-3xl">warning</span>
                        </div>
                    </div>

                    <p class="text-center text-gray-700 mb-2">
                        Apakah Anda yakin ingin menghapus user <span id="deleteUserName"
                            class="font-semibold"></span>?
                    </p>
                    <p class="text-center text-sm text-gray-500">
                        Tindakan ini tidak dapat dibatalkan. Semua data terkait user ini akan dihapus.
                    </p>
                </div>

                <div class="flex justify-center gap-3">
                    <button type="button"
                        class="close-modal px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium"
                        data-target="deleteUserModal">
                        Batal
                    </button>
                    <button type="button" id="confirmDeleteBtn"
                        class="px-5 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                        Hapus User
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Tambah User -->
    <div id="tambahUserModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah User Baru</h3>
                    <button type="button" class="close-modal text-gray-800 hover:text-gray-500"
                        data-target="tambahUserModal">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="tambahUserForm" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan nama lengkap">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Masukkan email">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                            <input type="password" name="password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Minimal 5 karakter">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select name="role" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Role</option>
                                <option value="karyawan">Karyawan</option>
                                <option value="finance">Finance</option>
                                <option value="manager_divisi">Manager Divisi</option>
                                <option value="general_manager">General Manager</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                            <!-- GANTI id="divisiSelectTambah" MENJADI id="tambahDivisiSelect" -->
                            <select name="divisi_id" id="tambahDivisiSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Pilih Divisi</option>

                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button"
                            class="cancel-modal px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                            data-target="tambahUserModal">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan
                            User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal Edit User -->
<div id="editUserModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit User</h3>
                <button type="button" class="close-modal text-gray-800 hover:text-gray-500"
                    data-target="editUserModal">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            
            <form id="editUserForm" class="space-y-4">
                @csrf
                <input type="hidden" id="editUserId" name="id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editUserName" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                            minlength="2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="editUserEmail" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password (kosongkan jika tidak diubah)
                        </label>
                        <input type="password" id="editUserPassword" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="Kosongkan jika tidak ingin mengubah"
                            minlength="5">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select id="editUserRole" name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Role</option>
                            <option value="owner">Owner</option>
                            <option value="admin">Admin</option>
                            <option value="general_manager">General Manager</option>
                            <option value="manager_divisi">Manager Divisi</option>
                            <option value="finance">Finance</option>
                            <option value="karyawan">Karyawan</option>
                        </select>
                    </div>
                    
                    <!-- PERBAIKAN: Pastikan ID ini sama dengan yang dicari JavaScript -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                        <select name="divisi_id" id="editDivisiSelect"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Divisi</option>
                            <!-- Options akan diisi oleh JavaScript -->
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button"
                        class="cancel-modal px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                        data-target="editUserModal">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update User
                    </button>
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
        // Pass PHP data to JavaScript
        window.divisisFromPHP = @json($divisis ?? []);

        // Fallback data jika API gagal
        const fallbackData = @json($divisis ?? []);
    </script>
    <script>
        // ==================== UTILITY FUNCTIONS ====================
        function getCsrfToken() {
            let token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token) token = document.querySelector('input[name="_token"]')?.value;
            if (!token) token = window.Laravel?.csrfToken;
            return token;
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                // Reset form ketika modal dibuka
                if (modalId === 'tambahUserModal') {
                    document.getElementById('tambahUserForm')?.reset();
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('hidden');
        }

        function showMinimalPopup(title, message, type = 'success') {
            const popup = document.getElementById('minimalPopup');
            if (!popup) {
                console.error('Popup element not found');
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

        // Fungsi untuk tombol edit yang ada di kode lama
        // Fungsi untuk modal edit
window.openModalEdit = function(id, name, divisiId, email, role) {
    console.log('📝 Opening edit modal for user:', {
        id,
        name,
        divisiId: divisiId || 'null',
        email,
        role
    });

    // Buka modal dulu
    openModal('editUserModal');
    
    // Isi form fields
    document.getElementById('editUserId').value = id;
    document.getElementById('editUserName').value = name;
    document.getElementById('editUserEmail').value = email;
    document.getElementById('editUserRole').value = role;
    
    // Reset password field
    const passwordField = document.getElementById('editUserPassword');
    if (passwordField) {
        passwordField.value = '';
    }

    // Tunggu modal terbuka sepenuhnya
    setTimeout(() => {
        // Load divisi untuk modal edit
        loadDivisis('editDivisiSelect').then((divisis) => {
            const select = document.getElementById('editDivisiSelect');
            if (select) {
                // Konversi divisiId
                let selectedDivisiId = divisiId;
                
                // Handle berbagai format divisiId
                if (!selectedDivisiId || 
                    selectedDivisiId === 'null' || 
                    selectedDivisiId === 'undefined' ||
                    selectedDivisiId === '' ||
                    selectedDivisiId === '0') {
                    selectedDivisiId = '';
                }
                
                // Set value
                select.value = selectedDivisiId;
                console.log('✅ Set divisi select to value:', selectedDivisiId);
                
                // Debug: lihat options yang tersedia
                console.log('📋 Available options:', 
                    Array.from(select.options).map(opt => ({ 
                        value: opt.value, 
                        text: opt.text 
                    }))
                );
            } else {
                console.error('❌ editDivisiSelect element still not found after timeout');
            }
        }).catch(error => {
            console.error('❌ Error loading divisis:', error);
        });
    }, 200); // Delay 200ms untuk memastikan modal terbuka
};

        // ==================== DIVISI FUNCTIONS ====================
async function loadDivisis(selectId = 'tambahDivisiSelect') {
    try {
        console.log('🔄 Loading divisi for select:', selectId);

        // Tunggu sebentar untuk memastikan DOM siap
        await new Promise(resolve => setTimeout(resolve, 100));
        
        const selectElement = document.getElementById(selectId);
        if (!selectElement) {
            console.error('❌ Select element not found:', selectId);
            
            // Coba cari lagi setelah delay
            await new Promise(resolve => setTimeout(resolve, 300));
            const retryElement = document.getElementById(selectId);
            if (!retryElement) {
                console.error('❌ Select element still not found after retry:', selectId);
                return [];
            }
            selectElement = retryElement;
        }

        // Clear existing options except first one
        while (selectElement.options.length > 1) {
            selectElement.remove(1);
        }

        // Coba fetch dari API
        const csrfToken = getCsrfToken();
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        try {
            const response = await fetch('/admin/divisis/list', {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin'
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data && Array.isArray(result.data)) {
                    console.log('✅ Divisi loaded from API:', result.data.length);
                    populateSelect(selectId, result.data);
                    return result.data;
                }
            }
        } catch (fetchError) {
            console.warn('⚠️ API fetch failed:', fetchError);
        }

        // Gunakan data dari PHP (fallback)
        const divisisFromPHP = window.divisisFromPHP || [];
        if (divisisFromPHP.length > 0) {
            console.log('✅ Using divisi data from PHP:', divisisFromPHP.length);
            populateSelect(selectId, divisisFromPHP);
            return divisisFromPHP;
        }

        // Jika semua gagal
        console.warn('⚠️ No divisi data available');
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Tidak ada divisi tersedia';
        selectElement.appendChild(option);
        
        return [];

    } catch (error) {
        console.error('❌ Error loading divisi:', error);
        return [];
    }
}

// Fungsi populateSelect yang diperbaiki
function populateSelect(selectId, data) {
    const select = document.getElementById(selectId);
    if (!select) {
        console.error('❌ Select element not found for populate:', selectId);
        return;
    }

    // Simpan option pertama (Pilih Divisi)
    const firstOption = select.options[0] ? select.options[0].outerHTML : '<option value="">Pilih Divisi</option>';
    select.innerHTML = firstOption;

    // Tambah options divisi
    if (data && Array.isArray(data) && data.length > 0) {
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            
            // Gunakan property yang benar
            if (item.divisi) {
                option.textContent = item.divisi;
            } else if (item.nama_divisi) {
                option.textContent = item.nama_divisi;
            } else if (item.name) {
                option.textContent = item.name;
            } else {
                option.textContent = 'Unknown';
            }
            
            select.appendChild(option);
        });
        console.log(`✅ Loaded ${data.length} divisi into ${selectId}`);
    } else {
        console.warn(`⚠️ No divisi data to populate in ${selectId}`);
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Tidak ada divisi tersedia';
        select.appendChild(option);
    }
}

        function populateSelect(selectId, data) {
            const select = document.getElementById(selectId);
            if (!select) {
                console.error('Select element not found:', selectId);
                return;
            }

            // Simpan option pertama (Pilih Divisi)
            const firstOption = select.querySelector('option');
            select.innerHTML = firstOption ? firstOption.outerHTML : '<option value="">Pilih Divisi</option>';

            // Tambah options divisi
            if (data && Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;

                    // Gunakan property yang benar
                    if (item.divisi) {
                        option.textContent = item.divisi;
                    } else if (item.nama_divisi) {
                        option.textContent = item.nama_divisi;
                    } else if (item.name) {
                        option.textContent = item.name;
                    } else {
                        option.textContent = 'Unknown';
                    }

                    select.appendChild(option);
                });
                console.log(`Loaded ${data.length} divisi into ${selectId}`);
            } else {
                console.warn(`No divisi data to populate in ${selectId}`);
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Tidak ada divisi tersedia';
                select.appendChild(option);
            }
        }

        function useFallbackDivisiData(selectId) {
            console.warn('Using fallback divisi data for:', selectId);

            populateSelect(selectId, fallbackData);

            // Tampilkan warning hanya sekali
            if (!window.fallbackWarningShown) {
                showMinimalPopup('Info', 'Menggunakan data default divisi', 'warning');
                window.fallbackWarningShown = true;
            }
        }

        // ==================== USER CRUD FUNCTIONS ====================
        async function handleTambahUser(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            console.log('Tambah user data:', data);

            // Validasi
            if (!data.name || !data.email || !data.password || !data.role) {
                showMinimalPopup('Error', 'Harap lengkapi semua field yang wajib (*)', 'error');
                return;
            }

            if (data.password.length < 5) {
                showMinimalPopup('Error', 'Password minimal 5 karakter', 'error');
                return;
            }

            // Disable submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent || 'Simpan';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Menyimpan...';
            }

            try {
                const response = await fetch('/admin/user/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                console.log('Store response status:', response.status);

                // Parse response
                let result;
                try {
                    result = await response.json();
                } catch (jsonError) {
                    console.error('Failed to parse JSON:', jsonError);
                    throw new Error('Invalid server response');
                }

                if (response.ok && result.success) {
                    showMinimalPopup('Berhasil', result.message || 'User berhasil ditambahkan', 'success');
                    form.reset();
                    closeModal('tambahUserModal');

                    // OPTION 1: Reload halaman (sederhana)
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);

                    // OPTION 2: Tambah row baru di atas tanpa reload (advanced)
                    // addNewUserToTable(result.data);

                } else {
                    showMinimalPopup('Error', result.message || 'Gagal menambahkan user', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMinimalPopup('Error', 'Terjadi kesalahan pada server: ' + error.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        }

// Di method handleEditUser, tambahkan debugging
async function handleEditUser(e) {
    e.preventDefault();

    const form = e.target;
    const userId = document.getElementById('editUserId')?.value;
    if (!userId) {
        showMinimalPopup('Error', 'User ID tidak ditemukan', 'error');
        return;
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    console.log('📝 Edit user data:', data);
    console.log('🔗 URL:', `/admin/user/update/${userId}`);

    // Validasi
    if (!data.name || !data.email || !data.role) {
        showMinimalPopup('Error', 'Harap lengkapi semua field yang wajib (*)', 'error');
        return;
    }

    if (data.password && data.password.length < 5) {
        showMinimalPopup('Error', 'Password minimal 5 karakter', 'error');
        return;
    }

    // Jika password kosong, hapus dari data
    if (!data.password) {
        delete data.password;
    }

    // Disable submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn?.textContent || 'Update';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Memperbarui...';
    }

    try {
        const csrfToken = getCsrfToken();
        console.log('🔑 CSRF Token:', csrfToken);
        
        const response = await fetch(`/admin/user/update/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        console.log('📤 Update response status:', response.status);
        console.log('📤 Update response headers:', response.headers);

        // Parse response
        let result;
        try {
            const responseText = await response.text();
            console.log('📤 Response text:', responseText);
            result = JSON.parse(responseText);
        } catch (jsonError) {
            console.error('❌ Failed to parse JSON:', jsonError);
            throw new Error('Invalid server response');
        }

        console.log('📤 Update response result:', result);

        if (response.ok && result.success) {
            showMinimalPopup('Berhasil', result.message || 'User berhasil diperbarui', 'success');
            closeModal('editUserModal');

            // **TAMBAHKAN INI: Reload halaman untuk update data karyawan**
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showMinimalPopup('Error', result.message || 'Gagal memperbarui user', 'error');
        }
    } catch (error) {
        console.error('❌ Error:', error);
        showMinimalPopup('Error', 'Terjadi kesalahan pada server: ' + error.message, 'error');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }
}

        // ==================== PAGINATION & SEARCH FUNCTIONS ====================
        function initializeUserManagement() {
            // Inisialisasi variabel
            let currentPage = 1;
            const itemsPerPage = 5;
            let searchTerm = '';

            // Dapatkan semua elemen user
            const userRows = document.querySelectorAll('.user-row');
            const userCards = document.querySelectorAll('.user-card');

            console.log('Total user rows found:', userRows.length);
            console.log('Total user cards found:', userCards.length);

            // Inisialisasi pagination dan search
            initializePagination();
            initializeSearch();

            // === PAGINATION ===
            function initializePagination() {
                renderPagination();
                updateVisibleItems();
            }

            function renderPagination() {
                const visibleRows = getFilteredRows();
                const totalPages = Math.ceil(visibleRows.length / itemsPerPage);
                const pageNumbersContainer = document.getElementById('pageNumbers');
                const prevButton = document.getElementById('prevPage');
                const nextButton = document.getElementById('nextPage');

                // Clear existing page numbers
                if (pageNumbersContainer) {
                    pageNumbersContainer.innerHTML = '';

                    // Generate page numbers
                    for (let i = 1; i <= totalPages; i++) {
                        const pageNumber = document.createElement('button');
                        pageNumber.textContent = i;
                        pageNumber.className = `desktop-page-btn ${i === currentPage ? 'active' : ''}`;
                        pageNumber.addEventListener('click', () => goToPage(i));
                        pageNumbersContainer.appendChild(pageNumber);
                    }
                }

                // Update navigation buttons
                if (prevButton) prevButton.disabled = currentPage === 1;
                if (nextButton) nextButton.disabled = currentPage === totalPages || totalPages === 0;

                // Add event listeners for navigation buttons
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
            }

            function getFilteredRows() {
                return Array.from(userRows).filter(row => {
                    const name = row.getAttribute('data-name') || '';
                    const email = row.getAttribute('data-email') || '';

                    if (!searchTerm) return true;

                    const searchLower = searchTerm.toLowerCase();
                    return name.toLowerCase().includes(searchLower) ||
                        email.toLowerCase().includes(searchLower);
                });
            }

            function getFilteredCards() {
                return Array.from(userCards).filter(card => {
                    const name = card.getAttribute('data-name') || '';
                    const email = card.getAttribute('data-email') || '';

                    if (!searchTerm) return true;

                    const searchLower = searchTerm.toLowerCase();
                    return name.toLowerCase().includes(searchLower) ||
                        email.toLowerCase().includes(searchLower);
                });
            }

            function updateVisibleItems() {
                const visibleRows = getFilteredRows();
                const visibleCards = getFilteredCards();

                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                // Hide all rows and cards first
                userRows.forEach(row => row.style.display = 'none');
                userCards.forEach(card => card.style.display = 'none');

                // Show only the rows for current page
                visibleRows.forEach((row, index) => {
                    if (index >= startIndex && index < endIndex) {
                        row.style.display = '';
                    }
                });

                // Show only the cards for current page
                visibleCards.forEach((card, index) => {
                    if (index >= startIndex && index < endIndex) {
                        card.style.display = '';
                    }
                });

                // Update total count
                const totalCountElement = document.getElementById('totalCount');
                if (totalCountElement) {
                    totalCountElement.textContent = visibleRows.length;
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
                        console.log('Search term changed to:', searchTerm);
                        currentPage = 1; // Reset ke halaman pertama saat search
                        renderPagination();
                        updateVisibleItems();
                    }, 300);
                });
            }
        }

        // ==================== EVENT LISTENERS ====================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== User Management Initialized ===');

            // Initialize user management (pagination, search)
            initializeUserManagement();

            // Load divisi saat modal tambah dibuka
            // Load divisi saat modal tambah dibuka
            const tambahUserBtn = document.getElementById('tambahUserBtn');
            if (tambahUserBtn) {
                tambahUserBtn.addEventListener('click', function() {
                    openModal('tambahUserModal');

                    // Clear dan load ulang divisi
                    const select = document.getElementById('tambahDivisiSelect');
                    if (select) {
                        // Simpan option pertama
                        const firstOption = select.querySelector('option');
                        select.innerHTML = firstOption ? firstOption.outerHTML :
                            '<option value="">Pilih Divisi</option>';
                    }

                    loadDivisis('tambahDivisiSelect');
                });
            }

            // Form submissions
            const tambahUserForm = document.getElementById('tambahUserForm');
            if (tambahUserForm) {
                tambahUserForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleTambahUser(e);
                });
            }

            const editUserForm = document.getElementById('editUserForm');
            if (editUserForm) {
                editUserForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleEditUser(e);
                });
            }

            // Modal close buttons
            document.querySelectorAll('.close-modal, .cancel-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    if (targetId) {
                        closeModal(targetId);
                    }
                });
            });

            // Popup close button
            const popupCloseBtn = document.querySelector('.minimal-popup-close');
            if (popupCloseBtn) {
                popupCloseBtn.addEventListener('click', function() {
                    document.getElementById('minimalPopup').classList.remove('show');
                });
            }

            // Initialize scroll hint for table
            const scrollableTable = document.getElementById('scrollableTable');
            if (scrollableTable) {
                // Check if scrolling is needed
                setTimeout(() => {
                    if (scrollableTable.scrollWidth > scrollableTable.clientWidth) {
                        // Add scroll hint if needed
                        if (!document.getElementById('scrollHint')) {
                            const scrollHint = document.createElement('div');
                            scrollHint.id = 'scrollHint';
                            scrollHint.className = 'scroll-hint';
                            scrollHint.innerHTML =
                                '<span class="material-icons-outlined text-sm">east</span> Geser untuk melihat lebih banyak';
                            scrollableTable.appendChild(scrollHint);
                        }
                    }
                }, 100);

                // Add scroll event listener
                scrollableTable.addEventListener('scroll', function() {
                    const scrollLeft = scrollableTable.scrollLeft;
                    const maxScroll = scrollableTable.scrollWidth - scrollableTable.clientWidth;
                    const scrollHint = document.getElementById('scrollHint');

                    // Hide hint when scrolled to the end
                    if (scrollHint) {
                        if (scrollLeft >= maxScroll - 10) {
                            scrollHint.classList.add('hidden');
                        } else {
                            scrollHint.classList.remove('hidden');
                        }
                    }
                });
            }

            // Auto-close popup after 3 seconds
            setInterval(() => {
                const popup = document.getElementById('minimalPopup');
                if (popup && popup.classList.contains('show')) {
                    popup.classList.remove('show');
                }
            }, 3000);
        });

        // ==================== DELETE FUNCTIONS ====================
        let userToDelete = null;

        function confirmDeleteUser(userId, userName) {
            userToDelete = userId;

            // Tampilkan modal konfirmasi
            document.getElementById('deleteUserName').textContent = userName;
            openModal('deleteUserModal');
        }

        async function handleDeleteUser() {
            if (!userToDelete) {
                showMinimalPopup('Error', 'User ID tidak ditemukan', 'error');
                return;
            }

            // Disable tombol hapus
            const deleteBtn = document.getElementById('confirmDeleteBtn');
            const originalText = deleteBtn?.textContent || 'Hapus User';
            if (deleteBtn) {
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<span class="loading-spinner"></span> Menghapus...';
            }

            try {
                const response = await fetch(`/admin/user/delete/${userToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                console.log('Delete response status:', response.status);

                // Parse response
                let result;
                try {
                    result = await response.json();
                } catch (jsonError) {
                    console.error('Failed to parse JSON:', jsonError);
                    // Mungkin redirect dengan session message
                    if (response.redirected) {
                        showMinimalPopup('Berhasil', 'User berhasil dihapus', 'success');
                        closeModal('deleteUserModal');
                        setTimeout(() => window.location.reload(), 1500);
                        return;
                    }
                    throw new Error('Invalid server response');
                }

                if (response.ok) {
                    showMinimalPopup('Berhasil', result.message || 'User berhasil dihapus', 'success');
                    closeModal('deleteUserModal');

                    // Reload halaman setelah 1.5 detik
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);

                } else {
                    showMinimalPopup('Error', result.message || 'Gagal menghapus user', 'error');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                showMinimalPopup('Error', 'Terjadi kesalahan: ' + error.message, 'error');
            } finally {
                // Reset state
                userToDelete = null;

                // Re-enable tombol
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = originalText;
                }
            }
        }

        // ==================== UPDATE EVENT LISTENERS ====================
        document.addEventListener('DOMContentLoaded', function() {
            // ... kode yang sudah ada ...

            // Tambahkan event listener untuk tombol confirm delete
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', handleDeleteUser);
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const deleteModal = document.getElementById('deleteUserModal');
                if (event.target === deleteModal) {
                    closeModal('deleteUserModal');
                    userToDelete = null; // Reset
                }
            });
        });
    </script>

</body>

</html>
