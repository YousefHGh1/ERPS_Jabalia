<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Arabic Permissions Sync Command
 *
 * Scans controllers and generates English permission keys
 * and syncs Arabic translations in lang/ar.json.
 */
class SyncControllerPermissions extends Command
{
    protected $signature = 'permissions:sync-controllers {--force : Force recreate permissions}';

    protected $description = 'Scan controllers and generate Arabic permissions matching sidebar config.';

    protected string $langFilePath;

    /**
     * Action verb mapping to Arabic
     */
    protected const VERB_MAP = [
        'index'                => 'عرض',
        'create'               => 'إنشاء',
        'store'                => 'حفظ',
        'show'                 => 'عرض',
        'edit'                 => 'تعديل',
        'update'               => 'تحديث',
        'destroy'              => 'حذف',
        'delete'               => 'حذف',
        'payment'              => 'دفع',
        'monitor'              => 'مراقبة',
        'manage'               => 'إدارة',
        'view'                 => 'عرض',
        'export'               => 'تصدير',
        'download'             => 'تحميل',
        'print'                => 'طباعة',
        'cancel'               => 'إلغاء',
        'renew'                => 'تجديد',
        'add'                  => 'إضافة',
        'remove'               => 'إزالة',
        'accept'               => 'قبول',
        'reject'               => 'رفض',
        'approve'              => 'موافقة',
        'clear'                => 'مسح',
        'refresh'              => 'تحديث',
        'send'                 => 'إرسال',
        'receive'              => 'استلام',
        'accrue'               => 'تراكم',
        'archive'              => 'أرشفة',
        'restore'              => 'استعادة',
        'duplicate'            => 'نسخ',
        'backup'               => 'نسخ احتياطي',
        'upload'               => 'رفع',
        'import'               => 'استيراد',
        'sync'                 => 'مزامنة',
        'addPayment'           => 'إضافة دفعة',
        'addPartner'           => 'إضافة شريك',
        'clearCacheds'         => 'مسح الذاكرة المؤقتة',
        'clearRequests'        => 'مسح الطلبات',
        'refreshStats'         => 'تحديث الإحصائيات',
        'accrueAnnualFees'     => 'تراكم الرسوم السنوية',
        'acceptPermission'     => 'قبول الإذن',
        'acceptHoliday'        => 'قبول الإجازة',
        'attendanceCalendar'   => 'تقويم الحضور',
        'administrative'       => 'إداري',
        'all'                  => 'الكل',
        'ajaxJebaiaDataNotice' => 'بيانات الإخطار',
        'ajaxNameDataNotice'   => 'بيانات الاسم',
        'ajaxUserDataNotice'   => 'بيانات المستخدم',
        'apiIndex'             => 'واجهة برمجية',
    ];

    /**
     * Resource names in Arabic (snake_case format)
     * These match the sidebar permission naming convention
     */
    protected const RESOURCE_MAP = [
        'categories'           => 'التصنيفات',
        'bonus'                => 'المكافآت',
        'bonuses'              => 'المكافآت',
        'jobs'                 => 'الوظائف',
        'departments'          => 'الدوائر',
        'sections'             => 'الأقسام',
        'branches'             => 'الشعب',
        'employees'            => 'الموظفين',
        'departmentdirector'   => 'مديرين الدوائر',
        'department_directors' => 'مديرين الدوائر',
        'sectiondirector'      => 'مديرين الأقسام',
        'section_directors'    => 'مديرين الأقسام',
        'branchdirector'       => 'مديرين الشعب',
        'branch_directors'     => 'مديرين الشعب',
        'holidays'             => 'العطلات',
        'main_holiday'         => 'العطلات الرسمية',
        'main_holidays'        => 'العطلات الرسمية',
        'employee_holiday'     => 'إجازات الموظفين',
        'employee_holidays'    => 'إجازات الموظفين',
        'employee_permission'  => 'أذونات الموظفين',
        'employee_permissions' => 'أذونات الموظفين',
        'shifts'               => 'الورديات',
        'zk'                   => 'الحضور والانصراف',
        'vehicles'             => 'المركبات',
        'permissions'          => 'الصلاحيات',
        'roles'                => 'الأدوار',
        'users'                => 'المستخدمين',
        'mokalafs'             => 'المكلفين',
        'requests'             => 'الطلبات',
        'request_types'        => 'أنواع الطلبات',
        'craft_licenses'       => 'تراخيص الحرف',
        'engineerings'         => 'الملفات الهندسية',
        'legacy_engineerings'  => 'الملفات الهندسية القديمة',
        'reservations'         => 'الحجوزات',
        'notices'              => 'الإخطارات',
        'imprisonment_orders'  => 'أوامر الحبس',
        'central_treasury'     => 'الصندوق المركزي',
        'external_collections' => 'التحصيل الخارجي',
        'cacheds'              => 'الذاكرة المؤقتة',
        'caches'               => 'التخزين المؤقت',
        'attachments'          => 'المرفقات',
        'request_mails'        => 'بريد الطلبات',
        'dashboard'            => 'لوحة التحكم',
        'admin_dashboards'     => 'لوحات التحكم الإدارية',
        'performance'          => 'الأداء',
        'notices'              => 'الإخطارات',
        'employee_portals'     => 'بوابات الموظفين',
    ];

    public function handle(): int
    {
        $this->langFilePath = lang_path('ar.json');

        $baseNamespace   = 'App\\Http\\Controllers\\';
        $controllersPath = app_path('Http/Controllers');

        $files              = $this->getPhpFiles($controllersPath);
        $createdPermissions = 0;
        $updatedPermissions = 0;
        $createdRoles       = 0;
        $syncedPermissions  = [];
        $syncedResources    = [];

        $this->info('🔄 بدء مزامنة الصلاحيات باستخدام المفاتيح الإنجليزية وترجمتها إلى العربية...');
        $this->newLine();

        foreach ($files as $file) {
            $relative  = str_replace($controllersPath . DIRECTORY_SEPARATOR, '', $file);
            $className = $baseNamespace . str_replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''], $relative);

            if (! class_exists($className)) {
                require_once $file;
                if (! class_exists($className)) {
                    continue;
                }
            }

            $ref = new ReflectionClass($className);
            if ($ref->isAbstract() || ! $ref->isSubclassOf(\App\Http\Controllers\Controller::class)) {
                continue;
            }

            $baseName = class_basename($className);
            if (! Str::endsWith($baseName, 'Controller')) {
                continue;
            }

            // استخراج اسم الـ resource
            $resourceBase = Str::beforeLast($baseName, 'Controller');
            if ($resourceBase === '') {
                continue;
            }

            $resource = Str::snake(Str::pluralStudly($resourceBase));

            // الحصول على الاسم العربي
            $resourceArabic = $this->getArabicResourceName($resource);

            // جمع الـ methods
            $methods = collect($ref->getMethods(ReflectionMethod::IS_PUBLIC))
                ->filter(function (ReflectionMethod $method) use ($ref) {
                    if ($method->class !== $ref->getName()) {
                        return false;
                    }
                    $name = $method->getName();
                    return $name !== '__construct';
                })
                ->map(fn(ReflectionMethod $method) => $method->getName())
                ->values()
                ->all();

            if (empty($methods)) {
                continue;
            }

            $guard             = 'web';
            $permissionsToSync = [];

            $this->line("📁 {$baseName}");

            foreach ($methods as $methodName) {
                $actionKey   = $this->mapMethodToAction($methodName);
                $verbArabic  = null;
                $translation = null;

                if ($actionKey === null) {
                    // صلاحية اسم الفنكشن مباشرة (بالإنجليزي)
                    $permName    = "{$methodName} {$resource}";
                    $verbArabic  = self::VERB_MAP[$methodName] ?? $methodName;
                    $translation = "{$verbArabic} {$resourceArabic}";
                } else {
                    // صلاحية إنجليزية قياسية مع ترجمة عربية
                    $permName    = "{$actionKey} {$resource}";
                    $verbArabic  = self::VERB_MAP[$actionKey] ?? $actionKey;
                    $translation = "{$verbArabic} {$resourceArabic}";
                }

                // إنشاء/تحديث الصلاحية
                $existing = Permission::where('name', $permName)->where('guard_name', $guard)->first();

                if (! $existing) {
                    $oldArabicName = $translation;
                    if ($oldArabicName !== $permName) {
                        $oldArabicPermission = Permission::where('name', $oldArabicName)
                            ->where('guard_name', $guard)
                            ->first();

                        if ($oldArabicPermission) {
                            $oldArabicPermission->update(['name' => $permName]);
                            $existing = $oldArabicPermission;
                            $this->info("   🔧 تم إعادة تسمية صلاحية قديمة: {$oldArabicName} -> {$permName}");
                        }
                    }
                }

                if (! $existing) {
                    Permission::create([
                        'name'       => $permName,
                        'guard_name' => $guard,
                    ]);
                    $createdPermissions++;
                    $this->info("   ➕ {$permName}");
                } elseif ($this->option('force')) {
                    $existing->update(['name' => $permName]);
                    $updatedPermissions++;
                    $this->comment("   ✏️  {$permName}");
                }

                $this->updateTranslationFile($permName, $translation);
                $permissionsToSync[] = $permName;
                $syncedPermissions[] = $permName;
            }

            // إنشاء دور manager لهذا الـ resource
            if (! empty($permissionsToSync)) {
                $permissions = Permission::whereIn('name', $permissionsToSync)
                    ->where('guard_name', $guard)
                    ->get();

                if ($permissions->isNotEmpty()) {
                    $roleNameAr = $this->getArabicManagerRoleName($resourceArabic);
                    $role       = Role::firstOrCreate(
                        ['name' => $roleNameAr, 'guard_name' => $guard],
                        ['name' => $roleNameAr, 'guard_name' => $guard]
                    );

                    $role->syncPermissions($permissions);
                    $createdRoles++;
                    $syncedResources[] = $resource;
                    $this->comment("   👤 دور: {$roleNameAr}");
                }
            }

            $this->newLine();
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════');
        $this->info("✅ انتهى المزامنة:");
        $this->info("   📝 صلاحيات جديدة: {$createdPermissions}");
        $this->info("   📝 صلاحيات محدثة: {$updatedPermissions}");
        $this->info("   👥 أدوار manager: {$createdRoles}");

        $deletedPermissions = $this->cleanupOldArabicPermissions($syncedPermissions);
        if ($deletedPermissions > 0) {
            $this->info("   🧹 صلاحيات عربية قديمة محذوفة: {$deletedPermissions}");
        }

        $deletedRoles = $this->cleanupOldEnglishManagerRoles($syncedResources);
        if ($deletedRoles > 0) {
            $this->info("   🧹 أدوار مدير إنجليزية قديمة محذوفة: {$deletedRoles}");
        }

        $this->info('═══════════════════════════════════════════');

        // ربط Admin بجميع الصلاحيات (عدم التكرار)
        $this->syncAdminWithAllPermissions();

        return self::SUCCESS;
    }

    /**
     * Sync Admin role with all permissions (avoid duplicates)
     */
    protected function syncAdminWithAllPermissions(): void
    {
        $admin = Role::findByName('admin', 'web');

        if ($admin) {
            // الحصول على جميع الصلاحيات الحالية
            $allPermissions = Permission::where('guard_name', 'web')->get();

            // مزامنة Admin مع جميع الصلاحيات (firstOrCreate يمنع التكرار)
            $admin->syncPermissions($allPermissions);

            $totalCount = $allPermissions->count();
            $this->info("🔑 تم ربط Admin بـ {$totalCount} صلاحية");
        } else {
            $this->warn("⚠️ دور admin غير موجود!");
        }
    }

    /**
     * Map controller method to standardized action
     */
    protected function mapMethodToAction(string $method): ?string
    {
        return match ($method) {
            'index'  => 'index',
            'create' => 'create',
            'store'  => 'store',
            'show'   => 'show',
            'edit'   => 'edit',
            'update' => 'update',
            'destroy', 'delete' => 'delete',
            default  => null,
        };
    }

    /**
     * Get Arabic translation for a permission name
     */
    protected function getPermissionTranslation(string $permName, string $resourceArabic): string
    {
        [$action, $resource] = explode(' ', $permName, 2);
        $verbArabic          = self::VERB_MAP[$action] ?? $action;

        return "{$verbArabic} {$resourceArabic}";
    }

    protected function getArabicManagerRoleName(string $resourceArabic): string
    {
        return "مدير {$resourceArabic}";
    }

    /**
     * Remove old Arabic-named permissions that were replaced by English keys.
     */
    protected function cleanupOldArabicPermissions(array $syncedPermissions): int
    {
        $deleted           = 0;
        $syncedPermissions = array_unique($syncedPermissions);

        foreach ($syncedPermissions as $permName) {
            if (! str_contains($permName, ' ')) {
                continue;
            }

            [$action, $resource] = explode(' ', $permName, 2);
            $resourceArabic      = $this->getArabicResourceName($resource);
            $translation         = $this->getPermissionTranslation($permName, $resourceArabic);

            if ($translation === $permName) {
                continue;
            }

            $oldArabicPermission = Permission::where('name', $translation)
                ->where('guard_name', 'web')
                ->first();

            if ($oldArabicPermission) {
                $oldArabicPermission->delete();
                $deleted++;
                $this->info("   🧹 تم حذف صلاحية عربية قديمة: {$translation}");
            }
        }

        return $deleted;
    }

    /**
     * Remove old English manager roles after Arabic managers are synced.
     */
    protected function cleanupOldEnglishManagerRoles(array $syncedResources): int
    {
        $deleted         = 0;
        $syncedResources = array_unique($syncedResources);

        if (empty($syncedResources)) {
            return $deleted;
        }

        $oldRoleNames = array_map(fn(string $resource) => "{$resource}_manager", $syncedResources);

        $roles = Role::where('guard_name', 'web')
            ->whereIn('name', $oldRoleNames)
            ->get();

        foreach ($roles as $role) {
            $role->delete();
            $deleted++;
            $this->info("   🧹 تم حذف دور مدير إنجليزي قديم: {$role->name}");
        }

        return $deleted;
    }

    /**
     * Get Arabic name for a resource
     */
    protected function getArabicResourceName(string $resource): string
    {
        // التحقق من resource map
        if (isset(self::RESOURCE_MAP[$resource])) {
            return self::RESOURCE_MAP[$resource];
        }

        // محاولة إيجاد المفرد في الخريطة
        $singular = Str::singular($resource);
        foreach (self::RESOURCE_MAP as $key => $arabic) {
            if ($key === $singular || Str::singular($key) === $singular) {
                return $arabic . 'ات'; // جمع مؤنث
            }
        }

        // إرجاع الاسم الإنجليزي مع إضافة ات للجمع
        return $resource;
    }

    /**
     * Get all PHP files under a directory recursively
     *
     * @return array<string>
     */
    protected function getPhpFiles(string $path): array
    {
        $files    = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Update ar.json translation file with new permissions
     */
    protected function updateTranslationFile(string $permName, string $translation): void
    {
        if (! isset($this->langFilePath)) {
            return;
        }

        if (! File::exists($this->langFilePath)) {
            return;
        }

        $translations = json_decode(File::get($this->langFilePath), true);

        if (! is_array($translations)) {
            $translations = [];
        }

        if (! isset($translations[$permName]) || $translations[$permName] !== $translation) {
            $translations[$permName] = $translation;
            File::put($this->langFilePath, json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $this->info("   📝 تم إضافة الترجمة: {$permName} => {$translation}");
        }
    }
}
