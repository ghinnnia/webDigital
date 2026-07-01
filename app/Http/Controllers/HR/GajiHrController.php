<?php
// app/Http/Controllers/HR/GajiHrController.php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Gaji;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\TunjanganKaryawan;
use App\Models\GajiTemplate;
use Carbon\Carbon;

class GajiHrController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $divisiId = $request->query('divisi_id');
        
        // Ambil karyawan
        $query = User::with(['divisi'])
            ->whereNotIn('role', [ 'owner']);
        
        if ($divisiId) {
            $query->where('divisi_id', $divisiId);
        }
        
        $karyawan = $query->orderBy('role')->orderBy('name')->get();
        
        // Daftar divisi untuk filter
        $divisiList = Divisi::orderBy('divisi')->get();
        
        // Data gaji yang sudah ada
        $gajiExisting = Gaji::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('karyawan_id');
        
        // PERBAIKAN: baca tunjangan dari karyawan_tunjangan (pivot bersih, tanpa bulan/tahun)
        // Map user.id → karyawan record → tunjangan master
        $userIds = $karyawan->pluck('id');
        $karyawanRecords = Karyawan::whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id'); // [user_id => Karyawan]

        // Ambil tunjangan default (dari karyawan_tunjangan) per karyawan, di-key oleh user_id
        $tunjanganData = collect();
        foreach ($karyawanRecords as $userId => $karyawanRecord) {
            // tunjanganDefault() mengembalikan TunjanganMaster objects (via karyawan_tunjangan)
            $tunjanganData[$userId] = $karyawanRecord->tunjanganDefault()->get();
        }
        
        // Hitung grand total
        $grandTotal = 0;
        foreach ($karyawan as $k) {
            $gaji = $gajiExisting[$k->id] ?? null;
            $tunjangan = $tunjanganData[$k->id] ?? collect();
            $totalTunjangan = $tunjangan->sum('nominal');
            
            $gajiPokok = $gaji->gaji_pokok ?? $k->gaji ?? ($k->role == 'general_manager' ? 15000000 : ($k->role == 'manager_divisi' ? 10000000 : 5000000));
            $potongan = $gaji->potongan_bpjs ?? 0;
            
            $grandTotal += $gajiPokok + $totalTunjangan - $potongan;
        }
        
        // Ambil data template gaji
        $gajiTemplates = GajiTemplate::with('divisi')
            ->orderBy('role')
            ->orderBy('divisi_id')
            ->get();
        
        return view('hr.gaji.index', compact(
            'karyawan', 
            'bulan', 
            'tahun', 
            'divisiList', 
            'gajiExisting', 
            'tunjanganData', 
            'grandTotal', 
            'gajiTemplates'
        ));
    }
    
    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        DB::beginTransaction();
        
        try {
            foreach ($request->gaji_pokok as $userId => $gajiPokok) {
                $totalTunjangan = $request->tunjangan[$userId] ?? 0;
                $potongan = $request->potongan[$userId] ?? 0;
                $totalGaji = $gajiPokok + $totalTunjangan - $potongan;
                
                // Ambil detail tunjangan dari hidden input
                $tunjanganDetail = $request->tunjangan_detail[$userId] ?? null;
                if ($tunjanganDetail && is_string($tunjanganDetail)) {
                    $tunjanganDetail = json_decode($tunjanganDetail, true);
                }
                
                Gaji::updateOrCreate(
                    [
                        'karyawan_id' => $userId,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ],
                    [
                        'gaji_pokok' => $gajiPokok,
                        'total_tunjangan' => $totalTunjangan,
                        'tunjangan_detail' => $tunjanganDetail,
                        'potongan_bpjs' => $potongan,
                        'total_gaji' => $totalGaji,
                        'status' => 'menunggu_finance'
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('hr.gaji.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', '✅ Data gaji berhasil disimpan! Finance sekarang bisa melihatnya.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    
    public function applyTemplate(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $divisiId = $request->divisi_id;
        $roleFilter = $request->role;
        
        DB::beginTransaction();
        
        try {
            $query = User::whereNotIn('role', ['admin', 'owner']);
            
            if ($divisiId) {
                $query->where('divisi_id', $divisiId);
            }
            
            if ($roleFilter) {
                $query->where('role', $roleFilter);
            }
            
            $karyawan = $query->get();
            
            foreach ($karyawan as $k) {
                // Cari template berdasarkan role dan divisi
                $template = GajiTemplate::where('role', $k->role)
                    ->where(function($q) use ($k) {
                        $q->where('divisi_id', $k->divisi_id)->orWhereNull('divisi_id');
                    })
                    ->first();
                
                if ($template) {
                    Gaji::updateOrCreate(
                        [
                            'karyawan_id' => $k->id,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                        ],
                        [
                            'gaji_pokok' => $template->gaji_pokok,
                            'tunjangan_tetap' => $template->tunjangan_tetap,
                            'tunjangan_kinerja' => $template->tunjangan_kinerja,
                            'bonus' => 0,
                            'potongan_bpjs' => 0,
                            'potongan_lain' => 0,
                            'total_gaji' => $template->gaji_pokok + $template->tunjangan_tetap + $template->tunjangan_kinerja,
                            'status' => 'draft'
                        ]
                    );
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Template berhasil diterapkan'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function kirimKeFinance(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        try {
            Gaji::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->update(['status' => 'menunggu_finance']);
            
            return redirect()->route('hr.gaji.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', 'Data gaji berhasil dikirim ke Finance');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim data ke Finance: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $gaji = Gaji::with('karyawan')->findOrFail($id);
        return view('hr.gaji_detail', compact('gaji'));
    }
    
    // API untuk mendapatkan template gaji berdasarkan role dan divisi
    public function getGajiTemplate(Request $request)
    {
        $role = $request->query('role');
        $divisiId = $request->query('divisi_id');
        
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role required'], 400);
        }
        
        $template = GajiTemplate::where('role', $role)
            ->where(function($q) use ($divisiId) {
                $q->where('divisi_id', $divisiId)->orWhereNull('divisi_id');
            })
            ->first();
        
        if ($template) {
            return response()->json([
                'success' => true,
                'data' => [
                    'gaji_pokok' => $template->gaji_pokok,
                    'tunjangan_tetap' => $template->tunjangan_tetap,
                    'tunjangan_kinerja' => $template->tunjangan_kinerja,
                    'gaji_formatted' => 'Rp ' . number_format($template->gaji_pokok, 0, ',', '.')
                ]
            ]);
        }
        
        // Default values
        $defaults = [
            'general_manager' => 15000000,
            'manager_divisi' => 10000000,
            'finance' => 8000000,
            'hr' => 7000000,
            'karyawan' => 5000000,
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'gaji_pokok' => $defaults[$role] ?? 5000000,
                'gaji_formatted' => 'Rp ' . number_format($defaults[$role] ?? 5000000, 0, ',', '.')
            ],
            'is_default' => true
        ]);

        
    }
}