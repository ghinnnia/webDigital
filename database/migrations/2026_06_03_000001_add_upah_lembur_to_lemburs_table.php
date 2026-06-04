<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->decimal('upah_per_jam', 15, 2)->nullable()->after('durasi');
            $table->decimal('total_upah', 15, 2)->nullable()->after('upah_per_jam');
        });
    }

    public function down()
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropColumn(['upah_per_jam', 'total_upah']);
        });
    }
};
