<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Perusahaan - Manager Divisi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('logo1.jpeg') }}">
    
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
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
            font-family: 'Inter', sans-serif;
        }
        
        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }
        
        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
        
        .modal {
            transition: opacity 0.25s ease;
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
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
        
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }
        
        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
            }
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
            min-width: 100px;
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
        
        .hidden-by-filter {
            display: none !important;
        }
        
        .mobile-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        
        .mobile-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .mobile-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .mobile-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        
        .mobile-card-info {
            flex: 1;
        }
        
        .mobile-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 4px 0;
        }
        
        .mobile-card-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }
        
        .mobile-card-actions {
            display: flex;
            gap: 8px;
        }
        
        .mobile-card-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .mobile-card-action-btn.edit {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .mobile-card-action-btn.edit:hover {
            background-color: rgba(59, 130, 246, 0.2);
        }
        
        .mobile-card-action-btn.delete {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .mobile-card-action-btn.delete:hover {
            background-color: rgba(239, 68, 68, 0.2);
        }
        
        .mobile-card-details {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }
        
        .mobile-card-detail-item {
            display: flex;
            margin-bottom: 8px;
        }
        
        .mobile-card-detail-label {
            font-size: 12px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 80px;
            flex-shrink: 0;
        }
        
        .mobile-card-detail-value {
            font-size: 14px;
            color: #475569;
            flex: 1;
        }
        
        .search-add-container {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        
        .search-container {
            flex: 1;
            position: relative;
        }
        
        .add-button-container {
            flex-shrink: 0;
        }
        
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
    </style>
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Menggunakan template header untuk Manager Divisi -->
        <!-- Pastikan path file header sesuai dengan struktur project Anda -->
        @include('manager_divisi/templet/sider')
        
        @php
            // DEFENSIVE: Ensure $perusahaans is always a collection.
            $perusahaans = $perusahaans ?? collect([]);
        @endphp

        <div class="flex-1 flex flex-col main-content">
            <div class="flex-1 p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Data Perusahaan</h2>
                
                <!-- Search and Add Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="search-add-container w-full">
                        <div class="search-container">
                            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari nama perusahaan atau klien..." type="text" />
                        </div>
                        <div class="add-button-container">
                            <button id="tambahPerusahaanBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined">add</span>
                                <span class="hidden sm:inline">Tambah Perusahaan</span>
                                <span class="sm:hidden">Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">business</span>
                            Data Perusahaan
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount" class="font-semibold text-text-light">{{ $perusahaans->count() }}</span> perusahaan</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- SCROLLABLE TABLE -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 50px;">No</th>
                                            <th style="min-width: 200px;">Nama Perusahaan</th>
                                            <th style="min-width: 150px;">Klien / PIC</th>
                                            <th style="min-width: 150px;">Kontak</th>
                                            <th style="min-width: 300px;">Alamat</th>
                                            <th style="min-width: 150px;">Jumlah Kerjasama</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        @if($perusahaans->count() > 0)
                                            @php $no = 1; @endphp
                                            @foreach($perusahaans as $perusahaan)
                                                <tr class="perusahaan-row" 
                                                    data-id="{{ $perusahaan->id }}" 
                                                    data-nama="{{ $perusahaan->nama_perusahaan }}" 
                                                    data-klien="{{ $perusahaan->klien }}"
                                                    data-kontak="{{ $perusahaan->kontak }}"
                                                    data-alamat="{{ $perusahaan->alamat }}"
                                                    data-jumlah="{{ $perusahaan->jumlah_kerjasama }}">
                                                    <td style="min-width: 50px;">{{ $no++ }}</td>
                                                    <td style="min-width: 200px;">
                                                        <div class="font-semibold text-gray-800">{{ $perusahaan->nama_perusahaan }}</div>
                                                    </td>
                                                    <td style="min-width: 150px;">{{ $perusahaan->klien }}</td>
                                                    <td style="min-width: 150px;">
                                                        <div class="flex flex-col">
                                                            <span>{{ $perusahaan->kontak }}</span>
                                                        </div>
                                                    </td>
                                                    <td style="min-width: 300px;">
                                                        <div class="truncate max-w-xs" title="{{ $perusahaan->alamat }}">{{ $perusahaan->alamat }}</div>
                                                    </td>
                                                    <td style="min-width: 150px;">
                                                        <!-- Tampilan Angka Murni -->
                                                        <span class="font-medium text-primary">
                                                            {{ $perusahaan->jumlah_kerjasama ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td style="min-width: 100px; text-align: center;">
                                                        <div class="flex justify-center gap-2">
                                                            <button class="edit-btn p-1 rounded-full hover:bg-blue-500/20 text-blue-600" 
                                                                    data-id="{{ $perusahaan->id }}"
                                                                    data-nama="{{ $perusahaan->nama_perusahaan }}"
                                                                    data-klien="{{ $perusahaan->klien }}"
                                                                    data-kontak="{{ $perusahaan->kontak }}"
                                                                    data-alamat="{{ $perusahaan->alamat }}"
                                                                    data-jumlah="{{ $perusahaan->jumlah_kerjasama }}"
                                                                    title="Edit">
                                                                <span class="material-icons-outlined">edit</span>
                                                            </button>
                                                            <button class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700" 
                                                                    data-id="{{ $perusahaan->id }}"
                                                                    title="Hapus">
                                                                <span class="material-icons-outlined">delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    Tidak ada data perusahaan
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Mobile Card View -->
                        <div class="mobile-cards" id="mobile-cards">
                            @if($perusahaans->count() > 0)
                                @php $no = 1; @endphp
                                @foreach($perusahaans as $perusahaan)
                                    <div class="mobile-card perusahaan-card" 
                                         data-id="{{ $perusahaan->id }}" 
                                         data-nama="{{ $perusahaan->nama_perusahaan }}" 
                                         data-klien="{{ $perusahaan->klien }}"
                                         data-kontak="{{ $perusahaan->kontak }}"
                                         data-alamat="{{ $perusahaan->alamat }}"
                                         data-jumlah="{{ $perusahaan->jumlah_kerjasama }}">
                                        <div class="mobile-card-header">
                                            <div class="mobile-card-icon">
                                                <span class="material-icons-outlined text-2xl text-primary">business</span>
                                            </div>
                                            <div class="mobile-card-info">
                                                <h4 class="mobile-card-title">{{ $perusahaan->nama_perusahaan }}</h4>
                                                <p class="mobile-card-subtitle">{{ $perusahaan->klien }}</p>
                                            </div>
                                            <div class="mobile-card-actions">
                                                <button class="mobile-card-action-btn edit" 
                                                        data-id="{{ $perusahaan->id }}"
                                                        data-nama="{{ $perusahaan->nama_perusahaan }}"
                                                        data-klien="{{ $perusahaan->klien }}"
                                                        data-kontak="{{ $perusahaan->kontak }}"
                                                        data-alamat="{{ $perusahaan->alamat }}"
                                                        data-jumlah="{{ $perusahaan->jumlah_kerjasama }}"
                                                        title="Edit">
                                                    <span class="material-icons-outlined" style="font-size: 18px;">edit</span>
                                                </button>
                                                <button class="mobile-card-action-btn delete" 
                                                        data-id="{{ $perusahaan->id }}"
                                                        title="Hapus">
                                                    <span class="material-icons-outlined" style="font-size: 18px;">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Mobile Details Box -->
                                        <div class="mobile-card-details">
                                            <div class="mobile-card-detail-item">
                                                <div class="mobile-card-detail-label">Kontak</div>
                                                <div class="mobile-card-detail-value">{{ $perusahaan->kontak }}</div>
                                            </div>
                                            <div class="mobile-card-detail-item">
                                                <div class="mobile-card-detail-label">Alamat</div>
                                                <div class="mobile-card-detail-value">{{ $perusahaan->alamat }}</div>
                                            </div>
                                            <div class="mobile-card-detail-item">
                                                <div class="mobile-card-detail-label">Jumlah</div>
                                                <div class="mobile-card-detail-value">
                                                    {{ $perusahaan->jumlah_kerjasama ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="bg-white rounded-lg border border-border-light p-8 text-center">
                                    <span class="material-icons-outlined text-4xl text-gray-300 mb-2">business</span>
                                    <p class="text-gray-500">Tidak ada data perusahaan</p>
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
                Copyright ©2025 by Manager Divisi
            </footer>
        </div>
    </div>

    <!-- Modal Tambah Perusahaan -->
    <div id="tambahPerusahaanModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Perusahaan Baru</h3>
                    <button id="closeModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <!-- Route: manager_divisi.perusahaan.store -->
                <form action="{{ route('manager_divisi.perusahaan.store') }}" method="POST" id="tambahPerusahaanForm" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama perusahaan" required>
                        </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Klien / PIC</label>
                        <input type="text" name="klien" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Nama kontak utama" required>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
                        <input type="text" name="kontak" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Telepon/Email" required>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" rows="3" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Alamat lengkap" required></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kerjasama</label>
                        <input type="number" name="jumlah_kerjasama" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="0" step="1" min="0">
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="batalTambahBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Perusahaan -->
    <div id="editPerusahaanModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Perusahaan</h3>
                    <button id="closeEditModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <!-- Route: manager_divisi.perusahaan.update -->
                <!-- Menggunakan data-route untuk JS injection yang aman -->
                <form data-route="{{ route('manager_divisi.perusahaan.update', ':id') }}" method="POST" id="editPerusahaanForm" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editId" name="id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                            <input type="text" id="editNamaPerusahaan" name="nama_perusahaan" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama perusahaan" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Klien / PIC</label>
                            <input type="text" id="editKlien" name="klien" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Nama kontak utama" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
                            <input type="text" id="editKontak" name="kontak" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Telepon/Email" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea id="editAlamat" name="alamat" rows="3" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Alamat lengkap" required></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kerjasama</label>
                            <input type="number" id="editJumlahKerjasama" name="jumlah_kerjasama" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="0" step="1" min="0">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="batalEditBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete Konfirmasi -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button id="closeDeleteModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <!-- Route: manager_divisi.perusahaan.destroy -->
                <form data-route="{{ route('manager_divisi.perusahaan.destroy', ':id') }}" id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus perusahaan ini?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteId" name="id">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="batalDeleteBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
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

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay hidden">
        <div class="loading-spinner"></div>
    </div>

    <script>
        // Inisialisasi variabel untuk pagination dan search
        let currentPage = 1;
        const itemsPerPage = 5;
        let searchTerm = '';
        
        // Dapatkan semua elemen perusahaan
        const perusahaanRows = document.querySelectorAll('.perusahaan-row');
        const perusahaanCards = document.querySelectorAll('.perusahaan-card');
        
        // Inisialisasi pagination dan search
        initializePagination();
        initializeSearch();
        initializeScrollDetection();

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
            pageNumbersContainer.innerHTML = '';
            
            // Generate page numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `desktop-page-btn ${i === currentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToPage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }
            
            // Update navigation buttons
            prevButton.disabled = currentPage === 1;
            nextButton.disabled = currentPage === totalPages || totalPages === 0;
            
            // Add event listeners for navigation buttons
            prevButton.onclick = () => {
                if (currentPage > 1) goToPage(currentPage - 1);
            };
            
            nextButton.onclick = () => {
                if (currentPage < totalPages) goToPage(currentPage + 1);
            };
        }
        
        function goToPage(page) {
            currentPage = page;
            renderPagination();
            updateVisibleItems();
            
            // Reset scroll position when changing pages
            const scrollableTable = document.getElementById('scrollableTable');
            if (scrollableTable) {
                scrollableTable.scrollLeft = 0;
            }
        }
        
        function getFilteredRows() {
            return Array.from(perusahaanRows).filter(row => !row.classList.contains('hidden-by-filter'));
        }
        
        function getFilteredCards() {
            return Array.from(perusahaanCards).filter(card => !card.classList.contains('hidden-by-filter'));
        }
        
        function updateVisibleItems() {
            const visibleRows = getFilteredRows();
            const visibleCards = getFilteredCards();
            
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            
            // Hide all rows and cards first
            perusahaanRows.forEach(row => row.style.display = 'none');
            perusahaanCards.forEach(card => card.style.display = 'none');
            
            // Show only rows for current page
            visibleRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = '';
                }
            });
            
            // Show only cards for current page
            visibleCards.forEach((card, index) => {
                if (index >= startIndex && index < endIndex) {
                    card.style.display = '';
                }
            });
            
            // Update total count
            document.getElementById('totalCount').textContent = visibleRows.length;
        }
        
        // === SEARCH ===
        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchTerm = searchInput.value.trim();
                    applySearch();
                }, 300); // Debounce search
            });
        }
        
        function applySearch() {
            // Reset to first page
            currentPage = 1;
            
            // Apply search to rows
            perusahaanRows.forEach(row => {
                const nama = row.getAttribute('data-nama').toLowerCase();
                const klien = row.getAttribute('data-klien').toLowerCase();
                
                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = nama.includes(searchLower) || 
                                   klien.includes(searchLower);
                }
                
                if (searchMatches) {
                    row.classList.remove('hidden-by-filter');
                } else {
                    row.classList.add('hidden-by-filter');
                }
            });
            
            // Apply same search to cards
            perusahaanCards.forEach(card => {
                const nama = card.getAttribute('data-nama').toLowerCase();
                const klien = card.getAttribute('data-klien').toLowerCase();
                
                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = nama.includes(searchLower) || 
                                   klien.includes(searchLower);
                }
                
                if (searchMatches) {
                    card.classList.remove('hidden-by-filter');
                } else {
                    card.classList.add('hidden-by-filter');
                }
            });
            
            // Update pagination and visible items
            renderPagination();
            updateVisibleItems();
        }

        // Initialize scroll detection for table
        function initializeScrollDetection() {
            const scrollableTable = document.getElementById('scrollableTable');
            
            if (scrollableTable) {
                // Add scroll event listener
                scrollableTable.addEventListener('scroll', function() {
                    const scrollLeft = scrollableTable.scrollLeft;
                    const maxScroll = scrollableTable.scrollWidth - scrollableTable.clientWidth;
                });
            }
        }

        // Minimalist Popup
        function showMinimalPopup(title, message, type = 'success') {
            const popup = document.getElementById('minimalPopup');
            if (!popup) return;
            
            const popupTitle = popup.querySelector('.minimal-popup-title');
            const popupMessage = popup.querySelector('.minimal-popup-message');
            const popupIcon = popup.querySelector('.minimal-popup-icon span');
            
            // Set content
            popupTitle.textContent = title;
            popupMessage.textContent = message;
            
            // Set type
            popup.className = 'minimal-popup show ' + type;
            
            // Set icon
            if (type === 'success') popupIcon.textContent = 'check';
            else if (type === 'error') popupIcon.textContent = 'error';
            else if (type === 'warning') popupIcon.textContent = 'warning';
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }
        
        // Close popup when clicking close button
        document.querySelector('.minimal-popup-close').addEventListener('click', function() {
            document.getElementById('minimalPopup').classList.remove('show');
        });

        // Modal elements
        const tambahPerusahaanModal = document.getElementById('tambahPerusahaanModal');
        const editPerusahaanModal = document.getElementById('editPerusahaanModal');
        const deleteModal = document.getElementById('deleteModal');

        // Buttons
        const tambahPerusahaanBtn = document.getElementById('tambahPerusahaanBtn');
        const batalTambahBtn = document.getElementById('batalTambahBtn');
        const batalEditBtn = document.getElementById('batalEditBtn');
        const batalDeleteBtn = document.getElementById('batalDeleteBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const closeEditModalBtn = document.getElementById('closeEditModalBtn');
        const closeDeleteModalBtn = document.getElementById('closeDeleteModalBtn');

        // Forms
        const tambahPerusahaanForm = document.getElementById('tambahPerusahaanForm');
        const editPerusahaanForm = document.getElementById('editPerusahaanForm');

        // Open tambah modal
        tambahPerusahaanBtn.addEventListener('click', () => {
            tambahPerusahaanModal.classList.remove('hidden');
        });

        // Close tambah modal
        batalTambahBtn.addEventListener('click', () => {
            tambahPerusahaanModal.classList.add('hidden');
            resetTambahForm();
        });

        closeModalBtn.addEventListener('click', () => {
            tambahPerusahaanModal.classList.add('hidden');
            resetTambahForm();
        });

        // Close edit modal
        batalEditBtn.addEventListener('click', () => {
            editPerusahaanModal.classList.add('hidden');
            resetEditForm();
        });

        closeEditModalBtn.addEventListener('click', () => {
            editPerusahaanModal.classList.add('hidden');
            resetEditForm();
        });

        // Close delete modal
        batalDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        closeDeleteModalBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        // Reset forms
        function resetTambahForm() {
            tambahPerusahaanForm.reset();
        }

        function resetEditForm() {
            editPerusahaanForm.reset();
        }

        // ============================
        // HANDLE EDIT BUTTON
        // ============================
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                // SET VALUE
                document.getElementById('editId').value = button.dataset.id;
                document.getElementById('editNamaPerusahaan').value = button.dataset.nama;
                document.getElementById('editKlien').value = button.dataset.klien;
                document.getElementById('editKontak').value = button.dataset.kontak;
                document.getElementById('editAlamat').value = button.dataset.alamat;
                // Set Value Jumlah (Tanpa Format Rupiah)
                document.getElementById('editJumlahKerjasama').value = button.dataset.jumlah ?? '';

                // SET ACTION URL DINAMIS (Update route ID)
                const baseUrl = editPerusahaanForm.dataset.route;
                editPerusahaanForm.action = baseUrl.replace(':id', button.dataset.id);

                editPerusahaanModal.classList.remove('hidden');
            });
        });
        
        // Handle Mobile Card Edit Buttons
        document.querySelectorAll('.mobile-card-action-btn.edit').forEach(button => {
             button.addEventListener('click', () => {
                // Reuse logic from desktop edit
                document.getElementById('editId').value = button.dataset.id;
                document.getElementById('editNamaPerusahaan').value = button.dataset.nama;
                document.getElementById('editKlien').value = button.dataset.klien;
                document.getElementById('editKontak').value = button.dataset.kontak;
                document.getElementById('editAlamat').value = button.dataset.alamat;
                document.getElementById('editJumlahKerjasama').value = button.dataset.jumlah ?? '';

                const baseUrl = editPerusahaanForm.dataset.route;
                editPerusahaanForm.action = baseUrl.replace(':id', button.dataset.id);

                editPerusahaanModal.classList.remove('hidden');
            });
        });

        // ============================
        // HANDLE DELETE BUTTON
        // ============================
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;

                // Set action form delete
                document.getElementById('deleteId').value = id;
                const baseUrl = document.getElementById('deleteForm').dataset.route;
                document.getElementById('deleteForm').action = baseUrl.replace(':id', id);

                // Tampilkan modal
                deleteModal.classList.remove('hidden');
            });
        });
        
        // Handle Mobile Card Delete Buttons
        document.querySelectorAll('.mobile-card-action-btn.delete').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                document.getElementById('deleteId').value = id;
                const baseUrl = document.getElementById('deleteForm').dataset.route;
                document.getElementById('deleteForm').action = baseUrl.replace(':id', id);
                deleteModal.classList.remove('hidden');
            });
        });

        // ============================
        // SUBMIT FORM TAMBAH
        // ============================
        tambahPerusahaanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Tampilkan loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span> Menyimpan...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            })
            .then(data => {
                if (data.success) {
                    tambahPerusahaanModal.classList.add('hidden');
                    resetTambahForm();
                    showMinimalPopup('Berhasil', data.message || 'Perusahaan berhasil ditambahkan', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showMinimalPopup('Gagal', data.message || 'Gagal menambah perusahaan.', 'error');
                }
            })
            .catch(error => {
                console.error('Error Detail:', error);
                showMinimalPopup('Error', error.message, 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });

        // ============================
        // SUBMIT FORM EDIT
        // ============================
        editPerusahaanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // ID sudah diset di hidden input
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span> Memperbarui...';

            fetch(this.action, {
                method: 'POST', // Form memiliki @method('PUT') di dalam body
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            })
            .then(data => {
                if (data.success) {
                    editPerusahaanModal.classList.add('hidden');
                    resetEditForm();
                    showMinimalPopup('Berhasil', data.message || 'Perusahaan berhasil diperbarui', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showMinimalPopup('Gagal', data.message || 'Gagal memperbarui perusahaan.', 'error');
                }
            })
            .catch(error => {
                console.error('Error Detail:', error);
                showMinimalPopup('Error', error.message, 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });

        // ============================
        // SUBMIT FORM DELETE
        // ============================
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span> Menghapus...';

            fetch(this.action, {
                method: 'POST', // Form memiliki @method('DELETE') di dalam body
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            })
            .then(data => {
                if (data.success) {
                    deleteModal.classList.add('hidden');
                    showMinimalPopup('Berhasil', data.message || 'Perusahaan berhasil dihapus', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showMinimalPopup('Gagal', data.message || 'Gagal menghapus perusahaan.', 'error');
                }
            })
            .catch(error => {
                console.error('Error Detail:', error);
                showMinimalPopup('Error', error.message, 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    </script>
</body>

</html>