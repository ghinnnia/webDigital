@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-[#f8fafc] main-content min-h-screen">
    <div class="p-4 lg:p-8">
        <div class="max-w-6xl mx-auto">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="p-3 bg-indigo-600 rounded-2xl text-white material-icons-outlined shadow-lg shadow-indigo-200">payments</span>
                        Master Tunjangan
                    </h1>
                    <p class="text-slate-500 mt-2 font-medium underline decoration-indigo-200 underline-offset-4">Konfigurasi komponen gaji perusahaan</p>
                </div>
                
                <button id="btnAddTunjangan" class="flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-indigo-100 transition-all active:scale-95">
                    <span class="material-icons-outlined text-lg mr-2">add_circle</span> Tambah Master
                </button>
            </div>

            @if(session('success'))
                <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center animate-fade-in shadow-sm">
                    <span class="material-icons-outlined mr-3 text-emerald-500">verified</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                <div class="space-y-5">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span> Daftar Tunjangan
                        </h2>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 shadow-sm">
                            <span class="material-icons-outlined text-sm">lock</span>
                            <span class="text-[10px] font-black uppercase tracking-wider">Fixed Allowance</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-xl shadow-slate-200/40 overflow-hidden transition-all hover:shadow-indigo-100">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Komponen</th>
                                    <th class="px-6 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal Default</th>
                                    <th class="px-6 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tunjanganMaster->where('tipe', 'bulanan') as $t)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-6">
                                        <div class="font-bold text-slate-800 text-base">{{ $t->nama }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Recurring Monthly</div>
                                    </td>
                                    <td class="px-6 py-6 text-right font-black text-indigo-600 text-sm italic">
                                        Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex justify-center gap-1.5">
                                            <button onclick="editTunjangan({{ $t->id }}, '{{ $t->nama }}', '{{ $t->tipe }}', {{ $t->nominal }})" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                <span class="material-icons-outlined text-sm">edit_note</span>
                                            </button>
                                            <button onclick="deleteTunjangan({{ $t->id }}, '{{ $t->nama }}')" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                <span class="material-icons-outlined text-sm">delete_sweep</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <p class="text-slate-300 font-black text-[10px] uppercase tracking-widest">Belum ada tunjangan tetap</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></span> Daftar Tunjangan
                        </h2>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl border border-amber-100 shadow-sm">
                            <span class="material-icons-outlined text-sm">auto_graph</span>
                            <span class="text-[10px] font-black uppercase tracking-wider">Variable Allowance</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-xl shadow-slate-200/40 overflow-hidden transition-all hover:shadow-indigo-100">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Komponen</th>
                                    <th class="px-6 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal Default</th>
                                    <th class="px-6 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tunjanganMaster->whereIn('tipe', ['bonus', 'insentif']) as $t)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-6">
                                        <div class="font-bold text-slate-800 text-base">{{ $t->nama }}</div>
                                        <div class="text-[10px] text-amber-500 font-bold uppercase mt-0.5 tracking-tighter">{{ $t->tipe }} Based</div>
                                    </td>
                                    <td class="px-6 py-6 text-right font-black text-slate-900 text-sm">
                                        Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex justify-center gap-1.5">
                                            <button onclick="editTunjangan({{ $t->id }}, '{{ $t->nama }}', '{{ $t->tipe }}', {{ $t->nominal }})" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                <span class="material-icons-outlined text-sm">edit_note</span>
                                            </button>
                                            <button onclick="deleteTunjangan({{ $t->id }}, '{{ $t->nama }}')" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                <span class="material-icons-outlined text-sm">delete_sweep</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-300 font-black text-[10px] uppercase tracking-widest">Belum ada tunjangan tidak tetap</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="mt-12 flex justify-center">
                <div class="inline-flex items-center gap-4 px-6 py-3 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                    </span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Gunakan data ini untuk mengatur tunjangan di profil setiap karyawan</span>
                </div>
            </div>

        </div>
    </div>
</main>

<div id="modalTunjangan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md hidden items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md overflow-hidden animate-zoom-in">
        <div class="p-10">
            <div class="flex justify-between items-center mb-8">
                <div class="w-14 h-14 rounded-[1.5rem] bg-indigo-600 text-white flex items-center justify-center shadow-xl shadow-indigo-200">
                    <span class="material-icons-outlined text-2xl">account_balance_wallet</span>
                </div>
                <button onclick="closeModal()" class="text-slate-300 hover:text-slate-600 transition-colors">
                    <span class="material-icons-outlined text-3xl">cancel</span>
                </button>
            </div>
            
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Tambah Master</h3>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mt-1 mb-8">Input komponen finansial baru</p>

            <form method="POST" action="{{ route('hr.tunjangan.add') }}" class="space-y-6">
                @csrf
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Tunjangan</label>
                    <input type="text" name="nama" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-300" placeholder="Contoh: Transport Operasional" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                        <select name="tipe" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold outline-none cursor-pointer">
                            <option value="bulanan">Fixed</option>
                            <option value="bonus">Variable</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nominal (IDR)</label>
                        <input type="number" name="nominal" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold outline-none focus:border-indigo-500 transition-all" placeholder="0" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black text-[11px] uppercase tracking-[0.3em] rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-[0.98]">
                        Daftarkan Komponen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalEditTunjangan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md hidden items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md overflow-hidden animate-zoom-in">
        <div class="p-10">
            <h3 class="text-xl font-black text-slate-800 tracking-tight mb-6 flex items-center gap-2">
                <span class="material-icons-outlined text-indigo-600">edit_square</span> 
                Update Master
            </h3>
            <form id="editForm" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Komponen</label>
                    <input type="text" name="nama" id="edit_nama" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none transition-all" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe</label>
                        <select name="tipe" id="edit_tipe" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none transition-all">
                            <option value="bulanan">Fixed</option>
                            <option value="bonus">Variable</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal</label>
                        <input type="number" name="nominal" id="edit_nominal" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none transition-all" required>
                    </div>
                </div>
                <button type="submit" class="w-full py-5 bg-slate-900 text-white font-black text-[11px] uppercase tracking-[0.3em] rounded-2xl shadow-lg transition-all active:scale-[0.98]">Update Konfigurasi</button>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modalTunjangan');
    const editModal = document.getElementById('modalEditTunjangan');

    function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    function closeEditModal() { editModal.classList.add('hidden'); editModal.classList.remove('flex'); }

    document.getElementById('btnAddTunjangan')?.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    function editTunjangan(id, nama, tipe, nominal) {
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_tipe').value = tipe;
        document.getElementById('edit_nominal').value = nominal;
        document.getElementById('editForm').action = '/hr/tunjangan/' + id;
        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
    }

    function deleteTunjangan(id, nama) {
        if (confirm('Hapus master komponen "' + nama + '"?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/hr/tunjangan/' + id;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
        if (e.target === editModal) closeEditModal();
    });
</script>

<style>
    @keyframes zoomIn {
        from { opacity: 0; transform: translateY(10px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-zoom-in { animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    
    /* Custom Scrollbar for better UI */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>