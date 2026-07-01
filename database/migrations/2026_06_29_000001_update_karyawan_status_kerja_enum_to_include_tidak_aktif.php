<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('karyawan')) {
            DB::statement("ALTER TABLE karyawan MODIFY status_kerja ENUM('aktif', 'resign', 'phk', 'tidak_aktif') NOT NULL DEFAULT 'aktif'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('karyawan')) {
            DB::statement("ALTER TABLE karyawan MODIFY status_kerja ENUM('aktif', 'resign', 'phk') NOT NULL DEFAULT 'aktif'");
        }
    }
};
