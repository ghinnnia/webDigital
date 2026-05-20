<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Surat Kerjasama</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    
    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#ffffff",
                        "background-dark": "#f8fafc",
                        "sidebar-light": "#f3f4f6",
                        "sidebar-dark": "#1e293b",
                        "card-light": "#ffffff",
                        "card-dark": "#1e293b",
                        "text-light": "#1e293b",
                        "text-dark": "#f8fafc",
                        "text-muted-light": "#64748b",
                        "text-muted-dark": "#94a3b8",
                        "border-light": "#e2e8f0",
                        "border-dark": "#334155",
                        "success": "#10b981",
                        "warning": "#f59e0b",
                        "danger": "#ef4444"
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                    boxShadow: {
                        card: "0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)",
                        "card-hover": "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)"
                    },
                },
            },
        };
    </script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }
        
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }

        .material-symbols-outlined.filled {
            font-variation-settings:
                'FILL' 1,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
        
        /* Panel Styles */
        .panel {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        
        .panel-header {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .panel-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .panel-body {
            padding: 1.5rem;
        }
        
        /* Custom styles untuk input minimalis */
        .minimal-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
        }
        
        .minimal-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Style untuk CKEditor agar sesuai tema */
        .ck-editor__editable {
            min-height: 200px;
        }
        
        /* Canvas untuk tanda tangan */
        #signature-pad {
            border: 1px solid #e2e8f0;
            cursor: crosshair;
            border-radius: 0.5rem;
        }
        
        /* Form section styles */
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Button styles */
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-secondary {
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background-color: #e2e8f0;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('admin.templet.sider')

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">
                <!-- Header Form -->
                <div class="mb-6">
                    <h2 class="text-xl sm:text-3xl font-bold mb-2">Buat Surat Kerjasama Baru</h2>
                    <p class="text-text-muted-light">Isi formulir di bawah ini untuk membuat surat kerjasama baru.</p>
                </div>

                <form method="POST" action="{{ route('admin.surat_kerjasama.store') }}">
                    @csrf

                    <!-- Panel Informasi Dasar -->
                    <div class="panel mb-6">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">info</span>
                                Informasi Dasar
                            </h3>
                        </div>
                        <div class="panel-body">
                            <!-- Grid untuk 2 kolom pada layar besar -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Judul -->
                                <div>
                                    <label for="judul" class="block text-sm font-medium text-gray-900 mb-2">
                                        Judul Surat
                                    </label>
                                    <input type="text" id="judul" name="judul"
                                           class="w-full minimal-input" required>
                                </div>

                                <!-- Nomor Surat -->
                                <div>
                                    <label for="nomor_surat" class="block text-sm font-medium text-gray-900 mb-2">
                                        Nomor Surat
                                    </label>
                                    <input type="text" id="nomor_surat" name="nomor_surat"
                                           class="w-full minimal-input" required>
                                </div>
                            </div>
                            
                            <!-- Tanggal -->
                            <div class="mt-6">
                                <label for="tanggal" class="block text-sm font-medium text-gray-900 mb-2">
                                    Tanggal Surat
                                </label>
                                <input type="date" id="tanggal" name="tanggal"
                                       class="w-full md:w-1/2 minimal-input" required>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Isi Surat -->
                    <div class="panel mb-6">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">description</span>
                                Isi Surat
                            </h3>
                        </div>
                        <div class="panel-body">
                            <!-- Bagian-bagian Surat dengan CKEditor -->
                            <div class="space-y-6">
                                <!-- Para Pihak -->
                                <div class="form-section">
                                    <label for="para_pihak" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">people</span>
                                        Para Pihak
                                    </label>
                                    <textarea id="para_pihak" name="para_pihak" class="hidden"></textarea>
                                </div>

                                <!-- Maksud dan Tujuan -->
                                <div class="form-section">
                                    <label for="maksud_tujuan" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">flag</span>
                                        Maksud dan Tujuan
                                    </label>
                                    <textarea id="maksud_tujuan" name="maksud_tujuan" class="hidden"></textarea>
                                </div>

                                <!-- Ruang Lingkup -->
                                <div class="form-section">
                                    <label for="ruang_lingkup" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">category</span>
                                        Ruang Lingkup Kerjasama
                                    </label>
                                    <textarea id="ruang_lingkup" name="ruang_lingkup" class="hidden"></textarea>
                                </div>

                                <!-- Jangka Waktu -->
                                <div class="form-section">
                                    <label for="jangka_waktu" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">schedule</span>
                                        Jangka Waktu Kerjasama
                                    </label>
                                    <textarea id="jangka_waktu" name="jangka_waktu" class="hidden"></textarea>
                                </div>

                                <!-- Biaya -->
                                <div class="form-section">
                                    <label for="biaya_pembayaran" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">payments</span>
                                        Biaya dan Pembayaran
                                    </label>
                                    <textarea id="biaya_pembayaran" name="biaya_pembayaran" class="hidden"></textarea>
                                </div>

                                <!-- Kerahasiaan -->
                                <div class="form-section">
                                    <label for="kerahasiaan" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">lock</span>
                                        Kerahasiaan
                                    </label>
                                    <textarea id="kerahasiaan" name="kerahasiaan" class="hidden"></textarea>
                                </div>

                                <!-- Sengketa -->
                                <div class="form-section">
                                    <label for="penyelesaian_sengketa" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">gavel</span>
                                        Penyelesaian Sengketa
                                    </label>
                                    <textarea id="penyelesaian_sengketa" name="penyelesaian_sengketa" class="hidden"></textarea>
                                </div>

                                <!-- Penutup -->
                                <div class="form-section">
                                    <label for="penutup" class="form-section-title">
                                        <span class="material-icons-outlined text-sm">summarize</span>
                                        Penutup
                                    </label>
                                    <textarea id="penutup" name="penutup" class="hidden"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Tanda Tangan -->
                    <div class="panel mb-6">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">draw</span>
                                Tanda Tangan Digital
                            </h3>
                        </div>
                        <div class="panel-body">
                            <p class="text-sm text-text-muted-light mb-4">Buat tanda tangan digital Anda di bawah ini:</p>
                            <div class="bg-gray-50 p-2 rounded">
                                <canvas id="signature-pad" width="600" height="200" class="w-full bg-white rounded"></canvas>
                            </div>
                            <input type="hidden" name="tanda_tangan" id="tanda_tangan">

                            <button type="button"
                                    onclick="clearCanvas()"
                                    class="mt-4 btn-secondary py-2 px-4 rounded flex items-center gap-2">
                                <i class='bx bx-eraser'></i>
                                Hapus Tanda Tangan
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end items-center gap-4">
                        <a href="{{ route('admin.surat_kerjasama.index') }}"
                           class="btn-secondary py-2.5 px-6 rounded flex items-center gap-2">
                            <i class='bx bx-arrow-back'></i>
                            Kembali
                        </a>
                        <button type="submit"
                                class="btn-primary py-2.5 px-6 rounded flex items-center gap-2">
                            <i class='bx bx-save'></i>
                            Simpan Surat
                        </button>
                    </div>
                </form>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- CKEditor Init -->
    <script>
        document.querySelectorAll('textarea').forEach(textarea => {
            if (textarea.id) {
                ClassicEditor
                    .create(document.querySelector('#' + textarea.id), {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', '|',
                                'link', 'bulletedList', 'numberedList', '|',
                                'outdent', 'indent', '|',
                                'blockQuote', 'insertTable', '|',
                                'undo', 'redo'
                            ]
                        }
                    })
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            textarea.value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    </script>

    <!-- Canvas Tanda Tangan -->
    <script>
        const canvas = document.getElementById('signature-pad');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        // Set ukuran canvas agar tidak blur
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            ctx.scale(ratio, ratio);
            ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        // Fungsi menggambar
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        canvas.addEventListener('mousemove', draw);

        // Touch events untuk mobile
        canvas.addEventListener('touchstart', handleTouch);
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchmove', handleTouch);

        function startDrawing(e) {
            drawing = true;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        function stopDrawing() {
            if (!drawing) return;
            drawing = false;
            document.getElementById('tanda_tangan').value = canvas.toDataURL();
        }

        function draw(e) {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000000';
            ctx.lineTo(x, y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('tanda_tangan').value = '';
            // Isi kembali dengan background putih
            ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
    </script>
</body>
</html>