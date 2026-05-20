<?php
// database/migrations/2026_05_06_120100_create_gaji_templates_table_fixed.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('gaji_templates')) {
            Schema::create('gaji_templates', function (Blueprint $table) {
                $table->id();
                $table->string('role');
                $table->foreignId('divisi_id')->nullable()->constrained('divisis')->onDelete('cascade');
                $table->decimal('gaji_pokok', 15, 2)->default(0);
                $table->decimal('tunjangan_tetap', 15, 2)->default(0);
                $table->decimal('tunjangan_kinerja', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
                
                $table->unique(['role', 'divisi_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('gaji_templates');
    }
};