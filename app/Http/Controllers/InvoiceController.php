<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Layanan;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        Log::info('Invoice Index accessed', [
            'user_id' => $userId,
            'user_role' => $user ? $user->role : 'guest',
            'request_type' => $request->ajax() ? 'AJAX' : 'WEB',
            'url' => $request->fullUrl()
        ]);

        // Untuk API requests (AJAX) - kembalikan JSON
        if ($request->ajax() || $request->wantsJson() || $request->is('*api*') || $request->expectsJson() || $request->filled('ajax')) {
            try {
                $query = Invoice::with(['layanan', 'perusahaan'])->latest();

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('company_name', 'like', "%$search%")
                            ->orWhere('client_name', 'like', "%$search%")
                            ->orWhere('invoice_no', 'like', "%$search%")
                            ->orWhere('nama_layanan', 'like', "%$search%");
                    });
                }

                // Filter berdasarkan status pembayaran
                if ($request->filled('status_pembayaran')) {
                    $query->where('status_pembayaran', $request->status_pembayaran);
                }

                // Filter berdasarkan nama layanan
                if ($request->filled('nama_layanan')) {
                    $query->where('nama_layanan', $request->nama_layanan);
                }

                // Return full collection for AJAX frontend (client-side pagination)
                $invoices = $query->orderBy('created_at', 'desc')->get();

                Log::info('API Invoice Index returning count', ['count' => $invoices->count()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data invoice berhasil diambil',
                    'data' => $invoices
                ], 200, ['Content-Type' => 'application/json']);
            } catch (\Exception $e) {
                Log::error('API Invoice Index Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memuat data invoice',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ], 500, ['Content-Type' => 'application/json']);
            }
        }

        // Untuk web requests - kembalikan view
        $role = $user ? $user->role : 'guest';
        
        if ($role == 'finance') {
            return view('finance.invoice');
        } elseif ($role == 'admin') {
            return view('admin.invoice');
        } else {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        try {
            // Ambil data layanan untuk dropdown
            $layanan = Layanan::orderBy('nama_layanan', 'asc')
                ->get(['id', 'nama_layanan', 'harga', 'deskripsi']);

            // Ambil data perusahaan untuk dropdown
            $perusahaanList = Perusahaan::orderBy('nama_perusahaan', 'asc')
                ->get(['id', 'nama_perusahaan', 'klien', 'alamat', 'kontak']);

            if ($user && $user->role == 'finance') {
                return view('finance.invoice_create', compact('layanan', 'perusahaanList'));
            } else {
                return view('admin.invoice_create', compact('layanan', 'perusahaanList'));
            }
        } catch (\Exception $e) {
            Log::error('Error getting data for create form: ' . $e->getMessage());

            $layanan = collect();
            $perusahaanList = collect();

            if ($user && $user->role == 'finance') {
                return view('finance.invoice_create', compact('layanan', 'perusahaanList'));
            } else {
                return view('admin.invoice_create', compact('layanan', 'perusahaanList'));
            }
        }
    }

    public function store(Request $request)
    {
        Log::info('Store Invoice Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'invoice_no' => 'required|string|unique:invoices,invoice_no',
            'invoice_date' => 'required|date',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'kontak' => 'nullable|string|max:255', // Pastikan field ini divalidasi
            'client_name' => 'required|string|max:255',
            'nama_layanan' => 'required|string|max:255',
            'status_pembayaran' => 'required|in:down payment,lunas,pembayaran awal',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
            'keterangan_tambahan' => 'nullable|string',
            'jenis_bank' => 'nullable|string|max:100',
            'kategori_pemasukan' => 'nullable|in:layanan,produk,fee/komisi',
            'fee_maintenance' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'order_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());

            if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $validated = $validator->validated();

            // TAMBAHKAN LOGIKA INI: Ambil data kontak dari perusahaan jika kosong
            if (empty($validated['kontak']) && !empty($validated['company_name'])) {
                $perusahaan = Perusahaan::where('nama_perusahaan', $validated['company_name'])->first();
                if ($perusahaan) {
                    // Ambil kontak dari berbagai kemungkinan field
                    if (!empty($perusahaan->kontak)) {
                        $validated['kontak'] = $perusahaan->kontak;
                    } elseif (!empty($perusahaan->telepon)) {
                        $validated['kontak'] = $perusahaan->telepon;
                    } elseif (!empty($perusahaan->no_telp)) {
                        $validated['kontak'] = $perusahaan->no_telp;
                    } elseif (!empty($perusahaan->phone)) {
                        $validated['kontak'] = $perusahaan->phone;
                    } elseif (!empty($perusahaan->no_hp)) {
                        $validated['kontak'] = $perusahaan->no_hp;
                    } elseif (!empty($perusahaan->telephone)) {
                        $validated['kontak'] = $perusahaan->telephone;
                    }
                    Log::info('Auto-filled kontak from perusahaan:', ['kontak' => $validated['kontak'] ?? '']);
                }
            }

            // TAMBAHKAN INI: Jika deskripsi kosong, ambil dari layanan
            if (empty($validated['description']) && !empty($validated['nama_layanan'])) {
                $layanan = Layanan::where('nama_layanan', $validated['nama_layanan'])->first();
                if ($layanan && !empty($layanan->deskripsi)) {
                    $validated['description'] = $layanan->deskripsi;
                    Log::info('Auto-filled description from layanan:', ['deskripsi' => $layanan->deskripsi]);
                }
            }

            // Generate invoice number if not provided
            if (empty($validated['invoice_no'])) {
                $validated['invoice_no'] = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            // Convert numeric values
            $validated['subtotal'] = (float) $validated['subtotal'];
            $validated['tax'] = (float) $validated['tax'];
            $validated['total'] = (float) $validated['total'];
            $validated['fee_maintenance'] = (float) ($validated['fee_maintenance'] ?? 0);
            $validated['kategori_pemasukan'] = $validated['kategori_pemasukan'] ?? 'layanan';

            // Update jumlah kerjasama dan aktifkan status perusahaan saat dipakai membuat invoice
            $companyName = trim($validated['company_name']);
            $perusahaan = Perusahaan::where('nama_perusahaan', $companyName)->first();
            if (!$perusahaan) {
                $perusahaan = Perusahaan::whereRaw('TRIM(nama_perusahaan) = ?', [$companyName])->first();
            }
            if ($perusahaan) {
                $perusahaan->jumlah_kerjasama = ($perusahaan->jumlah_kerjasama ?? 0) + 1;
                if (Schema::hasColumn('perusahaans', 'status')) {
                    $perusahaan->status = 'aktif';
                }
                $perusahaan->save();
            } else {
                Log::warning('Perusahaan not found for increment jumlah_kerjasama', ['company_name' => $companyName]);
            }

            Log::info('Creating invoice with data:', $validated);

            $invoice = Invoice::create($validated);
            Log::info('Invoice created successfully:', ['id' => $invoice->id]);

            // The Invoice model's created event will create the Order. Try to fetch it for logging.
            try {
                $order = Order::where('invoice_id', $invoice->id)->first();
                if ($order) {
                    Log::info('Order exists for invoice', ['order_id' => $order->id, 'invoice_id' => $invoice->id]);
                } else {
                    Log::warning('Order not found immediately after invoice creation', ['invoice_id' => $invoice->id]);
                }
            } catch (\Exception $e) {
                Log::error('Failed while checking for order after invoice creation: ' . $e->getMessage());
            }

            DB::commit();

            // Untuk API/AJAX request
            if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice berhasil dibuat',
                    'data' => $invoice->fresh()
                ], 201);
            }

            // Untuk web form request
            return redirect()->route('admin.invoice.index')
                ->with('success', 'Invoice berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating invoice: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Untuk API/AJAX request
            if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat invoice',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ], 500);
            }

            // Untuk web form request
            return redirect()->back()
                ->with('error', 'Gagal membuat invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $invoice = Invoice::with(['layanan', 'perusahaan', 'kwitansi', 'projects'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data invoice berhasil diambil',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing invoice: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan',
                'error' => config('app.debug') ? $e->getMessage() : 'Not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Ambil data layanan untuk dropdown
            $layanan = Layanan::orderBy('nama_layanan', 'asc')
                ->get(['id', 'nama_layanan', 'harga']);

            // Untuk API request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data invoice berhasil diambil untuk diedit',
                    'data' => $invoice,
                    'layanan' => $layanan
                ]);
            }

            // Untuk web request
            $user = Auth::user();
            if ($user && $user->role == 'finance') {
                return view('finance.invoice_edit', compact('invoice', 'layanan'));
            } else {
                return view('admin.invoice_edit', compact('invoice', 'layanan'));
            }
        } catch (\Exception $e) {
            Log::error('Error editing invoice: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan',
                    'error' => config('app.debug') ? $e->getMessage() : 'Not found'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Invoice tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            Log::info('Update Invoice Request:', [
                'id' => $id,
                'data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'invoice_no' => 'required|string|unique:invoices,invoice_no,' . $id,
                'invoice_date' => 'required|date',
                'company_name' => 'required|string|max:255',
                'company_address' => 'required|string',
                'kontak' => 'nullable|string|max:255',
                'client_name' => 'required|string|max:255',
                'nama_layanan' => 'required|string|max:255',
                'status_pembayaran' => 'required|in:down payment,lunas,pembayaran awal',
                'payment_method' => 'required|string',
                'description' => 'nullable|string',
                'keterangan_tambahan' => 'nullable|string',
                'jenis_bank' => 'nullable|string|max:100',
                'kategori_pemasukan' => 'nullable|in:layanan,produk,fee/komisi',
                'fee_maintenance' => 'nullable|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $validated = $validator->validated();

            // Convert numeric values
            $validated['subtotal'] = (float) $validated['subtotal'];
            $validated['tax'] = (float) $validated['tax'];
            $validated['total'] = (float) $validated['total'];
            $validated['fee_maintenance'] = (float) ($validated['fee_maintenance'] ?? 0);
            $validated['kategori_pemasukan'] = $validated['kategori_pemasukan'] ?? 'layanan';

            $invoice->update($validated);
            
            // Sync project jika ada
            if ($invoice->hasProject()) {
                $invoice->syncProjectWithInvoice();
            }
            
            DB::commit();

            Log::info('Invoice updated successfully:', ['id' => $invoice->id]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil diperbarui',
                'data' => $invoice->load(['layanan', 'perusahaan'])
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating invoice: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui invoice',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);

            // Log sebelum menghapus
            Log::info('Deleting invoice:', [
                'id' => $id,
                'invoice_no' => $invoice->invoice_no,
                'deleted_by' => Auth::id()
            ]);

            // Decrement jumlah kerjasama perusahaan terkait (min 0)
            if (!empty($invoice->company_name)) {
                $companyName = trim($invoice->company_name);
                $perusahaan = Perusahaan::where('nama_perusahaan', $companyName)->first();
                if (!$perusahaan) {
                    $perusahaan = Perusahaan::whereRaw('TRIM(nama_perusahaan) = ?', [$companyName])->first();
                }
                if ($perusahaan) {
                    $current = (int) ($perusahaan->jumlah_kerjasama ?? 0);
                    $perusahaan->jumlah_kerjasama = max(0, $current - 1);
                    $perusahaan->save();
                } else {
                    Log::warning('Perusahaan not found for decrement jumlah_kerjasama', [
                        'company_name' => $companyName,
                        'invoice_id' => $invoice->id
                    ]);
                }
            }

            $invoice->delete();
            DB::commit();

            Log::info('Invoice deleted successfully:', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dihapus'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting invoice: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus invoice',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * API: Get all unique layanan names from invoices
     */
    public function getLayananList(Request $request)
    {
        try {
            $layananList = Invoice::select('nama_layanan')
                ->distinct()
                ->whereNotNull('nama_layanan')
                ->orderBy('nama_layanan', 'asc')
                ->pluck('nama_layanan');

            return response()->json([
                'success' => true,
                'message' => 'List layanan dari invoice berhasil diambil',
                'data' => $layananList
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting invoice layanan list: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil list layanan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * API: Get status pembayaran list
     */
    public function getStatusPembayaranList(Request $request)
    {
        try {
            $statusList = Invoice::select('status_pembayaran')
                ->distinct()
                ->whereNotNull('status_pembayaran')
                ->orderBy('status_pembayaran', 'asc')
                ->pluck('status_pembayaran');

            return response()->json([
                'success' => true,
                'message' => 'List status pembayaran berhasil diambil',
                'data' => $statusList
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting status pembayaran list: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil list status pembayaran',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * API: Get layanan data for dropdown
     */
    public function getLayananData(Request $request)
    {
        try {
            $layanan = Layanan::orderBy('nama_layanan', 'asc')
                ->get(['id', 'nama_layanan', 'harga', 'deskripsi']);

            return response()->json([
                'success' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $layanan
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting layanan data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data layanan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Print invoice
     */
    public function print($id)
    {
        try {
            $invoice = Invoice::with(['layanan', 'perusahaan'])->findOrFail($id);

            // Jika request AJAX, kembalikan data JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data invoice untuk print berhasil diambil',
                    'data' => $invoice
                ]);
            }

            // Untuk web, kembalikan view print
            return view('admin.invoice_print', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error printing invoice: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan',
                    'error' => config('app.debug') ? $e->getMessage() : 'Not found'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'Invoice tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * API: Get finance invoices
     */
  // Di InvoiceController.php tambahkan method:

/**
 * API: Get finance invoices for dashboard
 */
public function getFinanceInvoices(Request $request)
{
    try {
        \Log::info('API getFinanceInvoices called', ['user_id' => auth()->id()]);
        
        $query = Invoice::query();
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('nama_layanan', 'like', "%{$search}%");
            });
        }
        
        $invoices = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Data invoice finance berhasil diambil',
            'data' => $invoices
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getFinanceInvoices: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data invoice',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}

/**
 * API: Get finance dashboard data
 */
public function getFinanceDashboardData(Request $request)
{
    try {
        \Log::info('API getFinanceDashboardData called');
        
        // Statistics
        $totalInvoiceCount = Invoice::count();
        $paidInvoiceCount = Invoice::where('status_pembayaran', 'lunas')->count();
        $pendingInvoiceCount = Invoice::where('status_pembayaran', 'pembayaran awal')->count();
        $totalRevenue = Invoice::where('status_pembayaran', 'lunas')->sum('total');
        
        // Get invoices
        $query = Invoice::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }
        
        $invoices = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Data dashboard finance berhasil diambil',
            'data' => [
                'statistics' => [
                    'total_invoice' => $totalInvoiceCount,
                    'paid_invoice' => $paidInvoiceCount,
                    'pending_invoice' => $pendingInvoiceCount,
                    'total_revenue' => (int)$totalRevenue
                ],
                'invoices' => $invoices
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getFinanceDashboardData: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data dashboard',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}

/**
 * API: Get invoices for kwitansi
 */
public function getInvoicesForKwitansi(Request $request)
{
    // Cek login dan role
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized: not logged in'
        ], 401);
    }
    
    $user = Auth::user();
    if ($user->role !== 'finance') {
        return response()->json([
            'success' => false,
            'message' => 'Forbidden: wrong role'
        ], 403);
    }

    try {
        // HAPUS with(['perusahaan', 'layanan']) - karena relasi tidak ada
        $query = Invoice::whereIn('status_pembayaran', ['down payment', 'pembayaran awal'])
                ->whereDoesntHave('kwitansi')
                ->orderBy('created_at', 'desc');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('invoice_no', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        $invoices = $query->get()->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no ?? '',
                'order_number' => $invoice->order_number ?? '',
                'company_name' => $invoice->company_name ?? '',
                'company_address' => $invoice->company_address ?? '',
                'kontak' => $invoice->kontak ?? '',
                'client_name' => $invoice->client_name ?? '',
                'description' => $invoice->description ?? '',
                'nama_layanan' => $invoice->nama_layanan ?? '',
                'invoice_date' => $invoice->invoice_date ?? '',
                'payment_method' => $invoice->payment_method ?? '',
                'subtotal' => (float) ($invoice->subtotal ?? 0),
                'tax' => (float) ($invoice->tax ?? 0),
                'fee_maintenance' => (float) ($invoice->fee_maintenance ?? 0),
                'total' => (float) ($invoice->total ?? 0),
                'status_pembayaran' => $invoice->status_pembayaran ?? 'down payment',
                'jenis_bank' => $invoice->jenis_bank ?? '',
                'kategori_pemasukan' => $invoice->kategori_pemasukan ?? 'layanan',
                'keterangan_tambahan' => $invoice->keterangan_tambahan ?? '',
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data invoice untuk kwitansi berhasil diambil',
            'data' => $invoices,
            'total' => $invoices->count()
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in getInvoicesForKwitansi: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data invoice: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * API: Get invoice detail for kwitansi form
     */
/**
 * API: Get invoice detail for kwitansi form
 */
public function getInvoiceDetailForKwitansi($id)
{
    try {
        // Ambil invoice tanpa relasi dulu
        $invoice = Invoice::find($id);
        
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }
        
        // Ambil data perusahaan dari field langsung, bukan relasi
        $company_name = $invoice->company_name ?? '';
        $kontak = $invoice->kontak ?? '';
        $company_address = $invoice->company_address ?? '';
        
        // Ambil data layanan dari field langsung
        $nama_layanan = $invoice->nama_layanan ?? '';
        
        // Ambil data client dari field langsung
        $client_name = $invoice->client_name ?? '';
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no ?? '',
                'company_name' => $company_name,
                'kontak' => $kontak,
                'company_address' => $company_address,
                'client_name' => $client_name,
                'nama_layanan' => $nama_layanan,
                'description' => $invoice->description ?? '',
                'subtotal' => (float) ($invoice->subtotal ?? 0),
                'tax' => (float) ($invoice->tax ?? 0),
                'fee_maintenance' => (float) ($invoice->fee_maintenance ?? 0),
                'total' => (float) ($invoice->total ?? 0),
                'payment_method' => $invoice->payment_method ?? '',
                'jenis_bank' => $invoice->jenis_bank ?? '',
                'kategori_pemasukan' => $invoice->kategori_pemasukan ?? '',
                'keterangan_tambahan' => $invoice->keterangan_tambahan ?? '',
                'status_pembayaran' => $invoice->status_pembayaran ?? ''
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in getInvoiceDetailForKwitansi: ' . $e->getMessage(), [
            'id' => $id,
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * API: Get all invoices - untuk kwitansi dropdown
     */
    public function getAllInvoicesApi(Request $request)
    {
        try {
            $query = Invoice::with(['perusahaan'])->orderBy('created_at', 'desc');

            // Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('invoice_no', 'like', "%{$search}%");
                });
            }

            $invoices = $query->get(['id', 'invoice_no', 'company_name', 'client_name', 'order_number', 'total', 'status_pembayaran']);

            return response()->json([
                'success' => true,
                'message' => 'Data semua invoice berhasil diambil',
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllInvoicesApi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data invoice',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * API: Create project from invoice manually
     */
    public function createProjectFromInvoice($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Cek apakah sudah ada project
            if ($invoice->hasProject()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice sudah memiliki project',
                    'data' => [
                        'invoice' => $invoice,
                        'project' => $invoice->project,
                        'project_link' => $invoice->project_link
                    ]
                ]);
            }

            // Buat project dari invoice
            $project = $invoice->createProjectFromInvoice();

            if ($project) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project berhasil dibuat dari invoice',
                    'data' => [
                        'invoice' => $invoice,
                        'project' => $project,
                        'project_link' => $invoice->project_link
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat project dari invoice'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error creating project from invoice: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Sync project with invoice
     */
    public function syncProjectWithInvoice($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            if (!$invoice->hasProject()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice belum memiliki project'
                ], 404);
            }

            $result = $invoice->syncProjectWithInvoice();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project berhasil disinkronisasi dengan invoice',
                    'data' => [
                        'invoice' => $invoice,
                        'project' => $invoice->project
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyinkronisasi project'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing project with invoice: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get project info for invoice
     */
    public function getInvoiceProjectInfo($id)
    {
        try {
            $invoice = Invoice::with(['projects'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Informasi project berhasil diambil',
                'data' => [
                    'has_project' => $invoice->hasProject(),
                    'project' => $invoice->project,
                    'project_status' => $invoice->project_status,
                    'project_link' => $invoice->project_link
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting invoice project info: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get layanan data for dropdown
     */
    public function getLayananForDropdown(Request $request)
    {
        try {
            Log::info('Getting layanan data for dropdown');

            $layanan = Layanan::orderBy('nama_layanan', 'asc')
                ->get(['id', 'nama_layanan', 'deskripsi', 'harga', 'hpp']);

            return response()->json([
                'success' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $layanan
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting layanan data for dropdown: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data layanan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    /**
     * API: Get perusahaan data for dropdown
     */
    public function getPerusahaanForDropdown(Request $request)
    {
        try {
            Log::info('Getting perusahaan data for dropdown');

            $perusahaan = Perusahaan::orderBy('nama_perusahaan', 'asc')
                ->get(['id', 'nama_perusahaan', 'klien', 'alamat', 'kontak', 'telepon', 'no_telp']);

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil diambil',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting perusahaan data for dropdown: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data perusahaan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getInvoicesForKwitansiAdmin(Request $request)
{
    // Cek login dan role
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized: not logged in'
        ], 401);
    }
    
    $user = Auth::user();
    if ($user->role !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'Forbidden: wrong role'
        ], 403);
    }

    try {
        // Query semua invoice (admin bisa lihat semua)
        $query = Invoice::whereDoesntHave('kwitansi')
            ->orderBy('created_at', 'desc');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        $invoices = $query->get()->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no ?? '',
                'company_name' => $invoice->company_name ?? '',
                'client_name' => $invoice->client_name ?? '',
                'total' => (float) ($invoice->total ?? 0),
                'status_pembayaran' => $invoice->status_pembayaran ?? ''
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data invoice untuk kwitansi admin berhasil diambil',
            'data' => $invoices,
            'total' => $invoices->count()
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in getInvoicesForKwitansiAdmin: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data invoice: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * API: Get invoice detail for kwitansi form - ADMIN VERSION
 */
public function getInvoiceDetailForKwitansiAdmin($id)
{
    try {
        $invoice = Invoice::find($id);
        
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no ?? '',
                'company_name' => $invoice->company_name ?? '',
                'kontak' => $invoice->kontak ?? '',
                'company_address' => $invoice->company_address ?? '',
                'client_name' => $invoice->client_name ?? '',
                'nama_layanan' => $invoice->nama_layanan ?? '',
                'description' => $invoice->description ?? '',
                'subtotal' => (float) ($invoice->subtotal ?? 0),
                'tax' => (float) ($invoice->tax ?? 0),
                'fee_maintenance' => (float) ($invoice->fee_maintenance ?? 0),
                'total' => (float) ($invoice->total ?? 0),
                'payment_method' => $invoice->payment_method ?? '',
                'jenis_bank' => $invoice->jenis_bank ?? '',
                'kategori_pemasukan' => $invoice->kategori_pemasukan ?? '',
                'keterangan_tambahan' => $invoice->keterangan_tambahan ?? '',
                'status_pembayaran' => $invoice->status_pembayaran ?? ''
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in getInvoiceDetailForKwitansiAdmin: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
}
