<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('perusahaans') && Schema::hasColumn('perusahaans', 'status')) {
            DB::statement("ALTER TABLE `perusahaans` MODIFY `status` ENUM('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('perusahaans') && Schema::hasColumn('perusahaans', 'status')) {
            DB::statement("ALTER TABLE `perusahaans` MODIFY `status` ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif'");
        }
    }
};

