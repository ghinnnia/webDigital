<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen TTD Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans">
    @include('admin.templet.sider')

    <main class="flex-1 ml-[250px] p-6">
        <div class="container mx-auto max-w-4xl">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-signature text-indigo-600"></i>
                        Manajemen Tanda Tangan Digital
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Kelola tanda tangan untuk Finance, HR, dan Karyawan</p>
                </div>
                <a href="{{ route('admin.beranda') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-pen-fancy"></i> Upload / Ganti TTD
                    </h2>
                    <p class="text-indigo-200 text-sm mt-1">Pilih jabatan, pilih pejabat, upload gambar tanda tangan</p>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.ttd.store') }}" enctype="multipart/form-data" id="ttdForm">
                        @csrf
                        
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Jabatan</label>
                            <select name="jabatan" id="jabatanSelect" class="w-full border rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="finance">💰 Finance</option>
                                <option value="hr">👥 HR (Human Resources)</option>
                                <option value="karyawan">👤 Karyawan</option>
                            </select>
                        </div>

                        <div id="previewData" class="mb-5 hidden">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <i class="fa-solid fa-eye"></i> Data Saat Ini
                                </h3>
                                <div id="currentData" class="text-sm text-gray-600"></div>
                            </div>
                        </div>
                        
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pejabat</label>
                            <select name="nama_pejabat" id="nama_pejabat" 
                                    class="w-full border rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">-- Pilih Pejabat --</option>
                            </select>
                        </div>
                        
                        <div class="mt-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Tanda Tangan</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition">
                                <input type="file" name="ttd_image" id="ttd_image" accept="image/png,image/jpg,image/jpeg" class="hidden" onchange="previewImage(this)">
                                <div id="uploadArea" onclick="document.getElementById('ttd_image').click()" class="cursor-pointer">
                                    <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500">Klik atau drag & drop untuk upload gambar</p>
                                    <p class="text-xs text-gray-400 mt-1">Format PNG/JPG, maks 2MB</p>
                                </div>
                                <div id="imagePreview" class="mt-3 hidden">
                                    <img id="previewImg" src="" alt="Preview" class="h-20 mx-auto border rounded p-1">
                                    <p class="text-xs text-green-600 mt-1">Gambar siap diupload</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5 flex items-center gap-3">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-indigo-600">
                                <span class="text-sm">Aktifkan Tanda Tangan ini</span>
                            </label>
                        </div>
                        
                        <div class="mt-6 flex gap-3">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                                <i class="fa-solid fa-save mr-2"></i> Simpan Tanda Tangan
                            </button>
                            <button type="button" onclick="resetForm()" class="px-6 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i> Tanda Tangan Tersimpan
                    </h2>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @php
                        $jabatans = ['finance' => '💰 Finance', 'hr' => '👥 HR', 'karyawan' => '👤 Karyawan'];
                    @endphp
                    
                    @foreach($jabatans as $jabatanKey => $jabatanName)
                        @php $ttd = $ttds[$jabatanKey] ?? null; @endphp
                        <div class="p-5 flex justify-between items-center hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-24 text-center">
                                    @if($ttd && $ttd->file_path)
                                        <img src="{{ asset('storage/' . $ttd->file_path) }}" alt="TTD" class="h-12 mx-auto object-contain">
                                    @else
                                        <div class="text-gray-400 text-sm">Belum upload</div>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">{{ explode(' ', $jabatanName)[0] }}</span>
                                        <span class="font-semibold text-gray-800">{{ $jabatanName }}</span>
                                        @if($ttd && $ttd->is_active)
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Aktif</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $ttd->nama_pejabat ?? 'Belum diisi' }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if($ttd)
                                    <button onclick="editTtd('{{ $jabatanKey }}', '{{ addslashes($ttd->nama_pejabat ?? '') }}', {{ $ttd->is_active ? 'true' : 'false' }})" 
                                            class="text-blue-600 hover:text-blue-800 p-2 rounded hover:bg-blue-50">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.ttd.destroy', $ttd->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus TTD ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 bg-white rounded-xl shadow-md p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i> Preview Tanda Tangan di Slip Gaji
                </h3>
                <div class="grid grid-cols-3 gap-4 text-center border-t pt-4">
                    @foreach($jabatans as $jabatanKey => $jabatanName)
                        @php $ttd = $ttds[$jabatanKey] ?? null; @endphp
                        <div>
                            <p class="text-sm font-semibold text-gray-600">{{ $jabatanName }}</p>
                            @if($ttd && $ttd->file_path)
                                <img src="{{ asset('storage/' . $ttd->file_path) }}" class="h-16 mx-auto mt-2">
                                <p class="text-xs font-medium mt-2">{{ $ttd->nama_pejabat ?? '-' }}</p>
                            @else
                                <div class="h-16 flex items-center justify-center text-gray-400 mt-2">___________</div>
                                <p class="text-xs text-gray-400 mt-2">Belum diisi</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <script>
        // Data TTD dari server
        const ttdData = @json($ttds);
        
        // Data pejabat dari database (dikelompokkan berdasarkan role)
        const pejabatData = @json($pejabatList);
        
        // Update dropdown pejabat berdasarkan jabatan yang dipilih
        function updatePejabatDropdown(jabatan) {
            const select = document.getElementById('nama_pejabat');
            
            // Hapus semua opsi kecuali opsi default pertama
            select.innerHTML = '<option value="">-- Pilih Pejabat --</option>';
            
            // Ambil data user yang memiliki role sesuai yang dipilih
            if (jabatan && pejabatData[jabatan] && pejabatData[jabatan].length > 0) {
                pejabatData[jabatan].forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.name;
                    option.textContent = user.name + (user.email ? ' (' + user.email + ')' : '');
                    select.appendChild(option);
                });
            } else if (jabatan) {
                // Jika tidak ada data, beri tahu user
                const option = document.createElement('option');
                option.value = "";
                option.textContent = "Tidak ada data karyawan dengan jabatan ini";
                option.disabled = true;
                select.appendChild(option);
            }
        }
        
        // Preview image sebelum upload
        function previewImage(input) {
            const preview = document.getElementById('previewImg');
            const imagePreview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    document.getElementById('uploadArea').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Reset form
        function resetForm() {
            document.getElementById('ttdForm').reset();
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('uploadArea').classList.remove('hidden');
            document.getElementById('previewData').classList.add('hidden');
            document.getElementById('previewImg').src = '';
            
            // Reset dropdown nama pejabat
            const select = document.getElementById('nama_pejabat');
            select.innerHTML = '<option value="">-- Pilih Pejabat --</option>';
        }
        
        // Edit TTD
        function editTtd(jabatan, nama, isActive) {
            const select = document.getElementById('jabatanSelect');
            select.value = jabatan;
            select.dispatchEvent(new Event('change'));
            
            // Setelah dropdown terisi, set nilai nama pejabat
            setTimeout(() => {
                const namaSelect = document.getElementById('nama_pejabat');
                for (let i = 0; i < namaSelect.options.length; i++) {
                    if (namaSelect.options[i].value === nama) {
                        namaSelect.value = nama;
                        break;
                    }
                }
            }, 100);
            
            document.getElementById('is_active').checked = isActive;
            
            // Scroll ke form
            document.querySelector('.bg-white.rounded-xl.shadow-md.overflow-hidden').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Load data saat jabatan dipilih
        document.getElementById('jabatanSelect').addEventListener('change', function() {
            const jabatan = this.value;
            const previewDiv = document.getElementById('previewData');
            const currentDataDiv = document.getElementById('currentData');
            
            // Jalankan fungsi filter dropdown nama pejabat
            updatePejabatDropdown(jabatan);
            
            if (jabatan && ttdData[jabatan]) {
                const data = ttdData[jabatan];
                currentDataDiv.innerHTML = `
                    <div class="flex items-center gap-4">
                        ${data.file_path ? `<img src="{{ asset('storage/') }}/${data.file_path}" class="h-12 object-contain border rounded p-1">` : '<div class="text-gray-400">Belum ada TTD</div>'}
                        <div>
                            <p><strong>Nama:</strong> ${data.nama_pejabat || '-'}</p>
                            <p><strong>Status:</strong> ${data.is_active ? '<span class="text-green-600">Aktif</span>' : '<span class="text-red-600">Tidak Aktif</span>'}</p>
                        </div>
                    </div>
                `;
                previewDiv.classList.remove('hidden');
                
                // Isi form dengan data yang ada
                const namaSelect = document.getElementById('nama_pejabat');
                let found = false;
                for (let i = 0; i < namaSelect.options.length; i++) {
                    if (namaSelect.options[i].value === data.nama_pejabat) {
                        namaSelect.value = data.nama_pejabat;
                        found = true;
                        break;
                    }
                }
                if (!found && data.nama_pejabat) {
                    // Jika nama tidak ditemukan di dropdown, tetap tampilkan
                    console.log('Nama tidak ditemukan di dropdown:', data.nama_pejabat);
                }
                
                document.getElementById('is_active').checked = data.is_active || false;
            } else if (jabatan) {
                currentDataDiv.innerHTML = '<p class="text-gray-500">Belum ada data untuk jabatan ini. Silakan upload TTD baru.</p>';
                previewDiv.classList.remove('hidden');
                document.getElementById('nama_pejabat').value = '';
                document.getElementById('is_active').checked = true;
            } else {
                previewDiv.classList.add('hidden');
            }
        });
        
        // Submit form
        document.getElementById('ttdForm').addEventListener('submit', function(e) {
            const jabatan = document.getElementById('jabatanSelect').value;
            const namaPejabat = document.getElementById('nama_pejabat').value;
            
            if (!jabatan) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Pilih jabatan terlebih dahulu', 'warning');
                return;
            }
            
            if (!namaPejabat) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Pilih nama pejabat terlebih dahulu', 'warning');
                return;
            }
            
            // Validasi file upload
            const fileInput = document.getElementById('ttd_image');
            if (!fileInput.files || fileInput.files.length === 0) {
                const existingData = ttdData[jabatan];
                if (!existingData || !existingData.file_path) {
                    e.preventDefault();
                    Swal.fire('Peringatan', 'Upload gambar tanda tangan terlebih dahulu', 'warning');
                    return;
                }
            }
        });
    </script>
</body>
</html>