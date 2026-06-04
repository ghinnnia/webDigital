<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LemburController extends Controller
{
    // ========== UNTUK KARYAWAN ==========
    // ========== UNTUK HR ==========

public function hrIndex(Request $request)
{
    $query = Lembur::with(['user', 'approver'])->orderBy('created_at', 'desc');
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }
    
    $lemburs = $query->paginate(15);
    $karyawan = User::where('role', 'karyawan')->get();
    
    return view('hr.lembur.index', compact('lemburs', 'karyawan'));
}
    public function index()
    {
        $lemburs = Lembur::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('karyawan.lembur.index', compact('lemburs'));
    }

    public function create()
    {
        return view('karyawan.lembur.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan' => 'nullable|string',
        ]);

        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = Carbon::parse($request->jam_selesai);
        $durasi = $selesai->diffInHours($mulai);

        Lembur::create([
            'user_id' => Auth::id(),
            'tanggal_lembur' => $request->tanggal_lembur,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'durasi' => $durasi,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.lembur.index')
            ->with('success', 'Pengajuan lembur berhasil dikirim!');
    }

    // ========== UNTUK ADMIN ==========
    
    public function adminIndex(Request $request)
    {
        $query = Lembur::with(['user', 'approver'])->orderBy('created_at', 'desc');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $lemburs = $query->paginate(15);
        $karyawan = User::where('role', 'karyawan')->get();
        
        return view('admin.lembur.index', compact('lemburs', 'karyawan'));
    }

    public function approve($id)
    {
        $lembur = Lembur::findOrFail($id);
        
        $lembur->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur disetujui!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $lembur = Lembur::findOrFail($id);
        
        $lembur->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur ditolak!');
    }

    // ========== UNTUK FINANCE ==========
    
    public function financeIndex(Request $request)
    {
        $query = Lembur::with(['user', 'approver'])
            ->where('status', 'approved')
            ->where('is_paid', false)
            ->orderBy('tanggal_lembur', 'asc');
        
        $lemburs = $query->paginate(15);
        
        $totalPerKaryawan = Lembur::where('status', 'approved')
            ->where('is_paid', false)
            ->selectRaw('user_id, SUM(durasi) as total_jam, COUNT(*) as total_hari')
            ->groupBy('user_id')
            ->get();
        
        return view('finance.lembur.index', compact('lemburs', 'totalPerKaryawan'));
    }

    public function markAsPaid(Request $request)
    {
        $request->validate([
            'lembur_ids' => 'required|array',
            'lembur_ids.*' => 'exists:lemburs,id',
        ]);

        Lembur::whereIn('id', $request->lembur_ids)->update([
            'is_paid' => true,
        ]);

        return redirect()->back()->with('success', 'Data lembur telah ditandai dibayar!');
    }
}