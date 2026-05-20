<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('users')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            
            // Komponen gaji
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('tunjangan_tetap', 15, 2)->default(0);
            $table->decimal('tunjangan_kinerja', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('potongan_bpjs', 15, 2)->default(0);
            $table->decimal('potongan_lain', 15, 2)->default(0);
            $table->decimal('total_gaji', 15, 2)->default(0);
            
            // ========== TAMBAHKAN INI UNTUK TUNJANGAN DETAIL ==========
            $table->decimal('total_tunjangan', 15, 2)->default(0)->after('gaji_pokok');
            $table->json('tunjangan_detail')->nullable()->after('total_tunjangan');
            // =========================================================
            
            $table->enum('status', ['draft', 'menunggu_finance', 'proses', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Unique per karyawan per bulan
            $table->unique(['karyawan_id', 'bulan', 'tahun'], 'unik_gaji');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gaji');
    }
    
};