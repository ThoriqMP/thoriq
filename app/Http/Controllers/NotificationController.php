<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->appNotifications()->paginate(20);
        $unreadCount   = Auth::user()->unreadNotificationsCount();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    /**
     * Helper: send a notification to one or more users.
     */
    public static function send(int|array $userIds, string $type, string $title, string $body, ?string $actionUrl = null): void
    {
        $ids = is_array($userIds) ? $userIds : [$userIds];
        foreach ($ids as $userId) {
            Notification::create([
                'user_id'    => $userId,
                'type'       => $type,
                'title'      => $title,
                'body'       => $body,
                'action_url' => $actionUrl,
            ]);
        }
    }
}
