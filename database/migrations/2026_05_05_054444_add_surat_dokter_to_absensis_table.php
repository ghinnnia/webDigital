<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('absensis', function (Blueprint $table) {
            if (!Schema::hasColumn('absensis', 'ada_surat_dokter')) {
                $table->boolean('ada_surat_dokter')->default(false)->after('keterangan');
            }
            if (!Schema::hasColumn('absensis', 'file_surat')) {
                $table->string('file_surat')->nullable()->after('ada_surat_dokter');
            }
        });
    }

    public function down()
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('ada_surat_dokter');
            $table->dropColumn('file_surat');
        });
    }
};