<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport" />
    <title>Top & Low Grade - Manager Divisi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        /* Custom Scrollbar */
        .content-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .content-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }
        .content-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }
        .content-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Grade Badge Gradients */
        .grade-a { background: linear-gradient(135deg, #10b981, #059669); }
        .grade-b { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .grade-c { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .grade-d { background: linear-gradient(135deg, #ef4444, #dc2626); }

        /* Leaderboard Rank Badges */
        .rank-1 { background: linear-gradient(135deg, #fcd34d, #f59e0b); }
        .rank-2 { background: linear-gradient(135deg, #cbd5e1, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #fed7aa, #b45309); }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <div class="flex min-h-screen w-full overflow-hidden">
        
        @include('manager_divisi.templet.sider')

        <main class="flex-1 min-w-0 md:ml-[260px] flex flex-col transition-all duration-300">
            
            <div class="content-scroll h-[calc(100vh-50px)] overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-8">
                    
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-6 border-b border-slate-200">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 flex items-center gap-3">
                                <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl shadow-sm">
                                    <i class="fa-solid fa-chart-line text-xl"></i>
                                </span>
                                Top & Low Grade Karyawan
                            </h1>
                            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-briefcase text-slate-400"></i>
                                    Divisi: <strong class="text-indigo-600">{{ $namaDivisi ?? 'Divisi Anda' }}</strong>
                                </span>
                                <span class="hidden sm:inline text-slate-300">•</span>
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-display text-slate-400"></i>
                                    Monitoring Kinerja Divisi
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 w-full lg:w-auto">
                            <form method="GET" action="{{ route('manager_divisi.top_low_grade') }}" class="flex flex-wrap sm:flex-nowrap items-end gap-3">
                                <div class="w-full sm:w-40">
                                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Bulan</label>
                                    <div class="relative">
                                        <select name="bulan" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pl-3 pr-8 appearance-none cursor-pointer text-slate-700 font-medium">
                                            @for($i=1; $i<=12; $i++)
                                                <option value="{{ $i }}" {{ request('bulan', $bulan) == $i ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::createFromDate(null, $i, 1)->format('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                <div class="w-full sm:w-32">
                                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Tahun</label>
                                    <div class="relative">
                                        <select name="tahun" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pl-3 pr-8 appearance-none cursor-pointer text-slate-700 font-medium">
                                            @for($i=now()->year-2; $i<=now()->year+1; $i++)
                                                <option value="{{ $i }}" {{ request('tahun', $tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm transition active:scale-95 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-filter text-xs"></i> Filter
                                    </button>
                                    <a href="{{ route('manager_divisi.top_low_grade') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-200 border border-slate-200 transition text-center flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-arrow-rotate-right text-xs"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition duration-300 flex items-center gap-4 group">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center transition duration-300 group-hover:bg-indigo-600 group-hover:text-white">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</p>
                                <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ $statistik['total_karyawan'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition duration-300 flex items-center gap-4 group">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center transition duration-300 group-hover:bg-emerald-600 group-hover:text-white">
                                <i class="fa-solid fa-award text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Grade A</p>
                                <p class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $statistik['grade_a_count'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition duration-300 flex items-center gap-4 group">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center transition duration-300 group-hover:bg-amber-500 group-hover:text-white">
                                <i class="fa-solid fa-chart-simple text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Nilai</p>
                                <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($statistik['rata_rata_nilai'] ?? 0, 1) }}%</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition duration-300 flex items-center gap-4 group">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center transition duration-300 group-hover:bg-purple-600 group-hover:text-white">
                                <i class="fa-solid fa-list-check text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Tugas Selesai</p>
                                <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ $statistik['total_tugas'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b">
                                <div>
                                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                                        <i class="fa-solid fa-trophy text-amber-300 text-xl animate-pulse"></i> 
                                        TOP 5 Karyawan Terbaik
                                    </h3>
                                    <p class="text-emerald-100/80 text-xs mt-0.5">Pemeringkatan performa berdasarkan akumulasi tugas dan KPI bulanan</p>
                                </div>
                                <span class="bg-emerald-500/30 text-white border border-emerald-400/30 px-3 py-1 rounded-full text-xs font-medium self-start sm:self-auto">
                                    Periode {{ $bulan }}/{{ $tahun }}
                                </span>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                                            <th class="px-6 py-4 text-center w-20">Peringkat</th>
                                            <th class="px-6 py-4">Informasi Karyawan</th>
                                            <th class="px-6 py-4">Jabatan / Role</th>
                                            <th class="px-6 py-4 text-center">Volume Tugas</th>
                                            <th class="px-6 py-4 text-center">Skor Akhir</th>
                                            <th class="px-6 py-4 text-center w-24">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($topKaryawan as $index => $k)
                                            <tr class="hover:bg-slate-50/70 transition duration-150">
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    <span class="inline-flex w-7 h-7 rounded-full items-center justify-center text-xs font-bold text-white shadow-sm
                                                        {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : 'bg-slate-400')) }}">
                                                        {{ $index + 1 }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm border border-indigo-200 shadow-inner">
                                                            {{ strtoupper(substr($k['name'], 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-slate-900 text-base">{{ $k['name'] }}</div>
                                                            <div class="text-xs text-slate-400 font-mono mt-0.5">ID: {{ $k['id'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                                        {{ ucfirst(str_replace('_', ' ', $k['role'])) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap font-medium text-slate-600">{{ $k['total_tugas'] }} Selesai</td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    <span class="font-bold text-indigo-600 text-base">{{ number_format($k['nilai'], 1) }}%</span>
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    <span class="inline-flex w-8 h-8 rounded-full items-center justify-center text-xs font-bold text-white shadow-sm grade-{{ strtolower($k['grade']) }}">
                                                        {{ $k['grade'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-16 text-center text-slate-400 bg-slate-50/50">
                                                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300 block"></i>
                                                    <span class="text-sm font-medium">Belum tersedia data pencapaian karyawan pada bulan ini.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-rose-600 to-rose-700 px-6 py-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b">
                                <div>
                                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                                        <i class="fa-solid fa-triangle-exclamation text-rose-300 text-xl"></i> 
                                        LOW 5 Evaluasi Karyawan
                                    </h3>
                                    <p class="text-rose-100/80 text-xs mt-0.5">Daftar staf dengan indikator capaian di bawah target operasional perusahaan</p>
                                </div>
                                <span class="bg-rose-500/30 text-white border border-rose-400/30 px-3 py-1 rounded-full text-xs font-medium self-start sm:self-auto">
                                    Periode {{ $bulan }}/{{ $tahun }}
                                </span>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                                            <th class="px-6 py-4 text-center w-20">No</th>
                                            <th class="px-6 py-4">Informasi Karyawan</th>
                                            <th class="px-6 py-4">Jabatan / Role</th>
                                            <th class="px-6 py-4 text-center">Volume Tugas</th>
                                            <th class="px-6 py-4 text-center">Skor Akhir</th>
                                            <th class="px-6 py-4 text-center w-24">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($lowKaryawan as $index => $k)
                                            <tr class="hover:bg-slate-50/70 transition duration-150">
                                                <td class="px-6 py-4 text-center whitespace-nowrap text-slate-500 font-semibold">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-50 to-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm border border-rose-200 shadow-inner">
                                                            {{ strtoupper(substr($k['name'], 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-slate-900 text-base">{{ $k['name'] }}</div>
                                                            <div class="text-xs text-slate-400 font-mono mt-0.5">ID: {{ $k['id'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                                        {{ ucfirst(str_replace('_', ' ', $k['role'])) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap font-medium text-slate-600">{{ $k['total_tugas'] }} Selesai</td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    <span class="font-bold text-rose-600 text-base">{{ number_format($k['nilai'], 1) }}%</span>
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    <span class="inline-flex w-8 h-8 rounded-full items-center justify-center text-xs font-bold text-white shadow-sm grade-{{ strtolower($k['grade']) }}">
                                                        {{ $k['grade'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-16 text-center text-emerald-600 bg-slate-50/50">
                                                    <i class="fa-solid fa-circle-check text-4xl mb-3 text-emerald-400 block"></i>
                                                    <span class="text-sm font-semibold">Luar Biasa! Semua karyawan memenuhi target performa minimum standar divisi.</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4.5 bg-blue-50/60 border border-blue-200 rounded-xl">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-circle-info text-blue-600"></i>
                                <h4 class="text-sm font-bold text-blue-900">Panduan Klasifikasi Grade Kinerja</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full grade-a shrink-0"></span>
                                    <span class="text-xs text-slate-600 font-medium">A = &ge;90% (Sangat Baik)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full grade-b shrink-0"></span>
                                    <span class="text-xs text-slate-600 font-medium">B = &ge;75% (Baik)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full grade-c shrink-0"></span>
                                    <span class="text-xs text-slate-600 font-medium">C = &ge;60% (Cukup)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full grade-d shrink-0"></span>
                                    <span class="text-xs text-slate-600 font-medium">D = &lt;60% (Kurang)</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4.5 bg-amber-50/60 border border-amber-200 rounded-xl flex items-start gap-3">
                            <span class="p-1.5 bg-amber-100 text-amber-700 rounded-lg shrink-0">
                                <i class="fa-solid fa-lightbulb"></i>
                            </span>
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-amber-900">Rekomendasi Manajerial</h4>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Karyawan yang terdata dengan <strong>Grade D / C</strong> dianjurkan untuk diagendakan sesi 1-on-1 coaching atau diberikan modul bimbingan tambahan intensif guna memulihkan ritme performa kerjanya.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <footer class="w-full text-center py-3 bg-slate-100 border-t border-slate-200 text-slate-400 font-medium text-[11px] tracking-wide mt-auto">
                Copyright &copy; 2025 &bull; Developed by <a href="https://digital kolaborasi.id" target="_blank" class="text-indigo-500 hover:underline font-semibold">digital kolaborasi.id</a>
            </footer>
        </main>
    </div>
</body>

</html>
