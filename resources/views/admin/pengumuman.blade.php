<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pengumuman Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: "class",
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
        .material-icons-outlined {
            font-size: 24px;
            vertical-align: middle;
        }
        
        /* Card hover effects */
        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Table styles */
        .order-table {
            transition: all 0.2s ease;
        }
        
        .order-table tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }
        
        /* Button styles */
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
        
        /* Action button styles - Perbesar dan warna abu-abu */
        .action-btn {
            width: 36px;
            height: 36px;
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
        
        /* Modal styles */
        .modal {
            transition: opacity 0.25s ease;
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        /* Status Badge Styles */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-paid {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .status-unpaid {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }
        
        .status-pending {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        
        /* Custom styles untuk transisi */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Animasi hamburger */
        .hamburger-line {
            transition: all 0.3s ease-in-out;
        }
        
        .hamburger-active .line1 {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburger-active .line2 {
            opacity: 0;
        }
        
        .hamburger-active .line3 {
            transform: rotate(-45deg) translate(7px, -6px);
        }
        
        /* Gaya untuk indikator aktif/hover */
        /* Default untuk mobile: di sebelah kanan */
        .nav-item::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background-color: #3b82f6;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        
        /* Override untuk desktop: di sebelah kiri */
        @media (min-width: 768px) {
            .nav-item::before {
                right: auto;
                left: 0;
                transform: translateX(-100%);
            }
        }
        
        .nav-item:hover::before,
        .nav-item.active::before {
            transform: translateX(0);
        }
        
        /* Memastikan sidebar tetap di posisinya saat scroll */
        .sidebar-fixed {
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
        }
        
        /* Menyesuaikan konten utama agar tidak tertutup sidebar */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }
        
        @media (min-width: 768px) {
            .main-content {
                margin-left: 256px; /* Lebar sidebar */
            }
        }
        
        /* Scrollbar kustom untuk sidebar */
        .sidebar-fixed::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-fixed::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .sidebar-fixed::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .sidebar-fixed::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Table mobile adjustments */
        @media (max-width: 639px) {
            .desktop-table {
                display: none;
            }
            
            .mobile-cards {
                display: block;
            }
            
            /* Hide desktop pagination on mobile */
            .desktop-pagination {
                display: none !important;
            }
        }
        
        @media (min-width: 640px) {
            .desktop-table {
                display: block;
            }
            
            .mobile-cards {
                display: none;
            }
            
            /* Hide mobile pagination on desktop */
            .mobile-pagination {
                display: none !important;
            }
        }
        
        /* Form input styles */
        .form-input {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Pagination styles */
        .page-btn {
            transition: all 0.2s ease;
        }
        
        .page-btn:hover:not(:disabled) {
            transform: scale(1.1);
        }
        
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Desktop pagination styles */
        .desktop-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
        }
        
        .desktop-page-btn {
            min-width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .desktop-page-btn.active {
            background-color: #3b82f6;
            color: white;
        }
        
        .desktop-page-btn:not(.active) {
            background-color: #f1f5f9;
            color: #64748b;
        }
        
        .desktop-page-btn:not(.active):hover {
            background-color: #e2e8f0;
        }
        
        .desktop-nav-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .desktop-nav-btn:hover:not(:disabled) {
            background-color: #e2e8f0;
        }
        
        .desktop-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Mobile pagination styles */
        .mobile-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding: 0 8px;
        }
        
        .mobile-page-info {
            font-size: 14px;
            color: #64748b;
        }
        
        .mobile-nav-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            border-radius: 6px;
            background-color: #f1f5f9;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .mobile-nav-btn:hover:not(:disabled) {
            background-color: #e2e8f0;
        }
        
        .mobile-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Panel Styles */
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
        
        /* SCROLLABLE TABLE */
        .scrollable-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
        }
        
        /* Force scrollbar to be visible */
        .scrollable-table-container {
            scrollbar-width: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .scrollable-table-container::-webkit-scrollbar {
            height: 12px;
            width: 12px;
        }
        
        .scrollable-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }
        
        .scrollable-table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
            border: 2px solid #f1f5f9;
        }
        
        .scrollable-table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Table with fixed width to ensure scrolling */
        .data-table {
            width: 100%;
            min-width: 1000px; /* Fixed minimum width */
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
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .data-table tbody tr:hover {
            background: #f3f4f6;
        }
        
        /* Shadow effect */
        .table-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Minimalist Popup Styles - Modified to match second file */
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
        
        .minimal-popup.error .minimal-popup-icon {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .minimal-popup.warning .minimal-popup-icon {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .minimal-popup-content {
            flex-grow: 1;
        }
        
        .minimal-popup-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }
        
        .minimal-popup-message {
            font-size: 14px;
            color: #64748b;
        }
        
        .minimal-popup-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .minimal-popup-close:hover {
            background-color: #f1f5f9;
            color: #64748b;
        }
        
        /* Filter Dropdown Styles */
        .filter-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px;
            min-width: 200px;
            z-index: 100;
            display: none;
        }
        
        .filter-dropdown.show {
            display: block;
        }
        
        .filter-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .filter-option:hover {
            color: #3b82f6;
        }
        
        .filter-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .filter-option label {
            cursor: pointer;
            user-select: none;
        }
        
        .filter-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }
        
        .filter-actions button {
            flex: 1;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        
        .filter-apply {
            background-color: #3b82f6;
            color: white;
        }
        
        .filter-apply:hover {
            background-color: #2563eb;
        }
        
        .filter-reset {
            background-color: #f1f5f9;
            color: #64748b;
        }
        
        .filter-reset:hover {
            background-color: #e2e8f0;
        }
        
        /* Hidden class for filtering */
        .hidden-by-filter {
            display: none !important;
        }
        
        /* Loading spinner */
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Selected user badge styles */
        .selected-user-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px;
        }
        
        .selected-user-badge button {
            background: none;
            border: none;
            color: #0369a1;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
        }
        
        /* Checkbox styles for users */
        .user-checkbox-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            background-color: #f9fafb;
        }
        
        .user-checkbox-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 6px;
            transition: background-color 0.2s;
            cursor: pointer;
        }
        
        .user-checkbox-item:hover {
            background-color: #e0f2fe;
        }
        
        .user-checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            cursor: pointer;
        }
        
        .user-checkbox-item label {
            cursor: pointer;
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-role {
            font-size: 12px;
            color: #64748b;
            background-color: #f1f5f9;
            padding: 2px 8px;
            border-radius: 12px;
        }
        
        .user-checkbox-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .user-checkbox-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .user-checkbox-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .user-checkbox-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Attachment Preview Modal Styles */
        .attachment-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .attachment-modal.show {
            opacity: 1;
            visibility: visible;
        }
        
        .attachment-modal-content {
            background: white;
            border-radius: 8px;
            max-width: 90%;
            max-height: 90%;
            width: 800px;
            height: 600px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .attachment-modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }
        
        .attachment-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .attachment-modal-close {
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .attachment-modal-close:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
        
        .attachment-modal-body {
            flex: 1;
            padding: 20px;
            overflow: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        
        .attachment-preview {
            max-width: 100%;
            max-height: 100%;
            position: relative;
        }
        
        .attachment-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .attachment-preview iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 4px;
        }
        
        .attachment-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .attachment-loading .spinner {
            margin: 0 auto 16px;
        }
        
        .attachment-error {
            text-align: center;
            padding: 40px;
        }
        
        .attachment-error-icon {
            width: 64px;
            height: 64px;
            background-color: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        
        .attachment-error-icon .material-icons-outlined {
            color: #ef4444;
            font-size: 32px;
        }
        
        .attachment-info {
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .attachment-name {
            font-size: 14px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .attachment-actions {
            display: flex;
            gap: 8px;
        }
        
        .attachment-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .attachment-btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .attachment-btn-primary:hover {
            background-color: #2563eb;
        }
        
        .attachment-btn-secondary {
            background-color: #f1f5f9;
            color: #64748b;
        }
        
        .attachment-btn-secondary:hover {
            background-color: #e2e8f0;
        }
    </style>
    
    <!-- Add CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        @include('admin.templet.sider')
        
        <!-- MAIN -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-grow p-3 sm:p-8">

                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Pengumuman</h2>
                
                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari judul atau isi pengumuman..." type="text" />
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <button id="createBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                            <span class="material-icons-outlined">add</span>
                            <span class="hidden sm:inline">Buat Pengumuman</span>
                            <span class="sm:hidden">Buat</span>
                        </button>
                    </div>
                </div>
                
                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">campaign</span>
                            Daftar Pengumuman
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount" class="font-semibold text-text-light">{{ count($pengumuman ?? []) }}</span> pengumuman</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Empty State -->
                        <div id="emptyState" class="{{ count($pengumuman ?? []) > 0 ? 'hidden' : '' }} text-center py-12">
                            <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <span class="material-icons-outlined text-3xl text-gray-400">campaign</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Pengumuman</h3>
                            <p class="text-gray-500 mb-6">Mulai dengan membuat pengumuman pertama Anda</p>
                            <button id="createFirstBtn" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                                Buat Pengumuman Pertama
                            </button>
                        </div>
                        
                        <!-- Loading State -->
                        <div id="loadingState" class="hidden text-center py-12">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto mb-4"></div>
                            <p class="text-gray-600">Memuat data pengumuman...</p>
                        </div>
                        
                        <!-- SCROLLABLE TABLE -->
                        <div id="tableContainer" class="{{ count($pengumuman ?? []) > 0 ? '' : 'hidden' }} desktop-table">
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 200px;">Judul</th>
                                            <th style="min-width: 300px;">Isi</th>
                                            <th style="min-width: 150px;">Kepada</th>
                                            <th style="min-width: 150px;">Lampiran</th>
                                            <th style="min-width: 100px;">Tanggal</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <!-- Table rows will be dynamically generated here -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Desktop Pagination -->
                            <div id="desktopPagination" class="desktop-pagination">
                                <button id="prevPage" class="desktop-nav-btn" disabled>
                                    <span class="material-icons-outlined">chevron_left</span>
                                </button>
                                <div id="pageNumbers" class="flex gap-1">
                                    <!-- Page numbers will be dynamically generated here -->
                                </div>
                                <button id="nextPage" class="desktop-nav-btn">
                                    <span class="material-icons-outlined">chevron_right</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Mobile Card View -->
                        <div id="mobileCards" class="{{ count($pengumuman ?? []) > 0 ? '' : 'hidden' }} mobile-cards space-y-4">
                            <!-- Mobile cards will be dynamically generated here -->
                            
                            <!-- Mobile Pagination -->
                            <div id="mobilePagination" class="mobile-pagination">
                                <button id="mobilePrevPage" class="mobile-nav-btn" disabled>
                                    <span class="material-icons-outlined">chevron_left</span>
                                    Sebelumnya
                                </button>
                                <span id="mobilePageInfo" class="mobile-page-info">Halaman 1 dari 1</span>
                                <button id="mobileNextPage" class="mobile-nav-btn">
                                    Selanjutnya
                                    <span class="material-icons-outlined">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Modal Popup -->
    <div id="modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-xl font-bold text-text-light dark:text-text-dark"></h3>
                    <button id="closeModal" class="text-muted-light dark:text-muted-dark hover:text-text-light dark:hover:text-text-dark">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                
                <div id="modalContent" class="mb-6">
                    <!-- Content will be dynamically inserted here -->
                </div>
                
                <div class="flex justify-end gap-3">
                    <button id="cancelBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                    <button id="confirmBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                        <span id="confirmBtnText">Simpan</span>
                        <span id="loadingSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal - Modified to match second file -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button id="closeDeleteModal" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus data ini?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteId" name="id">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelDeleteBtn"
                            class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Attachment Preview Modal -->
    <div id="attachmentModal" class="attachment-modal">
        <div class="attachment-modal-content">
            <div class="attachment-modal-header">
                <h3 class="attachment-modal-title">
                    <span class="material-icons-outlined">attach_file</span>
                    <span id="attachmentTitle">Lampiran</span>
                </h3>
                <button id="closeAttachmentModal" class="attachment-modal-close">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="attachment-modal-body">
                <div id="attachmentPreview" class="attachment-preview">
                    <!-- Attachment preview will be loaded here -->
                </div>
            </div>
            <div class="attachment-info">
                <div class="attachment-name">
                    <span class="material-icons-outlined text-sm">description</span>
                    <span id="attachmentFileName">file.pdf</span>
                </div>
                <div class="attachment-actions">
                    <button id="downloadAttachment" class="attachment-btn attachment-btn-secondary">
                        <span class="material-icons-outlined text-sm">download</span>
                        Unduh
                    </button>
                    <button id="openNewTab" class="attachment-btn attachment-btn-primary">
                        <span class="material-icons-outlined text-sm">open_in_new</span>
                        Buka di Tab Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimalist Popup - Modified to match second file -->
    <div id="notification" class="minimal-popup">
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
        // Global variables
        let currentAction = '';
        let currentId = null;
        let allUsers = [];
        let cachedUsers = [];
        let currentPage = 1;
        let itemsPerPage = 5; // Changed from 10 to 5
        let totalItems = 0;
        let totalPages = 1;
        let allData = [];
        let filteredData = [];
        let currentAttachmentUrl = '';
        let currentAttachmentName = '';
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Pengumuman page loaded');
            
            // Store all data for pagination
            @if(isset($pengumuman) && count($pengumuman) > 0)
                allData = @json($pengumuman);
                filteredData = [...allData];
                totalItems = allData.length;
                totalPages = Math.ceil(totalItems / itemsPerPage);
            @endif
            
            // Load users data
            loadUsers();
            
            // Event listeners - with null checks
            const createBtn = document.getElementById('createBtn');
            if (createBtn) createBtn.addEventListener('click', openCreateModal);
            
            const createFirstBtn = document.getElementById('createFirstBtn');
            if (createFirstBtn) createFirstBtn.addEventListener('click', openCreateModal);
            
            const confirmBtn = document.getElementById('confirmBtn');
            if (confirmBtn) confirmBtn.addEventListener('click', handleConfirm);
            
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.addEventListener('input', filterData);
            
            // Modal event listeners
            const closeModal_ = document.getElementById('closeModal');
            if (closeModal_) closeModal_.addEventListener('click', closeModal);
            
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            
            // Delete modal event listeners
            const closeDeleteModal_ = document.getElementById('closeDeleteModal');
            if (closeDeleteModal_) closeDeleteModal_.addEventListener('click', closeDeleteModal);
            
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeDeleteModal);
            
            // Attachment modal event listeners
            const closeAttachmentModal_ = document.getElementById('closeAttachmentModal');
            if (closeAttachmentModal_) closeAttachmentModal_.addEventListener('click', closeAttachmentModal);
            
            const downloadAttachment_ = document.getElementById('downloadAttachment');
            if (downloadAttachment_) downloadAttachment_.addEventListener('click', downloadAttachment);
            
            const openNewTab_ = document.getElementById('openNewTab');
            if (openNewTab_) openNewTab_.addEventListener('click', openInNewTab);
            
            // Close modal when clicking outside
            const modal = document.getElementById('modal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal();
                    }
                });
            }
            
            // Close delete modal when clicking outside
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });
            }
            
            // Close attachment modal when clicking outside
            const attachmentModal = document.getElementById('attachmentModal');
            if (attachmentModal) {
                attachmentModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAttachmentModal();
                    }
                });
            }
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    closeDeleteModal();
                    closeAttachmentModal();
                }
            });
            
            // Close notification when clicking the close button
            const popupClose = document.querySelector('.minimal-popup-close');
            if (popupClose) {
                popupClose.addEventListener('click', function() {
                    document.getElementById('notification').classList.remove('show');
                });
            }
            
            // Initialize filter
            initializeFilter();
            
            // Initialize scroll detection for table
            initializeScrollDetection();
            
            // Initialize pagination
            initializePagination();
            
            // Initial data load
            updateTableData();
        });
        
        // Initialize pagination
        function initializePagination() {
            // Add event listeners for pagination buttons - with null checks
            const prevPage = document.getElementById('prevPage');
            if (prevPage) {
                prevPage.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });
            }
            
            const nextPage = document.getElementById('nextPage');
            if (nextPage) {
                nextPage.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                    }
                });
            }
            
            const mobilePrevPage = document.getElementById('mobilePrevPage');
            if (mobilePrevPage) {
                mobilePrevPage.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });
            }
            
            const mobileNextPage = document.getElementById('mobileNextPage');
            if (mobileNextPage) {
                mobileNextPage.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                    }
                });
            }
            
            // Initial pagination update
            updatePagination();
        }
        
        // Update pagination
        function updatePagination() {
            // Update desktop pagination
            updateDesktopPagination();
            
            // Update mobile pagination
            updateMobilePagination();
            
            // Update table data
            updateTableData();
        }
        
        // Update desktop pagination
        function updateDesktopPagination() {
            const pageNumbers = document.getElementById('pageNumbers');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            
            // Clear existing page numbers
            pageNumbers.innerHTML = '';
            
            // If no pages, don't show pagination
            if (totalPages <= 1) {
                document.getElementById('desktopPagination').style.display = 'none';
                return;
            } else {
                document.getElementById('desktopPagination').style.display = 'flex';
            }
            
            // Calculate page range to display
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            
            // Adjust start page if we're near the end
            if (endPage - startPage < 4 && startPage > 1) {
                startPage = Math.max(1, endPage - 4);
            }
            
            // Add first page and ellipsis if needed
            if (startPage > 1) {
                addPageNumber(1);
                if (startPage > 2) {
                    addEllipsis();
                }
            }
            
            // Add page numbers
            for (let i = startPage; i <= endPage; i++) {
                addPageNumber(i);
            }
            
            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    addEllipsis();
                }
                addPageNumber(totalPages);
            }
            
            // Update navigation buttons
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
            
            // Helper functions
            function addPageNumber(pageNum) {
                const pageBtn = document.createElement('div');
                pageBtn.className = `desktop-page-btn ${pageNum === currentPage ? 'active' : ''}`;
                pageBtn.textContent = pageNum;
                pageBtn.addEventListener('click', () => {
                    currentPage = pageNum;
                    updatePagination();
                });
                pageNumbers.appendChild(pageBtn);
            }
            
            function addEllipsis() {
                const ellipsis = document.createElement('div');
                ellipsis.className = 'desktop-page-btn';
                ellipsis.textContent = '...';
                ellipsis.style.cursor = 'default';
                pageNumbers.appendChild(ellipsis);
            }
        }
        
        // Update mobile pagination
        function updateMobilePagination() {
            const pageInfo = document.getElementById('mobilePageInfo');
            const prevBtn = document.getElementById('mobilePrevPage');
            const nextBtn = document.getElementById('mobileNextPage');
            
            // Update page info
            if (totalPages <= 1) {
                document.getElementById('mobilePagination').style.display = 'none';
                return;
            } else {
                document.getElementById('mobilePagination').style.display = 'flex';
            }
            
            pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;
            
            // Update navigation buttons
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }
        
        // Update table data based on current page
        function updateTableData() {
            // Calculate start and end indices
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
            
            // Get data for current page
            const pageData = filteredData.slice(startIndex, endIndex);
            
            // Update desktop table
            updateDesktopTable(pageData, startIndex);
            
            // Update mobile cards
            updateMobileCards(pageData);
            
            // Update total count
            document.getElementById('totalCount').textContent = filteredData.length;
        }
        
        // Update desktop table
        function updateDesktopTable(data, startIndex) {
            const tableBody = document.getElementById('tableBody');
            
            // Clear existing rows
            tableBody.innerHTML = '';
            
            // Add rows for current page
            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50';
                
                // Format users display
                let usersDisplay = '<span class="text-gray-400">-</span>';
                if (item.users && item.users.length > 0) {
                    const userNames = item.users.slice(0, 2).map(u => u.name).join(', ');
                    const moreText = item.users.length > 2 ? ` +${item.users.length - 2} lainnya` : '';
                    usersDisplay = `<span class="text-sm">${userNames}${moreText}</span>`;
                }
                
                // Format attachment display
                let attachmentDisplay = '<span class="text-gray-400">-</span>';
                if (item.lampiran) {
                    attachmentDisplay = `
                        <button onclick="showAttachment('${item.lampiran}')" 
                                class="text-primary hover:underline flex items-center gap-1">
                            <span class="material-icons-outlined text-sm">attach_file</span>
                            File
                        </button>
                    `;
                }
                
                // Format date
                const date = new Date(item.created_at).toLocaleDateString('id-ID');
                
                row.innerHTML = `
                    <td class="p-3">${startIndex + index + 1}</td>
                    <td class="p-3 font-medium">${item.judul}</td>
                    <td class="p-3 max-w-xs truncate" title="${item.isi_pesan}">
                        ${item.isi_pesan.length > 50 ? item.isi_pesan.substring(0, 50) + '...' : item.isi_pesan}
                    </td>
                    <td class="p-3">${usersDisplay}</td>
                    <td class="p-3">${attachmentDisplay}</td>
                    <td class="p-3 text-sm text-gray-500">${date}</td>
                    <td class="p-3">
                        <div class="flex gap-2 justify-center">
                            <button onclick="editPengumuman(${item.id})" 
                                    class="action-btn edit" title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </button>
                            <button onclick="openDeleteModal(${item.id})" 
                                    class="action-btn delete" title="Hapus">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }
        
        // Update mobile cards
        function updateMobileCards(data) {
            const mobileCards = document.getElementById('mobileCards');
            
            // Clear existing cards except pagination
            const cards = mobileCards.querySelectorAll('.border.rounded-lg');
            cards.forEach(card => card.remove());
            
            // Add cards for current page
            data.forEach(item => {
                const card = document.createElement('div');
                card.className = 'border rounded-lg p-4 card-hover';
                
                // Format users display
                let usersDisplay = '-';
                if (item.users && item.users.length > 0) {
                    const userNames = item.users.slice(0, 2).map(u => u.name).join(', ');
                    const moreText = item.users.length > 2 ? ` +${item.users.length - 2} lainnya` : '';
                    usersDisplay = `Kepada: ${userNames}${moreText}`;
                }
                
                // Format date and time
                const date = new Date(item.created_at).toLocaleString('id-ID');
                
                // Format attachment display
                let attachmentDisplay = '';
                if (item.lampiran) {
                    attachmentDisplay = `
                        <button onclick="showAttachment('${item.lampiran}')" class="text-primary">
                            📎 Lampiran
                        </button>
                    `;
                }
                
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold">${item.judul}</h3>
                            <p class="text-sm text-gray-500">${usersDisplay}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editPengumuman(${item.id})" 
                                    class="action-btn edit" title="Edit">
                                <span class="material-icons-outlined">edit</span>
                            </button>
                            <button onclick="openDeleteModal(${item.id})" 
                                    class="action-btn delete" title="Hapus">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">${item.isi_pesan.length > 100 ? item.isi_pesan.substring(0, 100) + '...' : item.isi_pesan}</p>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span>${date}</span>
                        ${attachmentDisplay}
                    </div>
                `;
                
                // Insert before pagination
                const pagination = mobileCards.querySelector('.mobile-pagination');
                mobileCards.insertBefore(card, pagination);
            });
        }
        
        // Show attachment in modal
        function showAttachment(filename) {
            currentAttachmentName = filename;
            
            // Clean filename to remove duplicate path if exists
            let cleanFilename = filename;
            if (filename.includes('pengumuman/')) {
                cleanFilename = filename.split('pengumuman/').pop();
            }
            
            // Update modal title
            document.getElementById('attachmentTitle').textContent = 'Lampiran';
            document.getElementById('attachmentFileName').textContent = cleanFilename;
            
            // Clear previous content and show loading
            const previewContainer = document.getElementById('attachmentPreview');
            previewContainer.innerHTML = `
                <div class="attachment-loading">
                    <div class="spinner"></div>
                    <p class="text-gray-500">Memuat lampiran...</p>
                </div>
            `;
            
            // Show modal
            document.getElementById('attachmentModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Determine file type and show appropriate preview
            const fileExtension = cleanFilename.split('.').pop().toLowerCase();
            
            // Try multiple possible URLs
            const possibleUrls = [
                `/storage/pengumuman/${cleanFilename}`,
                `/storage/${cleanFilename}`,
                `/storage/pengumuman/pengumuman/${cleanFilename}`
            ];
            
            // Try to load the file
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                // Image file - try each URL until one works
                let urlIndex = 0;
                
                function tryLoadImage() {
                    if (urlIndex >= possibleUrls.length) {
                        // All URLs failed, show error
                        showAttachmentError(cleanFilename);
                        return;
                    }
                    
                    const img = new Image();
                    const currentUrl = possibleUrls[urlIndex];
                    
                    img.onload = function() {
                        // Image loaded successfully
                        currentAttachmentUrl = currentUrl;
                        previewContainer.innerHTML = '';
                        const imgElement = document.createElement('img');
                        imgElement.src = currentUrl;
                        imgElement.alt = cleanFilename;
                        imgElement.className = 'max-w-full max-h-full object-contain';
                        previewContainer.appendChild(imgElement);
                    };
                    
                    img.onerror = function() {
                        // Try next URL
                        urlIndex++;
                        tryLoadImage();
                    };
                    
                    img.src = currentUrl;
                }
                
                tryLoadImage();
                
            } else if (['pdf'].includes(fileExtension)) {
                // PDF file - try each URL until one works
                let urlIndex = 0;
                
                function tryLoadPDF() {
                    if (urlIndex >= possibleUrls.length) {
                        // All URLs failed, show error
                        showAttachmentError(cleanFilename);
                        return;
                    }
                    
                    const currentUrl = possibleUrls[urlIndex];
                    currentAttachmentUrl = currentUrl;
                    
                    // Create iframe
                    const iframe = document.createElement('iframe');
                    iframe.src = currentUrl;
                    iframe.title = cleanFilename;
                    iframe.className = 'w-full h-full border-none rounded';
                    
                    // Set timeout to detect if PDF loads
                    setTimeout(() => {
                        try {
                            // Check if iframe loaded content
                            if (iframe.contentDocument && iframe.contentDocument.body) {
                                previewContainer.innerHTML = '';
                                previewContainer.appendChild(iframe);
                            } else {
                                // Try next URL
                                urlIndex++;
                                tryLoadPDF();
                            }
                        } catch (e) {
                            // Try next URL
                            urlIndex++;
                            tryLoadPDF();
                        }
                    }, 2000);
                    
                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(iframe);
                }
                
                tryLoadPDF();
                
            } else {
                // Other file types - show file info
                currentAttachmentUrl = possibleUrls[0]; // Use first URL as default
                showFileInfo(cleanFilename);
            }
        }
        
        // Show attachment error
        function showAttachmentError(filename) {
            const previewContainer = document.getElementById('attachmentPreview');
            previewContainer.innerHTML = `
                <div class="attachment-error">
                    <div class="attachment-error-icon">
                        <span class="material-icons-outlined">error</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">File Tidak Ditemukan</h3>
                    <p class="text-gray-500 mb-6">File "${filename}" tidak dapat ditemukan atau telah dipindahkan</p>
                    <button onclick="downloadAttachment()" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                        <span class="material-icons-outlined text-sm mr-2">download</span>
                        Coba Unduh File
                    </button>
                </div>
            `;
        }
        
        // Show file info for non-previewable files
        function showFileInfo(filename) {
            const previewContainer = document.getElementById('attachmentPreview');
            previewContainer.innerHTML = `
                <div class="text-center p-8">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <span class="material-icons-outlined text-4xl text-gray-400">description</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">${filename}</h3>
                    <p class="text-gray-500 mb-6">Tidak dapat menampilkan pratinjau untuk jenis file ini</p>
                    <button onclick="downloadAttachment()" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                        <span class="material-icons-outlined text-sm mr-2">download</span>
                        Unduh File
                    </button>
                </div>
            `;
        }
        
        // Close attachment modal
        function closeAttachmentModal() {
            document.getElementById('attachmentModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            currentAttachmentUrl = '';
            currentAttachmentName = '';
        }
        
        // Download attachment
        function downloadAttachment() {
            if (currentAttachmentName) {
                // Try multiple possible URLs for download
                const cleanFilename = currentAttachmentName.includes('pengumuman/') ? 
                    currentAttachmentName.split('pengumuman/').pop() : 
                    currentAttachmentName;
                
                const possibleUrls = [
                    `/storage/pengumuman/${cleanFilename}`,
                    `/storage/${cleanFilename}`,
                    `/storage/pengumuman/pengumuman/${cleanFilename}`
                ];
                
                // Try first URL
                const link = document.createElement('a');
                link.href = possibleUrls[0];
                link.download = cleanFilename;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show notification
                showMinimalPopup('Info', 'Mengunduh file...', 'warning');
            }
        }
        
        // Open attachment in new tab
        function openInNewTab() {
            if (currentAttachmentUrl) {
                window.open(currentAttachmentUrl, '_blank');
            } else if (currentAttachmentName) {
                // Try to open with first possible URL
                const cleanFilename = currentAttachmentName.includes('pengumuman/') ? 
                    currentAttachmentName.split('pengumuman/').pop() : 
                    currentAttachmentName;
                
                const url = `/storage/pengumuman/${cleanFilename}`;
                window.open(url, '_blank');
            }
        }
        
        // Load users from server
        async function loadUsers() {
            try {
                const response = await fetch('/users/data', {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to load users');
                }
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    cachedUsers = result.data;
                    console.log(`Loaded ${cachedUsers.length} users`);
                } else {
                    console.warn('No users data found');
                    cachedUsers = [];
                }
            } catch (error) {
                console.error('Error loading users:', error);
                cachedUsers = [];
            }
        }
        
        // Modal functions
        function openCreateModal() {
            currentAction = 'create';
            currentId = null;
            
            showModal(
                'Buat Pengumuman Baru',
                getFormTemplate({}),
                'Simpan'
            );
            
            // Load users into checkboxes after modal is shown
            setTimeout(() => populateUserCheckboxes(), 100);
        }
        
        function showModal(title, content, confirmText = 'Simpan') {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('confirmBtnText').textContent = confirmText;
            document.getElementById('modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentAction = '';
            currentId = null;
        }
        
        // Delete modal functions
        function openDeleteModal(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').action = `/pengumuman/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Form template
        function getFormTemplate(data = {}) {
            return `
                <form id="pengumumanForm" class="space-y-4" onsubmit="return false;">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            <span class="text-red-500">*</span> Judul
                        </label>
                        <input type="text" id="judulInput" name="judul"
                            value="${data.judul || ''}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none"
                            placeholder="Masukkan judul pengumuman"
                            required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            <span class="text-red-500">*</span> Isi Pesan
                        </label>
                        <textarea id="isiInput" name="isi_pesan" rows="4"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none resize-none"
                            placeholder="Tulis isi pengumuman..."
                            required>${data.isi_pesan || ''}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            <span class="text-red-500">*</span> Penerima
                        </label>

                        <!-- Search for recipients -->
                        <div class="mb-2">
                            <input type="text" id="userSearchInput" oninput="populateUserCheckboxes()"
                                placeholder="Cari penerima (nama atau email)..."
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none"
                                autocomplete="off">
                        </div>

                        <div class="user-checkbox-container" id="usersCheckboxContainer">
                            <div class="text-center text-gray-500 py-4">
                                <span class="material-icons-outlined animate-spin">refresh</span>
                                <p class="mt-2">Memuat daftar user...</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" onclick="selectAllUsers()" class="text-sm text-primary hover:underline">
                                Pilih Semua
                            </button>
                            <button type="button" onclick="deselectAllUsers()" class="text-sm text-gray-500 hover:underline">
                                Hapus Pilihan
                            </button>
                        </div>
                        <div id="selectedUsers" class="mt-3 flex flex-wrap gap-2"></div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700">Lampiran</label>
                        <input type="file" id="fileInput" name="lampiran"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <p class="text-xs text-gray-500 mt-1">
                            Ukuran maksimal 10MB. Format: PDF, DOC, JPG, PNG
                        </p>
                        <div id="filePreview" class="mt-2"></div>
                    </div>
                </form>
            `;
        }
        
        // Populate user checkboxes
        function populateUserCheckboxes(selectedIds = []) {
            const container = document.getElementById('usersCheckboxContainer');
            const selectedUsersDiv = document.getElementById('selectedUsers');
            
            if (!container) return;
            
            // Preserve currently checked IDs so filtering doesn't lose selections
            const currentlyChecked = Array.from(document.querySelectorAll('#usersCheckboxContainer input[type="checkbox"]:checked')).map(cb => cb.value.toString());
            if ((!Array.isArray(selectedIds) || selectedIds.length === 0) && currentlyChecked.length > 0) {
                selectedIds = currentlyChecked;
            }

            // Clear existing content
            container.innerHTML = '';
            if (selectedUsersDiv) selectedUsersDiv.innerHTML = '';
            
            if (cachedUsers.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-4">Tidak ada user tersedia</div>';
                return;
            }
            
            // Apply search filter if present
            const searchInput = document.getElementById('userSearchInput');
            const searchTerm = searchInput && searchInput.value ? searchInput.value.trim().toLowerCase() : '';

            let usersToDisplay = cachedUsers;
            if (searchTerm !== '') {
                usersToDisplay = cachedUsers.filter(user => {
                    const name = (user.name || '').toString().toLowerCase();
                    const email = (user.email || '').toString().toLowerCase();
                    return name.includes(searchTerm) || email.includes(searchTerm);
                });
            }

            // Group users by role if available
            const groupedUsers = {};
            usersToDisplay.forEach(user => {
                const role = user.role || 'User';
                if (!groupedUsers[role]) {
                    groupedUsers[role] = [];
                }
                groupedUsers[role].push(user);
            });
            
            // Create checkboxes for each group
            Object.keys(groupedUsers).forEach(role => {
                // Add role header
                const roleHeader = document.createElement('div');
                roleHeader.className = 'text-sm font-semibold text-gray-600 mb-2 mt-3 first:mt-0';
                roleHeader.textContent = role;
                container.appendChild(roleHeader);
                
                // Add users in this role
                groupedUsers[role].forEach(user => {
                    const checkboxItem = document.createElement('div');
                    checkboxItem.className = 'user-checkbox-item';
                    
                    const isChecked = Array.isArray(selectedIds) && selectedIds.includes(user.id.toString());
                    
                    checkboxItem.innerHTML = `
                        <input type="checkbox" 
                               id="user_${user.id}" 
                               name="users[]" 
                               value="${user.id}"
                               ${isChecked ? 'checked' : ''}
                               onchange="updateSelectedBadges()">
                        <label for="user_${user.id}">
                            <span>${user.name}</span>
                            <span class="user-role">${user.role || 'User'}</span>
                        </label>
                    `;
                    
                    container.appendChild(checkboxItem);
                });
            });

            // If no users match search, show message
            if (usersToDisplay.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-4">Tidak ada user sesuai pencarian</div>';
            }
            
            // Update selected badges
            updateSelectedBadges();
        }
        
        // Select all users
        function selectAllUsers() {
            const checkboxes = document.querySelectorAll('#usersCheckboxContainer input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedBadges();
        }
        
        // Deselect all users
        function deselectAllUsers() {
            const checkboxes = document.querySelectorAll('#usersCheckboxContainer input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedBadges();
        }
        
        // Update selected user badges
        function updateSelectedBadges() {
            const selectedUsersDiv = document.getElementById('selectedUsers');
            const checkboxes = document.querySelectorAll('#usersCheckboxContainer input[type="checkbox"]:checked');
            
            if (!selectedUsersDiv) return;
            
            selectedUsersDiv.innerHTML = '';
            
            if (checkboxes.length === 0) {
                selectedUsersDiv.innerHTML = '<span class="text-sm text-gray-400">Belum ada penerima dipilih</span>';
                return;
            }
            
            checkboxes.forEach(checkbox => {
                const label = checkbox.nextElementSibling;
                const userName = label.querySelector('span').textContent;
                
                const badge = document.createElement('span');
                badge.className = 'selected-user-badge';
                badge.innerHTML = `
                    ${userName}
                    <button type="button" onclick="deselectUser('${checkbox.value}')" class="ml-1">
                        <span class="material-icons-outlined text-xs">close</span>
                    </button>
                `;
                selectedUsersDiv.appendChild(badge);
            });
        }
        
        // Deselect user
        function deselectUser(userId) {
            const checkbox = document.querySelector(`#usersCheckboxContainer input[value="${userId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedBadges();
            }
        }
        
        // Edit pengumuman
        async function editPengumuman(id) {
            try {
                currentAction = 'edit';
                currentId = id;
                
                const response = await fetch(`/pengumuman/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showModal(
                        'Edit Pengumuman',
                        getFormTemplate(result.data),
                        'Update'
                    );
                    
                    // Wait for modal to render, then populate users
                    setTimeout(() => {
                        const selectedUserIds = result.data.users?.map(u => u.id.toString()) || [];
                        populateUserCheckboxes(selectedUserIds);
                        
                        // Show file preview if exists
                        if (result.data.lampiran) {
                            showFilePreview(result.data.lampiran);
                        }
                    }, 100);
                } else {
                    showMinimalPopup('Error', result.message || 'Gagal memuat data', 'error');
                }
            } catch (error) {
                console.error('Error editing pengumuman:', error);
                showMinimalPopup('Error', 'Gagal memuat data pengumuman', 'error');
            }
        }
        
        // Show file preview
        function showFilePreview(filename) {
            const filePreview = document.getElementById('filePreview');
            if (filePreview) {
                filePreview.innerHTML = `
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <span class="material-icons-outlined text-sm">attach_file</span>
                        <span>File saat ini: ${filename}</span>
                    </div>
                `;
            }
        }
        
        // Handle form submission
        async function handleConfirm() {
            try {
                // Get form elements
                const judulInput = document.getElementById('judulInput');
                const isiInput = document.getElementById('isiInput');
                const checkboxes = document.querySelectorAll('#usersCheckboxContainer input[type="checkbox"]:checked');
                const fileInput = document.getElementById('fileInput');
                
                // Validation
                if (!judulInput || !isiInput) {
                    showMinimalPopup('Error', 'Form tidak lengkap', 'error');
                    return;
                }
                
                const judul = judulInput.value.trim();
                const isi = isiInput.value.trim();
                const selectedUsers = Array.from(checkboxes).map(cb => cb.value);
                
                if (!judul || !isi) {
                    showMinimalPopup('Error', 'Judul dan Isi Pesan wajib diisi', 'error');
                    return;
                }
                
                if (selectedUsers.length === 0) {
                    showMinimalPopup('Error', 'Pilih minimal satu penerima', 'error');
                    return;
                }
                
                // Prepare form data
                const formData = new FormData();
                formData.append('judul', judul);
                formData.append('isi_pesan', isi);
                
                // Add users
                selectedUsers.forEach(userId => {
                    formData.append('users[]', userId);
                });
                
                // Add file if exists
                if (fileInput && fileInput.files[0]) {
                    formData.append('lampiran', fileInput.files[0]);
                }
                
                // Determine URL and method
                let url = '/pengumuman';
                let method = 'POST';
                
                if (currentAction === 'edit') {
                    url = `/pengumuman/${currentId}`;
                    method = 'PUT';
                    formData.append('_method', 'PUT');
                }
                
                // Show loading
                showSubmitLoading(true);
                
                // Send request
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                // Hide loading
                showSubmitLoading(false);
                
                if (result.success) {
                    showMinimalPopup('Berhasil', result.message || 'Pengumuman berhasil disimpan', 'success');
                    setTimeout(() => {
                        closeModal();
                        // Reload page to get the latest data
                        window.location.reload();
                    }, 1500);
                } else {
                    let errorMsg = result.message || 'Terjadi kesalahan';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join(', ');
                    }
                    showMinimalPopup('Error', errorMsg, 'error');
                }
                
            } catch (error) {
                console.error('Error saving pengumuman:', error);
                showSubmitLoading(false);
                showMinimalPopup('Error', 'Gagal menyimpan: ' + error.message, 'error');
            }
        }
        
        // Delete pengumuman
        async function deletePengumuman(id) {
            try {
                const response = await fetch(`/pengumuman/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMinimalPopup('Berhasil', result.message || 'Pengumuman berhasil dihapus', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showMinimalPopup('Error', result.message || 'Gagal menghapus pengumuman', 'error');
                }
            } catch (error) {
                console.error('Error deleting pengumuman:', error);
                showMinimalPopup('Error', 'Gagal menghapus pengumuman', 'error');
            }
        }
        
        // Filter data
        function filterData() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            // Filter all data based on search term
            if (searchTerm === '') {
                filteredData = [...allData];
            } else {
                filteredData = allData.filter(item => {
                    return item.judul.toLowerCase().includes(searchTerm) || 
                           item.isi_pesan.toLowerCase().includes(searchTerm);
                });
            }
            
            // Update total items and pages
            totalItems = filteredData.length;
            totalPages = Math.ceil(totalItems / itemsPerPage);
            
            // Reset to first page if current page is out of range
            if (currentPage > totalPages && totalPages > 0) {
                currentPage = 1;
            }
            
            // Update pagination and table
            updatePagination();
        }
        
        // Initialize filter
        function initializeFilter() {
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const filterAll = document.getElementById('filterAll');
            const applyFilterBtn = document.getElementById('applyFilter');
            const resetFilterBtn = document.getElementById('resetFilter');
            
            // Return early if filter elements don't exist
            if (!filterBtn || !filterDropdown || !filterAll || !applyFilterBtn || !resetFilterBtn) {
                console.log('Filter elements not found, skipping filter initialization');
                return;
            }
            
            // Toggle filter dropdown
            filterBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                filterDropdown.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                filterDropdown.classList.remove('show');
            });
            
            // Handle "All" checkbox
            filterAll.addEventListener('change', function() {
                if (this.checked) {
                    // Uncheck all other checkboxes
                    document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                        cb.checked = false;
                    });
                }
            });
            
            // Handle other checkboxes
            document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        // Uncheck "All" checkbox
                        filterAll.checked = false;
                    }
                });
            });
            
            // Apply filter
            applyFilterBtn.addEventListener('click', function() {
                const filterAll = document.getElementById('filterAll');
                const filterUmum = document.getElementById('filterUmum');
                const filterPenting = document.getElementById('filterPenting');
                const filterInternal = document.getElementById('filterInternal');
                
                let activeFilters = [];
                if (filterAll.checked) {
                    activeFilters.push('all');
                } else {
                    if (filterUmum.checked) activeFilters.push('umum');
                    if (filterPenting.checked) activeFilters.push('penting');
                    if (filterInternal.checked) activeFilters.push('internal');
                }
                
                applyFilters(activeFilters);
                filterDropdown.classList.remove('show');
            });
            
            // Reset filter
            resetFilterBtn.addEventListener('click', function() {
                document.getElementById('filterAll').checked = true;
                document.getElementById('filterUmum').checked = false;
                document.getElementById('filterPenting').checked = false;
                document.getElementById('filterInternal').checked = false;
                applyFilters(['all']);
                filterDropdown.classList.remove('show');
            });
        }
        
        // Apply filters
        function applyFilters(filters) {
            // If "all" is selected, show all data
            if (filters.includes('all')) {
                filteredData = [...allData];
            } else {
                // Filter data based on selected categories
                // This is a placeholder - you would implement actual filtering logic here
                filteredData = [...allData];
            }
            
            // Update total items and pages
            totalItems = filteredData.length;
            totalPages = Math.ceil(totalItems / itemsPerPage);
            
            // Reset to first page
            currentPage = 1;
            
            // Update pagination and table
            updatePagination();
        }
        
        // Initialize scroll detection for table
        function initializeScrollDetection() {
            const scrollableTable = document.getElementById('scrollableTable');
            
            if (scrollableTable) {
                // Add scroll event listener
                scrollableTable.addEventListener('scroll', function() {
                    const scrollLeft = scrollableTable.scrollLeft;
                    const maxScroll = scrollableTable.scrollWidth - scrollableTable.clientWidth;
                });
            }
        }
        
        // Show loading state
        function showLoading(show, type = 'general') {
            if (type === 'users') {
                // Handle users loading if needed
                return;
            }
            
            const loadingState = document.getElementById('loadingState');
            const tableContainer = document.getElementById('tableContainer');
            const emptyState = document.getElementById('emptyState');
            const mobileCards = document.getElementById('mobileCards');
            
            if (show) {
                loadingState.classList.remove('hidden');
                if (tableContainer) tableContainer.classList.add('hidden');
                if (mobileCards) mobileCards.classList.add('hidden');
                if (emptyState) emptyState.classList.add('hidden');
            } else {
                loadingState.classList.add('hidden');
            }
        }
        
        function showSubmitLoading(show) {
            const confirmBtnText = document.getElementById('confirmBtnText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const confirmBtn = document.getElementById('confirmBtn');
            
            if (show) {
                confirmBtn.disabled = true;
                confirmBtnText.classList.add('hidden');
                loadingSpinner.classList.remove('hidden');
            } else {
                confirmBtn.disabled = false;
                confirmBtnText.classList.remove('hidden');
                loadingSpinner.classList.add('hidden');
            }
        }
        
        // Modified notification function to match second file
        function showMinimalPopup(title, message, type = 'success') {
            const popup = document.getElementById('notification');
            const popupTitle = popup.querySelector('.minimal-popup-title');
            const popupMessage = popup.querySelector('.minimal-popup-message');
            const popupIcon = popup.querySelector('.minimal-popup-icon span');

            // Set content
            popupTitle.textContent = title;
            popupMessage.textContent = message;

            // Set type
            popup.className = 'minimal-popup show ' + type;

            // Set icon
            if (type === 'success') {
                popupIcon.textContent = 'check';
            } else if (type === 'error') {
                popupIcon.textContent = 'error';
            } else if (type === 'warning') {
                popupIcon.textContent = 'warning';
            }

            // Auto hide after 3 seconds (changed from 5 seconds)
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }
        
        // Keep the old function for backward compatibility
        function showNotification(title, message, type = 'success') {
            showMinimalPopup(title, message, type);
        }
        
        function hideNotif() {
            document.getElementById('notification').classList.remove('show');
        }
        
        // Delete form submission handler
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const id = document.getElementById('deleteId').value;
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menghapus...';
                submitBtn.disabled = true;
                
                try {
                    const response = await fetch(`/pengumuman/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showMinimalPopup('Berhasil', result.message || 'Pengumuman berhasil dihapus', 'success');
                        closeDeleteModal();
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showMinimalPopup('Error', result.message || 'Gagal menghapus pengumuman', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting pengumuman:', error);
                    showMinimalPopup('Error', 'Gagal menghapus pengumuman', 'error');
                } finally {
                    // Reset button state
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });
        }
    </script>
</body>
</html>