<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Karyawan - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#ffffff",
                        "background-dark": "#f8fafc",
                        "sidebar-light": "#f3f4f6",
                        "sidebar-dark": "#1e293b",
                        "card-light": "#ffffff",
                        "card-dark": "#1e293b",
                        "text-light": "#1e293b",
                        "text-dark": "#f8fafc",
                        "text-muted-light": "#64748b",
                        "text-muted-dark": "#94a3b8",
                        "border-light": "#e2e8f0",
                        "border-dark": "#334155",
                        "success": "#10b981",
                        "warning": "#f59e0b",
                        "danger": "#ef4444"
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                    boxShadow: {
                        card: "0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)",
                        "card-hover": "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)"
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .material-icons-outlined { font-size: 24px; vertical-align: middle; }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
            outline: none;
        }

        .panel {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .panel-header {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .panel-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .panel-body { padding: 1.5rem; }

        .scrollable-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
            -webkit-overflow-scrolling: touch;
        }
        .scrollable-table-container::-webkit-scrollbar { height: 12px; }
        .scrollable-table-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 6px; }
        .scrollable-table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; border: 2px solid #f1f5f9; }
        .scrollable-table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .data-table {
            width: 100%;
            min-width: 1100px;
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
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
        }
        .data-table tbody tr:nth-child(even) { background: #f9fafb; }
        .data-table tbody tr:hover { background: #eff6ff; transition: background 0.15s; }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    @include('general_manajer.templet.header')

    <div class="main-content">
        <main class="flex-1 flex flex-col bg-background-light">
            <div class="flex-1 p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Data Karyawan</h2>

                {{-- ── Flash Messages ── --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════
                     FORM SEARCH & FILTER
                     • Search   → users.name / users.email
                     • Dropdown → tabel divisi (id, divisi)
                     • $item->nama_divisi dipakai di tabel (alias dari JOIN)
                ══════════════════════════════════════════════ --}}
                <form method="GET"
                      action="{{ route('general_manajer.data_karyawan') }}"
                      class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">

                    {{-- Kolom pencarian --}}
                    <div class="relative w-full md:w-1/3">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input
                            name="search"
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg
                                   focus:ring-2 focus:ring-primary focus:border-primary form-input"
                            placeholder="Cari nama atau email..."
                            type="text"
                        />
                    </div>

                    <div class="flex flex-wrap gap-3 w-full md:w-auto items-center">

                        {{-- ✅ DROPDOWN DIVISI
                             Sumber: $divisionsDropdown dari DB::table('divisi') di controller.
                             Ada fallback query langsung di Blade sebagai solusi anti-gagal
                             jika route/controller yang aktif di web web.php keliru/belum terupdate. --}}
                        <select
                            name="divisi"
                            class="w-full sm:w-auto px-3 py-2 bg-white border border-border-light
                                   text-gray-700 rounded-lg focus:ring-2 focus:ring-primary
                                   focus:border-primary form-input min-w-[160px]">

                            <option value="">Semua Divisi</option>

                            @php
                                // Solusi Cadangan (Fallback): Jika variabel tidak dilempar dari controller,
                                // lakukan query langsung ke DB agar data divisi di dropdown pasti muncul.
                                if (!isset($divisionsDropdown) || collect($divisionsDropdown)->isEmpty()) {
                                    $divisionsDropdown = \Illuminate\Support\Facades\DB::table('divisi')
                                                           ->select('id', 'divisi')
                                                           ->orderBy('divisi', 'asc')
                                                           ->get();
                                }
                                $divList = collect($divisionsDropdown);
                            @endphp

                            @if ($divList->count() > 0)
                                @foreach ($divList as $d)
                                    <option value="{{ $d->id }}"
                                        {{ (string) request('divisi') === (string) $d->id ? 'selected' : '' }}>
                                        {{ $d->divisi }}
                                    </option>
                                @endforeach
                            @else
                                {{-- Baris ini hanya muncul jika tabel divisi benar-benar kosong --}}
                                <option disabled>— Belum ada divisi —</option>
                            @endif
                        </select>

                        <button
                            type="submit"
                            class="w-full sm:w-auto px-5 py-2 bg-primary text-white font-medium
                                   rounded-lg hover:bg-blue-700 transition-colors">
                            Cari &amp; Filter
                        </button>

                        {{-- Tombol Reset — hanya muncul jika ada filter aktif --}}
                        @if (request('search') || request('divisi'))
                            <a href="{{ route('general_manajer.data_karyawan') }}"
                               class="w-full sm:w-auto px-5 py-2 bg-gray-200 text-gray-700 font-medium
                                      rounded-lg hover:bg-gray-300 transition-colors text-center">
                                Reset
                            </a>
                        @endif

                    </div>
                </form>

                {{-- ══════════════════════════════════════════════
                     PANEL TABEL DATA KARYAWAN
                     Catatan penting untuk kolom DIVISI:
                     - $item->nama_divisi  → nama divisi dari JOIN (selalu string/null)
                     - JANGAN pakai $item->divisi karena itu kolom string lama di tabel users
                ══════════════════════════════════════════════ --}}
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">people</span>
                            Daftar Karyawan
                        </h3>
                        <span class="text-sm text-text-muted-light">
                            Total: <span class="font-semibold text-text-light">{{ $karyawan->total() }}</span> karyawan
                        </span>
                    </div>
                    <div class="panel-body">

                        {{-- ── Desktop Table ── --}}
                        <div class="hidden sm:block">
                            <div class="scrollable-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="width:55px;">No</th>
                                            <th style="min-width:190px;">Nama Lengkap</th>
                                            <th style="min-width:130px;">Role</th>
                                            <th style="min-width:195px;">Email</th>
                                            <th style="min-width:150px;">Divisi</th>
                                            <th style="min-width:260px;">Alamat</th>
                                            <th style="min-width:145px;">Nomor Kontak</th>
                                            <th style="min-width:120px;">Status Kerja</th>
                                            <th style="min-width:135px;">Status Karyawan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($karyawan as $index => $item)
                                        <tr>
                                            {{-- Nomor urut dengan perhitungan halaman --}}
                                            <td class="text-gray-400 text-sm">
                                                {{ ($karyawan->currentPage() - 1) * $karyawan->perPage() + $index + 1 }}
                                            </td>

                                            {{-- Nama — dari users.name, di-alias sebagai `nama` di controller --}}
                                            <td class="font-semibold text-gray-900">
                                                {{ $item->nama ?? '-' }}
                                            </td>

                                            {{-- Role --}}
                                            <td>
                                                <span class="status-badge
                                                    {{ $item->role === 'manager_divisi'
                                                        ? 'bg-purple-100 text-purple-800'
                                                        : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $item->role ?? '-' }}
                                                </span>
                                            </td>

                                            {{-- Email --}}
                                            <td class="text-gray-600 text-sm">{{ $item->email ?? '-' }}</td>

                                            {{-- ✅ Divisi — pakai $item->nama_divisi (alias JOIN)
                                                 BUKAN $item->divisi agar tidak error --}}
                                            <td class="text-gray-700">
                                                {{ $item->divisi ?? '-' }}
                                            </td>

                                            {{-- Alamat --}}
                                            <td class="text-gray-600 text-sm"
                                                title="{{ $item->alamat }}">
                                                {{ \Illuminate\Support\Str::limit($item->alamat ?? '-', 45) }}
                                            </td>

                                            {{-- Kontak --}}
                                            <td class="text-gray-600 text-sm">{{ $item->kontak ?? '-' }}</td>

                                            {{-- Status Kerja --}}
                                            <td>
                                                @php
                                                    $warna_kerja = match($item->status_kerja) {
                                                        'aktif'    => 'bg-green-100 text-green-800',
                                                        'resign'   => 'bg-yellow-100 text-yellow-800',
                                                        'phk'      => 'bg-orange-100 text-orange-800',
                                                        default    => 'bg-red-100 text-red-800',
                                                    };
                                                @endphp
                                                <span class="status-badge {{ $warna_kerja }}">
                                                    {{ $item->status_kerja ?? '-' }}
                                                </span>
                                            </td>

                                            {{-- Status Karyawan --}}
                                            <td>
                                                @php
                                                    $warna_karyawan = match($item->status_karyawan) {
                                                        'tetap'     => 'bg-blue-100 text-blue-800',
                                                        'kontrak'   => 'bg-purple-100 text-purple-800',
                                                        'freelance' => 'bg-indigo-100 text-indigo-800',
                                                        default     => 'bg-gray-100 text-gray-600',
                                                    };
                                                @endphp
                                                <span class="status-badge {{ $warna_karyawan }}">
                                                    {{ $item->status_karyawan ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-10 text-gray-400">
                                                <span class="material-icons-outlined block text-4xl mb-2">manage_search</span>
                                                Tidak ada data karyawan yang sesuai.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ── Mobile Card View ── --}}
                        <div class="block sm:hidden space-y-4">
                            @forelse ($karyawan as $item)
                            <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-base text-gray-900">{{ $item->nama ?? '-' }}</h4>
                                        <span class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block
                                            {{ $item->role === 'manager_divisi' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $item->role ?? '-' }}
                                        </span>
                                    </div>
                                    @php
                                        $wk = match($item->status_kerja) {
                                            'aktif'  => 'bg-green-100 text-green-800',
                                            'resign' => 'bg-yellow-100 text-yellow-800',
                                            'phk'    => 'bg-orange-100 text-orange-800',
                                            default  => 'bg-red-100 text-red-800',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $wk }}">{{ $item->status_kerja ?? '-' }}</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-text-muted-light text-xs mb-0.5">Email</p>
                                        <p class="font-medium break-all text-gray-700">{{ $item->email ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light text-xs mb-0.5">Divisi</p>
                                        {{-- ✅ Pakai nama_divisi bukan divisi --}}
                                        <p class="font-medium text-gray-700">{{ $item->nama_divisi ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light text-xs mb-0.5">No. Kontak</p>
                                        <p class="font-medium text-gray-700">{{ $item->kontak ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light text-xs mb-0.5">Status Karyawan</p>
                                        @php
                                            $wkr = match($item->status_karyawan) {
                                                'tetap'     => 'bg-blue-100 text-blue-800',
                                                'kontrak'   => 'bg-purple-100 text-purple-800',
                                                'freelance' => 'bg-indigo-100 text-indigo-800',
                                                default     => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="status-badge {{ $wkr }}">{{ $item->status_karyawan ?? '-' }}</span>
                                    </div>
                                </div>

                                @if ($item->alamat)
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    <p class="text-text-muted-light text-xs mb-0.5">Alamat</p>
                                    <p class="font-medium text-gray-700 text-sm">{{ $item->alamat }}</p>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-10 text-gray-400">
                                <span class="material-icons-outlined block text-4xl mb-2">manage_search</span>
                                Tidak ada data karyawan yang sesuai.
                            </div>
                            @endforelse
                        </div>

                        {{-- ── Pagination ── --}}
                        <div class="mt-6">
                            {{ $karyawan->appends(request()->query())->links() }}
                        </div>

                    </div>
                </div>

            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2026 by digicity.id
            </footer>
        </main>
    </div>
</body>
</html>