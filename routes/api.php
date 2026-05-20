<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Notification;

// Test route
Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

// Route untuk notifikasi (perlu auth)
Route::middleware('auth:sanctum')->group(function () {
    
    // Ambil notifikasi terbaru (max 10)
    Route::get('/notifications', function() {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->take(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'is_read' => $n->is_read,
                    'task_id' => $n->task_id,
                    'time_ago' => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at->format('d M Y H:i'),
                ];
            })
        ]);
    });
    
    // Ambil jumlah notifikasi belum dibaca (Moved to web.php to support session auth)
    // Route::get('/notifications/unread-count', function() {
    //     $count = Auth::user()->notifications()
    //         ->where('is_read', false)
    //         ->count();
    //     
    //     return response()->json([
    //         'success' => true,
    //         'count' => $count
    //     ]);
    // });
    
    // Tandai notifikasi sudah dibaca
    Route::post('/notifications/{id}/mark-read', function($id) {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca'
        ]);
    });
    
    // Tandai semua notifikasi sudah dibaca
    Route::post('/notifications/mark-all-read', function() {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca'
        ]);
    });
    
    // Hapus notifikasi
    Route::delete('/notifications/{id}', function($id) {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi dihapus'
        ]);
    });
});