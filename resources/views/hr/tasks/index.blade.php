{{-- resources/views/hr/tasks/index.blade.php --}}
@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-slate-50 main-content">
    <div class="flex-1 p-3 sm:p-8">
        <div class="p-6">

            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">📑 Monitoring Tugas</h1>
                        <p class="text-gray-500 text-sm mt-1 flex items-center gap-2">
                            <span class="material-icons-outlined text-sm text-indigo-500">assignment</span>
                            Pantau progres dan hasil pengerjaan tugas karyawan
                        </p>
                    </div>
                    <div class="flex gap-2 mt-4 md:mt-0">
                        <a href="{{ route('hr.tasks.create') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition-all active:scale-95">
                            <span class="material-icons-outlined text-sm">add_circle_outline</span>
                            Buat Tugas
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center gap-3">
                <span class="material-icons-outlined">check_circle</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 group hover:border-indigo-300 transition-all">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Tugas</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalTasks ?? $tasks->count() }}</p>
                </div>
                <div class="bg-emerald-50 rounded-2xl shadow-sm border border-emerald-100 p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Selesai</p>
                        <span class="text-[10px] px-1.5 py-0.5 bg-emerald-500 text-white rounded font-black uppercase">Done</span>
                    </div>
                    <p class="text-2xl font-black text-emerald-700 mt-1">{{ $completedTasks ?? $tasks->where('status', 'selesai')->count() }}</p>
                </div>
                <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-100 p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">On Progress</p>
                        <span class="text-[10px] px-1.5 py-0.5 bg-amber-500 text-white rounded font-black uppercase">Active</span>
                    </div>
                    <p class="text-2xl font-black text-amber-700 mt-1">{{ $pendingTasks ?? $tasks->whereIn('status', ['pending', 'proses'])->count() }}</p>
                </div>
                <div class="bg-red-50 rounded-2xl shadow-sm border border-red-100 p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest">Terlambat</p>
                        <span class="text-[10px] px-1.5 py-0.5 bg-red-500 text-white rounded font-black uppercase">Overdue</span>
                    </div>
                    <p class="text-2xl font-black text-red-700 mt-1">{{ $overdueTasks ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 bg-white border-b flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Daftar Penugasan</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Seluruh tugas divisi terpusat</p>
                    </div>
                </div>
                <div class="overflow-x-auto scroll-indicator">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-gray-200">
                                <th class="px-6 py-5 text-left font-bold text-slate-700 w-16">No</th>
                                <th class="px-6 py-5 text-left font-bold text-slate-700">Informasi Tugas</th>
                                <th class="px-6 py-5 text-left font-bold text-slate-700">Penanggung Jawab</th>
                                <th class="px-6 py-5 text-center font-bold text-slate-700">Prioritas</th>
                                <th class="px-6 py-5 text-left font-bold text-slate-700">Tenggat Waktu</th>
                                <th class="px-6 py-5 text-center font-bold text-slate-700">Status</th>
                                <th class="px-6 py-5 text-center font-bold text-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tasks as $key => $task)
                            @php
                                $priorityColors = [
                                    'low' => 'bg-slate-100 text-slate-600',
                                    'medium' => 'bg-blue-100 text-blue-600',
                                    'high' => 'bg-amber-100 text-amber-700',
                                    'urgent' => 'bg-red-100 text-red-700',
                                ];
                                $statusColors = [
                                    'pending' => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                                    'proses' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-100',
                                    'selesai' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
                                    'menunggu' => 'bg-purple-100 text-purple-700 ring-1 ring-purple-200',
                                    'dibatalkan' => 'bg-red-50 text-red-600 ring-1 ring-red-100',
                                ];
                                $isOverdue = !in_array($task->status, ['selesai', 'dibatalkan']) && $task->deadline && now()->gt($task->deadline);
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 text-gray-400 font-mono text-xs">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-black text-slate-800 text-base">{{ $task->judul }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">
                                        ID: #TASK-{{ $task->id }} • Dibuat: {{ $task->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-[10px] ring-1 ring-indigo-100">
                                            {{ strtoupper(substr($task->assignedKaryawan->nama ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-700 leading-none">{{ $task->assignedKaryawan->nama ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-400 font-black uppercase mt-1">{{ $task->assignedKaryawan->divisi ?? 'No Division' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-xl text-[10px] font-black uppercase shadow-sm {{ $priorityColors[$task->priority] ?? 'bg-slate-100' }}">
                                        {{ $task->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold {{ $isOverdue ? 'text-red-600' : 'text-slate-700' }}">
                                            {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}
                                        </span>
                                        @if($isOverdue)
                                            <span class="text-[9px] font-black text-red-500 uppercase mt-1 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> Expired
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black shadow-sm {{ $statusColors[$task->status] ?? 'bg-slate-100' }}">
                                        {{ strtoupper($task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('hr.tasks.show', $task->id) }}" 
                                       class="w-8 h-8 inline-flex items-center justify-center bg-white border border-gray-200 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm" 
                                       title="Lihat Detail">
                                        <span class="material-icons-outlined text-sm">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <span class="material-icons-outlined text-5xl text-gray-200 block mb-3">assignment_late</span>
                                    <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Belum ada tugas yang tersedia</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span> Urgent Priority
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span> High Priority
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span> Medium Priority
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span> Task Completed
                </div>
            </div>

        </div>
    </div>
</main>

<style>
    .scroll-indicator::-webkit-scrollbar {
        height: 8px;
    }
    .scroll-indicator::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .scroll-indicator::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .scroll-indicator::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>