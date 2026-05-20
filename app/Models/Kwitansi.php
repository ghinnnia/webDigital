<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    use HasFactory;

    protected $table = 'kwitansis';

    protected $fillable = [
        'invoice_id',
        'kwitansi_no',
        'invoice_no',
        'tanggal',
        'nama_perusahaan',
        'company_address',
        'kontak',
        'order_number',
        'nama_klien',
        'nama_layanan',
        'payment_method',
        'deskripsi',
        'harga',
        'sub_total',
        'tax',
        'fee_maintenance',
        'total',
        'status',
        'bank',
        'jenis_bank',
        'no_rekening',
        'keterangan_tambahan',
        'kategori_pemasukan'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data asli.
     */
    protected $casts = [
        'tanggal' => 'date',
        'harga' => 'decimal:2',      
        'sub_total' => 'decimal:2',  
        'tax' => 'integer',
        'fee_maintenance' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Mendapatkan invoice yang terkait dengan kwitansi ini.
     * Relasi: Satu Invoice bisa memiliki banyak Kwitansi.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    // Format kwitansi number
    public function getKwitansiNoFormattedAttribute()
    {
        return '#2023_KWT_' . str_pad($this->kwitansi_no, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted harga attribute.
     * 
     * @return string
     */
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get formatted sub_total attribute.
     * 
     * @return string
     */
    public function getFormattedSubTotalAttribute()
    {
        return 'Rp ' . number_format($this->sub_total, 0, ',', '.');
    }

    /**
     * Get formatted fee_maintenance attribute.
     * 
     * @return string
     */
    public function getFormattedFeeMaintenanceAttribute()
    {
        return 'Rp ' . number_format($this->fee_maintenance, 0, ',', '.');
    }

    /**
     * Get formatted total attribute.
     * 
     * @return string
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /**
     * Get formatted tanggal attribute.
     * 
     * @return string
     */
    public function getTanggalIndoAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->locale('id')->isoFormat('DD/MM/YY');
    }

    /**
     * Boot model untuk menghitung total otomatis.
     */
    protected static function boot()
    {
        parent::boot();

        // Calculate total before saving
        static::saving(function ($kwitansi) {
            // Jika sub_total dan fee_maintenance ada, hitung total
            if (isset($kwitansi->sub_total) && isset($kwitansi->fee_maintenance)) {
                $kwitansi->total = $kwitansi->sub_total + $kwitansi->fee_maintenance;
            }
            // Jika hanya harga yang ada, gunakan harga sebagai sub_total
            elseif (isset($kwitansi->harga) && !isset($kwitansi->sub_total)) {
                $kwitansi->sub_total = $kwitansi->harga;
                $kwitansi->total = $kwitansi->harga + ($kwitansi->fee_maintenance ?? 0);
            }
        });
    }
}
