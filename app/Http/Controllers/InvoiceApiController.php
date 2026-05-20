<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceApiController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('API Invoice Index accessed', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_params' => $request->all()
        ]);
        
        try {
            $query = Invoice::latest();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%$search%")
                      ->orWhere('client_name', 'like', "%$search%")
                      ->orWhere('invoice_no', 'like', "%$search%");
                });
            }

            $perPage = $request->input('per_page', 10);
            $invoices = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data invoice berhasil diambil',
                'data' => $invoices->items(),
                'pagination' => [
                    'total' => $invoices->total(),
                    'per_page' => $invoices->perPage(),
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('API Invoice Index Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_no'       => 'required|string|unique:invoices,invoice_no',
            'invoice_date'     => 'required|date',
            'company_name'     => 'required|string|max:255',
            'company_address'  => 'required|string',
            'client_name'      => 'required|string|max:255',
            'payment_method'   => 'required|string|max:100',
            'description'      => 'nullable|string',
            'subtotal'         => 'required|integer|min:0',
            'tax'              => 'required|integer|min:0',
            'total'            => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Use a transaction so both Invoice and Order are created atomically
        \DB::beginTransaction();

        try {
            \Log::info('InvoiceApiController@store - starting create', $request->all());

            $invoice = Invoice::create($request->only([
                'invoice_no',
                'invoice_date',
                'company_name',
                'company_address',
                'client_name',
                'payment_method',
                'description',
                'subtotal',
                'tax',
                'total',
            ]));

            \Log::info('InvoiceApiController@store - invoice created', ['id' => $invoice->id]);

            // The Invoice model's created event will create an Order. Commit and then try to fetch it.
            \DB::commit();

            $order = Order::where('invoice_id', $invoice->id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dibuat',
                'data' => $invoice,
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Invoice creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice dan order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'invoice_no'       => 'required|string|unique:invoices,invoice_no,' . $id,
            'invoice_date'     => 'required|date',
            'company_name'     => 'required|string|max:255',
            'company_address'  => 'required|string',
            'client_name'      => 'required|string|max:255',
            'payment_method'   => 'required|string|max:100',
            'description'      => 'nullable|string',
            'subtotal'         => 'required|integer|min:0',
            'tax'              => 'required|integer|min:0',
            'total'            => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $invoice->update($request->only([
            'invoice_no',
            'invoice_date',
            'company_name',
            'company_address',
            'client_name',
            'payment_method',
            'description',
            'subtotal',
            'tax',
            'total',
        ]));

        // If an Order exists for this invoice, update its fields so data_orderan stays in sync
        try {
            $order = Order::where('invoice_id', $invoice->id)->first();

            if ($order) {
                $order->update([
                    'layanan' => $request->input('nama_layanan') ?? $request->input('layanan'),
                    'kategori' => $request->input('kategori'),
                    'price' => (int) $request->input('subtotal', 0),
                    'price_formatted' => number_format($request->input('subtotal', 0), 0, ',', '.'),
                    'klien' => $request->input('client_name'),
                    'company_name' => $request->input('company_name'),
                    'order_date' => $request->input('invoice_date'),
                    'company_address' => $request->input('company_address'),
                    'description' => $request->input('description'),
                    'subtotal' => $request->input('subtotal'),
                    'tax' => $request->input('tax'),
                    'total' => $request->input('total'),
                    'payment_method' => $request->input('payment_method'),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update related order: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil diperbarui',
            'data' => $invoice
        ]);
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil dihapus'
        ]);
    }
}