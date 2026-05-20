<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gaji', function (Blueprint $table) {
            if (!Schema::hasColumn('gaji', 'total_tunjangan')) {
                $table->decimal('total_tunjangan', 15, 2)->default(0)->after('gaji_pokok');
            }
            
            if (!Schema::hasColumn('gaji', 'tunjangan_detail')) {
                $table->json('tunjangan_detail')->nullable()->after('total_tunjangan');
            }
            
            // Update enum status jika perlu
            $table->enum('status', ['draft', 'menunggu_finance', 'proses', 'selesai'])
                ->default('draft')
                ->change();
        });
    }

    public function down()
    {
        Schema::table('gaji', function (Blueprint $table) {
            $table->dropColumn(['total_tunjangan', 'tunjangan_detail']);
        });
    }
};