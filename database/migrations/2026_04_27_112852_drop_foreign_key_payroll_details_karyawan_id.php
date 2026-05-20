<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            // Cek dulu apakah foreign key ada
            $table->dropForeign(['karyawan_id']);
        });
    }

    public function down()
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
        });
    }
};