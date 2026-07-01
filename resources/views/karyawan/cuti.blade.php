<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Manajemen Cuti - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Global Routes Object -->
    <script>
        window.appRoutes = {
            cuti: {
                index: "{{ route('karyawan.cuti.index') }}",
                data: "{{ route('karyawan.cuti.data') }}",
                store: "{{ route('karyawan.cuti.store') }}",
                stats: "{{ route('karyawan.cuti.stats') }}",
                quotaInfo: "{{ route('karyawan.cuti.quota.info') }}",
                update: (id) => {
                    const base = "{{ route('karyawan.cuti.update', ['cuti' => ':id']) }}";
                    return base.replace(':id', id);
                },
                destroy: (id) => {
                    const base = "{{ route('karyawan.cuti.destroy', ['cuti' => ':id']) }}";
                    return base.replace(':id', id);
                },
                cancelRefund: (id) => {
                    const base = "{{ route('karyawan.cuti.cancel.refund', ['cuti' => ':id']) }}";
                    return base.replace(':id', id);
                },
                edit: (id) => {
                    const base = "{{ route('karyawan.cuti.edit', ['cuti' => ':id']) }}";
                    return base.replace(':id', id);
                },
                create: "{{ route('karyawan.cuti.create') }}",
                calculateDuration: "{{ route('karyawan.cuti.calculate-duration') }}"
            }
        };
    </script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "text-light": "#1e293b",
                        "text-muted-light": "#64748b",
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                    boxShadow: {
                        card: "0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)",
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }

        /* --- ANIMATIONS --- */
        
        /* Fade In Up Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Pulse Animation for Pending Status */
        @keyframes subtlePulse {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        /* Slide In Right with Bounce for Toast */
        @keyframes slideInRightBounce {
            0% { transform: translateX(120%); opacity: 0; }
            60% { transform: translateX(-10%); opacity: 1; }
            80% { transform: translateX(5%); }
            100% { transform: translateX(0); }
        }

        /* Apply Animations */
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0; /* Start hidden */
        }

        .animate-pulse-subtle {
            animation: subtlePulse 2s infinite;
        }

        .animate-slide-in-bounce {
            animation: slideInRightBounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        /* Button Click Feedback */
        .btn-active:active {
            transform: scale(0.95);
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .status-disetujui {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .status-menunggu {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        /* Add subtle pulse to waiting status specifically */
        .status-menunggu.animate-pulse-subtle {
            background-color: rgba(245, 158, 11, 0.25);
        }

        .status-ditolak {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }

        .status-dibatalkan {
            background-color: rgba(107, 114, 128, 0.15);
            color: #374151;
        }

        /* Stats Card */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
        }

        .stat-card.blue::before {
            background: linear-gradient(180deg, #3b82f6, #2563eb);
        }

        .stat-card.red::before {
            background: linear-gradient(180deg, #ef4444, #dc2626);
        }

        .stat-card.green::before {
            background: linear-gradient(180deg, #10b981, #059669);
        }

        .stat-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: rotate(5deg) scale(1.05);
        }

        .stat-icon.blue {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .stat-icon.red {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .stat-icon.green {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        /* Table */
        .data-table {
            width: 100%;
            min-width: 800px;
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
        }

        .data-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .data-table tbody tr:hover {
            background: #f3f4f6;
        }

        .scrollable-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
        }

        /* Mobile Responsive */
        @media (max-width: 639px) {
            .desktop-table {
                display: none !important;
            }

            .mobile-cards {
                display: block !important;
            }

            .desktop-pagination {
                display: none !important;
            }
        }

        @media (min-width: 640px) {
            .desktop-table {
                display: block !important;
            }

            .mobile-cards {
                display: none !important;
            }

            .desktop-pagination {
                display: flex !important;
            }
        }

        /* Popup */
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
            /* Overridden by JS to use specific animation class */
        }

        .minimal-popup.error {
            border-left-color: #ef4444;
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

        .edit-popup {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        /* Add backdrop fade-in */
        .edit-popup.show {
            opacity: 1;
            visibility: visible;
        }

        .edit-popup-content {
            background: white;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .edit-popup.show .edit-popup-content {
            transform: scale(1);
        }

        /* Buttons */
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            cursor: pointer;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            cursor: pointer;
        }

        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Pagination */
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

        /* Modal Detail Cuti */
        .detail-modal-content {
            background: white;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            opacity: 0;
            animation: fadeInUp 0.4s ease-out forwards;
        }

        .detail-label {
            font-weight: 500;
            color: #6b7280;
        }

        .detail-value {
            font-weight: 400;
            color: #111827;
            text-align: right;
        }

        /* Filter Section */
        .filter-container {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .filter-badge:hover {
            background-color: #f8fafc;
        }

        .filter-badge.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="font-display bg-gray-50 text-gray-800">
    <div class="main-content">
        {{-- Ganti dengan header sesuai struktur template Anda --}}
        @include('karyawan.templet.header')
        
        <main class="flex-1 flex flex-col">
            <div class="flex-1 p-3 sm:p-8">
                <!-- Header -->
                <div class="page-header mb-6 animate-fade-in-up">
                    <h1 class="text-2xl font-bold text-gray-800">Cuti</h1>
                   
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card blue animate-fade-in-up" style="animation-delay: 100ms;">
                        <div class="stat-icon blue"><span class="material-icons-outlined text-xl">calendar_today</span>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Cuti Tahunan</div>
                            <div class="stat-value" id="stat-total-cuti">12</div>
                        </div>
                    </div>
                    <div class="stat-card red animate-fade-in-up" style="animation-delay: 200ms;">
                        <div class="stat-icon red"><span class="material-icons-outlined text-xl">event_busy</span></div>
                        <div class="stat-content">
                            <div class="stat-label">Cuti Terpakai</div>
                            <div class="stat-value" id="stat-cuti-terpakai">0</div>
                        </div>
                    </div>
                    <div class="stat-card green animate-fade-in-up" style="animation-delay: 300ms;">
                        <div class="stat-icon green"><span
                                class="material-icons-outlined text-xl">event_available</span></div>
                        <div class="stat-content">
                            <div class="stat-label">Sisa Cuti</div>
                            <div class="stat-value" id="stat-cuti-tersisa">12</div>
                        </div>
                    </div>
                </div>

                <!-- Filter dan Quick Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 animate-fade-in-up" style="animation-delay: 400ms;">
                    <div class="flex flex-wrap gap-2">
                        <button class="filter-badge active btn-active" onclick="setFilter('all')">
                            <span class="material-icons-outlined text-sm">list</span> Semua
                        </button>
                        <button class="filter-badge btn-active" onclick="setFilter('menunggu')">
                            <span class="material-icons-outlined text-sm">schedule</span> Menunggu
                        </button>
                        <button class="filter-badge btn-active" onclick="setFilter('disetujui')">
                            <span class="material-icons-outlined text-sm">check_circle</span> Disetujui
                        </button>
                        <button class="filter-badge btn-active" onclick="setFilter('ditolak')">
                            <span class="material-icons-outlined text-sm">cancel</span> Ditolak
                        </button>
                    </div>
                    <div class="flex gap-2">
                        
                        <button id="tambahCutiBtn"
                            class="btn-primary px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 btn-active">
                            <span class="material-icons-outlined text-sm">add</span>
                            Ajukan Cuti
                        </button>
                    </div>
                </div>

                <!-- Data Cuti Panel -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up" style="animation-delay: 500ms;">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h3 class="font-semibold text-lg text-gray-800 flex items-center gap-2">
                            <span class="material-icons-outlined text-primary">event_note</span>
                            Riwayat Pengajuan Cuti
                        </h3>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-500">Total: <span class="font-bold text-gray-800"
                                    id="cutiCount">0</span> data</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Loading -->
                        <div id="cutiLoading" class="text-center py-10">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            <p class="mt-2 text-gray-500 text-sm">Memuat data cuti...</p>
                        </div>

                        <!-- Desktop Table -->
                        <div id="cutiDesktopTable" class="desktop-table hidden">
                            <div class="scrollable-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 150px;">Tanggal Pengajuan</th>
                                            <th style="min-width: 150px;">Periode</th>
                                            <th style="min-width: 100px;">Durasi</th>
                                            <th style="min-width: 120px;">Jenis</th>
                                            <th style="min-width: 250px;">Keterangan</th>
                                            <th style="min-width: 120px;">Status</th>
                                            <th style="min-width: 150px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cutiTableBody">
                                        <!-- Data injected by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Cards -->
                        <div id="cuti-mobile-cards" class="mobile-cards space-y-4 hidden">
                            <!-- Cards injected by JS -->
                        </div>

                        <!-- No Data State -->
                        <div id="noCutiData" class="text-center py-12 hidden">
                            <span class="material-icons-outlined text-gray-300 text-6xl mb-4">event_busy</span>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengajuan cuti</h3>
                            <p class="text-gray-500">Mulai dengan mengajukan cuti baru</p>
                        </div>

                        <!-- Pagination -->
                        <div id="cutiPaginationContainer" class="desktop-pagination hidden">
                            <button id="cutiPrevPage" class="desktop-nav-btn btn-active"><span
                                    class="material-icons-outlined text-sm">chevron_left</span></button>
                            <div id="cutiPageNumbers" class="flex gap-1"></div>
                            <button id="cutiNextPage" class="desktop-nav-btn btn-active"><span
                                    class="material-icons-outlined text-sm">chevron_right</span></button>
                        </div>
                    </div>
                </div>

                <footer class="text-center p-6 mt-8 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Copyright ©{{ date('Y') }} by digital kolaborasi.id</p>
                </footer>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Cuti -->
    <div id="tambahCutiModal" class="edit-popup">
        <div class="edit-popup-content">
            <div class="edit-popup-header p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="edit-popup-title text-xl font-bold text-gray-800">Ajukan Cuti Baru</h3>
                <button class="text-gray-400 hover:text-gray-600 btn-active" onclick="closeModal('tambahCutiModal')"><span
                        class="material-icons-outlined">close</span></button>
            </div>
            <div class="edit-popup-body p-6">
                <form id="tambahCutiForm" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" required id="inputTanggalMulai"
                                min="{{ date('Y-m-d') }}" class="form-input w-full px-3 py-2 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" required id="inputTanggalSelesai"
                                min="{{ date('Y-m-d') }}" class="form-input w-full px-3 py-2 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Hari)</label>
                            <input type="number" name="durasi" id="inputDurasi" required
                                class="form-input w-full px-3 py-2 rounded-lg bg-gray-50" readonly>
                            <p class="text-xs text-gray-500 mt-1">*Durasi dihitung otomatis (Senin-Jumat)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti <span
                                    class="text-red-500">*</span></label>
                            <select name="jenis_cuti" required class="form-input w-full px-3 py-2 rounded-lg"
                                id="jenisCutiSelect">
                                <option value="">Pilih Jenis Cuti</option>
                                <option value="tahunan">Cuti Tahunan</option>
                                <option value="melahirkan">Cuti Melahirkan</option>
                                <option value="duka">Cuti Duka</option>
                                <option value="izin-khusus">Cuti Izin Khusus</option>
                                <option value="tanpa-gaji">Cuti Tanpa Gaji</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan/Alasan <span
                                class="text-red-500">*</span></label>
                        <textarea name="keterangan" rows="3" required placeholder="Masukkan alasan pengajuan cuti..."
                            class="form-input w-full px-3 py-2 rounded-lg"></textarea>
                    </div>
                    <div id="sisaCutiWarning" class="hidden p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700"><span
                                class="material-icons-outlined text-sm mr-1">warning</span>
                            Sisa cuti tahunan Anda: <span id="sisaCutiCount" class="font-bold">0</span> hari
                        </p>
                    </div>
                </form>
            </div>
            <div class="edit-popup-footer p-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('tambahCutiModal')"
                    class="btn-secondary px-4 py-2 rounded-lg font-medium btn-active">Batal</button>
                <button type="button" onclick="handleAddCuti()" id="submitCutiBtn"
                    class="btn-primary px-4 py-2 rounded-lg font-medium flex items-center gap-2 btn-active">
                    <span class="material-icons-outlined text-sm">send</span> Kirim Pengajuan
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Cuti -->
    <div id="detailCutiModal" class="edit-popup">
        <div class="detail-modal-content">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Detail Pengajuan Cuti</h3>
                <button class="text-gray-400 hover:text-gray-600 btn-active" onclick="closeModal('detailCutiModal')"><span
                        class="material-icons-outlined">close</span></button>
            </div>
            <div class="p-6 space-y-4" id="detailContent">
                <!-- Detail items injected by JS with animation -->
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end">
                <button onclick="closeModal('detailCutiModal')"
                    class="btn-secondary px-4 py-2 rounded-lg font-medium btn-active">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Cuti -->
    <div id="editCutiModal" class="edit-popup">
        <div class="edit-popup-content">
            <div class="edit-popup-header p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="edit-popup-title text-xl font-bold text-gray-800">Edit Pengajuan Cuti</h3>
                <button class="text-gray-400 hover:text-gray-600 btn-active" onclick="closeModal('editCutiModal')"><span
                        class="material-icons-outlined">close</span></button>
            </div>
            <div class="edit-popup-body p-6">
                <form id="editCutiForm" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="cuti_id" id="editCutiId">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" required id="editTanggalMulai"
                                min="{{ date('Y-m-d') }}" class="form-input w-full px-3 py-2 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" required id="editTanggalSelesai"
                                min="{{ date('Y-m-d') }}" class="form-input w-full px-3 py-2 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Hari)</label>
                            <input type="number" name="durasi" id="editDurasi" required
                                class="form-input w-full px-3 py-2 rounded-lg bg-gray-50" readonly>
                            <p class="text-xs text-gray-500 mt-1">*Durasi dihitung otomatis (Senin-Jumat)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti <span
                                    class="text-red-500">*</span></label>
                            <select name="jenis_cuti" required class="form-input w-full px-3 py-2 rounded-lg"
                                id="editJenisCuti">
                                <option value="">Pilih Jenis Cuti</option>
                                <option value="tahunan">Cuti Tahunan</option>
                                <option value="melahirkan">Cuti Melahirkan</option>
                                <option value="duka">Cuti Duka</option>
                                <option value="izin-khusus">Cuti Izin Khusus</option>
                                <option value="tanpa-gaji">Cuti Tanpa Gaji</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan/Alasan <span
                                class="text-red-500">*</span></label>
                        <textarea name="keterangan" rows="3" required placeholder="Masukkan alasan pengajuan cuti..."
                            id="editKeterangan" class="form-input w-full px-3 py-2 rounded-lg"></textarea>
                    </div>
                    <div id="editSisaCutiWarning" class="hidden p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700"><span
                                class="material-icons-outlined text-sm mr-1">warning</span>
                            Sisa cuti tahunan Anda: <span id="editSisaCutiCount" class="font-bold">0</span> hari
                        </p>
                    </div>
                </form>
            </div>
            <div class="edit-popup-footer p-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editCutiModal')"
                    class="btn-secondary px-4 py-2 rounded-lg font-medium btn-active">Batal</button>
                <button type="button" onclick="handleUpdateCuti()" id="updateCutiBtn"
                    class="btn-primary px-4 py-2 rounded-lg font-medium flex items-center gap-2 btn-active">
                    <span class="material-icons-outlined text-sm">save</span> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Quota Info -->
    <div id="quotaInfoModal" class="edit-popup">
        <div class="edit-popup-content">
            <div class="edit-popup-header p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="edit-popup-title text-xl font-bold text-gray-800">Informasi Quota Cuti</h3>
                <button class="text-gray-400 hover:text-gray-600 btn-active" onclick="closeModal('quotaInfoModal')"><span
                        class="material-icons-outlined">close</span></button>
            </div>
            <div class="edit-popup-body p-6">
                <div id="quotaLoading" class="text-center py-10">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <p class="mt-2 text-gray-500 text-sm">Memuat informasi quota...</p>
                </div>
                <div id="quotaContent" class="space-y-4 hidden">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-icons-outlined text-blue-500">calendar_today</span>
                            <h4 class="font-medium text-gray-800">Informasi Quota Tahun <span id="quotaTahun"></span></h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Total Cuti Tahunan</p>
                                <p class="text-lg font-bold text-gray-800" id="quotaTotal">-</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Cuti Terpakai</p>
                                <p class="text-lg font-bold text-red-500" id="quotaTerpakai">-</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Cuti Sakit Terpakai</p>
                                <p class="text-lg font-bold text-orange-500" id="statCutiSakit">0 hari</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Sisa Cuti</p>
                                <p class="text-lg font-bold text-green-500" id="quotaSisa">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-icons-outlined text-blue-500">info</span>
                            <h4 class="font-medium text-blue-800">Statistik</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-3 border border-blue-200">
                                <p class="text-xs text-gray-500 mb-1">Cuti Tahunan Disetujui</p>
                                <p class="text-lg font-bold text-blue-600" id="statCutiTahunan">0 hari</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-blue-200">
                                <p class="text-xs text-gray-500 mb-1">Pengajuan Menunggu</p>
                                <p class="text-lg font-bold text-yellow-600" id="statCutiMenunggu">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="edit-popup-footer p-6 border-t border-gray-100 flex justify-end">
                <button onclick="closeModal('quotaInfoModal')"
                    class="btn-secondary px-4 py-2 rounded-lg font-medium btn-active">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Minimalist Popup -->
    <div id="minimalPopup" class="minimal-popup">
        <div class="minimal-popup-icon"><span class="material-icons-outlined">check</span></div>
        <div class="minimal-popup-content">
            <div class="minimal-popup-title font-medium">Berhasil</div>
            <div class="minimal-popup-message text-sm">Operasi berhasil dilakukan</div>
        </div>
        <button class="minimal-popup-close btn-active" onclick="hidePopup()"><span
                class="material-icons-outlined text-sm">close</span></button>
    </div>

    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';
        
        // Routes Object
        const API_ROUTES = window.appRoutes?.cuti || {
            data: '/karyawan/cuti/data',
            store: '/karyawan/cuti',
            stats: '/karyawan/cuti/stats',
            quotaInfo: '/karyawan/cuti/quota-info',
            update: (id) => `/karyawan/cuti/${id}`,
            destroy: (id) => `/karyawan/cuti/${id}`,
            cancelRefund: (id) => `/karyawan/cuti/${id}/cancel-refund`,
            edit: (id) => `/karyawan/cuti/${id}/edit`,
            create: '/karyawan/cuti/create',
            calculateDuration: '/karyawan/cuti/calculate-duration'
        };

        let cutiCurrentPage = 1;
        let cutiStatusFilter = 'all';
        let totalCutiPages = 1;
        let currentQuotaInfo = null;
        let currentEditingCuti = null;

        // ==================== INITIALIZATION ====================
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🎯 Cuti Page Initialized');
            initializeCuti();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Tombol Tambah Cuti
            document.getElementById('tambahCutiBtn')?.addEventListener('click', () => {
                openModal('tambahCutiModal');
                loadCurrentQuotaForValidation();
            });

            // Tombol Pagination
            document.getElementById('cutiPrevPage')?.addEventListener('click', () => {
                if (cutiCurrentPage > 1) goToPage(cutiCurrentPage - 1);
            });

            document.getElementById('cutiNextPage')?.addEventListener('click', () => {
                if (cutiCurrentPage < totalCutiPages) goToPage(cutiCurrentPage + 1);
            });

            // Auto-calculate duration untuk tambah cuti
            const startInput = document.getElementById('inputTanggalMulai');
            const endInput = document.getElementById('inputTanggalSelesai');

            if (startInput && endInput) {
                startInput.addEventListener('change', () => calculateWorkingDays('tambah'));
                endInput.addEventListener('change', () => calculateWorkingDays('tambah'));
            }

            // Auto-calculate duration untuk edit cuti
            const editStartInput = document.getElementById('editTanggalMulai');
            const editEndInput = document.getElementById('editTanggalSelesai');

            if (editStartInput && editEndInput) {
                editStartInput.addEventListener('change', () => calculateWorkingDays('edit'));
                editEndInput.addEventListener('change', () => calculateWorkingDays('edit'));
            }

            // Jenis cuti change untuk validasi quota
            document.getElementById('jenisCutiSelect')?.addEventListener('change', function () {
                if (this.value === 'tahunan' && currentQuotaInfo) {
                    showSisaCutiWarning();
                } else {
                    hideSisaCutiWarning();
                }
            });

            // Filter buttons
            document.querySelectorAll('.filter-badge').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-badge').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        // ==================== CORE FUNCTIONS ====================
        async function initializeCuti() {
            try {
                await Promise.all([loadCutiData(), loadCutiStats()]);
            } catch (error) {
                console.error('Initialization error:', error);
                showPopup('Error', 'Gagal memuat data awal', 'error');
            }
        }

        async function loadCutiData() {
            showLoading();
            try {
                const params = new URLSearchParams({
                    page: cutiCurrentPage,
                    per_page: 10,
                    status: cutiStatusFilter,
                    _token: CSRF_TOKEN
                });

                const response = await fetch(`${API_ROUTES.data}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();

                if (data.success) {
                    renderCutiTable(data.data);
                    renderPagination(data.pagination);
                    document.getElementById('cutiCount').innerText = data.pagination.total;

                    if (data.pagination.total === 0) {
                        showNoData();
                    } else {
                        hideNoData();
                    }
                } else {
                    throw new Error(data.message || 'Gagal memuat data');
                }
            } catch (error) {
                console.error('Load data error:', error);
                showNoData();
                showPopup('Error', 'Gagal memuat data cuti', 'error');
            } finally {
                hideLoading();
            }
        }

        async function loadCutiStats() {
            try {
                const response = await fetch(API_ROUTES.stats, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();

                if (data.success) {
                    updateCutiStatsUI(data.data);
                }
            } catch (error) {
                console.error('Load stats error:', error);
            }
        }

        async function loadQuotaInfo() {
            try {
                openModal('quotaInfoModal');

                const quotaLoading = document.getElementById('quotaLoading');
                const quotaContent = document.getElementById('quotaContent');

                if (!quotaLoading || !quotaContent) {
                    console.error('Quota modal elements not found');
                    return;
                }

                quotaContent.classList.add('hidden');
                quotaLoading.classList.remove('hidden');

                const response = await fetch(API_ROUTES.quotaInfo, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    renderQuotaInfo(data.data);
                    currentQuotaInfo = data.data;
                } else {
                    throw new Error(data.message || 'Gagal memuat informasi quota');
                }
            } catch (error) {
                console.error('Load quota info error:', error);
                showPopup('Error', 'Gagal memuat informasi quota', 'error');
                const quotaLoading = document.getElementById('quotaLoading');
                if (quotaLoading) quotaLoading.classList.add('hidden');
            }
        }

        function renderQuotaInfo(data) {
            try {
                const quotaLoading = document.getElementById('quotaLoading');
                const quotaContent = document.getElementById('quotaContent');
                
                if (!quotaLoading || !quotaContent) return;
                
                quotaLoading.classList.add('hidden');
                quotaContent.classList.remove('hidden');
                
                if (data.quota) {
                    const quotaTahun = document.getElementById('quotaTahun');
                    const quotaTotal = document.getElementById('quotaTotal');
                    const quotaTerpakai = document.getElementById('quotaTerpakai');
                    const quotaSisa = document.getElementById('quotaSisa');
                    
                    if (quotaTahun) quotaTahun.textContent = data.quota.tahun || '-';
                    if (quotaTotal) quotaTotal.textContent = `${data.quota.quota_tahunan || 0} hari`;
                    if (quotaTerpakai) quotaTerpakai.textContent = `${data.quota.terpakai || 0} hari`;
                    if (quotaSisa) quotaSisa.textContent = `${data.quota.sisa || 0} hari`;
                }
                
                if (data.statistics) {
                    const statCutiTahunan = document.getElementById('statCutiTahunan');
                    const statCutiSakit = document.getElementById('statCutiSakit');
                    const statCutiMenunggu = document.getElementById('statCutiMenunggu');
                    
                    if (statCutiTahunan) statCutiTahunan.textContent = `${data.statistics.cuti_tahunan_disetujui || 0} hari`;
                    if (statCutiSakit) statCutiSakit.textContent = `${data.statistics.cuti_sakit_disetujui || 0} hari`;
                    if (statCutiMenunggu) statCutiMenunggu.textContent = data.statistics.cuti_menunggu || 0;
                }
            } catch (error) {
                console.error('Error rendering quota info:', error);
            }
        }

        async function loadCurrentQuotaForValidation() {
            try {
                const response = await fetch(API_ROUTES.quotaInfo, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        currentQuotaInfo = data.data;
                        if (data.data.quota?.sisa !== undefined) {
                            document.getElementById('sisaCutiCount').textContent = data.data.quota.sisa;
                        }
                    }
                }
            } catch (error) {
                console.error('Load quota for validation error:', error);
            }
        }

        // ==================== ANIMATION HELPER: Counter ====================
        function animateValue(obj, start, end, duration) {
            if (!obj) return;
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // ==================== RENDER FUNCTIONS ====================
        function renderCutiTable(cutiData) {
            const tbody = document.getElementById('cutiTableBody');
            const mobileContainer = document.getElementById('cuti-mobile-cards');

            if (!tbody || !mobileContainer) return;

            tbody.innerHTML = '';
            mobileContainer.innerHTML = '';

            if (!cutiData || cutiData.length === 0) {
                mobileContainer.classList.add('hidden');
                return;
            }

            mobileContainer.classList.remove('hidden');

            cutiData.forEach((item, index) => {
                const globalIndex = (cutiCurrentPage - 1) * 10 + index + 1;
                const formattedDate = new Date(item.created_at).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const startDate = new Date(item.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const endDate = new Date(item.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const periode = `${startDate} - ${endDate}`;

                // Status badge
                let badgeClass = 'status-menunggu';
                let statusText = 'Menunggu';

                if (item.status === 'disetujui') {
                    badgeClass = 'status-disetujui';
                    statusText = 'Disetujui';
                } else if (item.status === 'ditolak') {
                    badgeClass = 'status-ditolak';
                    statusText = 'Ditolak';
                } else if (item.status === 'dibatalkan') {
                    badgeClass = 'status-dibatalkan';
                    statusText = 'Dibatalkan';
                }

                const statusBadge = `<span class="status-badge ${badgeClass} ${item.status === 'menunggu' ? 'animate-pulse-subtle' : ''}">${statusText}</span>`;

                // Jenis cuti text - gunakan jenis_cuti_kode untuk mapping
                const jenisCutiMap = {
                    'tahunan': 'Cuti Tahunan',
                    'melahirkan': 'Cuti Melahirkan',
                    'duka': 'Cuti Duka',
                    'izin-khusus': 'Cuti Izin Khusus',
                    'tanpa-gaji': 'Cuti Tanpa Gaji',
                    'sakit': 'Cuti Sakit',
                    'penting': 'Cuti Penting',
                    'lainnya': 'Cuti Lainnya'
                };
                // Gunakan jenis_cuti_kode (kode) untuk mapping, bukan jenis_cuti (text)
                const jenisCutiText = item.jenis_cuti || jenisCutiMap[item.jenis_cuti_kode] || 'Cuti Lainnya';

                // Action buttons
                let actions = `<div class="flex justify-center gap-1">`;

                // Lihat detail
                actions += `<button class="text-blue-500 hover:bg-blue-50 p-1 rounded transition-colors btn-active" title="Lihat Detail" onclick="showDetailCuti(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                              <span class="material-icons-outlined text-sm">visibility</span>
                            </button>`;

                // Edit hanya untuk status menunggu
                if (item.status === 'menunggu' && item.dapat_diubah) {
                    actions += `<button class="text-green-500 hover:bg-green-50 p-1 rounded transition-colors btn-active" title="Edit" onclick="showEditCutiModal(${item.id})">
                                  <span class="material-icons-outlined text-sm">edit</span>
                                </button>`;
                }

                // Hapus/batal
                if (item.status === 'menunggu' && item.dapat_dihapus) {
                    actions += `<button class="text-red-500 hover:bg-red-50 p-1 rounded transition-colors btn-active" title="Hapus" onclick="deleteCuti(${item.id})">
                                  <span class="material-icons-outlined text-sm">delete</span>
                                </button>`;
                } else if (item.status === 'disetujui' && item.dapat_batalkan) {
                    actions += `<button class="text-orange-500 hover:bg-orange-50 p-1 rounded transition-colors btn-active" title="Batalkan" onclick="cancelCuti(${item.id})">
                                  <span class="material-icons-outlined text-sm">cancel</span>
                                </button>`;
                }

                actions += `</div>`;

                // Desktop Row - Add animation class with delay
                const tr = document.createElement('tr');
                tr.className = 'animate-fade-in-up';
                tr.style.animationDelay = `${index * 50}ms`;
                tr.innerHTML = `
                    <td>${globalIndex}</td>
                    <td>${formattedDate}</td>
                    <td>${periode}</td>
                    <td>${item.durasi} hari</td>
                    <td>${jenisCutiText}</td>
                    <td class="max-w-xs truncate" title="${item.keterangan}">${item.keterangan}</td>
                    <td>${statusBadge}</td>
                    <td>${actions}</td>
                `;
                tbody.appendChild(tr);

                // Mobile Card - Add animation class with delay
                const card = document.createElement('div');
                card.className = "bg-white border border-gray-200 rounded-lg p-4 shadow-sm animate-fade-in-up";
                card.style.animationDelay = `${index * 50}ms`;
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-semibold text-gray-800">${periode}</span>
                            <p class="text-xs text-gray-500 mt-1">${formattedDate}</p>
                        </div>
                        ${statusBadge}
                    </div>
                    <p class="text-gray-600 text-sm mb-2 truncate" title="${item.keterangan}">${item.keterangan}</p>
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>${item.durasi} hari • ${jenisCutiText}</span>
                        <div class="flex gap-1">${actions}</div>
                    </div>
                `;
                mobileContainer.appendChild(card);
            });
        }

        function renderPagination(pagination) {
            totalCutiPages = pagination.last_page;
            const container = document.getElementById('cutiPageNumbers');
            const paginationContainer = document.getElementById('cutiPaginationContainer');
            const prevBtn = document.getElementById('cutiPrevPage');
            const nextBtn = document.getElementById('cutiNextPage');

            if (!container || !prevBtn || !nextBtn) return;

            container.innerHTML = '';

            if (totalCutiPages > 1) {
                paginationContainer.classList.remove('hidden');

                let startPage = Math.max(1, cutiCurrentPage - 2);
                let endPage = Math.min(totalCutiPages, cutiCurrentPage + 2);

                if (startPage > 1) {
                    const btn = document.createElement('button');
                    btn.className = 'desktop-page-btn';
                    btn.innerText = '1';
                    btn.onclick = () => goToPage(1);
                    container.appendChild(btn);

                    if (startPage > 2) {
                        const ellipsis = document.createElement('span');
                        ellipsis.className = 'px-2';
                        ellipsis.innerText = '...';
                        container.appendChild(ellipsis);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const btn = document.createElement('button');
                    btn.className = `desktop-page-btn ${i === cutiCurrentPage ? 'active' : ''}`;
                    btn.innerText = i;
                    btn.onclick = () => goToPage(i);
                    container.appendChild(btn);
                }

                if (endPage < totalCutiPages) {
                    if (endPage < totalCutiPages - 1) {
                        const ellipsis = document.createElement('span');
                        ellipsis.className = 'px-2';
                        ellipsis.innerText = '...';
                        container.appendChild(ellipsis);
                    }

                    const btn = document.createElement('button');
                    btn.className = 'desktop-page-btn';
                    btn.innerText = totalCutiPages;
                    btn.onclick = () => goToPage(totalCutiPages);
                    container.appendChild(btn);
                }

                prevBtn.disabled = cutiCurrentPage === 1;
                nextBtn.disabled = cutiCurrentPage === totalCutiPages;
            } else {
                paginationContainer.classList.add('hidden');
            }
        }

        // ==================== UI HELPER FUNCTIONS ====================
        function showLoading() {
            document.getElementById('cutiLoading').classList.remove('hidden');
            document.getElementById('cutiDesktopTable').classList.add('hidden');
            document.getElementById('cuti-mobile-cards').classList.add('hidden');
        }

        function hideLoading() {
            document.getElementById('cutiLoading').classList.add('hidden');
            const isMobile = window.innerWidth < 640;

            if (!isMobile) {
                document.getElementById('cutiDesktopTable').classList.remove('hidden');
                document.getElementById('cuti-mobile-cards').classList.add('hidden');
            } else {
                document.getElementById('cutiDesktopTable').classList.add('hidden');
                document.getElementById('cuti-mobile-cards').classList.remove('hidden');
            }
        }

        function showNoData() {
            document.getElementById('noCutiData').classList.remove('hidden');
            document.getElementById('cutiDesktopTable').classList.add('hidden');
            document.getElementById('cuti-mobile-cards').classList.add('hidden');
            document.getElementById('cutiPaginationContainer').classList.add('hidden');
        }

        function hideNoData() {
            document.getElementById('noCutiData').classList.add('hidden');
        }

        function updateCutiStatsUI(stats) {
            const total = stats?.quota_info?.quota_tahunan ?? stats.total_cuti_tahunan ?? 12;
            const terpakai = stats?.quota_info?.terpakai ?? stats.cuti_terpakai ?? 0;
            const sisa = stats?.quota_info?.sisa ?? stats.sisa_cuti ?? (total - terpakai);

            // Get current values to animate from
            const currentTotal = parseInt(document.getElementById('stat-total-cuti').textContent) || 0;
            const currentTerpakai = parseInt(document.getElementById('stat-cuti-terpakai').textContent) || 0;
            const currentSisa = parseInt(document.getElementById('stat-cuti-tersisa').textContent) || 0;

            // Animate numbers
            animateValue(document.getElementById('stat-total-cuti'), currentTotal, total, 1000);
            animateValue(document.getElementById('stat-cuti-terpakai'), currentTerpakai, terpakai, 1000);
            animateValue(document.getElementById('stat-cuti-tersisa'), currentSisa, sisa, 1000);

            if (stats.quota_info) {
                // Normalisasi bentuk data agar konsisten dengan response getQuotaInfo (data.quota)
                currentQuotaInfo = { quota: stats.quota_info };
            }
        }

        function showSisaCutiWarning() {
            if (currentQuotaInfo?.quota?.sisa !== undefined) {
                document.getElementById('sisaCutiCount').textContent = currentQuotaInfo.quota.sisa;
                document.getElementById('sisaCutiWarning').classList.remove('hidden');
            }
        }

        function hideSisaCutiWarning() {
            document.getElementById('sisaCutiWarning').classList.add('hidden');
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';

                if (modalId === 'tambahCutiModal') {
                    document.getElementById('tambahCutiForm').reset();
                    hideSisaCutiWarning();
                } else if (modalId === 'editCutiModal') {
                    currentEditingCuti = null;
                }
            }
        }

        function showPopup(title, message, type = 'success') {
            const popup = document.getElementById('minimalPopup');
            const icon = popup.querySelector('.minimal-popup-icon');
            const titleEl = popup.querySelector('.minimal-popup-title');
            const messageEl = popup.querySelector('.minimal-popup-message');

            titleEl.textContent = title;
            messageEl.textContent = message;

            // Reset animation
            popup.classList.remove('animate-slide-in-bounce');
            void popup.offsetWidth; // Trigger reflow
            popup.classList.add('animate-slide-in-bounce');

            popup.className = `minimal-popup show animate-slide-in-bounce ${type}`;

            icon.innerHTML = type === 'success'
                ? '<span class="material-icons-outlined">check_circle</span>'
                : '<span class="material-icons-outlined">error</span>';

            setTimeout(() => {
                hidePopup();
            }, 3000);
        }

        function hidePopup() {
            document.getElementById('minimalPopup').classList.remove('show', 'animate-slide-in-bounce');
        }

        function goToPage(page) {
            cutiCurrentPage = page;
            loadCutiData();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setFilter(status) {
            cutiStatusFilter = status;
            cutiCurrentPage = 1;
            loadCutiData();
        }

        // ==================== CRUD OPERATIONS ====================
        async function calculateWorkingDays(type = 'tambah') {
            const prefix = type === 'tambah' ? 'input' : 'edit';
            const startInput = document.getElementById(`${prefix}TanggalMulai`);
            const endInput = document.getElementById(`${prefix}TanggalSelesai`);
            const durasiInput = document.getElementById(`${prefix}Durasi`);

            if (!startInput.value || !endInput.value) {
                durasiInput.value = '';
                return;
            }

            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);

            if (endDate < startDate) {
                showPopup('Error', 'Tanggal selesai tidak boleh sebelum tanggal mulai', 'error');
                endInput.value = '';
                durasiInput.value = '';
                return;
            }

            try {
                let count = 0;
                let current = new Date(startDate);

                while (current <= endDate) {
                    const day = current.getDay();
                    if (day !== 0 && day !== 6) { // 0 = Sunday, 6 = Saturday
                        count++;
                    }
                    current.setDate(current.getDate() + 1);
                }

                durasiInput.value = count;

                // Validasi kuota jika perlu
                const jenisCutiSelect = document.getElementById(`${prefix}JenisCuti`);
                if (jenisCutiSelect && jenisCutiSelect.value === 'tahunan' && currentQuotaInfo?.quota?.sisa !== undefined) {
                    const sisaCuti = currentQuotaInfo.quota.sisa;
                    if (count > sisaCuti) {
                        showPopup('Peringatan', `Sisa cuti hanya ${sisaCuti} hari, durasi yang diminta ${count} hari`, 'error');
                    }
                }

            } catch (error) {
                console.error('Calculate duration error:', error);
                durasiInput.value = ''; 
            }
        }

        async function handleAddCuti() {
            const form = document.getElementById('tambahCutiForm');
            const submitBtn = document.getElementById('submitCutiBtn');

            if (!form.checkValidity()) {
                showPopup('Error', 'Harap lengkapi semua field yang wajib diisi', 'error');
                return;
            }

            const formData = new FormData(form);
            const jenisCuti = document.getElementById('jenisCutiSelect').value;
            const durasi = parseInt(formData.get('durasi'));

            // Validasi cuti tahunan
            if (jenisCuti === 'tahunan' && currentQuotaInfo?.quota?.sisa !== undefined) {
                if (durasi > currentQuotaInfo.quota.sisa) {
                    showPopup('Error', `Sisa cuti tidak mencukupi. Sisa: ${currentQuotaInfo.quota.sisa} hari, dibutuhkan: ${durasi} hari`, 'error');
                    return;
                }
            }

            // Disable button
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="material-icons-outlined text-sm animate-spin">sync</span> Mengirim...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(API_ROUTES.store, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeModal('tambahCutiModal');
                    showPopup('Berhasil', data.message, 'success');

                    // Reload data
                    await Promise.all([
                        loadCutiData(),
                        loadCutiStats()
                    ]);
                } else {
                    let errorMessage = data.message || 'Gagal mengajukan cuti';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join(', ');
                    }
                    showPopup('Error', errorMessage, 'error');
                }
            } catch (error) {
                console.error('Add cuti error:', error);
                showPopup('Error', 'Terjadi kesalahan jaringan', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        async function showEditCutiModal(cutiId) {
            try {
                openModal('editCutiModal');
                
                const loading = document.createElement('div');
                loading.className = 'text-center py-10';
                loading.innerHTML = `
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <p class="mt-2 text-gray-500 text-sm">Memuat data cuti...</p>
                `;
                
                const editCutiForm = document.getElementById('editCutiForm');
                editCutiForm.innerHTML = '';
                editCutiForm.appendChild(loading);

                const response = await fetch(API_ROUTES.edit(cutiId), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();

                if (data.success) {
                    currentEditingCuti = data.data;
                    populateEditForm(data.data);
                } else {
                    throw new Error(data.message || 'Gagal memuat data edit');
                }
            } catch (error) {
                console.error('Load edit data error:', error);
                showPopup('Error', 'Gagal memuat data untuk diedit', 'error');
                closeModal('editCutiModal');
            }
        }

        function populateEditForm(cutiData) {
            // Map untuk old values yang tidak valid lagi ke nilai baru
            const valueMapping = {
                'sakit': 'duka',           
                'penting': 'izin-khusus', 
                'lainnya': 'tanpa-gaji'    
            };
            
            const validValues = ['tahunan', 'melahirkan', 'duka', 'izin-khusus', 'tanpa-gaji'];
            let selectedValue = cutiData.jenis_cuti_kode || cutiData.jenis_cuti;
            
            if (!validValues.includes(selectedValue)) {
                selectedValue = valueMapping[selectedValue] || 'tanpa-gaji'; 
            }

            const form = document.getElementById('editCutiForm');
            form.innerHTML = `
                @csrf
                @method('PUT')
                <input type="hidden" name="cuti_id" id="editCutiId" value="${cutiData.id}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" required id="editTanggalMulai" value="${cutiData.tanggal_mulai}" min="{{ date('Y-m-d') }}" class="form-input w-full px-3 py-2 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" required id="editTanggalSelesai" value="${cutiData.tanggal_selesai}" min="{{ date('Y-m-d') }}" class="form-input w-full px-3 py-2 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Hari)</label>
                        <input type="number" name="durasi" id="editDurasi" required value="${cutiData.durasi}" class="form-input w-full px-3 py-2 rounded-lg bg-gray-50" readonly>
                        <p class="text-xs text-gray-500 mt-1">*Durasi dihitung otomatis (Senin-Jumat)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti <span class="text-red-500">*</span></label>
                        <select name="jenis_cuti" required class="form-input w-full px-3 py-2 rounded-lg" id="editJenisCuti">
                            <option value="">Pilih Jenis Cuti</option>
                            <option value="tahunan" ${selectedValue === 'tahunan' ? 'selected' : ''}>Cuti Tahunan</option>
                            <option value="melahirkan" ${selectedValue === 'melahirkan' ? 'selected' : ''}>Cuti Melahirkan</option>
                            <option value="duka" ${selectedValue === 'duka' ? 'selected' : ''}>Cuti Duka</option>
                            <option value="izin-khusus" ${selectedValue === 'izin-khusus' ? 'selected' : ''}>Cuti Izin Khusus</option>
                            <option value="tanpa-gaji" ${selectedValue === 'tanpa-gaji' ? 'selected' : ''}>Cuti Tanpa Gaji</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan/Alasan <span class="text-red-500">*</span></label>
                    <textarea name="keterangan" rows="3" required placeholder="Masukkan alasan pengajuan cuti..." id="editKeterangan" class="form-input w-full px-3 py-2 rounded-lg">${cutiData.keterangan || ''}</textarea>
                </div>
                <div id="editSisaCutiWarning" class="hidden p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-700"><span class="material-icons-outlined text-sm mr-1">warning</span>
                        Sisa cuti tahunan Anda: <span id="editSisaCutiCount" class="font-bold">${cutiData.sisa_cuti_karyawan || 0}</span> hari
                    </p>
                </div>
            `;

            document.getElementById('editTanggalMulai').addEventListener('change', () => calculateWorkingDays('edit'));
            document.getElementById('editTanggalSelesai').addEventListener('change', () => calculateWorkingDays('edit'));
            document.getElementById('editJenisCuti').addEventListener('change', function() {
                if (this.value === 'tahunan' && currentQuotaInfo) {
                    showEditSisaCutiWarning();
                } else {
                    hideEditSisaCutiWarning();
                }
            });

            if (selectedValue === 'tahunan') {
                showEditSisaCutiWarning();
            }
        }

        function showEditSisaCutiWarning() {
            if (currentQuotaInfo?.quota?.sisa !== undefined) {
                document.getElementById('editSisaCutiCount').textContent = currentQuotaInfo.quota.sisa;
                document.getElementById('editSisaCutiWarning').classList.remove('hidden');
            }
        }

        function hideEditSisaCutiWarning() {
            document.getElementById('editSisaCutiWarning').classList.add('hidden');
        }

        async function handleUpdateCuti() {
            if (!currentEditingCuti) return;

            const form = document.getElementById('editCutiForm');
            const updateBtn = document.getElementById('updateCutiBtn');

            if (!form.checkValidity()) {
                showPopup('Error', 'Harap lengkapi semua field yang wajib diisi', 'error');
                return;
            }

            const formData = new FormData(form);
            const cutiId = formData.get('cuti_id');
            const jenisCuti = document.getElementById('editJenisCuti').value;
            const durasi = parseInt(formData.get('durasi'));

            // Validasi cuti tahunan
            if (jenisCuti === 'tahunan' && currentQuotaInfo?.quota?.sisa !== undefined) {
                const oldDurasi = currentEditingCuti.durasi;
                const selisihHari = durasi - oldDurasi;
                
                if (selisihHari > 0 && selisihHari > currentQuotaInfo.quota.sisa) {
                    showPopup('Error', `Sisa cuti tidak mencukupi untuk penambahan ini. Sisa: ${currentQuotaInfo.quota.sisa} hari, dibutuhkan tambahan: ${selisihHari} hari`, 'error');
                    return;
                }
            }

            const originalText = updateBtn.innerHTML;
            updateBtn.innerHTML = '<span class="material-icons-outlined text-sm animate-spin">sync</span> Menyimpan...';
            updateBtn.disabled = true;

            try {
                const response = await fetch(API_ROUTES.update(cutiId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeModal('editCutiModal');
                    showPopup('Berhasil', data.message, 'success');

                    await Promise.all([
                        loadCutiData(),
                        loadCutiStats()
                    ]);
                } else {
                    let errorMessage = data.message || 'Gagal memperbarui cuti';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join(', ');
                    }
                    showPopup('Error', errorMessage, 'error');
                }
            } catch (error) {
                console.error('Update cuti error:', error);
                showPopup('Error', 'Terjadi kesalahan jaringan', 'error');
            } finally {
                updateBtn.innerHTML = originalText;
                updateBtn.disabled = false;
            }
        }

        function showDetailCuti(item) {
            const detailContent = document.getElementById('detailContent');
            detailContent.innerHTML = ''; // Clear previous content

            const createdDate = new Date(item.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const startDate = new Date(item.tanggal_mulai).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            const endDate = new Date(item.tanggal_selesai).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            const periode = `${startDate} - ${endDate}`;

            const jenisCutiMap = {
                'tahunan': 'Cuti Tahunan',
                'melahirkan': 'Cuti Melahirkan',
                'duka': 'Cuti Duka',
                'izin-khusus': 'Cuti Izin Khusus',
                'tanpa-gaji': 'Cuti Tanpa Gaji',
                'sakit': 'Cuti Sakit',
                'penting': 'Cuti Penting',
                'lainnya': 'Cuti Lainnya'
            };
            const jenisCutiText = item.jenis_cuti || jenisCutiMap[item.jenis_cuti_kode] || 'Cuti Lainnya';

            let badgeClass = 'status-menunggu';
            let statusText = 'Menunggu';

            if (item.status === 'disetujui') {
                badgeClass = 'status-disetujui';
                statusText = 'Disetujui';
            } else if (item.status === 'ditolak') {
                badgeClass = 'status-ditolak';
                statusText = 'Ditolak';
            } else if (item.status === 'dibatalkan') {
                badgeClass = 'status-dibatalkan';
                statusText = 'Dibatalkan';
            }

            // Create detail items dynamically with animation delays
            const details = [
                { label: 'Tanggal Pengajuan', value: createdDate },
                { label: 'Periode', value: periode },
                { label: 'Durasi', value: `${item.durasi} hari` },
                { label: 'Jenis Cuti', value: jenisCutiText },
                { label: 'Keterangan', value: item.keterangan },
                { label: 'Status', value: `<span class="status-badge ${badgeClass}">${statusText}</span>`, isHtml: true }
            ];

            // Add conditional fields
            if (item.status === 'disetujui' || item.status === 'ditolak') {
                details.push({ label: 'Disetujui Oleh', value: item.disetujui_oleh || '-' });
                details.push({ 
                    label: 'Disetujui Pada', 
                    value: item.disetujui_pada ?
                    new Date(item.disetujui_pada).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-'
                });
            }

            if (item.status === 'ditolak' && item.catatan_penolakan) {
                details.push({ label: 'Catatan Penolakan', value: item.catatan_penolakan });
            }

            // Render with staggered animation
            details.forEach((d, index) => {
                const div = document.createElement('div');
                div.className = 'detail-item';
                div.style.animationDelay = `${index * 50}ms`;
                div.innerHTML = `
                    <span class="detail-label">${d.label}</span>
                    <span class="detail-value">${d.isHtml ? d.value : d.value}</span>
                `;
                detailContent.appendChild(div);
            });

            openModal('detailCutiModal');
        }

        async function deleteCuti(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus pengajuan cuti ini?')) return;

            try {
                const response = await fetch(API_ROUTES.destroy(id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showPopup('Berhasil', data.message, 'success');
                    await Promise.all([
                        loadCutiData(),
                        loadCutiStats()
                    ]);
                } else {
                    showPopup('Error', data.message || 'Gagal menghapus cuti', 'error');
                }
            } catch (error) {
                console.error('Delete cuti error:', error);
                showPopup('Error', 'Terjadi kesalahan jaringan', 'error');
            }
        }

        async function cancelCuti(id) {
            if (!confirm('Apakah Anda yakin ingin membatalkan cuti yang sudah disetujui ini? Quota cuti akan dikembalikan.')) return;

            try {
                const response = await fetch(API_ROUTES.cancelRefund(id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showPopup('Berhasil', data.message, 'success');
                    await Promise.all([
                        loadCutiData(),
                        loadCutiStats()
                    ]);
                } else {
                    showPopup('Error', data.message || 'Gagal membatalkan cuti', 'error');
                }
            } catch (error) {
                console.error('Cancel cuti error:', error);
                showPopup('Error', 'Terjadi kesalahan jaringan', 'error');
            }
        }

        // ==================== RESPONSIVE HANDLING ====================
        window.addEventListener('resize', () => {
            hideLoading();
        });

        // ==================== GLOBAL FUNCTIONS ====================
        window.openCutiModal = () => openModal('tambahCutiModal');
        window.closeCutiModal = () => closeModal('tambahCutiModal');
        window.loadCutiData = loadCutiData;
        window.loadQuotaInfo = loadQuotaInfo;
        window.showEditCutiModal = showEditCutiModal;
    </script>
</body>
</html>

