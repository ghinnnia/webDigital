<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini menyimpan mapping: rentang nilai KPA → persentase tunjangan kinerja.
     * Contoh data:
     *   nilai_min=90, nilai_max=100, persentase=15  → grade Sangat Baik  = 15% × gaji pokok
     *   nilai_min=75, nilai_max=89,  persentase=10  → grade Baik         = 10% × gaji pokok
     *   nilai_min=60, nilai_max=74,  persentase=7   → grade Cukup        = 7%  × gaji pokok
     *   nilai_min=0,  nilai_max=59,  persentase=0   → grade Kurang/Sangat Kurang = 0%
     */
    public function up(): void
    {
        Schema::create('kpa_tunjangan_rules', function (Blueprint $table) {
            $table->id();
            $table->decimal('nilai_min', 5, 2);            // batas bawah nilai KPA (inklusif)
            $table->decimal('nilai_max', 5, 2);            // batas atas nilai KPA (inklusif)
            $table->decimal('persentase', 5, 2)->default(0); // % dari gaji pokok
            $table->string('label')->nullable();           // "Sangat Baik", "Baik", dst
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpa_tunjangan_rules');
    }
};
