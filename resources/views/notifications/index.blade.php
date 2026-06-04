<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notifikasi - {{ Auth::user()->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', sans-serif; }
        
        .notification-item {
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 3px solid transparent;
        }
        .notification-item:hover {
            transform: translateX(4px);
            background-color: #f9fafb;
        }
        .notification-item.unread {
            background: linear-gradient(90deg, #eff6ff 0%, #ffffff 100%);
            border-left-color: #3b82f6;
        }
        .notification-item.unread:hover {
            background: linear-gradient(90deg, #dbeafe 0%, #f9fafb 100%);
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }
        
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        .toast.show {
            transform: translateX(0);
        }
        .toast-success { border-left: 4px solid #10b981; }
        .toast-error { border-left: 4px solid #ef4444; }
        .toast-warning { border-left: 4px solid #f59e0b; }
        .toast-info { border-left: 4px solid #3b82f6; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        
        /* Tabel styling */
        .data-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .data-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table tr:hover {
            background-color: #f9fafb;
        }
        
        /* Badge styles */
        .badge-payroll {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 500;
        }
        .badge-task {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-gray-50 font-display">

@php
    $userRole = Auth::user()->role;
@endphp

{{-- ==================== LAYOUT BERDASARKAN ROLE ==================== --}}

@if($userRole == 'hr')
    {{-- LAYOUT HR (dengan sidebar kiri) --}}
    @include('hr.templet.sider')
    <main class="flex-1 ml-[250px]">
        <div class="p-6">
@elseif($userRole == 'manager_divisi')
    {{-- LAYOUT MANAGER DIVISI (dengan sidebar kiri) --}}
    @include('manager_divisi.templet.sider')
    <main class="flex-1 ml-[250px]">
        <div class="p-6">
@else
    {{-- LAYOUT KARYAWAN (dengan header navbar) --}}
    <div class="flex flex-col min-h-screen">
        @include('karyawan.templet.header')
        <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
@endif


{{-- ==================== KONTEN UTAMA ==================== --}}

<!-- Header Section -->
<div class="mb-8 fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-2">
                <span class="material-icons-outlined text-blue-600 text-3xl">notifications</span>
                Notifikasi
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                @if($userRole == 'hr')
                    Pantau aktivitas karyawan dan tugas yang perlu perhatian
                @elseif($userRole == 'manager_divisi')
                    Pantau notifikasi untuk divisi Anda
                @else
                    Notifikasi tugas, slip gaji, dan update untuk Anda
                @endif
            </p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" id="markAllReadForm">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm">
                        <span class="material-icons-outlined text-sm">done_all</span>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
            
            @php
                if ($userRole == 'karyawan') {
                    $backRoute = route('karyawan.tugas.index');
                } elseif ($userRole == 'manager_divisi') {
                    $backRoute = route('manager.tasks.index');
                } elseif ($userRole == 'hr') {
                    $backRoute = route('hr.tasks.index');
                } else {
                    $backRoute = route('home');
                }
            @endphp
            <a href="{{ $backRoute }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition shadow-sm">
                <span class="material-icons-outlined text-sm">arrow_back</span>
                Kembali
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg fade-in">
    <div class="flex items-center gap-2">
        <span class="material-icons-outlined">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg fade-in">
    <div class="flex items-center gap-2">
        <span class="material-icons-outlined">error</span>
        <span>{{ session('error') }}</span>
    </div>
</div>
@endif


{{-- ==================== STATISTIK CARDS (BERBEDA PER ROLE) ==================== --}}

@if($userRole == 'hr')
    {{-- STATISTIK HR --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8 fade-in">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-blue-600">notifications</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Notifikasi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $notifications->total() }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-yellow-600">schedule</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Belum Dibaca</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $unreadCount }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">check_circle</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Sudah Dibaca</p>
                    <p class="text-2xl font-bold text-green-600">{{ $notifications->total() - $unreadCount }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-red-50 rounded-xl shadow-sm border border-red-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-red-600">warning</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Tugas Terlambat</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ $notifications->where('type', 'deadline_warning')->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

@elseif($userRole == 'manager_divisi')
    {{-- STATISTIK MANAGER DIVISI --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 fade-in">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-blue-600">notifications</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Notifikasi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $notifications->total() }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-yellow-600">schedule</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Belum Dibaca</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $unreadCount }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">check_circle</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Sudah Dibaca</p>
                    <p class="text-2xl font-bold text-green-600">{{ $notifications->total() - $unreadCount }}</p>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- STATISTIK KARYAWAN (TAMBAHKAN UNTUK SLIP GAJI) --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8 fade-in">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-blue-600">notifications</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Notifikasi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $notifications->total() }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-yellow-600">schedule</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Belum Dibaca</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $unreadCount }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">check_circle</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Sudah Dibaca</p>
                    <p class="text-2xl font-bold text-green-600">{{ $notifications->total() - $unreadCount }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-emerald-50 rounded-xl shadow-sm border border-emerald-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-emerald-600">receipt</span>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Slip Gaji</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $notifications->where('type', 'payroll')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
@endif


{{-- ==================== DAFTAR NOTIFIKASI (BERBEDA PER ROLE) ==================== --}}

@if($userRole == 'hr')
    {{-- TAMPILAN HR: TABEL FORMAL --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden fade-in">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="material-icons-outlined text-blue-500">list_alt</span>
                    Daftar Notifikasi
                </h3>
                <span class="text-xs text-gray-400">{{ $notifications->total() }} notifikasi</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Notifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Terkait</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notif)
                    <tr class="hover:bg-gray-50 transition {{ !$notif->is_read ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4">
                            @if(!$notif->is_read)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                    <span class="material-icons-outlined text-xs">fiber_new</span>
                                    Baru
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-500">
                                    <span class="material-icons-outlined text-xs">done</span>
                                    Dibaca
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if($notif->type == 'payroll')
                                    <span class="material-icons-outlined text-emerald-500">receipt</span>
                                @elseif($notif->type == 'deadline_warning')
                                    <span class="material-icons-outlined text-red-500">warning</span>
                                @elseif($notif->type == 'task_submitted')
                                    <span class="material-icons-outlined text-green-500">cloud_upload</span>
                                @else
                                    <span class="material-icons-outlined text-gray-500">notifications</span>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $notif->title }}</p>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $notif->message }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($notif->type == 'payroll')
                                <a href="{{ $notif->link ?? '#' }}" 
                                   class="text-emerald-600 hover:text-emerald-800 text-sm flex items-center gap-1">
                                    <span class="material-icons-outlined text-sm">receipt</span>
                                    Lihat Slip Gaji
                                </a>
                            @elseif($notif->task_id)
                                <a href="{{ route('hr.tasks.show', $notif->task_id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                    <span class="material-icons-outlined text-sm">visibility</span>
                                    Lihat Tugas
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="flex flex-col">
                                <span>{{ $notif->created_at->diffForHumans() }}</span>
                                <span class="text-xs">{{ $notif->created_at->format('d M Y H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                @if(!$notif->is_read)
                                    <form action="{{ route('notifications.mark-read', $notif->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition" title="Tandai dibaca">
                                            <span class="material-icons-outlined text-sm">done</span>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notifikasi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition" title="Hapus">
                                        <span class="material-icons-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-icons-outlined text-gray-300 text-6xl">notifications_none</span>
                                <p class="font-medium">Belum ada notifikasi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($userRole == 'manager_divisi')
    {{-- TAMPILAN MANAGER DIVISI: TABEL DENGAN FILTER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden fade-in">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="material-icons-outlined text-blue-500">list_alt</span>
                    Daftar Notifikasi
                </h3>
                <span class="text-xs text-gray-400">{{ $notifications->total() }} notifikasi</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Notifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Dari Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notif)
                    <tr class="hover:bg-gray-50 transition {{ !$notif->is_read ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4">
                            @if(!$notif->is_read)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                    <span class="material-icons-outlined text-xs">fiber_new</span>
                                    Baru
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-500">
                                    <span class="material-icons-outlined text-xs">done</span>
                                    Dibaca
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if($notif->type == 'payroll')
                                    <span class="material-icons-outlined text-emerald-500">receipt</span>
                                @elseif($notif->type == 'task_revision')
                                    <span class="material-icons-outlined text-orange-500">edit_note</span>
                                @elseif($notif->type == 'task_submitted')
                                    <span class="material-icons-outlined text-green-500">cloud_upload</span>
                                @else
                                    <span class="material-icons-outlined text-gray-500">notifications</span>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $notif->title }}</p>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $notif->message }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $karyawanName = \App\Models\User::find($notif->user_id)->name ?? '-';
                            @endphp
                            {{ $karyawanName }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $notif->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                @if(!$notif->is_read)
                                    <form action="{{ route('notifications.mark-read', $notif->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition" title="Tandai dibaca">
                                            <span class="material-icons-outlined text-sm">done</span>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notifikasi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition" title="Hapus">
                                        <span class="material-icons-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-icons-outlined text-gray-300 text-6xl">notifications_none</span>
                                <p class="font-medium">Belum ada notifikasi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@else
    {{-- TAMPILAN KARYAWAN: CARD VIEW PERSONAL (DENGAN SLIP GAJI) --}}
    <div class="space-y-4 fade-in">
        @forelse($notifications as $notif)
        <div class="notification-item {{ !$notif->is_read ? 'unread' : '' }} bg-white rounded-xl shadow-sm border border-gray-100 p-5 transition"
             data-id="{{ $notif->id }}"
             onclick="markAsRead({{ $notif->id }})">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        {{-- ICON BERDASARKAN TYPE --}}
                        @if($notif->type == 'payroll')
                            <span class="material-icons-outlined text-emerald-500 text-lg">receipt</span>
                            <span class="badge-payroll">Slip Gaji</span>
                        @elseif($notif->type == 'task_reminder' || $notif->type == 'deadline_reminder')
                            <span class="material-icons-outlined text-yellow-500 text-lg">schedule</span>
                        @elseif($notif->type == 'deadline_warning')
                            <span class="material-icons-outlined text-red-500 text-lg">warning</span>
                        @elseif($notif->type == 'task_submitted')
                            <span class="material-icons-outlined text-green-500 text-lg">cloud_upload</span>
                        @elseif($notif->type == 'task_approved')
                            <span class="material-icons-outlined text-blue-500 text-lg">check_circle</span>
                        @elseif($notif->type == 'task_revision')
                            <span class="material-icons-outlined text-orange-500 text-lg">edit_note</span>
                        @else
                            <span class="material-icons-outlined text-gray-500 text-lg">notifications</span>
                        @endif
                        
                        <h3 class="font-semibold {{ !$notif->is_read ? 'text-blue-800' : 'text-gray-800' }}">
                            {{ $notif->title }}
                        </h3>
                        @if(!$notif->is_read)
                            <span class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">Baru</span>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-3 leading-relaxed">{{ $notif->message }}</p>
                    
                    <div class="flex flex-wrap gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <span class="material-icons-outlined text-xs">access_time</span>
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="material-icons-outlined text-xs">event</span>
                            {{ $notif->created_at->format('d M Y H:i') }}
                        </span>
                        @if($notif->type == 'payroll' && $notif->link)
                            <a href="{{ $notif->link }}" 
                               class="text-emerald-600 hover:text-emerald-800 flex items-center gap-1"
                               onclick="event.stopPropagation()">
                                <span class="material-icons-outlined text-xs">receipt</span>
                                Lihat Slip Gaji
                            </a>
                        @elseif($notif->task_id)
                            <a href="{{ route('karyawan.tugas.show', $notif->task_id) }}" 
                               class="text-blue-600 hover:text-blue-800 flex items-center gap-1"
                               onclick="event.stopPropagation()">
                                <span class="material-icons-outlined text-xs">visibility</span>
                                Lihat Tugas
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="flex gap-1 ml-4">
                    @if(!$notif->is_read)
                        <button onclick="markAsRead({{ $notif->id }}, event)" 
                                class="text-green-600 hover:text-green-800 p-1 rounded-full hover:bg-green-50 transition"
                                title="Tandai sudah dibaca">
                            <span class="material-icons-outlined text-sm">done</span>
                        </button>
                    @endif
                    <button onclick="deleteNotification({{ $notif->id }}, event)" 
                            class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50 transition"
                            title="Hapus notifikasi">
                        <span class="material-icons-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <span class="material-icons-outlined text-gray-300 text-6xl">notifications_none</span>
                <p class="text-gray-400 font-medium">Belum ada notifikasi</p>
                <p class="text-sm text-gray-400">Notifikasi akan muncul saat ada tugas baru, deadline mendekat, atau slip gaji tersedia</p>
            </div>
        </div>
        @endforelse
    </div>
@endif


{{-- Pagination --}}
@if($notifications->hasPages())
<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endif


{{-- ==================== PENUTUP LAYOUT ==================== --}}

@if($userRole == 'hr' || $userRole == 'manager_divisi')
        </div>
    </main>
@else
        </main>
    </div>
@endif


{{-- ==================== TOAST & SCRIPTS ==================== --}}

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
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');
        
        toast.classList.remove('toast-success', 'toast-error', 'toast-warning', 'toast-info');
        
        if (type === 'success') {
            toast.classList.add('toast-success');
            toastTitle.textContent = 'Berhasil';
            toastIcon.textContent = 'check_circle';
            toastIcon.className = 'material-icons-outlined text-green-500';
        } else if (type === 'error') {
            toast.classList.add('toast-error');
            toastTitle.textContent = 'Gagal';
            toastIcon.textContent = 'error';
            toastIcon.className = 'material-icons-outlined text-red-500';
        } else {
            toast.classList.add('toast-info');
            toastTitle.textContent = 'Informasi';
            toastIcon.textContent = 'info';
            toastIcon.className = 'material-icons-outlined text-blue-500';
        }
        
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        toast.classList.add('show');
        
        setTimeout(() => hideToast(), 3000);
    }
    
    function hideToast() {
        const toast = document.getElementById('toast');
        toast.classList.remove('show');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }
    
    function markAsRead(id, event) {
        if (event) event.stopPropagation();
        
        fetch(`/notifications/${id}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Notifikasi ditandai sudah dibaca', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Gagal menandai notifikasi', 'error');
        });
    }
    
    function deleteNotification(id, event) {
        event.stopPropagation();
        
        if (confirm('Yakin ingin menghapus notifikasi ini?')) {
            fetch(`/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Notifikasi dihapus', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal menghapus notifikasi', 'error');
            });
        }
    }
    
    document.getElementById('markAllReadForm')?.addEventListener('submit', function(e) {
        if (!confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>