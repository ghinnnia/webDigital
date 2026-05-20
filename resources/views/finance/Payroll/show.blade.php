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
                        Detail Penggajian
                    </h1>
                    <p class="text-slate-500 mt-2">Periode: <span class="font-semibold text-indigo-600">{{ $period->nama_periode }}</span></p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('finance.payroll.hitung-potongan', $period->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-100 transition-all active:scale-95">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            🔄 Hitung Potongan
                        </button>
                    </form>
                    @if($period->status == 'processed')
                        <form action="{{ route('finance.payroll.approve', $period->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-100 transition-all active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Setujui Penggajian
                            </button>
                        </form>
                    @endif
                    @if($period->status == 'approved')
                        <form action="{{ route('finance.payroll.paid', $period->id) }}" method="POST" class="inline flex items-center gap-3">
                            @csrf
                            <input type="date" name="tanggal_pembayaran" value="{{ now()->format('Y-m-d') }}" 
                                   class="px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                            <button type="submit" class="flex items-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-100 transition-all active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tandai Dibayar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl shadow-sm flex items-center">
                    <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Total Karyawan</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $statistik['total_karyawan'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
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
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm0-10V7a2 2 0 012-2h10a2 2 0 012 2v6a2 2 0 01-2 2h-2m-6 0a4 4 0 108 0 4 4 0 00-8 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Total Potongan</p>
                            <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($statistik['total_potongan'], 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Gaji Bersih</p>
                            <p class="text-xl font-bold text-blue-600 mt-1">Rp {{ number_format($statistik['total_gaji'] - $statistik['total_potongan'], 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Detail Gaji -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-white border-b border-slate-100">
                    <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Daftar Gaji Karyawan
                    </h2>
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
                                <th class="px-5 py-4 text-right font-bold text-slate-700 bg-red-50 text-red-700">Potongan</th>
                                <th class="px-5 py-4 text-right font-bold text-slate-700 bg-emerald-50 text-emerald-700">Total Bersih</th>
                                <th class="px-5 py-4 text-center font-bold text-slate-700">Slip</th>
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
                                <td class="px-5 py-4 text-right font-mono text-emerald-600">Rp {{ number_format(($detail->tunjangan_tetap ?? 0) + ($detail->tunjangan_kinerja ?? 0) + ($detail->tunjangan_lain ?? 0), 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-mono text-red-600 bg-red-50/30">
                                    Rp {{ number_format(($detail->potongan_tidak_hadir ?? 0) + ($detail->potongan_bpjs ?? 0) + ($detail->potongan_lain ?? 0), 0, ',', '.') }}
                                    @if(($detail->potongan_tidak_hadir ?? 0) > 0)
                                    <span class="text-xs text-red-400 ml-1">(potongan hadir)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-indigo-600 text-base">Rp {{ number_format($detail->total_gaji_bersih, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('finance.payroll.slip', [$period->id, $detail->id]) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-semibold hover:bg-indigo-100 transition-all">
                                        📄 Slip
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 border-t-2 border-slate-200">
                            <tr>
                                <td colspan="5" class="px-5 py-4 text-right font-bold text-slate-700">TOTAL:</td>
                                <td class="px-5 py-4 text-right font-bold text-red-700">Rp {{ number_format($statistik['total_potongan'], 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-indigo-700 text-lg">Rp {{ number_format($statistik['total_gaji'] - $statistik['total_potongan'], 0, ',', '.') }}</td>
                                <td class="px-5 py-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Informasi -->
            <div class="mt-8 p-4 bg-blue-50 rounded-2xl border border-blue-200">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p><strong>Informasi Potongan:</strong> Potongan dihitung dari kehadiran karyawan.</p>
                        <ul class="mt-2 space-y-1 text-xs">
                            <li>• Alpha (tanpa keterangan) → Potong 1 hari gaji (Gaji Pokok ÷ 25)</li>
                            <li>• Izin (tanpa surat) → Potong 1 hari gaji</li>
                            <li>• Cuti di luar jatah → Potong 1 hari gaji</li>
                            <li>• Sakit dengan surat dokter → Tidak dipotong</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>