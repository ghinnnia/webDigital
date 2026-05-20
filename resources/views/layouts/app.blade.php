<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manajemen Order')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Order</h1>
                <div class="flex gap-4">
                    <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-800">Orders</a>
                    @if(auth()->check())
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        @if ($errors->any())
            <div class="container mx-auto px-4 mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="container mx-auto px-4 mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white shadow-md mt-12">
        <div class="container mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2026 Manajemen Order. Semua hak dilindungi.</p>
        </div>
    </footer>
</body>
</html>
