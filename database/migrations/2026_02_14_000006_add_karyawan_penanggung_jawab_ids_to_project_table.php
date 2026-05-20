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
        if (!Schema::hasTable('project') || Schema::hasColumn('project', 'karyawan_penanggung_jawab_ids')) {
            return;
        }

        if (Schema::hasColumn('project', 'karyawan_penanggung_jawab_id')) {
            Schema::table('project', function (Blueprint $table) {
                $table->json('karyawan_penanggung_jawab_ids')
                    ->nullable()
                    ->after('karyawan_penanggung_jawab_id');
            });

            return;
        }

        Schema::table('project', function (Blueprint $table) {
            $table->json('karyawan_penanggung_jawab_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('project') && Schema::hasColumn('project', 'karyawan_penanggung_jawab_ids')) {
            Schema::table('project', function (Blueprint $table) {
                $table->dropColumn('karyawan_penanggung_jawab_ids');
            });
        }
    }
};
