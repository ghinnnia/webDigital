<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    protected $table = 'task_submissions';
    
    protected $fillable = [
        // tambahkan kolom yang ada di tabel Anda
        // dari SQL, tabel task_submissions hanya punya id, created_at, updated_at
    ];
    
    // Relasi ke Task (jika ada)
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}