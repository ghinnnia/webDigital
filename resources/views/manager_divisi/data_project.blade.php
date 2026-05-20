<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Project</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

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
                        display: ["Inter", "sans-serif"],
                        body: ["Inter", "sans-serif"],
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
            font-family: "Inter", sans-serif;
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

        .status-pending {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .status-proses {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-dalam-pengerjaan {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .status-selesai {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-dibatalkan {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
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

        /* Gaya untuk indikator aktif/hover */
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
        <!-- Menggunakan template header -->
        @include('manager_divisi/templet/sider')
        <div class="flex-1 flex flex-col main-content">
            <div class="flex-1 p-3 sm:p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold mb-4 md:mb-0">Data Project</h2>
                </div>

                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span
                            class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                            placeholder="Cari nama project atau deskripsi..." type="text" />
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
                                    <label for="filterAll">Semua Status</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterPending" value="pending">
                                    <label for="filterPending">Pending</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterDalamPengerjaan" value="dalam_pengerjaan">
                                    <label for="filterDalamPengerjaan">Dalam Pengerjaan</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterSelesai" value="selesai">
                                    <label for="filterSelesai">Selesai</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterDibatalkan" value="dibatalkan">
                                    <label for="filterDibatalkan">Dibatalkan</label>
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
                            <span class="material-icons-outlined text-primary">assignment</span>
                            Data Project (Milik Saya)
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount"
                                    class="font-semibold text-text-light">{{ $projects->count() }}</span> project</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- INFO PANEL UNTUK MANAGER DIVISI -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <span class="material-icons-outlined text-blue-500 mr-3">info</span>
                                <div>
                                    <h4 class="font-semibold text-blue-800 mb-1">Project Anda</h4>
                                    <p class="text-blue-700 text-sm">
                                        Berikut adalah daftar project yang telah ditetapkan kepada Anda sebagai
                                        penanggung jawab oleh General Manager.
                                        Anda hanya dapat mengupdate <strong>progres</strong> dan <strong>status</strong>
                                        project.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- SCROLLABLE TABLE -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 200px;">Nama Project</th>
                                            <th style="min-width: 250px;">Deskripsi</th>
                                            <th style="min-width: 150px;">Harga</th>
                                            <th style="min-width: 150px;">Periode Pengerjaan</th>
                                            <th style="min-width: 150px;">Progres</th>
                                            <th style="min-width: 150px;">Status</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        @forelse ($projects as $index => $project)
                                            <tr class="orderan-row" data-status="{{ strtolower($project->status_pengerjaan) }}"
                                                data-nama="{{ strtolower($project->nama) }}"
                                                data-deskripsi="{{ strtolower($project->deskripsi) }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $project->nama }}</td>
                                                <td class="truncate max-w-xs">{{ $project->deskripsi }}</td>
                                                <td>Rp {{ number_format($project->harga, 0, ',', '.') }}</td>
                                                <td style="min-width: 180px;">{{ $project->tanggal_mulai_pengerjaan ? $project->tanggal_mulai_pengerjaan->format('Y-m-d') : '-' }} — {{ $project->tanggal_selesai_pengerjaan ? $project->tanggal_selesai_pengerjaan->format('Y-m-d') : '-' }}</td>
                                                <td>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="bg-primary h-2 rounded-full"
                                                                style="width: {{ $project->progres }}%"></div>
                                                        </div>
                                                        <span
                                                            class="text-sm font-medium whitespace-nowrap">{{ $project->progres }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="status-badge status-{{ strtolower(str_replace('_', '-', $project->status_pengerjaan)) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $project->status_pengerjaan)) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button
                                                        class="edit-btn p-2 rounded-full hover:bg-primary/10 transition-colors"
                                                        data-id="{{ $project->id }}"
                                                        data-nama="{{ $project->nama }}"
                                                        data-harga="{{ $project->harga }}"
                                                        data-deadline="{{ $project->deadline }}"
                                                        data-progres="{{ $project->progres }}"
                                                        data-status="{{ strtolower($project->status_pengerjaan) }}"
                                                        data-tanggal_mulai="{{ $project->tanggal_mulai_pengerjaan ? $project->tanggal_mulai_pengerjaan->format('Y-m-d') : '' }}"
                                                        data-tanggal_selesai="{{ $project->tanggal_selesai_pengerjaan ? $project->tanggal_selesai_pengerjaan->format('Y-m-d') : '' }}"
                                                        title="Update Progres, Status & Periode Pengerjaan">
                                                        <span
                                                            class="material-icons-outlined text-primary text-lg">trending_up</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-gray-500">
                                                    Anda belum memiliki project. Project akan muncul setelah ditugaskan
                                                    oleh General Manager.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4">
                            @foreach ($projects as $index => $project)
                                <div class="bg-white rounded-lg border p-4 shadow-sm orderan-card">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold">{{ $project->nama }}</h4>
                                            <p class="text-sm text-gray-500">
                                                Rp {{ number_format($project->harga, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <span class="status-badge status-{{ strtolower(str_replace('_', '-', $project->status_pengerjaan)) }}">
                                            {{ ucfirst(str_replace('_', ' ', $project->status_pengerjaan)) }}
                                        </span>
                                    </div>

                                    <p class="text-sm mb-2">{{ $project->deskripsi }}</p>

                                    <div class="text-sm mb-2">
                                        Deadline: {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                                    </div>

                                    <div class="mb-3">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span>Progres</span>
                                            <span class="font-medium">{{ $project->progres }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-300 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full"
                                                style="width: {{ $project->progres }}%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button
                                            class="edit-btn w-full py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
                                            data-id="{{ $project->id }}" data-nama="{{ $project->nama }}"
                                            data-harga="{{ $project->harga }}"
                                            data-deadline="{{ $project->deadline }}"
                                            data-progres="{{ $project->progres }}"
                                            data-status="{{ strtolower($project->status_pengerjaan) }}">
                                            <span class="material-icons-outlined mr-2 align-middle">edit</span>
                                            Update Progres
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($projects->hasPages())
                            <div id="paginationContainer" class="desktop-pagination">
                                @if ($projects->onFirstPage())
                                    <button class="desktop-nav-btn" disabled>
                                        <span class="material-icons-outlined text-sm">chevron_left</span>
                                    </button>
                                @else
                                    <a href="{{ $projects->previousPageUrl() }}" class="desktop-nav-btn">
                                        <span class="material-icons-outlined text-sm">chevron_left</span>
                                    </a>
                                @endif

                                <div id="pageNumbers" class="flex gap-1">
                                    @for ($i = 1; $i <= $projects->lastPage(); $i++)
                                        <a href="{{ $projects->url($i) }}"
                                            class="desktop-page-btn {{ $i == $projects->currentPage() ? 'active' : '' }}">
                                            {{ $i }}
                                        </a>
                                    @endfor
                                </div>

                                @if ($projects->hasMorePages())
                                    <a href="{{ $projects->nextPageUrl() }}" class="desktop-nav-btn">
                                        <span class="material-icons-outlined text-sm">chevron_right</span>
                                    </a>
                                @else
                                    <button class="desktop-nav-btn" disabled>
                                        <span class="material-icons-outlined text-sm">chevron_right</span>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </div>
    </div>

    <!-- Modal Edit untuk Manager Divisi (PROGRES, STATUS & PERIODE PENGERJAAN) -->
    <div id="editOrderanModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Update Project</h3>
                    <button id="closeEditModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>

                <!-- INFO PROJECT (READONLY) -->
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 gap-2">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nama Project:</span>
                            <span id="editNamaDisplay" class="text-sm font-semibold text-gray-700 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Deadline:</span>
                            <span id="editDeadlineDisplay" class="text-sm font-semibold text-gray-700 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Harga:</span>
                            <span id="editHargaDisplay" class="text-sm font-semibold text-gray-700 ml-2"></span>
                        </div>
                    </div>
                </div>

                <!-- GANTI MENJADI METHOD POST BIASA -->
                <form method="POST" id="editOrderanForm"
                    action="{{ route('manager_divisi.data_project.update', ['id' => '__ID__']) }}">
                    @csrf
                    <input type="hidden" id="editId" name="id">

                    <!-- Field untuk progres -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Progres (%) *</label>
                        <input type="number" id="editProgres" name="progres" min="0" max="100"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Masukkan progres (0-100)" required>
                        <div class="mt-1">
                            <input type="range" id="progresSlider" min="0" max="100" value="0"
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>0%</span>
                            <span id="progresValue">0%</span>
                            <span>100%</span>
                        </div>
                    </div>

                    <!-- Field untuk status -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select id="editStatus" name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            required>
                            <option value="">Pilih Status</option>
                            <option value="pending">Pending</option>
                            <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                        <div class="mt-2 flex gap-2 flex-wrap">
                            <button type="button"
                                class="status-quick-btn px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 transition-colors"
                                data-status="pending">Pending</button>
                            <button type="button"
                                class="status-quick-btn px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                                data-status="dalam_pengerjaan">Dalam Pengerjaan</button>
                            <button type="button"
                                class="status-quick-btn px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors"
                                data-status="selesai">Selesai</button>
                            <button type="button"
                                class="status-quick-btn px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
                                data-status="dibatalkan">Dibatalkan</button>
                        </div>
                    </div>

                    <!-- Field untuk periode pengerjaan -->
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Pengerjaan</label>
                            <input type="date" id="editTanggalMulai" name="tanggal_mulai_pengerjaan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Pengerjaan</label>
                            <input type="date" id="editTanggalSelesai" name="tanggal_selesai_pengerjaan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <!-- Keterangan tambahan -->
                    <div class="mb-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">
                            <span class="material-icons-outlined text-sm align-middle mr-1">info</span>
                            Sebagai Manager Divisi, Anda dapat mengupdate <strong>progres</strong>, <strong>status</strong>, dan <strong>periode pengerjaan</strong> project.
                        </p>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="batalEditBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Minimalist Popup -->
    <div id="minimalPopup" class="minimal-popup hidden">
        <div class="minimal-popup-icon">
            <span class="material-icons-outlined">check</span>
        </div>
        <div class="minimal-popup-content">
            <div id="popupTitle" class="minimal-popup-title">Berhasil</div>
            <div id="popupMessage" class="minimal-popup-message">Operasi berhasil dilakukan</div>
        </div>
        <button id="popupCloseBtn" class="minimal-popup-close">
            <span class="material-icons-outlined text-sm">close</span>
        </button>
    </div>

    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === MODAL FUNCTIONS ===
            const editOrderanModal = document.getElementById('editOrderanModal');
            const editOrderanForm = document.getElementById('editOrderanForm');
            const batalEditBtn = document.getElementById('batalEditBtn');
            const closeEditModalBtn = document.getElementById('closeEditModalBtn');
            const progresSlider = document.getElementById('progresSlider');
            const progresInput = document.getElementById('editProgres');
            const progresValue = document.getElementById('progresValue');

            // Handle edit button clicks
 // Di JavaScript
// Handle edit button clicks
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Ambil semua data attribute
        const id = this.getAttribute('data-id');
        const nama = this.getAttribute('data-nama');
        const harga = this.getAttribute('data-harga');
        const deadline = this.getAttribute('data-deadline');
        const progres = this.getAttribute('data-progres');
        const status = this.getAttribute('data-status');
        const tanggal_mulai = this.getAttribute('data-tanggal_mulai');
        const tanggal_selesai = this.getAttribute('data-tanggal_selesai');
        
        // DEBUG: Log semua data
        console.log('Button clicked with data:', {
            id, nama, harga, deadline, progres, status, tanggal_mulai, tanggal_selesai
        });
        
        // VALIDASI: Pastikan ID ada
        if (!id) {
            console.error('No ID found on button!');
            showMinimalPopup('Error', 'Data project tidak valid.', 'error');
            return;
        }
        
        // Set values for modal
        document.getElementById('editId').value = id;
        
        // Display project info (readonly)
        document.getElementById('editNamaDisplay').textContent = nama || 'Tidak ada data';
        
        if (harga) {
            document.getElementById('editHargaDisplay').textContent = 'Rp ' + 
                parseInt(harga).toLocaleString('id-ID');
        } else {
            document.getElementById('editHargaDisplay').textContent = 'Rp 0';
        }
        
        // Format deadline
        if (deadline) {
            try {
                const deadlineDate = new Date(deadline);
                if (!isNaN(deadlineDate.getTime())) {
                    const formattedDeadline = deadlineDate.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    document.getElementById('editDeadlineDisplay').textContent = formattedDeadline;
                } else {
                    document.getElementById('editDeadlineDisplay').textContent = deadline;
                }
            } catch (e) {
                document.getElementById('editDeadlineDisplay').textContent = deadline || 'Tidak ada deadline';
            }
        }
        
        // Set editable fields dengan validasi
        const progresValue = progres || '0';
        document.getElementById('editProgres').value = progresValue;
        
        const slider = document.getElementById('progresSlider');
        const valueDisplay = document.getElementById('progresValue');
        if (slider) slider.value = progresValue;
        if (valueDisplay) valueDisplay.textContent = progresValue + '%';
        
        document.getElementById('editStatus').value = status || 'pending';
        
        // Set tanggal mulai dan selesai
        document.getElementById('editTanggalMulai').value = tanggal_mulai || '';
        document.getElementById('editTanggalSelesai').value = tanggal_selesai || '';

        // Show modal
        editOrderanModal.classList.remove('hidden');
    });
});

            // Close edit modal
            batalEditBtn.addEventListener('click', function() {
                editOrderanModal.classList.add('hidden');
            });

            closeEditModalBtn.addEventListener('click', function() {
                editOrderanModal.classList.add('hidden');
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === editOrderanModal) {
                    editOrderanModal.classList.add('hidden');
                }
            });

            // Handle progres slider
            if (progresSlider && progresInput && progresValue) {
                // Update slider saat input berubah
                progresInput.addEventListener('input', function() {
                    let value = parseInt(this.value);
                    if (isNaN(value)) value = 0;
                    if (value < 0) value = 0;
                    if (value > 100) value = 100;

                    this.value = value;
                    progresSlider.value = value;
                    progresValue.textContent = value + '%';
                });

                // Update input saat slider digeser
                progresSlider.addEventListener('input', function() {
                    progresInput.value = this.value;
                    progresValue.textContent = this.value + '%';
                });
            }

            // Handle quick status buttons
            document.querySelectorAll('.status-quick-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    document.getElementById('editStatus').value = status;

                    // Auto-set progres berdasarkan status
                    if (progresInput && progresSlider && progresValue) {
                        if (status === 'pending') {
                            progresInput.value = 0;
                            progresSlider.value = 0;
                            progresValue.textContent = '0%';
                        } else if (status === 'dalam_pengerjaan') {
                            // Jika kurang dari 50%, set ke 50%
                            if (parseInt(progresInput.value) < 50) {
                                progresInput.value = 50;
                                progresSlider.value = 50;
                                progresValue.textContent = '50%';
                            }
                        } else if (status === 'selesai') {
                            progresInput.value = 100;
                            progresSlider.value = 100;
                            progresValue.textContent = '100%';
                        } else if (status === 'dibatalkan') {
                            // Dibatalkan bisa tetap dengan progres yang sekarang
                            // atau bisa di-reset, tergantung kebutuhan
                        }
                    }
                });
            });

            // Handle form submission
// Handle form submission
editOrderanForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = document.getElementById('editId').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // VALIDASI: Pastikan ID tidak kosong
    if (!id) {
        console.error('ID is empty!');
        showMinimalPopup('Error', 'ID project tidak ditemukan. Silakan refresh halaman.', 'error');
        return;
    }
    
    // Debug data yang akan dikirim
    console.log('Data to send:', {
        id: id,
        progres: formData.get('progres'),
        status: formData.get('status'),
        _token: csrfToken
    });
    
    // GUNAKAN URL YANG BENAR
    const url = `/manager-divisi/data_project/${id}/update`;
    console.log('Request URL:', url);
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response URL:', response.url);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Server error response:', text);
                let errorData;
                try {
                    errorData = JSON.parse(text);
                } catch {
                    errorData = { message: text || 'Unknown error' };
                }
                throw new Error(errorData.message || 'Server error: ' + response.status);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success data:', data);
        if (data.success) {
            showMinimalPopup('Berhasil', data.message, 'success');
            editOrderanModal.classList.add('hidden');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showMinimalPopup('Error', data.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showMinimalPopup('Error', error.message || 'Terjadi kesalahan. Silakan coba lagi.', 'error');
    });
});

            // === FILTER FUNCTIONS ===
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const applyFilterBtn = document.getElementById('applyFilter');
            const resetFilterBtn = document.getElementById('resetFilter');
            const filterAll = document.getElementById('filterAll');
            const searchInput = document.getElementById('searchInput');
            const orderanRows = document.querySelectorAll('.orderan-row');
            const orderanCards = document.querySelectorAll('.orderan-card');
            const totalCount = document.getElementById('totalCount');

            if (filterBtn && filterDropdown) {
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
                            // Uncheck all other checkboxes
                            document.querySelectorAll(
                                    '.filter-option input[type="checkbox"]:not(#filterAll)')
                                .forEach(cb => {
                                    cb.checked = false;
                                });
                        }
                    });
                }

                // Handle other checkboxes
                document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                    cb.addEventListener('change', function() {
                        if (this.checked && filterAll) {
                            // Uncheck "All" checkbox
                            filterAll.checked = false;
                        }
                    });
                });

                // Apply filter
                if (applyFilterBtn) {
                    applyFilterBtn.addEventListener('click', function() {
                        const filterAll = document.getElementById('filterAll');
                        const filterPending = document.getElementById('filterPending');
                        const filterDalamPengerjaan = document.getElementById('filterDalamPengerjaan');
                        const filterSelesai = document.getElementById('filterSelesai');
                        const filterDibatalkan = document.getElementById('filterDibatalkan');

                        let activeFilters = [];
                        if (filterAll && filterAll.checked) {
                            activeFilters.push('all');
                        } else {
                            if (filterPending && filterPending.checked) activeFilters.push('pending');
                            if (filterDalamPengerjaan && filterDalamPengerjaan.checked) activeFilters.push('dalam_pengerjaan');
                            if (filterSelesai && filterSelesai.checked) activeFilters.push('selesai');
                            if (filterDibatalkan && filterDibatalkan.checked) activeFilters.push('dibatalkan');
                        }

                        applyFilters(activeFilters);
                        filterDropdown.classList.remove('show');
                        showMinimalPopup('Filter Diterapkan',
                            `Menampilkan ${totalCount.textContent} project`, 'success');
                    });
                }

                // Reset filter
                if (resetFilterBtn) {
                    resetFilterBtn.addEventListener('click', function() {
                        if (filterAll) filterAll.checked = true;
                        const filterPending = document.getElementById('filterPending');
                        const filterDalamPengerjaan = document.getElementById('filterDalamPengerjaan');
                        const filterSelesai = document.getElementById('filterSelesai');
                        const filterDibatalkan = document.getElementById('filterDibatalkan');

                        if (filterPending) filterPending.checked = false;
                        if (filterDalamPengerjaan) filterDalamPengerjaan.checked = false;
                        if (filterSelesai) filterSelesai.checked = false;
                        if (filterDibatalkan) filterDibatalkan.checked = false;

                        applyFilters(['all']);
                        filterDropdown.classList.remove('show');
                        showMinimalPopup('Filter Direset', 'Menampilkan semua project', 'success');
                    });
                }
            }

            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.trim().toLowerCase();
                    applyFilters(null, searchTerm);
                });
            }

            function applyFilters(activeFilters = ['all'], searchTerm = '') {
                let visibleCount = 0;

                // Apply filters to rows
                orderanRows.forEach(row => {
                    const status = row.getAttribute('data-status').toLowerCase();
                    const nama = row.getAttribute('data-nama').toLowerCase();
                    const deskripsi = row.getAttribute('data-deskripsi').toLowerCase();

                    // Check if status matches filter
                    let statusMatches = false;
                    if (activeFilters && activeFilters.includes('all')) {
                        statusMatches = true;
                    } else if (activeFilters) {
                        statusMatches = activeFilters.some(filter => status.includes(filter.toLowerCase()));
                    } else {
                        statusMatches = true;
                    }

                    // Check if search term matches
                    let searchMatches = true;
                    if (searchTerm) {
                        searchMatches = nama.includes(searchTerm) ||
                            deskripsi.includes(searchTerm) ||
                            status.includes(searchTerm);
                    }

                    if (statusMatches && searchMatches) {
                        row.classList.remove('hidden-by-filter');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden-by-filter');
                    }
                });

                // Apply filters to cards
                orderanCards.forEach(card => {
                    const cardText = card.textContent.toLowerCase();

                    // For cards, we'll just use search
                    if (searchTerm && !cardText.includes(searchTerm)) {
                        card.classList.add('hidden-by-filter');
                    } else {
                        card.classList.remove('hidden-by-filter');
                    }
                });

                // Update total count
                if (totalCount) {
                    totalCount.textContent = visibleCount;
                }
            }

            // === MINIMAL POPUP ===
            function showMinimalPopup(title, message, type = 'success') {
                const popup = document.getElementById('minimalPopup');
                const popupTitle = document.getElementById('popupTitle');
                const popupMessage = document.getElementById('popupMessage');
                const popupIcon = popup.querySelector('.minimal-popup-icon span');
                const popupCloseBtn = document.getElementById('popupCloseBtn');

                if (!popup) return;

                // Set content
                popupTitle.textContent = title;
                popupMessage.textContent = message;

                // Set type
                popup.className = 'minimal-popup show';
                if (type === 'error') {
                    popup.classList.add('error');
                    popupIcon.textContent = 'error';
                } else if (type === 'warning') {
                    popup.classList.add('warning');
                    popupIcon.textContent = 'warning';
                } else {
                    popup.classList.add('success');
                    popupIcon.textContent = 'check';
                }

                // Auto hide after 3 seconds
                const autoHide = setTimeout(() => {
                    popup.classList.remove('show');
                }, 3000);

                // Manual close
                popupCloseBtn.onclick = () => {
                    clearTimeout(autoHide);
                    popup.classList.remove('show');
                };
            }
        });
    </script>
</body>

</html>
