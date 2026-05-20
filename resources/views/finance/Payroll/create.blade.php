{{-- resources/views/finance/payroll/create.blade.php --}}
@include('finance.templet.sider')

<main class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 to-slate-100 main-content">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto max-w-4xl">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
                        <i class="fa-solid fa-file-invoice-dollar text-indigo-600 mr-2"></i>Buat Periode Payroll
                    </h1>
                    <p class="text-slate-500 mt-1">Silakan pilih bulan dan tahun untuk memproses gaji baru.</p>
                </div>
                <div>
                    <a href="{{ route('finance.payroll.index') }}" class="inline-flex items-center px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition-all duration-200">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Notifikasi -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                    <i class="fa-solid fa-circle-xmark mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-white border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-plus text-indigo-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-800 text-lg">Form Periode Payroll</h2>
                            <p class="text-xs text-slate-400 mt-0.5">* Sistem akan otomatis menarik data dari HR untuk bulan yang dipilih.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('finance.payroll.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bulan -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Bulan</label>
                            <div class="relative">
                                <select name="bulan" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all duration-150 appearance-none bg-white" required>
                                    <option value="" disabled selected>Pilih Bulan</option>
                                    @foreach($months as $num => $name)
                                        <option value="{{ $num }}" {{ old('bulan', now()->month) == $num ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('bulan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tahun -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Tahun</label>
                            <div class="relative">
                                <select name="tahun" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all duration-150 appearance-none bg-white" required>
                                    <option value="" disabled selected>Pilih Tahun</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ old('tahun', now()->year) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('tahun')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-amber-800 font-semibold">Perhatian</p>
                            <p class="text-xs text-amber-700 mt-0.5">Pastikan data gaji di halaman HR sudah disubmit agar data yang ditarik oleh Finance akurat. Jika data HR belum disubmit, sistem akan menggunakan data terakhir yang tersedia.</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-gear fa-spin mr-1"></i> Proses Payroll
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>
