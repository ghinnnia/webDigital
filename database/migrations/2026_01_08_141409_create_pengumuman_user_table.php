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
        Schema::create('pengumuman_user', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pengumuman_id')
                  ->constrained('pengumuman')
                  ->cascadeOnDelete();
                  
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
                  
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['pengumuman_id', 'user_id'], 'pengumuman_user_unique');
            
            // Index untuk performa query
            $table->index('user_id');
            $table->index(['pengumuman_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_user');
    }
};