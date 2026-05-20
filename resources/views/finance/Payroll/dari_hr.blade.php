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
                            <h1 class="text-2xl font-bold text-slate-800 tracking-tight"><i class="fa-solid fa-download mr-2"></i>Ambil Data Gaji dari HR</h1>
                            <p class="text-slate-500 mt-1">Periode: <span class="font-semibold text-indigo-600">{{ \Carbon\Carbon::createFromDate(null, (int)$bulan, 1)->format('F') }} {{ $tahun }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Total Karyawan</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $dataGaji->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fa-solid fa-users text-indigo-600"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Total Gaji</p>
                            <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalGaji, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Gaji -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-white border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fa-solid fa-database text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-800 text-lg">Data Gaji dari HR</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Karyawan yang sudah dikirim HR dan siap diproses</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('finance.payroll.ambil-dari-hr') }}" method="POST">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100">
                                <tr class="border-b-2 border-slate-200">
                                    <th class="w-10 px-4 py-4 text-center">
                                        <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">No</th>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">Karyawan</th>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">Divisi</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700">Gaji Pokok</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700">Tunjangan Tetap</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700">Tunjangan Kinerja</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700">Bonus</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700">Potongan</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700">Total Gaji</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($dataGaji as $index => $gaji)
                                <tr class="hover:bg-indigo-50/30 transition-all duration-150">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="selected[]" value="{{ $gaji->id }}" class="check-item rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-800">{{ $gaji->karyawan->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">{{ $gaji->karyawan->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $gaji->karyawan->divisi->divisi ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($gaji->tunjangan_tetap, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($gaji->tunjangan_kinerja, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($gaji->bonus, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-mono text-red-600">Rp {{ number_format($gaji->potongan_bpjs + $gaji->potongan_lain, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-indigo-600">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fa-solid fa-inbox text-5xl text-slate-300 mb-3"></i>
                                            <p class="text-slate-500 font-medium">Tidak ada data gaji dari HR</p>
                                            <p class="text-xs text-slate-400 mt-1">Pastikan HR sudah mengirim data gaji untuk periode ini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($dataGaji->count() > 0)
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-200 transition-all duration-200">
                            <i class="fa-solid fa-download text-base mr-2"></i>
                            Ambil Data yang Dipilih
                        </button>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Informasi -->
            <div class="mt-6 p-4 bg-blue-50 rounded-2xl border border-blue-200">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <p class="text-sm text-blue-700">
                        <strong>Informasi:</strong> Centang karyawan yang ingin diproses, lalu klik "Ambil Data yang Dipilih". 
                        Data akan masuk ke periode penggajian dan siap diproses.
                    </p>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    document.getElementById('selectAll')?.addEventListener('change', function(e) {
        document.querySelectorAll('.check-item').forEach(cb => cb.checked = e.target.checked);
    });
</script>