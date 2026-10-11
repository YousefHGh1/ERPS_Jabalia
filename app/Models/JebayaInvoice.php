<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * JebayaInvoice
 *
 * فواتير الجباية المستوردة من ملف fawatweeer.xlsx ضمن شاشة استعلام قلم الجمهور.
 */
class JebayaInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'voucher_no',
        'voucher_date',
        'voucher_amt',
        'net_amt',
        'total_late',
        'last_due_date',
        'current_voucher_amt',
        'auto_discount_ratio',
        'voucher_month',
        'mobile_no',
        'arabic_name',
    ];

    protected $casts = [
        'voucher_date'  => 'date',
        'last_due_date' => 'date',
        'voucher_amt'   => 'decimal:2',
        'net_amt'       => 'decimal:2',
        'total_late'    => 'decimal:2',
        'current_voucher_amt' => 'decimal:2',
        'auto_discount_ratio' => 'decimal:2',
    ];

    /**
     * بحث مرن في فواتير الجباية.
     *
     * يدعم البحث عن:
     *  - الاسم مع تطبيع عربي موسّع داخل العمود المخزّن (أ/إ/آ/ٱ -> ا، ة -> ه،
     *    ى/ئ -> ي، ؤ -> و، إزالة التشكيل والكشيدة، توحيد الأرقام العربية)
     *    فلا تتأثر النتائج باختلاف التهجئة بين البيانات والمدخلات،
     *  - العبارة كاملة أولاً (متجاورة وبالتسلسل نفسه)، وعند عدم وجود أي
     *    مطابقة تامة فقط يُطبَّق fallback مرن: كل كلمات البحث يجب أن ترد
     *    في الاسم بأي ترتيب (AND) — مثل "محمد أحمد" يجد "أحمد محمد سالم"
     *    حتى مع اختلاف الترتيب،
     *  - رقم الجوال / رقم السند / رقم الاشتراك (بحث جزئي مع توحيد
     *    الأرقام العربية والمشرقية والشرطات والمسافات).
     *
     * يتم تهريب محارف LIKE الخاصة (% _ \) حتى تُبحث حرفياً وليس كأنماط عامة.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }

        // توحيد الأرقام العربية في مصطلح البحث ليطابق التخزين اللاتيني
        $normalizedTerm = normalizeArabicText($term);
        // نسخة رقمية صرفة (بدون شرطات/مسافات) للبحث في الجوال والاشتراكات
        $digitsOnly = preg_replace('/\D+/', '', $normalizedTerm) ?? '';
        $nameExpr = static::normalizedNameExpression();
        $mobileExpr = sqlNormalizeExpression('mobile_no');

        // هل توجد مطابقة تامة (عبارة كاملة في الاسم أو في الحقول الرقمية)؟
        // يُطبَّق fallback الكلمات المبعثرة فقط عند عدم وجود أي مطابقة تامة،
        // حتى لا تتضخم النتائج بصفحات تحوي الكلمات معاً وغير متجاورة
        // (مثل "يوسف خضر" الذي لا يجب أن يُطابق "خضر يوسف").
        $hasExactMatch = (clone $query)
            ->where(function (Builder $q) use ($term, $normalizedTerm, $digitsOnly, $nameExpr, $mobileExpr) {
                static::applyExactMatchConditions($q, $term, $normalizedTerm, $digitsOnly, $nameExpr, $mobileExpr);
            })
            ->exists();

        return $query->where(function (Builder $q) use ($term, $normalizedTerm, $digitsOnly, $nameExpr, $mobileExpr, $hasExactMatch) {
            // 1) المطابقة التامة: الحقول الرقمية + عبارة كاملة وبترتيبها في الاسم
            static::applyExactMatchConditions($q, $term, $normalizedTerm, $digitsOnly, $nameExpr, $mobileExpr);

            // 2) fallback مرن (عند غياب المطابقة التامة فقط): كل كلمات البحث
            //    (طول >= 2) يجب أن ترد في الاسم بأي ترتيب (AND)
            if (!$hasExactMatch && preg_match('/\p{Arabic}/u', $normalizedTerm)) {
                $tokens = array_values(array_filter(
                    preg_split('/\s+/u', $normalizedTerm) ?: [],
                    fn ($t) => mb_strlen($t) >= 2
                ));
                if (count($tokens) > 1) {
                    $q->where(function (Builder $tq) use ($tokens, $nameExpr) {
                        foreach ($tokens as $token) {
                            $tq->whereRaw($nameExpr . ' LIKE ?', ['%' . static::escapeLike($token) . '%']);
                        }
                    });
                }
            }
        });
    }

    /**
     * شروط المطابقة التامة: الحقول الرقمية (خام + مطبّع) وعبارة كاملة
     * في عمود الاسم المطبّع (عند توفر حروف عربية في مصطلح البحث).
     */
    private static function applyExactMatchConditions(
        Builder $q,
        string $term,
        string $normalizedTerm,
        string $digitsOnly,
        string $nameExpr,
        string $mobileExpr
    ): void {
        // البحث في الحقول الرقمية / النص الحرفي (الخام + المطبّع)
        $q->where('voucher_no', 'like', '%' . static::escapeLike($term) . '%')
            ->orWhere('agreement_id', 'like', '%' . static::escapeLike($term) . '%')
            ->orWhere('mobile_no', 'like', '%' . static::escapeLike($term) . '%');

        if ($normalizedTerm !== $term) {
            $q->orWhere('voucher_no', 'like', '%' . static::escapeLike($normalizedTerm) . '%')
                ->orWhere('agreement_id', 'like', '%' . static::escapeLike($normalizedTerm) . '%')
                ->orWhere('mobile_no', 'like', '%' . static::escapeLike($normalizedTerm) . '%');
        }

        // رقم جوال بصيغ مختلفة (شرطات/مسافات/صفر بادئ): قارن الأرقام فقط
        if ($digitsOnly !== '' && $digitsOnly !== $term && $digitsOnly !== $normalizedTerm) {
            $q->orWhereRaw(
                "REPLACE(REPLACE(REPLACE({$mobileExpr}, '-', ''), ' ', ''), '/', '') LIKE ?",
                ['%' . static::escapeLike($digitsOnly) . '%']
            );
        }

        // البحث في الاسم يكون فقط عندما يحوي مصطلح البحث حروفاً عربية
        // (الاسم عمود عربي بطبيعته، والأرقام المحضة تُبحث في الحقول الرقمية فقط)
        if (preg_match('/\p{Arabic}/u', $normalizedTerm)) {
            // مطابقة العبارة كاملة وبترتيبها تماماً على العمود المطبّع
            $q->orWhereRaw($nameExpr . ' LIKE ?', ['%' . static::escapeLike($normalizedTerm) . '%']);
        }
    }

    /**
     * تعبير SQL يطبّع عمود الاسم داخل الاستعلام دون تعديل البيانات نفسها،
     * ليطابق اختلافات الحروف العربية المخزّنة: أ/إ/آ -> ا، ة -> ه، ى -> ي.
     */
    public static function normalizedNameExpression(): string
    {
        return sqlNormalizeExpression('arabic_name');
    }

    /**
     * تهريب محارف LIKE الخاصة حتى تُبحث حرفياً: % _ \
     *
     * @deprecated استخدم الدالة العامة escapeLike() مباشرة.
     */
    public static function escapeLike($value): string
    {
        return escapeLike($value);
    }
}
