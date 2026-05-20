<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');          // 1–12
            $table->unsignedSmallInteger('tahun');         // e.g. 2026
            $table->date('tanggal_mulai');                 // awal periode hitung (biasanya tgl 1)
            $table->date('tanggal_selesai');               // akhir periode hitung (biasanya tgl 30/31)
            $table->date('tanggal_pembayaran')->nullable(); // target tgl 25
            $table->enum('status', ['draft', 'processed', 'approved', 'paid'])
                  ->default('draft');
            // draft      = belum dihitung
            // processed  = sudah dihitung, menunggu approval
            // approved   = sudah di-approve, siap bayar
            // paid       = sudah dibayar, notifikasi terkirim
            $table->unsignedBigInteger('dibuat_oleh')->nullable();   // user_id finance
            $table->unsignedBigInteger('disetujui_oleh')->nullable(); // user_id owner/GM
            $table->timestamp('disetujui_at')->nullable();
            $table->timestamp('dibayar_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['bulan', 'tahun']); // 1 periode per bulan
            $table->foreign('dibuat_oleh')->references('id')->on('users')->onDelete('set null');
            $table->foreign('disetujui_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
