<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DataKaryawanController extends Controller
{
    public function index()
    {
        // The view expects a collection of users (with related karyawan and divisi).
        $users = User::with(['karyawan', 'divisi'])->orderBy('id', 'desc')->get();

        // Provide list of divisi for the add/edit form dropdown
        $divisis = Divisi::orderBy('divisi', 'asc')->get();

        // Build a case-insensitive name -> id map for quick lookup
        $divisiNameToId = [];
        foreach ($divisis as $d) {
            $divisiNameToId[strtolower(trim($d->divisi))] = $d->id;
        }

        // Sync logic:
        // 1) If a User has no divisi_id but their Karyawan record contains a divisi name,
        //    try to find the Divisi by name and set user's divisi_id.
        // 2) If a Karyawan has empty or 'Divisi Tidak Diketahui', but the User has a divisi,
        //    copy the user's divisi name into karyawan.divisi.
        foreach ($users as $user) {
            $k = $user->karyawan;

            if ($k) {
                $kDivName = trim((string) $k->divisi);
                $kDivLower = strtolower($kDivName);

                // If user missing divisi_id but karyawan has a valid divisi name
                if (empty($user->divisi_id) && $kDivName !== '' && $kDivLower !== 'divisi tidak diketahui') {
                    if (isset($divisiNameToId[$kDivLower])) {
                        $user->divisi_id = $divisiNameToId[$kDivLower];
                        try {
                            $user->save();
                        } catch (\Exception $e) {
                            // ignore save error to avoid breaking the page
                        }
                    }
                }

                // If karyawan divisi is empty/unknown but user has a divisi relation
                if ((empty($kDivName) || $kDivLower === 'divisi tidak diketahui') && $user->divisi) {
                    $k->divisi = $user->divisi->divisi;
                    try {
                        $k->save();
                    } catch (\Exception $e) {
                        // ignore save error
                    }
                }
            }
        }

        return view('admin.data_karyawan', ['karyawan' => $users, 'divisis' => $divisis]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'jabatan' => 'required',
            'gaji' => 'required|numeric',
            'alamat' => 'required',
            'kontak' => 'required',
        ]);

        Karyawan::create([
            'nama'      => $request->nama,
            'jabatan'   => $request->jabatan,
            'gaji'      => $request->gaji,
            'alamat'    => $request->alamat,
            'kontak'    => $request->kontak,
        ]);

        return redirect()->back()->with('success','Data berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'jabatan' => 'required',
            'gaji' => 'required|numeric',
            'alamat' => 'required',
            'kontak' => 'required',
        ]);

        $k = Karyawan::findOrFail($id);

        $k->update([
            'nama'      => $request->nama,
            'jabatan'   => $request->jabatan,
            'gaji'      => $request->gaji,
            'alamat'    => $request->alamat,
            'kontak'    => $request->kontak,
        ]);

        return redirect()->back()->with('success','Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $k = Karyawan::findOrFail($id);
        $k->delete();

        return redirect()->back()->with('success','Data berhasil dihapus');
    }
}
