<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Template Surat Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
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
        
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }

        .material-symbols-outlined.filled {
            font-variation-settings:
                'FILL' 1,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
        
        /* Card hover effects */
        .template-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .template-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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

        /* Category Badge Styles */
        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .category-marketing {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }
        
        .category-development {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .category-hr {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        
        .category-finance {
            background-color: rgba(139, 92, 246, 0.15);
            color: #5b21b6;
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
        
        /* Template preview modal */
        .template-preview-container {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            height: 100%;
            overflow-y: auto;
        }
        
        .template-preview-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
        }
        
        .template-preview-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('admin.templet.sider')

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Template Surat</h2>
                
                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari judul atau kategori..." type="text" />
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative">
                            <button id="filterBtn" class="px-4 py-2 bg-white border border-border-light text-text-muted-light rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">filter_list</span>
                                Filter
                            </button>
                            <div id="filterDropdown" class="filter-dropdown">
                                <div class="filter-option">
                                    <input type="checkbox" id="filterAll" value="all" checked>
                                    <label for="filterAll">Semua Kategori</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterMarketing" value="marketing">
                                    <label for="filterMarketing">Digital Marketing</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterDevelopment" value="development">
                                    <label for="filterDevelopment">Web Development</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterHR" value="hr">
                                    <label for="filterHR">HR</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterFinance" value="finance">
                                    <label for="filterFinance">Finance</label>
                                </div>
                                <div class="filter-actions">
                                    <button id="applyFilter" class="filter-apply">Terapkan</button>
                                    <button id="resetFilter" class="filter-reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <button id="buatSuratBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Buat Surat</span>
                            <span class="sm:hidden">Buat</span>
                        </button>
                    </div>
                </div>
                
                <!-- Template Grid -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">description</span>
                            Daftar Template Surat
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount" class="font-semibold text-text-light">9</span> template</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="templateGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mb-8">
                            <!-- Templates will be inserted here by JavaScript -->
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
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Modal untuk Buat Surat Kerjasama -->
    <div id="buatSuratModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Buat Surat Kerjasama</h3>
                    <p class="text-gray-600 dark:text-gray-400">Isi form di bawah untuk membuat surat kerjasama baru</p>
                </div>
                <button onclick="closeBuatSuratModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-grow overflow-auto p-4">
                <form id="buatSuratForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan 1</label>
                            <input type="text" id="namaPerusahaan1" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan 2</label>
                            <input type="text" id="namaPerusahaan2" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Perusahaan 1</label>
                            <textarea id="alamatPerusahaan1" rows="2" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Perusahaan 2</label>
                            <textarea id="alamatPerusahaan2" rows="2" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required></textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Penanggung Jawab 1</label>
                            <input type="text" id="penanggungJawab1" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Penanggung Jawab 2</label>
                            <input type="text" id="penanggungJawab2" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan Penanggung Jawab 1</label>
                            <input type="text" id="jabatan1" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan Penanggung Jawab 2</label>
                            <input type="text" id="jabatan2" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Kerjasama</label>
                        <input type="text" id="judulKerjasama" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lingkup Kerjasama</label>
                        <textarea id="lingkupKerjasama" rows="3" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                            <input type="date" id="tanggalMulai" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
                            <input type="date" id="tanggalSelesai" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai Kontrak</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">Rp</span>
                            <input type="number" id="nilaiKontrak" class="w-full pl-10 pr-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Tambahan</label>
                        <textarea id="keterangan" rows="3" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                </form>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closeBuatSuratModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </button>
                <button onclick="submitBuatSurat()" class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-opacity-90 transition-colors">
                    Buat Surat
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Template Preview -->
    <div id="templateModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 id="modalTitle" class="text-xl font-bold text-gray-900 dark:text-white"></h3>
                    <p id="modalSubtitle" class="text-gray-600 dark:text-gray-400"></p>
                </div>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-grow overflow-auto p-4 bg-gray-100 dark:bg-gray-900">
                <div class="template-preview-container">
                    <div class="template-preview-header">
                        <h4 class="text-lg font-bold text-gray-800">SURAT PERJANJIAN KERJASAMA</h4>
                        <p class="text-sm text-gray-600">Nomor: SPK/001/VI/2023</p>
                    </div>
                    <div class="template-preview-content">
                        <p class="mb-4">Pada hari ini, Senin tanggal 1 Juni 2023, telah dibuat kesepakatan bersama antara:</p>
                        <div class="mb-4">
                            <p class="font-bold">PIHAK PERTAMA:</p>
                            <p>Nama: <span id="previewNama1">[Nama Perusahaan 1]</span></p>
                            <p>Alamat: <span id="previewAlamat1">[Alamat Perusahaan 1]</span></p>
                            <p>Penanggung Jawab: <span id="previewPenanggung1">[Nama Penanggung Jawab 1]</span></p>
                            <p>Jabatan: <span id="previewJabatan1">[Jabatan 1]</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="font-bold">PIHAK KEDUA:</p>
                            <p>Nama: <span id="previewNama2">[Nama Perusahaan 2]</span></p>
                            <p>Alamat: <span id="previewAlamat2">[Alamat Perusahaan 2]</span></p>
                            <p>Penanggung Jawab: <span id="previewPenanggung2">[Nama Penanggung Jawab 2]</span></p>
                            <p>Jabatan: <span id="previewJabatan2">[Jabatan 2]</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="font-bold">JUDUL KERJASAMA:</p>
                            <p id="previewJudul">[Judul Kerjasama]</p>
                        </div>
                        <div class="mb-4">
                            <p class="font-bold">LINGKUP KERJASAMA:</p>
                            <p id="previewLingkup">[Lingkup Kerjasama]</p>
                        </div>
                        <div class="mb-4">
                            <p class="font-bold">MASA BERLAKU:</p>
                            <p>Dari tanggal <span id="previewMulai">[Tanggal Mulai]</span> hingga <span id="previewSelesai">[Tanggal Selesai]</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="font-bold">NILAI KONTRAK:</p>
                            <p>Rp <span id="previewNilai">[Nilai Kontrak]</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="font-bold">KETERANGAN TAMBAHAN:</p>
                            <p id="previewKeterangan">[Keterangan Tambahan]</p>
                        </div>
                        <div class="grid grid-cols-2 gap-8 mt-8">
                            <div>
                                <p class="text-center mb-4">PIHAK PERTAMA</p>
                                <div class="border-t border-gray-800 pt-4">
                                    <p id="previewTtd1" class="text-center">[Nama Penanggung Jawab 1]</p>
                                    <p id="previewTtdJabatan1" class="text-center">[Jabatan 1]</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-center mb-4">PIHAK KEDUA</p>
                                <div class="border-t border-gray-800 pt-4">
                                    <p id="previewTtd2" class="text-center">[Nama Penanggung Jawab 2]</p>
                                    <p id="previewTtdJabatan2" class="text-center">[Jabatan 2]</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="downloadTemplate()" class="flex items-center bg-primary text-white font-medium py-2 px-4 rounded-lg hover:bg-opacity-90 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download
                </button>
                <button onclick="editTemplate()" class="flex items-center bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </button>
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
        // Inisialisasi variabel untuk pagination, filter, dan search
        let currentPage = 1;
        const itemsPerPage = 8;
        let activeFilters = ['all'];
        let searchTerm = '';
        
        // Template data
        const templates = [
            { id: 1, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 2, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 3, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 4, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 5, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 6, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 7, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 8, title: 'Surat Perjanjian Kerjasama', category: 'marketing', categoryName: 'Digital Marketing' },
            { id: 9, title: 'Surat Perjanjian Kerjasama', category: 'development', categoryName: 'Web Development' }
        ];
        
        // Inisialisasi pagination, filter, dan search
        initializePagination();
        initializeFilter();
        initializeSearch();
        renderTemplates();

        // === PAGINATION ===
        function initializePagination() {
            renderPagination();
            updateVisibleItems();
        }
        
        function renderPagination() {
            const visibleTemplates = getFilteredTemplates();
            const totalPages = Math.ceil(visibleTemplates.length / itemsPerPage);
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
        }
        
        function getFilteredTemplates() {
            return templates.filter(template => !template.hidden);
        }
        
        function updateVisibleItems() {
            const visibleTemplates = getFilteredTemplates();
            
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            
            // Hide all templates first
            templates.forEach(template => template.hidden = true);
            
            // Show only the templates for current page
            visibleTemplates.forEach((template, index) => {
                if (index >= startIndex && index < endIndex) {
                    template.hidden = false;
                }
            });
            
            // Re-render templates
            renderTemplates();
            
            // Update total count
            document.getElementById('totalCount').textContent = visibleTemplates.length;
        }
        
        // === FILTER ===
        function initializeFilter() {
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const applyFilterBtn = document.getElementById('applyFilter');
            const resetFilterBtn = document.getElementById('resetFilter');
            const filterAll = document.getElementById('filterAll');
            
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
            
            // Apply filter
            applyFilterBtn.addEventListener('click', function() {
                const filterAll = document.getElementById('filterAll');
                const filterMarketing = document.getElementById('filterMarketing');
                const filterDevelopment = document.getElementById('filterDevelopment');
                const filterHR = document.getElementById('filterHR');
                const filterFinance = document.getElementById('filterFinance');
                
                activeFilters = [];
                if (filterAll.checked) {
                    activeFilters.push('all');
                } else {
                    if (filterMarketing.checked) activeFilters.push('marketing');
                    if (filterDevelopment.checked) activeFilters.push('development');
                    if (filterHR.checked) activeFilters.push('hr');
                    if (filterFinance.checked) activeFilters.push('finance');
                }
                
                applyFilters();
                filterDropdown.classList.remove('show');
                const visibleCount = getFilteredTemplates().length;
                showMinimalPopup('Filter Diterapkan', `Menampilkan ${visibleCount} template`, 'success');
            });
            
            // Reset filter
            resetFilterBtn.addEventListener('click', function() {
                document.getElementById('filterAll').checked = true;
                document.getElementById('filterMarketing').checked = false;
                document.getElementById('filterDevelopment').checked = false;
                document.getElementById('filterHR').checked = false;
                document.getElementById('filterFinance').checked = false;
                activeFilters = ['all'];
                applyFilters();
                filterDropdown.classList.remove('show');
                const visibleCount = getFilteredTemplates().length;
                showMinimalPopup('Filter Direset', 'Menampilkan semua template', 'success');
            });
        }
        
        function applyFilters() {
            // Reset to first page
            currentPage = 1;
            
            // Apply filters to templates
            templates.forEach(template => {
                // Check if category matches filter
                let categoryMatches = false;
                if (activeFilters.includes('all')) {
                    categoryMatches = true;
                } else {
                    categoryMatches = activeFilters.some(filter => template.category.includes(filter));
                }
                
                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = template.title.toLowerCase().includes(searchLower) || 
                                   template.categoryName.toLowerCase().includes(searchLower);
                }
                
                template.hidden = !(categoryMatches && searchMatches);
            });
            
            // Update pagination and visible items
            renderPagination();
            updateVisibleItems();
        }
        
        // === SEARCH ===
        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchTerm = searchInput.value.trim();
                    applyFilters();
                }, 300); // Debounce search
            });
        }

        // === RENDER TEMPLATES ===
        function renderTemplates() {
            const grid = document.getElementById('templateGrid');
            grid.innerHTML = '';
            
            // Filter templates based on current page and filters
            const visibleTemplates = templates.filter(template => !template.hidden);
            
            // Render templates
            visibleTemplates.forEach(template => {
                const templateElement = document.createElement('div');
                templateElement.className = `template-card bg-background-light dark:bg-surface-dark rounded-lg p-3 sm:p-4 shadow-md`;
                
                // Determine category badge class
                let categoryClass = '';
                if (template.category === 'marketing') {
                    categoryClass = 'category-marketing';
                } else if (template.category === 'development') {
                    categoryClass = 'category-development';
                } else if (template.category === 'hr') {
                    categoryClass = 'category-hr';
                } else if (template.category === 'finance') {
                    categoryClass = 'category-finance';
                }
                
                templateElement.innerHTML = `
                    <div class="bg-gray-200 dark:bg-gray-700 w-full aspect-[3/4] mb-3 sm:mb-4 cursor-pointer template-preview"
                         onclick="openModal('${template.title}', '${template.categoryName}')">
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-6xl text-subtle-light dark:text-subtle-dark">description</span>
                        </div>
                    </div>
                    <div class="text-center py-2">
                        <h2 class="font-bold text-text-light dark:text-text-dark text-sm sm:text-base">${template.title}</h2>
                        <p class="text-text-light dark:text-text-dark text-xs sm:text-sm">
                            <span class="category-badge ${categoryClass}">${template.categoryName}</span>
                        </p>
                    </div>
                `;
                
                grid.appendChild(templateElement);
            });
        }

        // === MODAL FUNCTIONS ===
        function openModal(title, subtitle) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalSubtitle').textContent = subtitle;
            document.getElementById('templateModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('templateModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function downloadTemplate() {
            // Simulate download
            showMinimalPopup('Download Berhasil', 'Template berhasil diunduh', 'success');
            closeModal();
        }

        function editTemplate() {
            // Simulate edit
            closeModal();
            document.getElementById('buatSuratModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Modal functions for Buat Surat Kerjasama
        function closeBuatSuratModal() {
            document.getElementById('buatSuratModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('buatSuratForm').reset();
        }

        function submitBuatSurat() {
            const form = document.getElementById('buatSuratForm');
            
            // Simple form validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Get form data
            const formData = {
                namaPerusahaan1: document.getElementById('namaPerusahaan1').value,
                namaPerusahaan2: document.getElementById('namaPerusahaan2').value,
                alamatPerusahaan1: document.getElementById('alamatPerusahaan1').value,
                alamatPerusahaan2: document.getElementById('alamatPerusahaan2').value,
                penanggungJawab1: document.getElementById('penanggungJawab1').value,
                penanggungJawab2: document.getElementById('penanggungJawab2').value,
                jabatan1: document.getElementById('jabatan1').value,
                jabatan2: document.getElementById('jabatan2').value,
                judulKerjasama: document.getElementById('judulKerjasama').value,
                lingkupKerjasama: document.getElementById('lingkupKerjasama').value,
                tanggalMulai: document.getElementById('tanggalMulai').value,
                tanggalSelesai: document.getElementById('tanggalSelesai').value,
                nilaiKontrak: document.getElementById('nilaiKontrak').value,
                keterangan: document.getElementById('keterangan').value
            };
            
            // Update preview with form data
            document.getElementById('previewNama1').textContent = formData.namaPerusahaan1;
            document.getElementById('previewAlamat1').textContent = formData.alamatPerusahaan1;
            document.getElementById('previewPenanggung1').textContent = formData.penanggungJawab1;
            document.getElementById('previewJabatan1').textContent = formData.jabatan1;
            
            document.getElementById('previewNama2').textContent = formData.namaPerusahaan2;
            document.getElementById('previewAlamat2').textContent = formData.alamatPerusahaan2;
            document.getElementById('previewPenanggung2').textContent = formData.penanggungJawab2;
            document.getElementById('previewJabatan2').textContent = formData.jabatan2;
            
            document.getElementById('previewJudul').textContent = formData.judulKerjasama;
            document.getElementById('previewLingkup').textContent = formData.lingkupKerjasama;
            document.getElementById('previewMulai').textContent = formData.tanggalMulai;
            document.getElementById('previewSelesai').textContent = formData.tanggalSelesai;
            document.getElementById('previewNilai').textContent = Number(formData.nilaiKontrak).toLocaleString('id-ID');
            document.getElementById('previewKeterangan').textContent = formData.keterangan;
            
            document.getElementById('previewTtd1').textContent = formData.penanggungJawab1;
            document.getElementById('previewTtdJabatan1').textContent = formData.jabatan1;
            document.getElementById('previewTtd2').textContent = formData.penanggungJawab2;
            document.getElementById('previewTtdJabatan2').textContent = formData.jabatan2;
            
            // Show success message
            showMinimalPopup('Surat Dibuat', 'Surat kerjasama berhasil dibuat', 'success');
            
            // Close modal
            closeBuatSuratModal();
            
            // Open preview modal
            openModal('Surat Perjanjian Kerjasama', 'Preview');
        }

        // === EVENT LISTENERS ===
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener for tombol Buat Surat Kerjasama
            const buatSuratBtn = document.getElementById('buatSuratBtn');
            if (buatSuratBtn) {
                buatSuratBtn.addEventListener('click', function() {
                    document.getElementById('buatSuratModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }
            
            // Close modal when clicking outside
            document.getElementById('buatSuratModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeBuatSuratModal();
                }
            });

            document.getElementById('templateModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (!document.getElementById('buatSuratModal').classList.contains('hidden')) {
                        closeBuatSuratModal();
                    }
                    if (!document.getElementById('templateModal').classList.contains('hidden')) {
                        closeModal();
                    }
                }
            });
        });

        // === MINIMALIST POPUP ===
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
        
        // Close popup when clicking the close button
        document.querySelector('.minimal-popup-close').addEventListener('click', function() {
            document.getElementById('minimalPopup').classList.remove('show');
        });
    </script>
</body>
</html>