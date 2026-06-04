<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Lembur Karyawan - Finance</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                },
            },
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">

    @include('finance.templet.sider')

    <div class="min-h-screen transition-all duration-300 md:ml-64">
        <div class="p-4 md:p-8 max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-blue-500"></i>
                        Pembayaran Uang Lembur
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola dan proses pembayaran lembur karyawan yang sudah disetujui.</p>
                </div>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Set Tarif Global --}}
            <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-5 mb-6">
                <h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-blue-500"></i>
                    Set Tarif Upah Lembur (Custom per Jam)
                </h2>
                <form method="POST" action="{{ route('finance.lembur.set-upah') }}" id="formSetUpahGlobal">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3 items-end">
                        <div class="flex-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Upah per Jam (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                                <input type="number" name="upah_per_jam" id="upahPerJamInput" min="0"
                                    value="{{ $defaultUpahPerJam }}"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                    placeholder="Contoh: 50000">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Tarif ini akan diterapkan ke semua lembur yang dipilih (atau semua jika tidak ada yang dipilih).</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="applyToSelected()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm whitespace-nowrap">
                                <i class="fa-solid fa-check-double mr-1"></i> Terapkan ke Dipilih
                            </button>
                            <button type="button" onclick="applyToAll()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm whitespace-nowrap">
                                <i class="fa-solid fa-globe mr-1"></i> Terapkan ke Semua
                            </button>
                        </div>
                    </div>
                    {{-- Hidden fields, diisi via JS --}}
                    <div id="hiddenBulkIds"></div>
                </form>
            </div>

            {{-- Ringkasan per Karyawan --}}
            @if($totalPerKaryawan->count() > 0)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
                <h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple text-emerald-500"></i>
                    Ringkasan Total per Karyawan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($totalPerKaryawan as $summary)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                            {{ strtoupper(substr($summary->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $summary->user->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $summary->total_hari }} hari · {{ $summary->total_jam }} jam</p>
                            <p class="text-xs font-bold text-emerald-600">
                                Rp {{ $summary->total_upah ? number_format($summary->total_upah, 0, ',', '.') : '–' }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tabel utama --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="checkAll" onchange="toggleAll(this)"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <label for="checkAll" class="text-sm font-medium text-gray-600 cursor-pointer">Pilih Semua</label>
                    </div>
                    <div class="sm:ml-auto flex gap-2">
                        <button onclick="markSelectedPaid()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-colors shadow-sm">
                            <i class="fa-solid fa-money-check-dollar mr-1"></i> Tandai Terpilih Dibayar
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] table-auto">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="w-10 px-4 py-3.5"></th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Karyawan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Durasi</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase">Upah/Jam</th>
                                <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase">Total Upah</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="lemburTableBody">
                            @forelse($lemburs as $lembur)
                            <tr class="hover:bg-gray-50/70 transition-colors" data-id="{{ $lembur->id }}">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" class="lembur-check w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                        value="{{ $lembur->id }}">
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($lembur->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $lembur->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $lembur->user->divisi->divisi ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->translatedFormat('d M Y') }}
                                    <span class="block text-xs text-gray-400 font-mono">{{ $lembur->jam_mulai }} – {{ $lembur->jam_selesai }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-center text-gray-700">
                                    {{ $lembur->durasi }} jam
                                </td>
                                <td class="px-5 py-4 text-sm text-right">
                                    <span class="upah-per-jam-display text-gray-700 font-medium">
                                        {{ $lembur->upah_per_jam ? 'Rp ' . number_format($lembur->upah_per_jam, 0, ',', '.') : '–' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-right font-bold text-emerald-600">
                                    <span class="total-upah-display">
                                        {{ $lembur->total_upah ? 'Rp ' . number_format($lembur->total_upah, 0, ',', '.') : '–' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $lembur->keterangan }}">
                                    {{ $lembur->keterangan ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button onclick="setUpahSingle({{ $lembur->id }})"
                                        class="bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i>Set Upah
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <i class="fa-regular fa-calendar-check text-4xl"></i>
                                        <p class="text-sm font-medium">Tidak ada data lembur tertunda</p>
                                        <p class="text-xs">Semua lembur yang disetujui sudah dibayar.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($lemburs->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                    {{ $lemburs->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Form tersembunyi untuk mark-paid --}}
    <form id="formMarkPaid" method="POST" action="{{ route('finance.lembur.mark-paid') }}">
        @csrf
        <div id="paidIdsContainer"></div>
    </form>

    <script>
        const defaultUpah = {{ $defaultUpahPerJam }};

        // ── Pilih semua ──
        function toggleAll(master) {
            document.querySelectorAll('.lembur-check').forEach(cb => cb.checked = master.checked);
        }

        // ── Set upah ke satu record ──
        function setUpahSingle(id) {
            Swal.fire({
                title: 'Set Upah Lembur',
                html: `
                    <p class="text-sm text-gray-500 mb-3">Masukkan tarif upah per jam untuk lembur ini.</p>
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                        <span class="bg-gray-50 px-3 py-2.5 text-sm text-gray-500 border-r border-gray-200">Rp</span>
                        <input id="swalUpahInput" type="number" min="0" value="${defaultUpah}"
                            class="flex-1 px-3 py-2.5 text-sm outline-none" placeholder="Nominal per jam">
                    </div>`,
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3b82f6',
                preConfirm: () => {
                    const val = document.getElementById('swalUpahInput').value;
                    if (!val || isNaN(val) || Number(val) < 0) {
                        Swal.showValidationMessage('Masukkan nominal upah yang valid');
                    }
                    return val;
                }
            }).then(result => {
                if (result.isConfirmed) {
                    submitSetUpah([id], result.value);
                }
            });
        }

        // ── Terapkan ke yang dipilih ──
        function applyToSelected() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Pilih dulu!', text: 'Centang minimal satu lembur.', timer: 1800, showConfirmButton: false });
                return;
            }
            const upah = document.getElementById('upahPerJamInput').value;
            if (!upah || isNaN(upah)) {
                Swal.fire({ icon: 'error', title: 'Tarif tidak valid', text: 'Isi tarif upah per jam terlebih dahulu.', timer: 2000, showConfirmButton: false });
                return;
            }
            submitSetUpah(ids, upah);
        }

        // ── Terapkan ke semua ──
        function applyToAll() {
            const upah = document.getElementById('upahPerJamInput').value;
            if (!upah || isNaN(upah)) {
                Swal.fire({ icon: 'error', title: 'Tarif tidak valid', text: 'Isi tarif upah per jam terlebih dahulu.', timer: 2000, showConfirmButton: false });
                return;
            }
            const allIds = Array.from(document.querySelectorAll('.lembur-check')).map(cb => cb.value);
            submitSetUpah(allIds, upah);
        }

        // ── Submit set upah ──
        function submitSetUpah(ids, upah) {
            const form = document.getElementById('formSetUpahGlobal');
            const container = document.getElementById('hiddenBulkIds');
            container.innerHTML = '';
            ids.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'bulk_ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            // Update upah_per_jam value
            document.querySelector('[name="upah_per_jam"]').value = upah;
            form.submit();
        }

        // ── Tandai dibayar ──
        function markSelectedPaid() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Pilih dulu!', text: 'Centang minimal satu lembur.', timer: 1800, showConfirmButton: false });
                return;
            }
            Swal.fire({
                title: `Tandai ${ids.length} lembur dibayar?`,
                text: 'Data yang ditandai dibayar tidak akan muncul lagi di halaman ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tandai Dibayar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
            }).then(result => {
                if (result.isConfirmed) {
                    const container = document.getElementById('paidIdsContainer');
                    container.innerHTML = '';
                    ids.forEach(id => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'lembur_ids[]';
                        inp.value = id;
                        container.appendChild(inp);
                    });
                    document.getElementById('formMarkPaid').submit();
                }
            });
        }

        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.lembur-check:checked')).map(cb => cb.value);
        }
    </script>
</body>
</html>
