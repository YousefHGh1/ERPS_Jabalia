{{-- resources/views/legacy-engineering/partials/shahadat-mansha-print.blade.php --}}
{{--
    قالب طباعة "شهادة تسجيل منشأة" — يعرض نفس تصميم النموذج الورقي الأصلي
    (تخطيط جدول: تسمية على اليمين، قيمة على اليسار، لكل صف)، مع تعبئة البيانات
    ديناميكيًا من متغير $file (نسخة من App\Models\MasterFile).

    ثقة الربط الحالية (بعد تأكيد المستخدم لغالبية الحقول):
    - قطعة رقم (PIECE)          : مؤكدة
    - قسيمة رقم (COUPON)        : مؤكدة
    - مقسم رقم (DIVIDER)        : مؤكدة
    - مبالغ متبقية (REMAINDER)  : مؤكدة
    - المساحة المرخصة (AREA)    : مؤكدة
    - هوية المالك (ID_NO)       : مؤكدة
    - اسم المالك/ة (FULL_NAME)  : مؤكدة
    - العنوان (ADDRESS)         : مؤكدة
    - رقم ملف الترخيص (FILE_NO) : مؤكدة
    - الحي (STREET)             : مؤكدة
    - نوع البناء (BUILD_NO)     : مؤكدة
    - الأدوار (FLOOR_NO + PART_NO) : مؤكدة
    - تاريخ اعتماد اللجنة (SETTING_DATE) : مؤكدة
    - ملاحظات: NOTE مخصص فعليًا لـ"رقم جلسة اللجنة" (حسب تأكيد المستخدم لقالب الإفادة)
      وليس ملاحظات عامة — النموذج المرجعي لشهادة المنشأة كان هذا الحقل فارغًا أصلًا،
      لذلك تُرك بدون ربط بعمود (نقاط فارغة) بدل استخدام NOTE بشكل خاطئ هنا.
--}}
<div class="mansha-print" dir="rtl">
    {{-- <div class="mansha-print-date">
        تاريخ الطباعة<br>
        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div> --}}

    <div class="mansha-title-block">
        <div class="mansha-dept-title">دائرة الهندسة والتنظيم</div>
        <div class="mansha-cert-title">شهادة تسجيل منشأة</div>
        <div class="mansha-intro">تفيد بلدية جباليا النزلة بأن المنشأة المرخصة لديها وفق البيانات التالية:</div>
    </div>

    <table class="mansha-fields">
        <tr>
            <td class="mansha-label">رقم ملف الترخيص:</td>
            <td class="mansha-value">
                <u>{{ $file->FILE_NO ?: '.....' }}</u>
            </td>
        </tr>
        <tr>
            <td class="mansha-label">اسم المالك/ة:</td>
            <td class="mansha-value">
                <u>{{ trim(implode(' ', array_filter([
                    $file->FIRST_NAME,
                    $file->FATHER_NAME,
                    $file->GRAND_NAME,
                    $file->FAMILY_NAME,
                ]))) ?: ($file->FULL_NAME ?: '.....') }}</u>
            </td>
        </tr>
        <tr>
            <td class="mansha-label">هوية رقم:</td>
            <td class="mansha-value"><u>{{ $file->ID_NO ?: '.....' }}</u></td>
        </tr>
        <tr>
            <td class="mansha-label">العنوان:</td>
            <td class="mansha-value"><u>{{ $file->ADDRESS ?: '.....' }}</u></td>
        </tr>
        <tr>
            <td class="mansha-label">الحي:</td>
            <td class="mansha-value">
                <u>{{ $file->STREET ?: '.....' }}</u>
            </td>
        </tr>
        <tr>
            <td class="mansha-label">قطعة رقم:</td>
            <td class="mansha-value"><u>{{ $file->PIECE ?: '.....' }}</u></td>
        </tr>
        <tr>
            <td class="mansha-label">قسيمة رقم:</td>
            <td class="mansha-value"><u>{{ $file->COUPON ?: '.....' }}</u></td>
        </tr>
        <tr>
            <td class="mansha-label">مقسم رقم:</td>
            <td class="mansha-value"><u>{{ $file->DIVIDER ?: '.....' }}</u></td>
        </tr>
        <tr>
            <td class="mansha-label">نوع البناء:</td>
            <td class="mansha-value">
                <u>{{ $file->BUILD_NO ?: '.....' }}</u>
            </td>
        </tr>
        <tr>
            <td class="mansha-label">المساحة المرخصة:</td>
            <td class="mansha-value"><u>{{ $file->AREA ?: '.....' }}</u></td>
        </tr>
        <tr>
            <td class="mansha-label">الأدوار:</td>
            <td class="mansha-value">
                <u>{{ $file->FLOOR_NO ?: '.....' }}</u> + <u>{{ $file->PART_NO ?: '.....' }}</u>
            </td>
        </tr>
        <tr>
            <td class="mansha-label">ملاحظات:</td>
            <td class="mansha-value">
                {{-- لا يوجد عمود ملاحظات عامة مؤكد لشهادة المنشأة (NOTE مخصص لرقم جلسة اللجنة بالإفادة) --}}
                <u>.....</u>
            </td>
        </tr>
        <tr>
            <td class="mansha-label">مبالغ متبقية:</td>
            <td class="mansha-value"><u>{{ $file->REMAINDER ?? '.....' }}</u></td>
        </tr>
    </table>

    <p class="mansha-approval">
        وقد تم اعتماد بيانات الترخيص المدونة أعلاه من قبل اللجنة المحلية في البلدية بتاريخ:
        <strong><u>{{ $file->SETTING_DATE ? $file->SETTING_DATE->format('d/m/Y') : '.....' }}</u></strong>
    </p>

    <table class="mansha-signature">
        <tr>
            <td class="mansha-sign-block">
                <div class="mansha-sign-title">دائرة الهندسة والتنظيم</div>
                <div class="mansha-stamp-placeholder"></div>
            </td>
        </tr>
    </table>
</div>

<style>
    .mansha-print {
        font-family: 'Traditional Arabic', 'Arial', sans-serif;
        color: #1a1a1a;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        line-height: 1.8;
    }
    .mansha-header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .mansha-header-table td { vertical-align: top; padding: 0; }
    .mansha-header-left { text-align: left; width: 40%; font-size: 12px; }
    .mansha-header-right { text-align: right; width: 60%; }
    .mansha-gov-ar { font-weight: bold; font-size: 14px; color: #b03a2e; }
    .mansha-gov-en { font-size: 11px; color: #2e5aa8; }
    .mansha-muni-ar { font-weight: bold; font-size: 16px; color: #b03a2e; margin-top: 4px; }
    .mansha-muni-en { font-size: 12px; color: #b03a2e; }
    .mansha-print-date { font-size: 12px; margin-bottom: 10px; text-align: left; }
    .mansha-title-block { text-align: center; margin: 15px 0 25px; }
    .mansha-dept-title { font-size: 18px; font-weight: bold; text-decoration: underline; margin-bottom: 10px; }
    .mansha-cert-title { font-size: 20px; font-weight: bold; text-decoration: underline; margin-bottom: 15px; }
    .mansha-intro { font-size: 14px; }
    .mansha-fields { width: 100%; border-collapse: collapse; font-size: 15px; margin-bottom: 20px; }
    .mansha-fields td { padding: 8px 4px; vertical-align: bottom; }
    .mansha-label { text-align: right; width: 35%; font-weight: normal; white-space: nowrap; }
    .mansha-value { text-align: right; width: 65%; font-weight: bold; }
    .mansha-approval { font-size: 14px; margin: 25px 0; }
    .mansha-signature { width: 100%; margin-top: 40px; }
    .mansha-sign-title { font-weight: bold; font-size: 14px; }
    .mansha-stamp-placeholder { height: 100px; }
    .mansha-footer {
        width: 100%;
        border-top: 3px solid #b03a2e;
        margin-top: 40px;
        padding-top: 8px;
        font-size: 11px;
        border-collapse: collapse;
    }
    .mansha-footer-left { text-align: left; }
    .mansha-footer-right { text-align: right; }

    @media print {
        .mansha-print {
            padding: 0;
            max-width: 100%;
            line-height: 1.6;
        }
        .mansha-title-block {
            margin: 10px 0 15px;
        }
        .mansha-cert-title {
            margin-bottom: 8px;
        }
        .mansha-fields {
            font-size: 13px;
        }
        .mansha-fields td {
            padding: 5px 4px;
        }
        .mansha-approval {
            font-size: 13px;
            margin: 15px 0;
        }
        .mansha-signature {
            margin-top: 20px;
        }
        .mansha-stamp-placeholder {
            height: 60px;
        }
    }
</style>
