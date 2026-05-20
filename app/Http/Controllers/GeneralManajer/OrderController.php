<?php

namespace App\Http\Controllers\GeneralManajer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orderan; // Pastikan Anda mengimpor model Orderan

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mengambil data orderan dengan pagination (10 item per halaman)
        $orderan = Orderan::paginate(10);
        
        // Mengirim variabel $orderan ke view
        return view('general_manajer.kelola_order', compact('orderan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function store(Request $request)
{
    // Validasi data
    $request->validate([
        'nama' => 'required|string|max:255',
        'deskripsi' => 'required|string',
        'harga' => 'required|string',
        'deadline' => 'required|date',
        'progres' => 'required|integer|min:0|max:100',
        'status' => 'required|string|in:In Progress,Active,Completed,Cancelled',
    ]);

    // Membuat orderan baru
    Orderan::create($request->all());

    // Mengembalikan response JSON untuk AJAX
    return response()->json([
        'success' => true,
        'message' => 'Orderan berhasil ditambahkan!'
    ]);
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi data
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|string',
            'deadline' => 'required|date',
            'progres' => 'required|integer|min:0|max:100',
            'status' => 'required|string|in:In Progress,Active,Completed,Cancelled',
        ]);

        // Mencari dan mengupdate orderan
        $orderan = Orderan::findOrFail($id);
        $orderan->update($request->all());

        // Mengembalikan response JSON untuk AJAX
        return response()->json([
            'success' => true,
            'message' => 'Orderan berhasil diperbarui!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Mencari dan menghapus orderan
        $orderan = Orderan::findOrFail($id);
        $orderan->delete();

        // Mengembalikan response JSON untuk AJAX
        return response()->json([
            'success' => true,
            'message' => 'Orderan berhasil dihapus!'
        ]);
    }
}