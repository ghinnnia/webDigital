@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-background-light main-content">
    <div class="flex-1 p-3 sm:p-8">
        <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Pengumuman</h2>
        
        <!-- Search and Filter Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="relative w-full md:w-1/3">
                <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Cari judul atau isi pengumuman..." type="text" />
            </div>
            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <button id="createBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                    <span class="material-icons-outlined">add</span>
                    <span class="hidden sm:inline">Buat Pengumuman</span>
                    <span class="sm:hidden">Buat</span>
                </button>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Daftar Pengumuman</h3>
                <span class="text-sm text-gray-500">Total: <span id="totalCount" class="font-semibold">0</span> pengumuman</span>
            </div>
            <div class="p-6">
                <!-- Loading -->
                <div id="loadingState" class="text-center py-12 hidden">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Memuat data pengumuman...</p>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-12">
                    <span class="material-icons-outlined text-5xl text-gray-300 mb-3">campaign</span>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Pengumuman</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan membuat pengumuman pertama Anda</p>
                    <button id="createFirstBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Buat Pengumuman Pertama</button>
                </div>
                
                <!-- Desktop Table -->
                <div id="tableContainer" class="hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">No</th>
                                    <th class="px-4 py-3 text-left">Judul</th>
                                    <th class="px-4 py-3 text-left">Isi</th>
                                    <th class="px-4 py-3 text-center">Target</th>
                                    <th class="px-4 py-3 text-center">Tanggal</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-4">
                        <div>Menampilkan <span id="showingStart">0</span>-<span id="showingEnd">0</span> dari <span id="showingTotal">0</span></div>
                        <div class="flex gap-2">
                            <button id="prevPage" class="px-3 py-1 bg-gray-100 rounded disabled:opacity-50" disabled>← Sebelumnya</button>
                            <button id="nextPage" class="px-3 py-1 bg-gray-100 rounded disabled:opacity-50" disabled>Selanjutnya →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    let allData = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let currentAction = 'create';
    let currentId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadData();
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('createBtn')?.addEventListener('click', openCreateModal);
        document.getElementById('createFirstBtn')?.addEventListener('click', openCreateModal);
        document.getElementById('searchInput')?.addEventListener('input', filterData);
        document.getElementById('prevPage')?.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderData(); } });
        document.getElementById('nextPage')?.addEventListener('click', () => { const total = Math.ceil(filteredData.length / itemsPerPage); if (currentPage < total) { currentPage++; renderData(); } });
    }

    async function loadData() {
        showLoading(true);
        try {
            const response = await fetch('/pengumuman/data', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const result = await response.json();
            if (result.success && result.data) {
                allData = result.data;
                filteredData = [...allData];
                renderData();
            } else {
                document.getElementById('emptyState').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('emptyState').classList.remove('hidden');
        } finally {
            showLoading(false);
        }
    }

    function renderData() {
        const totalItems = filteredData.length;
        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, totalItems);
        const pageData = filteredData.slice(start, end);
        
        document.getElementById('totalCount').textContent = totalItems;
        document.getElementById('showingStart').textContent = totalItems > 0 ? start + 1 : 0;
        document.getElementById('showingEnd').textContent = end;
        document.getElementById('showingTotal').textContent = totalItems;
        
        renderTable(pageData, start);
        
        document.getElementById('tableContainer').classList.remove('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        
        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage >= Math.ceil(totalItems / itemsPerPage);
    }

    function renderTable(data, start) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';
        
        data.forEach((item, idx) => {
            const row = tbody.insertRow();
            const date = new Date(item.created_at).toLocaleDateString('id-ID');
            row.innerHTML = `
                <td class="px-4 py-3">${start + idx + 1}</td>
                <td class="px-4 py-3 font-medium">${escapeHtml(item.judul)}</td>
                <td class="px-4 py-3 max-w-xs truncate" title="${escapeHtml(item.isi_pesan)}">${escapeHtml(item.isi_pesan).substring(0, 80)}${item.isi_pesan && item.isi_pesan.length > 80 ? '...' : ''}</td>
                <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">${escapeHtml(item.target || 'semua')}</span></td>
                <td class="px-4 py-3 text-center">${date}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <button onclick="editData(${item.id})" class="text-amber-600 hover:text-amber-800" title="Edit"><span class="material-icons-outlined">edit</span></button>
                        <button onclick="deleteData(${item.id})" class="text-red-600 hover:text-red-800" title="Hapus"><span class="material-icons-outlined">delete</span></button>
                    </div>
                </td>
            `;
        });
    }

    function filterData() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        filteredData = allData.filter(item => 
            item.judul.toLowerCase().includes(searchTerm) || 
            (item.isi_pesan && item.isi_pesan.toLowerCase().includes(searchTerm))
        );
        currentPage = 1;
        renderData();
    }

    function openCreateModal() {
        currentAction = 'create';
        currentId = null;
        showModal('Buat Pengumuman Baru', getFormTemplate({}), 'Simpan');
    }

    async function editData(id) {
        try {
            currentAction = 'edit';
            currentId = id;
            const response = await fetch(`/pengumuman/${id}`);
            const result = await response.json();
            if (result.success) {
                showModal('Edit Pengumuman', getFormTemplate(result.data), 'Update');
            } else {
                alert('Gagal memuat data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal memuat data');
        }
    }

    function getFormTemplate(data) {
        return `
            <form id="pengumumanForm" class="space-y-4" onsubmit="return false;">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700"><span class="text-red-500">*</span> Judul</label>
                    <input type="text" id="judulInput" value="${escapeHtml(data.judul || '')}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700"><span class="text-red-500">*</span> Isi Pesan</label>
                    <textarea id="isiInput" rows="5" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>${escapeHtml(data.isi_pesan || '')}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Target</label>
                    <select id="targetInput" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="semua" ${(data.target || 'semua') === 'semua' ? 'selected' : ''}>Semua Pengguna</option>
                        <option value="hr" ${data.target === 'hr' ? 'selected' : ''}>HR Only</option>
                        <option value="manager" ${data.target === 'manager' ? 'selected' : ''}>Manager Only</option>
                        <option value="karyawan" ${data.target === 'karyawan' ? 'selected' : ''}>Karyawan Only</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Tanggal Mulai</label><input type="date" id="tanggalMulaiInput" value="${data.tanggal_mulai || ''}" class="w-full px-3 py-2 border rounded-lg"></div>
                    <div><label class="block text-sm font-medium mb-1">Tanggal Selesai</label><input type="date" id="tanggalSelesaiInput" value="${data.tanggal_selesai || ''}" class="w-full px-3 py-2 border rounded-lg"></div>
                </div>
            </form>
        `;
    }

    function showModal(title, content, confirmText) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4"><h3 class="text-xl font-bold">${title}</h3><button onclick="this.closest('.fixed').remove()" class="text-gray-500">&times;</button></div>
                    <div class="mb-6">${content}</div>
                    <div class="flex justify-end gap-3">
                        <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                        <button onclick="handleConfirm()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">${confirmText}</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    async function handleConfirm() {
        const judul = document.getElementById('judulInput')?.value.trim();
        const isi = document.getElementById('isiInput')?.value.trim();
        const target = document.getElementById('targetInput')?.value;
        const tanggalMulai = document.getElementById('tanggalMulaiInput')?.value;
        const tanggalSelesai = document.getElementById('tanggalSelesaiInput')?.value;

        if (!judul || !isi) {
            alert('Judul dan isi pesan wajib diisi');
            return;
        }

        const formData = { judul, isi_pesan: isi, target, tanggal_mulai: tanggalMulai, tanggal_selesai: tanggalSelesai };
        const url = currentAction === 'edit' ? `/pengumuman/${currentId}` : '/pengumuman';
        const method = currentAction === 'edit' ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ ...formData, _method: method })
            });
            const result = await response.json();
            if (result.success) {
                alert(result.message);
                document.querySelector('.fixed')?.remove();
                loadData();
            } else {
                alert(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menyimpan pengumuman');
        }
    }

    async function deleteData(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')) return;
        try {
            const response = await fetch(`/pengumuman/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) {
                alert('Pengumuman berhasil dihapus');
                loadData();
            } else {
                alert(result.message || 'Gagal menghapus');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menghapus pengumuman');
        }
    }

    function showLoading(show) {
        const loading = document.getElementById('loadingState');
        const table = document.getElementById('tableContainer');
        const empty = document.getElementById('emptyState');
        if (show) {
            loading.classList.remove('hidden');
            if (table) table.classList.add('hidden');
            if (empty) empty.classList.add('hidden');
        } else {
            loading.classList.add('hidden');
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>