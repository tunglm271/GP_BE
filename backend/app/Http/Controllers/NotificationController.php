<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = UserNotification::with('notification')
        ->where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($userNotification) {
            return [
                'id' => $userNotification->id,
                'user_id' => $userNotification->user_id,
                'is_read' => $userNotification->is_read,
                'sent_at' => $userNotification->sent_at,
                'created_at' => $userNotification->created_at,
                'updated_at' => $userNotification->updated_at,
                'data' => $userNotification->notification,
            ];
        });

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $userNotification = UserNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $userNotification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $userNotification = UserNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $userNotification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    public function clearAll(Request $request)
    {
        $user = $request->user();

        UserNotification::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'All notifications deleted']);
    }
}
