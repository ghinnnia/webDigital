<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Catatan Rapat Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    
    <!-- Tailwind Config -->
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
        
        /* Action button styles - Perbesar dan warna abu-abu */
        .action-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background-color: #f3f4f6;
            color: #6b7280;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .action-btn:hover {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .action-btn.edit:hover {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        
        .action-btn.delete:hover {
            background-color: #fee2e2;
            color: #b91c1c;
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
        
        .status-paid {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .status-unpaid {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }
        
        .status-pending {
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
        
        /* SCROLLABLE TABLE */
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
            min-width: 1000px; /* Fixed minimum width */
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
        
        /* Loading spinner */
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Selected user badge styles */
        .selected-user-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px;
        }
        
        .selected-user-badge button {
            background: none;
            border: none;
            color: #0369a1;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
        }
        
        /* Checkbox styles for users */
        .user-checkbox-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            background-color: #f9fafb;
        }
        
        .user-checkbox-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 6px;
            transition: background-color 0.2s;
            cursor: pointer;
        }
        
        .user-checkbox-item:hover {
            background-color: #e0f2fe;
        }
        
        .user-checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            cursor: pointer;
        }
        
        .user-checkbox-item label {
            cursor: pointer;
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-role {
            font-size: 12px;
            color: #64748b;
            background-color: #f1f5f9;
            padding: 2px 8px;
            border-radius: 12px;
        }
        
        .user-checkbox-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .user-checkbox-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .user-checkbox-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .user-checkbox-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Pagination info styles - FIXED */
        .pagination-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #64748b;
        }
        
        .pagination-info .showing {
            font-weight: 500;
            color: #374151;
        }
        
        .pagination-info .total {
            color: #374151;
        }
        
        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .pagination-controls label {
            font-size: 14px;
            color: #64748b;
            white-space: nowrap;
        }
        
        .pagination-controls select {
            font-size: 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 4px 8px;
            background-color: white;
            color: #374151;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        
        .pagination-controls select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
    
    <!-- Add CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('admin.templet.sider')
        
        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Catatan Rapat</h2>
                
                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari topik, peserta, atau hasil diskusi..." type="text" />
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <button id="createBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Buat Catatan Rapat</span>
                            <span class="sm:hidden">Buat</span>
                        </button>
                    </div>
                </div>
                
                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">description</span>
                            Daftar Catatan Rapat
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount" class="font-semibold text-text-light">0</span> catatan rapat</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Empty State -->
                        <div id="emptyState" class="hidden text-center py-12">
                            <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <span class="material-icons-outlined text-3xl text-gray-400">description</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Catatan Rapat</h3>
                            <p class="text-gray-500 mb-6">Mulai dengan membuat catatan rapat pertama Anda</p>
                            <button id="createFirstBtn" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                                Buat Catatan Rapat Pertama
                            </button>
                        </div>
                        
                        <!-- Loading State -->
                        <div id="loadingState" class="hidden text-center py-12">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto mb-4"></div>
                            <p class="text-gray-600">Memuat data catatan rapat...</p>
                        </div>
                        
                        <!-- SCROLLABLE TABLE -->
                        <div id="tableContainer" class="hidden desktop-table">
                            <!-- Pagination Info - FIXED LAYOUT -->
                            <div id="paginationInfo" class="pagination-info">
                                <div class="showing">
                                    Menampilkan <span id="showingStart">0</span>-<span id="showingEnd">0</span> dari <span id="showingTotal">0</span> catatan
                                </div>
                                <div class="pagination-controls">
                                    <label for="itemsPerPage">Per halaman:</label>
                                    <select id="itemsPerPage">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 120px;">Tanggal</th>
                                            <th style="min-width: 200px;">Topik</th>
                                            <th style="min-width: 250px;">Hasil Diskusi</th>
                                            <th style="min-width: 200px;">Keputusan</th>
                                            <th style="min-width: 150px;">Peserta</th>
                                            <th style="min-width: 150px;">Penugasan</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <!-- Data will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination Controls -->
                            <div id="paginationContainer" class="desktop-pagination">
                                <button id="firstPage" class="desktop-nav-btn" title="Halaman Pertama">
                                    <span class="material-icons-outlined text-sm">first_page</span>
                                </button>
                                <button id="prevPage" class="desktop-nav-btn" title="Halaman Sebelumnya">
                                    <span class="material-icons-outlined">chevron_left</span>
                                </button>
                                <div id="pageNumbers" class="flex gap-1">
                                    <!-- Page numbers will be generated by JavaScript -->
                                </div>
                                <button id="nextPage" class="desktop-nav-btn" title="Halaman Selanjutnya">
                                    <span class="material-icons-outlined">chevron_right</span>
                                </button>
                                <button id="lastPage" class="desktop-nav-btn" title="Halaman Terakhir">
                                    <span class="material-icons-outlined text-sm">last_page</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Mobile Card View -->
                        <div id="mobileCards" class="hidden mobile-cards space-y-4">
                            <!-- Mobile cards will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Modal Popup -->
    <div id="modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-xl font-bold text-text-light dark:text-text-dark"></h3>
                    <button id="closeModal" class="text-muted-light dark:text-muted-dark hover:text-text-light dark:hover:text-text-dark">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                
                <div id="modalContent" class="mb-6">
                    <!-- Content will be dynamically inserted here -->
                </div>
                
                <div class="flex justify-end gap-3">
                    <button id="cancelBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                    <button id="confirmBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                        <span id="confirmBtnText">Simpan</span>
                        <span id="loadingSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Detail Catatan Rapat</h3>
                    <button id="closeViewModal" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                
                <div id="viewContent" class="space-y-4">
                    <!-- Content will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <span class="material-icons-outlined text-red-500 text-3xl mr-3">warning</span>
                    <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Apakah Anda yakin ingin menghapus catatan rapat ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end gap-3">
                    <button id="cancelDelete" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                    <button id="confirmDelete" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimalist Popup -->
    <div id="notification" class="minimal-popup">
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
        // Global variables
        let currentAction = '';
        let currentId = null;
        let allUsers = [];
        let cachedUsers = [];
        let allCatatanRapat = [];
        let filteredData = [];
        let currentPage = 1;
        let itemsPerPage = 10; // Changed to 10 items per page
        let activeFilters = ['all'];
        let searchTerm = '';
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Catatan Rapat page loaded');
            
            // Load users data
            loadUsers();
            
            // Load catatan rapat data
            loadCatatanRapatData();
            
            // Event listeners
            document.getElementById('createBtn').addEventListener('click', openCreateModal);
            document.getElementById('createFirstBtn').addEventListener('click', openCreateModal);
            document.getElementById('confirmBtn').addEventListener('click', handleConfirm);
            document.getElementById('searchInput').addEventListener('input', filterData);
            document.getElementById('itemsPerPage').addEventListener('change', function() {
                itemsPerPage = parseInt(this.value);
                currentPage = 1;
                renderPagination();
                displayCurrentPage();
            });
            
            // Modal event listeners
            document.getElementById('closeModal').addEventListener('click', closeModal);
            document.getElementById('cancelBtn').addEventListener('click', closeModal);
            document.getElementById('closeViewModal').addEventListener('click', () => hideModal('viewModal'));
            document.getElementById('cancelDelete').addEventListener('click', () => hideModal('deleteModal'));
            
            // Close modal when clicking outside
            document.getElementById('modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
            
            document.getElementById('viewModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal('viewModal');
                }
            });
            
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal('deleteModal');
                }
            });
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    hideModal('viewModal');
                    hideModal('deleteModal');
                }
            });
            
            // Close notification when clicking the close button
            document.querySelector('.minimal-popup-close').addEventListener('click', function() {
                document.getElementById('notification').classList.remove('show');
            });
            
            // Initialize filter
            initializeFilter();
            
            // Initialize scroll detection for table
            initializeScrollDetection();
        });
        
        // Load users from server
        async function loadUsers() {
            try {
                const response = await fetch('/users/data', {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to load users');
                }
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    cachedUsers = result.data;
                    console.log(`Loaded ${cachedUsers.length} users`);
                } else {
                    console.warn('No users data found');
                    cachedUsers = [];
                }
            } catch (error) {
                console.error('Error loading users:', error);
                cachedUsers = [];
            }
        }

        // Helper function to format date to YYYY-MM-DD for input type="date"
        function formatDateValue(dateString) {
            if (!dateString) return '';
            
            // If already in correct format (YYYY-MM-DD), return as is
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                return dateString;
            }

            try {
                const date = new Date(dateString);
                // Adjust for timezone offset to ensure we get the correct calendar day
                const offset = date.getTimezoneOffset();
                const localDate = new Date(date.getTime() - (offset * 60 * 1000));
                return localDate.toISOString().split('T')[0];
            } catch (e) {
                return '';
            }
        }
        
        // Modal functions
        function openCreateModal() {
            currentAction = 'create';
            currentId = null;
            
            showModal(
                'Buat Catatan Rapat Baru',
                getFormTemplate({}),
                'Simpan'
            );
            
            // Load users into checkboxes after modal is shown
            setTimeout(() => {
                populateUserCheckboxes('peserta');
                populateUserCheckboxes('penugasan');
            }, 100);
        }
        
        function showModal(title, content, confirmText = 'Simpan') {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('confirmBtnText').textContent = confirmText;
            document.getElementById('modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentAction = '';
            currentId = null;
        }
        
        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Form template - MODIFIED FOR EDIT
        function getFormTemplate(data = {}) {
            const isEdit = currentAction === 'edit';
            
            return `
                <form id="catatanRapatForm" class="space-y-4" onsubmit="return false;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">
                                <span class="text-red-500">*</span> Tanggal Rapat
                            </label>
                            <input type="date" id="tanggalInput" name="tanggal"
                                value="${formatDateValue(data.tanggal) || new Date().toISOString().split('T')[0]}"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none"
                                ${!isEdit ? 'required' : ''}>
                            <p class="mt-1 text-sm text-gray-500">Pilih tanggal rapat dilaksanakan</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">
                                <span class="text-red-500">*</span> Topik Rapat
                            </label>
                            <input type="text" id="topikInput" name="topik"
                                value="${data.topik || ''}"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none"
                                placeholder="Contoh: Rapat Evaluasi Kinerja Triwulan 1"
                                ${!isEdit ? 'required' : ''}>
                            <p class="mt-1 text-sm text-gray-500">Masukkan topik pembahasan utama</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            <span class="text-red-500">*</span> Hasil Diskusi
                        </label>
                        <textarea id="hasilDiskusiInput" name="hasil_diskusi" rows="4"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none resize-none"
                            placeholder="Tuliskan hasil diskusi dalam rapat..."
                            ${!isEdit ? 'required' : ''}>${data.hasil_diskusi || ''}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Ringkasan pembahasan dan poin-poin penting</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            <span class="text-red-500">*</span> Keputusan
                        </label>
                        <textarea id="keputusanInput" name="keputusan" rows="3"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none resize-none"
                            placeholder="Tuliskan keputusan yang diambil..."
                            ${!isEdit ? 'required' : ''}>${data.keputusan || ''}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Kesimpulan dan tindak lanjut dari rapat</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            <span class="text-red-500">*</span> Peserta Rapat
                        </label>
                        <input type="text" id="searchPeserta" placeholder="Cari nama atau email peserta..."
                            class="w-full px-3 py-2 border rounded-lg mb-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none"
                            onkeyup="handleSearchUser('peserta')">
                        <div class="user-checkbox-container" id="pesertaCheckboxContainer">
                            <div class="text-center text-gray-500 py-4">
                                <span class="material-icons-outlined animate-spin">refresh</span>
                                <p class="mt-2">Memuat daftar user...</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" onclick="selectAllUsers('peserta')" class="text-sm text-primary hover:underline">
                                Pilih Semua
                            </button>
                            <button type="button" onclick="deselectAllUsers('peserta')" class="text-sm text-gray-500 hover:underline">
                                Hapus Pilihan
                            </button>
                        </div>
                        <div id="selectedPeserta" class="mt-3 flex flex-wrap gap-2"></div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            <span class="text-red-500">*</span> Penugasan
                        </label>
                            <input type="text" id="searchPenugasan" placeholder="Cari nama atau email untuk penugasan..."
                                class="w-full px-3 py-2 border rounded-lg mb-3 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none"
                                onkeyup="handleSearchUser('penugasan')">
                        <div class="user-checkbox-container" id="penugasanCheckboxContainer">
                            <div class="text-center text-gray-500 py-4">
                                <span class="material-icons-outlined animate-spin">refresh</span>
                                <p class="mt-2">Memuat daftar user...</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" onclick="selectAllUsers('penugasan')" class="text-sm text-primary hover:underline">
                                Pilih Semua
                            </button>
                            <button type="button" onclick="deselectAllUsers('penugasan')" class="text-sm text-gray-500 hover:underline">
                                Hapus Pilihan
                            </button>
                        </div>
                        <div id="selectedPenugasan" class="mt-3 flex flex-wrap gap-2"></div>
                    </div>
                </form>
            `;
        }
        
        // Populate user checkboxes
        function populateUserCheckboxes(type, selectedIds = []) {
            const container = document.getElementById(`${type}CheckboxContainer`);
            const selectedDiv = document.getElementById(`selected${type.charAt(0).toUpperCase() + type.slice(1)}`);
            
            if (!container) return;
            
            // Clear existing content
            container.innerHTML = '';
            if (selectedDiv) selectedDiv.innerHTML = '';
            
            if (cachedUsers.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-4">Tidak ada user tersedia</div>';
                return;
            }
            
            // Group users by role if available
            const groupedUsers = {};
                // Get search term for filtering
                const searchInput = document.getElementById(`search${type.charAt(0).toUpperCase() + type.slice(1)}`);
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            
                // Filter users based on search term
                let filteredUsersList = cachedUsers;
                if (searchTerm) {
                    filteredUsersList = cachedUsers.filter(user => {
                        const name = (user.name || '').toLowerCase();
                        const email = (user.email || '').toLowerCase();
                        return name.includes(searchTerm) || email.includes(searchTerm);
                    });
                }
            
                // Show "no results" message if search returns nothing
                if (searchTerm && filteredUsersList.length === 0) {
                    container.innerHTML = '<div class="text-center text-gray-500 py-4">Tidak ada user yang sesuai dengan pencarian</div>';
                    return;
                }
            
                filteredUsersList.forEach(user => {
                const role = user.role || 'User';
                if (!groupedUsers[role]) {
                    groupedUsers[role] = [];
                }
                groupedUsers[role].push(user);
            });
            
            // Create checkboxes for each group
            Object.keys(groupedUsers).forEach(role => {
                // Add role header
                const roleHeader = document.createElement('div');
                roleHeader.className = 'text-sm font-semibold text-gray-600 mb-2 mt-3 first:mt-0';
                roleHeader.textContent = role;
                container.appendChild(roleHeader);
                
                // Add users in this role
                groupedUsers[role].forEach(user => {
                    const checkboxItem = document.createElement('div');
                    checkboxItem.className = 'user-checkbox-item';
                    
                    const isChecked = Array.isArray(selectedIds) && selectedIds.includes(user.id.toString());
                    
                    checkboxItem.innerHTML = `
                        <input type="checkbox" 
                               id="${type}_user_${user.id}" 
                               name="${type}[]" 
                               value="${user.id}"
                               ${isChecked ? 'checked' : ''}
                               onchange="updateSelectedBadges('${type}')">
                        <label for="${type}_user_${user.id}">
                            <span>${user.name}</span>
                            <span class="user-role">${user.role || 'User'}</span>
                        </label>
                    `;
                    
                    container.appendChild(checkboxItem);
                });
            });
            
            // Update selected badges
            updateSelectedBadges(type);
        }
        
        // Select all users
        function selectAllUsers(type) {
            const checkboxes = document.querySelectorAll(`#${type}CheckboxContainer input[type="checkbox"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedBadges(type);
        }
        
        // Deselect all users
        function deselectAllUsers(type) {
            const checkboxes = document.querySelectorAll(`#${type}CheckboxContainer input[type="checkbox"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedBadges(type);
        }
        
        // Update selected user badges
        function updateSelectedBadges(type) {
            const selectedDiv = document.getElementById(`selected${type.charAt(0).toUpperCase() + type.slice(1)}`);
            const checkboxes = document.querySelectorAll(`#${type}CheckboxContainer input[type="checkbox"]:checked`);
            
            if (!selectedDiv) return;
            
            selectedDiv.innerHTML = '';
            
            if (checkboxes.length === 0) {
                selectedDiv.innerHTML = `<span class="text-sm text-gray-400">Belum ada ${type} dipilih</span>`;
                return;
            }
            
            checkboxes.forEach(checkbox => {
                const label = checkbox.nextElementSibling;
                const userName = label.querySelector('span').textContent;
                
                const badge = document.createElement('span');
                badge.className = 'selected-user-badge';
                badge.innerHTML = `
                    ${userName}
                    <button type="button" onclick="deselectUser('${type}', '${checkbox.value}')" class="ml-1">
                        <span class="material-icons-outlined text-xs">close</span>
                    </button>
                `;
                selectedDiv.appendChild(badge);
            });
        }
        
        // Deselect user
        function deselectUser(type, userId) {
            const checkbox = document.querySelector(`#${type}CheckboxContainer input[value="${userId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedBadges(type);
            }
        }
        
        // Handle live search in peserta / penugasan inputs
        function handleSearchUser(type) {
            try {
                const container = document.getElementById(`${type}CheckboxContainer`);

                // Preserve currently selected IDs so selections are not lost when re-rendering
                const selectedIds = container ? Array.from(container.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value.toString()) : [];

                // Re-populate checkboxes using the current search input value (populateUserCheckboxes reads the search input)
                populateUserCheckboxes(type, selectedIds);
            } catch (e) {
                console.error('handleSearchUser error:', e);
            }
        }
// PERBAIKAN: loadCatatanRapatData function
function loadCatatanRapatData() {
    const loadingState = document.getElementById('loadingState');
    const tableContainer = document.getElementById('tableContainer');
    const emptyState = document.getElementById('emptyState');
    const mobileCards = document.getElementById('mobileCards');
    
    // Show loading
    loadingState.classList.remove('hidden');
    tableContainer.classList.add('hidden');
    emptyState.classList.add('hidden');
    mobileCards.classList.add('hidden');
    
    // PERBAIKAN: URL dan sintaks yang benar
    fetch('/catatan-rapat/data', {  // <-- Perhatikan format URL yang benar
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server error: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        loadingState.classList.add('hidden');
        
        if (data.success && data.data && data.data.length > 0) {
            // Store all catatan rapat data
            allCatatanRapat = data.data;
            filteredData = [...data.data];
            
            // Show table
            tableContainer.classList.remove('hidden');
            mobileCards.classList.remove('hidden');
            
            // Apply filters and display
            applyFilters();
        } else {
            // Show empty state
            emptyState.classList.remove('hidden');
            
            // Update total count
            document.getElementById('totalCount').textContent = '0';
        }
    })
    .catch(error => {
        loadingState.classList.add('hidden');
        console.error('Error loading catatan rapat data:', error);
        showNotification('Error', 'Gagal memuat data catatan rapat: ' + error.message, 'error');
    });
} 
        // Display current page data
        function displayCurrentPage() {
            const tableBody = document.getElementById('tableBody');
            const mobileCards = document.getElementById('mobileCards');
            
            // Clear existing content
            tableBody.innerHTML = '';
            mobileCards.innerHTML = '';
            
            // Calculate pagination
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
            const pageData = filteredData.slice(startIndex, endIndex);
            
            // Update pagination info
            updatePaginationInfo(startIndex + 1, endIndex, filteredData.length);
            
            // Populate desktop table
            pageData.forEach((catatan, index) => {
                // Format date
                const date = catatan.tanggal ? new Date(catatan.tanggal).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) : '-';
                
                // Truncate text if too long
                let hasilDisplay = catatan.hasil_diskusi || '';
                if (hasilDisplay.length > 50) {
                    hasilDisplay = hasilDisplay.substring(0, 50) + '...';
                }
                
                let keputusanDisplay = catatan.keputusan || '';
                if (keputusanDisplay.length > 50) {
                    keputusanDisplay = keputusanDisplay.substring(0, 50) + '...';
                }
                
                // Format peserta and penugasan
                const pesertaDisplay = formatUsers(catatan.peserta);
                const penugasanDisplay = formatUsers(catatan.penugasan);
                
                // Create table row
                const row = document.createElement('tr');
                row.className = 'catatan-rapat-row border-b hover:bg-gray-50';
                row.setAttribute('data-id', catatan.id);
                row.setAttribute('data-tanggal', date);
                row.setAttribute('data-topik', catatan.topik);
                row.setAttribute('data-hasil', catatan.hasil_diskusi);
                row.setAttribute('data-keputusan', catatan.keputusan);
                row.setAttribute('data-peserta', JSON.stringify(catatan.peserta));
                row.setAttribute('data-penugasan', JSON.stringify(catatan.penugasan));
                
                row.innerHTML = `
                    <td class="p-3">${startIndex + index + 1}.</td>
                    <td class="p-3">${date}</td>
                    <td class="p-3 font-medium">${catatan.topik}</td>
                    <td class="p-3 max-w-xs truncate" title="${catatan.hasil_diskusi}">${hasilDisplay}</td>
                    <td class="p-3 max-w-xs truncate" title="${catatan.keputusan}">${keputusanDisplay}</td>
                    <td class="p-3">${pesertaDisplay}</td>
                    <td class="p-3">${penugasanDisplay}</td>
                    <td class="p-3">
                        <div class="flex gap-2 justify-center">
                            <button onclick="viewCatatanRapat(${catatan.id})" 
                                    class="action-btn" title="Lihat">
                                <span class="material-icons-outlined">visibility</span>
                            </button>
                            <button onclick="editCatatanRapat(${catatan.id})" 
                                    class="action-btn edit" title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </button>
                            <button onclick="deleteCatatanRapat(${catatan.id})" 
                                    class="action-btn delete" title="Hapus">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
                
                // Create mobile card
                const card = document.createElement('div');
                card.className = 'border rounded-lg p-4 card-hover';
                card.setAttribute('data-id', catatan.id);
                card.setAttribute('data-tanggal', date);
                card.setAttribute('data-topik', catatan.topik);
                
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold">${catatan.topik}</h3>
                            <p class="text-sm text-gray-500">${date}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="viewCatatanRapat(${catatan.id})" 
                                    class="action-btn" title="Lihat">
                                <span class="material-icons-outlined">visibility</span>
                            </button>
                            <button onclick="editCatatanRapat(${catatan.id})" 
                                    class="action-btn edit" title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </button>
                            <button onclick="deleteCatatanRapat(${catatan.id})" 
                                    class="action-btn delete" title="Hapus">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-text-muted-light">No</p>
                            <p class="font-medium">${startIndex + index + 1}</p>
                        </div>
                        <div>
                            <p class="text-text-muted-light">Peserta</p>
                            <p class="font-medium">${pesertaDisplay}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-text-muted-light">Hasil Diskusi</p>
                            <p class="font-medium">${hasilDisplay}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-text-muted-light">Keputusan</p>
                            <p class="font-medium">${keputusanDisplay}</p>
                        </div>
                    </div>
                `;
                
                mobileCards.appendChild(card);
            });
        }
        
        // Update pagination info
        function updatePaginationInfo(start, end, total) {
            document.getElementById('showingStart').textContent = start;
            document.getElementById('showingEnd').textContent = end;
            document.getElementById('showingTotal').textContent = total;
            document.getElementById('totalCount').textContent = total;
        }
        
        // Render pagination controls
        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const pageNumbersContainer = document.getElementById('pageNumbers');
            const firstBtn = document.getElementById('firstPage');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            const lastBtn = document.getElementById('lastPage');
            
            // Clear existing page numbers
            pageNumbersContainer.innerHTML = '';
            
            // Generate page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            // Add first page and ellipsis if needed
            if (startPage > 1) {
                const firstPageBtn = createPageButton(1);
                pageNumbersContainer.appendChild(firstPageBtn);
                
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'px-2 text-gray-400';
                    ellipsis.textContent = '...';
                    pageNumbersContainer.appendChild(ellipsis);
                }
            }
            
            // Add page numbers
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = createPageButton(i);
                pageNumbersContainer.appendChild(pageBtn);
            }
            
            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'px-2 text-gray-400';
                    ellipsis.textContent = '...';
                    pageNumbersContainer.appendChild(ellipsis);
                }
                
                const lastPageBtn = createPageButton(totalPages);
                pageNumbersContainer.appendChild(lastPageBtn);
            }
            
            // Update navigation buttons state
            firstBtn.disabled = currentPage === 1;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            lastBtn.disabled = currentPage === totalPages || totalPages === 0;
        }
        
        // Create page button
        function createPageButton(pageNum) {
            const button = document.createElement('button');
            button.className = `desktop-page-btn ${pageNum === currentPage ? 'active' : ''}`;
            button.textContent = pageNum;
            button.onclick = () => goToPage(pageNum);
            return button;
        }
        
        // Go to specific page
        function goToPage(page) {
            currentPage = page;
            renderPagination();
            displayCurrentPage();
        }
        
        // Helper function to format users
        function formatUsers(usersArray) {
            if (!Array.isArray(usersArray) || usersArray.length === 0) {
                return '<span class="text-gray-400">-</span>';
            }
            
            const names = usersArray.slice(0, 2).map(user => 
                `<span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">${user.name}</span>`
            );
            
            if (usersArray.length > 2) {
                names.push(`<span class="inline-block px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded">+${usersArray.length - 2}</span>`);
            }
            
            return names.join('');
        }
        
        // View catatan rapat
        function viewCatatanRapat(id) {
            const catatan = allCatatanRapat.find(item => item.id == id);
            
            if (!catatan) {
                showNotification('Error', 'Data tidak ditemukan', 'error');
                return;
            }
            
            // Format date
            const date = catatan.tanggal ? new Date(catatan.tanggal).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }) : '-';
            
            // Format peserta and penugasan
            const pesertaDisplay = formatUsers(catatan.peserta);
            const penugasanDisplay = formatUsers(catatan.penugasan);
            
            const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-text-muted-light mb-1">Tanggal</h4>
                        <p class="font-semibold">${date}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-text-muted-light mb-1">Topik</h4>
                        <p class="font-semibold">${catatan.topik}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-text-muted-light mb-1">Hasil Diskusi</h4>
                    <p class="whitespace-pre-wrap">${catatan.hasil_diskusi}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-text-muted-light mb-1">Keputusan</h4>
                    <p class="whitespace-pre-wrap">${catatan.keputusan}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-text-muted-light mb-1">Peserta</h4>
                    <div class="flex flex-wrap gap-2 mt-1">
                        ${catatan.peserta.map(user => 
                            `<span class="inline-block px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">${user.name}</span>`
                        ).join('')}
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-text-muted-light mb-1">Penugasan</h4>
                    <div class="flex flex-wrap gap-2 mt-1">
                        ${catatan.penugasan.map(user => 
                            `<span class="inline-block px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full">${user.name}</span>`
                        ).join('')}
                    </div>
                </div>
            `;
            
            document.getElementById('viewContent').innerHTML = content;
            document.getElementById('viewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        // Edit catatan rapat - FIXED
        async function editCatatanRapat(id) {
            try {
                currentAction = 'edit';
                currentId = id;
                
                // First try to find data in the loaded data
                let catatan = allCatatanRapat.find(item => item.id == id);
                
                if (catatan) {
                    // Use existing data if available
                    showModal(
                        'Edit Catatan Rapat',
                        getFormTemplate(catatan),
                        'Update'
                    );
                    
                    // Wait for modal to render, then populate users
                    setTimeout(() => {
                        const selectedPesertaIds = catatan.peserta ? catatan.peserta.map(u => u.id.toString()) : [];
                        const selectedPenugasanIds = catatan.penugasan ? catatan.penugasan.map(u => u.id.toString()) : [];
                        
                        populateUserCheckboxes('peserta', selectedPesertaIds);
                        populateUserCheckboxes('penugasan', selectedPenugasanIds);
                    }, 100);
                } else {
                    // Fallback to API call if not found in loaded data
                    const response = await fetch(`/catatan_rapat/${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showModal(
                            'Edit Catatan Rapat',
                            getFormTemplate(result.data),
                            'Update'
                        );
                        
                        // Wait for modal to render, then populate users
                        setTimeout(() => {
                            const selectedPesertaIds = result.data.peserta ? result.data.peserta.map(u => u.id.toString()) : [];
                            const selectedPenugasanIds = result.data.penugasan ? result.data.penugasan.map(u => u.id.toString()) : [];
                            
                            populateUserCheckboxes('peserta', selectedPesertaIds);
                            populateUserCheckboxes('penugasan', selectedPenugasanIds);
                        }, 100);
                    } else {
                        showNotification('Error', result.message || 'Gagal memuat data', 'error');
                    }
                }
            } catch (error) {
                console.error('Error editing catatan rapat:', error);
                showNotification('Error', 'Gagal memuat data catatan rapat: ' + error.message, 'error');
            }
        }
        
        // Delete catatan rapat
        function deleteCatatanRapat(id) {
            currentId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        // Handle form submission - MODIFIED VALIDATION
        async function handleConfirm() {
            try {
                // Get form elements
                const tanggalInput = document.getElementById('tanggalInput');
                const topikInput = document.getElementById('topikInput');
                const hasilDiskusiInput = document.getElementById('hasilDiskusiInput');
                const keputusanInput = document.getElementById('keputusanInput');
                const pesertaCheckboxes = document.querySelectorAll('#pesertaCheckboxContainer input[type="checkbox"]:checked');
                const penugasanCheckboxes = document.querySelectorAll('#penugasanCheckboxContainer input[type="checkbox"]:checked');
                
                // Get values
                const tanggal = tanggalInput.value.trim();
                const topik = topikInput.value.trim();
                const hasilDiskusi = hasilDiskusiInput.value.trim();
                const keputusan = keputusanInput.value.trim();
                const selectedPeserta = Array.from(pesertaCheckboxes).map(cb => cb.value);
                const selectedPenugasan = Array.from(penugasanCheckboxes).map(cb => cb.value);
                
                // Validation - MODIFIED FOR EDIT
                if (currentAction === 'create') {
                    // For create, all fields are required
                    if (!tanggal || !topik || !hasilDiskusi || !keputusan) {
                        showNotification('Error', 'Semua field wajib diisi', 'error');
                        return;
                    }
                } else {
                    // For edit, only validate if field has value (allow partial updates)
                    if (tanggal && !isValidDate(tanggal)) {
                        showNotification('Error', 'Format tanggal tidak valid', 'error');
                        return;
                    }
                }
                
                // Always validate peserta and penugasan
                if (selectedPeserta.length === 0) {
                    showNotification('Error', 'Pilih minimal satu peserta', 'error');
                    return;
                }
                
                if (selectedPenugasan.length === 0) {
                    showNotification('Error', 'Pilih minimal satu penugasan', 'error');
                    return;
                }
                
                // Prepare form data - only include fields with values for edit
                const formData = {};
                
                if (tanggal) formData.tanggal = tanggal;
                if (topik) formData.topik = topik;
                if (hasilDiskusi) formData.hasil_diskusi = hasilDiskusi;
                if (keputusan) formData.keputusan = keputusan;
                
                // Always include peserta and penugasan
                formData.peserta = selectedPeserta;
                formData.penugasan = selectedPenugasan;
                
                // Determine URL and method
                let url = '/catatan-rapat';
                let method = 'POST';
                
                if (currentAction === 'edit') {
                    url = `/catatan-rapat/${currentId}`;
                    method = 'PUT';
                }
                
                // Show loading
                showSubmitLoading(true);
                
                // Send request
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                // Hide loading
                showSubmitLoading(false);
                
                if (result.success) {
                    showNotification('Berhasil', result.message || 'Catatan rapat berhasil disimpan', 'success');
                    setTimeout(() => {
                        closeModal();
                        loadCatatanRapatData();
                    }, 1500);
                } else {
                    let errorMsg = result.message || 'Terjadi kesalahan';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join(', ');
                    }
                    showNotification('Error', errorMsg, 'error');
                }
                
            } catch (error) {
                console.error('Error saving catatan rapat:', error);
                showSubmitLoading(false);
                showNotification('Error', 'Gagal menyimpan: ' + error.message, 'error');
            }
        }
        
        // Helper function to validate date
        function isValidDate(dateString) {
            const date = new Date(dateString);
            return date instanceof Date && !isNaN(date);
        }
        
        // Confirm delete
        document.getElementById('confirmDelete').addEventListener('click', async function() {
            try {
                const response = await fetch(`/catatan-rapat/${currentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Berhasil', result.message || 'Catatan rapat berhasil dihapus', 'success');
                    hideModal('deleteModal');
                    loadCatatanRapatData();
                } else {
                    showNotification('Error', result.message || 'Gagal menghapus catatan rapat', 'error');
                }
            } catch (error) {
                console.error('Error deleting catatan rapat:', error);
                showNotification('Error', 'Gagal menghapus catatan rapat', 'error');
            }
        });
        
        // Filter data - CLIENT SIDE FILTERING
        function filterData() {
            searchTerm = document.getElementById('searchInput').value.toLowerCase();
            applyFilters();
        }
        
        // Initialize filter
        function initializeFilter() {
            const filterAll = document.getElementById('filterAll');
            const applyFilterBtn = document.getElementById('applyFilter');
            const resetFilterBtn = document.getElementById('resetFilter');
            const filterDropdown = document.getElementById('filterDropdown');

            // If required elements are not present on the page, skip initialization
            if (!filterAll || !applyFilterBtn || !resetFilterBtn) {
                console.debug('Filter elements not found; skipping initializeFilter');
                return;
            }

            // Handle "All" checkbox
            filterAll.addEventListener('change', function() {
                if (this.checked) {
                    // Uncheck all other checkboxes
                    document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                        cb.checked = false;
                    });
                }
            });
            // Handle other checkboxes
            document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        // Uncheck "All" checkbox
                        filterAll.checked = false;
                    }
                });
            });

            // Apply filter - CLIENT SIDE FILTERING
            applyFilterBtn.addEventListener('click', function() {
                const filterAllEl = document.getElementById('filterAll');
                const filterManagement = document.getElementById('filterManagement');
                const filterTeknis = document.getElementById('filterTeknis');
                const filterInternal = document.getElementById('filterInternal');

                activeFilters = [];
                if (filterAllEl && filterAllEl.checked) {
                    activeFilters.push('all');
                } else {
                    if (filterManagement && filterManagement.checked) activeFilters.push('management');
                    if (filterTeknis && filterTeknis.checked) activeFilters.push('teknis');
                    if (filterInternal && filterInternal.checked) activeFilters.push('internal');
                }

                // Apply client-side filtering
                applyFilters();
                if (filterDropdown) filterDropdown.classList.remove('show');
            });

            // Reset filter - CLIENT SIDE FILTERING
            resetFilterBtn.addEventListener('click', function() {
                const fAll = document.getElementById('filterAll');
                if (fAll) fAll.checked = true;
                document.getElementById('filterManagement').checked = false;
                document.getElementById('filterTeknis').checked = false;
                document.getElementById('filterInternal').checked = false;
                activeFilters = ['all'];
                
                // Apply client-side filtering
                applyFilters();
                document.getElementById('filterDropdown').classList.remove('show');
            });
        }
        
        // Apply filters - CLIENT SIDE FILTERING
        function applyFilters() {
            // Start with all data
            filteredData = allCatatanRapat.filter(item => {
                // Check if status matches filter
                let statusMatches = false;
                if (activeFilters.includes('all')) {
                    statusMatches = true;
                } else {
                    statusMatches = activeFilters.some(filter => 
                        item.topik.toLowerCase().includes(filter.toLowerCase())
                    );
                }
                
                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = item.topik.toLowerCase().includes(searchLower) || 
                                   item.hasil_diskusi.toLowerCase().includes(searchLower) ||
                                   item.keputusan.toLowerCase().includes(searchLower) ||
                                   (item.peserta && item.peserta.some(p => p.name.toLowerCase().includes(searchLower))) ||
                                   (item.penugasan && item.penugasan.some(p => p.name.toLowerCase().includes(searchLower)));
                }
                
                return statusMatches && searchMatches;
            });
            
            // Reset to first page
            currentPage = 1;
            
            // Update display
            renderPagination();
            displayCurrentPage();
        }
        
        // Pagination navigation event listeners
        document.getElementById('firstPage').addEventListener('click', () => {
            if (currentPage > 1) goToPage(1);
        });
        
        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) goToPage(currentPage - 1);
        });
        
        document.getElementById('nextPage').addEventListener('click', () => {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (currentPage < totalPages) goToPage(currentPage + 1);
        });
        
        document.getElementById('lastPage').addEventListener('click', () => {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            goToPage(totalPages);
        });
        
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
        
        // Show loading state
        function showLoading(show, type = 'general') {
            if (type === 'users') {
                // Handle users loading if needed
                return;
            }
            
            const loadingState = document.getElementById('loadingState');
            const tableContainer = document.getElementById('tableContainer');
            const emptyState = document.getElementById('emptyState');
            const mobileCards = document.getElementById('mobileCards');
            
            if (show) {
                loadingState.classList.remove('hidden');
                if (tableContainer) tableContainer.classList.add('hidden');
                if (mobileCards) mobileCards.classList.add('hidden');
                if (emptyState) emptyState.classList.add('hidden');
            } else {
                loadingState.classList.add('hidden');
            }
        }
        
        function showSubmitLoading(show) {
            const confirmBtnText = document.getElementById('confirmBtnText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const confirmBtn = document.getElementById('confirmBtn');
            
            if (show) {
                confirmBtn.disabled = true;
                confirmBtnText.classList.add('hidden');
                loadingSpinner.classList.remove('hidden');
            } else {
                confirmBtn.disabled = false;
                confirmBtnText.classList.remove('hidden');
                loadingSpinner.classList.add('hidden');
            }
        }
        
        // Notification functions
        function showNotification(title, message, type = 'success') {
            const notif = document.getElementById('notification');
            const icon = notif.querySelector('.minimal-popup-icon span');
            const notifTitle = notif.querySelector('.minimal-popup-title');
            const notifMessage = notif.querySelector('.minimal-popup-message');
            
            // Set styles based on type
            notif.className = 'minimal-popup show';
            
            if (type === 'success') {
                notif.classList.add('success');
                icon.textContent = 'check';
            } else if (type === 'error') {
                notif.classList.add('error');
                icon.textContent = 'error';
            } else if (type === 'info') {
                notif.classList.add('info');
                icon.textContent = 'info';
            }
            
            // Set content
            notifTitle.textContent = title;
            notifMessage.textContent = message;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                notif.classList.remove('show');
            }, 5000);
        }
        
        function hideNotif() {
            document.getElementById('notification').classList.remove('show');
        }
    </script>
</body>
</html>