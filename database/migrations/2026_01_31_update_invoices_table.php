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
            // Drop old columns if they exist
            if (Schema::hasColumn('invoices', 'nomor_order')) {
                $table->dropColumn('nomor_order');
            }
            if (Schema::hasColumn('invoices', 'nama_perusahaan')) {
                $table->dropColumn('nama_perusahaan');
            }
            if (Schema::hasColumn('invoices', 'nama_klien')) {
                $table->dropColumn('nama_klien');
            }
            if (Schema::hasColumn('invoices', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('invoices', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
            if (Schema::hasColumn('invoices', 'harga')) {
                $table->dropColumn('harga');
            }
            if (Schema::hasColumn('invoices', 'qty')) {
                $table->dropColumn('qty');
            }
            if (Schema::hasColumn('invoices', 'pajak')) {
                $table->dropColumn('pajak');
            }
            if (Schema::hasColumn('invoices', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }
            if (Schema::hasColumn('invoices', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
        });

        // Add new columns
        Schema::table('invoices', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('invoices', 'invoice_no')) {
                $table->string('invoice_no')->unique()->nullable();
            }
            if (!Schema::hasColumn('invoices', 'invoice_date')) {
                $table->date('invoice_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'company_address')) {
                $table->text('company_address')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'client_name')) {
                $table->string('client_name')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('invoices', 'tax')) {
                $table->decimal('tax', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('invoices', 'total')) {
                $table->decimal('total', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop new columns
            if (Schema::hasColumn('invoices', 'invoice_no')) {
                $table->dropColumn('invoice_no');
            }
            if (Schema::hasColumn('invoices', 'invoice_date')) {
                $table->dropColumn('invoice_date');
            }
            if (Schema::hasColumn('invoices', 'company_name')) {
                $table->dropColumn('company_name');
            }
            if (Schema::hasColumn('invoices', 'company_address')) {
                $table->dropColumn('company_address');
            }
            if (Schema::hasColumn('invoices', 'client_name')) {
                $table->dropColumn('client_name');
            }
            if (Schema::hasColumn('invoices', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('invoices', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
            if (Schema::hasColumn('invoices', 'tax')) {
                $table->dropColumn('tax');
            }
            if (Schema::hasColumn('invoices', 'total')) {
                $table->dropColumn('total');
            }
            if (Schema::hasColumn('invoices', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });

        // Restore old columns
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('nomor_order')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('nama_klien')->nullable();
            $table->text('alamat')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('pajak', 5, 2)->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->date('tanggal')->nullable();
        });
    }
};
