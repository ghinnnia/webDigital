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
        Schema::create('pengelola_tugas', function (Blueprint $table) {
            $table->id();
            
            // Manager yang membuat tugas
            $table->unsignedBigInteger('manager_id');
            
            // Karyawan yang ditugasi (nullable untuk broadcast ke divisi)
            $table->unsignedBigInteger('karyawan_id')->nullable();
            
            // Divisi target (jika broadcast ke seluruh divisi)
            $table->unsignedBigInteger('divisi_id')->nullable();
            
            // Detail Tugas
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'active', 'proses', 'completed', 'dibatalkan'])->default('draft');
            
            // Lampiran file
            $table->string('file_lampiran')->nullable();
            
            // Catatan
            $table->text('catatan_manager')->nullable();
            $table->text('catatan_karyawan')->nullable();
            
            // Progress
            $table->integer('progress_percentage')->default(0);
            
            // Tanggal submit dan selesai
            $table->timestamp('tanggal_mulai')->nullable();
            $table->timestamp('tanggal_submit')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('karyawan_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('divisi_id')->references('id')->on('divisi')->onDelete('set null');
            
            // Indexes
            $table->index('manager_id');
            $table->index('karyawan_id');
            $table->index('divisi_id');
            $table->index('status');
            $table->index('deadline');
            $table->index(['manager_id', 'status']);
            $table->index(['karyawan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengelola_tugas');
    }
};
