<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tugas_karyawan_to_manager')) {
            Schema::create('tugas_karyawan_to_manager', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('karyawan_id');
                $table->unsignedBigInteger('manager_divisi_id');
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('judul');
                $table->string('nama_tugas')->nullable();
                $table->text('deskripsi')->nullable();
                $table->dateTime('deadline')->nullable();
                $table->enum('status', ['draft', 'submitted', 'in_review', 'approved', 'rejected', 'proses', 'selesai', 'dibatalkan'])->default('submitted');
                $table->text('catatan')->nullable();
                $table->string('lampiran')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('karyawan_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('manager_divisi_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('project_id')->references('id')->on('project')->onDelete('set null');

                $table->index('karyawan_id');
                $table->index('manager_divisi_id');
                $table->index('project_id');
                $table->index('status');
                $table->index('deadline');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tugas_karyawan_to_manager')) {
            Schema::dropIfExists('tugas_karyawan_to_manager');
        }
    }
};
