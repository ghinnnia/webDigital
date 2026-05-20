<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'hr' to the role enum in karyawan table
        Schema::table('karyawan', function (Blueprint $table) {
            $table->enum('role', ['owner', 'admin', 'general_manager', 'manager_divisi', 'finance', 'karyawan', 'hr'])
                ->default('karyawan')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'hr' from role enum (rollback)
        Schema::table('karyawan', function (Blueprint $table) {
            $table->enum('role', ['owner', 'admin', 'general_manager', 'manager_divisi', 'finance', 'karyawan'])
                ->default('karyawan')
                ->change();
        });
    }
};
