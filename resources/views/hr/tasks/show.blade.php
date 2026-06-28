@include('hr.templet.sider')
<main class="flex-1 flex flex-col bg-background-light main-content">
<div class="flex-1 p-3 sm:p-8">
<div class="p-6">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('hr.tasks.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <span class="material-icons-outlined">arrow_back</span>
            Kembali ke Daftar Tugas
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Tugas</h2>
        <div class="flex gap-2">
            <a href="{{ route('hr.tasks.edit', $task->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">Edit</a>
            <form action="{{ route('hr.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">Hapus</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Info Tugas -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="material-icons-outlined text-blue-600">info</span>
            Informasi Tugas
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-400 text-sm">Judul Tugas</p>
                <p class="text-lg font-semibold">{{ $task->judul }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Status</p>
                @php
                    $statusColors = [
                        'pending' => 'bg-gray-100 text-gray-600',
                        'proses' => 'bg-yellow-100 text-yellow-700',
                        'selesai' => 'bg-green-100 text-green-700',
                        'menunggu' => 'bg-purple-100 text-purple-700',
                        'dibatalkan' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$task->status] ?? 'bg-gray-100' }}">
                    {{ ucfirst($task->status) }}
                </span>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Target Karyawan</p>
                <p class="font-medium">{{ $task->assignedKaryawan->nama ?? '-' }}</p>
                <!-- <p class="text-xs text-gray-400">{{ $task->assignedKaryawan->divisi ?? '-' }}</p> -->
            </div>
            <div>
                <p class="text-gray-400 text-sm">Priority</p>
                @php
                    $priorityColors = [
                        'low' => 'bg-gray-100 text-gray-600',
                        'medium' => 'bg-blue-100 text-blue-600',
                        'high' => 'bg-yellow-100 text-yellow-700',
                        'urgent' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $priorityColors[$task->priority] ?? 'bg-gray-100' }}">
                    {{ ucfirst($task->priority) }}
                </span>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Deadline</p>
                <p class="{{ $task->deadline && now()->gt($task->deadline) && $task->status != 'selesai' ? 'text-red-600 font-semibold' : '' }}">
                    {{ $task->deadline ? $task->deadline->format('d F Y H:i') : '-' }}
                </p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Dibuat Oleh</p>
                <p>{{ $task->creator->name ?? '-' }}</p>
                <p class="text-xs text-gray-400">{{ $task->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-gray-400 text-sm">Deskripsi</p>
                <p class="mt-1">{{ $task->deskripsi ?? 'Tidak ada deskripsi' }}</p>
            </div>
        </div>
    </div>

    <!-- HASIL UPLOAD KARYAWAN (FILE SUBMISSION) -->
    @if($task->submission_file)
    <div class="bg-white rounded-lg shadow-sm border border-green-200 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="material-icons-outlined text-green-600">cloud_upload</span>
            Hasil Upload Karyawan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm">File Tugas</p>
                <a href="{{ asset('storage/' . $task->submission_file) }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mt-1" 
                   target="_blank">
                    <span class="material-icons-outlined">download</span>
                    {{ basename($task->submission_file) }}
                </a>
            </div>
            
            @if($task->submission_notes)
            <div>
                <p class="text-gray-400 text-sm">Catatan Karyawan</p>
                <p class="mt-1 text-gray-700">{{ $task->submission_notes }}</p>
            </div>
            @endif
            
            <div>
                <p class="text-gray-400 text-sm">Dikumpulkan Pada</p>
                <p class="mt-1">{{ $task->submitted_at ? \Carbon\Carbon::parse($task->submitted_at)->format('d F Y H:i') : '-' }}</p>
            </div>
            
            <div>
                <p class="text-gray-400 text-sm">Status Pengerjaan</p>
                <p class="mt-1">
                    @if($task->status == 'selesai')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">✅ Selesai</span>
                    @elseif($task->status == 'menunggu')
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">⏳ Menunggu Review</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">{{ $task->status }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-3 text-gray-400">
            <span class="material-icons-outlined">hourglass_empty</span>
            <p>Karyawan belum mengupload tugas</p>
        </div>
    </div>
    @endif

    <!-- Form Update Status -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="material-icons-outlined text-blue-600">sync</span>
            Update Status Tugas
        </h3>
        <form action="{{ route('hr.tasks.update-status', $task->id) }}" method="POST" class="flex items-center gap-4">
            @csrf
            @method('PUT')
            <select name="status" class="border rounded-lg px-3 py-2">
                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="proses" {{ $task->status == 'proses' ? 'selected' : '' }}>Proses</option>
                <option value="menunggu" {{ $task->status == 'menunggu' ? 'selected' : '' }}>Menunggu Review</option>
                <option value="selesai" {{ $task->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ $task->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Update Status</button>
        </form>
    </div>
</div>

</main>
</div>
</body>
</html>