<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Support\Carbon;

class NotificationService
{
    public static function sendToUser(int $userId, array $notificationData): void
    {
        $notification = Notification::create([
            'type'    => $notificationData['type'],
            'title'   => $notificationData['title'],
            'message' => $notificationData['message'],
            'data'    => $notificationData['data'] ?? [],
        ]);
        UserNotification::create([
            'user_id'         => $userId,
            'notification_id' => $notification->id,
            'sent_at'         => Carbon::now(),
        ]);
    }

    public static function sendToMany(array $userIds, array $notificationData): void
    {
        $notification = Notification::create([
            'type'    => $notificationData['type'],
            'title'   => $notificationData['title'],
            'message' => $notificationData['message'],
            'data'    => $notificationData['data'] ?? [],
        ]);

        $now = Carbon::now();

        $records = array_map(function ($userId) use ($notification, $now) {
            return [
                'user_id'         => $userId,
                'notification_id' => $notification->id,
                'sent_at'         => $now,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }, $userIds);

        UserNotification::insert($records);
    }
}
