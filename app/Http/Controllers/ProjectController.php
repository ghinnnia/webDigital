<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index() { return redirect('/'); }
    public function create() { return redirect('/'); }
    public function store(Request $request) { return redirect('/'); }
    public function show($id) { return redirect('/'); }
    public function edit($id) { return redirect('/'); }
    public function update(Request $request, $id) { return redirect('/'); }
    public function destroy($id) { return redirect('/'); }
}