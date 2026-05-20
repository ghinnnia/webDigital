<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('absensis')) return;

        Schema::table('absensis', function (Blueprint $table) {
            if (!Schema::hasColumn('absensis', 'late_minutes')) {
                $table->integer('late_minutes')->nullable()->after('jam_masuk');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('absensis')) return;

        Schema::table('absensis', function (Blueprint $table) {
            if (Schema::hasColumn('absensis', 'late_minutes')) {
                $table->dropColumn('late_minutes');
            }
        });
    }
};
