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
        Schema::table('project', function (Blueprint $table) {
            // Make tanggal_mulai_pengerjaan nullable so projects created from invoices can leave dates empty
            if (Schema::hasColumn('project', 'tanggal_mulai_pengerjaan')) {
                $table->date('tanggal_mulai_pengerjaan')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            if (Schema::hasColumn('project', 'tanggal_mulai_pengerjaan')) {
                $table->date('tanggal_mulai_pengerjaan')->nullable(false)->change();
            }
        });
    }
};
