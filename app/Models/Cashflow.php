<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'cashflows';

    /**
     * Atribut yang dapat diisi secara massal (mass assignment).
     * 'nomor_transaksi' tidak perlu di sini karena akan dibuat otomatis.
     *
     * @var array
     */
    protected $fillable = [
        'tanggal_transaksi',
        'nama_transaksi',
        'deskripsi',
        'jumlah',
        'tipe_transaksi',
        'kategori_id',
        'subkategori',
    ];

    /**
     * Atribut yang harus "di-cast" ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Relasi: Satu transaksi cashflow milik satu kategori.
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriCashflow::class, 'kategori_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Event 'creating' dijalankan SEBELUM data disimpan ke database.
        static::creating(function ($cashflow) {
            // Cek apakah nomor_transaksi sudah diisi manual, jika tidak, buat otomatis.
            if (empty($cashflow->nomor_transaksi)) {
                $cashflow->nomor_transaksi = self::generateNomorTransaksi();
            }
        });
    }

    /**
     * Fungsi untuk menghasilkan nomor transaksi unik.
     *
     * @return string
     */
    public static function generateNomorTransaksi()
    {
        // Ambil tanggal hari ini dalam format YYYYMMDD
        $date = date('Ymd');

        // Cari transaksi terakhir pada hari ini yang dimulai dengan format yang sama
        $lastTransaction = self::where('nomor_transaksi', 'like', "INV-{$date}-%")
            ->orderBy('nomor_transaksi', 'desc')
            ->first();

        // Jika ada transaksi hari ini, ambil nomor urut terakhir dan tambah 1
        if ($lastTransaction) {
            $lastSequence = (int) substr($lastTransaction->nomor_transaksi, -4);
            $newSequence = $lastSequence + 1;
        } else {
            // Jika tidak ada transaksi hari ini, mulai dari 1
            $newSequence = 1;
        }

        // Format nomor urut menjadi 4 digit (contoh: 1 menjadi 0001)
        $formattedSequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);

        // Gabungkan semua bagian menjadi nomor transaksi final
        return "INV-{$date}-{$formattedSequence}";
    }
}
