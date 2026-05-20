@include('hr.templet.sider')
<main class="flex-1 flex flex-col bg-background-light main-content">
<div class="flex-1 p-3 sm:p-8">
<div class="p-6">
<!-- @extends('layouts.app')

@section('title', 'KPA - Kinerja Pegawai') -->

<!-- @section('content') -->
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">

<div class="container mx-auto p-6">
    <div class="mb-4">
        <a href="{{ route('hr.kpa.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <span class="material-icons-outlined">arrow_back</span>
            Kembali
        </a>
    </div>

    <h2 class="text-2xl font-bold mb-6">Edit Penilaian KPA</h2>

    <form action="{{ route('hr.kpa.update', $kinerja->id) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Karyawan</label>
            <input type="text" value="{{ $kinerja->karyawan->name ?? '-' }}" class="w-full border rounded-lg p-2 bg-gray-100" disabled>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Bulan</label>
                <input type="text" value="{{ \Carbon\Carbon::create()->month($kinerja->bulan)->format('F') }}" class="w-full border rounded-lg p-2 bg-gray-100" disabled>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tahun</label>
                <input type="text" value="{{ $kinerja->tahun }}" class="w-full border rounded-lg p-2 bg-gray-100" disabled>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-1">Nilai Kehadiran (0-100) <span class="text-red-500">*</span></label>
                <input type="number" name="nilai_kehadiran" value="{{ old('nilai_kehadiran', $kinerja->nilai_kehadiran) }}" class="w-full border rounded-lg p-2" min="0" max="100" step="1" required>
                @error('nilai_kehadiran') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nilai Ketepatan Waktu (0-100) <span class="text-red-500">*</span></label>
                <input type="number" name="nilai_ketepatan_waktu" value="{{ old('nilai_ketepatan_waktu', $kinerja->nilai_ketepatan_waktu) }}" class="w-full border rounded-lg p-2" min="0" max="100" step="1" required>
                @error('nilai_ketepatan_waktu') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nilai Penyelesaian Tugas (0-100) <span class="text-red-500">*</span></label>
                <input type="number" name="nilai_penyelesaian_tugas" value="{{ old('nilai_penyelesaian_tugas', $kinerja->nilai_penyelesaian_tugas) }}" class="w-full border rounded-lg p-2" min="0" max="100" step="1" required>
                @error('nilai_penyelesaian_tugas') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nilai Kesesuaian Tugas (0-100) <span class="text-red-500">*</span></label>
                <input type="number" name="nilai_kesesuaian_tugas" value="{{ old('nilai_kesesuaian_tugas', $kinerja->nilai_kesesuaian_tugas) }}" class="w-full border rounded-lg p-2" min="0" max="100" step="1" required>
                @error('nilai_kesesuaian_tugas') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium mb-1">Catatan (Opsional)</label>
            <textarea name="catatan" rows="3" class="w-full border rounded-lg p-2">{{ old('catatan', $kinerja->catatan) }}</textarea>
            @error('catatan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('hr.kpa.index') }}" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update</button>
        </div>
    </form>
</div>
@endsection