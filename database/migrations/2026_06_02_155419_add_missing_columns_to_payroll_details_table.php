<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('payroll_details', function (Blueprint $table) {
        if (!Schema::hasColumn('payroll_details', 'tunjangan_lain')) {
            $table->decimal('tunjangan_lain', 15, 2)->default(0);
            // ↑ hapus ->after('bonus')
        }
        if (!Schema::hasColumn('payroll_details', 'jam_lembur')) {
            // jam_lembur sudah ada di create migration, ini akan di-skip otomatis
            $table->decimal('jam_lembur', 8, 2)->default(0);
        }
        if (!Schema::hasColumn('payroll_details', 'upah_lembur')) {
            // upah_lembur sudah ada di create migration, ini akan di-skip otomatis
            $table->decimal('upah_lembur', 15, 2)->default(0);
        }
        if (!Schema::hasColumn('payroll_details', 'potongan_bpjs')) {
            $table->decimal('potongan_bpjs', 15, 2)->default(0);
            // ↑ hapus ->after('potongan_tidak_hadir') jika error juga
        }
    });
}

    public function down()
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn(['jam_lembur', 'upah_lembur', 'potongan_bpjs', 'tunjangan_lain']);
        });
    }
};