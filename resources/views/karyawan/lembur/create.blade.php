<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ajukan Lembur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s;
        }
        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    @include('karyawan.templet.header')

    <div class="main-content ml-0 md:ml-64 p-6 mt-16">
        <div class="container mx-auto max-w-lg">
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('karyawan.lembur.index') }}" class="text-gray-500 hover:text-gray-700">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
                <h1 class="text-2xl font-bold">Ajukan Lembur</h1>
            </div>

            <form method="POST" action="{{ route('karyawan.lembur.store') }}" class="bg-white rounded-lg shadow p-6">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tanggal Lembur *</label>
                    <input type="date" name="tanggal_lembur" required 
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Mulai *</label>
                        <input type="time" name="jam_mulai" required 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jam Selesai *</label>
                        <input type="time" name="jam_selesai" required 
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3" 
                              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                              placeholder="Contoh: Menyelesaikan project website client A..."></textarea>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <p class="text-sm text-gray-600">💡 <span class="font-semibold">Informasi:</span></p>
                    <ul class="text-xs text-gray-500 mt-1 ml-4 list-disc">
                        <li>Tarif lembur: <span class="font-semibold text-green-600">Rp 30.000 / jam</span></li>
                        <li>Pengajuan akan diproses oleh HR</li>
                        <li>Lembur yang disetujui akan dibayarkan bersama gaji bulanan</li>
                    </ul>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        Kirim Pengajuan
                    </button>
                    <a href="{{ route('karyawan.lembur.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>