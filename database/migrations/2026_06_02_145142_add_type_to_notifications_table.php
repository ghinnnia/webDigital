<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('notifications', function (Blueprint $table) {
        if (!Schema::hasColumn('notifications', 'type')) {
            $table->string('type')->default('task')->after('message');
        }
        if (!Schema::hasColumn('notifications', 'link')) {
            $table->string('link')->nullable()->after('type');
        }
    });
}
};