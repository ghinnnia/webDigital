<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Invoice - Finance</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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

        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
            }
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

        .data-table {
            width: 100%;
            min-width: 1400px;
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

        @media print {
            body * {
                visibility: hidden;
            }

            .print-container,
            .print-container * {
                visibility: visible;
            }

            .print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        .error-input {
            border-color: #ef4444 !important;
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        .session-expired-modal {
            z-index: 9999 !important;
            background-color: rgba(0, 0, 0, 0.7) !important;
        }

        /* CSS untuk modal detail */
        .detail-status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .detail-status-lunas {
            background-color: #d1fae5;
            color: #065f46;
        }

        .detail-status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .detail-status-pembayaran-awal {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .detail-amount {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .invoice-detail-item {
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .invoice-detail-item:last-child {
            border-bottom: none;
        }

        .invoice-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .action-buttons button {
                width: 100%;
            }
        }

        /* Select2 custom styling */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            height: 42px !important;
            padding: 0.5rem !important;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }

        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* CSS untuk Select2 Layanan */
        .select2-layanan + .select2-container .select2-selection--single {
            height: 42px !important;
            padding: 8px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
        }

        .select2-layanan + .select2-container .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 0 !important;
        }

        .select2-layanan + .select2-container .select2-selection__arrow {
            height: 40px !important;
        }

        .select2-layanan + .select2-container .select2-results__option {
            padding: 8px 12px !important;
        }

        .select2-layanan + .select2-container .select2-results__option--highlighted {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('finance/templet/sider')

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Kelola Invoice</h2>

                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span
                            class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                            placeholder="Cari nama perusahaan, nomor invoice, klien, atau layanan..." type="text" />
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative">
                            <button id="filterBtn"
                                class="px-4 py-2 bg-white border border-border-light text-text-muted-light rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">filter_list</span>
                                Filter
                            </button>
                            <div id="filterDropdown" class="filter-dropdown">
                                <div class="mb-3">
                                    <h4 class="font-semibold text-sm mb-2">Status Pembayaran</h4>
                                    <div class="filter-option flex items-center gap-2 mb-1">
                                        <input type="checkbox" id="filterAllStatus" value="all" checked>
                                        <label for="filterAllStatus" class="text-sm">Semua Status</label>
                                    </div>
                                    <div class="filter-option flex items-center gap-2 mb-1">
                                        <input type="checkbox" id="filterPembayaranAwal" value="down payment">
                                        <label for="filterPembayaranAwal" class="text-sm">Down Payment</label>
                                    </div>
                                    <div class="filter-option flex items-center gap-2">
                                        <input type="checkbox" id="filterLunas" value="lunas">
                                        <label for="filterLunas" class="text-sm">Lunas</label>
                                    </div>
                                </div>
                                <div class="filter-actions mt-4 pt-3 border-t">
                                    <button id="applyFilter"
                                        class="filter-apply bg-primary text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Terapkan</button>
                                    <button id="resetFilter"
                                        class="filter-reset bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-300 ml-2">Reset</button>
                                </div>
                            </div>
                        </div>
                        <button id="buatInvoiceBtn"
                            class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Buat Invoice</span>
                            <span class="sm:hidden">Buat</span>
                        </button>
                    </div>
                </div>

                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">receipt</span>
                            Daftar Invoice
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount"
                                    class="font-semibold text-text-light">0</span> invoice</span>
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
                                            <th style="min-width: 120px;">Nomor Perusahaan</th>
                                            <th style="min-width: 150px;">Nomor Invoice</th>
                                            <th style="min-width: 150px;">Nama Klien</th>
                                            <th style="min-width: 150px;">Nama Layanan</th>
                                            <th style="min-width: 200px;">Alamat</th>
                                            <th style="min-width: 200px;">Deskripsi</th>
                                            <th style="min-width: 120px;">Subtotal</th>
                                            <th style="min-width: 100px;">Pajak (%)</th>
                                            <th style="min-width: 120px;">Total</th>
                                            <th style="min-width: 150px;">Metode Pembayaran</th>
                                            <th style="min-width: 120px;">Jenis Bank</th>
                                            <th style="min-width: 140px;">Kategori Pemasukan</th>
                                            <th style="min-width: 130px;">Fee Maintenance</th>
                                            <th style="min-width: 150px;">Status Pembayaran</th>
                                            <th style="min-width: 200px;">Keterangan Tambahan</th>
                                            <th style="min-width: 150px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        <tr id="loadingRow">
                                            <td colspan="19" class="px-6 py-4 text-center">
                                                <div class="flex justify-center items-center">
                                                    <div class="spinner"></div>
                                                    <span class="ml-2">Memuat data...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="noDataRow" class="hidden">
                                            <td colspan="19" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Tidak ada data invoice
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
                            <div id="pageNumbers" class="flex gap-1"></div>
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

    <!-- Modal Buat Invoice -->
    <div id="buatInvoiceModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Buat Invoice Baru</h3>
                    <button id="closeModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
<form id="buatInvoiceForm" class="space-y-4">
    @csrf
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <!-- TAMBAHKAN INI -->
    <input type="hidden" name="kontak" id="kontakHidden">

                    <!-- Informasi Perusahaan -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-blue-500">business</span>
                            Informasi Perusahaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Perusahaan *</label>
                                <select id="selectPerusahaan" name="company_name"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                    <option value="">Pilih Perusahaan</option>
                                </select>
                                <span class="error-message" id="company_name_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Perusahaan</label>
                                <input type="text" id="kontakPerusahaan"
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700"
                                    readonly>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Klien *</label>
                                <input type="text" id="client_name" name="client_name"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="client_name_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Perusahaan *</label>
                                <input type="text" id="company_address" name="company_address"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="company_address_error"></span>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Layanan *</label>
                                <select id="selectLayanan" name="nama_layanan"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input select2-layanan"
                                    required>
                                    <option value="">Pilih Layanan</option>
                                    <!-- Options akan diisi oleh JavaScript -->
                                </select>
                                <span class="error-message" id="nama_layanan_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Layanan (Rp)</label>
                                <input type="text" id="hargaLayanan" name="hargaLayananDisplay"
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700"
                                    readonly>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Layanan</label>
                            <textarea id="deskripsiLayanan" name="deskripsiLayanan" rows="2"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700" readonly></textarea>
                        </div>
                        <!-- Tambahkan input hidden untuk menyimpan data asli -->
                        <input type="hidden" id="hargaHidden" name="harga">
                        <input type="hidden" id="hppHidden" name="hpp">
                    </div>

                    <!-- Informasi Invoice -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-purple-500">receipt</span>
                            Informasi Invoice
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice *</label>
                                <input type="text" id="invoice_no" name="invoice_no"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="invoice_no_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice *</label>
                                <input type="date" id="invoice_date" name="invoice_date"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="invoice_date_error"></span>
                            </div>

                        </div>
                    </div>

                    <!-- Informasi Pembayaran -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-yellow-500">payments</span>
                            Informasi Pembayaran
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                                <select id="payment_method" name="payment_method"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                    <option value="">Pilih Metode</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Cash">Cash</option>
                                </select>
                                <span class="error-message" id="payment_method_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran *</label>
                                <select id="status_pembayaran" name="status_pembayaran"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                    <option value="down payment">Down Payment</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                                <span class="error-message" id="status_pembayaran_error"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bank</label>
                                <select id="jenis_bank" name="jenis_bank"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input">
                                    <option value="">Pilih Bank</option>
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                    <option value="CIMB Niaga">CIMB Niaga</option>
                                    <option value="Permata">Permata</option>
                                    <option value="Danamon">Danamon</option>
                                    <option value="OVO">OVO</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <span class="error-message" id="jenis_bank_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Pemasukan</label>
                                <select id="kategori_pemasukan" name="kategori_pemasukan"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input">
                                    <option value="">Pilih Kategori</option>
                                    <option value="layanan">Layanan</option>
                                    <option value="produk">Produk</option>
                                    <option value="fee/komisi">Fee/Komisi</option>
                                </select>
                                <span class="error-message" id="kategori_pemasukan_error"></span>
                            </div>
                        </div>

                    </div>

                    <!-- Perhitungan Harga -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-red-500">calculate</span>
                            Perhitungan Harga
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (Rp) *</label>
                                <input type="text" id="subtotalDisplay" inputmode="numeric"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required placeholder="Rp 0">
                                <input type="hidden" id="subtotal" name="subtotal" value="0">
                                <span class="error-message" id="subtotal_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pajak (%) *</label>
                                <input type="number" id="tax_percentage" name="tax_percentage" min="0"
                                    max="100" step="0.01"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required value="11">
                                <span class="error-message" id="tax_percentage_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Maintenance (Rp)</label>
                                <input type="text" id="feeMaintenanceDisplay" inputmode="numeric"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    value="Rp 0">
                                <input type="hidden" id="fee_maintenance" name="fee_maintenance" value="0">
                                <span class="error-message" id="fee_maintenance_error"></span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total (Rp) *</label>
                            <input type="text" id="totalDisplay"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700"
                                readonly required value="Rp 0">
                            <input type="hidden" id="total" name="total" value="0">
                            <span class="error-message" id="total_error"></span>
                        </div>
                    </div>
                    
                    <!-- Keterangan Tambahan -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-teal-500">notes</span>
                            Keterangan Tambahan
                        </h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                            <textarea id="keterangan_tambahan" name="keterangan_tambahan" rows="3"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                placeholder="Tambahkan informasi atau catatan tambahan di sini..."></textarea>
                            <span class="error-message" id="keterangan_tambahan_error"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Buat Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Invoice -->
    <div id="editInvoiceModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Invoice</h3>
                    <button id="closeEditModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editInvoiceForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="editInvoiceId" name="id">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="kontak" id="editKontakHidden">

                    <!-- Informasi Perusahaan -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-blue-500">business</span>
                            Informasi Perusahaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Perusahaan *</label>
                                <select id="editSelectPerusahaan" name="company_name"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                    <option value="">Pilih Perusahaan</option>
                                </select>
                                <span class="error-message" id="edit_company_name_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Perusahaan</label>
                                <input type="text" id="editKontakPerusahaan"
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700"
                                    readonly>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Klien *</label>
                                <input type="text" id="editClientName" name="client_name"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="edit_client_name_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Perusahaan *</label>
                                <input type="text" id="editCompanyAddress" name="company_address"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="edit_company_address_error"></span>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Layanan *</label>
                                <select id="editSelectLayanan" name="nama_layanan"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input select2-layanan"
                                    required>
                                    <option value="">Pilih Layanan</option>
                                </select>
                                <span class="error-message" id="edit_nama_layanan_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Layanan (Rp)</label>
                                <input type="text" id="editHargaLayanan" name="editHargaLayananDisplay"
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700"
                                    readonly>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Layanan</label>
                            <textarea id="editDeskripsiLayanan" name="editDeskripsiLayanan" rows="2"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700" readonly></textarea>
                        </div>
                        <!-- Hidden fields untuk edit -->
                        <input type="hidden" id="editHargaHidden" name="harga">
                        <input type="hidden" id="editHppHidden" name="hpp">
                    </div>

                    <!-- Informasi Invoice -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-purple-500">receipt</span>
                            Informasi Invoice
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice *</label>
                                <input type="text" id="editInvoiceNo" name="invoice_no"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="edit_invoice_no_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice *</label>
                                <input type="date" id="editInvoiceDate" name="invoice_date"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                <span class="error-message" id="edit_invoice_date_error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pembayaran -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-yellow-500">payments</span>
                            Informasi Pembayaran
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                                <select id="editPaymentMethod" name="payment_method"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                    <option value="">Pilih Metode</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Cash">Cash</option>
                                </select>
                                <span class="error-message" id="edit_payment_method_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran *</label>
                                <select id="editStatusPembayaran" name="status_pembayaran"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required>
                                    <option value="down payment">Down Payment</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                                <span class="error-message" id="edit_status_pembayaran_error"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bank</label>
                                <select id="editJenisBank" name="jenis_bank"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input">
                                    <option value="">Pilih Bank</option>
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                    <option value="CIMB Niaga">CIMB Niaga</option>
                                    <option value="Permata">Permata</option>
                                    <option value="Danamon">Danamon</option>
                                    <option value="OVO">OVO</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <span class="error-message" id="edit_jenis_bank_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Pemasukan</label>
                                <select id="editKategoriPemasukan" name="kategori_pemasukan"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input">
                                    <option value="">Pilih Kategori</option>
                                    <option value="layanan">Layanan</option>
                                    <option value="produk">Produk</option>
                                    <option value="fee/komisi">Fee/Komisi</option>
                                </select>
                                <span class="error-message" id="edit_kategori_pemasukan_error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Perhitungan Harga -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-red-500">calculate</span>
                            Perhitungan Harga
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (Rp) *</label>
                                <input type="text" id="editSubtotalDisplay" inputmode="numeric"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required placeholder="Rp 0">
                                <input type="hidden" id="editSubtotal" name="subtotal" value="0">
                                <span class="error-message" id="edit_subtotal_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pajak (%) *</label>
                                <input type="number" id="editTaxPercentage" name="tax_percentage" min="0"
                                    max="100" step="0.01"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    required value="11">
                                <span class="error-message" id="edit_tax_percentage_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Maintenance (Rp)</label>
                                <input type="text" id="editFeeMaintenanceDisplay" inputmode="numeric"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                    value="Rp 0">
                                <input type="hidden" id="editFeeMaintenance" name="fee_maintenance" value="0">
                                <span class="error-message" id="edit_fee_maintenance_error"></span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total (Rp) *</label>
                            <input type="text" id="editTotalDisplay"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700"
                                readonly required value="Rp 0">
                            <input type="hidden" id="editTotal" name="total" value="0">
                            <span class="error-message" id="edit_total_error"></span>
                        </div>
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-teal-500">notes</span>
                            Keterangan Tambahan
                        </h4>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                            <textarea id="editKeteranganTambahan" name="keterangan_tambahan" rows="3"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                placeholder="Tambahkan informasi atau catatan tambahan di sini..."></textarea>
                            <span class="error-message" id="edit_keterangan_tambahan_error"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelEditBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Invoice -->
    <div id="deleteInvoiceModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button id="closeDeleteModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus invoice untuk <span
                                id="deleteInvoiceNama" class="font-semibold"></span> dengan nomor invoice <span
                                id="deleteInvoiceNomor" class="font-semibold"></span>?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteInvoiceId">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelDeleteBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="button" id="confirmDeleteBtn"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Invoice -->
    <div id="detailInvoiceModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="material-icons-outlined text-blue-600 text-2xl">receipt_long</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Detail Invoice</h3>
                            <p class="text-gray-600" id="detailInvoiceSubtitle">Informasi lengkap invoice</p>
                        </div>
                    </div>
                    <button id="closeDetailModalBtn"
                        class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100">
                        <span class="material-icons-outlined text-2xl">close</span>
                    </button>
                </div>

                <!-- Invoice Header -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-outlined text-blue-600">business</span>
                                <h4 class="font-semibold text-blue-700">Perusahaan</h4>
                            </div>
                            <p id="detailCompanyName" class="text-xl font-bold text-gray-800 truncate"></p>
                            <p id="detailCompanyAddress" class="text-gray-600 text-sm"></p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-outlined text-green-600">person</span>
                                <h4 class="font-semibold text-green-700">Klien</h4>
                            </div>
                            <p id="detailClientName" class="text-xl font-bold text-gray-800"></p>
                            <div class="flex items-center gap-2">
                                <span class="material-icons-outlined text-gray-400 text-sm">date_range</span>
                                <span id="detailInvoiceDate" class="text-gray-600 text-sm"></span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-outlined text-purple-600">tag</span>
                                <h4 class="font-semibold text-purple-700">Invoice Info</h4>
                            </div>
                            <p id="detailInvoiceNo" class="text-xl font-bold text-gray-800 font-mono"></p>
                            <div id="detailStatusBadge" class="inline-block mt-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Left Column - Service & Payment Info -->
                    <div class="space-y-6">
                        <!-- Service Information -->
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="material-icons-outlined text-blue-500">description</span>
                                Informasi Layanan
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center invoice-detail-item">
                                    <span class="text-gray-600 font-medium">Nama Layanan</span>
                                    <span id="detailNamaLayanan" class="font-semibold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between items-center invoice-detail-item">
                                    <span class="text-gray-600 font-medium">Metode Pembayaran</span>
                                    <span id="detailPaymentMethod" class="font-semibold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between items-center invoice-detail-item">
                                    <span class="text-gray-600 font-medium">Deskripsi</span>
                                    <span id="detailDescription"
                                        class="font-semibold text-gray-800 text-right max-w-xs"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="material-icons-outlined text-teal-500">info</span>
                                Informasi Tambahan
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center invoice-detail-item">
                                    <span class="text-gray-600 font-medium">Jenis Bank</span>
                                    <span id="detailJenisBank" class="font-semibold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between items-center invoice-detail-item">
                                    <span class="text-gray-600 font-medium">Kategori Pemasukan</span>
                                    <span id="detailKategoriPemasukan" class="font-semibold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between items-center invoice-detail-item">
                                    <span class="text-gray-600 font-medium">Fee Maintenance</span>
                                    <span id="detailFeeMaintenance" class="font-semibold text-gray-800"></span>
                                </div>
                                <div class="flex justify-between items-center invoice-detail-item">
                                    <span class="text-gray-600 font-medium">Keterangan Tambahan</span>
                                    <span id="detailKeteranganTambahan" class="font-semibold text-gray-800 text-right max-w-xs"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="material-icons-outlined text-green-500">history</span>
                                Riwayat Status
                            </h4>
                            <div class="relative pl-8">
                                <!-- Timeline line -->
                                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                                <!-- Created -->
                                <div class="relative mb-6">
                                    <div
                                        class="absolute left-[-20px] top-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                                        <span class="material-icons-outlined text-white text-sm">check</span>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-800">Invoice Dibuat</p>
                                        <p id="detailCreatedAt" class="text-sm text-gray-500 mt-1"></p>
                                    </div>
                                </div>

                                <!-- Status Update -->
                                <div class="relative">
                                    <div id="detailStatusIcon"
                                        class="absolute left-[-20px] top-0 w-8 h-8 rounded-full flex items-center justify-center">
                                        <!-- Icon akan diisi berdasarkan status -->
                                    </div>
                                    <div class="ml-4">
                                        <p id="detailStatusText" class="font-medium text-gray-800">Status Pembayaran
                                        </p>
                                        <p id="detailUpdatedAt" class="text-sm text-gray-500 mt-1"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Financial Summary -->
                    <div
                        class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-5 shadow-sm">
                        <h4 class="font-semibold text-gray-800 mb-6 flex items-center gap-2">
                            <span class="material-icons-outlined text-purple-500">payments</span>
                            Ringkasan Keuangan
                        </h4>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center py-3 px-4 bg-white rounded-lg border border-gray-200">
                                <div>
                                    <span class="text-gray-600">Subtotal</span>
                                    <p class="text-xs text-gray-500 mt-1">Sebelum pajak</p>
                                </div>
                                <span id="detailSubtotal"
                                    class="text-lg font-bold text-gray-800 detail-amount"></span>
                            </div>

                            <div
                                class="flex justify-between items-center py-3 px-4 bg-white rounded-lg border border-gray-200">
                                <div>
                                    <span class="text-gray-600">Pajak</span>
                                    <p id="detailTaxPercentageText" class="text-xs text-gray-500 mt-1"></p>
                                </div>
                                <span id="detailTax" class="text-lg font-bold text-gray-800 detail-amount"></span>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-300">
                                <div
                                    class="flex justify-between items-center py-4 px-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                                    <div>
                                        <span class="text-lg font-bold text-gray-800">Total</span>
                                        <p class="text-sm text-gray-600 mt-1">Jumlah yang harus dibayar</p>
                                    </div>
                                    <span id="detailTotal"
                                        class="text-2xl font-bold text-blue-600 detail-amount"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <p>Invoice ID: <span id="detailInvoiceId" class="font-mono font-medium"></span></p>
                        <p class="mt-1">Terakhir diupdate: <span id="detailLastUpdated" class="font-medium"></span>
                        </p>
                    </div>
                    <div class="action-buttons">
                        <button onclick="closeDetailModal()"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                            <span class="material-icons-outlined">close</span>
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Print Invoice -->
    <div id="printInvoiceModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Print Invoice</h3>
                    <p class="text-gray-600 dark:text-gray-400">Preview invoice sebelum mencetak</p>
                </div>
                <button onclick="closePrintInvoiceModal()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="flex-grow overflow-auto p-4">
                <div id="printInvoiceContent" class="print-container"></div>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closePrintInvoiceModal()"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Tutup</button>
                <button onclick="printInvoice()"
                    class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <span class="material-icons mr-2">print</span>Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Sesi Habis -->
    <div id="sessionExpiredModal"
        class="modal fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[9999] hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex flex-col items-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <span class="material-icons-outlined text-red-500 text-3xl">error</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 text-center mb-2">Sesi Anda Telah Habis</h3>
                    <p class="text-gray-600 text-center mb-6">Silakan login kembali untuk melanjutkan.</p>
                </div>
                <div class="flex justify-center">
                    <button id="reloadPageBtn" class="px-6 py-3 btn-primary rounded-lg font-medium">Login
                        Ulang</button>
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
        <div class="flex flex-col items-center">
            <div class="spinner"></div>
            <p class="mt-3 text-gray-600">Memuat data...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
// ==================== GLOBAL VARIABLES ====================
let allInvoices = [];
let filteredInvoices = [];
let currentPage = 1;
const perPage = 10;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
let dataLayanan = [];
let dataPerusahaan = [];
let currentDetailInvoiceId = null;

// ==================== DOM ELEMENTS ====================
const buatInvoiceBtn = document.getElementById('buatInvoiceBtn');
const buatModal = document.getElementById('buatInvoiceModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');
const buatInvoiceForm = document.getElementById('buatInvoiceForm');
const searchInput = document.getElementById('searchInput');
const totalCount = document.getElementById('totalCount');
const desktopTableBody = document.getElementById('desktopTableBody');
const mobileCards = document.getElementById('mobile-cards');
const loadingRow = document.getElementById('loadingRow');
const noDataRow = document.getElementById('noDataRow');
const prevPageBtn = document.getElementById('prevPage');
const nextPageBtn = document.getElementById('nextPage');
const pageNumbers = document.getElementById('pageNumbers');
const editInvoiceForm = document.getElementById('editInvoiceForm');
const cancelEditBtn = document.getElementById('cancelEditBtn');
const closeEditModalBtn = document.getElementById('closeEditModalBtn');
const closeDetailModalBtn = document.getElementById('closeDetailModalBtn');
const sessionExpiredModal = document.getElementById('sessionExpiredModal');
const reloadPageBtn = document.getElementById('reloadPageBtn');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
const closeDeleteModalBtn = document.getElementById('closeDeleteModalBtn');
const filterBtn = document.getElementById('filterBtn');
const filterDropdown = document.getElementById('filterDropdown');
const applyFilterBtn = document.getElementById('applyFilter');
const resetFilterBtn = document.getElementById('resetFilter');
const loadingOverlay = document.getElementById('loadingOverlay');

// ==================== FUNGSI UNTUK MENGAMBIL DATA PERUSAHAAN ====================
async function loadDataPerusahaan() {
    try {
        console.log('Memuat data perusahaan dari endpoint...');

        const response = await fetch('/finance/api/perusahaan', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });

        if (checkSessionError(response)) {
            return;
        }

        if (!response.ok) {
            console.warn('Response perusahaan tidak OK:', response.status);
            return;
        }

        const data = await response.json();
        console.log('Response data perusahaan:', data);

        if (data && Array.isArray(data)) {
            dataPerusahaan = data;
        } else if (data && data.success && Array.isArray(data.data)) {
            dataPerusahaan = data.data;
        } else if (data && Array.isArray(data.perusahaan)) {
            dataPerusahaan = data.perusahaan;
        } else {
            console.warn('Format data perusahaan tidak dikenali:', data);
            return;
        }

        console.log(`Berhasil memuat ${dataPerusahaan.length} data perusahaan`);
        populatePerusahaanOptions();
    } catch (error) {
        console.error('Error loading perusahaan data:', error);
    }
}

// Fungsi untuk mengisi dropdown perusahaan
function populatePerusahaanOptions() {
    const perusahaanSelect = document.getElementById('selectPerusahaan');
    const editPerusahaanSelect = document.getElementById('editSelectPerusahaan');

    function populateSelect(selectElement, isEditForm = false) {
        if (!selectElement) {
            console.warn('Element select perusahaan tidak ditemukan');
            return;
        }

        // Simpan value yang dipilih sebelumnya (jika ada)
        const currentValue = selectElement.value;

        // Clear existing options except first one
        selectElement.innerHTML = '<option value="">Pilih Perusahaan</option>';

        if (dataPerusahaan.length === 0) {
            console.warn('Data perusahaan kosong, menambahkan option default');
            const option = document.createElement('option');
            option.value = 'data_kosong';
            option.textContent = 'Tidak ada data perusahaan';
            option.disabled = true;
            selectElement.appendChild(option);
            return;
        }

        console.log(`Menambahkan ${dataPerusahaan.length} perusahaan ke dropdown`);

        dataPerusahaan.forEach((perusahaan) => {
            const option = document.createElement('option');

            // Ambil data dengan berbagai kemungkinan nama field
            const nama = perusahaan.nama_perusahaan || perusahaan.nama || `Perusahaan ${perusahaan.id}`;
            const klien = perusahaan.klien || '';
            const alamat = perusahaan.alamat || '';

            // Cari kontak dari berbagai kemungkinan field
            let kontak = '';
            if (perusahaan.kontak) {
                kontak = perusahaan.kontak;
            } else if (perusahaan.telepon) {
                kontak = perusahaan.telepon;
            } else if (perusahaan.phone) {
                kontak = perusahaan.phone;
            } else if (perusahaan.no_telp) {
                kontak = perusahaan.no_telp;
            } else if (perusahaan.hp) {
                kontak = perusahaan.hp;
            } else if (perusahaan.no_hp) {
                kontak = perusahaan.no_hp;
            } else if (perusahaan.telephone) {
                kontak = perusahaan.telephone;
            }

            // Cari deskripsi dari berbagai kemungkinan field
            let deskripsi = '';
            if (perusahaan.deskripsi) {
                deskripsi = perusahaan.deskripsi;
            } else if (perusahaan.description) {
                deskripsi = perusahaan.description;
            } else if (perusahaan.deskripsi_perusahaan) {
                deskripsi = perusahaan.deskripsi_perusahaan;
            } else if (perusahaan.tentang) {
                deskripsi = perusahaan.tentang;
            } else if (perusahaan.profil) {
                deskripsi = perusahaan.profil;
            }

            option.value = nama;
            option.textContent = nama;
            option.setAttribute('data-klien', klien);
            option.setAttribute('data-alamat', alamat);
            option.setAttribute('data-kontak', kontak);
            option.setAttribute('data-deskripsi', deskripsi);

            selectElement.appendChild(option);
        });

        // Restore previous value
        if (currentValue) {
            selectElement.value = currentValue;
        }

        // Re-initialize Select2 jika sudah diinisialisasi sebelumnya
        if ($(selectElement).hasClass('select2-hidden-accessible')) {
            $(selectElement).trigger('change');
        }
    }

    // Populate create form
    populateSelect(perusahaanSelect, false);
    
    // Populate edit form
    if (editPerusahaanSelect) {
        populateSelect(editPerusahaanSelect, true);
    }

    // Initialize Select2 untuk create form
    if (perusahaanSelect) {
        if ($(perusahaanSelect).data('select2')) {
            $(perusahaanSelect).select2('destroy');
        }

        // Initialize Select2 dengan proper configuration
        $(perusahaanSelect).select2({
            placeholder: 'Pilih Perusahaan',
            allowClear: true,
            width: '100%'
        });

        // Setup change event listener SETELAH Select2 diinisialisasi
        $(perusahaanSelect).off('change').on('change', function() {
            console.log('Perusahaan dropdown changed - calling handlePerusahaanChange');
            handlePerusahaanChange(this);
        });
    }

    // Initialize Select2 untuk edit form
    if (editPerusahaanSelect) {
        if ($(editPerusahaanSelect).data('select2')) {
            $(editPerusahaanSelect).select2('destroy');
        }

        $(editPerusahaanSelect).select2({
            placeholder: 'Pilih Perusahaan',
            allowClear: true,
            width: '100%'
        });

        $(editPerusahaanSelect).off('change').on('change', function() {
            console.log('Edit Perusahaan dropdown changed - calling handlePerusahaanChange');
            handlePerusahaanChange(this, true);
        });
    }
}

// ==================== FUNGSI UNTUK MENGAMBIL DATA LAYANAN ====================
async function loadDataLayanan() {
    try {
        console.log('Memuat data layanan dari endpoint...');

        const response = await fetch('{{ route("finance.api.layanan.dropdown") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });

        if (checkSessionError(response)) {
            return;
        }

        if (!response.ok) {
            console.warn('Response layanan tidak OK:', response.status);
            useDummyLayananData();
            return;
        }

        const data = await response.json();
        console.log('Response data layanan:', data);

        if (data && data.success && Array.isArray(data.data)) {
            dataLayanan = data.data;
            console.log(`Berhasil memuat ${dataLayanan.length} data layanan`);
            populateLayananOptions();
        } else {
            console.warn('Format data layanan tidak dikenali:', data);
            useDummyLayananData();
        }
    } catch (error) {
        console.error('Error loading layanan data:', error);
        useDummyLayananData();
    }
}

function useDummyLayananData() {
    dataLayanan = [
        {
            id: 1,
            nama_layanan: 'Web Development',
            deskripsi: 'Pembuatan website perusahaan',
            harga: 5000000,
            hpp: 2000000
        },
        {
            id: 2,
            nama_layanan: 'Mobile App',
            deskripsi: 'Pembuatan aplikasi mobile',
            harga: 8000000,
            hpp: 3000000
        },
        {
            id: 3,
            nama_layanan: 'Digital Marketing',
            deskripsi: 'Kampanye pemasaran digital',
            harga: 3000000,
            hpp: 1000000
        }
    ];
    console.log('Menggunakan data layanan dummy:', dataLayanan.length);
    populateLayananOptions();
}

// Fungsi untuk mengisi dropdown layanan
function populateLayananOptions() {
    console.log('Populating layanan options with', dataLayanan.length, 'items');
    
    const layananSelect = document.getElementById('selectLayanan');
    const editLayananSelect = document.getElementById('editSelectLayanan');
    
    function createOptions(selectElement, isEditForm = false) {
        if (!selectElement) {
            console.warn('Element select tidak ditemukan');
            return;
        }

        // Simpan value yang dipilih sebelumnya
        const currentValue = selectElement.value;

        // Clear existing options except first one
        selectElement.innerHTML = '<option value="">Pilih Layanan</option>';

        if (dataLayanan.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Tidak ada data layanan';
            option.disabled = true;
            selectElement.appendChild(option);
            return;
        }

        dataLayanan.forEach(layanan => {
            const option = document.createElement('option');
            option.value = layanan.nama_layanan;
            
            // Format tampilan: Nama Layanan (Rp harga)
            const hargaFormatted = formatCurrency(layanan.harga || 0);
            option.textContent = `${layanan.nama_layanan} - ${hargaFormatted}`;
            
            // Simpan data tambahan sebagai atribut
            option.setAttribute('data-id', layanan.id);
            option.setAttribute('data-harga', layanan.harga || 0);
            option.setAttribute('data-hpp', layanan.hpp || 0);
            option.setAttribute('data-deskripsi', layanan.deskripsi || '');
            
            selectElement.appendChild(option);
        });

        // Restore previous value
        if (currentValue) {
            selectElement.value = currentValue;
            // Trigger change untuk mengisi data lainnya
            setTimeout(() => {
                triggerLayananChange(selectElement, isEditForm);
            }, 100);
        }
    }

    createOptions(layananSelect, false);
    
    // Populate edit form with correct element ID
    if (editLayananSelect) {
        createOptions(editLayananSelect, true);
    }

    // Initialize Select2
    initializeLayananSelect2();
}

// Fungsi untuk trigger perubahan layanan
function triggerLayananChange(selectElement, isEditForm = false) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const harga = selectedOption.getAttribute('data-harga') || 0;
        const deskripsi = selectedOption.getAttribute('data-deskripsi') || '';
        const hpp = selectedOption.getAttribute('data-hpp') || 0;
        
        if (isEditForm) {
            // Untuk form edit
            const hargaDisplay = document.getElementById('editHargaLayanan');
            const deskripsiDisplay = document.getElementById('editDeskripsiLayanan');
            
            if (hargaDisplay) {
                hargaDisplay.value = formatCurrency(parseFloat(harga));
            }
            if (deskripsiDisplay) {
                deskripsiDisplay.value = deskripsi;
            }
            setCurrencyField('editSubtotalDisplay', 'editSubtotal', harga, true, true);
            
            // Set hidden fields untuk edit
            const hargaHidden = document.getElementById('editHargaHidden');
            const hppHidden = document.getElementById('editHppHidden');
            if (hargaHidden) hargaHidden.value = harga;
            if (hppHidden) hppHidden.value = hpp;
        } else {
            // Untuk form create
            const hargaDisplay = document.getElementById('hargaLayanan');
            const deskripsiDisplay = document.getElementById('deskripsiLayanan');
            
            if (hargaDisplay) {
                hargaDisplay.value = formatCurrency(parseFloat(harga));
            }
            if (deskripsiDisplay) {
                deskripsiDisplay.value = deskripsi;
            }
            setCurrencyField('subtotalDisplay', 'subtotal', harga, true, false);
            
            // Set hidden fields untuk create
            const hargaHidden = document.getElementById('hargaHidden');
            const hppHidden = document.getElementById('hppHidden');
            if (hargaHidden) hargaHidden.value = harga;
            if (hppHidden) hppHidden.value = hpp;
        }
    }
}

// Initialize Select2 untuk layanan
function initializeLayananSelect2() {
    // Untuk form create
    const layananSelect = $('#selectLayanan');
    if (layananSelect.length) {
        layananSelect.select2({
            placeholder: 'Pilih Layanan',
            allowClear: true,
            width: '100%',
            templateResult: formatLayananOption,
            templateSelection: formatLayananSelection
        }).on('change', function() {
            triggerLayananChange(this, false);
        });
    }
    
    // Untuk form edit
    const editLayananSelect = $('#editSelectLayanan');
    if (editLayananSelect.length) {
        editLayananSelect.select2({
            placeholder: 'Pilih Layanan',
            allowClear: true,
            width: '100%',
            templateResult: formatLayananOption,
            templateSelection: formatLayananSelection
        }).on('change', function() {
            triggerLayananChange(this, true);
        });
    }
}

// Format tampilan option di Select2
function formatLayananOption(layanan) {
    if (!layanan.id) return layanan.text;
    
    const harga = layanan.element.getAttribute('data-harga') || 0;
    const deskripsi = layanan.element.getAttribute('data-deskripsi') || '';
    const hargaFormatted = formatCurrency(parseFloat(harga));
    
    const $container = $(
        `<div class="flex flex-col">
            <div class="font-medium">${layanan.text}</div>
            <div class="text-sm text-gray-600">${deskripsi}</div>
            <div class="text-sm text-green-600 font-semibold">${hargaFormatted}</div>
        </div>`
    );
    
    return $container;
}

// Format tampilan selection di Select2
function formatLayananSelection(layanan) {
    if (!layanan.id) return layanan.text;
    
    const harga = layanan.element.getAttribute('data-harga') || 0;
    const hargaFormatted = formatCurrency(parseFloat(harga));
    
    return `${layanan.text} - ${hargaFormatted}`;
}

// ==================== SESSION HANDLING ====================
function checkSessionError(response) {
    if (response.status === 401 || response.status === 419) {
        showSessionExpiredModal();
        return true;
    }
    return false;
}

function showSessionExpiredModal() {
    showModal(sessionExpiredModal);
}

function hideSessionExpiredModal() {
    hideModal(sessionExpiredModal);
}

// ==================== CALCULATION FUNCTIONS ====================
function calculateTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
    const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
    const feeMaintenance = parseFloat(document.getElementById('fee_maintenance').value) || 0;

    const taxAmount = Math.round(subtotal * (taxPercentage / 100));
    const total = subtotal + taxAmount + feeMaintenance;

    const totalRounded = Math.round(total);
    document.getElementById('total').value = totalRounded;
    const totalDisplay = document.getElementById('totalDisplay');
    if (totalDisplay) {
        totalDisplay.value = formatCurrency(totalRounded);
    }
}

function calculateTotalEdit() {
    const subtotal = parseFloat(document.getElementById('editSubtotal').value) || 0;
    const taxPercentage = parseFloat(document.getElementById('editTaxPercentage').value) || 0;
    const feeMaintenance = parseFloat(document.getElementById('editFeeMaintenance').value) || 0;

    const taxAmount = Math.round(subtotal * (taxPercentage / 100));
    const total = subtotal + taxAmount + feeMaintenance;

    const totalRounded = Math.round(total);
    document.getElementById('editTotal').value = totalRounded;
    const editTotalDisplay = document.getElementById('editTotalDisplay');
    if (editTotalDisplay) {
        editTotalDisplay.value = formatCurrency(totalRounded);
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

function formatNumber(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function parseCurrencyInput(value) {
    const numeric = String(value ?? '').replace(/[^0-9]/g, '');
    return numeric ? parseFloat(numeric) : 0;
}

function syncCurrencyField(displayId, hiddenId, triggerTotal = false, isEdit = false) {
    const displayEl = document.getElementById(displayId);
    const hiddenEl = document.getElementById(hiddenId);
    if (!displayEl || !hiddenEl) return;

    const numeric = parseCurrencyInput(displayEl.value);
    hiddenEl.value = numeric;
    displayEl.value = formatCurrency(numeric);

    if (triggerTotal) {
        if (isEdit) {
            calculateTotalEdit();
        } else {
            calculateTotal();
        }
    }
}

function setCurrencyField(displayId, hiddenId, value, triggerTotal = false, isEdit = false) {
    const displayEl = document.getElementById(displayId);
    const hiddenEl = document.getElementById(hiddenId);
    const numeric = parseFloat(value) || 0;

    if (hiddenEl) hiddenEl.value = numeric;
    if (displayEl) displayEl.value = formatCurrency(numeric);

    if (triggerTotal) {
        if (isEdit) {
            calculateTotalEdit();
        } else {
            calculateTotal();
        }
    }
}

// Function untuk mendapatkan tanggal hari ini dalam format YYYY-MM-DD
function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// ==================== EVENT LISTENERS ====================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing invoice management system...');

    // Event listeners untuk perhitungan otomatis CREATE form
    const subtotalInput = document.getElementById('subtotalDisplay');
    const taxPercentageInput = document.getElementById('tax_percentage');
    const feMaintenanceInput = document.getElementById('feeMaintenanceDisplay');

    if (subtotalInput && taxPercentageInput) {
        subtotalInput.addEventListener('input', () => syncCurrencyField('subtotalDisplay', 'subtotal', true, false));
        subtotalInput.addEventListener('blur', () => syncCurrencyField('subtotalDisplay', 'subtotal', true, false));
        taxPercentageInput.addEventListener('input', calculateTotal);
    }

    if (feMaintenanceInput) {
        feMaintenanceInput.addEventListener('input', () => syncCurrencyField('feeMaintenanceDisplay', 'fee_maintenance', true, false));
        feMaintenanceInput.addEventListener('blur', () => syncCurrencyField('feeMaintenanceDisplay', 'fee_maintenance', true, false));
    }

    // Event listeners untuk perhitungan otomatis EDIT form
    const editSubtotalInput = document.getElementById('editSubtotalDisplay');
    const editTaxPercentageInput = document.getElementById('editTaxPercentage');
    const editFeMaintenanceInput = document.getElementById('editFeeMaintenanceDisplay');

    if (editSubtotalInput && editTaxPercentageInput) {
        editSubtotalInput.addEventListener('input', () => syncCurrencyField('editSubtotalDisplay', 'editSubtotal', true, true));
        editSubtotalInput.addEventListener('blur', () => syncCurrencyField('editSubtotalDisplay', 'editSubtotal', true, true));
        editTaxPercentageInput.addEventListener('input', calculateTotalEdit);
    }

    if (editFeMaintenanceInput) {
        editFeMaintenanceInput.addEventListener('input', () => syncCurrencyField('editFeeMaintenanceDisplay', 'editFeeMaintenance', true, true));
        editFeMaintenanceInput.addEventListener('blur', () => syncCurrencyField('editFeeMaintenanceDisplay', 'editFeeMaintenance', true, true));
    }

    setCurrencyField('subtotalDisplay', 'subtotal', 0, false, false);
    setCurrencyField('feeMaintenanceDisplay', 'fee_maintenance', 0, false, false);
    setCurrencyField('totalDisplay', 'total', 0, false, false);
    setCurrencyField('editSubtotalDisplay', 'editSubtotal', 0, false, true);
    setCurrencyField('editFeeMaintenanceDisplay', 'editFeeMaintenance', 0, false, true);
    setCurrencyField('editTotalDisplay', 'editTotal', 0, false, true);

    // Event listener untuk tombol Buat Invoice
    if (buatInvoiceBtn) {
        buatInvoiceBtn.addEventListener('click', async function() {
            console.log('Tombol Buat Invoice diklik');

            try {
                // Set tanggal default ke hari ini
                const today = new Date().toISOString().split('T')[0];
                const invoiceDateInput = document.getElementById('invoice_date');
                if (invoiceDateInput) {
                    invoiceDateInput.value = today;
                }

                // Generate invoice number
                const invoiceNo = 'INV-' + new Date().getTime();
                const invoiceNoInput = document.getElementById('invoice_no');
                if (invoiceNoInput) {
                    invoiceNoInput.value = invoiceNo;
                }

                // Tampilkan modal
                console.log('Menampilkan modal...');
                showModal(buatModal);

                // Tunggu agar modal benar-benar tampil
                await new Promise(resolve => setTimeout(resolve, 100));

                // Reset form
                resetCreateForm();

                // Load data perusahaan dan layanan
                console.log('Memulai load data untuk form...');
                await loadDataForCreateForm();

                console.log('Modal dan form siap digunakan');
            } catch (error) {
                console.error('Error dalam handle klik Buat Invoice:', error);
                showModal(buatModal);
            }
        });
    } else {
        console.error('Tombol Buat Invoice tidak ditemukan di DOM!');
    }

    // Event untuk close modal create
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            hideModal(buatModal);
            resetCreateForm();
            clearValidationErrors('create');
        });
    }

    // Event untuk cancel button create
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            hideModal(buatModal);
            resetCreateForm();
            clearValidationErrors('create');
        });
    }

    // Event untuk form submit create
    if (buatInvoiceForm) {
        buatInvoiceForm.addEventListener('submit', handleCreateInvoice);
    }

    // Event untuk close modal edit
    const editModal = document.getElementById('editInvoiceModal');
    if (closeEditModalBtn && editModal) {
        closeEditModalBtn.addEventListener('click', function() {
            hideModal(editModal);
            clearValidationErrors('edit');
        });
    }

    // Event untuk cancel button edit
    if (cancelEditBtn && editModal) {
        cancelEditBtn.addEventListener('click', function() {
            hideModal(editModal);
            clearValidationErrors('edit');
        });
    }

    // Event untuk form submit edit
    if (editInvoiceForm) {
        editInvoiceForm.addEventListener('submit', handleUpdateInvoice);
    }

    // Setup event listener untuk dropdown perusahaan DI EDIT FORM
    const editSelectPerusahaan = document.getElementById('editSelectPerusahaan');
    if (editSelectPerusahaan) {
        $(editSelectPerusahaan).select2({
            placeholder: 'Pilih Perusahaan',
            allowClear: true,
            width: '100%'
        }).off('change').on('change', function() {
            console.log('Edit perusahaan dropdown changed - calling handlePerusahaanChange');
            handlePerusahaanChange(this, true);
        });
    }

    // Setup event listener untuk dropdown layanan DI EDIT FORM
    const editSelectLayanan = document.getElementById('editSelectLayanan');
    if (editSelectLayanan) {
        $(editSelectLayanan).select2({
            placeholder: 'Pilih Layanan',
            allowClear: true,
            width: '100%',
            templateResult: formatLayananOption,
            templateSelection: formatLayananSelection
        }).off('change').on('change', function() {
            console.log('Edit layanan dropdown changed');
            triggerLayananChange(this, true);
        });
    }

    // Event untuk search
    if (searchInput) {
        searchInput.addEventListener('input', filterInvoices);
    }

    // Event untuk pagination
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', goToPrevPage);
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', goToNextPage);
    }

    // Event untuk reload page button
    if (reloadPageBtn) {
        reloadPageBtn.addEventListener('click', function() {
            window.location.reload();
        });
    }

    // Event untuk delete
    const deleteModal = document.getElementById('deleteInvoiceModal');
    if (confirmDeleteBtn && deleteModal) {
        confirmDeleteBtn.addEventListener('click', handleDeleteInvoice);
    }

    if (cancelDeleteBtn && deleteModal) {
        cancelDeleteBtn.addEventListener('click', function() {
            hideModal(deleteModal);
        });
    }

    if (closeDeleteModalBtn && deleteModal) {
        closeDeleteModalBtn.addEventListener('click', function() {
            hideModal(deleteModal);
        });
    }

    // Event untuk filter dropdown
    if (filterBtn && filterDropdown) {
        filterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            filterDropdown.classList.toggle('show');
        });
    }

    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            if (filterDropdown) filterDropdown.classList.remove('show');
            filterInvoices();
        });
    }

    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            // Reset semua checkbox
            const checkboxes = document.querySelectorAll('#filterDropdown input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkbox.id === 'filterAllStatus';
            });
            if (filterDropdown) filterDropdown.classList.remove('show');
            filterInvoices();
        });
    }

    // Close filter dropdown ketika klik di luar
    if (filterDropdown && filterBtn) {
        document.addEventListener('click', function(e) {
            if (!filterDropdown.contains(e.target) && !filterBtn.contains(e.target)) {
                filterDropdown.classList.remove('show');
            }
        });
    }

    // Event untuk close modal detail
    const detailModal = document.getElementById('detailInvoiceModal');
    if (closeDetailModalBtn && detailModal) {
        closeDetailModalBtn.addEventListener('click', closeDetailModal);
    }

    // Close modal detail dengan ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (detailModal && !detailModal.classList.contains('hidden')) {
                closeDetailModal();
            }
        }
    });

    // Close modal detail ketika klik di luar
    if (detailModal) {
        detailModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });
    }

    // Load data awal
    loadInvoices();
    loadDataPerusahaan();
    loadDataLayanan();
});

// Initialize Select2
function initializeSelect2() {
    console.log('Initializing Select2...');

    // Payment method dropdown
    const paymentMethod = document.getElementById('payment_method');
    if (paymentMethod) {
        $('#payment_method').select2({
            placeholder: 'Pilih Metode Pembayaran',
            minimumResultsForSearch: -1,
            width: '100%'
        });
        console.log('Select2 payment_method initialized');
    }

    const editPaymentMethod = document.getElementById('editPaymentMethod');
    if (editPaymentMethod) {
        $('#editPaymentMethod').select2({
            placeholder: 'Pilih Metode Pembayaran',
            minimumResultsForSearch: -1,
            width: '100%'
        });
    }

    // Status pembayaran dropdown
    const statusPembayaran = document.getElementById('status_pembayaran');
    if (statusPembayaran) {
        $('#status_pembayaran').select2({
            placeholder: 'Pilih Status',
            minimumResultsForSearch: -1,
            width: '100%'
        });
        console.log('Select2 status_pembayaran initialized');
    }

    const editStatusPembayaran = document.getElementById('editStatusPembayaran');
    if (editStatusPembayaran) {
        $('#editStatusPembayaran').select2({
            placeholder: 'Pilih Status',
            minimumResultsForSearch: -1,
            width: '100%'
        });
    }


    // Perusahaan dropdown
    const perusahaanSelect = document.getElementById('selectPerusahaan');
    if (perusahaanSelect) {
        $(perusahaanSelect).select2({
            placeholder: 'Pilih Perusahaan',
            allowClear: true,
            width: '100%'
        });
        console.log('Select2 perusahaan initialized');
    }
}

// Load data for create form
async function loadDataForCreateForm() {
    console.log('Loading data for create form...');

    try {
        // Cek apakah form sudah ada di DOM
        const formModal = document.getElementById('buatInvoiceModal');
        if (!formModal) {
            console.error('Modal create form tidak ditemukan!');
            return;
        }

        // Set tanggal default ke hari ini
        const today = new Date().toISOString().split('T')[0];
        const invoiceDateInput = document.getElementById('invoice_date');
        if (invoiceDateInput) {
            invoiceDateInput.value = today;
        }

        // Generate invoice number
        const invoiceNo = 'INV-' + new Date().getTime();
        const invoiceNoInput = document.getElementById('invoice_no');
        if (invoiceNoInput) {
            invoiceNoInput.value = invoiceNo;
        }

        // Load perusahaan data if empty
        if (dataPerusahaan.length === 0) {
            console.log('Memuat data perusahaan...');
            await loadDataPerusahaan();
        } else {
            console.log('Menggunakan data perusahaan yang sudah dimuat:', dataPerusahaan.length);
            populatePerusahaanOptions();
        }

        // Load layanan data if empty
        if (dataLayanan.length === 0) {
            console.log('Memuat data layanan...');
            await loadDataLayanan();
        } else {
            console.log('Menggunakan data layanan yang sudah dimuat:', dataLayanan.length);
            populateLayananOptions();
        }

        // Setup event listeners for select changes
        setupSelectListeners();

        // Inisialisasi Select2 dengan benar
        initializeSelect2();
        
        // Inisialisasi Select2 untuk layanan (khusus)
        initializeLayananSelect2();

        console.log('Data for create form loaded successfully');
    } catch (error) {
        console.error('Error loading data for create form:', error);
    }
}

function setupSelectListeners() {
    console.log('Setting up select listeners...');

    // Event listener untuk pilih perusahaan sudah di-setup di populatePerusahaanOptions
    console.log('Select listeners setup completed');
}

function handlePerusahaanChange(selectElement, isEditForm = false) {
    const prefix = isEditForm ? 'edit' : '';
    const clientNameInput = document.getElementById(prefix + 'ClientName') || document.getElementById('client_name');
    const companyAddressInput = document.getElementById(prefix + 'CompanyAddress') || document.getElementById('company_address');
    const kontakInput = document.getElementById(prefix + 'KontakPerusahaan') || document.getElementById('kontakPerusahaan');
    const kontakHiddenInput = document.getElementById(prefix + 'KontakHidden') || document.getElementById('kontakHidden');
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    if (selectedOption && selectedOption.value && selectedOption.value !== 'data_kosong') {
        const klien = selectedOption.getAttribute('data-klien') || '';
        const alamat = selectedOption.getAttribute('data-alamat') || '';
        const kontak = selectedOption.getAttribute('data-kontak') || '';

        if (clientNameInput) {
            clientNameInput.value = klien;
        }
        if (companyAddressInput) {
            companyAddressInput.value = alamat;
        }
        if (kontakInput) {
            kontakInput.value = kontak;
        }

        // Auto-fill hidden kontak field  
        if (isEditForm) {
            const kontakHidden = document.getElementById('editKontakHidden');
            if (kontakHidden) {
                kontakHidden.value = kontak;
            }
        } else {
            const kontakHidden = document.getElementById('kontakHidden');
            if (kontakHidden) {
                kontakHidden.value = kontak;
            } else {
                // Buat jika belum ada
                const newKontakHidden = document.createElement('input');
                newKontakHidden.type = 'hidden';
                newKontakHidden.id = 'kontakHidden';
                newKontakHidden.name = 'kontak';
                newKontakHidden.value = kontak;
                
                const form = document.getElementById('buatInvoiceForm');
                if (form) {
                    form.appendChild(newKontakHidden);
                }
            }
        }
        
        console.log('Perusahaan selected - kontak:', kontak);
    } else {
        // Reset semua field jika tidak ada perusahaan yang dipilih
        if (clientNameInput) clientNameInput.value = '';
        if (companyAddressInput) companyAddressInput.value = '';
        if (kontakInput) kontakInput.value = '';
        
        if (isEditForm) {
            const kontakHidden = document.getElementById('editKontakHidden');
            if (kontakHidden) kontakHidden.value = '';
        } else {
            const kontakHidden = document.getElementById('kontakHidden');
            if (kontakHidden) kontakHidden.value = '';
        }
    }
}

function resetCreateForm() {
    console.log('Reset create form...');

    // Reset text inputs
    const fieldsToReset = [
        'client_name', 'company_address', 'kontakPerusahaan',
        'hargaLayanan', 'deskripsiLayanan',
        'invoice_no', 'keterangan_tambahan'
    ];

    fieldsToReset.forEach(id => { 
        const element = document.getElementById(id);
        if (element) {
            if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                element.value = '';
            }
        }
    });

    // Set default values
    const taxPercentage = document.getElementById('tax_percentage');
    if (taxPercentage) taxPercentage.value = '11';

    // Reset Select2 dropdowns dengan benar
    const select2Ids = ['selectPerusahaan', 'selectLayanan', 'payment_method', 'status_pembayaran', 'jenis_bank', 'kategori_pemasukan'];

    select2Ids.forEach(id => {
        const selectElement = $(`#${id}`);
        if (selectElement.length) {
            if (id === 'status_pembayaran') {
                selectElement.val('down payment').trigger('change');
            } else {
                selectElement.val(null).trigger('change');
            }
        }
    });

    // Set default values untuk fee maintenance
    setCurrencyField('subtotalDisplay', 'subtotal', 0, false, false);
    setCurrencyField('feeMaintenanceDisplay', 'fee_maintenance', 0, false, false);
    setCurrencyField('totalDisplay', 'total', 0, false, false);

    // Hitung ulang total
    calculateTotal();

    // Clear validation errors
    clearValidationErrors('create');

    console.log('Create form reset completed');
}

// ==================== MODAL FUNCTIONS ====================
function showModal(modal) {
    if (modal) {
        console.log(`Menampilkan modal: ${modal.id}`);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('Modal tidak ditemukan!');
    }
}

function hideModal(modal) {
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// ==================== LOADING OVERLAY FUNCTIONS ====================
function showLoadingOverlay() {
    if (loadingOverlay) {
        loadingOverlay.classList.remove('hidden');
    }
}

function hideLoadingOverlay() {
    if (loadingOverlay) {
        loadingOverlay.classList.add('hidden');
    }
}

// ==================== DETAIL INVOICE FUNCTIONS ====================
function showDetailModal(id) {
    console.log('Showing detail for invoice:', id);
    currentDetailInvoiceId = id;

    // Tampilkan loading
    showPopup('info', 'Memuat', 'Memuat detail invoice...');

    // Cari invoice dari data yang sudah dimuat
    const invoice = allInvoices.find(inv => inv.id == id);
    if (invoice) {
        populateDetailModal(invoice);
        showModal(document.getElementById('detailInvoiceModal'));
    } else {
        // Jika tidak ada di cache, fetch dari server
        fetchInvoiceDetail(id);
    }
}

function fetchInvoiceDetail(id) {
    fetch(`{{ route('finance.invoice.show', ':id') }}`.replace(':id', id), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        })
        .then(response => {
            if (checkSessionError(response)) {
                return Promise.reject('Session expired');
            }
            return response.json();
        })
        .then(data => {
            let invoice;
            if (data.success) {
                invoice = data.data || data.invoice;
            } else if (data.invoice) {
                invoice = data.invoice;
            } else {
                invoice = data;
            }

            if (invoice) {
                populateDetailModal(invoice);
                showModal(document.getElementById('detailInvoiceModal'));
            } else {
                throw new Error('Data invoice tidak ditemukan');
            }
        })
        .catch(error => {
            console.error('Error loading invoice detail:', error);
            if (error.message !== 'Session expired') {
                showPopup('error', 'Gagal', 'Gagal memuat detail invoice');
            }
        });
}

function populateDetailModal(invoice) {
    console.log('Populating detail modal with:', invoice);

    // Format tanggal
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const formatDateTime = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // Basic Information
    document.getElementById('detailCompanyName').textContent =
        invoice.company_name || invoice.nama_perusahaan || '-';
    document.getElementById('detailCompanyAddress').textContent =
        invoice.company_address || invoice.alamat || '-';
    document.getElementById('detailClientName').textContent =
        invoice.client_name || invoice.nama_klien || '-';
    document.getElementById('detailInvoiceNo').textContent =
        invoice.invoice_no || invoice.nomor_order || '-';
    document.getElementById('detailInvoiceDate').textContent =
        formatDate(invoice.invoice_date || invoice.tanggal);
    document.getElementById('detailInvoiceSubtitle').textContent =
        `Invoice #${invoice.invoice_no || invoice.nomor_order || ''}`;
    document.getElementById('detailInvoiceId').textContent = invoice.id || '-';

    // Service Information
    document.getElementById('detailNamaLayanan').textContent =
        invoice.nama_layanan || '-';
    document.getElementById('detailPaymentMethod').textContent =
        invoice.payment_method || invoice.metode_pembayaran || '-';

    // Additional Information
    // Set values for additional information
    const jenisBank = document.getElementById('detailJenisBank');
    if (jenisBank) {
        jenisBank.textContent = invoice.jenis_bank || '-';
    }
    
    const kategoriPemasukan = document.getElementById('detailKategoriPemasukan');
    if (kategoriPemasukan) {
        kategoriPemasukan.textContent = invoice.kategori_pemasukan ? 
            invoice.kategori_pemasukan.charAt(0).toUpperCase() + invoice.kategori_pemasukan.slice(1) : '-';
    }
    
    const feeMaintenance = document.getElementById('detailFeeMaintenance');
    if (feeMaintenance) {
        feeMaintenance.textContent = invoice.fee_maintenance ? 
            formatCurrency(invoice.fee_maintenance) : 'Rp 0';
    }
    
    const keteranganTambahan = document.getElementById('detailKeteranganTambahan');
    if (keteranganTambahan) {
        keteranganTambahan.textContent = invoice.keterangan_tambahan || '-';
    }

    // Description
    const description = invoice.deskripsi || invoice.deskripsiLayanan || 'Tidak ada deskripsi';
    document.getElementById('detailDescription').textContent = description;

    // Status dengan styling
    const status = invoice.status_pembayaran || 'down payment';
    const statusElement = document.getElementById('detailStatusBadge');
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);

    let statusClass = '';
    let statusIcon = '';

    if (status === 'lunas') {
        statusClass = 'detail-status-lunas';
        statusIcon = '<span class="material-icons-outlined text-sm">check_circle</span>';
    } else if (status === 'down payment') {
        statusClass = 'detail-status-pembayaran-awal';
        statusIcon = '<span class="material-icons-outlined text-sm">payments</span>';
    } else {
        statusClass = 'detail-status-pending';
        statusIcon = '<span class="material-icons-outlined text-sm">pending</span>';
    }

    if (statusElement) {
        statusElement.innerHTML = `
            <span class="detail-status-badge ${statusClass} flex items-center gap-1">
                ${statusIcon}
                ${statusText}
            </span>
        `;
    }

    // Financial Information
    const subtotal = invoice.subtotal || 0;
    const taxAmount = invoice.tax || 0;
    const total = invoice.total || 0;
    const taxPercentage = invoice.tax_percentage || (subtotal > 0 ? ((taxAmount / subtotal) * 100) : 0);

    document.getElementById('detailSubtotal').textContent = formatCurrency(subtotal);
    document.getElementById('detailTax').textContent = formatCurrency(taxAmount);
    document.getElementById('detailTotal').textContent = formatCurrency(total);
    document.getElementById('detailTaxPercentageText').textContent =
        `Pajak ${taxPercentage.toFixed(2)}%`;

    // Timeline/History
    document.getElementById('detailCreatedAt').textContent =
        formatDateTime(invoice.created_at || new Date().toISOString());
    document.getElementById('detailUpdatedAt').textContent =
        formatDateTime(invoice.updated_at || invoice.created_at || new Date().toISOString());
    document.getElementById('detailLastUpdated').textContent =
        formatDateTime(invoice.updated_at || invoice.created_at || new Date().toISOString());

    // Status Icon
    const statusIconElement = document.getElementById('detailStatusIcon');
    if (statusIconElement) {
        statusIconElement.innerHTML = '';
        statusIconElement.className =
            'absolute left-[-20px] top-0 w-8 h-8 rounded-full flex items-center justify-center ';

        const icon = document.createElement('span');
        icon.className = 'material-icons-outlined text-white text-sm';

        if (status === 'lunas') {
            statusIconElement.classList.add('bg-green-500');
            icon.textContent = 'check_circle';
            document.getElementById('detailStatusText').textContent = 'Lunas';
        } else if (status === 'down payment') {
            statusIconElement.classList.add('bg-blue-500');
            icon.textContent = 'payments';
            document.getElementById('detailStatusText').textContent = 'Down Payment';
        } else {
            statusIconElement.classList.add('bg-yellow-500');
            icon.textContent = 'pending';
            document.getElementById('detailStatusText').textContent = 'Menunggu Pembayaran';
        }

        statusIconElement.appendChild(icon);
    }
}

function closeDetailModal() {
    hideModal(document.getElementById('detailInvoiceModal'));
    currentDetailInvoiceId = null;
}

function printInvoiceModalFromDetail() {
    if (currentDetailInvoiceId) {
        closeDetailModal();
        setTimeout(() => {
            printInvoiceModal(currentDetailInvoiceId);
        }, 300);
    }
}

function editInvoiceFromDetail() {
    if (currentDetailInvoiceId) {
        closeDetailModal();
        setTimeout(() => {
            editInvoice(currentDetailInvoiceId);
        }, 300);
    }
}

// ==================== INVOICE FUNCTIONS ====================
function loadInvoices() {
    console.log('Memuat data invoice...');
    showLoading(true);

    fetch('/finance/api/invoices?ajax=1', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        })
        .then(response => {
            console.log('Response status:', response.status);

            if (checkSessionError(response)) {
                return Promise.reject('Session expired');
            }

            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response text:', text);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Data invoice diterima:', data);

            if (data.error) {
                console.error('Error dalam response:', data.error);
                throw new Error(data.error || 'Terjadi kesalahan saat memuat data');
            }

            allInvoices = data.data || data.invoices || [];
            filteredInvoices = [...allInvoices];
            renderInvoices();
            updateTotalCount();
            renderPagination();
            showLoading(false);
        })
        .catch(error => {
            console.error('Error loading invoices:', error);

            if (error.message === 'Session expired') {
                return;
            }

            showPopup('error', 'Gagal', 'Gagal memuat data invoice: ' + error.message);
            showLoading(false);

            desktopTableBody.innerHTML = `
            <tr id="noDataRow">
                <td colspan="19" class="px-6 py-4 text-center text-sm text-gray-500">
                    <div class="flex flex-col items-center">
                        <span class="material-icons-outlined text-red-500 mb-2">error</span>
                        <p>Gagal memuat data: ${error.message}</p>
                        <p class="text-xs text-gray-400 mt-1">Periksa koneksi internet atau coba refresh halaman</p>
                        <button onclick="loadInvoices()" class="mt-3 px-4 py-2 btn-primary rounded-lg text-sm">
                            <span class="material-icons-outlined text-sm mr-2">refresh</span>
                            Coba Lagi
                        </button>
                    </div>
                </td>
                </tr>
        `;
            noDataRow.classList.remove('hidden');
        });
}

function handleCreateInvoice(e) {
    e.preventDefault();
    console.log('Membuat invoice baru...');

    clearValidationErrors('create');

    // Get form data
    const form = document.getElementById('buatInvoiceForm');
    if (!form) {
        showPopup('error', 'Error', 'Form tidak ditemukan');
        return;
    }

    // Get form values
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Validasi required fields
    const requiredFields = ['company_name', 'invoice_date', 'invoice_no', 'client_name', 'company_address',
        'nama_layanan', 'status_pembayaran', 'payment_method', 'subtotal', 'tax_percentage'
    ];

    for (const field of requiredFields) {
        if (!data[field]) {
            showPopup('error', 'Validasi Gagal', `Harap isi field ${field}`);
            return;
        }
    }

    // Convert numeric values
    data.subtotal = parseFloat(data.subtotal) || 0;
    data.tax_percentage = parseFloat(data.tax_percentage) || 0;
    data.fee_maintenance = parseFloat(data.fee_maintenance) || 0;
    data.tax = Math.round(data.subtotal * (data.tax_percentage / 100));
    data.total = data.subtotal + data.tax + data.fee_maintenance;

    console.log('Data yang akan dikirim:', data);

    // Find submit button
    const submitBtn = document.querySelector('#buatInvoiceModal button[type="submit"]');
    
    if (!submitBtn) {
        console.error('Submit button tidak ditemukan!');
        showPopup('error', 'Error', 'Tombol submit tidak ditemukan');
        return;
    }
    
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Menyimpan...';
    submitBtn.classList.add('opacity-50');

    fetch('{{ route('finance.invoice.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data),
            credentials: 'include'
        })
        .then(async response => {
            if (checkSessionError(response)) {
                return Promise.reject('Session expired');
            }

            const responseData = await response.json();
            console.log('Response dari server:', responseData);

            if (!response.ok) {
                if (response.status === 422 && responseData.errors) {
                    showValidationErrorsFromServer(responseData.errors, 'create');
                    throw new Error('Validasi gagal');
                } else if (response.status === 500) {
                    throw new Error(responseData.message || 'Terjadi kesalahan server. Silakan coba lagi.');
                } else {
                    throw new Error(responseData.message ||
                        `Gagal membuat invoice (Status: ${response.status})`);
                }
            }

            return responseData;
        })
        .then(responseData => {
            console.log('Invoice berhasil dibuat:', responseData);
            showPopup('success', 'Berhasil', 'Invoice berhasil dibuat');
            hideModal(buatModal);
            resetCreateForm();
            loadInvoices();
        })
        .catch(error => {
            console.error('Error creating invoice:', error);

            if (error.message === 'Session expired') {
                return;
            }

            if (error.message !== 'Validasi gagal') {
                showPopup('error', 'Gagal', error.message ||
                    'Gagal membuat invoice. Silakan cek konsol untuk detail.');
            }
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                submitBtn.classList.remove('opacity-50');
            }
        });
}

function handleUpdateInvoice(e) {
    e.preventDefault();

    const id = document.getElementById('editInvoiceId').value;

    // Get fresh form reference
    const form = document.getElementById('editInvoiceForm');
    if (!form) {
        showPopup('error', 'Error', 'Form tidak ditemukan di halaman');
        return;
    }

    // Get form values
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Validasi required fields
    const requiredFields = ['company_name', 'invoice_date', 'invoice_no', 'client_name', 'company_address',
        'nama_layanan', 'status_pembayaran', 'payment_method', 'subtotal', 'tax_percentage'
    ];

    for (const field of requiredFields) {
        if (!data[field]) {
            showPopup('error', 'Validasi Gagal', `Harap isi field ${field}`);
            return;
        }
    }

    // Convert numeric values
    data.subtotal = parseFloat(data.subtotal) || 0;
    data.tax_percentage = parseFloat(data.tax_percentage) || 0;
    data.fee_maintenance = parseFloat(data.fee_maintenance) || 0;
    data.tax = Math.round(data.subtotal * (data.tax_percentage / 100));
    data.total = data.subtotal + data.tax + data.fee_maintenance;
    data._method = 'PUT';

    console.log('Updating invoice:', id, data);

    // Try to find and disable submit button
    const submitBtn = document.querySelector('#editInvoiceModal button[type="submit"]');
    let originalBtnText = null;
    if (submitBtn) {
        originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memperbarui...';
        submitBtn.classList.add('opacity-50');
    }

    fetch(`{{ route('finance.invoice.update', ':id') }}`.replace(':id', id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data),
            credentials: 'include'
        })
        .then(async response => {
            if (checkSessionError(response)) {
                return Promise.reject('Session expired');
            }

            const responseData = await response.json();

            if (!response.ok) {
                if (response.status === 422 && responseData.errors) {
                    showValidationErrorsFromServer(responseData.errors, 'edit');
                    throw new Error('Validasi gagal');
                }
                throw new Error(responseData.message || 'Gagal update invoice');
            }

            return responseData;
        })
        .then(responseData => {
            showPopup('success', 'Berhasil', 'Invoice berhasil diperbarui');
            hideModal(document.getElementById('editInvoiceModal'));
            loadInvoices();
        })
        .catch(error => {
            console.error(error);

            if (error.message === 'Session expired') {
                return;
            }

            if (error.message !== 'Validasi gagal') {
                showPopup('error', 'Gagal', error.message);
            }
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                submitBtn.classList.remove('opacity-50');
            }
        });
}

function editInvoice(id) {
    console.log('Edit invoice:', id);

    showPopup('info', 'Memuat', 'Memuat data invoice...');

    // Ensure perusahaan and layanan data are loaded
    Promise.all([
        dataPerusahaan.length === 0 ? loadDataPerusahaan() : Promise.resolve(),
        dataLayanan.length === 0 ? loadDataLayanan() : Promise.resolve()
    ]).then(() => {
        // After data is loaded, populate the dropdowns
        populatePerusahaanOptions();
        populateLayananOptions();
        
        // Then fetch and populate invoice data
        return fetch(`{{ route('finance.invoice.show', ':id') }}`.replace(':id', id), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });
    }).then(response => {
        console.log('Edit response status:', response.status);

        if (checkSessionError(response)) {
            return Promise.reject('Session expired');
        }

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    }).then(data => {
        console.log('Invoice data received:', data);

        let invoice;
        if (data.success) {
            invoice = data.data || data.invoice;
        } else if (data.invoice) {
            invoice = data.invoice;
        } else {
            invoice = data;
        }

        if (!invoice) {
            throw new Error('Data invoice tidak ditemukan');
        }

        console.log('Invoice data to edit:', invoice);

        // Populate form fields
        const editInvoiceIdField = document.getElementById('editInvoiceId');
        if (editInvoiceIdField) editInvoiceIdField.value = invoice.id;
        
        const editSelectPerusahaanField = document.getElementById('editSelectPerusahaan');
        if (editSelectPerusahaanField) {
            editSelectPerusahaanField.value = invoice.company_name || invoice.nama_perusahaan || '';
            // Trigger change untuk mengisi kontak dan data lainnya
            setTimeout(() => {
                if ($('#editSelectPerusahaan').hasClass('select2-hidden-accessible')) {
                    $('#editSelectPerusahaan').trigger('change');
                } else {
                    handlePerusahaanChange(editSelectPerusahaanField, true);
                }
            }, 50);
        }
        
        const editKontakPerusahaanField = document.getElementById('editKontakPerusahaan');
        if (editKontakPerusahaanField) editKontakPerusahaanField.value = invoice.kontak || '';
        
        const editKontakHiddenField = document.getElementById('editKontakHidden');
        if (editKontakHiddenField) editKontakHiddenField.value = invoice.kontak || '';
        
        const editInvoiceDateField = document.getElementById('editInvoiceDate');
        if (editInvoiceDateField) {
            editInvoiceDateField.value = invoice.invoice_date ?
                (invoice.invoice_date.includes('T') ? invoice.invoice_date.split('T')[0] : invoice.invoice_date) : '';
        }
        
        const editInvoiceNoField = document.getElementById('editInvoiceNo');
        if (editInvoiceNoField) editInvoiceNoField.value = invoice.invoice_no || invoice.nomor_order || '';
        
        const editClientNameField = document.getElementById('editClientName');
        if (editClientNameField) editClientNameField.value = invoice.client_name || invoice.nama_klien || '';
        
        const editCompanyAddressField = document.getElementById('editCompanyAddress');
        if (editCompanyAddressField) editCompanyAddressField.value = invoice.company_address || invoice.alamat || '';
        
        const editKeteranganTambahanField = document.getElementById('editKeteranganTambahan');
        if (editKeteranganTambahanField) editKeteranganTambahanField.value = invoice.keterangan_tambahan || '';
        
        const editJenisBankField = document.getElementById('editJenisBank');
        if (editJenisBankField) editJenisBankField.value = invoice.jenis_bank || '';
        
        const editKategoriPemasukanField = document.getElementById('editKategoriPemasukan');
        if (editKategoriPemasukanField) editKategoriPemasukanField.value = invoice.kategori_pemasukan || '';
        
        const editFeeMaintenanceField = document.getElementById('editFeeMaintenance');
        if (editFeeMaintenanceField) editFeeMaintenanceField.value = invoice.fee_maintenance || '0';
        setCurrencyField('editFeeMaintenanceDisplay', 'editFeeMaintenance', invoice.fee_maintenance || 0, false, true);
        
        const editSubtotalField = document.getElementById('editSubtotal');
        if (editSubtotalField) editSubtotalField.value = invoice.subtotal || 0;
        setCurrencyField('editSubtotalDisplay', 'editSubtotal', invoice.subtotal || 0, false, true);
        
        const editTaxPercentageField = document.getElementById('editTaxPercentage');
        if (editTaxPercentageField) editTaxPercentageField.value = invoice.tax_percentage || 0;
        
        const editTotalField = document.getElementById('editTotal');
        if (editTotalField) editTotalField.value = invoice.total || 0;
        setCurrencyField('editTotalDisplay', 'editTotal', invoice.total || 0, false, true);
        
        const editPaymentMethodField = document.getElementById('editPaymentMethod');
        if (editPaymentMethodField) editPaymentMethodField.value = invoice.payment_method || invoice.metode_pembayaran || 'Bank Transfer';
        
    const editStatusPembayaranField = document.getElementById('editStatusPembayaran');
    if (editStatusPembayaranField) editStatusPembayaranField.value = invoice.status_pembayaran || 'down payment';

        // Set nilai untuk field layanan
        const selectLayananEdit = document.getElementById('editSelectLayanan');
        if (selectLayananEdit) {
            selectLayananEdit.value = invoice.nama_layanan || '';
            // Trigger change untuk mengisi data lainnya
            setTimeout(() => {
                if ($('#editSelectLayanan').hasClass('select2-hidden-accessible')) {
                    $('#editSelectLayanan').trigger('change');
                } else {
                    triggerLayananChange(selectLayananEdit, true);
                }
            }, 50);
        }

        calculateTotalEdit();
        showModal(document.getElementById('editInvoiceModal'));
    }).catch(error => {
        console.error('Error loading invoice:', error);

        if (error.message === 'Session expired') {
            return;
        }

        const invoice = allInvoices.find(inv => inv.id == id);
        if (invoice) {
            console.log('Using cached invoice data:', invoice);

            const editInvoiceIdField = document.getElementById('editInvoiceId');
            if (editInvoiceIdField) editInvoiceIdField.value = invoice.id;
            
            const editSelectPerusahaanField = document.getElementById('editSelectPerusahaan');
            if (editSelectPerusahaanField) {
                editSelectPerusahaanField.value = invoice.company_name || invoice.nama_perusahaan || '';
                setTimeout(() => {
                    if ($('#editSelectPerusahaan').hasClass('select2-hidden-accessible')) {
                        $('#editSelectPerusahaan').trigger('change');
                    } else {
                        handlePerusahaanChange(editSelectPerusahaanField, true);
                    }
                }, 50);
            }
            
            const editKontakPerusahaanField = document.getElementById('editKontakPerusahaan');
            if (editKontakPerusahaanField) editKontakPerusahaanField.value = invoice.kontak || '';
            
            const editKontakHiddenField = document.getElementById('editKontakHidden');
            if (editKontakHiddenField) editKontakHiddenField.value = invoice.kontak || '';
            
            const editInvoiceDateField = document.getElementById('editInvoiceDate');
            if (editInvoiceDateField) {
                editInvoiceDateField.value = invoice.invoice_date ?
                    (invoice.invoice_date.includes('T') ? invoice.invoice_date.split('T')[0] : invoice.invoice_date) : '';
            }
            
            const editInvoiceNoField = document.getElementById('editInvoiceNo');
            if (editInvoiceNoField) editInvoiceNoField.value = invoice.invoice_no || invoice.nomor_order || '';
            
            const editClientNameField = document.getElementById('editClientName');
            if (editClientNameField) editClientNameField.value = invoice.client_name || invoice.nama_klien || '';
            
            const editCompanyAddressField = document.getElementById('editCompanyAddress');
            if (editCompanyAddressField) editCompanyAddressField.value = invoice.company_address || invoice.alamat || '';
            
            const editKeteranganTambahanField = document.getElementById('editKeteranganTambahan');
            if (editKeteranganTambahanField) editKeteranganTambahanField.value = invoice.keterangan_tambahan || '';
            
            const editJenisBankField = document.getElementById('editJenisBank');
            if (editJenisBankField) editJenisBankField.value = invoice.jenis_bank || '';
            
            const editKategoriPemasukkanField = document.getElementById('editKategoriPemasukan');
            if (editKategoriPemasukkanField) editKategoriPemasukkanField.value = invoice.kategori_pemasukan || '';
            
            const editFeeMaintenanceField = document.getElementById('editFeeMaintenance');
            if (editFeeMaintenanceField) editFeeMaintenanceField.value = invoice.fee_maintenance || '0';
            setCurrencyField('editFeeMaintenanceDisplay', 'editFeeMaintenance', invoice.fee_maintenance || 0, false, true);
            
            const editSubtotalField = document.getElementById('editSubtotal');
            if (editSubtotalField) editSubtotalField.value = invoice.subtotal || 0;
            setCurrencyField('editSubtotalDisplay', 'editSubtotal', invoice.subtotal || 0, false, true);
            
            const editTaxPercentageField = document.getElementById('editTaxPercentage');
            if (editTaxPercentageField) editTaxPercentageField.value = invoice.tax_percentage || 0;
            
            const editTotalField = document.getElementById('editTotal');
            if (editTotalField) editTotalField.value = invoice.total || 0;
            setCurrencyField('editTotalDisplay', 'editTotal', invoice.total || 0, false, true);
            
            const editPaymentMethodField = document.getElementById('editPaymentMethod');
            if (editPaymentMethodField) editPaymentMethodField.value = invoice.payment_method || invoice.metode_pembayaran || 'Bank Transfer';
            
            const editStatusPembayaranField = document.getElementById('editStatusPembayaran');
            if (editStatusPembayaranField) editStatusPembayaranField.value = invoice.status_pembayaran || 'down payment';

            // Set nilai untuk field layanan dari cache
            const selectLayananEdit = document.getElementById('editSelectLayanan');
            if (selectLayananEdit) {
                selectLayananEdit.value = invoice.nama_layanan || '';
                setTimeout(() => {
                    if ($('#editSelectLayanan').hasClass('select2-hidden-accessible')) {
                        $('#editSelectLayanan').trigger('change');
                    } else {
                        triggerLayananChange(selectLayananEdit, true);
                    }
                }, 50);
            }

            // Trigger perusahaan change
            const selectPerusahaan = document.getElementById('editSelectPerusahaan');
            if (selectPerusahaan) {
                setTimeout(() => {
                    handlePerusahaanChange(selectPerusahaan, true);
                }, 50);
            }

            calculateTotalEdit();
            showModal(document.getElementById('editInvoiceModal'));
        } else {
            showPopup('error', 'Gagal', 'Gagal memuat data invoice. Silakan refresh halaman.');
        }
    });
}

function deleteInvoice(id) {
    const invoice = allInvoices.find(inv => inv.id == id);
    if (!invoice) return;

    document.getElementById('deleteInvoiceId').value = id;
    document.getElementById('deleteInvoiceNama').textContent = invoice.company_name || invoice.nama_perusahaan ||
        '';
    document.getElementById('deleteInvoiceNomor').textContent = invoice.invoice_no || invoice.nomor_order || '';

    showModal(document.getElementById('deleteInvoiceModal'));
}

function handleDeleteInvoice() {
    const id = document.getElementById('deleteInvoiceId').value;
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalBtnText = deleteBtn.textContent;

    // Tampilkan loading state
    deleteBtn.disabled = true;
    deleteBtn.textContent = 'Menghapus...';
    deleteBtn.classList.add('opacity-50');

    fetch(`{{ route('finance.invoice.destroy', ':id') }}`.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        })
        .then(response => {
            console.log('Delete response status:', response.status);

            if (checkSessionError(response)) {
                return Promise.reject('Session expired');
            }

            if (!response.ok) {
                throw new Error('DELETE method failed, trying POST method');
            }
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data);
            if (data.success) {
                showPopup('success', 'Berhasil', 'Invoice berhasil dihapus');
                hideModal(document.getElementById('deleteInvoiceModal'));
                loadInvoices();
            } else {
                throw new Error(data.message || 'Gagal menghapus invoice');
            }
        })
        .catch(error => {
            console.log('Trying POST method for delete...');
            // Method 2: POST dengan _method DELETE (fallback)
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', csrfToken);

            fetch(`{{ route('finance.invoice.destroy', ':id') }}`.replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    credentials: 'include'
                })
                .then(response => {
                    if (checkSessionError(response)) {
                        return Promise.reject('Session expired');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('POST delete response:', data);
                    if (data.success) {
                        showPopup('success', 'Berhasil', 'Invoice berhasil dihapus');
                        hideModal(document.getElementById('deleteInvoiceModal'));
                        loadInvoices();
                    } else {
                        showPopup('error', 'Gagal', data.message || 'Gagal menghapus invoice');
                    }
                })
                .catch(secondError => {
                    console.error('Error deleting invoice:', secondError);

                    if (secondError.message === 'Session expired') {
                        return;
                    }

                    showPopup('error', 'Gagal', 'Gagal menghapus invoice. Silakan coba lagi.');
                })
                .finally(() => {
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = originalBtnText;
                    deleteBtn.classList.remove('opacity-50');
                });
        });
}

function printInvoiceModal(id) {
    console.log('Print invoice modal:', id);
    const invoice = allInvoices.find(inv => inv.id == id);
    if (invoice) {
        const namaPerusahaan = invoice.company_name || invoice.nama_perusahaan;
        const nomorOrder = invoice.invoice_no || invoice.nomor_order;
        const tanggal = formatTanggal(invoice.invoice_date || invoice.tanggal);
        const namaKlien = invoice.client_name || invoice.nama_klien;
        const alamat = invoice.company_address || invoice.alamat;
        const deskripsi = invoice.deskripsi || invoice.deskripsiLayanan;
        const metodePembayaran = invoice.payment_method || invoice.metode_pembayaran;
        const subtotal = invoice.subtotal || 0;
        const taxAmount = invoice.tax || 0;
        const total = invoice.total || 0;
        const taxPercentage = invoice.tax_percentage || (subtotal > 0 ? ((taxAmount / subtotal) * 100) : 0);
        const namaLayanan = invoice.nama_layanan || '';
        const statusPembayaran = invoice.status_pembayaran || 'down payment';

        document.getElementById('printInvoiceContent').innerHTML = `
    <div style="padding: 30px; background: white; max-width: 800px; margin: 0 auto; font-family: 'Poppins', sans-serif;">
        <div style="border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px;">
            <h2 style="font-size: 28px; font-weight: bold; margin: 0 0 10px 0;">${namaPerusahaan}</h2>
            <p style="margin: 5px 0; color: #666;">Invoice #${nomorOrder}</p>
            <p style="margin: 5px 0; color: #666;">Tanggal: ${tanggal}</p>
            <p style="margin: 5px 0; color: #666;">Status Pembayaran: <strong>${statusPembayaran.toUpperCase()}</strong></p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px;">
            <div>
                <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 10px;">Bill To:</h3>
                <p style="margin: 5px 0;"><strong>Nama Klien:</strong> ${namaKlien}</p>
                <p style="margin: 5px 0;"><strong>Alamat:</strong> ${alamat}</p>
                <p style="margin: 5px 0;"><strong>Layanan:</strong> ${namaLayanan}</p>
            </div>
            <div>
                <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 10px;">Payment Details:</h3>
                <p style="margin: 5px 0;"><strong>Metode Pembayaran:</strong> ${metodePembayaran}</p>
                <p style="margin: 5px 0;"><strong>Status:</strong> ${statusPembayaran}</p>
            </div>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin: 30px 0;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Deskripsi</th>
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Subtotal</th>
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Pajak (${taxPercentage.toFixed(2)}%)</th>
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Jumlah Pajak</th>
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 12px;">${deskripsi}</td>
                    <td style="border: 1px solid #ddd; padding: 12px; text-align: right;">${formatCurrency(subtotal)}</td>
                    <td style="border: 1px solid #ddd; padding: 12px; text-align: right;">${taxPercentage.toFixed(2)}%</td>
                    <td style="border: 1px solid #ddd; padding: 12px; text-align: right;">${formatCurrency(taxAmount)}</td>
                    <td style="border: 1px solid #ddd; padding: 12px; text-align: right;">${formatCurrency(total)}</td>
                </tr>
            </tbody>
        </table>
        
        <div style="text-align: right; margin-top: 30px;">
            <table style="width: 300px; margin-left: auto; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; text-align: right;"><strong>Subtotal:</strong></td>
                    <td style="padding: 8px; text-align: right;">${formatCurrency(subtotal)}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; text-align: right;"><strong>Pajak (${taxPercentage.toFixed(2)}%):</strong></td>
                    <td style="padding: 8px; text-align: right;">${formatCurrency(taxAmount)}</td>
                </tr>
                <tr style="font-size: 18px; font-weight: bold;">
                    <td style="padding: 12px 8px; text-align: right; border-top: 2px solid #333;"><strong>Total:</strong></td>
                    <td style="padding: 12px 8px; text-align: right; border-top: 2px solid #333;">${formatCurrency(total)}</td>
                </tr>
            </table>
        </div>
        
        <div style="border-top: 2px solid #333; padding-top: 20px; margin-top: 40px;">
            <p style="margin: 10px 0;"><strong>Catatan:</strong></p>
            <p style="margin: 5px 0; color: #666;">Silakan transfer ke rekening yang tertera atau bayar sesuai metode pembayaran di atas.</p>
            <p style="margin: 30px 0 10px 0; font-style: italic;">Terima kasih atas kerjasamanya.</p>
        </div>
    </div>
`;

        showModal(document.getElementById('printInvoiceModal'));
    } else {
        showPopup('error', 'Gagal', 'Data invoice tidak ditemukan');
    }
}

function closePrintInvoiceModal() {
    hideModal(document.getElementById('printInvoiceModal'));
}

function printInvoice() {
    window.print();
}

// Tampilkan badge untuk status pembayaran
function getStatusBadge(status) {
    if (status === 'lunas') {
        return '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Lunas</span>';
    } else {
        return '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Down Payment</span>';
    }
}

function renderInvoices() {
    if (!desktopTableBody || !mobileCards) return;

    desktopTableBody.innerHTML = '';
    mobileCards.innerHTML = '';

    if (filteredInvoices.length === 0) {
        if (noDataRow) noDataRow.classList.remove('hidden');
        return;
    }

    if (noDataRow) noDataRow.classList.add('hidden');

    const startIndex = (currentPage - 1) * perPage;
    const endIndex = Math.min(startIndex + perPage, filteredInvoices.length);
    const currentPageInvoices = filteredInvoices.slice(startIndex, endIndex);

    // Render desktop table
    currentPageInvoices.forEach((invoice, index) => {
        const rowNumber = startIndex + index + 1;
        const namaPerusahaan = invoice.company_name || invoice.nama_perusahaan;
        const kontak = invoice.kontak || '-';
        const nomorOrder = invoice.invoice_no || invoice.nomor_order;
        const namaKlien = invoice.client_name || invoice.nama_klien;
        const alamat = invoice.company_address || invoice.alamat;
        const deskripsi = invoice.deskripsi || invoice.deskripsiLayanan || invoice.nama_layanan ||
            'Tidak ada deskripsi';
        const metodePembayaran = invoice.payment_method || invoice.metode_pembayaran;
        const jenisBank = invoice.jenis_bank || '-';
        const kategoriPemasukan = invoice.kategori_pemasukan ? 
            (invoice.kategori_pemasukan.charAt(0).toUpperCase() + invoice.kategori_pemasukan.slice(1)) : '-';
        const feeMaintenance = invoice.fee_maintenance ? formatCurrency(invoice.fee_maintenance) : 'Rp 0';
        const tanggal = formatTanggal(invoice.invoice_date || invoice.tanggal);
        const subtotal = invoice.subtotal || 0;
        const taxAmount = invoice.tax || 0;
        const total = invoice.total || 0;
        const taxPercentage = invoice.tax_percentage || (subtotal > 0 ? ((taxAmount / subtotal) * 100) : 0);
        const namaLayanan = invoice.nama_layanan || '';
        const statusPembayaran = invoice.status_pembayaran || 'down payment';
        const keteranganTambahan = invoice.keterangan_tambahan || '-';

        const row = document.createElement('tr');
        row.innerHTML = `
<td>${rowNumber}</td>
<td>${tanggal}</td>
<td>${namaPerusahaan}</td>
<td>${kontak}</td>
<td>${nomorOrder}</td>
<td>${namaKlien}</td>
<td>${namaLayanan}</td>
<td class="max-w-xs truncate">${alamat}</td>
<td class="max-w-xs truncate" title="${deskripsi}">${deskripsi}</td>
<td>${formatCurrency(subtotal)}</td>
<td>${taxPercentage.toFixed(2)}%</td>
<td>${formatCurrency(total)}</td>
<td>${metodePembayaran}</td>
<td>${jenisBank}</td>
<td><span class="px-2 py-1 rounded-full text-xs font-semibold" style="background-color: ${kategoriPemasukan === 'Layanan' ? '#dbeafe' : kategoriPemasukan === 'Produk' ? '#dcfce7' : '#fef3c7'}; color: ${kategoriPemasukan === 'Layanan' ? '#1e40af' : kategoriPemasukan === 'Produk' ? '#15803d' : '#92400e'}">${kategoriPemasukan}</span></td>
<td>${feeMaintenance}</td>
<td>${getStatusBadge(statusPembayaran)}</td>
<td class="max-w-xs truncate" title="${keteranganTambahan}">${keteranganTambahan}</td>
<td class="text-center">
    <div class="flex items-center justify-center gap-2">
        <button onclick="showDetailModal(${invoice.id})" class="text-purple-500 hover:text-purple-700 p-1 rounded-full hover:bg-purple-50" title="Detail">
            <span class="material-icons-outlined text-sm">visibility</span>
        </button>
        <button onclick="editInvoice(${invoice.id})" class="text-blue-500 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50" title="Edit">
            <span class="material-icons-outlined text-sm">edit</span>
        </button>
        <button onclick="deleteInvoice(${invoice.id})" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50" title="Hapus">
            <span class="material-icons-outlined text-sm">delete</span>
        </button>
        <button onclick="printInvoiceModal(${invoice.id})" class="text-green-500 hover:text-green-700 p-1 rounded-full hover:bg-green-50" title="Print">
            <span class="material-icons-outlined text-sm">print</span>
        </button>
    </div>
</td>
`;
        desktopTableBody.appendChild(row);
    });

    // Render mobile cards
    currentPageInvoices.forEach((invoice, index) => {
        const rowNumber = startIndex + index + 1;
        const namaPerusahaan = invoice.company_name || invoice.nama_perusahaan;
        const nomorOrder = invoice.invoice_no || invoice.nomor_order;
        const namaKlien = invoice.client_name || invoice.nama_klien;
        const tanggal = formatTanggal(invoice.invoice_date || invoice.tanggal);
        const subtotal = invoice.subtotal || 0;
        const taxAmount = invoice.tax || 0;
        const total = invoice.total || 0;
        const taxPercentage = invoice.tax_percentage || (subtotal > 0 ? ((taxAmount / subtotal) * 100) : 0);
        const namaLayanan = invoice.nama_layanan || '';
        const statusPembayaran = invoice.status_pembayaran || 'down payment';
        const deskripsi = invoice.deskripsi || invoice.deskripsiLayanan || invoice.nama_layanan ||
            'Tidak ada deskripsi';
        const jenisBank = invoice.jenis_bank || '-';
        const kategoriPemasukan = invoice.kategori_pemasukan ? 
            (invoice.kategori_pemasukan.charAt(0).toUpperCase() + invoice.kategori_pemasukan.slice(1)) : '-';
        const feeMaintenance = invoice.fee_maintenance ? formatCurrency(invoice.fee_maintenance) : 'Rp 0';
        const keteranganTambahan = invoice.keterangan_tambahan || '-';

        const card = document.createElement('div');
        card.className = 'bg-white border rounded-lg p-4 shadow';
        card.innerHTML = `
<div class="flex justify-between items-start mb-2">
    <div>
        <h4 class="font-semibold">${namaPerusahaan}</h4>
        <p class="text-sm text-gray-500">${nomorOrder}</p>
    </div>
    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">#${rowNumber}</span>
</div>
<p class="text-sm mb-1"><span class="font-medium">Klien:</span> ${namaKlien}</p>
<p class="text-sm mb-1"><span class="font-medium">Layanan:</span> ${namaLayanan}</p>
<p class="text-sm mb-1"><span class="font-medium">Deskripsi:</span> ${deskripsi}</p>
<p class="text-sm mb-1"><span class="font-medium">Tanggal:</span> ${tanggal}</p>
<p class="text-sm mb-1"><span class="font-medium">Jenis Bank:</span> ${jenisBank}</p>
<p class="text-sm mb-1"><span class="font-medium">Kategori:</span> <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background-color: ${kategoriPemasukan === 'Layanan' ? '#dbeafe' : kategoriPemasukan === 'Produk' ? '#dcfce7' : '#fef3c7'}; color: ${kategoriPemasukan === 'Layanan' ? '#1e40af' : kategoriPemasukan === 'Produk' ? '#15803d' : '#92400e'}">${kategoriPemasukan}</span></p>
<p class="text-sm mb-1"><span class="font-medium">Fee Maintenance:</span> ${feeMaintenance}</p>
<p class="text-sm mb-1"><span class="font-medium">Keterangan:</span> <span class="text-xs">${keteranganTambahan}</span></p>
<p class="text-sm mb-1"><span class="font-medium">Status:</span> ${getStatusBadge(statusPembayaran)}</p>
<p class="text-sm mb-1"><span class="font-medium">Subtotal:</span> ${formatCurrency(subtotal)}</p>
<p class="text-sm mb-1"><span class="font-medium">Pajak (${taxPercentage.toFixed(2)}%):</span> ${formatCurrency(taxAmount)}</p>
<p class="text-sm mb-2"><span class="font-medium">Total:</span> <b>${formatCurrency(total)}</b></p>
<div class="flex justify-between mt-3">
    <button onclick="showDetailModal(${invoice.id})" class="text-purple-500 hover:text-purple-700 p-1 rounded-full hover:bg-purple-50" title="Detail">
        <span class="material-icons-outlined text-sm">visibility</span>
    </button>
    <button onclick="editInvoice(${invoice.id})" class="text-blue-500 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50" title="Edit">
        <span class="material-icons-outlined text-sm">edit</span>
    </button>
    <button onclick="deleteInvoice(${invoice.id})" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50" title="Hapus">
        <span class="material-icons-outlined text-sm">delete</span>
    </button>
    <button onclick="printInvoiceModal(${invoice.id})" class="text-green-500 hover:text-green-700 p-1 rounded-full hover:bg-green-50" title="Print">
        <span class="material-icons-outlined text-sm">print</span>
    </button>
</div>
`;
        mobileCards.appendChild(card);
    });
}

function filterInvoices() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    
    // Filter berdasarkan pencarian
    filteredInvoices = allInvoices.filter(invoice => {
        const searchableFields = [
            invoice.company_name || invoice.nama_perusahaan || '',
            invoice.invoice_no || invoice.nomor_order || '',
            invoice.client_name || invoice.nama_klien || '',
            invoice.deskripsi || invoice.deskripsiLayanan || '',
            invoice.nama_layanan || '',
            invoice.company_address || invoice.alamat || ''
        ];
        
        return searchableFields.some(field => 
            field.toLowerCase().includes(searchTerm)
        );
    });

    // Filter berdasarkan status pembayaran
    const selectedStatus = [];
    if (!document.getElementById('filterAllStatus')?.checked) {
        if (document.getElementById('filterPembayaranAwal')?.checked) {
            selectedStatus.push('down payment');
        }
        if (document.getElementById('filterLunas')?.checked) {
            selectedStatus.push('lunas');
        }
    }

    if (selectedStatus.length > 0) {
        filteredInvoices = filteredInvoices.filter(invoice => {
            const status = invoice.status_pembayaran || '';
            return selectedStatus.includes(status);
        });
    }

    currentPage = 1;
    renderInvoices();
    updateTotalCount();
    renderPagination();
}

function updateTotalCount() {
    if (totalCount) {
        totalCount.textContent = filteredInvoices.length;
    }
}

function renderPagination() {
    if (!pageNumbers) return;

    pageNumbers.innerHTML = '';
    const totalPages = Math.ceil(filteredInvoices.length / perPage);

    if (prevPageBtn) {
        prevPageBtn.disabled = currentPage === 1;
    }

    if (nextPageBtn) {
        nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
    }

    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = `desktop-page-btn ${i === currentPage ? 'active' : ''}`;
        pageBtn.textContent = i;
        pageBtn.addEventListener('click', () => goToPage(i));
        pageNumbers.appendChild(pageBtn);
    }
}

function goToPage(page) {
    currentPage = page;
    renderInvoices();
    renderPagination();
}

function goToPrevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderInvoices();
        renderPagination();
    }
}

function goToNextPage() {
    const totalPages = Math.ceil(filteredInvoices.length / perPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderInvoices();
        renderPagination();
    }
}

function showLoading(show) {
    if (loadingRow) {
        loadingRow.style.display = show ? '' : 'none';
    }
}

// ==================== HELPER FUNCTIONS ====================
function formatTanggal(isoString) {
    if (!isoString) return '-';

    const date = new Date(isoString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}

function showPopup(type, title, message) {
    const popup = document.getElementById('minimalPopup');
    if (!popup) return;

    const titleElement = popup.querySelector('.minimal-popup-title');
    const messageElement = popup.querySelector('.minimal-popup-message');
    const iconElement = popup.querySelector('.minimal-popup-icon');

    if (titleElement) titleElement.textContent = title;
    if (messageElement) messageElement.textContent = message;

    popup.className = 'minimal-popup show';
    popup.classList.add(type);

    if (iconElement) {
        iconElement.innerHTML = '';
        const icon = document.createElement('span');
        icon.className = 'material-icons-outlined';

        if (type === 'success') {
            icon.textContent = 'check_circle';
            popup.style.borderLeftColor = '#10b981';
        } else if (type === 'error') {
            icon.textContent = 'error';
            popup.style.borderLeftColor = '#ef4444';
        } else if (type === 'warning') {
            icon.textContent = 'warning';
            popup.style.borderLeftColor = '#f59e0b';
        } else {
            icon.textContent = 'info';
            popup.style.borderLeftColor = '#3b82f6';
        }

        iconElement.appendChild(icon);
    }

    setTimeout(() => {
        popup.classList.remove('show');
    }, 3000);

    const closeBtn = popup.querySelector('.minimal-popup-close');
    if (closeBtn) {
        closeBtn.onclick = () => {
            popup.classList.remove('show');
        };
    }
}

function clearValidationErrors(formType = 'create') {
    const prefix = formType === 'create' ? '' : 'edit_';
    const form = formType === 'create' ? buatInvoiceForm : editInvoiceForm;

    if (!form) return;

    const inputs = form.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.classList.remove('error-input');
    });

    const errorElements = form.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
    });
}

function showValidationErrorsFromServer(errors, formType = 'create') {
    const prefix = formType === 'create' ? '' : 'edit_';

    for (const field in errors) {
        const input = document.getElementById(`${prefix}${field}`);
        const errorElement = document.getElementById(`${prefix}${field}_error`);

        if (input) {
            input.classList.add('error-input');
        }

        if (errorElement) {
            errorElement.textContent = errors[field][0];
        }
    }
}
</script>
</body>

</html>

