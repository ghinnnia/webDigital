<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('karyawan_tunjangan')) {
            Schema::create('karyawan_tunjangan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
                $table->foreignId('tunjangan_id')->constrained('tunjangan_master')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('karyawan_tunjangan');
    }
};
