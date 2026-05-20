<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pengaturan Jam Kerja & Libur - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link rel="icon" type="image/png" href="logo1.jpeg">
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

        .holiday-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .holiday-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .holiday-type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .holiday-automatic {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .holiday-manual {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }

        .calendar-container {
            width: 100%;
            max-width: 500px;
            margin-right: 1.5rem;
        }

        .year-selector {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .year-nav-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .year-nav-btn:hover:not(:disabled) {
            background-color: #e2e8f0;
        }

        .year-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .year-display {
            font-weight: 600;
            font-size: 1rem;
        }

        .months-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .month-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .month-header {
            background-color: #f8fafc;
            padding: 0.25rem 0.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .mini-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #e2e8f0;
            padding: 1px;
        }

        .mini-calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            font-size: 0.625rem;
            font-weight: 500;
        }

        .mini-calendar-day.other-month {
            color: #cbd5e1;
        }

        .mini-calendar-day.has-holiday {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 600;
        }

        .mini-calendar-day.today {
            background-color: #3b82f6;
            color: white;
        }

        .holiday-list-container {
            flex: 1;
            overflow-y: auto;
            max-height: 600px;
        }

        .holiday-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .holiday-filter {
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #f1f5f9;
            color: #64748b;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn.active {
            background-color: #3b82f6;
            color: white;
        }

        /* Mobile responsive styles */
        @media (max-width: 1024px) {
            .calendar-container {
                max-width: 400px;
            }
            
            .months-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

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

            .tab-button {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
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

            .calendar-container {
                max-width: 100%;
                margin-right: 0;
                margin-bottom: 1.5rem;
            }
            
            .months-grid {
                grid-template-columns: repeat(1, 1fr);
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

            .tab-button {
                padding: 0.5rem 0.125rem;
                font-size: 0.7rem;
            }

            .minimal-popup {
                top: 10px;
                left: 10px;
                right: 10px;
                padding: 12px 16px;
            }

            .holiday-card {
                padding: 0.75rem;
            }
        }
    </style>
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen app-container">
        <!-- Sidebar -->
        <aside class="sidebar w-64 bg-white shadow-md h-screen">
            <div class="p-4">
                <div class="flex items-center mb-8">
                    <img src="logo1.jpeg" alt="Logo" class="w-10 h-10 mr-3">
                    <h1 class="text-xl font-bold">Admin Dashboard</h1>
                </div>
                <nav class="space-y-2">
                    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-100">
                        <span class="material-icons-outlined mr-3">dashboard</span>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center p-3 rounded-lg bg-blue-50 text-blue-600">
                        <span class="material-icons-outlined mr-3">settings</span>
                        Pengaturan
                    </a>
                    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-100">
                        <span class="material-icons-outlined mr-3">people</span>
                        Karyawan
                    </a>
                    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-100">
                        <span class="material-icons-outlined mr-3">assessment</span>
                        Laporan
                    </a>
                    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-100">
                        <span class="material-icons-outlined mr-3">logout</span>
                        Keluar
                    </a>
                </nav>
            </div>
        </aside>

        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Pengaturan Jam Kerja & Libur</h2>

                <!-- Tab Navigation -->
                <div class="tab-container">
                    <button class="tab-button active" data-tab="workhours">Jam Kerja</button>
                    <button class="tab-button" data-tab="holidays">Libur</button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content active" id="workhours-tab">
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">schedule</span>
                                Pengaturan Jam Kerja
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                                        <input type="time" name="check_in_time" id="checkInTime"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            value="08:00" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang</label>
                                        <input type="time" name="check_out_time" id="checkOutTime"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            value="17:00" required>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                        <textarea name="workhours_description" id="workhoursDescription"
                                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                            rows="2">Jam kerja standar dari Senin hingga Jumat</textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6">
                                    <button type="button" class="px-4 py-2 btn-secondary rounded-lg w-full sm:w-auto">Batal</button>
                                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg w-full sm:w-auto">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="holidays-tab">
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <span class="material-icons-outlined text-primary">event</span>
                                Pengaturan Hari Libur
                            </h3>
                            <button class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">add</span>
                                <span class="hidden sm:inline">Tambah Libur Manual</span>
                                <span class="sm:hidden">Tambah</span>
                            </button>
                        </div>
                        <div class="panel-body">
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Calendar View -->
                                <div class="calendar-container">
                                    <div class="year-selector">
                                        <button class="year-nav-btn">
                                            <span class="material-icons-outlined text-sm">chevron_left</span>
                                        </button>
                                        <div class="year-display">2023</div>
                                        <button class="year-nav-btn">
                                            <span class="material-icons-outlined text-sm">chevron_right</span>
                                        </button>
                                    </div>
                                    <div class="months-grid">
                                        <!-- January -->
                                        <div class="month-card">
                                            <div class="month-header">Januari</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day">31</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                            </div>
                                        </div>
                                        
                                        <!-- February -->
                                        <div class="month-card">
                                            <div class="month-header">Februari</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">29</div>
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day other-month">31</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                            </div>
                                        </div>
                                        
                                        <!-- March -->
                                        <div class="month-card">
                                            <div class="month-header">Maret</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">26</div>
                                                <div class="mini-calendar-day other-month">27</div>
                                                <div class="mini-calendar-day other-month">28</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day has-holiday">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day">31</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                            </div>
                                        </div>
                                        
                                        <!-- April -->
                                        <div class="month-card">
                                            <div class="month-header">April</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">31</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day has-holiday">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                            </div>
                                        </div>
                                        
                                        <!-- May -->
                                        <div class="month-card">
                                            <div class="month-header">Mei</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day">31</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                            </div>
                                        </div>
                                        
                                        <!-- June -->
                                        <div class="month-card">
                                            <div class="month-header">Juni</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">28</div>
                                                <div class="mini-calendar-day other-month">29</div>
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day other-month">31</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day has-holiday">23</div>
                                                <div class="mini-calendar-day has-holiday">24</div>
                                                <div class="mini-calendar-day has-holiday">25</div>
                                                <div class="mini-calendar-day has-holiday">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                                <div class="mini-calendar-day other-month">7</div>
                                            </div>
                                        </div>
                                        
                                        <!-- July -->
                                        <div class="month-card">
                                            <div class="month-header">Juli</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">25</div>
                                                <div class="mini-calendar-day other-month">26</div>
                                                <div class="mini-calendar-day other-month">27</div>
                                                <div class="mini-calendar-day other-month">28</div>
                                                <div class="mini-calendar-day other-month">29</div>
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day">31</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                            </div>
                                        </div>
                                        
                                        <!-- August -->
                                        <div class="month-card">
                                            <div class="month-header">Agustus</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">31</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day has-holiday">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day">31</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                                <div class="mini-calendar-day other-month">7</div>
                                                <div class="mini-calendar-day other-month">8</div>
                                            </div>
                                        </div>
                                        
                                        <!-- September -->
                                        <div class="month-card">
                                            <div class="month-header">September</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">27</div>
                                                <div class="mini-calendar-day other-month">28</div>
                                                <div class="mini-calendar-day other-month">29</div>
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day other-month">31</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                            </div>
                                        </div>
                                        
                                        <!-- October -->
                                        <div class="month-card">
                                            <div class="month-header">Oktober</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">25</div>
                                                <div class="mini-calendar-day other-month">26</div>
                                                <div class="mini-calendar-day other-month">27</div>
                                                <div class="mini-calendar-day other-month">28</div>
                                                <div class="mini-calendar-day other-month">29</div>
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day">31</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                            </div>
                                        </div>
                                        
                                        <!-- November -->
                                        <div class="month-card">
                                            <div class="month-header">November</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">29</div>
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day other-month">31</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                                <div class="mini-calendar-day other-month">7</div>
                                                <div class="mini-calendar-day other-month">8</div>
                                                <div class="mini-calendar-day other-month">9</div>
                                            </div>
                                        </div>
                                        
                                        <!-- December -->
                                        <div class="month-card">
                                            <div class="month-header">Desember</div>
                                            <div class="mini-calendar-grid">
                                                <div class="mini-calendar-day other-month">26</div>
                                                <div class="mini-calendar-day other-month">27</div>
                                                <div class="mini-calendar-day other-month">28</div>
                                                <div class="mini-calendar-day other-month">29</div>
                                                <div class="mini-calendar-day other-month">30</div>
                                                <div class="mini-calendar-day">1</div>
                                                <div class="mini-calendar-day">2</div>
                                                <div class="mini-calendar-day">3</div>
                                                <div class="mini-calendar-day">4</div>
                                                <div class="mini-calendar-day">5</div>
                                                <div class="mini-calendar-day">6</div>
                                                <div class="mini-calendar-day">7</div>
                                                <div class="mini-calendar-day">8</div>
                                                <div class="mini-calendar-day">9</div>
                                                <div class="mini-calendar-day">10</div>
                                                <div class="mini-calendar-day">11</div>
                                                <div class="mini-calendar-day">12</div>
                                                <div class="mini-calendar-day">13</div>
                                                <div class="mini-calendar-day">14</div>
                                                <div class="mini-calendar-day">15</div>
                                                <div class="mini-calendar-day">16</div>
                                                <div class="mini-calendar-day">17</div>
                                                <div class="mini-calendar-day">18</div>
                                                <div class="mini-calendar-day">19</div>
                                                <div class="mini-calendar-day">20</div>
                                                <div class="mini-calendar-day">21</div>
                                                <div class="mini-calendar-day">22</div>
                                                <div class="mini-calendar-day">23</div>
                                                <div class="mini-calendar-day">24</div>
                                                <div class="mini-calendar-day has-holiday">25</div>
                                                <div class="mini-calendar-day">26</div>
                                                <div class="mini-calendar-day">27</div>
                                                <div class="mini-calendar-day">28</div>
                                                <div class="mini-calendar-day">29</div>
                                                <div class="mini-calendar-day">30</div>
                                                <div class="mini-calendar-day">31</div>
                                                <div class="mini-calendar-day other-month">1</div>
                                                <div class="mini-calendar-day other-month">2</div>
                                                <div class="mini-calendar-day other-month">3</div>
                                                <div class="mini-calendar-day other-month">4</div>
                                                <div class="mini-calendar-day other-month">5</div>
                                                <div class="mini-calendar-day other-month">6</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Holiday List -->
                                <div class="holiday-list-container">
                                    <div class="holiday-list-header">
                                        <h4 class="text-lg font-semibold">Daftar Hari Libur</h4>
                                        <div class="holiday-filter">
                                            <button class="filter-btn active">Semua</button>
                                            <button class="filter-btn">Otomatis</button>
                                            <button class="filter-btn">Manual</button>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Cuti Bersama Idul Fitri</h4>
                                                    <p class="text-sm text-gray-500">Jumat, 23 Juni 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-manual">Manual</span>
                                            </div>
                                            <p class="text-gray-600">Cuti bersama dalam rangka Idul Fitri 1444 Hijriah</p>
                                            <div class="flex justify-end mt-2">
                                                <button class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition-colors">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Hari Raya Idul Fitri 1444 H</h4>
                                                    <p class="text-sm text-gray-500">Sabtu, 24 Juni 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-automatic">Otomatis</span>
                                            </div>
                                            <p class="text-gray-600">Hari Raya Idul Fitri 1444 Hijriah</p>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Hari Raya Idul Fitri 1444 H</h4>
                                                    <p class="text-sm text-gray-500">Minggu, 25 Juni 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-automatic">Otomatis</span>
                                            </div>
                                            <p class="text-gray-600">Hari Raya Idul Fitri 1444 Hijriah (Hari ke-2)</p>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Cuti Bersama Idul Fitri</h4>
                                                    <p class="text-sm text-gray-500">Senin, 26 Juni 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-manual">Manual</span>
                                            </div>
                                            <p class="text-gray-600">Cuti bersama tambahan Idul Fitri</p>
                                            <div class="flex justify-end mt-2">
                                                <button class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition-colors">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Hari Raya Waisak</h4>
                                                    <p class="text-sm text-gray-500">Kamis, 1 Juni 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-automatic">Otomatis</span>
                                            </div>
                                            <p class="text-gray-600">Perayaan Tri Suci Waisak 2567 BE</p>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Hari Lahir Pancasila</h4>
                                                    <p class="text-sm text-gray-500">Kamis, 1 Juni 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-automatic">Otomatis</span>
                                            </div>
                                            <p class="text-gray-600">Memperingati Hari Lahir Pancasila</p>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Company Gathering</h4>
                                                    <p class="text-sm text-gray-500">Jumat, 9 Juni 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-manual">Manual</span>
                                            </div>
                                            <p class="text-gray-600">Acara tahunan perusahaan di Bandung</p>
                                            <div class="flex justify-end mt-2">
                                                <button class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition-colors">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Hari Kemerdekaan RI</h4>
                                                    <p class="text-sm text-gray-500">Kamis, 17 Agustus 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-automatic">Otomatis</span>
                                            </div>
                                            <p class="text-gray-600">Hari Kemerdekaan Republik Indonesia ke-78</p>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Hari Natal</h4>
                                                    <p class="text-sm text-gray-500">Senin, 25 Desember 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-automatic">Otomatis</span>
                                            </div>
                                            <p class="text-gray-600">Perayaan Hari Natal 2023</p>
                                        </div>

                                        <div class="holiday-card">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">Libur Akhir Tahun</h4>
                                                    <p class="text-sm text-gray-500">Senin, 25 Desember 2023</p>
                                                </div>
                                                <span class="holiday-type-badge holiday-manual">Manual</span>
                                            </div>
                                            <p class="text-gray-600">Cuti bersama libur akhir tahun</p>
                                            <div class="flex justify-end mt-2">
                                                <button class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition-colors">
                                                    <span class="material-icons-outlined text-sm">delete</span>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Holiday Modal -->
                <div id="addHolidayModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-800">Tambah Hari Libur</h3>
                                <button class="text-gray-800 hover:text-gray-500">
                                    <span class="material-icons-outlined">close</span>
                                </button>
                            </div>
                            <form class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Hari Libur</label>
                                    <input type="text" name="holiday_name" id="holidayName"
                                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                    <input type="date" name="holiday_date" id="holidayDate"
                                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea name="holiday_description" id="holidayDescription"
                                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary form-input"
                                        rows="2"></textarea>
                                </div>
                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2023 by digicity.id
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tab Navigation - Only this functionality is enabled
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', function () {
                    // Remove active class from all tabs and contents
                    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });
        });
    </script>
</body>

</html>