<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSectionNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sectionId;
    protected $title;
    protected $message;
    protected $link;
    protected $icon;
    protected $priority;

    /**
     * Create a new job instance.
     */
    public function __construct(int $sectionId, string $title, string $message, ?string $link = null, ?string $icon = null, string $priority = 'normal')
    {
        $this->sectionId = $sectionId;
        $this->title = $title;
        $this->message = $message;
        $this->link = $link;
        $this->icon = $icon ?? 'fa-bell';
        $this->priority = $priority;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $employees = Employee::where('section_id', $this->sectionId)
                ->where('status', 'active')
                ->whereHas('user')
                ->with('user')
                ->get();

            foreach ($employees as $employee) {
                if ($employee->user) {
                    Notification::create([
                        'user_id' => $employee->user->id,
                        'title' => $this->title,
                        'message' => $this->message,
                        'link' => $this->link,
                        'icon' => $this->icon,
                        'priority' => $this->priority,
                        'read_at' => null,
                    ]);
                }
            }

            Log::info('Section notification sent', [
                'section_id' => $this->sectionId,
                'title' => $this->title,
                'count' => $employees->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send section notification', [
                'section_id' => $this->sectionId,
                'title' => $this->title,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
