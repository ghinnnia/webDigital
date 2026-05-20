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
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan', 255); // Menambah batas panjang (Best Practice)
            $table->string('klien', 255);            // Menambah batas panjang
            $table->string('kontak', 255);           // Menambah batas panjang
            $table->text('alamat');                   // Text panjang (bisa > 255 karakter)
            
            // PERUBAHAN PENTING:
            // Menggunakan bigInteger untuk menyimpan ANGKA MURNI.
            // Ini TIDAK akan menyimpan format "Rp 100.000".
            // Hanya menyimpan angka: 0, 1, 100, 5000, dst.
            $table->bigInteger('jumlah_kerjasama')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaans');
    }
};