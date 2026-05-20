<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Increase precision of tax to support larger monetary values
            if (Schema::hasColumn('orders', 'tax')) {
                $table->decimal('tax', 15, 2)->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'tax')) {
                $table->decimal('tax', 5, 2)->nullable()->change();
            }
        });
    }
};