<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->update(['is_read' => true]);
        
        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }
    
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }
    
    public function destroy($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->delete();
        
        return back()->with('success', 'Notifikasi dihapus');
    }
}