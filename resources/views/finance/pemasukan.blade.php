<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Keuangan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
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
                        "danger": "#ef4444",
                        "income": "#10b981",
                        "expense": "#ef4444"
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
        /* ... (Style CSS Tetap Sama) ... */
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 1,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }

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

        .finance-table {
            transition: all 0.2s ease;
        }

        .finance-table tr:hover {
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

        .toggle-btn {
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .toggle-btn:hover {
            background-color: #e2e8f0;
        }

        .toggle-btn.active {
            background-color: #3b82f6;
            color: white;
        }

        .toggle-btn.income.active {
            background-color: #10b981;
        }

        .toggle-btn.expense.active {
            background-color: #ef4444;
        }

        .modal {
            transition: opacity 0.25s ease;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .type-income {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .type-expense {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
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

        .hidden-by-filter {
            display: none !important;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-card-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .stat-card-value {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-card-change {
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-card-change.positive {
            color: #10b981;
        }

        .stat-card-change.negative {
            color: #ef4444;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Container untuk sidebar yang akan dimuat -->
        @include('finance.templet.sider')

        <!-- Main Content -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-1 p-3 sm:p-8">
                <header class="mb-4 sm:mb-8">
                    <h1 class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-white">Data Keuangan</h1>
                </header>

                <!-- Stat Cards (Dinamis dari Controller) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <h3 class="stat-card-title">Total Pemasukan</h3>
                            <div class="stat-card-icon bg-green-100">
                                <span class="material-icons-outlined text-green-600">trending_up</span>
                            </div>
                        </div>
                        <div class="stat-card-value text-green-600" id="stat-income">Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-card-change positive">
                            <span class="material-icons-outlined text-sm">arrow_upward</span>
                            <span id="stat-income-change">0% dari bulan lalu</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <h3 class="stat-card-title">Total Pengeluaran</h3>
                            <div class="stat-card-icon bg-red-100">
                                <span class="material-icons-outlined text-red-600">trending_down</span>
                            </div>
                        </div>
                        <div class="stat-card-value text-red-600" id="stat-expense">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-card-change negative">
                            <span class="material-icons-outlined text-sm">arrow_upward</span>
                            <span id="stat-expense-change">0% dari bulan lalu</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <h3 class="stat-card-title">Saldo Bersih</h3>
                            <div class="stat-card-icon bg-blue-100">
                                <span class="material-icons-outlined text-blue-600">account_balance</span>
                            </div>
                        </div>
                        <div class="stat-card-value text-blue-600" id="stat-balance">Rp {{ number_format($totalKeuangan ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-card-change positive">
                            <span class="material-icons-outlined text-sm">arrow_upward</span>
                            <span id="stat-balance-change">0% margin keuntungan</span>
                        </div>
                    </div>
                </div>

                <!-- Toggle Buttons and Search Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="flex flex-wrap gap-2">
                        <button id="toggleAll" class="toggle-btn active px-4 py-2 rounded-lg flex items-center gap-2">
                            <span class="material-icons-outlined">all_inclusive</span>
                            <span>Semua</span>
                        </button>
                        <button id="toggleIncome"
                            class="toggle-btn income px-4 py-2 rounded-lg flex items-center gap-2">
                            <span class="material-icons-outlined">arrow_downward</span>
                            <span>Pemasukan</span>
                        </button>
                        <button id="toggleExpense"
                            class="toggle-btn expense px-4 py-2 rounded-lg flex items-center gap-2">
                            <span class="material-icons-outlined">arrow_upward</span>
                            <span>Pengeluaran</span>
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-1/3">
                            <span
                                class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input id="finance-search"
                                class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                                placeholder="Cari nama, kategori, atau deskripsi..." type="text" />
                        </div>
                        <div class="relative">
                            <button id="filterBtn"
                                class="px-4 py-2 bg-white border border-border-light text-text-muted-light rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">filter_list</span>
                                Filter
                            </button>
                            <div id="filterDropdown" class="filter-dropdown">
                                <!-- Filter options akan diisi oleh JavaScript -->
                            </div>
                        </div>
                        <button onclick="openAddModal()"
                            class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Tambah Transaksi</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>
                </div>

                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">account_balance_wallet</span>
                            Data Keuangan
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount"
                                    class="font-semibold text-text-light">0</span> transaksi</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- SCROLLABLE TABLE -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No Transaksi</th>
                                            <th style="min-width: 150px;">Tanggal</th>
                                            <th style="min-width: 200px;">Nama</th>
                                            <th style="min-width: 150px;">Kategori</th>
                                            <th style="min-width: 300px;">Deskripsi</th>
                                            <th style="min-width: 150px;">Jumlah</th>
                                            <th style="min-width: 120px;">Tipe</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="finance-table-body">
                                        <!-- Data akan diisi dengan JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4" id="mobile-cards">
                            <!-- Card akan diisi dengan JavaScript -->
                        </div>

                        <!-- Pagination -->
                        <div id="finance-pagination" class="desktop-pagination">
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
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Modal Tambah Data Keuangan -->
    <div id="addModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Data Keuangan</h3>
                    <button onclick="closeAddModal()" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <!-- Form Action diarahkan ke route yang benar -->
                <form id="addModalForm" action="{{ route('finance.cashflow.store') }}" method="POST" class="space-y-4">
                    @csrf <!-- Token Keamanan Laravel -->

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" name="tanggal"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                            <select name="tipe" id="transaction-type"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                onchange="updateCategoryOptions()" required>
                                <option value="">Pilih Tipe</option>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="kategori_id" id="transaction-category"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                                <option value="">Pilih Tipe Transaksi Terlebih Dahulu</option>
                            </select>
                            <!-- Subkategori khusus untuk tipe Pengeluaran -->
                            <select name="subkategori" id="transaction-subcategory"
                                class="w-full px-3 py-2 bg-gray-50 border border-border-light rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary mt-2 hidden">
                                <option value="">Pilih Subkategori</option>
                            </select>
                        </div>    

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" name="nama"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Nama Transaksi" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="text" id="transaction-amount-display" inputmode="numeric"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Contoh: Rp 1.500.000" required>
                            <input type="hidden" id="transaction-amount" name="jumlah" value="0">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary"
                                rows="3" placeholder="Deskripsi Transaksi"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button id="addModalSaveBtn" type="submit" class="px-4 py-2 btn-primary rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div id="transactionDetailModal"
        class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Detail Transaksi</h3>
                    <button onclick="closeTransactionDetailModal()" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>

                <!-- Transaction Detail Content -->
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Informasi
                                Transaksi</h5>
                            <div class="space-y-2">
                                <div class="flex flex-col sm:flex-row">
                                    <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Nomor Transaksi:</span>
                                    <span id="detail-no" class="text-xs sm:text-sm text-text-light font-medium"></span>
                                </div>
                                <div class="flex flex-col sm:flex-row">
                                    <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Tanggal Transaksi:</span>
                                    <span id="detail-date"
                                        class="text-xs sm:text-sm text-text-light font-medium"></span>
                                </div>
                                <div class="flex flex-col sm:flex-row">
                                    <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Tipe Transaksi:</span>
                                    <span id="detail-type"
                                        class="text-xs sm:text-sm text-text-light font-medium"></span>
                                </div>
                                <div class="flex flex-col sm:flex-row">
                                    <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Kategori Transaksi:</span>
                                    <span id="detail-category"
                                        class="text-xs sm:text-sm text-text-light font-medium"></span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Detail
                                Pembayaran</h5>
                            <div class="space-y-2">
                                <div class="flex flex-col sm:flex-row">
                                    <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Nama:</span>
                                    <span id="detail-name"
                                        class="text-xs sm:text-sm text-text-light font-medium"></span>
                                </div>
                                <div class="flex flex-col sm:flex-row">
                                    <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Jumlah:</span>
                                    <span id="detail-amount"
                                        class="text-xs sm:text-sm text-text-light font-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-6">
                        <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Deskripsi</h5>
                        <div class="bg-white rounded-lg p-3 sm:p-4">
                            <p id="detail-description" class="text-xs sm:text-sm text-text-light"></p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex flex-col sm:flex-row justify-center sm:justify-end gap-2 sm:gap-3 mt-4 sm:mt-6">
                    <button onclick="printTransaction()"
                        class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                        <span class="material-icons-outlined">print</span>
                        <span>Cetak</span>
                    </button>
                    <button onclick="downloadTransaction()"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center gap-2">
                        <span class="material-icons-outlined">download</span>
                        <span>Download</span>
                    </button>
                    <button onclick="closeTransactionDetailModal()"
                        class="px-4 py-2 btn-secondary rounded-lg flex items-center gap-2">
                        <span class="material-icons-outlined">close</span>
                        <span>Tutup</span>
                    </button>
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

    <script>
        // 1. DATA DARI CONTROLLER
        // Data keuangan dan kategori diambil dari variable yang dikirim Controller
        const allFinanceData = @json($financeData ?? []);
        const allKategori = @json($allKategori ?? []);
        const serverTotalPemasukan = @json($totalPemasukan ?? 0);
        const serverTotalCashflowPengeluaran = @json($totalCashflowPengeluaran ?? 0);
        const serverTotalPayrollExpenses = @json($totalPayrollExpenses ?? 0);
        const serverTotalPengeluaran = @json($totalPengeluaran ?? 0);
        const serverTotalKeuangan = @json($totalKeuangan ?? 0);

        // Pagination variables
        let financeCurrentPage = 1;
        const financeItemsPerPage = 5;
        let financeFilteredData = [...allFinanceData];
        let activeFilters = new Set(['all']); // Gunakan Set untuk memudahkan pengecekan
        let activeType = 'all'; // 'all', 'pemasukan', or 'pengeluaran'
        let searchTerm = '';

        // Inisialisasi toggle buttons
        function initializeToggleButtons() {
            const toggleAll = document.getElementById('toggleAll');
            const toggleIncome = document.getElementById('toggleIncome');
            const toggleExpense = document.getElementById('toggleExpense');

            toggleAll.addEventListener('click', function () {
                setActiveType('all');
            });

            toggleIncome.addEventListener('click', function () {
                setActiveType('pemasukan');
            });

            toggleExpense.addEventListener('click', function () {
                setActiveType('pengeluaran');
            });
        }

        function setActiveType(type) {
            activeType = type;
            updateToggleButtons();
            applyFilters();
        }

        function updateToggleButtons() {
            const toggleAll = document.getElementById('toggleAll');
            const toggleIncome = document.getElementById('toggleIncome');
            const toggleExpense = document.getElementById('toggleExpense');

            // Reset all buttons
            toggleAll.classList.remove('active');
            toggleIncome.classList.remove('active');
            toggleExpense.classList.remove('active');

            // Set active button
            if (activeType === 'all') {
                toggleAll.classList.add('active');
            } else if (activeType === 'pemasukan') {
                toggleIncome.classList.add('active');
            } else if (activeType === 'pengeluaran') {
                toggleExpense.classList.add('active');
            }
        }

        // Inisialisasi filter
        function initializeFilter() {
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const filterContainer = document.getElementById('filterDropdown');

            // Buat daftar kategori unik dari allKategori yang dikirim dari controller
            let uniqueCategories = [];
            if (allKategori && allKategori.length > 0) {
                uniqueCategories = allKategori.map(k => k.nama_kategori);
            } else {
                // Fallback: extract dari financeData jika allKategori kosong
                uniqueCategories = [...new Set(allFinanceData.map(item => item.kategori))];
            }
            
            console.log('Categories:', uniqueCategories); // Debug log
            
            let filterHTML = `
                <div class="filter-option">
                    <input type="checkbox" id="filterAll" value="all" ${activeFilters.has('all') ? 'checked' : ''}>
                    <label for="filterAll">Semua Kategori</label>
                </div>
            `;
            uniqueCategories.forEach(cat => {
                const safeId = 'filter' + cat.replace(/[^a-zA-Z0-9]/g, '');
                const isChecked = activeFilters.has(cat) ? 'checked' : '';
                filterHTML += `
                    <div class="filter-option">
                        <input type="checkbox" id="${safeId}" value="${cat}" ${isChecked}>
                        <label for="${safeId}">${cat}</label>
                    </div>
                `;
            });
            filterHTML += `
                <div class="filter-actions">
                    <button id="applyFilter" class="filter-apply">Terapkan</button>
                    <button id="resetFilter" class="filter-reset">Reset</button>
                </div>
            `;
            filterContainer.innerHTML = filterHTML;


            // Toggle filter dropdown
            filterBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                filterDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function () {
                filterDropdown.classList.remove('show');
            });

            // Prevent dropdown from closing when clicking inside
            filterDropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            // Apply filter
            document.getElementById('applyFilter').addEventListener('click', function () {
                activeFilters.clear();
                const checkboxes = filterContainer.querySelectorAll('input[type="checkbox"]:checked');
                checkboxes.forEach(cb => {
                    activeFilters.add(cb.value);
                });
                applyFilters();
                filterDropdown.classList.remove('show');
                const visibleCount = getFilteredRows().length;
                showMinimalPopup('Filter Diterapkan', `Menampilkan ${visibleCount} transaksi`, 'success');
            });

            // Reset filter
            document.getElementById('resetFilter').addEventListener('click', function () {
                activeFilters.clear();
                activeFilters.add('all');
                document.getElementById('filterAll').checked = true;
                filterContainer.querySelectorAll('input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                    cb.checked = false;
                });
                applyFilters();
                filterDropdown.classList.remove('show');
                const visibleCount = getFilteredRows().length;
                showMinimalPopup('Filter Direset', 'Menampilkan semua transaksi', 'success');
            });
        }

        function getFilteredRows() {
            return financeFilteredData;
        }

        function applyFilters() {
            // Reset to first page
            financeCurrentPage = 1;

            // Apply filters
            financeFilteredData = allFinanceData.filter(item => {
                // Check if type matches filter
                let typeMatches = false;
                if (activeType === 'all') {
                    typeMatches = true;
                } else {
                    typeMatches = item.tipe_transaksi === activeType;
                }

                // Check if category matches filter
                let categoryMatches = false;
                if (activeFilters.has('all')) {
                    categoryMatches = true;
                } else {
                    categoryMatches = activeFilters.has(item.kategori);
                }

                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = item.nama_transaksi.toLowerCase().includes(searchLower) ||
                        (item.deskripsi && item.deskripsi.toLowerCase().includes(searchLower)) ||
                        item.kategori.toLowerCase().includes(searchLower) ||
                        item.tipe_transaksi.toLowerCase().includes(searchLower) ||
                        item.nomor_transaksi.toLowerCase().includes(searchLower);
                }

                return typeMatches && categoryMatches && searchMatches;
            });

            // Update pagination and visible items
            renderFinanceTable();
            renderFinancePagination();
            updateStatCards();
        }

        // Update category options based on selected type (dipanggil saat form dibuka dan tipe berubah)
        function updateCategoryOptions() {
            const typeSelect = document.getElementById('transaction-type');
            const categorySelect = document.getElementById('transaction-category');
            const subcategorySelect = document.getElementById('transaction-subcategory');
            const selectedType = typeSelect.value;

            // Reset category and subcategory
            categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';
            // Reset to default name and required state
            categorySelect.name = 'kategori_id';
            categorySelect.setAttribute('required', 'required');

            if (subcategorySelect) {
                subcategorySelect.innerHTML = '<option value="">Pilih Subkategori</option>';
                subcategorySelect.classList.add('hidden');
                subcategorySelect.removeAttribute('required');
                subcategorySelect.name = 'subkategori';
            }

            if (selectedType) {
                if (selectedType === 'expense') {
                    // Populate main groups for Pengeluaran
                    const groups = [
                        { key: 'biaya_operasional', label: 'Biaya Operasional' },
                        { key: 'biaya_karyawan', label: 'Biaya Karyawan' },
                        { key: 'biaya_pemasaran', label: 'Biaya Pemasaran dan Teknologi' },
                        { key: 'biaya_proyek', label: 'Biaya Proyek & Variabel' },
                        { key: 'biaya_administrasi', label: 'Biaya Administrasi & Legal' },
                        { key: 'giving', label: 'Giving' }
                    ];

                    // Change the select 'name' so kategori_id is NOT submitted for expense
                    categorySelect.name = 'kategori_group';
                    categorySelect.removeAttribute('required');

                    groups.forEach(g => {
                        const option = document.createElement('option');
                        option.value = g.key;
                        option.textContent = g.label;
                        categorySelect.appendChild(option);
                    });

                    // Attach handler to populate subcategories when a group is selected
                    categorySelect.onchange = function () {
                        populateSubcategoriesForGroup(categorySelect.value);
                    };

                    // Make subcategory required and visible
                    if (subcategorySelect) {
                        subcategorySelect.classList.remove('hidden');
                        subcategorySelect.setAttribute('required', 'required');
                        subcategorySelect.name = 'subkategori';
                    }
                } else {
                    // income: gunakan kategori dari database seperti sebelumnya
                    const tipeDatabase = selectedType === 'income' ? 'pemasukan' : 'pengeluaran';
                    const filteredKategori = allKategori.filter(k => k.tipe_kategori === tipeDatabase);

                    filteredKategori.forEach(kategori => {
                        const option = document.createElement('option');
                        option.value = kategori.id; // Simpan ID kategori
                        option.textContent = kategori.nama_kategori; // Tampilkan nama kategori
                        categorySelect.appendChild(option);
                    });

                    categorySelect.onchange = null;

                    // ensure subcategory is hidden and not required
                    if (subcategorySelect) {
                        subcategorySelect.classList.add('hidden');
                        subcategorySelect.removeAttribute('required');
                        subcategorySelect.name = 'subkategori';
                    }
                }
            } else {
                categorySelect.onchange = null;
            }
        }

        // Populate subcategories for each expense group
        function populateSubcategoriesForGroup(groupKey) {
            const subcategorySelect = document.getElementById('transaction-subcategory');
            if (!subcategorySelect) return;
            const mapping = {
                'biaya_operasional': ['Sewa kantor', 'Listrik', 'Internet', 'Air', 'Biaya perawatan'],
                'biaya_karyawan': ['Gaji', 'Bonus', 'Tunjangan', 'Asuransi', 'Biaya pelatihan'],
                'biaya_pemasaran': ['Iklan', 'Langganan Software/Tools', 'Infrastuktur IT'],
                'biaya_proyek': ['Biaya Proyek & Variabel'],
                'biaya_administrasi': ['Biaya Administrasi & Legal'],
                'giving': ['Zakat', 'Infaq', 'Sedekah', 'Wakaf']
            };

            subcategorySelect.innerHTML = '<option value="">Pilih Subkategori</option>';
            if (groupKey && mapping[groupKey]) {
                mapping[groupKey].forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;
                    subcategorySelect.appendChild(option);
                });
                subcategorySelect.classList.remove('hidden');
            } else {
                subcategorySelect.classList.add('hidden');
            }
        }

        function formatRupiah(value) {
            const numeric = String(value ?? '').replace(/\D/g, '');
            if (!numeric) return 'Rp 0';
            return 'Rp ' + Number(numeric).toLocaleString('id-ID');
        }

        function parseRupiah(value) {
            const numeric = String(value ?? '').replace(/\D/g, '');
            return numeric ? Number(numeric) : 0;
        }

        function syncTransactionAmount(triggerValidation = false) {
            const displayInput = document.getElementById('transaction-amount-display');
            const hiddenInput = document.getElementById('transaction-amount');
            if (!displayInput || !hiddenInput) return;

            const numericValue = parseRupiah(displayInput.value);
            hiddenInput.value = numericValue;
            displayInput.value = formatRupiah(numericValue);

            if (triggerValidation && numericValue <= 0) {
                displayInput.setCustomValidity('Jumlah harus lebih dari 0');
            } else {
                displayInput.setCustomValidity('');
            }
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Reset form saat modal dibuka
            const form = document.querySelector('#addModal form');
            form.reset();
            const category = document.getElementById('transaction-category');
            category.innerHTML = '<option value="">Pilih Tipe Transaksi Terlebih Dahulu</option>';
            category.name = 'kategori_id';
            category.setAttribute('required', 'required');

            const subcat = document.getElementById('transaction-subcategory');
            if (subcat) {
                subcat.innerHTML = '<option value="">Pilih Subkategori</option>';
                subcat.classList.add('hidden');
                subcat.removeAttribute('required');
                subcat.name = 'subkategori';
            }

            const amountDisplay = document.getElementById('transaction-amount-display');
            const amountHidden = document.getElementById('transaction-amount');
            if (amountHidden) amountHidden.value = '0';
            if (amountDisplay) {
                amountDisplay.value = 'Rp 0';
                amountDisplay.setCustomValidity('');
            }
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Transaction detail modal functions
        function openTransactionDetailModal(transactionId) {
            const transaction = allFinanceData.find(t => t.id == transactionId);
            if (!transaction) {
                showMinimalPopup('Error', 'Data transaksi tidak tersedia', 'error');
                return;
            }

            // Fill modal with data
            document.getElementById('detail-no').textContent = transaction.nomor_transaksi;
            document.getElementById('detail-date').textContent = transaction.tanggal_transaksi;
            document.getElementById('detail-name').textContent = transaction.nama_transaksi;
            document.getElementById('detail-amount').textContent = 'Rp ' + parseFloat(transaction.jumlah).toLocaleString('id-ID');
            document.getElementById('detail-description').textContent = transaction.deskripsi || 'Tidak ada deskripsi';

            // Determine type badge
            let typeBadge = '';
            let amountClass = '';
            if (transaction.tipe_transaksi === 'pemasukan') {
                typeBadge = '<span class="type-badge type-income">Pemasukan</span>';
            } else {
                typeBadge = '<span class="type-badge type-expense">Pengeluaran</span>';
            }
            document.getElementById('detail-type').innerHTML = typeBadge;

            // Category badge
            const categoryBadge = `<span class="category-badge">${transaction.kategori}</span>`;
            document.getElementById('detail-category').innerHTML = categoryBadge;

            // Show modal
            document.getElementById('transactionDetailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTransactionDetailModal() {
            document.getElementById('transactionDetailModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function printTransaction() {
            window.print();
        }

        function downloadTransaction() {
            showMinimalPopup('Info', 'Fitur download akan segera tersedia', 'warning');
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const addModal = document.getElementById('addModal');
            const transactionDetailModal = document.getElementById('transactionDetailModal');

            if (event.target == addModal) {
                closeAddModal();
            }
            if (event.target == transactionDetailModal) {
                closeTransactionDetailModal();
            }
        }

        // Handle escape key to close modals
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddModal();
                closeTransactionDetailModal();
            }
        });

        // Finance table functions
        function renderFinanceTable() {
            const tableBody = document.getElementById('finance-table-body');
            const mobileCards = document.getElementById('mobile-cards');
            tableBody.innerHTML = '';
            mobileCards.innerHTML = '';

            const startIndex = (financeCurrentPage - 1) * financeItemsPerPage;
            const endIndex = Math.min(startIndex + financeItemsPerPage, financeFilteredData.length);

            for (let i = startIndex; i < endIndex; i++) {
                const item = financeFilteredData[i];
                const amount = parseFloat(item.jumlah);
                const amountClass = item.tipe_transaksi === 'pemasukan' ? 'text-green-600' : 'text-red-600';
                const typeBadge = item.tipe_transaksi === 'pemasukan' ?
                    '<span class="type-badge type-income">Pemasukan</span>' :
                    '<span class="type-badge type-expense">Pengeluaran</span>';
                const categoryBadge = `<span class="category-badge">${item.kategori}</span>`;

                // Create table row for desktop
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="min-width: 60px;">${item.nomor_transaksi}</td>
                    <td style="min-width: 150px;">${item.tanggal_transaksi}</td>
                    <td style="min-width: 200px;">${item.nama_transaksi}</td>
                    <td style="min-width: 150px;">${categoryBadge}</td>
                    <td style="min-width: 300px;">${item.deskripsi || '-'}</td>
                    <td style="min-width: 150px;" class="${amountClass} font-semibold">Rp ${amount.toLocaleString('id-ID')}</td>
                    <td style="min-width: 120px;">${typeBadge}</td>
                    <td style="min-width: 120px; text-align: center;">
                        <div class="flex justify-center gap-2">
                            <button onclick="openTransactionDetailModal(${item.id})" class="p-1 rounded-full hover:bg-primary/20 text-gray-700" title="Lihat Detail">
                                <span class="material-icons-outlined">description</span>
                            </button>
                            <button onclick="openEditModal(${item.id})" class="p-1 rounded-full hover:bg-blue-100 text-blue-600" title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </button>
                            <button onclick="deleteTransaction(${item.id})" class="p-1 rounded-full hover:bg-red-100 text-red-600" title="Hapus">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);

                // Create card for mobile
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg border border-border-light p-4 shadow-sm finance-card';
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                <span class="material-icons-outlined text-primary">${item.tipe_transaksi === 'pemasukan' ? 'arrow_downward' : 'arrow_upward'}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-base">${item.nama_transaksi}</h4>
                                <p class="text-sm text-text-muted-light">${item.tanggal_transaksi}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openTransactionDetailModal(${item.id})" class="p-1 rounded-full hover:bg-primary/20 text-gray-700" title="Lihat Detail">
                                <span class="material-icons-outlined">description</span>
                            </button>
                            <button onclick="openEditModal(${item.id})" class="p-1 rounded-full hover:bg-blue-100 text-blue-600" title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </button>
                            <button onclick="deleteTransaction(${item.id})" class="p-1 rounded-full hover:bg-red-100 text-red-600" title="Hapus">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-text-muted-light">No</p>
                            <p class="font-medium">${item.id}</p>
                        </div>
                        <div>
                            <p class="text-text-muted-light">Tipe</p>
                            <p>${typeBadge}</p>
                        </div>
                        <div>
                            <p class="text-text-muted-light">Kategori</p>
                            <p>${categoryBadge}</p>
                        </div>
                        <div>
                            <p class="text-text-muted-light">Jumlah</p>
                            <p class="font-medium ${amountClass}">Rp ${amount.toLocaleString('id-ID')}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-text-muted-light">Deskripsi</p>
                            <p class="font-medium text-xs">${item.deskripsi || '-'}</p>
                        </div>
                    </div>
                `;
                mobileCards.appendChild(card);
            }

            // Update info
            document.getElementById('totalCount').textContent = financeFilteredData.length;
        }

        function renderFinancePagination() {
            const pagination = document.getElementById('finance-pagination');
            const pageNumbers = document.getElementById('pageNumbers');
            const prevButton = document.getElementById('prevPage');
            const nextButton = document.getElementById('nextPage');

            pageNumbers.innerHTML = '';
            const totalPages = Math.ceil(financeFilteredData.length / financeItemsPerPage);

            for (let i = 1; i <= totalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `desktop-page-btn ${i === financeCurrentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToPage(i));
                pageNumbers.appendChild(pageNumber);
            }

            prevButton.disabled = financeCurrentPage === 1;
            nextButton.disabled = financeCurrentPage === totalPages || totalPages === 0;
            prevButton.onclick = () => goToPage(financeCurrentPage - 1);
            nextButton.onclick = () => goToPage(financeCurrentPage + 1);
        }

        function goToPage(page) {
            financeCurrentPage = page;
            renderFinanceTable();
            renderFinancePagination();
            const scrollableTable = document.getElementById('scrollableTable');
            if (scrollableTable) {
                scrollableTable.scrollLeft = 0;
            }
        }

        function updateStatCards() {
            const useServerTotals = activeType === 'all' && !searchTerm && activeFilters.has('all') && activeFilters.size === 1;

            let totalIncome = useServerTotals ? Number(serverTotalPemasukan) : 0;
            let totalExpense = useServerTotals ? Number(serverTotalPengeluaran) : 0;

            if (!useServerTotals) {
                (financeFilteredData || []).forEach(item => {
                    const amount = parseFloat(item.jumlah);
                    if (item.tipe_transaksi === 'pemasukan') {
                        totalIncome += amount;
                    } else {
                        totalExpense += amount;
                    }
                });
            }

            const netBalance = totalIncome - totalExpense;
            const balancePercentage = totalIncome > 0 ? (netBalance / totalIncome) * 100 : 0;

            // Update stat cards values
            document.getElementById('stat-income').textContent = 'Rp ' + totalIncome.toLocaleString('id-ID');
            document.getElementById('stat-expense').textContent = 'Rp ' + totalExpense.toLocaleString('id-ID');
            document.getElementById('stat-balance').textContent = 'Rp ' + netBalance.toLocaleString('id-ID');

            if (useServerTotals) {
                document.getElementById('stat-income-change').textContent = `Total pemasukan cashflow: Rp ${Number(serverTotalPemasukan).toLocaleString('id-ID')}`;
                document.getElementById('stat-expense-change').textContent = `Pengeluaran cashflow: Rp ${Number(serverTotalCashflowPengeluaran).toLocaleString('id-ID')} + payroll: Rp ${Number(serverTotalPayrollExpenses).toLocaleString('id-ID')}`;
                document.getElementById('stat-balance-change').textContent = `Saldo bersih yang sama dengan beranda: Rp ${Number(serverTotalKeuangan).toLocaleString('id-ID')}`;
            } else {
                document.getElementById('stat-income-change').textContent = `Dari ${financeFilteredData.filter(i => i.tipe_transaksi === 'pemasukan').length} transaksi`;
                document.getElementById('stat-expense-change').textContent = `Dari ${financeFilteredData.filter(i => i.tipe_transaksi === 'pengeluaran').length} transaksi`;
                document.getElementById('stat-balance-change').textContent = `${balancePercentage >= 0 ? '+' : ''}${balancePercentage.toFixed(1)}% margin keuntungan`;
            }
        }

        function initializeStatCardsFromServer() {
            updateStatCards();
        }

        function filterFinance() {
            searchTerm = document.getElementById('finance-search').value.trim();
            applyFilters();
        }

        // Minimalist Popup
        function showMinimalPopup(title, message, type = 'success') {
            const popup = document.getElementById('minimalPopup');
            const popupTitle = popup.querySelector('.minimal-popup-title');
            const popupMessage = popup.querySelector('.minimal-popup-message');
            const popupIcon = popup.querySelector('.minimal-popup-icon span');

            popupTitle.textContent = title;
            popupMessage.textContent = message;
            popup.className = 'minimal-popup show ' + type;

            if (type === 'success') {
                popupIcon.textContent = 'check';
            } else if (type === 'error') {
                popupIcon.textContent = 'error';
            } else if (type === 'warning') {
                popupIcon.textContent = 'warning';
            }

            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        // Close popup when clicking the close button
        document.querySelector('.minimal-popup-close').addEventListener('click', function () {
            document.getElementById('minimalPopup').classList.remove('show');
        });

        // Initialize everything on page load
        document.addEventListener('DOMContentLoaded', function () {
            renderFinanceTable();
            renderFinancePagination();
            initializeFilter();
            initializeToggleButtons();
            initializeStatCardsFromServer();
            updateStatCards();
            document.getElementById('finance-search').addEventListener('input', filterFinance);

            const amountDisplay = document.getElementById('transaction-amount-display');
            const addForm = document.getElementById('addModalForm');

            if (amountDisplay) {
                amountDisplay.addEventListener('input', () => syncTransactionAmount(false));
                amountDisplay.addEventListener('blur', () => syncTransactionAmount(true));
                amountDisplay.value = 'Rp 0';
            }

            if (addForm) {
                addForm.addEventListener('submit', function () {
                    syncTransactionAmount(true);
                });
            }
        });

        // --- EDIT / DELETE HANDLERS (JS only) ---
        const destroyUrlTemplate = "{{ route('finance.cashflow.destroy', ['id' => 'REPLACE_ID']) }}";
        const updateUrlTemplate = "{{ route('finance.cashflow.update', ['id' => 'REPLACE_ID']) }}";

        function openEditModal(id) {
            const transaction = allFinanceData.find(t => t.id == id);
            if (!transaction) return showMinimalPopup('Gagal', 'Data transaksi tidak ditemukan', 'error');

            // Open modal and prefill fields
            openAddModal();
            const form = document.getElementById('addModalForm');
            const saveBtn = document.getElementById('addModalSaveBtn');

            // If hidden _method not exists, create it
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            // Set form action to update URL
            form.action = updateUrlTemplate.replace('REPLACE_ID', id);

            // Prefill common fields
            form.querySelector('input[name="tanggal"]').value = transaction.tanggal_transaksi;

            // Tipe may use different naming; try to set value directly, else map
            const tipeSelect = form.querySelector('select[name="tipe"]') || document.getElementById('transaction-type');
            if (tipeSelect) {
                // Try direct set
                if ([...tipeSelect.options].some(o => o.value == transaction.tipe_transaksi)) {
                    tipeSelect.value = transaction.tipe_transaksi;
                } else if ([...tipeSelect.options].some(o => o.value == 'income') && transaction.tipe_transaksi.toLowerCase().includes('pemas')) {
                    tipeSelect.value = 'income';
                } else if ([...tipeSelect.options].some(o => o.value == 'expense') && transaction.tipe_transaksi.toLowerCase().includes('pengel')) {
                    tipeSelect.value = 'expense';
                }
            }

            // Ensure category options are populated based on tipe
            if (typeof updateCategoryOptions === 'function') updateCategoryOptions();

            // Try to select category by matching option text
            const categorySelect = document.getElementById('transaction-category');
            if (categorySelect) {
                let matched = false;
                for (const opt of categorySelect.options) {
                    if (opt.textContent.trim() === (transaction.kategori || '').trim()) {
                        categorySelect.value = opt.value;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    // if not matched, leave as is
                }
            }

            // name, jumlah, deskripsi
            form.querySelector('input[name="nama"]').value = transaction.nama_transaksi || '';
            const amountDisplay = document.getElementById('transaction-amount-display');
            const amountHidden = document.getElementById('transaction-amount');
            const amountValue = parseFloat(transaction.jumlah) || 0;
            if (amountHidden) amountHidden.value = amountValue;
            if (amountDisplay) amountDisplay.value = formatRupiah(amountValue);
            form.querySelector('textarea[name="deskripsi"]').value = transaction.deskripsi || '';

            saveBtn.textContent = 'Perbarui Data';
        }

        async function deleteTransaction(id) {
            if (!confirm('Yakin ingin menghapus transaksi ini?')) return;
            const url = destroyUrlTemplate.replace('REPLACE_ID', id);
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
                });

                let data = null;
                try { data = await res.json(); } catch(e) { /* ignore parse errors */ }

                if (!res.ok) {
                    const msg = data && data.message ? data.message : 'Gagal menghapus';
                    throw new Error(msg);
                }

                if (data && data.success === false) {
                    throw new Error(data.message || 'Gagal menghapus');
                }

                // Remove from client-side array and re-render
                const idx = allFinanceData.findIndex(t => t.id == id);
                if (idx !== -1) allFinanceData.splice(idx, 1);
                applyFilters();
                showMinimalPopup('Berhasil', 'Transaksi dihapus', 'success');
            } catch (err) {
                console.error(err);
                showMinimalPopup('Gagal', err.message || 'Tidak dapat menghapus transaksi', 'error');
            }
        }
    </script>

    <!-- Script PHP untuk Menangani Pesan Sukses (Flash Session) -->
    @if(session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                showMinimalPopup('Berhasil', '{{ session('success') }}', 'success');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                showMinimalPopup('Gagal', '{{ session('error') }}', 'error');
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                showMinimalPopup('Error', '{{ $errors->first() }}', 'error');
                openAddModal(); // Reopen modal to show errors
            });
        </script>
    @endif
</body>

</html>
