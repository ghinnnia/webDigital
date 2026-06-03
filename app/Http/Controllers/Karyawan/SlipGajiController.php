<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\PayrollDetail;
use Illuminate\Support\Facades\Auth;

class SlipGajiController extends Controller
{
    public function index()
    {
        $slipGaji = PayrollDetail::with('payrollPeriod')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('karyawan.slip_gaji.index', compact('slipGaji'));
    }
    
    public function show($id)
    {
        $slip = PayrollDetail::with(['user', 'payrollPeriod'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        // Tandai notifikasi terkait sebagai sudah dibaca
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('link', 'like', "%slip-gaji/{$id}%")
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return view('karyawan.slip_gaji.show', compact('slip'));
    }
}