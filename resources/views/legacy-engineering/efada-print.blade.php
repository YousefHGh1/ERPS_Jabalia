{{-- resources/views/legacy-engineering/partials/efada-print.blade.php --}}
{{--
    قالب طباعة "إفادة" — يعرض نفس تصميم النموذج الورقي الأصلي،
    مع تعبئة البيانات ديناميكيًا من متغير $file (نسخة من App\Models\MasterFile).

    كل الحقول الرئيسية مؤكدة الآن (تمت مطابقتها تجريبيًا على سجلات حقيقية):
    الاسم، الهوية، القسيمة (COUPON)، القطعة (PIECE)، المساحة (AREA)، الحي (STREET)،
    مشروع الإفراز (ADDRESS)، رقم جلسة اللجنة (NOTE)، تاريخ الجلسة (SETTING_DATE)،
    رقم ملف الترخيص (FILE_NO)، نوع البناء (BUILD_NO)، الأدوار (FLOOR_NO + PART_NO).
    لا توجد حقول TODO متبقية في هذا القالب حاليًا.

    تصحيحات تمت بعد مطابقة النموذج مع صورة إفادة حقيقية:
    1) ترتيب "الدور": دور [FLOOR_NO] ([BUILD_NO]) + [PART_NO] — كان BUILD_NO
       يظهر بعد PART_NO بدل أن يكون مباشرة بعد الدور.
    2) التاريخ أعلى الصفحة قبل شريط العنوان (وليس أسفل التوقيعات)، ومحاذى لليسار
       كما في الأصل الورقي.
--}}
<div class="efada-print" dir="rtl">
    <!--<div class="efada-date efada-date-top">-->
    <!--    التاريخ: {{ \Carbon\Carbon::now()->format('Y/m/d') }}-->
    <!--</div>-->

    <div class="efada-title-bar">إفادة</div>

    <div class="efada-body">
        <p>
            تفيد بلدية جباليا النزلة بأن السيد /
            <strong><u>{{ trim(implode(' ', array_filter([
                $file->FIRST_NAME,
                $file->FATHER_NAME,
                $file->GRAND_NAME,
                $file->FAMILY_NAME,
            ]))) ?: ($file->FULL_NAME ?: '..........................') }}</u></strong>
            هوية/
            <strong><u>{{ $file->ID_NO ?: '..........................' }}</u></strong>
        </p>

        <p>
            قد سدد رسوم بناء
            {{-- BUILD_NO = نوع البناء، FLOOR_NO + PART_NO = وصف الأدوار (مؤكدة) --}}
            دور
            <strong><u>{{ $file->FLOOR_NO ?: '.....' }}</u></strong>
            ({{ $file->BUILD_NO ?: '.....' }})
            {{-- PART_NO --}}
            + <strong><u>{{ $file->PART_NO ?: '.....' }}</u></strong>
            بمساحة اجمالية
            (<strong><u>{{ $file->AREA ?: '.....' }}</u></strong> م²)
            فقط في القسيمة
            (<strong><u>{{ $file->COUPON ?: '.....' }}</u></strong>)
            قطعة
            (<strong><u>{{ $file->PIECE ?: '.....' }}</u></strong>)
            حي
            <strong><u>{{ $file->STREET ?: '..........................' }}</u></strong>
            مشروع افراز
            <strong><u>{{ $file->ADDRESS ?: '..........................' }}</u></strong>
            ضمن نفوذ بلدية جباليا النزلة وقد وافقت اللجنة المحلية للتنظيم والبناء بجلستها رقم:
            {{-- NOTE = رقم جلسة اللجنة (مؤكدة) --}}
            <strong><u>{{ $file->NOTE ?: '..........................' }}</u></strong>
            المنعقدة بتاريخ:
            <strong><u>{{ $file->SETTING_DATE ? $file->SETTING_DATE->format('Y/m/d') : '..........................' }}</u></strong>
            م بملف ترخيص رقم:
            {{-- FILE_NO = رقم ملف الترخيص (مؤكدة) --}}
            <strong><u>{{ $file->FILE_NO ?: '..........................' }}</u></strong>.
        </p>

        <p class="efada-purpose">وقد أعطيت هذه الإفادة لتقديمها لمن يهمه الأمر</p>

        <p class="efada-regards">*** بمزيد الاحترام ***</p>
    </div>

    <table class="efada-signatures">
        <tr>
            <td class="efada-sign-left">
                <div class="efada-stamp-placeholder"></div>
                <div class="efada-sign-name">م. مازن سالم النجار</div>
                <div class="efada-sign-title">رئيس اللجنة المحلية للتنظيم والبناء</div>
                <div class="efada-sign-title">ببلدية جباليا النزلة</div>
            </td>
            <td class="efada-sign-right">
                <div class="efada-sign-name">م. إياد محمود الهلول</div>
                <div class="efada-sign-title">مدير دائرة التخطيط والتنظيم</div>
            </td>
        </tr>
    </table>
</div>

<style>
    .efada-print {
        font-family: 'Traditional Arabic', 'Arial', sans-serif;
        color: #1a1a1a;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        line-height: 1.9;
    }
    .efada-header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .efada-header-table td { vertical-align: top; padding: 0; }
    .efada-header-left { text-align: left; width: 40%; font-size: 12px; }
    .efada-header-right { text-align: right; width: 60%; }
    .efada-gov-ar { font-weight: bold; font-size: 14px; color: #b03a2e; }
    .efada-gov-en { font-size: 11px; color: #2e5aa8; }
    .efada-muni-ar { font-weight: bold; font-size: 16px; color: #b03a2e; margin-top: 4px; }
    .efada-muni-en { font-size: 12px; color: #b03a2e; }
    .efada-date { text-align: left; margin: 15px 0; font-size: 13px; }
    .efada-date-top { margin: 0 0 15px; }
    .efada-title-bar {
        background: #f2f2f2;
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        padding: 8px 0;
        margin: 15px 0 25px;
    }
    .efada-body { font-size: 14px; text-align: justify; }
    .efada-body p { margin: 0 0 18px; }
    .efada-purpose, .efada-regards { text-align: center; margin: 20px 0 !important; }
    .efada-signatures { width: 100%; margin-top: 60px; border-collapse: collapse; }
    .efada-signatures td { width: 50%; vertical-align: top; font-size: 13px; }
    .efada-sign-left { text-align: right; }
    .efada-sign-right { text-align: left; }
    .efada-sign-name { font-weight: bold; margin-top: 8px; }
    .efada-stamp-placeholder { height: 90px; }
    .efada-footer {
        width: 100%;
        border-top: 3px solid #b03a2e;
        margin-top: 50px;
        padding-top: 8px;
        font-size: 11px;
        border-collapse: collapse;
    }
    .efada-footer-left { text-align: left; }
    .efada-footer-right { text-align: right; }

    @media print {
        .efada-print {
            padding: 0;
            max-width: 100%;
            line-height: 1.7;
        }
        .efada-title-bar {
            margin: 10px 0 15px;
        }
        .efada-body {
            font-size: 13px;
        }
        .efada-signatures {
            margin-top: 30px;
        }
        .efada-stamp-placeholder {
            height: 60px;
        }
        .efada-date-top {
            margin-bottom: 10px;
        }
    }
</style>
