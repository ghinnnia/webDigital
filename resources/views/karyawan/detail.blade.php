<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Tugas - Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="mb-4">
            <a href="{{ route('karyawan.tugas.index') }}" class="text-blue-600 hover:text-blue-800">← Kembali ke Daftar Tugas</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-2xl font-bold mb-4">Detail Tugas</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-400 text-sm">Judul Tugas</p>
                    <p class="font-semibold">{{ $task->judul ?? $task->nama_tugas }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Pemberi Tugas</p>
                    <p>{{ $task->creator->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Deadline</p>
                    <p class="{{ $task->deadline && now()->gt($task->deadline) && $task->status != 'selesai' ? 'text-red-600 font-semibold' : '' }}">
                        {{ $task->deadline ? $task->deadline->format('d F Y H:i') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Status</p>
                    @if($task->status == 'selesai')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">✅ Selesai</span>
                    @elseif($task->status == 'proses')
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">🔄 Proses</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">⏳ Pending</span>
                    @endif
                </div>
                <div class="col-span-2">
                    <p class="text-gray-400 text-sm">Deskripsi</p>
                    <p class="mt-1">{{ $task->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>
            </div>
        </div>

        @if($task->status != 'selesai')
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-lg mb-4">Upload Tugas</h2>
            <form action="{{ route('karyawan.tugas.upload', $task->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">File Tugas <span class="text-red-500">*</span></label>
                    <input type="file" name="file" class="w-full border rounded p-2" required>
                    <p class="text-xs text-gray-400 mt-1">Format: PDF, DOC, DOCX, ZIP, JPG, PNG (Max 10MB)</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" class="w-full border rounded p-2" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    📤 Upload & Selesaikan Tugas
                </button>
            </form>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-lg shadow p-6">
            <h2 class="font-semibold text-lg mb-4 text-green-700">✅ Tugas Sudah Selesai</h2>
            @if($task->submission_file)
            <div class="mb-4">
                <p class="text-gray-400 text-sm">File yang diupload:</p>
                <a href="{{ asset('storage/' . $task->submission_file) }}" class="text-blue-600" target="_blank">📎 Lihat File Tugas</a>
            </div>
            @endif
            @if($task->submission_notes)
            <div class="mb-4">
                <p class="text-gray-400 text-sm">Catatan:</p>
                <p>{{ $task->submission_notes }}</p>
            </div>
            @endif
            <div>
                <p class="text-gray-400 text-sm">Dikumpulkan pada:</p>
                <p>{{ $task->submitted_at ? \Carbon\Carbon::parse($task->submitted_at)->format('d F Y H:i') : '-' }}</p>
            </div>
        </div>
        @endif
    </div>
</body>
</html>