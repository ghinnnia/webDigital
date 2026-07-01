<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kwitansi Management - Finance</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
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
            min-width: 1400px;
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Styles untuk cetak kwitansi */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            font-family: 'Arial', sans-serif;
            box-sizing: border-box;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #cetakContent,
            #cetakContent * {
                visibility: visible;
            }

            #cetakContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .print-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }
        }

        .kwitansi-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .kwitansi-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            text-decoration: underline;
        }

        .kwitansi-info {
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        .table-container {
            width: 100%;
            margin: 30px 0;
            border-collapse: collapse;
        }

        .table-container table {
            width: 100%;
            border: 1px solid #333;
        }

        .table-container th,
        .table-container td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .table-container th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-section {
            text-align: right;
            margin-top: 30px;
        }

        .total-row {
            margin-bottom: 5px;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }

        .footer-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-area {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #333;
            width: 100%;
        }

        .signature-name {
            margin-top: 5px;
            font-weight: bold;
        }

        .signature-title {
            font-size: 12px;
            color: #666;
        }

        .payment-info {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #333;
            background-color: #f9f9f9;
        }

        .status-badge-print {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-lunas {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pembayaran-awal {
            background-color: #fff3cd;
            color: #856404;
        }

        .watermark {
            position: absolute;
            opacity: 0.1;
            font-size: 80px;
            transform: rotate(-45deg);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            color: #ccc;
            z-index: -1;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('finance/templet/sider')

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Kelola Kwitansi</h2>

                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span
                            class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                            placeholder="Cari nama perusahaan, atau klien..." type="text" />
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
                                    <input type="checkbox" id="filterPaid" value="paid">
                                    <label for="filterPaid">Paid</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterUnpaid" value="unpaid">
                                    <label for="filterUnpaid">Unpaid</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterPending" value="pending">
                                    <label for="filterPending">Pending</label>
                                </div>
                                <div class="filter-actions">
                                    <button id="applyFilter" class="filter-apply">Terapkan</button>
                                    <button id="resetFilter" class="filter-reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <button id="buatKwitansiBtn"
                            class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Buat Kwitansi</span>
                            <span class="sm:hidden">Buat</span>
                        </button>
                    </div>
                </div>

                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">receipt</span>
                            Daftar Kwitansi
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount"
                                    class="font-semibold text-text-light">0</span> kwitansi</span>
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
                                            <th style="min-width: 120px;">Tanggal</th>
                                            <th style="min-width: 180px;">Nama Perusahaan</th>
                                            <th style="min-width: 120px;">Kontak</th>
                                            <th style="min-width: 150px;">Nomor Invoice</th>
                                            <th style="min-width: 150px;">Nama Klien</th>
                                            <th style="min-width: 150px;">Nama Layanan</th>
                                            <th style="min-width: 200px;">Deskripsi</th>
                                            <th style="min-width: 120px;">Harga</th>
                                            <th style="min-width: 120px;">Pajak</th>
                                            <th style="min-width: 150px;">Fee Maintenance</th>
                                            <th style="min-width: 120px;">Total</th>
                                            <th style="min-width: 120px;">Metode Pembayaran</th>
                                            <th style="min-width: 120px;">Jenis Bank</th>
                                            <th style="min-width: 140px;">Kategori Pemasukan</th>
                                            <th style="min-width: 100px;">Status</th>
                                            <th style="min-width: 150px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        <!-- Data will be populated here -->
                                        <tr id="loadingRow">
                                            <td colspan="18" class="px-6 py-4 text-center">
                                                <div class="flex justify-center items-center">
                                                    <div class="spinner"></div>
                                                    <span class="ml-2">Memuat data...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="noDataRow" class="hidden">
                                            <td colspan="18" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Tidak ada data kwitansi
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4" id="mobile-cards">
                            <!-- Mobile cards will be populated here -->
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
                Copyright ©2025 by digital kolaborasi.id
            </footer>
        </main>
    </div>

    <!-- Modal Buat Kwitansi -->
    <div id="buatKwitansiModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Buat Kwitansi Baru dari Invoice</h3>
                    <button id="closeModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-blue-800">
                        <span class="font-semibold">📋 Cara Menggunakan:</span> Pilih invoice dari dropdown, 
                        data dari invoice akan otomatis terisi. Anda hanya perlu mengatur tanggal kwitansi.
                    </p>
                </div>
                <form id="buatKwitansiForm" class="space-y-4">
                    @csrf
                    <!-- Pemilihan Invoice -->
                    <div class="border-b pb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Invoice <span class="text-red-500">*</span></label>
                            <select id="pilihInvoice" name="invoice_id"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                                <option value="">-- Pilih Invoice --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal Kwitansi -->
                    <div class="border-b pb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kwitansi <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal" name="tanggal"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Data lainnya akan otomatis terisi dari invoice yang dipilih</p>
                        </div>
                    </div>

                    <!-- Informasi Perusahaan -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-blue-500">business</span>
                            Informasi Perusahaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                                <input type="text" id="namaPerusahaan" name="nama_perusahaan"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Perusahaan</label>
                                <input type="text" id="kontakPerusahaan" name="kontak"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Klien</label>
                                <input type="text" id="namaKlien" name="nama_klien"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Perusahaan</label>
                                <input type="text" id="alamatPerusahaan" name="company_address"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Layanan -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-green-500">description</span>
                            Informasi Layanan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                                <input type="text" id="namaLayanan" name="nama_layanan"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Layanan (Rp)</label>
                                <input type="number" id="hargaLayanan" name="harga_layanan"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed resize-none"
                                readonly></textarea>
                        </div>
                    </div>

                    <!-- Informasi Kwitansi -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-purple-500">receipt</span>
                            Informasi Invoice
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice</label>
                                <input type="text" id="invoiceNo" name="invoice_no"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <!-- Metode Pembayaran removed from here and moved to Informasi Pembayaran -->
                        </div>
                    </div>

                    <!-- Perhitungan Harga -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-yellow-500">payments</span>
                            Perhitungan Harga
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                <input type="number" id="harga" name="harga"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pajak (Rp)</label>
                                <input type="number" id="pajak" name="tax"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Maintenance (Rp)</label>
                                <input type="number" id="feeMaintenance" name="fee_maintenance"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total (Rp)</label>
                            <input type="text" id="total" name="total_display"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 font-semibold cursor-not-allowed"
                                readonly>
                        </div>
                    </div>

                    <!-- Informasi Pembayaran -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-indigo-500">account_balance</span>
                            Informasi Pembayaran
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                                <input type="text" id="metodePembayaran" name="payment_method"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                                <input type="text" id="statusPembayaran" name="status_display"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bank</label>
                                <input type="text" id="jenisBank" name="jenis_bank"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Pemasukan</label>
                                <input type="text" id="kategoriPemasukan" name="kategori_pemasukan"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Tambahan (dipisah dari Informasi Pembayaran) -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-gray-600">note</span>
                            Keterangan Tambahan
                        </h4>
                        <div>
                            <textarea id="keteranganTambahan" name="keterangan_tambahan" rows="3"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed resize-none"
                                readonly></textarea>
                        </div>
                    </div>

                    <!-- Hidden fields for numeric values -->
                    <input type="hidden" id="totalHidden" name="total">
                    <input type="hidden" id="subTotalHidden" name="sub_total">
                    <input type="hidden" id="statusHidden" name="status" value="Pembayaran Awal">

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Buat Kwitansi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kwitansi -->
    <div id="editKwitansiModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Kwitansi</h3>
                    <button id="closeEditModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>

                <!-- Info Kwitansi Display (Read-only) -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 space-y-2">
                    <p class="text-sm text-blue-900">
                        <span class="font-semibold">Nama Perusahaan:</span> <span id="displayNamaPerusahaan" class="text-gray-700">-</span>
                    </p>
                    <p class="text-sm text-blue-900">
                        <span class="font-semibold">Klien:</span> <span id="displayNamaKlien" class="text-gray-700">-</span>
                    </p>
                    <p class="text-sm text-blue-900">
                        <span class="font-semibold">Total:</span> <span id="displayTotal" class="text-gray-700">-</span>
                    </p>
                </div>

                <form id="editKwitansiForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="editKwitansiId" name="id">

                    <!-- Edit Field 1: Tanggal -->
                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kwitansi <span class="text-red-500">*</span></label>
                        <input type="date" id="editTanggal" name="tanggal"
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                            required>
                    </div>

                    <!-- Edit Field 2: Status Pembayaran -->
                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran <span class="text-red-500">*</span></label>
                        <select id="editStatus" name="status"
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                            required>
                            <option value="">Pilih Status</option>
                            <option value="Pembayaran Awal">Pembayaran Awal</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelEditBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Kwitansi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Cetak Kwitansi -->
    <div id="cetakKwitansiModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Cetak Kwitansi</h3>
                    <button id="closeCetakModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <div id="cetakContent" class="space-y-4">
                    <!-- Content will be loaded here -->
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" id="cancelCetakBtn"
                        class="px-4 py-2 btn-secondary rounded-lg">Tutup</button>
                    <button type="button" id="printBtn" class="px-4 py-2 btn-primary rounded-lg">🖨️ Cetak</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden canvas for PDF generation -->
    <canvas id="pdfCanvas" style="display: none;"></canvas>


    <!-- Modal Hapus Kwitansi -->
    <div id="deleteKwitansiModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button id="closeDeleteModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="deleteKwitansiForm" class="space-y-4">
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus kwitansi untuk <span id="deleteKwitansiNama" class="font-semibold">-</span>?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteKwitansiId" name="id">
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

    <script>
        // Function to load kwitansi data for printing
        // Ganti fungsi loadKwitansiForPrint() dengan:
        function loadKwitansiForPrint(id) {
            // Show loading
            const cetakContent = document.getElementById('cetakContent');
            cetakContent.innerHTML = '<div class="text-center py-8">Memuat data kwitansi...</div>';

            // Gunakan route yang benar
            const url = CETAK_ROUTE.replace('ID', id);

            // Fetch kwitansi data
            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        renderPrintContent(data.data);
                    } else {
                        cetakContent.innerHTML =
                            '<div class="text-center py-8 text-red-500">' +
                            (data.message || 'Gagal memuat data kwitansi') +
                            '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading kwitansi for print:', error);
                    cetakContent.innerHTML =
                        '<div class="text-center py-8 text-red-500">' +
                        'Terjadi kesalahan saat memuat data' +
                        '</div>';
                });
        }

        // Function to render print content
        function renderPrintContent(kwitansi) {
            const cetakContent = document.getElementById('cetakContent');

            // Format date
            let formattedDate = '';
            if (kwitansi.tanggal) {
                const date = new Date(kwitansi.tanggal);
                formattedDate = date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            // Format amounts
            const formattedHarga = new Intl.NumberFormat('id-ID').format(kwitansi.harga);
            const formattedSubTotal = new Intl.NumberFormat('id-ID').format(kwitansi.sub_total);
            const formattedFeeMaintenance = new Intl.NumberFormat('id-ID').format(kwitansi.fee_maintenance);
            const formattedTotal = new Intl.NumberFormat('id-ID').format(kwitansi.total);

            // Status badge
            let statusBadge = '';
            if (kwitansi.status === 'Lunas') {
                statusBadge = '<span class="status-badge-print status-lunas">Lunas</span>';
            } else {
                statusBadge = '<span class="status-badge-print status-pembayaran-awal">Pembayaran Awal</span>';
            }

            // Create print HTML
            const printHTML = `
        <div class="print-container">
            <div class="watermark">KWITANSI</div>
            
            <div class="kwitansi-header">
                <div class="company-name">digital kolaborasi.ID</div>
                <div class="company-address">
                    Jl. Contoh No. 123, Jakarta Selatan<br>
                    Telp: (021) 12345678 | Email: info@digital kolaborasi.id<br>
                    Website: www.digital kolaborasi.id
                </div>
            </div>
            
            <div class="kwitansi-title">KWITANSI PEMBAYARAN</div>
            
            <div class="kwitansi-info">
                <div class="info-row">
                    <div class="info-label">Nomor Kwitansi:</div>
                    <div class="info-value">${kwitansi.kwitansi_no || 'KW-' + new Date().getFullYear() + '-' + kwitansi.id.toString().padStart(5, '0')}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal:</div>
                    <div class="info-value">${formattedDate}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">${statusBadge}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Perusahaan:</div>
                    <div class="info-value">${kwitansi.nama_perusahaan}</div>
                </div>
                <div class="info-row">
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Klien:</div>
                    <div class="info-value">${kwitansi.nama_klien}</div>
                </div>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th>Fee Maintenance</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${kwitansi.deskripsi || '-'}</td>
                            <td>Rp ${formattedHarga}</td>
                            <td>Rp ${formattedSubTotal}</td>
                            <td>Rp ${formattedFeeMaintenance}</td>
                            <td>Rp ${formattedTotal}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="total-section">
                <div class="total-row">
                    <strong>Sub Total: Rp ${formattedSubTotal}</strong>
                </div>
                <div class="total-row">
                    <strong>Fee Maintenance: Rp ${formattedFeeMaintenance}</strong>
                </div>
                <div class="grand-total">
                    <strong>TOTAL: Rp ${formattedTotal}</strong>
                </div>
            </div>
            
            <div class="payment-info">
                <h4>Informasi Pembayaran:</h4>
                <p>Bank: ${kwitansi.bank || 'BRI'}</p>
                <p>No. Rekening: ${kwitansi.no_rekening || '4030-01-011093-53-6'}</p>
                <p>a.n. digital kolaborasi.ID</p>
            </div>
            
            <div class="footer-section">
                <div class="signature-area">
                    <div>Diterima Oleh,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">${kwitansi.nama_klien}</div>
                    <div class="signature-title">Klien</div>
                </div>
                
                <div class="signature-area">
                    <div>Hormat Kami,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">Admin</div>
                    <div class="signature-title">digital kolaborasi.ID</div>
                </div>
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Kwitansi ini sah dan dapat digunakan sebagai bukti pembayaran yang sah.</p>
                <p>Terima kasih atas kerjasamanya.</p>
            </div>
        </div>
    `;

            cetakContent.innerHTML = printHTML;
        }

        // Function to open print modal
        function openPrintModal(id) {
            console.log('Opening print modal for ID:', id); // Debug log
            console.log('Route:', CETAK_ROUTE.replace('ID', id)); // Debug route

            var modal = document.getElementById('cetakKwitansiModal');
            if (modal) modal.classList.remove('hidden');
            if (document.body) document.body.style.overflow = 'hidden';
            loadKwitansiForPrint(id);
        }

        // Function to close print modal
        function closePrintModal() {
            var modal = document.getElementById('cetakKwitansiModal');
            if (modal) modal.classList.add('hidden');
            if (document.body) document.body.style.overflow = 'auto';
        }

        // Function to print kwitansi (new version)
        function cetakKwitansi(id) {
            // Show print modal first
            openPrintModal(id);
        }

        // Update cetak button event listeners
        function updateCetakButtonListeners() {
            document.querySelectorAll('.cetak-kwitansi-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    cetakKwitansi(id);
                });
            });
        }

        // Add print functionality
        function setupPrintFunctionality() {
            // Print button in modal
            var printBtn = document.getElementById('printBtn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }

            // Close print modal buttons
            var closeCetakModalBtn = document.getElementById('closeCetakModalBtn');
            if (closeCetakModalBtn) closeCetakModalBtn.addEventListener('click', closePrintModal);
            var cancelCetakBtn = document.getElementById('cancelCetakBtn');
            if (cancelCetakBtn) cancelCetakBtn.addEventListener('click', closePrintModal);

            // Close modal when clicking outside
            var cetakKwitansiModal = document.getElementById('cetakKwitansiModal');
            if (cetakKwitansiModal) {
                cetakKwitansiModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closePrintModal();
                    }
                });
            }
        }

        // Initialize print functionality
        document.addEventListener('DOMContentLoaded', function() {
            setupPrintFunctionality();
        });

        // Update the cetakKwitansi function to use modal instead of new window
        function cetakKwitansi(id) {
            openPrintModal(id);
        }
        // Deklarasi konstanta route untuk cetak
        const CETAK_ROUTE = "{{ route('finance.kwitansi.cetak.data', ['id' => 'ID']) }}";
        // ===========================================

        // Declare all global variables at the top level
        let currentPage = 1;
        const itemsPerPage = 5;
        let activeFilters = ['all'];
        let searchTerm = '';
        let allKwitansi = []; // Store all kwitansi data

        document.addEventListener('DOMContentLoaded', function() {
            // Set today's date as default for the date input
            const today = new Date().toISOString().split('T')[0];
            const tanggalInput = document.getElementById('tanggal');
            if (tanggalInput) {
                tanggalInput.value = today;
            }

            // Load invoice options when page loads
            loadInvoiceOptions();

            // Load kwitansi data on page load
            loadKwitansiData();

            // Event listener for search input
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        searchTerm = this.value.trim();
                        currentPage = 1; // Reset to first page on search
                        applyFilters();
                    }, 300); // Debounce search
                });
            }


            // Event listener for filter button
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            if (filterBtn && filterDropdown) {
                filterBtn.addEventListener('click', function() {
                    filterDropdown.classList.toggle('show');
                });

                // Close filter dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('#filterDropdown') && !event.target.closest('#filterBtn')) {
                        filterDropdown.classList.remove('show');
                    }
                });

                // Prevent dropdown from closing when clicking inside
                filterDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Event listener for Buat Kwitansi button
            const buatKwitansiBtn = document.getElementById('buatKwitansiBtn');
            const buatKwitansiModal = document.getElementById('buatKwitansiModal');
            if (buatKwitansiBtn && buatKwitansiModal) {
                buatKwitansiBtn.addEventListener('click', function() {
                    buatKwitansiModal.classList.remove('hidden');
                    if (document.body) document.body.style.overflow = 'hidden';
                });

                // Close modal when clicking outside
                buatKwitansiModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeBuatKwitansiModal();
                    }
                });
            }

            const editKwitansiModal = document.getElementById('editKwitansiModal');
            if (editKwitansiModal) {
                editKwitansiModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeEditKwitansiModal();
                    }
                });
            }

            const deleteKwitansiModal = document.getElementById('deleteKwitansiModal');
            if (deleteKwitansiModal) {
                deleteKwitansiModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteKwitansiModal();
                    }
                });
            }
            document.querySelectorAll('.cetak-kwitansi-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    cetakKwitansi(id);
                });
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    var buatKwitansiModal = document.getElementById('buatKwitansiModal');
                    if (buatKwitansiModal && !buatKwitansiModal.classList.contains('hidden')) {
                        closeBuatKwitansiModal();
                    }
                    var editKwitansiModal = document.getElementById('editKwitansiModal');
                    if (editKwitansiModal && !editKwitansiModal.classList.contains('hidden')) {
                        closeEditKwitansiModal();
                    }
                    var deleteKwitansiModal = document.getElementById('deleteKwitansiModal');
                    if (deleteKwitansiModal && !deleteKwitansiModal.classList.contains('hidden')) {
                        closeDeleteKwitansiModal();
                    }
                }
            });

            // Close popup when clicking the close button
            document.querySelector('.minimal-popup-close').addEventListener('click', function() {
                document.getElementById('minimalPopup').classList.remove('show');
            });

            // Modal close buttons
            document.getElementById('closeModalBtn').addEventListener('click', closeBuatKwitansiModal);
            document.getElementById('cancelBtn').addEventListener('click', closeBuatKwitansiModal);
            document.getElementById('closeEditModalBtn').addEventListener('click', closeEditKwitansiModal);
            document.getElementById('cancelEditBtn').addEventListener('click', closeEditKwitansiModal);
            document.getElementById('closeDeleteModalBtn').addEventListener('click', closeDeleteKwitansiModal);
            document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteKwitansiModal);

            // Form submissions
            document.getElementById('buatKwitansiForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitBuatKwitansi();
            });

            document.getElementById('editKwitansiForm').addEventListener('submit', function(e) {
                e.preventDefault();
                // Actual submit handler is attached later in the file
                // This prevents calling a now-removed wrapper function
            });

            document.getElementById('deleteKwitansiForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('deleteKwitansiId').value;
                deleteKwitansi(id);
            });

            // Initialize filter
            initializeFilter();

            // Initialize scroll detection for table
            initializeScrollDetection();

            // Event listener for invoice selection
            const pilihInvoice = document.getElementById('pilihInvoice');
            if (pilihInvoice) {
                pilihInvoice.addEventListener('change', function() {
                    const invoiceId = this.value;
                    if (invoiceId) {
                        console.log('Fetching invoice details for ID:', invoiceId);
                        
fetch(`/finance/api/invoice-for-kwitansi/${invoiceId}`, {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
    },
    credentials: 'same-origin'
})
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(
                                        `Server error: ${response.status} ${response.statusText}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Invoice data received:', data);
                                
                                if (data.success && data.data) {
                                    // Helper function to safely set value with logging
                                    const setElementValue = (id, value) => {
                                        const elem = document.getElementById(id);
                                        if (elem) {
                                            elem.value = value || '';
                                            console.log(`✓ Set ${id}:`, value);
                                        } else {
                                            console.warn(`✗ Element not found: ${id}`);
                                        }
                                    };

                                    // Calculate values first before using them
                                    const subtotal = parseFloat(data.data.subtotal) || 0;
                                    const tax = parseFloat(data.data.tax) || 0;
                                    const feeMaintenance = parseFloat(data.data.fee_maintenance) || 0;
                                    const total = subtotal + tax + feeMaintenance;

                                    console.log('Calculated totals:', {
                                        subtotal,
                                        tax,
                                        feeMaintenance,
                                        total
                                    });

                                    // Populate company information
                                    setElementValue('namaPerusahaan', data.data.company_name);
                                    setElementValue('kontakPerusahaan', data.data.kontak);
                                    setElementValue('namaKlien', data.data.client_name);
                                    setElementValue('alamatPerusahaan', data.data.company_address);
                                    
                                    // Populate service information
                                    setElementValue('namaLayanan', data.data.nama_layanan);
                                    setElementValue('hargaLayanan', subtotal);
                                    setElementValue('deskripsi', data.data.description);
                                    
                                    // Populate invoice information
                                    setElementValue('invoiceNo', data.data.invoice_no);
                                    setElementValue('metodePembayaran', data.data.payment_method);
                                    
                                    // Populate price calculation
                                    setElementValue('harga', subtotal);
                                    setElementValue('pajak', tax);
                                    setElementValue('feeMaintenance', feeMaintenance);
                                    
                                    // Set hidden fields for backend
                                    setElementValue('totalHidden', total);
                                    setElementValue('subTotalHidden', subtotal);
                                    
                                    // Populate payment information
                                    setElementValue('jenisBank', data.data.jenis_bank);
                                    setElementValue('kategoriPemasukan', data.data.kategori_pemasukan);
                                    setElementValue('keteranganTambahan', data.data.keterangan_tambahan);
                                    
                                    // Display total in formatted rupiah
                                    setElementValue('total', formatToRupiah(total));
                                    
                                    console.log('Form population completed successfully');
                                    showMinimalPopup('Success', 'Data invoice berhasil dimuat', 'success');
                                } else {
                                    console.error('API Error:', data.message);
                                    showMinimalPopup('Error', data.message ||
                                        'Gagal mengambil data invoice', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching invoice details:', error);
                                showMinimalPopup('Error',
                                    'Terjadi kesalahan saat mengambil data invoice: ' + error.message, 'error');
                            });
                    }
                });
            }

            // Event listeners for automatic total calculation in create form
            // Note: These fields are now disabled, so we only recalculate when invoice is selected
            // (see below in the pilihInvoice event listener)

            // Event listeners for automatic total calculation in edit form
            const editSubTotal = document.getElementById('editSubTotal');
            const editFeeMaintenance = document.getElementById('editFeeMaintenance');
            if (editSubTotal) editSubTotal.addEventListener('input', calculateEditTotal);
            if (editFeeMaintenance) editFeeMaintenance.addEventListener('input', calculateEditTotal);
        });

        // Function to format number to Rupiah
        function formatToRupiah(number) {
            if (isNaN(number)) return 'Rp 0';
            return 'Rp ' + parseFloat(number).toLocaleString('id-ID');
        }

        // Function to parse Rupiah string back to number
        function parseRupiah(rupiahString) {
            if (typeof rupiahString !== 'string') return 0;
            // Remove "Rp " and all non-digit characters except decimal point
            const cleanString = rupiahString.replace(/Rp\s/g, '').replace(/[^\d.-]/g, '');
            return parseFloat(cleanString) || 0;
        }

        // Fungsi untuk memuat daftar invoice
        function loadInvoiceOptions() {
            const select = document.getElementById('pilihInvoice');

            if (!select) {
                console.error("Elemen #pilihInvoice tidak ditemukan.");
                return;
            }

            // Tampilkan loading
            const originalHtml = select.innerHTML;
            select.innerHTML = '<option value="">Memuat data invoice...</option>';
            // Fetch invoices dari API (correct endpoint)
fetch('/finance/api/invoices-for-kwitansi', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
    },
    credentials: 'same-origin'
})
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Invoice data received:', data); // Debug log

                    // Clear existing options
                    select.innerHTML = '<option value="">-- Pilih Invoice --</option>';

                    // Add invoice options
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(invoice => {
                            const option = document.createElement('option');
                            option.value = invoice.id;

                            // Format teks display
                            let displayText = '';
                            if (invoice.invoice_no) {
                                displayText += `${invoice.invoice_no} - `;
                            }
                            if (invoice.company_name) {
                                displayText += `${invoice.company_name}`;
                            }
                            if (invoice.client_name) {
                                displayText += ` (${invoice.client_name})`;
                            }
                            if (invoice.total) {
                                displayText += ` - Rp ${parseFloat(invoice.total).toLocaleString('id-ID')}`;
                            }

                            option.textContent = displayText;
                            select.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = "";
                        option.textContent = "Tidak ada invoice yang tersedia";
                        select.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error loading invoice options:', error);
                    select.innerHTML = originalHtml;
                    showMinimalPopup('Error', 'Gagal memuat data invoice: ' + error.message, 'error');
                });
        }


        // Load kwitansi data from database
        function loadKwitansiData() {
            const loadingRow = document.getElementById('loadingRow');
            const noDataRow = document.getElementById('noDataRow');
            const tableBody = document.getElementById('desktopTableBody');
            const mobileCards = document.getElementById('mobile-cards');

            // Show loading
            loadingRow.classList.remove('hidden');
            noDataRow.classList.add('hidden');

            // Remove existing kwitansi rows and cards
            const existingRows = tableBody.querySelectorAll('.kwitansi-row');
            existingRows.forEach(row => row.remove());

            const existingCards = mobileCards.querySelectorAll('.kwitansi-card');
            existingCards.forEach(card => card.remove());

            // Fetch data from API - PERBAIKAN: ganti endpoint untuk JSON response
            fetch('/finance/kwitansi?ajax=1', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    loadingRow.classList.add('hidden');

                    if (data.success && data.data && data.data.length > 0) {
                        // Store all kwitansi data
                        allKwitansi = data.data;

                        // Populate table with kwitansi data
                        data.data.forEach((kwitansi, index) => {
                            // Create desktop table row
                            const row = document.createElement('tr');
                            row.className = 'kwitansi-row';
                            row.setAttribute('data-id', kwitansi.id);
                            row.setAttribute('data-tanggal', kwitansi.tanggal);
                            row.setAttribute('data-nama-perusahaan', kwitansi.nama_perusahaan);
                            row.setAttribute('data-kontak', kwitansi.kontak || '');
                            row.setAttribute('data-company-address', kwitansi.company_address || '');
                            row.setAttribute('data-nama-klien', kwitansi.nama_klien);
                            row.setAttribute('data-nama-layanan', kwitansi.nama_layanan || '');
                            row.setAttribute('data-deskripsi', kwitansi.deskripsi || '');
                            row.setAttribute('data-invoice-no', kwitansi.invoice_no || '');
                            row.setAttribute('data-harga', kwitansi.harga);
                            row.setAttribute('data-tax', kwitansi.tax || 0);
                            row.setAttribute('data-sub-total', kwitansi.sub_total);
                            row.setAttribute('data-fee-maintenance', kwitansi.fee_maintenance);
                            row.setAttribute('data-total', kwitansi.total);
                            row.setAttribute('data-payment-method', kwitansi.payment_method || '');
                            row.setAttribute('data-status', kwitansi.status);
                            row.setAttribute('data-jenis-bank', kwitansi.jenis_bank || '');
                            row.setAttribute('data-kategori-pemasukan', kwitansi.kategori_pemasukan || '');
                            row.setAttribute('data-keterangan-tambahan', kwitansi.keterangan_tambahan || '');
                            row.setAttribute('data-no-rekening', kwitansi.no_rekening || '');

                            // Format date
                            let formattedDate = '';
                            if (kwitansi.tanggal) {
                                const date = new Date(kwitansi.tanggal);
                                formattedDate = date.toLocaleDateString('id-ID', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                            }

                            // Format price with thousand separator
                            const formattedHarga = new Intl.NumberFormat('id-ID').format(kwitansi.harga);
                            const formattedSubTotal = new Intl.NumberFormat('id-ID').format(kwitansi.sub_total);
                            const formattedFeeMaintenance = new Intl.NumberFormat('id-ID').format(kwitansi
                                .fee_maintenance);
                            const formattedTotal = new Intl.NumberFormat('id-ID').format(kwitansi.total);

                            // Truncate description if too long
                            let deskripsiDisplay = kwitansi.deskripsi || '';
                            if (deskripsiDisplay.length > 50) {
                                deskripsiDisplay = deskripsiDisplay.substring(0, 50) + '...';
                            }

                            // Status badge
                            let statusBadge = '';
                            if (kwitansi.status === 'Lunas') {
                                statusBadge = '<span class="status-badge status-paid">Lunas</span>';
                            } else if (kwitansi.status === 'Pembayaran Awal') {
                                statusBadge =
                                    '<span class="status-badge status-pending">Pembayaran Awal</span>';
                            } else {
                                statusBadge = '<span class="status-badge status-unpaid">Unpaid</span>';
                            }

                            row.innerHTML = `
                            <td style="min-width: 60px;">${index + 1}.</td>
                            <td style="min-width: 120px;">${formattedDate}</td>
                            <td style="min-width: 180px;">${kwitansi.nama_perusahaan}</td>
                            <td style="min-width: 120px;">${kwitansi.kontak || '-'}</td>
                            <td style="min-width: 150px;">${kwitansi.invoice_no || '-'}</td>
                            <td style="min-width: 150px;">${kwitansi.nama_klien}</td>
                            <td style="min-width: 150px;">${kwitansi.nama_layanan || '-'}</td>
                            <td style="min-width: 200px;" title="${kwitansi.deskripsi || ''}">${deskripsiDisplay}</td>
                            <td style="min-width: 120px;">Rp ${formattedHarga}</td>
                            <td style="min-width: 120px;">Rp ${new Intl.NumberFormat('id-ID').format(kwitansi.tax || 0)}</td>
                            <td style="min-width: 150px;">Rp ${formattedFeeMaintenance}</td>
                            <td style="min-width: 120px;">Rp ${formattedTotal}</td>
                            <td style="min-width: 120px;">${kwitansi.payment_method || '-'}</td>
                            <td style="min-width: 120px;">${kwitansi.jenis_bank || '-'}</td>
                            <td style="min-width: 140px;">${kwitansi.kategori_pemasukan || '-'}</td>
                            <td style="min-width: 100px;">${statusBadge}</td>
                            <td style="min-width: 150px; text-align: center;">
                                <div class="flex justify-center gap-2">
                                    <button class="edit-kwitansi-btn p-1 rounded-full hover:bg-primary/20 text-gray-700" 
                                        data-id="${kwitansi.id}"
                                        data-tanggal="${kwitansi.tanggal}"
                                        data-nama-perusahaan="${kwitansi.nama_perusahaan}"
                                        data-nama-klien="${kwitansi.nama_klien}"
                                        data-kontak="${kwitansi.kontak || ''}"
                                        data-company-address="${kwitansi.company_address || ''}"
                                        data-nama-layanan="${kwitansi.nama_layanan || ''}"
                                        data-deskripsi="${kwitansi.deskripsi || ''}"
                                        data-invoice-no="${kwitansi.invoice_no || ''}"
                                        data-harga="${kwitansi.harga}"
                                        data-tax="${kwitansi.tax || 0}"
                                        data-sub-total="${kwitansi.sub_total}"
                                        data-fee-maintenance="${kwitansi.fee_maintenance}"
                                        data-total="${kwitansi.total}"
                                        data-status="${kwitansi.status}"
                                        data-payment-method="${kwitansi.payment_method || ''}"
                                        data-jenis-bank="${kwitansi.jenis_bank || ''}"
                                        data-kategori-pemasukan="${kwitansi.kategori_pemasukan || ''}"
                                        data-bank="${kwitansi.bank || ''}"
                                        data-no-rekening="${kwitansi.no_rekening || ''}"
                                        data-keterangan-tambahan="${kwitansi.keterangan_tambahan || ''}">
                                        <span class="material-icons-outlined">edit</span>
                                    </button>
                                    <button class="cetak-kwitansi-btn p-1 rounded-full hover:bg-primary/20 text-gray-700" 
                                        data-id="${kwitansi.id}">
                                        <span class="material-icons-outlined">print</span>
                                    </button>
                                    <button class="delete-kwitansi-btn p-1 rounded-full hover:bg-red-500/20 text-red-600" 
                                        data-id="${kwitansi.id}"
                                        data-nama-perusahaan="${kwitansi.nama_perusahaan}"
                                        aria-label="Hapus Kwitansi">
                                        <span class="material-icons-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        `;

                            tableBody.appendChild(row);

                            // Create mobile card
                            const card = document.createElement('div');
                            card.className =
                                'kwitansi-card bg-white rounded-lg border border-border-light p-4 shadow-sm';
                            card.setAttribute('data-id', kwitansi.id);
                            card.setAttribute('data-tanggal', kwitansi.tanggal);
                            card.setAttribute('data-nama-perusahaan', kwitansi.nama_perusahaan);
                            card.setAttribute('data-nama-klien', kwitansi.nama_klien);
                            card.setAttribute('data-deskripsi', kwitansi.deskripsi || '');
                            card.setAttribute('data-status', kwitansi.status);

                            // Status badge for mobile
                            let mobileStatusBadge = '';
                            if (kwitansi.status === 'Lunas') {
                                mobileStatusBadge =
                                    '<span class="status-badge status-paid text-xs">Lunas</span>';
                            } else if (kwitansi.status === 'Pembayaran Awal') {
                                mobileStatusBadge =
                                    '<span class="status-badge status-pending text-xs">Pembayaran Awal</span>';
                            } else {
                                mobileStatusBadge =
                                    '<span class="status-badge status-unpaid text-xs">Unpaid</span>';
                            }

                            card.innerHTML = `
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-base">${kwitansi.nama_perusahaan}</h4>
                                    <div class="mt-1">${mobileStatusBadge}</div>
                                </div>
                                <div class="flex gap-2">
                                    <button class="edit-kwitansi-btn p-1 rounded-full hover:bg-primary/20 text-gray-700" 
                                        data-id="${kwitansi.id}"
                                        data-tanggal="${kwitansi.tanggal}"
                                        data-nama-perusahaan="${kwitansi.nama_perusahaan}"
                                        data-nama-klien="${kwitansi.nama_klien}"
                                        data-kontak="${kwitansi.kontak || ''}"
                                        data-company-address="${kwitansi.company_address || ''}"
                                        data-nama-layanan="${kwitansi.nama_layanan || ''}"
                                        data-deskripsi="${kwitansi.deskripsi || ''}"
                                        data-invoice-no="${kwitansi.invoice_no || ''}"
                                        data-harga="${kwitansi.harga}"
                                        data-tax="${kwitansi.tax || 0}"
                                        data-sub-total="${kwitansi.sub_total}"
                                        data-fee-maintenance="${kwitansi.fee_maintenance}"
                                        data-total="${kwitansi.total}"
                                        data-status="${kwitansi.status}"
                                        data-payment-method="${kwitansi.payment_method || ''}"
                                        data-jenis-bank="${kwitansi.jenis_bank || ''}"
                                        data-kategori-pemasukan="${kwitansi.kategori_pemasukan || ''}"
                                        data-bank="${kwitansi.bank || ''}"
                                        data-no-rekening="${kwitansi.no_rekening || ''}"
                                        data-keterangan-tambahan="${kwitansi.keterangan_tambahan || ''}">
                                        <span class="material-icons-outlined">edit</span>
                                    </button>
                                    <button class="cetak-kwitansi-btn p-1 rounded-full hover:bg-primary/20 text-gray-700" 
                                        data-id="${kwitansi.id}">
                                        <span class="material-icons-outlined">print</span>
                                    </button>
                                    <button class="delete-kwitansi-btn p-1 rounded-full hover:bg-red-500/20 text-red-600" 
                                        data-id="${kwitansi.id}"
                                        data-nama-perusahaan="${kwitansi.nama_perusahaan}"
                                        aria-label="Hapus Kwitansi">
                                        <span class="material-icons-outlined">delete</span>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <p class="text-text-muted-light">No</p>
                                    <p class="font-medium">${index + 1}</p>
                                </div>
                                <div>
                                    <p class="text-text-muted-light">Tanggal</p>
                                    <p class="font-medium">${formattedDate}</p>
                                </div>
                                <div>
                                    <p class="text-text-muted-light">Nama Klien</p>
                                    <p class="font-medium">${kwitansi.nama_klien}</p>
                                </div>
                                <div>
                                    <p class="text-text-muted-light">Total</p>
                                    <p class="font-medium">Rp ${formattedTotal}</p>
                                </div>
                            </div>
                        `;

                            mobileCards.appendChild(card);
                        });

                        // Add event listeners to edit buttons
                        document.querySelectorAll('.edit-kwitansi-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                const tanggal = this.getAttribute('data-tanggal');
                                const namaPerusahaan = this.getAttribute('data-nama-perusahaan');
                                const namaKlien = this.getAttribute('data-nama-klien');
                                const total = this.getAttribute('data-total');
                                const status = this.getAttribute('data-status');

                                document.getElementById('editKwitansiId').value = id;

                                // Format date for input field (YYYY-MM-DD)
                                let tanggalValue = tanggal;
                                if (tanggalValue) {
                                    const date = new Date(tanggalValue);
                                    tanggalValue = date.toISOString().split('T')[0];
                                }
                                document.getElementById('editTanggal').value = tanggalValue;
                                document.getElementById('editStatus').value = status;

                                // Display read-only info
                                document.getElementById('displayNamaPerusahaan').textContent = namaPerusahaan;
                                document.getElementById('displayNamaKlien').textContent = namaKlien;
                                document.getElementById('displayTotal').textContent = 'Rp ' + parseFloat(total).toLocaleString('id-ID');

                                document.getElementById('editKwitansiModal').classList.remove('hidden');
                                document.body.style.overflow = 'hidden';
                            });
                        });

                        // Add event listeners to cetak buttons
                        document.querySelectorAll('.cetak-kwitansi-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                openPrintModal(id); // Langsung buka modal cetak
                            });
                        });

                        // Add event listeners to delete buttons
                        document.querySelectorAll('.delete-kwitansi-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                const namaPerusahaan = this.getAttribute('data-nama-perusahaan');

                                document.getElementById('deleteKwitansiId').value = id;
                                document.getElementById('deleteKwitansiNama').textContent =
                                    namaPerusahaan;

                                document.getElementById('deleteKwitansiModal').classList.remove(
                                    'hidden');
                                document.body.style.overflow = 'hidden';
                            });
                        });

                        // Apply filters and initialize pagination
                        applyFilters();

                        // Update total count
                        document.getElementById('totalCount').textContent = data.data.length;
                    } else {
                        // Show no data message
                        noDataRow.classList.remove('hidden');

                        // Update total count
                        document.getElementById('totalCount').textContent = '0';
                    }
                })
                .catch(error => {
                    loadingRow.classList.add('hidden');
                    noDataRow.classList.remove('hidden');
                    console.error('Error loading kwitansi data:', error);
                    showMinimalPopup('Error', 'Gagal memuat data kwitansi: ' + error.message, 'error');
                });
        }

        // Function to print kwitansi
        function cetakKwitansi(id) {
            openPrintModal(id); // Langsung buka modal
        }


        // Function to create a new kwitansi
        function submitBuatKwitansi() {
            const form = document.getElementById('buatKwitansiForm');

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Menyimpan...';
            submitBtn.disabled = true;

            // Get form data
            const formData = new FormData(form);

            // Send data to API - PERBAIKAN: ganti endpoint
            fetch('/finance/kwitansi', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            if (data.errors) {
                                const firstErrorKey = Object.keys(data.errors)[0];
                                throw new Error(data.errors[firstErrorKey][0]);
                            }
                            throw new Error(data.message || 'Unknown error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMinimalPopup('Berhasil', 'Kwitansi berhasil dibuat!', 'success');
                        closeBuatKwitansiModal();
                        loadKwitansiData();
                        loadInvoiceOptions();
                    } else {
                        showMinimalPopup('Error', 'Gagal membuat kwitansi: ' + (data.message || 'Unknown error'),
                            'error');
                    }
                })
                .catch(error => {
                    console.error('Error creating kwitansi:', error);
                    showMinimalPopup('Error', 'Gagal membuat kwitansi: ' + error.message, 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Handle edit form submission
        document.getElementById('editKwitansiForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Menyimpan...';
            submitBtn.disabled = true;

            const id = document.getElementById('editKwitansiId').value;
            const tanggal = document.getElementById('editTanggal').value;
            const status = document.getElementById('editStatus').value;

            // Create FormData with only tanggal and status
            const formData = new FormData();
            formData.append('tanggal', tanggal);
            formData.append('status', status);

            // Send data to API - use /api/kwitansi/{id} endpoint
            fetch(`/api/kwitansi/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            if (data.errors) {
                                const firstErrorKey = Object.keys(data.errors)[0];
                                throw new Error(data.errors[firstErrorKey][0]);
                            }
                            throw new Error(data.message || 'Unknown error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMinimalPopup('Berhasil', 'Kwitansi berhasil diperbarui!', 'success');
                        closeEditKwitansiModal();
                        loadKwitansiData();
                    } else {
                        showMinimalPopup('Error', 'Gagal memperbarui kwitansi: ' + (data.message || 'Unknown error'),
                            'error');
                    }
                })
                .catch(error => {
                    console.error('Error updating kwitansi:', error);
                    showMinimalPopup('Error', 'Gagal memperbarui kwitansi: ' + error.message, 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        });

        // Function to delete a kwitansi
        function deleteKwitansi(id) {
            // Show loading state
            const submitBtn = document.querySelector('#deleteKwitansiForm button[type="submit"]');
            let originalText = null;
            if (submitBtn) {
                originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menghapus...';
                submitBtn.disabled = true;
            }

            // Send delete request to API using correct endpoint
            fetch(`/api/kwitansi/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Unknown error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMinimalPopup('Berhasil', 'Kwitansi berhasil dihapus!', 'success');
                        closeDeleteKwitansiModal();
                        loadKwitansiData();
                        loadInvoiceOptions();
                    } else {
                        showMinimalPopup('Error', 'Gagal menghapus kwitansi: ' + (data.message || 'Unknown error'),
                            'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting kwitansi:', error);
                    showMinimalPopup('Error', 'Gagal menghapus kwitansi: ' + error.message, 'error');
                })
                .finally(() => {
                    // Reset button state
                    if (submitBtn) {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    }
                });
        }

        // Modal functions for Buat Kwitansi
        function closeBuatKwitansiModal() {
            document.getElementById('buatKwitansiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('buatKwitansiForm').reset();

            // Reset all display fields
            document.getElementById('total').value = '';
            document.getElementById('namaPerusahaan').value = '';
            document.getElementById('kontakPerusahaan').value = '';
            document.getElementById('hargaLayanan').value = '';
            document.getElementById('namaKlien').value = '';
            document.getElementById('alamatPerusahaan').value = '';
            document.getElementById('namaLayanan').value = '';
            document.getElementById('deskripsi').value = '';
            document.getElementById('invoiceNo').value = '';
            // clear moved payment method field
            document.getElementById('metodePembayaran').value = '';
            document.getElementById('statusPembayaran').value = '';
            document.getElementById('statusHidden').value = 'Pembayaran Awal';
            document.getElementById('harga').value = '';
            document.getElementById('pajak').value = '';
            document.getElementById('feeMaintenance').value = '';
            document.getElementById('jenisBank').value = '';
            document.getElementById('kategoriPemasukan').value = '';
            document.getElementById('keteranganTambahan').value = '';

            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            const tanggalInput = document.getElementById('tanggal');
            if (tanggalInput) {
                tanggalInput.value = today;
            }
        }

        // Modal functions for Edit Kwitansi
        function closeEditKwitansiModal() {
            document.getElementById('editKwitansiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Modal functions for Delete Kwitansi
        function closeDeleteKwitansiModal() {
            document.getElementById('deleteKwitansiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Function to calculate total in create form
        function calculateTotal() {
            const subTotal = parseFloat(document.getElementById('subTotal').value) || 0;
            const feeMaintenance = parseFloat(document.getElementById('feeMaintenance').value) || 0;
            const total = subTotal + feeMaintenance;

            // Set both the display and the actual value
            document.getElementById('total').value = formatToRupiah(total);
            document.getElementById('totalValue').value = total;
        }

        // Function to calculate total in edit form
        function calculateEditTotal() {
            const subTotal = parseFloat(document.getElementById('editSubTotal').value) || 0;
            const feeMaintenance = parseFloat(document.getElementById('editFeeMaintenance').value) || 0;
            const total = subTotal + feeMaintenance;

            // Set both the display and the actual value
            document.getElementById('editTotal').value = formatToRupiah(total);
            document.getElementById('editTotalValue').value = total;
        }

        // Minimalist Popup function
        function showMinimalPopup(title, message, type = 'success') {
            const popup = document.getElementById('minimalPopup');
            const popupTitle = popup.querySelector('.minimal-popup-title');
            const popupMessage = popup.querySelector('.minimal-popup-message');
            const popupIcon = popup.querySelector('.minimal-popup-icon span');

            // Set content
            popupTitle.textContent = title;
            popupMessage.textContent = message;

            // Set type
            popup.className = 'minimal-popup show ' + type;

            // Set icon
            if (type === 'success') {
                popupIcon.textContent = 'check';
            } else if (type === 'error') {
                popupIcon.textContent = 'error';
            } else if (type === 'warning') {
                popupIcon.textContent = 'warning';
            }

            // Auto hide after 3 seconds
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        // Initialize filter
        function initializeFilter() {
            const filterAll = document.getElementById('filterAll');
            const applyFilterBtn = document.getElementById('applyFilter');
            const resetFilterBtn = document.getElementById('resetFilter');

            // Handle "All" checkbox
            filterAll.addEventListener('change', function() {
                if (this.checked) {
                    // Uncheck all other checkboxes
                    document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(
                        cb => {
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

            // Apply filter
            applyFilterBtn.addEventListener('click', function() {
                const filterAll = document.getElementById('filterAll');
                const filterPaid = document.getElementById('filterPaid');
                const filterUnpaid = document.getElementById('filterUnpaid');
                const filterPending = document.getElementById('filterPending');

                activeFilters = [];
                if (filterAll.checked) {
                    activeFilters.push('all');
                } else {
                    if (filterPaid.checked) activeFilters.push('paid');
                    if (filterUnpaid.checked) activeFilters.push('unpaid');
                    if (filterPending.checked) activeFilters.push('pending');
                }

                currentPage = 1; // Reset to first page when filter is applied
                applyFilters();
                document.getElementById('filterDropdown').classList.remove('show');
                const visibleCount = getFilteredRows().length;
                showMinimalPopup('Filter Diterapkan', `Menampilkan ${visibleCount} kwitansi`, 'success');
            });

            // Reset filter
            resetFilterBtn.addEventListener('click', function() {
                document.getElementById('filterAll').checked = true;
                document.getElementById('filterPaid').checked = false;
                document.getElementById('filterUnpaid').checked = false;
                document.getElementById('filterPending').checked = false;
                activeFilters = ['all'];
                currentPage = 1; // Reset to first page when filter is reset
                applyFilters();
                document.getElementById('filterDropdown').classList.remove('show');
                const visibleCount = getFilteredRows().length;
                showMinimalPopup('Filter Direset', 'Menampilkan semua kwitansi', 'success');
            });
        }

        // Initialize pagination
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
            return Array.from(document.querySelectorAll('.kwitansi-row')).filter(row => !row.classList.contains(
                'hidden-by-filter'));
        }

        function getFilteredCards() {
            return Array.from(document.querySelectorAll('.kwitansi-card')).filter(card => !card.classList.contains(
                'hidden-by-filter'));
        }

        function updateVisibleItems() {
            const visibleRows = getFilteredRows();
            const visibleCards = getFilteredCards();

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            // Hide all rows and cards first
            document.querySelectorAll('.kwitansi-row').forEach(row => row.style.display = 'none');
            document.querySelectorAll('.kwitansi-card').forEach(card => card.style.display = 'none');

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
        }

        function applyFilters() {
            // Apply filters to rows
            document.querySelectorAll('.kwitansi-row').forEach(row => {
                const namaPerusahaan = row.getAttribute('data-nama-perusahaan').toLowerCase();
                const namaKlien = row.getAttribute('data-nama-klien').toLowerCase();
                const deskripsi = row.getAttribute('data-deskripsi').toLowerCase();
                const status = row.getAttribute('data-status').toLowerCase();

                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = namaPerusahaan.includes(searchLower) ||
                        namaKlien.includes(searchLower) ||
                        deskripsi.includes(searchLower);
                }

                // Check if status filter matches
                let statusMatches = true;
                if (activeFilters.length > 0 && !activeFilters.includes('all')) {
                    statusMatches = false;
                    if (activeFilters.includes('paid') && status === 'lunas') {
                        statusMatches = true;
                    } else if (activeFilters.includes('pending') && status.includes('pembayaran')) {
                        statusMatches = true;
                    } else if (activeFilters.includes('unpaid') && (status === 'unpaid' || status === '')) {
                        statusMatches = true;
                    }
                }

                if (searchMatches && statusMatches) {
                    row.classList.remove('hidden-by-filter');
                } else {
                    row.classList.add('hidden-by-filter');
                }
            });

            // Apply same filters to cards
            document.querySelectorAll('.kwitansi-card').forEach(card => {
                const namaPerusahaan = card.getAttribute('data-nama-perusahaan').toLowerCase();
                const namaKlien = card.getAttribute('data-nama-klien').toLowerCase();
                const deskripsi = card.getAttribute('data-deskripsi').toLowerCase();
                const status = card.getAttribute('data-status').toLowerCase();

                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = namaPerusahaan.includes(searchLower) ||
                        namaKlien.includes(searchLower) ||
                        deskripsi.includes(searchLower);
                }

                // Check if status filter matches
                let statusMatches = true;
                if (activeFilters.length > 0 && !activeFilters.includes('all')) {
                    statusMatches = false;
                    if (activeFilters.includes('paid') && status === 'lunas') {
                        statusMatches = true;
                    } else if (activeFilters.includes('pending') && status.includes('pembayaran')) {
                        statusMatches = true;
                    } else if (activeFilters.includes('unpaid') && (status === 'unpaid' || status === '')) {
                        statusMatches = true;
                    }
                }

                if (searchMatches && statusMatches) {
                    card.classList.remove('hidden-by-filter');
                } else {
                    card.classList.add('hidden-by-filter');
                }
            });

            // Update pagination and visible items
            renderPagination();
            updateVisibleItems();

            // Update total count
            document.getElementById('totalCount').textContent = getFilteredRows().length;
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
    </script>
</body>

</html>

