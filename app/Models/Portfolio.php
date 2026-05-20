<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'technologies_used',
        'order'
    ];

    /**
     * Mendapatkan semua portofolio diurutkan berdasarkan order
     */
    public static function getAllOrdered()
    {
        return static::orderBy('order', 'asc')->get();
    }
}