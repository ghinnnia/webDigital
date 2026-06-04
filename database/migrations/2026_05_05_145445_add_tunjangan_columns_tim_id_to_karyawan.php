<?php
// database/migrations/xxxx_xx_xx_add_tim_id_to_karyawan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('karyawan', 'tim_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->foreignId('tim_id')->nullable()->after('divisi_id')->constrained('tim')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('karyawan', 'tim_id')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropForeign(['tim_id']);
                $table->dropColumn('tim_id');
            });
        }
    }
};