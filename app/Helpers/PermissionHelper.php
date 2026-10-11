<?php

use Illuminate\Support\Str;

if (! function_exists('load_permission_translations')) {
    /**
     * تحميل ملف ترجمة الصلاحيات مباشرة
     */
    function load_permission_translations(): array
    {
        $path = base_path('lang/ar/permissions.php');
        if (file_exists($path)) {
            $data = require $path;
            return is_array($data) ? $data : [];
        }
        return [];
    }
}

if (! function_exists('perm_name_ar')) {
    /**
     * ترجمة اسم الصلاحية إلى العربية
     *
     * @param string $name  اسم الصلاحية بالإنجليزية (مثلاً: "index employees")
     * @return string الاسم بالعربية
     */
    function perm_name_ar(string $name): string
    {
        static $translations = null;

        if ($translations === null) {
            $translations = load_permission_translations();
        }

        // إذا كان هناك ترجمة كاملة مطابقة
        if (isset($translations[$name]) && is_string($translations[$name])) {
            return $translations[$name];
        }

        // فصل الـ action والـ resource
        $parts = explode(' ', $name, 2);
        $action = $parts[0] ?? $name;
        $resource = $parts[1] ?? '';

        $actionAr = $translations['actions'][$action] ?? $action;
        $resourceAr = $translations['resources'][$resource] ?? $resource;

        return trim("{$actionAr} {$resourceAr}");
    }
}

if (! function_exists('role_name_ar')) {
    /**
     * ترجمة اسم الدور إلى العربية
     */
    function role_name_ar(string $name): string
    {
        if ($name === 'admin') {
            return 'مدير النظام';
        }

        if ($name === 'super-admin') {
            return 'المدير الأعلى';
        }

        if ($name === 'super_user') {
            return 'مستخدم متقدم';
        }

        if ($name === 'employee') {
            return 'موظف';
        }

        // الأسماء المخصصة للأدوار من lang/ar.json (مفتاح roles.*)
        $roleKey = 'roles.' . $name;
        $customAr = __($roleKey);
        if ($customAr !== $roleKey) {
            return $customAr;
        }

        static $translations = null;
        if ($translations === null) {
            $translations = load_permission_translations();
        }

        // البحث في resources مباشرة (اسم الدور = اسم الـ resource)
        $resourceAr = $translations['resources'][$name] ?? null;
        if ($resourceAr) {
            return 'مدير ' . $resourceAr;
        }

        return $name;
    }
}

if (! function_exists('get_role_badge_class')) {
    /**
     * الحصول على class البadge المناسب للدور
     */
    function get_role_badge_class(string $roleName): string
    {
        $adminRoles = ['admin', 'super-admin', 'super_user'];
        $managerRoles = ['manager', 'manager'];
        
        if (in_array($roleName, $adminRoles)) {
            return 'badge-danger';
        }
        
        if (str_contains($roleName, 'manager')) {
            return 'badge-primary';
        }
        
        if ($roleName === 'employee') {
            return 'badge-info';
        }
        
        return 'badge-secondary';
    }
}

if (! function_exists('get_permission_badge_class')) {
    /**
     * الحصول على class البadge المناسب للصلاحية
     */
    function get_permission_badge_class(string $permissionName): string
    {
        if (str_starts_with($permissionName, 'index') || str_starts_with($permissionName, 'view')) {
            return 'badge-info';
        }
        
        if (str_starts_with($permissionName, 'store') || str_starts_with($permissionName, 'create')) {
            return 'badge-success';
        }
        
        if (str_starts_with($permissionName, 'update') || str_starts_with($permissionName, 'edit')) {
            return 'badge-warning';
        }
        
        if (str_starts_with($permissionName, 'delete') || str_starts_with($permissionName, 'destroy')) {
            return 'badge-danger';
        }
        
        return 'badge-secondary';
    }
}

if (! function_exists('is_basic_role')) {
    /**
     * التحقق مما إذا كان الدور من الأدوار الأساسية
     */
    function is_basic_role(string $roleName): bool
    {
        $basicRoles = ['admin', 'super-admin', 'super_user', 'employee'];
        return in_array($roleName, $basicRoles);
    }
}

if (! function_exists('is_section_role')) {
    /**
     * التحقق مما إذا كان الدور من أدوار الأقسام
     */
    function is_section_role(string $roleName): bool
    {
        return str_contains($roleName, 'manager') || 
               str_contains($roleName, 'affairs') ||
               str_contains($roleName, 'director');
    }
}

if (! function_exists('getCategoryBadgeClass')) {
    /**
     * الحصول على class البadge المناسب لفئة الصلاحيات
     */
    function getCategoryBadgeClass(string $category): string
    {
        return match($category) {
            'صلاحيات العرض' => 'badge-info',
            'صلاحيات الإدارة' => 'badge-dark',
            'صلاحيات الفهرسة' => 'badge-primary',
            'صلاحيات الإضافة' => 'badge-success',
            'صلاحيات التعديل' => 'badge-warning',
            'صلاحيات الحذف' => 'badge-danger',
            'صلاحيات أخرى' => 'badge-secondary',
            default => 'badge-primary',
        };
    }
}
