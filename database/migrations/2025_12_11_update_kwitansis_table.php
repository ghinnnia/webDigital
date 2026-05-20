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
        Schema::table('kwitansis', function (Blueprint $table) {
            // Tambah field yang ada di invoice tapi belum di kwitansi
            
            // Jika kolom sudah ada, skip. Tapi kita tambahkan yang definitely belum ada
            if (!Schema::hasColumn('kwitansis', 'invoice_no')) {
                $table->string('invoice_no')->nullable()->after('kwitansi_no');
            }
            
            if (!Schema::hasColumn('kwitansis', 'company_address')) {
                $table->text('company_address')->nullable()->after('nama_perusahaan');
            }
            
            if (!Schema::hasColumn('kwitansis', 'kontak')) {
                $table->string('kontak')->nullable()->after('company_address');
            }
            
            if (!Schema::hasColumn('kwitansis', 'order_number')) {
                $table->string('order_number')->nullable()->after('kontak');
            }
            
            if (!Schema::hasColumn('kwitansis', 'nama_layanan')) {
                $table->string('nama_layanan')->nullable()->after('nama_klien');
            }
            
            if (!Schema::hasColumn('kwitansis', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('nama_layanan');
            }
            
            if (!Schema::hasColumn('kwitansis', 'tax')) {
                $table->integer('tax')->default(0)->after('harga');
            }
            
            if (!Schema::hasColumn('kwitansis', 'keterangan_tambahan')) {
                $table->text('keterangan_tambahan')->nullable()->after('fee_maintenance');
            }
            
            if (!Schema::hasColumn('kwitansis', 'jenis_bank')) {
                $table->string('jenis_bank')->nullable()->after('bank');
            }
            
            if (!Schema::hasColumn('kwitansis', 'kategori_pemasukan')) {
                $table->enum('kategori_pemasukan', ['layanan', 'produk', 'fee/komisi'])->default('layanan')->after('keterangan_tambahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kwitansis', function (Blueprint $table) {
            $columns = ['invoice_no', 'company_address', 'kontak', 'order_number', 'nama_layanan', 'payment_method', 'tax', 'keterangan_tambahan', 'kategori_pemasukan'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('kwitansis', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
