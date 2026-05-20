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
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id(); // Menggantikan 'no', auto increment primary key
            
            // Tanggal Transaksi
            $table->date('tanggal');
            
            // Nama Transaksi
            $table->string('nama');
            
            // Kategori (sesuai data JS: salary, project, investment, office, marketing, utilities)
            // Kita gunakan string agar fleksibel, bisa juga diubah ke enum jika ingin ketat
            $table->string('kategori');
            
            // Deskripsi
            $table->text('deskripsi')->nullable();
            
            // Jumlah (Decimal(15,2) untuk menyimpan angka mata uang dengan presisi)
            // Di frontend 'Rp 15.000.000', di database disimpan sebagai 15000000.00
            $table->decimal('jumlah', 15, 2);
            
            // Tipe Transaksi (Pemasukan/Pengeluaran)
            $table->enum('tipe', ['income', 'expense']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};