<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $slip->user->name ?? 'Karyawan' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media print {
            body * { visibility: hidden; }
            #slipGaji, #slipGaji * { visibility: visible; }
            #slipGaji { position: absolute; top: 0; left: 0; width: 100%; margin: 0; padding: 20px; }
            button, .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    @include('karyawan.templet.header')

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-4xl mx-auto">
            <!-- Tombol Kembali -->
            <div class="mb-4 no-print">
                <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-800">
                    ← Kembali
                </a>
            </div>

            <!-- Slip Gaji -->
            <div id="slipGaji" class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold">SLIP GAJI</h2>
                            <p class="text-indigo-200 mt-1">Periode: {{ $slip->payrollPeriod->nama_periode ?? '-' }}</p>
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
                            <p class="text-xs text-slate-400 uppercase">Nama Karyawan</p>
                            <p class="text-lg font-bold text-slate-800">{{ $slip->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase">Divisi</p>
                            <p class="text-lg font-semibold text-slate-700">{{ $slip->divisi ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Rincian Gaji -->
                <div class="px-8 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Pendapatan -->
                        <div>
                            <h3 class="font-bold text-emerald-600 border-b pb-2 mb-4">PENDAPATAN</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span>Gaji Pokok</span>
                                    <span>Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tunjangan Tetap</span>
                                    <span>Rp {{ number_format($slip->tunjangan_tetap ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tunjangan Kinerja</span>
                                    <span>Rp {{ number_format($slip->tunjangan_kinerja ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Bonus</span>
                                    <span>Rp {{ number_format($slip->bonus ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between bg-blue-50 p-2 rounded">
                                    <span class="font-semibold">Upah Lembur</span>
                                    <span class="font-semibold text-blue-600">Rp {{ number_format($slip->upah_lembur ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t font-bold">
                                    <span>TOTAL PENDAPATAN</span>
                                    <span class="text-emerald-600">Rp {{ number_format(($slip->gaji_pokok ?? 0) + ($slip->tunjangan_tetap ?? 0) + ($slip->tunjangan_kinerja ?? 0) + ($slip->bonus ?? 0) + ($slip->upah_lembur ?? 0), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Potongan -->
                        <div>
                            <h3 class="font-bold text-red-600 border-b pb-2 mb-4">POTONGAN</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span>Potongan Tidak Hadir</span>
                                    <span class="text-red-600">Rp {{ number_format($slip->potongan_tidak_hadir ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Potongan BPJS</span>
                                    <span class="text-red-600">Rp {{ number_format($slip->potongan_bpjs ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t font-bold">
                                    <span>TOTAL POTONGAN</span>
                                    <span class="text-red-600">Rp {{ number_format(($slip->potongan_tidak_hadir ?? 0) + ($slip->potongan_bpjs ?? 0), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gaji Bersih -->
                    <div class="mt-6 pt-4 border-t-2 border-indigo-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-indigo-800">GAJI BERSIH</h3>
                            <p class="text-2xl font-bold text-indigo-700">Rp {{ number_format($slip->total_gaji_bersih, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Tanda Tangan -->
                    <div class="mt-6 pt-4 border-t grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-slate-400">Finance</p>
                            <div class="mt-8 mb-1">
                                <svg width="100" height="40" viewBox="0 0 200 30">
                                    <path d="M10,20 Q30,10 50,20 Q70,25 90,15 Q110,10 130,20 Q150,25 170,15 Q185,10 190,18" fill="none" stroke="#333" stroke-width="1"/>
                                    <text x="5" y="28" font-size="8" fill="#666">Finance Staff</text>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold">(Finance)</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">HR Manager</p>
                            <div class="mt-8 mb-1">
                                <svg width="100" height="40" viewBox="0 0 200 30">
                                    <path d="M10,18 Q30,22 50,18 Q70,14 90,20 Q110,22 130,16 Q150,14 170,20 Q185,24 190,18" fill="none" stroke="#333" stroke-width="1"/>
                                    <text x="5" y="28" font-size="8" fill="#666">HR Manager</text>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold">(HR Manager)</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Karyawan</p>
                            <div class="mt-8 mb-1">
                                <svg width="100" height="40" viewBox="0 0 200 30">
                                    <path d="M10,18 Q40,14 70,20 Q100,24 130,16 Q160,12 190,18" fill="none" stroke="#333" stroke-width="1"/>
                                    <text x="5" y="28" font-size="8" fill="#666">{{ substr($slip->user->name ?? 'Karyawan', 0, 15) }}</text>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold">({{ $slip->user->name ?? 'Karyawan' }})</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-4 bg-slate-50 border-t text-center text-xs text-slate-400">
                    Dokumen ini dicetak secara otomatis oleh sistem.
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 mt-6 no-print">
                <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700">
                    🖨️ Cetak Slip
                </button>
                <a href="{{ route('karyawan.slip-gaji.index') }}" class="px-5 py-2.5 bg-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-400">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</body>
</html>