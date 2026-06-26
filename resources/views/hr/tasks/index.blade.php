{{-- resources/views/hr/tasks/index.blade.php --}}
@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-slate-50 main-content">
    <div class="flex-1 p-3 sm:p-8">
        <div class="p-6">

            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-semibold text-gray-800 tracking-tight">📑 Monitoring Tugas</h1>
                        <p class="text-gray-500 text-sm mt-1 flex items-center gap-2 font-normal">
                            <span class="material-icons-outlined text-sm text-indigo-500">assignment</span>
                            Pantau progres dan hasil pengerjaan tugas karyawan
                        </p>
                    </div>
                    <div class="flex gap-2 mt-4 md:mt-0">
                        <a href="{{ route('hr.tasks.create') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition-all active:scale-95 font-medium text-sm">
                            <span class="material-icons-outlined text-sm">add_circle_outline</span>
                            Buat Tugas
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center gap-3">
                <span class="material-icons-outlined text-emerald-600">check_circle</span>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
            @endif

            <!-- Statistik Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 transition-all hover:shadow-md hover:border-gray-200">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Tugas</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-2">{{ $totalTasks ?? $tasks->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 transition-all hover:shadow-md">
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-medium text-emerald-600 uppercase tracking-wide">Selesai</p>
                        <span class="text-[10px] px-2 py-0.5 bg-emerald-500 text-white rounded-full font-medium">Done</span>
                    </div>
                    <p class="text-2xl font-semibold text-emerald-700 mt-2">{{ $completedTasks ?? $tasks->where('status', 'selesai')->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 transition-all hover:shadow-md">
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-medium text-amber-600 uppercase tracking-wide">On Progress</p>
                        <span class="text-[10px] px-2 py-0.5 bg-amber-500 text-white rounded-full font-medium">Active</span>
                    </div>
                    <p class="text-2xl font-semibold text-amber-700 mt-2">{{ $pendingTasks ?? $tasks->whereIn('status', ['pending', 'proses'])->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 transition-all hover:shadow-md">
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-medium text-red-600 uppercase tracking-wide">Terlambat</p>
                        <span class="text-[10px] px-2 py-0.5 bg-red-500 text-white rounded-full font-medium">Overdue</span>
                    </div>
                    <p class="text-2xl font-semibold text-red-700 mt-2">{{ $overdueTasks ?? 0 }}</p>
                </div>
            </div>

            <!-- Tabel Daftar Tugas -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 bg-white border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-base">Daftar Penugasan</h3>
                        <p class="text-xs text-gray-400 mt-0.5 font-normal">Seluruh tugas divisi terpusat</p>
                    </div>
                </div>
                <div class="overflow-x-auto scroll-indicator">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-6 py-4 text-left font-medium text-gray-500 text-xs w-16">No</th>
                                <th class="px-6 py-4 text-left font-medium text-gray-500 text-xs">Informasi Tugas</th>
                                <th class="px-6 py-4 text-left font-medium text-gray-500 text-xs">Penanggung Jawab</th>
                                <th class="px-6 py-4 text-center font-medium text-gray-500 text-xs">Prioritas</th>
                                <th class="px-6 py-4 text-left font-medium text-gray-500 text-xs">Tenggat Waktu</th>
                                <th class="px-6 py-4 text-center font-medium text-gray-500 text-xs">Status</th>
                                <th class="px-6 py-4 text-center font-medium text-gray-500 text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($tasks as $key => $task)
                            @php
                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-600',
                                    'medium' => 'bg-blue-50 text-blue-600',
                                    'high' => 'bg-amber-50 text-amber-700',
                                    'urgent' => 'bg-red-50 text-red-700',
                                ];
                                $priorityIcons = [
                                    'low' => 'flag',
                                    'medium' => 'flag',
                                    'high' => 'flag',
                                    'urgent' => 'warning',
                                ];
                                $statusColors = [
                                    'pending' => 'bg-gray-100 text-gray-500',
                                    'proses' => 'bg-amber-50 text-amber-600',
                                    'selesai' => 'bg-emerald-50 text-emerald-700',
                                    'menunggu' => 'bg-purple-50 text-purple-700',
                                    'dibatalkan' => 'bg-red-50 text-red-600',
                                ];
                                $isOverdue = !in_array($task->status, ['selesai', 'dibatalkan']) && $task->deadline && now()->gt($task->deadline);
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 text-gray-400 font-mono text-xs">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $task->judul }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        ID: #TASK-{{ $task->id }} • Dibuat: {{ $task->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-medium ring-1 ring-indigo-100">
                                            {{ strtoupper(substr($task->assignedKaryawan->nama ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-700 leading-none">{{ $task->assignedKaryawan->nama ?? '-' }}</div>
                                            <!-- <div class="text-xs text-gray-400 mt-1">{{ $task->assignedKaryawan->divisi ?? 'No Division' }}</div> -->
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $priorityColors[$task->priority] ?? 'bg-gray-100' }}">
                                        <span class="material-icons-outlined text-xs">{{ $priorityIcons[$task->priority] ?? 'flag' }}</span>
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-700 {{ $isOverdue ? 'text-red-600' : '' }}">
                                            {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}
                                        </span>
                                        @if($isOverdue)
                                            <span class="text-xs text-red-500 mt-1 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Terlambat
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium {{ $statusColors[$task->status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('hr.tasks.show', $task->id) }}" 
                                       class="w-8 h-8 inline-flex items-center justify-center bg-white border border-gray-200 text-indigo-500 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all" 
                                       title="Lihat Detail">
                                        <span class="material-icons-outlined text-sm">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <span class="material-icons-outlined text-5xl text-gray-300 block mb-3">assignment_late</span>
                                    <p class="text-gray-400 font-medium text-sm">Belum ada tugas yang tersedia</p>
                                    <p class="text-xs text-gray-300 mt-1">Silakan buat tugas baru untuk memulai</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-6 flex flex-wrap gap-4 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span>Urgent Priority</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                    <span>High Priority</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    <span>Medium Priority</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    <span>Task Completed</span>
                </div>
            </div>

        </div>
    </div>
</main>

<style>
    .scroll-indicator::-webkit-scrollbar {
        height: 6px;
    }
    .scroll-indicator::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .scroll-indicator::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .scroll-indicator::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    
    /* Better table row hover */
    tbody tr {
        transition: background-color 0.2s ease;
    }
</style>