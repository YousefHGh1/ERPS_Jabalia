<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * لوحة تحكم الصلاحيات + أزرار التصدير إلى Excel.
 *
 * كانت أزرار التصدير تفشل بخطأ: Class "Maatwebsite\Excel\Facades\Excel" not found
 * لأن حزمة maatwebsite/excel غير مثبّتة، وتم استبدالها بـ PhpSpreadsheet مباشرة.
 * هذا الاختبار يمنع رجوع الخطأ ويضمن أن اللوحة الجديدة تُعرض بنجاح.
 */
class PermissionsDashboardExportTest extends TestCase
{
    use RefreshDatabase;

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

        $user = User::factory()->create(['name' => 'Dashboard User', 'password' => 'password']);
        $role = Role::create(['name' => 'dashboard-role']);
        $permission = Permission::create(['name' => 'index dashboard']);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->actingAs($user);
    }

    public function test_dashboard_renders_the_new_design(): void
    {
        $response = $this->get('/permissions/dashboard');

        $response->assertOk();
        $response->assertSee('perm-kpi', false);
        $response->assertSee('perm-types', false);
        $response->assertSee('perm-quick-card', false);
        $response->assertSee('--perm-blue', false);
    }

    /**
     * @dataProvider exportRoutesProvider
     */
    public function test_export_routes_return_a_valid_xlsx_file(string $path, string $filename): void
    {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString($filename, (string) $response->headers->get('content-disposition'));

        // ملفات xlsx هي أرشيفات zip تبدأ بالتوقيع PK
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public static function exportRoutesProvider(): array
    {
        return [
            'users' => ['/permissions/export/users', 'users.xlsx'],
            'roles' => ['/permissions/export/roles', 'roles.xlsx'],
            'permissions' => ['/permissions/export/permissions', 'permissions.xlsx'],
            'matrix' => ['/permissions/export/permission-matrix', 'permission_matrix.xlsx'],
        ];
    }
}
