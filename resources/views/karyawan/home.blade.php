<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Employee Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1e293b",
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
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }

        /* --- ENTRANCE ANIMATION --- */
        .reveal-on-load {
            opacity: 0;
            transform: translateY(30px);
            will-change: opacity, transform;
        }

        .reveal-active {
            animation: elegantEntrance 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        @keyframes elegantEntrance {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- CUSTOM UI ELEMENTS --- */
        
        /* Glass effect for Hero */
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Card Style */
        .modern-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modern-card:hover {
            transform: translateY(-5px);
        }

        /* Calendar Grid */
        .calendar-day {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .calendar-day:hover:not(.empty) {
            background-color: rgba(59, 130, 246, 0.08);
            border-radius: 0.5rem;
        }
        .calendar-day.selected {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
            font-weight: 600;
        }
        
        /* Button Gradient */
        .btn-primary-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }
        .btn-primary-gradient:hover {
            box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.5);
            transform: translateY(-2px);
        }

        /* List Item Indicator */
        .list-indicator {
            position: relative;
        }
        .list-indicator::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 0 4px 4px 0;
        }
        .indicator-blue::before { background-color: #3b82f6; }
        .indicator-purple::before { background-color: #8b5cf6; }
        .indicator-yellow::before { background-color: #f59e0b; }
        .indicator-red::before { background-color: #ef4444; }

        /* Scrollbar styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #475569;
        }

        /* Line clamp for announcement text */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-gray-700 dark:text-gray-300 font-display">
    <div class="flex flex-col min-h-screen p-4 sm:p-6 lg:p-8">
        @include('karyawan.templet.header')

        <main class="flex-grow my-8 max-w-7xl mx-auto w-full">
            <!-- Hero Section with Glassmorphism -->
            <section class="reveal-on-load glass-panel dark:bg-surface-dark/80 rounded-3xl p-8 sm:p-12 lg:p-16 shadow-soft relative overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-blue-400/10 dark:bg-blue-400/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

                <div class="max-w-4xl mx-auto relative z-10">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-2 tracking-tight">HALLO,
                        <span id="employee-name" class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600">{{ Auth::user()->name ?? 'Karyawan' }}</span>
                    </h2>

                    <div class="flex items-center mb-6">
                        <span
                            class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ring-1 ring-blue-200 dark:ring-blue-800">
                            <span class="material-symbols-outlined text-sm mr-1.5">business</span>
                            Divisi {{ $user_divisi ?? optional(Auth::user()->divisi)->divisi ?? 'Tidak Diketahui' }}
                        </span>
                    </div>

                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-10 leading-relaxed max-w-2xl">
                        @if($user_role === 'general_manager')
                            Selamat datang di Dashboard General Manager. Kelola tim dan pantau kinerja perusahaan dari sini.
                        @elseif($user_role === 'manager')
                            Selamat datang di Dashboard Manajer. Pantau tim divisi {{ $user_divisi ?? optional(Auth::user()->divisi)->divisi ?? 'Tidak Diketahui' }} dan kelola tugas mereka.
                        @else
                            Bisnis digital agency adalah perusahaan yang membantu bisnis lain memasarkan produk atau jasanya secara online melalui berbagai layanan digital.
                        @endif
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="/karyawan/absensi"
                            class="btn-primary-gradient text-white px-8 py-3.5 rounded-xl font-semibold shadow-lg shadow-blue-500/30 inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-[1.25rem]">fingerprint</span>
                            Presensi Karyawan
                        </a>

                        @if($user_role === 'general_manager')
                            <a href="/pegawai"
                                class="bg-white dark:bg-surface-dark text-gray-800 dark:text-gray-200 px-8 py-3.5 rounded-xl font-semibold shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-[1.25rem] text-green-600">manage_accounts</span>
                                Kelola Karyawan
                            </a>
                        @endif

                        @if($user_role === 'manager' || $user_role === 'general_manager')
                            <a href="/tugas"
                                class="bg-white dark:bg-surface-dark text-gray-800 dark:text-gray-200 px-8 py-3.5 rounded-xl font-semibold shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-[1.25rem] text-purple-600">checklist</span>
                                Kelola Tugas
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Stats Grid -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
                <!-- Status Presensi -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-blue-100 dark:ring-blue-800">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">person</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Presensi</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="attendance-status">{{ $attendance_status }}</p>
                    </div>
                </div>

                <!-- Total Hadir -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-blue-100 dark:ring-blue-800">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Hadir</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="total-hadir">{{ $total_hadir }}</p>
                    </div>
                </div>

                <!-- Total Terlambat -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-red-50 dark:bg-red-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-red-100 dark:ring-red-800">
                        <span class="material-symbols-outlined text-red-600 dark:text-red-400">schedule</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Terlambat</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="total-terlambat">{{ $total_terlambat }}</p>
                    </div>
                </div>

                <!-- Total Sakit -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-orange-50 dark:bg-orange-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-orange-100 dark:ring-orange-800">
                        <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">sick</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sakit</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="total-sakit">{{ $total_sakit }}</p>
                    </div>
                </div>

                <!-- Total Izin -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-yellow-100 dark:ring-yellow-800">
                        <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">event_available</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Izin</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="total-izin">{{ $total_izin }}</p>
                    </div>
                </div>

                <!-- Total Cuti -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-green-50 dark:bg-green-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-green-100 dark:ring-green-800">
                        <span class="material-symbols-outlined text-green-600 dark:text-green-400">beach_access</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cuti</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="total-cuti">{{ $total_cuti }}</p>
                    </div>
                </div>

                <!-- Jumlah Tugas -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-purple-50 dark:bg-purple-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-purple-100 dark:ring-purple-800">
                        <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">assignment</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Tugas</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="tugas-count">{{ $tugas_count }}</p>
                    </div>
                </div>

                <!-- Penanggung Jawab Project -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-start gap-4">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-indigo-100 dark:ring-indigo-800">
                        <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">account_circle</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penanggung Jawab Project</p>
                        @if(($penanggung_project_count ?? 0) > 0)
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">Ya</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $penanggung_project_count }} project</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Aktif: {{ $penanggung_project_aktif_count ?? 0 }} | Berjalan: {{ $penanggung_project_berjalan_count ?? 0 }}
                            </p>
                            @if(($penanggung_projects_preview ?? collect())->isNotEmpty())
                                <div class="mt-2 space-y-1">
                                    @foreach(($penanggung_projects_preview ?? collect()) as $projectPreview)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 truncate" title="{{ $projectPreview->nama }}">
                                            {{ \Illuminate\Support\Str::limit($projectPreview->nama, 24) }}
                                            ({{ (int) ($projectPreview->progres ?? 0) }}%)
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                            <button
                                type="button"
                                id="openProjectDetail"
                                class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200"
                            >
                                <span class="material-symbols-outlined text-base">open_in_new</span>
                                Lihat Detail
                            </button>
                        @else
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">Tidak</p>
                        @endif
                    </div>
                </div>

                <!-- Total Presensi -->
                <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center gap-4">
                    <div class="bg-gray-100 dark:bg-gray-700 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ring-1 ring-gray-200 dark:ring-gray-600">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">event_busy</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Presensi</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1" id="total-absen">Memuat...</p>
                    </div>
                </div>
            </section>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">
                <!-- Calendar Section -->
                <section class="reveal-on-load bg-white dark:bg-surface-dark rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-lg text-sm">calendar_month</span>
                            Kalender
                        </h3>
                        <div class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-800/50 p-1 rounded-lg">
                            <button id="prev-month" class="p-2 rounded-md hover:bg-white dark:hover:bg-gray-700 shadow-sm transition-all">
                                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300">chevron_left</span>
                            </button>
                            <span id="current-month" class="text-sm font-semibold text-gray-800 dark:text-gray-200 w-32 text-center"></span>
                            <button id="next-month" class="p-2 rounded-md hover:bg-white dark:hover:bg-gray-700 shadow-sm transition-all">
                                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300">chevron_right</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                        <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
                    </div>
                    <div id="calendar-days" class="grid grid-cols-7 gap-1">
                        <!-- JS Generated -->
                    </div>
                    
                    <div class="flex justify-center mt-6 gap-6 text-xs font-medium text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-sm shadow-blue-500/50"></div>
                            <span>Meeting</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></div>
                            <span>Pengumuman</span>
                        </div>
                    </div>
                </section>

                <!-- Meeting Notes Section -->
                <section class="reveal-on-load bg-white dark:bg-surface-dark rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 p-6 flex flex-col h-[450px]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-500 bg-purple-100 dark:bg-purple-900/30 p-1.5 rounded-lg text-sm">description</span>
                            Catatan Meeting
                        </h3>
                        <button id="refresh-notes" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-gray-500">
                            <span class="material-symbols-outlined">refresh</span>
                        </button>
                    </div>
                    <div id="meeting-notes-container" class="space-y-4 overflow-y-auto pr-2 custom-scrollbar flex-grow">
                        <div class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                            <span class="material-symbols-outlined text-5xl mb-2 opacity-50">event_note</span>
                            <p class="text-sm">Pilih tanggal untuk melihat catatan meeting</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Announcements Section - DIPERBAIKI -->
            <section class="reveal-on-load bg-white dark:bg-surface-dark rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 p-6 mt-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 bg-amber-100 dark:bg-amber-900/30 p-1.5 rounded-lg text-sm">campaign</span>
                        Pengumuman Terbaru
                    </h3>
                    <button id="refresh-announcements" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-gray-500">
                        <span class="material-symbols-outlined">refresh</span>
                    </button>
                </div>
                <div id="announcements-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($announcements as $announcement)
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-5 rounded-xl list-indicator indicator-yellow hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300 border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-bold text-gray-900 dark:text-white text-lg leading-tight">{{ $announcement->judul ?? 'Tanpa Judul' }}</h4>
                                <span class="text-xs font-medium bg-white dark:bg-gray-700 px-2.5 py-1 rounded-full border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400">
                                    {{ is_object($announcement) && $announcement->created_at ? $announcement->created_at->format('d M Y') : (is_array($announcement) && isset($announcement['created_at']) ? date('d M Y', strtotime($announcement['created_at'])) : '-') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                {{ is_object($announcement) ? ($announcement->ringkasan ?? $announcement->isi_pesan ?? 'Tidak ada pesan') : (isset($announcement['isi_pesan']) ? $announcement['isi_pesan'] : (isset($announcement['ringkasan']) ? $announcement['ringkasan'] : 'Tidak ada pesan')) }}
                            </p>
                            <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-3 mt-auto">
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="material-symbols-outlined text-sm">person</span>
                                    {{ is_object($announcement) ? ($announcement->creator ?? 'System') : (isset($announcement['creator']) ? $announcement['creator'] : 'System') }}
                                </div>
                                @php
                                    $lampiranUrl = is_object($announcement) ? ($announcement->lampiran_url ?? null) : (isset($announcement['lampiran_url']) ? $announcement['lampiran_url'] : null);
                                @endphp
                                @if($lampiranUrl)
                                    <a href="{{ $lampiranUrl }}" target="_blank" class="text-xs font-medium text-primary hover:text-blue-700 dark:hover:text-blue-400 transition-colors flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">attach_file</span>
                                        Lampiran
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full h-32 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                            <span class="material-symbols-outlined text-5xl mb-2 opacity-50">campaign</span>
                            <p class="text-sm">Tidak ada pengumuman</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Role-specific Cards -->
            @if($user_role === 'general_manager')
                <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8 reveal-on-load">
                    <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center space-x-4">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-800">
                            <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">groups</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Karyawan</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $role_based_data['totalKaryawan'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center space-x-4">
                        <div class="bg-teal-50 dark:bg-teal-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-teal-100 dark:ring-teal-800">
                            <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">business</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Divisi</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $role_based_data['totalDivisi'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center space-x-4">
                        <div class="bg-orange-50 dark:bg-orange-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-orange-100 dark:ring-orange-800">
                            <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">pending_actions</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu Persetujuan</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $role_based_data['pendingApprovals'] ?? 0 }}</p>
                        </div>
                    </div>
                </section>
            @elseif($user_role === 'manager')
                <section class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-8 reveal-on-load">
                    <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center space-x-4">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-800">
                            <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">groups</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Anggota Tim</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $role_based_data['teamMembers'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="modern-card bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700/50 flex items-center space-x-4">
                        <div class="bg-orange-50 dark:bg-orange-900/20 w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ring-1 ring-orange-100 dark:ring-orange-800">
                            <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">pending_actions</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tim Menunggu Persetujuan</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $role_based_data['teamPendingApprovals'] ?? 0 }}</p>
                        </div>
                    </div>
                </section>
            @endif
        </main>
        
        <footer class="max-w-7xl mx-auto w-full mt-12 text-center text-sm text-gray-400 dark:text-gray-600 pb-4">
            <p>Copyright ©2025 by digicity.id</p>
        </footer>
    </div>

    <!-- Modal Detail Project -->
    <div id="projectDetailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-surface-dark w-full max-w-3xl mx-4 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700/50">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white">Project yang Ditanggung Jawab</h4>
                <button id="closeProjectDetail" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-5">
                <div id="projectDetailLoading" class="text-sm text-gray-500">Memuat data...</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm" id="projectDetailTable">
                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700/50">
                                <th class="py-2 pr-3">Nama</th>
                                <th class="py-2 pr-3">Deskripsi</th>
                                <th class="py-2 pr-3">Periode</th>
                                <th class="py-2 pr-3">Status</th>
                            </table>
                        </thead>
                        <tbody id="projectDetailBody"></tbody>
                    </table>
                </div>
                <div id="projectDetailEmpty" class="hidden text-sm text-gray-500 py-4">Tidak ada project.</div>
            </div>
        </div>
    </div>

    <script>
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- ANIMASI MASUK (ENTRANCE ANIMATION) ---
        document.addEventListener('DOMContentLoaded', () => {
            const revealElements = document.querySelectorAll('.reveal-on-load');
            
            revealElements.forEach((element, index) => {
                const delay = index * 70; 
                setTimeout(() => {
                    element.classList.add('reveal-active');
                }, delay);
            });
            
            // Set total absen
            const totalAbsenEl = document.getElementById('total-absen');
            if (totalAbsenEl) {
                const totalHadir = parseInt(document.getElementById('total-hadir')?.innerText || '0');
                const totalSakit = parseInt(document.getElementById('total-sakit')?.innerText || '0');
                const totalIzin = parseInt(document.getElementById('total-izin')?.innerText || '0');
                const totalCuti = parseInt(document.getElementById('total-cuti')?.innerText || '0');
                const total = totalHadir + totalSakit + totalIzin + totalCuti;
                totalAbsenEl.innerText = total;
            }
            
            // Initialize calendar
            renderCalendar();
            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            selectDate(todayStr);
        });

        // Helper function to animate numbers
        function animateValue(obj, start, end, duration) {
            if (!obj) return;
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        function extractNumber(str) {
            if (!str) return 0;
            const num = parseInt(str.toString().replace(/\D/g, ''));
            return isNaN(num) ? 0 : num;
        }

        const mobileMenuButton = document.getElementById('mobile-menu-button');
        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', function () {
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu) {
                    mobileMenu.classList.toggle('hidden');
                }
            });
        }

        document.addEventListener('click', function (event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuButton = document.getElementById('mobile-menu-button');

            if (mobileMenu && mobileMenuButton && !mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        function formatAttendanceStatus(status) {
            if (status === 'Belum Absen') return '<span class="text-gray-400 font-medium">Belum Absen</span>';
            if (status === 'Tepat Waktu') return '<span class="text-green-500 font-bold bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-md">Tepat Waktu</span>';
            if (status === 'Terlambat') return '<span class="text-red-500 font-bold bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-md">Terlambat</span>';
            if (['Sakit', 'Izin', 'Dinas Luar', 'Cuti'].includes(status)) return `<span class="text-yellow-600 font-medium">${status}</span>`;
            if (status === 'Lainnya' || status === 'Tidak Hadir') return '<span class="text-gray-500">Tidak Masuk</span>';
            return status;
        }

        async function apiFetch(endpoint, options = {}) {
            const cacheBuster = `_t=${Date.now()}`;
            const url = `/api/karyawan${endpoint}${endpoint.includes('?') ? '&' : '?'}${cacheBuster}`;

            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            };
            const finalOptions = { ...defaultOptions, ...options };

            const response = await fetch(url, finalOptions);
            const responseText = await response.text();
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                throw new Error('Invalid JSON response');
            }

            if (response.status === 419) throw new Error('CSRF token mismatch. Silakan muat ulang halaman.');
            if (!response.ok) {
                throw new Error(data.message || data.error || 'Something went wrong');
            }

            return data;
        }

        // Calendar functionality
        let currentDate = new Date();
        let selectedDate = null;
        let highlightedDates = @json($highlighted_dates ?? []);
        let announcementDates = @json($announcement_dates ?? []);

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const monthEl = document.getElementById('current-month');
            
            if (monthEl) {
                monthEl.style.opacity = '0';
                setTimeout(() => {
                    monthEl.textContent = `${monthNames[month]} ${year}`;
                    monthEl.style.opacity = '1';
                }, 150);
            }

            const calendarDays = document.getElementById('calendar-days');
            if(!calendarDays) return;

            calendarDays.innerHTML = '';

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                calendarDays.appendChild(emptyDay);
            }

            const today = new Date();

            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day p-2.5 text-center rounded-xl text-sm text-gray-700 dark:text-gray-300 font-medium cursor-pointer relative hover:bg-gray-100 dark:hover:bg-gray-700 transition';
                dayElement.textContent = day;

                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                const hasMeeting = highlightedDates.includes(dateStr);
                const hasAnnouncement = announcementDates.includes(dateStr);

                // Tambahkan indikator jika ada meeting/pengumuman
                if (hasMeeting) {
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute bottom-1.5 left-1/2 transform -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-blue-500 shadow-sm';
                    dayElement.appendChild(indicator);
                }
                if (hasAnnouncement) {
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute bottom-1.5 left-1/2 transform -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-amber-500 shadow-sm';
                    if (hasMeeting) indicator.style.left = '60%';
                    dayElement.appendChild(indicator);
                }

                // Semua tanggal bisa diklik
                dayElement.addEventListener('click', function () {
                    selectDate(dateStr);
                });

                if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                    dayElement.classList.add('border', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                }

                if (selectedDate === dateStr) {
                    dayElement.classList.add('selected');
                }

                calendarDays.appendChild(dayElement);
            }
        }

        function selectDate(dateStr) {
            selectedDate = dateStr;
            renderCalendar();
            loadMeetingNotes(dateStr);
            loadAnnouncements(dateStr);
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
// Load meeting notes (langsung dari data Blade, tanpa API)
function loadMeetingNotes(date) {
    const container = document.getElementById('meeting-notes-container');
    if (!container) return;
    
    // Data catatan meeting dari Blade
    let meetingNotes = @json($meetingNotes ?? []);
    
    // Filter berdasarkan tanggal
    if (date && meetingNotes && meetingNotes.length > 0) {
        meetingNotes = meetingNotes.filter(note => {
            let tanggalNote = note.tanggal || note.created_at;
            if (tanggalNote) {
                const dateOnly = normalizeDate(tanggalNote);
                return dateOnly === date;
            }
            return false;
        });
    }
    
    if (!meetingNotes || meetingNotes.length === 0) {
        container.innerHTML = `<div class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
            <span class="material-symbols-outlined text-5xl mb-2 opacity-50">event_note</span>
            <p class="text-sm">Tidak ada catatan meeting pada tanggal ini</p>
        </div>`;
        return;
    }
    
    container.innerHTML = meetingNotes.map(note => `
        <div class="bg-gray-50 dark:bg-gray-800/30 p-4 rounded-xl list-indicator indicator-purple">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">${escapeHtml(note.topik || 'Tanpa Topik')}</h4>
            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                <div><span class="font-medium">Hasil Diskusi:</span><p class="mt-1">${escapeHtml(note.hasil_diskusi || '-')}</p></div>
                <div><span class="font-medium">Keputusan:</span><p class="mt-1">${escapeHtml(note.keputusan || '-')}</p></div>
            </div>
            <div class="mt-2 text-xs text-gray-400">
                📅 ${formatTanggal(note.tanggal || note.created_at)}
            </div>
        </div>
    `).join('');
}

// Load announcements (langsung dari data Blade, tanpa API)
function loadAnnouncements(selectedDate = null) {
    const container = document.getElementById('announcements-container');
    if (!container) return;
    
    // Data pengumuman dari Blade
    let announcements = @json($announcements ?? []);
    
    // Filter berdasarkan tanggal jika ada
    if (selectedDate && announcements && announcements.length > 0) {
        announcements = announcements.filter(a => {
            let tanggal = a.tanggal || a.created_at || a.tanggal_indo;
            if (tanggal) {
                const dateOnly = normalizeDate(tanggal);
                return dateOnly === selectedDate;
            }
            return false;
        });
    }
    
    if (!announcements || announcements.length === 0) {
        const msg = selectedDate ? 'Tidak ada pengumuman pada tanggal ini' : 'Tidak ada pengumuman';
        container.innerHTML = `<div class="col-span-full h-32 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
            <span class="material-symbols-outlined text-5xl mb-2 opacity-50">campaign</span>
            <p class="text-sm">${msg}</p>
        </div>`;
        return;
    }
    
    container.innerHTML = announcements.map(announcement => `
        <div class="bg-gray-50 dark:bg-gray-800/50 p-5 rounded-xl list-indicator indicator-yellow hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300 border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
            <div class="flex justify-between items-start mb-3">
                <h4 class="font-bold text-gray-900 dark:text-white text-lg leading-tight">${escapeHtml(announcement.judul || 'Tanpa Judul')}</h4>
                <span class="text-xs font-medium bg-white dark:bg-gray-700 px-2.5 py-1 rounded-full border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400">
                    ${formatTanggal(announcement.tanggal_indo || announcement.tanggal || announcement.created_at)}
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                ${escapeHtml(announcement.ringkasan || announcement.isi_pesan || 'Tidak ada pesan')}
            </p>
            <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-3 mt-auto">
                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <span class="material-symbols-outlined text-sm">person</span>
                    ${escapeHtml(announcement.creator || announcement.creator_name || 'System')}
                </div>
                ${announcement.lampiran_url ? `<a href="${escapeHtml(announcement.lampiran_url)}" target="_blank" class="text-xs font-medium text-primary hover:text-blue-700 dark:hover:text-blue-400 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">attach_file</span>
                    Lampiran
                </a>` : ''}
            </div>
        </div>
    `).join('');
}

// Helper functions
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function formatTanggal(tanggal) {
    if (!tanggal) return '-';
    try {
        const date = new Date(tanggal);
        if (!isNaN(date.getTime())) {
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }
    } catch(e) {}
    return tanggal;
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

// Helper function untuk escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Helper function format tanggal
function formatTanggal(tanggal) {
    if (!tanggal) return '-';
    try {
        const date = new Date(tanggal);
        if (!isNaN(date.getTime())) {
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }
    } catch(e) {}
    return tanggal;
}
        function formatProjectDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID');
        }

        function renderProjectRows(items) {
            if (!projectBodyEl) return;
            projectBodyEl.innerHTML = '';
            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-100 dark:border-gray-700/50';
                const startDate = item.tanggal_mulai_pengerjaan || item.tanggal_mulai_kerjasama || null;
                const endDate = item.tanggal_selesai_pengerjaan || item.tanggal_selesai_kerjasama || null;
                tr.innerHTML = `
                    <td class="py-2 pr-3 font-medium text-gray-900 dark:text-white">${item.nama}</td>
                    <td class="py-2 pr-3 text-gray-700 dark:text-gray-300">${item.deskripsi || '-'}</td>
                    <td class="py-2 pr-3 text-gray-700 dark:text-gray-300">${formatProjectDate(startDate)} - ${formatProjectDate(endDate)}</td>
                    <td class="py-2 pr-3 text-gray-700 dark:text-gray-300">${item.status_pengerjaan || '-'}</td>
                `;
                projectBodyEl.appendChild(tr);
            });
        }

        async function loadPenanggungProjects() {
            if (!projectLoadingEl || !projectBodyEl || !projectEmptyEl) return;
            projectLoadingEl.classList.remove('hidden');
            projectEmptyEl.classList.add('hidden');
            projectBodyEl.innerHTML = '';

            try {
                const response = await fetch('/karyawan/api/penanggung-projects', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                projectLoadingEl.classList.add('hidden');
                if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                    renderProjectRows(data.data);
                } else {
                    projectEmptyEl.classList.remove('hidden');
                }
            } catch (e) {
                projectLoadingEl.textContent = 'Gagal memuat data.';
            }
        }

        if (projectOpenBtn && projectModal) {
            projectOpenBtn.addEventListener('click', function () {
                projectModal.classList.remove('hidden');
                projectModal.classList.add('flex');
                loadPenanggungProjects();
            });
        }
        if (projectCloseBtn && projectModal) {
            projectCloseBtn.addEventListener('click', function () {
                projectModal.classList.add('hidden');
                projectModal.classList.remove('flex');
            });
        }
        if (projectModal) {
            projectModal.addEventListener('click', function (e) {
                if (e.target === projectModal) {
                    projectModal.classList.add('hidden');
                    projectModal.classList.remove('flex');
                }
            });
        }
    </script>
</body>
</html>