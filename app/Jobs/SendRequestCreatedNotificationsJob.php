<?php

namespace App\Jobs;

use App\Models\Request as RequestModel;
use App\Models\Notification;
use App\Models\RequestResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendRequestCreatedNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $requestId
    ) {}

    public function handle(): void
    {
        $request = RequestModel::with(['requestType', 'creator'])
            ->find($this->requestId);

        if (!$request) {
            Log::warning("SendRequestCreatedNotificationsJob: request {$this->requestId} not found");
            return;
        }

        $notifiedUserIds = [];
        $smsNotifiedEmployeeIds = [];

        $requestTypeName = $request->requestType->name ?? 'غير محدد';
        $message = "تم إسناد طلب جديد رقم: {$request->id} ({$requestTypeName}) إليك - يرجى مراجعته في بريد الطلبات";
        $link = route('requests.mail.waiting');

        $responses = RequestResponse::with('assignedEmployee.user')
            ->where('request_id', $request->id)
            ->where('response_type', 4)
            ->whereNotNull('assigned_employee_id')
            ->get();

        foreach ($responses as $response) {
            $user = $response->assignedEmployee?->user;
            if (!$user || in_array($user->id, $notifiedUserIds, true)) {
                continue;
            }

            try {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'request',
                    'title' => 'طلب جديد مسند إليك',
                    'message' => $message,
                    'link' => $link,
                    'icon' => 'fa-envelope',
                    'priority' => 'normal',
                    'is_read' => false,
                ]);
                $notifiedUserIds[] = $user->id;
            } catch (\Exception $e) {
                Log::error("SendRequestCreatedNotificationsJob: failed for employee {$response->assigned_employee_id}: " . $e->getMessage());
            }

            $employee = $response->assignedEmployee;
            $phone = $employee?->mobile ?: $user->mobile;
            if ($employee && $phone && !in_array((int) $employee->id, $smsNotifiedEmployeeIds, true)) {
                $this->sendSms($request, $requestTypeName, $employee, $phone);
                $smsNotifiedEmployeeIds[] = (int) $employee->id;
            }
        }

        Log::info("SendRequestCreatedNotificationsJob: request {$request->id}, notified " . count($notifiedUserIds) . " users");
    }

    private function sendSms(RequestModel $request, string $requestTypeName, $employee, string $phone): void
    {
        $config = config('services.tweetsms');
        if (empty($config['enabled'])) {
            return;
        }

        if (empty($config['username']) || empty($config['password']) || empty($config['sender'])) {
            Log::warning('TweetSMS is enabled but credentials or sender are missing.');
            return;
        }

        $message = "طلب جديد يحتاج للمعالجة. رقم الطلب: {$request->id}، النوع: {$requestTypeName}. يرجى الدخول إلى بريد الطلبات.";

        try {
            $response = Http::asForm()
                ->timeout((int) ($config['timeout'] ?? 10))
                ->get($config['base_url'], [
                    'comm' => 'sendsms',
                    'user' => $config['username'],
                    'pass' => $config['password'],
                    'to' => $phone,
                    'message' => $message,
                    'sender' => $config['sender'],
                ]);

            $body = trim($response->body());
            if (!$response->successful() || !preg_match('/(?:^|\\s)1(?:\\s|$)/', $body)) {
                Log::warning('TweetSMS did not confirm delivery for assigned request employee.', [
                    'request_id' => $request->id,
                    'employee_id' => $employee->id,
                    'http_status' => $response->status(),
                    'provider_response' => $body,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('TweetSMS request notification failed.', [
                'request_id' => $request->id,
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
