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
        // ========== TABEL CUTI ==========
        Schema::create('cuti', function (Blueprint $table) {
            $table->id();
            
            // Relationship
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID karyawan yang mengajukan cuti');
            
            // Periode Cuti
            $table->date('tanggal_mulai')
                ->comment('Tanggal mulai cuti');
            $table->date('tanggal_selesai')
                ->comment('Tanggal selesai cuti');
            $table->integer('durasi')
                ->comment('Jumlah hari cuti (hanya hari kerja)');
            $table->integer('total_hari_kalender')
                ->default(0)
                ->comment('Total hari termasuk weekend');
            
            // Informasi Cuti
            $table->text('keterangan')
                ->comment('Alasan atau keterangan cuti');
            $table->enum('jenis_cuti', ['tahunan', 'sakit', 'penting', 'melahirkan', 'lainnya'])
                ->default('lainnya')
                ->comment('tahunan=Cuti tahunan, sakit=Cuti sakit, penting=Cuti penting/khusus, melahirkan=Cuti melahirkan, lainnya=Cuti lainnya');
            
            // Status Persetujuan
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan'])
                ->default('menunggu')
                ->comment('menunggu=Menunggu persetujuan, disetujui=Disetujui, ditolak=Ditolak, dibatalkan=Dibatalkan');
            
            // Persetujuan
            $table->foreignId('disetujui_oleh')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('ID user yang menyetujui cuti');
            $table->text('catatan_penolakan')->nullable()
                ->comment('Catatan jika cuti ditolak');
            $table->timestamp('disetujui_pada')->nullable()
                ->comment('Waktu cuti disetujui/ditolak');
            
            // Informasi Cuti Tahunan
            $table->integer('sisa_cuti_sebelum')->nullable()
                ->comment('Sisa cuti sebelum pengajuan (untuk cuti tahunan)');
            $table->integer('sisa_cuti_sesudah')->nullable()
                ->comment('Sisa cuti setelah disetujui (untuk cuti tahunan)');
            
            // Referensi
            $table->string('nomor_cuti', 50)->nullable()
                ->unique()
                ->comment('Nomor surat/referensi cuti');
            $table->string('dokumen_pendukung')->nullable()
                ->comment('Path file dokumen pendukung');
            
            // Informasi Tambahan
            $table->string('lokasi_cuti', 255)->nullable()
                ->comment('Lokasi selama cuti (jika perlu)');
            $table->string('kontak_darurat', 100)->nullable()
                ->comment('Kontak darurat selama cuti');
            
            // Flags
            $table->boolean('is_pengganti')->default(false)
                ->comment('Apakah cuti ini sebagai pengganti');
            $table->foreignId('cuti_pengganti_id')->nullable()
                ->constrained('cuti')
                ->onDelete('set null')
                ->comment('ID cuti yang digantikan');
            $table->boolean('is_urgent')->default(false)
                ->comment('Apakah cuti mendesak');
            
            // Tracking
            $table->boolean('is_overlapping')->default(false)
                ->comment('Apakah cuti bertabrakan dengan cuti lain');
            $table->text('overlap_info')->nullable()
                ->comment('Informasi cuti yang bertabrakan');
            
            // Timestamps
            $table->timestamp('submitted_at')->nullable()
                ->comment('Waktu cuti diajukan');
            $table->timestamp('cancelled_at')->nullable()
                ->comment('Waktu cuti dibatalkan');
            $table->timestamps();
            $table->softDeletes();
            
            // Deleted by
            $table->unsignedBigInteger('deleted_by')->nullable()
                ->comment('ID user yang menghapus cuti');
            
            // ====== INDEXES ======
            // Main query indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'tanggal_mulai']);
            $table->index(['jenis_cuti', 'status']);
            
            // Filtering indexes
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');
            $table->index('jenis_cuti');
            $table->index('disetujui_oleh');
            $table->index('is_urgent');
            
            // Reporting indexes
            $table->index(['user_id', 'jenis_cuti', 'created_at']);
            $table->index(['disetujui_pada', 'status']);
            
            // Soft delete indexes
            $table->index(['deleted_at', 'status']);
            
            // Composite indexes for common queries
            $table->index(['user_id', 'deleted_at', 'status', 'tanggal_mulai']);
            $table->index(['jenis_cuti', 'deleted_at', 'status', 'disetujui_pada']);
        });

        // ========== TABEL CUTI_HISTORIES ==========
        Schema::create('cuti_histories', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('cuti_id')
                ->constrained('cuti')
                ->onDelete('cascade')
                ->comment('ID cuti yang dihistory');
                
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID user yang melakukan aksi');
            
            // Action details
            $table->string('action', 50)
                ->comment('created, updated, approved, rejected, cancelled, deleted, restored');
            $table->text('changes')->nullable()
                ->comment('Perubahan data dalam format JSON');
            $table->text('note')
                ->comment('Catatan atau deskripsi aksi');
            
            // System info
            $table->string('ip_address', 45)->nullable()
                ->comment('IP address user');
            $table->text('user_agent')->nullable()
                ->comment('User agent/browser');
            
            $table->timestamps();
            
            // ====== INDEXES ======
            $table->index(['cuti_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['cuti_id', 'action']);
        });

        // ========== TABEL CUTI_QUOTA (Kuota cuti per tahun) ==========
        Schema::create('cuti_quotas', function (Blueprint $table) {
            $table->id();
            
            // Relationship
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID karyawan');
            
            // Quota info
            $table->year('tahun')
                ->comment('Tahun quota cuti');
            $table->integer('quota_tahunan')
                ->default(12)
                ->comment('Kuota cuti tahunan (default: 12 hari)');
            $table->integer('terpakai')
                ->default(0)
                ->comment('Cuti yang sudah terpakai');
            $table->integer('sisa')
                ->default(12)
                ->comment('Sisa cuti');
            
            // Special quotas
            $table->integer('quota_khusus')->default(0)
                ->comment('Kuota cuti khusus (selain tahunan)');
            $table->integer('terpakai_khusus')->default(0)
                ->comment('Cuti khusus yang terpakai');
            
            // Flags
            $table->boolean('is_active')->default(true)
                ->comment('Apakah quota tahun ini aktif');
            $table->boolean('is_reset')->default(false)
                ->comment('Apakah sudah direset untuk tahun depan');
            
            // Reset info
            $table->timestamp('reset_at')->nullable()
                ->comment('Waktu terakhir direset');
            $table->unsignedBigInteger('reset_by')->nullable()
                ->comment('ID user yang mereset');
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['user_id', 'tahun']);
            
            // ====== INDEXES ======
            $table->index(['user_id', 'tahun', 'is_active']);
            $table->index(['tahun', 'is_active']);
            $table->index('sisa');
        });

        // ========== TABEL CUTI_ATTACHMENTS (Dokumen pendukung) ==========
        Schema::create('cuti_attachments', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('cuti_id')
                ->constrained('cuti')
                ->onDelete('cascade')
                ->comment('ID cuti');
                
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID user yang mengupload');
            
            // File info
            $table->string('filename')
                ->comment('Nama file di storage');
            $table->string('original_name')
                ->comment('Nama asli file');
            $table->string('path')
                ->comment('Path file di storage');
            $table->string('mime_type')->nullable()
                ->comment('MIME type file');
            $table->integer('size')
                ->default(0)
                ->comment('Size file dalam bytes');
            $table->string('extension', 10)->nullable()
                ->comment('Extension file');
            
            // Type & Purpose
            $table->enum('type', ['surat', 'dokumen', 'foto', 'laporan', 'lainnya'])
                ->default('dokumen')
                ->comment('Jenis dokumen');
            $table->string('description')->nullable()
                ->comment('Deskripsi dokumen');
            
            // Status
            $table->boolean('is_verified')->default(false)
                ->comment('Apakah dokumen sudah diverifikasi');
            $table->unsignedBigInteger('verified_by')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('ID user yang memverifikasi');
            $table->timestamp('verified_at')->nullable()
                ->comment('Waktu verifikasi');
            
            $table->timestamps();
            
            // ====== INDEXES ======
            $table->index(['cuti_id', 'type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['cuti_id', 'is_verified']);
        });

        // ========== TABEL CUTI_REPLACEMENTS (Penggantian cuti) ==========
        Schema::create('cuti_replacements', function (Blueprint $table) {
            $table->id();
            
            // Cuti yang digantikan
            $table->foreignId('cuti_id')
                ->constrained('cuti')
                ->onDelete('cascade')
                ->comment('ID cuti yang digantikan');
            
            // Pengganti
            $table->foreignId('replacement_user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ID karyawan pengganti');
            
            // Status
            $table->enum('status', ['pending', 'accepted', 'rejected'])
                ->default('pending')
                ->comment('Status persetujuan penggantian');
            
            // Details
            $table->text('reason')->nullable()
                ->comment('Alasan penggantian');
            $table->text('notes')->nullable()
                ->comment('Catatan tambahan');
            
            // Approval
            $table->unsignedBigInteger('approved_by')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('ID user yang menyetujui penggantian');
            $table->timestamp('approved_at')->nullable()
                ->comment('Waktu disetujui');
            
            $table->timestamps();
            
            // ====== INDEXES ======
            $table->index(['cuti_id', 'status']);
            $table->index(['replacement_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order (child first)
        Schema::dropIfExists('cuti_replacements');
        Schema::dropIfExists('cuti_attachments');
        Schema::dropIfExists('cuti_quotas');
        Schema::dropIfExists('cuti_histories');
        Schema::dropIfExists('cuti');
    }
};