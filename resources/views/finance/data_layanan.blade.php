<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Layanan - Finance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
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
                        display: ["Inter", "sans-serif"],
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
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 1,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }

        body {
            font-family: 'Inter', sans-serif;
        }
        
        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
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
        
        /* SCROLLABLE TABLE */
        .scrollable-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
        }
        
        .data-table {
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        
        .data-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .data-table tbody tr:hover {
            background: #f3f4f6;
        }
        
        /* Minimalist Popup Styles */
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
        
        .minimal-popup-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .minimal-popup-close:hover {
            background-color: #f1f5f9;
            color: #64748b;
        }
        
        /* Mobile cards */
        .mobile-cards {
            display: none;
        }
        
        @media (max-width: 768px) {
            .desktop-table {
                display: none;
            }
            .mobile-cards {
                display: block;
            }
        }
    </style>
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('finance.templet.sider')
        
        <div class="flex-1 flex flex-col main-content">
            <div class="flex-1 p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Data Layanan</h2>
                
                <!-- Search Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari nama layanan atau deskripsi..." type="text" />
                    </div>
                </div>
                
                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">miscellaneous_services</span>
                            Data Layanan (Finance View)
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount" class="font-semibold text-text-light">{{ count($layanans) }}</span> layanan</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Desktop Table -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container scroll-indicator">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 200px;">Nama Layanan</th>
                                            <th style="min-width: 250px;">Deskripsi</th>
                                            <th style="min-width: 150px;">Harga</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        @forelse ($layanans as $index => $layanan)
                                        <tr class="layanan-row" data-id="{{ $layanan->id }}" data-nama="{{ $layanan->nama_layanan }}" data-deskripsi="{{ $layanan->deskripsi }}" data-harga="{{ $layanan->harga }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $layanan->nama_layanan }}</td>
                                            <td>{{ $layanan->deskripsi ?? 'Tidak ada deskripsi' }}</td>
                                            <td>Rp. {{ number_format($layanan->harga, 0, ',', '.') }}</td>
                                            <td style="text-align: center;">
                                                <button class="edit-harga-btn px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                                                        data-id="{{ $layanan->id }}"
                                                        data-nama="{{ $layanan->nama_layanan }}"
                                                        data-deskripsi="{{ $layanan->deskripsi }}"
                                                        data-harga="{{ $layanan->harga }}">
                                                    Edit Harga
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-gray-500">Belum ada data layanan.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="mobile-cards space-y-4">
                            @forelse ($layanans as $index => $layanan)
                            <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm layanan-card" 
                                 data-id="{{ $layanan->id }}" 
                                 data-nama="{{ $layanan->nama_layanan }}" 
                                 data-deskripsi="{{ $layanan->deskripsi }}" 
                                 data-harga="{{ $layanan->harga }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="material-icons-outlined text-primary">miscellaneous_services</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base">{{ $layanan->nama_layanan }}</h4>
                                            <p class="text-sm text-text-muted-light">Rp. {{ number_format($layanan->harga, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <button class="edit-harga-btn px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 transition-colors"
                                            data-id="{{ $layanan->id }}"
                                            data-nama="{{ $layanan->nama_layanan }}"
                                            data-deskripsi="{{ $layanan->deskripsi }}"
                                            data-harga="{{ $layanan->harga }}">
                                        Edit Harga
                                    </button>
                                </div>
                                <div class="text-sm">
                                    <p class="text-text-muted-light mb-1">Deskripsi</p>
                                    <p class="font-medium">{{ $layanan->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="bg-white rounded-lg border border-border-light p-8 text-center text-gray-500">
                                Belum ada data layanan.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </div>
    </div>

    <!-- Modal Edit Harga -->
    <div id="editHargaModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Harga Layanan</h3>
                    <button id="closeModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editHargaForm" class="space-y-4">
                    <input type="hidden" id="editId" name="id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                        <input type="text" id="editNamaLayanan" 
                               class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 cursor-not-allowed" 
                               readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="editDeskripsi" 
                                  class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 cursor-not-allowed" 
                                  rows="2" readonly></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga <span class="text-red-500">*</span></label>
                        <input type="text" id="editHarga" name="harga" 
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                               placeholder="Masukkan harga" required>
                        <p class="text-xs text-gray-500 mt-1">Hanya dapat mengedit harga</p>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">Simpan Harga</button>
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
        // === INITIALIZATION ===
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Finance Layanan Page Loaded');
            
            // Initialize sidebar jika elemen ada
            initSidebar();
            
            // Initialize modal dan fungsi lainnya
            initializePage();
        });

        // === SIDEBAR INITIALIZATION ===
        function initSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Cek dulu apakah elemen ada
            if (!sidebar) {
                console.log('Sidebar element not found - skipping sidebar initialization');
                return;
            }
            
            console.log('Initializing sidebar...');
            
            // Atur posisi awal sidebar
            sidebar.style.transform = 'translateX(0)';
            
            if (overlay) {
                overlay.style.display = 'block';
            }
            
            // Event listeners untuk toggle sidebar (jika ada hamburger menu)
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');
            
            if (hamburgerBtn) {
                hamburgerBtn.addEventListener('click', toggleSidebar);
            }
            
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', toggleSidebar);
            }
            
            // Close sidebar ketika klik overlay
            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }
            
            function toggleSidebar() {
                const isOpen = sidebar.style.transform === 'translateX(0px)' || 
                              sidebar.style.transform === 'translateX(0)';
                
                if (isOpen) {
                    sidebar.style.transform = 'translateX(-100%)';
                    if (overlay) overlay.style.display = 'none';
                } else {
                    sidebar.style.transform = 'translateX(0)';
                    if (overlay) overlay.style.display = 'block';
                }
            }
        }

        // === PAGE INITIALIZATION ===
        function initializePage() {
            console.log('Initializing finance layanan page...');
            
            // Modal elements
            const editHargaModal = document.getElementById('editHargaModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const editHargaForm = document.getElementById('editHargaForm');
            
            // Check if modal elements exist
            if (!editHargaModal || !closeModalBtn || !cancelBtn || !editHargaForm) {
                console.log('Modal elements not found - finance may only have view permission');
                return;
            }
            
            // Show popup function
            function showPopup(title, message, type = 'success') {
                const popup = document.getElementById('minimalPopup');
                if (!popup) {
                    console.log('Popup element not found');
                    return;
                }
                
                const popupTitle = popup.querySelector('.minimal-popup-title');
                const popupMessage = popup.querySelector('.minimal-popup-message');
                const popupIcon = popup.querySelector('.minimal-popup-icon span');
                
                if (popupTitle) popupTitle.textContent = title;
                if (popupMessage) popupMessage.textContent = message;
                popup.className = 'minimal-popup show ' + type;
                
                if (popupIcon) {
                    if (type === 'success') {
                        popupIcon.textContent = 'check';
                    } else if (type === 'error') {
                        popupIcon.textContent = 'error';
                    }
                }
                
                setTimeout(() => {
                    popup.classList.remove('show');
                }, 3000);
            }
            
            // Close popup when clicking close button
            const popupCloseBtn = document.querySelector('.minimal-popup-close');
            if (popupCloseBtn) {
                popupCloseBtn.addEventListener('click', function() {
                    const popup = document.getElementById('minimalPopup');
                    if (popup) popup.classList.remove('show');
                });
            }
            
            // Format input harga
            function formatHargaInput(input) {
                if (!input) return;
                
                let value = input.value.replace(/\./g, '');
                
                // Hanya angka
                if (!/^\d*$/.test(value)) {
                    value = value.replace(/\D/g, '');
                }
                
                if (value) {
                    input.value = parseInt(value).toLocaleString('id-ID');
                }
            }
            
            // Format harga on input
            const hargaInput = document.getElementById('editHarga');
            if (hargaInput) {
                hargaInput.addEventListener('input', function(e) {
                    formatHargaInput(this);
                });
            }
            
            // Handle edit buttons
            document.querySelectorAll('.edit-harga-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const deskripsi = this.getAttribute('data-deskripsi');
                    const harga = this.getAttribute('data-harga');
                    
                    // Set modal values
                    document.getElementById('editId').value = id;
                    document.getElementById('editNamaLayanan').value = nama;
                    document.getElementById('editDeskripsi').value = deskripsi;
                    
                    // Format harga untuk input
                    const formattedHarga = parseInt(harga).toLocaleString('id-ID');
                    document.getElementById('editHarga').value = formattedHarga;
                    
                    // Show modal
                    editHargaModal.classList.remove('hidden');
                });
            });
            
            // Close modal events
            closeModalBtn.addEventListener('click', function() {
                editHargaModal.classList.add('hidden');
                editHargaForm.reset();
            });
            
            cancelBtn.addEventListener('click', function() {
                editHargaModal.classList.add('hidden');
                editHargaForm.reset();
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === editHargaModal) {
                    editHargaModal.classList.add('hidden');
                    editHargaForm.reset();
                }
            });
            
            // Handle form submission
            editHargaForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const id = document.getElementById('editId').value;
                const hargaInput = document.getElementById('editHarga').value;
                const nama = document.getElementById('editNamaLayanan').value;
                
                // Convert harga from formatted string to number
                const harga = parseInt(hargaInput.replace(/\./g, '')) || 0;
                
                if (harga <= 0) {
                    showPopup('Error', 'Harga harus lebih dari 0', 'error');
                    return;
                }
                
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span> Menyimpan...';
                
                try {
                    const response = await fetch(`/finance/layanan/${id}/update-harga`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            harga: harga
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Update UI
                        const row = document.querySelector(`.layanan-row[data-id="${id}"]`);
                        if (row) {
                            row.setAttribute('data-harga', harga);
                            const hargaCell = row.querySelector('td:nth-child(4)');
                            if (hargaCell) {
                                hargaCell.textContent = `Rp. ${harga.toLocaleString('id-ID')}`;
                            }
                            
                            // Update button data attribute
                            const editBtn = row.querySelector('.edit-harga-btn');
                            if (editBtn) {
                                editBtn.setAttribute('data-harga', harga);
                            }
                        }
                        
                        // Update mobile cards
                        const card = document.querySelector(`.layanan-card[data-id="${id}"]`);
                        if (card) {
                            card.setAttribute('data-harga', harga);
                            const priceElement = card.querySelector('.text-text-muted-light');
                            if (priceElement) {
                                priceElement.textContent = `Rp. ${harga.toLocaleString('id-ID')}`;
                            }
                            
                            const editBtnMobile = card.querySelector('.edit-harga-btn');
                            if (editBtnMobile) {
                                editBtnMobile.setAttribute('data-harga', harga);
                            }
                        }
                        
                        // Close modal and show success
                        editHargaModal.classList.add('hidden');
                        editHargaForm.reset();
                        showPopup('Berhasil', 'Harga layanan berhasil diperbarui', 'success');
                        
                        // Notify admin side (optional)
                        console.log('Harga updated, admin should see the change');
                    } else {
                        showPopup('Gagal', result.message || 'Gagal memperbarui harga', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showPopup('Error', 'Terjadi kesalahan saat menyimpan', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.toLowerCase().trim();
                        const rows = document.querySelectorAll('.layanan-row');
                        const cards = document.querySelectorAll('.layanan-card');
                        let visibleCount = 0;
                        
                        rows.forEach(row => {
                            const nama = row.getAttribute('data-nama').toLowerCase();
                            const deskripsi = row.getAttribute('data-deskripsi').toLowerCase();
                            
                            if (searchTerm === '' || nama.includes(searchTerm) || deskripsi.includes(searchTerm)) {
                                row.style.display = '';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        });
                        
                        cards.forEach(card => {
                            const nama = card.getAttribute('data-nama').toLowerCase();
                            const deskripsi = card.getAttribute('data-deskripsi').toLowerCase();
                            
                            if (searchTerm === '' || nama.includes(searchTerm) || deskripsi.includes(searchTerm)) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                        
                        const totalCountElement = document.getElementById('totalCount');
                        if (totalCountElement) {
                            totalCountElement.textContent = visibleCount;
                        }
                    }, 300);
                });
            }
        }

        // === ERROR HANDLING ===
        // Tangkap error global
        window.addEventListener('error', function(e) {
            console.error('Global error caught:', e.message, 'at', e.filename, 'line', e.lineno);
            
            // Suppress sidebar errors jika sidebar tidak ada
            if (e.message.includes('sidebar') || e.message.includes('null')) {
                console.log('Sidebar error suppressed - element may not exist on this page');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>