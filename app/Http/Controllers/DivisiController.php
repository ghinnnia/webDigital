<?php
// app/Http/Controllers/DivisiController.php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisiController extends Controller
{
    /**
     * Get list of all divisi
     */
    public function list(Request $request)
    {
        try {
            // First try to get from divisis table
            $divisis = Divisi::orderBy('divisi', 'asc')->get();
            
            if ($divisis->isNotEmpty()) {
                return response()->json($divisis);
            }
            
            // If no data in divisis table, get from users table
            $divisisFromUsers = User::select('divisi_id', 'divisi')
                ->whereNotNull('divisi_id')
                ->with('divisi')
                ->distinct()
                ->get()
                ->filter(function($item) {
                    return $item->divisi != null;
                })
                ->map(function($item) {
                    return [
                        'id' => $item->divisi_id,
                        'divisi' => $item->divisi->divisi ?? $item->divisi
                    ];
                });
            
            if ($divisisFromUsers->isNotEmpty()) {
                return response()->json($divisisFromUsers->values());
            }
            
            // Return default divisi if no data found
            $defaultDivisis = [
                ['id' => 1, 'divisi' => 'IT'],
                ['id' => 2, 'divisi' => 'HR'],
                ['id' => 3, 'divisi' => 'Finance'],
                ['id' => 4, 'divisi' => 'Marketing'],
                ['id' => 5, 'divisi' => 'Sales'],
                ['id' => 6, 'divisi' => 'Operations'],
                ['id' => 7, 'divisi' => 'Legal'],
            ];
            
            return response()->json($defaultDivisis);
            
        } catch (\Exception $e) {
            // Return default divisi on error
            return response()->json([
                ['id' => 1, 'divisi' => 'IT'],
                ['id' => 2, 'divisi' => 'HR'],
                ['id' => 3, 'divisi' => 'Finance'],
                ['id' => 4, 'divisi' => 'Marketing'],
                ['id' => 5, 'divisi' => 'Sales'],
                ['id' => 6, 'divisi' => 'Operations'],
                ['id' => 7, 'divisi' => 'Legal'],
            ]);
        }
    }

    /**
     * Get all divisi with details
     */
    public function index()
    {
        $divisis = Divisi::withCount('users')->orderBy('divisi')->get();
        return response()->json($divisis);
    }

    /**
     * Store new divisi
     */
    public function store(Request $request)
    {
        $request->validate([
            'divisi' => 'required|string|max:255|unique:divisis,divisi'
        ]);

        $divisi = Divisi::create([
            'divisi' => $request->divisi,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil ditambahkan',
            'data' => $divisi
        ]);
    }

    /**
     * Update divisi
     */
    public function update(Request $request, $id)
    {
        $divisi = Divisi::findOrFail($id);
        
        $request->validate([
            'divisi' => 'required|string|max:255|unique:divisis,divisi,' . $id
        ]);

        $divisi->update([
            'divisi' => $request->divisi,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil diupdate',
            'data' => $divisi
        ]);
    }

    /**
     * Delete divisi
     */
    public function destroy($id)
    {
        $divisi = Divisi::findOrFail($id);
        
        // Check if there are users in this divisi
        $userCount = User::where('divisi_id', $id)->count();
        if ($userCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus divisi karena masih ada ' . $userCount . ' karyawan di divisi ini'
            ], 400);
        }
        
        $divisi->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil dihapus'
        ]);
    }
}