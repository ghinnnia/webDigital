<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Orderan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
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
                        display: ["Inter", "sans-serif"],
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
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 1,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }

        body {
            font-family: 'Inter', sans-serif;
        }
        
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
        .payment-table {
            transition: all 0.2s ease;
        }
        
        .payment-table tr:hover {
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
        
        .btn-danger {
            background-color: #ef4444;
            color: white;
            transition: all 0.2s ease;
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
        }
        
        .btn-warning {
            background-color: #f59e0b;
            color: white;
            transition: all 0.2s ease;
        }
        
        .btn-warning:hover {
            background-color: #d97706;
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
        
        .status-partial {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        
        .status-pending {
            background-color: rgba(107, 114, 128, 0.15);
            color: #4b5563;
        }
        
        .status-overdue {
            background-color: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }
        
        /* Work Status Badge Styles */
        .work-status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .work-status-planning {
            background-color: rgba(139, 92, 246, 0.15);
            color: #5b21b6;
        }
        
        .work-status-progress {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }
        
        .work-status-review {
            background-color: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        
        .work-status-completed {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        
        .work-status-onhold {
            background-color: rgba(107, 114, 128, 0.15);
            color: #4b5563;
        }
        
        /* Category Badge Styles */
        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .category-design {
            background-color: rgba(139, 92, 246, 0.15);
            color: #5b21b6;
        }
        
        .category-programming {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }
        
        .category-marketing {
            background-color: rgba(16, 185, 129, 0.15);
            color: #065f46;
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
        .nav-item {
            position: relative;
            overflow: hidden;
        }
        
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
        
        .data-table {
            width: 100%;
            min-width: 1200px;
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
        
        .table-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Minimalist Popup Styles */
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
        
        /* Delete Confirmation Modal */
        .delete-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .delete-modal.show {
            opacity: 1;
            visibility: visible;
        }
        
        .delete-modal-content {
            background-color: white;
            border-radius: 8px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .delete-modal.show .delete-modal-content {
            transform: scale(1);
        }
        
        .delete-modal-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .delete-modal-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: rgba(239, 68, 68, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            color: #ef4444;
        }
        
        .delete-modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
        }
        
        .delete-modal-body {
            color: #64748b;
            margin-bottom: 24px;
        }
        
        .delete-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .notification-bell:hover {
            transform: scale(1.1);
            background: #f1f5f9;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 5px rgba(239, 68, 68, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Notification Panel */
        .notification-panel {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 380px;
            max-width: calc(100vw - 40px);
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .notification-panel.show {
            display: flex;
        }

        .notification-header {
            padding: 16px 20px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .notification-header h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notification-header button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.2s;
        }

        .notification-header button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .notification-list {
            flex: 1;
            overflow-y: auto;
            max-height: 400px;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .notification-item:hover {
            background-color: #f8fafc;
        }

        .notification-item.unread {
            background-color: #eff6ff;
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: #3b82f6;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon.warning {
            background-color: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .notification-icon.danger {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        .notification-icon.success {
            background-color: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .notification-icon.info {
            background-color: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .notification-message {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #94a3b8;
        }

        .notification-empty span {
            font-size: 48px;
            margin-bottom: 12px;
            display: block;
        }

        /* Overlay for notification panel */
        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 999;
            display: none;
        }

        .notification-overlay.show {
            display: block;
        }
    </style>
</head>
<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Container untuk sidebar yang akan dimuat -->
        @include('finance.templet.sider')
        @php
            if (!isset($orders)) {
                $orders = \App\Models\Order::orderBy('id', 'desc')->paginate(10);
            }
        @endphp
        
        <!-- Notification Bell & Panel -->
        <div class="fixed top-4 right-4 z-50 flex items-center gap-2">
            <!-- Notification Bell -->
            <div class="notification-bell" onclick="toggleNotificationPanel()">
                <span class="material-icons-outlined text-gray-600 text-2xl">notifications</span>
                <span id="notificationCount" class="notification-badge" style="display: none;">0</span>
            </div>
        </div>

        <!-- Notification Panel -->
        <div id="notificationPanel" class="notification-panel">
            <div class="notification-header">
                <h3>
                    <span class="material-icons-outlined">notifications</span>
                    Notifikasi
                </h3>
                <button onclick="markAllNotificationsRead()">Tandai semua dibaca</button>
            </div>
            <div id="notificationList" class="notification-list">
                <div class="notification-empty">
                    <span class="material-icons-outlined">notifications_none</span>
                    <p>Memuat notifikasi...</p>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div id="notificationOverlay" class="notification-overlay" onclick="closeNotificationPanel()"></div>
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col main-content">
            <div class="flex-1 p-3 sm:p-8">
                <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Data Orderan</h2>
                
                <!-- Stat Cards Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    <div class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-blue-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-icons-outlined text-primary">people</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Total Lead</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="total-lead-value">{{ $totalLead ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-green-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-icons-outlined text-green-500">trending_up</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Conversion Rate</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="conversion-rate-value">{{ $conversionRate ?? 0 }}%</p>
                        </div>
                    </div>
                    <div class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-purple-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-icons-outlined text-purple-500">person</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Customer</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="customer-value">{{ $uniqueCustomers ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="stat-card bg-card-light rounded-DEFAULT p-2 sm:p-5 flex items-center border border-border-light">
                        <div class="icon-container w-8 h-8 sm:w-12 sm:h-12 bg-orange-100 rounded-lg mr-3 sm:mr-4 flex items-center justify-center">
                            <span class="material-icons-outlined text-orange-500">receipt_long</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="label-text text-xs sm:text-sm text-text-muted-light truncate">Total Order</p>
                            <p class="value-text text-base sm:text-xl font-bold truncate" id="total-order-value">{{ $totalOrders ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="relative w-full md:w-1/3">
                        <form id="searchForm" method="GET" action="{{ route('orders.index') }}">
                            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input id="payment-search" name="q" value="{{ request('q') }}" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari layanan, klien, atau status..." type="text" />
                        </form>
                    </div>
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <div class="relative">
                            <button id="filterBtn" class="px-4 py-2 bg-white border border-border-light text-text-muted-light rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                                <span class="material-icons-outlined text-sm">filter_list</span>
                                Filter
                            </button>
                            <div id="filterDropdown" class="filter-dropdown">
                                <div class="filter-option">
                                    <input type="checkbox" id="filterAll" value="all" checked>
                                    <label for="filterAll">Semua Status</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterPaid" value="paid">
                                    <label for="filterPaid">Lunas</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterPartial" value="partial">
                                    <label for="filterPartial">Sebagian</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterPending" value="pending">
                                    <label for="filterPending">Pending</label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filterOverdue" value="overdue">
                                    <label for="filterOverdue">Terlambat</label>
                                </div>
                                <div class="filter-actions">
                                    <button id="applyFilter" class="filter-apply">Terapkan</button>
                                    <button id="resetFilter" class="filter-reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        @if (auth()->user()->role !== 'finance')
                            <button onclick="openAddModal()" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2 flex-1 md:flex-none">
                                <span class="material-icons-outlined">add</span>
                                <span class="hidden sm:inline">Tambah Orderan</span>
                                <span class="sm:hidden">Tambah</span>
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">receipt_long</span>
                            Data Orderan
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount" class="font-semibold text-text-light">{{ $orders->total() ?? 0 }}</span> pembayaran</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- SCROLLABLE TABLE -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">No</th>
                                            <th style="min-width: 150px;">Invoice</th>
                                            <th style="min-width: 200px;">Nama Project</th>
                                            <th style="min-width: 200px;">Deskripsi</th>
                                            <th style="min-width: 120px;">Harga</th>
                                            <th style="min-width: 150px;">Penanggung Jawab</th>
                                            <th style="min-width: 200px;">Periode Pengerjaan</th>
                                            <th style="min-width: 200px;">Periode Kerjasama</th>
                                            <th style="min-width: 120px;">Status Pengerjaan</th>
                                            <th style="min-width: 120px;">Status Kerjasama</th>
                                            <th style="min-width: 150px;">Progres</th>
                                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payment-table-body">
                                        @foreach($orders as $index => $order)
                                        @php
                                            $status = $order->status ?? 'pending';
                                            $work = $order->work_status ?? 'planning';
                                            $project = $order->invoice ? $order->invoice->projects->first() : null;
                                            $invoiceLabel = $order->invoice_no ?: ($order->invoice_id ? ('Invoice #' . $order->invoice_id) : '-');
                                            $workRaw = $project->status_pengerjaan ?? $work;
                                            $workStatusLabel = match($workRaw) {
                                                'planning' => 'Perencanaan',
                                                'progress', 'dalam_pengerjaan' => 'Dalam Pengerjaan',
                                                'review' => 'Review',
                                                'completed' => 'Selesai',
                                                'pending' => 'Pending',
                                                'selesai' => 'Selesai',
                                                'dibatalkan' => 'Dibatalkan',
                                                'onhold' => 'Ditunda',
                                                default => '-',
                                            };
                                            $progressValue = is_numeric($project->progres ?? null) ? (int) $project->progres : match($workRaw) {
                                                'planning' => 20,
                                                'progress', 'dalam_pengerjaan' => 60,
                                                'review' => 85,
                                                'completed', 'selesai' => 100,
                                                'onhold' => 0,
                                                default => 0,
                                            };
                                            $kerjasamaRaw = $project->status_kerjasama ?? null;
                                            $kerjasamaLabel = $kerjasamaRaw ? ucfirst($kerjasamaRaw) : match($status) {
                                                'paid' => 'Selesai',
                                                'overdue' => 'Ditangguhkan',
                                                default => 'Aktif',
                                            };
                                            $kerjasamaBadge = match($kerjasamaRaw ?: $status) {
                                                'selesai', 'paid' => 'status-paid',
                                                'ditangguhkan', 'overdue' => 'status-overdue',
                                                default => 'status-pending',
                                            };
                                            $penanggungJawab = $project && $project->penanggungJawab ? $project->penanggungJawab->name : '-';
                                            $periodePengerjaan = '-';
                                            if ($project) {
                                                $startPengerjaan = $project->tanggal_mulai_pengerjaan ? $project->tanggal_mulai_pengerjaan->format('Y-m-d') : null;
                                                $endPengerjaan = $project->tanggal_selesai_pengerjaan ? $project->tanggal_selesai_pengerjaan->format('Y-m-d') : null;
                                                $periodePengerjaan = $startPengerjaan && $endPengerjaan
                                                    ? ($startPengerjaan . ' - ' . $endPengerjaan)
                                                    : ($startPengerjaan ?: '-');
                                            }
                                            $periodeKerjasama = '-';
                                            if ($project) {
                                                $startKerjasama = $project->tanggal_mulai_kerjasama ? $project->tanggal_mulai_kerjasama->format('Y-m-d') : null;
                                                $endKerjasama = $project->tanggal_selesai_kerjasama ? $project->tanggal_selesai_kerjasama->format('Y-m-d') : null;
                                                $periodeKerjasama = $startKerjasama && $endKerjasama
                                                    ? ($startKerjasama . ' - ' . $endKerjasama)
                                                    : ($startKerjasama ?: '-');
                                            }
                                        @endphp
                                        <tr>
                                            <td style="min-width: 60px;">{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</td>
                                            <td style="min-width: 150px;">{{ $invoiceLabel }}</td>
                                            <td style="min-width: 200px;">{{ $project->nama ?? $order->layanan ?? '-' }}</td>
                                            <td style="min-width: 200px;" class="truncate-text" title="{{ $project->deskripsi ?? $order->description ?? '-' }}">
                                                {{ \Illuminate\Support\Str::limit($project->deskripsi ?? $order->description ?? '-', 50) }}
                                            </td>
                                            <td style="min-width: 120px;">{{ ($project && $project->harga) ? 'Rp '.number_format($project->harga,0,',','.') : ($order->total ? 'Rp '.number_format($order->total,0,',','.') : '-') }}</td>
                                            <td style="min-width: 150px;">{{ $penanggungJawab }}</td>
                                            <td style="min-width: 200px;" class="periode-pengerjaan">{{ $periodePengerjaan }}</td>
                                            <td style="min-width: 200px;" class="periode-kerjasama">{{ $periodeKerjasama }}</td>
                                            <td style="min-width: 120px;">
                                                <span class="work-status-badge work-status-{{ $workRaw }}">{{ $workStatusLabel }}</span>
                                            </td>
                                            <td style="min-width: 120px;">
                                                <span class="status-badge {{ $kerjasamaBadge }}">{{ $kerjasamaLabel }}</span>
                                            </td>
                                            <td style="min-width: 150px;">
                                                <span class="text-xs text-gray-600">{{ $progressValue }}%</span>
                                            </td>
                                            <td style="min-width: 100px; text-align: center;">
                                                <div class="flex justify-center gap-2">
                                                    <button onclick="openEditModal({{ $order->id }})" class="p-1 rounded-full hover:bg-warning/20 text-warning" title="Edit Order">
                                                        <span class="material-icons-outlined">edit</span>
                                                    </button>
                                                    <button onclick="openDeleteModal({{ $order->id }})" class="p-1 rounded-full hover:bg-danger/20 text-danger" title="Hapus Order">
                                                        <span class="material-icons-outlined">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Mobile Card View -->
                        <div class="mobile-cards space-y-4" id="mobile-cards">
                            @foreach($orders as $index => $order)
                            @php
                                $status = $order->status ?? 'pending';
                                $work = $order->work_status ?? 'planning';
                                $project = $order->invoice ? $order->invoice->projects->first() : null;
                                $category = $order->kategori ?? '';
                                $icon = 'miscellaneous_services';
                                if($category=='programming') $icon='code';
                                if($category=='design') $icon='palette';
                                if($category=='marketing') $icon='trending_up';
                                $workRaw = $project->status_pengerjaan ?? $work;
                                $progressValue = is_numeric($project->progres ?? null) ? (int) $project->progres : match($workRaw) {
                                    'planning' => 20,
                                    'progress', 'dalam_pengerjaan' => 60,
                                    'review' => 85,
                                    'completed', 'selesai' => 100,
                                    default => 0,
                                };
                                $kerjasamaRaw = $project->status_kerjasama ?? null;
                            @endphp
                            <div class="bg-white rounded-lg border border-border-light p-4 shadow-sm payment-card">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="material-icons-outlined text-primary">{{ $icon }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-base">{{ $project->nama ?? $order->layanan }}</h4>
                                            <p class="text-sm text-text-muted-light">{{ $order->price_formatted ?? ($order->price ? 'Rp '.number_format($order->price,0,',','.') : '-') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="openEditModal({{ $order->id }})" class="p-1 rounded-full hover:bg-warning/20 text-warning" title="Edit Order">
                                            <span class="material-icons-outlined">edit</span>
                                        </button>
                                        <button onclick="openDeleteModal({{ $order->id }})" class="p-1 rounded-full hover:bg-danger/20 text-danger" title="Hapus Order">
                                            <span class="material-icons-outlined">delete</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <p class="text-text-muted-light">No</p>
                                        <p class="font-medium">{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light">Invoice</p>
                                        <p class="font-medium">{{ $order->invoice_no ?: ($order->invoice_id ? 'Invoice #'.$order->invoice_id : '-') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light">Nama Project</p>
                                        <p class="font-medium">{{ $project->nama ?? $order->layanan ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light">Harga</p>
                                        <p class="font-medium">{{ ($project && $project->harga) ? 'Rp '.number_format($project->harga,0,',','.') : ($order->total ? 'Rp '.number_format($order->total,0,',','.') : '-') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light">Status Pengerjaan</p>
                                        <p>@if($workRaw == 'planning')<span class="work-status-badge work-status-planning">Perencanaan</span>@elseif($workRaw=='progress' || $workRaw=='dalam_pengerjaan')<span class="work-status-badge work-status-progress">Dalam Pengerjaan</span>@elseif($workRaw=='review')<span class="work-status-badge work-status-review">Review</span>@elseif($workRaw=='completed' || $workRaw=='selesai')<span class="work-status-badge work-status-completed">Selesai</span>@else<span class="work-status-badge work-status-onhold">Ditunda</span>@endif</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light">Status Kerjasama</p>
                                        <p>@if($kerjasamaRaw == 'selesai' || $status == 'paid')<span class="status-badge status-paid">Selesai</span>@elseif($kerjasamaRaw == 'ditangguhkan' || $status=='overdue')<span class="status-badge status-overdue">Ditangguhkan</span>@else<span class="status-badge status-pending">Aktif</span>@endif</p>
                                    </div>
                                    <div>
                                        <p class="text-text-muted-light">Progres</p>
                                        <p class="font-medium">{{ $progressValue }}%</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div id="payment-pagination" class="desktop-pagination">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by digicity.id
            </footer>
        </main>
    </div>

    <!-- Modal Tambah Data Orderan -->
    <div id="addModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Data Orderan</h3>
                    <button onclick="closeAddModal()" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form class="space-y-4" method="POST" action="{{ route('orders.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Layanan</label>
                            <select id="payment-category" name="kategori" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" onchange="updateServiceOptions()">
                                <option value="">Pilih Kategori</option>
                                <option value="design">Desain</option>
                                <option value="programming">Programming</option>
                                <option value="marketing">Digital Marketing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
                            <select id="payment-service" name="layanan" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Layanan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                            <input type="text" name="company_name" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Nama Perusahaan">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" name="order_date" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomer Invoice</label>
                            <input type="text" name="invoice_no" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Nomer Invoice">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Klien</label>
                            <select name="klien" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Klien</option>
                                <option value="PT. Teknologi Maju">PT. Teknologi Maju</option>
                                <option value="CV. Digital Solusi">CV. Digital Solusi</option>
                                <option value="UD. Kreatif Indonesia">UD. Kreatif Indonesia</option>
                                <option value="PT. Inovasi Nusantara">PT. Inovasi Nusantara</option>
                                <option value="CV. Kreatif">CV. Kreatif</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Perusahaan</label>
                            <textarea name="company_address" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Alamat Perusahaan" rows="2"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Deskripsi" rows="2"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                            <input type="number" name="subtotal" step="0.01" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Subtotal">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pajak (%)</label>
                            <input type="number" name="tax" step="0.01" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Pajak">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                            <input type="number" name="total" step="0.01" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Total">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="transfer_bank">Transfer Bank</option>
                                <option value="cash">Tunai</option>
                                <option value="check">Cek</option>
                                <option value="e_wallet">E-Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                            <select name="status" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Pilih Status</option>
                                <option value="paid">Lunas</option>
                                <option value="partial">Sebagian</option>
                                <option value="pending">Pending</option>
                                <option value="overdue">Terlambat</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Data Orderan -->
    <div id="editModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Data Orderan</h3>
                    <button onclick="closeEditModal()" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="editForm" class="space-y-4" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editOrderId" name="id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <input type="text" id="edit-total-display" inputmode="numeric" required class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Rp 0">
                        <input type="hidden" id="edit-total" name="total" value="">
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Invoice -->
    <div id="invoiceDetailModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-6xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Detail Invoice</h3>
                    <button onclick="closeInvoiceDetailModal()" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                
                <!-- Header Invoice -->
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-4">
                        <div>
                            <h4 class="text-base sm:text-lg font-semibold text-text-light mb-2">INVOICE</h4>
                            <p class="text-xs sm:text-sm text-text-muted-light">Nomor: <span id="invoice-no" class="font-medium text-text-light"></span></p>
                            <p class="text-xs sm:text-sm text-text-muted-light">Tanggal: <span id="invoice-date" class="font-medium text-text-light"></span></p>
                        </div>
                        <div class="text-left sm:text-right">
                            <h4 class="text-base sm:text-lg font-semibold text-text-light mb-2">DigiCity</h4>
                            <p class="text-xs sm:text-sm text-text-muted-light">Jl. Teknologi No. 123</p>
                            <p class="text-xs sm:text-sm text-text-muted-light">Jakarta, Indonesia</p>
                            <p class="text-xs sm:text-sm text-text-muted-light">Email: info@digicity.id</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Klien -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Informasi Perusahaan</h5>
                        <div class="space-y-2">
                            <div class="flex flex-col sm:flex-row">
                                <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Nama:</span>
                                <span id="company-name" class="text-xs sm:text-sm text-text-light font-medium"></span>
                            </div>
                            <div class="flex flex-col sm:flex-row">
                                <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Alamat:</span>
                                <span id="company-address" class="text-xs sm:text-sm text-text-light font-medium"></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Informasi Kontak</h5>
                        <div class="space-y-2">
                            <div class="flex flex-col sm:flex-row">
                                <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Nama Klien:</span>
                                <span id="client-name" class="text-xs sm:text-sm text-text-light font-medium"></span>
                            </div>
                            <div class="flex flex-col sm:flex-row">
                                <span class="text-xs sm:text-sm text-text-muted-light sm:w-32">Nomor Order:</span>
                                <span id="order-number" class="text-xs sm:text-sm text-text-light font-medium"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail Items -->
                <div class="mb-4 sm:mb-6">
                    <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Detail Layanan</h5>
                    <div class="scrollable-table-container">
                        <table class="data-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border border-border-light px-2 sm:px-4 py-2 text-left font-semibold text-text-light whitespace-nowrap">No</th>
                                    <th class="border border-border-light px-2 sm:px-4 py-2 text-left font-semibold text-text-light">Deskripsi</th>
                                    <th class="border border-border-light px-2 sm:px-4 py-2 text-center font-semibold text-text-light whitespace-nowrap">Kategori</th>
                                    <th class="border border-border-light px-2 sm:px-4 py-2 text-center font-semibold text-text-light whitespace-nowrap">Harga</th>
                                    <th class="border border-border-light px-2 sm:px-4 py-2 text-center font-semibold text-text-light whitespace-nowrap">Qty</th>
                                    <th class="border border-border-light px-2 sm:px-4 py-2 text-right font-semibold text-text-light whitespace-nowrap">Total</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-items">
                                <!-- Items akan diisi dengan JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ringkasan Pembayaran -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Metode Pembayaran</h5>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <p id="payment-method" class="text-xs sm:text-sm text-text-light"></p>
                        </div>
                    </div>
                    <div>
                        <h5 class="font-semibold text-text-light mb-2 sm:mb-3 text-sm sm:text-base">Ringkasan Orderan</h5>
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-xs sm:text-sm text-text-muted-light">Subtotal:</span>
                                <span id="subtotal" class="text-xs sm:text-sm text-text-light font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs sm:text-sm text-text-muted-light">Pajak (11%):</span>
                                <span id="tax" class="text-xs sm:text-sm text-text-light font-medium"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-border-light">
                                <span class="text-xs sm:text-sm font-semibold text-text-light">Total:</span>
                                <span id="total" class="text-xs sm:text-sm font-semibold text-text-light"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex flex-col sm:flex-row justify-center sm:justify-end gap-2 sm:gap-3 mt-4 sm:mt-6">
                    <button onclick="printInvoice()" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                        <span class="material-icons-outlined">print</span>
                        <span>Cetak</span>
                    </button>
                    <button onclick="downloadInvoice()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center gap-2">
                        <span class="material-icons-outlined">download</span>
                        <span>Download</span>
                    </button>
                    <button onclick="closeInvoiceDetailModal()" class="px-4 py-2 btn-secondary rounded-lg flex items-center gap-2">
                        <span class="material-icons-outlined">close</span>
                        <span>Tutup</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="delete-modal">
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <div class="delete-modal-icon">
                    <span class="material-icons-outlined">warning</span>
                </div>
                <h3 class="delete-modal-title">Konfirmasi Hapus</h3>
            </div>
            <div class="delete-modal-body">
                <p>Apakah Anda yakin ingin menghapus data orderan ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="delete-modal-footer">
                <button onclick="closeDeleteModal()" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                <button onclick="confirmDelete()" class="px-4 py-2 btn-danger rounded-lg">Hapus</button>
            </div>
        </div>
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
        // Data Orderan untuk demo (akan diganti dengan data dari server)
        const paymentData = [
            { no: 1, layanan: "Pembuatan Website", kategori: "programming", harga: "Rp 5.000.000", klien: "PT. Teknologi Maju", awal: "Rp 2.500.000", lunas: "Rp 2.500.000", status: "paid", statusPengerjaan: "completed" },
            { no: 2, layanan: "SEO Optimization", kategori: "marketing", harga: "Rp 3.000.000", klien: "CV. Digital Solusi", awal: "Rp 1.500.000", lunas: "Rp 1.500.000", status: "paid", statusPengerjaan: "completed" },
            { no: 3, layanan: "Manajemen Sosial Media", kategori: "marketing", harga: "Rp 4.000.000", klien: "UD. Kreatif Indonesia", awal: "Rp 2.000.000", lunas: "Rp 0", status: "partial", statusPengerjaan: "progress" },
            { no: 4, layanan: "Pengembangan Aplikasi Mobile", kategori: "programming", harga: "Rp 8.000.000", klien: "PT. Inovasi Nusantara", awal: "Rp 4.000.000", lunas: "Rp 0", status: "pending", statusPengerjaan: "planning" },
            { no: 5, layanan: "Desain UI/UX", kategori: "design", harga: "Rp 7.500.000", klien: "CV. Kreatif", awal: "Rp 2.500.000", lunas: "Rp 0", status: "overdue", statusPengerjaan: "onhold" }
        ];

        // Layanan berdasarkan kategori
        const servicesByCategory = {
            design: [
                "Desain UI/UX",
                "Desain Logo",
                "Desain Brand Identity",
                "Desain Grafis",
                "Desain Kemasan",
                "Desain Buku",
                "Desain Kaos",
                "Desain Interior"
            ],
            programming: [
                "Pembuatan Website",
                "Pengembangan Aplikasi Mobile",
                "Pengembangan Sistem Informasi",
                "Pengembangan API",
                "Integrasi Sistem",
                "Pengembangan E-commerce",
                "Pengembangan CRM",
                "Pengembangan Aplikasi Desktop"
            ],
            marketing: [
                "SEO Optimization",
                "Manajemen Sosial Media",
                "Content Marketing",
                "Email Marketing",
                "Google Ads",
                "Facebook Ads",
                "Instagram Marketing",
                "Digital Marketing Strategy"
            ]
        };

        // Pagination variables
        let paymentCurrentPage = 1;
        const paymentItemsPerPage = 5;
        let paymentFilteredData = [...paymentData];
        let activeFilters = ['all'];
        let searchTerm = '';
        let deleteOrderId = null;

        // ============================================================
        // NOTIFICATION SYSTEM FOR PROJECT DEADLINES
        // ============================================================

        let notifications = [];
        let notificationCheckInterval = null;

        // Function to check project deadlines from table data
        function checkProjectDeadlines() {
            const projects = [];
            const tableRows = document.querySelectorAll('#payment-table-body tr');
            
            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 10) {
                    const projectName = cells[2]?.textContent.trim() || '-';
                    const periodePengerjaan = cells[6]?.textContent.trim() || '-';
                    const periodeKerjasama = cells[7]?.textContent.trim() || '-';
                    const statusPengerjaan = cells[8]?.textContent.trim() || '';
                    const statusKerjasama = cells[9]?.textContent.trim() || '';
                    
                    projects.push({
                        name: projectName,
                        periodePengerjaan: periodePengerjaan,
                        periodeKerjasama: periodeKerjasama,
                        statusPengerjaan: statusPengerjaan,
                        statusKerjasama: statusKerjasama
                    });
                }
            });
            
            const today = new Date();
            const newNotifications = [];
            
            projects.forEach(project => {
                // Check Periode Pengerjaan
                if (project.periodePengerjaan && project.periodePengerjaan !== '-') {
                    const parts = project.periodePengerjaan.split(' - ');
                    if (parts.length === 2) {
                        const startDate = new Date(parts[0]);
                        const endDate = new Date(parts[1]);
                        
                        if (!isNaN(endDate.getTime())) {
                            // Check if project is delayed (end date passed and not completed)
                            if (endDate < today && project.statusPengerjaan !== 'Selesai' && project.statusPengerjaan !== 'completed') {
                                const daysOverdue = Math.ceil((today - endDate) / (1000 * 60 * 60 * 24));
                                const notificationKey = `${project.name}_pengerjaan_overdue`;
                                
                                if (!isNotificationExists(notificationKey)) {
                                    newNotifications.push({
                                        id: notificationKey,
                                        title: '⚠️ Proyek Melebihi Deadline Pengerjaan!',
                                        message: `Proyek "${project.name}" sudah melebihi deadline pengerjaan ${daysOverdue} hari yang lalu. Segera selesaikan!`,
                                        time: new Date(),
                                        type: 'danger',
                                        icon: 'warning',
                                        read: false,
                                        projectName: project.name
                                    });
                                }
                            }
                            
                            // Check if deadline is approaching (3 days left)
                            const daysUntilDeadline = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
                            if (daysUntilDeadline <= 3 && daysUntilDeadline >= 0 && project.statusPengerjaan !== 'Selesai' && project.statusPengerjaan !== 'completed') {
                                const notificationKey = `${project.name}_pengerjaan_approaching`;
                                
                                if (!isNotificationExists(notificationKey)) {
                                    newNotifications.push({
                                        id: notificationKey,
                                        title: '⏰ Deadline Pengerjaan Mendekat!',
                                        message: `Proyek "${project.name}" akan berakhir dalam ${daysUntilDeadline} hari. Pastikan semua tugas selesai tepat waktu.`,
                                        time: new Date(),
                                        type: 'warning',
                                        icon: 'schedule',
                                        read: false,
                                        projectName: project.name
                                    });
                                }
                            }
                        }
                    }
                }
                
                // Check Periode Kerjasama
                if (project.periodeKerjasama && project.periodeKerjasama !== '-') {
                    const parts = project.periodeKerjasama.split(' - ');
                    if (parts.length === 2) {
                        const startDate = new Date(parts[0]);
                        const endDate = new Date(parts[1]);
                        
                        if (!isNaN(endDate.getTime())) {
                            // Check if contract expired
                            if (endDate < today && project.statusKerjasama !== 'Selesai') {
                                const daysExpired = Math.ceil((today - endDate) / (1000 * 60 * 60 * 24));
                                const notificationKey = `${project.name}_kerjasama_expired`;
                                
                                if (!isNotificationExists(notificationKey)) {
                                    newNotifications.push({
                                        id: notificationKey,
                                        title: '⚠️ Masa Kerjasama Telah Berakhir!',
                                        message: `Masa kerjasama untuk proyek "${project.name}" sudah berakhir ${daysExpired} hari yang lalu. Segera perpanjang atau selesaikan kontrak.`,
                                        time: new Date(),
                                        type: 'danger',
                                        icon: 'warning',
                                        read: false,
                                        projectName: project.name
                                    });
                                }
                            }
                            
                            // Check if contract ending soon (7 days left)
                            const daysUntilEnd = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
                            if (daysUntilEnd <= 7 && daysUntilEnd >= 0 && project.statusKerjasama !== 'Selesai') {
                                const notificationKey = `${project.name}_kerjasama_ending`;
                                
                                if (!isNotificationExists(notificationKey)) {
                                    newNotifications.push({
                                        id: notificationKey,
                                        title: '📅 Masa Kerjasama Akan Berakhir',
                                        message: `Masa kerjasama proyek "${project.name}" akan berakhir dalam ${daysUntilEnd} hari. Siapkan perpanjangan kontrak jika diperlukan.`,
                                        time: new Date(),
                                        type: 'info',
                                        icon: 'event',
                                        read: false,
                                        projectName: project.name
                                    });
                                }
                            }
                        }
                    }
                }
            });
            
            // Add new notifications
            if (newNotifications.length > 0) {
                notifications = [...newNotifications, ...notifications];
                saveNotificationsToStorage();
                updateNotificationBell();
                renderNotificationList();
                
                // Show popup for each new notification
                newNotifications.forEach(notif => {
                    showNotificationPopup(notif);
                });
            }
        }

        // Check if notification already exists
        function isNotificationExists(id) {
            return notifications.some(notif => notif.id === id);
        }

        // Save notifications to localStorage
        function saveNotificationsToStorage() {
            localStorage.setItem('project_notifications_finance', JSON.stringify(notifications));
        }

        // Load notifications from localStorage
        function loadNotificationsFromStorage() {
            const stored = localStorage.getItem('project_notifications_finance');
            if (stored) {
                notifications = JSON.parse(stored);
                // Convert string dates back to Date objects
                notifications.forEach(notif => {
                    notif.time = new Date(notif.time);
                });
                updateNotificationBell();
                renderNotificationList();
            } else {
                // Initial check after table loads
                setTimeout(() => {
                    checkProjectDeadlines();
                }, 2000);
            }
        }

        // Update notification bell badge
        function updateNotificationBell() {
            const unreadCount = notifications.filter(n => !n.read).length;
            const badge = document.getElementById('notificationCount');
            
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        // Render notification list in panel
        function renderNotificationList() {
            const container = document.getElementById('notificationList');
            if (!container) return;
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="notification-empty">
                        <span class="material-icons-outlined">notifications_none</span>
                        <p>Tidak ada notifikasi</p>
                    </div>
                `;
                return;
            }
            
            // Sort by time descending (newest first)
            const sortedNotifications = [...notifications].sort((a, b) => b.time - a.time);
            
            container.innerHTML = sortedNotifications.map(notif => `
                <div class="notification-item ${notif.read ? '' : 'unread'}" onclick="markNotificationRead('${notif.id}')">
                    <div class="flex gap-3">
                        <div class="notification-icon ${notif.type}">
                            <span class="material-icons-outlined">${notif.icon === 'warning' ? 'warning' : (notif.icon === 'schedule' ? 'schedule' : (notif.icon === 'event' ? 'event' : 'info'))}</span>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${notif.title}</div>
                            <div class="notification-message">${notif.message}</div>
                            <div class="notification-time">${formatNotificationTime(notif.time)}</div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Format notification time
        function formatNotificationTime(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Baru saja';
            if (diffMins < 60) return `${diffMins} menit yang lalu`;
            if (diffHours < 24) return `${diffHours} jam yang lalu`;
            return `${diffDays} hari yang lalu`;
        }

        // Show popup notification
        function showNotificationPopup(notification) {
            // Create popup element
            const popup = document.createElement('div');
            popup.className = `minimal-popup show ${notification.type}`;
            popup.style.position = 'fixed';
            popup.style.bottom = '20px';
            popup.style.right = '20px';
            popup.style.top = 'auto';
            popup.style.left = 'auto';
            popup.style.zIndex = '1001';
            
            let iconName = 'info';
            if (notification.type === 'danger') iconName = 'warning';
            if (notification.type === 'warning') iconName = 'schedule';
            
            popup.innerHTML = `
                <div class="minimal-popup-icon">
                    <span class="material-icons-outlined">${iconName}</span>
                </div>
                <div class="minimal-popup-content">
                    <div class="minimal-popup-title">${notification.title}</div>
                    <div class="minimal-popup-message">${notification.message}</div>
                </div>
                <button class="minimal-popup-close" onclick="this.closest('.minimal-popup').remove()">
                    <span class="material-icons-outlined text-sm">close</span>
                </button>
            `;
            
            document.body.appendChild(popup);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (popup && popup.parentNode) {
                    popup.classList.remove('show');
                    setTimeout(() => popup.remove(), 300);
                }
            }, 5000);
        }

        // Mark notification as read
        function markNotificationRead(id) {
            const notification = notifications.find(n => n.id === id);
            if (notification) {
                notification.read = true;
                saveNotificationsToStorage();
                updateNotificationBell();
                renderNotificationList();
            }
        }

        // Mark all notifications as read
        function markAllNotificationsRead() {
            notifications.forEach(notif => {
                notif.read = true;
            });
            saveNotificationsToStorage();
            updateNotificationBell();
            renderNotificationList();
        }

        // Toggle notification panel
        function toggleNotificationPanel() {
            const panel = document.getElementById('notificationPanel');
            const overlay = document.getElementById('notificationOverlay');
            
            if (panel.classList.contains('show')) {
                panel.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                panel.classList.add('show');
                overlay.classList.add('show');
                renderNotificationList();
            }
        }

        // Close notification panel
        function closeNotificationPanel() {
            const panel = document.getElementById('notificationPanel');
            const overlay = document.getElementById('notificationOverlay');
            if (panel) panel.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        }

        // Start periodic checking (every 5 minutes)
        function startNotificationChecker() {
            // Check every 5 minutes
            if (notificationCheckInterval) {
                clearInterval(notificationCheckInterval);
            }
            notificationCheckInterval = setInterval(() => {
                checkProjectDeadlines();
            }, 5 * 60 * 1000);
        }

        // Inisialisasi filter
        function initializeFilter() {
            const filterBtn = document.getElementById('filterBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const applyFilterBtn = document.getElementById('applyFilter');
            const resetFilterBtn = document.getElementById('resetFilter');
            const filterAll = document.getElementById('filterAll');
            
            if (filterBtn) {
                filterBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    filterDropdown.classList.toggle('show');
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                if (filterDropdown) filterDropdown.classList.remove('show');
            });
            
            // Prevent dropdown from closing when clicking inside
            if (filterDropdown) {
                filterDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Handle "All" checkbox
            if (filterAll) {
                filterAll.addEventListener('change', function() {
                    if (this.checked) {
                        document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                            cb.checked = false;
                        });
                    }
                });
            }
            
            // Handle other checkboxes
            document.querySelectorAll('.filter-option input[type="checkbox"]:not(#filterAll)').forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked && filterAll) {
                        filterAll.checked = false;
                    }
                });
            });
            
            // Apply filter
            if (applyFilterBtn) {
                applyFilterBtn.addEventListener('click', function() {
                    const filterAll = document.getElementById('filterAll');
                    const filterPaid = document.getElementById('filterPaid');
                    const filterPartial = document.getElementById('filterPartial');
                    const filterPending = document.getElementById('filterPending');
                    const filterOverdue = document.getElementById('filterOverdue');
                    
                    activeFilters = [];
                    if (filterAll && filterAll.checked) {
                        activeFilters.push('all');
                    } else {
                        if (filterPaid && filterPaid.checked) activeFilters.push('paid');
                        if (filterPartial && filterPartial.checked) activeFilters.push('partial');
                        if (filterPending && filterPending.checked) activeFilters.push('pending');
                        if (filterOverdue && filterOverdue.checked) activeFilters.push('overdue');
                    }
                    
                    applyFilters();
                    if (filterDropdown) filterDropdown.classList.remove('show');
                    const visibleCount = getFilteredRows().length;
                    showMinimalPopup('Filter Diterapkan', `Menampilkan ${visibleCount} Orderan`, 'success');
                });
            }
            
            // Reset filter
            if (resetFilterBtn) {
                resetFilterBtn.addEventListener('click', function() {
                    const filterAll = document.getElementById('filterAll');
                    const filterPaid = document.getElementById('filterPaid');
                    const filterPartial = document.getElementById('filterPartial');
                    const filterPending = document.getElementById('filterPending');
                    const filterOverdue = document.getElementById('filterOverdue');
                    
                    if (filterAll) filterAll.checked = true;
                    if (filterPaid) filterPaid.checked = false;
                    if (filterPartial) filterPartial.checked = false;
                    if (filterPending) filterPending.checked = false;
                    if (filterOverdue) filterOverdue.checked = false;
                    activeFilters = ['all'];
                    applyFilters();
                    if (filterDropdown) filterDropdown.classList.remove('show');
                    const visibleCount = getFilteredRows().length;
                    showMinimalPopup('Filter Direset', 'Menampilkan semua Orderan', 'success');
                });
            }
        }

        function getFilteredRows() {
            return paymentFilteredData.filter(row => !row.hiddenByFilter);
        }

        function applyFilters() {
            paymentCurrentPage = 1;
            
            paymentFilteredData = paymentData.filter(item => {
                let statusMatches = false;
                if (activeFilters.includes('all')) {
                    statusMatches = true;
                } else {
                    statusMatches = activeFilters.some(filter => item.status.includes(filter.toLowerCase()));
                }
                
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = item.layanan.toLowerCase().includes(searchLower) || 
                                   item.klien.toLowerCase().includes(searchLower) ||
                                   item.status.toLowerCase().includes(searchLower) ||
                                   item.kategori.toLowerCase().includes(searchLower);
                }
                
                return statusMatches && searchMatches;
            });
            
            renderPaymentTable();
            renderPaymentPagination();
        }

        function updateServiceOptions() {
            const categorySelect = document.getElementById('payment-category');
            const serviceSelect = document.getElementById('payment-service');
            const selectedCategory = categorySelect ? categorySelect.value : '';
            
            if (serviceSelect) {
                serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';
                
                if (selectedCategory && servicesByCategory[selectedCategory]) {
                    servicesByCategory[selectedCategory].forEach(service => {
                        const option = document.createElement('option');
                        option.value = service;
                        option.textContent = service;
                        serviceSelect.appendChild(option);
                    });
                }
            }
        }

        function formatRupiah(value) {
            const numeric = String(value ?? '').replace(/\D/g, '');
            if (!numeric) return 'Rp 0';
            return 'Rp ' + Number(numeric).toLocaleString('id-ID');
        }

        function parseRupiah(value) {
            const numeric = String(value ?? '').replace(/\D/g, '');
            return numeric ? Number(numeric) : 0;
        }

        // Modal functions
        function openAddModal() {
            const modal = document.getElementById('addModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function openEditModal(orderId) {
            fetch(`{{ route('orders.show', '') }}/${orderId}`, {
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch order');
                return response.json();
            })
            .then(order => {
                const editForm = document.getElementById('editForm');
                if (editForm) {
                    editForm.action = `{{ route('orders.update', '') }}/${orderId}`;
                }
                
                const editOrderId = document.getElementById('editOrderId');
                if (editOrderId) editOrderId.value = order.id;
                
                const totalValue = parseRupiah(order.total || 0);
                const editTotal = document.getElementById('edit-total');
                const editTotalDisplay = document.getElementById('edit-total-display');
                if (editTotal) editTotal.value = totalValue;
                if (editTotalDisplay) editTotalDisplay.value = formatRupiah(totalValue);
                
                const editModal = document.getElementById('editModal');
                if (editModal) {
                    editModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMinimalPopup('Error', 'Gagal memuat data order', 'error');
            });
        }

        function closeEditModal() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function openDeleteModal(orderId) {
            deleteOrderId = orderId;
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) deleteModal.classList.add('show');
        }

        function closeDeleteModal() {
            deleteOrderId = null;
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) deleteModal.classList.remove('show');
        }

        function confirmDelete() {
            if (!deleteOrderId) return;
            
            const confirmBtn = document.querySelector('[onclick="confirmDelete()"]');
            if (confirmBtn) confirmBtn.disabled = true;
            
            fetch(`{{ route('orders.destroy', '') }}/${deleteOrderId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to delete order');
                return response.json();
            })
            .then(data => {
                showMinimalPopup('Berhasil', 'Data orderan berhasil dihapus', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                showMinimalPopup('Error', 'Gagal menghapus data orderan', 'error');
            })
            .finally(() => {
                if (confirmBtn) confirmBtn.disabled = false;
                closeDeleteModal();
            });
        }

        function openInvoiceDetailModal(paymentNo) {
            // Implement if needed
            showMinimalPopup('Info', 'Fitur detail invoice sedang dikembangkan', 'warning');
        }

        function closeInvoiceDetailModal() {
            const modal = document.getElementById('invoiceDetailModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function printInvoice() {
            window.print();
        }

        function downloadInvoice() {
            showMinimalPopup('Info', 'Fitur download akan segera tersedia', 'warning');
        }

        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');
            const invoiceDetailModal = document.getElementById('invoiceDetailModal');
            
            if (event.target == addModal) closeAddModal();
            if (event.target == editModal) closeEditModal();
            if (event.target == deleteModal) closeDeleteModal();
            if (event.target == invoiceDetailModal) closeInvoiceDetailModal();
        }
        
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
                closeInvoiceDetailModal();
                closeNotificationPanel();
            }
        });

        function renderPaymentTable() {
            const tableBody = document.getElementById('payment-table-body');
            const mobileCards = document.getElementById('mobile-cards');
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            if (mobileCards) mobileCards.innerHTML = '';
            
            const startIndex = (paymentCurrentPage - 1) * paymentItemsPerPage;
            const endIndex = Math.min(startIndex + paymentItemsPerPage, paymentFilteredData.length);
            
            for (let i = startIndex; i < endIndex; i++) {
                const item = paymentFilteredData[i];
                
                const row = document.createElement('tr');
                row.className = 'payment-row';
                row.setAttribute('data-id', item.no);
                row.setAttribute('data-layanan', item.layanan);
                row.setAttribute('data-kategori', item.kategori);
                row.setAttribute('data-harga', item.harga);
                row.setAttribute('data-klien', item.klien);
                row.setAttribute('data-awal', item.awal);
                row.setAttribute('data-lunas', item.lunas);
                row.setAttribute('data-status', item.status);
                row.setAttribute('data-statusPengerjaan', item.statusPengerjaan);
                
                let statusBadge = '';
                switch(item.status) {
                    case 'paid': statusBadge = '<span class="status-badge status-paid">Lunas</span>'; break;
                    case 'partial': statusBadge = '<span class="status-badge status-partial">Sebagian</span>'; break;
                    case 'pending': statusBadge = '<span class="status-badge status-pending">Pending</span>'; break;
                    case 'overdue': statusBadge = '<span class="status-badge status-overdue">Terlambat</span>'; break;
                }
                
                let workStatusBadge = '';
                switch(item.statusPengerjaan) {
                    case 'planning': workStatusBadge = '<span class="work-status-badge work-status-planning">Perencanaan</span>'; break;
                    case 'progress': workStatusBadge = '<span class="work-status-badge work-status-progress">Sedang Dikerjakan</span>'; break;
                    case 'review': workStatusBadge = '<span class="work-status-badge work-status-review">Review</span>'; break;
                    case 'completed': workStatusBadge = '<span class="work-status-badge work-status-completed">Selesai</span>'; break;
                    case 'onhold': workStatusBadge = '<span class="work-status-badge work-status-onhold">Ditunda</span>'; break;
                }
                
                let categoryBadge = '';
                switch(item.kategori) {
                    case 'design': categoryBadge = '<span class="category-badge category-design">Desain</span>'; break;
                    case 'programming': categoryBadge = '<span class="category-badge category-programming">Programming</span>'; break;
                    case 'marketing': categoryBadge = '<span class="category-badge category-marketing">Digital Marketing</span>'; break;
                }

                const invoiceLabel = item.invoiceNo || `INV-${String(item.no).padStart(3, '0')}`;
                const kerjasamaLabel = item.status === 'paid' ? 'Selesai' : (item.status === 'overdue' ? 'Ditangguhkan' : 'Aktif');
                const kerjasamaBadge = item.status === 'paid' ? 'status-paid' : (item.status === 'overdue' ? 'status-overdue' : 'status-pending');
                const progresValue = item.statusPengerjaan === 'completed' ? 100 :
                    (item.statusPengerjaan === 'review' ? 85 :
                    (item.statusPengerjaan === 'progress' ? 60 :
                    (item.statusPengerjaan === 'planning' ? 20 : 0)));
                
                row.innerHTML = `
                    <td style="min-width: 60px;">${item.no}</td>
                    <td style="min-width: 150px;">${invoiceLabel}</td>
                    <td style="min-width: 200px;">${item.layanan}</td>
                    <td style="min-width: 200px;">${item.layanan}</td>
                    <td style="min-width: 120px;">${item.harga}</td>
                    <td style="min-width: 150px;">-</td>
                    <td style="min-width: 200px;">-</td>
                    <td style="min-width: 200px;">-</td>
                    <td style="min-width: 120px;">${workStatusBadge}</td>
                    <td style="min-width: 120px;"><span class="status-badge ${kerjasamaBadge}">${kerjasamaLabel}</span></td>
                    <td style="min-width: 150px;"><span class="text-xs text-gray-600">${progresValue}%</span></td>
                    <td style="min-width: 100px; text-align: center;">
                        <div class="flex justify-center gap-2">
                            <button onclick="openEditModal(${item.no})" class="p-1 rounded-full hover:bg-warning/20 text-warning" title="Edit Order">
                                <span class="material-icons-outlined">edit</span>
                            </button>
                            <button onclick="openDeleteModal(${item.no})" class="p-1 rounded-full hover:bg-danger/20 text-danger" title="Hapus Order">
                                <span class="material-icons-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
                
                if (mobileCards) {
                    const card = document.createElement('div');
                    card.className = 'bg-white rounded-lg border border-border-light p-4 shadow-sm payment-card';
                    
                    let icon = 'miscellaneous_services';
                    if (item.kategori === 'programming') icon = 'code';
                    else if (item.kategori === 'design') icon = 'palette';
                    else if (item.kategori === 'marketing') icon = 'trending_up';
                    
                    card.innerHTML = `
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-icons-outlined text-primary">${icon}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-base">${item.layanan}</h4>
                                    <p class="text-sm text-text-muted-light">${item.harga}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openEditModal(${item.no})" class="p-1 rounded-full hover:bg-warning/20 text-warning" title="Edit Order">
                                    <span class="material-icons-outlined">edit</span>
                                </button>
                                <button onclick="openDeleteModal(${item.no})" class="p-1 rounded-full hover:bg-danger/20 text-danger" title="Hapus Order">
                                    <span class="material-icons-outlined">delete</span>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><p class="text-text-muted-light">No</p><p class="font-medium">${item.no}</p></div>
                            <div><p class="text-text-muted-light">Invoice</p><p class="font-medium">${invoiceLabel}</p></div>
                            <div><p class="text-text-muted-light">Nama Project</p><p class="font-medium">${item.layanan}</p></div>
                            <div><p class="text-text-muted-light">Harga</p><p class="font-medium">${item.harga}</p></div>
                            <div><p class="text-text-muted-light">Status Pengerjaan</p><p>${workStatusBadge}</p></div>
                            <div><p class="text-text-muted-light">Status Kerjasama</p><p><span class="status-badge ${kerjasamaBadge}">${kerjasamaLabel}</span></p></div>
                            <div><p class="text-text-muted-light">Progres</p><p class="font-medium">${progresValue}%</p></div>
                        </div>
                    `;
                    
                    mobileCards.appendChild(card);
                }
            }
            
            const totalCount = document.getElementById('totalCount');
            if (totalCount) totalCount.textContent = paymentFilteredData.length;
        }

        function renderPaymentPagination() {
            const pagination = document.getElementById('payment-pagination');
            if (!pagination) return;
            
            const totalPages = Math.ceil(paymentFilteredData.length / paymentItemsPerPage);
            
            let paginationHtml = '<div class="flex justify-center items-center gap-2 mt-6">';
            paginationHtml += `<button class="desktop-nav-btn" onclick="goToPage(${paymentCurrentPage - 1})" ${paymentCurrentPage === 1 ? 'disabled' : ''}>&laquo;</button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                paginationHtml += `<button class="desktop-page-btn ${i === paymentCurrentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
            }
            
            paginationHtml += `<button class="desktop-nav-btn" onclick="goToPage(${paymentCurrentPage + 1})" ${paymentCurrentPage === totalPages || totalPages === 0 ? 'disabled' : ''}>&raquo;</button>`;
            paginationHtml += '</div>';
            
            pagination.innerHTML = paginationHtml;
        }

        function goToPage(page) {
            const totalPages = Math.ceil(paymentFilteredData.length / paymentItemsPerPage);
            if (page < 1 || page > totalPages) return;
            paymentCurrentPage = page;
            renderPaymentTable();
            renderPaymentPagination();
            
            const scrollableTable = document.getElementById('scrollableTable');
            if (scrollableTable) scrollableTable.scrollLeft = 0;
        }

        function filterPayments() {
            const searchInput = document.getElementById('payment-search');
            searchTerm = searchInput ? searchInput.value.trim() : '';
            applyFilters();
        }

        function showMinimalPopup(title, message, type = 'success') {
            const popup = document.getElementById('minimalPopup');
            if (!popup) return;
            
            const popupTitle = popup.querySelector('.minimal-popup-title');
            const popupMessage = popup.querySelector('.minimal-popup-message');
            const popupIcon = popup.querySelector('.minimal-popup-icon span');
            
            if (popupTitle) popupTitle.textContent = title;
            if (popupMessage) popupMessage.textContent = message;
            
            popup.className = 'minimal-popup show ' + type;
            
            if (popupIcon) {
                if (type === 'success') popupIcon.textContent = 'check';
                else if (type === 'error') popupIcon.textContent = 'error';
                else if (type === 'warning') popupIcon.textContent = 'warning';
            }
            
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }
        
        document.querySelector('.minimal-popup-close')?.addEventListener('click', function() {
            const popup = document.getElementById('minimalPopup');
            if (popup) popup.classList.remove('show');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('payment-search');
            if (search) {
                search.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const searchForm = document.getElementById('searchForm');
                        if (searchForm) searchForm.submit();
                    }
                });
            }
            
            initializeFilter();
            loadNotificationsFromStorage();
            startNotificationChecker();
            
            // Trigger initial check after table loads
            setTimeout(() => {
                checkProjectDeadlines();
            }, 3000);
            
            const editForm = document.getElementById('editForm');
            if (editForm) {
                const editTotalDisplay = document.getElementById('edit-total-display');
                const editTotalHidden = document.getElementById('edit-total');

                if (editTotalDisplay && editTotalHidden) {
                    editTotalDisplay.addEventListener('input', function() {
                        const numericValue = parseRupiah(this.value);
                        editTotalHidden.value = numericValue;
                        this.value = formatRupiah(numericValue);
                    });

                    editTotalDisplay.addEventListener('blur', function() {
                        const numericValue = parseRupiah(this.value);
                        this.value = formatRupiah(numericValue);
                    });
                }

                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const orderId = document.getElementById('editOrderId')?.value;
                    const formattedInput = document.getElementById('edit-total-display');
                    const hiddenInput = document.getElementById('edit-total');
                    const numericTotal = parseRupiah(formattedInput ? formattedInput.value : 0);

                    if (hiddenInput) hiddenInput.value = numericTotal;

                    if (numericTotal < 0) {
                        showMinimalPopup('Error', 'Harga tidak valid', 'error');
                        return;
                    }

                    const formData = new FormData(this);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    
                    fetch(`{{ route('orders.update', '') }}/${orderId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to update order');
                        return response.json();
                    })
                    .then(data => {
                        showMinimalPopup('Berhasil', 'Data orderan berhasil diupdate', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMinimalPopup('Error', 'Gagal mengupdate data orderan', 'error');
                    });
                });
            }
            
            const addForm = document.querySelector('form[action="{{ route("orders.store") }}"]');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    
                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to create order');
                        return response.json();
                    })
                    .then(data => {
                        showMinimalPopup('Berhasil', 'Data orderan berhasil ditambahkan', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMinimalPopup('Error', 'Gagal menambahkan data orderan', 'error');
                    });
                });
            }
        });
    </script>
</body>
</html>