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
        if (Schema::hasTable('project') && !Schema::hasColumn('project', 'penanggung_jawab_ids')) {
            Schema::table('project', function (Blueprint $table) {
                $table->json('penanggung_jawab_ids')->nullable()->after('penanggung_jawab_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('project') && Schema::hasColumn('project', 'penanggung_jawab_ids')) {
            Schema::table('project', function (Blueprint $table) {
                $table->dropColumn('penanggung_jawab_ids');
            });
        }
    }
};

