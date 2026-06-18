<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lembur; // Sesuaikan dengan nama Model Lembur Anda
use App\Models\PerintahLembur; // Sesuaikan jika Anda memisahkan model perintah lembur
use Auth;
use Carbon\Carbon;

class LemburController extends Controller
{
    /**
     * Menampilkan halaman utama lembur karyawan (2 Tab)
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil data Pengajuan Lembur Mandiri milik karyawan
        // Disesuaikan string huruf kecil 'pengajuan' berdasarkan hasil Tinker database
        $lemburs = Lembur::where('user_id', $userId)
            ->where('type', 'pengajuan') 
            ->orderBy('tanggal_lembur', 'desc')
            ->paginate(10);

        // 2. Ambil data Perintah Lembur dari Manager untuk karyawan ini
        // Memperbaiki kolom 'tipe' menjadi 'type' dan string menjadi 'perintah' agar sinkron
        $perintahLemburs = Lembur::where('user_id', $userId)
            ->where('type', 'perintah') 
            ->orderBy('tanggal_lembur', 'desc')
            ->get();

        return view('karyawan.lembur.index', compact('lemburs', 'perintahLemburs'));
    }

    /**
     * Menampilkan form buat pengajuan lembur mandiri
     */
    public function create()
    {
        return view('karyawan.lembur.create');
    }

    /**
     * Menyimpan data pengajuan lembur mandiri ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_lembur' => 'required|date',
            'jam_mulai'      => 'required',
            'jam_selesai'    => 'required',
            'keterangan'     => 'required|string|max:255',
        ]);

        // Hitung durasi lembur (jam)
        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = Carbon::parse($request->jam_selesai);
        $durasi = $mulai->diffInHours($selesai);

        // Simpan data ke database dengan format type huruf kecil 'pengajuan'
        Lembur::create([
            'user_id'         => Auth::id(),
            'tanggal_lembur'  => $request->tanggal_lembur,
            'jam_mulai'       => $request->jam_mulai,
            'jam_selesai'     => $request->jam_selesai,
            'durasi'          => $durasi,
            'keterangan'      => $request->keterangan,
            'status'          => 'pending',
            'type'            => 'pengajuan' 
        ]);

        return redirect()->route('karyawan.lembur.index')->with('success', 'Pengajuan lembur mandiri berhasil dikirim!');
    }

    /**
     * Aksi untuk karyawan melaporkan bahwa perintah lembur dari manager telah selesai
     */
    public function terimaPerintah(Request $request, $id)
    {
        // Cari data perintah lembur berdasarkan ID
        if (class_exists('App\Models\PerintahLembur')) {
            $perintah = PerintahLembur::findOrFail($id);
            $perintah->update(['status' => 'completed']);
        } else {
            $perintah = Lembur::findOrFail($id);
            $perintah->update(['status' => 'approved']); // atau disesuaikan dengan flow sistem Anda
        }

        return redirect()->route('karyawan.lembur.index')->with('success', 'Status perintah lembur berhasil diperbarui menjadi selesai!');
    }
}