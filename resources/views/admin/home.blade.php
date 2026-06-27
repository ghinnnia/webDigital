<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --white: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        .font-display {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .material-symbols-rounded {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        .material-icons-outlined {
            font-size: 20px;
            vertical-align: middle;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 250px;
            min-height: 100vh;
            padding: 2rem 1rem;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }

        .sidebar-logo .material-symbols-rounded {
            color: #667eea;
            font-size: 24px;
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link .material-symbols-rounded {
            margin-right: 0.75rem;
        }

        .nav-text {
            font-weight: 500;
        }

        .sidebar-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        /* --- UPDATED STAT CARDS (Compact, Icon Left) --- */
        .stat-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 1rem; /* Reduced padding */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-card-icon .material-symbols-rounded {
            font-size: 20px;
        }

        .stat-card-content {
            flex: 1;
        }

        .stat-card-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .stat-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        /* --- END UPDATED STAT CARDS --- */

        /* Panels & Tabs */
        .panel {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .panel-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
        }

        .panel-title .material-symbols-rounded {
            margin-right: 0.5rem;
            color: #3b82f6;
        }

        .panel-actions {
            display: flex;
        }

        .panel-action {
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .panel-action:hover {
            background-color: #f1f5f9;
            color: #3b82f6;
        }

        .panel-body {
            padding: 1.5rem;
        }

        /* Action Button Style (From Reference) */
        .action-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background-color: #f3f4f6;
            color: #6b7280;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .action-btn:hover {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .action-btn.edit:hover {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        
        .action-btn.delete:hover {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* User Badge Style (From Reference) */
        .user-badge-sm {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px;
            background-color: #f3f4f6;
            color: #374151;
            white-space: nowrap;
        }
        
        .user-badge-more {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px;
            background-color: #e5e7eb;
            color: #6b7280;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }

        .data-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }
        
        .tab-nav {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }

        .tab-button {
            padding: 0.75rem 1rem;
            font-weight: 500;
            color: #64748b;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .tab-button:hover {
            color: #3b82f6;
        }

        .tab-button.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        /* --- COMPACT CALENDAR STYLES --- */
        .calendar-notes-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            max-width: 600px;
            margin-left: 0;
            margin-right: auto;
            margin-bottom: 2rem;
        }

        .calendar-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .calendar-header {
            background: linear-gradient(to right, #3b82f6, #2563eb);
            color: white;
            padding: 0.5rem 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-nav-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .calendar-nav-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .calendar-nav-button .material-symbols-rounded {
            font-size: 0.875rem;
        }

        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.125rem;
            padding: 0.25rem 0.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .calendar-weekday {
            text-align: center;
            font-size: 0.625rem;
            font-weight: 600;
            color: #64748b;
            padding: 0.25rem 0;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.125rem;
            padding: 0.25rem 0.5rem;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            font-size: 0.75rem;
        }

        .calendar-day:hover {
            background: #f1f5f9;
            transform: scale(1.05);
        }

        .calendar-day.today {
            background: #3b82f6;
            color: white;
            font-weight: 600;
        }

        .calendar-day.today:hover {
            background: #2563eb;
        }

        .calendar-day.selected {
            background: #dbeafe;
            color: #1e40af;
            font-weight: 600;
            box-shadow: inset 0 0 0 2px #3b82f6;
        }

        .calendar-day.selected:hover {
            background: #bfdbfe;
        }

        .calendar-day.has-event::after {
            content: '';
            position: absolute;
            bottom: 0.125rem;
            width: 0.125rem;
            height: 0.125rem;
            background: #ef4444;
            border-radius: 50%;
        }

        .calendar-day.today.has-event::after {
            background: white;
        }

        .calendar-day.selected.has-event::after {
            background: #1e40af;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        
        .modal.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background: white;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }

            .page-header {
                margin-bottom: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .panel-header,
            .panel-body {
                padding: 1rem;
            }

            .data-table {
                font-size: 0.813rem;
            }

            .data-table th,
            .data-table td {
                padding: 0.5rem;
            }

            .tab-button {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }

            .calendar-notes-container {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 0.75rem;
            }

            .panel-header,
            .panel-body {
                padding: 0.75rem;
            }
        }

        /* Card-based layout for tables on mobile */
        .mobile-table-cards {
            display: none;
        }

        .mobile-table-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .mobile-table-card-header {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-table-card-row {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .mobile-table-card-label {
            font-weight: 600;
            width: 40%;
            color: #64748b;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .mobile-table-card-value {
            width: 60%;
            color: #1f2937;
            font-size: 0.875rem;
            word-wrap: break-word;
        }

        @media (max-width: 768px) {
            .data-table-container {
                display: none;
            }

            .mobile-table-cards {
                display: block;
            }
        }

        /* Mobile menu toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 200;
            background: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        /* Helper for hiding elements */
        .hidden-row {
            display: none !important;
        }
    </style>
</head>

<body class="font-display bg-gray-50 text-gray-800">
    <div class="app-container">
       
   @include('admin.templet.sider')

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Dashboard</h1>
            </div>

            <!-- UPDATED Stats Cards (Compact & Icon Left) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="stat-card">
                    <div class="stat-card-icon bg-blue-100">
                        <span class="material-symbols-rounded text-blue-600">groups</span>
                    </div>
                    <div class="stat-card-content">
                        <h3 class="stat-card-title">Jumlah Karyawan</h3>
                        <p class="stat-card-value">{{ $jumlahKaryawan }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon bg-green-100">
                        <span class="material-symbols-rounded text-green-600">business</span>
                    </div>
                    <div class="stat-card-content">
                        <h3 class="stat-card-title">Jumlah Perusahaan</h3>
                        <p class="stat-card-value">{{ $jumlahPerusahaan }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon bg-purple-100">
                        <span class="material-symbols-rounded text-purple-600">design_services</span>
                    </div>
                    <div class="stat-card-content">
                        <h3 class="stat-card-title">Jumlah Layanan</h3>
                        <p class="stat-card-value">{{ $jumlahLayanan }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon bg-yellow-100">
                        <span class="material-symbols-rounded text-yellow-600">handshake</span>
                    </div>
                    <div class="stat-card-content">
                        <h3 class="stat-card-title">Jumlah Project</h3>
                        <p class="stat-card-value">{{ $jumlahProject }}</p>
                    </div>
                </div>
            </div>

            <!-- TABS & PANELS (Positioned at Top) -->

            <!-- Tab Navigation -->
            <div class="tab-nav">
                <button id="meetingTab" class="tab-button active" onclick="switchTab('meeting')">
                    <span class="material-symbols-rounded align-middle mr-2">description</span>
                    Catatan Meeting
                </button>
                <button id="announcementTab" class="tab-button" onclick="switchTab('announcement')">
                    <span class="material-symbols-rounded align-middle mr-2">campaign</span>
                    Pengumuman Terbaru
                </button>
            </div>

            <!-- Meeting Notes Panel -->
            <div id="meetingPanel" class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <span class="material-symbols-rounded">description</span>
                        Catatan Meeting
                    </h3>
                    <div class="panel-actions">
                        <button class="panel-action">
                            <span class="material-symbols-rounded">filter_list</span>
                        </button>
                        <button class="panel-action">
                            <span class="material-symbols-rounded">more_vert</span>
                        </button>
                    </div>
                </div>
                <div class="panel-body">
                    <!-- Desktop Table View -->
                    <div class="data-table-container overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Topik Rapat</th>
                                    <th>Hasil Diskusi</th>
                                    <th>Keputusan</th>
                                    <th>Peserta</th>
                                    <th>Penugasan</th>
                                    <th style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($catatanRapat->count() > 0)
                                    @foreach ($catatanRapat as $rapat)
                                        <!-- Storing Data for JS Modal -->
                                        <tr 
                                            data-id="{{ $rapat->id }}"
                                            data-tanggal="{{ \Carbon\Carbon::parse($rapat->tanggal)->translatedFormat('d F Y') }}"
                                            data-topik="{{ $rapat->topik }}"
                                            data-hasil="{{ $rapat->hasil_diskusi }}"
                                            data-keputusan="{{ $rapat->keputusan }}"
                                            data-peserta="{{ json_encode($rapat->peserta) }}"
                                            data-penugasan="{{ json_encode($rapat->penugasan) }}"
                                            data-date="{{ \Carbon\Carbon::parse($rapat->tanggal)->format('Y-m-d') }}"
                                        >
                                            <td class="font-medium">{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($rapat->tanggal)->translatedFormat('d F Y') }}</td>
                                            <td>{{ $rapat->topik }}</td>
                                            <td>{{ Str::limit($rapat->hasil_diskusi, 30) }}</td>
                                            <td>{{ Str::limit($rapat->keputusan, 30) }}</td>
                                            
                                            <!-- Styled Peserta Column -->
                                            <td>
                                                @if($rapat->peserta && count($rapat->peserta) > 0)
                                                    @foreach($rapat->peserta->take(2) as $user)
                                                        <span class="user-badge-sm">{{ $user->name }}</span>
                                                    @endforeach
                                                    @if(count($rapat->peserta) > 2)
                                                        <span class="user-badge-more">+{{ count($rapat->peserta) - 2 }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                            
                                            <!-- Styled Penugasan Column -->
                                            <td>
                                                @if($rapat->penugasan && count($rapat->penugasan) > 0)
                                                    @foreach($rapat->penugasan->take(2) as $user)
                                                        <span class="user-badge-sm">{{ $user->name }}</span>
                                                    @endforeach
                                                    @if(count($rapat->penugasan) > 2)
                                                        <span class="user-badge-more">+{{ count($rapat->penugasan) - 2 }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>

                                            <!-- Action Column -->
                                            <td style="text-align: center;">
                                                <button onclick="viewCatatanRapat(this)" class="action-btn" title="Lihat Selengkapnya">
                                                    <span class="material-icons-outlined">visibility</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="no-data-row">
                                        <td colspan="8" class="text-center py-6 text-gray-500">
                                            Belum ada catatan meeting
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile Card View -->
                    <div class="mobile-table-cards">
                        @if ($catatanRapat->count() > 0)
                            @foreach ($catatanRapat as $rapat)
                                <div class="mobile-table-card" 
                                     data-id="{{ $rapat->id }}"
                                     data-tanggal="{{ \Carbon\Carbon::parse($rapat->tanggal)->translatedFormat('d F Y') }}"
                                     data-topik="{{ $rapat->topik }}"
                                     data-hasil="{{ $rapat->hasil_diskusi }}"
                                     data-keputusan="{{ $rapat->keputusan }}"
                                     data-peserta="{{ json_encode($rapat->peserta) }}"
                                     data-penugasan="{{ json_encode($rapat->penugasan) }}"
                                     data-date="{{ \Carbon\Carbon::parse($rapat->tanggal)->format('Y-m-d') }}"
                                >
                                    <div class="mobile-table-card-header">
                                        <span>#{{ $loop->iteration }} - {{ \Carbon\Carbon::parse($rapat->tanggal)->translatedFormat('d F Y') }}</span>
                                        <button onclick="viewCatatanRapat(this)" class="action-btn" title="Lihat Selengkapnya">
                                            <span class="material-icons-outlined">visibility</span>
                                        </button>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Topik:</div>
                                        <div class="mobile-table-card-value">{{ $rapat->topik }}</div>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Peserta:</div>
                                        <div class="mobile-table-card-value flex flex-wrap gap-1">
                                            @if($rapat->peserta && count($rapat->peserta) > 0)
                                                @foreach($rapat->peserta->take(2) as $user)
                                                    <span class="user-badge-sm">{{ $user->name }}</span>
                                                @endforeach
                                                @if(count($rapat->peserta) > 2)
                                                    <span class="user-badge-more">+{{ count($rapat->peserta) - 2 }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Hasil:</div>
                                        <div class="mobile-table-card-value">{{ Str::limit($rapat->hasil_diskusi, 50) }}</div>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Keputusan:</div>
                                        <div class="mobile-table-card-value">{{ Str::limit($rapat->keputusan, 50) }}</div>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Penugasan:</div>
                                        <div class="mobile-table-card-value flex flex-wrap gap-1">
                                            @if($rapat->penugasan && count($rapat->penugasan) > 0)
                                                @foreach($rapat->penugasan->take(2) as $user)
                                                    <span class="user-badge-sm">{{ $user->name }}</span>
                                                @endforeach
                                                @if(count($rapat->penugasan) > 2)
                                                    <span class="user-badge-more">+{{ count($rapat->penugasan) - 2 }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="mobile-table-card no-data-row">
                                <div class="text-center py-6 text-gray-500">
                                    Belum ada catatan meeting
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Announcement Panel -->
            <div id="announcementPanel" class="panel hidden">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <span class="material-symbols-rounded">campaign</span>
                        Pengumuman Terbaru
                    </h3>
                    <div class="panel-actions">
                        <button class="panel-action">
                            <span class="material-symbols-rounded">filter_list</span>
                        </button>
                        <button class="panel-action">
                            <span class="material-symbols-rounded">more_vert</span>
                        </button>
                    </div>
                </div>
                <div class="panel-body">
                    <!-- Desktop Table View -->
                    <div class="data-table-container overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Isi</th>
                                    <th>Kepada</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($pengumumanTerbaru->count() > 0)
                                    @foreach ($pengumumanTerbaru as $item)
                                        <tr data-date="{{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->format('Y-m-d') }}">
                                            <td class="font-medium">{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->translatedFormat('d F Y') }}</td>
                                            <td>{{ $item->judul }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($item->isi_pesan, 50) }}</td>
                                            <td>
                                                @if ($item->kepada === 'specific')
                                                    @foreach ($item->users as $user)
                                                        <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                                            {{ $user->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    {{ $item->users->take(2)->pluck('name')->join(', ') }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($item->lampiran)
                                                    <a href="{{ asset('storage/' . $item->lampiran) }}"
                                                        class="text-blue-600 hover:underline" target="_blank">
                                                        Lihat
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="no-data-row">
                                        <td colspan="6" class="text-center py-8 text-gray-500">
                                            Tidak ada pengumuman terbaru
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile Card View -->
                    <div class="mobile-table-cards">
                        @if ($pengumumanTerbaru->count() > 0)
                            @foreach ($pengumumanTerbaru as $item)
                                <div class="mobile-table-card" data-date="{{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->format('Y-m-d') }}">
                                    <div class="mobile-table-card-header">
                                        #{{ $loop->iteration }} - {{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->translatedFormat('d F Y') }}
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Judul:</div>
                                        <div class="mobile-table-card-value">{{ $item->judul }}</div>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Isi:</div>
                                        <div class="mobile-table-card-value">{{ \Illuminate\Support\Str::limit($item->isi_pesan, 100) }}</div>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Kepada:</div>
                                        <div class="mobile-table-card-value">
                                            @if ($item->kepada === 'specific')
                                                @foreach ($item->users as $user)
                                                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs mr-1 mb-1">
                                                        {{ $user->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                {{ $item->users->take(2)->pluck('name')->join(', ') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mobile-table-card-row">
                                        <div class="mobile-table-card-label">Lampiran:</div>
                                        <div class="mobile-table-card-value">
                                            @if ($item->lampiran)
                                                <a href="{{ asset('storage/' . $item->lampiran) }}"
                                                    class="text-blue-600 hover:underline" target="_blank">
                                                    Lihat
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="mobile-table-card no-data-row">
                                <div class="text-center py-8 text-gray-500">
                                    Tidak ada pengumuman terbaru
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- COMPACT CALENDAR (Positioned at Bottom, Small Size) -->
            <div class="calendar-notes-container">
                <div class="calendar-container">
                    <div class="calendar-header">
                        <button class="calendar-nav-button" onclick="prevMonth()">
                            <span class="material-symbols-rounded">chevron_left</span>
                        </button>
                        <h3 id="calendarTitle" class="text-sm font-semibold">
                            <!-- JS Injected -->
                        </h3>
                        <button class="calendar-nav-button" onclick="nextMonth()">
                            <span class="material-symbols-rounded">chevron_right</span>
                        </button>
                    </div>
                    <div class="calendar-weekdays">
                        <div class="calendar-weekday">Min</div>
                        <div class="calendar-weekday">Sen</div>
                        <div class="calendar-weekday">Sel</div>
                        <div class="calendar-weekday">Rab</div>
                        <div class="calendar-weekday">Kam</div>
                        <div class="calendar-weekday">Jum</div>
                        <div class="calendar-weekday">Sab</div>
                    </div>
                    <div id="calendarDays" class="calendar-days">
                        <!-- JS Injected -->
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- View Modal (Similar to Reference Code) -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Detail Catatan Rapat</h3>
                <button onclick="closeModal('viewModal')" class="text-gray-800 hover:text-gray-500">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            
            <div id="viewContent" class="space-y-4">
                <!-- Content will be dynamically inserted here via JS -->
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Function to switch between tabs
        window.switchTab = function (tabName) {
            const meetingTab = document.getElementById('meetingTab');
            const announcementTab = document.getElementById('announcementTab');
            const meetingPanel = document.getElementById('meetingPanel');
            const announcementPanel = document.getElementById('announcementPanel');

            meetingPanel.classList.add('hidden');
            announcementPanel.classList.add('hidden');
            meetingTab.classList.remove('active');
            announcementTab.classList.remove('active');

            if (tabName === 'meeting') {
                meetingPanel.classList.remove('hidden');
                meetingTab.classList.add('active');
            } else if (tabName === 'announcement') {
                announcementPanel.classList.remove('hidden');
                announcementTab.classList.add('active');
            }
        }

        // --- FILTERING LOGIC (CALENDAR) ---
        let activeFilterDate = null;

        function filterTablesByDate(dateStr) {
            const meetingRows = document.querySelectorAll('#meetingPanel .data-table tbody tr:not(.no-data-row)');
            const meetingCards = document.querySelectorAll('#meetingPanel .mobile-table-card:not(.no-data-row)');

            const announcementRows = document.querySelectorAll('#announcementPanel .data-table tbody tr:not(.no-data-row)');
            const announcementCards = document.querySelectorAll('#announcementPanel .mobile-table-card:not(.no-data-row)');

            const toggleVisibility = (elements, show) => {
                elements.forEach(el => {
                    if (show) {
                        el.classList.remove('hidden-row');
                    } else {
                        el.classList.add('hidden-row');
                    }
                });
            };

            if (!dateStr) {
                toggleVisibility(meetingRows, true);
                toggleVisibility(meetingCards, true);
                toggleVisibility(announcementRows, true);
                toggleVisibility(announcementCards, true);
            } else {
                toggleVisibility(meetingRows, false);
                toggleVisibility(meetingCards, false);
                toggleVisibility(announcementRows, false);
                toggleVisibility(announcementCards, false);

                const showMatches = (elements) => {
                    elements.forEach(el => {
                        const itemDate = el.getAttribute('data-date');
                        if (itemDate === dateStr) {
                            el.classList.remove('hidden-row');
                        }
                    });
                };

                showMatches(meetingRows);
                showMatches(meetingCards);
                showMatches(announcementRows);
                showMatches(announcementCards);
            }
        }

        // --- VIEW MODAL LOGIC (New) ---
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        // Open modal when "View" button is clicked
        function viewCatatanRapat(buttonElement) {
            // Find the parent row or card
            const parentRow = buttonElement.closest('tr') || buttonElement.closest('.mobile-table-card');
            
            if (!parentRow) return;

            // Read data attributes
            const tanggal = parentRow.getAttribute('data-tanggal');
            const topik = parentRow.getAttribute('data-topik');
            const hasil = parentRow.getAttribute('data-hasil');
            const keputusan = parentRow.getAttribute('data-keputusan');
            const pesertaJson = parentRow.getAttribute('data-peserta');
            const penugasanJson = parentRow.getAttribute('data-penugasan');

            // Parse JSON
            const peserta = JSON.parse(pesertaJson || '[]');
            const penugasan = JSON.parse(penugasanJson || '[]');

            // Populate Modal Content
            const contentDiv = document.getElementById('viewContent');
            
            // Helper to generate badge HTML
            const generateBadges = (users, colorClass) => {
                if (!users || users.length === 0) return '<span class="text-gray-400">-</span>';
                return users.map(user => 
                    `<span class="inline-block px-3 py-1 text-xs ${colorClass} rounded-full">${user.name}</span>`
                ).join(' ');
            };

            contentDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Tanggal</h4>
                        <p class="font-semibold text-gray-800">${tanggal || '-'}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Topik</h4>
                        <p class="font-semibold text-gray-800">${topik || '-'}</p>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Hasil Diskusi</h4>
                    <p class="text-gray-800 whitespace-pre-wrap">${hasil || '-'}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Keputusan</h4>
                    <p class="text-gray-800 whitespace-pre-wrap">${keputusan || '-'}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Peserta</h4>
                    <div class="flex flex-wrap gap-2 mt-1">
                        ${generateBadges(peserta, 'bg-blue-100 text-blue-800')}
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Penugasan</h4>
                    <div class="flex flex-wrap gap-2 mt-1">
                        ${generateBadges(penugasan, 'bg-green-100 text-green-800')}
                    </div>
                </div>
            `;

            // Show Modal
            const modal = document.getElementById('viewModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        // Close modal when clicking outside
        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('viewModal');
            }
        });


        // --- CALENDAR FUNCTIONALITY ---
        const events = @json($events);
        const today = new Date();
        const calendarTitle = document.getElementById('calendarTitle');
        const calendarDays = document.getElementById('calendarDays');

        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();

        function updateCalendarTitle() {
            const monthNames = [
                "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
                "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
            ];

            calendarTitle.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        }

        function renderCalendar() {
            updateCalendarTitle();

            calendarDays.innerHTML = '';

            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day';
                calendarDays.appendChild(emptyDay);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;

                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                const currentDate = new Date();
                if (currentYear === currentDate.getFullYear() &&
                    currentMonth === currentDate.getMonth() &&
                    day === currentDate.getDate()) {
                    dayElement.classList.add('today');
                }

                if (events[dateStr]) {
                    dayElement.classList.add('has-event');
                }

                if (activeFilterDate === dateStr) {
                    dayElement.classList.add('selected');
                }

                dayElement.addEventListener('click', function () {
                    if (activeFilterDate === dateStr) {
                        activeFilterDate = null;
                        filterTablesByDate(null);
                        renderCalendar(); 
                    } else {
                        activeFilterDate = dateStr;
                        filterTablesByDate(dateStr);
                        renderCalendar();
                    }
                });

                calendarDays.appendChild(dayElement);
            }
        }

        function prevMonth() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        }

        renderCalendar();
    </script>
</body>

</html>