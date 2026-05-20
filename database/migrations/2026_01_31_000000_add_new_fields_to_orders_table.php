<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('orders', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            if (!Schema::hasColumn('orders', 'order_date')) {
                $table->date('order_date')->nullable();
            }
            if (!Schema::hasColumn('orders', 'invoice_no')) {
                $table->string('invoice_no')->nullable();
            }
            if (!Schema::hasColumn('orders', 'company_address')) {
                $table->text('company_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('orders', 'tax')) {
                $table->decimal('tax', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('orders', 'total')) {
                $table->decimal('total', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['company_name', 'order_date', 'invoice_no', 'company_address', 'description', 'subtotal', 'tax', 'total', 'payment_method'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
