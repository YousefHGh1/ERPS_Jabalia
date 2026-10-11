<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\ControllerPermissionsSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * فصل صلاحيات الأقسام القانونية (أوامر الحبس، الحجوزات، الإخطارات)
 * عن دور «قلم الجمهور» (public_relations) وتثبيتها في دور جديد
 * «legal_affairs_employee» (موظف الشؤون القانونية).
 */
class PublicPenRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** الأقسام الثلاثة المطلوب فصلها عن قلم الجمهور */
    private const LEGAL_MODULES = ['notices', 'reservations', 'imprisonment_orders'];

    protected function setUp(): void
    {
        parent::setUp();

        // تصنيف أساسي يمنع EmployeeFactory من إنشاء تصنيف بحقل bonus_percent
        // الذي لم يعد موجوداً في الجدول بعد الترحيلات اللاحقة.
        Category::create([
            'name'         => 'General',
            'start_degree' => 'A',
            'end_degree'   => 'Z',
            'basic_salary' => 0,
        ]);

        // نفس ترتيب DatabaseSeeder: صلاحيات أساسية ← صلاحيات الكنترولرات ← ربط الأدوار
        $this->seed(PermissionSeeder::class);
        $this->seed(ControllerPermissionsSeeder::class);
        $this->seed(RoleSeeder::class);
    }

    /**
     * @test
     */
    public function public_relations_role_has_no_legal_module_permissions(): void
    {
        $role = Role::findByName('public_relations', 'web');

        $this->assertSame([], $this->legalPermissionsOf($role));

        // يظل محتفظاً بصلاحيات المكلفين والطلبات
        $this->assertTrue($role->hasPermissionTo('index mokalafs'));
        $this->assertTrue($role->hasPermissionTo('index requests'));
    }

    /**
     * @test
     */
    public function republic_pen_manager_role_has_no_legal_module_permissions(): void
    {
        $role = Role::findByName('republic_pen_manager', 'web');

        $this->assertSame([], $this->legalPermissionsOf($role));

        $this->assertTrue($role->hasPermissionTo('index mokalafs'));
        $this->assertTrue($role->hasPermissionTo('index requests'));
    }

    /**
     * @test
     */
    public function legal_affairs_employee_role_holds_only_the_three_legal_modules(): void
    {
        $role = Role::findByName('legal_affairs_employee', 'web');

        foreach (self::LEGAL_MODULES as $module) {
            $this->assertTrue(
                $role->hasPermissionTo("index {$module}"),
                "الدور الجديد يفتقد صلاحية عرض القسم: {$module}"
            );
        }

        // جميع صلاحيات الدور الجديد تخص الأقسام الثلاثة فقط
        $nonLegal = $role->permissions
            ->reject(fn (Permission $permission) => preg_match('/notice|reservation|imprisonment/i', $permission->name))
            ->pluck('name')
            ->all();

        $this->assertSame([], $nonLegal);
        $this->assertNotEmpty($this->legalPermissionsOf($role));
    }

    /**
     * @test
     */
    public function public_pen_user_loses_legal_access_until_new_role_is_assigned(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole('public_relations', 'republic_pen_manager');
        $user = $user->fresh();

        foreach (self::LEGAL_MODULES as $module) {
            $this->assertFalse($user->can("index {$module}"), "المستخدم لا يجب أن يملك index {$module}");
        }
        $this->assertTrue($user->can('index mokalafs'));
        $this->assertTrue($user->can('index requests'));

        // بعد منح الدور الجديد يستعيد صلاحيات الأقسام الثلاثة
        $user->assignRole('legal_affairs_employee');
        $user = $user->fresh();

        foreach (self::LEGAL_MODULES as $module) {
            $this->assertTrue($user->can("index {$module}"), "المستخدم يجب أن يملك index {$module}");
        }
    }

    /**
     * @test
     */
    public function sidebar_composite_for_public_relations_excludes_legal_modules(): void
    {
        $this->assertSame(
            ['mokalafs', 'request_types', 'requests'],
            config('sidebar_permissions.composites.public_relations')
        );

        $this->assertSame(
            ['reservations', 'notices', 'imprisonment_orders'],
            config('sidebar_permissions.composites.legal_affairs_employee')
        );
    }

    /**
     * أسماء الصلاحيات التي تخص الأقسام القانونية الثلاثة.
     *
     * @return string[]
     */
    private function legalPermissionsOf(Role $role): array
    {
        return $role->permissions
            ->filter(fn (Permission $permission) => preg_match('/notice|reservation|imprisonment/i', $permission->name))
            ->pluck('name')
            ->sort()
            ->values()
            ->all();
    }
}
