<?php

namespace App\Http\Controllers;

use App\Models\Pelayanan;
use Illuminate\Http\Request;

class PelayananController extends Controller
{
    /**
     * Display a listing of the resource with search functionality.
     * Menampilkan daftar layanan dengan fungsi pencarian.
     */
    public function index(Request $request)
    {
        // Ambil query pencarian dari input, default kosong
        $search = $request->input('search');

        // Mulai query untuk model Pelayanan
        $query = Pelayanan::query();

        // Jika ada query pencarian, filter data berdasarkan nama layanan
        if ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        // Ambil data dengan pagination (3 data per halaman)
        $pelayanan = $query->latest()->paginate(3);

        // Tampilkan view dan kirim data pelayanan serta query pencarian
        return view('general_manajer.data_layanan', compact('pelayanan', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan data layanan baru.
     */
    public function store(Request $request)
    {
        // Validasi input menggunakan fungsi bantuan
        $validated = $request->validate($this->validationRules());

        // Simpan data baru ke database
        Pelayanan::create($validated);

        // Redirect menggunakan fungsi bantuan
        return $this->redirectWithSuccess('Layanan berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui data layanan yang ada.
     */
    public function update(Request $request, $id)
    {
        // Cari data pelayanan berdasarkan ID
        $pelayanan = Pelayanan::findOrFail($id);

        // Validasi input menggunakan fungsi bantuan
        $validated = $request->validate($this->validationRules());

        // Update data
        $pelayanan->update($validated);

        // Redirect menggunakan fungsi bantuan
        return $this->redirectWithSuccess('Data layanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data layanan.
     */
    public function destroy($id)
    {
        // Cari data pelayanan berdasarkan ID
        $pelayanan = Pelayanan::findOrFail($id);
        
        // Hapus data
        $pelayanan->delete();

        // Redirect menggunakan fungsi bantuan
        return $this->redirectWithSuccess('Layanan berhasil dihapus!');
    }

    /**
     * Get the validation rules that apply to the request.
     * Fungsi bantuan untuk aturan validasi agar tidak diulang.
     *
     * @return array
     */
    private function validationRules()
    {
        return [
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'durasi' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'kategori' => 'required|in:Teknologi,Desain,Marketing,Konsultasi',
        ];
    }

    /**
     * Redirect to the index page with a success message.
     * Fungsi bantuan untuk redirect agar tidak diulang.
     *
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectWithSuccess($message)
    {
        return redirect('/layanan')->with('success', $message);
    }
}