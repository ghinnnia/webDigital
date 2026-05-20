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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            
            // Early checkout tracking
            $table->boolean('is_early_checkout')->default(false);
            $table->text('early_checkout_reason')->nullable();

            // Reason fields for different status types
            $table->text('reason')->nullable(); // Alasan untuk izin/dinas
            $table->string('location', 255)->nullable(); // Lokasi untuk dinas luar
            $table->string('purpose', 255)->nullable(); // Tujuan untuk dinas luar
            
            // Approval fields (untuk izin, cuti, dinas luar)
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Additional fields for leave/absence tracking
            $table->date('tanggal_akhir')->nullable(); // End date for leave/absence
            $table->enum('jenis_ketidakhadiran', ['cuti', 'sakit', 'izin', 'dinas-luar', 'lainnya'])->nullable();
            $table->text('keterangan')->nullable(); // Keterangan detail
            
            $table->timestamps();
            $table->softDeletes();
            
            // Composite unique constraint: satu user hanya bisa absen sekali per hari
            $table->unique(['user_id', 'tanggal']);
            
            // Indexes for better performance
            $table->index(['user_id', 'tanggal']);
            $table->index('tanggal');
            $table->index('approval_status');
            $table->index('jenis_ketidakhadiran');
            $table->index(['tanggal', 'approval_status']);
            $table->index(['user_id', 'approval_status']);
            
            // Index tambahan untuk performa query kehadiran/ketidakhadiran
            $table->index(['tanggal', 'jenis_ketidakhadiran']);
            $table->index(['user_id', 'tanggal', 'jenis_ketidakhadiran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};