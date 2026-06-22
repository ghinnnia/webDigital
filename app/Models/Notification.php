<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 
        'title', 
        'message', 
        'type', 
        'link', 
        'is_read',
        'task_id', 
        'payroll_id' 
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}