@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-slate-50 main-content">
    <div class="flex-1 p-3 sm:p-8">
        <div class="p-6">

            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900"><i class="fa-solid fa-chart-simple mr-2"></i>Kinerja Pegawai (KPA)</h1>
                        <p class="text-gray-500 text-sm mt-1 flex items-center gap-2">
                            <span class="material-icons-outlined text-sm">analytics</span>
                            Monitoring dan penilaian kinerja karyawan
                        </p>
                    </div>
                    <div class="flex gap-2 mt-4 md:mt-0">
                        <a href="{{ route('hr.kpa.target-kuantitas', request()->all()) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl shadow-sm transition-all active:scale-95">
                            <span class="material-icons-outlined text-sm">track_changes</span>
                            Setting Target
                        </a>
                        <a href="{{ route('hr.kpa.export-pdf', request()->all()) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-sm transition-all active:scale-95">
                            <span class="material-icons-outlined text-sm">picture_as_pdf</span>
                            Export PDF
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center gap-3">
                <span class="material-icons-outlined">check_circle</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm flex items-center gap-3">
                <span class="material-icons-outlined">error</span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                        <span class="material-icons-outlined text-gray-400 text-base">filter_list</span>
                        Parameter Filter
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <form method="GET" action="{{ route('hr.kpa.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div class="min-w-[160px]">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1">Tampilan</label>
                            <select name="view" class="w-full border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="bulanan" {{ request('view', 'bulanan') == 'bulanan' ? 'selected' : '' }}>Mode Bulanan</option>
                                <option value="tahunan" {{ request('view') == 'tahunan' ? 'selected' : '' }}>Mode Tahunan</option>
                            </select>
                        </div>
                        @if(request('view', 'bulanan') == 'bulanan')
                        <div class="min-w-[160px]">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1">Pilih Bulan</label>
                            <select name="bulan" class="w-full border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                @for($i=1;$i<=12;$i++)
                                <option value="{{ $i }}" {{ request('bulan', $bulan) == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, (int)$i, 1)->format('F') }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        @endif
                        <div class="min-w-[120px]">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1">Pilih Tahun</label>
                            <select name="tahun" class="w-full border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                @for($i=now()->year-2;$i<=now()->year+1;$i++)
                                <option value="{{ $i }}" {{ request('tahun', $tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="min-w-[180px]">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1">Divisi Kerja</label>
                            <select name="divisi_id" class="w-full border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">Seluruh Divisi</option>
                                @foreach($divisiList as $d)
                                <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all">Filter</button>
                            <a href="{{ route('hr.kpa.index') }}" class="px-6 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Reset</a>
                        </div>
                        <div class="ml-auto">
                            <button type="button" 
                                onclick="document.getElementById('formHitungUlang').submit();"
                                class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-sm font-bold shadow-md shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">refresh</span>
                                Kalkulasi Ulang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <form id="formHitungUlang" action="{{ route('hr.kpa.hitung-semua') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="bulan" value="{{ request('bulan', $bulan) }}">
                <input type="hidden" name="tahun" value="{{ request('tahun', $tahun) }}">
                <input type="hidden" name="divisi_id" value="{{ request('divisi_id') }}">
            </form>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 group hover:border-indigo-300 transition-all">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Staff</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 group hover:border-indigo-300 transition-all">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rata-rata</p>
                    <p class="text-2xl font-black text-indigo-600 mt-1">{{ round($stats['rata_rata'], 1) }}%</p>
                </div>
                <div class="bg-emerald-50 rounded-2xl shadow-sm border border-emerald-100 p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Grade A</p>
                        <span class="text-[10px] px-1.5 py-0.5 bg-emerald-500 text-white rounded font-black">SUPERIOR</span>
                    </div>
                    <p class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['grade_a'] }}</p>
                </div>
                <div class="bg-blue-50 rounded-2xl shadow-sm border border-blue-100 p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Grade B</p>
                        <span class="text-[10px] px-1.5 py-0.5 bg-blue-500 text-white rounded font-black">GOOD</span>
                    </div>
                    <p class="text-2xl font-black text-blue-700 mt-1">{{ $stats['grade_b'] }}</p>
                </div>
                <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-100 p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Grade C</p>
                        <span class="text-[10px] px-1.5 py-0.5 bg-amber-500 text-white rounded font-black">AVERAGE</span>
                    </div>
                    <p class="text-2xl font-black text-amber-700 mt-1">{{ $stats['grade_c'] }}</p>
                </div>
                <div class="bg-red-50 rounded-2xl shadow-sm border border-red-100 p-5">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest">Grade D</p>
                        <span class="text-[10px] px-1.5 py-0.5 bg-red-500 text-white rounded font-black">LOW</span>
                    </div>
                    <p class="text-2xl font-black text-red-700 mt-1">{{ $stats['grade_d'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 bg-white border-b flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Daftar Penilaian Kinerja</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Rincian indikator penilaian per individu</p>
                    </div>
                    <a href="{{ route('hr.kpa.aspek-indikator') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-sm font-bold hover:bg-indigo-100 transition-all">
                        <span class="material-icons-outlined text-sm">settings</span>
                        Konfigurasi Aspek
                    </a>
                </div>
                <div class="overflow-x-auto scroll-indicator">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-gray-200">
                                <th rowspan="2" class="px-6 py-5 text-left font-bold text-slate-700 w-16">No</th>
                                <th rowspan="2" class="px-6 py-5 text-left font-bold text-slate-700">Identitas Karyawan</th>
                                <th rowspan="2" class="px-6 py-5 text-left font-bold text-slate-700">Divisi</th>
                                
                                @foreach($aspekList as $aspek)
                                    <th colspan="{{ $aspek->indikator->count() }}" class="px-4 py-3 text-center border-l border-gray-200" 
                                        style="background-color: {{ $loop->index % 2 == 0 ? '#f0f7ff' : '#f0fff4' }}">
                                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $aspek->nama }}</div>
                                        <div class="text-xs font-bold text-slate-700">{{ $aspek->bobot }}% Bobot</div>
                                    </th>
                                @endforeach

                                <th rowspan="2" class="px-6 py-5 text-center border-l border-gray-200 font-bold bg-indigo-600 text-white">SKOR</th>
                                <th rowspan="2" class="px-6 py-5 text-center font-bold text-slate-700">Grade</th>
                                <th rowspan="2" class="px-6 py-5 text-center font-bold text-slate-700">Aksi</th>
                            </tr>
                            <tr class="bg-slate-50/50 border-b border-gray-100">
                                @foreach($aspekList as $aspek)
                                    @foreach($aspek->indikator as $ind)
                                        <th class="px-3 py-3 text-center text-[10px] border-l border-gray-100 font-black text-slate-400 uppercase tracking-tighter">
                                            {{ $ind->nama }}
                                        </th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($kinerjaList as $index => $item)
                            @php
                                $karyawan = $item['karyawan'];
                                $penilaian = $item['penilaian'] ?? [];
                                
                                $gradeColors = [
                                    'A' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
                                    'B' => 'bg-blue-100 text-blue-700 ring-1 ring-blue-200',
                                    'C' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
                                    'D' => 'bg-red-100 text-red-700 ring-1 ring-red-200',
                                ];
                                $colorClass = $gradeColors[$item['grade']] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 text-gray-400 font-mono text-xs">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-black text-slate-800">{{ $karyawan->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold">{{ $karyawan->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase">
                                        {{ $karyawan->divisi->divisi ?? '-' }}
                                    </span>
                                </td>
                                
                                @foreach($aspekList as $aspek)
                                    @foreach($aspek->indikator as $ind)
                                        <td class="px-3 py-4 text-center border-l border-gray-50">
                                            @php $nilai = $penilaian[$ind->nama] ?? null; @endphp
                                            @if($nilai !== null)
                                                <span class="font-bold text-slate-700">{{ $nilai }}%</span>
                                            @else
                                                <span class="text-slate-200">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                @endforeach
                                
                                <td class="px-6 py-4 text-center border-l border-indigo-100 font-black text-indigo-700 bg-indigo-50/50">
                                    {{ $item['total_nilai'] }}%
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex w-8 h-8 rounded-xl items-center justify-center text-xs font-black shadow-sm {{ $colorClass }}">
                                        {{ $item['grade'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('hr.kpa.penilaian', ['karyawan_id' => $karyawan->id, 'bulan' => request('bulan', $bulan), 'tahun' => request('tahun', $tahun)]) }}" 
                                       class="w-8 h-8 inline-flex items-center justify-center bg-white border border-gray-200 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Input Nilai">
                                        <span class="material-icons-outlined text-sm">edit_note</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100" class="px-6 py-20 text-center">
                                    <span class="material-icons-outlined text-5xl text-gray-200 block mb-3">folder_off</span>
                                    <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Data KPA tidak ditemukan untuk periode ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    const viewSelect = document.querySelector('select[name="view"]');
    if (viewSelect) {
        viewSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
</script>

<style>
    .scroll-indicator::-webkit-scrollbar {
        height: 8px;
    }
    .scroll-indicator::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .scroll-indicator::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .scroll-indicator::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>