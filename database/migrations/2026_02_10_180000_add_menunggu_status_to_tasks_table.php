<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'menunggu' status to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            // Drop the old enum and create new one with 'menunggu'
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'proses', 'selesai', 'dibatalkan', 'menunggu') DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Restore old enum without 'menunggu'
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'proses', 'selesai', 'dibatalkan') DEFAULT 'pending'");
        });
    }
};
