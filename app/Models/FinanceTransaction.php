<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    use HasFactory;

    protected $table = 'finance_transactions';

    /**
     * Kolom yang bisa diisi secara mass assignment (mass assignable)
     */
    protected $fillable = [
        'tanggal',
        'nama',
        'kategori',
        'deskripsi',
        'jumlah',
        'tipe',
    ];

    /**
     * Atribur untuk casting tipe data otomatis
     */
    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2', // Memastikan format uang selalu 2 desimal
    ];
    
    /**
     * Accessor untuk format Rupiah di Backend (Opsional)
     * Penggunaan: $transaction->formatted_amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }
}