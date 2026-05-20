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
        Schema::table('karyawan', function (Blueprint $table) {
            // Add tim_id as nullable foreign key
            $table->unsignedBigInteger('tim_id')->nullable()->after('user_id');
            $table->foreign('tim_id')->references('id')->on('tim')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropForeign(['tim_id']);
            $table->dropColumn('tim_id');
        });
    }
};
