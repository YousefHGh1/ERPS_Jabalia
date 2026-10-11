<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * إضافة موظف من داخل نافذة الهيكلية (resources/views/structure/org-chart.blade.php):
 * - النموذج يعرض قائمة الموظفين الموجودين للاختيار منهم (بدلاً من إدخال موظف جديد).
 * - التبعية الكاملة تُحسب على الخادم حسب الوحدة المختارة:
 *   دائرة => department فقط | قسم => section + department | شعبة => branch + section + department.
 */
class OrgChartAttachEmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected Department $department;
    protected Section $section;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // تصنيف أساسي يمنع EmployeeFactory من استخدام عمود bonus_percent غير الموجود.
        Category::firstOrCreate(
            ['name' => 'General'],
            ['start_degree' => 'A', 'end_degree' => 'Z', 'basic_salary' => 0]
        );

        foreach (['store sections', 'store branches', 'store employees', 'update employees'] as $permission) {
            Permission::findOrCreate($permission);
        }

        $this->department = Department::create(['name' => 'دائرة الاختبار']);
        $this->section    = Section::create(['name' => 'قسم الاختبار', 'department_id' => $this->department->id]);
        $this->branch     = Branch::create(['name' => 'شعبة الاختبار', 'section_id' => $this->section->id]);
    }

    /**
     * مستخدم يملك الصلاحيات المطلوبة للاختبار.
     */
    private function viewer(string ...$permissions): User
    {
        // تمرير كلمة مرور نصّية حتى يعيد الـ cast تجزئتها بالإعدادات الحالية
        // (القيمة المُجزّأة مسبقًا في الـ factory بتكلفة 10 تتعارض مع BCRYPT_ROUNDS=4 في phpunit.xml).
        $user = User::factory()->create(['password' => 'password']);
        $user->givePermissionTo($permissions ?: ['update employees']);

        return $user;
    }

    /**
     * بيانات نموذج الإضافة لموظف تحت وحدة معيّنة.
     */
    private function createData(User $user, string $unitType, int $unitId): array
    {
        return $this->actingAs($user)
            ->getJson(route('structure.org-chart.create-data', ['type' => 'employee', 'unitType' => $unitType, 'id' => $unitId]))
            ->assertOk()
            ->json();
    }

    /**
     * بيانات نموذج الإضافة تُرجع قائمة الموظفين الموجودين + التبعية المتوقعة بالأسماء.
     *
     * @test
     */
    public function create_data_returns_existing_employees_and_full_assignment_names(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->createData($this->viewer('store employees'), 'branch', $this->branch->id);

        $this->assertTrue($response['ok']);
        $this->assertSame('employee', $response['target']);
        $this->assertSame('branch_id', $response['parent']['field']);
        $this->assertSame($this->branch->id, (int) $response['parent']['value']);

        $this->assertContains($employee->id, array_column($response['employees'], 'id'));

        // التبعية الكاملة: شعبة + قسم + دائرة مع أسمائها
        $assignment = $response['assignment'];
        $this->assertSame($this->branch->id, $assignment['branch_id']);
        $this->assertSame($this->section->id, $assignment['section_id']);
        $this->assertSame($this->department->id, $assignment['department_id']);
        $this->assertSame('شعبة الاختبار', $assignment['branch']['name']);
        $this->assertSame('قسم الاختبار', $assignment['section']['name']);
        $this->assertSame('دائرة الاختبار', $assignment['department']['name']);
    }

    /**
     * إلحاق موظف بدائرة: يتبع الدائرة فقط ويُصفَّر القسم والشعبة.
     *
     * @test
     */
    public function attaching_employee_to_department_sets_department_only(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => null,
            'section_id'    => null,
            'branch_id'     => null,
        ]);

        $this->actingAs($this->viewer('store employees'))
            ->postJson(route('structure.org-chart.store', ['type' => 'employee']), [
                'employee_id'   => $employee->id,
                'department_id' => $this->department->id,
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $employee->refresh();
        $this->assertSame($this->department->id, $employee->department_id);
        $this->assertNull($employee->section_id);
        $this->assertNull($employee->branch_id);
    }

    /**
     * إلحاق موظف بقسم: يتبع القسم + الدائرة الأب ويُصفَّر الشعبة.
     *
     * @test
     */
    public function attaching_employee_to_section_sets_section_and_parent_department(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => null,
            'section_id'    => null,
            'branch_id'     => null,
        ]);

        $this->actingAs($this->viewer('store employees'))
            ->postJson(route('structure.org-chart.store', ['type' => 'employee']), [
                'employee_id' => $employee->id,
                'section_id'  => $this->section->id,
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $employee->refresh();
        $this->assertSame($this->section->id, $employee->section_id);
        $this->assertSame($this->department->id, $employee->department_id);
        $this->assertNull($employee->branch_id);
    }

    /**
     * إلحاق موظف بشعبة: يتبع الشعبة + القسم + الدائرة.
     *
     * @test
     */
    public function attaching_employee_to_branch_sets_full_hierarchy(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => null,
            'section_id'    => null,
            'branch_id'     => null,
        ]);

        $this->actingAs($this->viewer('store employees'))
            ->postJson(route('structure.org-chart.store', ['type' => 'employee']), [
                'employee_id' => $employee->id,
                'branch_id'   => $this->branch->id,
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $employee->refresh();
        $this->assertSame($this->branch->id, $employee->branch_id);
        $this->assertSame($this->section->id, $employee->section_id);
        $this->assertSame($this->department->id, $employee->department_id);
    }

    /**
     * صلاحية «تعديل» وحدها تكفي للإلحاق (لأنه إلحاق بموظف موجود لا إنشاء جديد).
     *
     * @test
     */
    public function update_employees_permission_alone_allows_attaching(): void
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->viewer('update employees'))
            ->postJson(route('structure.org-chart.store', ['type' => 'employee']), [
                'employee_id'   => $employee->id,
                'department_id' => $this->department->id,
            ])
            ->assertOk();

        $this->assertSame($this->department->id, $employee->fresh()->department_id);
    }

    /**
     * بدون أي من الصلاحيتين: مرفوض.
     *
     * @test
     */
    public function user_without_permissions_is_rejected(): void
    {
        $user     = User::factory()->create(['password' => 'password']);
        $employee = Employee::factory()->create();

        $this->actingAs($user)
            ->postJson(route('structure.org-chart.store', ['type' => 'employee']), [
                'employee_id'   => $employee->id,
                'department_id' => $this->department->id,
            ])
            ->assertForbidden();
    }

    /**
     * بدون اختيار موظف: خطأ تحقق واضح على الحقل.
     *
     * @test
     */
    public function missing_employee_selection_is_rejected(): void
    {
        $this->actingAs($this->viewer('store employees'))
            ->postJson(route('structure.org-chart.store', ['type' => 'employee']), [
                'department_id' => $this->department->id,
            ])
            ->assertJsonValidationErrors(['employee_id']);
    }
}
