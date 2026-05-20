<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        secondary: "#64748b",
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen pb-10">

    @include('karyawan.templet.header')
    @php
        $user = $karyawan ?? Auth::user();
        $divisiNama = optional($user->divisi)->divisi;
        if (!$divisiNama && !empty(optional($user->karyawan)->divisi)) {
            $divisiRaw = trim((string) $user->karyawan->divisi);
            if (is_numeric($divisiRaw)) {
                $divisiNama = optional(\App\Models\Divisi::find((int) $divisiRaw))->divisi;
            } else {
                $divisiNama = $divisiRaw;
            }
        }
        $divisiNama = $divisiNama ?: '-';
        $timNama = optional(optional($user->karyawan)->tim)->tim ?? null;
        $timLabel = $timNama ? ('Tim ' . $timNama) : '-';
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Profil saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi pribadi dan data pekerjaan Anda.</p>
        </div>
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Foto Profil Saja -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Card Foto -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 text-center">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">FOTO KARYAWAN</h3>
                        
                        <div class="relative w-40 h-40 mx-auto mb-4 group">
                            <div class="w-full h-full rounded-full bg-gray-200 overflow-hidden border-4 border-gray-50 shadow-inner">
                                <!-- Placeholder Image -->
                                <img src="{{ !empty($user->foto) ? asset('storage/' . $user->foto) : ('https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=0D8ABC&color=fff&size=256') }}" 
                                     alt="Foto Profil" 
                                     class="w-full h-full object-cover">
                            </div>
                            <!-- Edit Button Overlay (Optional) -->
                            <button type="button" onclick="document.getElementById('profileFotoInput').click()"
                                class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-md text-gray-600 hover:text-primary transition">
                                <span class="material-symbols-outlined text-2xl">edit</span>
                            </button>
                        </div>
                        
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name ?? '-' }}</h2>
                        <p class="text-sm text-gray-500 mb-6">{{ $user->role ?? '-' }}</p>
                        
                        <form id="profileFotoForm" action="{{ route('karyawan.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input id="profileFotoInput" type="file" name="foto" accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('profileFotoInput').click()"
                                class="w-full py-2 px-4 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                                Ubah Foto
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Info Pekerjaan & Informasi Pribadi -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Card Info Pekerjaan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">work</span>
                        Informasi Pekerjaan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-6">
                        <!-- Divisi -->
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 uppercase font-semibold mb-1">Divisi</span>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-400 text-lg">business</span>
                                <span class="text-sm font-medium text-gray-800">{{ $divisiNama }}</span>
                            </div>
                        </div>

                        <!-- Tim -->
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 uppercase font-semibold mb-1">Tim</span>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-400 text-lg">groups</span>
                                <span class="text-sm font-medium text-gray-800">{{ $timLabel }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Personal Info (Dipindahkan dari kiri) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span>
                        Informasi Pribadi
                    </h3>
                    <form action="{{ route('karyawan.profile.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                        @csrf
                        <div>
                            <label for="name" class="text-xs text-gray-400 uppercase font-semibold">Nama Lengkap</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label for="email" class="text-xs text-gray-400 uppercase font-semibold">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label for="kontak" class="text-xs text-gray-400 uppercase font-semibold">Nomor Telepon</label>
                            <input id="kontak" name="kontak" type="text" value="{{ old('kontak', $user->kontak) }}"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 uppercase font-semibold">Posisi</label>
                            <input type="text" value="{{ $user->role ?? '-' }}" readonly
                                class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat" class="text-xs text-gray-400 uppercase font-semibold">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                        <div class="md:col-span-2 mt-2">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                                <span class="material-symbols-outlined text-sm">save</span>
                                Simpan Informasi Pribadi
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-12 text-center text-gray-400 text-sm">
            <p>Copyright ©2025 by digicity.id</p>
        </footer>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fotoInput = document.getElementById('profileFotoInput');
        const fotoForm = document.getElementById('profileFotoForm');

        if (fotoInput && fotoForm) {
            fotoInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    fotoForm.submit();
                }
            });
        }
    });
</script>
</body>
</html>
