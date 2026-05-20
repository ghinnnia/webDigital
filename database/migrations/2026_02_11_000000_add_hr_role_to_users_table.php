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
        // Add 'hr' to the role enum
        // Untuk MySQL, kita perlu mengubah kolom dengan cara modify
        if (Schema::hasColumn('users', 'role')) {
            // Drop constraint jika ada (untuk foreign keys)
            // Kemudian ubah enum
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['owner', 'admin', 'general_manager', 'manager_divisi', 'finance', 'karyawan', 'hr'])
                    ->default('karyawan')
                    ->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'hr' from role enum (rollback)
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'admin', 'general_manager', 'manager_divisi', 'finance', 'karyawan'])
                ->default('karyawan')
                ->change();
        });
    }
};
