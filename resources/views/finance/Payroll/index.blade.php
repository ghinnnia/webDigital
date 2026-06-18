<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Penggajian - Finance</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.1); }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
        .badge-draft { background: #e2e8f0; color: #475569; }
        .badge-processed { background: #dbeafe; color: #1d4ed8; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-paid { background: #fef3c7; color: #92400e; }
        .btn { padding: 8px 16px; border-radius: 10px; font-weight: 600; font-size: 13px; transition: all 0.2s; }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-primary:hover { background: #4338ca; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-outline { background: transparent; border: 1.5px solid #e2e8f0; color: #475569; }
        .btn-outline:hover { background: #f1f5f9; }
    </style>
</head>
<body>

@include('finance.templet.sider')

<main class="flex-1 p-4 lg:p-8 md:ml-64">
    <div class="max-w-7xl mx-auto">
        
        <!-- ========== HEADER ========== -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <i class="fa-solid fa-file-invoice-dollar text-indigo-600 text-2xl"></i>
                    Manajemen Penggajian
                </h1>
                <p class="text-sm text-slate-500 mt-1">Kelola periode penggajian dan monitoring status pembayaran</p>
            </div>
            <!-- Tombol Lihat Data HR -->
<a href="{{ route('finance.payroll.dari-hr') }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">
    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Lihat Data HR
</a>
           <!-- Tombol Ambil Data HR -->
<form action="{{ route('finance.payroll.ambil-dari-hr') }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">
        <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Ambil Data HR
    </button>
</form>
                <!-- Tombol Buat Periode Baru -->
                <a href="{{ route('finance.payroll.create') }}" class="btn btn-primary flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Buat Periode Baru
                </a>
            </div>
        </div>

        <!-- ========== ALERT ========== -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- ========== STATISTIK CARDS ========== -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="card-hover bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Periode</p>
                        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $periods->total() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <i class="fa-solid fa-calendar text-indigo-600"></i>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Diproses</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $periods->getCollection()->where('status', 'processed')->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-spinner text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Disetujui</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $periods->getCollection()->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-emerald-600"></i>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dibayar</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $periods->getCollection()->where('status', 'paid')->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== TABEL PERIODE ========== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h2 class="font-semibold text-slate-800">Daftar Periode Penggajian</h2>
                <span class="text-xs text-slate-400">{{ $periods->total() }} periode</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Periode</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Karyawan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Total Gaji</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($periods as $period)
                        @php
                            $totalGaji = $period->details->sum('total_gaji_bersih');
                            $totalKaryawan = $period->details->count();
                            $statusClass = 'badge-' . $period->status;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $period->nama_periode }}</div>
                                <div class="text-xs text-slate-400">{{ $period->tahun }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center text-slate-600 text-xs">
                                {{ \Carbon\Carbon::parse($period->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="badge {{ $statusClass }}">
                                    @if($period->status == 'draft') 📄 Draft
                                    @elseif($period->status == 'processed') ⏳ Diproses
                                    @elseif($period->status == 'approved') ✅ Disetujui
                                    @elseif($period->status == 'paid') 💰 Dibayar
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-medium text-slate-700">{{ $totalKaryawan }}</td>
                            <td class="px-4 py-3.5 text-right font-bold text-slate-800">Rp {{ number_format($totalGaji, 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <a href="{{ route('finance.payroll.show', $period->id) }}" 
                                   class="btn bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-medium transition flex items-center gap-1 justify-center">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-3"></i>
                                    <p class="text-slate-500 font-medium">Belum ada periode penggajian</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik "Buat Periode Baru" untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-3 bg-slate-50/70 border-t border-slate-100">
                {{ $periods->links('pagination::tailwind') }}
            </div>
        </div>

        <!-- ========== LEGENDA ========== -->
        <div class="mt-6 flex flex-wrap gap-4 items-center justify-center py-3 border-t border-slate-200 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Diproses (dari HR)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Disetujui (terverifikasi)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Dibayar (selesai)</span>
        </div>

    </div>
</main>

</body>
</html>