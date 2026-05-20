<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update old jenis_cuti values to new values
        DB::table('cuti')->where('jenis_cuti', 'sakit')->update(['jenis_cuti' => 'duka']);
        DB::table('cuti')->where('jenis_cuti', 'penting')->update(['jenis_cuti' => 'izin-khusus']);
        DB::table('cuti')->where('jenis_cuti', 'lainnya')->update(['jenis_cuti' => 'tanpa-gaji']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes if needed
        DB::table('cuti')->where('jenis_cuti', 'duka')->where('keterangan', 'like', '%sakit%')->update(['jenis_cuti' => 'sakit']);
        DB::table('cuti')->where('jenis_cuti', 'izin-khusus')->where('keterangan', 'like', '%penting%')->update(['jenis_cuti' => 'penting']);
        DB::table('cuti')->where('jenis_cuti', 'tanpa-gaji')->where('keterangan', 'like', '%lainnya%')->update(['jenis_cuti' => 'lainnya']);
    }
};
