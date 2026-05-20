<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Beranda Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
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

        .status-pending {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .status-overdue {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        /* Sidebar link styles */
        .sidebar-link {
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .sidebar-link.active {
            background-color: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            font-weight: 600;
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

        /* Date Filter Styles */
        .date-filter {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .date-filter select {
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .date-filter select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Chart Container Styles */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Pagination styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
            gap: 0.25rem;
        }

        .pagination-btn {
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            background-color: white;
            color: #1e293b;
        }

        .pagination-btn:hover {
            background-color: #f1f5f9;
        }

        .pagination-btn.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Main content positioning */
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

        /* Sidebar positioning */
        .sidebar-fixed {
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
        }

        /* Dark mode adjustments */
        .dark .status-paid {
            background-color: rgba(16, 185, 129, 0.25);
            color: #6ee7b7;
        }

        .dark .status-pending {
            background-color: rgba(245, 158, 11, 0.25);
            color: #fcd34d;
        }

        .dark .status-overdue {
            background-color: rgba(239, 68, 68, 0.25);
            color: #fca5a5;
        }

        /* Mobile card adjustments */
        @media (max-width: 639px) {
            .stat-card {
                padding: 0.75rem !important;
            }

            .stat-card .icon-container {
                width: 2rem !important;
                height: 2rem !important;
            }

            .stat-card .material-symbols-outlined {
                font-size: 1.25rem !important;
            }

            .stat-card .value-text {
                font-size: 0.875rem !important;
                line-height: 1.2 !important;
            }

            .stat-card .label-text {
                font-size: 0.625rem !important;
                line-height: 1 !important;
            }

            .stat-card .mr-3 {
                margin-right: 0.5rem !important;
            }

            .pagination-container {
                flex-wrap: wrap;
            }

            .chart-container {
                height: 250px;
            }
            
            /* Calendar adjustments for mobile */
            .calendar-day {
                min-height: 40px !important;
            }
            
            .calendar-day-number {
                font-size: 0.75rem !important;
            }
            
            .calendar-event {
                font-size: 0.625rem !important;
                padding: 1px 3px !important;
            }
        }
        
        /* Calendar Styles - Updated with indicators */
        .calendar-day {
            transition: all 0.2s ease;
            position: relative;
            color: #000000; /* Teks hitam untuk hari-hari */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 3rem; /* Tinggi minimum untuk memberi ruang pada indikator */
        }

        .calendar-day.has-event {
            cursor: pointer;
        }

        .calendar-day.has-event:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .calendar-day.highlighted {
            background-color: rgba(59, 130, 246, 0.2);
            font-weight: 600;
            color: #000000; /* Teks hitam untuk tanggal yang dihighlight */
        }

        .calendar-day.selected {
            background-color: rgba(59, 130, 246, 0.3);
            font-weight: 700;
            color: #000000; /* Teks hitam untuk tanggal yang dipilih */
        }
        
        /* Indicators positioned at the bottom */
        .indicators-container {
            position: absolute;
            bottom: 2px;
            display: flex;
            gap: 2px;
        }

        .event-indicator {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #3b82f6; /* Blue for meeting */
        }

        .announcement-indicator {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #f59e0b; /* Orange for announcement */
        }
        
        /* Event Modal Styles */
        .event-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .event-modal.show {
            display: flex;
        }
        
        .event-modal-content {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .event-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .event-modal-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }
        
        .event-modal-close:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .event-modal-body {
            margin-bottom: 1rem;
        }
        
        .event-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('finance.templet.sider')

        <!-- Main Content -->
        <main class="flex-1 flex flex-col bg-background-light main-content">
            <div class="flex-1 p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Beranda</h2>

                <!-- Stat Cards + Average Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 sm:mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-text-light">Ringkasan Keuangan</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm text-text-muted-light">Periode rata-rata</span>
                        <select id="avg-period" class="text-xs sm:text-sm border border-border-light rounded-md px-2 py-1 bg-white">
                            <option value="week">Minggu</option>
                            <option value="month" selected>Bulan</option>
                            <option value="year">Tahun</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-6 sm:mb-8">
                    <div
                        class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div
                            class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-blue-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">trending_up</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Pemasukan</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="income-value">Rp
                                20.000.000</p>
                        </div>
                    </div>
                    <div
                        class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div
                            class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-red-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-symbols-outlined text-red-500">trending_down</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Pengeluaran</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="expense-value">Rp
                                10.000.000</p>
                        </div>
                    </div>
                    <div
                        class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div
                            class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-purple-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-symbols-outlined text-purple-500">account_balance_wallet</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Total Keuangan</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="total-finance">Rp
                                10.000.000</p>
                        </div>
                    </div>
                    <div
                        class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div
                            class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-green-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-500">calculate</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Rata-rata Pemasukan</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="average-value">Rp 0</p>
                            <p class="text-[10px] sm:text-xs text-text-muted-light mt-0.5" id="average-caption"></p>
                        </div>
                    </div>
                    <div
                        class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div
                            class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-orange-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-symbols-outlined text-orange-500">calculate</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Rata-rata Pengeluaran</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="average-expense-value">Rp 0</p>
                            <p class="text-[10px] sm:text-xs text-text-muted-light mt-0.5" id="average-expense-caption"></p>
                        </div>
                    </div>
                </div>

                <!-- Financial Chart Section -->
                <div class="bg-card-light rounded-DEFAULT p-3 sm:p-6 border border-border-light shadow-card mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                        <h3 class="text-lg font-semibold">Grafik Keuangan</h3>
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                            <div class="date-filter">
                                <select id="chart-filter">
                                    <option value="week">Minggu Ini</option>
                                    <option value="month" selected>Bulan Ini</option>
                                    <option value="year">Tahun Ini</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="finance-chart"></canvas>
                    </div>
                </div>

                <!-- Calendar and Meeting Notes Section - Updated with new design -->
                <section class="bg-white p-3 sm:p-6 rounded-2xl shadow-sm border border-gray-200 mt-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Calendar Section -->
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg md:text-xl font-bold text-black">Kalender</h3>
                                <div class="flex items-center space-x-2">
                                    <button id="prev-month"
                                        class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                                    </button>
                                    <span id="current-month" class="text-lg font-medium text-black"></span>
                                    <button id="next-month"
                                        class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-600 mb-2">
                                <div>Min</div>
                                <div>Sen</div>
                                <div>Sel</div>
                                <div>Rab</div>
                                <div>Kam</div>
                                <div>Jum</div>
                                <div>Sab</div>
                            </div>
                            <div id="calendar-days" class="grid grid-cols-7 gap-1">
                                <!-- Calendar days will be generated by JavaScript -->
                            </div>
                            <div class="flex justify-center mt-4 space-x-4 text-xs">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-1"></div>
                                    <span class="text-gray-600">Meeting</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-amber-500 rounded-full mr-1"></div>
                                    <span class="text-gray-600">Pengumuman</span>
                                </div>
                            </div>
                        </div>

                        <!-- Meeting Notes Section -->
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg md:text-xl font-bold text-black">Catatan Meeting</h3>
                                <button id="refresh-notes"
                                    class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                                    <span class="material-symbols-outlined text-sm">refresh</span>
                                </button>
                            </div>
                            <div id="meeting-notes-container" class="space-y-3 max-h-96 overflow-y-auto">
                                <div class="text-center py-8 text-gray-500">
                                    <span class="material-symbols-outlined text-4xl">event_note</span>
                                    <p class="mt-2">Pilih tanggal untuk melihat catatan meeting</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Announcements Section -->
                <section class="bg-white p-3 sm:p-6 rounded-2xl shadow-sm border border-gray-200 mt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg md:text-xl font-bold text-black">Pengumuman</h3>
                        <button id="refresh-announcements"
                            class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                            <span class="material-symbols-outlined text-sm">refresh</span>
                        </button>
                    </div>
                    <div id="announcements-container" class="space-y-3 max-h-96 overflow-y-auto">
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-symbols-outlined text-4xl">campaign</span>
                            <p class="mt-2">Tidak ada pengumuman</p>
                        </div>
                    </div>
                </section>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Event Modal -->
    <div id="event-modal" class="event-modal">
        <div class="event-modal-content">
            <div class="event-modal-header">
                <h3 id="event-modal-title" class="text-lg font-semibold"></h3>
                <button id="event-modal-close" class="event-modal-close">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="event-modal-body" id="event-modal-body">
                <!-- Event details will be inserted here -->
            </div>
            <div class="event-modal-footer">
                <button id="event-modal-view-details" class="btn-primary px-4 py-2 rounded-lg">
                    Lihat Detail
                </button>
                <button id="event-modal-close-btn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari PHP
        const financeDataPHP = @json($financeData ?? []);

        // Statistik dari database
        const totalPemasukan = @json($totalPemasukan ?? 0);
        const totalPengeluaran = @json($totalPengeluaran ?? 0);
        const totalKeuangan = @json($totalKeuangan ?? 0);

        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });

        // Update stat cards
        const incomeValueEl = document.getElementById('income-value');
        const expenseValueEl = document.getElementById('expense-value');
        const avgValueEl = document.getElementById('average-value');
        const avgPeriodEl = document.getElementById('avg-period');
        const avgCaptionEl = document.getElementById('average-caption');
        const avgExpenseValueEl = document.getElementById('average-expense-value');
        const avgExpensePeriodEl = document.getElementById('avg-period');
        const avgExpenseCaptionEl = document.getElementById('average-expense-caption');
        const totalFinanceEl = document.getElementById('total-finance');

        if (incomeValueEl) incomeValueEl.textContent = formatter.format(totalPemasukan);
        if (expenseValueEl) expenseValueEl.textContent = formatter.format(totalPengeluaran);
        if (totalFinanceEl) totalFinanceEl.textContent = 'Rp' + totalKeuangan.toLocaleString('id-ID');

        function computeAverage(period, transactionType) {
            const now = new Date();
            let sum = 0;
            let divisor = 1;
            let caption = '';

            if (period === 'week') {
                // Rata-rata harian dalam minggu ini (Senin - Minggu)
                const startOfWeek = new Date(now);
                const day = startOfWeek.getDay(); // 0=Min
                const diffToMonday = day === 0 ? -6 : 1 - day;
                startOfWeek.setDate(startOfWeek.getDate() + diffToMonday);
                startOfWeek.setHours(0, 0, 0, 0);
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                endOfWeek.setHours(23, 59, 59, 999);

                divisor = 7;
                caption = 'Rata-rata per hari (minggu ini)';

                financeDataPHP.forEach(item => {
                    if (item.tipe_transaksi !== transactionType) return;
                    const date = new Date(item.tanggal_transaksi);
                    if (date >= startOfWeek && date <= endOfWeek) {
                        sum += parseFloat(item.jumlah) || 0;
                    }
                });
            } else if (period === 'month') {
                const month = now.getMonth();
                const year = now.getFullYear();
                const startOfMonth = new Date(year, month, 1);
                const endOfMonth = new Date(year, month + 1, 0);
                const totalWeeks = Math.ceil((endOfMonth.getDate() - startOfMonth.getDate() + startOfMonth.getDay() + 1) / 7);
                divisor = totalWeeks;
                caption = `Rata-rata per minggu (${totalWeeks} minggu)`;

                financeDataPHP.forEach(item => {
                    if (item.tipe_transaksi !== transactionType) return;
                    const date = new Date(item.tanggal_transaksi);
                    if (date.getFullYear() === year && date.getMonth() === month) {
                        sum += parseFloat(item.jumlah) || 0;
                    }
                });
            } else {
                const year = now.getFullYear();
                divisor = 12;
                caption = 'Rata-rata per bulan (12 bulan)';

                financeDataPHP.forEach(item => {
                    if (item.tipe_transaksi !== transactionType) return;
                    const date = new Date(item.tanggal_transaksi);
                    if (date.getFullYear() === year) {
                        sum += parseFloat(item.jumlah) || 0;
                    }
                });
            }

            const avg = divisor > 0 ? sum / divisor : 0;
            return { avg, caption };
        }

        function renderAverage() {
            if (!avgValueEl || !avgPeriodEl) return;
            const period = avgPeriodEl.value;
            const result = computeAverage(period, 'pemasukan');
            avgValueEl.textContent = formatter.format(result.avg);
            if (avgCaptionEl) avgCaptionEl.textContent = result.caption;
        }

        if (avgPeriodEl) {
            avgPeriodEl.addEventListener('change', renderAverage);
        }
        renderAverage();

        function renderAverageExpense() {
            if (!avgExpenseValueEl || !avgExpensePeriodEl) return;
            const period = avgExpensePeriodEl.value;
            const result = computeAverage(period, 'pengeluaran');
            avgExpenseValueEl.textContent = formatter.format(result.avg);
            if (avgExpenseCaptionEl) avgExpenseCaptionEl.textContent = result.caption;
        }

        if (avgExpensePeriodEl) {
            avgExpensePeriodEl.addEventListener('change', renderAverageExpense);
        }
        renderAverageExpense();

        // Calendar variables
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        let selectedDate = null;
        let meetingNotes = [];
        let announcements = [];

        // CSRF Token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- VARIABEL GLOBAL ---
        let highlightedDates = [];
        let announcementDates = [];

        // --- FUNGSI API ---
        async function apiFetch(endpoint, options = {}) {
            const cacheBuster = `_t=${Date.now()}`;
            const url = `/finance/api${endpoint}${endpoint.includes('?') ? '&' : '?'}${cacheBuster}`;
            const defaultOptions = { headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': window.csrfToken } };
            const finalOptions = { ...defaultOptions, ...options };

            const response = await fetch(url, finalOptions);
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Server Error');
            }
            return await response.json();
        }

        // Function to get chart data based on filter
        function getChartData(filterType) {
            const now = new Date();
            let labels = [];
            let income = [];
            let expense = [];

            if (filterType === 'week') {
                // Minggu ini: Senin sampai Minggu
                labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                income = new Array(7).fill(0);
                expense = new Array(7).fill(0);

                // Hitung start dan end of week
                const startOfWeek = new Date(now);
                startOfWeek.setDate(now.getDate() - now.getDay() + 1); // Senin
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6); // Minggu

                financeDataPHP.forEach(item => {
                    const date = new Date(item.tanggal_transaksi);
                    if (date >= startOfWeek && date <= endOfWeek) {
                        const dayOfWeek = date.getDay(); // 0=Min, 1=Sen, ..., 6=Sab
                        const adjustedDay = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // 0=Sen, 1=Sel, ..., 6=Min
                        if (adjustedDay >= 0 && adjustedDay < 7) {
                            if (item.tipe_transaksi === 'pemasukan') {
                                income[adjustedDay] += parseFloat(item.jumlah);
                            } else if (item.tipe_transaksi === 'pengeluaran') {
                                expense[adjustedDay] += parseFloat(item.jumlah);
                            }
                        }
                    }
                });
            } else if (filterType === 'month') {
                // Bulan ini: Minggu 1 sampai 4
                const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                const totalWeeks = Math.ceil((endOfMonth.getDate() - startOfMonth.getDate() + startOfMonth.getDay() + 1) /
                    7);
                labels = Array.from({
                    length: totalWeeks
                }, (_, i) => `Minggu ${i + 1}`);
                income = new Array(totalWeeks).fill(0);
                expense = new Array(totalWeeks).fill(0);

                financeDataPHP.forEach(item => {
                    const date = new Date(item.tanggal_transaksi);
                    if (date.getFullYear() === now.getFullYear() && date.getMonth() === now.getMonth()) {
                        const weekIndex = Math.min(Math.floor((date.getDate() - 1) / 7), totalWeeks - 1);
                        if (item.tipe_transaksi === 'pemasukan') {
                            income[weekIndex] += parseFloat(item.jumlah);
                        } else if (item.tipe_transaksi === 'pengeluaran') {
                            expense[weekIndex] += parseFloat(item.jumlah);
                        }
                    }
                });
            } else if (filterType === 'year') {
                // Tahun ini: Jan sampai Des
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                income = new Array(12).fill(0);
                expense = new Array(12).fill(0);

                financeDataPHP.forEach(item => {
                    const date = new Date(item.tanggal_transaksi);
                    if (date.getFullYear() === now.getFullYear()) {
                        const monthIndex = date.getMonth();
                        if (item.tipe_transaksi === 'pemasukan') {
                            income[monthIndex] += parseFloat(item.jumlah);
                        } else if (item.tipe_transaksi === 'pengeluaran') {
                            expense[monthIndex] += parseFloat(item.jumlah);
                        }
                    }
                });
            }

            return {
                labels,
                income,
                expense
            };
        }

        let chartInstance = null;

        // Function to initialize chart
        function initChart(filterType = 'month') {
            const canvas = document.getElementById('finance-chart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');

            // Hancurkan chart yang sudah ada jika ada
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Ambil data berdasarkan filter
            const data = getChartData(filterType);

            // Buat chart baru
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                            label: 'Pemasukan',
                            data: data.income,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Pengeluaran',
                            data: data.expense,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        // --- FUNGSI KALENDER ---
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                              "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            
            const monthElement = document.getElementById('current-month');
            const calendarDays = document.getElementById('calendar-days');
            if (!monthElement || !calendarDays) {
                console.error("Elemen kalender tidak ditemukan!");
                return;
            }

            monthElement.textContent = `${monthNames[month]} ${year}`;
            calendarDays.innerHTML = '';
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Tambahkan hari kosong di awal bulan
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                calendarDays.appendChild(emptyDay);
            }
            
            // Tambahkan hari-hari dalam bulan
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day p-2 text-center rounded cursor-pointer';
                
                // Tambahkan angka hari
                const dayNumber = document.createElement('span');
                dayNumber.textContent = day;
                dayElement.appendChild(dayNumber);
                
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                // Buat semua tanggal bisa diklik
                dayElement.addEventListener('click', () => selectDate(dateStr));
                
                // Cek apakah tanggal ini memiliki event
                const hasMeeting = highlightedDates.includes(dateStr);
                const hasAnnouncement = announcementDates.includes(dateStr);

                if (hasMeeting || hasAnnouncement) {
                    dayElement.classList.add('has-event', 'font-bold');
                    
                    // Buat container untuk indikator
                    const indicatorsContainer = document.createElement('div');
                    indicatorsContainer.className = 'indicators-container';
                    
                    if (hasMeeting) {
                        const indicator = document.createElement('div');
                        indicator.className = 'event-indicator';
                        indicatorsContainer.appendChild(indicator);
                    }
                    
                    if (hasAnnouncement) {
                        const indicator = document.createElement('div');
                        indicator.className = 'announcement-indicator';
                        indicatorsContainer.appendChild(indicator);
                    }
                    
                    dayElement.appendChild(indicatorsContainer);
                }
                
                // Tandai tanggal yang dipilih
                if (selectedDate === dateStr) {
                    dayElement.classList.add('selected');
                }
                
                calendarDays.appendChild(dayElement);
            }
        }

        function selectDate(dateStr) {
            console.log('Tanggal dipilih:', dateStr);
            selectedDate = dateStr;
            renderCalendar();
            // Memuat catatan meeting untuk tanggal yang dipilih
            loadMeetingNotes(dateStr);
            loadAnnouncements(dateStr);
        }

        // --- FUNGSI PEMANGGIL DATA ---
        async function loadHighlightedDates() {
            try {
                const response = await apiFetch('/meeting-notes-dates');
                const dates = Array.isArray(response) ? response : (response.dates || []);
                highlightedDates = dates.map(date => new Date(date).toISOString().split('T')[0]);
                console.log('Tanggal meeting berhasil dimuat:', highlightedDates);
                renderCalendar();
            } catch (error) { console.error('Gagal load tanggal meeting:', error); }
        }
        
        async function loadAnnouncementDates() {
            try {
                const response = await apiFetch('/announcements-dates');
                const dates = Array.isArray(response) ? response : (response.dates || []);
                announcementDates = dates.map(date => new Date(date).toISOString().split('T')[0]);
                console.log('Tanggal pengumuman berhasil dimuat:', announcementDates);
                renderCalendar();
            } catch (error) { console.error('Gagal load tanggal pengumuman:', error); }
        }
        
        async function loadMeetingNotes(date) {
            const container = document.getElementById('meeting-notes-container');
            // Tampilkan loading state
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <span class="material-symbols-outlined text-4xl animate-spin">refresh</span>
                    <p class="mt-2">Memuat catatan...</p>
                </div>
            `;
            
            try {
                // Format tanggal untuk API
                const formattedDate = new Date(date + 'T00:00:00').toISOString().split('T')[0];
                console.log('Memuat catatan untuk tanggal:', formattedDate);
                
                const response = await apiFetch(`/meeting-notes?date=${encodeURIComponent(formattedDate)}`);
                const notes = response.data || [];
                
                if (!Array.isArray(notes) || notes.length === 0) {
                    // Format tanggal untuk tampilan
                    const dateObj = new Date(date + 'T00:00:00');
                    const formattedDateDisplay = dateObj.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    
                    container.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-symbols-outlined text-4xl">event_note</span>
                            <p class="mt-2">Tidak ada catatan meeting</p>
                            <p class="text-xs mt-1">${formattedDateDisplay}</p>
                        </div>
                    `;
                    return;
                }
                
                // Tampilkan catatan meeting
                container.innerHTML = notes.map(note => `
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h4 class="font-semibold text-black mb-2">${note.topik || 'Tanpa Topik'}</h4>
                        <div class="text-sm text-gray-600 space-y-2">
                            <div><span class="font-medium">Hasil Diskusi:</span><p class="mt-1">${note.hasil_diskusi || 'Tidak ada hasil diskusi'}</p></div>
                            <div><span class="font-medium">Keputusan:</span><p class="mt-1">${note.keputusan || 'Tidak ada keputusan'}</p></div>
                        </div>
                    </div>
                `).join('');
                
            } catch (error) {
                console.error('Error loading meeting notes:', error);
                container.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <span class="material-symbols-outlined text-4xl">error</span>
                        <p class="mt-2">Gagal memuat catatan meeting</p>
                        <p class="text-xs mt-1">${error.message}</p>
                    </div>
                `;
            }
        }

        function normalizeDate(value) {
            if (!value) return '';
            if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
                return value.slice(0, 10);
            }
            try {
                return new Date(value).toISOString().split('T')[0];
            } catch (e) {
                return '';
            }
        }

        async function loadAnnouncements(selectedDate = null) {
            const container = document.getElementById('announcements-container');
            container.innerHTML = '<p class="text-center text-gray-500">Memuat...</p>';
            try {
                const response = await apiFetch('/announcements');
                let announcements = response.data || [];

                if (!Array.isArray(announcements)) announcements = [];

                if (selectedDate) {
                    announcements = announcements.filter(a => {
                        const rawDate = a.tanggal || a.created_at || a.tanggal_indo || a.formatted_tanggal;
                        const dateOnly = normalizeDate(rawDate);
                        return dateOnly === selectedDate;
                    });
                }
                
                if (announcements.length === 0) {
                    const msg = selectedDate ? 'Tidak ada pengumuman pada tanggal ini' : 'Tidak ada pengumuman';
                    container.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-symbols-outlined text-4xl">campaign</span>
                            <p class="mt-2">${msg}</p>
                        </div>
                    `;
                    return;
                }
                
                container.innerHTML = announcements.map(announcement => `
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-black">${announcement.judul || 'Tanpa Judul'}</h4>
                            <span class="text-xs text-gray-600">${announcement.tanggal_indo || announcement.formatted_tanggal || new Date(announcement.created_at).toLocaleDateString('id-ID')}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${announcement.ringkasan || announcement.isi_pesan || announcement.isi || 'Tidak ada pesan'}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600">Oleh: ${announcement.creator || announcement.creator_name || 'System'}</span>
                            ${announcement.lampiran_url ? `<a href="${announcement.lampiran_url}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Lampiran</a>` : ''}
                        </div>
                    </div>
                `).join('');
                
            } catch (error) {
                console.error('Error loading announcements:', error);
                container.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <span class="material-symbols-outlined text-4xl">error</span>
                        <p class="mt-2">Gagal memuat pengumuman</p>
                    </div>
                `;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initChart('month'); // Inisialisasi grafik dengan filter bulan

            // Event listener untuk filter chart
            const chartFilter = document.getElementById('chart-filter');
            if (chartFilter) {
                chartFilter.addEventListener('change', function() {
                    initChart(this.value);
                });
            }
            
            // Calendar event listeners
            const prevMonthBtn = document.getElementById('prev-month');
            const nextMonthBtn = document.getElementById('next-month');
            const refreshNotesBtn = document.getElementById('refresh-notes');
            const refreshAnnouncementsBtn = document.getElementById('refresh-announcements');
            
            if (prevMonthBtn) {
                prevMonthBtn.addEventListener('click', function() {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });
            }
            
            if (nextMonthBtn) {
                nextMonthBtn.addEventListener('click', function() {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });
            }
            
            if (refreshNotesBtn) {
                refreshNotesBtn.addEventListener('click', function() {
                    if (selectedDate) loadMeetingNotes(selectedDate);
                });
            }
            
            if (refreshAnnouncementsBtn) {
                refreshAnnouncementsBtn.addEventListener('click', () => loadAnnouncements(selectedDate));
            }
            
            // Event modal listeners
            const eventModalClose = document.getElementById('event-modal-close');
            const eventModalCloseBtn = document.getElementById('event-modal-close-btn');
            const eventModal = document.getElementById('event-modal');
            
            if (eventModalClose) {
                eventModalClose.addEventListener('click', function() {
                    eventModal.classList.remove('show');
                });
            }
            
            if (eventModalCloseBtn) {
                eventModalCloseBtn.addEventListener('click', function() {
                    eventModal.classList.remove('show');
                });
            }
            
            if (eventModal) {
                eventModal.addEventListener('click', function(e) {
                    if (e.target === eventModal) {
                        eventModal.classList.remove('show');
                    }
                });
            }
            
            // Initialize calendar and load data
            console.log('Inisialisasi dimulai...');
            renderCalendar();
            
            // Load data asynchronously
            (async function() {
                await loadHighlightedDates();
                await loadAnnouncementDates();
                
                // Pilih hari ini dan muat catatannya
                const today = new Date();
                const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                selectDate(todayStr);
                console.log('Inisialisasi selesai.');
            })();
        });
    </script>
</body>
</html>
