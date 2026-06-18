<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Approve & Perintah Lembur - Manager Divisi</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 50; }
        .modal.show { display: flex; }
        .badge-warning { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 9999px; font-size: 12px; }
        .badge-success { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 9999px; font-size: 12px; }
        .badge-danger { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 9999px; font-size: 12px; }
        .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 9999px; font-size: 12px; }
        .badge-purple { background: #f3e8ff; color: #9333ea; padding: 4px 12px; border-radius: 9999px; font-size: 12px; }
        .badge-orange { background: #ffedd5; color: #ea580c; padding: 4px 12px; border-radius: 9999px; font-size: 12px; }
    </style>
</head>
<body>

<div class="flex h-screen overflow-hidden">
    @include('manager_divisi.templet.sider')
    
    <main class="flex-1 overflow-y-auto p-6 md:ml-64">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">⏰ Approve & Perintah Lembur</h1>
                <p class="text-gray-500">Setujui pengajuan lembur karyawan atau berikan perintah lembur</p>
            </div>
            
            <div class="mb-6 flex justify-end">
                <button onclick="openModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg flex items-center gap-2 shadow-md">
                    <i class="fa-solid fa-plus"></i> Perintah Lembur
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-500">
                    <div class="text-gray-600 text-sm">Total</div>
                    <div class="text-2xl font-bold">{{ $statistik['total'] ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
                    <div class="text-yellow-600 text-sm">Menunggu</div>
                    <div class="text-2xl font-bold">{{ $statistik['pending'] ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
                    <div class="text-green-600 text-sm">Disetujui</div>
                    <div class="text-2xl font-bold">{{ $statistik['approved'] ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
                    <div class="text-red-600 text-sm">Ditolak</div>
                    <div class="text-2xl font-bold">{{ $statistik['rejected'] ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
                    <div class="text-purple-600 text-sm">Perintah Saya</div>
                    <div class="text-2xl font-bold">{{ $statistik['perintah'] ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-orange-500">
                    <div class="text-orange-600 text-sm">Dibatalkan</div>
                    <div class="text-2xl font-bold">{{ $statistik['cancelled'] ?? 0 }}</div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" class="flex gap-3 flex-wrap">
                    <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    <select name="type" class="border rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="pengajuan" {{ request('type')=='pengajuan' ? 'selected' : '' }}>Pengajuan Karyawan</option>
                        <option value="perintah" {{ request('type')=='perintah' ? 'selected' : '' }}>Perintah Manager</option>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
                    <a href="{{ route('manager_divisi.lembur.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">Reset</a>
                </form>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left w-12">No</th>
                                <th class="px-4 py-3 text-center w-36">Tipe</th>
                                <th class="px-4 py-3 text-left">Karyawan</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-center">Jam</th>
                                <th class="px-4 py-3 text-center w-20">Durasi</th>
                                <th class="px-4 py-3 text-right">Upah/Jam</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-center w-32">Status</th>
                                <th class="px-4 py-3 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($lemburs as $index => $lembur)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $lemburs->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center w-full">
                                        @if($lembur->type == 'perintah')
                                            <span class="badge-purple inline-flex items-center gap-1 justify-center"><i class="fa-solid fa-crown"></i> Perintah</span>
                                        @else
                                            <span class="badge-info inline-flex items-center gap-1 justify-center"><i class="fa-solid fa-user"></i> Pengajuan</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $lembur->user->name }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    {{ \Carbon\Carbon::parse($lembur->jam_mulai)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">{{ abs($lembur->durasi) }} jam</td>
                                <td class="px-4 py-3 text-right">
                                    @if($lembur->custom_rate)
                                        <span class="text-green-600 font-medium">Rp {{ number_format($lembur->custom_rate, 0, ',', '.') }}</span>
                                        <span class="text-xs text-gray-400 line-through ml-1">(Rp {{ number_format($lembur->hourly_rate ?? 30000, 0, ',', '.') }})</span>
                                    @else
                                        Rp {{ number_format($lembur->hourly_rate ?? 30000, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-600">
                                    Rp {{ number_format(($lembur->custom_rate ?? $lembur->hourly_rate ?? 30000) * abs($lembur->durasi), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($lembur->status == 'pending')
                                        @if($lembur->type == 'perintah')
                                            <span class="badge-orange"><i class="fa-solid fa-hourglass-half"></i> Menunggu Konfirmasi</span>
                                        @else
                                            <span class="badge-warning">⏳ Menunggu</span>
                                        @endif
                                    @elseif($lembur->status == 'approved')
                                        @if($lembur->type == 'perintah')
                                            <span class="badge-success"><i class="fa-solid fa-check-circle"></i> Dikonfirmasi</span>
                                        @else
                                            <span class="badge-success">✅ Disetujui</span>
                                        @endif
                                    @elseif($lembur->status == 'rejected')
                                        <span class="badge-danger">❌ Ditolak</span>
                                    @elseif($lembur->status == 'cancelled')
                                        <span class="badge-danger"><i class="fa-solid fa-ban"></i> Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($lembur->status == 'pending')
                                        @if($lembur->type == 'perintah')
                                            <div class="flex gap-1 justify-center">
                                                <button onclick="openEditModal({{ json_encode($lembur) }}, '{{ addslashes($lembur->user->name) }}')" 
                                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center gap-1">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button onclick="cancelPerintah({{ $lembur->id }}, '{{ addslashes($lembur->user->name) }}')" 
                                                        class="bg-orange-500 hover:bg-orange-600 text-white px-2 py-1 rounded text-xs flex items-center gap-1">
                                                    <i class="fa-solid fa-ban"></i> Batalkan
                                                </button>
                                            </div>
                                        @else
                                            <div class="flex gap-1 justify-center">
                                                <button onclick="approveLembur({{ $lembur->id }})" 
                                                        class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center gap-1">
                                                    <i class="fa-solid fa-check"></i> Setuju
                                                </button>
                                                <button onclick="showRejectModal({{ $lembur->id }}, '{{ addslashes($lembur->user->name) }}')" 
                                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs flex items-center gap-1">
                                                    <i class="fa-solid fa-times"></i> Tolak
                                                </button>
                                            </div>
                                        @endif
                                    @elseif($lembur->status == 'rejected' && $lembur->alasan_penolakan)
                                        <button onclick="showReason('{{ addslashes($lembur->alasan_penolakan) }}')" 
                                                class="text-gray-400 hover:text-indigo-600 text-xs flex items-center gap-1 mx-auto">
                                            <i class="fa-solid fa-eye"></i> Lihat Alasan
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-4 py-10 text-center text-gray-400">
                                    <i class="fa-solid fa-inbox text-4xl mb-2 block"></i>
                                    Belum ada data lembur
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t">
                    {{ $lemburs->withQueryString()->links() }}
                </div>
            </div>
            
        </div>
    </main>
</div>

<div id="perintahModal" class="modal">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-crown text-indigo-600"></i> Perintah Lembur
            </h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="formPerintah" method="POST">
            @csrf
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Pilih Karyawan *</label>
                    <select name="karyawan_id" id="karyawan_id" class="w-full border rounded-lg p-2.5" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}">{{ $karyawan->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Lembur *</label>
                    <input type="date" name="tanggal_lembur" id="tanggal_lembur" value="{{ date('Y-m-d') }}" class="w-full border rounded-lg p-2.5" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Mulai *</label>
                        <input type="time" name="jam_mulai" id="jam_mulai" value="17:00" class="w-full border rounded-lg p-2.5" required onchange="hitungEstimasi()" onkeyup="hitungEstimasi()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Selesai *</label>
                        <input type="time" name="jam_selesai" id="jam_selesai" value="20:00" class="w-full border rounded-lg p-2.5" required onchange="hitungEstimasi()" onkeyup="hitungEstimasi()">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi Tugas *</label>
                    <textarea name="deskripsi_tugas" id="deskripsi_tugas" rows="3" class="w-full border rounded-lg p-2.5" placeholder="Jelaskan tugas yang harus diselesaikan..." required></textarea>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <h3 class="font-semibold mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-coins text-amber-600"></i> Setting Upah Lembur
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">Default: Rp {{ number_format($defaultRate ?? 30000, 0, ',', '.') }}/jam | Maksimal: Rp {{ number_format($maxRate ?? 100000, 0, ',', '.') }}/jam</p>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Upah per Jam (Rp)</label>
                        <input type="number" name="custom_rate" id="custom_rate" value="{{ $defaultRate ?? 30000 }}" 
                               class="w-full border rounded-lg p-2.5" min="{{ $defaultRate ?? 30000 }}" max="{{ $maxRate ?? 100000 }}" step="1000" onchange="hitungEstimasi(); cekAlasanKenaikan()" onkeyup="hitungEstimasi(); cekAlasanKenaikan()">
                    </div>
                    
                    <div id="alasanDiv" style="display: none;" class="mt-3">
                        <label class="block text-sm font-medium mb-1">Alasan Kenaikan Upah *</label>
                        <input type="text" name="alasan_kenaikan" id="alasan_kenaikan" class="w-full border rounded-lg p-2.5" placeholder="Contoh: Proyek urgent, deadline mepet...">
                        <p class="text-xs text-red-500 mt-1" id="warningAlasan" style="display: none;">Alasan kenaikan upah wajib diisi!</p>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t flex justify-between text-sm">
                        <span>Estimasi Durasi: <strong id="durasi_hasil" class="text-indigo-600">-</strong></span>
                        <span>Estimasi Total: <strong id="total_hasil" class="text-emerald-600">-</strong></span>
                    </div>
                </div>
            </div>
            <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Kirim Perintah</button>
            </div>
        </form>
    </div>
</div>

<div id="editPerintahModal" class="modal">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i> Edit Perintah Lembur
            </h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="formEditPerintah" method="POST">
            @csrf
            <input type="hidden" id="edit_lembur_id">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-500">Nama Karyawan</label>
                    <input type="text" id="edit_karyawan_name" class="w-full border rounded-lg p-2.5 bg-gray-100 cursor-not-allowed" readonly>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Lembur *</label>
                    <input type="date" name="tanggal_lembur" id="edit_tanggal_lembur" class="w-full border rounded-lg p-2.5" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Mulai *</label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai" class="w-full border rounded-lg p-2.5" required onchange="hitungEstimasiEdit()" onkeyup="hitungEstimasiEdit()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Selesai *</label>
                        <input type="time" name="jam_selesai" id="edit_jam_selesai" class="w-full border rounded-lg p-2.5" required onchange="hitungEstimasiEdit()" onkeyup="hitungEstimasiEdit()">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi Tugas *</label>
                    <textarea name="deskripsi_tugas" id="edit_deskripsi_tugas" rows="3" class="w-full border rounded-lg p-2.5" placeholder="Jelaskan tugas yang harus diselesaikan..." required></textarea>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <h3 class="font-semibold mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-coins text-amber-600"></i> Setting Upah Lembur
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">Default: Rp {{ number_format($defaultRate ?? 30000, 0, ',', '.') }}/jam | Maksimal: Rp {{ number_format($maxRate ?? 100000, 0, ',', '.') }}/jam</p>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Upah per Jam (Rp)</label>
                        <input type="number" name="custom_rate" id="edit_custom_rate" class="w-full border rounded-lg p-2.5" min="{{ $defaultRate ?? 30000 }}" max="{{ $maxRate ?? 100000 }}" step="1000" onchange="hitungEstimasiEdit(); cekAlasanKenaikanEdit()" onkeyup="hitungEstimasiEdit(); cekAlasanKenaikanEdit()">
                    </div>
                    
                    <div id="edit_alasanDiv" style="display: none;" class="mt-3">
                        <label class="block text-sm font-medium mb-1">Alasan Kenaikan Upah *</label>
                        <input type="text" name="alasan_kenaikan" id="edit_alasan_kenaikan" class="w-full border rounded-lg p-2.5" placeholder="Contoh: Proyek urgent, deadline mepet...">
                        <p class="text-xs text-red-500 mt-1" id="edit_warningAlasan" style="display: none;">Alasan kenaikan upah wajib diisi!</p>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t flex justify-between text-sm">
                        <span>Estimasi Durasi: <strong id="edit_durasi_hasil" class="text-indigo-600">-</strong></span>
                        <span>Estimasi Total: <strong id="edit_total_hasil" class="text-emerald-600">-</strong></span>
                    </div>
                </div>
            </div>
            <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="modal">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Tolak Pengajuan Lembur</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <input type="hidden" id="rejectId">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Alasan Penolakan</label>
                <textarea name="alasan_penolakan" id="alasanPenolakan" rows="3" class="w-full border rounded-lg p-2.5" required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Tolak</button>
            </div>
        </form>
    </div>
</div>

<div id="reasonModal" class="modal">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Alasan Penolakan</h3>
        <p id="reasonText" class="text-gray-600 mb-4 p-3 bg-gray-50 rounded-lg"></p>
        <button onclick="closeReasonModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Tutup</button>
    </div>
</div>

<script>
    var defaultRate = {{ $defaultRate ?? 30000 }};
    var maxRate = {{ $maxRate ?? 100000 }};
    
    function openModal() {
        document.getElementById('formPerintah').reset();
        document.getElementById('custom_rate').value = defaultRate;
        document.getElementById('alasanDiv').style.display = 'none';
        document.getElementById('warningAlasan').style.display = 'none';
        document.getElementById('durasi_hasil').innerHTML = '-';
        document.getElementById('total_hasil').innerHTML = '-';
        document.getElementById('perintahModal').classList.add('show');
        hitungEstimasi();
    }
    
    function closeModal() {
        document.getElementById('perintahModal').classList.remove('show');
    }

    function openEditModal(lembur, namaKaryawan) {
        document.getElementById('formEditPerintah').reset();
        document.getElementById('edit_lembur_id').value = lembur.id;
        document.getElementById('edit_karyawan_name').value = namaKaryawan;
        document.getElementById('edit_tanggal_lembur').value = lembur.tanggal_lembur;
        
        let jamMulaiRaw = lembur.jam_mulai;
        let jamSelesaiRaw = lembur.jam_selesai;
        
        if (jamMulaiRaw && jamMulaiRaw.includes(' ')) {
            jamMulaiRaw = jamMulaiRaw.split(' ')[1];
        }
        if (jamSelesaiRaw && jamSelesaiRaw.includes(' ')) {
            jamSelesaiRaw = jamSelesaiRaw.split(' ')[1];
        }
        
        document.getElementById('edit_jam_mulai').value = jamMulaiRaw ? jamMulaiRaw.substring(0, 5) : '17:00';
        document.getElementById('edit_jam_selesai').value = jamSelesaiRaw ? jamSelesaiRaw.substring(0, 5) : '20:00';
        document.getElementById('edit_deskripsi_tugas').value = lembur.deskripsi_tugas || '';
        
        let currentRate = lembur.custom_rate ? lembur.custom_rate : defaultRate;
        document.getElementById('edit_custom_rate').value = currentRate;
        document.getElementById('edit_alasan_kenaikan').value = lembur.alasan_kenaikan ?? '';
        
        document.getElementById('editPerintahModal').classList.add('show');
        
        hitungEstimasiEdit();
        cekAlasanKenaikanEdit();
    }

    function closeEditModal() {
        document.getElementById('editPerintahModal').classList.remove('show');
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('show');
    }
    
    function closeReasonModal() {
        document.getElementById('reasonModal').classList.remove('show');
    }
    
    function showReason(reason) {
        document.getElementById('reasonText').innerHTML = reason;
        document.getElementById('reasonModal').classList.add('show');
    }
    
    function cekAlasanKenaikan() {
        let customRate = parseInt(document.getElementById('custom_rate').value) || defaultRate;
        let alasanDiv = document.getElementById('alasanDiv');
        let alasanInput = document.getElementById('alasan_kenaikan');
        let warningAlasan = document.getElementById('warningAlasan');
        
        if(customRate > defaultRate) {
            alasanDiv.style.display = 'block';
            if(!alasanInput.value.trim()) {
                warningAlasan.style.display = 'block';
            } else {
                warningAlasan.style.display = 'none';
            }
        } else {
            alasanDiv.style.display = 'none';
            warningAlasan.style.display = 'none';
        }
    }

    function cekAlasanKenaikanEdit() {
        let customRate = parseInt(document.getElementById('edit_custom_rate').value) || defaultRate;
        let alasanDiv = document.getElementById('edit_alasanDiv');
        let alasanInput = document.getElementById('edit_alasan_kenaikan');
        let warningAlasan = document.getElementById('edit_warningAlasan');
        
        if(customRate > defaultRate) {
            alasanDiv.style.display = 'block';
            if(!alasanInput.value.trim()) {
                warningAlasan.style.display = 'block';
            } else {
                warningAlasan.style.display = 'none';
            }
        } else {
            alasanDiv.style.display = 'none';
            warningAlasan.style.display = 'none';
        }
    }
    
    function hitungEstimasi() {
        let jamMulai = document.getElementById('jam_mulai').value;
        let jamSelesai = document.getElementById('jam_selesai').value;
        let customRate = parseInt(document.getElementById('custom_rate').value) || defaultRate;
        
        if(!jamMulai || !jamSelesai) return;
        
        let mulai = jamMulai.split(':');
        let selesai = jamSelesai.split(':');
        
        let menitMulai = (parseInt(mulai[0]) * 60) + parseInt(mulai[1]);
        let menitSelesai = (parseInt(selesai[0]) * 60) + parseInt(selesai[1]);
        
        if (menitSelesai <= menitMulai) {
            menitSelesai += 24 * 60;
        }
        
        let durasi = Math.ceil((menitSelesai - menitMulai) / 60);
        let total = durasi * customRate;
        
        document.getElementById('durasi_hasil').innerHTML = durasi + ' jam';
        document.getElementById('total_hasil').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    function hitungEstimasiEdit() {
        let jamMulai = document.getElementById('edit_jam_mulai').value;
        let jamSelesai = document.getElementById('edit_jam_selesai').value;
        let customRate = parseInt(document.getElementById('edit_custom_rate').value) || defaultRate;
        
        if(!jamMulai || !jamSelesai) return;
        
        let mulai = jamMulai.split(':');
        let selesai = jamSelesai.split(':');
        
        let menitMulai = (parseInt(mulai[0]) * 60) + parseInt(mulai[1]);
        let menitSelesai = (parseInt(selesai[0]) * 60) + parseInt(selesai[1]);
        
        if (menitSelesai <= menitMulai) {
            menitSelesai += 24 * 60;
        }
        
        let durasi = Math.ceil((menitSelesai - menitMulai) / 60);
        let total = durasi * customRate;
        
        document.getElementById('edit_durasi_hasil').innerHTML = durasi + ' jam';
        document.getElementById('edit_total_hasil').innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
    
    document.getElementById('custom_rate')?.addEventListener('input', function() {
        hitungEstimasi();
        cekAlasanKenaikan();
    });
    document.getElementById('jam_mulai')?.addEventListener('change', hitungEstimasi);
    document.getElementById('jam_selesai')?.addEventListener('change', hitungEstimasi);

    document.getElementById('edit_custom_rate')?.addEventListener('input', function() {
        hitungEstimasiEdit();
        cekAlasanKenaikanEdit();
    });
    document.getElementById('edit_jam_mulai')?.addEventListener('change', hitungEstimasiEdit);
    document.getElementById('edit_jam_selesai')?.addEventListener('change', hitungEstimasiEdit);
    
    // Submit Perintah Baru
    document.getElementById('formPerintah')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let customRate = parseInt(document.getElementById('custom_rate').value) || defaultRate;
        let alasanKenaikan = document.getElementById('alasan_kenaikan').value;
        
        if(customRate > defaultRate && !alasanKenaikan.trim()) {
            alert('Alasan kenaikan upah wajib diisi!');
            document.getElementById('alasan_kenaikan').focus();
            return;
        }
        
        let jamMulai = document.getElementById('jam_mulai').value;
        let jamSelesai = document.getElementById('jam_selesai').value;
        let deskripsi = document.getElementById('deskripsi_tugas').value;
        
        let data = {
            karyawan_id: document.getElementById('karyawan_id').value,
            tanggal_lembur: document.getElementById('tanggal_lembur').value,
            jam_mulai: jamMulai,
            jam_selesai: jamSelesai,
            deskripsi_tugas: deskripsi,
            custom_rate: customRate,
            alasan_kenaikan: alasanKenaikan
        };
        
        if(!data.karyawan_id) { alert('Pilih karyawan!'); return; }
        if(!deskripsi) { alert('Isi deskripsi tugas!'); return; }
        if(!jamMulai || !jamSelesai) { alert('Jam mulai dan jam selesai harus diisi!'); return; }
        if(data.custom_rate > maxRate) {
            alert('Upah lembur tidak boleh melebihi Rp ' + new Intl.NumberFormat('id-ID').format(maxRate));
            return;
        }
        
        fetch('{{ route("manager_divisi.lembur.order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                let errorMsg = typeof data.message === 'object' ? JSON.stringify(data.message) : data.message;
                alert('Error: ' + errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan pada server.');
        });
    });

    // Submit Update Edit
    document.getElementById('formEditPerintah')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let id = document.getElementById('edit_lembur_id').value;
        let customRate = parseInt(document.getElementById('edit_custom_rate').value) || defaultRate;
        let alasanKenaikan = document.getElementById('edit_alasan_kenaikan').value;
        
        if(customRate > defaultRate && !alasanKenaikan.trim()) {
            alert('Alasan kenaikan upah wajib diisi!');
            document.getElementById('edit_alasan_kenaikan').focus();
            return;
        }
        
        let jamMulai = document.getElementById('edit_jam_mulai').value;
        let jamSelesai = document.getElementById('edit_jam_selesai').value;
        let deskripsi = document.getElementById('edit_deskripsi_tugas').value;
        
        let data = {
            tanggal_lembur: document.getElementById('edit_tanggal_lembur').value,
            jam_mulai: jamMulai,
            jam_selesai: jamSelesai,
            deskripsi_tugas: deskripsi,
            custom_rate: customRate,
            alasan_kenaikan: alasanKenaikan
        };
        
        if(!deskripsi) { alert('Isi deskripsi tugas!'); return; }
        if(!jamMulai || !jamSelesai) { alert('Jam mulai dan jam selesai harus diisi!'); return; }
        if(data.custom_rate > maxRate) {
            alert('Upah lembur tidak boleh melebihi Rp ' + new Intl.NumberFormat('id-ID').format(maxRate));
            return;
        }
        
        let updateUrl = "{{ url('/manager-divisi/lembur') }}/" + id + "/update";
        
        fetch(updateUrl, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message || 'Perintah lembur berhasil diperbarui!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem.');
        });
    });
    
    function approveLembur(id) {
        if(confirm('Setujui lembur ini?')) {
            let url = "{{ url('/manager-divisi/lembur') }}/" + id + "/approve";
            fetch(url, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) location.reload();
                else alert(data.message);
            })
            .catch(error => alert('Error: ' + error));
        }
    }
    
    function cancelPerintah(id, nama) {
        if(confirm('Batalkan perintah lembur untuk ' + nama + '?')) {
            let url = "{{ url('/manager-divisi/lembur') }}/" + id + "/cancel";
            fetch(url, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => alert('Error: ' + error));
        }
    }
    
    function showRejectModal(id, nama) {
        document.getElementById('rejectId').value = id;
        document.getElementById('rejectModal').classList.add('show');
    }
    
    document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('rejectId').value;
        let alasan = document.getElementById('alasanPenolakan').value;
        
        if(!alasan.trim()) {
            alert('Alasan penolakan harus diisi!');
            return;
        }
        
        let url = "{{ url('/manager-divisi/lembur') }}/" + id + "/reject";
        fetch(url, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ alasan_penolakan: alasan })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) location.reload();
            else alert(data.message);
        })
        .catch(error => alert('Error: ' + error));
    });
    
    setTimeout(function() {
        hitungEstimasi();
        hitungEstimasiEdit();
    }, 100);
</script>
</body>
</html>