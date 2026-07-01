<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Data Layanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
     <link rel="icon" type="image/png" href="{{ asset('logo1.jpeg') }}">
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
        
        /* Modal styles */
        .modal {
            transition: opacity 0.25s ease;
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
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
            min-width: 100px;
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
        
        /* Minimalist Popup Styles - Updated to match second file */
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
        
        /* Hidden class for filtering */
        .hidden-by-filter {
            display: none !important;
        }
        
        /* Image upload styles */
        .image-upload-container {
            position: relative;
            width: 100%;
            height: 200px;
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            overflow: hidden;
        }
        
        .image-upload-container:hover {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.05);
        }
        
        .image-upload-container.has-image {
            border-style: solid;
            border-color: #e2e8f0;
        }
        
        .image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        
        .image-upload-container.has-image .image-preview {
            display: block;
        }
        
        .image-upload-container.has-image .upload-placeholder {
            display: none;
        }
        
        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: #64748b;
        }
        
        .upload-icon {
            font-size: 48px;
            color: #94a3b8;
        }
        
        .upload-text {
            font-size: 14px;
            text-align: center;
        }
        
        .remove-image {
            position: absolute;
            top: 8px;
            right: 8px;
            background-color: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .image-upload-container.has-image:hover .remove-image {
            opacity: 1;
        }
        
        .remove-image:hover {
            background-color: rgba(220, 38, 38, 0.9);
        }
        
        /* Table image styles */
        .table-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .table-image:hover {
            transform: scale(1.05);
        }
        
        /* Card image styles */
        .card-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .card-image:hover {
            transform: scale(1.05);
        }
        
        /* Deskripsi truncated styles */
        .deskripsi-truncated {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Read more link styles */
        .read-more-link {
            color: #3b82f6;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.2s ease;
            font-size: 14px;
            margin-left: 4px;
        }
        
        .read-more-link:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        
        /* Mobile card styles - Original design */
        .mobile-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        
        .mobile-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .mobile-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .mobile-card-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 12px;
            cursor: pointer;
        }
        
        .mobile-card-info {
            flex: 1;
        }
        
        .mobile-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 4px 0;
        }
        
        .mobile-card-price {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }
        
        .mobile-card-actions {
            display: flex;
            gap: 8px;
        }
        
        .mobile-card-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .mobile-card-action-btn.edit {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .mobile-card-action-btn.edit:hover {
            background-color: rgba(59, 130, 246, 0.2);
        }
        
        .mobile-card-action-btn.delete {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .mobile-card-action-btn.delete:hover {
            background-color: rgba(239, 68, 68, 0.2);
        }
        
        .mobile-card-description {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }
        
        .mobile-card-description-label {
            font-size: 12px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        
        .mobile-card-description-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
        }
        
        /* Description panel styles */
        .description-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
        }
        
        .description-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .description-panel-title {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .description-panel-content {
            font-size: 14px;
            color: #1e293b;
            line-height: 1.5;
        }
        
        .description-panel-content.truncated {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Mobile description box */
        .mobile-description-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
        }
        
        .mobile-description-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .mobile-description-box-title {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .mobile-description-box-content {
            font-size: 14px;
            color: #1e293b;
            line-height: 1.5;
        }
        
        .mobile-description-box-content.truncated {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Image Modal Styles */
        .image-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .image-modal.show {
            opacity: 1;
            visibility: visible;
        }
        
        .image-modal-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
        }
        
        .image-modal-img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }
        
        .image-modal-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 32px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .image-modal-close:hover {
            transform: scale(1.1);
        }
        
        /* Detail Modal Styles */
        .detail-modal {
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
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .detail-modal.show {
            opacity: 1;
            visibility: visible;
        }
        
        .detail-modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .detail-modal-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .detail-modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .detail-modal-close {
            background: none;
            border: none;
            color: #64748b;
            font-size: 24px;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .detail-modal-close:hover {
            color: #1e293b;
        }
        
        .detail-modal-body {
            padding: 20px;
        }
        
        .detail-image-container {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .detail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .detail-info {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 16px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #64748b;
        }
        
        .detail-value {
            color: #1e293b;
        }
        
        .detail-description {
            grid-column: 1 / -1;
            margin-top: 8px;
            line-height: 1.6;
        }
        
        /* Search and Add button container */
        .search-add-container {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        
        .search-container {
            flex: 1;
            position: relative;
        }
        
        .add-button-container {
            flex-shrink: 0;
        }
    </style>
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-display bg-background-light text-text-light">
    <div class="flex min-h-screen">
        <!-- Menggunakan template header -->
        @include('admin/templet/sider')
        <div class="flex-1 flex flex-col main-content">
            <div class="flex-1 p-3 sm:p-8">
                 <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Daftar layanan</h2>
                
                <!-- Search and Add Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="search-add-container w-full">
                        <div class="search-container">
                            <span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input id="searchInput" class="w-full pl-10 pr-4 py-2 bg-white border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-primary form-input" placeholder="Cari nama layanan atau deskripsi..." type="text" />
                        </div>
                        <div class="add-button-container">
                            <button id="tambahLayananBtn" class="px-4 py-2 btn-primary rounded-lg flex items-center gap-2">
                                <span class="material-icons-outlined">add</span>
                                <span class="hidden sm:inline">Tambah Layanan</span>
                                <span class="sm:hidden">Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Data Table Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">
                            <span class="material-icons-outlined text-primary">miscellaneous_services</span>
                            Data Layanan
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-text-muted-light">Total: <span id="totalCount" class="font-semibold text-text-light">{{ count($layanans) }}</span> layanan</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        @php
                            $resolveFotoUrl = function ($foto) {
                                if (!$foto) {
                                    return '';
                                }

                                if (filter_var($foto, FILTER_VALIDATE_URL)) {
                                    return $foto;
                                }

                                if (str_starts_with($foto, '/storage/')) {
                                    return asset(ltrim($foto, '/'));
                                }

                                if (str_starts_with($foto, 'storage/')) {
                                    return asset($foto);
                                }

                                return asset('storage/' . ltrim($foto, '/'));
                            };
                        @endphp

                        <!-- SCROLLABLE TABLE -->
                        <div class="desktop-table">
                            <div class="scrollable-table-container scroll-indicator table-shadow" id="scrollableTable">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 50px;">No</th>
                                            <th style="min-width: 80px;">Foto</th>
                                            <th style="min-width: 80px;">Nama Layanan</th>
                                            <th style="min-width: 80px;">Deskripsi</th>
                                            <th style="min-width: 70px;">HPP</th>
                                            <th style="min-width: 70px;">Harga</th>
                                            <th style="min-width: 100px; ">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="desktopTableBody">
                                        @if(isset($layanans) && count($layanans) > 0)
                                            @php $no = 1; @endphp
                                            @foreach($layanans as $layanan)
                                                <tr class="layanan-row" 
                                                    data-id="{{ $layanan->id }}" 
                                                    data-nama="{{ $layanan->nama_layanan }}" 
                                                    data-deskripsi="{{ $layanan->deskripsi }}" 
                                                    data-harga="{{ $layanan->harga }}" 
                                                    data-hpp="{{ $layanan->hpp }}" 
                                                    data-foto="{{ $layanan->foto }}"
                                                    data-foto-url="{{ $resolveFotoUrl($layanan->foto) }}">
                                                    <td style="min-width: 60px;">{{ $no++ }}</td>
                                                    <td style="min-width: 80px;">
                                                        @if($layanan->foto)
                                                            <img src="{{ $resolveFotoUrl($layanan->foto) }}" alt="{{ $layanan->nama_layanan }}" class="table-image" onclick="showImageModal('{{ $resolveFotoUrl($layanan->foto) }}')">
                                                        @else
                                                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                                <span class="material-icons-outlined text-gray-400">image</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td style="min-width: 200px;">{{ $layanan->nama_layanan }}</td>
                                                    <td style="min-width: 250px;">
                                                        <div class="deskripsi-truncated" title="{{ $layanan->deskripsi }}">
                                                            {{ $layanan->deskripsi }}
                                                        </div>
                                                    </td>
                                                    <td style="min-width: 150px;">Rp. {{ number_format($layanan->hpp, 0, ',', '.') }}</td>
                                                    <td style="min-width: 150px;">Rp. {{ number_format($layanan->harga, 0, ',', '.') }}</td>
                                                    <td style="min-width: 100px; text-align: center;">
                                                        <div class="flex justify-center gap-2">
                                                            <button class="detail-btn p-1 rounded-full hover:bg-blue-500/20 text-blue-600" 
                                                                    data-id="{{ $layanan->id }}"
                                                                    data-nama="{{ $layanan->nama_layanan }}"
                                                                    data-deskripsi="{{ $layanan->deskripsi }}"
                                                                    data-harga="{{ $layanan->harga }}"
                                                                    data-hpp="{{ $layanan->hpp }}"
                                                                    data-foto="{{ $layanan->foto }}"
                                                                    data-foto-url="{{ $resolveFotoUrl($layanan->foto) }}"
                                                                    title="Lihat Detail">
                                                                <span class="material-icons-outlined">visibility</span>
                                                            </button>
                                                            <button class="edit-btn p-1 rounded-full hover:bg-primary/20 text-gray-700" 
                                                                    data-id="{{ $layanan->id }}"
                                                                    data-nama="{{ $layanan->nama_layanan }}"
                                                                    data-deskripsi="{{ $layanan->deskripsi }}"
                                                                    data-harga="{{ $layanan->harga }}"
                                                                    data-hpp="{{ $layanan->hpp }}"
                                                                    data-foto="{{ $layanan->foto }}"
                                                                    data-foto-url="{{ $resolveFotoUrl($layanan->foto) }}"
                                                                    title="Edit">
                                                                <span class="material-icons-outlined">edit</span>
                                                            </button>
                                                            <button class="delete-btn p-1 rounded-full hover:bg-red-500/20 text-gray-700" 
                                                                    data-id="{{ $layanan->id }}"
                                                                    title="Hapus">
                                                                <span class="material-icons-outlined">delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    Tidak ada data layanan
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Mobile Card View -->
                        <div class="mobile-cards" id="mobile-cards">
                            @if(isset($layanans) && count($layanans) > 0)
                                @php $no = 1; @endphp
                                @foreach($layanans as $layanan)
                                    <div class="mobile-card layanan-card" 
                                         data-id="{{ $layanan->id }}" 
                                         data-nama="{{ $layanan->nama_layanan }}" 
                                         data-deskripsi="{{ $layanan->deskripsi }}" 
                                         data-harga="{{ $layanan->harga }}" 
                                         data-hpp="{{ $layanan->hpp }}" 
                                         data-foto="{{ $layanan->foto }}"
                                         data-foto-url="{{ $resolveFotoUrl($layanan->foto) }}">
                                        <div class="mobile-card-header">
                                            @if($layanan->foto)
                                                <img src="{{ $resolveFotoUrl($layanan->foto) }}" alt="{{ $layanan->nama_layanan }}" class="mobile-card-image" onclick="showImageModal('{{ $resolveFotoUrl($layanan->foto) }}')">
                                            @else
                                                <div class="w-15 h-15 bg-gray-200 rounded-lg flex items-center justify-center" style="width: 60px; height: 60px;">
                                                    <span class="material-icons-outlined text-gray-400 text-2xl">image</span>
                                                </div>
                                            @endif
                                            <div class="mobile-card-info">
                                                <h4 class="mobile-card-title">{{ $layanan->nama_layanan }}</h4>
                                                <p class="mobile-card-price">
                                                    HPP: Rp. {{ number_format($layanan->hpp, 0, ',', '.') }}<br>
                                                    Harga: Rp. {{ number_format($layanan->harga, 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <div class="mobile-card-actions">
                                                <button class="mobile-card-action-btn edit" 
                                                        data-id="{{ $layanan->id }}"
                                                        data-nama="{{ $layanan->nama_layanan }}"
                                                        data-deskripsi="{{ $layanan->deskripsi }}"
                                                        data-harga="{{ $layanan->harga }}"
                                                        data-hpp="{{ $layanan->hpp }}"
                                                        data-foto="{{ $layanan->foto }}"
                                                        data-foto-url="{{ $resolveFotoUrl($layanan->foto) }}"
                                                        title="Edit">
                                                    <span class="material-icons-outlined" style="font-size: 18px;">edit</span>
                                                </button>
                                                <button class="mobile-card-action-btn delete" 
                                                        data-id="{{ $layanan->id }}"
                                                        title="Hapus">
                                                    <span class="material-icons-outlined" style="font-size: 18px;">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Mobile Description Box -->
                                        <div class="mobile-description-box">
                                            <div class="mobile-description-box-header">
                                                <div class="mobile-description-box-title">Deskripsi</div>
                                                @if(strlen($layanan->deskripsi) > 150)
                                                    <span class="read-more-link" onclick="toggleDescription(this)">Baca selengkapnya</span>
                                                @endif
                                            </div>
                                            <div class="mobile-description-box-content truncated" id="desc-{{ $layanan->id }}">
                                                {{ $layanan->deskripsi }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="bg-white rounded-lg border border-border-light p-8 text-center">
                                    <span class="material-icons-outlined text-4xl text-gray-300 mb-2">miscellaneous_services</span>
                                    <p class="text-gray-500">Tidak ada data layanan</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Pagination -->
                        <div id="paginationContainer" class="desktop-pagination">
                            <button id="prevPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_left</span>
                            </button>
                            <div id="pageNumbers" class="flex gap-1">
                                <!-- Page numbers will be generated by JavaScript -->
                            </div>
                            <button id="nextPage" class="desktop-nav-btn">
                                <span class="material-icons-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="text-center p-4 bg-gray-100 text-text-muted-light text-sm border-t border-border-light">
                Copyright ©2025 by Digital Kolaborasi.id
            </footer>
        </div>
    </div>

    <!-- Modal Tambah Layanan -->
    <div id="tambahLayananModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Layanan Baru</h3>
                    <button id="closeModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form action="{{ route('admin.layanan.store') }}" method="POST" id="tambahLayananForm" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                        <input type="text" name="nama_layanan" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama layanan" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">HPP (Harga Pokok Produksi)</label>
                            <input type="text" inputmode="numeric" id="addHpp" name="hpp" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan HPP" value="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                            <input type="text" inputmode="numeric" id="addHarga" name="harga" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan harga jual" value="0">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan deskripsi layanan" required></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                        <div class="image-upload-container" id="tambahFotoContainer">
                            <img src="" alt="Preview" class="image-preview" id="tambahFotoPreview">
                            <div class="upload-placeholder">
                                <span class="material-icons-outlined upload-icon">cloud_upload</span>
                                <p class="upload-text">Klik untuk mengunggah foto</p>
                                <p class="text-xs text-gray-400">JPG, PNG (Maks. 5MB)</p>
                            </div>
                            <button type="button" class="remove-image" id="tambahRemoveFoto">
                                <span class="material-icons-outlined text-sm">close</span>
                            </button>
                        </div>
                        <input type="file" name="foto" id="tambahFotoInput" class="hidden" accept="image/*">
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="batalTambahBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal Edit Layanan -->
<div id="editLayananModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Layanan</h3>
                <button id="closeEditModalBtn" class="text-gray-800 hover:text-gray-500">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form method="POST" id="editLayananForm" class="space-y-4" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editId" name="id">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                        <input type="text" id="editNamaLayanan" name="nama_layanan" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan nama layanan" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HPP (Harga Pokok)</label>
                        <input type="text" inputmode="numeric" id="editHpp" name="hpp" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan HPP">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                        <input type="text" inputmode="numeric" id="editHarga" name="harga" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan harga jual">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea id="editDeskripsi" name="deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Masukkan deskripsi layanan" required></textarea>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <div class="image-upload-container" id="editFotoContainer">
                        <img src="" alt="Preview" class="image-preview" id="editFotoPreview">
                        <div class="upload-placeholder">
                            <span class="material-icons-outlined upload-icon">cloud_upload</span>
                            <p class="upload-text">Klik untuk mengunggah foto</p>
                            <p class="text-xs text-gray-400">JPG, PNG, GIF (Maks. 2MB)</p>
                        </div>
                        <button type="button" class="remove-image" id="editRemoveFoto">
                            <span class="material-icons-outlined text-sm">close</span>
                        </button>
                    </div>
                    <input type="file" name="foto" id="editFotoInput" class="hidden" accept="image/*">
                    <input type="hidden" id="editCurrentFoto" name="current_foto">
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" id="batalEditBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 btn-primary rounded-lg">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Modal Delete Konfirmasi -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                    <button id="closeDeleteModalBtn" class="text-gray-800 hover:text-gray-500">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <form id="deleteForm" method="POST" action="{{ route('admin.layanan.delete', '') }}">
                    @csrf
                    @method('DELETE')
                    <div class="mb-6">
                        <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus layanan ini?</p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        <input type="hidden" id="deleteId" name="id">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="batalDeleteBtn" class="px-4 py-2 btn-secondary rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <div class="image-modal-content">
            <button class="image-modal-close" onclick="closeImageModal()">
                <span class="material-icons-outlined">close</span>
            </button>
            <img src="" alt="Service Image" class="image-modal-img" id="imageModalImg">
        </div>
    </div>

    <!-- Detail Modal - Updated for HPP -->
    <div id="detailModal" class="detail-modal">
        <div class="detail-modal-content">
            <div class="detail-modal-header">
                <h3 class="detail-modal-title">Detail Layanan</h3>
                <button class="detail-modal-close" onclick="closeDetailModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="detail-modal-body">
                <div class="detail-image-container" id="detailImageContainer">
                    <img src="" alt="Service Image" class="detail-image" id="detailImage">
                </div>
                <div class="detail-info">
                    <div class="detail-label">Nama Layanan</div>
                    <div class="detail-value" id="detailNama"></div>
                    
                    <div class="detail-label">HPP (Harga Pokok)</div>
                    <div class="detail-value" id="detailHpp"></div>
                    
                    <div class="detail-label">Harga Jual</div>
                    <div class="detail-value" id="detailHarga"></div>
                    
                    <div class="detail-label">Deskripsi</div>
                    <div class="detail-description" id="detailDeskripsi"></div>
                </div>
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
        // Inisialisasi variabel untuk pagination dan search
        let currentPage = 1;
        const itemsPerPage = 5;
        let searchTerm = '';
        
        // Dapatkan semua elemen layanan
        const layananRows = document.querySelectorAll('.layanan-row');
        const layananCards = document.querySelectorAll('.layanan-card');
        
        // ===== FORMAT NUMBER FUNCTIONS =====
        // Format angka dengan pemisah ribuan (titik) untuk Indonesia
        function formatNumberDisplay(value) {
            if (!value && value !== 0) return '';
            // Hapus semua karakter non-digit
            const cleanValue = value.toString().replace(/\D/g, '');
            // Format dengan titik sebagai separator ribuan
            return cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Format angka ke tampilan Rupiah
        function formatRupiahDisplay(value) {
            if (!value && value !== 0) return '';
            const cleanValue = value.toString().replace(/\D/g, '');
            if (!cleanValue) return '';
            const formattedNumber = cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return `Rp ${formattedNumber}`;
        }

        // Unformat angka (hapus titik) untuk keperluan proses
        function unformatNumber(value) {
            if (!value && value !== 0) return 0;
            return parseInt(value.toString().replace(/\D/g, '')) || 0;
        }

        // Initialize number input formatting untuk layanan
        function initializeLayananNumberFormatting() {
            const numberInputIds = ['addHpp', 'addHarga'];
            const rupiahInputIds = ['editHpp', 'editHarga'];
            
            numberInputIds.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', function(e) {
                        // Format display value
                        const formattedValue = formatNumberDisplay(this.value);
                        this.value = formattedValue;
                    });

                    input.addEventListener('blur', function(e) {
                        // Ensure proper formatting on blur
                        const formattedValue = formatNumberDisplay(this.value);
                        this.value = formattedValue;
                    });
                }
            });

            rupiahInputIds.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', function(e) {
                        const formattedValue = formatRupiahDisplay(this.value);
                        this.value = formattedValue;
                    });

                    input.addEventListener('blur', function(e) {
                        const formattedValue = formatRupiahDisplay(this.value);
                        this.value = formattedValue;
                    });
                }
            });
        }
        // ===== END FORMAT NUMBER FUNCTIONS =====
        
        // Inisialisasi pagination dan search
        initializePagination();
        initializeSearch();
        initializeScrollDetection();
        initializeImageUpload();
        initializeLayananNumberFormatting();

        // === PAGINATION ===
        function initializePagination() {
            renderPagination();
            updateVisibleItems();
        }
        
        function renderPagination() {
            const visibleRows = getFilteredRows();
            const totalPages = Math.ceil(visibleRows.length / itemsPerPage);
            const pageNumbersContainer = document.getElementById('pageNumbers');
            const prevButton = document.getElementById('prevPage');
            const nextButton = document.getElementById('nextPage');
            
            // Clear existing page numbers
            pageNumbersContainer.innerHTML = '';
            
            // Generate page numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageNumber = document.createElement('button');
                pageNumber.textContent = i;
                pageNumber.className = `desktop-page-btn ${i === currentPage ? 'active' : ''}`;
                pageNumber.addEventListener('click', () => goToPage(i));
                pageNumbersContainer.appendChild(pageNumber);
            }
            
            // Update navigation buttons
            prevButton.disabled = currentPage === 1;
            nextButton.disabled = currentPage === totalPages || totalPages === 0;
            
            // Add event listeners for navigation buttons
            prevButton.onclick = () => {
                if (currentPage > 1) goToPage(currentPage - 1);
            };
            
            nextButton.onclick = () => {
                if (currentPage < totalPages) goToPage(currentPage + 1);
            };
        }
        
        function goToPage(page) {
            currentPage = page;
            renderPagination();
            updateVisibleItems();
            
            // Reset scroll position when changing pages
            const scrollableTable = document.getElementById('scrollableTable');
            if (scrollableTable) {
                scrollableTable.scrollLeft = 0;
            }
        }
        
        function getFilteredRows() {
            return Array.from(layananRows).filter(row => !row.classList.contains('hidden-by-filter'));
        }
        
        function getFilteredCards() {
            return Array.from(layananCards).filter(card => !card.classList.contains('hidden-by-filter'));
        }
        
        function updateVisibleItems() {
            const visibleRows = getFilteredRows();
            const visibleCards = getFilteredCards();
            
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            
            // Hide all rows and cards first
            layananRows.forEach(row => row.style.display = 'none');
            layananCards.forEach(card => card.style.display = 'none');
            
            // Show only the rows for current page
            visibleRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = '';
                }
            });
            
            // Show only the cards for current page
            visibleCards.forEach((card, index) => {
                if (index >= startIndex && index < endIndex) {
                    card.style.display = '';
                }
            });
            
            // Update total count
            document.getElementById('totalCount').textContent = visibleRows.length;
        }
        
        // === SEARCH ===
        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchTerm = searchInput.value.trim();
                    applySearch();
                }, 300); // Debounce search
            });
        }
        
        function applySearch() {
            // Reset to first page
            currentPage = 1;
            
            // Apply search to rows
            layananRows.forEach(row => {
                const nama = row.getAttribute('data-nama').toLowerCase();
                const deskripsi = row.getAttribute('data-deskripsi').toLowerCase();
                
                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = nama.includes(searchLower) || 
                                   deskripsi.includes(searchLower);
                }
                
                if (searchMatches) {
                    row.classList.remove('hidden-by-filter');
                } else {
                    row.classList.add('hidden-by-filter');
                }
            });
            
            // Apply same search to cards
            layananCards.forEach(card => {
                const nama = card.getAttribute('data-nama').toLowerCase();
                const deskripsi = card.getAttribute('data-deskripsi').toLowerCase();
                
                // Check if search term matches
                let searchMatches = true;
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    searchMatches = nama.includes(searchLower) || 
                                   deskripsi.includes(searchLower);
                }
                
                if (searchMatches) {
                    card.classList.remove('hidden-by-filter');
                } else {
                    card.classList.add('hidden-by-filter');
                }
            });
            
            // Update pagination and visible items
            renderPagination();
            updateVisibleItems();
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
        
        // === IMAGE UPLOAD ===
        function initializeImageUpload() {
            // Tambah Layanan Modal
            const tambahFotoContainer = document.getElementById('tambahFotoContainer');
            const tambahFotoInput = document.getElementById('tambahFotoInput');
            const tambahFotoPreview = document.getElementById('tambahFotoPreview');
            const tambahRemoveFoto = document.getElementById('tambahRemoveFoto');
            
            // Edit Layanan Modal
            const editFotoContainer = document.getElementById('editFotoContainer');
            const editFotoInput = document.getElementById('editFotoInput');
            const editFotoPreview = document.getElementById('editFotoPreview');
            const editRemoveFoto = document.getElementById('editRemoveFoto');
            
            // Tambah Layanan Modal Events
            tambahFotoContainer.addEventListener('click', function() {
                tambahFotoInput.click();
            });
            
            tambahFotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        tambahFotoPreview.src = e.target.result;
                        tambahFotoContainer.classList.add('has-image');
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            tambahRemoveFoto.addEventListener('click', function(e) {
                e.stopPropagation();
                tambahFotoInput.value = '';
                tambahFotoPreview.src = '';
                tambahFotoContainer.classList.remove('has-image');
            });
            
            // Edit Layanan Modal Events
            editFotoContainer.addEventListener('click', function() {
                editFotoInput.click();
            });
            
            editFotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editFotoPreview.src = e.target.result;
                        editFotoContainer.classList.add('has-image');
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            editRemoveFoto.addEventListener('click', function(e) {
                e.stopPropagation();
                editFotoInput.value = '';
                editFotoPreview.src = '';
                editFotoContainer.classList.remove('has-image');
                document.getElementById('editCurrentFoto').value = '';
            });
        }

        // === IMAGE MODAL ===
        function showImageModal(imageSrc) {
            const imageModal = document.getElementById('imageModal');
            const imageModalImg = document.getElementById('imageModalImg');
            
            imageModalImg.src = imageSrc;
            imageModal.classList.add('show');
            
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }
        
        function closeImageModal() {
            const imageModal = document.getElementById('imageModal');
            
            imageModal.classList.remove('show');
            
            // Restore body scroll
            document.body.style.overflow = '';
        }
        
        // === DETAIL MODAL (UPDATED FOR HPP) ===
        function showDetailModal(id, nama, deskripsi, harga, hpp, foto, fotoUrl = '') {
            const detailModal = document.getElementById('detailModal');
            const detailNama = document.getElementById('detailNama');
            const detailDeskripsi = document.getElementById('detailDeskripsi');
            const detailHarga = document.getElementById('detailHarga');
            const detailHpp = document.getElementById('detailHpp');
            const detailImage = document.getElementById('detailImage');
            const detailImageContainer = document.getElementById('detailImageContainer');
            
            // Set modal content
            detailNama.textContent = nama;
            detailDeskripsi.textContent = deskripsi;
            detailHarga.textContent = 'Rp. ' + Number(harga).toLocaleString('id-ID');
            detailHpp.textContent = 'Rp. ' + Number(hpp).toLocaleString('id-ID');
            
            // Set image
            if (fotoUrl) {
                detailImage.src = fotoUrl;
                detailImageContainer.style.display = 'block';
            } else if (foto) {
                detailImage.src = `/storage/${foto}`;
                detailImageContainer.style.display = 'block';
            } else {
                detailImageContainer.style.display = 'none';
            }
            
            // Show modal
            detailModal.classList.add('show');
            
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }
        
        function closeDetailModal() {
            const detailModal = document.getElementById('detailModal');
            
            detailModal.classList.remove('show');
            
            // Restore body scroll
            document.body.style.overflow = '';
        }
        
        // === TOGGLE DESCRIPTION ON MOBILE ===
        function toggleDescription(element) {
            const card = element.closest('.mobile-card');
            const descContent = card.querySelector('.mobile-description-box-content');
            const fullText = card.getAttribute('data-deskripsi');
            
            if (element.textContent === 'Baca selengkapnya') {
                // Show full text
                descContent.textContent = fullText;
                descContent.classList.remove('truncated');
                element.textContent = 'Tutup';
            } else {
                // Show truncated text
                descContent.classList.add('truncated');
                element.textContent = 'Baca selengkapnya';
            }
        }

        // Minimalist Popup
        function showMinimalPopup(title, message, type = 'success') {
            const popup = document.getElementById('minimalPopup');
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
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }
        
        // Close popup when clicking the close button
        document.querySelector('.minimal-popup-close').addEventListener('click', function() {
            document.getElementById('minimalPopup').classList.remove('show');
        });

        // Modal elements
        const tambahLayananModal = document.getElementById('tambahLayananModal');
        const editLayananModal = document.getElementById('editLayananModal');
        const deleteModal = document.getElementById('deleteModal');

        // Buttons
        const tambahLayananBtn = document.getElementById('tambahLayananBtn');
        const batalTambahBtn = document.getElementById('batalTambahBtn');
        const batalEditBtn = document.getElementById('batalEditBtn');
        const batalDeleteBtn = document.getElementById('batalDeleteBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const closeEditModalBtn = document.getElementById('closeEditModalBtn');
        const closeDeleteModalBtn = document.getElementById('closeDeleteModalBtn');

        // Forms
        const tambahLayananForm = document.getElementById('tambahLayananForm');
        const editLayananForm = document.getElementById('editLayananForm');

        // Open tambah modal
        tambahLayananBtn.addEventListener('click', () => {
            tambahLayananModal.classList.remove('hidden');
        });

        // Close tambah modal
        batalTambahBtn.addEventListener('click', () => {
            tambahLayananModal.classList.add('hidden');
            resetTambahForm();
        });

        closeModalBtn.addEventListener('click', () => {
            tambahLayananModal.classList.add('hidden');
            resetTambahForm();
        });

        // Close edit modal
        batalEditBtn.addEventListener('click', () => {
            editLayananModal.classList.add('hidden');
            resetEditForm();
        });

        closeEditModalBtn.addEventListener('click', () => {
            editLayananModal.classList.add('hidden');
            resetEditForm();
        });

        // Close delete modal
        batalDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        closeDeleteModalBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        // Reset forms
        function resetTambahForm() {
            tambahLayananForm.reset();
            document.getElementById('tambahFotoPreview').src = '';
            document.getElementById('tambahFotoContainer').classList.remove('has-image');
        }

        function resetEditForm() {
            editLayananForm.reset();
            document.getElementById('editFotoPreview').src = '';
            document.getElementById('editFotoContainer').classList.remove('has-image');
        }

        // ============================
        // HANDLE DETAIL BUTTON (UPDATED FOR HPP)
        // ============================
        document.querySelectorAll('.detail-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const nama = button.dataset.nama;
                const deskripsi = button.dataset.deskripsi;
                const harga = button.dataset.harga;
                const hpp = button.dataset.hpp;
                const foto = button.dataset.foto;
                const fotoUrl = button.dataset.fotoUrl || '';
                
                showDetailModal(id, nama, deskripsi, harga, hpp, foto, fotoUrl);
            });
        });

// ============================
// HANDLE EDIT BUTTON (UPDATED FOR HPP) - FIXED
// ============================
document.querySelectorAll('.edit-btn, .mobile-card-action-btn.edit').forEach(button => {
    button.addEventListener('click', () => {
        // SET VALUE
        document.getElementById('editId').value = button.dataset.id;
        document.getElementById('editNamaLayanan').value = button.dataset.nama;
        document.getElementById('editDeskripsi').value = button.dataset.deskripsi;
        document.getElementById('editHarga').value = formatRupiahDisplay(button.dataset.harga);
        document.getElementById('editHpp').value = formatRupiahDisplay(button.dataset.hpp);
        document.getElementById('editCurrentFoto').value = button.dataset.foto;
        
        // SET FOTO PREVIEW
        if (button.dataset.foto || button.dataset.fotoUrl) {
            const fotoUrl = button.dataset.fotoUrl || `/storage/${button.dataset.foto}`;
            document.getElementById('editFotoPreview').src = fotoUrl;
            document.getElementById('editFotoContainer').classList.add('has-image');
        } else {
            document.getElementById('editFotoPreview').src = '';
            document.getElementById('editFotoContainer').classList.remove('has-image');
        }

        // SET ACTION URL DINAMIS - PERBAIKAN UTAMA
        const editForm = document.getElementById('editLayananForm');
        const baseUrl = window.location.origin;
        editForm.action = `${baseUrl}/admin/layanan/${button.dataset.id}`;

        editLayananModal.classList.remove('hidden');
    });
});

// ============================
// SUBMIT FORM EDIT - FIXED VERSION
// ============================
editLayananForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id = document.getElementById('editId').value;
    const formData = new FormData(this);
    
    // Unformat nilai numeric sebelum dikirim
    if (formData.get('harga')) {
        formData.set('harga', unformatNumber(formData.get('harga')));
    }
    if (formData.get('hpp')) {
        formData.set('hpp', unformatNumber(formData.get('hpp')));
    }
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    // Tampilkan loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span> Memperbarui...';

    try {
        // Debug: Tampilkan data FormData
        console.log('=== DEBUG EDIT DATA ===');
        console.log('ID:', id);
        console.log('URL:', this.action);
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }

        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        console.log('Response status:', response.status);
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            editLayananModal.classList.add('hidden');
            resetEditForm();
            showMinimalPopup('Berhasil', data.message || 'Layanan berhasil diperbarui', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showMinimalPopup('Gagal', data.message || 'Gagal memperbarui layanan.', 'error');
        }
    } catch (error) {
        console.error('Error Detail:', error);
        console.error('Error stack:', error.stack);
        
        let errorMessage = 'Terjadi kesalahan saat memperbarui data.';
        if (error.message.includes('NetworkError')) {
            errorMessage = 'Koneksi jaringan bermasalah. Periksa koneksi internet Anda.';
        } else if (error.message.includes('JSON')) {
            errorMessage = 'Server mengembalikan response yang tidak valid.';
        }
        
        showMinimalPopup('Error', errorMessage, 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    }
});

        // ============================
        // HANDLE DELETE BUTTON
        // ============================
        document.querySelectorAll('.delete-btn, .mobile-card-action-btn.delete').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;

                // Set action form delete
                document.getElementById('deleteForm').action = `/admin/layanan/${id}`;
                document.getElementById('deleteId').value = id;

                // Tampilkan modal
                deleteModal.classList.remove('hidden');
            });
        });

        // ============================
// ============================
// SUBMIT FORM TAMBAH - FIXED
// ============================
tambahLayananForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Unformat nilai numeric sebelum dikirim
    if (formData.get('harga')) {
        formData.set('harga', unformatNumber(formData.get('harga')));
    }
    if (formData.get('hpp')) {
        formData.set('hpp', unformatNumber(formData.get('hpp')));
    }
    
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Tampilkan loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span> Menyimpan...';
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    tambahLayananModal.classList.add('hidden');
                    resetTambahForm();
                    showMinimalPopup('Berhasil', data.message || 'Layanan berhasil ditambahkan', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showMinimalPopup('Gagal', data.message || 'Gagal menambah layanan.', 'error');
                }
            } catch (error) {
                console.error('Error Detail:', error);
                showMinimalPopup('Error', error.message, 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });

        // ============================
        // SUBMIT FORM DELETE - FIXED
        // ============================
        document.getElementById('deleteForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('deleteId').value;
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="material-icons-outlined animate-spin">refresh</span> Menghapus...';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    deleteModal.classList.add('hidden');
                    showMinimalPopup('Berhasil', data.message || 'Layanan berhasil dihapus', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showMinimalPopup('Gagal', data.message || 'Gagal menghapus layanan.', 'error');
                }
            } catch (error) {
                console.error('Error Detail:', error);
                showMinimalPopup('Error', error.message, 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    </script>
</body>
</html>

