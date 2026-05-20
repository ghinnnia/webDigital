<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tunjangan_karyawan')) {
            Schema::create('tunjangan_karyawan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('tunjangan_id')->constrained('tunjangan_master')->onDelete('cascade');
                $table->integer('bulan');
                $table->integer('tahun');
                $table->decimal('nominal', 15, 2)->default(0);
                $table->text('catatan')->nullable();
                $table->boolean('diberikan')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tunjangan_karyawan');
    }
};