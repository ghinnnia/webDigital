<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('task_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('payroll_id')->nullable()->after('task_id');
            $table->string('link')->nullable()->after('message');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['task_id', 'payroll_id', 'link']);
        });
    }
};