<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_allowances', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                         // "Tunjangan Transport", "Tunjangan Makan", dll
            $table->enum('tipe', ['tunjangan_tetap', 'tarif_lembur']);
            // tunjangan_tetap = nominal flat per bulan (transport, makan)
            // tarif_lembur    = nominal per jam lembur
            $table->decimal('nilai', 15, 2)->default(0);   // nominal dalam rupiah
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_allowances');
    }
};
