<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect to the related URL if available
        $url = $notification->data['url'] ?? route('notifications.index');

        return redirect($url);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
