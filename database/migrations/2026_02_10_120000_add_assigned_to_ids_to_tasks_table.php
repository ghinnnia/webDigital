<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'assigned_to_ids')) {
            Schema::table('tasks', function (Blueprint $table) {
                // Use JSON column so we can query with whereJsonContains
                $table->json('assigned_to_ids')->nullable()->after('assigned_to');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tasks', 'assigned_to_ids')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('assigned_to_ids');
            });
        }
    }
};
