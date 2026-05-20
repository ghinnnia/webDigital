<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Data dasar
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Role dan status
            $table->enum('role', ['owner', 'admin', 'general_manager', 'manager_divisi', 'finance', 'karyawan'])->default('karyawan');
            $table->enum('status_kerja', ['aktif', 'resign', 'phk'])->default('aktif');
            $table->enum('status_karyawan', ['tetap', 'kontrak', 'freelance'])->default('tetap');
            
            // Relasi ke divisi
            $table->foreignId('divisi_id')->nullable()->constrained('divisi')->onDelete('set null');
            
            // Data karyawan
            $table->decimal('gaji', 15, 2)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kontak', 20)->nullable();
            $table->string('foto')->nullable();
            
            // Manajemen cuti
            $table->integer('sisa_cuti')->default(12);
            $table->integer('total_cuti_tahunan')->default(12)->comment('Total cuti tahunan');
            $table->date('cuti_reset_date')->nullable()->comment('Tanggal terakhir reset cuti');
            $table->integer('cuti_terpakai_tahun_ini')->default(0)->comment('Cuti terpakai tahun berjalan');
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};