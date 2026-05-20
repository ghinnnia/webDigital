<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telp', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($divisi = $request->query('divisi')) {
            $query->where('divisi', $divisi);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $pegawai = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('general_manajer.data_karyawan', compact('pegawai'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'telp' => 'required|string|max:50',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'status' => 'required|in:Magang,Karyawan Tetap',
            'email' => 'required|email|max:255',
        ]);

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource (AJAX JSON).
     */
    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return response()->json($pegawai);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'telp' => 'required|string|max:50',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'status' => 'required|in:Magang,Karyawan Tetap',
            'email' => 'required|email|max:255',
        ]);

        $pegawai->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}