<!DOCTYPE html>
<html lang="id">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tugas Saya - Karyawan</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">

   <style>
    /* FIX NAV LINK - HANYA GARIS BAWAH SAAT AKTIF */
    .nav-link {
        color: #6b7280 !important; /* Warna teks abu-abu standar */
        padding: 8px 12px;
        border-radius: 0px; /* Hapus radius karena kita pakai garis bawah */
        border-bottom: 2px solid transparent; /* Garis transparan agar tidak goyang saat aktif */
        transition: all 0.3s ease;
        font-weight: 500;
        background: transparent !important; /* Pastikan tidak ada background */
    }

    .nav-link:hover {
        color: #3b82f6 !important; /* Teks jadi biru saat hover */
        background: transparent !important;
        border-bottom: 2px solid #93c5fd; /* Garis bawah biru muda saat hover */
    }

    .nav-link.active {
        color: #3b82f6 !important; /* Teks biru saat aktif */
        background: transparent !important; /* Hapus background biru */
        border-bottom: 2px solid #3b82f6; /* Garis bawah biru tegas */
    }

    /* Dark mode fix */
    .dark .nav-link {
        color: #9ca3af !important;
    }

    .dark .nav-link.active,
    .dark .nav-link:hover {
        color: #60a5fa !important;
        background: transparent !important;
    }
    
    /* ... Sisa CSS lainnya tetap sama ... */
    * { font-family: 'Poppins', sans-serif; }
    /* ... dst ... */
</style>
</head>

<!-- <body class="bg-gray-100"> -->
    <body class="bg-background-light dark:bg-background-dark text-gray-700 dark:text-gray-300 font-display">
    <div class="flex flex-col min-h-screen p-4 sm:p-6 lg:p-8">
 @include('karyawan.templet.header')
<!-- NAVBAR HORIZONTAL (SESUAI GAMBAR) -->
<!-- Script Notifikasi -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi notifikasi
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        if (notificationBell) {
            // Hapus event listener lama (jika ada)
            const newBell = notificationBell.cloneNode(true);
            notificationBell.parentNode.replaceChild(newBell, notificationBell);
            
            // Tambah event listener baru
            newBell.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
                loadNotifications();
            });
        }
        
        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (notificationDropdown && !notificationDropdown.contains(e.target) && e.target !== notificationBell) {
                notificationDropdown.classList.remove('show');
            }
        });
        
        function loadNotifications() {
            const container = document.getElementById('notificationList');
            if (!container) return;
            
            container.innerHTML = '<div class="p-4 text-center text-gray-400">Memuat...</div>';
            
            fetch('/api/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        container.innerHTML = data.notifications.map(n => `
                            <div class="notification-item ${!n.is_read ? 'unread' : ''}" onclick="window.location.href='/notifications'">
                                <div class="flex items-start gap-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">${escapeHtml(n.title)}</p>
                                        <p class="text-xs text-gray-500 mt-1">${escapeHtml(n.message.substring(0, 80))}${n.message.length > 80 ? '...' : ''}</p>
                                        <p class="text-xs text-gray-400 mt-1">${n.time_ago || ''}</p>
                                    </div>
                                    ${!n.is_read ? '<span class="w-2 h-2 bg-blue-500 rounded-full mt-1"></span>' : ''}
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<div class="p-4 text-center text-gray-400">Tidak ada notifikasi</div>';
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    container.innerHTML = '<div class="p-4 text-center text-red-400">Gagal memuat notifikasi</div>';
                });
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function updateBadge() {
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
        }
        
        updateBadge();
        setInterval(updateBadge, 30000);
    });
</script>

<!-- MAIN CONTENT -->
<main class="container mx-auto px-6 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tugas Saya</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan upload tugas yang diberikan kepada Anda</p>
        </div>
    </div>

    <!-- STATISTIK CARDS (DENGAN CARD SEDANG DIREVIEW) -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border">
            <p class="text-gray-400 text-sm">Total Tugas</p>
            <p class="text-2xl font-bold" id="statTotal">{{ $tasks->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border">
            <p class="text-gray-400 text-sm">Pending</p>
            <p class="text-2xl font-bold text-yellow-600" id="statPending">0</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border">
            <p class="text-gray-400 text-sm">Proses</p>
            <p class="text-2xl font-bold text-blue-600" id="statProses">0</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border">
            <p class="text-gray-400 text-sm">Sedang Direview</p>
            <p class="text-2xl font-bold text-purple-600" id="statMenunggu">0</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border">
            <p class="text-gray-400 text-sm">Selesai</p>
            <p class="text-2xl font-bold text-green-600" id="statSelesai">0</p>
        </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs text-gray-400 mb-1">Filter Status</label>
                <select id="statusFilter" class="w-full border rounded-lg p-2 text-sm">
                    <option value="all">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="proses">Proses</option>
                    <option value="menunggu">Menunggu Review</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-400 mb-1">Cari Tugas</label>
                <input type="text" id="searchInput" placeholder="Cari judul tugas..." class="w-full border rounded-lg p-2 text-sm">
            </div>
        </div>
    </div>

    <!-- TASK LIST & DETAIL PANEL -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LIST TUGAS (Card View) -->
        <div class="lg:col-span-2 space-y-4">
            @forelse($tasks as $task)
            @php
                // Cek apakah deadline sudah lewat
                $isDeadlineExpired = false;
                $deadlineDate = $task->deadline ? \Carbon\Carbon::parse($task->deadline) : null;
                $today = \Carbon\Carbon::now();
                $isExpired = $deadlineDate && $deadlineDate->isPast() && $task->status != 'selesai' && $task->status != 'menunggu';
            @endphp
            <div class="task-card bg-white rounded-xl shadow-sm border p-4 task-item {{ $isExpired ? 'deadline-expired' : '' }}"
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
                        <span class="text-xs px-2 py-1 rounded-full {{ $task->created_by_role == 'hr' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $task->created_by_role == 'hr' ? 'HRD' : 'Manager' }}
                        </span>
                        @if($task->priority == 'urgent')
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">Urgent</span>
                        @elseif($task->priority == 'high')
                            <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700">Tinggi</span>
                        @endif
                        @if($isExpired)
                            <span class="text-xs px-2 py-1 rounded-full bg-red-200 text-red-800 animate-pulse">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Lewat Deadline
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 {{ $isExpired ? 'text-red-500 font-semibold' : '' }}">
                        {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : '-' }}
                    </p>
                </div>
                
                <h3 class="font-bold text-gray-800 mb-1 {{ $isExpired ? 'text-red-700' : '' }}">{{ $task->judul ?? $task->nama_tugas }}</h3>
                <p class="text-sm text-gray-500 mb-3">{{ Str::limit($task->deskripsi ?? 'Tidak ada deskripsi', 60) }}</p>
                
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
                    <button class="text-blue-600 text-sm flex items-center gap-1" onclick="event.stopPropagation(); showTaskDetail(this.parentElement.parentElement)">
                        <span class="material-icons-outlined text-sm">visibility</span>
                        Lihat Detail
                    </button>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm border p-12 text-center text-gray-400">
                <span class="material-icons-outlined text-5xl mb-2 block">assignment_late</span>
                <p>Belum ada tugas yang diberikan</p>
            </div>
            @endforelse
        </div>

        <!-- DETAIL PANEL -->
        <div class="bg-white rounded-xl shadow-sm border p-5 h-fit sticky top-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="material-icons-outlined text-blue-500">info</span>
                Detail Tugas
            </h3>
            <div id="detailPanel">
                <div class="text-center text-gray-400 py-8">
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
            <h4 id="toastTitle" class="font-semibold text-gray-800">Notifikasi</h4>
            <p id="toastMessage" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
            <span class="material-icons-outlined text-sm">close</span>
        </button>
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
                <div class="bg-red-50 rounded-lg p-3 mb-3 border border-red-200">
                    <div class="flex items-center gap-2">
                        <span class="material-icons-outlined text-red-500">warning</span>
                        <span class="text-sm font-medium text-red-700">Tugas ini sudah melewati deadline!</span>
                    </div>
                    <p class="text-xs text-red-600 mt-1">Segera selesaikan tugas Anda.</p>
                </div>
            `;
        }
        
        let terimaButton = '';
        if (card.dataset.status === 'pending') {
            terimaButton = `
                <div class="pt-3">
                    <button onclick="terimaTugas(${card.dataset.id})"
                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                        Terima Tugas
                    </button>
                </div>
            `;
        }
        
        let submissionInfo = '';
        if (hasSubmission) {
            submissionInfo = `
                <div class="bg-green-50 rounded-lg p-3 mt-3">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons-outlined text-green-600 text-sm">cloud_done</span>
                        <span class="text-sm font-medium text-green-700">File sudah diupload</span>
                    </div>
                    <a href="${card.dataset.submission}" class="text-blue-600 text-sm flex items-center gap-1" target="_blank">
                        <span class="material-icons-outlined text-sm">download</span>
                        Lihat File Tugas
                    </a>
                    ${card.dataset.submitted ? `<p class="text-xs text-gray-500 mt-2">Dikumpulkan: ${card.dataset.submitted}</p>` : ''}
                </div>
            `;
        }
        
        let uploadButton = '';
        if (card.dataset.status !== 'selesai' && card.dataset.status !== 'menunggu') {
            uploadButton = `
                <div class="pt-3 border-t mt-3">
                    <a href="{{ route('karyawan.tugas.show', '') }}/${selectedTaskId}" 
                       class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm text-center block hover:bg-blue-700">
                        Upload Tugas
                    </a>
                </div>
            `;
        }
        
        const html = `
            <div class="space-y-4">
                ${deadlineWarning}
                <div>
                    <p class="text-xs text-gray-400">Judul Tugas</p>
                    <h4 class="font-bold text-lg ${isExpired ? 'text-red-700' : ''}">${escapeHtml(card.dataset.judul)}</h4>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Deskripsi</p>
                    <p class="text-sm text-gray-600">${escapeHtml(card.dataset.deskripsi)}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-400">Deadline</p>
                        <p class="text-sm ${isExpired ? 'text-red-600 font-semibold' : ''}">${card.dataset.deadline}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Status</p>
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
    
    // Notifikasi
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBell) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
            loadNotifications();
        });
    }
    
    document.addEventListener('click', function() {
        notificationDropdown.classList.remove('show');
    });
    
    function loadNotifications() {
        fetch('/api/notifications')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('notificationList');
                if (data.notifications && data.notifications.length > 0) {
                    container.innerHTML = data.notifications.map(n => `
                        <div class="notification-item ${!n.is_read ? 'unread' : ''}">
                            <p class="text-sm font-medium">${n.title}</p>
                            <p class="text-xs text-gray-500">${n.message}</p>
                            <p class="text-xs text-gray-400 mt-1">${n.time_ago}</p>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="p-4 text-center text-gray-400">Tidak ada notifikasi</div>';
                }
            })
            .catch(err => console.error('Error:', err));
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
    
    // Variabel untuk menyimpan notifikasi deadline yang sudah ditampilkan
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
                
                // Jika deadline sudah lewat dan status belum selesai/menunggu
                if (diffDays < 0 && status !== 'selesai' && status !== 'menunggu') {
                    // Tambahkan class expired jika belum
                    if (!task.classList.contains('deadline-expired')) {
                        task.classList.add('deadline-expired');
                        
                        // Update badge jika diperlukan
                        const badgeSpan = task.querySelector('.badge');
                        if (badgeSpan && !badgeSpan.classList.contains('badge-expired')) {
                            badgeSpan.classList.remove('badge-pending', 'badge-proses');
                            badgeSpan.classList.add('badge-expired');
                            badgeSpan.textContent = 'Terlambat';
                        }
                        
                        // Tampilkan notifikasi sekali saja per tugas
                        const notifKey = `expired_${taskId}`;
                        if (!shownDeadlineNotifications.has(notifKey)) {
                            shownDeadlineNotifications.add(notifKey);
                            showToast('Tugas Terlambat', `Tugas "${taskJudul}" sudah melewati deadline! Segera selesaikan.`, 'error');
                            
                            // Animasi blink pada card
                            task.classList.add('deadline-warning');
                            setTimeout(() => {
                                task.classList.remove('deadline-warning');
                            }, 3000);
                        }
                    }
                }
                // Jika deadline besok (H-1) dan status belum selesai/menunggu
                else if (diffDays === 1 && status !== 'selesai' && status !== 'menunggu') {
                    const notifKey = `reminder_${taskId}`;
                    if (!shownDeadlineNotifications.has(notifKey)) {
                        shownDeadlineNotifications.add(notifKey);
                        showToast('⏰ Pengingat Deadline', `Tugas "${taskJudul}" deadline besok!`, 'warning');
                    }
                }
            }
        });
        
        // Update statistik setelah pengecekan
        updateStats();
    }
    
    // Jalankan pengecekan deadline setelah halaman load
    setTimeout(checkDeadlines, 500);
    
    // Jalankan pengecekan setiap 1 jam
    setInterval(checkDeadlines, 3600000);
    
    updateStats();
    
    setInterval(() => {
        fetch('/api/notifications/unread-count')
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (data.count > 0) {
                    if (badge) badge.textContent = data.count > 9 ? '9+' : data.count;
                } else if (badge) {
                    badge.remove();
                }
            })
            .catch(err => console.error('Error:', err));
    }, 30000);
</script>

</body>
</html>