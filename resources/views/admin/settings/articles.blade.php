<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pengaturan Artikel - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('logo1.jpeg') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <!-- GANTI DENGAN TRIX EDITOR -->
    <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#ffffff",
                        "text-light": "#1e293b",
                        "text-muted-light": "#64748b",
                        "border-light": "#e2e8f0",
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

        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Style khusus untuk Trix agar sesuai dengan tema */
        trix-editor {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem;
            min-height: 300px;
            max-height: 400px;
            overflow-y: auto;
        }

        trix-editor:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

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
            flex-wrap: wrap;
            gap: 1rem;
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

        /* Enhanced Modal styles with proper scrolling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            padding: 1rem;
            box-sizing: border-box;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background-color: white;
            border-radius: 0.75rem;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            transition: transform 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.active .modal {
            transform: scale(1);
        }

        /* Delete modal specific styling */
        #deleteModal .modal {
            max-width: 500px;
            max-height: 80vh;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8fafc;
            flex-shrink: 0;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
            /* Make modal body scrollable */
        }

        .image-preview {
            width: 100%;
            height: 200px;
            background-color: #f1f5f9;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #3b82f6;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .breadcrumb a {
            color: #3b82f6;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb-separator {
            margin: 0 0.5rem;
        }

        .article-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .article-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .article-image {
            height: 200px;
            background-color: #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .article-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .article-content {
            padding: 1rem;
        }

        .article-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 0 1rem 1rem;
        }

        /* Enhanced popup styles */
        .minimal-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 350px;
            border-left: 4px solid #10b981;
        }

        .minimal-popup.show {
            transform: translateX(0);
        }

        .minimal-popup.error {
            border-left-color: #ef4444;
        }

        .minimal-popup.warning {
            border-left-color: #f59e0b;
        }

        .minimal-popup-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .minimal-popup.success .minimal-popup-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .minimal-popup.error .minimal-popup-icon {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .minimal-popup.warning .minimal-popup-icon {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .minimal-popup-content {
            flex-grow: 1;
        }

        .minimal-popup-title {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .minimal-popup-message {
            font-size: 0.875rem;
            color: #64748b;
        }

        .minimal-popup-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .minimal-popup-close:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        /* Form group styling */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.75rem;
            font-size: 0.875rem;
            color: #1e293b;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .app-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }

            .main-content {
                width: 100%;
            }

            .panel-header {
                padding: 0.75rem 1rem;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: space-between;
            }

            .panel-header button {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }

            .panel-body {
                padding: 1rem;
            }

            .panel-title {
                font-size: 1rem;
            }

            /* Modal adjustments for mobile */
            .modal-overlay {
                padding: 0.5rem;
            }

            .modal {
                width: 100%;
                max-width: none;
                max-height: 95vh;
                border-radius: 0.75rem 0.75rem 0 0;
                margin: auto 0;
            }

            .modal-header {
                padding: 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-title {
                font-size: 1.1rem;
            }

            /* Trix Editor adjustment for mobile */
            trix-editor {
                min-height: 250px;
                max-height: 300px;
            }

            /* Form buttons in modal */
            .modal-body .flex.justify-end {
                flex-direction: column;
                gap: 0.75rem;
            }

            .modal-body .flex.justify-end button {
                width: 100%;
            }

            /* Popup adjustments */
            .minimal-popup {
                left: 20px;
                right: 20px;
                max-width: none;
                transform: translateY(-100px);
            }

            .minimal-popup.show {
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .panel-header {
                padding: 0.5rem 0.75rem;
            }

            .panel-header button {
                padding: 0.5rem 0.5rem;
                font-size: 0.8rem;
            }

            .panel-body {
                padding: 0.75rem;
            }

            .panel-title {
                font-size: 0.9rem;
            }

            /* Further modal adjustments for very small screens */
            .modal-overlay {
                padding: 0;
            }

            .modal {
                width: 100%;
                max-height: 100vh;
                border-radius: 0;
                height: 100vh;
            }

            .modal-header {
                padding: 0.75rem;
            }

            .modal-body {
                padding: 0.75rem;
            }

            .modal-title {
                font-size: 1rem;
            }

            /* Further Trix Editor adjustment */
            trix-editor {
                min-height: 200px;
                max-height: 250px;
            }

            /* Further popup adjustments */
            .minimal-popup {
                top: 10px;
                left: 10px;
                right: 10px;
                padding: 12px 16px;
            }
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen app-container">
        @include('admin/templet/sider')

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">

                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="{{ route('admin.settings.contact') }}">Dashboard</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Pengaturan Artikel</span>
                </div>

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Pengaturan Artikel</h2>

                <div class="panel mb-6">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">article</span>
                            Daftar Artikel
                        </h3>
                        <button id="addArticleBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                            <span class="material-icons-outlined text-sm">add</span>
                            <span class="hidden sm:inline">Tambah Artikel</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($articles as $article)
                                <div class="article-card" data-id="{{ $article->id }}">
                                    <div class="article-image">
                                        @if($article->image)
                                            <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}">
                                        @else
                                            <div class="flex items-center justify-center h-full">
                                                <span class="material-icons-outlined text-4xl text-gray-400">image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="article-content">
                                        <h4 class="font-semibold text-lg mb-1">{{ $article->title }}</h4>
                                        <p class="text-gray-600 text-sm mb-2">
                                            {{ Str::limit($article->excerpt ?: strip_tags($article->content), 100) }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                @if($article->is_featured)
                                                    <span
                                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Unggulan</span>
                                                @endif
                                                <span class="text-gray-500 text-xs">Urutan: {{ $article->order }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="article-actions">
                                        <button class="edit-article-btn p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                            data-id="{{ $article->id }}">
                                            <span class="material-icons-outlined">edit</span>
                                        </button>
                                        <button class="delete-article-btn p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                            data-id="{{ $article->id }}">
                                            <span class="material-icons-outlined">delete</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Modal Artikel -->
    <div id="articleModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <span class="material-icons-outlined text-primary">article</span>
                    <span id="modalTitle">Tambah Artikel</span>
                </h3>
                <button class="modal-close" id="closeModal">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="articleForm">
                    @csrf
                    <input type="hidden" id="articleId" name="id">
                    <input type="hidden" id="_method" name="_method" value="POST">

                    <div class="form-group">
                        <label class="form-label" for="titleInput">Judul</label>
                        <input type="text" name="title" id="titleInput"
                            class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="excerptInput">Ringkasan (Opsional)</label>
                        <textarea name="excerpt" id="excerptInput"
                            class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contentInput">Konten</label>
                        <input type="hidden" name="content" id="contentInput">
                        <trix-editor input="contentInput" class="form-control"></trix-editor>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="imageInput">Gambar</label>
                        <div class="image-preview" id="imagePreview">
                            <span class="material-icons-outlined text-gray-400">image</span>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/*"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <div class="flex items-center justify-between">
                            <label class="form-label mb-0">Tampilkan di Halaman Utama</label>
                            <label class="switch">
                                <input type="checkbox" name="is_featured" id="isFeaturedInput">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="orderInput">Urutan Tampil</label>
                        <input type="number" name="order" id="orderInput" min="0" value="0"
                            class="form-control">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button type="button" id="cancelBtn" class="px-4 py-2 btn-secondary rounded-lg w-full sm:w-auto">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg w-full sm:w-auto">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <span class="material-icons-outlined text-danger">warning</span>
                    Konfirmasi Hapus
                </h3>
                <button class="modal-close" id="closeDeleteModal">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus artikel ini?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteId" name="id">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelDeleteBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Minimalist Popup -->
    <div id="minimalPopup" class="minimal-popup">
        <div class="minimal-popup-icon">
            <span class="material-icons-outlined">check</span>
        </div>
        <div class="minimal-popup-content">
            <div class="minimal-popup-title">Berhasil</div>
            <div class="minimal-popup-message">Operasi berhasil dilakukan</div>
        </div>
        <button class="minimal-popup-close">
            <span class="material-icons-outlined text-sm">close</span>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Deklarasi Semua Elemen yang Dibutuhkan ---
            const articleModal = document.getElementById('articleModal');
            const deleteModal = document.getElementById('deleteModal');
            const articleForm = document.getElementById('articleForm');
            const deleteForm = document.getElementById('deleteForm');
            const addArticleBtn = document.getElementById('addArticleBtn');
            const closeModalBtn = document.getElementById('closeModal');
            const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const modalTitle = document.getElementById('modalTitle');
            const articleIdInput = document.getElementById('articleId');
            const deleteIdInput = document.getElementById('deleteId');
            const methodInput = document.getElementById('_method');
            const titleInput = document.getElementById('titleInput');
            const excerptInput = document.getElementById('excerptInput');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const isFeaturedInput = document.getElementById('isFeaturedInput');
            const orderInput = document.getElementById('orderInput');
            const contentInput = document.getElementById('contentInput');

            // --- Cek Elemen Krusial ---
            if (!articleForm || !articleIdInput || !methodInput) {
                alert('Error: Elemen form kritis tidak ditemukan. Pastikan input dengan id "articleId" dan "_method" ada di dalam form.');
                return;
            }

            // --- Fungsi Helper untuk Popup ---
            function showMinimalPopup(title, message, type = 'success') {
                const popup = document.getElementById('minimalPopup');
                if (!popup) return;

                const popupTitle = popup.querySelector('.minimal-popup-title');
                const popupMessage = popup.querySelector('.minimal-popup-message');
                const popupIcon = popup.querySelector('.minimal-popup-icon span');

                popupTitle.textContent = title;
                popupMessage.textContent = message;
                popup.className = `minimal-popup show ${type}`;

                if (type === 'success') popupIcon.textContent = 'check';
                else if (type === 'error') popupIcon.textContent = 'error';
                else if (type === 'warning') popupIcon.textContent = 'warning';

                setTimeout(() => popup.classList.remove('show'), 3000);
            }

            // --- Fungsi untuk menampilkan modal dengan animasi ---
            function showModal(modal) {
                modal.classList.add('active');
                document.body.classList.add('modal-open'); // Prevent body scroll
                
                // Add touch prevention for mobile
                document.body.addEventListener('touchmove', preventScroll, { passive: false });
            }

            // --- Fungsi untuk menyembunyikan modal dengan animasi ---
            function hideModal(modal) {
                modal.classList.remove('active');
                document.body.classList.remove('modal-open'); // Restore body scroll
                
                // Remove touch prevention
                document.body.removeEventListener('touchmove', preventScroll, { passive: false });
            }

            // Prevent scroll function for mobile
            function preventScroll(e) {
                e.preventDefault();
            }

            // --- Event Listeners ---

            // Buka Modal untuk Tambah
            addArticleBtn.addEventListener('click', function () {
                modalTitle.textContent = 'Tambah Artikel';
                articleForm.reset();
                articleIdInput.value = '';
                methodInput.value = 'POST';
                document.querySelector('trix-editor').innerHTML = '';
                imagePreview.innerHTML = '<span class="material-icons-outlined text-gray-400">image</span>';
                showModal(articleModal);
            });

            // Tutup Modal Artikel
            closeModalBtn.addEventListener('click', () => hideModal(articleModal));
            cancelBtn.addEventListener('click', () => hideModal(articleModal));
            
            // Tutup Modal Hapus
            closeDeleteModalBtn.addEventListener('click', () => hideModal(deleteModal));
            cancelDeleteBtn.addEventListener('click', () => hideModal(deleteModal));
            
            // Tutup modal dengan klik di luar area modal
            [articleModal, deleteModal].forEach(modal => {
                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        hideModal(modal);
                    }
                });
            });

            // Preview Gambar
            imageInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    reader.readAsDataURL(file);
                }
            });

            // Edit Artikel
            document.querySelectorAll('.edit-article-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idToEdit = this.getAttribute('data-id');
                    fetch(`/admin/settings/articles/${idToEdit}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const article = data.article;
                                modalTitle.textContent = 'Edit Artikel';
                                articleIdInput.value = article.id;
                                titleInput.value = article.title;
                                excerptInput.value = article.excerpt || '';
                                isFeaturedInput.checked = article.is_featured;
                                orderInput.value = article.order;
                                document.querySelector('trix-editor').innerHTML = article.content;
                                
                                if (article.image) {
                                    imagePreview.innerHTML = `<img src="/storage/${article.image}" alt="${article.title}">`;
                                } else {
                                    imagePreview.innerHTML = '<span class="material-icons-outlined text-gray-400">image</span>';
                                }
                                showModal(articleModal);
                            } else {
                                showMinimalPopup('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            showMinimalPopup('Error', 'Gagal memuat data artikel', 'error');
                        });
                });
            });

            // Hapus Artikel
            document.querySelectorAll('.delete-article-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idToDelete = this.getAttribute('data-id');
                    deleteIdInput.value = idToDelete;
                    deleteForm.action = `/admin/settings/articles/${idToDelete}`;
                    showModal(deleteModal);
                });
            });

            // Submit Form Artikel
            articleForm.addEventListener('submit', function (e) {
                e.preventDefault();
                
                // Sinkronisasi konten Trix Editor ke input hidden
                contentInput.value = document.querySelector('trix-editor').innerHTML;

                const isEdit = articleIdInput.value !== '';
                methodInput.value = isEdit ? 'PUT' : 'POST';
                
                const formData = new FormData(this);
                const url = isEdit ? `/admin/settings/articles/${articleIdInput.value}` : '/admin/settings/articles';

                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => { throw err; });
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMinimalPopup('Berhasil', data.message, 'success');
                        hideModal(articleModal);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showMinimalPopup('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Submit Error:', error);
                    if (error.errors) {
                        const firstError = Object.values(error.errors)[0][0];
                        showMinimalPopup('Validasi Gagal', firstError, 'warning');
                    } else {
                        showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
                    }
                });
            });

            // Submit Form Hapus
            deleteForm.addEventListener('submit', function (e) {
                e.preventDefault();
                
                const idToDelete = deleteIdInput.value;
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menghapus...';
                submitBtn.disabled = true;
                
                fetch(`/admin/settings/articles/${idToDelete}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                        'Accept': 'application/json' 
                    }
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => { throw err; });
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMinimalPopup('Berhasil', data.message, 'success');
                        hideModal(deleteModal);
                        document.querySelector(`.article-card[data-id="${idToDelete}"]`).remove();
                    } else {
                        showMinimalPopup('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Delete Error:', error);
                    showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
            });

            // Tutup Popup
            document.querySelector('.minimal-popup-close')?.addEventListener('click', () => {
                document.getElementById('minimalPopup')?.classList.remove('show');
            });

            // Handle escape key to close modals
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (articleModal.classList.contains('active')) {
                        hideModal(articleModal);
                    }
                    if (deleteModal.classList.contains('active')) {
                        hideModal(deleteModal);
                    }
                }
            });
        });
    </script>
</body>

</html>