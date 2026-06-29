{{-- resources/views/hr/gaji/index.blade.php --}}
@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 to-slate-100 main-content">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto max-w-full">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight"><i class="fa-solid fa-money-bill-wave text-indigo-600 mr-2"></i>Manajemen Gaji</h1>
                    <p class="text-slate-500 mt-1">Periode: <span class="font-semibold text-indigo-600">{{ \Carbon\Carbon::createFromDate(null, (int)$bulan, 1)->format('F') }} {{ $tahun }}</span></p>
                    <p class="text-xs text-green-600 mt-1"><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Data gaji + tunjangan akan otomatis masuk ke Finance</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="btnShowTemplateList" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-200 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <i class="fa-solid fa-clipboard-list mr-2"></i> Lihat Template Gaji
                    </button>
                    <a href="{{ route('hr.tunjangan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-200 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Kelola Tunjangan
                    </a>
                </div>
            </div>

            <!-- Notifikasi -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-white border-b border-slate-100">
                    <form method="GET" action="{{ route('hr.gaji.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Bulan</label>
                            <select name="bulan" class="border border-slate-200 rounded-xl px-4 py-2 text-sm">
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $i, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Tahun</label>
                            <select name="tahun" class="border border-slate-200 rounded-xl px-4 py-2 text-sm">
                                @for($i=now()->year-2; $i<=now()->year+1; $i++)
                                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Divisi</label>
                            <select name="divisi_id" class="border border-slate-200 rounded-xl px-4 py-2 text-sm">
                                <option value="">Semua Divisi</option>
                                @foreach($divisiList as $d)
                                    <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Role</label>
                            <select name="role" class="border border-slate-200 rounded-xl px-4 py-2 text-sm">
                                <option value="">Semua Role</option>
                                <option value="general_manager" {{ request('role') == 'general_manager' ? 'selected' : '' }}>General Manager</option>
                                <option value="manager_divisi" {{ request('role') == 'manager_divisi' ? 'selected' : '' }}>Manager Divisi</option>
                                <option value="karyawan" {{ request('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                <option value="finance" {{ request('role') == 'finance' ? 'selected' : '' }}>Finance</option>
                                <option value="hr" {{ request('role') == 'hr' ? 'selected' : '' }}>HR</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">Filter</button>
                            <a href="{{ route('hr.gaji.index') }}" class="px-5 py-2 ml-2 bg-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-300 transition">Reset</a>
                            <button type="button" id="applyTemplateToAll" class="px-5 py-2 ml-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition">
                                <i class="fa-solid fa-clipboard-check mr-2"></i> Apply Template ke Semua
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form Input Gaji -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-white border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-800 text-lg">Input Gaji Karyawan</h2>
                            <p class="text-xs text-slate-400 mt-0.5">* Gaji pokok + tunjangan akan dikirim ke Finance untuk diproses</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('hr.gaji.store') }}">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                        <table class="w-full text-sm" id="gajiTable">
                            <thead class="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">No</th>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">Karyawan</th>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">Role</th>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">Divisi</th>
                                    <th class="px-4 py-4 text-left font-bold text-slate-700">Tunjangan yang Didapat</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700">Gaji Pokok</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700 bg-emerald-50 text-emerald-700">Total Tunjangan</th>
                                    <th class="px-4 py-4 text-right font-bold text-slate-700 bg-indigo-50 text-indigo-700">Total Gaji</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($karyawan as $index => $k)
                                @php
                                    $gaji = $gajiExisting[$k->id] ?? null;
                                    $tunjanganKaryawan = isset($tunjanganData[$k->id]) ? $tunjanganData[$k->id] : collect();
                                    $totalTunjangan = $tunjanganKaryawan->sum('nominal');
                                    
                                    $template = $gajiTemplates->where('role', $k->role)->where('divisi_id', $k->divisi_id)->first() 
                                                ?? $gajiTemplates->where('role', $k->role)->where('divisi_id', null)->first();
                                    
                                    $gajiPokok = $gaji->gaji_pokok ?? ($template->gaji_pokok ?? 5000000);
                                    
                                    $total = $gaji->total_gaji ?? ($gajiPokok + $totalTunjangan);
                                @endphp
                                <tr class="hover:bg-indigo-50/30 transition-all duration-150">
                                    <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($k->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-800">{{ $k->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $k->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ ucfirst(str_replace('_', ' ', $k->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                            @if($k->divisi && $k->divisi->divisi == 'IT') bg-purple-100 text-purple-700
                                            @elseif($k->divisi && $k->divisi->divisi == 'HR') bg-pink-100 text-pink-700
                                            @elseif($k->divisi && $k->divisi->divisi == 'Marketing') bg-orange-100 text-orange-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ $k->divisi->divisi ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs">
                                            @if($tunjanganKaryawan && $tunjanganKaryawan->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($tunjanganKaryawan as $tunjangan)
                                                        <span class="px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">
                                                        {{ $tunjangan->tunjanganMaster->nama ?? $tunjangan->nama ?? 'Tunjangan' }}
                                                            (Rp {{ number_format($tunjangan->nominal, 0, ',', '.') }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-slate-400 text-xs">Tidak ada tunjangan</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text"
                                               value="Rp {{ number_format($gajiPokok, 0, ',', '.') }}"
                                               class="gaji-pokok formatted-gaji-pokok w-36 text-right bg-white border border-slate-200 focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 rounded-lg px-3 py-1.5 text-sm transition-all duration-150"
                                               placeholder="0">
                                        <input type="hidden" 
                                               name="gaji_pokok[{{ $k->id }}]" 
                                               value="{{ $gajiPokok }}"
                                               class="gaji-pokok-hidden">
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-emerald-600 bg-emerald-50/30">
                                        Rp {{ number_format($totalTunjangan, 0, ',', '.') }}
                                        <input type="hidden" name="tunjangan[{{ $k->id }}]" value="{{ $totalTunjangan }}" class="tunjangan-value">
                                        <input type="hidden" name="tunjangan_detail[{{ $k->id }}]" value='@json($tunjanganKaryawan->toArray())'>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-indigo-600 total-gaji">
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-100 border-t-2 border-slate-200">
                                <tr>
                                    <td colspan="7" class="px-4 py-4 text-right font-bold text-slate-700">GRAND TOTAL:</td>
                                    <td class="px-4 py-4 text-right font-bold text-indigo-700 text-lg grand-total">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="p-6 bg-white border-t border-slate-100 flex justify-end gap-3">
                        @if(request('role') !== 'karyawan')
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Semua Gaji
                        </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Informasi -->
            <div class="mt-6 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm text-blue-700">
                        <i class="fa-solid fa-lightbulb text-amber-500 mr-2"></i> <strong>Informasi:</strong> 
                        <ul class="ml-4 mt-1">
                            <li>• Tunjangan yang didapat karyawan sudah ditentukan di halaman <strong>Data Karyawan</strong></li>
                            <li>• Total Gaji = Gaji Pokok + Total Tunjangan</li>
                            <li>• Setelah klik "Simpan", data akan langsung masuk ke Finance</li>
                        </ul>
                    </span>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- MODAL TEMPLATE GAJI LIST -->
<div id="templateListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-white border-b border-slate-200 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-lg"><i class="fa-solid fa-clipboard-list text-indigo-600 mr-2"></i>Template Gaji Default</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar gaji pokok berdasarkan Role dan Divisi</p>
                </div>
            </div>
            <button id="closeTemplateListModal" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
        <div class="overflow-x-auto p-4">
            <table class="w-full text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-slate-700">No</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-700">Role</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-700">Divisi</th>
                        <th class="px-4 py-3 text-right font-bold text-slate-700">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right font-bold text-slate-700">Tunjangan Tetap</th>
                        <th class="px-4 py-3 text-right font-bold text-slate-700">Tunjangan Kinerja</th>
                        <th class="px-4 py-3 text-right font-bold text-slate-700">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($gajiTemplates as $index => $template)
                    <tr>
                        <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium">{{ ucfirst(str_replace('_', ' ', $template->role)) }}</td>
                        <td class="px-4 py-3">
                            @if($template->divisi)
                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                    @if($template->divisi->divisi == 'IT') bg-purple-100 text-purple-700
                                    @elseif($template->divisi->divisi == 'HR') bg-pink-100 text-pink-700
                                    @elseif($template->divisi->divisi == 'Marketing') bg-orange-100 text-orange-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $template->divisi->divisi }}
                                </span>
                            @else
                                <span class="text-slate-400">Semua Divisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($template->gaji_pokok, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($template->tunjangan_tetap, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($template->tunjangan_kinerja, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-indigo-600">
                            Rp {{ number_format($template->gaji_pokok + $template->tunjangan_tetap + $template->tunjangan_kinerja, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right font-bold text-slate-700">Rata-rata Gaji Pokok:</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-600">
                            Rp {{ number_format($gajiTemplates->avg('gaji_pokok'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-slate-200 text-right">
            <button id="closeTemplateListModalBtn" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">Tutup</button>
        </div>
    </div>
</div>

<script>
    // Hitung total per baris
    function parseNumericValue(value) {
        if (!value) {
            return 0;
        }
        return parseFloat(value.toString().replace(/[^0-9\-\.]/g, '')) || 0;
    }

    function formatCurrency(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    function calculateRowTotal(row) {
        const gajiPokokInput = row.querySelector('.gaji-pokok');
        const gajiPokok = parseNumericValue(gajiPokokInput?.value);
        const tunjangan = parseNumericValue(row.querySelector('.tunjangan-value')?.value);
        
        const total = gajiPokok + tunjangan;
        const totalCell = row.querySelector('.total-gaji');
        if (totalCell) {
            totalCell.textContent = formatCurrency(total);
        }
        return total;
    }

    // Hitung grand total
    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('#gajiTable tbody tr').forEach(row => {
            grandTotal += calculateRowTotal(row);
        });
        const grandTotalCell = document.querySelector('.grand-total');
        if (grandTotalCell) {
            grandTotalCell.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
        }
    }

    function setFormattedGajiPokokValue(input) {
        const rawValue = parseNumericValue(input.value);
        const hiddenInput = input.closest('td')?.querySelector('.gaji-pokok-hidden');
        if (hiddenInput) {
            hiddenInput.value = rawValue;
        }
        input.value = rawValue > 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(rawValue) : '';
    }

    // Event listeners untuk input
    document.querySelectorAll('.formatted-gaji-pokok').forEach(input => {
        input.addEventListener('input', function() {
            const cleaned = this.value.replace(/[^0-9]/g, '');
            this.value = cleaned.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            setFormattedGajiPokokValue(this);
            calculateRowTotal(this.closest('tr'));
            calculateGrandTotal();
        });

        input.addEventListener('blur', function() {
            setFormattedGajiPokokValue(this);
        });
    });

    // Hitung awal
    setTimeout(() => {
        calculateGrandTotal();
    }, 100);

      // Apply template ke semua karyawan
    const applyTemplateBtn = document.getElementById('applyTemplateToAll');
    if (applyTemplateBtn) {
        applyTemplateBtn.addEventListener('click', async function() {
            if (confirm('Yakin ingin menerapkan template gaji default ke semua karyawan?\n\nData gaji yang sudah diisi sebelumnya akan ditimpa!')) {
                try {
                    const response = await fetch('{{ route("hr.gaji.apply-template") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            bulan: {{ $bulan }},
                            tahun: {{ $tahun }},
                            divisi_id: '{{ request('divisi_id') }}',
                            role: '{{ request('role') }}'
                        })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        location.reload();
                    } else {
                        alert('Gagal menerapkan template: ' + result.message);
                    }
                } catch (error) {
                    alert('Terjadi kesalahan: ' + error.message);
                }
            }
        });
    }

    // Modal Template List
    const templateListModal = document.getElementById('templateListModal');
    const btnShowTemplateList = document.getElementById('btnShowTemplateList');
    const closeTemplateListModal = document.getElementById('closeTemplateListModal');
    const closeTemplateListModalBtn = document.getElementById('closeTemplateListModalBtn');

    function openTemplateListModal() {
        if (templateListModal) {
            templateListModal.classList.remove('hidden');
            templateListModal.classList.add('flex');
        }
    }

    function closeTemplateListModalFunc() {
        if (templateListModal) {
            templateListModal.classList.add('hidden');
            templateListModal.classList.remove('flex');
        }
    }

    if (btnShowTemplateList) {
        btnShowTemplateList.addEventListener('click', openTemplateListModal);
    }
    if (closeTemplateListModal) {
        closeTemplateListModal.addEventListener('click', closeTemplateListModalFunc);
    }
    if (closeTemplateListModalBtn) {
        closeTemplateListModalBtn.addEventListener('click', closeTemplateListModalFunc);
    }

    // Tutup modal jika klik di luar
    if (templateListModal) {
        templateListModal.addEventListener('click', function(e) {
            if (e.target === templateListModal) {
                closeTemplateListModalFunc();
            }
        });
    }
</script>