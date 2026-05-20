<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Layanan - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
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
        
        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .order-table {
            transition: all 0.2s ease;
        }
        
        .order-table tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
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
        
        .modal {
            transition: opacity 0.25s ease;
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-teknologi {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }
        
        .status-desain {
            background-color: rgba(168, 85, 247, 0.15);
            color: #6b21a8;
        }
        
        .status-marketing {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .status-konsultasi {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        .hamburger-line {
            transition: all 0.3s ease-in-out;
        }
        
        .hamburger-active .line1 {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburger-active .line2 {
            opacity: 0;
        }
        
        .hamburger-active .line3 {
            transform: rotate(-45deg) translate(7px, -6px);
        }
        
        .nav-item {
            position: relative;
            overflow: hidden;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background-color: #3b82f6;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        
        @media (min-width: 768px) {
            .nav-item::before {
                right: auto;
                left: 0;
                transform: translateX(-100%);
            }
        }
        
        .nav-item:hover::before,
        .nav-item.active::before {
            transform: translateX(0);
        }
        
        .sidebar-fixed {
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
        }
        
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }
        
        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px;
            }
        }
        
        .sidebar-fixed::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-fixed::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .sidebar-fixed::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .sidebar-fixed::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        @media (max-width: 639px) {
            .desktop-table {
                display: none;
            }
            
            .mobile-cards {
                display: block;
            }
            
            .desktop-pagination {
                display: none !important;
            }
        }
        
        @media (min-width: 640px) {
            .desktop-table {
                display: block;
            }
            
            .mobile-cards {
                display: none;
            }
            
            .mobile-pagination {
                display: none !important;
            }
        }
        
        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .page-btn {
            transition: all 0.2s ease;
        }
        
        .page-btn:hover:not(:disabled) {
            transform: scale(1.1);
        }
        
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .desktop-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
        }
        
        .desktop-page-btn {
            min-width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .desktop-page-btn.active {
            background-color: #3b82f6;
            color: white;
        }
        
        .desktop-page-btn:not(.active) {
            background-color: #f1f5f9;
            color: #64748b;
        }
        
        .desktop-page-btn:not(.active):hover {
            background-color: #e2e8f0;
        }
        
        .desktop-nav-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .desktop-nav-btn:hover:not(:disabled) {
            background-color: #e2e8f0;
        }
        
        .desktop-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
        
        .scrollable-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
        }
        
        .scrollable-table-container {
            scrollbar-width: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .scrollable-table-container::-webkit-scrollbar {
            height: 12px;
            width: 12px;
        }
        
        .scrollable-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }
        
        .scrollable-table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
            border: 2px solid #f1f5f9;
        }
        
        .scrollable-table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
        
        .table-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .truncate-text {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    @include('general_manajer/templet/header')
    
    <!-- Main Content Container -->
    <div class="main-content">
        <main class="flex-1 flex flex-col bg-background-light">
            <div class="flex-1 p-3 sm:p-8">

                <!-- Flash Message -->
                @if(session('success'))
                    <div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform transition-transform duration-300 flex items-center">
                        <span class="mr-2">{{ session('success') }}</span>
                        <button onclick="this.parentElement.style.display='none'" class="text-white hover:text-gray-200">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                @endif

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Data Layanan</h2>
                
                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <!-- PERUBAHAN: Search input dibungkus dalam form GET -->
                    <form action="/layanan" method="GET" class="relative w-full md:w-1/3">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input 
                            name="search" 
                            value="{{ $search ?? '' }}" 
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" 
                            placeholder="Search..." 
                            type="text" 
                        />
                    </form>  
                </div>
                
                <!-- Data Table Panel -->
<!-- Data Table Panel -->
<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">
            <span class="material-icons-outlined text-primary">miscellaneous_services</span>
            Daftar Layanan
        </h3>
        <div class="flex items-center gap-2">
            <span class="text-sm text-text-muted-light">
                Total: <span class="font-semibold text-text-light">{{ $layanan->count() }}</span> layanan
            </span>
        </div>
    </div>

    <div class="panel-body">
        <!-- SCROLLABLE TABLE - TANPA INDICATOR -->
        <div class="desktop-table">
            <div class="scrollable-table-container table-shadow" id="scrollableTable">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="min-width: 60px;">No</th>
                            <th style="min-width: 100px;">Nama Layanan</th>
                            <th style="min-width: 120px;">Harga</th>
                            <th style="min-width: 100px;">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layanan as $item)
                            <tr>
                                <td style="min-width: 60px;">{{ $loop->iteration }}</td>
                                <td style="min-width: 100px;">{{ $item->nama_layanan }}</td>
                                <td style="min-width: 120px;">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td style="min-width: 100px;" class="truncate-text" title="{{ $item->deskripsi }}">
                                    {{ \Illuminate\Support\Str::limit($item->deskripsi, 50) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">
                                    Data layanan belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-cards space-y-4">
            @forelse($layanan as $item)
                <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-base">{{ $item->nama }}</h4>
                            <p class="text-sm text-text-muted-light">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700"
                                onclick="openEditModal('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->harga }}', '{{ $item->durasi }}', '{{ $item->deskripsi }}', '{{ $item->kategori }}')"
                                title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </button>

                            <form action="/layanan/{{ $item->id }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700" title="Hapus">
                                    <span class="material-icons-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-text-muted-light">Durasi</p>
                            <p class="font-medium">{{ $item->durasi }}</p>
                        </div>
                        <div>
                            <p class="text-text-muted-light">Kategori</p>
                            <p>
                                <span class="status-badge status-{{ strtolower($item->kategori) }}">
                                    {{ $item->kategori }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <p class="text-text-muted-light">Deskripsi</p>
                        <p class="font-medium">{{ \Illuminate\Support\Str::limit($item->deskripsi, 80) }}</p>

                        @if(strlen($item->deskripsi) > 80)
                            <button class="text-primary text-sm mt-1"
                                onclick="openDetailModal('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->harga }}', '{{ $item->durasi }}', '{{ $item->deskripsi }}', '{{ $item->kategori }}')">
                                Lihat selengkapnya
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-gray-500">
                    Data layanan belum tersedia.
                </div>
            @endforelse
        </div>

        {{-- Pagination dihapus karena controller pakai ->get() --}}
    </div>
</div>

            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Modal Tambah Layanan -->
    <div id="tambahModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Layanan</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form action="/layanan" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                        <input type="text" name="nama" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-700">Rp</span>
                            <input type="number" name="harga" class="w-full pl-10 pr-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                        <input type="text" name="durasi" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Contoh: 30 Hari" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required></textarea>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="kategori" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Teknologi">Teknologi</option>
                            <option value="Desain">Desain</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Konsultasi">Konsultasi</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Layanan -->
    <div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Layanan</h3>
                    <button class="close-modal text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                        <input type="text" name="nama" id="editNama" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-700">Rp</span>
                            <input type="number" name="harga" id="editHarga" class="w-full pl-10 pr-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                        <input type="text" name="durasi" id="editDurasi" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" id="editDeskripsi" rows="3" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required></textarea>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="kategori" id="editKategori" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" required>
                            <option value="Teknologi">Teknologi</option>
                            <option value="Desain">Desain</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Konsultasi">Konsultasi</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Layanan -->
    <div id="detailModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Detail Layanan</h3>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">ID Layanan</h4>
                            <p class="text-base font-medium" id="detailId"></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Nama Layanan</h4>
                            <p class="text-base font-medium" id="detailNama"></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Harga</h4>
                            <p class="text-base font-medium" id="detailHarga"></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Durasi</h4>
                            <p class="text-base font-medium" id="detailDurasi"></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Kategori</h4>
                            <p id="detailKategori"></p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Deskripsi</h4>
                        <p class="text-base" id="detailDeskripsi"></p>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="close-modal px-4 py-2 btn-secondary rounded-lg">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal elements
            const tambahModal = document.getElementById('tambahModal');
            const editModal = document.getElementById('editModal');
            const detailModal = document.getElementById('detailModal');
            
            // Buttons
            const tambahLayananBtn = document.getElementById('tambahLayananBtn');
            const closeModals = document.querySelectorAll('.close-modal');
            
            // Show tambah modal
            tambahLayananBtn.addEventListener('click', function() {
                tambahModal.classList.remove('hidden');
            });
            
            // Close modals
            closeModals.forEach(btn => {
                btn.addEventListener('click', function() {
                    tambahModal.classList.add('hidden');
                    editModal.classList.add('hidden');
                    detailModal.classList.add('hidden');
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === tambahModal) {
                    tambahModal.classList.add('hidden');
                }
                if (event.target === editModal) {
                    editModal.classList.add('hidden');
                }
                if (event.target === detailModal) {
                    detailModal.classList.add('hidden');
                }
            });
        });

        // Open detail modal with data
        function openDetailModal(id, nama, harga, durasi, deskripsi, kategori) {
            document.getElementById('detailId').textContent = '#' + id;
            document.getElementById('detailNama').textContent = nama;
            document.getElementById('detailHarga').textContent = 'Rp ' + Number(harga).toLocaleString('id-ID');
            document.getElementById('detailDurasi').textContent = durasi;
            document.getElementById('detailDeskripsi').textContent = deskripsi;
            
            // Set kategori badge
            const kategoriElement = document.getElementById('detailKategori');
            kategoriElement.innerHTML = `<span class="status-badge status-${kategori.toLowerCase()}">${kategori}</span>`;
            
            document.getElementById('detailModal').classList.remove('hidden');
        }

        // Open edit modal with data
        function openEditModal(id, nama, harga, durasi, deskripsi, kategori) {
            document.getElementById('editNama').value = nama;
            document.getElementById('editHarga').value = harga;
            document.getElementById('editDurasi').value = durasi;
            document.getElementById('editDeskripsi').value = deskripsi;
            document.getElementById('editKategori').value = kategori;
            
            // Set action URL untuk form edit
            const editForm = document.getElementById('editForm');
            editForm.action = `/layanan/${id}`;
            
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</body>
</html>