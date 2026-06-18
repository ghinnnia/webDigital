<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            // Cek kolom 'bonus' dulu, kalau tidak ada pakai 'after' biasa
            $hasBonus = Schema::hasColumn('payroll_details', 'bonus');
            
            // Kolom jam_lembur
            if (!Schema::hasColumn('payroll_details', 'jam_lembur')) {
                if ($hasBonus) {
                    $table->decimal('jam_lembur', 8, 2)->default(0)->after('bonus');
                } else {
                    $table->decimal('jam_lembur', 8, 2)->default(0);
                }
            }
            
            // Kolom upah_lembur
            if (!Schema::hasColumn('payroll_details', 'upah_lembur')) {
                if (Schema::hasColumn('payroll_details', 'jam_lembur')) {
                    $table->decimal('upah_lembur', 15, 2)->default(0)->after('jam_lembur');
                } else {
                    $table->decimal('upah_lembur', 15, 2)->default(0);
                }
            }
            
            // Kolom potongan_bpjs
            if (!Schema::hasColumn('payroll_details', 'potongan_bpjs')) {
                if (Schema::hasColumn('payroll_details', 'potongan_tidak_hadir')) {
                    $table->decimal('potongan_bpjs', 15, 2)->default(0)->after('potongan_tidak_hadir');
                } else {
                    $table->decimal('potongan_bpjs', 15, 2)->default(0);
                }
            }
            
            // Kolom tunjangan_lain - jika bonus tidak ada, jangan pakai after bonus
            if (!Schema::hasColumn('payroll_details', 'tunjangan_lain')) {
                if ($hasBonus) {
                    $table->decimal('tunjangan_lain', 15, 2)->default(0)->after('bonus');
                } else {
                    $table->decimal('tunjangan_lain', 15, 2)->default(0);
                }
            }
        });
    }

    public function down()
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $columns = ['jam_lembur', 'upah_lembur', 'potongan_bpjs', 'tunjangan_lain'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('payroll_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};