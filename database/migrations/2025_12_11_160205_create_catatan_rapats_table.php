<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('catatan_rapats', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('tanggal');
    $table->string('topik');
    $table->text('hasil_diskusi');
    $table->text('keputusan');
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_rapats');
    }
};
