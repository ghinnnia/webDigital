<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Top & Low Grade - General Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .grade-a { background: linear-gradient(135deg, #10b981, #059669); }
        .grade-b { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .grade-c { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .grade-d { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #cd7f32, #b45309); }
    </style>
</head>

<body>
    @include('general_manajer/templet/header')

    <main class="flex-1 flex flex-col main-content">
        <div class="flex-1 p-4 sm:p-8">
            <div class="container mx-auto max-w-7xl">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800"><i class="fa-solid fa-chart-simple mr-2"></i>Top & Low Grade Performance</h1>
                        <p class="text-slate-500 mt-1">Monitoring kinerja manager dan divisi</p>
                    </div>
                    <div>
                        <form method="GET" action="{{ route('general_manajer.top_low_grade') }}" class="flex gap-3 items-end">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Bulan</label>
                                <select name="bulan" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ request('bulan', $bulan) == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $i, 1)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Tahun</label>
                                <select name="tahun" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                    @for($i=now()->year-2; $i<=now()->year+1; $i++)
                                        <option value="{{ $i }}" {{ request('tahun', $tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Filter</button>
                                <a href="{{ route('general_manajer.top_low_grade') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm ml-2">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistik Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    <div class="stat-card bg-white rounded-xl p-5 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <span class="material-icons-outlined text-blue-600">people</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Total Manager</p>
                                <p class="text-2xl font-bold text-slate-800">{{ $statistik['total_manager'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-white rounded-xl p-5 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                                <span class="material-icons-outlined text-emerald-600">corporate_fare</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Total Divisi</p>
                                <p class="text-2xl font-bold text-slate-800">{{ $statistik['total_divisi'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-white rounded-xl p-5 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                                <span class="material-icons-outlined text-purple-600">trending_up</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Rata-rata Manager</p>
                                <p class="text-2xl font-bold text-slate-800">{{ number_format($statistik['rata_rata_manager'], 1) }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-white rounded-xl p-5 border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                                <span class="material-icons-outlined text-amber-600">analytics</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Rata-rata Divisi</p>
                                <p class="text-2xl font-bold text-slate-800">{{ number_format($statistik['rata_rata_divisi'], 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOP & LOW MANAGER -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                                <i class="fa-solid fa-trophy mr-2 text-amber-300"></i> TOP 5 MANAGER
                            </h3>
                            <p class="text-emerald-100 text-xs">Periode {{ $bulan }}/{{ $tahun }}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left">No</th>
                                        <th class="px-4 py-3 text-left">Nama Manager</th>
                                        <th class="px-4 py-3 text-left">Divisi</th>
                                        <th class="px-4 py-3 text-center">Nilai</th>
                                        <th class="px-4 py-3 text-center">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($topManagers as $index => $m)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3">
                                                <span class="inline-flex w-7 h-7 rounded-full items-center justify-center text-xs font-bold text-white 
                                                    {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : 'bg-slate-400')) }}">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $m['name'] }}</td>
                                            <td class="px-4 py-3 text-slate-600">{{ $m['divisi'] }}</td>
                                            <td class="px-4 py-3 text-center font-semibold text-indigo-600">{{ number_format($m['nilai'], 1) }}%</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex w-8 h-8 rounded-full items-center justify-center text-xs font-bold text-white grade-{{ strtolower($m['grade']) }}">
                                                    {{ $m['grade'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data manager</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-rose-600 to-rose-700 px-6 py-4">
                            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation mr-2 text-rose-300"></i> LOW 5 MANAGER
                            </h3>
                            <p class="text-rose-100 text-xs">Periode {{ $bulan }}/{{ $tahun }}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left">No</th>
                                        <th class="px-4 py-3 text-left">Nama Manager</th>
                                        <th class="px-4 py-3 text-left">Divisi</th>
                                        <th class="px-4 py-3 text-center">Nilai</th>
                                        <th class="px-4 py-3 text-center">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($lowManagers as $index => $m)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $m['name'] }}</td>
                                            <td class="px-4 py-3 text-slate-600">{{ $m['divisi'] }}</td>
                                            <td class="px-4 py-3 text-center font-semibold text-rose-600">{{ number_format($m['nilai'], 1) }}%</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex w-8 h-8 rounded-full items-center justify-center text-xs font-bold text-white grade-{{ strtolower($m['grade']) }}">
                                                    {{ $m['grade'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data manager</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TOP & LOW DIVISI -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                                <i class="fa-solid fa-trophy mr-2 text-amber-300"></i> TOP 5 DIVISI
                            </h3>
                            <p class="text-emerald-100 text-xs">Rata-rata nilai karyawan</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left">No</th>
                                        <th class="px-4 py-3 text-left">Divisi</th>
                                        <th class="px-4 py-3 text-center">Jml Karyawan</th>
                                        <th class="px-4 py-3 text-center">Rata-rata</th>
                                        <th class="px-4 py-3 text-center">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($topDivisi as $index => $d)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3">
                                                <span class="inline-flex w-7 h-7 rounded-full items-center justify-center text-xs font-bold text-white 
                                                    {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : 'bg-slate-400')) }}">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $d['nama'] }}</td>
                                            <td class="px-4 py-3 text-center text-slate-600">{{ $d['jumlah_karyawan'] }}</td>
                                            <td class="px-4 py-3 text-center font-semibold text-indigo-600">{{ number_format($d['nilai_rata_rata'], 1) }}%</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex w-8 h-8 rounded-full items-center justify-center text-xs font-bold text-white grade-{{ strtolower($d['grade']) }}">
                                                    {{ $d['grade'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data divisi</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-rose-600 to-rose-700 px-6 py-4">
                            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation mr-2 text-rose-300"></i> LOW 5 DIVISI
                            </h3>
                            <p class="text-rose-100 text-xs">Rata-rata nilai karyawan</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left">No</th>
                                        <th class="px-4 py-3 text-left">Divisi</th>
                                        <th class="px-4 py-3 text-center">Jml Karyawan</th>
                                        <th class="px-4 py-3 text-center">Rata-rata</th>
                                        <th class="px-4 py-3 text-center">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($lowDivisi as $index => $d)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $d['nama'] }}</td>
                                            <td class="px-4 py-3 text-center text-slate-600">{{ $d['jumlah_karyawan'] }}</td>
                                            <td class="px-4 py-3 text-center font-semibold text-rose-600">{{ number_format($d['nilai_rata_rata'], 1) }}%</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex w-8 h-8 rounded-full items-center justify-center text-xs font-bold text-white grade-{{ strtolower($d['grade']) }}">
                                                    {{ $d['grade'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data divisi</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Informasi Footer -->
                <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="flex items-center gap-2">
                        <span class="material-icons-outlined text-blue-600">info</span>
                        <p class="text-sm text-blue-700">
                            <strong>Informasi:</strong> Grade A = ≥90%, B = ≥75%, C = ≥60%, D = &lt;60%
                        </p>
                    </div>
                </div>

            </div>
        </div>
        <footer class="text-center p-4 bg-gray-100 text-gray-600 text-sm border-t border-gray-200">
            Copyright ©2025 by digicity.id
        </footer>
    </main>
</body>
</html>