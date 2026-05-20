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
        Schema::create('cashflows', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->date('tanggal_transaksi');
            $table->string('nama_transaksi');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->enum('tipe_transaksi', ['pemasukan', 'pengeluaran']);

            $table->foreignId('kategori_id')->constrained(
                table: 'kategori_cashflow',
                indexName: 'cashflows_kategori_id_foreign'
            )->onDelete('restrict');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflows');
    }
};
