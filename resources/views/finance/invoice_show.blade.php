@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM CREATE --}}
    <div class="card mb-4">
        <div class="card-header">Buat Invoice</div>
        <div class="card-body">
            <form action="{{ route('invoices.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>No Invoice</label>
                        <input type="text" name="invoice_no" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="invoice_date" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Nama Perusahaan</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Alamat Perusahaan</label>
                        <input type="text" name="company_address" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nama Klien</label>
                        <input type="text" name="client_name" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>No Order</label>
                        <input type="text" name="order_number" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Metode Pembayaran</label>
                        <input type="text" name="payment_method" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Kategori</label>
                        <input type="text" name="category" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Status Pekerjaan</label>
                        <input type="text" name="work_status" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Subtotal</label>
                        <input type="number" name="subtotal" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Pajak</label>
                        <input type="number" name="tax" class="form-control" required>
                    </div>
                </div>

                <button class="btn btn-primary">Simpan Invoice</button>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-header">Data Invoice</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Perusahaan</th>
                        <th>Klien</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_no }}</td>
                            <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->company_name }}</td>
                            <td>{{ $invoice->client_name }}</td>
                            <td>Rp {{ number_format($invoice->total) }}</td>
                            <td>
                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
