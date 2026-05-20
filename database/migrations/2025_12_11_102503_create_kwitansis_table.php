<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kwitansis', function (Blueprint $table) {
            $table->id();
            $table->string('kwitansi_no')->unique(); // ✅ TAMBAHKAN INI
            $table->string('nama_perusahaan');
            $table->date('tanggal');
            $table->string('nama_klien');
            $table->text('deskripsi');
            $table->decimal('harga', 15, 2);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('fee_maintenance', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['Pembayaran Awal', 'Lunas'])->default('Pembayaran Awal');
            $table->string('bank')->nullable(); // ✅ Ditambahkan
            $table->string('no_rekening')->nullable(); // ✅ Ditambahkan

            // Foreign key ke tabel invoices
            $table->foreignId('invoice_id')->nullable()->constrained('invoices');

            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kwitansis');
    }
};
