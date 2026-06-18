<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Beranda (Home) - Manager Divisi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }

        /* Layout Fix - SIDEBAR TIDAK KETUMPUK */
        .app-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            width: calc(100% - 260px);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Card hover effects */
        .stat-card {
            transition: all 0.3s ease;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Calendar styles */
        .calendar-day {
            transition: all 0.2s ease;
            position: relative;
            color: #1e293b;
            min-height: 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        .calendar-day:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        .calendar-day.has-event {
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
        .calendar-day.inactive {
            color: #94a3b8;
            opacity: 0.5;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>

<body class="font-display">
    <div class="app-container">
        <!-- Sidebar -->
        @include('manager_divisi.templet.sider')

        <main class="main-content">
            <div class="p-4 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8 text-slate-800">Beranda</h2>

                <!-- Stat Cards Grid -->
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-6 sm:mb-8">
                    <!-- Belum Dikerjakan Card -->
                    <div class="stat-card rounded-xl p-3 sm:p-5 flex flex-col items-center justify-center text-center">
                        <div class="icon-container w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg mb-2 flex items-center justify-center">
                            <span class="material-icons-outlined text-blue-600 text-sm sm:text-base">task_alt</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-slate-500 truncate">Belum Dikerjakan</p>
                            <p id="stat-pending" class="value-text text-base sm:text-xl font-bold truncate text-slate-800">0</p>
                        </div>
                    </div>

                    <!-- Tugas Dikerjakan Card -->
                    <div class="stat-card rounded-xl p-3 sm:p-5 flex flex-col items-center justify-center text-center">
                        <div class="icon-container w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg mb-2 flex items-center justify-center">
                            <span class="material-icons-outlined text-yellow-600 text-sm sm:text-base">assignment_turned_in</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-slate-500 truncate">Tugas Dikerjakan</p>
                            <p id="stat-inprogress" class="value-text text-base sm:text-xl font-bold truncate text-slate-800">0</p>
                        </div>
                    </div>

                    <!-- Tugas Selesai Card -->
                    <div class="stat-card rounded-xl p-3 sm:p-5 flex flex-col items-center justify-center text-center">
                        <div class="icon-container w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg mb-2 flex items-center justify-center">
                            <span class="material-icons-outlined text-green-600 text-sm sm:text-base">check_circle</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-slate-500 truncate">Tugas Selesai</p>
                            <p id="stat-completed" class="value-text text-base sm:text-xl font-bold truncate text-slate-800">0</p>
                        </div>
                    </div>

                    <!-- Total Tugas Card -->
                    <div class="stat-card rounded-xl p-3 sm:p-5 flex flex-col items-center justify-center text-center">
                        <div class="icon-container w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg mb-2 flex items-center justify-center">
                            <span class="material-icons-outlined text-purple-600 text-sm sm:text-base">summarize</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-slate-500 truncate">Total Tugas</p>
                            <p id="stat-total" class="value-text text-base sm:text-xl font-bold truncate text-slate-800">0</p>
                        </div>
                    </div>
                </div>

                <!-- Calendar and Meeting Notes Section -->
                <section class="bg-white rounded-xl p-3 sm:p-8 border border-slate-200 shadow-card mt-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Calendar Section -->
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold text-black">Kalender</h3>
                                <div class="flex items-center space-x-2">
                                    <button id="prev-month" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                        <span class="material-icons-outlined text-sm">chevron_left</span>
                                    </button>
                                    <span id="current-month" class="text-lg font-medium text-black"></span>
                                    <button id="next-month" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                        <span class="material-icons-outlined text-sm">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-600 mb-2">
                                <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
                            </div>
                            <div id="calendar-days" class="grid grid-cols-7 gap-1"></div>
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
                                <h3 class="text-xl font-semibold text-black">Catatan Meeting</h3>
                                <button id="refresh-notes" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                    <span class="material-icons-outlined text-sm">refresh</span>
                                </button>
                            </div>
                            <div id="meeting-notes-container" class="space-y-3 max-h-96 overflow-y-auto">
                                <div class="text-center py-8 text-gray-500">
                                    <span class="material-icons-outlined text-4xl">event_note</span>
                                    <p class="mt-2">Pilih tanggal untuk melihat catatan meeting</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Announcements Section -->
                <section class="bg-white rounded-xl p-3 sm:p-8 border border-slate-200 shadow-card mt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-black">Pengumuman</h3>
                        <button id="refresh-announcements" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                            <span class="material-icons-outlined text-sm">refresh</span>
                        </button>
                    </div>
                    <div id="announcements-container" class="space-y-3 max-h-96 overflow-y-auto">
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-icons-outlined text-4xl">campaign</span>
                            <p class="mt-2">Tidak ada pengumuman</p>
                        </div>
                    </div>
                </section>
                
                <footer class="text-center p-4 mt-6 text-sm text-slate-500 border-t border-slate-200">
                    Copyright ©2025 by digicity.id
                </footer>
            </div>
        </main>
    </div>

    <script>
        // --- VARIABEL GLOBAL UNTUK KALENDER ---
        let managerCurrentDate = new Date();
        let managerSelectedDate = null;
        let managerHighlightedDates = [];
        let managerAnnouncementDates = [];

        // --- FUNGSI API UNTUK MANAGER DIVISI ---
        async function managerApiFetch(endpoint, options = {}) {
            const cacheBuster = `_t=${Date.now()}`;
            const url = `/api/manager-divisi${endpoint}${endpoint.includes('?') ? '&' : '?'}${cacheBuster}`;

            const tokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!tokenElement) {
                console.error("CSRF token meta tag not found!");
                throw new Error("CSRF token not found");
            }

            const defaultOptions = {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': tokenElement.getAttribute('content')
                }
            };
            const finalOptions = { ...defaultOptions, ...options };

            const response = await fetch(url, finalOptions);
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Server Error');
            }
            return response.json();
        }

        // --- LOAD MANAGER STATISTICS FOR CARDS ---
        async function loadManagerStats() {
            try {
                const res = await managerApiFetch('/tasks/statistics');
                if (res && res.data) {
                    const stats = res.data;
                    document.getElementById('stat-pending').innerText = stats.pending ?? 0;
                    document.getElementById('stat-inprogress').innerText = stats.in_progress ?? 0;
                    document.getElementById('stat-completed').innerText = stats.completed ?? 0;
                    document.getElementById('stat-total').innerText = stats.total ?? 0;
                }
            } catch (err) {
                console.error('Gagal load statistik tugas:', err);
            }
        }

        // --- FUNGSI KALENDER ---
        function renderManagerCalendar() {
            const year = managerCurrentDate.getFullYear();
            const month = managerCurrentDate.getMonth();
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            
            const monthElement = document.getElementById('current-month');
            const calendarDays = document.getElementById('calendar-days');
            if (!monthElement || !calendarDays) return;

            monthElement.textContent = `${monthNames[month]} ${year}`;
            calendarDays.innerHTML = '';

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                calendarDays.appendChild(emptyDay);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day p-2 text-center rounded cursor-pointer';
                dayElement.textContent = day;
                
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                dayElement.addEventListener('click', () => selectManagerDate(dateStr));
                
                const hasMeeting = managerHighlightedDates.includes(dateStr);
                const hasAnnouncement = managerAnnouncementDates.includes(dateStr);

                if (hasMeeting || hasAnnouncement) {
                    dayElement.classList.add('has-event', 'font-bold');
                    const indicatorsContainer = document.createElement('div');
                    indicatorsContainer.className = 'indicators-container absolute bottom-1 flex gap-0.5';
                    if (hasMeeting) {
                        const indicator = document.createElement('div');
                        indicator.className = 'event-indicator relative';
                        indicatorsContainer.appendChild(indicator);
                    }
                    if (hasAnnouncement) {
                        const indicator = document.createElement('div');
                        indicator.className = 'announcement-indicator relative';
                        indicatorsContainer.appendChild(indicator);
                    }
                    dayElement.appendChild(indicatorsContainer);
                }
                
                if (managerSelectedDate === dateStr) {
                    dayElement.classList.add('selected');
                }
                calendarDays.appendChild(dayElement);
            }
        }

        function selectManagerDate(dateStr) {
            managerSelectedDate = dateStr;
            renderManagerCalendar();
            loadManagerMeetingNotes(dateStr);
            loadManagerAnnouncements(dateStr);
        }

        async function loadManagerHighlightedDates() {
            try {
                const data = await managerApiFetch('/meeting-notes-dates');
                const dates = Array.isArray(data) ? data : (data.dates || data.data || []);
                managerHighlightedDates = dates.map(d => {
                    try {
                        return new Date(d).toISOString().split('T')[0];
                    } catch(e) { return null; }
                }).filter(Boolean);
                renderManagerCalendar();
            } catch (error) { console.error('Gagal load tanggal meeting:', error); }
        }

        async function loadManagerAnnouncementDates() {
            try {
                const data = await managerApiFetch('/announcements-dates');
                const dates = Array.isArray(data) ? data : (data.dates || data.data || []);
                managerAnnouncementDates = dates.map(d => {
                    try {
                        return new Date(d).toISOString().split('T')[0];
                    } catch(e) { return null; }
                }).filter(Boolean);
                renderManagerCalendar();
            } catch (error) { console.error('Gagal load tanggal pengumuman:', error); }
        }

        async function loadManagerMeetingNotes(date) {
            const container = document.getElementById('meeting-notes-container');
            container.innerHTML = '<div class="text-center py-8"><span class="material-icons-outlined text-4xl animate-spin">refresh</span><p>Memuat...</p></div>';
            try {
                const response = await managerApiFetch(`/meeting-notes?date=${encodeURIComponent(date)}`);
                const notes = response.data || response;
                
                if (!notes || notes.length === 0) {
                    container.innerHTML = `<div class="text-center py-8 text-gray-500"><span class="material-icons-outlined text-4xl">event_note</span><p>Tidak ada catatan meeting</p></div>`;
                    return;
                }
                
                container.innerHTML = notes.map(note => `
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h4 class="font-semibold text-black mb-2">${note.topik || 'Tanpa Topik'}</h4>
                        <div class="text-sm text-gray-600 space-y-2">
                            <div><span class="font-medium">Hasil Diskusi:</span><p class="mt-1">${note.hasil_diskusi || '-'}</p></div>
                            <div><span class="font-medium">Keputusan:</span><p class="mt-1">${note.keputusan || '-'}</p></div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading meeting notes:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><span class="material-icons-outlined text-4xl">error</span><p>Gagal memuat catatan meeting</p></div>`;
            }
        }

        async function loadManagerAnnouncements(selectedDate = null) {
            const container = document.getElementById('announcements-container');
            container.innerHTML = '<div class="text-center py-8"><span class="material-icons-outlined text-4xl animate-spin">refresh</span><p>Memuat...</p></div>';
            try {
                const response = await managerApiFetch('/announcements');
                let announcements = response.data || response;
                
                if (selectedDate) {
                    announcements = announcements.filter(a => {
                        const dateOnly = a.tanggal || a.created_at?.split('T')[0];
                        return dateOnly === selectedDate;
                    });
                }
                
                if (!announcements || announcements.length === 0) {
                    container.innerHTML = `<div class="text-center py-8 text-gray-500"><span class="material-icons-outlined text-4xl">campaign</span><p>Tidak ada pengumuman</p></div>`;
                    return;
                }
                
                container.innerHTML = announcements.map(announcement => `
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-black">${announcement.judul || 'Tanpa Judul'}</h4>
                            <span class="text-xs text-gray-500">${announcement.tanggal_indo || (announcement.created_at ? new Date(announcement.created_at).toLocaleDateString('id-ID') : '-')}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${announcement.isi_pesan || announcement.ringkasan || 'Tidak ada pesan'}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Oleh: ${announcement.creator || 'System'}</span>
                            ${announcement.lampiran_url ? `<a href="${announcement.lampiran_url}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Lampiran</a>` : ''}
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading announcements:', error);
                container.innerHTML = `<div class="text-center py-8 text-red-500"><span class="material-icons-outlined text-4xl">error</span><p>Gagal memuat pengumuman</p></div>`;
            }
        }

        // --- EVENT LISTENERS ---
        document.getElementById('prev-month')?.addEventListener('click', () => { 
            managerCurrentDate.setMonth(managerCurrentDate.getMonth() - 1); 
            renderManagerCalendar(); 
        });
        document.getElementById('next-month')?.addEventListener('click', () => { 
            managerCurrentDate.setMonth(managerCurrentDate.getMonth() + 1); 
            renderManagerCalendar(); 
        });
        document.getElementById('refresh-notes')?.addEventListener('click', () => { 
            if (managerSelectedDate) loadManagerMeetingNotes(managerSelectedDate); 
        });
        document.getElementById('refresh-announcements')?.addEventListener('click', () => { 
            if (managerSelectedDate) loadManagerAnnouncements(managerSelectedDate); 
        });

        // --- INISIALISASI ---
        document.addEventListener('DOMContentLoaded', function () {
            renderManagerCalendar();
            loadManagerHighlightedDates();
            loadManagerAnnouncementDates();
            loadManagerStats();
            
            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            selectManagerDate(todayStr);
        });
    </script>
</body>
</html>