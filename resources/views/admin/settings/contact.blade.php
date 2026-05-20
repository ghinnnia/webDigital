<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pengaturan Kontak - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('logo1.jpeg') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "background-light": "#ffffff",
                        "text-light": "#1e293b",
                        "text-muted-light": "#64748b",
                        "border-light": "#e2e8f0",
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
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .panel {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
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
            flex-wrap: wrap;
            gap: 1rem;
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

        .panel-body {
            padding: 1.5rem;
        }

        .minimal-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 350px;
            border-left: 4px solid #10b981;
        }

        .minimal-popup.show {
            transform: translateX(0);
        }

        .minimal-popup.error {
            border-left-color: #ef4444;
        }

        .minimal-popup.warning {
            border-left-color: #f59e0b;
        }

        .minimal-popup-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .minimal-popup.success .minimal-popup-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .tab-container {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }

        .tab-button {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: #64748b;
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            white-space: nowrap;
            flex: 1;
        }

        .tab-button:hover {
            color: #3b82f6;
        }

        .tab-button.active {
            color: #3b82f6;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #3b82f6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .contact-preview,
        .about-preview {
            background-color: #f8fafc;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-icon {
            background-color: #0f172a;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            color: black;
            border-radius: 0.5rem;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            outline: none;
        }

        .whatsapp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            background-color: #f8fafc;
        }

        .whatsapp-btn .bx {
            margin-right: 8px;
            font-size: 20px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .close-modal {
            color: #64748b;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.5rem;
            padding: 0;
            line-height: 1;
        }

        .close-modal:hover {
            color: #1e293b;
        }

        .holiday-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .holiday-item:last-child {
            border-bottom: none;
        }

        .holiday-date {
            font-weight: 500;
            color: #1e293b;
        }

        .holiday-name {
            color: #64748b;
            margin-left: 10px;
        }

        .holiday-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .holiday-type.auto {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .holiday-type.manual {
            background-color: #dcfce7;
            color: #166534;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #3b82f6;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        /* Style for disabled delete button */
        .delete-disabled {
            color: #d1d5db !important;
            cursor: not-allowed !important;
            opacity: 0.5;
        }

        .delete-disabled:hover {
            color: #d1d5db !important;
        }

        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .app-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }

            .main-content {
                width: 100%;
            }

            .panel-header {
                padding: 0.75rem 1rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .panel-body {
                padding: 1rem;
            }

            .panel-title {
                font-size: 1rem;
            }

            /* Tab styles untuk mobile - PERUBAHAN DI SINI */
            .tab-button {
                padding: 0.5rem 0.25rem; /* Kurangi padding secara signifikan */
                font-size: 0.75rem; /* Kurangi ukuran font */
            }

            .minimal-popup {
                left: 20px;
                right: 20px;
                max-width: none;
                transform: translateY(-100px);
            }

            .minimal-popup.show {
                transform: translateY(0);
            }

            .modal-content {
                margin: 20% auto;
                width: 95%;
            }
        }

        @media (max-width: 480px) {
            .panel-header {
                padding: 0.5rem 0.75rem;
            }

            .panel-body {
                padding: 0.75rem;
            }

            .panel-title {
                font-size: 0.9rem;
            }

            /* Tab styles untuk layar sangat kecil - PERUBAHAN DI SINI */
            .tab-button {
                padding: 0.5rem 0.125rem; /* Kurangi padding lebih lanjut */
                font-size: 0.7rem; /* Kurangi ukuran font lebih lanjut */
            }

            .minimal-popup {
                top: 10px;
                left: 10px;
                right: 10px;
                padding: 12px 16px;
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen app-container">
        @include('admin/templet/sider')

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Pengaturan Kontak</h2>

                <!-- Tab Navigation -->
                <div class="tab-container">
                    <button class="tab-button active" data-tab="contact">Kontak</button>
                    <button class="tab-button" data-tab="about">Tentang</button>
                    <button class="tab-button" data-tab="operational">Jam Operasional</button>
                    <button class="tab-button" data-tab="articles">Artikel</button>
                    <button class="tab-button" data-tab="portfolios">Portofolio</button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content active" id="contact-tab">
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">contact_phone</span>
                                Informasi Kontak
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form id="contactForm" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" id="emailInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            value="{{ $contactData['email'] }}" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">No
                                            WhatsApp/Telepon</label>
                                        <input type="tel" name="phone" id="phoneInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            value="{{ $contactData['phone'] }}" required>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                        <textarea name="address" id="addressInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            rows="3" required>{{ $contactData['address'] }}</textarea>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan Default
                                            WhatsApp</label>
                                        <textarea name="whatsapp_message" id="whatsappMessageInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            rows="2" required>{{ $contactData['whatsapp_message'] }}</textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6">
                                    <button type="button" id="cancelContactBtn"
                                        class="px-4 py-2 btn-secondary rounded-lg w-full sm:w-auto">Batal</button>
                                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg w-full sm:w-auto">Simpan
                                        Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="about-tab">
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">info</span>
                                Informasi Tentang
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form id="aboutForm" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                                        <input type="text" name="title" id="titleInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            value="{{ $aboutData['title'] }}" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                        <textarea name="description" id="descriptionInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            rows="5" required>{{ $aboutData['description'] }}</textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6">
                                    <button type="button" id="cancelAboutBtn"
                                        class="px-4 py-2 btn-secondary rounded-lg w-full sm:w-auto">Batal</button>
                                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg w-full sm:w-auto">Simpan
                                        Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab Jam Operasional -->
                <div class="tab-content" id="operational-tab">
                    <div class="panel mb-6">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">schedule</span>
                                Pengaturan Jam Operasional
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form id="operationalForm" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                                        <input type="text" name="start_time" id="startTimeInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            value="08:00" placeholder="HH:mm" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric" required>
                                        <p class="text-xs text-gray-500 mt-1">Format 24 jam (HH:mm)</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang</label>
                                        <input type="text" name="end_time" id="endTimeInput"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            value="17:00" placeholder="HH:mm" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric" required>
                                        <p class="text-xs text-gray-500 mt-1">Format 24 jam (HH:mm)</p>
                                    </div>
                                </div>

                                <!-- Pengaturan Keterlambatan -->
                                <div class="border-t pt-4 mt-4">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <span class="material-icons-outlined text-warning mr-2">warning</span>
                                        Pengaturan Keterlambatan
                                    </h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Batas Terlambat (24 jam)</label>
                                            <input type="text" name="late_limit_time" id="lateLimitTimeInput"
                                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                                value="09:05" placeholder="HH:mm" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric" required>
                                            <p class="text-xs text-gray-500 mt-1">Gunakan format 24 jam (HH:mm)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6">
                                    <button type="button" id="cancelOperationalBtn"
                                        class="px-4 py-2 btn-secondary rounded-lg w-full sm:w-auto">Batal</button>
                                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg w-full sm:w-auto">Simpan
                                        Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">event</span>
                                Jadwal Libur
                            </h3>
                            <button id="addHolidayBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">add</span>
                                <span>Tambah Libur</span>
                            </button>
                        </div>
                        <div class="panel-body">
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700 mr-2">Libur Otomatis:</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="autoHolidayToggle" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <span class="text-xs text-gray-500">Libur otomatis akan diambil dari kalender nasional</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-700">Tanggal</th>
                                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-700">Nama Libur</th>
                                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-700">Tipe</th>
                                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-700">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="holidayList">
                                        <tr class="border-b border-gray-100 holiday-auto">
                                            <td class="py-2 px-2 text-sm">01 Januari 2024</td>
                                            <td class="py-2 px-2 text-sm">Tahun Baru Masehi</td>
                                            <td class="py-2 px-2">
                                                <span class="holiday-type auto">Otomatis</span>
                                            </td>
                                            <td class="py-2 px-2">
                                                <button class="delete-disabled" title="Libur otomatis tidak dapat dihapus">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 holiday-auto">
                                            <td class="py-2 px-2 text-sm">17 Agustus 2024</td>
                                            <td class="py-2 px-2 text-sm">Hari Kemerdekaan</td>
                                            <td class="py-2 px-2">
                                                <span class="holiday-type auto">Otomatis</span>
                                            </td>
                                            <td class="py-2 px-2">
                                                <button class="delete-disabled" title="Libur otomatis tidak dapat dihapus">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 holiday-auto">
                                            <td class="py-2 px-2 text-sm">25 Desember 2024</td>
                                            <td class="py-2 px-2 text-sm">Hari Raya Natal</td>
                                            <td class="py-2 px-2">
                                                <span class="holiday-type auto">Otomatis</span>
                                            </td>
                                            <td class="py-2 px-2">
                                                <button class="delete-disabled" title="Libur otomatis tidak dapat dihapus">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 holiday-manual">
                                            <td class="py-2 px-2 text-sm">15 Juni 2024</td>
                                            <td class="py-2 px-2 text-sm">Libur Khusus</td>
                                            <td class="py-2 px-2">
                                                <span class="holiday-type manual">Manual</span>
                                            </td>
                                            <td class="py-2 px-2">
                                                <button class="text-red-500 hover:text-red-700 delete-holiday">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <div class="tab-content" id="articles-tab">
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">article</span>
                                Pengaturan Artikel
                            </h3>
                            <a href="{{ route('admin.settings.articles') }}"
                                class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">settings</span>
                                <span class="hidden sm:inline">Kelola Artikel</span>
                                <span class="sm:hidden">Artikel</span>
                            </a>
                        </div>
                        <div class="panel-body">
                            <p class="text-gray-600 mb-4">Kelola artikel yang akan ditampilkan di halaman landing page.
                                Anda dapat menambah, mengedit, atau menghapus artikel.</p>
                            <p class="text-gray-600">Klik tombol "Kelola Artikel" untuk mengatur artikel secara lengkap.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan konten tab baru -->
                <div class="tab-content" id="portfolios-tab">
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">work</span>
                                Pengaturan Portofolio
                            </h3>
                            <a href="{{ route('admin.settings.portfolios') }}"
                                class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">settings</span>
                                <span class="hidden sm:inline">Kelola Portofolio</span>
                                <span class="sm:hidden">Portofolio</span>
                            </a>
                        </div>
                        <div class="panel-body">
                            <p class="text-gray-600 mb-4">Kelola portofolio yang akan ditampilkan di halaman landing
                                page.
                                Anda dapat menambah, mengedit, atau menghapus portofolio.</p>
                            <p class="text-gray-600">Klik tombol "Kelola Portofolio" untuk mengatur portofolio secara
                                lengkap.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Minimalist Popup -->
    <div id="minimalPopup" class="minimal-popup">
        <div class="minimal-popup-icon">
            <span class="material-icons-outlined">check</span>
        </div>
        <div class="minimal-popup-content">
            <div class="minimal-popup-title">Berhasil</div>
            <div class="minimal-popup-message">Operasi berhasil dilakukan</div>
        </div>
        <button class="minimal-popup-close">
            <span class="material-icons-outlined text-sm">close</span>
        </button>
    </div>

    <!-- Modal Tambah Libur -->
    <div id="holidayModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Jadwal Libur</h4>
                <button class="close-modal" id="closeHolidayModal">&times;</button>
            </div>
            <form id="holidayForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Libur</label>
                    <input type="date" name="holiday_date" id="holidayDateInput"
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Libur</label>
                    <input type="text" name="holiday_name" id="holidayNameInput"
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                        placeholder="Contoh: Libur Khusus" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                    <textarea name="holiday_description" id="holidayDescriptionInput"
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                        rows="3" placeholder="Tambahkan keterangan jika diperlukan"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6">
                    <button type="button" id="cancelHolidayBtn"
                        class="px-4 py-2 btn-secondary rounded-lg w-full sm:w-auto">Batal</button>
                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg w-full sm:w-auto">Tambah Libur</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Minimalist Popup
            function showMinimalPopup(title, message, type = 'success') {
                const popup = document.getElementById('minimalPopup');
                const popupTitle = popup.querySelector('.minimal-popup-title');
                const popupMessage = popup.querySelector('.minimal-popup-message');
                const popupIcon = popup.querySelector('.minimal-popup-icon span');

                popupTitle.textContent = title;
                popupMessage.textContent = message;
                popup.className = 'minimal-popup show ' + type;

                if (type === 'success') {
                    popupIcon.textContent = 'check';
                } else if (type === 'error') {
                    popupIcon.textContent = 'error';
                } else if (type === 'warning') {
                    popupIcon.textContent = 'warning';
                }

                setTimeout(() => {
                    popup.classList.remove('show');
                }, 3000);
            }

            // Close popup
            document.querySelector('.minimal-popup-close').addEventListener('click', function () {
                document.getElementById('minimalPopup').classList.remove('show');
            });

            // Tab Navigation
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                    this.classList.add('active');
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });

            // Contact Form
            document.getElementById('contactForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menyimpan...';
                submitBtn.disabled = true;

                const formData = new FormData(this);

                try {
                    const response = await fetch("/admin/settings/contact", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            "Accept": "application/json"
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && res.errors) {
                            const message = Object.values(res.errors)[0][0];
                            showMinimalPopup('Validasi Gagal', message, 'warning');
                            return;
                        }

                        showMinimalPopup('Error', res.message || 'Terjadi kesalahan', 'error');
                        return;
                    }

                    showMinimalPopup('Berhasil', res.message, 'success');
                } catch (error) {
                    console.error(error);
                    showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });

            // About Form
            document.getElementById('aboutForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menyimpan...';
                submitBtn.disabled = true;

                const formData = new FormData(this);

                try {
                    const response = await fetch("/admin/settings/about", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            "Accept": "application/json"
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && res.errors) {
                            const message = Object.values(res.errors)[0][0];
                            showMinimalPopup('Validasi Gagal', message, 'warning');
                            return;
                        }

                        showMinimalPopup('Error', res.message || 'Terjadi kesalahan', 'error');
                        return;
                    }

                    showMinimalPopup('Berhasil', res.message, 'success');
                } catch (error) {
                    console.error(error);
                    showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });

            // Operational Form - UPDATE KODE DI SINI
            document.getElementById('operationalForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menyimpan...';
                submitBtn.disabled = true;

                const formData = new FormData(this);
                const data = {
                    start_time: formData.get('start_time'),
                    end_time: formData.get('end_time'),
                    late_limit_time: formData.get('late_limit_time'),
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                try {
                    const response = await fetch("{{ route('admin.settings.operational.hours.update') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });

                    const res = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && res.errors) {
                            const message = Object.values(res.errors)[0][0];
                            showMinimalPopup('Validasi Gagal', message, 'warning');
                            return;
                        }

                        showMinimalPopup('Error', res.message || 'Terjadi kesalahan', 'error');
                        return;
                    }

                    showMinimalPopup('Berhasil', res.message, 'success');
                } catch (error) {
                    console.error(error);
                    showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });

            // Load existing operational hours when page loads - TAMBAHKAN KODE INI
            (async function loadOperationalHours() {
                try {
                    const response = await fetch("{{ route('admin.settings.operational.hours') }}", {
                        headers: { "Accept": "application/json" }
                    });
                    const contentType = response.headers.get('content-type') || '';
                    if (!response.ok || !contentType.includes('application/json')) {
                        throw new Error(`Invalid response (${response.status})`);
                    }
                    const res = await response.json();

                    if (res.success && res.data) {
                        document.getElementById('startTimeInput').value = res.data.start_time;
                        document.getElementById('endTimeInput').value = res.data.end_time;
                        document.getElementById('lateLimitTimeInput').value = res.data.late_limit_time
                            || `${String(res.data.late_limit_hour ?? 9).padStart(2, '0')}:${String(res.data.late_limit_minute ?? 5).padStart(2, '0')}`;
                    }
                } catch (error) {
                    console.error('Error loading operational hours:', error);
                }
            })();

            // Holiday Modal
            const holidayModal = document.getElementById('holidayModal');
            const addHolidayBtn = document.getElementById('addHolidayBtn');
            const closeHolidayModal = document.getElementById('closeHolidayModal');
            const cancelHolidayBtn = document.getElementById('cancelHolidayBtn');

            // Open modal
            if (addHolidayBtn && holidayModal) {
                addHolidayBtn.addEventListener('click', function () {
                    holidayModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                });
            }

            // Close modal
            if (closeHolidayModal && holidayModal) {
                closeHolidayModal.addEventListener('click', function () {
                    holidayModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            }

            if (cancelHolidayBtn && holidayModal) {
                cancelHolidayBtn.addEventListener('click', function () {
                    holidayModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            }

            // Close modal when clicking outside
            if (holidayModal) {
                window.addEventListener('click', function (event) {
                    if (event.target === holidayModal) {
                        holidayModal.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }
                });
            }

            // Holiday Form
            const holidayForm = document.getElementById('holidayForm');
            if (holidayForm) holidayForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menambahkan...';
                submitBtn.disabled = true;

                const formData = new FormData(this);
                const holidayDate = formData.get('holiday_date');
                const holidayName = formData.get('holiday_name');
                const holidayDescription = formData.get('holiday_description');

                try {
                    // Simulasi API call
                    setTimeout(() => {
                        // Add new holiday to the table
                        const holidayList = document.getElementById('holidayList');
                        const newHolidayRow = document.createElement('tr');
                        newHolidayRow.className = 'border-b border-gray-100 holiday-manual';
                        
                        // Format date
                        const date = new Date(holidayDate);
                        const formattedDate = date.toLocaleDateString('id-ID', { 
                            day: 'numeric', 
                            month: 'long', 
                            year: 'numeric' 
                        });
                        
                        newHolidayRow.innerHTML = `
                            <td class="py-2 px-2 text-sm">${formattedDate}</td>
                            <td class="py-2 px-2 text-sm">${holidayName}</td>
                            <td class="py-2 px-2">
                                <span class="holiday-type manual">Manual</span>
                            </td>
                            <td class="py-2 px-2">
                                <button class="text-red-500 hover:text-red-700 delete-holiday">
                                    <span class="material-icons-outlined text-sm">delete</span>
                                </button>
                            </td>
                        `;
                        
                        holidayList.appendChild(newHolidayRow);
                        
                        // Add event listener to delete button
                        newHolidayRow.querySelector('.delete-holiday').addEventListener('click', function() {
                            if (confirm('Apakah Anda yakin ingin menghapus jadwal libur ini?')) {
                                newHolidayRow.remove();
                                showMinimalPopup('Berhasil', 'Jadwal libur berhasil dihapus', 'success');
                            }
                        });
                        
                        // Close modal and reset form
                        holidayModal.style.display = 'none';
                        document.body.style.overflow = 'auto';
                        this.reset();
                        
                        showMinimalPopup('Berhasil', 'Jadwal libur berhasil ditambahkan', 'success');
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    }, 1000);
                } catch (error) {
                    console.error(error);
                    showMinimalPopup('Error', 'Terjadi kesalahan server', 'error');
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });

            // Delete holiday buttons for manual holidays only
            document.querySelectorAll('.delete-holiday').forEach(button => {
                button.addEventListener('click', function () {
                    if (confirm('Apakah Anda yakin ingin menghapus jadwal libur ini?')) {
                        this.closest('tr').remove();
                        showMinimalPopup('Berhasil', 'Jadwal libur berhasil dihapus', 'success');
                    }
                });
            });

            // Show tooltip for disabled delete buttons
            document.querySelectorAll('.delete-disabled').forEach(button => {
                button.addEventListener('click', function () {
                    showMinimalPopup('Info', 'Libur otomatis dari kalender tidak dapat dihapus', 'warning');
                });
            });

            // Auto holiday toggle
            const autoHolidayToggle = document.getElementById('autoHolidayToggle');
            if (autoHolidayToggle) {
                autoHolidayToggle.addEventListener('change', function () {
                    const isChecked = this.checked;
                    const message = isChecked ?
                        'Libur otomatis diaktifkan. Libur akan diambil dari kalender nasional.' :
                        'Libur otomatis dinonaktifkan. Hanya libur manual yang akan ditampilkan.';

                    showMinimalPopup('Info', message, 'success');
                });
            }

            // Cancel buttons
            document.getElementById('cancelContactBtn').addEventListener('click', function () {
                location.reload();
            });

            document.getElementById('cancelAboutBtn').addEventListener('click', function () {
                location.reload();
            });

            document.getElementById('cancelOperationalBtn').addEventListener('click', function () {
                location.reload();
            });

            // Tambahkan fungsi untuk memuat portofolio
            function loadPortfolios() {
                console.log("Memuat data portofolio...");
                fetch('/api/portfolios')
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`Network response was not ok: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data portofolio yang diterima:', data);

                        const portfolioContainer = document.querySelector('.portfolio-container > div');
                        if (!portfolioContainer) {
                            console.error("KESALAH KRITIS: Container portofolio tidak ditemukan! Portofolio tidak akan dimuat.");
                            return;
                        }

                        portfolioContainer.innerHTML = '';

                        if (data.success && data.data && data.data.length > 0) {
                            data.data.forEach(portfolio => {
                                const techArray = portfolio.technologies_used ? portfolio.technologies_used.split(',').map(tech => tech.trim()) : [];
                                const techString = techArray.join(', ');

                                const portfolioHtml = `
                            <div class="portfolio-card bg-card-light p-6 rounded-2xl flex flex-col w-72 shadow-sm border border-border-light">
                                <div class="relative flex-grow aspect-[4/5] bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg mb-4 overflow-hidden">
                                    ${portfolio.image ?
                                        `<img src="/storage/${portfolio.image}" alt="${portfolio.title}" class="w-full h-full object-cover">` :
                                        `<div class="flex items-center justify-center h-full text-white"><span class="material-icons-outlined text-4xl">work</span></div>`
                                    }
                                    <button class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm w-8 h-8 rounded-full flex items-center justify-center text-black hover:bg-white transition-colors">
                                        <span class="material-icons-outlined text-base">arrow_forward</span>
                                    </button>
                                </div>
                                <h3 class="font-bold text-white text-lg mb-4">${portfolio.title}</h3>
                                <button class="w-full btn-primary bg-black text-white text-sm font-medium py-2 px-4 rounded-lg flex justify-between items-center portfolio-btn" data-title="${portfolio.title}" data-description="${portfolio.description}" data-tech="${techString}">
                                    <span>Lihat Detail</span>
                                    <span class="material-icons-outlined text-base">chevron_right</span>
                                </button>
                            </div>
                        `;
                                portfolioContainer.innerHTML += portfolioHtml;
                            });

                            // Re-attach event listeners to new portfolio buttons
                            attachPortfolioEventListeners();
                        } else {
                            portfolioContainer.innerHTML = `
                        <div class="col-span-2 text-center py-12">
                            <span class="material-icons-outlined text-6xl text-gray-300">work</span>
                            <h3 class="text-xl font-semibold text-gray-500 mt-4">Belum Ada Portofolio</h3>
                            <p class="text-gray-400 mt-2">Portofolio akan segera tersedia. Silakan kunjungi kembali nanti.</p>
                        </div>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching portfolios:', error);
                        const portfolioContainer = document.querySelector('.portfolio-container > div');
                        if (portfolioContainer) {
                            portfolioContainer.innerHTML = `
                        <div class="col-span-2 text-center py-12">
                            <span class="material-icons-outlined text-6xl text-red-300">error_outline</span>
                            <h3 class="text-xl font-semibold text-red-500 mt-4">Gagal Memuat Portofolio</h3>
                            <p class="text-red-400 mt-2">Terjadi kesalahan, silakan refresh halaman.</p>
                        </div>
                    `;
                        }
                    });
            }

            // Fungsi untuk menambahkan event listener ke tombol portofolio
            function attachPortfolioEventListeners() {
                const portfolioBtns = document.querySelectorAll('.portfolio-btn');
                if (portfolioBtns) {
                    portfolioBtns.forEach(btn => {
                        btn.addEventListener('click', function () {
                            const title = this.getAttribute('data-title');
                            const description = this.getAttribute('data-description');
                            const tech = this.getAttribute('data-tech').split(', ');

                            if (portfolioModal) {
                                const modalTitle = document.getElementById('modalTitle');
                                const modalDescription = document.getElementById('modalDescription');
                                const modalTech = document.getElementById('modalTech');

                                if (modalTitle) modalTitle.textContent = title;
                                if (modalDescription) modalDescription.textContent = description;

                                if (modalTech) {
                                    modalTech.innerHTML = '';
                                    tech.forEach(techItem => {
                                        const techBadge = document.createElement('span');
                                        techBadge.className = 'bg-gray-700 text-white text-sm px-3 py-1 rounded-full';
                                        techBadge.textContent = techItem;
                                        modalTech.appendChild(techBadge);
                                    });
                                }

                                portfolioModal.style.display = 'block';
                                document.body.style.overflow = 'hidden';
                            } else {
                                console.error("KESALAH KRITIS: Modal portofolio tidak ditemukan.");
                            }
                        });
                    });
                }
            }

            // Panggil fungsi untuk memuat portofolio
            loadPortfolios();
        });
    </script>
</body>

</html>
