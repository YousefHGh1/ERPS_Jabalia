<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\JebayaInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JebayaInvoiceSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // تصنيف أساسي يمنع EmployeeFactory من إنشاء تصنيف بحقل bonus_percent
        // الذي لم يعد موجوداً في الجدول بعد الترحيلات اللاحقة.
        Category::create([
            'name'          => 'General',
            'start_degree'  => 'A',
            'end_degree'    => 'Z',
            'basic_salary'  => 0,
        ]);

        $this->seedInvoices();
    }

    /**
     * إنشاء فواتير تجريبية بتهجئات عربية مختلفة.
     */
    protected function seedInvoices(): void
    {
        $rows = [
            ['voucher_no' => '1000001', 'agreement_id' => '9001', 'mobile_no' => '0599000001', 'arabic_name' => 'أحمد محمد سالم', 'voucher_amt' => 100.00, 'net_amt' => 90.00, 'total_late' => 10.00],
            ['voucher_no' => '1000002', 'agreement_id' => '9002', 'mobile_no' => '0599000002', 'arabic_name' => 'احمد علي حسن', 'voucher_amt' => 200.00, 'net_amt' => 180.00, 'total_late' => 20.00],
            ['voucher_no' => '1000003', 'agreement_id' => '9003', 'mobile_no' => '0599000003', 'arabic_name' => 'موسى خالد نمر', 'voucher_amt' => 300.00, 'net_amt' => 270.00, 'total_late' => 30.00],
            ['voucher_no' => '1000004', 'agreement_id' => '9004', 'mobile_no' => '0599000004', 'arabic_name' => 'موسي عبد الله عمر', 'voucher_amt' => 400.00, 'net_amt' => 360.00, 'total_late' => 40.00],
            ['voucher_no' => '1000005', 'agreement_id' => '9005', 'mobile_no' => '0599000005', 'arabic_name' => 'سامي إبراهيم قاسم', 'voucher_amt' => 500.00, 'net_amt' => 450.00, 'total_late' => 50.00],
            ['voucher_no' => '1000006', 'agreement_id' => '9006', 'mobile_no' => '0599000006', 'arabic_name' => 'سامي إبراهيم', 'voucher_amt' => 600.00, 'net_amt' => 540.00, 'total_late' => 60.00],
            ['voucher_no' => '1000007', 'agreement_id' => '9007', 'mobile_no' => '0599000007', 'arabic_name' => 'أحمد 100% مطابق', 'voucher_amt' => 700.00, 'net_amt' => 630.00, 'total_late' => 70.00],
        ];

        foreach ($rows as $row) {
            JebayaInvoice::create($row);
        }
    }

    /**
     * @test
     */
    public function search_matches_name_regardless_of_hamza_variants()
    {
        // البحث "احمد" (بدون همزة) يجب أن يجد كل أسماء "أحمد" المخزنة والعكس
        // (أحمد محمد سالم + احمد علي حسن + أحمد 100% مطابق)
        $this->assertSame(3, JebayaInvoice::search('احمد')->count());
        $this->assertSame(3, JebayaInvoice::search('أحمد')->count());
    }

    /**
     * @test
     */
    public function search_matches_yeh_and_ta_marbuta_variants()
    {
        // البحث "موسي" يجب أن يجد "موسى" المخزنة
        $this->assertSame(2, JebayaInvoice::search('موسي')->count());
        $this->assertSame(2, JebayaInvoice::search('موسى')->count());
    }

    /**
     * @test
     */
    public function search_multiple_words_requires_adjacent_words_in_order()
    {
        // سجلات تجريبية لاختبار التطابق المتجاور وبالتسلسل:
        $rows = [
            ['voucher_no' => '1000008', 'agreement_id' => '9008', 'mobile_no' => '0599000008', 'arabic_name' => 'يوسف خضر'],
            ['voucher_no' => '1000009', 'agreement_id' => '9009', 'mobile_no' => '0599000009', 'arabic_name' => 'خضر يوسف'],
            ['voucher_no' => '1000010', 'agreement_id' => '9010', 'mobile_no' => '0599000010', 'arabic_name' => 'يوسف محمد خضر'],
            ['voucher_no' => '1000011', 'agreement_id' => '9011', 'mobile_no' => '0599000011', 'arabic_name' => 'احمد يوسف خضر علي'],
        ];
        foreach ($rows as $row) {
            JebayaInvoice::create($row);
        }

        // البحث "يوسف خضر" يجلب فقط الأسماء التي تحوي الكلمتين متجاورتين وبالتسلسل نفسه
        $this->assertSame(2, JebayaInvoice::search('يوسف خضر')->count());
        $names = JebayaInvoice::search('يوسف خضر')->pluck('arabic_name')->sort()->values()->all();
        $this->assertSame(['احمد يوسف خضر علي', 'يوسف خضر'], $names);

        // الترتيب المعكوس لا يُطابق "يوسف خضر" وإنما يُطابق "خضر يوسف" فقط
        $this->assertSame(1, JebayaInvoice::search('خضر يوسف')->count());
        $this->assertSame(['خضر يوسف'], JebayaInvoice::search('خضر يوسف')->pluck('arabic_name')->all());

        // ثلاث كلمات تُطابق العبارة كاملة فقط
        $this->assertSame(1, JebayaInvoice::search('يوسف محمد خضر')->count());
    }

    /**
     * @test
     */
    public function search_by_voucher_number()
    {
        $this->assertSame(1, JebayaInvoice::search('1000003')->count());
        $this->assertSame(7, JebayaInvoice::search('100000')->count()); // بحث جزئي
    }

    /**
     * @test
     */
    public function search_by_agreement_id()
    {
        $this->assertSame(1, JebayaInvoice::search('9004')->count());
    }

    /**
     * @test
     */
    public function search_by_mobile_number()
    {
        $this->assertSame(1, JebayaInvoice::search('0599000002')->count());
    }

    /**
     * @test
     */
    public function like_wildcards_are_escaped_and_searched_literally()
    {
        // % و _ لا يعودان بكل الفواتير (لو لم يُهرَّبا لرجعت كل السجلات)
        $this->assertSame(0, JebayaInvoice::search('%')->count());
        $this->assertSame(0, JebayaInvoice::search('_')->count());

        // % الحرفي داخل نص عربي يبحث عنه حرفياً داخل الأسماء
        $this->assertSame(1, JebayaInvoice::search('أحمد 100%')->count());
    }

    /**
     * @test
     */
    public function empty_search_returns_all_rows_and_controller_guards_against_it()
    {
        // النطاق بحد ذاته لا يفلتر عند غياب مصطلح البحث (مثل باقي النطاقات)
        $this->assertSame(7, JebayaInvoice::search('')->count());
        $this->assertSame(7, JebayaInvoice::search('   ')->count());

        // وحدة التحكم تعرض الشاشة دون أي نتائج عند عدم وجود بحث
        $user = $this->inquiryPageUser();

        $response = $this->actingAs($user)->get(route('collection.inquiry'));

        $response->assertOk();
        $response->assertSee('الاستعلام عن فواتير الجباية');
        $response->assertDontSee('نتائج الاستعلام');
    }

    public function inquiryPageUser(): User
    {
        // تجاوز كلمة مرور المصنع الافتراضية: المصنع يمرر هاشاً مُعَدّاً بكلفة 10
        // بينما كلفة التطبيق 12 فيفشل تحقق مصفوفة hashed cast.
        return User::factory()->create(['password' => 'password']);
    }

    /**
     * @test
     */
    public function inquiry_page_renders_results_for_authenticated_user()
    {
        $user = $this->inquiryPageUser();

        $response = $this->actingAs($user)
            ->get(route('collection.inquiry', ['search' => 'موسي']));

        $response->assertOk();
        $response->assertSee('موسى خالد نمر');
        $response->assertSee('موسي عبد الله عمر');
    }
}
