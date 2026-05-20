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
        Schema::table('project', function (Blueprint $table) {
            $table->foreignId('karyawan_penanggung_jawab_id')
                ->nullable()
                ->after('penanggung_jawab_id')
                ->constrained('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropForeign(['karyawan_penanggung_jawab_id']);
            $table->dropColumn('karyawan_penanggung_jawab_id');
        });
    }
};
