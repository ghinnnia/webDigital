<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KaryawanProfileController extends Controller
{
    public function index()
    {
        $karyawan = Auth::user()?->load(['divisi', 'karyawan.tim']);

        if (!$karyawan) {
            return redirect('/login');
        }

        return view('karyawan.profile', compact('karyawan'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // Mode upload foto saja
        if ($request->hasFile('foto') && !$request->filled('name') && !$request->filled('email')) {
            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if (!empty($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto')->store('users', 'public');
            $user->update(['foto' => $path]);

            return back()->with('success', 'Foto profil berhasil diperbarui');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'kontak' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if (!empty($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $validated['foto'] = $request->file('foto')->store('users', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
