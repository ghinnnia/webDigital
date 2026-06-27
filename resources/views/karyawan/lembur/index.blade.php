<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan Lembur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header Atas (Horizontal) -->
    @include('karyawan.templet.header')

    <!-- 
      KUNCI PERBAIKAN:
      - Menghapus 'md:ml-64' karena kamu pakai Topbar, bukan Sidebar.
      - Menggunakan 'mx-auto' dan 'max-w-6xl' atau 'max-w-7xl' agar konten otomatis ngumpul di tengah secara simetris.
    -->
    <div class="pt-28 p-4 md:p-6 max-w-7xl mx-auto">
        
        <!-- Header Judul & Tombol -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    📋 Pengajuan Lembur Karyawan
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">Pantau status riwayat lembur dan pengajuan Anda di sini.</p>
            </div>
            <a href="{{ route('karyawan.lembur.create') }}" class="w-full sm:w-auto justify-center bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 flex items-center gap-2 font-medium shadow-sm transition-colors text-sm">
                <span class="material-icons-outlined text-base">add</span>
                Ajukan Lembur
            </a>
        </div>

        <!-- Info Tarif Lembur Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="material-icons-outlined text-blue-600 mt-0.5">info</span>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Informasi Tarif Lembur: Rp 30.000 / jam</p>
                    <p class="text-xs text-blue-700 mt-0.5 leading-relaxed">Kalkulasi lembur akan diverifikasi oleh HRD terlebih dahulu dan dana kompensasi akan dibayarkan secara kolektif bersamaan dengan pengiriman gaji bulanan Anda.</p>
                </div>
            </div>
        </div>

        <!-- Container Tabel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[750px] table-auto">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Kerja Lembur</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Durasi</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Berkas</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alasan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lemburs as $lembur)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono bg-gray-50/50">
                                {{ $lembur->jam_mulai }} - {{ $lembur->jam_selesai }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-700 font-semibold">
                                {{ $lembur->durasi }} jam
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                @if($lembur->status == 'pending')
                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @elseif($lembur->status == 'approved')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $lembur->keterangan }}">
                                {{ $lembur->keterangan ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-icons-outlined text-5xl text-gray-300">schedule</span>
                                    <p class="font-medium text-gray-600 mt-2">Belum Ada Pengajuan Lembur</p>
                                    <p class="text-xs text-gray-400 max-w-sm">Riwayat pengajuan lembur Anda kosong. Jika Anda melakukan kerja lembur, silakan ajukan berkas kompensasi segera.</p>
                                    <a href="{{ route('karyawan.lembur.create') }}" class="text-blue-500 hover:text-blue-600 text-sm font-semibold underline mt-2">
                                        Ajukan lembur sekarang &rarr;
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($lemburs->hasPages())
        <div class="mt-5">
            {{ $lemburs->links() }}
        </div>
        @endif

    </div>

</body>
</html>