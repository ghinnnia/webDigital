<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TASKS TABLE - VERSI DIPERBAIKI
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            
            $table->text('deskripsi');
            $table->dateTime('deadline');
            $table->enum('status', ['pending', 'proses', 'selesai', 'dibatalkan'])->default('pending');
            
            // Foreign keys
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_by_manager')->nullable();
            
            $table->enum('target_type', ['karyawan', 'divisi', 'manager'])->default('karyawan');
            $table->unsignedBigInteger('target_divisi_id')->nullable();
            $table->unsignedBigInteger('target_manager_id')->nullable();
            $table->boolean('is_broadcast')->default(false);
            
            $table->string('submission_file')->nullable();
            $table->text('submission_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            $table->text('catatan')->nullable();
            $table->text('catatan_update')->nullable();
            
            $table->integer('progress_percentage')->default(0);
            
            $table->unsignedBigInteger('parent_task_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by_manager')->references('id')->on('users')->onDelete('set null');
            $table->foreign('target_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_task_id')->references('id')->on('tasks')->onDelete('cascade');
            
            // Foreign key untuk project_id akan ditambahkan di migration terpisah setelah project table dibuat
            
            // INDEXES
            $table->index('status');
            $table->index('deadline');
            $table->index(['assigned_to', 'status']);
            $table->index(['created_by', 'status']);
            $table->index('target_divisi_id');
            $table->index('project_id');
            $table->index('parent_task_id');
        });

        // TASK_FILES TABLE
        Schema::create('task_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->bigInteger('size')->default(0);
            $table->string('mime_type')->nullable();
            $table->text('description')->nullable();
            
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('task_id');
            $table->index('user_id');
        });

        // TASK_COMMENTS TABLE
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            
            $table->text('content');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('task_id');
            $table->index('user_id');
            $table->index(['task_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_files');
        Schema::dropIfExists('tasks');
    }
};