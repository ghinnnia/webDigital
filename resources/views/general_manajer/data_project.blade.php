<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Project - Dashboard</title>
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
        
        .status-todo {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }
        
        .status-progress {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        
        .status-done {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .status-active {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .status-inprogress {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        
        .status-Pending {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }
        
        .status-DalamPengerjaan {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .status-Selesai {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .status-pending {
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
        
        .status-Selesai {
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
                margin-left: 256px; /* Lebar sidebar */
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
        
        /* SCROLLABLE TABLE - TANPA TEKS SCROLL */
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
            min-width: 1400px; /* Fixed minimum width */
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
        
        /* Progress bar styles */
        .progress-bar {
            width: 100%;
            background-color: #e2e8f0;
            border-radius: 9999px;
            height: 8px;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 9999px;
        }
        
        /* Truncate text style */
        .truncate-text {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
       @include('general_manajer/templet/header')
        
        <!-- Main Content Container -->
        <div class="main-content flex-1 flex flex-col overflow-y-auto bg-background-light">
            <main class="flex-1 flex flex-col bg-background-light">
                <div class="flex-1 p-3 sm:p-8">

                    <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Data Project - Penetapan Penanggung Jawab</h2>
                    
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <form method="GET" action="{{ route('general_manajer.data_project') }}" class="relative w-full md:w-1/3">
        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            search
        </span>
        <input
            id="searchInput"
            name="search"
            value="{{ request('search') }}"
            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
            placeholder="Cari nama project, penanggung jawab..."
            type="text"
        />
    </form>
</div>
                    
                    <!-- Data Table Panel -->
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">view_list</span>
                                Daftar Project
                            </h3>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-text-muted-light">Total: <span class="font-semibold text-text-light">{{ $projects->total() }}</span> project</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <!-- INFO PANEL UNTUK GENERAL MANAGER -->
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <span class="material-icons-outlined text-blue-500 mr-3">info</span>
                                    <div>
                                        <h4 class="font-semibold text-blue-800 mb-1">Fitur Penetapan Penanggung Jawab</h4>
                                        <p class="text-blue-700 text-sm">
                                            Sebagai General Manager, Anda dapat menetapkan <strong>Manager Divisi</strong> sebagai penanggung jawab project dengan mengklik tombol <span class="material-icons-outlined align-middle text-sm">edit</span> Edit.
                                            Project yang sudah ditetapkan penanggung jawabnya akan muncul di dashboard Manager Divisi terkait.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SCROLLABLE TABLE - TANPA INDICATOR -->
                            <div class="desktop-table">
                                <div class="scrollable-table-container table-shadow" id="scrollableTable">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 60px;">No</th>
                                                <th style="min-width: 200px;">Nama Project</th>
                                                <th style="min-width: 250px;">Deskripsi</th>
                                                <th style="min-width: 120px;">Harga</th>
                                                <th style="min-width: 180px;">Periode Pengerjaan</th>
                                                <th style="min-width: 180px;">Penanggung Jawab</th>
                                                <th style="min-width: 150px;">Progres</th>
                                                <th style="min-width: 120px;">Status</th>
                                                <th style="min-width: 120px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="desktopTableBody">
                                            @foreach($projects as $index => $item)
                                                <tr>
                                                    <td style="min-width: 60px;">{{ ($projects->currentPage() - 1) * $projects->perPage() + $index + 1 }}</td>
                                                    <td style="min-width: 200px;">{{ $item->nama }}</td>
                                                    <td style="min-width: 250px;" class="truncate-text" title="{{ $item->deskripsi }}">
                                                        {{ Str::limit($item->deskripsi, 40) }}
                                                    </td>
                                                    <td style="min-width: 120px;">{{ $item->harga }}</td>
                                                    <td style="min-width: 180px;">{{ $item->tanggal_mulai_pengerjaan ? $item->tanggal_mulai_pengerjaan->format('Y-m-d') : '-' }} — {{ $item->tanggal_selesai_pengerjaan ? $item->tanggal_selesai_pengerjaan->format('Y-m-d') : '-' }}</td>
                                                    <td style="min-width: 180px;">
                                                        @php
                                                            $managerIds = collect($item->penanggung_jawab_ids ?? [])
                                                                ->filter(fn($id) => !is_null($id) && $id !== '')
                                                                ->map(fn($id) => (int) $id)
                                                                ->values();
                                                            if ($managerIds->isEmpty() && $item->penanggung_jawab_id) {
                                                                $managerIds = collect([(int) $item->penanggung_jawab_id]);
                                                            }
                                                            $managerPjs = $managers->whereIn('id', $managerIds)->values();

                                                            $karyawanIds = collect($item->karyawan_penanggung_jawab_ids ?? [])
                                                                ->filter(fn($id) => !is_null($id) && $id !== '')
                                                                ->map(fn($id) => (int) $id)
                                                                ->values();
                                                            if ($karyawanIds->isEmpty() && $item->karyawan_penanggung_jawab_id) {
                                                                $karyawanIds = collect([(int) $item->karyawan_penanggung_jawab_id]);
                                                            }
                                                            $karyawanPjs = $karyawans->whereIn('id', $karyawanIds)->values();
                                                        @endphp
                                                        @if($managerPjs->isNotEmpty() || $item->penanggungJawab || $karyawanPjs->isNotEmpty() || $item->karyawanPenanggungJawab)
                                                            <div class="flex flex-col gap-2">
                                                                @if($managerPjs->isNotEmpty())
                                                                    @foreach($managerPjs as $managerItem)
                                                                        <div class="flex items-center gap-2">
                                                                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                                                                <span class="material-icons-outlined text-primary text-sm">person</span>
                                                                            </div>
                                                                            <div>
                                                                                <div class="font-medium text-sm">{{ $managerItem->name }}</div>
                                                                                <div class="text-xs text-gray-500">Manager - {{ optional($managerItem->divisi)->divisi ?? '-' }}</div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @elseif($item->penanggungJawab)
                                                                    <div class="flex items-center gap-2">
                                                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                                                            <span class="material-icons-outlined text-primary text-sm">person</span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="font-medium text-sm">{{ $item->penanggungJawab->name }}</div>
                                                                            <div class="text-xs text-gray-500">Manager - {{ optional($item->penanggungJawab->divisi)->divisi ?? '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if($karyawanPjs->isNotEmpty())
                                                                    @foreach($karyawanPjs as $karyawanItem)
                                                                        <div class="flex items-center gap-2">
                                                                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                                                <span class="material-icons-outlined text-indigo-600 text-sm">badge</span>
                                                                            </div>
                                                                            <div>
                                                                                <div class="font-medium text-sm">{{ $karyawanItem->name }}</div>
                                                                                <div class="text-xs text-gray-500">Karyawan - {{ optional($karyawanItem->divisi)->divisi ?? '-' }}</div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @elseif($item->karyawanPenanggungJawab)
                                                                    <div class="flex items-center gap-2">
                                                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                                            <span class="material-icons-outlined text-indigo-600 text-sm">badge</span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="font-medium text-sm">{{ $item->karyawanPenanggungJawab->name }}</div>
                                                                            <div class="text-xs text-gray-500">Karyawan - {{ optional($item->karyawanPenanggungJawab->divisi)->divisi ?? '-' }}</div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400 text-sm italic">Belum ditetapkan</span>
                                                        @endif
                                                    </td>
                                                    <td style="min-width: 150px;">
                                                        <div class="progress-bar">
                                                            <div class="progress-fill {{ $item->progres < 50 ? 'bg-red-500' : ($item->progres < 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $item->progres }}%"></div>
                                                        </div>
                                                        <span class="text-xs text-gray-600 dark:text-gray-400 mt-1 block">{{ $item->progres }}%</span>
                                                    </td>
                                                    <td style="min-width: 120px;">
                                                        <span class="status-badge 
                                                            @if($item->status_pengerjaan == 'pending') status-pending 
                                                            @elseif($item->status_pengerjaan == 'dalam_pengerjaan') status-dalam-pengerjaan 
                                                            @elseif($item->status_pengerjaan == 'selesai') status-selesai 
                                                            @elseif($item->status_pengerjaan == 'dibatalkan') status-dibatalkan
                                                            @else status-todo @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $item->status_pengerjaan)) }}
                                                        </span>
                                                    </td>
                                                    <td style="min-width: 120px; text-align: center;">
                                                        <div class="flex justify-center gap-2">
                                                            <button class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                                                onclick="openEditModal(
                                                                    {{ $item->id }}, 
                                                                    '{{ addslashes($item->nama) }}', 
                                                                    '{{ addslashes($item->deskripsi) }}', 
                                                                    '{{ $item->harga }}', 
                                                                    '{{ $item->tanggal_mulai_pengerjaan ? $item->tanggal_mulai_pengerjaan->format('Y-m-d') : '' }}',
                                                                    '{{ $item->tanggal_selesai_pengerjaan ? $item->tanggal_selesai_pengerjaan->format('Y-m-d') : '' }}',
                                                                    '{{ $item->tanggal_mulai_kerjasama ? $item->tanggal_mulai_kerjasama->format('Y-m-d') : '' }}',
                                                                    '{{ $item->tanggal_selesai_kerjasama ? $item->tanggal_selesai_kerjasama->format('Y-m-d') : '' }}',
                                                                    '{{ $item->status_pengerjaan }}', 
                                                                    '{{ $item->status_kerjasama }}',
                                                                    {{ $item->progres }}, 
                                                                    {{ $item->penanggung_jawab_id ?? 'null' }},
                                                                    {{ $item->karyawan_penanggung_jawab_id ?? 'null' }},
                                                                    @json($item->penanggung_jawab_ids ?? []),
                                                                    @json($item->karyawan_penanggung_jawab_ids ?? [])
                                                                )"
                                                                title="Tentukan Penanggung Jawab">
                                                                <span class="material-icons-outlined">edit</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Desktop Pagination -->
                            @if($projects->lastPage() > 1)
                            <div class="desktop-pagination">
                                <button class="desktop-nav-btn" @if($projects->currentPage() == 1) disabled @endif onclick="window.location.href='{{ $projects->previousPageUrl() }}'">
                                    <span class="material-icons-outlined text-sm">chevron_left</span>
                                </button>
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= $projects->lastPage(); $i++)
                                        <button class="desktop-page-btn {{ $i == $projects->currentPage() ? 'active' : '' }}" 
                                            onclick="window.location.href='{{ $projects->url($i) }}'">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <button class="desktop-nav-btn" @if($projects->currentPage() == $projects->lastPage()) disabled @endif onclick="window.location.href='{{ $projects->nextPageUrl() }}'">
                                    <span class="material-icons-outlined text-sm">chevron_right</span>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                    Copyright ©2025 by digicity.id
                </footer>
            </main>
        </div>
    </div>

    <!-- Modal Edit untuk Menetapkan Penanggung Jawab -->
    <div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tetapkan Penanggung Jawab Project</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editForm" action="{{ route('general_manajer.data_project.update', '') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editId">
                    
                    <!-- Informasi Project (Readonly) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Project</label>
                        <input type="text" id="editNamaDisplay" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700" readonly>
                        <input type="hidden" name="nama" id="editNama">
                    </div>
                    
                   
                    
                    <!-- Field untuk mengubah penanggung jawab -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Pilih Manager Divisi (Bisa Lebih dari 1)</label>
                        <input type="hidden" name="penanggung_jawab_id" id="editPenanggungJawabPrimary">
                        <input
                            type="text"
                            id="managerSearchInput"
                            class="w-full px-3 py-2 mb-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Search manager..."
                        >
                        <div id="editPenanggungJawabList"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg max-h-[180px] overflow-y-auto space-y-2">
                            @foreach($managers as $manager)
                                @php
                                    $id = data_get($manager, 'id', '');
                                    $name = data_get($manager, 'name', 'Nama tidak tersedia');
                                    $email = data_get($manager, 'email', 'Email tidak tersedia');
                                    $divisi_name = optional($manager->divisi)->divisi;
                                @endphp
                                
                                @if($id && $name)
                                    <label class="manager-item flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" name="penanggung_jawab_ids[]" value="{{ $id }}"
                                            class="manager-checkbox mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="text-sm text-gray-700">
                                            {{ $name }}
                                            @if($divisi_name)
                                                - Divisi {{ $divisi_name }}
                                            @endif
                                            @if($email)
                                                ({{ $email }})
                                            @endif
                                        </span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                        <p id="managerSearchEmpty" class="text-xs text-red-500 mt-1 hidden">Manager tidak ditemukan.</p>
                        <p class="text-xs text-gray-400 mt-1">Centang satu atau lebih manager divisi.</p>
                    </div>

                    <!-- Dropdown Karyawan (Muncul saat Manager Divisi dipilih) -->
                    <div class="mb-6 hidden" id="karyawanDivisiWrapper">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Penanggung Jawab Karyawan (Bisa Lebih dari 1)</label>
                        <input type="hidden" name="karyawan_penanggung_jawab_id" id="editKaryawanPenanggungJawabPrimary">
                        <input
                            type="text"
                            id="karyawanSearchInput"
                            class="w-full px-3 py-2 mb-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Search karyawan..."
                        >
                        <div id="karyawanDivisiList"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg max-h-[180px] overflow-y-auto space-y-2">
                        </div>
                        <p id="karyawanSearchEmpty" class="text-xs text-red-500 mt-1 hidden">Karyawan tidak ditemukan.</p>
                        <p class="text-xs text-gray-400 mt-1">Centang satu atau lebih karyawan dari divisi manager yang dipilih.</p>
                    </div>
                    
                    <!-- Fields hidden lainnya (tetap dikirim tapi tidak bisa diubah) -->
                    <input type="hidden" name="deskripsi" id="editDeskripsi">
                    <input type="hidden" name="status_kerjasama" id="editStatusKerjasama" value="aktif">
                    
                    <div class="flex justify-end gap-2">
                        <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Tetapkan Penanggung Jawab</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-20 opacity-0 flex items-center">
        <span id="toastMessage" class="mr-2"></span>
        <button id="closeToast" class="ml-2 text-white hover:text-gray-200">
            <span class="material-icons-outlined">close</span>
        </button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal elements
        const editModal = document.getElementById('editModal');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        
        // Buttons
        const closeModals = document.querySelectorAll('.close-modal');
        const closeToastBtn = document.getElementById('closeToast');
        
        // Forms
        const editForm = document.getElementById('editForm');
        
        // Close modals
        closeModals.forEach(btn => {
            btn.addEventListener('click', function() {
                editModal.classList.add('hidden');
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editModal) {
                editModal.classList.add('hidden');
            }
        });
        
        // Handle edit form submission with AJAX
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedManagerIds = Array.from(document.querySelectorAll('input.manager-checkbox[name="penanggung_jawab_ids[]"]:checked'))
                .map(input => input.value)
                .filter(Boolean);

            if (selectedManagerIds.length === 0) {
                showToast('Pilih minimal 1 manager divisi.', 'error');
                return;
            }

            const primaryManagerInput = document.getElementById('editPenanggungJawabPrimary');
            if (primaryManagerInput) {
                primaryManagerInput.value = selectedManagerIds[0];
            }
            if (typeof window.syncPrimaryKaryawanSelection === 'function') {
                window.syncPrimaryKaryawanSelection();
            }
            
            const formData = new FormData(editForm);
            const id = document.getElementById('editId').value;
            
            fetch(`/general_manajer/data_project/${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        const error = new Error('Server responded with an error status');
                        error.response = response;
                        error.data = errorData;
                        throw error;
                    }).catch(() => {
                        const error = new Error('Server responded with an error status');
                        error.response = response;
                        throw error;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('Penanggung jawab berhasil ditetapkan!', 'success');
                    editModal.classList.add('hidden');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Terjadi kesalahan yang tidak diketahui.', 'error');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                handleFetchError(error);
            });
        });
        
        // Close toast notification
        closeToastBtn.addEventListener('click', function() {
            toast.classList.add('translate-y-20', 'opacity-0');
        });
        
        // Function to show toast notification
        function showToast(message, type = 'success') {
            toastMessage.textContent = message;
            
            // Change background color based on type
            toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg transform transition-transform duration-300 flex items-center`;
            if (type === 'error') {
                toast.classList.add('bg-red-500', 'text-white');
            } else {
                toast.classList.add('bg-green-500', 'text-white');
            }
            
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            // Auto hide after 5 seconds for error messages
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, type === 'error' ? 5000 : 3000);
        }
        
        // Function to handle fetch errors
        function handleFetchError(error) {
            if (error.response) {
                if (error.response.status === 422) {
                    let errorMessages = '';
                    if (error.data && error.data.errors) {
                        const errors = error.data.errors;
                        for (const field in errors) {
                            errorMessages += errors[field].join(', ') + ' ';
                        }
                        showToast('Validasi gagal: ' + errorMessages.trim(), 'error');
                    } else {
                        showToast('Data yang dimasukkan tidak valid.', 'error');
                    }
                } else if (error.response.status === 401) {
                    showToast('Anda belum login. Sesi mungkin telah habis.', 'error');
                } else if (error.response.status === 403) {
                    showToast('Anda tidak memiliki izin untuk melakukan aksi ini.', 'error');
                } else if (error.response.status === 404) {
                    showToast('Data tidak ditemukan atau endpoint tidak valid.', 'error');
                } else if (error.response.status >= 500) {
                    showToast('Terjadi kesalahan pada server. Periksa konsol browser untuk detail.', 'error');
                } else {
                    showToast(`Terjadi kesalahan (Status: ${error.response.status}).`, 'error');
                }
            } else if (error.request) {
                showToast('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.', 'error');
            } else {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        }

        const karyawanWrapper = document.getElementById('karyawanDivisiWrapper');
        const karyawanList = document.getElementById('karyawanDivisiList');
        const managerPrimaryInput = document.getElementById('editPenanggungJawabPrimary');
        const karyawanPrimaryInput = document.getElementById('editKaryawanPenanggungJawabPrimary');
        const managerCheckboxes = Array.from(document.querySelectorAll('input.manager-checkbox[name="penanggung_jawab_ids[]"]'));
        const managerSearchInput = document.getElementById('managerSearchInput');
        const karyawanSearchInput = document.getElementById('karyawanSearchInput');
        const managerSearchEmpty = document.getElementById('managerSearchEmpty');
        const karyawanSearchEmpty = document.getElementById('karyawanSearchEmpty');

        function getCheckedValues(selector) {
            return Array.from(document.querySelectorAll(selector))
                .filter(input => input.checked)
                .map(input => input.value)
                .filter(Boolean);
        }

        function getSelectedManagerIds() {
            return getCheckedValues('input.manager-checkbox[name="penanggung_jawab_ids[]"]');
        }

        function getSelectedKaryawanIds() {
            return getCheckedValues('#karyawanDivisiList input[name="karyawan_penanggung_jawab_ids[]"]');
        }

        function syncPrimaryKaryawan() {
            if (!karyawanPrimaryInput) return;
            const selectedKaryawanIds = getSelectedKaryawanIds();
            karyawanPrimaryInput.value = selectedKaryawanIds.length > 0 ? selectedKaryawanIds[0] : '';
        }

        function applySearchFilter(itemSelector, keyword, emptyElement) {
            const normalizedKeyword = String(keyword || '').toLowerCase().trim();
            const items = Array.from(document.querySelectorAll(itemSelector));

            if (items.length === 0) {
                if (emptyElement) emptyElement.classList.add('hidden');
                return;
            }

            let visibleCount = 0;
            items.forEach(item => {
                const text = (item.textContent || '').toLowerCase();
                const isVisible = normalizedKeyword === '' || text.includes(normalizedKeyword);
                item.classList.toggle('hidden', !isVisible);
                if (isVisible) visibleCount++;
            });

            if (emptyElement) {
                emptyElement.classList.toggle('hidden', visibleCount > 0 || normalizedKeyword === '');
            }
        }

        function renderKaryawanMessage(message) {
            if (!karyawanList) return;
            karyawanList.innerHTML = '';
            if (karyawanSearchEmpty) {
                karyawanSearchEmpty.classList.add('hidden');
            }
            const text = document.createElement('p');
            text.className = 'text-sm text-gray-500';
            text.textContent = message;
            karyawanList.appendChild(text);
        }

        async function loadKaryawanByManagers(managerIds, selectedKaryawanIds = []) {
            if (!Array.isArray(managerIds) || managerIds.length === 0) {
                if (karyawanWrapper) karyawanWrapper.classList.add('hidden');
                if (karyawanList) karyawanList.innerHTML = '';
                if (karyawanPrimaryInput) karyawanPrimaryInput.value = '';
                if (karyawanSearchEmpty) karyawanSearchEmpty.classList.add('hidden');
                return;
            }

            try {
                const selectedIds = Array.isArray(selectedKaryawanIds)
                    ? selectedKaryawanIds.map(id => String(id)).filter(Boolean)
                    : [];

                const responses = await Promise.all(managerIds.map(async (managerId) => {
                    const response = await fetch(`/general_manajer/data_project/karyawan-by-manager/${managerId}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (!data.success || !Array.isArray(data.data)) {
                        return [];
                    }
                    return data.data;
                }));

                const uniqueKaryawansMap = new Map();
                responses.flat().forEach((karyawan) => {
                    if (!karyawan || !karyawan.id) {
                        return;
                    }
                    const key = String(karyawan.id);
                    if (!uniqueKaryawansMap.has(key)) {
                        uniqueKaryawansMap.set(key, karyawan);
                    }
                });

                const uniqueKaryawans = Array.from(uniqueKaryawansMap.values());
                if (karyawanList) {
                    karyawanList.innerHTML = '';
                    if (uniqueKaryawans.length > 0) {
                        uniqueKaryawans.forEach(karyawan => {
                            const label = document.createElement('label');
                            label.className = 'karyawan-item flex items-start gap-2 cursor-pointer';

                            const input = document.createElement('input');
                            input.type = 'checkbox';
                            input.name = 'karyawan_penanggung_jawab_ids[]';
                            input.value = String(karyawan.id);
                            input.className = 'karyawan-checkbox mt-1 rounded border-gray-300 text-primary focus:ring-primary';
                            input.checked = selectedIds.includes(String(karyawan.id));

                            const text = document.createElement('span');
                            text.className = 'text-sm text-gray-700';
                            text.textContent = karyawan.name + (karyawan.email ? ` (${karyawan.email})` : '');

                            label.appendChild(input);
                            label.appendChild(text);
                            karyawanList.appendChild(label);
                        });
                    } else {
                        renderKaryawanMessage('Tidak ada karyawan di divisi manager terpilih');
                    }
                }

                if (karyawanWrapper) karyawanWrapper.classList.remove('hidden');
                syncPrimaryKaryawan();
                applySearchFilter(
                    '#karyawanDivisiList .karyawan-item',
                    karyawanSearchInput ? karyawanSearchInput.value : '',
                    karyawanSearchEmpty
                );
            } catch (err) {
                console.error('Error load karyawan:', err);
                if (karyawanWrapper) karyawanWrapper.classList.remove('hidden');
                renderKaryawanMessage('Gagal memuat karyawan');
                if (karyawanPrimaryInput) karyawanPrimaryInput.value = '';
            }
        }

        function syncPrimaryManagerAndKaryawan(selectedKaryawanIds = []) {
            const selectedManagerIds = getSelectedManagerIds();
            if (managerPrimaryInput) {
                managerPrimaryInput.value = selectedManagerIds.length > 0 ? selectedManagerIds[0] : '';
            }
            loadKaryawanByManagers(selectedManagerIds, selectedKaryawanIds);
        }

        managerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                syncPrimaryManagerAndKaryawan();
            });
        });

        if (managerSearchInput) {
            managerSearchInput.addEventListener('input', function() {
                applySearchFilter(
                    '#editPenanggungJawabList .manager-item',
                    managerSearchInput.value,
                    managerSearchEmpty
                );
            });
        }

        if (karyawanSearchInput) {
            karyawanSearchInput.addEventListener('input', function() {
                applySearchFilter(
                    '#karyawanDivisiList .karyawan-item',
                    karyawanSearchInput.value,
                    karyawanSearchEmpty
                );
            });
        }

        if (karyawanList) {
            karyawanList.addEventListener('change', function(event) {
                if (event.target && event.target.matches('input[name="karyawan_penanggung_jawab_ids[]"]')) {
                    syncPrimaryKaryawan();
                }
            });
        }

        window.loadKaryawanBySelectedManagers = loadKaryawanByManagers;
        window.syncPrimaryKaryawanSelection = syncPrimaryKaryawan;
    });

    // Open edit modal with data - untuk menetapkan penanggung jawab
    function openEditModal(id, nama, deskripsi, harga, tanggalMulaiPengerjaan, tanggalSelesaiPengerjaan, tanggalMulaiKerjasama, tanggalSelesaiKerjasama, statusPengerjaan, statusKerjasama, progres, penanggungJawabId, karyawanPenanggungJawabId = null, penanggungJawabIds = [], karyawanPenanggungJawabIds = []) {
        document.getElementById('editId').value = id;
        
        // Tampilkan info project (readonly)
        document.getElementById('editNamaDisplay').value = nama;
        document.getElementById('editNama').value = nama;
        
        // Set minimal hidden fields yang diperlukan
        document.getElementById('editDeskripsi').value = deskripsi;
        document.getElementById('editStatusKerjasama').value = statusKerjasama || 'aktif';

        const managerSearchInput = document.getElementById('managerSearchInput');
        const karyawanSearchInput = document.getElementById('karyawanSearchInput');
        const managerSearchEmpty = document.getElementById('managerSearchEmpty');
        const karyawanSearchEmpty = document.getElementById('karyawanSearchEmpty');
        if (managerSearchInput) {
            managerSearchInput.value = '';
        }
        if (karyawanSearchInput) {
            karyawanSearchInput.value = '';
        }
        if (managerSearchEmpty) {
            managerSearchEmpty.classList.add('hidden');
        }
        if (karyawanSearchEmpty) {
            karyawanSearchEmpty.classList.add('hidden');
        }

        document.querySelectorAll('#editPenanggungJawabList .manager-item').forEach(item => {
            item.classList.remove('hidden');
        });
        
        // Set penanggung jawab manager (checkbox)
        const managerCheckboxes = Array.from(document.querySelectorAll('input.manager-checkbox[name="penanggung_jawab_ids[]"]'));
        const managerIds = Array.isArray(penanggungJawabIds)
            ? penanggungJawabIds.map(String).filter(Boolean)
            : [];
        if (managerIds.length === 0 && penanggungJawabId && penanggungJawabId !== 'null') {
            managerIds.push(String(penanggungJawabId));
        }

        const karyawanIds = Array.isArray(karyawanPenanggungJawabIds)
            ? karyawanPenanggungJawabIds.map(String).filter(Boolean)
            : [];
        if (karyawanIds.length === 0 && karyawanPenanggungJawabId && karyawanPenanggungJawabId !== 'null') {
            karyawanIds.push(String(karyawanPenanggungJawabId));
        }

        if (managerCheckboxes.length > 0) {
            managerCheckboxes.forEach(checkbox => {
                checkbox.checked = managerIds.includes(String(checkbox.value));
            });

            const selectedManagerIds = managerCheckboxes
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value)
                .filter(Boolean);
            const primaryManagerId = selectedManagerIds.length > 0 ? selectedManagerIds[0] : '';
            const primaryManagerInput = document.getElementById('editPenanggungJawabPrimary');
            if (primaryManagerInput) {
                primaryManagerInput.value = primaryManagerId;
            }

            if (typeof window.loadKaryawanBySelectedManagers === 'function') {
                window.loadKaryawanBySelectedManagers(selectedManagerIds, karyawanIds);
            } else {
                const karyawanWrapper = document.getElementById('karyawanDivisiWrapper');
                const karyawanList = document.getElementById('karyawanDivisiList');
                if (karyawanWrapper) karyawanWrapper.classList.add('hidden');
                if (karyawanList) karyawanList.innerHTML = '';
            }
        }
        
        // Update form action
        const editForm = document.getElementById('editForm');
        editForm.action = `/general_manajer/data_project/${id}`;
        
        // Focus ke checkbox pertama saat modal terbuka
        document.getElementById('editModal').classList.remove('hidden');
        setTimeout(() => {
            const firstManagerCheckbox = document.querySelector('input.manager-checkbox[name="penanggung_jawab_ids[]"]');
            if (firstManagerCheckbox) {
                firstManagerCheckbox.focus();
            }
        }, 100);
    }
    </script>
    
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>
