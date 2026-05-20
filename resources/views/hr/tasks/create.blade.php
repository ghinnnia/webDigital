{{-- resources/views/hr/tasks/create.blade.php --}}
@include('hr.templet.sider')
<main class="flex-1 flex flex-col bg-background-light main-content">
            <div class="flex-1 p-3 sm:p-8"></div>

<div class="p-6">
    <div class="mb-4">
        <a href="{{ route('hr.tasks.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <span class="material-icons-outlined">arrow_back</span>
            Kembali
        </a>
    </div>

    <h2 class="text-2xl font-bold mb-6">Buat Tugas Baru</h2>

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('hr.tasks.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Judul Tugas <span class="text-red-500">*</span></label>
            <input type="text" name="judul" value="{{ old('judul') }}" class="w-full border rounded-lg p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border rounded-lg p-2">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Pilih Karyawan <span class="text-red-500">*</span></label>
            <select name="karyawan_id" class="w-full border rounded-lg p-2" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach($karyawan as $k)
                    <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }} - {{ $k->divisi ?? 'Tanpa Divisi' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Priority <span class="text-red-500">*</span></label>
            <select name="priority" class="w-full border rounded-lg p-2" required>
                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Deadline <span class="text-red-500">*</span></label>
            <input type="datetime-local" name="deadline" value="{{ old('deadline') }}" class="w-full border rounded-lg p-2" required>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
            <a href="{{ route('hr.tasks.index') }}" class="bg-gray-300 px-4 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>

</main>
</div>
</body>
</html>