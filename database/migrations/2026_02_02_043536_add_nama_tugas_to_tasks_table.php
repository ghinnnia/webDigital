<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // File migration baru
public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->string('nama_tugas')->nullable()->after('judul');
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn('nama_tugas');
    });
}

    /**
     * Reverse the migrations.
     */
   
};
