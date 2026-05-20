@include('finance.templet.sider')

<main class="flex-1 flex flex-col bg-[#f8fafc] main-content min-h-screen">
    <div class="p-4 lg:p-8">
        <div class="max-w-[1600px] mx-auto">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="p-2.5 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        Manajemen Penggajian
                    </h1>
                    <div class="flex items-center gap-3 mt-2 text-slate-500">
                        <span class="flex items-center gap-1.5 text-sm font-medium bg-slate-100 px-3 py-1 rounded-full">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Pusat Kendali Payroll Finance
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('finance.payroll.dari-hr') }}" 
                       class="flex items-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-100 transition-all active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Ambil Data HR
                    </a>
                    <a href="{{ route('finance.payroll.create') }}" 
                       class="flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-100 transition-all active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Periode Baru
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl shadow-sm flex items-center">
                    <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center mr-3 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl shadow-sm flex items-center">
                    <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center mr-3 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 transition-all hover:shadow-md hover:border-indigo-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Periode</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $periods->total() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-indigo-100 rounded-full overflow-hidden">
                        <div class="h-full w-full bg-indigo-500 rounded-full"></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 transition-all hover:shadow-md hover:border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Diproses</p>
                            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $periods->where('status', 'processed')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-blue-100 rounded-full overflow-hidden">
                        <div class="h-full w-full bg-blue-500 rounded-full"></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 transition-all hover:shadow-md hover:border-emerald-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Disetujui</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $periods->where('status', 'approved')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-emerald-100 rounded-full overflow-hidden">
                        <div class="h-full w-full bg-emerald-500 rounded-full"></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 transition-all hover:shadow-md hover:border-amber-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Dibayar</p>
                            <p class="text-3xl font-bold text-amber-600 mt-1">{{ $periods->where('status', 'paid')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-amber-100 rounded-full overflow-hidden">
                        <div class="h-full w-full bg-amber-500 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Tabel Periode Penggajian -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-white border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-800 text-lg">Daftar Periode Penggajian</h2>
                            <p class="text-xs text-slate-400">Monitoring status & rekapan bulanan</p>
                        </div>
                    </div>
                    <div class="bg-slate-100 px-4 py-2 rounded-xl">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Total Records</span>
                        <span class="text-lg font-bold text-indigo-600 ml-2">{{ $periods->total() }} Periode</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal Selesai</th>
                                <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Karyawan</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Gaji</th>
                                <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($periods as $period)
                            @php
                                $totalGaji = $period->details->sum('total_gaji_bersih');
                                $totalKaryawan = $period->details->count();
                                
                                $statusStyles = [
                                    'draft' => 'bg-slate-100 text-slate-600',
                                    'processed' => 'bg-blue-100 text-blue-700',
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'paid' => 'bg-amber-100 text-amber-700'
                                ];
                                $statusIcons = [
                                    'draft' => '📝',
                                    'processed' => '🔄',
                                    'approved' => '✅',
                                    'paid' => '💰'
                                ];
                                $statusLabel = [
                                    'draft' => 'Draft',
                                    'processed' => 'Diproses',
                                    'approved' => 'Disetujui',
                                    'paid' => 'Dibayar'
                                ][$period->status] ?? ucfirst($period->status);
                                $statusColor = $statusStyles[$period->status] ?? 'bg-slate-100 text-slate-600';
                                $statusIcon = $statusIcons[$period->status] ?? '📋';
                            @endphp
                            <tr class="hover:bg-slate-50 transition-all duration-150">
                                <td class="px-6 py-5">
                                    <span class="font-mono font-bold text-slate-400 text-sm">#{{ str_pad($period->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-800">{{ $period->nama_periode }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $period->tahun }}</div>
                                </td>
                                <td class="px-6 py-5 text-center text-slate-600">{{ $period->tanggal_mulai->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 text-center text-slate-600">{{ $period->tanggal_selesai->format('d/m/Y') }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $statusColor }}">
                                        <span>{{ $statusIcon }}</span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="inline-flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span class="font-semibold text-slate-700">{{ $totalKaryawan }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="text-base font-bold text-slate-800">Rp {{ number_format($totalGaji, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <a href="{{ route('finance.payroll.show', $period->id) }}" 
                                       class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-200"
                                       title="Detail Penggajian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">Belum ada periode penggajian</p>
                                        <p class="text-xs text-slate-400 mt-1">Klik "Buat Periode Baru" atau "Ambil Data HR" untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                        <p class="text-xs text-slate-500">
                            Menampilkan <span class="font-semibold">{{ $periods->firstItem() ?? 0 }}</span> - <span class="font-semibold">{{ $periods->lastItem() ?? 0 }}</span> dari <span class="font-semibold">{{ $periods->total() }}</span> periode
                        </p>
                        <div class="flex items-center gap-2">
                            {{ $periods->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-8 flex flex-wrap gap-6 items-center justify-center py-4 border-t border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Diproses: Data dari HR</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Disetujui: Terverifikasi</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Dibayar: Selesai</span>
                </div>
            </div>

        </div>
    </div>
</main>

<style>
    /* Custom Scrollbar */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Pagination Styling */
    .finance-pagination nav {
        display: flex;
        gap: 4px;
    }
    .finance-pagination nav .relative {
        display: inline-flex;
    }
    .finance-pagination nav a, 
    .finance-pagination nav span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .finance-pagination nav a:hover {
        background-color: #eef2ff;
        color: #4f46e5;
    }
    .finance-pagination nav .bg-indigo-600 {
        background-color: #4f46e5 !important;
        color: white !important;
    }
    .finance-pagination nav svg {
        width: 16px;
        height: 16px;
    }
</style>