<?php

namespace App\Listeners;

use App\Events\RequestCreated;
use App\Models\RequestWorkflow;
use App\Models\Employee;
use App\Notifications\RequestCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendRequestNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(RequestCreated $event)
    {
        try {
            $request = $event->request;

            // الحصول على جميع خطوات سير العمل لهذا النوع من الطلبات (حسب الأقسام sections)
            $workflowSteps = RequestWorkflow::with('section')
                ->where('request_type_id', $request->request_type_id)
                ->active()
                ->ordered()
                ->get();

            if ($workflowSteps->isEmpty()) {
                Log::info('No workflow steps found for request type: ' . $request->request_type_id);
                return;
            }

            // الحصول على معرفات الأقسام (sections) من خطوات سير العمل
            $sectionIds = $workflowSteps->pluck('section_id')
                ->unique()
                ->filter()
                ->values();

            if ($sectionIds->isEmpty()) {
                Log::info('No sections found for workflow steps');
                return;
            }

            // الحصول على الموظفين التابعين لهذه الأقسام
            $employees = $this->getEmployeesBySections($sectionIds->toArray());

            if ($employees->isEmpty()) {
                Log::info('No employees found for sections: ' . implode(', ', $sectionIds->toArray()));
                return;
            }

            // إرسال الإشعارات لجميع الموظفين
            foreach ($employees as $employee) {
                try {
                    if ($employee->user) {
                        $employee->user->notify(
                            new RequestCreatedNotification($request, $workflowSteps->toArray())
                        );
                        Log::info("Notification sent to employee: {$employee->name} (ID: {$employee->id})");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to employee {$employee->id}: " . $e->getMessage());
                }
            }

            Log::info("Request notifications sent successfully. Request ID: {$request->id}, Employees notified: {$employees->count()}");

        } catch (\Exception $e) {
            Log::error("Error in SendRequestNotifications: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * الحصول على الموظفين التابعين للأقسام (sections)
     */
    private function getEmployeesBySections(array $sectionIds): \Illuminate\Support\Collection
    {
        return Employee::with('user')
            ->whereIn('section_id', $sectionIds)
            ->where('status', 'active')
            ->get()
            ->filter(function ($employee) {
                return $employee->user !== null;
            });
    }

    public function failed(RequestCreated $event, \Throwable $exception)
    {
        Log::error('Request notification failed: ' . $exception->getMessage(), [
            'request_id' => $event->request->id,
            'exception' => $exception
        ]);
    }
}
