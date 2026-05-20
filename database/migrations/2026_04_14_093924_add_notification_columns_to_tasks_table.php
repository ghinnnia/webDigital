<?php
// database/migrations/2024_01_15_000003_add_notification_columns_to_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'is_reminded')) {
                $table->boolean('is_reminded')->default(false);
            }
            if (!Schema::hasColumn('tasks', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'is_overdue_notified')) {
                $table->boolean('is_overdue_notified')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['is_reminded', 'reminder_sent_at', 'is_overdue_notified']);
        });
    }
};