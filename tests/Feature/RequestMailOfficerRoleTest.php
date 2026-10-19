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
 * دور «موظف متابعة بريد الطلبات» (request_mail_officer):
 * رد ومتابعة بريد الطلبات فقط — دون إدارة المكلفين أو أنواع
 * الطلبات أو مساراتها أو الأقسام القانونية.
 */
class RequestMailOfficerRoleTest extends TestCase
{
    use RefreshDatabase;

    /** الصلاحيات الضيقة المسموحة للدور */
    private const ALLOWED_PERMISSIONS = [
        'index requests',
        'show requests',
        'show request_mails',
        'store request_mails',
        'update request_mails',
        'index mokalafs',
        'index request_types',
    ];

    /** صلاحيات الإدارة المحظورة على الدور */
    private const FORBIDDEN_PERMISSIONS = [
        'store requests',
        'update requests',
        'delete requests',
        'store mokalafs',
        'update mokalafs',
        'delete mokalafs',
        'store request_types',
        'update request_types',
        'delete request_types',
        'store request_workflows',
        'update request_workflows',
        'delete request_workflows',
        'index notices',
        'index reservations',
        'index imprisonment_orders',
    ];

    protected function setUp(): void
    {
        parent::setUp();

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
    public function request_mail_officer_role_exists_with_only_narrow_permissions(): void
    {
        $role = Role::findByName('request_mail_officer', 'web');

        $this->assertSame(
            collect(self::ALLOWED_PERMISSIONS)->sort()->values()->all(),
            $role->permissions->pluck('name')->sort()->values()->all()
        );
    }

    /**
     * @test
     */
    public function request_mail_officer_role_has_no_management_or_legal_permissions(): void
    {
        $role = Role::findByName('request_mail_officer', 'web');

        foreach (self::FORBIDDEN_PERMISSIONS as $permission) {
            if (! Permission::where('name', $permission)->exists()) {
                continue;
            }
            $this->assertFalse(
                $role->hasPermissionTo($permission),
                "الدور يجب ألا يملك صلاحية الإدارة: {$permission}"
            );
        }
    }

    /**
     * @test
     */
    public function user_with_officer_role_can_see_mail_sidebar_gates(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole('request_mail_officer');
        $user = $user->fresh();

        // بوابة قائمة «Mail» وبوابة قائمة «الطلبات» تعتمدان على index requests
        $this->assertTrue($user->can('index requests'));
        $this->assertTrue($user->can('show request_mails'));
        $this->assertTrue($user->can('store request_mails'));
        $this->assertTrue($user->can('update request_mails'));

        // لا يملك إدارة الطلبات أو المكلفين أو المسارات
        $this->assertFalse($user->can('store requests'));
        $this->assertFalse($user->can('delete requests'));
        $this->assertFalse($user->can('store mokalafs'));
        $this->assertFalse($user->can('store request_types'));
        $this->assertFalse($user->can('store request_workflows'));
    }

    /**
     * @test
     */
    public function sidebar_group_for_officer_matches_role_permissions(): void
    {
        $group = config('sidebar_permissions.groups.request_mail_officer');

        $this->assertSame(
            collect(self::ALLOWED_PERMISSIONS)->sort()->values()->all(),
            collect($group)->sort()->values()->all()
        );
    }
}
