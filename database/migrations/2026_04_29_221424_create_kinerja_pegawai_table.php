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
            $table->foreignId('karyawan_id')->constrained('users')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->decimal('nilai_kehadiran', 5, 2)->default(0);
            $table->decimal('nilai_ketepatan_waktu', 5, 2)->default(0);
            $table->decimal('nilai_penyelesaian_tugas', 5, 2)->default(0);
            $table->decimal('nilai_kesesuaian_tugas', 5, 2)->default(0);
            $table->decimal('nilai_rata_rata', 5, 2)->default(0);
            $table->string('grade', 2)->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
            
            $table->unique(['karyawan_id', 'bulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kinerja_pegawai');
    }
};