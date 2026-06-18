<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setting Upah Lembur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50">

@include('finance.templet.sider')

<main class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 to-slate-100 main-content">
    <div class="flex-1 p-4 sm:p-8">
        <div class="container mx-auto max-w-full">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ request('redirect_to', route('finance.overtime-settings.index')) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-all">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            ⚙️ Setting Upah Lembur
                        </h1>
                        <p class="text-slate-500 text-sm mt-0.5">Terakhir update: <span class="font-semibold text-indigo-600">{{ now()->format('d/m/Y H:i') }} WIB</span></p>
                    </div>
                </div>
            </div>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-2 text-lg"></i>
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                    <div class="flex items-center mb-2 font-semibold">
                        <i class="fa-solid fa-circle-xmark mr-2 text-lg text-red-600"></i> Terjadi Kesalahan:
                    </div>
                    <ul class="list-disc pl-6 text-sm text-red-600/90 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center gap-3 mb-5 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-building text-indigo-600 text-xl"></i>
                        <h2 class="text-lg font-bold text-slate-800">🏢 Standar Perusahaan</h2>
                    </div>
                    
                    <form action="{{ route('finance.overtime-settings.update-default') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @if(request()->has('redirect_to'))
                            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                        @endif
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Upah Lembur Default (per jam)</label>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-medium">Rp</span>
                                <input type="number" name="default_rate" class="w-full rounded-xl border-slate-200 bg-slate-50/50 p-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none border transition font-mono" 
                                       value="{{ $defaultSetting->default_rate ?? 30000 }}" min="0" step="1000">
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Digunakan jika divisi tidak punya setting khusus</p>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Maksimal Upah Lembur (per jam)</label>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-medium">Rp</span>
                                <input type="number" name="max_rate" class="w-full rounded-xl border-slate-200 bg-slate-50/50 p-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none border transition font-mono" 
                                       value="{{ $defaultSetting->max_rate ?? 100000 }}" min="0" step="1000">
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Manager tidak bisa mengatur upah di atas batas ini</p>
                        </div>
                        
                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-xl shadow-md shadow-indigo-100 hover:bg-indigo-700 transition active:scale-95">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Setting Default
                        </button>
                    </form>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center gap-3 mb-5 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-layer-group text-emerald-600 text-xl"></i>
                        <h2 class="text-lg font-bold text-slate-800">📊 Setting Per Divisi</h2>
                    </div>
                    
                    <div class="overflow-x-auto max-h-96 overflow-y-auto border border-slate-100 rounded-xl shadow-inner">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-slate-50 sticky top-0 border-b border-slate-100 text-slate-600 font-semibold text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="p-3 text-left">Divisi</th>
                                    <th class="p-3 text-left">Upah/Jam</th>
                                    <th class="p-3 text-left">Maksimal</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($divisions as $divisi)
                                @php
                                    $divSetting = $settings->where('division_id', $divisi->id)->first();
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-3 font-semibold text-slate-800">{{ $divisi->divisi }}</td>
                                    <td class="p-3">
                                        <form action="{{ route('finance.overtime-settings.update-division', $divisi->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            
                                            @if(request()->has('redirect_to'))
                                                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                                            @endif
                                            
                                            <input type="number" name="default_rate" class="w-28 p-1.5 border border-slate-200 rounded-lg text-sm bg-slate-50/50 focus:bg-white outline-none font-mono" 
                                                   value="{{ $divSetting->default_rate ?? $defaultSetting->default_rate ?? 30000 }}" step="1000">
                                            <input type="hidden" name="max_rate" value="{{ $divSetting->max_rate ?? $defaultSetting->max_rate ?? 100000 }}">
                                            
                                            <button type="submit" class="bg-emerald-600 text-white px-2.5 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-700 transition shadow-sm">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                    <td class="p-3 text-slate-500 font-mono">
                                        Rp {{ number_format($divSetting->max_rate ?? $defaultSetting->max_rate ?? 100000, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        @if($divSetting)
                                        <form action="{{ route('finance.overtime-settings.reset-division', $divisi->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            
                                            @if(request()->has('redirect_to'))
                                                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                                            @endif
                                            
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-semibold flex items-center justify-center gap-1 mx-auto" onclick="return confirm('Reset divisi {{ $divisi->divisi }} ke default?')">
                                                <i class="fa-solid fa-undo"></i> Reset
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-xs text-slate-400 italic font-medium">Menggunakan Default</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-base">
                    <i class="fa-solid fa-clock-rotate-left text-slate-500"></i> 📜 Riwayat Perubahan
                </h3>
                <div class="overflow-x-auto border border-slate-100 rounded-xl">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="p-3 text-left">Tanggal</th>
                                <th class="p-3 text-left">Divisi</th>
                                <th class="p-3 text-left">Rate Lama</th>
                                <th class="p-3 text-left">Rate Baru</th>
                                <th class="p-3 text-left">Diubah oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-slate-50/20">
                                <td colspan="5" class="text-center text-slate-400 italic py-6 text-xs">
                                    Belum ada riwayat (Fitur ini membutuhkan tabel log tambahan di database)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</main>

</body>
</html>