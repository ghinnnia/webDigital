<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kwitansi;
use App\Models\Invoice; // TAMBAHKAN INI
use App\Models\Cashflow; // TAMBAHAN IMPORT CASHFLOW
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class KwitansiController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Kwitansi::with('invoice')->latest();

            if ($request->has('search') && $request->search != '') {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nama_klien', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('nama_perusahaan', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('deskripsi', 'LIKE', "%{$searchTerm}%");
                });
            }

            $kwitansis = $query->get();

            // Return view for web requests, JSON for API requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kwitansi berhasil diambil',
                    'data' => $kwitansis
                ], 200);
            }

            // Return HTML view for normal web requests
            return view('admin.kwitansi', [
                'kwitansis' => $kwitansis
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
                ], 500);
            }

            // Return error view for web requests
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Method untuk API (JSON response)
     */
    public function getKwitansiData(Request $request)
    {
        try {
            $query = Kwitansi::with('invoice')->latest();

            if ($request->has('search') && $request->search != '') {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nama_klien', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('nama_perusahaan', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('deskripsi', 'LIKE', "%{$searchTerm}%");
                });
            }

            $kwitansis = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kwitansi berhasil diambil',
                'data' => $kwitansis
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finance index page
     */
    public function financeIndex(Request $request)
    {
        $query = Kwitansi::with('invoice')->latest();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_klien', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nama_perusahaan', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('deskripsi', 'LIKE', "%{$searchTerm}%");
            });
        }

        $kwitansis = $query->paginate(10);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->is('*ajax*')) {
            return response()->json([
                'success' => true,
                'message' => 'Data kwitansi berhasil diambil',
                'data' => $kwitansis->items(),
                'pagination' => [
                    'total' => $kwitansis->total(),
                    'per_page' => $kwitansis->perPage(),
                    'current_page' => $kwitansis->currentPage(),
                    'last_page' => $kwitansis->lastPage(),
                    'from' => $kwitansis->firstItem(),
                    'to' => $kwitansis->lastItem()
                ]
            ]);
        }

        return view('finance.kwitansi', compact('kwitansis'));
    }

    /**
     * Display the specified resource.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $kwitansi = Kwitansi::with('invoice')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $kwitansi
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kwitansi tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    // KwitansiController.php - store method
    public function store(Request $request): JsonResponse
    {
        try {
            // Validasi input awal - hanya tanggal dan invoice_id yang required
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'tanggal' => 'required|date',
                'status' => 'nullable|in:Pembayaran Awal,Lunas'
            ]);

            // Cegah pembuatan kwitansi ganda untuk invoice yang sama
            if (Kwitansi::where('invoice_id', $validated['invoice_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice ini sudah memiliki kwitansi.'
                ], 422);
            }

            // Ambil data invoice
            $invoice = Invoice::findOrFail($validated['invoice_id']);

            DB::beginTransaction();

            // Generate kwitansi number
            $year = date('Y');
            $lastKwitansi = Kwitansi::orderBy('id', 'desc')->first();

            if ($lastKwitansi && $lastKwitansi->kwitansi_no) {
                $lastNumber = intval(substr($lastKwitansi->kwitansi_no, -5));
                $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '00001';
            }

            $kwitansiNo = "KW-$year-$nextNumber";

            // Populate kwitansi data dari invoice
            $status = $request->input('status', 'Pembayaran Awal');

            $kwitansiData = [
                'kwitansi_no' => $kwitansiNo,
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'tanggal' => $validated['tanggal'],
                
                // Data perusahaan dari invoice
                'nama_perusahaan' => $invoice->company_name,
                'company_address' => $invoice->company_address,
                'kontak' => $invoice->kontak,
                
                'order_number' => $invoice->order_number,
                'nama_klien' => $invoice->client_name,
                
                // Data layanan dari invoice
                'nama_layanan' => $invoice->nama_layanan,
                'deskripsi' => $invoice->description,
                'payment_method' => $invoice->payment_method,
                
                // Data finansial dari invoice
                'harga' => $invoice->subtotal ?? 0,
                'sub_total' => $invoice->subtotal ?? 0,
                'tax' => $invoice->tax ?? 0,
                'fee_maintenance' => $invoice->fee_maintenance ?? 0,
                'total' => $invoice->total ?? 0,
                
                // Status pembayaran dari form (default: Pembayaran Awal)
                'status' => $status,
                
                // Data bank - gunakan jenis_bank dari invoice atau default
                'bank' => $invoice->jenis_bank ?? 'BCA',
                'jenis_bank' => $invoice->jenis_bank,
                'no_rekening' => $request->no_rekening ?? null,
                
                // Data tambahan dari invoice
                'keterangan_tambahan' => $invoice->keterangan_tambahan,
                'kategori_pemasukan' => $invoice->kategori_pemasukan
            ];

            $kwitansi = Kwitansi::create($kwitansiData);

            // Tambahkan data ke Cashflow sebagai pemasukan
            Cashflow::create([
                'tanggal_transaksi' => $validated['tanggal'],
                'nama_transaksi' => 'Kwitansi - ' . $invoice->client_name . ' (' . $kwitansiNo . ')',
                'deskripsi' => $invoice->description ?? $invoice->nama_layanan,
                'jumlah' => $kwitansi->total,
                'tipe_transaksi' => 'pemasukan',
                'kategori_id' => 1, // Kategori default untuk pemasukan dari kwitansi
                'subkategori' => null,
            ]);

            // Sinkron status invoice dengan status kwitansi.
            $this->syncInvoicePaymentStatusFromKwitansi($kwitansi, $invoice);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kwitansi berhasil dibuat dari Invoice!',
                'data' => $kwitansi
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $kwitansi = Kwitansi::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kwitansi tidak ditemukan.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Only allow editing of tanggal and status from the frontend
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'status' => 'required|in:Pembayaran Awal,Lunas',
            ]);

            $oldStatus = $kwitansi->status;
            $newStatus = $validated['status'];

            // Update only the validated fields
            $kwitansi->update($validated);

            // Jika status berubah dari "Pembayaran Awal" menjadi "Lunas", tambahkan/update di Cashflow
            if ($oldStatus !== 'Lunas' && $newStatus === 'Lunas') {
                // Cek apakah sudah ada record cashflow untuk kwitansi ini
                $cashflow = Cashflow::where('nama_transaksi', 'LIKE', '%' . $kwitansi->kwitansi_no . '%')->first();

                if (!$cashflow) {
                    // Buat record baru di Cashflow
                    Cashflow::create([
                        'tanggal_transaksi' => $validated['tanggal'],
                        'nama_transaksi' => 'Kwitansi - ' . $kwitansi->nama_klien . ' (' . $kwitansi->kwitansi_no . ')',
                        'deskripsi' => $kwitansi->deskripsi,
                        'jumlah' => $kwitansi->total,
                        'tipe_transaksi' => 'pemasukan',
                        'kategori_id' => 1,
                        'subkategori' => null,
                    ]);
                } else {
                    // Update record yang sudah ada
                    $cashflow->update([
                        'tanggal_transaksi' => $validated['tanggal'],
                        'jumlah' => $kwitansi->total,
                    ]);
                }
            }

            // Sinkron status invoice dengan status kwitansi terbaru.
            $this->syncInvoicePaymentStatusFromKwitansi($kwitansi);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kwitansi berhasil diperbarui!',
                'data' => $kwitansi
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            // Handle database errors
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $kwitansi = Kwitansi::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kwitansi tidak ditemukan.'
            ], 404);
        }

        try {
            // Hapus record cashflow yang terkait dengan kwitansi ini
            Cashflow::where('nama_transaksi', 'LIKE', '%' . $kwitansi->kwitansi_no . '%')->delete();

            // Delete kwitansi
            $kwitansi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kwitansi berhasil dihapus!'
            ]);
        } catch (QueryException $e) {
            // Handle database errors (constraint violations, etc.)
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kwitansi. Mungkin masih terkait dengan data lain.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice detail for kwitansi form
     * Frontend akan call ini untuk autofill form saat invoice dipilih
     */
    public function getInvoiceDetail($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'invoice_date' => $invoice->invoice_date,
                    'nama_perusahaan' => $invoice->company_name,
                    'company_address' => $invoice->company_address,
                    'kontak' => $invoice->kontak,
                    'nama_klien' => $invoice->client_name,
                    'nama_layanan' => $invoice->nama_layanan,
                    'payment_method' => $invoice->payment_method,
                    'deskripsi' => $invoice->description,
                    'harga' => $invoice->subtotal,
                    'sub_total' => $invoice->subtotal,
                    'tax' => $invoice->tax,
                    'fee_maintenance' => $invoice->fee_maintenance,
                    'total' => $invoice->total,
                    'status_pembayaran' => $invoice->status_pembayaran,
                    'jenis_bank' => $invoice->jenis_bank,
                    'keterangan_tambahan' => $invoice->keterangan_tambahan,
                    'kategori_pemasukan' => $invoice->kategori_pemasukan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Cetak kwitansi
     */
    /**
     * Get kwitansi data for printing (API)
     */
    public function getKwitansiForPrint($id)
    {
        try {
            $kwitansi = Kwitansi::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data kwitansi berhasil diambil',
                'data' => $kwitansi
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kwitansi tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cetak kwitansi (view)
     */
    public function cetak($id)
    {
        try {
            $kwitansi = Kwitansi::findOrFail($id);

            // Format tanggal
            $tanggal = \Carbon\Carbon::parse($kwitansi->tanggal)->locale('id')->isoFormat('DD/MM/YY');
            $tanggalLengkap = \Carbon\Carbon::parse($kwitansi->tanggal)->locale('id')->isoFormat('DD MMMM YYYY');

            return response()->json([
                'success' => true,
                'message' => 'Data kwitansi berhasil diambil',
                'data' => $kwitansi,
                'tanggal' => $tanggal,
                'tanggalLengkap' => $tanggalLengkap
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapping status kwitansi -> status pembayaran invoice.
     */
    private function mapKwitansiStatusToInvoiceStatus(?string $kwitansiStatus): string
    {
        $normalizedStatus = strtolower(trim((string) $kwitansiStatus));

        return $normalizedStatus === 'lunas'
            ? 'lunas'
            : 'down payment';
    }

    /**
     * Sinkronisasi status pembayaran invoice berdasarkan status kwitansi.
     */
    private function syncInvoicePaymentStatusFromKwitansi(Kwitansi $kwitansi, ?Invoice $invoice = null): void
    {
        $targetInvoice = $invoice ?: Invoice::find($kwitansi->invoice_id);

        if (!$targetInvoice) {
            return;
        }

        $targetStatus = $this->mapKwitansiStatusToInvoiceStatus($kwitansi->status);

        if ((string) $targetInvoice->status_pembayaran !== $targetStatus) {
            $targetInvoice->status_pembayaran = $targetStatus;
            $targetInvoice->save();
        }
    }
}
