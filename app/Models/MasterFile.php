<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * ملفات هندسة وتنظيم تاريخية (جدول master المستورد من Oracle ENG.MASTER)
 * للعرض والبحث فقط - لا يُعدّل من التطبيق.
 */
class MasterFile extends Model
{
    use Auditable;

    protected $table = 'master';

    protected $primaryKey = 'ID';

    public $incrementing = true;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'SETTING_DATE' => 'date',
        'REG_DATE' => 'date',
        'LAST_PAY_DATE' => 'date',
        'INSERT_DATE' => 'date',
        'UPDATE_DATE' => 'date',
        'DELETE_DATE' => 'date',
        'TOTAL_AMT' => 'decimal:2',
        'LAST_UPDATE_AMT' => 'decimal:2',
        'REMAINDER' => 'decimal:2',
    ];

    /**
     * بحث مرن في ملفات MASTER التاريخية (هندسة وتنظيم).
     *
     * - رقم الملف: بحث جزئي حرفي (FILE_NO وملحقاته وID_NO).
     * - الاسم: مطابقة العبارة كاملة مع تطبيع الحروف العربية داخل الاستعلام
     *   (أ/إ/آ -> ا، ة -> ه، ى -> ي)، والكلمات المتعددة يجب أن ترد
     *   متجاورة وبالتسلسل نفسه.
     * - العنوان والحالة: بحث مطبّع أيضاً في حقولها.
     *
     * محارف LIKE الخاصة (% _ \) تُهرَّب لتُبحث حرفياً وليس كأنماط عامة.
     */
    public function scopeSmartSearch(Builder $query, string $search = '', string $fileNumber = '', string $ownerName = '', string $status = ''): Builder
    {
        $search = trim($search);

        if ($search !== '') {
            // توحيد الأرقام العربية ليطابق البحثُ التخزينَ اللاتيني في أرقام الملفات
            $normalized = normalizeArabicText($search);
            $hasArabic = (bool) preg_match('/\p{Arabic}/u', $normalized);
            // كلمات البحث (طول >= 2) للـ fallback المرن بأي ترتيب
            $tokens = array_values(array_filter(
                preg_split('/\s+/u', $normalized) ?: [],
                fn ($t) => mb_strlen($t) >= 2
            ));

            $query->where(function (Builder $q) use ($search, $normalized, $hasArabic, $tokens) {
                $q->where(function (Builder $num) use ($search, $normalized) {
                    $like = '%' . static::escapeLike($search) . '%';
                    $num->where('FILE_NO', 'like', $like)
                        ->orWhere('FILE_NO_LONG', 'like', $like)
                        ->orWhere('FILE_NO_VAL', 'like', $like)
                        ->orWhere('GIS_FILE_NO', 'like', $like)
                        ->orWhere('JWAL_NO', 'like', $like)
                        ->orWhere('ID_NO', 'like', $like)
                        ->orWhere('COUPON', 'like', $like)
                        ->orWhere('PIECE', 'like', $like)
                        ->orWhere('HAY_NO', 'like', $like)
                        ->orWhere('AGREMENT_NO_1', 'like', $like)
                        ->orWhere('AGREMENT_NO_2', 'like', $like)
                        ->orWhere('AGREMENT_NO_3', 'like', $like)
                        ->orWhere('TEL_NO', 'like', $like);
                    if ($normalized !== $search) {
                        $nLike = '%' . static::escapeLike($normalized) . '%';
                        $num->orWhere('FILE_NO', 'like', $nLike)
                            ->orWhere('FILE_NO_LONG', 'like', $nLike)
                            ->orWhere('FILE_NO_VAL', 'like', $nLike)
                            ->orWhere('GIS_FILE_NO', 'like', $nLike);
                    }
                });

                if ($hasArabic) {
                    $nameLike = '%' . static::escapeLike($normalized) . '%';
                    $q->orWhereRaw(sqlNormalizeExpression('FULL_NAME') . ' LIKE ?', [$nameLike])
                        ->orWhereRaw(sqlNormalizeExpression('FULL_NAME_F') . ' LIKE ?', [$nameLike])
                        ->orWhereRaw(
                            sqlNormalizeExpression("CONCAT_WS(' ', FIRST_NAME, FATHER_NAME, GRAND_NAME, FAMILY_NAME)") . ' LIKE ?',
                            [$nameLike]
                        )
                        ->orWhereRaw(sqlNormalizeExpression('OTHER_OWNER') . ' LIKE ?', [$nameLike])
                        ->orWhereRaw(sqlNormalizeExpression('ADDRESS') . ' LIKE ?', [$nameLike])
                        ->orWhereRaw(sqlNormalizeExpression('STREET') . ' LIKE ?', [$nameLike])
                        ->orWhereRaw(sqlNormalizeExpression('FILE_STATUSE') . ' LIKE ?', [$nameLike]);

                    // fallback مرن: كل كلمات البحث يجب أن ترد (بأي ترتيب) في الاسم أو العنوان
                    if (count($tokens) > 1) {
                        $q->orWhere(function (Builder $tq) use ($tokens) {
                            foreach ($tokens as $token) {
                                $like = '%' . static::escapeLike($token) . '%';
                                $tq->where(function (Builder $w) use ($like) {
                                    $w->whereRaw(sqlNormalizeExpression('FULL_NAME') . ' LIKE ?', [$like])
                                        ->orWhereRaw(
                                            sqlNormalizeExpression("CONCAT_WS(' ', FIRST_NAME, FATHER_NAME, GRAND_NAME, FAMILY_NAME)") . ' LIKE ?',
                                            [$like]
                                        )
                                        ->orWhereRaw(sqlNormalizeExpression('OTHER_OWNER') . ' LIKE ?', [$like])
                                        ->orWhereRaw(sqlNormalizeExpression('ADDRESS') . ' LIKE ?', [$like]);
                                });
                            }
                        });
                    }
                }
            });
        }

        if (trim($fileNumber) !== '') {
            $like = '%' . static::escapeLike(trim($fileNumber)) . '%';
            $query->where(function (Builder $q) use ($like) {
                $q->where('FILE_NO', 'like', $like)
                    ->orWhere('FILE_NO_LONG', 'like', $like)
                    ->orWhere('FILE_NO_VAL', 'like', $like)
                    ->orWhere('GIS_FILE_NO', 'like', $like);
            });
        }

        if (trim($ownerName) !== '') {
            $normalized = normalizeArabicText($ownerName);
            $ownerLike = '%' . static::escapeLike($normalized !== '' ? $normalized : trim($ownerName)) . '%';
            $query->where(function (Builder $q) use ($ownerLike) {
                $q->whereRaw(sqlNormalizeExpression('FULL_NAME') . ' LIKE ?', [$ownerLike])
                    ->orWhereRaw(sqlNormalizeExpression('FULL_NAME_F') . ' LIKE ?', [$ownerLike])
                    ->orWhereRaw(
                        sqlNormalizeExpression("CONCAT_WS(' ', FIRST_NAME, FATHER_NAME, GRAND_NAME, FAMILY_NAME)") . ' LIKE ?',
                        [$ownerLike]
                    )
                    ->orWhereRaw(sqlNormalizeExpression('OTHER_OWNER') . ' LIKE ?', [$ownerLike]);
            });
        }

        if (trim($status) !== '') {
            $normalized = normalizeArabicText($status);
            $statusLike = '%' . static::escapeLike($normalized !== '' ? $normalized : trim($status)) . '%';
            $query->where(function (Builder $q) use ($statusLike) {
                $q->whereRaw(sqlNormalizeExpression('FILE_STATUSE') . ' LIKE ?', [$statusLike])
                    ->orWhereRaw(sqlNormalizeExpression('S_STATUSE') . ' LIKE ?', [$statusLike])
                    ->orWhereRaw(sqlNormalizeExpression('CLASS_STATUSE') . ' LIKE ?', [$statusLike]);
            });
        }

        return $query;
    }

    /**
     * الاسم الكامل (من الحقول العربية إن وُجدت أو المُجمّع)
     */
    public function getFullNameDisplayAttribute(): string
    {
        if (! empty($this->FULL_NAME)) {
            return $this->FULL_NAME;
        }
        $parts = array_filter([
            $this->FIRST_NAME,
            $this->FATHER_NAME,
            $this->GRAND_NAME,
            $this->FAMILY_NAME,
        ]);

        return implode(' ', $parts) ?: '—';
    }

    /**
     * ترتيب النتائج حسب القرب من مصطلح البحث:
     * 1) رقم الملف مطابق أولاً،
     * 2) الأسماء التي تبدأ بالعبارة كاملة (LOCATE = 1)،
     * 3) الأسماء التي تحوي العبارة كاملة،
     * 4) الأسماء التي تحوي كل الكلمات (fallback)،
     * 5) ثم الأحدث (ID تنازلي).
     */
    public function scopeOrderBySearchRelevance(Builder $query, string $term): Builder
    {
        $normalized = normalizeArabicText($term);
        $escaped = static::escapeLike($normalized !== '' ? $normalized : $term);
        $nameExpr = sqlNormalizeExpression('FULL_NAME');

        // تطابق رقم الملف تماماً يأتي أولاً
        $query->orderByRaw("CASE WHEN FILE_NO = ? THEN 0 ELSE 1 END", [$term]);

        if ($normalized !== '' && preg_match('/\p{Arabic}/u', $normalized)) {
            // العبارة كاملة متجاورة أولاً
            $query->orderByRaw("CASE WHEN {$nameExpr} LIKE ? THEN 0 ELSE 1 END", ['%' . $escaped . '%']);
            // موضع العبارة (الأسماء التي تبدأ بها تأتي قبل غيرها)
            $query->orderByRaw("CASE WHEN LOCATE(?, {$nameExpr}) = 0 THEN 999999 ELSE LOCATE(?, {$nameExpr}) END", [$normalized, $normalized]);
        }

        return $query->orderByDesc('ID');
    }

    /**
     * تهريب محارف LIKE الخاصة حتى تُبحث حرفياً: % _ \
     */
    public static function escapeLike($value): string
    {
        return escapeLike($value);
    }
}
