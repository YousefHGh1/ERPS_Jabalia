<?php
namespace App\Listeners;

use App\Events\EmployeeCreated;
use App\Events\EmployeeDeleted;
use App\Events\EmployeeUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogEmployeeActivity
{
    /**
     * Handle employee created event.
     */
    public function handleEmployeeCreated(EmployeeCreated $event): void
    {
        $this->logActivity('created', $event->employee, [
            'employee_id'     => $event->employee->id,
            'employee_name'   => $event->employee->name,
            'employee_number' => $event->employee->emp_number,
        ]);
    }

    /**
     * Generic handler called by the events dispatcher.
     */
    public function handle($event): void
    {
        if ($event instanceof EmployeeCreated) {
            $this->handleEmployeeCreated($event);
            return;
        }

        if ($event instanceof EmployeeUpdated) {
            $this->handleEmployeeUpdated($event);
            return;
        }

        if ($event instanceof EmployeeDeleted) {
            $this->handleEmployeeDeleted($event);
            return;
        }
    }

    /**
     * Fallback invokable for any dispatcher that attempts to __invoke the listener.
     */
    public function __invoke($event): void
    {
        $this->handle($event);
    }

    /**
     * Handle employee updated event.
     */
    public function handleEmployeeUpdated(EmployeeUpdated $event): void
    {
        $this->logActivity('updated', $event->employee, [
            'employee_id'   => $event->employee->id,
            'employee_name' => $event->employee->name,
        ]);
    }

    /**
     * Handle employee deleted event.
     */
    public function handleEmployeeDeleted(EmployeeDeleted $event): void
    {
        $this->logActivity('deleted', $event->employee, [
            'employee_id'   => $event->employee->id,
            'employee_name' => $event->employee->name,
        ]);
    }

    protected function logActivity(string $event, $employee, array $properties = []): void
    {
        // Determine action text
        $actionText = match($event) {
            'created' => 'إضافة',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            default => $event,
        };
        
        // Log to activity log with subject and causer
        activity()
            ->performedOn($employee)
            ->causedBy(Auth::user())
            ->withProperties($properties)
            ->log("تم {$actionText} الموظف: {$employee->name}");
        
        // Keep the original Log::info for debugging
        Log::info("Employee {$event}", array_merge($properties, [
            'user_id' => Auth::id(),
        ]));
    }
}
