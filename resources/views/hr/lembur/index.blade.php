<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lembur - HR</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font(['Poppins',_sans-serif])">

    @include('hr.templet.sider')

    <div class="min-h-screen transition-all duration-300 md:ml-64">
        
        <div class="p-4 md:p-8 max-w-7xl mx-auto">
            
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <span>📋</span> Kelola Pengajuan Lembur
                </h1>
                <p class="text-sm text-gray-500 mt-1">Setujui atau tolak lembur lembur karyawan yang masuk.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
                <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end sm:items-center flex-wrap">
                    <div class="w-full sm:w-auto flex flex-col gap-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                        <select name="status" class="border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg px-3 py-2 text-sm bg-gray-50 w-full sm:w-44">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-auto flex flex-col gap-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Karyawan</label>
                        <select name="user_id" class="border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg px-3 py-2 text-sm bg-gray-50 w-full sm:w-56">
                            <option value="">Semua Karyawan</option>
                            @foreach($karyawan as $k)
                            <option value="{{ $k->id }}" {{ request('user_id')==$k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto pt-2 sm:pt-0">
                        <button type="submit" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('hr.lembur.index') }}" class="flex-1 sm:flex-none text-center bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] table-auto">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Lembur</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Durasi</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($lemburs as $lembur)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $lembur->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $lembur->jam_mulai }} - {{ $lembur->jam_selesai }}</td>
                                <td class="px-6 py-4 text-sm text-center font-medium text-gray-700">{{ $lembur->durasi }} jam</td>
                                <td class="px-6 py-4 text-sm text-center">
                                    @if($lembur->status == 'pending')
                                        <span class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-medium">Menunggu</span>
                                    @elseif($lembur->status == 'approved')
                                        <span class="inline-flex items-center bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-xs font-medium">Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-full text-xs font-medium">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $lembur->keterangan }}">{{ $lembur->keterangan ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-center">
                                    @if($lembur->status == 'pending')
                                    <div class="flex gap-2 justify-center items-center">
                                        <form method="POST" action="{{ route('hr.lembur.approve', $lembur->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-emerald-600 shadow-sm transition-colors">✅ Setujui</button>
                                        </form>
                                        <button onclick="showRejectModal({{ $lembur->id }})" class="bg-rose-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-rose-600 shadow-sm transition-colors">❌ Tolak</button>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400 font-medium">Selesai diproses</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
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

    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 transition-all">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl border border-gray-100 mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tolak Pengajuan Lembur</h3>
            <p class="text-xs text-gray-500 mb-4">Berikan alasan mengapa pengajuan lembur dari karyawan ini ditolak.</p>
            <form id="rejectForm" method="POST">
                @csrf
                <textarea name="alasan_penolakan" class="w-full border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg px-3 py-2 text-sm mb-4 resize-none" placeholder="Alasan penolakan..." rows="4" required></textarea>
                <div class="flex justify-end gap-2.5">
                    <button type="button" onclick="closeRejectModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(id) {
            document.getElementById('rejectForm').action = '/hr/lembur/' + id + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
</body>
</html>