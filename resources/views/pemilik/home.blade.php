<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Digital Agency Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
                        "surface-dark": "#1e293b",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                        "text-light": "#111827",
                        "text-dark": "#f9fafb",
                        "subtext-light": "#6b7280",
                        "subtext-dark": "#d1d5db",
                        "border-light": "#e5e7eb",
                        "border-dark": "#4b5563",
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "1rem",
                        '2xl': '1.5rem',
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'hover': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
                        'glow': '0 0 15px rgba(59, 130, 246, 0.15)',
                    },
                },
            },
        };
    </script>
    <style>
        /* Custom styles for improved appearance */
        .gradient-primary {
            background: linear-gradient(135deg, #000000, #111827);
        }

        .gradient-dark {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
        }

        .gradient-subtle {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        }

        /* Modern Card Hover Effects */
        .modern-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        /* Button hover effects */
        .btn-primary {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        /* Menghapus efek hover pada tombol OWNERS (Jika diperlukan nanti, tapi sekarang pakai style standar) */
        .btn-no-hover {
            transition: none !important;
            background-color: white !important;
            color: black !important;
        }

        .btn-no-hover:hover {
            background-color: white !important;
            color: black !important;
            transform: none !important;
        }

        .btn-no-hover:before {
            display: none !important;
        }

        /* Chart bar animation */
        .chart-bar {
            transition: height 0.5s ease-in-out;
        }

        /* Modal Styles - Simplified */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 0;
            border: none;
            width: 90%;
            max-width: 500px;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: modalopen 0.3s;
        }

        @keyframes modalopen {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            padding: 8px 16px;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Loading animation */
        .loading-dots {
            display: inline-block;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '';
            }

            40% {
                content: '.';
            }

            60% {
                content: '..';
            }

            80%,
            100% {
                content: '...';
            }
        }

        /* Fade in animation for loaded content */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Calendar styles */
        .calendar-day {
            transition: all 0.2s ease;
            position: relative;
            color: #000000;
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
            color: #000000;
        }

        .calendar-day.selected {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
            font-weight: 600;
        }

        .event-indicator {
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #3b82f6;
        }

        .announcement-indicator {
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #f59e0b;
        }

        /* Chart Container Styles */
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="container mx-auto p-4 md:p-8">
        <!-- Include header template -->
        @include('pemilik/template/header')

        <main class="space-y-6 md:space-y-8">
            
            <!-- HERO SECTION - BACKGROUND DIUBAH MENJADI PUTIH (bg-white) -->
            <section class="bg-white rounded-2xl shadow-soft border border-gray-100 relative overflow-hidden p-6 md:p-8 lg:p-12">
                <!-- Decorative elements (Diubah warna agar terlihat di bg putih) -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100 opacity-50 rounded-full blur-3xl -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-50 opacity-50 rounded-full blur-3xl -ml-24 -mb-24"></div>

                <div class="max-w-4xl mx-auto relative z-10">
                    <!-- Teks Judul Diubah Menjadi Gelap (text-gray-900) -->
                    <h2 class="text-2xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 md:mb-4">HALLO, <span
                            id="ownerName">-</span>
                    </h2>
                    
                    <!-- Teks Deskripsi Diubah Menjadi Abu-abu (text-gray-600) -->
                    <p class="text-sm md:text-base text-gray-600 mb-6 md:mb-8 leading-relaxed">
                        Bisnis digital agency adalah perusahaan yang membantu bisnis lain memasarkan produk atau
                        jasanya secara online melalui berbagai layanan digital.
                    </p>
                    
                    <!-- Tombol OWNERS Diubah Menjadi Biru (bg-primary text-white) agar kontras dengan bg putih -->
                    <a href="/karyawan/absensi"
                        class="bg-primary text-white px-6 py-2 md:px-8 md:py-3 rounded-lg font-semibold shadow-lg shadow-blue-500/30 inline-block text-sm md:text-base cursor-pointer hover:bg-blue-600 transition-colors">
                        OWNERS
                    </a>
                </div>
            </section>

            <!-- Stats Grid - Menggunakan Style Modern Card dengan Layout Vertikal -->
            <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                
                <!-- Kehadiran Karyawan Card - Blue Theme -->
                <div id="attendance-card-trigger"
                    class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex flex-col items-center text-center space-y-4 cursor-pointer">
                    <div class="bg-blue-50 dark:bg-blue-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-blue-100 dark:ring-blue-800">
                        <span class="material-icons text-blue-600 dark:text-blue-400">groups</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kehadiran Karyawan</p>
                        <p id="attendancePercentage" class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">
                            00%</p>
                    </div>
                </div>

                <!-- JUMLAH LAYANAN CARD - Purple Theme -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex flex-col items-center text-center space-y-4">
                    <div class="bg-purple-50 dark:bg-purple-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-purple-100 dark:ring-purple-800">
                        <span class="material-icons text-purple-600 dark:text-purple-400">design_services</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Layanan</p>
                        <p id="serviceCount" class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">0</p>
                    </div>
                </div>


                <!-- TOTAL PEMASUKAN CARD - Green Theme -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex flex-col items-center text-center space-y-4">
                    <div class="bg-green-50 dark:bg-green-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-green-100 dark:ring-green-800">
                        <span class="material-icons text-green-600 dark:text-green-400">arrow_downward</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Pemasukan</p>
                        <p id="totalIncome" class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mt-0.5">Rp 0</p>
                    </div>
                </div>

                <!-- TOTAL PENGELUARAN CARD - Red Theme -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex flex-col items-center text-center space-y-4">
                    <div class="bg-red-50 dark:bg-red-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-red-100 dark:ring-red-800">
                        <span class="material-icons text-red-600 dark:text-red-400">arrow_upward</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Pengeluaran</p>
                        <p id="totalExpense" class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mt-0.5">Rp 0</p>
                    </div>
                </div>

                <!-- TOTAL KEUNTUNGAN CARD - Indigo Theme -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex flex-col items-center text-center space-y-4">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-800">
                        <span class="material-icons text-indigo-600 dark:text-indigo-400">account_balance_wallet</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Keuntungan</p>
                        <p id="totalProfit" class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mt-0.5">Rp 0</p>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-soft border border-gray-200 p-4 md:p-6 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-3 flex-wrap">
                    <h3 class="text-lg md:text-xl font-bold text-black">Grafik Keuangan</h3>
                    <div class="flex gap-2 flex-wrap">
                        <button id="filter-minggu" class="filter-btn active px-4 py-2 rounded-lg bg-blue-500 text-white font-medium text-sm hover:bg-blue-600 transition-colors">Per Minggu</button>
                        <button id="filter-bulan" class="filter-btn px-4 py-2 rounded-lg bg-gray-300 text-black font-medium text-sm hover:bg-gray-400 transition-colors">Per Bulan</button>
                        <button id="filter-tahun" class="filter-btn px-4 py-2 rounded-lg bg-gray-300 text-black font-medium text-sm hover:bg-gray-400 transition-colors">Per Tahun</button>
                    </div>
                </div>

                <!-- Grafik Keuangan menggunakan Chart.js -->
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="finance-chart"></canvas>
                </div>
            </section>


            <!-- Calendar and Meeting Notes Section -->
            <section class="gradient-subtle p-4 md:p-6 rounded-2xl shadow-soft mt-6 md:mt-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Calendar Section -->
                    <div class="bg-white p-4 rounded-lg shadow-soft border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg md:text-xl font-bold text-black">Kalender</h3>
                            <div class="flex items-center space-x-2">
                                <button id="prev-month"
                                    class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                                    <span class="material-icons text-sm">chevron_left</span>
                                </button>
                                <span id="current-month" class="text-lg font-medium text-black"></span>
                                <button id="next-month"
                                    class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                                    <span class="material-icons text-sm">chevron_right</span>
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
                    <div class="bg-white p-4 rounded-lg shadow-soft border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg md:text-xl font-bold text-black">Catatan Meeting</h3>
                            <button id="refresh-notes"
                                class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                                <span class="material-icons text-sm">refresh</span>
                            </button>
                        </div>
                        <div id="meeting-notes-container" class="space-y-3 max-h-96 overflow-y-auto">
                            <div class="text-center py-8 text-gray-500">
                                <span class="material-icons text-4xl">event_note</span>
                                <p class="mt-2">Tidak ada catatan pada tanggal ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Announcements Section -->
            <section class="gradient-subtle p-4 md:p-6 rounded-2xl shadow-soft mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg md:text-xl font-bold text-black">Pengumuman</h3>
                    <button id="refresh-announcements"
                        class="p-2 rounded-full bg-gray-100 text-black hover:bg-gray-200 transition-colors">
                        <span class="material-icons text-sm">refresh</span>
                    </button>
                </div>
                <div id="announcements-container" class="space-y-3 max-h-96 overflow-y-auto">
                    <div class="text-center py-8 text-gray-500">
                        <span class="material-icons text-4xl">campaign</span>
                        <p class="mt-2">Tidak ada pengumuman</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="mt-8 md:mt-12 gradient-dark text-center py-3 md:py-4 rounded-lg shadow-soft">
            <p class="text-xs md:text-sm text-gray-700">Copyright ©2025 by digital kolaborasi.id</p>
        </footer>
    </div>

    <!-- Modal Kehadiran Per Divisi -->
    <div id="attendanceModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Persentase Kehadiran Per Divisi</h3>
                <div id="modal-body-content" class="space-y-3">
                    <!-- Konten akan diisi oleh JavaScript -->
                    <p class="text-center text-gray-500">Memuat...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- VARIABEL GLOBAL ---
            let currentDate = new Date();
            let selectedDate = null;
            let highlightedDates = [];
            let announcementDates = [];
            let currentChartFilter = 'minggu'; // Default to weekly
            window.chartDataCache = {
                pemasukan_per_bulan: [0, 0, 0, 0, 0, 0, 0],
                pengeluaran_per_bulan: [0, 0, 0, 0, 0, 0, 0]
            };

            // --- FUNGSI API UNTUK OWNER ---
            async function ownerApiFetch(endpoint, options = {}) {
                const cacheBuster = `_t=${Date.now()}`;
                const url = `/api/owner${endpoint}${endpoint.includes('?') ? '&' : '?'}${cacheBuster}`;
                const defaultOptions = { headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } };
                const finalOptions = { ...defaultOptions, ...options };

                const response = await fetch(url, finalOptions);
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Server Error');
                }
                return await response.json();
            }

            // --- FUNGSI-FUNGSI DASHBOARD ---
            async function fetchOwnerData() {
                try {
                    const response = await ownerApiFetch('/data');
                    if (response.success && response.data) {
                        document.getElementById('ownerName').textContent = response.data.name.toUpperCase();
                    }
                } catch (error) {
                    console.error('Error loading owner data:', error);
                }
            }

            async function fetchServiceCount() {
                try {
                    const response = await ownerApiFetch('/service-count');
                    if (response.success) {
                        document.getElementById('serviceCount').textContent = response.data;
                    }
                } catch (error) {
                    console.error('Error loading service count:', error);
                    document.getElementById('serviceCount').textContent = '0';
                }
            }

            async function fetchDashboardData() {
                try {
                    // Fetch attendance percentage
                    const attendanceResponse = await ownerApiFetch('/attendance-percentage');
                    if (attendanceResponse.success) {
                        const percentage = attendanceResponse.data.persentase;
                        document.getElementById('attendancePercentage').textContent = percentage + '%';
                    }

                    // Fetch financial stats and chart data
                    const statsResponse = await ownerApiFetch('/dashboard-stats');
                    if (statsResponse.success) {
                        const stats = statsResponse.data;
                        console.log('Dashboard stats received:', stats);
                        document.getElementById('totalIncome').textContent = formatCurrency(stats.total_pemasukan);
                        document.getElementById('totalExpense').textContent = formatCurrency(stats.total_pengeluaran);
                        document.getElementById('totalProfit').textContent = formatCurrency(stats.total_keuntungan);
                        
                        // Store chart data untuk digunakan oleh filter
                        const pemasukanData = stats.pemasukan_per_bulan || [0, 0, 0, 0, 0, 0, 0];
                        const pengeluaranData = stats.pengeluaran_per_bulan || [0, 0, 0, 0, 0, 0, 0];
                        
                        window.chartDataCache = {
                            pemasukan_per_bulan: Array.isArray(pemasukanData) ? pemasukanData : [0, 0, 0, 0, 0, 0, 0],
                            pengeluaran_per_bulan: Array.isArray(pengeluaranData) ? pengeluaranData : [0, 0, 0, 0, 0, 0, 0]
                        };
                        
                        console.log('Chart data cached:', window.chartDataCache);
                        
                        // Update chart dengan data default (per minggu)
                        updateChart(window.chartDataCache.pemasukan_per_bulan, window.chartDataCache.pengeluaran_per_bulan, 'minggu');
                    }
                } catch (error) {
                    console.error('Error loading dashboard data:', error);
                    // Provide default data even if API fails
                    window.chartDataCache = {
                        pemasukan_per_bulan: [0, 0, 0, 0, 0, 0, 0],
                        pengeluaran_per_bulan: [0, 0, 0, 0, 0, 0, 0]
                    };
                    updateChart([0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0], 'minggu');
                }
            }

            async function updateChartByFilter(filter) {
                try {
                    currentChartFilter = filter;
                    let endpoint = '/dashboard-stats?period=weekly';
                    
                    if (filter === 'bulan') {
                        endpoint = '/dashboard-stats?period=monthly';
                    } else if (filter === 'tahun') {
                        endpoint = '/dashboard-stats?period=yearly';
                    }
                    
                    const statsResponse = await ownerApiFetch(endpoint);
                    if (statsResponse.success) {
                        const stats = statsResponse.data;
                        const pemasukanData = stats.pemasukan_per_bulan || stats.pemasukan_per_periode || [0, 0, 0, 0, 0, 0, 0];
                        const pengeluaranData = stats.pengeluaran_per_bulan || stats.pengeluaran_per_periode || [0, 0, 0, 0, 0, 0, 0];
                        const labels = stats.labels || null;
                        
                        updateChart(pemasukanData, pengeluaranData, filter, labels);
                    } else {
                        const pemasukanData = [0, 0, 0, 0, 0, 0, 0];
                        const pengeluaranData = [0, 0, 0, 0, 0, 0, 0];
                        updateChart(pemasukanData, pengeluaranData, filter);
                    }
                    } catch (error) {
                    console.error('Error updating chart:', error);
                    const pemasukanData = [0, 0, 0, 0, 0, 0, 0];
                    const pengeluaranData = [0, 0, 0, 0, 0, 0, 0];
                    updateChart(pemasukanData, pengeluaranData, filter);
                }
            }

            function formatCurrency(amount) {
                if (amount === null || amount === undefined || isNaN(amount)) {
                    return 'Rp 0';
                }
                
                // Konversi ke number jika string
                const numAmount = parseFloat(amount);
                
                if (numAmount === 0 || !isFinite(numAmount)) {
                    return 'Rp 0';
                }
                
                // Format manual ke Rupiah
                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(numAmount);
                
                return formatted;
            }

            function updateChart(pemasukanPerBulan, pengeluaranPerBulan, filter = 'minggu', labelsFromApi = null) {
                const ctx = document.getElementById('finance-chart');
                if (!ctx) return;

                // Validasi dan sanitasi data
                const pemasukanData = Array.isArray(pemasukanPerBulan) ? pemasukanPerBulan : [0, 0, 0, 0, 0, 0, 0];
                const pengeluaranData = Array.isArray(pengeluaranPerBulan) ? pengeluaranPerBulan : [0, 0, 0, 0, 0, 0, 0];
                
                // Pastikan semua nilai adalah number
                const pemasukanArray = pemasukanData.map(v => parseFloat(v) || 0);
                const pengeluaranArray = pengeluaranData.map(v => parseFloat(v) || 0);

                // Gunakan labels dari API jika tersedia
                let labels = Array.isArray(labelsFromApi) && labelsFromApi.length ? labelsFromApi.slice() : null;

                if (!labels) {
                    if (filter === 'minggu') {
                        labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                    } else if (filter === 'bulan') {
                        // Jika API tidak memberikan label, buat Minggu 1..N
                        labels = Array.from({ length: pemasukanArray.length }, (_, i) => `Minggu ${i + 1}`);
                    } else if (filter === 'tahun') {
                        // Jika API tidak memberikan label, gunakan Januari..Desember
                        labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'].slice(0, pemasukanArray.length);
                    }
                }

                // Destroy chart lama jika ada
                if (window.chartInstance) {
                    window.chartInstance.destroy();
                }

                window.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: pemasukanArray,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#10b981',
                                pointBorderWidth: 2,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Pengeluaran',
                                data: pengeluaranArray,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: '#ef4444',
                                pointBorderWidth: 2,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: { size: 12, weight: 'bold' },
                                    padding: 15,
                                    usePointStyle: true,
                                    color: '#000000'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                borderColor: '#ddd',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatCurrency(value);
                                    },
                                    font: { size: 10 },
                                    color: '#000000'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: true
                                }
                            },
                            x: {
                                ticks: {
                                    font: { size: 11 },
                                    color: '#000000'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // --- MODAL LOGIC ---
            const attendanceModal = document.getElementById('attendanceModal');
            const closeModal = document.querySelector('.close-modal');
            const attendanceCardTrigger = document.getElementById('attendance-card-trigger');

            if (closeModal) {
                closeModal.addEventListener('click', () => {
                    attendanceModal.style.display = 'none';
                });
            }

            if (attendanceCardTrigger) {
                attendanceCardTrigger.addEventListener('click', async () => {
                    try {
                        // Reload data attendance terbaru sebelum membuka modal
                        const attendanceResponse = await ownerApiFetch('/attendance-percentage');
                        let overallPercentage = 0;
                        if (attendanceResponse.success) {
                            overallPercentage = attendanceResponse.data.persentase;
                            document.getElementById('attendancePercentage').textContent = overallPercentage + '%';
                        }
                        
                        const response = await ownerApiFetch('/attendance-by-division');
                        if (response.success && response.data) {
                            const modalBody = document.getElementById('modal-body-content');
                            if (response.data.length === 0) {
                                modalBody.innerHTML = '<p class="text-center text-gray-500">Tidak ada data kehadiran</p>';
                            } else {
                                
                                modalBody.innerHTML = `
                                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-300">
                                        <p class="text-sm text-gray-600 mb-1">Kehadiran Keseluruhan:</p>
                                        <p class="text-3xl font-bold text-blue-600">${overallPercentage}%</p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700 mb-3">Breakdown Per Divisi:</p>
                                    ${response.data.map(div => `
                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border-l-4 border-green-500 mb-2">
                                            <div>
                                                <p class="font-semibold text-gray-800">${div.divisi}</p>
                                                <p class="text-xs text-gray-600">Hadir: ${div.hadir}/${div.total}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-2xl font-bold text-green-600">${div.persentase}%</p>
                                            </div>
                                        </div>
                                    `).join('')}
                                `;
                            }
                            attendanceModal.style.display = 'block';
                        }
                    } catch (error) {
                        console.error('Error loading attendance data:', error);
                        const modalBody = document.getElementById('modal-body-content');
                        modalBody.innerHTML = '<p class="text-center text-red-500">Gagal memuat data kehadiran</p>';
                        attendanceModal.style.display = 'block';
                    }
                });
            }

            window.addEventListener('click', (event) => {
                if (event.target === attendanceModal) {
                    attendanceModal.style.display = 'none';
                }
            });

            // --- FUNGSI KALENDER & PEMANGGIL DATA BARU ---
            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                
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
                    dayElement.className = 'calendar-day p-2 text-center rounded hover:bg-gray-100 cursor-pointer';
                    dayElement.textContent = day;
                    
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    
                    // PERUBAHAN PENTING: Buat semua tanggal bisa diklik
                    dayElement.addEventListener('click', () => selectDate(dateStr));
                    
                    // Cek apakah tanggal ini memiliki event
                    const hasMeeting = highlightedDates.includes(dateStr);
                    const hasAnnouncement = announcementDates.includes(dateStr);

                    if (hasMeeting || hasAnnouncement) {
                        dayElement.classList.add('has-event', 'font-bold', 'bg-blue-50');
                        
                        if (hasMeeting) {
                            const indicator = document.createElement('div');
                            indicator.className = 'event-indicator';
                            dayElement.appendChild(indicator);
                        }
                        
                        if (hasAnnouncement) {
                            const indicator = document.createElement('div');
                            indicator.className = 'announcement-indicator';
                            if (hasMeeting) indicator.style.left = '60%';
                            dayElement.appendChild(indicator);
                        }
                    }
                    
                    // Tandai tanggal yang dipilih
                    if (selectedDate === dateStr) {
                        dayElement.classList.add('selected', 'bg-blue-200', 'font-bold');
                    }
                    
                    calendarDays.appendChild(dayElement);
                }
            }

            function selectDate(dateStr) {
                console.log('Tanggal dipilih:', dateStr);
                selectedDate = dateStr;
                renderCalendar(); // Render ulang untuk menandai tanggal yang dipilih
                loadOwnerMeetingNotes(dateStr);
                loadOwnerAnnouncements(dateStr);
            }

            async function loadOwnerHighlightedDates() {
                try {
                    const response = await ownerApiFetch('/meeting-notes-dates');
                    const dates = response.data || [];
                    highlightedDates = dates.map(date => {
                        const dateObj = new Date(date);
                        return dateObj.toISOString().split('T')[0];
                    });
                    console.log('Tanggal meeting berhasil dimuat:', highlightedDates);
                    renderCalendar();
                } catch (error) { 
                    console.error('Gagal load tanggal meeting:', error); 
                }
            }

            async function loadOwnerAnnouncementDates() {
                try {
                    const response = await ownerApiFetch('/announcements-dates');
                    const dates = response.data || [];
                    announcementDates = dates.map(date => {
                        const dateObj = new Date(date);
                        return dateObj.toISOString().split('T')[0];
                    });
                    console.log('Tanggal pengumuman berhasil dimuat:', announcementDates);
                    renderCalendar();
                } catch (error) { 
                    console.error('Gagal load tanggal pengumuman:', error); 
                }
            }

            async function loadOwnerMeetingNotes(date) {
                const container = document.getElementById('meeting-notes-container');
                container.innerHTML = '<p class="text-center text-gray-500">Memuat...</p>';
                try {
                    const response = await ownerApiFetch(`/meeting-notes?date=${encodeURIComponent(date)}`);
                    const notes = response.data || [];

                    if (!Array.isArray(notes) || notes.length === 0) {
                        container.innerHTML = `<div class="text-center py-8 text-gray-500"><span class="material-icons text-4xl">event_note</span><p class="mt-2">Tidak ada catatan pada tanggal ini</p></div>`;
                        return;
                    }

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
                    container.innerHTML = `<div class="text-center py-8 text-red-500"><span class="material-icons text-4xl">error</span><p class="mt-2">Gagal memuat catatan meeting</p></div>`;
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

            async function loadOwnerAnnouncements(selectedDate = null) {
                const container = document.getElementById('announcements-container');
                container.innerHTML = '<p class="text-center text-gray-500">Memuat...</p>';
                try {
                    const response = await ownerApiFetch('/announcements');
                    let announcements = response.data || [];

                    if (!Array.isArray(announcements)) announcements = [];

                    if (selectedDate) {
                        announcements = announcements.filter(a => {
                            const rawDate = a.tanggal || a.created_at || a.tanggal_indo;
                            const dateOnly = normalizeDate(rawDate);
                            return dateOnly === selectedDate;
                        });
                    }

                    if (announcements.length === 0) {
                        const msg = selectedDate ? 'Tidak ada pengumuman pada tanggal ini' : 'Tidak ada pengumuman';
                        container.innerHTML = `<div class="text-center py-8 text-gray-500"><span class="material-icons text-4xl">campaign</span><p class="mt-2">${msg}</p></div>`;
                        return;
                    }

                    container.innerHTML = announcements.map(announcement => {
                        const tanggal = announcement.created_at ? new Date(announcement.created_at).toLocaleDateString('id-ID') : 'Tanpa tanggal';
                        return `
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-black">${announcement.judul || 'Tanpa Judul'}</h4>
                                <span class="text-xs text-gray-600">${tanggal}</span>
                            </div>
                            <p class="text-sm text-gray-600">${announcement.isi_pesan || 'Tidak ada pesan'}</p>
                        </div>
                    `}).join('');

                } catch (error) {
                    console.error('Error loading announcements:', error);
                    container.innerHTML = `<div class="text-center py-8 text-red-500"><span class="material-icons text-4xl">error</span><p class="mt-2">Gagal memuat pengumuman</p></div>`;
                }
            }

            // --- EVENT LISTENERS BARU ---
            document.getElementById('prev-month')?.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
            document.getElementById('next-month')?.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
            document.getElementById('refresh-notes')?.addEventListener('click', () => { if (selectedDate) loadOwnerMeetingNotes(selectedDate); });
            document.getElementById('refresh-announcements')?.addEventListener('click', () => loadOwnerAnnouncements(selectedDate));

            // --- FILTER CHART EVENT LISTENERS ---
            document.getElementById('filter-minggu')?.addEventListener('click', function() {
                updateChartByFilter('minggu');
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active', 'bg-blue-500', 'text-white'));
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.add('bg-gray-300', 'text-black'));
                this.classList.remove('bg-gray-300', 'text-black');
                this.classList.add('active', 'bg-blue-500', 'text-white');
            });

            document.getElementById('filter-bulan')?.addEventListener('click', function() {
                updateChartByFilter('bulan');
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active', 'bg-blue-500', 'text-white'));
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.add('bg-gray-300', 'text-black'));
                this.classList.remove('bg-gray-300', 'text-black');
                this.classList.add('active', 'bg-blue-500', 'text-white');
            });

            document.getElementById('filter-tahun')?.addEventListener('click', function() {
                updateChartByFilter('tahun');
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active', 'bg-blue-500', 'text-white'));
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.add('bg-gray-300', 'text-black'));
                this.classList.remove('bg-gray-300', 'text-black');
                this.classList.add('active', 'bg-blue-500', 'text-white');
            });

            // --- INISIALISASI UTAMA ---
            // Initialize chart dengan data default terlebih dahulu
            updateChart([0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0], 'minggu');
            
            fetchOwnerData();
            fetchServiceCount();
            fetchDashboardData();

            // Inisialisasi fitur baru
            renderCalendar();
            loadOwnerHighlightedDates();
            loadOwnerAnnouncementDates();
            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            selectDate(todayStr);
        });
    </script>
</body>

</html>
