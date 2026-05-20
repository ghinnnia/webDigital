<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check tunjangan IDs
$tunjangan = \App\Models\TunjanganMaster::whereIn('id', [8, 2])->get();

echo "Tunjangan dengan ID 8 dan 2:\n";
foreach ($tunjangan as $t) {
    echo "  ID: " . $t->id . ", Nama: " . $t->nama . ", Tipe: " . $t->tipe . "\n";
}

// Check all tunjangan
echo "\nSemua Tunjangan:\n";
$all = \App\Models\TunjanganMaster::all();
foreach ($all as $t) {
    echo "  ID: " . $t->id . ", Nama: " . $t->nama . ", Tipe: " . $t->tipe . "\n";
}

// Check pivot table for maya
echo "\nPivot table untuk maya (karyawan_id=90):\n";
$pivot = \Illuminate\Support\Facades\DB::table('karyawan_tunjangan')->where('karyawan_id', 90)->get();
echo "Count: " . $pivot->count() . "\n";
foreach ($pivot as $p) {
    echo "  karyawan_id: " . $p->karyawan_id . ", tunjangan_id: " . $p->tunjangan_id . "\n";
}
