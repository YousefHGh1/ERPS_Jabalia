<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AutoGeneratePermissions extends Command
{
    protected $signature = 'permissions:auto-generate
        {--force : حذف القديم وإعادة إنشاء}
        {--assign-user=1 : معرف المستخدم لإعطائه دور admin}';

    protected $description = 'حذف كل الصلاحيات والأدوار وإنشاؤها تلقائياً من الكنترولرات';

    /**
     * Controllers عامة لا تمثل CRUD resource — يتم تخطيها
     */
    protected const SKIP_CONTROLLERS = [
        'Controller',
        'HomeController',
        'AdminDashboardController',
        'RealTimeDashboardController',
        'Controller',
    ];

    public function handle(): int
    {
        $this->info('🚀 بدء عملية إعادة إنشاء الصلاحيات والأدوار...');

        if (! $this->option('force')) {
            $this->warn('⚠️ استخدم --force لحذف القديم وإعادة الإنشاء');
            return self::FAILURE;
        }

        if (! $this->confirm('⚠️ سيتم حذف جميع الصلاحيات والأدوار الحالية. هل أنت متأكد؟', true)) {
            return self::FAILURE;
        }

        // 1. حذف القديم + تنظيف الكاش
        $this->clearOldPermissions();

        // 2. إنشاء الجديد
        $stats = $this->generateFromControllers();

        // 3. إنشاء دور admin
        $this->createAdminRole();

        // 4. إعطاء admin للمستخدم
        $this->assignAdminToUser();

        // 5. طباعة ملخص
        $this->printSummary($stats);

        return self::SUCCESS;
    }

    protected function clearOldPermissions(): void
    {
        $this->info('🧹 جارِ حذف الصلاحيات والأدوار القديمة...');

        // مسح كاش Spatie Permission
        Cache::forget('spatie.permission.cache');
        Cache::forget('spatie.permission.cache.roles');
        Cache::forget('spatie.permission.cache.permissions');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('✅ تم حذف جميع الصلاحيات والأدوار.');
    }

    protected function generateFromControllers(): array
    {
        $controllersPath = app_path('Http/Controllers');
        $files = File::allFiles($controllersPath);

        $createdPermissions = 0;
        $skippedPermissions = 0;
        $createdRoles = 0;
        $resourcePermissions = [];

        $this->info('🔍 جارِ فحص الكنترولرات...');

        foreach ($files as $file) {
            $className = $this->getClassFromFile($file);
            if (! $className || ! class_exists($className)) {
                continue;
            }

            $ref = new ReflectionClass($className);
            if ($ref->isAbstract() || ! $ref->isSubclassOf(\App\Http\Controllers\Controller::class)) {
                continue;
            }

            $shortName = $ref->getShortName();
            if (! Str::endsWith($shortName, 'Controller')) {
                continue;
            }

            // تخطي الـ Controllers العامة
            if (in_array($shortName, self::SKIP_CONTROLLERS)) {
                $this->line("⏭️  {$shortName} (مُتجاوز)");
                continue;
            }

            // استخراج اسم الـ resource
            $resourceBase = Str::beforeLast($shortName, 'Controller');
            $resource = Str::snake(Str::pluralStudly($resourceBase));

            // تخطي الـ Controllers التي لا تمثل resources واضحة
            if (in_array($resource, ['controllers', 'home', 'dashboards', 'admins', 'auth', 'passwords', 'forgot', 'reset', 'cached'])) {
                continue;
            }

            $this->line("📁 {$shortName} → resource: {$resource}");

            $methods = collect($ref->getMethods(ReflectionMethod::IS_PUBLIC))
                ->filter(function (ReflectionMethod $m) use ($className) {
                    return $m->class === $className
                        && $m->getName() !== '__construct'
                        && ! str_starts_with($m->getName(), '__');
                })
                ->map(fn (ReflectionMethod $m) => $m->getName())
                ->values();

            if ($methods->isEmpty()) {
                continue;
            }

            $permsForResource = [];

            foreach ($methods as $methodName) {
                $permName = "{$methodName} {$resource}";

                $permission = Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web'],
                    ['name' => $permName, 'guard_name' => 'web']
                );

                if ($permission->wasRecentlyCreated) {
                    $createdPermissions++;
                    $this->line("   ➕ {$permName}");
                } else {
                    $this->line("   ⚠️  {$permName} (موجودة مسبقاً)");
                    $skippedPermissions++;
                }

                $permsForResource[] = $permName;
            }

            // إنشاء دور للـ resource (إذا لم يكن موجوداً)
            if (! empty($permsForResource)) {
                $role = Role::firstOrCreate(
                    ['name' => $resource, 'guard_name' => 'web'],
                    ['name' => $resource, 'guard_name' => 'web']
                );
                $role->syncPermissions($permsForResource);
                $createdRoles++;
                $resourcePermissions[$resource] = $permsForResource;
                $this->info("   👤 دور: {$resource} (" . count($permsForResource) . ' صلاحية)');
            }
        }

        return [
            'permissions' => $createdPermissions,
            'skipped' => $skippedPermissions,
            'roles' => $createdRoles,
            'resourcePermissions' => $resourcePermissions,
        ];
    }

    protected function createAdminRole(): void
    {
        $this->info('🔑 جارِ إنشاء دور admin...');

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web']
        );

        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        $this->info("✅ تم إنشاء admin وربطه بـ {$allPermissions->count()} صلاحية.");
    }

    protected function assignAdminToUser(): void
    {
        $userId = (int) $this->option('assign-user');
        $this->info("👤 جارِ ربط دور admin بالمستخدم رقم {$userId}...");

        $user = User::find($userId);
        if (! $user) {
            $this->warn("⚠️ لم يتم العثور على المستخدم رقم {$userId}!");
            return;
        }

        $user->syncRoles(['admin']);
        $this->info("✅ تم إعطاء دور admin للمستخدم: {$user->name} ({$user->email})");
    }

    protected function getClassFromFile($file): ?string
    {
        $relativePath = str_replace([app_path('Http/Controllers') . DIRECTORY_SEPARATOR, '.php'], '', $file->getPathname());
        $relativePath = str_replace([DIRECTORY_SEPARATOR, '/'], '\\', $relativePath);
        return 'App\\Http\\Controllers\\' . $relativePath;
    }

    protected function printSummary(array $stats): void
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════');
        $this->info('✅ تمت العملية بنجاح!');
        $this->info("   📝 صلاحيات جديدة: {$stats['permissions']}");
        if ($stats['skipped'] > 0) {
            $this->info("   ⚠️  صلاحيات متكررة تم تخطيها: {$stats['skipped']}");
        }
        $this->info("   👥 أدوار جديدة: {$stats['roles']}");
        $this->info('   🔑 دور admin: جميع الصلاحيات');
        $this->info('═══════════════════════════════════════════');
    }
}
