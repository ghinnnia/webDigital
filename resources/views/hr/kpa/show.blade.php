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

    <h2 class="text-2xl font-bold mb-6">📋 Detail Penilaian KPA</h2>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Nama Karyawan</p>
                <p class="font-semibold text-lg">{{ $kinerja->karyawan->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Divisi</p>
                <p class="font-semibold text-lg">{{ $kinerja->karyawan->divisi->divisi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Periode Penilaian</p>
                <p class="font-semibold text-lg">{{ \Carbon\Carbon::create()->month($kinerja->bulan)->format('F') }} {{ $kinerja->tahun }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Indikator</p>
                <p class="font-semibold text-lg">{{ $kinerja->indikator->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Nilai</p>
                <p class="text-2xl font-bold text-blue-600">{{ $kinerja->nilai ?? 0 }}%</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $kinerja->nilai ?? 0 }}%"></div>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-500">Catatan</p>
                <p class="text-gray-700">{{ $kinerja->catatan ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</main>