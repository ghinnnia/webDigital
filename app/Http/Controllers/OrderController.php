<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $q = $request->input('q');

        $query = Order::with(['invoice.projects.penanggungJawab']);

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('layanan', 'like', "%{$q}%")
                    ->orWhere('klien', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%");
            });
        }

        $orders = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        // Card finance:
        // - Total Lead = total seluruh perusahaan
        // - Customer = perusahaan dengan status aktif
        $totalLead = Perusahaan::count();
        $uniqueCustomers = Schema::hasColumn('perusahaans', 'status')
            ? Perusahaan::where('status', 'aktif')->count()
            : Perusahaan::where('jumlah_kerjasama', '>', 0)->count();
        $conversionRate = $totalLead > 0 ? round(($uniqueCustomers / $totalLead) * 100, 2) : 0;
        $totalOrders = Order::count();

        return view('finance.data_orderan', compact('orders', 'totalLead', 'uniqueCustomers', 'conversionRate', 'totalOrders'));
    }

    public function show($id)
    {
        $order = Order::with(['invoice.projects.penanggungJawab'])->findOrFail($id);
        
        // Check if request expects JSON
        if (request()->wantsJson()) {
            return response()->json($order);
        }
        
        return view('finance.order_show', compact('order'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_no' => 'nullable|string',
            'layanan' => 'required|string',
            'kategori' => 'nullable|string',
            'company_name' => 'nullable|string',
            'order_date' => 'nullable|date',
            'invoice_no' => 'nullable|string',
            'klien' => 'nullable|string',
            'company_address' => 'nullable|string',
            'description' => 'nullable|string',
            'subtotal' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'status' => 'nullable|in:paid,partial,pending,overdue',
            'work_status' => 'nullable|in:planning,progress,review,completed,onhold',
            'invoice_id' => 'nullable|integer|exists:invoices,id',
        ]);

        $order = Order::create($data);
        
        // Check if request expects JSON
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'data' => $order
            ], 201);
        }

        return redirect()->route('orders.show', $order->id);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'total' => 'required|numeric|min:0',
        ]);

        $order->update($data);
        
        // Check if request expects JSON
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order berhasil diupdate',
                'data' => $order
            ]);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        
        // Check if request expects JSON
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dihapus'
            ]);
        }
        
        return redirect()->route('orders.index');
    }
}
