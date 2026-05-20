<?php
// database/migrations/2024_01_15_000002_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type'); // task_reminder, deadline_warning, task_assigned
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};