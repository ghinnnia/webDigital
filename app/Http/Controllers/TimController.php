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
            // Try to get from tims table
            $tims = Tim::where('divisi_id', $divisiId)
                ->orderBy('tim', 'asc')
                ->get(['id', 'tim', 'divisi_id']);
            
            if ($tims->isNotEmpty()) {
                return response()->json($tims);
            }
            
            // If no tims found, return empty array
            return response()->json([]);
            
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Get all tims
     */
    public function index(Request $request)
    {
        $query = Tim::with('divisi');
        
        if ($request->has('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
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
            'divisi_id' => 'required|exists:divisis,id'
        ]);

        // Check if tim already exists in the same divisi
        $existing = Tim::where('tim', $request->tim)
            ->where('divisi_id', $request->divisi_id)
            ->first();
            
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Tim sudah ada di divisi ini'
            ], 400);
        }

        $tim = Tim::create([
            'tim' => $request->tim,
            'divisi_id' => $request->divisi_id,
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
            'divisi_id' => 'required|exists:divisis,id'
        ]);

        // Check if tim already exists in the same divisi (excluding current)
        $existing = Tim::where('tim', $request->tim)
            ->where('divisi_id', $request->divisi_id)
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
            'divisi_id' => $request->divisi_id,
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