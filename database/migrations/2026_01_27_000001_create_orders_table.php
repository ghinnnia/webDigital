<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no')->nullable()->index();
            $table->string('layanan');
            $table->string('kategori')->nullable()->index();
            $table->unsignedBigInteger('price')->default(0)->comment('price in Rupiah');
            $table->string('price_formatted')->nullable();
            $table->string('klien')->nullable();
            $table->unsignedBigInteger('deposit')->nullable()->default(0);
            $table->unsignedBigInteger('paid')->nullable()->default(0);
            $table->enum('status', ['paid','partial','pending','overdue'])->default('pending');
            $table->enum('work_status', ['planning','progress','review','completed','onhold'])->default('planning');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });
        Schema::dropIfExists('orders');
    }
};
