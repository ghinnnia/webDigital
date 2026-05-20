<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\SuratKerjasama;
use Illuminate\Http\Request;

class SuratKerjasamaController extends Controller
{
    public function index()
    {
        $surat = SuratKerjasama::latest()->get();
        return view('admin.surat_kerjasama.index', compact('surat'));
    }

    public function create()
    {
        return view('admin.surat_kerjasama.create');
    }

    public function store(Request $request)
    {
        SuratKerjasama::create($request->all());
        return redirect()->route('admin.surat_kerjasama.index')
            ->with('success', 'Surat berhasil ditambahkan');
    }

    public function edit($id)
    {
        $surat = SuratKerjasama::findOrFail($id);
        return view('admin.surat_kerjasama.edit', compact('surat'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratKerjasama::findOrFail($id);

        $data = $request->all();

        if (empty($request->tanda_tangan)) {
            $data['tanda_tangan'] = $surat->tanda_tangan;
        }

        $surat->update($data);

        return redirect()
            ->route('admin.surat_kerjasama.show', $id)
            ->with('success', 'Surat berhasil diperbarui');
    }

    public function show($id)
    {
        $surat = SuratKerjasama::findOrFail($id);
        return view('admin.surat_kerjasama.show', compact('surat'));
    }

public function cetak($id)
{
    $surat = SuratKerjasama::findOrFail($id);

    // 1. Generate PDF
    $pdf = Pdf::loadView(
        'admin.surat_kerjasama.cetak',
        compact('surat')
    );

    // (Opsional) simpan PDF ke storage
    $pdfPath = 'pdf/surat-' . $surat->id . '.pdf';
    Storage::put('public/' . $pdfPath, $pdf->output());

    // 2. Tampilkan PDF ke browser
    return $pdf->stream('surat-' . $surat->id . '.pdf');
}




    public function destroy($id)
    {
        SuratKerjasama::findOrFail($id)->delete();
        return back()->with('success', 'Surat berhasil dihapus');
    }
}
