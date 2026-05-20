<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'Employee Dashboard' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#0ea5e9", // A shade of blue
                        background: "#ffffff",
                        surface: "#f8fafc",
                        "text-primary": "#1e293b",
                        "text-secondary": "#64748b",
                        "border-color": "#e2e8f0",
                    },
                    fontFamily: {
                        display: ["Roboto", "Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                        lg: "1rem",
                        full: "9999px",
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Roboto', 'Poppins', sans-serif;
        }

        /* Style untuk indikator halaman aktif */
        .nav-active {
            position: relative;
            color: #0ea5e9; /* Warna teks primer saat aktif */
            font-weight: 600;
        }

        .nav-active::after {
            content: '';
            position: absolute;
            bottom: -18px; /* Posisikan di bawah border header */
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #0ea5e9; /* Cahaya biru */
            border-radius: 2px;
        }
    </style>
</head>

<body class="bg-background text-text-secondary">
    <div class="min-h-screen flex flex-col p-4 sm:p-6 lg:p-8">
        @include('karyawan.header')

        <main class="flex-grow w-full max-w-7xl mx-auto">
            @yield('content')
        </main>

        @include('karyawan.footer')
    </div>
</body>

</html>