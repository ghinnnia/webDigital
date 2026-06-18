<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tugas Saya - Karyawan</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        /* Dark mode custom properties */
        :root {
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        .dark {
            --bg-page: #0f172a;
            --bg-card: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        /* Nav Link Styles */
        .nav-link {
            color: var(--text-secondary) !important;
            padding: 8px 12px;
            border-radius: 0px;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
            background: transparent !important;
        }

        .nav-link:hover {
            color: #3b82f6 !important;
            background: transparent !important;
            border-bottom: 2px solid #93c5fd;
        }

        .nav-link.active {
            color: #3b82f6 !important;
            background: transparent !important;
            border-bottom: 2px solid #3b82f6;
        }

        .dark .nav-link {
            color: #9ca3af !important;
        }

        .dark .nav-link.active,
        .dark .nav-link:hover {
            color: #60a5fa !important;
            background: transparent !important;
        }

        /* Card Styles */
        .bg-white {
            background-color: var(--bg-card) !important;
        }

        .border {
            border-color: var(--border-color) !important;
        }

        .text-gray-800, .text-gray-700, .text-gray-900 {
            color: var(--text-primary) !important;
        }

        .text-gray-400, .text-gray-500, .text-gray-600 {
            color: var(--text-secondary) !important;
        }

        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .dark .badge-pending {
            background-color: #451a03;
            color: #fcd34d;
        }

        .badge-proses {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .dark .badge-proses {
            background-color: #1e3a5f;
            color: #60a5fa;
        }

        .badge-menunggu {
            background-color: #f3e8ff;
            color: #9333ea;
        }

        .dark .badge-menunggu {
            background-color: #3b0764;
            color: #c084fc;
        }

        .badge-selesai {
            background-color: #d1fae5;
            color: #059669;
        }

        .dark .badge-selesai {
            background-color: #064e3b;
            color: #34d399;
        }

        .badge-expired {
            background-color: #fee2e2;
            color: #dc2626;
            animation: pulse 1s infinite;
        }

        .dark .badge-expired {
            background-color: #7f1d1d;
            color: #fca5a5;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        /* Task Card Styles */
        .task-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .task-card.selected {
            border-left: 4px solid #3b82f6;
            background-color: rgba(59, 130, 246, 0.05);
        }

        .dark .task-card.selected {
            background-color: rgba(59, 130, 246, 0.15);
        }

        .deadline-expired {
            border-left: 4px solid #ef4444;
            background-color: rgba(239, 68, 68, 0.05);
        }

        .dark .deadline-expired {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .deadline-warning {
            animation: blink 0.5s ease-in-out 3;
        }

        @keyframes blink {
            0%, 100% { background-color: var(--bg-card); }
            50% { background-color: rgba(239, 68, 68, 0.2); }
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            background-color: var(--bg-card);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateX(400px);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-success { border-left: 4px solid #10b981; }
        .toast-error { border-left: 4px solid #ef4444; }
        .toast-warning { border-left: 4px solid #f59e0b; }
        .toast-info { border-left: 4px solid #3b82f6; }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            right: 0;
            top: 40px;
            width: 320px;
            background-color: var(--bg-card);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            z-index: 100;
            display: none;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s;
        }

        .notification-item:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .notification-item.unread {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .dark .notification-item.unread {
            background-color: rgba(59, 130, 246, 0.2);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--border-color);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        .dark ::-webkit-scrollbar-track {
            background: #334155;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #5b6e8c;
        }

        /* Form inputs */
        select, input {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        select:focus, input:focus {
            outline: none;
            ring: 2px solid #3b82f6;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-gray-700 dark:text-gray-300 font-display">
    <div class="flex flex-col min-h-screen p-4 sm:p-6 lg:p-8">
        @include('karyawan.templet.header')

        <!-- MAIN CONTENT -->
        <main class="container mx-auto px-6 py-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tugas Saya</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola dan upload tugas yang diberikan kepada Anda</p>
                </div>
            </div>

            <!-- STATISTIK CARDS -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Total Tugas</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white" id="statTotal">{{ $tasks->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="statPending">0</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Proses</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="statProses">0</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Sedang Direview</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="statMenunggu">0</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Selesai</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400" id="statSelesai">0</p>
                </div>
            </div>

            <!-- FILTER -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs text-gray-400 dark:text-gray-500 mb-1">Filter Status</label>
                        <select id="statusFilter" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                            <option value="all">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="proses">Proses</option>
                            <option value="menunggu">Menunggu Review</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-gray-400 dark:text-gray-500 mb-1">Cari Tugas</label>
                        <input type="text" id="searchInput" placeholder="Cari judul tugas..." 
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                    </div>
                </div>
            </div>

            <!-- TASK LIST & DETAIL PANEL -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LIST TUGAS (Card View) -->
                <div class="lg:col-span-2 space-y-4">
                    @forelse($tasks as $task)
                    @php
                        $isDeadlineExpired = false;
                        $deadlineDate = $task->deadline ? \Carbon\Carbon::parse($task->deadline) : null;
                        $today = \Carbon\Carbon::now();
                        $isExpired = $deadlineDate && $deadlineDate->isPast() && $task->status != 'selesai' && $task->status != 'menunggu';
                    @endphp
                    <div class="task-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 task-item {{ $isExpired ? 'deadline-expired' : '' }}"
                         data-id="{{ $task->id }}"
                         data-judul="{{ $task->judul ?? $task->nama_tugas }}"
                         data-deskripsi="{{ $task->deskripsi ?? 'Tidak ada deskripsi' }}"
                         data-deadline="{{ $task->deadline ? $task->deadline->format('d F Y H:i') : '-' }}"
                         data-deadline-raw="{{ $task->deadline ? $task->deadline->toDateString() : '' }}"
                         data-status="{{ $task->status }}"
                         data-submission="{{ $task->submission_file ? asset('storage/' . $task->submission_file) : null }}"
                         data-submitted="{{ $task->submitted_at ? \Carbon\Carbon::parse($task->submitted_at)->format('d F Y H:i') : null }}"
                         data-expired="{{ $isExpired ? 'true' : 'false' }}"
                         onclick="selectTask(this)">
                        
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex gap-2 flex-wrap">
                                <span class="text-xs px-2 py-1 rounded-full {{ $task->created_by_role == 'hr' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' }}">
                                    {{ $task->created_by_role == 'hr' ? 'HRD' : 'Manager' }}
                                </span>
                                @if($task->priority == 'urgent')
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Urgent</span>
                                @elseif($task->priority == 'high')
                                    <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300">Tinggi</span>
                                @endif
                                @if($isExpired)
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-200 animate-pulse">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Lewat Deadline
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 {{ $isExpired ? 'text-red-500 dark:text-red-400 font-semibold' : '' }}">
                                {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : '-' }}
                            </p>
                        </div>
                        
                        <h3 class="font-bold text-gray-800 dark:text-white mb-1 {{ $isExpired ? 'text-red-700 dark:text-red-400' : '' }}">
                            {{ $task->judul ?? $task->nama_tugas }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            {{ Str::limit($task->deskripsi ?? 'Tidak ada deskripsi', 60) }}
                        </p>
                        
                        <div class="flex justify-between items-center">
                            @php
                                $badgeClass = 'badge-pending';
                                $statusText = 'Pending';
                                if ($task->status == 'proses') { $badgeClass = 'badge-proses'; $statusText = 'Proses'; }
                                elseif ($task->status == 'menunggu') { $badgeClass = 'badge-menunggu'; $statusText = 'Menunggu Review'; }
                                elseif ($task->status == 'selesai') { $badgeClass = 'badge-selesai'; $statusText = 'Selesai'; }
                                if ($isExpired && $task->status != 'selesai') { $badgeClass = 'badge-expired'; $statusText = 'Terlambat'; }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                            <button class="text-blue-600 dark:text-blue-400 text-sm flex items-center gap-1" onclick="event.stopPropagation(); showTaskDetail(this.parentElement.parentElement)">
                                <span class="material-icons-outlined text-sm">visibility</span>
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-500">
                        <span class="material-icons-outlined text-5xl mb-2 block">assignment_late</span>
                        <p>Belum ada tugas yang diberikan</p>
                    </div>
                    @endforelse
                </div>

                <!-- DETAIL PANEL -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 h-fit sticky top-6">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-icons-outlined text-blue-500 dark:text-blue-400">info</span>
                        Detail Tugas
                    </h3>
                    <div id="detailPanel">
                        <div class="text-center text-gray-400 dark:text-gray-500 py-8">
                            <span class="material-icons-outlined text-5xl mb-2 block">assignment</span>
                            <p>Pilih tugas dari daftar</p>
                            <p class="text-sm mt-1">Klik "Lihat Detail" pada tugas</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- TOAST NOTIFICATION -->
        <div id="toast" class="toast hidden">
            <div class="flex items-start p-4">
                <div class="flex-shrink-0 mr-3">
                    <span id="toastIcon" class="material-icons-outlined">info</span>
                </div>
                <div class="flex-1">
                    <h4 id="toastTitle" class="font-semibold text-gray-800 dark:text-white">Notifikasi</h4>
                    <p id="toastMessage" class="text-sm text-gray-600 dark:text-gray-400 mt-1"></p>
                </div>
                <button onclick="hideToast()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                    <span class="material-icons-outlined text-sm">close</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function showToast(title, message, type = 'info') {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            
            toast.className = 'toast show';
            if (type === 'success') {
                toast.classList.add('toast-success');
                toastIcon.textContent = 'check_circle';
                toastIcon.className = 'material-icons-outlined text-green-500';
            } else if (type === 'error') {
                toast.classList.add('toast-error');
                toastIcon.textContent = 'error';
                toastIcon.className = 'material-icons-outlined text-red-500';
            } else if (type === 'warning') {
                toast.classList.add('toast-warning');
                toastIcon.textContent = 'warning';
                toastIcon.className = 'material-icons-outlined text-yellow-500';
            } else {
                toast.classList.add('toast-info');
                toastIcon.textContent = 'info';
                toastIcon.className = 'material-icons-outlined text-blue-500';
            }
            
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => hideToast(), 5000);
        }
        
        function hideToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('show');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }
        
        let selectedTaskId = null;
        
        function selectTask(card) {
            document.querySelectorAll('.task-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedTaskId = card.dataset.id;
            
            const isExpired = card.dataset.expired === 'true';
            const isMenunggu = card.dataset.status === 'menunggu';
            const hasSubmission = card.dataset.submission && card.dataset.submission !== 'null';
            
            let statusBadge = '';
            if (card.dataset.status === 'menunggu') {
                statusBadge = '<span class="badge badge-menunggu mt-2 inline-block">⏳ Menunggu Review</span>';
            } else if (card.dataset.status === 'selesai') {
                statusBadge = '<span class="badge badge-selesai mt-2 inline-block">✅ Selesai</span>';
            } else if (isExpired) {
                statusBadge = '<span class="badge badge-expired mt-2 inline-block">❌ Terlambat (Lewat Deadline)</span>';
            }
            
            let deadlineWarning = '';
            if (isExpired && card.dataset.status !== 'selesai') {
                deadlineWarning = `
                    <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-3 mb-3 border border-red-200 dark:border-red-800">
                        <div class="flex items-center gap-2">
                            <span class="material-icons-outlined text-red-500">warning</span>
                            <span class="text-sm font-medium text-red-700 dark:text-red-400">Tugas ini sudah melewati deadline!</span>
                        </div>
                        <p class="text-xs text-red-600 dark:text-red-300 mt-1">Segera selesaikan tugas Anda.</p>
                    </div>
                `;
            }
            
            let terimaButton = '';
            if (card.dataset.status === 'pending') {
                terimaButton = `
                    <div class="pt-3">
                        <button onclick="terimaTugas(${card.dataset.id})"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition">
                            Terima Tugas
                        </button>
                    </div>
                `;
            }
            
            let submissionInfo = '';
            if (hasSubmission) {
                submissionInfo = `
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-3 mt-3">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-icons-outlined text-green-600 dark:text-green-400 text-sm">cloud_done</span>
                            <span class="text-sm font-medium text-green-700 dark:text-green-400">File sudah diupload</span>
                        </div>
                        <a href="${card.dataset.submission}" class="text-blue-600 dark:text-blue-400 text-sm flex items-center gap-1 hover:underline" target="_blank">
                            <span class="material-icons-outlined text-sm">download</span>
                            Lihat File Tugas
                        </a>
                        ${card.dataset.submitted ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Dikumpulkan: ${card.dataset.submitted}</p>` : ''}
                    </div>
                `;
            }
            
            let uploadButton = '';
            if (card.dataset.status !== 'selesai' && card.dataset.status !== 'menunggu') {
                uploadButton = `
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700 mt-3">
                        <a href="{{ route('karyawan.tugas.show', '') }}/${selectedTaskId}" 
                           class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm text-center block hover:bg-blue-700 transition">
                            Upload Tugas
                        </a>
                    </div>
                `;
            }
            
            const html = `
                <div class="space-y-4">
                    ${deadlineWarning}
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Judul Tugas</p>
                        <h4 class="font-bold text-lg ${isExpired ? 'text-red-700 dark:text-red-400' : 'text-gray-800 dark:text-white'}">${escapeHtml(card.dataset.judul)}</h4>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Deskripsi</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">${escapeHtml(card.dataset.deskripsi)}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Deadline</p>
                            <p class="text-sm ${isExpired ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-800 dark:text-white'}">${card.dataset.deadline}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Status</p>
                            ${statusBadge}
                        </div>
                    </div>
                    ${submissionInfo}
                    ${terimaButton}
                    ${uploadButton}
                </div>
            `;
            
            document.getElementById('detailPanel').innerHTML = html;
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function showTaskDetail(card) {
            selectTask(card);
        }
        
        function terimaTugas(taskId) {
            fetch(`/karyawan/tugas/${taskId}/terima`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Berhasil', 'Tugas berhasil diterima', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('Gagal', data.message || 'Tidak bisa menerima tugas', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error', 'Terjadi kesalahan', 'error');
            });
        }
        
        function updateStats() {
            const cards = document.querySelectorAll('.task-item');
            let pending = 0, proses = 0, menunggu = 0, selesai = 0;
            cards.forEach(card => {
                const status = card.dataset.status;
                if (status === 'pending') pending++;
                else if (status === 'proses') proses++;
                else if (status === 'menunggu') menunggu++;
                else if (status === 'selesai') selesai++;
            });
            document.getElementById('statTotal').textContent = cards.length;
            document.getElementById('statPending').textContent = pending;
            document.getElementById('statProses').textContent = proses;
            document.getElementById('statMenunggu').textContent = menunggu;
            document.getElementById('statSelesai').textContent = selesai;
        }
        
        const statusFilter = document.getElementById('statusFilter');
        const searchInput = document.getElementById('searchInput');
        
        function applyFilters() {
            const statusValue = statusFilter.value;
            const searchValue = searchInput.value.toLowerCase();
            const cards = document.querySelectorAll('.task-item');
            cards.forEach(card => {
                const cardStatus = card.dataset.status;
                const cardJudul = card.dataset.judul.toLowerCase();
                const matchesStatus = statusValue === 'all' || cardStatus === statusValue;
                const matchesSearch = searchValue === '' || cardJudul.includes(searchValue);
                card.style.display = matchesStatus && matchesSearch ? '' : 'none';
            });
            updateStats();
        }
        
        statusFilter.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);
        
        // Deadline checking
        const shownDeadlineNotifications = new Set();
        
        function checkDeadlines() {
            const tasks = document.querySelectorAll('.task-item');
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            tasks.forEach(task => {
                const deadlineRaw = task.dataset.deadlineRaw;
                const taskId = task.dataset.id;
                const taskJudul = task.dataset.judul;
                const status = task.dataset.status;
                
                if (deadlineRaw && deadlineRaw !== '') {
                    const deadline = new Date(deadlineRaw);
                    deadline.setHours(0, 0, 0, 0);
                    const diffDays = Math.ceil((deadline - today) / (1000 * 60 * 60 * 24));
                    
                    if (diffDays < 0 && status !== 'selesai' && status !== 'menunggu') {
                        if (!task.classList.contains('deadline-expired')) {
                            task.classList.add('deadline-expired');
                            
                            const badgeSpan = task.querySelector('.badge');
                            if (badgeSpan && !badgeSpan.classList.contains('badge-expired')) {
                                badgeSpan.classList.remove('badge-pending', 'badge-proses');
                                badgeSpan.classList.add('badge-expired');
                                badgeSpan.textContent = 'Terlambat';
                            }
                            
                            const notifKey = `expired_${taskId}`;
                            if (!shownDeadlineNotifications.has(notifKey)) {
                                shownDeadlineNotifications.add(notifKey);
                                showToast('Tugas Terlambat', `Tugas "${taskJudul}" sudah melewati deadline! Segera selesaikan.`, 'error');
                                
                                task.classList.add('deadline-warning');
                                setTimeout(() => {
                                    task.classList.remove('deadline-warning');
                                }, 3000);
                            }
                        }
                    } else if (diffDays === 1 && status !== 'selesai' && status !== 'menunggu') {
                        const notifKey = `reminder_${taskId}`;
                        if (!shownDeadlineNotifications.has(notifKey)) {
                            shownDeadlineNotifications.add(notifKey);
                            showToast('⏰ Pengingat Deadline', `Tugas "${taskJudul}" deadline besok!`, 'warning');
                        }
                    }
                }
            });
            
            updateStats();
        }
        
        setTimeout(checkDeadlines, 500);
        setInterval(checkDeadlines, 3600000);
        
        updateStats();
        
        setInterval(() => {
            fetch('/api/notifications/unread-count')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (data.count > 0) {
                        if (badge) {
                            badge.textContent = data.count > 9 ? '9+' : data.count;
                            badge.style.display = 'flex';
                        }
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(err => console.error('Error:', err));
        }, 30000);
    </script>
</body>
</html>