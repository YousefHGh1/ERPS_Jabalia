<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SendAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param array<int> $userIds
     * @param array{type?: string, title: string, message: string, link?: string, icon?: string, priority?: string} $data
     */
    public function __construct(
        public array $userIds,
        public array $data
    ) {}

    public function handle(): void
    {
        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $this->data['type'] ?? 'system',
                'title' => $this->data['title'],
                'message' => $this->data['message'],
                'link' => $this->data['link'] ?? null,
                'icon' => $this->data['icon'] ?? null,
                'priority' => $this->data['priority'] ?? 'normal',
                'is_read' => false,
            ]);
            Cache::forget('user_notifications_' . $user->id);
        }
    }
}
