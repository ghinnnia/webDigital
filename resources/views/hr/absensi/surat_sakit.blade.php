@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 to-slate-100 main-content">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto max-w-full">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">📋 Pengajuan Surat Sakit</h1>
                    <p class="text-slate-500 mt-1">Kelola dan approve surat sakit karyawan</p>
                </div>
                <a href="/hr/kelola-absensi" class="px-4 py-2 bg-slate-200 rounded-lg hover:bg-slate-300 transition">← Kembali</a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-700 rounded-r-xl">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-white border-b">
                    <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Daftar Pengajuan Surat Sakit
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Karyawan</th>
                                <th class="px-4 py-3 text-left">Divisi</th>
                                <th class="px-4 py-3 text-left">Keterangan</th>
                                <th class="px-4 py-3 text-center">Surat</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanSakit as $absen)
                            <tr class="border-b hover:bg-slate-50 transition">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $absen->user->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $absen->user->divisi->divisi ?? '-' }}</td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $absen->keterangan ?? '-' }}">
                                    {{ Str::limit($absen->keterangan ?? '-', 50) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($absen->file_surat)
                                        <span class="text-emerald-600 font-medium"><i class="fa-solid fa-file-circle-check mr-1"></i>Ada Surat</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusClass = $absen->status_surat == 'approved' ? 'bg-green-100 text-green-700' : 
                                                      ($absen->status_surat == 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                                        $statusText = $absen->status_surat == 'approved' ? 'Disetujui' : 
                                                     ($absen->status_surat == 'rejected' ? 'Ditolak' : 'Pending');
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($absen->status_surat == 'pending')
                                        <div class="flex gap-2 justify-center">
                                            <form action="{{ route('hr.absensi.approve-sakit', $absen->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-lg text-xs hover:bg-green-600 transition">
                                                    ✅ Setuju
                                                </button>
                                            </form>
                                            <form action="{{ route('hr.absensi.reject-sakit', $absen->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600 transition">
                                                    ❌ Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs">Sudah diproses</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="font-medium">Tidak ada pengajuan surat sakit</p>
                                        <p class="text-xs mt-1">Karyawan yang upload surat sakit akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pengajuanSakit->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    {{ $pengajuanSakit->links() }}
                </div>
                @endif
            </div>

            <!-- Informasi -->
            <div class="mt-6 p-4 bg-blue-50 rounded-2xl border border-blue-200">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p><strong>Informasi:</strong></p>
                        <ul class="mt-2 space-y-1">
                            <li>• Surat sakit yang <strong>disetujui</strong> → Karyawan <strong>TIDAK dipotong gaji</strong></li>
                            <li>• Surat sakit yang <strong>ditolak</strong> → Karyawan <strong>DIPOTONG gaji</strong> (sama seperti izin/alpha)</li>
                            <li>• Status <strong>Pending</strong> → Masih menunggu persetujuan HR</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>