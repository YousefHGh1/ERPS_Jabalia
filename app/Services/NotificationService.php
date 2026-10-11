<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendAppNotificationJob;

class NotificationService
{
    /**
     * Send a notification to a specific user or multiple users (queued).
     *
     * @param mixed $users User model or collection of users or user IDs
     * @param array $data Notification data (type, title, message, link, icon, priority)
     * @return void
     */
    public static function send($users, array $data)
    {
        if ($users instanceof User) {
            $users = collect([$users]);
        } elseif (is_numeric($users)) {
            $users = collect([User::find($users)]);
        } elseif (is_array($users) && isset($users[0]) && is_numeric($users[0])) {
            $users = User::whereIn('id', $users)->get();
        }

        $userIds = collect($users)->filter()->pluck('id')->unique()->values()->all();
        if (empty($userIds)) {
            return;
        }

        $payload = [
            'type' => $data['type'] ?? 'system',
            'title' => $data['title'],
            'message' => $data['message'],
            'link' => $data['link'] ?? null,
            'icon' => $data['icon'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
        ];

        SendAppNotificationJob::dispatch($userIds, $payload);
    }

    /**
     * Send notification to all users with specific roles
     */
    public static function sendToRoles(array $roles, array $data)
    {
        $users = User::role($roles)->get();
        self::send($users, $data);
    }

    /**
     * Send notification to all administrators
     */
    public static function notifyAdmins(array $data)
    {
        self::sendToRoles(['admin', 'Super Admin'], $data);
    }
}
