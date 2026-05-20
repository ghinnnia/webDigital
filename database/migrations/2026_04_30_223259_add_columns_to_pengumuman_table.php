<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            // Tambah kolom jika belum ada
            if (!Schema::hasColumn('pengumuman', 'target')) {
                $table->enum('target', ['semua', 'hr', 'manager', 'karyawan'])->default('semua')->after('user_id');
            }
            if (!Schema::hasColumn('pengumuman', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('target');
            }
            if (!Schema::hasColumn('pengumuman', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('pengumuman', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            }
        });
    }

    public function down()
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropColumn(['target', 'is_active', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }
};