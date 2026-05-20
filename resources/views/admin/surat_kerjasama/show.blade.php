<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Surat Kerjasama</title>

    <!-- Tailwind CSS (Versi Terbaru) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Ikon (Font Awesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Gaya Khusus untuk Cetak -->
    <style>
        @media print {
            body {
                background-color: white;
            }
            .no-print {
                display: none !important;
            }
            .print-break {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen py-8">

<!-- Container Utama -->
<div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-xl overflow-hidden print:shadow-none print:rounded-none">

    <!-- Header Dokumen -->
    <header class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6 no-print">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-file-contract mr-2"></i>
                Detail Surat Kerjasama
            </h1>
            <div class="flex gap-2">
                <!-- Tombol Cetak -->
                <button onclick="window.print()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                    <i class="fas fa-print mr-2"></i> Cetak
                </button>
            </div>
        </div>
    </header>

    <!-- Konten Surat -->
    <main class="p-8 md:p-12">

        <!-- Kop Surat -->
        <section class="text-center mb-8 print-break">
            <h1 class="text-3xl font-bold uppercase text-gray-800">{{ $surat->judul }}</h1>
            <div class="mt-4 text-gray-600">
                <p class="text-lg">Nomor Surat: <strong class="text-gray-800">{{ $surat->nomor_surat }}</strong></p>
                <p class="text-lg">Tanggal: <strong class="text-gray-800">{{ \Carbon\Carbon::parse($surat->tanggal)->translatedFormat('d F Y') }}</strong></p>
            </div>
        </section>

        <!-- Garis Pemisah -->
        <hr class="border-t-2 border-gray-300 mb-8">

        <!-- Isi Surat -->
        <div class="space-y-8 text-gray-700 leading-relaxed print-break">
            @php
                $sections = [
                    'para_pihak' => 'Para Pihak',
                    'maksud_tujuan' => 'Maksud dan Tujuan',
                    'ruang_lingkup' => 'Ruang Lingkup Kerjasama',
                    'jangka_waktu' => 'Jangka Waktu Kerjasama',
                    'biaya_pembayaran' => 'Biaya dan Pembayaran',
                    'kerahasiaan' => 'Kerahasiaan',
                    'penyelesaian_sengketa' => 'Penyelesaian Sengketa',
                    'penutup' => 'Penutup'
                ];
            @endphp

            @foreach($sections as $key => $title)
            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3 pb-1 border-b-2 border-indigo-500">{{ $title }}</h2>
                <div class="prose max-w-none">
                    {!! $surat->$key !!}
                </div>
            </section>
            @endforeach
        </div>

        <!-- Tanda Tangan -->
        <section class="mt-12 flex justify-end print-break">
            <div class="w-1/2 text-center">
                <p class="font-semibold text-gray-700 mb-8">Menyetujui,</p>
                
                @if($surat->tanda_tangan)
                    <div class="mb-2">
                        <img src="{{ $surat->tanda_tangan }}" alt="Tanda Tangan" class="mx-auto w-40 h-auto">
                    </div>
                @else
                    <div class="mb-8 h-20"></div>
                @endif
                
                <div class="border-t-2 border-gray-800 pt-2">
                    <p class="font-semibold text-gray-800">(...........................)</p>
                    <p class="text-sm text-gray-600 mt-1">Pihak Terkait</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer Aksi -->
    <footer class="bg-gray-50 px-8 py-4 border-t no-print">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.surat_kerjasama.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 px-6 rounded-lg transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('admin.surat_kerjasama.edit', $surat->id) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out">
                <i class="fas fa-edit mr-2"></i> Edit Surat
            </a>
        </div>
    </footer>

</div>

</body>
</html>