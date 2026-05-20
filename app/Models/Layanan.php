<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanans';
    
    protected $fillable = [
        'nama_layanan',
        'deskripsi',
        'hpp',
        'harga',
        'foto',
    ];
    
    /**
     * Relasi ke Project
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}