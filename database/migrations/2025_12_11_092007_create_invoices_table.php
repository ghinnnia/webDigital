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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('invoice_date');

            $table->string('company_name');
            $table->text('company_address');
            $table->string('kontak')->nullable(); // TAMBAHKAN FIELD KONTAK DI SINI

            $table->string('client_name');
            $table->string('order_number')->nullable();

            $table->string('nama_layanan')->nullable();
            $table->enum('status_pembayaran', ['down payment', 'lunas'])->default('down payment');

            $table->string('payment_method');
            $table->text('description')->nullable();

            $table->integer('subtotal')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('total')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('company_name');
            $table->index('client_name');
            $table->index('invoice_date');
            $table->index('nama_layanan');
            $table->index('status_pembayaran');
            $table->index('kontak'); // Tambah index untuk kontak
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};