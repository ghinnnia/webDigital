<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kinerja_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            
            // Nilai komponen (0-100)
            $table->decimal('nilai_absensi', 5, 2)->default(0);
            $table->decimal('nilai_tugas', 5, 2)->default(0);
            $table->decimal('nilai_ketepatan', 5, 2)->default(0);
            $table->decimal('nilai_rata_rata', 5, 2)->default(0);
            
            // Grade (A, B, C, D, E)
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E'])->nullable();
            
            // Rekomendasi
            $table->text('rekomendasi')->nullable();
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            // Unique per karyawan per bulan
            $table->unique(['karyawan_id', 'bulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kinerja_pegawai');
    }
};