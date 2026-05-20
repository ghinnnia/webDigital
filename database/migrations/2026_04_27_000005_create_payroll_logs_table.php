<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit log: mencatat setiap aksi penting di sistem penggajian.
     * Tidak bisa dihapus (append-only).
     */
    public function up(): void
    {
        Schema::create('payroll_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')
                  ->constrained('payroll_periods')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('user_id');          // siapa yang melakukan aksi
            $table->enum('aksi', [
                'created',      // periode dibuat
                'processed',    // gaji dihitung
                'approved',     // disetujui owner/GM
                'rejected',     // ditolak, dikembalikan ke draft
                'paid',         // ditandai sudah dibayar
                'slip_printed', // slip dicetak
                'exported',     // data di-export ke Excel
            ]);
            $table->text('keterangan')->nullable();          // detail tambahan
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Tidak ada updated_at — log tidak diubah
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_logs');
    }
};
