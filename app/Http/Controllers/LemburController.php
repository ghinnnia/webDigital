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
            ->selectRaw('user_id, SUM(durasi) as total_jam, COUNT(*) as total_hari, SUM(total_upah) as total_upah')
            ->groupBy('user_id')
            ->with('user')
            ->get();
        
        // Tarif lembur default (Rp per jam) jika belum di-set
        $defaultUpahPerJam = 50000;
        
        return view('finance.lembur.index', compact('lemburs', 'totalPerKaryawan', 'defaultUpahPerJam'));
    }

    /**
     * Set custom upah lembur per jam untuk satu atau banyak record
     */
    public function setUpahLembur(Request $request)
    {
        $request->validate([
            'lembur_id'    => 'required_without:bulk_ids|exists:lemburs,id',
            'bulk_ids'     => 'required_without:lembur_id|array',
            'bulk_ids.*'   => 'exists:lemburs,id',
            'upah_per_jam' => 'required|numeric|min:0',
        ]);

        $ids = $request->filled('bulk_ids') ? $request->bulk_ids : [$request->lembur_id];
        $upahPerJam = $request->upah_per_jam;

        foreach ($ids as $id) {
            $lembur = Lembur::find($id);
            if ($lembur) {
                $lembur->update([
                    'upah_per_jam' => $upahPerJam,
                    'total_upah'   => $lembur->durasi * $upahPerJam,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Upah lembur berhasil diperbarui!');
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

    // ========== UNTUK MANAGER DIVISI ==========

    public function managerDivisiIndex(Request $request)
    {
        $user = Auth::user();
        $divisiId = $user->divisi_id;

        $query = Lembur::with(['user', 'approver'])
            ->whereHas('user', function ($q) use ($divisiId) {
                $q->where('divisi_id', $divisiId);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $lemburs = $query->paginate(15);

        $karyawan = User::where('role', 'karyawan')
            ->where('divisi_id', $divisiId)
            ->get();

        return view('manager_divisi.lembur.index', compact('lemburs', 'karyawan'));
    }

    public function approveByManager($id)
    {
        $user = Auth::user();
        $lembur = Lembur::with('user')->findOrFail($id);

        // Pastikan karyawan ini adalah anggota divisi manager
        if ($lembur->user->divisi_id !== $user->divisi_id) {
            return redirect()->back()->with('error', 'Anda tidak berwenang menyetujui lembur ini.');
        }

        $lembur->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil disetujui!');
    }

    public function rejectByManager(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $lembur = Lembur::with('user')->findOrFail($id);

        if ($lembur->user->divisi_id !== $user->divisi_id) {
            return redirect()->back()->with('error', 'Anda tidak berwenang menolak lembur ini.');
        }

        $lembur->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur ditolak.');
    }

}