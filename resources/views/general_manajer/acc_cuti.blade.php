<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Manajemen Cuti - General Manager</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <!-- Konfigurasi Tailwind -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                },
            },
        };

        // FIXED ROUTES
        window.appRoutes = {
            gm_cuti: {
                index: '/general_manajer/cuti/data', 
                approve: (id) => `/general_manajer/cuti/${id}/approve`,
                reject: (id) => `/general_manajer/cuti/${id}/reject`,
                stats: '/general_manajer/cuti/stats'
            }
        };
    </script>

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f1f5f9; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .material-icons-outlined { font-size: 24px; vertical-align: middle; }

        /* Status Badge */
        .status-badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; gap: 4px; }
        .status-disetujui { background-color: rgba(16, 185, 129, 0.1); color: #047857; border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-menunggu { background-color: rgba(245, 158, 11, 0.1); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.2); }
        .status-ditolak { background-color: rgba(239, 68, 68, 0.1); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.2); }

        /* Stats Cards */
        .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 1rem; position: relative; overflow: hidden; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 4px; }
        .stat-card.blue::before { background: #3b82f6; }
        .stat-card.green::before { background: #10b981; }
        .stat-card.red::before { background: #ef4444; }
        .stat-card.yellow::before { background: #f59e0b; }
        
        .stat-icon { width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon.blue { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .stat-icon.green { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
        .stat-icon.red { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .stat-icon.yellow { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        
        .stat-content { flex: 1; }
        .stat-label { font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1.2; }

        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .data-table th { background: #f8fafc; font-weight: 600; color: #475569; font-size: 0.75rem; text-transform: uppercase; position: sticky; top: 0; z-index: 10; }
        .data-table tbody tr:hover { background-color: #f8fafc; }
        .scrollable-table-container { width: 100%; overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 0.75rem; background: white; min-height: 200px; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 50; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; backdrop-filter: blur(2px); }
        .modal-overlay.open { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; border-radius: 1rem; width: 90%; max-width: 500px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .modal-overlay.open .modal-content { transform: scale(1); }

        /* Buttons */
        .action-btn { padding: 0.5rem; border-radius: 0.5rem; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; }
        .btn-approve { background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .btn-approve:hover:not(:disabled) { background-color: #10b981; color: white; }
        .btn-reject { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-reject:hover:not(:disabled) { background-color: #ef4444; color: white; }
        .action-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Toast */
        .toast { position: fixed; top: 20px; right: 20px; z-index: 100; transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55); }
        .toast.show { transform: translateX(0); }
        .toast.success { border-left: 4px solid #10b981; }
        .toast.error { border-left: 4px solid #ef4444; }
        .toast.warning { border-left: 4px solid #f59e0b; }
    </style>
</head>

<body class="font-display text-gray-800 antialiased overflow-hidden">
    <div class="flex h-screen">
        
        <!-- 1. WRAPPER SIDEBAR -->
        <div class="w-64 flex-shrink-0 bg-white border-r border-gray-200 hidden md:flex flex-col z-20">
            <!-- Include Template Sidebar GM -->
            @include('general_manajer.templet.header')
        </div>

        <!-- 2. MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- Topbar -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <!-- Tombol Back ke Dashboard (TAMBAHAN) -->
                   
                    <h2 class="font-semibold text-lg text-gray-800">Manajemen Pengajuan Cuti</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <!-- Avatar Dinamis -->
                   
                      
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
                
                <!-- Header Section -->
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                    
                      
                    </div>
                    <div class="flex gap-2">
                       
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card blue">
                        <div class="stat-icon blue"><span class="material-icons-outlined text-2xl">description</span></div>
                        <div class="stat-content"><div class="stat-label">Total Pengajuan</div><div class="stat-value" id="stat-total">-</div></div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-icon green"><span class="material-icons-outlined text-2xl">check_circle</span></div>
                        <div class="stat-content"><div class="stat-label">Disetujui</div><div class="stat-value" id="stat-approved">-</div></div>
                    </div>
                    <div class="stat-card yellow">
                        <div class="stat-icon yellow"><span class="material-icons-outlined text-2xl">hourglass_top</span></div>
                        <div class="stat-content"><div class="stat-label">Menunggu</div><div class="stat-value" id="stat-pending">-</div></div>
                    </div>
                    <div class="stat-card red">
                        <div class="stat-icon red"><span class="material-icons-outlined text-2xl">cancel</span></div>
                        <div class="stat-content"><div class="stat-label">Ditolak</div><div class="stat-value" id="stat-rejected">-</div></div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                        <div class="w-full md:w-1/3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama</label>
                            <div class="relative">
                                <span class="material-icons-outlined absolute left-3 top-2.5 text-gray-400">search</span>
                                <input type="text" id="searchInput" 
                                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                    placeholder="Ketik nama karyawan...">
                            </div>
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                             <select id="statusFilter" class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                                <option value="all">Semua Status</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <!-- Loading State -->
                    <div id="loadingState" class="hidden absolute inset-0 bg-white/80 z-10 flex items-center justify-center">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-8 w-8 text-blue-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-500 text-sm font-medium">Memuat data...</span>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="scrollable-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 60px;">ID</th>
                                    <th style="min-width: 220px;">Nama Karyawan</th>
                                    <th style="min-width: 250px;">Keterangan</th>
                                    <th style="min-width: 180px;">Tanggal</th>
                                    <th style="min-width: 100px;">Durasi</th>
                                    <th style="min-width: 140px;">Status</th>
                                    <th style="min-width: 140px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cutiTableBody">
                                <!-- Data Injected by JS -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State (Enhanced) -->
                    <div id="noCutiData" class="hidden flex-col items-center justify-center py-16">
                        <div class="bg-blue-50 rounded-full w-20 h-20 flex items-center justify-center mb-4">
                            <span class="material-icons-outlined text-3xl text-blue-400">inbox</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-600">Belum ada data pengajuan ditemukan</h3>
                        <p class="text-gray-500 text-sm mt-2 text-center max-w-md mx-auto">Belum ada karyawan yang mengajukan cuti saat ini.</p>
                    </div>
                </div>

                <footer class="mt-8 text-center text-gray-400 text-sm py-4">
                    &copy; {{ date('Y') }} digicity.id
                </footer>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div id="rejectModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="flex items-center gap-3 mb-4 text-red-600">
                <span class="material-icons-outlined text-3xl">warning</span>
                <h2 class="text-xl font-bold">Tolak Pengajuan Cuti</h2>
            </div>
            <div class="mb-6">
                <p class="text-gray-600 mb-4">Anda yakin ingin menolak pengajuan cuti ini?</p>
                <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="rejectReason" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none" placeholder="Tuliskan alasan..."></textarea>
                <p id="rejectError" class="text-red-500 text-xs mt-1 hidden">Alasan penolakan wajib diisi.</p>
            </div>
            <div class="flex justify-end gap-3">
                <button onclick="app.closeModal('rejectModal')" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                <button id="confirmRejectBtn" class="px-5 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 shadow-md transition">Tolak</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast bg-white shadow-xl rounded-lg px-6 py-4 flex items-center gap-3 min-w-[300px]">
        <span id="toast-icon" class="material-icons-outlined text-2xl">check_circle</span>
        <div>
            <h4 id="toast-title" class="font-bold text-gray-800 text-sm">Berhasil</h4>
            <p id="toast-message" class="text-gray-500 text-xs mt-0.5">Operasi berhasil dilakukan.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            app.init();
        });

        const CSRF_TOKEN = '{{ csrf_token() }}';

        const app = {
            data: [],
            currentId: null,
            
            init: function() {
                this.cacheDOM();
                this.bindEvents();
                this.refreshData();
            },

            cacheDOM: function() {
                this.dom = {
                    tableBody: document.getElementById('cutiTableBody'),
                    searchInput: document.getElementById('searchInput'),
                    statusFilter: document.getElementById('statusFilter'),
                    noData: document.getElementById('noCutiData'),
                    loading: document.getElementById('loadingState'),
                    rejectModal: document.getElementById('rejectModal'),
                    rejectReason: document.getElementById('rejectReason'),
                    rejectError: document.getElementById('rejectError'),
                    confirmRejectBtn: document.getElementById('confirmRejectBtn'),
                    toast: document.getElementById('toast'),
                    toastIcon: document.getElementById('toast-icon'),
                    toastTitle: document.getElementById('toast-title'),
                    toastMessage: document.getElementById('toast-message'),
                    stats: {
                        total: document.getElementById('stat-total'),
                        approved: document.getElementById('stat-approved'),
                        rejected: document.getElementById('stat-rejected'),
                        pending: document.getElementById('stat-pending')
                    }
                };
            },

            bindEvents: function() {
                this.dom.searchInput.addEventListener('input', () => this.renderTable());
                this.dom.statusFilter.addEventListener('change', () => this.renderTable());
                this.dom.confirmRejectBtn.addEventListener('click', () => this.processReject());
            },

            refreshData: function() {
                this.setLoading(true);
                
                // 1. Fetch Stats
                fetch(window.appRoutes.gm_cuti.stats)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengambil stats');
                        return response.json();
                    })
                    .then(res => {
                        if(res && res.success && res.data) {
                            this.updateStatsUI(res.data);
                        } else {
                            console.warn("Stats response invalid:", res);
                            this.updateStatsUI({ total: 0, disetujui: 0, ditolak: 0, menunggu: 0 });
                        }
                    })
                    .catch(err => {
                        console.error("Gagal ambil stats", err);
                        this.updateStatsUI({ total: 0, disetujui: 0, ditolak: 0, menunggu: 0 });
                    });

                // 2. Fetch Data List
                fetch(window.appRoutes.gm_cuti.index)
                    .then(response => {
                        const contentType = response.headers.get("content-type");
                        if (!response.ok || !contentType || !contentType.includes("application/json")) {
                            return response.text().then(text => { 
                                console.error("=== SERVER ERROR DITEMUKAN ===");
                                console.error(text);
                                throw new Error("Server Error (Bukan JSON): " + text.substring(0, 200)); 
                            });
                        }
                        return response.json();
                    })
                    .then(res => {
                        if (res && res.success && Array.isArray(res.data)) {
                            // FIXED: Gunakan item.nama bukan item.user.name
                            this.data = res.data.map(item => ({
                                id: item.id,
                                karyawan: item.nama || 'Unknown',  // ← FIXED: item.nama bukan item.user.name
                                keterangan: item.keterangan || '-',
                                tanggal_mulai: item.tanggal_mulai,
                                durasi: item.durasi,
                                status: item.status,
                                divisi: item.divisi || '-',
                                periode: item.periode || '-'
                            }));
                            this.renderTable();
                        } else {
                            console.warn("Response tidak valid:", res);
                            this.data = [];
                            this.renderTable();
                        }
                    })
                    .catch(err => {
                        console.error("Gagal ambil data list", err);
                        this.showToast('Gagal memuat data tabel. ' + err.message, 'error');
                        this.dom.tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-red-500 py-4">Gagal memuat data dari server.</td></tr>';
                    })
                    .finally(() => {
                        this.setLoading(false);
                    });
            },

            updateStatsUI: function(stats) {
                this.dom.stats.total.textContent = stats?.total ?? 0;
                this.dom.stats.approved.textContent = stats?.disetujui ?? 0;
                this.dom.stats.rejected.textContent = stats?.ditolak ?? 0;
                this.dom.stats.pending.textContent = stats?.menunggu ?? 0;
            },

            setLoading: function(isLoading) {
                if (isLoading) {
                    this.dom.loading.classList.remove('hidden');
                    this.dom.loading.classList.add('flex');
                } else {
                    this.dom.loading.classList.add('hidden');
                    this.dom.loading.classList.remove('flex');
                }
            },

            renderTable: function() {
                const term = this.dom.searchInput.value.toLowerCase();
                const statusFilter = this.dom.statusFilter.value;

                const filteredData = this.data.filter(item => {
                    const matchSearch = item.karyawan.toLowerCase().includes(term) || 
                                       item.keterangan.toLowerCase().includes(term) ||
                                       (item.divisi && item.divisi.toLowerCase().includes(term));
                    const matchStatus = statusFilter === 'all' || item.status === statusFilter;
                    return matchSearch && matchStatus;
                });

                this.dom.tableBody.innerHTML = '';
                
                if (filteredData.length === 0) {
                    this.dom.noData.classList.remove('hidden');
                    this.dom.noData.classList.add('flex');
                    return;
                }
                
                this.dom.noData.classList.add('hidden');
                this.dom.noData.classList.remove('flex');

                filteredData.forEach(cuti => {
                    const isPending = cuti.status === 'menunggu';
                    const disabled = isPending ? '' : 'disabled';
                    const opacity = isPending ? '' : 'opacity: 0.5; cursor: not-allowed;';

                    let badgeClass = 'status-menunggu', badgeText = 'Menunggu', icon = 'schedule';
                    if (cuti.status === 'disetujui') { badgeClass = 'status-disetujui'; badgeText = 'Disetujui'; icon = 'check_circle'; }
                    if (cuti.status === 'ditolak') { badgeClass = 'status-ditolak'; badgeText = 'Ditolak'; icon = 'cancel'; }

                    const date = cuti.tanggal_mulai ? new Date(cuti.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-xs font-mono text-gray-400">#${cuti.id}</td>
                        <td>
                            <div class="font-medium text-gray-800">${cuti.karyawan}</div>
                            <div class="text-xs text-gray-500 mt-1">${cuti.divisi}</div>
                        </td>
                        <td class="text-gray-600 text-sm">${cuti.keterangan}</td>
                        <td class="text-sm text-gray-600">
                            <div>${date}</div>
                            <div class="text-xs text-gray-400">${cuti.periode}</div>
                        </td>
                        <td class="text-sm font-medium text-gray-700">${cuti.durasi} Hari</td>
                        <td><span class="status-badge ${badgeClass}"><span class="material-icons-outlined text-sm">${icon}</span> ${badgeText}</span></td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <button class="action-btn btn-approve" ${disabled} style="${opacity}" onclick="app.approveCuti(${cuti.id})" title="Setujui">
                                    <span class="material-icons-outlined text-lg">check</span>
                                </button>
                                <button class="action-btn btn-reject" ${disabled} style="${opacity}" onclick="app.openRejectModal(${cuti.id})" title="Tolak">
                                    <span class="material-icons-outlined text-lg">close</span>
                                </button>
                            </div>
                        </td>
                    `;
                    this.dom.tableBody.appendChild(tr);
                });
            },

            approveCuti: function(id) {
                if (!confirm('Setujui pengajuan cuti ini?')) return;

                const btn = document.querySelector(`button[onclick="app.approveCuti(${id})"]`);
                const originalContent = btn.innerHTML;
                
                btn.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span>';
                btn.disabled = true;

                fetch(window.appRoutes.gm_cuti.approve(id), {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF_TOKEN, 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ _token: CSRF_TOKEN })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const idx = this.data.findIndex(d => d.id === id);
                        if(idx !== -1) this.data[idx].status = 'disetujui';
                        
                        this.showToast('Pengajuan disetujui', 'success');
                        this.renderTable();
                        this.refreshData();
                    } else {
                        this.showToast(data.message || 'Gagal menyetujui', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.showToast('Terjadi kesalahan koneksi', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
            },

            openRejectModal: function(id) {
                this.currentId = id;
                this.dom.rejectReason.value = '';
                this.dom.rejectError.classList.add('hidden');
                this.dom.rejectModal.classList.remove('hidden');
                void this.dom.rejectModal.offsetWidth; 
                this.dom.rejectModal.classList.add('open');
            },

            closeModal: function(id) {
                const modal = document.getElementById(id);
                modal.classList.remove('open');
                setTimeout(() => modal.classList.add('hidden'), 300);
            },

            processReject: function() {
                const reason = this.dom.rejectReason.value.trim();
                if (!reason) {
                    this.dom.rejectError.classList.remove('hidden');
                    return;
                }

                const btn = this.dom.confirmRejectBtn;
                const originalText = btn.textContent;
                btn.textContent = 'Memproses...';
                btn.disabled = true;

                fetch(window.appRoutes.gm_cuti.reject(this.currentId), {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF_TOKEN, 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ 
                        alasan_penolakan: reason, 
                        _token: CSRF_TOKEN 
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    this.closeModal('rejectModal');
                    this.showToast('Pengajuan ditolak', 'warning');
                    
                    const idx = this.data.findIndex(d => d.id === this.currentId);
                    if(idx !== -1) this.data[idx].status = 'ditolak';
                    
                    this.renderTable();
                    this.refreshData();
                })
                .catch(err => {
                    console.error(err);
                    this.showToast('Gagal menolak pengajuan', 'error');
                })
                .finally(() => {
                    btn.textContent = originalText;
                    btn.disabled = false;
                });
            },

            showToast: function(msg, type = 'success') {
                const toast = this.dom.toast;
                
                toast.className = 'toast bg-white shadow-xl rounded-lg px-6 py-4 flex items-center gap-3 min-w-[300px]';
                
                if (type === 'success') { 
                    toast.classList.add('success');
                    this.dom.toastIcon.textContent = 'check_circle';
                    this.dom.toastIcon.className = 'material-icons-outlined text-2xl text-green-500';
                    this.dom.toastTitle.textContent = 'Berhasil';
                }
                else if (type === 'warning') { 
                    toast.classList.add('warning');
                    this.dom.toastIcon.textContent = 'warning';
                    this.dom.toastIcon.className = 'material-icons-outlined text-2xl text-amber-500';
                    this.dom.toastTitle.textContent = 'Perhatian';
                }
                else { 
                    toast.classList.add('error');
                    this.dom.toastIcon.textContent = 'error';
                    this.dom.toastIcon.className = 'material-icons-outlined text-2xl text-red-500';
                    this.dom.toastTitle.textContent = 'Gagal';
                }
                
                this.dom.toastMessage.textContent = msg;
                toast.classList.add('show');
                
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        };
    </script>
</body>
</html>