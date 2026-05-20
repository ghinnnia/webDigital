<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->string('nama', 100);
            $table->enum('role', ['owner', 'admin', 'general_manager', 'manager_divisi', 'finance', 'karyawan', ])->default('karyawan');
            $table->string('email', 100);
            $table->string('divisi', 100)->nullable(); // Divisi nullable
            $table->string('gaji', 100)->nullable(); // Divisi nullable
            $table->text('alamat');
            $table->string('kontak', 20);
            $table->string('foto')->nullable();
            $table->enum('status_kerja', ['aktif', 'resign', 'phk'])->default('aktif');
            $table->enum('status_karyawan', ['tetap', 'kontrak', 'freelance'])->default('tetap');
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index(['nama', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};