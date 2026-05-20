{{-- resources/views/hr/tasks/edit.blade.php --}}

@include('hr.templet.sider')

<main class="flex-1 flex flex-col bg-background-light main-content">
    <div class="flex-1 p-6 sm:p-8">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('hr.tasks.index') }}"
               class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <span class="material-icons-outlined">arrow_back</span>
                Kembali
            </a>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-bold mb-6">Edit Tugas</h2>

        <!-- Error -->
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('hr.tasks.update', $task->id) }}" method="POST"
              class="bg-white rounded-lg shadow-sm border p-6">

            @csrf
            @method('PUT')

            <!-- Judul -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Judul Tugas <span class="text-red-500">*</span>
                </label>
                <input type="text" name="judul"
                       value="{{ old('judul', $task->judul) }}"
                       class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Deskripsi -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $task->deskripsi) }}</textarea>
            </div>

            <!-- Karyawan -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Pilih Karyawan <span class="text-red-500">*</span>
                </label>
                <select name="karyawan_id"
                        class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawan as $k)
                        <option value="{{ $k->id }}"
                            {{ old('karyawan_id', $task->assigned_to) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} - {{ $k->divisi ?? 'Tanpa Divisi' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Priority -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Priority <span class="text-red-500">*</span>
                </label>
                <select name="priority"
                        class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            <!-- Deadline -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">
                    Deadline <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" name="deadline"
                       value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d\TH:i') : '') }}"
                       class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Update
                </button>

                <a href="{{ route('hr.tasks.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg">
                    Batal
                </a>
            </div>
        </form>

    </div>
</main>