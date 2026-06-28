<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Tampilan halaman pengumuman (untuk admin dan HR)
     */
    public function index()
    {
        $user = Auth::user();
        
        // Jika admin, pakai view admin
        if ($user->role == 'admin') {
            return view('admin.pengumuman');
        }
        
        // Jika HR atau lainnya, pakai view HR
        return view('hr.pengumuman.index');
    }

    /**
     * API: Get all announcements (JSON)
     */
    public function getData()
    {
        $pengumuman = Pengumuman::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Get single announcement
     */
    public function show($id)
    {
        $pengumuman = Pengumuman::with('creator')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Create announcement
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_pesan' => 'required|string',
            'target' => 'required|in:semua,hr,manager,karyawan'
        ]);

        $pengumuman = Pengumuman::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'isi_pesan' => $request->isi_pesan,
            'target' => $request->target,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat',
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Update announcement
     */
    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_pesan' => 'required|string',
            'target' => 'required|in:semua,hr,manager,karyawan'
        ]);

        $pengumuman->update([
            'judul' => $request->judul,
            'isi_pesan' => $request->isi_pesan,
            'target' => $request->target,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diupdate',
            'data' => $pengumuman
        ]);
    }

    /**
     * API: Delete announcement
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus'
        ]);
    }

    public function getAnnouncementsApi()
    {
        $pengumuman = Pengumuman::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengumuman
        ]);
    }

    public function getAnnouncementDatesApi()
    {
        $dates = Pengumuman::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->pluck('date');

        return response()->json([
            'success' => true,
            'dates' => $dates
        ]);
    }
}