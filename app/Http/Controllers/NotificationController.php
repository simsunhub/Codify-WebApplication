<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('pages.notifications', compact('notifications'));
    }

    /**
     * Mark a specific notification as read and redirect to its URL.
     */
    public function read(Notification $notification)
    {
        // Authorize
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        // Redirect to target URL or default back
        if ($notification->url) {
            return redirect($notification->url);
        }

        return back()->with('success', __('messages.notification_read_success', [], 'en'));
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', __('messages.notifications_all_read_success', [], 'en'));
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        // Authorize
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', __('messages.notification_deleted_success', [], 'en'));
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function destroyAll()
    {
        Notification::where('user_id', auth()->id())->delete();

        return back()->with('success', __('messages.notifications_all_cleared_success', [], 'en'));
    }
}