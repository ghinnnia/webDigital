<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing foreign key, make column nullable, re-add foreign key
        DB::statement('ALTER TABLE `cashflows` DROP FOREIGN KEY `cashflows_kategori_id_foreign`');
        DB::statement('ALTER TABLE `cashflows` MODIFY `kategori_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `cashflows` ADD CONSTRAINT `cashflows_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_cashflow`(`id`) ON DELETE RESTRICT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `cashflows` DROP FOREIGN KEY `cashflows_kategori_id_foreign`');
        DB::statement('ALTER TABLE `cashflows` MODIFY `kategori_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `cashflows` ADD CONSTRAINT `cashflows_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_cashflow`(`id`) ON DELETE RESTRICT');
    }
};
