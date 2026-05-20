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

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i>Input Penilaian Manual</h2>
            <p class="text-gray-500">Karyawan: <strong>{{ $karyawan->name }}</strong> | Periode: {{ $bulan }}/{{ $tahun }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('hr.kpa.penilaian.store') }}">
        @csrf
        <input type="hidden" name="karyawan_id" value="{{ $karyawan->id }}">
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        @foreach($indikatorManual->groupBy('aspek.nama') as $aspekNama => $indikatorList)
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-3">
                <h3 class="font-semibold text-lg">{{ $aspekNama }}</h3>
                <p class="text-sm text-blue-100">Manual Input HR</p>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Indikator</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bobot</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nilai (0-100)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($indikatorList as $indikator)
                            @php
                                $existing = $nilaiExisting[$indikator->id] ?? null;
                                $nilaiSekarang = $existing->nilai ?? 75;
                            @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $indikator->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $indikator->tipe == 'otomatis' ? 'Otomatis' : 'Manual HR' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">{{ $indikator->bobot }}%</td>
                                <td class="px-6 py-4">
                                    <input type="number" 
                                           name="nilai[{{ $indikator->id }}]" 
                                           class="w-24 mx-auto text-center border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           min="0" 
                                           max="100" 
                                           value="{{ $nilaiSekarang }}"
                                           required>
                                    <div class="text-xs text-gray-400 mt-1">0=Kurang, 100=Sangat Baik</div>
                                </td>
                                <td class="px-6 py-4">
                                    <textarea name="catatan[{{ $indikator->id }}]" 
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                              rows="2"
                                              placeholder="Catatan (opsional)">{{ $existing->catatan ?? '' }}</textarea>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Ringkasan Nilai Otomatis -->
        <div class="bg-gradient-to-r from-green-50 to-white rounded-lg shadow border border-green-200 mb-6 overflow-hidden">
            <div class="bg-green-600 text-white px-6 py-3">
                <h3 class="font-semibold text-lg"><i class="fa-solid fa-chart-simple mr-2"></i>Ringkasan Nilai Otomatis</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">
                    Nilai untuk <strong>Target Tugas, Ketepatan Waktu, dan Kuantitas Kerja</strong> dihitung otomatis oleh sistem.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-gray-600"><i class="fa-solid fa-clipboard-list mr-1"></i>Target Tugas</p>
                        <p class="text-2xl font-bold text-blue-600" id="previewTargetTugas">Menghitung...</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-gray-600"><i class="fa-solid fa-clock mr-1"></i>Ketepatan Waktu</p>
                        <p class="text-2xl font-bold text-yellow-600" id="previewKetepatan">Menghitung...</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-gray-600"><i class="fa-solid fa-chart-simple mr-1"></i>Kuantitas Kerja</p>
                        <p class="text-2xl font-bold text-green-600" id="previewKuantitas">Menghitung...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('hr.kpa.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan Penilaian</button>
        </div>
    </form>
</div>

</div>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('hr.kpa.preview-nilai', ['karyawan_id' => $karyawan->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}")
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('previewTargetTugas').innerHTML = data.target_tugas + '%';
                document.getElementById('previewKetepatan').innerHTML = data.ketepatan_waktu + '%';
                document.getElementById('previewKuantitas').innerHTML = data.kuantitas + '%';
            } else {
                document.getElementById('previewTargetTugas').innerHTML = 'Belum dihitung';
                document.getElementById('previewKetepatan').innerHTML = 'Belum dihitung';
                document.getElementById('previewKuantitas').innerHTML = 'Belum dihitung';
            }
        })
        .catch(err => {
            document.getElementById('previewTargetTugas').innerHTML = 'Belum dihitung';
            document.getElementById('previewKetepatan').innerHTML = 'Belum dihitung';
            document.getElementById('previewKuantitas').innerHTML = 'Belum dihitung';
        });
});
</script>