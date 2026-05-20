<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project', function (Blueprint $table) {
            $table->id();
            
            // Mengganti layanan_id dengan invoice_id
            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->onDelete('set null');
            
            // Penanggung jawab project
            $table->foreignId('penanggung_jawab_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            // Informasi dasar project
            $table->string('nama');
            $table->text('deskripsi');
            $table->decimal('harga', 15, 2)->nullable();
            
            // Tanggal pengerjaan (mengganti deadline)
            $table->date('tanggal_mulai_pengerjaan');
            $table->date('tanggal_selesai_pengerjaan')->nullable();
            
            // Tanggal kerjasama
            $table->date('tanggal_mulai_kerjasama')->nullable();
            $table->date('tanggal_selesai_kerjasama')->nullable();
            
            // Dua status terpisah
            $table->enum('status_pengerjaan', ['pending', 'dalam_pengerjaan', 'selesai', 'dibatalkan'])->default('pending');
            $table->enum('status_kerjasama', ['aktif', 'selesai', 'ditangguhkan'])->default('aktif');
            
            // Progres pengerjaan
            $table->integer('progres')->default(0);
            
            $table->timestamps();
            $table->softDeletes(); // Optional: untuk soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};