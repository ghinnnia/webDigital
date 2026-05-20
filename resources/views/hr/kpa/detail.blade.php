@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-background-light main-content">
<div class="flex-1 p-3 sm:p-8">
<div class="p-6">

<div class="container mx-auto p-6">
    <div class="mb-4">
        <a href="{{ route('hr.kpa.index', request()->only(['bulan', 'tahun', 'divisi_id'])) }}" 
           class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <span class="material-icons-outlined">arrow_back</span>
            Kembali ke Daftar KPA
        </a>
    </div>

    <h2 class="text-2xl font-bold mb-2"><i class="fa-solid fa-chart-simple mr-2"></i>Detail Riwayat Kinerja</h2>
    <h4 class="text-lg mb-6">Karyawan: <strong>{{ $karyawan->name ?? '-' }}</strong></h4>

    @forelse($riwayatKinerja as $periode => $penilaianList)
    @php
        $periodeArr = explode('-', $periode);
        $tahunPeriode = $periodeArr[0];
        $bulanPeriode = $periodeArr[1];
        $namaBulan = \Carbon\Carbon::create()->month($bulanPeriode)->format('F');
    @endphp
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-3">
            <h3 class="font-semibold text-lg">{{ $namaBulan }} {{ $tahunPeriode }}</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aspek</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Indikator</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nilai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bobot</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kontribusi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($penilaianList as $item)
                        <tr>
                            <td class="px-6 py-4">{{ $no++ }}</td>
                            <td class="px-6 py-4">{{ $item->indikator->aspek->nama ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->indikator->nama }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                    {{ $item->nilai >= 80 ? 'bg-green-100 text-green-700' : ($item->nilai >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $item->nilai }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $item->indikator->bobot }}%</td>
                            <td class="px-6 py-4 text-center">
                                @php $kontribusi = ($item->nilai / 100) * $item->indikator->bobot; @endphp
                                {{ number_format($kontribusi, 1) }}%
                            </td>
                            <td class="px-6 py-4">{{ $item->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right font-semibold">Total Nilai Periode:</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $total = 0;
                                    foreach($penilaianList as $item) {
                                        $total += ($item->nilai / 100) * $item->indikator->bobot;
                                    }
                                    $grade = $total >= 90 ? 'A' : ($total >= 75 ? 'B' : ($total >= 60 ? 'C' : 'D'));
                                @endphp
                                <strong class="text-green-600">{{ number_format($total, 1) }}%</strong>
                                <span class="ml-2 px-3 py-1 rounded-full text-sm font-semibold 
                                    {{ $grade == 'A' ? 'bg-green-100 text-green-700' : ($grade == 'B' ? 'bg-blue-100 text-blue-700' : ($grade == 'C' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                                    Grade {{ $grade }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
        <p class="text-yellow-700">Belum ada data riwayat kinerja untuk karyawan ini.</p>
    </div>
    @endforelse
</div>

</div>
</div>
</main>