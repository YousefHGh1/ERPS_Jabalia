<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

trait HasStatusTransitions
{
    /**
     * Boot the trait
     */
    public static function bootHasStatusTransitions()
    {
        static::saving(function ($model) {
            if ($model->isDirty('status')) {
                $model->validateStatusTransition($model->getOriginal('status'), $model->status);
            }
        });

        static::saved(function ($model) {
            if ($model->wasChanged('status')) {
                $model->handleStatusTransition($model->getOriginal('status'), $model->status);
            }
        });
    }

    /**
     * Validate status transition
     */
    public function validateStatusTransition($oldStatus, $newStatus): void
    {
        $allowedTransitions = $this->getAllowedStatusTransitions();
        
        if (!isset($allowedTransitions[$oldStatus])) {
            throw new \InvalidArgumentException("Invalid old status: {$oldStatus}");
        }

        if (!in_array($newStatus, $allowedTransitions[$oldStatus])) {
            throw new \InvalidArgumentException(
                "Cannot transition from {$oldStatus} to {$newStatus}. " .
                "Allowed transitions: " . implode(', ', $allowedTransitions[$oldStatus])
            );
        }
    }

    /**
     * Handle status transition
     */
    public function handleStatusTransition($oldStatus, $newStatus): void
    {
        // Log the transition
        $this->logStatusTransition($oldStatus, $newStatus);

        // Trigger transition-specific actions
        $this->performTransitionActions($oldStatus, $newStatus);

        // Send notifications
        $this->sendStatusNotifications($oldStatus, $newStatus);

        // Fire events
        $this->fireStatusTransitionEvents($oldStatus, $newStatus);
    }

    /**
     * Get allowed status transitions
     */
    protected function getAllowedStatusTransitions(): array
    {
        // Override in child models
        return [
            'draft' => ['pending', 'cancelled'],
            'pending' => ['approved', 'rejected', 'cancelled'],
            'approved' => ['completed', 'cancelled'],
            'rejected' => ['pending', 'cancelled'],
            'completed' => [], // Final state
            'cancelled' => [], // Final state
        ];
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        $statusNames = $this->getStatusDisplayNames();
        return $statusNames[$this->status] ?? $this->status;
    }

    /**
     * Get status display names
     */
    protected function getStatusDisplayNames(): array
    {
        // Override in child models
        return [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor(): string
    {
        $statusColors = $this->getStatusColors();
        return $statusColors[$this->status] ?? 'secondary';
    }

    /**
     * Get status colors
     */
    protected function getStatusColors(): array
    {
        // Override in child models
        return [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'primary',
            'cancelled' => 'dark',
        ];
    }

    /**
     * Check if status is final
     */
    public function isFinalStatus(): bool
    {
        $allowedTransitions = $this->getAllowedStatusTransitions();
        return empty($allowedTransitions[$this->status] ?? []);
    }

    /**
     * Check if status allows editing
     */
    public function isEditable(): bool
    {
        $editableStatuses = $this->getEditableStatuses();
        return in_array($this->status, $editableStatuses);
    }

    /**
     * Get editable statuses
     */
    protected function getEditableStatuses(): array
    {
        // Override in child models
        return ['draft', 'pending'];
    }

    /**
     * Check if status allows deletion
     */
    public function isDeletable(): bool
    {
        $deletableStatuses = $this->getDeletableStatuses();
        return in_array($this->status, $deletableStatuses);
    }

    /**
     * Get deletable statuses
     */
    protected function getDeletableStatuses(): array
    {
        // Override in child models
        return ['draft', 'cancelled'];
    }

    /**
     * Get next possible statuses
     */
    public function getNextPossibleStatuses(): array
    {
        $allowedTransitions = $this->getAllowedStatusTransitions();
        return $allowedTransitions[$this->status] ?? [];
    }

    /**
     * Get status transition history
     */
    public function getStatusHistory(): \Illuminate\Support\Collection
    {
        if (method_exists($this, 'getAuditHistory')) {
            return $this->getAuditHistory()
                ->where('action', 'status_changed')
                ->map(function ($entry) {
                    $metadata = $this->formatAuditMetadata($entry);
                    return [
                        'from_status' => $metadata['from_status'] ?? null,
                        'to_status' => $metadata['to_status'] ?? null,
                        'user_name' => $entry->user_name ?? ($entry->causer->name ?? 'System'),
                        'created_at' => $entry->created_at,
                        'notes' => $metadata['notes'] ?? null,
                    ];
                });
        }

        return collect([]);
    }

    /**
     * Log status transition
     */
    protected function logStatusTransition($oldStatus, $newStatus): void
    {
        if (method_exists($this, 'logAudit')) {
            $this->logAudit('status_changed', [
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'transition_time' => now(),
            ]);
        }
    }

    /**
     * Perform transition-specific actions
     */
    protected function performTransitionActions($oldStatus, $newStatus): void
    {
        // Override in child models to implement custom actions
        switch ($newStatus) {
            case 'approved':
                $this->onApproved($oldStatus);
                break;
            case 'rejected':
                $this->onRejected($oldStatus);
                break;
            case 'completed':
                $this->onCompleted($oldStatus);
                break;
            case 'cancelled':
                $this->onCancelled($oldStatus);
                break;
        }
    }

    /**
     * Action when status is approved
     */
    protected function onApproved($oldStatus): void
    {
        // Override in child models
    }

    /**
     * Action when status is rejected
     */
    protected function onRejected($oldStatus): void
    {
        // Override in child models
    }

    /**
     * Action when status is completed
     */
    protected function onCompleted($oldStatus): void
    {
        // Override in child models
    }

    /**
     * Action when status is cancelled
     */
    protected function onCancelled($oldStatus): void
    {
        // Override in child models
    }

    /**
     * Send status notifications
     */
    protected function sendStatusNotifications($oldStatus, $newStatus): void
    {
        // Override in child models to implement notifications
    }

    /**
     * Fire status transition events
     */
    protected function fireStatusTransitionEvents($oldStatus, $newStatus): void
    {
        $eventName = $this->getStatusTransitionEventName($newStatus);
        event($eventName, [$this, $oldStatus, $newStatus]);
    }

    /**
     * Get status transition event name
     */
    protected function getStatusTransitionEventName($newStatus): string
    {
        $modelBaseName = strtolower(class_basename(static::class));
        return "{$modelBaseName}.status.{$newStatus}";
    }

    /**
     * Transition to new status
     */
    public function transitionTo(string $newStatus, array $data = []): bool
    {
        $this->validateStatusTransition($this->status, $newStatus);
        
        $this->status = $newStatus;
        
        // Add any additional data
        foreach ($data as $key => $value) {
            if ($this->isFillable($key)) {
                $this->$key = $value;
            }
        }
        
        return $this->save();
    }

    /**
     * Bulk transition multiple records
     */
    public static function bulkTransition(array $ids, string $newStatus, array $data = []): int
    {
        $count = 0;
        
        foreach ($ids as $id) {
            $model = static::find($id);
            if ($model && $model->transitionTo($newStatus, $data)) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Get status statistics
     */
    public static function getStatusStatistics(): array
    {
        $instance = new static;
        $statusNames = $instance->getStatusDisplayNames();
        $statusColors = $instance->getStatusColors();
        
        $statistics = [];
        
        foreach ($statusNames as $status => $displayName) {
            $count = static::where('status', $status)->count();
            $statistics[$status] = [
                'name' => $displayName,
                'count' => $count,
                'color' => $statusColors[$status] ?? 'secondary',
                'percentage' => static::count() > 0 ? round(($count / static::count()) * 100, 1) : 0,
            ];
        }
        
        return $statistics;
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope by multiple statuses
     */
    public function scopeByStatuses($query, array $statuses)
    {
        return $query->whereIn('status', $statuses);
    }

    /**
     * Scope excluding final statuses
     */
    public function scopeNotFinal($query)
    {
        $finalStatuses = [];
        $allowedTransitions = $this->getAllowedStatusTransitions();
        
        foreach ($allowedTransitions as $status => $transitions) {
            if (empty($transitions)) {
                $finalStatuses[] = $status;
            }
        }
        
        return $query->whereNotIn('status', $finalStatuses);
    }
}
