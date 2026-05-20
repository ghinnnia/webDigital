<?php
// database/migrations/2025_01_01_000003_create_tunjangan_master_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tunjangan_master')) {
            Schema::create('tunjangan_master', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->enum('tipe', ['bulanan', 'bonus', 'insentif'])->default('bulanan');
                $table->decimal('nominal', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tunjangan_master');
    }
};