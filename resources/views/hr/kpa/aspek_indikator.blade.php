@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-slate-50 main-content font-sans">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto">
            <div class="mb-6">
                <a href="{{ route('hr.kpa.index', request()->only(['bulan', 'tahun', 'divisi_id'])) }}" 
                   class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    <span class="material-icons-outlined text-base">arrow_back</span>
                    Kembali ke Daftar KPA
                </a>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">⚙️ Kelola Aspek & Indikator</h2>
                    <p class="text-slate-500 mt-1">Konfigurasi bobot penilaian dan parameter indikator KPA.</p>
                    <div class="flex flex-wrap gap-4 mt-3">
                        <span class="flex items-center gap-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            OTOMATIS: Target, Waktu, Kuantitas
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-semibold bg-amber-100 text-amber-700 px-3 py-1 rounded-full">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            MANUAL: Kualitas, Perilaku, Budaya
                        </span>
                    </div>
                </div>
                <button type="button" onclick="openModalTambahAspek()" 
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">
                    <span class="material-icons-outlined mr-2">add</span>
                    Tambah Aspek
                </button>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-sm flex items-center gap-3">
                <span class="material-icons-outlined text-emerald-500">check_circle</span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-sm flex items-center gap-3">
                <span class="material-icons-outlined text-rose-500">error</span>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 gap-8">
                @foreach($aspekList as $aspek)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all hover:shadow-md">
                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-center sm:text-left">
                            <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $aspek->nama }}</h3>
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-700 mt-1">
                                BOBOT TOTAL: {{ $aspek->bobot }}%
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openModalTambahIndikator({{ $aspek->id }})" 
                                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 hover:border-blue-400 hover:text-blue-600 transition-all">
                                <span class="material-icons-outlined text-sm mr-1.5">add_circle_outline</span>
                                Tambah Indikator
                            </button>
                            <button type="button" onclick="hapusAspek({{ $aspek->id }}, '{{ $aspek->nama }}')" 
                                    class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Aspek">
                                <span class="material-icons-outlined">delete_sweep</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Indikator</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Bobot</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($aspek->indikator as $index => $indikator)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 text-sm text-slate-400 font-medium">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-slate-700">{{ $indikator->nama }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-bold text-slate-600">{{ $indikator->bobot }}%</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($indikator->tipe == 'otomatis')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                Otomatis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">
                                                Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center">
                                            @if($indikator->is_active)
                                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50" title="Aktif"></span>
                                            @else
                                                <span class="h-2.5 w-2.5 rounded-full bg-slate-300 ring-4 ring-slate-100" title="Nonaktif"></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button" onclick="hapusIndikator({{ $indikator->id }}, '{{ $indikator->nama }}')" 
                                                class="text-slate-400 hover:text-rose-600 transition-colors">
                                            <span class="material-icons-outlined text-xl">delete</span>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <span class="material-icons-outlined text-4xl mb-2">inventory_2</span>
                                            <p class="text-sm">Belum ada indikator untuk aspek ini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-blue-900 rounded-2xl p-6 mt-10 shadow-xl shadow-blue-100 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-icons-outlined">info</span>
                        <h4 class="font-bold text-lg">Panduan Penilaian</h4>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-sm font-bold mb-2 text-blue-200 uppercase tracking-widest">Sistem Otomatis</p>
                            <ul class="text-xs space-y-2 text-blue-50">
                                <li class="flex justify-between"><span>Target Kerja</span> <strong>(Realisasi / Target) × 100%</strong></li>
                                <li class="flex justify-between"><span>Ketepatan Waktu</span> <strong>100% - (Terlambat × 10%)</strong></li>
                                <li class="flex justify-between"><span>Kuantitas Kerja</span> <strong>(Selesai / Total) × 100%</strong></li>
                            </ul>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-xs leading-relaxed">
                            <p class="text-sm font-bold mb-2 text-blue-200 uppercase tracking-widest">Input Manual</p>
                            <p>Terdapat <strong>11 indikator manual</strong> lainnya. HR diwajibkan melakukan input nilai secara objektif dalam rentang <strong>0-100%</strong> pada menu input KPA setelah aspek ini dikonfigurasi.</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500 rounded-full opacity-20"></div>
            </div>
        </div>
    </div>
</main>
<script>
    // Buka Modal Aspek
    function openModalTambahAspek() {
        document.getElementById('modalAspek').classList.remove('hidden');
        document.getElementById('modalAspek').classList.add('flex');
    }

    function closeModalAspek() {
        document.getElementById('modalAspek').classList.add('hidden');
        document.getElementById('modalAspek').classList.remove('flex');
    }

    // Buka Modal Indikator
    function openModalTambahIndikator(aspekId) {
        document.getElementById('aspek_id').value = aspekId;
        document.getElementById('modalIndikator').classList.remove('hidden');
        document.getElementById('modalIndikator').classList.add('flex');
    }

    function closeModalIndikator() {
        document.getElementById('modalIndikator').classList.add('hidden');
        document.getElementById('modalIndikator').classList.remove('flex');
    }

    // Tutup modal jika klik di luar area modal
    window.onclick = function(event) {
        if (event.target.id.includes('modal')) {
            closeModalAspek();
            closeModalIndikator();
        }
    }
</script>

<style>
    .modal-active { @apply flex items-center justify-center; }
    .main-content { min-height: 100vh; }
</style>

<div id="modalAspek" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 transition-all p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-xl font-bold text-slate-800 tracking-tight">Tambah Aspek Baru</h3>
            <button onclick="closeModalAspek()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('hr.kpa.aspek.store') }}" method="POST">
            @csrf
            <div class="p-8">
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide text-[10px]">Nama Aspek</label>
                    <input type="text" name="nama" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" required placeholder="Misal: Perilaku Kerja">
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide text-[10px]">Bobot (%)</label>
                    <input type="number" name="bobot" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" required min="0" max="100">
                    <p class="text-[11px] text-slate-500 mt-2 italic flex items-center gap-1">
                        <span class="material-icons-outlined text-sm">tips_and_updates</span>
                        Total bobot semua aspek harus berjumlah 100%
                    </p>
                </div>
            </div>
            <div class="px-8 py-5 bg-slate-50 flex flex-col sm:flex-row justify-end gap-3">
                <button type="button" onclick="closeModalAspek()" class="px-6 py-2.5 text-slate-600 font-bold hover:text-slate-800 transition-colors">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all">Simpan Aspek</button>
            </div>
        </form>
    </div>
</div>

<div id="modalIndikator" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 transition-all p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-xl font-bold text-slate-800 tracking-tight">Tambah Indikator</h3>
            <button onclick="closeModalIndikator()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('hr.kpa.indikator.store') }}" method="POST">
            @csrf
            <input type="hidden" name="aspek_id" id="aspek_id">
            <div class="p-8">
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide text-[10px]">Nama Indikator</label>
                    <input type="text" name="nama" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" required placeholder="Misal: Kerja Sama Tim">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide text-[10px]">Bobot (%)</label>
                    <input type="number" name="bobot" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" required min="0" max="100">
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide text-[10px]">Tipe Penilaian</label>
                    <select name="tipe" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all appearance-none bg-no-repeat bg-[right_1rem_center]" required>
                        <option value="manual">✏️ Manual (Input HR)</option>
                        <option value="otomatis">✅ Otomatis (Dari Sistem)</option>
                    </select>
                </div>
            </div>
            <div class="px-8 py-5 bg-slate-50 flex flex-col sm:flex-row justify-end gap-3">
                <button type="button" onclick="closeModalIndikator()" class="px-6 py-2.5 text-slate-600 font-bold hover:text-slate-800 transition-colors">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all">Simpan Indikator</button>
            </div>
        </form>
    </div>
</div>