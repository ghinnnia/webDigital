<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendance Screen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: "#3b82f6", "primary-dark": "#2563eb", "primary-light": "#60a5fa", secondary: "#8b5cf6", success: "#10b981", warning: "#f59e0b", danger: "#ef4444", background: "#f8fafc", "background-alt": "#f1f5f9", surface: "#ffffff", "text-primary": "#1e293b", "text-secondary": "#64748b", "border-color": "#e2e8f0", "shadow-color": "rgba(0, 0, 0, 0.08)", },
                    fontFamily: { display: ["Roboto", "sans-serif"] },
                    borderRadius: { DEFAULT: "1rem", lg: "1.25rem", full: "9999px" },
                    boxShadow: { card: "0 10px 25px rgba(0,0,0,0.08)", "card-hover": "0 20px 40px rgba(0,0,0,0.12)" },
                },
            },
            darkMode: 'class',
        };
    </script>
    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-alt: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: rgba(226, 232, 240, 0.5);
            --shadow-color: rgba(0, 0, 0, 0.08);
            --card-bg: linear-gradient(145deg, #ffffff, #f8fafc);
            --table-hover: #f8fafc;
        }

        .dark {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-alt: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --border-color: rgba(51, 65, 85, 0.5);
            --shadow-color: rgba(0, 0, 0, 0.3);
            --card-bg: linear-gradient(145deg, #1e293b, #334155);
            --table-hover: #334155;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-alt) 100%);
            min-height: 100vh;
            color: var(--text-secondary);
        }

        * {
            box-sizing: border-box;
        }

        .card {
            background: var(--card-bg);
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 10px 25px var(--shadow-color);
            border: 1px solid var(--border-color);
        }

        .action-card {
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .action-card:hover:not(:disabled) {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px var(--shadow-color);
        }

        .action-card:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .action-card.checkin .material-icons {
            color: #10b981;
        }

        .action-card.checkout .material-icons {
            color: #ef4444;
        }

        .action-card.sakit .material-icons {
            color: #ef4444;
        }

        .action-card.izin .material-icons {
            color: #f59e0b;
        }

        .action-card .material-icons {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }

        .clock-container {
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .clock-time {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .table-row {
            transition: all 0.2s ease;
            cursor: default;
        }

        .table-row:hover {
            background-color: var(--table-hover);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-on-time {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-late {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .status-absent {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .status-no-show {
            background-color: rgba(156, 163, 175, 0.1);
            color: #9ca3af;
        }

        .status-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .late-time-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 500;
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            margin-left: 0.5rem;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .pagination-btn {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            min-width: 36px;
            text-align: center;
        }

        .pagination-btn.active {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            color: white;
            border-color: transparent;
        }

        .swal2-popup {
            font-family: 'Roboto', sans-serif;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px var(--shadow-color);
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .swal2-header {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            padding: 1.25rem 1.5rem 1rem;
            margin: 0 !important;
            border-radius: 1.25rem 1.25rem 0 0;
        }

        .swal2-title {
            color: white !important;
            font-weight: 600 !important;
            font-size: 1.3rem !important;
            margin: 0 !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .swal2-html-container {
            padding: 1.25rem 1.5rem !important;
            margin: 0 !important;
            color: var(--text-primary);
        }

        .swal2-actions {
            padding: 0 1.5rem 1.25rem !important;
            margin: 0 !important;
            gap: 0.75rem !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            border-radius: 0.75rem !important;
            font-weight: 500 !important;
            padding: 0.75rem 1.25rem !important;
            transition: all 0.3s ease !important;
            font-size: 0.9rem !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
        }

        .swal2-cancel {
            background: var(--bg-alt) !important;
            color: var(--text-secondary) !important;
            border: 1px solid var(--border-color) !important;
        }

        .form-group {
            margin-bottom: 1rem;
            width: 100%;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group label .material-icons {
            font-size: 1.1rem;
            color: #3b82f6;
        }

        .form-group .required {
            color: #ef4444;
            margin-left: 0.25rem;
        }

        .uniform-input,
        .uniform-textarea,
        .uniform-select,
        .uniform-file {
            border-radius: 0.75rem !important;
            border: 2px solid var(--border-color) !important;
            padding: 0.75rem 1rem !important;
            width: 100% !important;
            font-size: 0.9rem !important;
            transition: all 0.3s ease !important;
            background: var(--bg-alt) !important;
            font-family: 'Roboto', sans-serif !important;
            color: var(--text-primary);
        }

        .uniform-input,
        .uniform-select,
        .uniform-file {
            height: 48px !important;
        }

        .uniform-input:focus,
        .uniform-textarea:focus,
        .uniform-select:focus,
        .uniform-file:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            background: var(--bg-secondary) !important;
            outline: none !important;
        }

        .uniform-textarea {
            min-height: 100px !important;
            resize: vertical !important;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }

        .empty-state .material-icons {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }

        .filter-select {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 0.9rem;
            outline: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .file-helper {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        @media (max-width: 640px) {
            .swal2-popup {
                width: 95% !important;
                max-width: 95% !important;
            }

            .clock-time {
                font-size: 2.5rem;
            }

            .action-card {
                padding: 1.5rem;
            }

            .action-card .material-icons {
                font-size: 2.5rem;
            }

            .card {
                padding: 1.5rem;
            }
        }

        /* Timezone info */
        .timezone-info {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-align: center;
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex flex-col p-4 lg:p-8">
        <!-- Assuming header blade partial exists or use placeholder -->
        @include('karyawan.templet.header')

        <main class="flex-grow w-full max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold" style="color: var(--text-primary);">ABSENSI KARYAWAN</h2>

            </div>

            <!-- Clock -->
            <div class="card clock-container mb-8">
                <p class="clock-time" id="clock-time">12:00:00</p>
                <p class="text-lg mt-2" id="clock-date">Senin, 01 Januari 2025</p>
                <p class="timezone-info" id="clock-timezone"></p>
            </div>

            <!-- Action Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

                <!-- Dynamic Check-in / Check-out Button -->
                <div id="main-action-btn" class="card action-card checkin">
                    <span id="main-action-icon" class="material-icons">login</span>
                    <p id="main-action-text" class="font-semibold" style="color: var(--text-primary);">ABSEN MASUK</p>
                </div>

                <!-- Sakit Button -->
                <div class="card action-card sakit btn-sakit">
                    <span class="material-icons">local_hospital</span>
                    <p class="font-semibold" style="color: var(--text-primary);">SAKIT</p>
                </div>

                <!-- Izin Button -->
                <div class="card action-card izin btn-izin">
                    <span class="material-icons">event_busy</span>
                    <p class="font-semibold" style="color: var(--text-primary);">IZIN</p>
                </div>
            </div>

            <!-- Status & History -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Riwayat Absensi -->
                <div class="card lg:col-span-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4">
                        <h3 class="font-bold text-xl flex items-center" style="color: var(--text-primary);">
                            <span class="material-icons text-primary mr-2">history</span>Riwayat Absensi
                        </h3>
                        <!-- Filter with Icon -->
                        <div class="relative">
                            <select id="history-filter" class="filter-select">
                                <option value="week">Minggu Ini</option>
                                <option value="month" selected>Bulan Ini</option>
                                <option value="year">Tahun Ini</option>
                            </select>
                            <!-- Custom Icon Overlay -->
                            <span
                                class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400"
                                style="font-size: 20px;">
                                filter_list
                            </span>
                        </div>
                    </div>

                    <table class="w-full text-left">
                        <thead>
                            <tr style="color: var(--text-primary); border-bottom: 1px solid var(--border-color);">
                                <th class="pb-3 font-medium">No</th>
                                <th class="pb-3 font-medium">Tanggal</th>
                                <th class="pb-3 font-medium">Jam Masuk</th>
                                <th class="pb-3 font-medium">Jam Pulang</th>
                                <th class="pb-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody" style="color: var(--text-secondary);"></tbody>
                    </table>
                    <div id="empty-state" class="empty-state">
                        <span class="material-icons">assignment_late</span>
                        <h4>Belum Ada Riwayat Absensi</h4>
                        <p>Anda belum memiliki riwayat absensi periode ini.</p>
                    </div>
                    <div class="pagination-container" id="pagination"></div>
                </div>

                <!-- Status Absensi -->
                <div class="card">
                    <h3 class="font-bold text-xl mb-4 flex items-center" style="color: var(--text-primary);">
                        <span class="material-icons text-primary mr-2">assignment</span>Status Absensi
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-primary">login</span>
                            <div>
                                <p class="font-medium" style="color: var(--text-primary);">Absen Masuk</p>
                                <p class="text-sm" id="today-checkin" style="color: var(--text-secondary);">-</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-primary">logout</span>
                            <div>
                                <p class="font-medium" style="color: var(--text-primary);">Absen Pulang</p>
                                <p class="text-sm" id="today-checkout" style="color: var(--text-secondary);">-</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-success">check_circle</span>
                            <div>
                                <p class="font-medium" style="color: var(--text-primary);">Status Hari Ini</p>
                                <p class="font-medium text-success" id="today-status">Belum Absen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="w-full max-w-7xl mx-auto mt-8 text-center text-xs sm:text-sm py-4"
            style="color: var(--text-secondary);">
            <p>Copyright ©2025 by digicity.id | <span id="current-timezone">Timezone: Loading...</span></p>
        </footer>
    </div>

    <script>
    // --- KONFIGURASI GLOBAL ---
    window.currentUserId = {{ auth()->id() }}; 
    window.apiBasePath = '/api/karyawan';
    window.todayData = null;
    
    // --- VARIABEL PAGINATION ---
    let currentPage = 1;
    const recordsPerPage = 5;
    let attendanceHistoryData = [];
    
    // --- KONSTAN TIMEZONE & JAM BATAS ---
    const TIMEZONE = 'Asia/Jakarta'; // WIB (UTC+7)
    const LIMIT_HOUR = 9; // Jam 9 pagi WIB
    const LIMIT_MINUTE = 5; // 5 menit (jadi batasnya 09:05)
    const LIMIT_TOTAL_MINUTES = LIMIT_HOUR * 60 + LIMIT_MINUTE; // 545 menit (09:05)

    // --- FUNGSI HELPER TIMEZONE ---

    function getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    // Fungsi yang lebih reliable untuk mendapatkan waktu WIB
    function getCurrentWIBTime() {
        const now = new Date();
        
        // Cara 1: Gunakan toLocaleString dengan timezone
        try {
            const options = {
                timeZone: 'Asia/Jakarta',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const timeStr = now.toLocaleTimeString('id-ID', options);
            const [hours, minutes, seconds] = timeStr.split(':').map(Number);
            return { hours, minutes, seconds };
        } catch (e) {
            console.warn("Cannot get WIB time via toLocaleString, using UTC+7 calculation");
            
            // Cara 2: Jika error, gunakan UTC+7
            const utcHours = now.getUTCHours();
            const utcMinutes = now.getUTCMinutes();
            
            // WIB = UTC+7
            const wibHours = (utcHours + 7) % 24;
            
            return { hours: wibHours, minutes: utcMinutes, seconds: now.getUTCSeconds() };
        }
    }

    // Format waktu dengan debug
    function formatTime(timeString) {
        if (!timeString || timeString === '00:00:00' || timeString === '00:00' || timeString === '00:00:00.000000') {
            return '-';
        }
        
        try {
            // Jika format waktu biasa (HH:mm:ss) - asumsi sudah WIB
            if (timeString.includes(':') && !timeString.includes('T')) {
                const parts = timeString.split(':');
                const hours = parts[0];
                const minutes = parts[1];
                return `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
            }
            
            // Jika format ISO (tanggal lengkap) - PERBAIKAN PENTING
            // Fungsi ini menangani format ISO seperti "2026-01-24T04:19:08.000000Z"
            if (timeString.includes('T')) {
                const date = new Date(timeString);
                if (!isNaN(date.getTime())) {
                    // Menggunakan toLocaleTimeString untuk konversi otomatis ke WIB
                    // atau secara manual sesuai kebutuhan.
                    // Di sini kita mempertahankan logika asli menggunakan getHours/getMinutes
                    // tapi perlu diperhatikan timezone browser vs UTC server.
                    
                    // Jika server mengirim UTC (diakhiri Z), getHours() akan mengambil jam UTC.
                    // Kita akan memaksa menggunakan getUTCHours() + 7 untuk konsistensi WIB jika inputnya UTC.
                    const utcHours = date.getUTCHours();
                    const utcMinutes = date.getUTCMinutes();
                    
                    // Konversi ke WIB (UTC+7)
                    let wibHours = (utcHours + 7) % 24;
                    let wibMinutes = utcMinutes;
                    
                    // Handle case where UTC hours + 7 crosses midnight
                    if (utcHours + 7 >= 24) {
                        // do nothing, modulo handles it
                    }

                    return `${wibHours.toString().padStart(2, '0')}:${wibMinutes.toString().padStart(2, '0')}`;
                }
            }
            
            return timeString;
            
        } catch (e) { 
            console.error('❌ Error formatting time:', e, timeString);
            return timeString; 
        }
    }

    // Format waktu lengkap untuk display
    function formatTimeDetailed(timeString) {
        if (!timeString) return '-';
        
        const formattedTime = formatTime(timeString);
        return formattedTime === '-' ? '-' : `${formattedTime} WIB`;
    }

    // --- FUNGSI PERHITUNGAN KETERLAMBATAN YANG DIPERBAIKI ---

    // Fungsi utama untuk menghitung keterlambatan - VERSI SEDERHANA DAN AKURAT
    function calculateLateMinutesFromTime(timeString) {
        console.log("🚨 CALCULATE LATE START - Input:", timeString);
        
        if (!timeString || timeString === '-' || timeString === '00:00:00' || timeString === '00:00' || timeString === '00:00:00.000000') {
            console.log("🚨 Returning 0 - invalid time string");
            return 0;
        }
        
        try {
            // --- PERBAIKAN DI SINI ---
            // Normalisasi waktu ISO ke HH:mm menggunakan formatTime yang sudah ada
            // Ini menangani input seperti "2026-01-24T04:19:08.000000Z" menjadi "11:19"
            const normalizedTime = formatTime(timeString);

            let hours = 0;
            let minutes = 0;
            
            if (normalizedTime !== '-' && normalizedTime.includes(':')) {
                const parts = normalizedTime.split(':');
                hours = parseInt(parts[0], 10);
                minutes = parseInt(parts[1], 10);
                
                console.log("🚨 Parsed (FIXED) - Hours:", hours, "Minutes:", minutes);
            } else {
                console.warn("🚨 Normalized time is invalid:", normalizedTime);
                return 0;
            }
            // -------------------------

            // Validasi
            if (isNaN(hours) || isNaN(minutes) || hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
                console.warn("🚨 Invalid time values - Hours:", hours, "Minutes:", minutes);
                return 0;
            }
            
            // Hitung total menit dari waktu masuk
            const totalMinutes = (hours * 60) + minutes;
            
            // Batas waktu: 09:05 = 545 menit - GUNAKAN ANGKA LANGSUNG
            const limitMinutes = 545; // 9*60 + 5 = 545
            
            console.log("🚨 CRITICAL CALCULATION:", {
                waktu_masuk: `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`,
                total_menit_masuk: totalMinutes,
                batas_waktu: "09:05",
                batas_menit: limitMinutes,
                selisih: totalMinutes - limitMinutes
            });
            
            // Keterlambatan = total menit waktu masuk - batas menit (545)
            const lateMinutes = Math.max(0, totalMinutes - limitMinutes);
            
            console.log("🚨 FINAL RESULT - Terlambat:", lateMinutes, "menit", 
                lateMinutes > 0 ? `(${Math.floor(lateMinutes/60)} jam ${lateMinutes%60} menit)` : "");
            
            return lateMinutes;
            
        } catch (e) {
            console.error('❌ Error calculating late minutes:', e);
            return 0;
        }
    }

    // Fungsi alternatif untuk verifikasi
    function simpleCalculateLate(timeString) {
        if (!timeString || timeString === '-' || timeString === '00:00:00') return 0;
        
        // Ambil jam dan menit dengan regex
        const match = timeString.toString().match(/(\d{1,2}):(\d{1,2})/);
        if (!match) return 0;
        
        const hours = parseInt(match[1], 10);
        const minutes = parseInt(match[2], 10);
        
        // Validasi
        if (isNaN(hours) || isNaN(minutes)) return 0;
        
        // Total menit sejak midnight
        const totalMinutes = (hours * 60) + minutes;
        
        // Batas 09:05 = 545 menit
        const lateMinutes = Math.max(0, totalMinutes - 545);
        
        console.log("🔍 SIMPLE CALC:", {
            input: timeString,
            parsed: hours + ":" + minutes,
            totalMinutes: totalMinutes,
            limit: 545,
            lateMinutes: lateMinutes,
            isLate: lateMinutes > 0 ? "YA" : "TIDAK"
        });
        
        return lateMinutes;
    }

    // Format waktu keterlambatan yang lebih baik
    function formatLateTime(totalMinutes) {
        if (totalMinutes <= 0) return '0 menit';
        
        const hours = Math.floor(totalMinutes / 60);
        const minutes = Math.floor(totalMinutes % 60);
        
        // Format yang lebih baik
        const parts = [];
        if (hours > 0) parts.push(`${hours} jam`);
        if (minutes > 0) parts.push(`${minutes} menit`);
        
        return parts.join(' ') || '0 menit';
    }

    // Hitung waktu yang telah berlalu sejak absen masuk - DIPERBAIKI
    function calculateTimeElapsedSinceCheckin(checkinTime) {
        if (!checkinTime || checkinTime === '-') return 0;
        
        try {
            // --- PERBAIKAN DI SINI ---
            // Normalisasi waktu agar ISO string bisa diproses
            const normalizedTime = formatTime(checkinTime);

            const parts = normalizedTime.split(':');
            const checkinHour = parseInt(parts[0], 10);
            const checkinMinute = parseInt(parts[1], 10);
            
            if (isNaN(checkinHour) || isNaN(checkinMinute)) {
                return 0;
            }
            // -------------------------
            
            // Dapatkan waktu WIB sekarang
            const wibTimeNow = getCurrentWIBTime();
            const currentHour = wibTimeNow.hours;
            const currentMinute = wibTimeNow.minutes;
            
            // Hitung total menit
            const currentTotalMinutes = (currentHour * 60) + currentMinute;
            const checkinTotalMinutes = (checkinHour * 60) + checkinMinute;
            
            let elapsedMinutes = currentTotalMinutes - checkinTotalMinutes;
            
            // Handle kasus melewati tengah malam
            if (elapsedMinutes < 0) {
                elapsedMinutes += (24 * 60);
            }
            
            return elapsedMinutes;
            
        } catch (e) {
            console.error('❌ Error calculating time elapsed:', e);
            return 0;
        }
    }

    function getCurrentWIBTimeString() {
        try {
            const wibTime = getCurrentWIBTime();
            const hours = wibTime.hours.toString().padStart(2, '0');
            const minutes = wibTime.minutes.toString().padStart(2, '0');
            const seconds = wibTime.seconds.toString().padStart(2, '0');
            return `${hours}:${minutes}:${seconds}`;
        } catch (error) {
            // Fallback
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            return `${hours}:${minutes}:${seconds}`;
        }
    }

    // Dapatkan jam dan menit WIB saat ini (format HH:mm)
    function getCurrentWIBHoursMinutes() {
        try {
            const wibTime = getCurrentWIBTime();
            const hours = wibTime.hours.toString().padStart(2, '0');
            const minutes = wibTime.minutes.toString().padStart(2, '0');
            return `${hours}:${minutes}`;
        } catch (error) {
            // Fallback
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            return `${hours}:${minutes}`;
        }
    }

    // Dapatkan jam WIB saat ini (angka)
    function getCurrentWIBHour() {
        const wibTime = getCurrentWIBTime();
        return wibTime.hours;
    }

    // Dapatkan menit WIB saat ini (angka)
    function getCurrentWIBMinutes() {
        const wibTime = getCurrentWIBTime();
        return wibTime.minutes;
    }

    // --- API FUNCTIONS ---

    async function apiFetch(endpoint, options = {}) {
        const url = window.apiBasePath + endpoint;
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
        const token = getCSRFToken();
        if (token) headers['X-CSRF-TOKEN'] = token;

        const config = { 
            ...options, 
            headers: { ...headers, ...options.headers } 
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                const message = data.message || data.error || `Terjadi kesalahan (${response.status})`;
                throw new Error(message);
            }
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // --- UI UPDATE FUNCTIONS ---

    function updateMainActionButton(hasCheckedIn) {
        const btn = document.getElementById('main-action-btn');
        const icon = document.getElementById('main-action-icon');
        const text = document.getElementById('main-action-text');

        if (hasCheckedIn) {
            btn.classList.remove('checkin');
            btn.classList.add('checkout');
            icon.textContent = 'logout';
            text.textContent = 'ABSEN PULANG';
            btn.dataset.action = 'Absen Pulang';
            btn.disabled = false;
            btn.style.pointerEvents = 'auto';
            btn.style.opacity = '1';
        } else {
            btn.classList.remove('checkout');
            btn.classList.add('checkin');
            icon.textContent = 'login';
            text.textContent = 'ABSEN MASUK';
            btn.dataset.action = 'Absen Masuk';
            btn.disabled = false;
            btn.style.pointerEvents = 'auto';
            btn.style.opacity = '1';
        }
    }

    function enableButtons(enable) {
        const mainBtn = document.getElementById('main-action-btn');
        if (enable) {
            mainBtn.style.pointerEvents = 'auto';
            mainBtn.style.opacity = '1';
            mainBtn.disabled = false;
        } else {
            mainBtn.style.pointerEvents = 'none';
            mainBtn.style.opacity = '0.5';
            mainBtn.disabled = true;
        }
        
        document.querySelectorAll('.btn-sakit, .btn-izin').forEach(el => {
            if (enable) {
                el.style.pointerEvents = 'auto';
                el.style.opacity = '1';
            } else {
                el.style.pointerEvents = 'none';
                el.style.opacity = '0.5';
            }
        });
    }

    async function fetchAndDisplayTodayStatus() {
        try {
            console.log("📡 Fetching today status...");
            const res = await apiFetch('/today-status');
            console.log("📡 Today status response:", JSON.stringify(res, null, 2));
            
            if (res && res.data && typeof res.data === 'object') {
                window.todayData = res.data;
                
                // DEBUG: Test calculation langsung
                if (res.data.jam_masuk) {
                    console.log("📡 DEBUG MANUAL CALCULATION:");
                    console.log("Jam masuk dari API:", res.data.jam_masuk);
                    
                    // Test dengan fungsi utama
                    const lateMain = calculateLateMinutesFromTime(res.data.jam_masuk);
                    
                    // Test dengan fungsi simple
                    const lateSimple = simpleCalculateLate(res.data.jam_masuk);
                    
                    // Parse manual
                    const match = res.data.jam_masuk.toString().match(/(\d{1,2}):(\d{1,2})/);
                    if (match) {
                        const hours = parseInt(match[1], 10);
                        const minutes = parseInt(match[2], 10);
                        const total = (hours * 60) + minutes;
                        const lateManual = Math.max(0, total - 545);
                        
                        console.log("📡 MANUAL DEBUG:", {
                            hours: hours,
                            minutes: minutes,
                            totalMinutes: total,
                            limit: 545,
                            lateManual: lateManual,
                            lateFromMainFunc: lateMain,
                            lateFromSimpleFunc: lateSimple,
                            api_late_minutes: res.data.late_minutes,
                            api_is_terlambat: res.data.is_terlambat
                        });
                    }
                }
            } else if (res && typeof res === 'object') {
                window.todayData = res;
            } else {
                window.todayData = null;
            }

            renderTodayStatusUI();
            
        } catch (error) {
            console.error('❌ Gagal memuat status hari ini:', error);
            window.todayData = null;
            renderTodayStatusUI();
        }
    }

    // --- FUNGSI RENDER STATUS HARI INI YANG DIPERBAIKI ---
    function renderTodayStatusUI() {
        const data = window.todayData;
        
        console.log("🎨 RENDER UI - Today Data:", data);
        
        if (!data) {
            // Jika tidak ada data hari ini
            document.getElementById('today-checkin').textContent = '-';
            document.getElementById('today-checkout').textContent = '-';
            
            const statusEl = document.getElementById('today-status');
            statusEl.textContent = 'Belum Absen';
            statusEl.className = 'font-medium text-success';
            
            // Set tombol ke mode absen masuk
            const mainBtn = document.getElementById('main-action-btn');
            mainBtn.classList.remove('checkout');
            mainBtn.classList.add('checkin');
            mainBtn.querySelector('.material-icons').textContent = 'login';
            mainBtn.querySelector('p').textContent = 'ABSEN MASUK';
            mainBtn.dataset.action = 'Absen Masuk';
            
            enableButtons(true);
            return;
        }

        // Update waktu masuk dan pulang
        const jamMasukWIB = data.jam_masuk ? formatTime(data.jam_masuk) : '-';
        const jamPulangWIB = data.jam_pulang ? formatTime(data.jam_pulang) : '-';
        
        console.log("🎨 UI Times:", {
            jam_masuk_raw: data.jam_masuk,
            jam_masuk_formatted: jamMasukWIB,
            jam_pulang_raw: data.jam_pulang,
            jam_pulang_formatted: jamPulangWIB
        });
        
        document.getElementById('today-checkin').textContent = jamMasukWIB === '-' ? '-' : `${jamMasukWIB} WIB`;
        document.getElementById('today-checkout').textContent = jamPulangWIB === '-' ? '-' : `${jamPulangWIB} WIB`;
        
        // Update status
        const statusEl = document.getElementById('today-status');
        
        // Reset class terlebih dahulu
        statusEl.className = 'font-medium';
        
        if (data.jenis_ketidakhadiran) {
            // Jika ada pengajuan sakit/izin
            statusEl.textContent = data.jenis_ketidakhadiran_label || 'Pengajuan';
            if (data.approval_status === 'pending') {
                statusEl.textContent += ' (Menunggu)';
                statusEl.classList.add('text-warning');
            } else {
                statusEl.classList.add('text-secondary');
            }
            
            // Nonaktifkan tombol jika ada pengajuan
            enableButtons(false);
            
        } else if (data.jam_masuk && data.jam_pulang) {
            // Sudah absen masuk dan pulang
            statusEl.textContent = 'Selesai';
            statusEl.classList.add('text-success');
            
            // Nonaktifkan tombol utama
            const mainBtn = document.getElementById('main-action-btn');
            mainBtn.querySelector('.material-icons').textContent = 'check_circle';
            mainBtn.querySelector('p').textContent = 'SELESAI';
            mainBtn.classList.remove('checkin', 'checkout');
            mainBtn.style.pointerEvents = 'none';
            mainBtn.style.opacity = '0.5';
            mainBtn.disabled = true;
            
            // Nonaktifkan tombol sakit/izin
            enableButtons(false);
            
        } else if (data.jam_masuk && !data.jam_pulang) {
            // Sudah absen masuk, belum pulang
            console.log("🎨 Status: Sudah absen masuk, belum pulang");
            
            // Hitung keterlambatan dengan dua metode untuk verifikasi
            const lateMinutesMain = calculateLateMinutesFromTime(data.jam_masuk);
            const lateMinutesSimple = simpleCalculateLate(data.jam_masuk);
            
            // Gunakan nilai yang lebih besar untuk memastikan
            const lateMinutes = Math.max(lateMinutesMain, lateMinutesSimple);
            
            console.log("🎨 LATE CALCULATION VERIFICATION:", {
                jam_masuk: data.jam_masuk,
                jam_masuk_formatted: jamMasukWIB,
                lateMinutesMain: lateMinutesMain,
                lateMinutesSimple: lateMinutesSimple,
                lateMinutesUsed: lateMinutes,
                isLate: lateMinutes > 0 ? "TERLAMBAT" : "TEPAT WAKTU",
                expected_for_11_19: "134 menit (11:19 - 09:05 = 2 jam 14 menit)",
                batas_waktu: "09:05 WIB"
            });
            
            // Hitung waktu yang telah berlalu sejak check-in
            const elapsedMinutes = calculateTimeElapsedSinceCheckin(jamMasukWIB);
            
            if (lateMinutes > 0) {
                // TERLAMBAT
                const formattedLateTime = formatLateTime(lateMinutes);
                const formattedElapsedTime = formatLateTime(elapsedMinutes);
                
                // Bersihkan dan buat ulang konten
                statusEl.innerHTML = '';
                
                // Teks utama "Terlambat"
                const mainText = document.createElement('span');
                mainText.textContent = 'Terlambat ';
                statusEl.appendChild(mainText);
                
                // Badge keterlambatan
                const lateBadge = document.createElement('span');
                lateBadge.className = 'late-time-badge';
                lateBadge.style.backgroundColor = 'rgba(239, 68, 68, 0.1)';
                lateBadge.style.color = '#ef4444';
                lateBadge.style.marginLeft = '0.5rem';
                lateBadge.style.padding = '0.125rem 0.5rem';
                lateBadge.style.borderRadius = '9999px';
                lateBadge.style.fontSize = '0.7rem';
                lateBadge.style.fontWeight = '500';
                lateBadge.textContent = `+${formattedLateTime}`;
                statusEl.appendChild(lateBadge);
                
                // Line break
                statusEl.appendChild(document.createElement('br'));
                
                // Waktu yang telah berlalu
                const elapsedText = document.createElement('small');
                elapsedText.style.color = 'var(--text-secondary)';
                elapsedText.style.fontSize = '0.8rem';
                elapsedText.textContent = `${formattedElapsedTime} sejak check-in`;
                statusEl.appendChild(elapsedText);
                
                statusEl.className = 'font-medium text-warning';
                
                console.log("🎨 Setting status: TERLAMBAT", {
                    lateMinutes: lateMinutes,
                    lateTime: formattedLateTime,
                    elapsedTime: formattedElapsedTime
                });
            } else {
                // TEPAT WAKTU
                const formattedElapsedTime = formatLateTime(elapsedMinutes);
                
                // Bersihkan dan buat ulang konten
                statusEl.innerHTML = '';
                
                // Teks utama "Tepat Waktu"
                const mainText = document.createElement('span');
                mainText.textContent = 'Tepat Waktu ';
                statusEl.appendChild(mainText);
                
                // Badge waktu yang telah berlalu
                const elapsedBadge = document.createElement('span');
                elapsedBadge.className = 'late-time-badge';
                elapsedBadge.style.backgroundColor = 'rgba(16, 185, 129, 0.1)';
                elapsedBadge.style.color = '#10b981';
                elapsedBadge.style.marginLeft = '0.5rem';
                elapsedBadge.style.padding = '0.125rem 0.5rem';
                elapsedBadge.style.borderRadius = '9999px';
                elapsedBadge.style.fontSize = '0.7rem';
                elapsedBadge.style.fontWeight = '500';
                elapsedBadge.textContent = `${formattedElapsedTime} sejak check-in`;
                statusEl.appendChild(elapsedBadge);
                
                statusEl.className = 'font-medium text-success';
                
                console.log("🎨 Setting status: TEPAT WAKTU", {
                    elapsedMinutes: elapsedMinutes,
                    elapsedTime: formattedElapsedTime
                });
            }
            
            // Simpan di data untuk referensi
            data.elapsed_minutes = elapsedMinutes;
            data.is_terlambat = lateMinutes > 0;
            
            // Set tombol ke mode absen pulang
            updateMainActionButton(true);
            enableButtons(true);
            
        } else {
            // Belum absen sama sekali
            statusEl.textContent = 'Belum Absen';
            statusEl.className = 'font-medium text-success';
            
            updateMainActionButton(false);
            enableButtons(true);
        }
    }

    // Fungsi untuk update UI langsung setelah action
    function updateUIAfterAction(responseData) {
        if (!responseData) return;
        
        // Update data hari ini
        window.todayData = responseData;
        
        // Update UI status
        renderTodayStatusUI();
        
        // Tambahkan ke riwayat jika belum ada
        if (responseData.tanggal) {
            const existingIndex = attendanceHistoryData.findIndex(
                item => item.tanggal === responseData.tanggal
            );
            
            if (existingIndex >= 0) {
                // Update data yang sudah ada
                attendanceHistoryData[existingIndex] = responseData;
            } else {
                // Tambahkan data baru di awal array
                attendanceHistoryData.unshift(responseData);
            }
            
            // Render ulang tabel riwayat
            renderHistoryTable();
        }
    }

    // --- HISTORY FUNCTIONS ---

    async function fetchAndRenderHistory() {
        try {
            const filterValue = document.getElementById('history-filter').value;
            const res = await apiFetch(`/history?filter=${filterValue}`);
            
            if (res && res.data && Array.isArray(res.data)) {
                attendanceHistoryData = res.data;
                
                // Debug: lihat data yang diterima
                console.log('History data received:', attendanceHistoryData);
                if (attendanceHistoryData.length > 0) {
                    console.log('Sample record:', {
                        tanggal: attendanceHistoryData[0].tanggal,
                        jam_masuk_raw: attendanceHistoryData[0].jam_masuk,
                        jam_masuk_formatted: formatTime(attendanceHistoryData[0].jam_masuk),
                        jam_pulang_raw: attendanceHistoryData[0].jam_pulang,
                        jam_pulang_formatted: formatTime(attendanceHistoryData[0].jam_pulang)
                    });
                }
            } else {
                attendanceHistoryData = [];
            }
            
            renderHistoryTable();
            
        } catch (error) {
            console.error('Gagal memuat riwayat:', error);
            attendanceHistoryData = [];
            renderHistoryTable();
        }
    }

    function renderHistoryTable() {
        const tbody = document.getElementById('history-tbody');
        const emptyState = document.getElementById('empty-state');
        const filterValue = document.getElementById('history-filter').value;
        const pagination = document.getElementById('pagination');

        // Filter data berdasarkan periode
        let filteredData = [...attendanceHistoryData];
        const now = new Date();

        if (filterValue === 'week') {
            const oneWeekAgo = new Date(now);
            oneWeekAgo.setDate(now.getDate() - 7);
            filteredData = filteredData.filter(item => {
                const itemDate = new Date(item.tanggal);
                return itemDate >= oneWeekAgo;
            });
        } else if (filterValue === 'month') {
            filteredData = filteredData.filter(item => {
                const itemDate = new Date(item.tanggal);
                return itemDate.getMonth() === now.getMonth() && 
                       itemDate.getFullYear() === now.getFullYear();
            });
        } else if (filterValue === 'year') {
            filteredData = filteredData.filter(item => {
                const itemDate = new Date(item.tanggal);
                return itemDate.getFullYear() === now.getFullYear();
            });
        }

        // Urutkan dari tanggal terbaru
        filteredData.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

        // Tampilkan empty state jika tidak ada data
        if (filteredData.length === 0) {
            tbody.style.display = 'none';
            emptyState.style.display = 'block';
            pagination.style.display = 'none';
            return;
        }

        tbody.style.display = 'table-row-group';
        emptyState.style.display = 'none';
        tbody.innerHTML = '';

        // Pagination logic
        const totalPages = Math.ceil(filteredData.length / recordsPerPage);
        if (currentPage > totalPages) currentPage = 1;
        
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = startIndex + recordsPerPage;
        const pageData = filteredData.slice(startIndex, endIndex);

        // Render rows
        pageData.forEach((record, index) => {
            const displayNo = startIndex + index + 1;
            const row = tbody.insertRow();
            row.className = 'table-row';
            row.style.borderBottom = '1px solid var(--border-color)';
            
            // Format tanggal
            const dateObj = new Date(record.tanggal);
            const dateStr = dateObj.toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            });
            
            // Tentukan status
            let statusHTML = '';
            
            if (record.jenis_ketidakhadiran) {
                let badgeClass = 'status-absent';
                if (record.jenis_ketidakhadiran === 'sakit') badgeClass = 'status-late';
                if (record.jenis_ketidakhadiran === 'izin') badgeClass = 'status-pending';
                
                let statusText = record.jenis_ketidakhadiran_label || 'Pengajuan';
                if (record.approval_status === 'pending') {
                    statusText += ' (Menunggu)';
                }
                
                statusHTML = `<span class="status-badge ${badgeClass}">${statusText}</span>`;
                
            } else if (record.jam_masuk) {
                // Cek keterlambatan dengan fungsi yang diperbaiki (batas 09:05)
                const lateMinutes = calculateLateMinutesFromTime(record.jam_masuk);
                const isLate = lateMinutes > 0;
                
                const statusClass = isLate ? 'status-late' : 'status-on-time';
                const statusLabel = isLate ? 'Terlambat' : 'Tepat Waktu';
                
                statusHTML = `<span class="status-badge ${statusClass}">${statusLabel}</span>`;
                
                if (isLate && lateMinutes > 0) {
                    const formattedLateTime = formatLateTime(lateMinutes);
                    statusHTML += `<span class="late-time-badge">+${formattedLateTime}</span>`;
                }
                
            } else {
                statusHTML = '<span class="status-badge status-no-show">Tidak Hadir</span>';
            }

            row.innerHTML = `
                <td class="py-3" style="color: var(--text-primary);">${displayNo}</td>
                <td class="py-3">${dateStr}</td>
                <td class="py-3">${formatTime(record.jam_masuk)}</td>
                <td class="py-3">${formatTime(record.jam_pulang)}</td>
                <td class="py-3">${statusHTML}</td>
            `;
        });

        // Render pagination
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const container = document.getElementById('pagination');
        container.innerHTML = '';
        
        if (totalPages <= 1) {
            container.style.display = 'none';
            return;
        }
        
        container.style.display = 'flex';

        // Tombol Previous
        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.innerHTML = '<span class="material-icons" style="font-size: 16px;">chevron_left</span>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderHistoryTable();
            }
        };
        container.appendChild(prevBtn);

        // Tombol nomor halaman
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => {
                currentPage = i;
                renderHistoryTable();
            };
            container.appendChild(pageBtn);
        }

        // Tombol Next
        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.innerHTML = '<span class="material-icons" style="font-size: 16px;">chevron_right</span>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderHistoryTable();
            }
        };
        container.appendChild(nextBtn);
    }

    // --- CLOCK FUNCTION (WIB) ---
    function updateClock() {
        const now = new Date();
        
        // Format waktu sesuai timezone 'Asia/Jakarta' (WIB)
        const timeOptions = {
            timeZone: 'Asia/Jakarta',
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        
        // Format tanggal sesuai timezone 'Asia/Jakarta'
        const dateOptions = {
            timeZone: 'Asia/Jakarta',
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        
        try {
            // Waktu WIB
            const timeStr = now.toLocaleTimeString('id-ID', timeOptions);
            document.getElementById('clock-time').textContent = timeStr;
            
            // Tanggal WIB
            let dateStr = now.toLocaleDateString('id-ID', dateOptions);
            // Kapitalisasi hari
            dateStr = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
            document.getElementById('clock-date').textContent = dateStr;
            
        } catch (error) {
            // Fallback jika toLocaleString error
            console.error('Timezone error:', error);
            
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            document.getElementById('clock-time').textContent = `${hours}:${minutes}:${seconds}`;
            
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dateStr = `${days[now.getDay()]}, ${String(now.getDate()).padStart(2, '0')} ${months[now.getMonth()]} ${now.getFullYear()}`;
            document.getElementById('clock-date').textContent = dateStr;
        }
        
        // Update timezone info
        document.getElementById('clock-timezone').textContent = `Waktu Indonesia Barat (WIB) - Jakarta`;
        
        // Update footer timezone info
        try {
            const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const nowUTC = new Date(now.toUTCString());
            const localTime = new Date(now.toString());
            const offset = (localTime - nowUTC) / (1000 * 60 * 60); // Offset in hours
            
            document.getElementById('current-timezone').textContent = 
                `Timezone: ${TIMEZONE} | Browser: ${browserTimezone} (UTC${offset >= 0 ? '+' : ''}${offset})`;
        } catch (e) {
            document.getElementById('current-timezone').textContent = `Timezone: ${TIMEZONE}`;
        }
    }

    // --- EVENT LISTENERS & INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        // Set theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        }
        
        // Start clock (WIB)
        updateClock();
        setInterval(updateClock, 1000);
        
        // Load initial data
        fetchAndDisplayTodayStatus();
        fetchAndRenderHistory();
        
        // Setup filter event listener
        document.getElementById('history-filter').addEventListener('change', () => {
            currentPage = 1;
            renderHistoryTable();
        });
        
        // --- MAIN ACTION BUTTON (Check-in/Check-out) ---
        document.getElementById('main-action-btn').addEventListener('click', async function() {
            const action = this.dataset.action;
            
            // Refresh data dulu untuk memastikan state terkini
            await fetchAndDisplayTodayStatus();
            
            if (action === 'Absen Pulang') {
                // Validasi: harus sudah absen masuk dulu
                if (!window.todayData || !window.todayData.jam_masuk) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Belum Absen Masuk',
                        text: 'Anda harus melakukan absen masuk terlebih dahulu sebelum absen pulang.',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                // Validasi: sudah absen pulang
                if (window.todayData.jam_pulang) {
                    await Swal.fire({
                        icon: 'info',
                        title: 'Sudah Absen Pulang',
                        text: 'Anda sudah melakukan absen pulang hari ini.',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                // Tampilkan waktu WIB saat ini
                const currentWIBTime = getCurrentWIBHoursMinutes();
                
                // Konfirmasi absen pulang dengan waktu WIB
                const result = await Swal.fire({
                    title: 'Konfirmasi Absen Pulang',
                    html: `<p>Apakah Anda yakin ingin melakukan <strong>Absen Pulang</strong>?</p>
                           <p class="text-sm mt-2">Waktu saat ini: <strong>${currentWIBTime} WIB</strong></p>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Absen Pulang',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280'
                });
                
                if (result.isConfirmed) {
                    // Cek apakah pulang lebih awal (sebelum jam 17:00 WIB)
                    const currentWIBHour = getCurrentWIBHour();
                    
                    let payload = {};
                    
                    if (currentWIBHour < 17) {
                        const earlyResult = await Swal.fire({
                            title: 'Pulang Lebih Awal',
                            html: `
                                <div class="form-group">
                                    <label><span class="material-icons">info</span> Alasan Pulang Lebih Awal <span class="required">*</span></label>
                                    <textarea id="early-checkout-reason" class="uniform-textarea" placeholder="Masukkan alasan pulang lebih awal..." rows="3" required></textarea>
                                    <p class="file-helper">Harap isi alasan jika pulang sebelum jam 17:00 WIB</p>
                                </div>
                            `,
                            focusConfirm: false,
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            preConfirm: () => {
                                const reason = document.getElementById('early-checkout-reason').value.trim();
                                if (!reason) {
                                    Swal.showValidationMessage('Harap masukkan alasan pulang lebih awal');
                                    return false;
                                }
                                return reason;
                            }
                        });
                        
                        if (earlyResult.isConfirmed) {
                            payload.early_checkout_reason = earlyResult.value;
                        } else {
                            return; // User membatalkan
                        }
                    }
                    
                    // Kirim request absen pulang
                    try {
                        Swal.showLoading();
                        
                        const response = await apiFetch('/absen-pulang', {
                            method: 'POST',
                            body: JSON.stringify(payload)
                        });
                        
                        Swal.hideLoading();
                        
                        if (response.success) {
                            // Update UI dengan data baru
                            updateUIAfterAction(response.data);
                            
                            // Tampilkan waktu WIB yang berhasil dicatat
                            const jamPulangWIB = formatTimeDetailed(response.data.jam_pulang);
                            
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: `Absen pulang berhasil dicatat<br>
                                       <p class="text-sm mt-2">Jam Pulang: <strong>${jamPulangWIB}</strong></p>`,
                                confirmButtonColor: '#3b82f6'
                            });
                            
                        } else {
                            throw new Error(response.message || 'Gagal melakukan absen pulang');
                        }
                        
                    } catch (error) {
                        Swal.hideLoading();
                        await Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message || 'Terjadi kesalahan saat melakukan absen pulang',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                }
                
            } else if (action === 'Absen Masuk') {
                // Validasi: sudah absen masuk hari ini
                if (window.todayData && window.todayData.jam_masuk) {
                    const jamMasukWIB = formatTimeDetailed(window.todayData.jam_masuk);
                    await Swal.fire({
                        icon: 'info',
                        title: 'Sudah Absen Masuk',
                        text: `Anda sudah melakukan absen masuk hari ini pukul ${jamMasukWIB}`,
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                // Validasi: ada pengajuan sakit/izin
                if (window.todayData && window.todayData.jenis_ketidakhadiran) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Absen',
                        text: `Anda sudah mengajukan ${window.todayData.jenis_ketidakhadiran_label} hari ini.`,
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                // Tampilkan waktu WIB saat ini
                const currentWIBTime = getCurrentWIBHoursMinutes();
                
                // Konfirmasi absen masuk dengan info waktu
                const result = await Swal.fire({
                    title: 'Konfirmasi Absen Masuk',
                    html: `<p>Apakah Anda yakin ingin melakukan <strong>Absen Masuk</strong>?</p>
                           <p class="text-sm mt-2">Waktu saat ini: <strong>${currentWIBTime} WIB</strong></p>
                           <p class="text-xs mt-1 text-warning">Batas waktu absen masuk: <strong>09:05 WIB</strong></p>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Absen Masuk',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280'
                });
                
                if (result.isConfirmed) {
                    try {
                        Swal.showLoading();
                        
                        const response = await apiFetch('/absen-masuk', {
                            method: 'POST',
                            body: JSON.stringify({})
                        });
                        
                        Swal.hideLoading();
                        
                        if (response.success) {
                            // Update UI dengan data baru
                            updateUIAfterAction(response.data);
                            
                            // Tampilkan pesan sesuai status terlambat
                            let message = response.message || 'Absen masuk berhasil dicatat';
                            let icon = 'success';
                            let title = 'Berhasil!';
                            let jamMasukWIB = formatTimeDetailed(response.data.jam_masuk);
                            
                            console.log("Absen masuk response:", {
                                raw: response.data.jam_masuk,
                                formatted: jamMasukWIB
                            });
                            
                            // Hitung keterlambatan untuk notifikasi (batas 09:05)
                            const lateMinutes = calculateLateMinutesFromTime(response.data.jam_masuk);
                            
                            if (lateMinutes > 0) {
                                const lateTime = formatLateTime(lateMinutes);
                                message = `Absen masuk berhasil dicatat<br><small>(Terlambat ${lateTime} dari batas 09:05)</small>`;
                                icon = 'warning';
                                title = 'Absen Masuk (Terlambat)';
                            } else {
                                message = `Absen masuk berhasil dicatat<br><small>(Tepat waktu, batas 09:05)</small>`;
                            }
                            
                            await Swal.fire({
                                icon: icon,
                                title: title,
                                html: `${message}<br>
                                       <p class="text-sm mt-2">Jam Masuk: <strong>${jamMasukWIB}</strong></p>`,
                                confirmButtonColor: '#3b82f6'
                            });
                            
                        } else {
                            throw new Error(response.message || 'Gagal melakukan absen masuk');
                        }
                        
                    } catch (error) {
                        Swal.hideLoading();
                        await Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message || 'Terjadi kesalahan saat melakukan absen masuk',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                }
            }
        });
        
        // --- SAKIT BUTTON ---
        document.querySelector('.btn-sakit').addEventListener('click', async () => {
            // Refresh data dulu
            await fetchAndDisplayTodayStatus();
            
            // Validasi: sudah absen atau ada pengajuan
            if (window.todayData) {
                if (window.todayData.jam_masuk) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Mengajukan',
                        text: 'Anda sudah melakukan absen masuk hari ini.',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                if (window.todayData.jenis_ketidakhadiran) {
                    await Swal.fire({
                        icon: 'info',
                        title: 'Sudah Ada Pengajuan',
                        text: `Anda sudah mengajukan ${window.todayData.jenis_ketidakhadiran_label} hari ini.`,
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
            }
            
            // Tampilkan form sakit
            const today = new Date().toISOString().split('T')[0];
            
            const result = await Swal.fire({
                title: '<span class="material-icons">local_hospital</span> Form Sakit',
                html: `
                    <div class="form-group">
                        <label><span class="material-icons">calendar_today</span> Tanggal Mulai <span class="required">*</span></label>
                        <input type="date" id="sakit-start" class="uniform-input" value="${today}" min="${today}">
                    </div>
                    <div class="form-group">
                        <label><span class="material-icons">calendar_today</span> Tanggal Selesai <span class="required">*</span></label>
                        <input type="date" id="sakit-end" class="uniform-input" value="${today}" min="${today}">
                    </div>
                    <div class="form-group">
                        <label><span class="material-icons">description</span> Keterangan <span class="required">*</span></label>
                        <textarea id="sakit-reason" class="uniform-textarea" placeholder="Masukkan keterangan sakit..." rows="3" required></textarea>
                        <p class="file-helper">Harap isi keterangan dengan jelas</p>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Kirim Pengajuan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                preConfirm: () => {
                    const start = document.getElementById('sakit-start').value;
                    const end = document.getElementById('sakit-end').value;
                    const reason = document.getElementById('sakit-reason').value.trim();
                    
                    if (!start || !end || !reason) {
                        Swal.showValidationMessage('Semua field harus diisi');
                        return false;
                    }
                    
                    if (new Date(end) < new Date(start)) {
                        Swal.showValidationMessage('Tanggal selesai tidak boleh sebelum tanggal mulai');
                        return false;
                    }
                    
                    return { start_date: start, end_date: end, reason: reason };
                }
            });
            
            if (result.isConfirmed) {
                try {
                    Swal.showLoading();
                    
                    const response = await apiFetch('/submit-izin', {
                        method: 'POST',
                        body: JSON.stringify({
                            tanggal: result.value.start_date,
                            tanggal_akhir: result.value.end_date,
                            keterangan: result.value.reason,
                            jenis: 'sakit'
                        })
                    });
                    
                    Swal.hideLoading();
                    
                    if (response.success) {
                        // Update UI
                        updateUIAfterAction(response.data);
                        
                        await Swal.fire({
                            icon: 'success',
                            title: 'Pengajuan Terkirim!',
                            text: 'Pengajuan sakit Anda telah dikirim dan sedang menunggu persetujuan.',
                            confirmButtonColor: '#3b82f6'
                        });
                        
                    } else {
                        throw new Error(response.message || 'Gagal mengirim pengajuan');
                    }
                    
                } catch (error) {
                    Swal.hideLoading();
                    await Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat mengirim pengajuan',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }
        });
        
        // --- IZIN BUTTON ---
        document.querySelector('.btn-izin').addEventListener('click', async () => {
            // Refresh data dulu
            await fetchAndDisplayTodayStatus();
            
            // Validasi: sudah absen atau ada pengajuan
            if (window.todayData) {
                if (window.todayData.jam_masuk) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Mengajukan',
                        text: 'Anda sudah melakukan absen masuk hari ini.',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                
                if (window.todayData.jenis_ketidakhadiran) {
                    await Swal.fire({
                        icon: 'info',
                        title: 'Sudah Ada Pengajuan',
                        text: `Anda sudah mengajukan ${window.todayData.jenis_ketidakhadiran_label} hari ini.`,
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
            }
            
            // Tampilkan form izin
            const today = new Date().toISOString().split('T')[0];
            
            const result = await Swal.fire({
                title: '<span class="material-icons">event_busy</span> Form Izin',
                html: `
                    <div class="form-group">
                        <label><span class="material-icons">calendar_today</span> Tanggal Mulai <span class="required">*</span></label>
                        <input type="date" id="izin-start" class="uniform-input" value="${today}" min="${today}">
                    </div>
                    <div class="form-group">
                        <label><span class="material-icons">calendar_today</span> Tanggal Selesai <span class="required">*</span></label>
                        <input type="date" id="izin-end" class="uniform-input" value="${today}" min="${today}">
                    </div>
                    <div class="form-group">
                        <label><span class="material-icons">description</span> Keterangan <span class="required">*</span></label>
                        <textarea id="izin-reason" class="uniform-textarea" placeholder="Masukkan keterangan izin..." rows="3" required></textarea>
                        <p class="file-helper">Harap isi keterangan dengan jelas</p>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Kirim Pengajuan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                preConfirm: () => {
                    const start = document.getElementById('izin-start').value;
                    const end = document.getElementById('izin-end').value;
                    const reason = document.getElementById('izin-reason').value.trim();
                    
                    if (!start || !end || !reason) {
                        Swal.showValidationMessage('Semua field harus diisi');
                        return false;
                    }
                    
                    if (new Date(end) < new Date(start)) {
                        Swal.showValidationMessage('Tanggal selesai tidak boleh sebelum tanggal mulai');
                        return false;
                    }
                    
                    return { start_date: start, end_date: end, reason: reason };
                }
            });
            
            if (result.isConfirmed) {
                try {
                    Swal.showLoading();
                    
                    const response = await apiFetch('/submit-izin', {
                        method: 'POST',
                        body: JSON.stringify({
                            tanggal: result.value.start_date,
                            tanggal_akhir: result.value.end_date,
                            keterangan: result.value.reason,
                            jenis: 'izin'
                        })
                    });
                    
                    Swal.hideLoading();
                    
                    if (response.success) {
                        // Update UI
                        updateUIAfterAction(response.data);
                        
                        await Swal.fire({
                            icon: 'success',
                            title: 'Pengajuan Terkirim!',
                            text: 'Pengajuan izin Anda telah dikirim dan sedang menunggu persetujuan.',
                            confirmButtonColor: '#3b82f6'
                        });
                        
                    } else {
                        throw new Error(response.message || 'Gagal mengirim pengajuan');
                    }
                    
                } catch (error) {
                    Swal.hideLoading();
                    await Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat mengirim pengajuan',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }
        });
        
        // Auto refresh data setiap 30 detik
        setInterval(async () => {
            await fetchAndDisplayTodayStatus();
        }, 30000);
    });
</script>
</body>

</html>