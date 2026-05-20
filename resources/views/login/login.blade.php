<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Digital Agency</title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#6366f1",
                        "custom-blue": {
                            50: "#eff6ff",
                            100: "#dbeafe",
                            200: "#bfdbfe",
                            300: "#93c5fd",
                            400: "#60a5fa",
                            500: "#3b82f6",
                            600: "#2563eb",
                            700: "#1d4ed8",
                            800: "#1e40af",
                            900: "#1e3a8a",
                        },
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                        sans: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                },
            },
        };
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .animate-fade {
            transition: opacity 0.5s ease-in-out;
        }
        .opacity-0 {
            opacity: 0;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4 transition-colors duration-200">

    <main class="w-full max-w-md animate-fade">
        <!-- Tambahkan ID pada form untuk diakses JavaScript -->
        <form id="loginForm" class="space-y-5" method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-gray-100 transition-colors duration-200">
                
                <!-- Logo Anda -->
                <div class="flex flex-col items-center mb-8">
                    <div class="w-40 mb-4 flex items-center justify-center">
                        <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Digital Agency Logo" class="w-full h-auto object-contain max-h-full">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 text-center">Selamat Datang</h1>
                    <p class="text-gray-500 text-center text-sm">Silakan login ke Digital Agency</p>
                </div>

                <!-- Pesan Error -->
                @if(session('error'))
                    <div class="mb-6 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Input Email -->
                <div class="mb-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="Masukkan email"
                        class="@error('email') border-red-500 @enderror w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-custom-blue-500 focus:border-transparent transition-all outline-none placeholder-gray-400">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Password -->
                <!-- TAMBAHKAN KELAS mb-8 DI SINI -->
                <div class="mb-8">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Password</label>
                    <div class="relative">
                        <!-- Type diubah menjadi text agar bisa dikontrol JS -->
                        <input id="password" type="text" name="password_placeholder" required placeholder="Masukkan password" autocomplete="current-password"
                            class="@error('password') border-red-500 @enderror w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-custom-blue-500 focus:border-transparent transition-all outline-none placeholder-gray-400">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                            <span id="toggleIcon" class="material-icons-outlined">visibility_off</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Login -->
                <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-lg bg-custom-blue-500 hover:bg-custom-blue-600 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.01] active:scale-[0.99] transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Login
                </button>
            </div>
        </form>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');
        const toggleIcon = document.getElementById('toggleIcon');
        
        // Karakter untuk sensor password (kotak kecil)
        const maskChar = '▪';
        
        // Variabel untuk menyimpan password asli
        let actualPassword = '';
        let isPasswordVisible = false;

        // Fungsi untuk memperbarui tampilan password menjadi sensor kotak kecil
        function updateDisplay() {
            if (!isPasswordVisible) {
                // Simpan posisi kursor sebelum mengubah nilai
                const cursorPos = passwordInput.selectionStart;
                passwordInput.value = maskChar.repeat(actualPassword.length);
                // Kembalikan posisi kursor
                passwordInput.setSelectionRange(cursorPos, cursorPos);
            }
        }

        // Event listener untuk mengetik
        passwordInput.addEventListener('keydown', (e) => {
            // Jika password sedang terlihat, biarkan perilaku default
            if (isPasswordVisible) {
                actualPassword = passwordInput.value; // Sinkronkan password asli
                return;
            }

            // Tangani input karakter biasa
            if (e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                const cursorPos = passwordInput.selectionStart;
                actualPassword = actualPassword.slice(0, cursorPos) + e.key + actualPassword.slice(cursorPos);
                updateDisplay();
                // Pindahkan kursor ke posisi berikutnya
                passwordInput.setSelectionRange(cursorPos + 1, cursorPos + 1);
            } 
            // Tangani penghapusan (backspace)
            else if (e.key === 'Backspace') {
                e.preventDefault();
                const cursorPos = passwordInput.selectionStart;
                if (cursorPos > 0) {
                    actualPassword = actualPassword.slice(0, cursorPos - 1) + actualPassword.slice(cursorPos);
                    updateDisplay();
                    passwordInput.setSelectionRange(cursorPos - 1, cursorPos - 1);
                }
            }
            // Tangani delete
            else if (e.key === 'Delete') {
                 e.preventDefault();
                 const cursorPos = passwordInput.selectionStart;
                 if (cursorPos < actualPassword.length) {
                    actualPassword = actualPassword.slice(0, cursorPos) + actualPassword.slice(cursorPos + 1);
                    updateDisplay();
                    passwordInput.setSelectionRange(cursorPos, cursorPos);
                 }
            }
        });
        
        // Event listener untuk paste (Ctrl+V)
        passwordInput.addEventListener('paste', (e) => {
            e.preventDefault();
            if (isPasswordVisible) {
                 // Biarkan paste default jika terlihat, lalu sinkronkan
                 setTimeout(() => actualPassword = passwordInput.value, 0);
                 return;
            }
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const cursorPos = passwordInput.selectionStart;
            actualPassword = actualPassword.slice(0, cursorPos) + pastedText + actualPassword.slice(cursorPos);
            updateDisplay();
            // Letakkan kursor di akhir teks yang dipaste
            const newCursorPos = cursorPos + pastedText.length;
            passwordInput.setSelectionRange(newCursorPos, newCursorPos);
        });

        // Fungsi untuk toggle visibility password
        function togglePassword() {
            isPasswordVisible = !isPasswordVisible;
            
            if (isPasswordVisible) {
                // Tampilkan password asli
                passwordInput.value = actualPassword;
                toggleIcon.textContent = 'visibility';
            } else {
                // Sembunyikan dan tampilkan sensor kotak kecil
                updateDisplay();
                toggleIcon.textContent = 'visibility_off';
            }
        }

        // Saat form akan di-submit, pastikan yang dikirim adalah password asli
        loginForm.addEventListener('submit', (e) => {
            // Jika password masih dalam mode sensor, pastikan nilai input adalah password asli
            if (!isPasswordVisible) {
                // Buat input hidden baru untuk menyimpan password asli
                const hiddenPasswordInput = document.createElement('input');
                hiddenPasswordInput.type = 'hidden';
                hiddenPasswordInput.name = 'password'; // Nama asli yang diharapkan server
                hiddenPasswordInput.value = actualPassword;
                
                // Ubah nama input yang terlihat agar tidak ikut dikirim
                passwordInput.name = 'password_visible_placeholder';
                
                // Tambahkan input hidden ke form
                loginForm.appendChild(hiddenPasswordInput);
            } else {
                // Jika terlihat, cukup ubah namanya menjadi nama asli
                passwordInput.name = 'password';
            }
            // Form akan melanjutkan submit
        });

        // Inisialisasi tampilan awal
        updateDisplay();
    </script>
</body>
</html>