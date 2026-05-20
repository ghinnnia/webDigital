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
        Schema::create('kategori_cashflow', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori')->unique(); // Nama kategori, contoh: "Gaji", "Iklan Online"
            $table->enum('tipe_kategori', ['pemasukan', 'pengeluaran']); // Untuk memisahkan jenisnya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_cashflow');
    }
};
