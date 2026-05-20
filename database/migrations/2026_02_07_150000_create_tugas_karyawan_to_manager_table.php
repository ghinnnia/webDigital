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
        Schema::create('tugas_karyawan_to_manager', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('manager_divisi_id');
            $table->unsignedBigInteger('project_id')->nullable();
            
            // Task details
            $table->string('judul');
            $table->string('nama_tugas')->nullable();
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline')->nullable();
            
            // Status dan catatan
            $table->enum('status', ['draft', 'submitted', 'in_review', 'approved', 'rejected', 'selesai'])->default('submitted');
            $table->text('catatan')->nullable();
            
            // File attachment
            $table->string('lampiran')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('karyawan_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('manager_divisi_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('project')->onDelete('set null');
            
            // Indexes
            $table->index('karyawan_id');
            $table->index('manager_divisi_id');
            $table->index('project_id');
            $table->index('status');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_karyawan_to_manager');
    }
};
