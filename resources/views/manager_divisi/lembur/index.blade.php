<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Lembur - Manager Divisi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    @include('manager_divisi.templet.sider')

    <div class="min-h-screen transition-all duration-300 md:ml-64">
        <div class="p-4 md:p-8 max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i>
                    Approve Pengajuan Lembur
                </h1>
                <p class="text-sm text-gray-500 mt-1">Setujui atau tolak pengajuan lembur karyawan divisi Anda.</p>
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

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
                <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end flex-wrap">
                    <div class="flex flex-col gap-1 w-full sm:w-auto">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-indigo-500 outline-none w-full sm:w-44">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1 w-full sm:w-auto">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Karyawan</label>
                        <select name="user_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-indigo-500 outline-none w-full sm:w-56">
                            <option value="">Semua Karyawan</option>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->id }}" {{ request('user_id')==$k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto pt-2 sm:pt-0">
                        <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fa-solid fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('manager_divisi.lembur.index') }}" class="flex-1 sm:flex-none text-center bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Stats ringkas --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-amber-100 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $lemburs->where('status','pending')->count() + $lemburs->filter(fn($l)=>$l->status=='pending')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Menunggu</p>
                </div>
                <div class="bg-white rounded-xl border border-emerald-100 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $lemburs->where('status','approved')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Disetujui</p>
                </div>
                <div class="bg-white rounded-xl border border-rose-100 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-rose-600">{{ $lemburs->where('status','rejected')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Ditolak</p>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] table-auto">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Karyawan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Jam Lembur</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Durasi</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($lemburs as $lembur)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($lembur->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $lembur->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600 font-mono">
                                    {{ $lembur->jam_mulai }} – {{ $lembur->jam_selesai }}
                                </td>
                                <td class="px-5 py-4 text-sm text-center font-semibold text-gray-700">
                                    {{ $lembur->durasi }} jam
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $lembur->keterangan }}">
                                    {{ $lembur->keterangan ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($lembur->status == 'pending')
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Menunggu
                                        </span>
                                    @elseif($lembur->status == 'approved')
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-full text-xs font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Ditolak
                                        </span>
                                        @if($lembur->alasan_penolakan)
                                            <p class="text-xs text-gray-400 mt-1">{{ Str::limit($lembur->alasan_penolakan, 40) }}</p>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($lembur->status == 'pending')
                                    <div class="flex gap-2 justify-center">
                                        <form method="POST" action="{{ route('manager_divisi.lembur.approve', $lembur->id) }}">
                                            @csrf
                                            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors shadow-sm">
                                                <i class="fa-solid fa-check mr-1"></i>Setujui
                                            </button>
                                        </form>
                                        <button onclick="showRejectModal({{ $lembur->id }})" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors shadow-sm">
                                            <i class="fa-solid fa-xmark mr-1"></i>Tolak
                                        </button>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400">
                                        Diproses oleh {{ $lembur->approver->name ?? 'sistem' }}
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <i class="fa-regular fa-calendar-xmark text-4xl"></i>
                                        <p class="text-sm font-medium">Tidak ada pengajuan lembur</p>
                                        <p class="text-xs">Tidak ada pengajuan lembur dari karyawan divisi Anda saat ini.</p>
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

    {{-- Modal Tolak --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-ban text-rose-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Tolak Pengajuan Lembur</h3>
                    <p class="text-xs text-gray-500">Masukkan alasan penolakan untuk karyawan</p>
                </div>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <textarea name="alasan_penolakan" rows="4" required
                    class="w-full border border-gray-200 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 rounded-xl px-3 py-2.5 text-sm mb-4 resize-none outline-none"
                    placeholder="Contoh: Kapasitas lembur bulan ini sudah penuh..."></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(id) {
            document.getElementById('rejectForm').action = '/manager-divisi/lembur/' + id + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
</body>
</html>
