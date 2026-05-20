<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Laporan Keuangan Owner</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "hsl(210, 100%, 50%)",
                        "background-light": "#ffffff",
                        "background-dark": "#121212",
                        "card-light": "#f8fafc",
                        "card-dark": "#1e293b",
                        "text-light": "#1e293b",
                        "text-dark": "#f1f5f9",
                        "border-light": "#e2e8f0",
                        "border-dark": "#334155",
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: "Poppins", sans-serif
        }

        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem
        }
        
        /* Custom styles for pagination */
        .pagination-btn {
            transition: all 0.2s ease;
        }
        .pagination-btn:hover:not(.active):not(:disabled) {
            background-color: #f1f5f9;
            transform: translateY(-1px);
        }
        .pagination-btn.active {
            background-color: #3b82f6;
            color: white;
        }
        
        /* Toggle Button Styles */
        .toggle-btn {
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
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

        .section-card {
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 24px -18px rgba(15, 23, 42, 0.3);
        }

        .table-shell {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .filter-dropdown {
            width: min(320px, 92vw);
            padding: 0.75rem;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.25rem;
            color: #334155;
        }

        .filter-option input[type="checkbox"] {
            accent-color: #3b82f6;
        }

        .filter-option label {
            font-size: 0.875rem;
            cursor: pointer;
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
            padding-top: 0.75rem;
            margin-top: 0.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .filter-apply,
        .filter-reset {
            flex: 1;
            border-radius: 0.5rem;
            padding: 0.45rem 0.65rem;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .filter-apply {
            background: #3b82f6;
            color: white;
        }

        .filter-reset {
            background: #f1f5f9;
            color: #475569;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        @include('pemilik/template/header')
        <main class="mt-6 sm:mt-8">
            <section class="section-card bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-slate-800 dark:to-slate-700 p-4 sm:p-6 rounded-xl mb-6 sm:mb-8">
                <p class="text-xs sm:text-sm font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">Owner Dashboard</p>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">Laporan Keuangan</h1>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-2">Pantau ringkasan pemasukan, pengeluaran, dan detail transaksi dalam satu tampilan.</p>
            </section>

            <!-- Financial Cards Section -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
                <div class="section-card bg-white dark:bg-card-dark p-4 sm:p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Total Pemasukan</p>
                            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-green-600 dark:text-green-500 mt-1">Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                            <i class='bx bx-trending-up text-2xl text-green-600 dark:text-green-500'></i>
                        </div>
                    </div>
                </div>
                <div class="section-card bg-white dark:bg-card-dark p-4 sm:p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Total Pengeluaran</p>
                            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-red-600 dark:text-red-500 mt-1">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full">
                            <i class='bx bx-trending-down text-2xl text-red-600 dark:text-red-500'></i>
                        </div>
                    </div>
                </div>
                <div class="section-card bg-white dark:bg-card-dark p-4 sm:p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Total Keuntungan</p>
                            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-600 dark:text-blue-500 mt-1">Rp {{ number_format($totalKeuntungan ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                            <i class='bx bx-wallet text-2xl text-blue-600 dark:text-blue-500'></i>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Controls & Recent Transactions -->
            <section class="section-card bg-white dark:bg-card-dark p-4 sm:p-6 rounded-xl shadow-md mb-6 sm:mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Kontrol Laporan</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Filter transaksi berdasarkan kategori dan unduh laporan PDF.</p>
                    </div>
                    <button id="exportPdfBtn"
                        class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg font-medium text-sm sm:text-base hover:bg-blue-600 transition-colors w-full sm:w-auto flex items-center justify-center">
                        <i class='bx bx-download mr-2'></i>
                        Export PDF
                    </button>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="relative w-full sm:w-auto">
                        <button id="filterBtn" class="w-full bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 pl-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-offset-2 focus:ring-offset-background-light dark:focus:ring-offset-background-dark focus:ring-primary text-sm sm:text-base flex items-center justify-between gap-2">
                            <span><i class='bx bx-filter text-lg'></i> Filter Kategori</span>
                            <i class='bx bx-chevron-down text-lg'></i>
                        </button>
                        <div id="filterDropdown" class="filter-dropdown absolute top-full right-0 mt-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-50" style="display: none;">
                            <!-- Filter options will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                @if(count($pemasukan ?? []) > 0)
                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Transaksi Terbaru</h3>
                        <div class="table-shell overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr class="border-b border-gray-300 dark:border-gray-600">
                                        <th class="text-left py-2.5 px-3 font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                                        <th class="text-left py-2.5 px-3 font-semibold text-gray-700 dark:text-gray-300">Kategori</th>
                                        <th class="text-left py-2.5 px-3 font-semibold text-gray-700 dark:text-gray-300">Deskripsi</th>
                                        <th class="text-right py-2.5 px-3 font-semibold text-gray-700 dark:text-gray-300">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pemasukan->take(5) as $transaksi)
                                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                            <td class="py-3 px-3 text-gray-700 dark:text-gray-300">
                                                {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-3 py-1 rounded-full text-xs font-medium">
                                                    {{ $transaksi->kategori }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-gray-700 dark:text-gray-300">{{ $transaksi->nama }}</td>
                                            <td class="py-3 px-3 text-right font-bold text-green-600 dark:text-green-400">
                                                Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="mt-6 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-5 text-sm text-gray-500 dark:text-gray-400">
                        Belum ada transaksi terbaru untuk ditampilkan.
                    </div>
                @endif
            </section>
            
            <!-- Data Keuangan Section -->
            <section class="section-card bg-white dark:bg-card-dark p-4 sm:p-6 md:p-8 rounded-xl shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Data Keuangan</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cari dan filter transaksi untuk melihat detail laporan.</p>
                    </div>
                </div>

                <!-- Toggle Buttons and Search Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="flex flex-wrap gap-2">
                        <button id="toggleAll" class="toggle-btn active px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class='bx bx-layer text-lg'></i>
                            <span>Semua</span>
                        </button>
                        <button id="toggleIncome" class="toggle-btn income px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class='bx bx-trending-up text-lg'></i>
                            <span>Pemasukan</span>
                        </button>
                        <button id="toggleExpense" class="toggle-btn expense px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class='bx bx-trending-down text-lg'></i>
                            <span>Pengeluaran</span>
                        </button>
                    </div>

                    <div class="w-full md:w-auto relative">
                        <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg'></i>
                        <input id="financeSearch" type="text" placeholder="Cari data keuangan..."
                            class="w-full py-2 pl-10 pr-4 text-sm rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:ring-primary focus:border-primary" />
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-shell overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">No</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Kategori</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Deskripsi</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Jumlah</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Tipe</th>
                            </tr>
                        </thead>
                        <tbody id="financeTableBody">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mt-6">
                    <p id="financeInfo" class="text-sm text-gray-600 dark:text-gray-400">Menampilkan 0 data</p>
                    <div id="financePagination" class="flex flex-wrap gap-1">
                        <!-- Pagination buttons akan diisi oleh JavaScript -->
                    </div>
                </div>
            </section>
        </main>
        <footer class="mt-8 sm:mt-12 bg-gray-100 dark:bg-gray-800 py-4 sm:py-6 rounded-xl">
            <p class="text-center text-gray-600 dark:text-gray-400 text-xs sm:text-sm">Copyright �2025 by digicity.id</p>
        </footer>
    </div>

    <script>
        // Finance Data dari Controller
        const allFinanceData = @json($financeData ?? []);
        const allKategori = @json($allKategori ?? []);
        
        // Pagination variables
        let currentPage = 1;
        const itemsPerPage = 5;
        let filteredData = [...allFinanceData];
        let activeType = 'all'; // 'all', 'pemasukan', 'pengeluaran'
        let activeFilters = new Set(['all']); // Filter kategori
        
        // Function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        }

        // Function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // Toggle buttons
        function initializeToggleButtons() {
            document.getElementById('toggleAll').addEventListener('click', function() {
                setActiveType('all');
            });
            document.getElementById('toggleIncome').addEventListener('click', function() {
                setActiveType('pemasukan');
            });
            document.getElementById('toggleExpense').addEventListener('click', function() {
                setActiveType('pengeluaran');
            });
        }

        function setActiveType(type) {
            activeType = type;
            currentPage = 1;
            updateToggleButtons();
            applyFilters();
        }

        function updateToggleButtons() {
            document.getElementById('toggleAll').classList.remove('active');
            document.getElementById('toggleIncome').classList.remove('active');
            document.getElementById('toggleExpense').classList.remove('active');

            if (activeType === 'all') {
                document.getElementById('toggleAll').classList.add('active');
            } else if (activeType === 'pemasukan') {
                document.getElementById('toggleIncome').classList.add('active');
            } else if (activeType === 'pengeluaran') {
                document.getElementById('toggleExpense').classList.add('active');
            }
        }

        // Apply filters
        function applyFilters() {
            const searchInput = document.getElementById('financeSearch');
            const searchTerm = (searchInput?.value || '').toLowerCase().trim();
            
            filteredData = allFinanceData.filter(item => {
                let typeMatch = true;
                if (activeType === 'pemasukan') {
                    typeMatch = item.tipe_transaksi === 'pemasukan';
                } else if (activeType === 'pengeluaran') {
                    typeMatch = item.tipe_transaksi === 'pengeluaran';
                }

                // Filter kategori
                let categoryMatch = true;
                if (!activeFilters.has('all')) {
                    categoryMatch = activeFilters.has(item.kategori);
                }

                const kategori = (item.kategori || '').toLowerCase();
                const deskripsi = (item.deskripsi || item.nama || '').toLowerCase();
                const searchMatch = !searchTerm || 
                    kategori.includes(searchTerm) ||
                    deskripsi.includes(searchTerm);

                return typeMatch && categoryMatch && searchMatch;
            });

            currentPage = 1;
            renderTable();
            renderPagination();
        }

        // Render table
        function renderTable() {
            const tableBody = document.getElementById('financeTableBody');
            tableBody.innerHTML = '';

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);

            if (filteredData.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-500">Tidak ada data</td></tr>';
                document.getElementById('financeInfo').textContent = 'Menampilkan 0 data';
                return;
            }

            for (let i = startIndex; i < endIndex; i++) {
                const item = filteredData[i];
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700';

                const amountColor = item.tipe_transaksi === 'pemasukan' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                const typeColor = item.tipe_transaksi === 'pemasukan'
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                const typeLabel = item.tipe_transaksi === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';

                row.innerHTML = `
                    <td class="py-3 px-4 text-gray-800 dark:text-gray-200">${i + 1}</td>
                    <td class="py-3 px-4 text-gray-800 dark:text-gray-200">${formatDate(item.tanggal_transaksi)}</td>
                    <td class="py-3 px-4">
                        <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-3 py-1 rounded-full text-xs font-medium">
                            ${item.kategori}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-800 dark:text-gray-200">${item.deskripsi || item.nama || '-'}</td>
                    <td class="py-3 px-4 text-right font-bold ${amountColor}">
                        Rp ${formatCurrency(item.jumlah)}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium ${typeColor}">
                            ${typeLabel}
                        </span>
                    </td>
                `;

                tableBody.appendChild(row);
            }

            // Update info
            document.getElementById('financeInfo').textContent = `Menampilkan ${startIndex + 1}-${endIndex} dari ${filteredData.length} data`;
        }

        // Render pagination
        function renderPagination() {
            const pagination = document.getElementById('financePagination');
            pagination.innerHTML = '';

            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (totalPages <= 1) return;

            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn px-3 py-1 rounded-md border border-gray-300 text-sm';
            prevBtn.innerHTML = '<i class="bx bx-chevron-left"></i>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => changePage(currentPage - 1));
            pagination.appendChild(prevBtn);

            // Page numbers
            for (let i = 1; i <= Math.min(totalPages, 5); i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `pagination-btn px-3 py-1 rounded-md border text-sm ${i === currentPage ? 'active bg-blue-500 text-white border-blue-500' : 'border-gray-300'}`;
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', () => changePage(i));
                pagination.appendChild(pageBtn);
            }

            if (totalPages > 5) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 py-1 text-gray-500';
                ellipsis.textContent = '...';
                pagination.appendChild(ellipsis);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn px-3 py-1 rounded-md border border-gray-300 text-sm';
            nextBtn.innerHTML = '<i class="bx bx-chevron-right"></i>';
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            nextBtn.addEventListener('click', () => changePage(currentPage + 1));
            pagination.appendChild(nextBtn);
        }

        function changePage(page) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
            renderPagination();
        }

        // Initialize filter dropdown
        function initializeFilter() {
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const filterContainer = filterDropdown;

            // Buat daftar kategori unik untuk filter
            const uniqueCategories = [...new Set(allKategori.map(k => k.nama_kategori).filter(Boolean))];
            let filterHTML = `
                <div class="filter-option">
                    <input type="checkbox" id="filterAll" value="all" checked>
                    <label for="filterAll">Semua Kategori</label>
                </div>
            `;
            uniqueCategories.forEach(cat => {
                const isChecked = activeFilters.has(cat) ? 'checked' : '';
                filterHTML += `
                    <div class="filter-option">
                        <input type="checkbox" id="filter${cat.replace(/\s+/g, '')}" value="${cat}" ${isChecked}>
                        <label for="filter${cat.replace(/\s+/g, '')}">${cat}</label>
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
                filterDropdown.style.display = filterDropdown.style.display === 'none' ? 'block' : 'none';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function () {
                filterDropdown.style.display = 'none';
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
                filterDropdown.style.display = 'none';
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
                filterDropdown.style.display = 'none';
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeToggleButtons();
            initializeFilter();
            document.getElementById('financeSearch').addEventListener('input', applyFilters);
            renderTable();
            renderPagination();

            const exportBtn = document.getElementById('exportPdfBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function () {
                    const params = new URLSearchParams();

                    if (activeType && activeType !== 'all') {
                        params.set('type', activeType);
                    }

                    const searchTerm = document.getElementById('financeSearch').value.trim();
                    if (searchTerm) {
                        params.set('search', searchTerm);
                    }

                    if (!activeFilters.has('all')) {
                        const kategori = Array.from(activeFilters).filter(v => v !== 'all');
                        if (kategori.length > 0) {
                            params.set('kategori', kategori.join(','));
                        }
                    }

                    const url = `/owner/laporan/pdf${params.toString() ? '?' + params.toString() : ''}`;
                    window.open(url, '_blank');
                });
            }
        });
    </script>
</body>

</html>

