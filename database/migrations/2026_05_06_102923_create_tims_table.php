<?php
// database/migrations/2025_01_06_000001_create_tims_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tims')) {
            Schema::create('tims', function (Blueprint $table) {
                $table->id();
                $table->string('tim');
                $table->foreignId('divisi_id')->nullable()->constrained('divisis')->onDelete('set null');
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tims');
    }
};