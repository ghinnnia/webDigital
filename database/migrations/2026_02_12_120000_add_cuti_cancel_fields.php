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
        Schema::table('cuti', function (Blueprint $table) {
            if (!Schema::hasColumn('cuti', 'dibatalkan_oleh')) {
                $table->unsignedBigInteger('dibatalkan_oleh')->nullable()->after('disetujui_pada');
            }
            if (!Schema::hasColumn('cuti', 'dibatalkan_pada')) {
                $table->timestamp('dibatalkan_pada')->nullable()->after('dibatalkan_oleh');
            }
            if (!Schema::hasColumn('cuti', 'catatan_pembatalan')) {
                $table->text('catatan_pembatalan')->nullable()->after('dibatalkan_pada');
            }
        });

        Schema::table('cuti', function (Blueprint $table) {
            if (Schema::hasColumn('cuti', 'dibatalkan_oleh')) {
                $table->foreign('dibatalkan_oleh')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            if (Schema::hasColumn('cuti', 'dibatalkan_oleh')) {
                $table->dropForeign(['dibatalkan_oleh']);
                $table->dropColumn('dibatalkan_oleh');
            }
            if (Schema::hasColumn('cuti', 'dibatalkan_pada')) {
                $table->dropColumn('dibatalkan_pada');
            }
            if (Schema::hasColumn('cuti', 'catatan_pembatalan')) {
                $table->dropColumn('catatan_pembatalan');
            }
        });
    }
};
