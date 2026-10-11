<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAuditTrail
{
    /**
     * Boot the trait
     */
    public static function bootHasAuditTrail()
    {
        static::created(function ($model) {
            $model->logAudit('created');
        });

        static::updated(function ($model) {
            $model->logAudit('updated');
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted');
        });
    }

    /**
     * Log audit trail entry
     */
    public function logAudit(string $action, array $metadata = []): void
    {
        $auditData = [
            'model_type' => static::class,
            'model_id' => $this->id,
            'action' => $action,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => array_merge($this->getAuditMetadata($action), $metadata),
        ];

        // Add old and new values for updates
        if ($action === 'updated' && $this->wasChanged()) {
            $auditData['old_values'] = $this->getOriginal();
            $auditData['new_values'] = $this->getChanges();
        }

        // Store audit entry
        $this->storeAuditEntry($auditData);
    }

    /**
     * Get audit metadata for action
     */
    protected function getAuditMetadata(string $action): array
    {
        $metadata = [];

        switch ($action) {
            case 'created':
                $metadata['created_at'] = $this->created_at;
                break;
            case 'updated':
                $metadata['updated_at'] = $this->updated_at;
                $metadata['changed_fields'] = array_keys($this->getChanges());
                break;
            case 'deleted':
                $metadata['deleted_at'] = now();
                break;
        }

        return $metadata;
    }

    /**
     * Store audit entry
     */
    protected function storeAuditEntry(array $auditData): void
    {
        try {
            // Use Laravel's activitylog if available
            if (class_exists('\Spatie\Activitylog\Facades\ActivityLog')) {
                activity()
                    ->performedOn($this)
                    ->causedBy(Auth::user())
                    ->withProperties($auditData)
                    ->log($auditData['action']);
            } else {
                // Fallback to database table
                \DB::table('audit_trails')->insert([
                    'model_type' => $auditData['model_type'],
                    'model_id' => $auditData['model_id'],
                    'action' => $auditData['action'],
                    'user_id' => $auditData['user_id'],
                    'ip_address' => $auditData['ip_address'],
                    'user_agent' => $auditData['user_agent'],
                    'old_values' => json_encode($auditData['old_values'] ?? []),
                    'new_values' => json_encode($auditData['new_values'] ?? []),
                    'metadata' => json_encode($auditData['metadata']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            \Log::error('Audit trail error: ' . $e->getMessage(), [
                'model' => static::class,
                'model_id' => $this->id,
                'action' => $auditData['action'],
            ]);
        }
    }

    /**
     * Get audit history for this model
     */
    public function getAuditHistory(): \Illuminate\Support\Collection
    {
        try {
            if (class_exists('\Spatie\Activitylog\Models\Activity')) {
                return \Spatie\Activitylog\Models\Activity()
                    ->where('subject_type', static::class)
                    ->where('subject_id', $this->id)
                    ->with('causer')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                return \DB::table('audit_trails')
                    ->where('model_type', static::class)
                    ->where('model_id', $this->id)
                    ->leftJoin('users', 'audit_trails.user_id', '=', 'users.id')
                    ->select('audit_trails.*', 'users.name as user_name')
                    ->orderBy('audit_trails.created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get formatted audit history
     */
    public function getFormattedAuditHistory(): array
    {
        $history = $this->getAuditHistory();
        $formatted = [];

        foreach ($history as $entry) {
            $formatted[] = [
                'id' => $entry->id ?? null,
                'action' => $entry->action,
                'user_name' => $entry->user_name ?? ($entry->causer->name ?? 'System'),
                'ip_address' => $entry->ip_address,
                'created_at' => $entry->created_at,
                'changes' => $this->formatAuditChanges($entry),
                'metadata' => $this->formatAuditMetadata($entry),
            ];
        }

        return $formatted;
    }

    /**
     * Format audit changes
     */
    protected function formatAuditChanges($entry): array
    {
        $changes = [];

        if (isset($entry->old_values) && isset($entry->new_values)) {
            $oldValues = is_string($entry->old_values) ? json_decode($entry->old_values, true) : $entry->old_values;
            $newValues = is_string($entry->new_values) ? json_decode($entry->new_values, true) : $entry->new_values;

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $this->formatAuditValue($oldValue),
                        'new' => $this->formatAuditValue($newValue),
                    ];
                }
            }
        }

        return $changes;
    }

    /**
     * Format audit metadata
     */
    protected function formatAuditMetadata($entry): array
    {
        $metadata = [];

        if (isset($entry->metadata)) {
            $metadata = is_string($entry->metadata) ? json_decode($entry->metadata, true) : $entry->metadata;
        } elseif (isset($entry->properties)) {
            $metadata = $entry->properties->toArray();
        }

        return $metadata;
    }

    /**
     * Format audit value for display
     */
    protected function formatAuditValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        if ($value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) $value;
    }

    /**
     * Check if field should be audited
     */
    protected function shouldAuditField(string $field): bool
    {
        // Don't audit sensitive fields
        $sensitiveFields = [
            'password',
            'remember_token',
            'api_token',
            'two_factor_secret',
        ];

        return !in_array($field, $sensitiveFields);
    }

    /**
     * Get fields to exclude from audit
     */
    protected function getExcludedAuditFields(): array
    {
        return [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token',
        ];
    }

    /**
     * Create audit trail table if it doesn't exist
     */
    public static function createAuditTable(): void
    {
        if (!\Schema::hasTable('audit_trails')) {
            \Schema::create('audit_trails', function ($table) {
                $table->id();
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->string('action');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('user_id');
                $table->index('action');
                $table->index('created_at');
            });
        }
    }
}
