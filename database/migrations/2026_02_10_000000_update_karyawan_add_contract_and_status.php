<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Add contract columns
            $table->date('kontrak_mulai')->nullable()->after('gaji');
            $table->date('kontrak_selesai')->nullable()->after('kontrak_mulai');
            $table->index('kontrak_selesai');
        });

        // Ensure status_kerja enum contains 'nonaktif'
        // This raw statement is DB-specific (MySQL/MariaDB). Adjust if using another DB.
        DB::statement("ALTER TABLE `karyawan` MODIFY `status_kerja` ENUM('aktif','resign','phk','nonaktif') NOT NULL DEFAULT 'aktif'");
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropIndex(['kontrak_selesai']);
            $table->dropColumn(['kontrak_mulai', 'kontrak_selesai']);
        });

        // Revert enum to previous set (best-effort)
        DB::statement("ALTER TABLE `karyawan` MODIFY `status_kerja` ENUM('aktif','resign','phk') NOT NULL DEFAULT 'aktif'");
    }
};
