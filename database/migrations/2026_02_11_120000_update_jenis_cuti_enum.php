<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum for jenis_cuti to the new values
        // Old values: 'tahunan', 'sakit', 'penting', 'melahirkan', 'lainnya'
        // New values: 'tahunan', 'melahirkan', 'duka', 'izin-khusus', 'tanpa-gaji'
        
        DB::statement("ALTER TABLE cuti MODIFY jenis_cuti ENUM('tahunan', 'melahirkan', 'duka', 'izin-khusus', 'tanpa-gaji') DEFAULT 'tanpa-gaji' COMMENT 'tahunan=Cuti Tahunan, melahirkan=Cuti Melahirkan, duka=Cuti Duka, izin-khusus=Cuti Izin Khusus, tanpa-gaji=Cuti Tanpa Gaji'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum values if needed
        DB::statement("ALTER TABLE cuti MODIFY jenis_cuti ENUM('tahunan', 'sakit', 'penting', 'melahirkan', 'lainnya') DEFAULT 'lainnya' COMMENT 'tahunan=Cuti tahunan, sakit=Cuti sakit, penting=Cuti penting/khusus, melahirkan=Cuti melahirkan, lainnya=Cuti lainnya'");
    }
};
