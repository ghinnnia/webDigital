<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan foreign key project_id ke tasks table SETELAH project table dibuat
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Jika belum ada constraint, tambahkan sekarang
            if (!Schema::hasColumn('tasks', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('parent_task_id');
            }
            
            // Tambahkan foreign key constraint
            $table->foreign('project_id')
                ->references('id')
                ->on('project')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['project_id']);
        });
    }
};
