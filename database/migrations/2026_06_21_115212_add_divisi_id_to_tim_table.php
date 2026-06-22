<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tim', function (Blueprint $table) {
            // Tambah kolom divisi_id jika belum ada
            if (!Schema::hasColumn('tim', 'divisi_id')) {
                $table->foreignId('divisi_id')->nullable()->after('divisi')->constrained('divisi')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tim', function (Blueprint $table) {
            if (Schema::hasColumn('tim', 'divisi_id')) {
                $table->dropForeign(['divisi_id']);
                $table->dropColumn('divisi_id');
            }
        });
    }
};