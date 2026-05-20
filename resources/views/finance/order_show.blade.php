@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">
                Detail Order #{{ $order->order_no ?? $order->id }}
            </h1>

            <a href="{{ route('orders.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Kembali
            </a>
        </div>

        <!-- INFO ORDER -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            <!-- INFORMASI ORDER -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-4">Informasi Order</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Nomor Order</span>
                        <p class="font-medium">{{ $order->order_no ?? '-' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600">Layanan</span>
                        <p class="font-medium">{{ $order->layanan ?? '-' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600">Kategori</span>
                        <div class="mt-1">
                            @switch($order->kategori)
                                @case('design')
                                    <span class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        Desain
                                    </span>
                                    @break
                                @case('programming')
                                    <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        Programming
                                    </span>
                                    @break
                                @case('marketing')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Digital Marketing
                                    </span>
                                    @break
                                @default
                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                        -
                                    </span>
                            @endswitch
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-600">Tanggal Order</span>
                        <p class="font-medium">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '-' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600">Nomer Invoice</span>
                        <p class="font-medium">{{ $order->invoice_no ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- INFORMASI KLIEN -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-4">Informasi Klien</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Nama Klien</span>
                        <p class="font-medium">{{ $order->klien ?? '-' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600">Nama Perusahaan</span>
                        <p class="font-medium">{{ $order->company_name ?? '-' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600">Alamat Perusahaan</span>
                        <p class="font-medium text-justify">{{ $order->company_address ?? '-' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600">Deskripsi</span>
                        <p class="font-medium text-justify">{{ $order->description ?? '-' }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- STATUS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-4">Status</h3>

                <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-gray-600">Status Pembayaran</span>
                        <div class="mt-1">
                            @switch($order->status)
                                @case('paid')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Lunas
                                    </span>
                                    @break
                                @case('partial')
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        Sebagian
                                    </span>
                                    @break
                                @case('overdue')
                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        Terlambat
                                    </span>
                                    @break
                                @default
                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                        Pending
                                    </span>
                            @endswitch
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-600">Status Pengerjaan</span>
                        <div class="mt-1">
                            @switch($order->work_status)
                                @case('planning')
                                    <span class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        Perencanaan
                                    </span>
                                    @break
                                @case('progress')
                                    <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        Dikerjakan
                                    </span>
                                    @break
                                @case('review')
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        Review
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                    @break
                                @default
                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                        Ditunda
                                    </span>
                            @endswitch
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-600">Metode Pembayaran</span>
                        <p class="font-medium mt-1">
                            @switch($order->payment_method)
                                @case('transfer_bank')
                                    Transfer Bank
                                    @break
                                @case('cash')
                                    Tunai
                                    @break
                                @case('check')
                                    Cek
                                    @break
                                @case('e_wallet')
                                    E-Wallet
                                    @break
                                @default
                                    {{ $order->payment_method ?? '-' }}
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-4">Rincian Biaya</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <p class="font-medium">{{ $order->subtotal ? 'Rp ' . number_format($order->subtotal, 0, ',', '.') : '-' }}</p>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Pajak</span>
                        <p class="font-medium">{{ $order->tax ? $order->tax . '%' : '-' }}</p>
                    </div>

                    <div class="flex justify-between border-t pt-2">
                        <span class="text-gray-600 font-semibold">Total</span>
                        <p class="font-semibold">{{ $order->total ? 'Rp ' . number_format($order->total, 0, ',', '.') : '-' }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- INVOICE -->
        @if ($order->invoice_id)
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2">Invoice Terkait</h3>

            <div class="flex justify-between items-center">
                <p>Invoice #{{ $order->invoice_id }}</p>

                <a href="{{ route('invoices.show', $order->invoice_id) }}"
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Lihat Invoice
                </a>
            </div>
        </div>
        @endif

        <!-- TIMESTAMP -->
        <div class="text-sm text-gray-500 border-t pt-4">
            <p>Dibuat: {{ $order->created_at?->format('d M Y H:i') ?? '-' }}</p>
            <p>Diperbarui: {{ $order->updated_at?->format('d M Y H:i') ?? '-' }}</p>
        </div>

        <!-- ACTION -->
        <div class="mt-6">
            <a href="{{ route('orders.index') }}"
               class="block text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Kembali ke Daftar Order
            </a>
        </div>

    </div>
</div>
@endsection
