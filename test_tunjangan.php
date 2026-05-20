<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$k = \App\Models\Karyawan::with('tunjanganMaster')->latest()->first();
if ($k) {
    echo "Karyawan: " . $k->nama . "\n";
    echo "Tunjangan count: " . $k->tunjanganMaster->count() . "\n";
    foreach ($k->tunjanganMaster as $t) {
        echo "  - " . $t->nama . " (" . $t->tipe . ")\n";
    }
} else {
    echo "Tidak ada karyawan\n";
}
