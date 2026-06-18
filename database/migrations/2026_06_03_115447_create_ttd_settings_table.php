<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ttd_settings', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('nama_pejabat')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ttd_settings');
    }
};