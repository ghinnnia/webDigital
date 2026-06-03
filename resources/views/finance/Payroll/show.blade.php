@include('finance.templet.sider')

<main class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 to-slate-100 main-content">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto max-w-full">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('finance.payroll.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800 tracking-tight"><i class="fa-solid fa-file-invoice-dollar mr-2"></i>Detail Penggajian</h1>
                            <p class="text-slate-500 mt-1">Periode: <span class="font-semibold text-indigo-600">{{ $period->nama_periode }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('finance.payroll.hitung-potongan', $period->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-100 transition-all active:scale-95">
                            <i class="fa-solid fa-calculator mr-2"></i> Hitung Potongan & Lembur
                        </button>
                    </form>
                    @if($period->status == 'processed')
                        <form action="{{ route('finance.payroll.approve', $period->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-100 transition-all active:scale-95">
                                <i class="fa-solid fa-check-circle mr-2"></i> Setujui Penggajian
                            </button>
                        </form>
                    @endif
                    @if($period->status == 'approved')
                        <form action="{{ route('finance.payroll.paid', $period->id) }}" method="POST" class="inline flex items-center gap-3">
                            @csrf
                            <input type="date" name="tanggal_pembayaran" value="{{ now()->format('Y-m-d') }}" 
                                   class="px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                            <button type="submit" class="flex items-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-100 transition-all active:scale-95">
                                <i class="fa-solid fa-money-bill-wave mr-2"></i> Tandai Dibayar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Total Karyawan</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $statistik['total_karyawan'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fa-solid fa-users text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Total Gaji</p>
                            <p class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($statistik['total_gaji'], 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave text-emerald-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Total Lembur</p>
                            <p class="text-xl font-bold text-blue-600 mt-1">Rp {{ number_format($statistik['total_lembur'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            <i class="fa-solid fa-clock text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Potongan Kehadiran</p>
                            <p class="text-xl font-bold text-orange-600 mt-1">Rp {{ number_format($statistik['total_potongan_hadir'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                            <i class="fa-solid fa-user-clock text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Potongan BPJS</p>
                            <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($statistik['total_potongan_bpjs'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-heartbeat text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Gaji Bersih</p>
                            <p class="text-xl font-bold text-indigo-600 mt-1">Rp {{ number_format($statistik['total_gaji'] + ($statistik['total_lembur'] ?? 0) - ($statistik['total_potongan_hadir'] ?? 0) - ($statistik['total_potongan_bpjs'] ?? 0), 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fa-solid fa-wallet text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Detail Gaji (TANPA CHECKBOX) -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-white border-b border-slate-100 flex justify-between items-center">
                    <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fa-solid fa-table-list text-indigo-600"></i>
                        Daftar Gaji Karyawan
                    </h2>
                    <button onclick="kirimSemuaSlipGaji()" class="flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Semua Slip Gaji
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="border-b border-slate-200">
                                <th class="px-5 py-4 text-left font-bold text-slate-700">No</th>
                                <th class="px-5 py-4 text-left font-bold text-slate-700">Karyawan</th>
                                <th class="px-5 py-4 text-left font-bold text-slate-700">Divisi</th>
                                <th class="px-5 py-4 text-right font-bold text-slate-700">Gaji Pokok</th>
                                <th class="px-5 py-4 text-right font-bold text-slate-700">Tunjangan</th>
                                <th class="px-5 py-4 text-right font-bold text-slate-700 bg-blue-50 text-blue-700">Lembur</th>
                                <th class="px-5 py-4 text-right font-bold text-slate-700 bg-orange-50 text-orange-700">Potongan Kehadiran</th>
                                <th class="px-5 py-4 text-right font-bold text-slate-700 bg-red-50 text-red-700">Potongan BPJS</th>
                                <th class="px-5 py-4 text-right font-bold text-slate-700 bg-emerald-50 text-emerald-700">Total Bersih</th>
                                <th class="px-5 py-4 text-center font-bold text-slate-700">Slip Gaji</th>
                                <th class="px-5 py-4 text-center font-bold text-slate-700">Kirim</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($period->details as $index => $detail)
                            <tr class="hover:bg-indigo-50/30 transition-all">
                                <td class="px-5 py-4 text-slate-600">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $detail->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400">{{ $detail->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $detail->divisi ?? '-' }}</td>
                                <td class="px-5 py-4 text-right font-mono">Rp {{ number_format($detail->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-mono text-emerald-600">Rp {{ number_format(($detail->tunjangan_tetap ?? 0) + ($detail->tunjangan_kinerja ?? 0), 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-mono text-blue-600 bg-blue-50/30">
                                    @php
                                        $upahLembur = $detail->upah_lembur ?? 0;
                                        $jamLembur = $detail->jam_lembur ?? 0;
                                    @endphp
                                    @if($upahLembur > 0)
                                        Rp {{ number_format($upahLembur, 0, ',', '.') }}
                                        <span class="text-xs text-blue-400 ml-1 block">({{ $jamLembur }} jam)</span>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-mono text-orange-600 bg-orange-50/30">
                                    Rp {{ number_format($detail->potongan_tidak_hadir ?? 0, 0, ',', '.') }}
                                    @if(($detail->potongan_tidak_hadir ?? 0) > 0)
                                    @php
                                        $hariTidakHadir = round(($detail->potongan_tidak_hadir ?? 0) / ($detail->gaji_pokok / 25));
                                    @endphp
                                    <span class="text-xs text-orange-400 ml-1 block">(tidak hadir {{ $hariTidakHadir }} hari)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-mono text-red-600 bg-red-50/30">
                                    Rp {{ number_format($detail->potongan_bpjs ?? 0, 0, ',', '.') }}
                                    <span class="text-xs text-red-400 ml-1 block">(BPJS)</span>
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-indigo-600 text-base">
                                    Rp {{ number_format($detail->total_gaji_bersih, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('finance.payroll.slip', [$period->id, $detail->id]) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-semibold hover:bg-indigo-100 transition-all">
                                        <i class="fa-solid fa-file-pdf mr-1"></i> Slip
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button onclick="kirimSlipGaji({{ $period->id }}, {{ $detail->id }}, '{{ $detail->user->name ?? '-' }}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-semibold hover:bg-emerald-100 transition-all"
                                            title="Kirim Slip Gaji ke {{ $detail->user->email ?? '-' }}">
                                        <i class="fa-solid fa-envelope mr-1"></i> Kirim
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 border-t-2 border-slate-200">
                            <tr>
                                <td colspan="5" class="px-5 py-4 text-right font-bold text-slate-700">TOTAL:</td>
                                <td class="px-5 py-4 text-right font-bold text-blue-700">Rp {{ number_format($statistik['total_lembur'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-orange-700">Rp {{ number_format($statistik['total_potongan_hadir'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-red-700">Rp {{ number_format($statistik['total_potongan_bpjs'] ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-indigo-700 text-lg">
                                    Rp {{ number_format(($statistik['total_gaji'] ?? 0) + ($statistik['total_lembur'] ?? 0) - ($statistik['total_potongan_hadir'] ?? 0) - ($statistik['total_potongan_bpjs'] ?? 0), 0, ',', '.') }}
                                </td>
                                <td colspan="2" class="px-5 py-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Informasi Potongan -->
            <div class="mt-8 p-4 bg-blue-50 rounded-2xl border border-blue-200">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-700">
                        <p><strong>Informasi Potongan & Lembur:</strong></p>
                        <ul class="mt-2 space-y-1 text-xs">
                            <li>• <strong>Potongan Kehadiran</strong> → Alpha / Izin tanpa surat / Telat > 12 siang (Gaji Pokok ÷ 25 per hari)</li>
                            <li>• <strong>Potongan BPJS</strong> → Rp 100.000 per karyawan (dipotong seragam)</li>
                            <li>• <strong>Upah Lembur</strong> → Rp 30.000 per jam (disetujui HR)</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Kirim single slip gaji
function kirimSlipGaji(periodId, detailId, namaKaryawan) {
    Swal.fire({
        title: 'Kirim Slip Gaji',
        html: `Apakah Anda yakin ingin mengirim slip gaji ke <strong>${namaKaryawan}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1"></i> Kirim',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const url = `{{ url('finance/payroll') }}/${periodId}/send-notification/${detailId}`;
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Berhasil!', result.message, 'success');
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error!', 'Gagal mengirim slip gaji', 'error');
            }
        }
    });
}

// Kirim semua slip gaji (massal)
function kirimSemuaSlipGaji() {
    // Ambil semua ID detail dari tabel
    const allIds = @json($period->details->pluck('id'));
    
    if (allIds.length === 0) {
        Swal.fire('Peringatan', 'Tidak ada data karyawan', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Kirim Slip Gaji Massal',
        html: `Apakah Anda yakin ingin mengirim slip gaji ke <strong>${allIds.length} karyawan</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1"></i> Kirim Semua',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const url = `{{ url('finance/payroll') }}/{{ $period->id }}/send-notification-mass`;
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: allIds })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire('Berhasil!', result.message, 'success');
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error!', 'Gagal mengirim slip gaji massal', 'error');
            }
        }
    });
}
</script>