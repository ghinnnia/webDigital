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
        if (!Schema::hasTable('tugas_approval_history')) {
            Schema::create('tugas_approval_history', function (Blueprint $table) {
                $table->id();
                
                // Reference to tugas_karyawan_to_manager
                $table->unsignedBigInteger('tugas_id');
                
                // Manager divisi who approves
                $table->unsignedBigInteger('approved_by');
                
                // Action: approved, rejected, returned
                $table->enum('action', ['approved', 'rejected', 'returned'])->default('approved');
                
                // Optional notes
                $table->text('notes')->nullable();
                
                // Timestamps
                $table->timestamps();
                
                // Foreign keys
                $table->foreign('tugas_id')
                    ->references('id')
                    ->on('tugas_karyawan_to_manager')
                    ->onDelete('cascade');
                
                $table->foreign('approved_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                
                // Indexes
                $table->index('tugas_id');
                $table->index('approved_by');
                $table->index('action');
                $table->index(['tugas_id', 'action']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_approval_history');
    }
};
