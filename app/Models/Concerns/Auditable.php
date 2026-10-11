<?php

namespace App\Models\Concerns;

use App\Services\ModelAuditLabels;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait Auditable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->resolveAuditableAttributes())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->resolveAuditLogName())
            ->setDescriptionForEvent(fn (string $event) => $this->resolveAuditDescription($event));
    }

    protected function resolveAuditableAttributes(): array
    {
        if (method_exists($this, 'auditAttributes')) {
            return $this->auditAttributes();
        }

        $config = config('model_audit.' . static::class);
        if (!empty($config['attributes'])) {
            return $config['attributes'];
        }

        $excluded = static::excludedAuditFields();

        if (!empty($this->fillable)) {
            return array_values(array_diff($this->fillable, $excluded));
        }

        try {
            return array_values(array_diff(
                Schema::getColumnListing($this->getTable()),
                $excluded
            ));
        } catch (\Throwable) {
            return [];
        }
    }

    protected function resolveAuditLogName(): string
    {
        if (method_exists(static::class, 'auditLogName')) {
            return static::auditLogName();
        }

        $config = config('model_audit.' . static::class);
        if (!empty($config['log_name'])) {
            return $config['log_name'];
        }

        return str($this->getTable())->singular()->replace('_', '-')->toString();
    }

    protected function resolveAuditDescription(string $event): string
    {
        try {
            $classReflection = new \ReflectionClass(static::class);
            $methodReflection = $classReflection->getMethod('getDescriptionForEvent');

            // If the method is defined in the same file as the concrete model class,
            // it is a model-specific override (not inherited from a trait).
            if ($methodReflection->getFileName() === $classReflection->getFileName()) {
                return $this->getDescriptionForEvent($event);
            }
        } catch (\ReflectionException) {
            // method does not exist at all
        }

        $label = ModelAuditLabels::get(static::class);
        $action = match ($event) {
            'created' => 'إنشاء',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            default => $event,
        };

        return "تم {$action} {$label}";
    }

    protected static function excludedAuditFields(): array
    {
        return [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'password',
            'remember_token',
            'email_verified_at',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ];
    }
}
