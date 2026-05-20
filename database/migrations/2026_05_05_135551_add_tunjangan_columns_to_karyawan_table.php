<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cek dan tambah kolom tunjangan_tetap_ids
        if (!Schema::hasColumn('karyawan', 'tunjangan_tetap_ids')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->json('tunjangan_tetap_ids')->nullable();
            });
        }
        
        // Cek dan tambah kolom tunjangan_tidak_tetap_ids
        if (!Schema::hasColumn('karyawan', 'tunjangan_tidak_tetap_ids')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->json('tunjangan_tidak_tetap_ids')->nullable();
            });
        }
        
        // Cek dan tambah kolom divisi_id
        if (!Schema::hasColumn('karyawan', 'divisi_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->unsignedBigInteger('divisi_id')->nullable();
                $table->foreign('divisi_id')->references('id')->on('divisis')->onDelete('set null');
            });
        }
        
        // Cek dan tambah kolom tim_id
        if (!Schema::hasColumn('karyawan', 'tim_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->unsignedBigInteger('tim_id')->nullable();
                $table->foreign('tim_id')->references('id')->on('tims')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        // Hapus foreign key dulu
        if (Schema::hasColumn('karyawan', 'divisi_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropForeign(['divisi_id']);
                $table->dropColumn('divisi_id');
            });
        }
        
        if (Schema::hasColumn('karyawan', 'tim_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropForeign(['tim_id']);
                $table->dropColumn('tim_id');
            });
        }
        
        if (Schema::hasColumn('karyawan', 'tunjangan_tetap_ids')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropColumn(['tunjangan_tetap_ids', 'tunjangan_tidak_tetap_ids']);
            });
        }
    }
};