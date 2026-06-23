<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        // Vérification que la notification appartient à l'utilisateur connecté
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }
        $notification->markAsRead();
        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(DatabaseNotification $notification)
    {
        // Vérification que la notification appartient à l'utilisateur connecté
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }
        $notification->delete();
        return back()->with('success', 'Notification supprimée.');
    }

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'Toutes les notifications ont été supprimées.');
    }
}