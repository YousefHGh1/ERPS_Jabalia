<?php

namespace App\Services;

use App\Services\ModelAuditLabels;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class AuditTrailService
{
    public function getLogsForUser(?User $user, int $limit = 100): Collection
    {
        if (!$user) {
            return collect();
        }

        try {
            if (class_exists(Activity::class)) {
                return Activity::query()
                    ->where(function ($query) use ($user) {
                        // عمليات قام بها الموظف مباشرة
                        $query->where('causer_id', $user->id)
                              ->where('causer_type', get_class($user));
                    })
                    ->orWhere(function ($query) use ($user) {
                        // عمليات على بيانات الموظف نفسه
                        $query->where('subject_type', 'App\\Models\\Employee')
                              ->whereJsonContains('properties->employee_id', $user->employee_id);
                    })
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get()
                    ->map(fn ($activity) => $this->normalizeSpatieActivity($activity));
            }

            if (Schema::hasTable('audit_trails')) {
                return DB::table('audit_trails')
                    ->where('user_id', $user->id)
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get()
                    ->map(fn ($log) => $this->normalizeAuditTrailRow($log));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to fetch audit logs: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }

        return collect();
    }

    private function normalizeSpatieActivity(Activity $activity): object
    {
        return (object) [
            'action' => $activity->event ?? $activity->description,
            'table_name' => $this->getSubjectLabel($activity->subject_type),
            'description' => $activity->description,
            'properties' => $activity->properties,
            'created_at' => $activity->created_at,
        ];
    }

    private function normalizeAuditTrailRow(object $log): object
    {
        return (object) [
            'action' => $log->action,
            'table_name' => $this->getSubjectLabel($log->model_type ?? null),
            'description' => $log->description ?? null,
            'properties' => $log->properties ?? null,
            'created_at' => $log->created_at,
        ];
    }

    private function getSubjectLabel(?string $subjectType): string
    {
        if (!$subjectType) {
            return '-';
        }

        return ModelAuditLabels::get($subjectType);
    }
}
