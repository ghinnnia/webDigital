<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan & Perintah Lembur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1e293b",
                    },
                },
            },
        };
    </script>
    <style>
        :root {
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --bg-secondary: #f9fafb;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        .dark {
            --bg-page: #0f172a;
            --bg-card: #1e293b;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .bg-white { background-color: var(--bg-card) !important; }
        .border-gray-200 { border-color: var(--border-color) !important; }
        .bg-gray-50 { background-color: var(--bg-secondary) !important; }
        .text-gray-800, .text-gray-900, .text-gray-700 { color: var(--text-primary) !important; }
        .text-gray-500, .text-gray-400, .text-gray-600 { color: var(--text-secondary) !important; }
        .divide-gray-100 > * + * { border-color: var(--border-color) !important; }

        .bg-amber-50 { background-color: rgba(245, 158, 11, 0.15) !important; }
        .text-amber-700 { color: #d97706 !important; }
        .dark .text-amber-700 { color: #fbbf24 !important; }
        
        .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.15) !important; }
        .text-emerald-700 { color: #059669 !important; }
        .dark .text-emerald-700 { color: #34d399 !important; }

        .bg-rose-50 { background-color: rgba(239, 68, 68, 0.15) !important; }
        .text-rose-700 { color: #dc2626 !important; }
        .dark .text-rose-700 { color: #fca5a5 !important; }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        .pagination a, .pagination span {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
    </style>
</head>
<body style="background-color: var(--bg-page);">

    <!-- Memanggil File Header Terpisah -->
    @include('karyawan.templet.header')

    <div class="pt-28 p-4 md:p-6 max-w-7xl mx-auto">
        
        <!-- Header Konten -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2" style="color: var(--text-primary);">
                    💼 Menu Lembur Karyawan
                </h1>
                <p class="text-sm mt-0.5" style="color: var(--text-secondary);">Kelola pengajuan lembur mandiri atau lihat instruksi perintah lembur dari Manager.</p>
            </div>
            <a href="{{ route('karyawan.lembur.create') }}" class="w-full sm:w-auto justify-center bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 flex items-center gap-2 font-medium shadow-sm transition-colors text-sm">
                <span class="material-icons-outlined text-base">add</span>
                Ajukan Lembur Mandiri
            </a>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 dark:bg-slate-800 border border-blue-200 dark:border-blue-900 rounded-xl p-4 mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="material-icons-outlined text-blue-600 dark:text-blue-400 mt-0.5">info</span>
                <div>
                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">Informasi Tarif Lembur Standar: Rp 30.000 / jam</p>
                    <p class="text-xs text-blue-700 dark:text-blue-400 mt-0.5 leading-relaxed">Seluruh lemburan (Mandiri maupun Perintah) akan melewati tahap verifikasi Manager Divisi sebelum masuk ke rekapan payroll bulanan.</p>
                </div>
            </div>
        </div>

        <!-- Tab Navigasi Internal -->
        <div class="flex border-b border-gray-200 mb-6 gap-2">
            <button onclick="switchTab('pengajuan')" id="tab-pengajuan" class="px-5 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600 transition-all flex items-center gap-2">
                <span class="material-icons-outlined text-lg">description</span>
                Pengajuan Mandiri
            </button>
            <button onclick="switchTab('perintah')" id="tab-perintah" class="px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all flex items-center gap-2 relative">
                <span class="material-icons-outlined text-lg">assignment_turned_in</span>
                Perintah Lembur Manager
                @if(isset($perintahLemburs) && $perintahLemburs->where('status', 'pending')->count() > 0)
                    <span class="absolute top-1 right-0 bg-red-500 text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full font-bold">
                        {{ $perintahLemburs->where('status', 'pending')->count() }}
                    </span>
                @endif
            </button>
        </div>

        <!-- ISI TAB 1: PENGAJUAN MANDIRI -->
        <div id="content-pengajuan" class="tab-content">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] table-auto">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Tanggal</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Jam Kerja</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Durasi</th>
                                <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Estimasi Pendapatan</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Status Berkas</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Alasan / Aktivitas Kerja</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Catatan Penolakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($lemburs as $lembur)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium" style="color: var(--text-primary);">
                                    {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-mono" style="color: var(--text-primary);">
                                    {{ date('H:i', strtotime($lembur->jam_mulai)) }} - {{ date('H:i', strtotime($lembur->jam_selesai)) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center font-semibold" style="color: var(--text-primary);">
                                    {{ abs($lembur->durasi) }} jam
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-emerald-600 dark:text-emerald-400">
                                    @php
                                        $tarifPengajuan = $lembur->custom_rate ?? $lembur->hourly_rate ?? 30000;
                                        $totalUpahPengajuan = abs($lembur->durasi) * $tarifPengajuan;
                                    @endphp
                                    Rp {{ number_format($totalUpahPengajuan, 0, ',', '.') }}
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
                                <td class="px-6 py-4 text-sm max-w-xs truncate" title="{{ $lembur->keterangan }}" style="color: var(--text-secondary);">
                                    {{ $lembur->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($lembur->status == 'rejected' && $lembur->alasan_penolakan)
                                        <button onclick="showAlasanPenolakan('{{ addslashes($lembur->alasan_penolakan) }}', '{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d/m/Y') }}')" 
                                                class="text-red-600 hover:text-red-800 underline text-xs flex items-center gap-1">
                                            <span class="material-icons-outlined text-sm">visibility</span> Lihat Alasan
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center" style="color: var(--text-secondary);">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-icons-outlined text-5xl opacity-50">schedule</span>
                                        <p class="font-medium mt-2">Belum Ada Pengajuan Lembur</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($lemburs->hasPages())
            <div class="pagination">{{ $lemburs->links() }}</div>
            @endif
        </div>

        <!-- ISI TAB 2: PERINTAH LEMBUR MANAGER -->
        <div id="content-perintah" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] table-auto">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Tanggal Instruksi</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Jam Kerja (Estimasi)</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Durasi</th>
                                <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Upah Terhitung</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Beban Project</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Deskripsi Tugas Lembur</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(isset($perintahLemburs) && count($perintahLemburs) > 0)
                                @foreach($perintahLemburs as $perintah)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium">
                                        {{ \Carbon\Carbon::parse($perintah->tanggal_lembur)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-blue-600 dark:text-blue-400">
                                        {{ date('H:i', strtotime($perintah->jam_mulai)) }} - {{ date('H:i', strtotime($perintah->jam_selesai)) }}
                                    </td>
                                    <!-- FIX: Menggunakan abs() agar durasi tidak minus -->
                                    <td class="px-6 py-4 text-sm text-center font-medium">
                                        {{ abs($perintah->durasi) }} jam
                                    </td>
                                    <!-- FIX: Menggunakan abs() agar upah perkalian tidak bernilai minus -->
                                    <td class="px-6 py-4 text-sm text-right font-bold text-blue-600 dark:text-blue-400">
                                        @php
                                            $tarifPerintah = $perintah->custom_rate ?? $perintah->hourly_rate ?? 30000;
                                            $totalUpahPerintah = abs($perintah->durasi) * $tarifPerintah;
                                        @endphp
                                        <span title="{{ $perintah->custom_rate ? 'Tarif Khusus Manager' : 'Tarif Normal' }}">
                                            Rp {{ number_format($totalUpahPerintah, 0, ',', '.') }}
                                            @if($perintah->custom_rate)
                                                <small class="block text-[10px] text-amber-500 font-normal">*Custom Rate</small>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center font-medium">
                                        <span class="bg-gray-100 dark:bg-slate-700 px-2.5 py-1 rounded-md text-xs">
                                            {{ $perintah->project_name ?? 'Umum / Internal' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm max-w-xs truncate" title="{{ $perintah->tugas ?? $perintah->keterangan }}">
                                        {{ $perintah->tugas ?? $perintah->keterangan }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        @if($perintah->status == 'pending')
                                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-medium">
                                                Belum Diambil
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-medium">
                                                Sudah Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        @if($perintah->status == 'pending')
                                            <form action="{{ route('karyawan.lembur.terima_perintah', $perintah->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1 shadow-sm transition-all">
                                                    <span class="material-icons-outlined text-xs">check</span> Laporkan Selesai
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs">Clear</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center" style="color: var(--text-secondary);">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <span class="material-icons-outlined text-5xl opacity-50">assignment_late</span>
                                            <p class="font-medium mt-2">Tidak Ada Instruksi Perintah Lembur</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL PENOLAKAN -->
    <div id="alasanModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black bg-opacity-50 modal-overlay" onclick="closeAlasanModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-5 transform transition-all" style="background-color: var(--bg-card);">
            <div class="flex items-center justify-between pb-4 border-b" style="border-color: var(--border-color);">
                <div class="flex items-center gap-3">
                    <span class="material-icons-outlined text-red-600 bg-red-100 p-2 rounded-full">feedback</span>
                    <h3 class="text-lg font-bold">Alasan Penolakan</h3>
                </div>
                <button onclick="closeAlasanModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="pt-4">
                <p id="modalTanggal" class="text-xs mb-2 text-gray-500"></p>
                <div class="bg-red-50 dark:bg-slate-800 p-4 rounded-xl border border-red-100 dark:border-red-950">
                    <p id="modalAlasan" class="text-sm leading-relaxed"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            const btnPengajuan = document.getElementById('tab-pengajuan');
            const btnPerintah = document.getElementById('tab-perintah');
            
            btnPengajuan.className = "px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all flex items-center gap-2";
            btnPerintah.className = "px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all flex items-center gap-2 relative";

            if(tabName === 'pengajuan') {
                document.getElementById('content-pengajuan').classList.remove('hidden');
                btnPengajuan.className = "px-5 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600 transition-all flex items-center gap-2";
            } else {
                document.getElementById('content-perintah').classList.remove('hidden');
                btnPerintah.className = "px-5 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600 transition-all flex items-center gap-2 relative";
            }
        }

        function showAlasanPenolakan(alasan, tanggal) {
            document.getElementById('modalTanggal').innerText = 'Tanggal Lembur: ' + tanggal;
            document.getElementById('modalAlasan').innerText = alasan;
            document.getElementById('alasanModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeAlasanModal() {
            document.getElementById('alasanModal').style.display = 'none';
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>