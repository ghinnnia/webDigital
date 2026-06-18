<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Penggajian - {{ $period->nama_periode }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50">

@include('finance.templet.sider')

<main class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 to-slate-100 main-content">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto max-w-full">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('finance.payroll.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-all">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice-dollar text-indigo-600"></i> Detail Penggajian
                            </h1>
                            <p class="text-slate-500 text-sm mt-0.5">Periode: <span class="font-semibold text-indigo-600">{{ $period->nama_periode }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
                    
                    <a href="{{ route('finance.overtime-settings.index', ['redirect_to' => route('finance.payroll.show', $period->id)]) }}" class="w-full sm:w-auto flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl shadow-sm border border-slate-300 transition-all active:scale-95">
                        <i class="fa-solid fa-gear mr-2 text-slate-500"></i> Atur Tarif Lembur
                    </a>

                    <form action="{{ route('finance.payroll.hitung-potongan', $period->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto flex items-center justify-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-md shadow-amber-100 transition-all active:scale-95">
                            <i class="fa-solid fa-calculator mr-2"></i> Hitung Potongan & Lembur
                        </button>
                    </form>
                    
                    @if($period->status == 'processed')
                        <form action="{{ route('finance.payroll.approve', $period->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto flex items-center justify-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-100 transition-all active:scale-95">
                                <i class="fa-solid fa-check-circle mr-2"></i> Setujui Penggajian
                            </button>
                        </form>
                    @endif
                    
                    @if($period->status == 'approved')
                        <form action="{{ route('finance.payroll.paid', $period->id) }}" method="POST" class="inline flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            @csrf
                            <input type="date" name="tanggal_pembayaran" value="{{ now()->format('Y-m-d') }}" 
                                   class="px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                            <button type="submit" class="flex items-center justify-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-md shadow-amber-100 transition-all active:scale-95">
                                <i class="fa-solid fa-money-bill-wave mr-2"></i> Tandai Dibayar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-2 text-lg"></i>
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-5 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $statistik['total_karyawan'] }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
                            <i class="fa-solid fa-users text-indigo-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Gaji</p>
                            <p class="text-lg font-bold text-slate-800 mt-1.5">Rp {{ number_format($statistik['total_gaji'], 0, ',', '.') }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center">
                            <i class="fa-solid fa-money-bill text-slate-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Lembur</p>
                            <p class="text-lg font-bold text-blue-600 mt-1.5">Rp {{ number_format($statistik['total_lembur'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i class="fa-solid fa-clock text-blue-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Potongan Hadir</p>
                            <p class="text-lg font-bold text-orange-600 mt-1.5">Rp {{ number_format($statistik['total_potongan_hadir'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center">
                            <i class="fa-solid fa-user-clock text-orange-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Potongan BPJS</p>
                            <p class="text-lg font-bold text-red-600 mt-1.5">Rp {{ number_format($statistik['total_potongan_bpjs'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                            <i class="fa-solid fa-heartbeat text-red-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 bg-gradient-to-br from-indigo-600 to-indigo-700 text-white border-none shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wider">Gaji Bersih</p>
                            <p class="text-lg font-bold mt-1.5 text-white">Rp {{ number_format($statistik['total_gaji'] + ($statistik['total_lembur'] ?? 0) - ($statistik['total_potongan_hadir'] ?? 0) - ($statistik['total_potongan_bpjs'] ?? 0), 0, ',', '.') }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center">
                            <i class="fa-solid fa-wallet text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-white border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fa-solid fa-table-list text-indigo-600"></i>
                        Daftar Gaji Karyawan
                    </h2>
                    <button onclick="kirimSemuaSlipGaji()" class="w-full sm:w-auto flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Semua
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                                <th class="px-4 py-4 text-center whitespace-nowrap">No</th>
                                <th class="px-5 py-4 text-left whitespace-nowrap">Karyawan</th>
                                <th class="px-5 py-4 text-left whitespace-nowrap">Divisi</th>
                                <th class="px-5 py-4 text-right whitespace-nowrap">Gaji Pokok</th>
                                <th class="px-5 py-4 text-right whitespace-nowrap">Tunjangan</th>
                                <th class="px-5 py-4 text-right whitespace-nowrap bg-blue-50/50 text-blue-800">Lembur</th>
                                <th class="px-5 py-4 text-right whitespace-nowrap bg-orange-50/50 text-orange-800">Potongan Hadir</th>
                                <th class="px-5 py-4 text-right whitespace-nowrap bg-red-50/50 text-red-800">Potongan BPJS</th>
                                <th class="px-5 py-4 text-right whitespace-nowrap bg-emerald-50/50 text-emerald-800">Total Bersih</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Slip</th>
                                <th class="px-4 py-4 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($period->details as $index => $detail)
                            <tr class="hover:bg-slate-50/80 transition-all">
                                <td class="px-4 py-4 text-center text-slate-400 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $detail->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $detail->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-600">{{ $detail->divisi ?? '-' }}</td>
                                <td class="px-5 py-4 text-right font-mono font-medium">Rp {{ number_format($detail->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-mono text-emerald-600 font-medium">Rp {{ number_format(($detail->tunjangan_tetap ?? 0) + ($detail->tunjangan_kinerja ?? 0), 0, ',', '.') }}</td>
                                
                                <td class="px-5 py-4 text-right font-mono text-blue-600 bg-blue-50/20">
                                    @php
                                        $upahLembur = $detail->upah_lembur ?? 0;
                                        $jamLembur = $detail->jam_lembur ?? 0;
                                    @endphp
                                    @if($upahLembur > 0)
                                        <span class="font-semibold">Rp {{ number_format($upahLembur, 0, ',', '.') }}</span>
                                        <span class="text-xs text-blue-500 block mt-0.5">({{ $jamLembur }} jam)</span>
                                    @else
                                        <span class="text-slate-400">Rp 0</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-right font-mono text-orange-600 bg-orange-50/20">
                                    Rp {{ number_format($detail->potongan_tidak_hadir ?? 0, 0, ',', '.') }}
                                    @if(($detail->potongan_tidak_hadir ?? 0) > 0)
                                        @php
                                            $hariTidakHadir = $detail->gaji_pokok > 0 ? round(($detail->potongan_tidak_hadir ?? 0) / ($detail->gaji_pokok / 25)) : 0;
                                        @endphp
                                        <span class="text-xs text-orange-500 block mt-0.5">({{ $hariTidakHadir }} hari absen)</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-right font-mono text-red-600 bg-red-50/20">
                                    Rp {{ number_format($detail->potongan_bpjs ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right font-mono font-bold text-indigo-600 bg-emerald-50/10 text-base">
                                    Rp {{ number_format($detail->total_gaji_bersih, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <a href="{{ route('finance.payroll.slip', [$period->id, $detail->id]) }}" 
                                       target="_blank"
                                       class="inline-flex items-center justify-center w-8 h-8 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-all"
                                       title="Lihat PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                </td>
                                

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <button onclick="kirimSlipGaji({{ $period->id }}, {{ $detail->id }}, '{{ $detail->user->name ?? '-' }}')" 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition-all"
                                            title="Kirim">
                                        <i class="fa-solid fa-envelope"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100/80 border-t-2 border-slate-200 font-bold text-slate-700">
                            <tr>
                                <td colspan="5" class="px-5 py-4 text-right tracking-wide">TOTAL KESELURUHAN:</td>
                                <td class="px-5 py-4 text-right font-mono text-blue-700 bg-blue-50/40">Rp {{ number_format($statistik['total_lembur'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-mono text-orange-700 bg-orange-50/40">Rp {{ number_format($statistik['total_potongan_hadir'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-mono text-red-700 bg-red-50/40">Rp {{ number_format($statistik['total_potongan_bpjs'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-mono text-indigo-700 text-base bg-emerald-50/40">
                                    Rp {{ number_format(($statistik['total_gaji'] ?? 0) + ($statistik['total_lembur'] ?? 0) - ($statistik['total_potongan_hadir'] ?? 0) - ($statistik['total_potongan_bpjs'] ?? 0), 0, ',', '.') }}
                                </td>
                                <td colspan="2" class="px-4 py-4 bg-slate-100/40"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50/60 rounded-2xl border border-blue-100 shadow-sm">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 text-base"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold">Informasi Aturan Potongan & Lembur:</p>
                        <ul class="mt-2 space-y-1.5 text-xs text-blue-700/90 tracking-wide">
                            <li>• <strong class="text-blue-900">Potongan Kehadiran:</strong> Diambil dari Alpha / Izin tanpa berkas resmi / Keterlambatan fatal melebihi pukul 12.00 siang. Kalkulasi: <code class="bg-blue-100/80 px-1 py-0.5 rounded font-mono">Gaji Pokok ÷ 25 hari kerja</code> per hari absen.</li>
                            <li>• <strong class="text-blue-900">Potongan BPJS:</strong> Flat sebesar <code class="bg-blue-100/80 px-1 py-0.5 rounded font-mono">Rp 100.000</code> per kepala karyawan (asuransi kesehatan kolektif).</li>
                            <li>• <strong class="text-blue-900">Upah Lembur:</strong> Menggunakan **Tarif Dinamis Per Divisi** atau standar default perusahaan yang telah diset di menu pengaturan lembur.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
// Kirim single
function kirimSlipGaji(periodId, detailId, namaKaryawan) {
    Swal.fire({
        title: 'Kirim Slip Gaji',
        html: `Apakah Anda yakin ingin mengirim ke <strong>${namaKaryawan}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1"></i> Kirim',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
                if (!csrfToken) throw new Error('CSRF token tidak ditemukan.');
                
                const url = `/finance/payroll/${periodId}/send-notification/${detailId}`;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error('Respons server bermasalah.');
                return await response.json();
            } catch (error) {
                Swal.showValidationMessage(`Gagal: ${error.message}`);
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            if (result.value.success) {
                Swal.fire('Berhasil!', result.value.message, 'success');
            } else {
                Swal.fire('Gagal!', result.value.message, 'error');
            }
        }
    });
}

// Kirim massal (Diperbaiki & Dioptimalkan)
function kirimSemuaSlipGaji() {
    const allIds = @json($period->details->pluck('id'));
    if (!allIds || allIds.length === 0) {
        Swal.fire('Peringatan', 'Tidak ada data karyawan untuk dikirim.', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Kirim Massal',
        html: `Apakah Anda yakin ingin mengirim ke <strong>${allIds.length} karyawan</strong>?<br><small class="text-slate-400">Proses ini mungkin memakan waktu beberapa saat.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1"></i> Kirim Semua',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        didOpen: () => {
            // Opsional: Bisa ditambahkan animasi custom di sini jika mau
        },
        preConfirm: async () => {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
                if (!csrfToken) throw new Error('CSRF token tidak ditemukan.');
                
                const url = `/finance/payroll/{{ $period->id }}/send-notification-mass`;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: allIds })
                });
                
                if (!response.ok) throw new Error('Server merespons dengan error (Status: ' + response.status + ')');
                return await response.json();
            } catch (error) {
                Swal.showValidationMessage(`Gagal mengirim massal: ${error.message}`);
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            if (result.value.success) {
                Swal.fire('Berhasil!', result.value.message, 'success');
            } else {
                Swal.fire('Gagal!', result.value.message, 'error');
            }
        }
    });
}
</script>

</body>
</html>