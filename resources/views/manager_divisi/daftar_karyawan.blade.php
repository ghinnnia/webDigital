<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport" />
    <title>Daftar Karyawan - Manager Divisi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        /* Layout Utama - RESPONSIF */
        .app-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar untuk desktop */
        .sidebar-desktop {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            z-index: 40;
            transition: transform 0.3s ease;
        }

        /* Konten Utama */
        .main-content {
            flex: 1;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        /* Untuk mobile */
        @media (max-width: 768px) {
            .sidebar-desktop {
                transform: translateX(-100%);
                box-shadow: none;
            }
            
            .sidebar-desktop.open {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 50;
            background-color: white;
            border-radius: 0.5rem;
            padding: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }

        /* Overlay untuk mobile */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 35;
            display: none;
        }
        
        .sidebar-overlay.show {
            display: block;
        }

        /* Tabel Responsive */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .data-table {
            min-width: 800px;
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-aktif { background-color: rgba(16, 185, 129, 0.15); color: #065f46; }
        .status-resign { background-color: rgba(245, 158, 11, 0.15); color: #92400e; }
        .status-phk { background-color: rgba(239, 68, 68, 0.15); color: #991b1b; }
        .status-tetap { background-color: rgba(16, 185, 129, 0.15); color: #065f46; }
        .status-kontrak { background-color: rgba(59, 130, 246, 0.15); color: #1e40af; }
        .status-freelance { background-color: rgba(168, 85, 247, 0.15); color: #6d28d9; }

        /* Role Badge */
        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .role-karyawan { background-color: rgba(16, 185, 129, 0.15); color: #065f46; }
        .role-manager { background-color: rgba(59, 130, 246, 0.15); color: #1e40af; }
        .role-finance { background-color: rgba(139, 92, 246, 0.15); color: #6d28d9; }
        .role-admin { background-color: rgba(239, 68, 68, 0.15); color: #991b1b; }

        /* Search Input */
        .search-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        /* Card untuk mobile */
        .employee-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            transition: all 0.2s;
        }
        .employee-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <button id="mobileMenuBtn" class="mobile-menu-btn">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="app-container">
        <aside id="sidebar" class="sidebar-desktop">
            @include('manager_divisi.templet.sider')
        </aside>

        <main class="main-content">
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-1">Data Karyawan</h2>
                        <p class="text-sm text-gray-500">
                            @if(isset($namaDivisiManager) && $namaDivisiManager)
                                Divisi: <span class="font-semibold text-blue-600">{{ $namaDivisiManager }}</span>
                            @else
                                <span class="text-yellow-600">Anda belum memiliki divisi yang ditetapkan</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            <span class="material-icons-outlined text-sm mr-1">badge</span>
                            Manager Divisi
                        </span>
                    </div>
                </div>

                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-green-500 mr-2">check_circle</span>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <div class="mb-6">
                    <form method="GET" action="{{ route('manager_divisi.daftar_karyawan') }}" class="w-full md:w-1/2 lg:w-1/3">
                        <div class="relative">
                            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari nama, email, atau role..." 
                                   class="search-input w-full pl-10 pr-4 py-2.5 rounded-lg bg-white border border-gray-200 focus:border-blue-500">
                        </div>
                    </form>
                </div>

                @if(isset($namaDivisiManager) && $namaDivisiManager && count($karyawan) > 0)
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="material-icons-outlined text-blue-500 text-lg">info</span>
                            <p class="text-sm text-blue-700">
                                Menampilkan <strong>{{ count($karyawan) }}</strong> karyawan dari divisi <strong>{{ $namaDivisiManager }}</strong>
                            </p>
                        </div>
                    </div>
                @endif

                @if(count($karyawan) > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                                <span class="material-icons-outlined text-blue-500">people</span>
                                Daftar Karyawan
                            </h3>
                        </div>
                        
                        <div class="hidden md:block">
                            <div class="table-wrapper">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Role</th>
                                            <th>Email</th>
                                            <th>Divisi</th>
                                            <th>Alamat</th>
                                            <th>Kontak</th>
                                            <th>Status Kerja</th>
                                            <th>Status Karyawan</th>
                                            <th>Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($karyawan as $index => $k)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="font-medium">{{ $k->nama }}</td>
                                                <td>
                                                    <span class="role-badge 
                                                        {{ $k->role == 'karyawan' ? 'role-karyawan' : 
                                                           ($k->role == 'manager_divisi' ? 'role-manager' : 
                                                           ($k->role == 'finance' ? 'role-finance' : 'role-admin')) }}">
                                                        {{ $k->role }}
                                                    </span>
                                                </td>
                                                <td>{{ $k->email }}</td>
                                                <td>{{ $k->divisi->nama ?? $k->divisi ?? $namaDivisiManager ?? '-' }}</td>
                                                <td>{{ Str::limit($k->alamat, 30) ?? '-' }}</td>
                                                <td>{{ $k->kontak ?? '-' }}</td>
                                                <td>
                                                    <span class="status-badge 
                                                        {{ $k->status_kerja == 'aktif' ? 'status-aktif' : 
                                                           ($k->status_kerja == 'resign' ? 'status-resign' : 'status-phk') }}">
                                                        {{ ucfirst($k->status_kerja) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="status-badge 
                                                        {{ $k->status_karyawan == 'tetap' ? 'status-tetap' : 
                                                           ($k->status_karyawan == 'kontrak' ? 'status-kontrak' : 'status-freelance') }}">
                                                        {{ ucfirst($k->status_karyawan) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(!empty($k->foto))
                                                        <img src="{{ asset('storage/' . $k->foto) }}" 
                                                             alt="Foto" 
                                                             class="w-8 h-8 rounded-full object-cover"
                                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}'">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}&background=3b82f6&color=fff" 
                                                             alt="Foto" 
                                                             class="w-8 h-8 rounded-full">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="block md:hidden p-4 space-y-3">
                            @foreach ($karyawan as $index => $k)
                                <div class="employee-card">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            @if(!empty($k->foto))
                                                <img src="{{ asset('storage/' . $k->foto) }}" 
                                                     class="w-12 h-12 rounded-full object-cover"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}'">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}&background=3b82f6&color=fff" 
                                                     class="w-12 h-12 rounded-full">
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-semibold text-gray-800">{{ $k->nama }}</h4>
                                                    <p class="text-xs text-gray-500">{{ $k->email }}</p>
                                                </div>
                                                <span class="role-badge 
                                                    {{ $k->role == 'karyawan' ? 'role-karyawan' : 
                                                       ($k->role == 'manager_divisi' ? 'role-manager' : 
                                                       ($k->role == 'finance' ? 'role-finance' : 'role-admin')) }}">
                                                    {{ $k->role }}
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                                                <div>
                                                    <p class="text-xs text-gray-400">Divisi</p>
                                                    <p class="text-sm text-gray-700">{{ $k->divisi->nama ?? $k->divisi ?? $namaDivisiManager ?? '-' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400">Status Kerja</p>
                                                    <span class="status-badge text-xs 
                                                        {{ $k->status_kerja == 'aktif' ? 'status-aktif' : 
                                                           ($k->status_kerja == 'resign' ? 'status-resign' : 'status-phk') }}">
                                                        {{ ucfirst($k->status_kerja) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400">Status Karyawan</p>
                                                    <span class="status-badge text-xs 
                                                        {{ $k->status_karyawan == 'tetap' ? 'status-tetap' : 
                                                           ($k->status_karyawan == 'kontrak' ? 'status-kontrak' : 'status-freelance') }}">
                                                        {{ ucfirst($k->status_karyawan) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400">Kontak</p>
                                                    <p class="text-sm text-gray-700">{{ $k->kontak ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-2 pt-2 border-t border-gray-100">
                                                <p class="text-xs text-gray-400">Alamat</p>
                                                <p class="text-sm text-gray-600">{{ Str::limit($k->alamat, 60) ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <span class="material-icons-outlined text-gray-400 text-4xl">people</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                @if(isset($namaDivisiManager) && $namaDivisiManager)
                                    Tidak ada karyawan di divisi {{ $namaDivisiManager }}
                                @else
                                    Belum ada data karyawan
                                @endif
                            </h3>
                            <p class="text-gray-500 max-w-md">
                                @if(isset($namaDivisiManager) && $namaDivisiManager)
                                    Saat ini belum ada karyawan yang terdaftar di divisi Anda.
                                @else
                                    Anda belum memiliki divisi yang ditetapkan. Hubungi administrator untuk menetapkan divisi Anda.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <footer class="text-center p-4 bg-white border-t border-gray-200 text-gray-500 text-sm">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleMobileMenu() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }

        function closeMobileMenu() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', toggleMobileMenu);
        }

        if (overlay) {
            overlay.addEventListener('click', closeMobileMenu);
        }

        // Close menu on window resize (if screen becomes desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });

        // Auto-focus search input on desktop
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && window.innerWidth > 768) {
            searchInput.focus();
        }
    </script>
</body>
</html>