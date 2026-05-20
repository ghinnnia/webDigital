<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tim', function (Blueprint $table) {
            // Change string column to integer with default 0
            $table->integer('jumlah_anggota')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tim', function (Blueprint $table) {
            // Revert back to string
            $table->string('jumlah_anggota')->change();
        });
    }
};
