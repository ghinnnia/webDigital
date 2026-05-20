@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-background-light main-content">
    <div class="flex-1 p-3 sm:p-8">
        <div class="p-6">
            <div class="container mx-auto">
                <div class="mb-4">
                    <a href="{{ route('hr.kpa.index', request()->only(['bulan', 'tahun', 'divisi_id'])) }}" 
                       class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        ← Kembali ke Daftar KPA
                    </a>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">🎯 Setting Target Kerja</h2>
                        <p class="text-gray-500">HR menentukan target, realisasi otomatis diambil dari tugas yang berstatus 'Selesai'.</p>
                        <p class="text-gray-400 text-sm mt-1">Nilai otomatis: (Tugas Selesai ÷ Target) × 100</p>
                    </div>
                    {{-- Tombol untuk memaksa sinkronisasi ulang jika diperlukan --}}
                    <form action="{{ route('hr.kpa.hitung-semua') }}" method="GET">
                        <input type="hidden" name="bulan" value="{{ request('bulan', $bulan) }}">
                        <input type="hidden" name="tahun" value="{{ request('tahun', $tahun) }}">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                            🔄 Sinkronisasi Tugas
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow mb-6 p-4">
                    <form method="GET" action="{{ route('hr.kpa.target-kuantitas') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium mb-1">Bulan</label>
                            <select name="bulan" class="border rounded-lg px-3 py-2">
                                @for($i=1;$i<=12;$i++)
                                <option value="{{ $i }}" {{ request('bulan', $bulan) == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tahun</label>
                            <select name="tahun" class="border rounded-lg px-3 py-2">
                                @for($i=now()->year-2;$i<=now()->year+1;$i++)
                                <option value="{{ $i }}" {{ request('tahun', $tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Divisi</label>
                            <select name="divisi_id" class="border rounded-lg px-3 py-2">
                                <option value="">Semua Divisi</option>
                                @foreach($divisiList as $d)
                                <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Filter</button>
                        </div>
                    </form>
                </div>

                <form method="POST" action="{{ route('hr.kpa.target-kuantitas.store') }}">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ request('bulan', $bulan) }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun', $tahun) }}">

                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Karyawan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Divisi</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500">Target (HR)</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500">Realisasi (Auto)</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500">Nilai Target Kerja</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($karyawan as $index => $k)
                                    @php
                                        $target = $targetExisting[$k->id] ?? null;
                                        $targetVal = $target->target ?? 0;
                                        $realisasiVal = $target->realisasi ?? 0;
                                        $nilai = $targetVal > 0 ? min(100, round(($realisasiVal / $targetVal) * 100)) : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium">{{ $k->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $k->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">{{ $k->divisi->divisi ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="number" name="target[{{ $k->id }}]" 
                                                   class="w-24 text-center border rounded-lg px-3 py-2 bg-blue-50 focus:ring-2 focus:ring-blue-500"
                                                   value="{{ $targetVal }}" min="0" placeholder="0">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{-- Input realisasi diubah jadi readonly agar karyawan tidak bisa manipulasi manual di sini --}}
                                            <input type="number" name="realisasi[{{ $k->id }}]" 
                                                   class="w-24 text-center border rounded-lg px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                                                   value="{{ $realisasiVal }}" readonly>
                                            <div class="text-[10px] text-gray-400 mt-1">Dari status Tugas</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                                {{ $nilai >= 80 ? 'bg-green-100 text-green-700' : ($nilai >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $nilai }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">Tidak ada karyawan ditemukan untuk filter ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Simpan & Update Nilai
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800">📌 Sistem Penilaian Otomatis</h4>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1">
                        <li>• <strong>Target:</strong> Angka ekspektasi yang diinput oleh HR (Contoh: Target 10 tugas selesai).</li>
                        <li>• <strong>Realisasi:</strong> Angka ini diambil secara otomatis dari modul <strong>Monitoring Tugas</strong> (hanya tugas dengan status 'Selesai').</li>
                        <li>• <strong>Sinkronisasi:</strong> Jika data realisasi belum muncul, klik tombol <strong>"Sinkronisasi Tugas"</strong> di pojok kanan atas.</li>
                        <li>• <strong>Hasil Akhir:</strong> Persentase pencapaian akan otomatis masuk sebagai nilai indikator <strong>Target Kerja</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>