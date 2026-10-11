<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use Auditable;

    protected static function auditLogName(): string
    {
        return 'roles';
    }

    protected function auditAttributes(): array
    {
        return ['name', 'guard_name'];
    }
}
