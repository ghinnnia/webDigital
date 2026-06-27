<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TunjanganMaster;
use App\Models\TunjanganKaryawan;
use App\Models\KinerjaPegawai;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TunjanganHrController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $divisiId = $request->get('divisi_id');
        $search = $request->get('search');

        // 🔥 AMBIL KARYAWAN DENGAN RELASI DIVISI
        $queryKaryawan = User::where('role', 'karyawan')
            ->with('divisi');  // Pastikan relasi divisi di-load

        if ($divisiId && $divisiId != '') {
            $queryKaryawan->where('divisi_id', $divisiId);
        }

        if ($search && $search != '') {
            $queryKaryawan->where('name', 'like', '%' . $search . '%');
        }

        $karyawan = $queryKaryawan->orderBy('name')->get();

        // 🔥 AMBIL MASTER TUNJANGAN (tetap ada untuk tambah jenis)
        $tunjanganMaster = TunjanganMaster::orderBy('tipe')->orderBy('nama')->get();

        // 🔥 AMBIL TUNJANGAN YANG SUDAH DIBERIKAN
        $tunjanganDiberikan = TunjanganKaryawan::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->groupBy('karyawan_id');

        // 🔥 AMBIL GRADE KPA (A,B,C,D)
        $kpaData = KinerjaPegawai::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('karyawan_id');

        $divisiList = Divisi::orderBy('divisi')->get();

        return view('hr.tunjangan.index', compact(
            'karyawan', 'tunjanganMaster', 'tunjanganDiberikan', 'kpaData',
            'bulan', 'tahun', 'divisiList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tunjangan' => 'required|array',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        try {
            DB::beginTransaction();

            TunjanganKaryawan::where('bulan', $request->bulan)
                ->where('tahun', $request->tahun)
                ->delete();

            $validIds = TunjanganMaster::pluck('id')->toArray();

            foreach ($request->tunjangan as $karyawanId => $tunjangans) {
                foreach ($tunjangans as $tunjanganId => $nominal) {
                    if (in_array($tunjanganId, $validIds) && !empty($nominal) && $nominal > 0) {
                        TunjanganKaryawan::create([
                            'karyawan_id' => $karyawanId,
                            'tunjangan_id' => $tunjanganId,
                            'bulan' => $request->bulan,
                            'tahun' => $request->tahun,
                            'nominal' => $nominal,
                            'diberikan' => 1,
                        ]);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Tunjangan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function addTunjangan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:bulanan,bonus,insentif',
            'nominal' => 'required|numeric|min:0'
        ]);

        TunjanganMaster::create([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'nominal' => $request->nominal,
            // is_active tidak ada di tabel tunjangan_master
        ]);

        return redirect()->back()->with('success', 'Jenis tunjangan baru berhasil ditambahkan!');
    }

    public function updateTunjangan(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:bulanan,bonus,insentif',
            'nominal' => 'required|numeric|min:0'
        ]);

        $tunjangan = TunjanganMaster::findOrFail($id);
        $tunjangan->update([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'nominal' => $request->nominal,
        ]);

        return redirect()->back()->with('success', 'Jenis tunjangan berhasil diupdate!');
    }

    public function destroyTunjangan($id)
    {
        $tunjangan = TunjanganMaster::findOrFail($id);
        TunjanganKaryawan::where('tunjangan_id', $id)->delete();
        $tunjangan->delete();

        return redirect()->back()->with('success', 'Jenis tunjangan berhasil dihapus!');
    }
}