<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Mokalaf;
use App\Models\Request;
use App\Models\RequestStatus;
use App\Models\RequestType;
use App\Models\RequestWorkflow;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RequestApprovalEmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Department $department;
    protected Section $section;
    protected RequestType $requestType;
    protected Employee $eligibleEmployee;
    protected Employee $ineligibleEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['store requests', 'store request_types', 'store request_workflows'] as $permission) {
            Permission::findOrCreate($permission);
        }

        Category::create([
            'name'         => 'General',
            'start_degree' => 'A',
            'end_degree'   => 'Z',
            'basic_salary' => 0,
        ]);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['store requests', 'store request_types', 'store request_workflows']);

        $this->department = Department::create(['name' => 'دائرة اختبار الموظف المسؤول']);
        $this->section = Section::create([
            'name' => 'قسم اختبار الموظف المسؤول',
            'department_id' => $this->department->id,
        ]);
        $this->requestType = RequestType::factory()->create();
        RequestStatus::factory()->create(['name' => 'قيد الانتظار']);

        foreach ([[1, 'مقبول'], [2, 'معلق'], [3, 'مرفوض'], [4, 'قيد الانتظار']] as [$id, $name]) {
            DB::table('response_types')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'created_by' => null]
            );
        }

        RequestWorkflow::create([
            'request_type_id' => $this->requestType->id,
            'section_id'      => $this->section->id,
            'department_id'   => null,
            'name'            => 'استلام الطلب',
            'step_order'      => 1,
            'is_active'       => true,
        ]);

        // موظف مؤهل (له user account وفي القسم الصحيح)
        $this->eligibleEmployee = Employee::factory()->create([
            'section_id' => $this->section->id,
            'status' => 'active',
        ]);
        User::factory()->create([
            'employee_id' => $this->eligibleEmployee->id,
        ]);

        // موظف غير مؤهل (في قسم مختلف)
        $otherSection = Section::create([
            'name' => 'قسم آخر',
            'department_id' => $this->department->id,
        ]);
        $this->ineligibleEmployee = Employee::factory()->create([
            'section_id' => $otherSection->id,
            'status' => 'active',
        ]);
        User::factory()->create([
            'employee_id' => $this->ineligibleEmployee->id,
        ]);
    }

    /**
     * إنشاء طلب مع موظف مسؤول مؤهل يجب أن ينجح.
     * @test
     */
    public function can_create_request_with_eligible_approval_employee()
    {
        $mokalaf = Mokalaf::factory()->create();

        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب اختبار الموظف المسؤول',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
                'approval_employee_id' => $this->eligibleEmployee->id,
                'approval_employee_type' => 'primary',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('requests.index'));

        $this->assertDatabaseHas('requests', [
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $mokalaf->id,
            'approval_employee_id' => $this->eligibleEmployee->id,
            'approval_employee_type' => 'primary',
        ]);
    }

    /**
     * إنشاء طلب مع موظف غير مؤهل يجب أن يفشل.
     * @test
     */
    public function cannot_create_request_with_ineligible_approval_employee()
    {
        $mokalaf = Mokalaf::factory()->create();

        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب اختبار الموظف غير المؤهل',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
                'approval_employee_id' => $this->ineligibleEmployee->id,
            ])
            ->assertSessionHasErrors(['approval_employee_id']);

        $this->assertDatabaseMissing('requests', [
            'approval_employee_id' => $this->ineligibleEmployee->id,
        ]);
    }

    /**
     * إنشاء طلب بدون موظف مسؤول يجب أن ينجح (الحقل اختياري).
     * @test
     */
    public function can_create_request_without_approval_employee()
    {
        $mokalaf = Mokalaf::factory()->create();

        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب بدون موظف مسؤول',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('requests.index'));

        $this->assertDatabaseHas('requests', [
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $mokalaf->id,
            'approval_employee_id' => null,
        ]);
    }

    /**
     * اختيار موظف بديل مع سبب الاستبدال يجب أن ينجح.
     * @test
     */
    public function can_create_request_with_substitute_employee_and_reason()
    {
        $mokalaf = Mokalaf::factory()->create();

        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب مع موظف بديل',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
                'approval_employee_id' => $this->eligibleEmployee->id,
                'approval_employee_type' => 'substitute',
                'approval_substitution_reason' => 'غياب الموظف الأساسي',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('requests.index'));

        $this->assertDatabaseHas('requests', [
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $mokalaf->id,
            'approval_employee_id' => $this->eligibleEmployee->id,
            'approval_employee_type' => 'substitute',
            'approval_substitution_reason' => 'غياب الموظف الأساسي',
        ]);
    }

    /**
     * موظف غير نشط يجب أن يتم رفضه.
     * @test
     */
    public function cannot_create_request_with_inactive_employee()
    {
        $inactiveEmployee = Employee::factory()->create([
            'section_id' => $this->section->id,
            'status' => 'inactive',
        ]);
        User::factory()->create([
            'employee_id' => $inactiveEmployee->id,
        ]);

        $mokalaf = Mokalaf::factory()->create();

        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب مع موظف غير نشط',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
                'approval_employee_id' => $inactiveEmployee->id,
            ])
            ->assertSessionHasErrors(['approval_employee_id']);
    }

    /**
     * موظف بدون user account يجب أن يتم رفضه.
     * @test
     */
    public function cannot_create_request_with_employee_without_user_account()
    {
        $employeeWithoutUser = Employee::factory()->create([
            'section_id' => $this->section->id,
            'status' => 'active',
        ]);

        $mokalaf = Mokalaf::factory()->create();

        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب مع موظف بدون حساب',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
                'approval_employee_id' => $employeeWithoutUser->id,
            ])
            ->assertSessionHasErrors(['approval_employee_id']);
    }

    /**
     * API endpoint للحصول على الموظفين المؤهلين يجب أن يعمل.
     * @test
     */
    public function get_eligible_employees_endpoint_returns_correct_employees()
    {
        $this->actingAs($this->user)
            ->get(route('requests.get-eligible-employees', [
                'request_type_id' => $this->requestType->id,
            ]))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $response = $this->actingAs($this->user)
            ->get(route('requests.get-eligible-employees', [
                'request_type_id' => $this->requestType->id,
            ]))
            ->json();

        $this->assertNotEmpty($response['employees']);
        $employeeIds = collect($response['employees'])->pluck('id');
        $this->assertContains($this->eligibleEmployee->id, $employeeIds);
        $this->assertNotContains($this->ineligibleEmployee->id, $employeeIds);
    }

    /**
     * API endpoint للبحث في جميع الموظفين يجب أن يعمل.
     * @test
     */
    public function search_all_employees_endpoint_returns_employees()
    {
        $this->actingAs($this->user)
            ->get(route('requests.search-all-employees', [
                'search' => $this->eligibleEmployee->name,
            ]))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $response = $this->actingAs($this->user)
            ->get(route('requests.search-all-employees', [
                'search' => $this->eligibleEmployee->name,
            ]))
            ->json();

        $this->assertNotEmpty($response['employees']);
        $employeeIds = collect($response['employees'])->pluck('id');
        $this->assertContains($this->eligibleEmployee->id, $employeeIds);
    }

    /**
     * تحديث الطلب مع موظف مسؤول يجب أن ينجح.
     * @test
     */
    public function can_update_request_with_approval_employee()
    {
        $mokalaf = Mokalaf::factory()->create();
        $request = Request::factory()->create([
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $mokalaf->id,
            'status_id' => RequestStatus::where('name', 'قيد الانتظار')->first()->id,
        ]);

        $this->actingAs($this->user)
            ->put(route('requests.update', $request), [
                'text' => 'طلب محدث',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
                'approval_employee_id' => $this->eligibleEmployee->id,
                'approval_employee_type' => 'primary',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'approval_employee_id' => $this->eligibleEmployee->id,
        ]);
    }

    /**
     * Request model يجب أن يكون له علاقة مع approvalEmployee.
     * @test
     */
    public function request_model_has_approval_employee_relationship()
    {
        $mokalaf = Mokalaf::factory()->create();
        $request = Request::factory()->create([
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $mokalaf->id,
            'approval_employee_id' => $this->eligibleEmployee->id,
            'status_id' => RequestStatus::where('name', 'قيد الانتظار')->first()->id,
        ]);

        $this->assertInstanceOf(Employee::class, $request->approvalEmployee);
        $this->assertEquals($this->eligibleEmployee->id, $request->approvalEmployee->id);
    }
}
