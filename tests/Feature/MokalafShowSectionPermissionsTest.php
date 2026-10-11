<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Mokalaf;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * حماية أقسام (جداول) صفحة ملف المكلف: resources/views/mokalafs/show.blade.php
 *
 * كل قسم ملفوف بـ @can/@canany بنفس الصلاحية المفروضة فعلياً في الكنترولر/السياسة
 * الخاصة به، وهذا الاختبار يتأكد أن القسم يظهر فقط لمن يملك صلاحية عرضه،
 * وأن زر «إضافة جديد» يظهر فقط حسب صلاحية الإضافة الخاصة بالقسم.
 */
class MokalafShowSectionPermissionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * نص الحالة الفارغة داخل جسم كل بطاقة — يظهر فقط عند ظهور القسم للمستخدم.
     * (المكلف المُنشأ في الاختبار بلا سجلات مرتبطة، فيظهر النص الفارغ دائماً)
     */
    private const SECTION_EMPTY_MESSAGES = [
        'requests'             => 'لا توجد طلبات مرتبطة بهذا المكلف',
        'craft_licenses'       => 'لا توجد تراخيص حرف مرتبطة برقم الهوية',
        'engineerings'         => 'لا توجد ملفات هندسة مرتبطة برقم الهوية',
        'external_collections' => 'لا توجد تحصيلات خارجية مرتبطة',
        'imprisonment_orders'  => 'لا توجد أوامر حبس مرتبطة برقم الهوية',
        'reservations'         => 'لا توجد حجوزات مرتبطة برقم الهوية',
        'notices'              => 'لا توجد إخطارات مرتبطة برقم الهوية',
    ];

    /** صلاحية عرض كل قسم — نفس ما يفرضه كنترولر/سياسة القسم في الشاشة المستقلة */
    private const SECTION_VIEW_PERMISSIONS = [
        'requests'             => 'index requests',
        'craft_licenses'       => 'index craft_licenses',
        'engineerings'         => 'index engineerings',
        'external_collections' => 'view external_collections',
        'imprisonment_orders'  => 'index imprisonment_orders',
        'reservations'         => 'index reservations',
        'notices'              => 'index notices',
    ];

    protected Mokalaf $mokalaf;

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

        $permissions = array_merge(
            ['index mokalafs', 'update mokalafs'],
            array_values(self::SECTION_VIEW_PERMISSIONS),
            // صلاحيات أزرار «إضافة جديد» كما تستخدمها الكنترولرات/السياسات
            [
                'create requests',
                'store requests',
                'create craft_licenses',
                'create engineerings',
                'create external_collections',
                'store imprisonment_orders',
                'store reservations',
                'store notices',
            ],
        );

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $this->mokalaf = Mokalaf::factory()->create();
    }

    /**
     * إنشاء مستخدم يملك صلاحية عرض ملف المكلف دائماً + الصلاحيات المطلوبة للاختبار.
     */
    private function viewer(string ...$extraPermissions): User
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->givePermissionTo(array_merge(['index mokalafs'], $extraPermissions));

        return $user;
    }

    private function renderPage(User $user): TestResponse
    {
        $response = $this->actingAs($user)->get(route('mokalafs.show', $this->mokalaf));

        $response->assertOk();

        return $response;
    }

    /**
     * @test
     */
    public function user_without_section_permissions_sees_no_section_table(): void
    {
        $response = $this->renderPage($this->viewer());

        foreach (self::SECTION_EMPTY_MESSAGES as $message) {
            $response->assertDontSee($message, false);
        }

        // أزرار الإضافة مخفية أيضاً
        $response->assertDontSee('إضافة جديد', false);

        // رأس الصفحة (بيانات المكلف) يظل ظاهراً
        // مطابقة مهروَّبة كما تفعل Blade لتفادي اختلاف ترميز علامات مثل '
        $response->assertSee($this->mokalaf->name);
        $response->assertSee('عودة لقائمة المكلفين', false);
    }

    /**
     * @test
     */
    public function each_section_appears_only_with_its_own_view_permission(): void
    {
        foreach (self::SECTION_VIEW_PERMISSIONS as $section => $permission) {
            $response = $this->renderPage($this->viewer($permission));

            $response->assertSee(self::SECTION_EMPTY_MESSAGES[$section], false);

            foreach (self::SECTION_EMPTY_MESSAGES as $otherSection => $message) {
                if ($otherSection === $section) {
                    continue;
                }

                $response->assertDontSee($message, false);
            }
        }
    }

    /**
     * التحصيلات الخارجية تظهر بصلاحية view external_collections
     * (نفس صلاحية كنترولر التحصيل الخارجي) ولا تكفي صلاحية index القديمة.
     *
     * @test
     */
    public function external_collections_section_requires_view_permission(): void
    {
        // صلاحية قديمة/يتيمة لا يُنشئها أي Seeder ولا تستخدمها أي شاشة أخرى،
        // نُنشئها هنا للتأكد أنها وحدها لا تكفي لإظهار جدول التحصيلات الخارجية.
        Permission::findOrCreate('index external_collections');

        $indexOnly = $this->renderPage($this->viewer('index external_collections'));
        $indexOnly->assertDontSee(self::SECTION_EMPTY_MESSAGES['external_collections'], false);

        $withView = $this->renderPage($this->viewer('view external_collections'));
        $withView->assertSee(self::SECTION_EMPTY_MESSAGES['external_collections'], false);
    }

    /**
     * زر «إضافة جديد» في قسم الطلبات يتبع RequestPolicy::create
     * (create requests أو store requests) — كل واحدة منهما تكفي.
     *
     * @test
     */
    public function requests_add_button_follows_create_or_store_permission(): void
    {
        $viewOnly = $this->renderPage($this->viewer('index requests'));
        $viewOnly->assertSee(self::SECTION_EMPTY_MESSAGES['requests'], false);
        $viewOnly->assertDontSee('إضافة جديد', false);

        $withCreate = $this->renderPage($this->viewer('index requests', 'create requests'));
        $withCreate->assertSee('إضافة جديد', false);

        $withStore = $this->renderPage($this->viewer('index requests', 'store requests'));
        $withStore->assertSee('إضافة جديد', false);
    }

    /**
     * زر «إضافة جديد» داخل قسم الإخطارات يتبع صلاحية store notices.
     *
     * @test
     */
    public function notices_add_button_requires_store_notices_permission(): void
    {
        $viewOnly = $this->renderPage($this->viewer('index notices'));
        $viewOnly->assertSee(self::SECTION_EMPTY_MESSAGES['notices'], false);
        $viewOnly->assertDontSee('إضافة جديد', false);

        $withStore = $this->renderPage($this->viewer('index notices', 'store notices'));
        $withStore->assertSee('إضافة جديد', false);
    }

    /**
     * زر تعديل ملف المكلف يتبع صلاحية update mokalafs (MokalafPolicy::update).
     *
     * @test
     */
    public function mokalaf_edit_button_requires_update_permission(): void
    {
        $withoutUpdate = $this->renderPage($this->viewer());
        $withoutUpdate->assertDontSee(route('mokalafs.edit', $this->mokalaf), false);

        $withUpdate = $this->renderPage($this->viewer('update mokalafs'));
        $withUpdate->assertSee(route('mokalafs.edit', $this->mokalaf), false);
    }

    /**
     * مستخدم يملك كل صلاحيات الأقسام يرى الجداول السبعة وأزرار «إضافة جديد» السبعة.
     *
     * @test
     */
    public function user_with_all_section_permissions_sees_every_section_and_add_button(): void
    {
        $permissions = array_merge(
            array_values(self::SECTION_VIEW_PERMISSIONS),
            [
                'create requests',
                'create craft_licenses',
                'create engineerings',
                'create external_collections',
                'store imprisonment_orders',
                'store reservations',
                'store notices',
            ],
        );

        $response = $this->renderPage($this->viewer(...$permissions));

        foreach (self::SECTION_EMPTY_MESSAGES as $message) {
            $response->assertSee($message, false);
        }

        // سبعة أقسام = سبعة أزرار «إضافة جديد»
        $this->assertSame(7, substr_count($response->getContent(), 'إضافة جديد'));
    }
}
