<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    // Change 'task_files' if your database table name is different
    protected $table = 'task_files'; 
    
    protected $fillable = ['task_id', 'user_id', 'filename', 'original_name', 'path', 'size', 'mime_type'];
    
    // Relationship to the Task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // <--- ADD THIS RELATIONSHIP --->
    // This connects 'files.uploader' in your Controller to the User model
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}