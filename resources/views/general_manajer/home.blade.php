<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Beranda (Home) - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6", // Biru yang lebih terang dan standar
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
                        DEFAULT: "0.75rem", // 12px
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

        /* Deadline card hover effects */
      

        /* Button styles */
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        /* Custom styles untuk transisi - Dihapus karena sudah ada di template header */

        /* Animasi hamburger - Dihapus karena sudah ada di template header */

        /* Style untuk efek hover yang lebih menonjol - Dihapus karena sudah ada di template header */

        /* Gaya untuk indikator aktif/hover - Dihapus karena sudah ada di template header */

        /* Memastikan sidebar tetap di posisinya saat scroll - Dihapus karena sudah ada di template header */

        /* Menyesuaikan konten utama agar tidak tertutup sidebar - Dihapus karena sudah ada di template header */

        /* Scrollbar kustom untuk sidebar - Dihapus karena sudah ada di template header */

        /* Mobile card adjustments */
        @media (max-width: 639px) {
            .stat-card {
                padding: 0.75rem !important;
            }

            .stat-card .icon-container {
                width: 2rem !important;
                height: 2rem !important;
            }

            .stat-card .material-icons-outlined {
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

        /* Calendar styles */
        .calendar-day {
            transition: all 0.2s ease;
            position: relative;
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
        }

        .calendar-day.selected {
            background-color: rgba(59, 130, 246, 0.3);
            font-weight: 700;
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
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('general_manajer/templet/header')

        <main class="flex-1 flex flex-col bg-background-light main-content">
            <div class="flex-1 p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Beranda</h2>

                @php
                    $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
                    $totalLayanan = \App\Models\Layanan::count();
                    $totalProject = \App\Models\Project::count();
                @endphp

              <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6 sm:mb-8">
    <div class="stat-card bg-card-light rounded-DEFAULT p-3 sm:p-5 flex flex-col items-center justify-center text-center border border-border-light">
        <div class="icon-container w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg mb-2 flex items-center justify-center">
            <span class="material-icons-outlined text-primary text-sm sm:text-base">groups</span>
        </div>
        <div class="min-w-0 flex-1">
            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Jumlah Karyawan</p>
            <p class="value-text text-base sm:text-xl font-bold truncate">{{ \App\Models\User::whereNotIn('role', ['admin', 'owner'])->count() }}</p>
        </div>
    </div>

                    <div
                        class="stat-card bg-card-light rounded-DEFAULT p-3 sm:p-5 flex flex-col items-center justify-center text-center border border-border-light">
                        <div
                            class="icon-container w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg mb-2 flex items-center justify-center">
                            <span class="material-icons-outlined text-green-500 text-sm sm:text-base">design_services</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Total Layanan</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate">{{ $totalLayanan }}</p>
                        </div>
                    </div>

                    <div
                        class="stat-card bg-card-light rounded-DEFAULT p-3 sm:p-5 flex flex-col items-center justify-center text-center border border-border-light">
                        <div
                            class="icon-container w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg mb-2 flex items-center justify-center">
                            <span class="material-icons-outlined text-purple-500 text-sm sm:text-base">folder_open</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Total Project</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate">{{ $totalProject }}</p>
                        </div>
                    </div>
                </div>

                </div>
        

            <section class="bg-white rounded-xl p-3 sm:p-8 border border-gray-200 shadow-card mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-black">Kalender</h3>
                            <div class="flex items-center space-x-2">
                                <button id="prev-month"
                                    class="p-2 rounded-full hover:bg-gray-100">
                                    <span class="material-icons-outlined">chevron_left</span>
                                </button>
                                <span id="current-month" class="text-lg font-medium text-black"></span>
                                <button id="next-month"
                                    class="p-2 rounded-full hover:bg-gray-100">
                                    <span class="material-icons-outlined">chevron_right</span>
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

                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-black">Catatan Meeting</h3>
                            <button id="refresh-notes"
                                class="p-2 rounded-full hover:bg-gray-100">
                                <span class="material-icons-outlined">refresh</span>
                            </button>
                        </div>
                        <div id="meeting-notes-container" class="space-y-3 max-h-96 overflow-y-auto">
                            <div class="text-center py-8 text-gray-600">
                                <span class="material-icons-outlined text-4xl">event_note</span>
                                <p class="mt-2">Tidak ada catatan pada tanggal ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-xl p-3 sm:p-8 border border-gray-200 shadow-card mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-black">Pengumuman</h3>
                    <button id="refresh-announcements"
                        class="p-2 rounded-full hover:bg-gray-100">
                        <span class="material-icons-outlined">refresh</span>
                    </button>
                </div>
                <div id="announcements-container" class="space-y-3 max-h-96 overflow-y-auto">
                    <div class="text-center py-8 text-gray-600">
                        <span class="material-icons-outlined text-4xl">campaign</span>
                        <p class="mt-2">Tidak ada pengumuman</p>
                    </div>
                </div>
            </section>

            <footer class="text-center p-4 bg-gray-100 text-gray-600 text-sm border-t border-gray-200">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <script>
        // CSRF Token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- VARIABEL GLOBAL ---
       
  

        let currentDate = new Date();
        let selectedDate = null;
        let highlightedDates = [];
        let announcementDates = [];

        // --- FUNGSI API ---
        async function apiFetch(endpoint, options = {}) {
            const cacheBuster = `_t=${Date.now()}`;
            const url = `/general_manager/api${endpoint}${endpoint.includes('?') ? '&' : '?'}${cacheBuster}`;
            const defaultOptions = { headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': window.csrfToken } };
            const finalOptions = { ...defaultOptions, ...options };

            const response = await fetch(url, finalOptions);
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Server Error');
            }
            return await response.json();
        }

        // --- FUNGSI KALENDER (YANG SUDAH DIPERBAIKI) ---
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
            dayElement.className = 'calendar-day p-2 text-center rounded hover:bg-gray-100 cursor-pointer'; // Tambahkan style hover dan cursor
            dayElement.textContent = day;
            
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            
            // PERUBAHAN PENTING: Buat semua tanggal bisa diklik
            dayElement.addEventListener('click', () => selectDate(dateStr));
            
            // Cek apakah tanggal ini memiliki event
            const hasMeeting = highlightedDates.includes(dateStr);
            const hasAnnouncement = announcementDates.includes(dateStr);

            if (hasMeeting || hasAnnouncement) {
                dayElement.classList.add('has-event', 'font-bold', 'bg-blue-50'); // Tambahkan visual cue
                
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
        console.log('Tanggal dipilih:', dateStr); // Tambahkan log untuk debugging
        selectedDate = dateStr;
        renderCalendar(); // Render ulang untuk menandai tanggal yang dipilih
        loadMeetingNotes(dateStr);
        loadAnnouncements(dateStr);
    }

    // --- FUNGSI PEMANGGIL DATA ---
    async function loadHighlightedDates() {
        try {
            const dates = await apiFetch('/meeting-notes-dates');
            highlightedDates = dates.map(date => new Date(date).toISOString().split('T')[0]);
            console.log('Tanggal meeting berhasil dimuat:', highlightedDates);
            renderCalendar();
        } catch (error) { console.error('Gagal load tanggal meeting:', error); }
    }
    async function loadAnnouncementDates() {
        try {
            const dates = await apiFetch('/announcements-dates');
            announcementDates = dates.map(date => new Date(date).toISOString().split('T')[0]);
            console.log('Tanggal pengumuman berhasil dimuat:', announcementDates);
            renderCalendar();
        } catch (error) { console.error('Gagal load tanggal pengumuman:', error); }
    }
    
    async function loadMeetingNotes(date) {
        const container = document.getElementById('meeting-notes-container');
        container.innerHTML = '<p class="text-center text-gray-500">Memuat...</p>';
        try {
            const response = await apiFetch(`/meeting-notes?date=${encodeURIComponent(date)}`);
            const notes = response.data || [];
            
            if (!Array.isArray(notes) || notes.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-600">
                        <span class="material-icons-outlined text-4xl">event_note</span>
                        <p class="mt-2">Tidak ada catatan pada tanggal ini</p>
                        <p class="text-xs mt-1">Tanggal: ${date}</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = notes.map(note => `
                <div class="bg-gray-50 p-4 rounded-lg">
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
                    <span class="material-icons-outlined text-4xl">error</span>
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
                    const rawDate = a.tanggal || a.created_at || a.tanggal_indo;
                    const dateOnly = normalizeDate(rawDate);
                    return dateOnly === selectedDate;
                });
            }
            
            if (announcements.length === 0) {
                const msg = selectedDate ? 'Tidak ada pengumuman pada tanggal ini' : 'Tidak ada pengumuman';
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-600">
                        <span class="material-icons-outlined text-4xl">campaign</span>
                        <p class="mt-2">${msg}</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = announcements.map(announcement => `
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold text-black">${announcement.judul || 'Tanpa Judul'}</h4>
                        <span class="text-xs text-gray-600">${announcement.tanggal_indo || new Date(announcement.created_at).toLocaleDateString('id-ID')}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">${announcement.ringkasan || announcement.isi_pesan || 'Tidak ada pesan'}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Oleh: ${announcement.creator || 'System'}</span>
                        ${announcement.lampiran_url ? `<a href="${announcement.lampiran_url}" target="_blank" class="text-xs text-primary hover:underline">Lihat Lampiran</a>` : ''}
                    </div>
                </div>
            `).join('');
            
        } catch (error) {
            console.error('Error loading announcements:', error);
            container.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <span class="material-icons-outlined text-4xl">error</span>
                    <p class="mt-2">Gagal memuat pengumuman</p>
                    <p class="text-xs mt-1">${error.message}</p>
                </div>
            `;
        }
    }

    // --- EVENT LISTENERS ---
    document.getElementById('prev-month')?.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
    document.getElementById('next-month')?.addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
    document.getElementById('refresh-notes')?.addEventListener('click', () => { if (selectedDate) loadMeetingNotes(selectedDate); });
    document.getElementById('refresh-announcements')?.addEventListener('click', () => loadAnnouncements(selectedDate));

    // --- INISIALISASI UTAMA ---
    document.addEventListener('DOMContentLoaded', async () => {
        console.log('Inisialisasi dimulai...');
        renderCalendar(); // Panggil renderCalendar() dulu
        await loadHighlightedDates();
        await loadAnnouncementDates();
        await loadAnnouncements(selectedDate);
        
        const today = new Date();
        const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        selectDate(todayStr);
        console.log('Inisialisasi selesai.');
    });
</script>
</body>

</html>