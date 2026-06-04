<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('karyawan.templet.header')

    <div class="container mx-auto px-4 py-8 mt-16">
        <h1 class="text-2xl font-bold mb-6">📄 Slip Gaji Saya</h1>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-right">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right">Lembur</th>
                        <th class="px-4 py-3 text-right">Potongan</th>
                        <th class="px-4 py-3 text-right">Total Bersih</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slipGaji as $slip)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $slip->payrollPeriod->nama_periode ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($slip->upah_lembur ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-red-600">Rp {{ number_format(($slip->potongan_tidak_hadir ?? 0) + ($slip->potongan_bpjs ?? 0), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">Rp {{ number_format($slip->total_gaji_bersih, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('karyawan.slip-gaji.show', $slip->id) }}" 
                               class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                Lihat Slip
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>