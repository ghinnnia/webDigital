<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel aspek_kpa (baru)
        if (!Schema::hasTable('aspek_kpa')) {
            Schema::create('aspek_kpa', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->integer('bobot');
                $table->integer('urutan');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tabel indikator_kpa (baru)
        if (!Schema::hasTable('indikator_kpa')) {
            Schema::create('indikator_kpa', function (Blueprint $table) {
                $table->id();
                $table->foreignId('aspek_id')->constrained('aspek_kpa')->onDelete('cascade');
                $table->string('nama');
                $table->integer('bobot');
                $table->enum('tipe', ['otomatis', 'manual']);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Tabel target_kuantitas (baru)
        if (!Schema::hasTable('target_kuantitas')) {
            Schema::create('target_kuantitas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('users')->onDelete('cascade');
                $table->integer('bulan');
                $table->integer('tahun');
                $table->integer('target');
                $table->integer('realisasi')->default(0);
                $table->timestamps();
            });
        }

        // 4. Tabel penilaian_kpa (baru)
        if (!Schema::hasTable('penilaian_kpa')) {
            Schema::create('penilaian_kpa', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('indikator_id')->constrained('indikator_kpa')->onDelete('cascade');
                $table->integer('bulan');
                $table->integer('tahun');
                $table->integer('nilai');
                $table->text('catatan')->nullable();
                $table->timestamps();
                
                $table->unique(['karyawan_id', 'indikator_id', 'bulan', 'tahun'], 'unique_penilaian');
            });
        }

        // 5. Tabel tunjangan (baru)
        if (!Schema::hasTable('tunjangan')) {
            Schema::create('tunjangan', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->integer('nominal');
                $table->enum('tipe', ['bulanan', 'bonus']);
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 6. Tabel tunjangan_karyawan (baru)
        if (!Schema::hasTable('tunjangan_karyawan')) {
            Schema::create('tunjangan_karyawan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('tunjangan_id')->constrained('tunjangan')->onDelete('cascade');
                $table->integer('bulan');
                $table->integer('tahun');
                $table->boolean('diberikan')->default(true);
                $table->timestamps();
            });
        }

        // 7. Tabel pengumuman (CEK DULU, JIKA SUDAH ADA, TAMBAHKAN KOLOM YANG KURANG)
        if (!Schema::hasTable('pengumuman')) {
            Schema::create('pengumuman', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->text('isi');
                $table->foreignId('created_by')->constrained('users');
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->enum('target', ['semua', 'hr', 'manager', 'karyawan'])->default('semua');
                $table->timestamps();
            });
        } else {
            // Jika tabel sudah ada, cek dan tambahkan kolom yang mungkin kurang
            Schema::table('pengumuman', function (Blueprint $table) {
                if (!Schema::hasColumn('pengumuman', 'target')) {
                    $table->enum('target', ['semua', 'hr', 'manager', 'karyawan'])->default('semua');
                }
            });
        }

        // 8. Insert data awal (Cek dulu apakah sudah ada data)
        if (DB::table('aspek_kpa')->count() == 0) {
            DB::table('aspek_kpa')->insert([
                ['nama' => 'Performance', 'bobot' => 50, 'urutan' => 1, 'created_at' => now()],
                ['nama' => 'Perilaku & Kompetensi', 'bobot' => 30, 'urutan' => 2, 'created_at' => now()],
                ['nama' => 'Sikap & Budaya Kerja', 'bobot' => 20, 'urutan' => 3, 'created_at' => now()],
            ]);
        }

        // Insert indikator Performance
        $aspekPerformance = DB::table('aspek_kpa')->where('nama', 'Performance')->first();
        if ($aspekPerformance && DB::table('indikator_kpa')->where('aspek_id', $aspekPerformance->id)->count() == 0) {
            DB::table('indikator_kpa')->insert([
                ['aspek_id' => $aspekPerformance->id, 'nama' => 'Target Tugas', 'bobot' => 30, 'tipe' => 'otomatis', 'created_at' => now()],
                ['aspek_id' => $aspekPerformance->id, 'nama' => 'Kualitas Kerja', 'bobot' => 20, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekPerformance->id, 'nama' => 'Ketepatan Waktu', 'bobot' => 30, 'tipe' => 'otomatis', 'created_at' => now()],
                ['aspek_id' => $aspekPerformance->id, 'nama' => 'Kuantitas Kerja', 'bobot' => 20, 'tipe' => 'otomatis', 'created_at' => now()],
            ]);
        }

        // Insert indikator Perilaku & Kompetensi
        $aspekPerilaku = DB::table('aspek_kpa')->where('nama', 'Perilaku & Kompetensi')->first();
        if ($aspekPerilaku && DB::table('indikator_kpa')->where('aspek_id', $aspekPerilaku->id)->count() == 0) {
            DB::table('indikator_kpa')->insert([
                ['aspek_id' => $aspekPerilaku->id, 'nama' => 'Kerja Sama Tim', 'bobot' => 15, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekPerilaku->id, 'nama' => 'Inisiatif', 'bobot' => 15, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekPerilaku->id, 'nama' => 'Kemandirian', 'bobot' => 15, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekPerilaku->id, 'nama' => 'Disiplin', 'bobot' => 15, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekPerilaku->id, 'nama' => 'Tanggung Jawab', 'bobot' => 15, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekPerilaku->id, 'nama' => 'Kompetensi Teknik', 'bobot' => 15, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekPerilaku->id, 'nama' => 'Kepemimpinan', 'bobot' => 10, 'tipe' => 'manual', 'created_at' => now()],
            ]);
        }

        // Insert indikator Sikap & Budaya Kerja
        $aspekSikap = DB::table('aspek_kpa')->where('nama', 'Sikap & Budaya Kerja')->first();
        if ($aspekSikap && DB::table('indikator_kpa')->where('aspek_id', $aspekSikap->id)->count() == 0) {
            DB::table('indikator_kpa')->insert([
                ['aspek_id' => $aspekSikap->id, 'nama' => 'Orientasi Pelayanan', 'bobot' => 40, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekSikap->id, 'nama' => 'Integrasi & Komitmen', 'bobot' => 35, 'tipe' => 'manual', 'created_at' => now()],
                ['aspek_id' => $aspekSikap->id, 'nama' => 'Kemampuan Beradaptasi', 'bobot' => 25, 'tipe' => 'manual', 'created_at' => now()],
            ]);
        }
    }

    public function down()
    {
        // Hapus tabel baru saja, jangan hapus yang sudah ada sebelumnya
        Schema::dropIfExists('penilaian_kpa');
        Schema::dropIfExists('target_kuantitas');
        Schema::dropIfExists('tunjangan_karyawan');
        Schema::dropIfExists('tunjangan');
        Schema::dropIfExists('indikator_kpa');
        Schema::dropIfExists('aspek_kpa');
        
        // Jangan hapus tabel pengumuman karena sudah ada sebelumnya
        // Hanya hapus kolom target jika perlu (optional)
        if (Schema::hasTable('pengumuman') && Schema::hasColumn('pengumuman', 'target')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dropColumn('target');
            });
        }
    }
};