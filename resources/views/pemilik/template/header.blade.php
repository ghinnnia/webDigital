<!DOCTYPE html>
<html class="scroll-smooth" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Brand Navigation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#0f172a", // Biru sangat tua mendekati hitam
                        "background-light": "#ffffff", // Putih untuk background utama
                        "background-dark": "#f8fafc", // Putih sangat terang untuk mode gelap
                        "card-light": "#111827", // Hitam untuk kartu
                        "card-dark": "#1f2937", // Abu-abu untuk kartu mode gelap
                        "text-light": "#111827", // Hitam untuk teks
                        "text-dark": "#f9fafb", // Putih terang untuk teks mode gelap
                        "border-light": "#e5e7eb", // Abu-abu terang untuk border
                        "border-dark": "#4b5563", // Abu-abu untuk border mode gelap
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "1rem",
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Smooth Scrolling untuk seluruh halaman */
        html {
            scroll-behavior: smooth;
        }

        /* Sticky Navigation */
        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .sticky-header.scrolled {
            background-color: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        /* Padding untuk konten agar tidak tertutup header */
        .main-content {
            padding-top: 80px;
        }

        /* Hamburger Menu Animation */
        .hamburger-line {
            transition: all 0.3s ease;
        }

        .hamburger.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Mobile Navigation - Menyatu dengan Header */
        .mobile-nav {
            max-height: 0;
            overflow: hidden;
            background-color: rgba(255, 255, 255, 0.95);
            /* Warna sama dengan header */
            transition: max-height 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 0 0 1rem 1rem;
            /* Border radius hanya di bagian bawah */
        }

        .mobile-nav.active {
            max-height: 70vh;
            /* Tinggi maksimum saat aktif */
        }

        .mobile-nav-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .mobile-nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.1);
            /* Border dengan warna primary */
            margin-bottom: 1rem;
        }

        .mobile-nav .brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            /* Warna primary */
        }

        .mobile-nav nav {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .mobile-nav .nav-link {
            color: #0f172a;
            /* Warna primary */
            font-size: 1.125rem;
            font-weight: 500;
            text-decoration: none;
            position: relative;
            transition: color 0.3s ease;
            padding: 0.75rem 0;
            display: block;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .mobile-nav .nav-link:hover,
        .mobile-nav .nav-link.active {
            color: #0f172a;
            /* Warna primary */
            background-color: rgba(15, 23, 42, 0.1);
            /* Background dengan warna primary */
        }

        .mobile-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            width: 0;
            height: 2px;
            background-color: #0f172a;
            /* Warna primary */
            transition: width 0.3s ease;
        }

        .mobile-nav .nav-link:hover::after,
        .mobile-nav .nav-link.active::after {
            width: 30px;
        }

        .mobile-nav .login-btn {
            background-color: #0f172a;
            /* Warna primary */
            color: white;
            /* Teks putih */
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            display: inline-block;
            transition: all 0.3s ease;
            text-align: center;
            width: 100%;
            text-decoration: none;
        }

        .mobile-nav .login-btn:hover {
            background-color: rgba(15, 23, 42, 0.9);
            /* Warna primary dengan opacity */
            transform: translateY(-2px);
        }

        .mobile-nav .close-btn {
            color: #0f172a;
            /* Warna primary */
            font-size: 1.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .mobile-nav .close-btn:hover {
            background-color: rgba(15, 23, 42, 0.1);
            /* Background dengan warna primary */
            transform: rotate(90deg);
        }

        /* Active navigation link - Perbaikan CSS */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #0f172a;
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: #0f172a;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: #0f172a;
            font-weight: 600;
            /* Teks lebih tebal untuk link aktif */
        }

        .nav-link.active::after {
            width: 100%;
            /* Garis bawah selalu muncul untuk link aktif */
        }

        /* Mobile navigation clickable area */
        .mobile-nav-clickable {
            cursor: pointer;
            position: relative;
            z-index: 10;
        }
    </style>
</head>

<body class="bg-background-light text-text-light">
    <!-- Sticky Header -->
    <header id="header" class="sticky-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <!-- Brand Logo -->
                <div class="flex items-center">
                    <img src="{{ asset('images/logo_inovindo.jpg') }}" alt="Inovindo Logo"
                        class="h-10 w-auto object-contain">
                </div>

                <!-- Desktop Navigation - Centered -->
                <nav
                    class="hidden md:flex items-center space-x-4 lg:space-x-8 absolute left-1/2 transform -translate-x-1/2">
                    <a class="nav-link text-sm font-medium text-gray-700" data-page="home" href="/owner/home">Beranda</a>
                    <a class="nav-link text-sm font-medium text-gray-700" data-page="rekap" href="/owner/rekap_absen">Rekap
                        Absensi</a>
                    <a class="nav-link text-sm font-medium text-gray-700" data-page="laporan"
                        href="/owner/laporan">Laporan</a>
                </nav>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="hidden md:flex items-center bg-black text-white text-sm font-medium py-2 px-6 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class='bx bx-log-out-circle text-xl mr-2'></i>
                        Log Out
                    </button>
                </form>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn"
                    class="md:hidden flex flex-col justify-center items-center w-8 h-8 hamburger mobile-nav-clickable">
                    <span class="hamburger-line w-6 h-0.5 bg-black mb-1.5"></span>
                    <span class="hamburger-line w-6 h-0.5 bg-black mb-1.5"></span>
                    <span class="hamburger-line w-6 h-0.5 bg-black"></span>
                </button>
            </div>

            <!-- Mobile Navigation - Menyatu dengan Header -->
            <div id="mobileNav" class="mobile-nav">
                <div class="mobile-nav-content">
                    <nav>
                        <a class="nav-link" data-page="home" href="/owner/home">
                            <i class='bx bx-home-alt text-xl mr-2'></i>
                            Beranda
                        </a>
                        <a class="nav-link" data-page="rekap" href="/owner/rekap_absen">
                            <i class='bx bx-calendar-check text-xl mr-2'></i>
                            Rekap Absensi
                        </a>
                        <a class="nav-link" data-page="laporan" href="/owner/laporan">
                            <i class='bx bx-file text-xl mr-2'></i>
                            Laporan
                        </a>
                    </nav>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex md:hidden items-center bg-black text-white text-sm font-medium py-2 px-6 rounded-lg hover:bg-gray-800 transition-colors login-btn">
                            <i class='bx bx-log-out-circle text-xl mr-2'></i>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        </div>
    </main>

    <script>
        // Sticky Header
        const header = document.getElementById('header');

        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Mobile Navigation
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileNav = document.getElementById('mobileNav');

        // Pastikan navigasi mobile tersembunyi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            mobileNav.classList.remove('active');
            mobileMenuBtn.classList.remove('active');

            // Set active navigation link based on current page
            setActiveNavLink();
        });

        // Toggle mobile navigation
        mobileMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent event bubbling
            mobileMenuBtn.classList.toggle('active');
            mobileNav.classList.toggle('active');
        });

        // Close mobile nav when clicking outside
        document.addEventListener('click', function (e) {
            if (!mobileNav.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                mobileMenuBtn.classList.remove('active');
                mobileNav.classList.remove('active');
            }
        });

        // Close mobile nav when clicking on a link
        const mobileNavLinks = mobileNav.querySelectorAll('.nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                // Allow the default link behavior to proceed (navigation)
                // Close the menu after a short delay to allow navigation to start
                setTimeout(() => {
                    mobileMenuBtn.classList.remove('active');
                    mobileNav.classList.remove('active');
                }, 300);
            });
        });

        // Also close when clicking on login button
        const mobileLoginBtn = mobileNav.querySelector('.login-btn');
        mobileLoginBtn.addEventListener('click', function (e) {
            // Allow the default link behavior to proceed (navigation)
            // Close the menu immediately
            mobileMenuBtn.classList.remove('active');
            mobileNav.classList.remove('active');
        });

        // Function to set active navigation link based on current page
        function setActiveNavLink() {
            // Get current page path
            const currentPath = window.location.pathname;

            // Get all navigation links (both desktop and mobile)
            const allNavLinks = document.querySelectorAll('.nav-link');

            // Remove active class from all links
            allNavLinks.forEach(link => {
                link.classList.remove('active');
            });

            // Determine which page we're on based on URL
            let currentPage = '';
            if (currentPath.includes('/pemilik') || currentPath === '/') {
                currentPage = 'home';
            } else if (currentPath.includes('/rekap_absensi')) {
                currentPage = 'rekap';
            } else if (currentPath.includes('/laporan')) {
                currentPage = 'laporan';
            }

            // Add active class to the link that matches the current page
            allNavLinks.forEach(link => {
                const pageAttr = link.getAttribute('data-page');
                if (pageAttr === currentPage) {
                    link.classList.add('active');
                }
            });
        }

        // Prevent event propagation inside mobile nav
        mobileNav.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    </script>
</body>

</html>