<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the mapping logic
$u = \App\Models\User::with(['divisi', 'karyawan.tim', 'karyawan.tunjanganMaster'])
    ->where('role', 'karyawan')
    ->whereHas('karyawan')
    ->first();

if ($u && $u->karyawan) {
    $k = $u->karyawan;
    
    echo "User: " . $u->name . "\n";
    echo "Karyawan ID: " . $k->id . "\n";
    echo "Karyawan tunjanganMaster count: " . (optional($k)->tunjanganMaster ? optional($k)->tunjanganMaster->count() : 0) . "\n";
    
    // Simulate controller mapping
    $tunjangan_tetap_ids = $k && optional($k)->tunjanganMaster 
        ? collect(optional($k)->tunjanganMaster)->where('tipe', 'bulanan')->pluck('id')->toArray() 
        : [];
    
    $tunjangan_tidak_tetap_ids = $k && optional($k)->tunjanganMaster 
        ? collect(optional($k)->tunjanganMaster)->whereIn('tipe', ['bonus', 'insentif'])->pluck('id')->toArray() 
        : [];
    
    $tunjangan_tetap_list = $k && optional($k)->tunjanganMaster 
        ? collect(optional($k)->tunjanganMaster)->where('tipe', 'bulanan') 
        : collect();
    
    $tunjangan_tidak_tetap_list = $k && optional($k)->tunjanganMaster 
        ? collect(optional($k)->tunjanganMaster)->whereIn('tipe', ['bonus', 'insentif']) 
        : collect();
    
    echo "\nMapped tunjangan_tetap_ids: " . json_encode($tunjangan_tetap_ids) . "\n";
    echo "Mapped tunjangan_tidak_tetap_ids: " . json_encode($tunjangan_tidak_tetap_ids) . "\n";
    echo "Mapped tunjangan_tetap_list count: " . $tunjangan_tetap_list->count() . "\n";
    echo "Mapped tunjangan_tidak_tetap_list count: " . $tunjangan_tidak_tetap_list->count() . "\n";
    
    // Simulate view rendering
    if ($tunjangan_tetap_list) {
        $tetapNames = $tunjangan_tetap_list->pluck('nama')->toArray();
        echo "Tetap names: " . implode(', ', $tetapNames) . "\n";
    }
    
    if ($tunjangan_tidak_tetap_list) {
        $tidakTetapNames = $tunjangan_tidak_tetap_list->pluck('nama')->toArray();
        echo "Tidak tetap names: " . implode(', ', $tidakTetapNames) . "\n";
    }
} else {
    echo "User atau Karyawan tidak ditemukan\n";
}
