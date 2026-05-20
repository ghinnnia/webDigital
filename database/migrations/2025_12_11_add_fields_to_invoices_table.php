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
        Schema::table('invoices', function (Blueprint $table) {
            // Tambah field keterangan tambahan
            $table->text('keterangan_tambahan')->nullable()->after('description');
            
            // Tambah field jenis bank
            $table->string('jenis_bank')->nullable()->after('keterangan_tambahan');
            
            // Tambah field kategori pemasukan
            $table->enum('kategori_pemasukan', ['layanan', 'produk', 'fee/komisi'])->default('layanan')->after('jenis_bank');
            
            // Tambah field fee maintenance
            $table->integer('fee_maintenance')->default(0)->after('kategori_pemasukan');
            
            // Tambah index untuk kategori_pemasukan
            $table->index('kategori_pemasukan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['kategori_pemasukan']);
            $table->dropColumn(['keterangan_tambahan', 'jenis_bank', 'kategori_pemasukan', 'fee_maintenance']);
        });
    }
};
