<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasPermissions
{
    /**
     * Check if model has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Super admin and admin have all permissions
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return true;
        }

        // Check direct permission
        if ($user->hasPermissionTo($permission)) {
            return true;
        }

        // Check role-based permissions
        return $this->hasRolePermission($permission);
    }

    /**
     * Check if user has permission through role
     */
    protected function hasRolePermission(string $permission): bool
    {
        $user = Auth::user();
        
        foreach ($user->roles as $role) {
            if ($role->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all permissions for the model
     */
    public function getPermissions(): array
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $permissions = [];

        // Direct permissions
        foreach ($user->permissions as $permission) {
            $permissions[] = $permission->name;
        }

        // Role permissions
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->name;
            }
        }

        return array_unique($permissions);
    }

    /**
     * Check if user can perform action on this model
     */
    public function canPerformAction(string $action): bool
    {
        $permission = $this->getActionPermission($action);
        return $this->hasPermission($permission);
    }

    /**
     * Get permission name for action
     */
    protected function getActionPermission(string $action): string
    {
        $modelName = strtolower(class_basename(static::class));
        return "{$action} {$modelName}";
    }

    /**
     * Scope to only show records user can view
     */
    public function scopeWhereUserCanView($query)
    {
        if (!Auth::check()) {
            return $query->whereRaw('1 = 0');
        }

        $user = Auth::user();
        
        // Super admin and admin can see all
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return $query;
        }

        // Apply model-specific view restrictions
        return $this->applyViewRestrictions($query, $user);
    }

    /**
     * Apply view restrictions based on user roles
     */
    protected function applyViewRestrictions($query, $user)
    {
        // Override in child models
        return $query;
    }

    /**
     * Check if user can edit this specific record
     */
    public function canEdit(): bool
    {
        if (!$this->canPerformAction('update')) {
            return false;
        }

        // Check ownership or department restrictions
        return $this->checkEditRestrictions();
    }

    /**
     * Check edit restrictions for this record
     */
    protected function checkEditRestrictions(): bool
    {
        // Override in child models
        return true;
    }

    /**
     * Check if user can delete this record
     */
    public function canDelete(): bool
    {
        if (!$this->canPerformAction('delete')) {
            return false;
        }

        // Additional delete restrictions
        return $this->checkDeleteRestrictions();
    }

    /**
     * Check delete restrictions for this record
     */
    protected function checkDeleteRestrictions(): bool
    {
        // Override in child models
        return true;
    }

    /**
     * Get audit trail data for this model
     */
    public function getAuditData(): array
    {
        return [
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => Auth::id(),
            'action' => 'unknown',
            'old_values' => [],
            'new_values' => $this->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
    }

    /**
     * Log permission check
     */
    protected function logPermissionCheck(string $permission, bool $granted): void
    {
        if (config('app.debug')) {
            \Log::info("Permission Check", [
                'permission' => $permission,
                'granted' => $granted,
                'user_id' => Auth::id(),
                'model' => static::class,
                'model_id' => $this->id ?? null,
            ]);
        }
    }
}
