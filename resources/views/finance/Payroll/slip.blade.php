@include('finance.templet.sider')

<main class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 to-slate-100 main-content">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto max-w-4xl">
            
            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="{{ route('finance.payroll.show', $period->id) }}" class="inline-flex items-center text-slate-600 hover:text-indigo-600 transition-colors">
                    <span class="material-icons-outlined text-sm mr-1">arrow_back</span>
                    Kembali ke Detail Periode
                </a>
            </div>

            <!-- Slip Gaji Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden" id="slipGaji">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold">SLIP GAJI</h2>
                            <p class="text-indigo-200 mt-1">Periode: {{ $period->nama_periode }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-indigo-200">Tanggal Cetak</p>
                            <p class="font-semibold">{{ now()->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Karyawan -->
                <div class="px-8 py-6 border-b border-slate-200 bg-slate-50/50">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Nama Karyawan</p>
                            <p class="text-lg font-bold text-slate-800 mt-1">{{ $detail->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Divisi</p>
                            <p class="text-lg font-semibold text-slate-700 mt-1">{{ $detail->divisi ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Rincian Gaji -->
                <div class="px-8 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Pendapatan -->
                        <div>
                            <h3 class="font-bold text-emerald-600 border-b-2 border-emerald-200 pb-2 mb-4 flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">trending_up</span>
                                PENDAPATAN
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Gaji Pokok</span>
                                    <span class="font-semibold">Rp {{ number_format($detail->gaji_pokok, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Tunjangan Tetap</span>
                                    <span class="font-semibold">Rp {{ number_format($detail->tunjangan_tetap ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Tunjangan Kinerja (KPA)</span>
                                    <span class="font-semibold">Rp {{ number_format($detail->tunjangan_kinerja ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Bonus</span>
                                    <span class="font-semibold">Rp {{ number_format($detail->bonus ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Tunjangan Lain</span>
                                    <span class="font-semibold">Rp {{ number_format($detail->tunjangan_lain ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-emerald-50 rounded-lg px-3 -mx-3">
                                    <span class="font-bold text-emerald-800">TOTAL PENDAPATAN</span>
                                    <span class="font-bold text-emerald-800">Rp {{ number_format(($detail->gaji_pokok ?? 0) + ($detail->tunjangan_tetap ?? 0) + ($detail->tunjangan_kinerja ?? 0) + ($detail->bonus ?? 0) + ($detail->tunjangan_lain ?? 0), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Potongan -->
                        <div>
                            <h3 class="font-bold text-red-600 border-b-2 border-red-200 pb-2 mb-4 flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">trending_down</span>
                                POTONGAN
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Potongan Tidak Hadir</span>
                                    <span class="font-semibold text-red-600">Rp {{ number_format($detail->potongan_tidak_hadir ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Potongan BPJS</span>
                                    <span class="font-semibold text-red-600">Rp {{ number_format($detail->potongan_bpjs ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100">
                                    <span class="text-slate-600">Potongan Lain</span>
                                    <span class="font-semibold text-red-600">Rp {{ number_format($detail->potongan_lain ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-red-50 rounded-lg px-3 -mx-3">
                                    <span class="font-bold text-red-800">TOTAL POTONGAN</span>
                                    <span class="font-bold text-red-800">Rp {{ number_format(($detail->potongan_tidak_hadir ?? 0) + ($detail->potongan_bpjs ?? 0) + ($detail->potongan_lain ?? 0), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Gaji Bersih -->
                    <div class="mt-8 pt-6 border-t-2 border-indigo-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold text-indigo-800">GAJI BERSIH</h3>
                                <p class="text-xs text-slate-400">Setelah dipotong semua komponen</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-extrabold text-indigo-700">Rp {{ number_format($detail->total_gaji_bersih, 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ ucwords(str_replace('_', ' ', $period->status)) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Tangan -->
                    @if($period->status == 'paid')
                    <div class="mt-6 pt-4 border-t border-slate-200 flex justify-between">
                        <div class="text-center">
                            <p class="text-xs text-slate-400">HR Manager</p>
                            <div class="mt-8 mb-2">__________________</div>
                            <p class="text-xs font-semibold">(................................)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-slate-400">Finance</p>
                            <div class="mt-8 mb-2">__________________</div>
                            <p class="text-xs font-semibold">(................................)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-slate-400">Karyawan</p>
                            <div class="mt-8 mb-2">__________________</div>
                            <p class="text-xs font-semibold">(................................)</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 text-center">
                    <p class="text-xs text-slate-400">Dokumen ini dicetak secara otomatis oleh sistem. Tidak memerlukan tanda tangan basah.</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="window.print()" class="inline-flex items-center px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-all duration-200">
                    <span class="material-icons-outlined text-base mr-2">print</span>
                    Cetak Slip
                </button>
                <a href="{{ route('finance.payroll.show', $period->id) }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all duration-200">
                    Kembali
                </a>
            </div>

        </div>
    </div>
</main>

<style media="print">
    @media print {
        body * {
            visibility: hidden;
        }
        #slipGaji, #slipGaji * {
            visibility: visible;
        }
        #slipGaji {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 20px;
            box-shadow: none;
        }
        .main-content, .sidebar-fixed, .container, .flex-1, .p-4, .p-8 {
            margin: 0 !important;
            padding: 0 !important;
        }
        button, .btn, a[href*="print"] {
            display: none !important;
        }
    }
</style>