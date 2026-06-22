<?php
// app/Http/Controllers/TimController.php

namespace App\Http\Controllers;

use App\Models\Tim;
use App\Models\Divisi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimController extends Controller
{
    /**
     * Get tim by divisi ID
     */
    public function getByDivisi($divisiId)
    {
        try {
            $divisi = Divisi::find($divisiId);
            if (!$divisi) {
                return response()->json([]);
            }

            // Mencari relasi menggunakan nama divisi (string) bukan divisi_id
            $tims = Tim::where('divisi', $divisi->divisi)
                ->orderBy('tim', 'asc')
                ->get(['id', 'tim', 'divisi']);
            
            return response()->json($tims);
            
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Get all tims
     */
    public function index(Request $request)
    {
        $query = Tim::query();
        
        if ($request->has('divisi_id')) {
            $divisi = Divisi::find($request->divisi_id);
            if ($divisi) {
                $query->where('divisi', $divisi->divisi);
            }
        }
        
        $tims = $query->orderBy('tim')->get();
        
        return response()->json($tims);
    }

    /**
     * Store new tim
     */
    public function store(Request $request)
    {
        $request->validate([
            'tim' => 'required|string|max:255',
            'divisi_id' => 'required|exists:divisi,id' // Nama tabel adalah 'divisi' (bukan divisis)
        ]);

        $divisi = Divisi::findOrFail($request->divisi_id);

        // Check if tim already exists in the same divisi
        $existing = Tim::where('tim', $request->tim)
            ->where('divisi', $divisi->divisi)
            ->first();
            
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Tim sudah ada di divisi ini'
            ], 400);
        }

        $tim = Tim::create([
            'tim' => $request->tim,
            'divisi' => $divisi->divisi,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil ditambahkan',
            'data' => $tim
        ]);
    }

    /**
     * Update tim
     */
    public function update(Request $request, $id)
    {
        $tim = Tim::findOrFail($id);
        
        $request->validate([
            'tim' => 'required|string|max:255',
            'divisi_id' => 'required|exists:divisi,id' // Nama tabel adalah 'divisi'
        ]);

        $divisi = Divisi::findOrFail($request->divisi_id);

        // Check if tim already exists in the same divisi (excluding current)
        $existing = Tim::where('tim', $request->tim)
            ->where('divisi', $divisi->divisi)
            ->where('id', '!=', $id)
            ->first();
            
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Tim sudah ada di divisi ini'
            ], 400);
        }

        $tim->update([
            'tim' => $request->tim,
            'divisi' => $divisi->divisi,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil diupdate',
            'data' => $tim
        ]);
    }

    /**
     * Delete tim
     */
    public function destroy($id)
    {
        $tim = Tim::findOrFail($id);
        
        // Check if there are karyawan in this tim
        $karyawanCount = Karyawan::where('tim_id', $id)->count();
        if ($karyawanCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus tim karena masih ada ' . $karyawanCount . ' karyawan di tim ini'
            ], 400);
        }
        
        $tim->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tim berhasil dihapus'
        ]);
    }

    /**
     * Get members of a tim
     */
    public function getMembers($id)
    {
        $tim = Tim::with(['karyawan.user'])->findOrFail($id);
        
        $members = $tim->karyawan->map(function($karyawan) {
            return [
                'id' => $karyawan->id,
                'user_id' => $karyawan->user_id,
                'nama' => $karyawan->nama,
                'email' => $karyawan->email,
                'role' => $karyawan->role,
                'foto' => $karyawan->foto ? asset('storage/' . $karyawan->foto) : null
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'tim' => $tim,
                'members' => $members,
                'total_members' => $members->count()
            ]
        ]);
    }
}