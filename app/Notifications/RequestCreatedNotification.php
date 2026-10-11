<?php

namespace App\Notifications;

use App\Models\Request as RequestModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
class RequestCreatedNotification extends Notification
{
    use Queueable;

    protected $request;
    protected $workflowSteps;

    public function __construct(RequestModel $request, $workflowSteps = [])
    {
        $this->request = $request;
        $this->workflowSteps = $workflowSteps;
    }

    public function via($notifiable)
    {
        // إرسال فوري بدون queue - database للإشعار في الجرس
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        // رابط بريد الطلبات (قيد التنفيذ) ليتمكن الموظف من تعديل الحالة
        $url = route('requests.mail.waiting');
        return [
            'title' => 'طلب جديد',
            'message' => "تم إرسال طلب جديد رقم: {$this->request->id} لقسمك - يرجى مراجعته وتعديل الحالة",
            'request_id' => $this->request->id,
            'request_type' => $this->request->requestType->name ?? 'غير محدد',
            'created_by' => $this->request->creator->name ?? 'غير محدد',
            'url' => $url,
            'workflow_steps' => $this->workflowSteps,
        ];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('طلب جديد - ' . ($this->request->requestType->name ?? 'غير محدد'))
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم إرسال طلب جديد لقسمك في النظام:')
            ->line('رقم الطلب: ' . $this->request->id)
            ->line('نوع الطلب: ' . ($this->request->requestType->name ?? 'غير محدد'))
            ->line('نص الطلب: ' . Str::limit($this->request->text, 100))
            ->line('أنشأ بواسطة: ' . ($this->request->creator->name ?? 'غير محدد'))
            ->action('تعديل الحالة', route('requests.mail.waiting'))
            ->line('يرجى مراجعة الطلب وتعديل الحالة (موافقة/رفض/تعليق).');
    }

    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->request->id,
            'request_type' => $this->request->requestType->name ?? 'غير محدد',
            'message' => "تم إرسال طلب جديد رقم: {$this->request->id} لقسمك",
            'url' => route('requests.mail.waiting'),
        ];
    }
}
