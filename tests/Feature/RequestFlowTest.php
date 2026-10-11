<?php

namespace Tests\Feature;

use App\Models\Mokalaf;
use App\Models\Request as RequestModel;
use App\Models\RequestPayment;
use App\Models\RequestStatus;
use App\Models\RequestType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RequestFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $regularUser;
    protected RequestType $requestType;
    protected Mokalaf $mokalaf;
    protected RequestStatus $initialStatus;
    protected RequestStatus $approvedStatus;
    protected RequestStatus $rejectedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء حالات الطلبات
        $this->initialStatus = RequestStatus::factory()->create(['name' => 'قيد الانتظار']);
        $this->approvedStatus = RequestStatus::factory()->create(['name' => 'مقبول']);
        $this->rejectedStatus = RequestStatus::factory()->create(['name' => 'مرفوض']);

        // إنشاء نوع طلب
        $this->requestType = RequestType::factory()->create();

        // إنشاء مكلف
        $this->mokalaf = Mokalaf::factory()->create();

        // إنشاء المستخدمين بدون roles/permissions مؤقتاً
        $this->admin = User::factory()->create();
        $this->regularUser = User::factory()->create();

        Permission::findOrCreate('store requests');
        Permission::findOrCreate('update requests');
        Permission::findOrCreate('delete requests');
        Permission::findOrCreate('index requests');

        $this->regularUser->givePermissionTo('store requests');
    }

    // ============ اختبارات إنشاء الطلبات ============

    /**
     * اختبار إنشاء طلب جديد بنجاح
     * @test
     */
    public function can_create_new_request_successfully()
    {
        $this->actingAs($this->regularUser, 'web');

        $data = [
            'text' => 'طلب اختبار',
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $this->mokalaf->id,
            'common_name' => 'اسم المشترك',
            'address' => 'العنوان',
            'mobile' => '0599999999',
            'fee_amount' => 150.50,
            'is_urgent' => false,
        ];

        $response = $this->post(route('requests.store'), $data);

        $response->assertRedirect(route('requests.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('requests', [
            'text' => 'طلب اختبار',
            'request_type_id' => $this->requestType->id,
            'created_by' => $this->regularUser->id,
            'status_id' => $this->initialStatus->id,
        ]);
    }

    /**
     * اختبار محاولة إنشاء طلب بدون صلاحية
     * @test
     */
    public function cannot_create_request_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'text' => 'طلب بدون صلاحية',
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $this->mokalaf->id,
        ];

        $response = $this->post(route('requests.store'), $data);
        $response->assertForbidden();
    }

    /**
     * اختبار التحقق من صحة بيانات الطلب
     * @test
     */
    public function request_validation_fails_with_invalid_data()
    {
        $this->actingAs($this->regularUser);

        // بيانات ناقصة
        $data = [
            'text' => '', // مفقود
            'request_type_id' => 999, // غير موجود
        ];

        $response = $this->post(route('requests.store'), $data);
        $response->assertSessionHasErrors();
    }

    /**
     * اختبار إنشاء طلب بملفات مرفقة
     * @test
     */
    public function can_create_request_with_attachments()
    {
        $this->actingAs($this->regularUser);

        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        $data = [
            'text' => 'طلب مع مرفقات',
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $this->mokalaf->id,
            'attachments' => [$file],
        ];

        $response = $this->post(route('requests.store'), $data);

        $response->assertRedirect(route('requests.index'));

        $request = RequestModel::latest()->first();
        $this->assertCount(1, $request->attachments);
    }

    // ============ اختبارات تحديث الطلبات ============

    /**
     * اختبار تحديث الطلب من قبل المنشئ
     * @test
     */
    public function creator_can_update_own_request()
    {
        $request = RequestModel::factory()->create([
            'created_by' => $this->regularUser->id,
            'status_id' => $this->initialStatus->id,
        ]);

        $this->regularUser->givePermissionTo('update requests');
        $this->actingAs($this->regularUser);

        $data = [
            'text' => 'طلب محدث',
            'request_type_id' => $request->request_type_id,
            'mokalaf_id' => $request->mokalaf_id,
        ];

        $response = $this->put(route('requests.update', $request), $data);

        $response->assertRedirect();
        $this->assertEquals('طلب محدث', $request->fresh()->text);
    }

    /**
     * اختبار عدم استطاعة المستخدم تحديث طلب من مستخدم آخر
     * @test
     */
    public function user_cannot_update_others_request()
    {
        $otherUser = User::factory()->create();
        $otherUser->givePermissionTo('update requests');

        $request = RequestModel::factory()->create([
            'created_by' => $this->regularUser->id,
            'status_id' => $this->initialStatus->id,
        ]);

        $this->actingAs($otherUser);

        $data = ['text' => 'محاولة التعديل'];

        $response = $this->put(route('requests.update', $request), $data);
        $response->assertForbidden();
    }

    /**
     * اختبار أن Admin يمكنه تحديث أي طلب
     * @test
     */
    public function admin_can_update_any_request()
    {
        $request = RequestModel::factory()->create([
            'created_by' => $this->regularUser->id,
        ]);

        $this->actingAs($this->admin);

        $data = ['text' => 'محدث من قبل admin', 'request_type_id' => $request->request_type_id];

        $response = $this->put(route('requests.update', $request), $data);

        $response->assertRedirect();
        $this->assertEquals('محدث من قبل admin', $request->fresh()->text);
    }

    // ============ اختبارات انتقال حالات الطلبات ============

    /**
     * اختبار تحديث حالة الطلب بنجاح
     * @test
     */
    public function can_update_request_status()
    {
        $request = RequestModel::factory()->create([
            'status_id' => $this->initialStatus->id,
        ]);

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        $response = $this->put(
            route('requests.update-status', $request),
            [
                'status_id' => $this->approvedStatus->id,
                'status_reason' => 'تم المراجعة بنجاح',
            ]
        );

        $response->assertRedirect();
        $this->assertEquals($this->approvedStatus->id, $request->fresh()->status_id);
        $this->assertEquals('تم المراجعة بنجاح', $request->fresh()->status_reason);
    }

    /**
     * اختبار تحديث الحالة بدون صلاحية
     * @test
     */
    public function cannot_update_status_without_permission()
    {
        $request = RequestModel::factory()->create([
            'status_id' => $this->initialStatus->id,
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put(
            route('requests.update-status', $request),
            ['status_id' => $this->approvedStatus->id]
        );

        $response->assertForbidden();
    }

    /**
     * اختبار التحقق من صحة حالة الطلب
     * @test
     */
    public function request_status_validation_fails_with_invalid_status()
    {
        $request = RequestModel::factory()->create();

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        $response = $this->put(
            route('requests.update-status', $request),
            ['status_id' => 999] // حالة غير موجودة
        );

        $response->assertSessionHasErrors();
    }

    /**
     * اختبار التسلسل الطبيعي للحالات
     * @test
     */
    public function request_lifecycle_transitions()
    {
        $request = RequestModel::factory()->create([
            'status_id' => $this->initialStatus->id,
        ]);

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        // الانتقال إلى قيد المراجعة
        $this->put(route('requests.update-status', $request), [
            'status_id' => $this->approvedStatus->id,
        ]);

        $this->assertEquals($this->approvedStatus->id, $request->fresh()->status_id);

        // الانتقال إلى مرفوض
        $this->put(route('requests.update-status', $request), [
            'status_id' => $this->rejectedStatus->id,
            'status_reason' => 'لم يحقق الشروط',
        ]);

        $request->refresh();
        $this->assertEquals($this->rejectedStatus->id, $request->status_id);
    }

    // ============ اختبارات الدفع والتحصيل ============

    /**
     * اختبار إضافة دفعة للطلب
     * @test
     */
    public function can_add_payment_to_request()
    {
        $request = RequestModel::factory()->create([
            'fee_amount' => 500.00,
            'paid_amount' => 0,
            'remaining_amount' => 500.00,
        ]);

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        $response = $this->post(
            route('requests.add-payment', $request),
            [
                'amount' => 150.00,
                'payment_method' => 'cash',
                'payment_date' => now()->toDateString(),
                'reference_number' => 'REF-001',
                'notes' => 'دفعة أولى',
            ]
        );

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('request_payments', [
            'request_id' => $request->id,
            'amount' => 150.00,
            'payment_method' => 'cash',
        ]);

        // التحقق من تحديث الأموال
        $request->refresh();
        $this->assertEquals(150.00, $request->paid_amount);
        $this->assertEquals(350.00, $request->remaining_amount);
    }

    /**
     * اختبار عدم السماح بدفع يتجاوز المبلغ المتبقي
     * @test
     */
    public function cannot_add_payment_exceeding_remaining_amount()
    {
        $request = RequestModel::factory()->create([
            'fee_amount' => 500.00,
            'paid_amount' => 300.00,
            'remaining_amount' => 200.00,
        ]);

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        $response = $this->post(
            route('requests.add-payment', $request),
            [
                'amount' => 300.00, // أكثر من المتبقي
                'payment_method' => 'cash',
                'payment_date' => now()->toDateString(),
            ]
        );

        $response->assertJson(['success' => false]);
    }

    /**
     * اختبار عدم السماح بدفعة بدون رسم مستحق
     * @test
     */
    public function cannot_add_payment_without_fee()
    {
        $request = RequestModel::factory()->create([
            'fee_amount' => 0,
            'paid_amount' => 0,
            'remaining_amount' => 0,
        ]);

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        $response = $this->post(
            route('requests.add-payment', $request),
            [
                'amount' => 100.00,
                'payment_method' => 'cash',
                'payment_date' => now()->toDateString(),
            ]
        );

        $response->assertJson(['success' => false]);
    }

    /**
     * اختبار إلغاء دفعة
     * @test
     */
    public function can_cancel_payment()
    {
        $request = RequestModel::factory()->create([
            'fee_amount' => 500.00,
            'paid_amount' => 150.00,
            'remaining_amount' => 350.00,
        ]);

        $payment = RequestPayment::factory()->create([
            'request_id' => $request->id,
            'amount' => 150.00,
            'status' => 'active',
        ]);

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        $response = $this->post(
            route('requests.cancel-payment', $payment),
            ['cancellation_reason' => 'خطأ في الإدخال']
        );

        $response->assertJson(['success' => true]);

        $payment->refresh();
        $this->assertEquals('cancelled', $payment->status);
        $this->assertEquals('خطأ في الإدخال', $payment->cancellation_reason);
    }

    /**
     * اختبار دفع متعدد حتى السداد الكامل
     * @test
     */
    public function can_pay_request_in_multiple_installments()
    {
        $request = RequestModel::factory()->create([
            'fee_amount' => 1000.00,
            'paid_amount' => 0,
            'remaining_amount' => 1000.00,
        ]);

        $this->admin->givePermissionTo('update requests');
        $this->actingAs($this->admin);

        // الدفعة الأولى
        $this->post(
            route('requests.add-payment', $request),
            ['amount' => 300.00, 'payment_method' => 'cash', 'payment_date' => now()->toDateString()]
        );

        $request->refresh();
        $this->assertEquals(300.00, $request->paid_amount);

        // الدفعة الثانية
        $this->post(
            route('requests.add-payment', $request),
            ['amount' => 400.00, 'payment_method' => 'bank', 'payment_date' => now()->toDateString()]
        );

        $request->refresh();
        $this->assertEquals(700.00, $request->paid_amount);

        // الدفعة الثالثة (الأخيرة)
        $this->post(
            route('requests.add-payment', $request),
            ['amount' => 300.00, 'payment_method' => 'check', 'payment_date' => now()->toDateString()]
        );

        $request->refresh();
        $this->assertEquals(1000.00, $request->paid_amount);
        $this->assertEquals(0.00, $request->remaining_amount);
    }

    // ============ اختبارات الصلاحيات والأدوار ============

    /**
     * اختبار صلاحية عرض الطلبات
     * @test
     */
    public function user_can_view_requests_with_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('index requests');

        $this->actingAs($user);

        $response = $this->get(route('requests.index'));
        $response->assertSuccessful();
    }

    /**
     * اختبار عدم عرض الطلبات بدون صلاحية
     * @test
     */
    public function user_cannot_view_requests_without_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('requests.index'));
        $response->assertForbidden();
    }

    /**
     * اختبار حذف طلب من قبل المنشئ
     * @test
     */
    public function creator_can_delete_own_request()
    {
        $request = RequestModel::factory()->create([
            'created_by' => $this->regularUser->id,
        ]);

        $this->regularUser->givePermissionTo('delete requests');
        $this->actingAs($this->regularUser);

        $response = $this->delete(route('requests.destroy', $request));

        $response->assertRedirect();
        $this->assertSoftDeleted('requests', ['id' => $request->id]);
    }

    /**
     * اختبار عدم استطاعة المستخدم حذف طلب من مستخدم آخر
     * @test
     */
    public function user_cannot_delete_others_request()
    {
        $otherUser = User::factory()->create();
        $otherUser->givePermissionTo('delete requests');

        $request = RequestModel::factory()->create([
            'created_by' => $this->regularUser->id,
        ]);

        $this->actingAs($otherUser);

        $response = $this->delete(route('requests.destroy', $request));
        $response->assertForbidden();
    }

    // ============ اختبارات البحث والتصفية ============

    /**
     * اختبار البحث عن الطلبات
     * @test
     */
    public function can_search_requests_by_text()
    {
        RequestModel::factory()->create(['text' => 'طلب البحث عن عطل في الطريق']);
        RequestModel::factory()->create(['text' => 'طلب آخر تماماً']);

        $this->admin->givePermissionTo('index requests');
        $this->actingAs($this->admin);

        $response = $this->get(route('requests.index', ['search' => 'عطل']));

        $response->assertSuccessful();
        $this->assertCount(1, $response->viewData('requests')->items());
    }

    /**
     * اختبار تصفية الطلبات حسب الحالة
     * @test
     */
    public function can_filter_requests_by_status()
    {
        RequestModel::factory()->create(['status_id' => $this->initialStatus->id]);
        RequestModel::factory()->create(['status_id' => $this->approvedStatus->id]);
        RequestModel::factory()->create(['status_id' => $this->initialStatus->id]);

        $this->admin->givePermissionTo('index requests');
        $this->actingAs($this->admin);

        $response = $this->get(route('requests.index', ['status_id' => $this->approvedStatus->id]));

        $response->assertSuccessful();
        $items = $response->viewData('requests')->items();
        $this->assertCount(1, $items);
        $this->assertEquals($this->approvedStatus->id, $items[0]->status_id);
    }

    /**
     * اختبار تصفية الطلبات المستعجلة
     * @test
     */
    public function can_filter_urgent_requests()
    {
        RequestModel::factory()->create(['is_urgent' => true]);
        RequestModel::factory()->create(['is_urgent' => false]);
        RequestModel::factory()->create(['is_urgent' => true]);

        $this->admin->givePermissionTo('index requests');
        $this->actingAs($this->admin);

        $response = $this->get(route('requests.index', ['is_urgent' => 1]));

        $response->assertSuccessful();
        $items = $response->viewData('requests')->items();
        $this->assertCount(2, $items);
        foreach ($items as $request) {
            $this->assertTrue($request->is_urgent);
        }
    }

    // ============ اختبارات الرسائل والإخطارات ============

    /**
     * اختبار أن إنشاء الطلب يطلق حدث
     * @test
     */
    public function request_creation_fires_event()
    {
        $this->actingAs($this->regularUser);
        $this->regularUser->givePermissionTo('store requests');

        \Illuminate\Support\Facades\Event::fake();

        $data = [
            'text' => 'طلب جديد',
            'request_type_id' => $this->requestType->id,
            'mokalaf_id' => $this->mokalaf->id,
        ];

        $this->post(route('requests.store'), $data);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\RequestCreated::class);
    }

    /**
     * اختبار تسجيل الأنشطة (Activity Log)
     * @test
     */
    public function request_activity_is_logged()
    {
        $request = RequestModel::factory()->create();

        $request->update(['text' => 'نص محدث']);

        $activities = $request->activities;
        $this->assertCount(1, $activities);
        $this->assertEquals('تعديل', $activities->first()->description);
    }
}
