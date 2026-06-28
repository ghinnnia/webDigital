<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectNotification extends Model
{
    protected $table = 'project_notifications';
    protected $fillable = ['project_id', 'type', 'message', 'trigger_date', 'is_read'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function getNotifications()
{
    $notifications = ProjectNotification::with('project')
        ->orderBy('created_at', 'desc')
        ->take(50)
        ->get();
    return response()->json(['success' => true, 'data' => $notifications]);
}

public function markNotificationAsRead($id)
{
    $notif = ProjectNotification::findOrFail($id);
    $notif->is_read = true;
    $notif->save();
    return response()->json(['success' => true]);
}

public function markAllNotificationsAsRead()
{
    ProjectNotification::where('is_read', false)->update(['is_read' => true]);
    return response()->json(['success' => true]);
}
}