<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel utama yang menyimpan rincian gaji per karyawan per periode.
     * Relasinya:
     *   payroll_periods (1) ──< payroll_details (N) >── users / karyawan
     */
    public function up(): void
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();

            // === Relasi ===
            $table->foreignId('payroll_period_id')
                  ->constrained('payroll_periods')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('user_id');         // FK ke users.id
            $table->unsignedBigInteger('karyawan_id');     // FK ke karyawan.id (untuk ambil data kontrak dll)

            // === Snapshot data dasar (disimpan agar histori tidak berubah) ===
            $table->string('nama_karyawan');
            $table->string('divisi')->nullable();
            $table->decimal('gaji_per_bulan', 15, 2)->default(0); // dari karyawan.gaji saat dihitung

            // === Komponen Kehadiran ===
            $table->unsignedSmallInteger('total_hari_kerja')->default(0);   // hari kerja dalam periode
            $table->unsignedSmallInteger('hari_hadir')->default(0);         // hadir (ada absen masuk)
            $table->unsignedSmallInteger('hari_alfa')->default(0);          // tidak hadir tanpa keterangan
            $table->unsignedSmallInteger('hari_sakit_tanpa_surat')->default(0);
            $table->unsignedSmallInteger('hari_izin_tanpa_ket')->default(0);
            $table->unsignedSmallInteger('hari_cuti')->default(0);          // cuti approved (tidak dipotong)
            $table->unsignedSmallInteger('hari_sakit_dengan_surat')->default(0); // tidak dipotong
            $table->decimal('jam_lembur', 8, 2)->default(0);                // total jam lembur

            // === Komponen Perhitungan Gaji ===
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            // = (gaji_per_bulan / 30) × hari_hadir_efektif

            $table->decimal('potongan_tidak_hadir', 15, 2)->default(0);
            // = (gaji_per_bulan / 30) × (hari_alfa + hari_sakit_tanpa_surat + hari_izin_tanpa_ket)

            $table->decimal('tunjangan_tetap', 15, 2)->default(0);
            // = sum(payroll_allowances WHERE tipe = 'tunjangan_tetap')

            $table->decimal('nilai_kpa', 5, 2)->default(0);                // snapshot nilai_akhir dari kpa
            $table->decimal('persentase_tunjangan_kinerja', 5, 2)->default(0); // dari kpa_tunjangan_rules
            $table->decimal('tunjangan_kinerja', 15, 2)->default(0);
            // = persentase_tunjangan_kinerja / 100 × gaji_pokok

            $table->decimal('tarif_lembur_per_jam', 15, 2)->default(0);    // snapshot tarif saat hitung
            $table->decimal('upah_lembur', 15, 2)->default(0);
            // = jam_lembur × tarif_lembur_per_jam

            // === Total ===
            $table->decimal('total_gaji_bersih', 15, 2)->default(0);
            // = gaji_pokok - potongan_tidak_hadir + tunjangan_tetap + tunjangan_kinerja + upah_lembur

            // === Status & Audit ===
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->text('catatan')->nullable();             // catatan khusus per karyawan
            $table->timestamps();

            // Index
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
            $table->unique(['payroll_period_id', 'user_id']); // 1 baris per karyawan per periode
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
    }
};
