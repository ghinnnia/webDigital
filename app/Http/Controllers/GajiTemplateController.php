<?php
// app/Http/Controllers/GajiTemplateController.php

namespace App\Http\Controllers;

use App\Models\GajiTemplate;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GajiTemplateController extends Controller
{
    /**
     * Get template gaji berdasarkan role dan divisi
     */
    public function getTemplate(Request $request)
    {
        $role = $request->query('role');
        $divisiId = $request->query('divisi_id');

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role diperlukan'
            ], 400);
        }

        // Cari template berdasarkan role dan divisi
        $template = GajiTemplate::where('role', $role);
        
        if ($divisiId) {
            $template->where('divisi_id', $divisiId);
        } else {
            $template->whereNull('divisi_id');
        }
        
        $template = $template->first();

        if ($template) {
            return response()->json([
                'success' => true,
                'data' => [
                    'gaji_pokok' => $template->gaji_pokok,
                    'tunjangan_tetap' => $template->tunjangan_tetap,
                    'tunjangan_kinerja' => $template->tunjangan_kinerja,
                    'gaji_pokok_formatted' => 'Rp ' . number_format($template->gaji_pokok, 0, ',', '.'),
                    'total' => $template->gaji_pokok + $template->tunjangan_tetap + $template->tunjangan_kinerja,
                    'total_formatted' => 'Rp ' . number_format($template->gaji_pokok + $template->tunjangan_tetap + $template->tunjangan_kinerja, 0, ',', '.')
                ]
            ]);
        }

        // Return default values
        $defaultGaji = $this->getDefaultGajiByRole($role);
        
        return response()->json([
            'success' => true,
            'data' => $defaultGaji,
            'is_default' => true
        ]);
    }

    /**
     * Get all templates
     */
    public function index()
    {
        $templates = GajiTemplate::with('divisi')
            ->orderBy('role')
            ->orderBy('divisi_id')
            ->get();
            
        $roles = User::select('role')->distinct()->pluck('role');
        $divisis = Divisi::orderBy('divisi')->get();
        
        return view('hr.gaji_template', compact('templates', 'roles', 'divisis'));
    }

    /**
     * Store new template
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan_tetap' => 'nullable|numeric|min:0',
            'tunjangan_kinerja' => 'nullable|numeric|min:0',
        ]);

        $divisiId = $request->divisi_id ?: null;

        // Check if template exists
        $existing = GajiTemplate::where('role', $request->role)
            ->where(function($q) use ($divisiId) {
                if ($divisiId) {
                    $q->where('divisi_id', $divisiId);
                } else {
                    $q->whereNull('divisi_id');
                }
            })
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Template sudah ada untuk role ini'
            ], 400);
        }

        $template = GajiTemplate::create([
            'role' => $request->role,
            'divisi_id' => $divisiId,
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangan_tetap' => $request->tunjangan_tetap ?? 0,
            'tunjangan_kinerja' => $request->tunjangan_kinerja ?? 0,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template gaji berhasil ditambahkan',
            'data' => $template
        ]);
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $template = GajiTemplate::findOrFail($id);

        $request->validate([
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan_tetap' => 'nullable|numeric|min:0',
            'tunjangan_kinerja' => 'nullable|numeric|min:0',
        ]);

        $template->update([
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangan_tetap' => $request->tunjangan_tetap ?? 0,
            'tunjangan_kinerja' => $request->tunjangan_kinerja ?? 0,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template gaji berhasil diupdate',
            'data' => $template
        ]);
    }

    /**
     * Delete template
     */
    public function destroy($id)
    {
        $template = GajiTemplate::findOrFail($id);
        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template gaji berhasil dihapus'
        ]);
    }

    /**
     * Get default gaji by role
     */
    private function getDefaultGajiByRole($role)
    {
        $defaults = [
            'general_manager' => ['gaji_pokok' => 15000000, 'tunjangan_tetap' => 2000000, 'tunjangan_kinerja' => 3000000],
            'manager_divisi' => ['gaji_pokok' => 10000000, 'tunjangan_tetap' => 1500000, 'tunjangan_kinerja' => 2000000],
            'finance' => ['gaji_pokok' => 8000000, 'tunjangan_tetap' => 1000000, 'tunjangan_kinerja' => 1000000],
            'hr' => ['gaji_pokok' => 7000000, 'tunjangan_tetap' => 1000000, 'tunjangan_kinerja' => 1000000],
            'karyawan' => ['gaji_pokok' => 5000000, 'tunjangan_tetap' => 500000, 'tunjangan_kinerja' => 500000],
        ];

        $data = $defaults[$role] ?? ['gaji_pokok' => 4000000, 'tunjangan_tetap' => 0, 'tunjangan_kinerja' => 0];
        
        return [
            'gaji_pokok' => $data['gaji_pokok'],
            'tunjangan_tetap' => $data['tunjangan_tetap'],
            'tunjangan_kinerja' => $data['tunjangan_kinerja'],
            'gaji_pokok_formatted' => 'Rp ' . number_format($data['gaji_pokok'], 0, ',', '.'),
            'total' => $data['gaji_pokok'] + $data['tunjangan_tetap'] + $data['tunjangan_kinerja'],
            'total_formatted' => 'Rp ' . number_format($data['gaji_pokok'] + $data['tunjangan_tetap'] + $data['tunjangan_kinerja'], 0, ',', '.')
        ];
    }
}