<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Department;
use App\Models\Mokalaf;
use App\Models\RequestStatus;
use App\Models\RequestType;
use App\Models\RequestWorkflow;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * حماية نموذج إضافة الطلب من تداخل الحقول مع نوافذ "نوع الطلب" و"مسارات نوع الطلب".
 *
 * الخطأ الأصلي: النافذة الديناميكية كانت تحتوي على حقل مخفي باسم request_type_id
 * داخل نموذج الطلب الرئيسي (#kt_form_2)، فيتم إرسال قيمة فارغة ثانية تطغى على
 * نوع الطلب المختار، وتظهر رسالة "نوع الطلب مطلوب" رغم اختيار النوع.
 */
class RequestCreateFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Department $department;
    protected Section $section;
    protected RequestType $requestType;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['store requests', 'store request_types', 'store request_workflows'] as $permission) {
            Permission::findOrCreate($permission);
        }

        // تصنيف أساسي يمنع EmployeeFactory من إنشاء تصنيف بحقل bonus_percent
        // الذي لم يعد موجوداً في الجدول بعد الترحيلات اللاحقة.
        Category::create([
            'name'         => 'General',
            'start_degree' => 'A',
            'end_degree'   => 'Z',
            'basic_salary' => 0,
        ]);

        $this->user = User::factory()->create(['password' => 'password']);
        $this->user->givePermissionTo(['store requests', 'store request_types', 'store request_workflows']);

        $this->department = Department::create(['name' => 'دائرة اختبار النموذج']);
        $this->section = Section::create([
            'name' => 'قسم اختبار النموذج',
            'department_id' => $this->department->id,
        ]);
        $this->requestType = RequestType::factory()->create();
        RequestStatus::factory()->create(['name' => 'قيد الانتظار']);

        // أنواع الردود المطلوبة لإنشاء ردود المسار (4 = قيد الانتظار)
        foreach ([[1, 'مقبول'], [2, 'معلق'], [3, 'مرفوض'], [4, 'قيد الانتظار']] as [$id, $name]) {
            DB::table('response_types')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'created_by' => null]
            );
        }

        // مسار الاستلام لنوع الطلب (بدون مسار سابق)
        RequestWorkflow::create([
            'request_type_id' => $this->requestType->id,
            'section_id'      => $this->section->id,
            'department_id'   => null,
            'name'            => 'استلام الطلب',
            'step_order'      => 1,
            'is_active'       => true,
        ]);
    }

    private function renderCreatePage(): string
    {
        $response = $this->actingAs($this->user)->get(route('requests.create'));
        $response->assertSuccessful();

        return $response->getContent();
    }

    private function extractMainForm(string $html): string
    {
        $start = strpos($html, 'id="kt_form_2"');
        $this->assertNotFalse($start, 'لم يتم العثور على نموذج الطلب الرئيسي (#kt_form_2).');

        $end = strpos($html, '</form>', $start);
        $this->assertNotFalse($end, 'لم يتم العثور على نهاية نموذج الطلب الرئيسي.');

        return substr($html, $start, $end - $start);
    }

    /**
     * حقل نوع الطلب يجب أن يظهر مرة واحدة فقط داخل نموذج الطلب.
     * @test
     */
    public function request_type_field_is_not_duplicated_inside_the_main_form()
    {
        $form = $this->extractMainForm($this->renderCreatePage());

        $this->assertSame(
            1,
            substr_count($form, 'name="request_type_id"'),
            'حقل request_type_id يجب أن يظهر مرة واحدة داخل نموذج الطلب حتى لا تطغى قيمة فارغة على النوع المختار.'
        );

        $this->assertStringNotContainsString(
            'id="new-request-type-id" name="request_type_id"',
            $form,
            'الحقل المخفي داخل نافذة نوع الطلب يجب ألا يحمل خاصية name.'
        );

        $this->assertStringNotContainsString(
            '<input type="hidden" id="new-request-type-id" name=',
            $form,
            'الحقل المخفي داخل نافذة نوع الطلب يجب ألا يحمل خاصية name.'
        );
    }

    /**
     * حقول نوافذ نوع الطلب / المسارات يجب ألا تُرسل مع نموذج الطلب.
     * @test
     */
    public function modal_fields_do_not_leak_into_the_main_form()
    {
        $form = $this->extractMainForm($this->renderCreatePage());

        foreach (['section_id', 'unit_type', 'unit_id', 'step_order', 'previous_workflow_id'] as $field) {
            $this->assertStringNotContainsString(
                'name="' . $field . '"',
                $form,
                'الحقل ' . $field . ' يخص نافذة المسارات ويجب ألا يكون داخل نموذج الطلب.'
            );
        }
    }

    /**
     * نافذة المسارات في صفحة إضافة الطلب تعرض الدوائر والأقسام.
     * @test
     */
    public function workflow_modal_lists_departments_and_sections()
    {
        $html = $this->renderCreatePage();

        $this->assertStringContainsString('id="new-workflow-unit"', $html);
        $this->assertStringContainsString('value="department:' . $this->department->id . '"', $html);
        $this->assertStringContainsString('value="section:' . $this->section->id . '"', $html);
    }

    /**
     * مسار حفظ نافذة المسارات يعتمد على unit_type و unit_id.
     * @test
     */
    public function workflow_modal_script_posts_unit_type_and_unit_id()
    {
        $html = $this->renderCreatePage();

        $this->assertStringContainsString("unit_type: unitParts[0]", $html);
        $this->assertStringContainsString("unit_id: unitParts[1]", $html);
        $this->assertStringContainsString("request_type_id: requestTypeId", $html);
    }

    /**
     * حفظ مسار من نافذة المسارات يرسل unit_type و unit_id ويُخزّن القسم أو الدائرة بشكل صحيح.
     * @test
     */
    public function workflow_modal_store_accepts_department_and_section_units()
    {
        $this->actingAs($this->user)
            ->post(route('request-workflows.store'), [
                'request_type_id'      => $this->requestType->id,
                'unit_type'            => 'department',
                'unit_id'              => $this->department->id,
                'step_order'           => 2,
                'previous_workflow_id' => null,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('request_workflows', [
            'request_type_id' => $this->requestType->id,
            'department_id'   => $this->department->id,
            'section_id'      => null,
            'step_order'      => 2,
        ]);

        // قسم جديد لتفادي تحقق التكرار (المسار الأولي في setUp يستخدم نفس القسم)
        $otherSection = Section::create([
            'name'          => 'قسم ثانٍ لاختبار المسارات',
            'department_id' => $this->department->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('request-workflows.store'), [
                'request_type_id'      => $this->requestType->id,
                'unit_type'            => 'section',
                'unit_id'              => $otherSection->id,
                'step_order'           => 3,
                'previous_workflow_id' => null,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('request_workflows', [
            'request_type_id' => $this->requestType->id,
            'section_id'      => $otherSection->id,
            'department_id'   => null,
            'step_order'      => 3,
        ]);

        // إرسال الشكل القديم (section_id فقط) بدون unit_type / unit_id يجب أن يُرفض
        $this->actingAs($this->user)
            ->post(route('request-workflows.store'), [
                'request_type_id' => $this->requestType->id,
                'section_id'      => $this->section->id,
                'step_order'      => 4,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['unit_type', 'unit_id']);
    }

    /**
     * إرسال نوع الطلب ينجح، وإرساله فارغاً يعطي الرسالة التي كانت تظهر للمستخدم.
     * @test
     */
    public function submitting_a_request_type_creates_the_request_while_empty_type_is_rejected()
    {
        $mokalaf = Mokalaf::factory()->create();

        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب اختبار نموذج الإضافة',
                'request_type_id' => $this->requestType->id,
                'mokalaf_id' => $mokalaf->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('requests.index'));

        $this->assertDatabaseHas('requests', [
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $mokalaf->id,
        ]);

        // إنشاء الطلب يجب أن ينشئ ردود المسار في القسم/الدائرة المرتبطة بالمسار
        $this->assertDatabaseHas('request_responses', [
            'request_id' => \App\Models\Request::where('mokalaf_id', $mokalaf->id)->value('id'),
            'section_id' => $this->section->id,
            'response_type' => 4,
        ]);

        // توثيق خطأ الرفض عند وصول قيمة فارغة (سبب المشكلة الأصلية)
        $this->actingAs($this->user)
            ->post(route('requests.store'), [
                'text' => 'طلب بدون نوع',
                'request_type_id' => '',
                'mokalaf_id' => $mokalaf->id,
            ])
            ->assertSessionHasErrors([
                'request_type_id' => 'نوع الطلب مطلوب',
            ]);
    }
}