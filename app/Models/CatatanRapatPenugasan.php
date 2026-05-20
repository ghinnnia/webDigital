<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanRapatPenugasan extends Model
{
    use HasFactory;
    
    protected $table = 'catatan_rapat_penugasan';
    
    protected $fillable = [
        'catatan_rapat_id',
        'user_id',
    ];
    
    public function catatanRapat()
    {
        return $this->belongsTo(CatatanRapat::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}