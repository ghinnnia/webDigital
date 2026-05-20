<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('tunjangan_karyawan', 'nominal')) {
                $table->decimal('nominal', 15, 2)->default(0)->after('tunjangan_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->dropColumn('nominal');
        });
    }
};