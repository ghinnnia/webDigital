<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectNotification extends Model
{
    use HasFactory;

    protected $table = 'project_notifications';
    
    protected $fillable = [
        'project_id', 
        'type', 
        'message', 
        'trigger_date', 
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'trigger_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Boot method untuk set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notification) {
            // Set trigger_date ke now() jika tidak diisi
            if (!$notification->trigger_date) {
                $notification->trigger_date = now();
            }
            
            // Set is_read ke false jika tidak diisi
            if ($notification->is_read === null) {
                $notification->is_read = false;
            }
        });
    }
    
}