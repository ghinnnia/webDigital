<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'image',
        'is_featured',
        'order'
    ];

    /**
     * Mendapatkan artikel yang ditampilkan di halaman utama
     */
    public static function getHomepageArticles()
    {
        return static::where('is_featured', true)
            ->orderBy('order', 'asc')
            ->take(4) // Mengambil 4 artikel untuk ditampilkan di homepage
            ->get();
    }

    /**
     * Mendapatkan semua artikel dengan urutan
     */
    public static function getAllOrdered()
    {
        return static::orderBy('is_featured', 'desc')
            ->orderBy('order', 'asc')
            ->get();
    }
}