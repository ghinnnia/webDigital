<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Daftar Karyawan</title>
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

        /* Card hover effects */
        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Table styles */
        .order-table {
            transition: all 0.2s ease;
        }

        .order-table tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
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

        /* Modal styles */
        .modal {
            transition: opacity 0.25s ease;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        /* Status Badge Styles */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-aktif {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-resign {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .status-phk {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        .status-tetap {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-kontrak {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-freelance {
            background-color: rgba(168, 85, 247, 0.15);
            color: #7c3aed;
        }

        /* Role Badge Styles */
        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .role-karyawan {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .role-manager {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .role-finance {
            background-color: rgba(139, 92, 246, 0.15);
            color: #6d28d9;
        }

        .role-admin {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        /* Custom styles untuk transisi */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        /* Animasi hamburger */
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

        /* Gaya untuk indikator aktif/hover */
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

        /* Table mobile adjustments */
        @media (max-width: 639px) {
            .desktop-table {
                display: none;
            }

            .mobile-cards {
                display: block;
            }

            /* Hide desktop pagination on mobile */
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

            /* Hide mobile pagination on desktop */
            .mobile-pagination {
                display: none !important;
            }
        }

        /* Form input styles */
        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Pagination styles */
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
            min-width: 1400px;
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

        /* Empty state styles */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            background-color: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .empty-state-icon .material-icons-outlined {
            font-size: 40px;
            color: #94a3b8;
        }

        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-state-description {
            color: #64748b;
            max-width: 400px;
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Menggunakan template header -->
        @include('manager_divisi.templet.sider')
        <div class="flex-1 flex flex-col main-content">
            <div class="flex-1 p-3 sm:p-8">
                <!-- HEADER DENGAN NAMA DIVISI -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">Data Karyawan</h2>
                        <p class="text-gray-600">
                            @if(isset($namaDivisiManager) && $namaDivisiManager)
                                Divisi: <span class="font-semibold text-primary">{{ $namaDivisiManager }}</span>
                            @else
                                <span class="text-yellow-600">Anda belum memiliki divisi yang ditetapkan</span>
                            @endif
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <span class="material-icons-outlined text-sm mr-1">badge</span>
                            Manager Divisi
                        </span>
                    </div>
                </div>

                <!-- PERUBAHAN: Notifikasi sukses -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <form method="GET" action="{{ route('manager_divisi.daftar_karyawan') }}" class="relative w-full md:w-1/3">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input name="search" value="{{ request('search') }}" 
                               class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input"
                               placeholder="Cari nama, email, atau role..." 
                               type="text" />
                    </form>
                </div>

                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">people</span>
                            Data Karyawan Divisi {{ $namaDivisiManager ?? 'Anda' }}
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span class="font-semibold text-text-light">{{ count($karyawan) }}</span> karyawan</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if(count($karyawan) > 0)
                            <!-- INFO DIVISI -->
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <span class="material-icons-outlined text-blue-500 mr-2">info</span>
                                    <p class="text-sm text-blue-700">
                                        Menampilkan karyawan dari divisi <strong>{{ $namaDivisiManager ?? 'Anda' }}</strong>. 
                                        Hanya karyawan dengan divisi yang sama yang akan ditampilkan.
                                    </p>
                                </div>
                            </div>

                            <!-- SCROLLABLE TABLE -->
                            <div class="desktop-table">
                                <div class="scrollable-table-container scroll-indicator table-shadow">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 60px;">No</th>
                                                <th style="min-width: 200px;">Nama</th>
                                                <th style="min-width: 150px;">Role</th>
                                                <th style="min-width: 200px;">Email</th>
                                                <th style="min-width: 150px;">Divisi</th>
                                                <th style="min-width: 350px;">Alamat</th>
                                                <th style="min-width: 150px;">Kontak</th>
                                                <th style="min-width: 120px;">Status Kerja</th>
                                                <th style="min-width: 120px;">Status Karyawan</th>
                                                <th style="min-width: 100px;">Foto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($karyawan as $index => $k)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $k->nama }}</td>
                                                    <td>
                                                        <span class="role-badge 
                                                            {{ $k->role == 'karyawan' ? 'role-karyawan' : 
                                                               ($k->role == 'manager_divisi' ? 'role-manager' : 
                                                               ($k->role == 'finance' ? 'role-finance' : 'role-admin')) }}">
                                                            {{ $k->role }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $k->email }}</td>
                                                    <td>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ isset($k->divisi_name) ? $k->divisi_name : (isset($k->user) && isset($k->user->divisi) ? $k->user->divisi : ($k->divisi ?? '-')) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $k->alamat }}</td>
                                                    <td>{{ $k->kontak }}</td>
                                                    <td>
                                                        <span class="status-badge 
                                                            {{ $k->status_kerja == 'aktif' ? 'status-aktif' : 
                                                               ($k->status_kerja == 'resign' ? 'status-resign' : 'status-phk') }}">
                                                            {{ $k->status_kerja }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge 
                                                            {{ $k->status_karyawan == 'tetap' ? 'status-tetap' : 
                                                               ($k->status_karyawan == 'kontrak' ? 'status-kontrak' : 'status-freelance') }}">
                                                            {{ $k->status_karyawan }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (!empty($k->foto_url))
                                                            <img src="{{ $k->foto_url }}"
                                                                 alt="Foto" 
                                                                 class="w-10 h-10 rounded-full object-cover"
                                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}';">
                                                        @elseif (!empty($k->foto))
                                                            <img src="{{ str_starts_with($k->foto, 'http') ? $k->foto : asset('storage/' . ltrim($k->foto, '/')) }}"
                                                                 alt="Foto" 
                                                                 class="w-10 h-10 rounded-full object-cover"
                                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}';">
                                                        @else
                                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}"
                                                                 alt="Foto" 
                                                                 class="w-10 h-10 rounded-full object-cover">
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="mobile-cards space-y-4">
                                @foreach ($karyawan as $index => $k)
                                    <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="h-12 w-12 rounded-full overflow-hidden">
                                                    @if (!empty($k->foto_url))
                                                        <img src="{{ $k->foto_url }}"
                                                             alt="Foto"
                                                             class="w-full h-full object-cover"
                                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}';">
                                                    @elseif (!empty($k->foto))
                                                        <img src="{{ str_starts_with($k->foto, 'http') ? $k->foto : asset('storage/' . ltrim($k->foto, '/')) }}"
                                                             alt="Foto"
                                                             class="w-full h-full object-cover"
                                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}';">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}"
                                                             alt="Foto"
                                                             class="w-full h-full object-cover">
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-base">{{ $k->nama }}</h4>
                                                    <p class="text-sm text-text-muted-light">{{ $k->email }}</p>
                                                    <span class="role-badge 
                                                        {{ $k->role == 'karyawan' ? 'role-karyawan' : 
                                                           ($k->role == 'manager_divisi' ? 'role-manager' : 
                                                           ($k->role == 'finance' ? 'role-finance' : 'role-admin')) }}">
                                                        {{ $k->role }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div>
                                                <p class="text-text-muted-light">No</p>
                                                <p class="font-medium">{{ $index + 1 }}</p>
                                            </div>
                                            <div>
                                                <p class="text-text-muted-light">Divisi</p>
                                                <p class="font-medium">{{ isset($k->divisi_name) ? $k->divisi_name : (isset($k->user) && isset($k->user->divisi) ? $k->user->divisi : ($k->divisi ?? '-')) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-text-muted-light">Status Kerja</p>
                                                <p>
                                                    <span class="status-badge 
                                                        {{ $k->status_kerja == 'aktif' ? 'status-aktif' : 
                                                           ($k->status_kerja == 'resign' ? 'status-resign' : 'status-phk') }}">
                                                        {{ $k->status_kerja }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-text-muted-light">Status Karyawan</p>
                                                <p>
                                                    <span class="status-badge 
                                                        {{ $k->status_karyawan == 'tetap' ? 'status-tetap' : 
                                                           ($k->status_karyawan == 'kontrak' ? 'status-kontrak' : 'status-freelance') }}">
                                                        {{ $k->status_karyawan }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-span-2">
                                                <p class="text-text-muted-light">Alamat</p>
                                                <p class="font-medium">{{ $k->alamat }}</p>
                                            </div>
                                            <div class="col-span-2">
                                                <p class="text-text-muted-light">Kontak</p>
                                                <p class="font-medium">{{ $k->kontak }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <span class="material-icons-outlined">people</span>
                                </div>
                                <h3 class="empty-state-title">
                                    @if(isset($namaDivisiManager) && $namaDivisiManager)
                                        Tidak ada karyawan di divisi {{ $namaDivisiManager }}
                                    @else
                                        Belum ada data karyawan
                                    @endif
                                </h3>
                                <p class="empty-state-description">
                                    @if(isset($namaDivisiManager) && $namaDivisiManager)
                                        Saat ini belum ada karyawan yang terdaftar di divisi {{ $namaDivisiManager }}.
                                    @else
                                        Anda belum memiliki divisi yang ditetapkan. Hubungi administrator untuk menetapkan divisi Anda.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digital kolaborasi.id
            </footer>
        </div>
    </div>

    <!-- PERUBAHAN: Hapus JavaScript yang tidak diperlukan -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus search input
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                setTimeout(() => {
                    searchInput.focus();
                }, 300);
            }
        });
    </script>
</body>
</html>

