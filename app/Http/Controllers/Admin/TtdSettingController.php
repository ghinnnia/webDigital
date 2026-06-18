<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TtdSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TtdSettingController extends Controller
{
    public function index()
    {
        $ttds = TtdSetting::all()->keyBy('role');
        
        // Ambil data pejabat dari tabel users berdasarkan role
        $pejabatList = [
            'finance' => User::where('role', 'finance')->select('id', 'name', 'email')->get(),
            'hr' => User::where('role', 'hr')->select('id', 'name', 'email')->get(),
            'karyawan' => User::where('role', 'karyawan')->select('id', 'name', 'email')->get(),
        ];
        
        return view('admin.ttd.index', compact('ttds', 'pejabatList'));
    }

    public function store(Request $request)
    {
        // Debug: log data yang masuk
        \Log::info('TTD Store Request:', $request->all());
        
        $request->validate([
            'jabatan' => 'required|in:finance,hr,karyawan',
            'nama_pejabat' => 'required|string|max:255',
            'ttd_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $data = [
            'role' => $request->jabatan,
            'nama_pejabat' => $request->nama_pejabat,
            'jabatan' => $request->jabatan, // simpan juga sebagai jabatan
            'is_active' => $request->has('is_active')
        ];

        if ($request->hasFile('ttd_image')) {
            // Hapus file lama jika ada
            $old = TtdSetting::where('role', $request->jabatan)->first();
            if ($old && $old->file_path) {
                Storage::disk('public')->delete($old->file_path);
            }
            
            $file = $request->file('ttd_image');
            $filename = 'ttd_' . $request->jabatan . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('ttd', $filename, 'public');
            $data['file_path'] = $path;
        }

        TtdSetting::updateOrCreate(
            ['role' => $request->jabatan],
            $data
        );

        return redirect()->back()->with('success', 'TTD berhasil disimpan');
    }

    public function destroy($id)
    {
        $ttd = TtdSetting::findOrFail($id);
        if ($ttd->file_path) {
            Storage::disk('public')->delete($ttd->file_path);
        }
        $ttd->delete();
        return redirect()->back()->with('success', 'TTD berhasil dihapus');
    }
}