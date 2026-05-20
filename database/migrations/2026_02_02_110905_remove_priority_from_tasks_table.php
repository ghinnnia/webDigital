<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }
            
            // Juga hapus kolom lain yang tidak diperlukan
            $columnsToRemove = ['progress_percentage', 'target_type', 'is_broadcast', 'kategori'];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('tasks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Jika perlu rollback, tambahkan kembali
            $table->string('priority', 20)->default('medium');
            $table->integer('progress_percentage')->default(0);
            $table->string('target_type', 50)->default('karyawan');
            $table->boolean('is_broadcast')->default(false);
            $table->string('kategori')->nullable();
        });
    }
};