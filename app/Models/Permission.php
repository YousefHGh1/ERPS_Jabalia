<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use Auditable;

    protected static function auditLogName(): string
    {
        return 'permissions';
    }

    protected function auditAttributes(): array
    {
        return ['name', 'guard_name'];
    }
}
