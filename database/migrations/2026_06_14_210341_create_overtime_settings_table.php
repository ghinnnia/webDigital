<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('overtime_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->decimal('default_rate', 12, 2)->default(30000);
            $table->decimal('max_rate', 12, 2)->default(50000);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('division_id')->references('id')->on('divisi')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('overtime_settings');
    }
};