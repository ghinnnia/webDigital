<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumumanUser extends Model
{
    use HasFactory;
    
    protected $table = 'pengumuman_user';
    
    protected $fillable = [
        'pengumuman_id',
        'user_id',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}