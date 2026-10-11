<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $message;
    protected $link;
    protected $icon;
    protected $priority;
    protected $userIds;

    /**
     * Create a new job instance.
     */
    public function __construct(string $title, string $message, ?string $link = null, ?string $icon = null, string $priority = 'normal', array $userIds = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->link = $link;
        $this->icon = $icon ?? 'fa-bell';
        $this->priority = $priority;
        $this->userIds = $userIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $users = empty($this->userIds) 
                ? User::where('status', 'active')->get() 
                : User::whereIn('id', $this->userIds)->where('status', 'active')->get();

            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $this->title,
                    'message' => $this->message,
                    'link' => $this->link,
                    'icon' => $this->icon,
                    'priority' => $this->priority,
                    'read_at' => null,
                ]);
            }

            Log::info('Bulk notification sent', [
                'title' => $this->title,
                'count' => $users->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bulk notification', [
                'title' => $this->title,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
