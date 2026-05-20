<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check maya
$u = \App\Models\User::where('email', 'maya56@gmail.com')->with('karyawan.tunjanganMaster')->first();

if ($u) {
    echo "User: " . $u->name . "\n";
    if ($u->karyawan) {
        echo "Karyawan ID: " . $u->karyawan->id . "\n";
        echo "Tunjangan count: " . $u->karyawan->tunjanganMaster->count() . "\n";
        foreach ($u->karyawan->tunjanganMaster as $t) {
            echo "  - " . $t->nama . " (" . $t->tipe . ")\n";
        }
    } else {
        echo "No karyawan record\n";
    }
} else {
    echo "User maya tidak ditemukan\n";
}
