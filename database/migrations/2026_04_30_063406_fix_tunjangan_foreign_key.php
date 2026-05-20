<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus foreign key yang lama
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->dropForeign(['tunjangan_id']);
        });

        // Tambah foreign key baru ke tunjangan_master
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->foreign('tunjangan_id')
                  ->references('id')
                  ->on('tunjangan_master')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->dropForeign(['tunjangan_id']);
            $table->foreign('tunjangan_id')
                  ->references('id')
                  ->on('tunjangan')
                  ->onDelete('cascade');
        });
    }
};