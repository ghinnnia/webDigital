<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'item_no',
        'description',
        'price',
        'qty',
        'total',
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'price' => 'integer',
        'qty' => 'integer',
        'total' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
