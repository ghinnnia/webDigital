<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_kerjasamas', function (Blueprint $table) {
            $table->id();

            $table->string('judul');
            $table->string('nomor_surat')->unique();

            $table->date('tanggal');

            $table->longText('para_pihak')->nullable();
            $table->longText('maksud_tujuan')->nullable();
            $table->longText('ruang_lingkup')->nullable();
            $table->longText('jangka_waktu')->nullable();
            $table->longText('biaya_pembayaran')->nullable();
            $table->longText('kerahasiaan')->nullable();
            $table->longText('penyelesaian_sengketa')->nullable();
            $table->longText('penutup')->nullable();

            // Tanda tangan base64 (canvas)
            $table->longText('tanda_tangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_kerjasamas');
    }
};
