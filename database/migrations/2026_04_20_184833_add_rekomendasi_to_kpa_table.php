<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    if (!Schema::hasTable('kpa')) {
        return; // skip jika tabel tidak ada
    }

    Schema::table('kpa', function (Blueprint $table) {
        if (!Schema::hasColumn('kpa', 'rekomendasi')) {
            $table->text('rekomendasi')->nullable()->after('grade');
        }
    });
}

public function down()
{
    if (!Schema::hasTable('kpa')) {
        return;
    }

    Schema::table('kpa', function (Blueprint $table) {
        if (Schema::hasColumn('kpa', 'rekomendasi')) {
            $table->dropColumn('rekomendasi');
        }
    });
}
};
