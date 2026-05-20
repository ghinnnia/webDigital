<?php
// database/migrations/2026_05_06_120200_add_tunjangan_columns_to_karyawan_fixed.php

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
                $table->json('tunjangan_tetap_ids')->nullable()->after('gaji');
            });
        }
        
        // Cek dan tambah kolom tunjangan_tidak_tetap_ids
        if (!Schema::hasColumn('karyawan', 'tunjangan_tidak_tetap_ids')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->json('tunjangan_tidak_tetap_ids')->nullable()->after('tunjangan_tetap_ids');
            });
        }
        
        // Cek dan tambah kolom divisi_id jika belum ada
        if (!Schema::hasColumn('karyawan', 'divisi_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->foreignId('divisi_id')->nullable()->after('divisi')->constrained('divisi')->onDelete('set null');
            });
        }
        
        // Cek dan tambah kolom tim_id jika belum ada
        if (!Schema::hasColumn('karyawan', 'tim_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->foreignId('tim_id')->nullable()->after('divisi_id')->constrained('tims')->onDelete('set null');
            });
        }
    }

    public function down()
    {
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