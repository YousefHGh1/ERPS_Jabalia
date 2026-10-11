<?php
namespace App\Services;

class HistoricalFilesQueryService
{
    /**
     * قائمة مفاتيح عمود "رقم الملف" المعروفة في بيانات الحرف المستوردة.
     */
    public const CRAFT_FILE_NUMBER_KEYS = ['file_no', 'file_number', 'File Number', 'رقم الملف'];

    public function normalizeFilters(array $filters = []): array
    {
        return [
            'type'        => strtolower(trim((string) ($filters['type'] ?? 'all'))),
            'search'      => trim((string) ($filters['search'] ?? '')),
            'file_number' => trim((string) ($filters['file_number'] ?? '')),
            'owner_name'  => trim((string) ($filters['owner_name'] ?? '')),
            'status'      => trim((string) ($filters['status'] ?? '')),
            'column'      => strtolower(trim((string) ($filters['column'] ?? ''))),
            'date_from'   => trim((string) ($filters['date_from'] ?? '')),
            'date_to'     => trim((string) ($filters['date_to'] ?? '')),
        ];
    }

    public function getIncludedSources(string $type): array
    {
        $type = strtolower(trim($type));

        if ($type === 'engineering') {
            return ['engineering'];
        }

        if ($type === 'crafts') {
            return ['crafts'];
        }

        return ['crafts', 'engineering'];
    }

    /**
     * هل توجد أي فلاتر بحث فعالة (تُستخدم لإظهار النتائج فقط عند البحث).
     */
    public function hasActiveFilters(array $filters): bool
    {
        foreach (['search', 'file_number', 'owner_name', 'status', 'date_from', 'date_to'] as $key) {
            if (! empty($filters[$key])) {
                return true;
            }
        }

        return ! empty($filters['column']) && ! empty($filters['search']);
    }

    /**
     * قيمة حقل رقم الملف من بيانات الحرفة (file_no أولاً ثم البدائل).
     */
    public function craftFileNumber(array $data): string
    {
        foreach (self::CRAFT_FILE_NUMBER_KEYS as $key) {
            if (isset($data[$key]) && $data[$key] !== '' && $data[$key] !== null) {
                return (string) $data[$key];
            }
        }

        return '';
    }

    /**
     * اسم المالك: full_name جاهزاً، وإلا الاسم الرباعي مركّباً بنفس منطق الـ blade.
     */
    public function craftOwnerName(array $data): string
    {
        if (! empty($data['full_name'])) {
            return (string) $data['full_name'];
        }

        if (! empty($data['owner_name']) || ! empty($data['Owner Name']) || ! empty($data['اسم المالك'])) {
            return (string) ($data['owner_name'] ?? $data['Owner Name'] ?? $data['اسم المالك']);
        }

        $parts = array_filter([
            $data['first_name'] ?? null,
            $data['father_name'] ?? null,
            $data['grand_name'] ?? null,
            $data['family_name'] ?? null,
        ], fn ($value) => ! empty($value));

        return trim(implode(' ', $parts));
    }

    /**
     * حالة الحرفة: craft_status لو موجودة، وإلا note كبديل
     * (كثير من السجلات عندها craft_status فارغة والحالة الفعلية موجودة داخل note
     * مثل "الحرفة مغلقة" أو "الحرفة متوقفة").
     */
    public function craftStatus(array $data): string
    {
        if (! empty($data['craft_status'])) {
            return (string) $data['craft_status'];
        }

        if (! empty($data['note'])) {
            // بعض قيم note فيها كشيدة (ـ) للتزيين، نزيلها قبل العرض كحالة
            return trim(str_replace("\u{0640}", '', (string) $data['note']));
        }

        return (string) ($data['status'] ?? $data['Status'] ?? $data['الحالة'] ?? '');
    }

    /**
     * مطابقة عربية مطبّعة (تطبيع موسّع: همزات/ة/ى/تشكيل/كشيدة/أرقام عربية):
     * هل يحتوي النص القيمةَ؟ العبارة كاملة أولاً، وإلا كل الكلمات بأي ترتيب.
     */
    public function containsNormalized(?string $haystack, ?string $needle): bool
    {
        $needle = normalizeArabicText((string) $needle);
        if ($needle === '') {
            return true;
        }

        $haystackNorm = mb_strtolower(normalizeArabicText((string) $haystack));
        $needleNorm = mb_strtolower($needle);

        // 1) العبارة كاملة متجاورة وبترتيبها
        if (str_contains($haystackNorm, $needleNorm)) {
            return true;
        }

        // 2) fallback مرن: كل كلمات البحث (طول >= 2) ترد في النص بأي ترتيب
        $tokens = array_values(array_filter(
            preg_split('/\s+/u', $needleNorm) ?: [],
            fn ($t) => mb_strlen($t) >= 2
        ));
        if (count($tokens) > 1) {
            foreach ($tokens as $token) {
                if (! str_contains($haystackNorm, $token)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * كلمات البحث المطبّعة (طول >= 2) للـ fallback المرن.
     *
     * @return string[]
     */
    public function searchTokens(string $search): array
    {
        $normalized = mb_strtolower(normalizeArabicText($search));

        return array_values(array_filter(
            preg_split('/\s+/u', $normalized) ?: [],
            fn ($t) => mb_strlen($t) >= 2
        ));
    }

    /**
     * هل يطابق سجل حرفة واحد كل الفلاتر؟ (التحقق الدقيق بعد التضييق الأولي)
     *
     * @param array $data   بيانات الحرفة (عمود data مفكوكاً)
     * @param array $filters الفلاتر المطبّعة
     * @param string|null $itemDate تاريخ الإنشاء بصيغة Y-m-d (للمقارنة)
     */
    public function craftMatchesFilters(array $data, array $filters, ?string $itemDate = null): bool
    {
        if (! empty($filters['search'])) {
            $searchable = collect($data)->map(fn ($value) => (string) $value)->implode(' ');
            if (! $this->containsNormalized($searchable, $filters['search'])) {
                return false;
            }

            if (! empty($filters['column'])) {
                $column = $this->resolveCraftColumn($filters['column']);
                if ($column && ! isset($data[$column])) {
                    return false;
                }
                if ($column && ! $this->containsNormalized((string) ($data[$column] ?? ''), $filters['search'])) {
                    return false;
                }
            }
        }

        if (! empty($filters['file_number']) && ! $this->containsNormalized($this->craftFileNumber($data), $filters['file_number'])) {
            return false;
        }

        if (! empty($filters['owner_name']) && ! $this->containsNormalized($this->craftOwnerName($data), $filters['owner_name'])) {
            return false;
        }

        if (! empty($filters['status']) && ! $this->containsNormalized($this->craftStatus($data), $filters['status'])) {
            return false;
        }

        if (! empty($filters['date_from']) && $itemDate && $itemDate < $filters['date_from']) {
            return false;
        }

        if (! empty($filters['date_to']) && $itemDate && $itemDate > $filters['date_to']) {
            return false;
        }

        return true;
    }

    /**
     * درجة القرب لمصطلح البحث داخل سجل حرفة (للترتيب بالأكثر صلة أولاً):
     * تطابق رقم الملف أولاً، ثم العبارة الكاملة، ثم كل الكلمات بأي ترتيب،
     * ثم بداية الاسم، ثم موضع الورود، ثم حداثة السجل.
     *
     * البنية: [tier, phrasePos, tokenHits, date]
     * - tier: 0 رقم ملف | 1 عبارة كاملة من البداية | 2 عبارة كاملة |
     *         3 كل الكلمات بأي ترتيب | 4 لا يوجد (لا يحدث بعد الفلترة)
     */
    public function craftRelevance(array $data, string $search, ?string $itemDate = null): array
    {
        $normalized = mb_strtolower(normalizeArabicText($search));

        if ($normalized === '') {
            return [1, 0, 0, $itemDate ?? ''];
        }

        $fileNumber = mb_strtolower(normalizeArabicText($this->craftFileNumber($data)));
        if ($fileNumber !== '' && str_contains($fileNumber, $normalized)) {
            return [0, 0, 0, $itemDate ?? ''];
        }

        $fields = [
            mb_strtolower(normalizeArabicText($this->craftOwnerName($data))),
            mb_strtolower(normalizeArabicText((string) ($data['address'] ?? ''))),
            mb_strtolower(normalizeArabicText((string) ($data['detailed_type'] ?? ''))),
        ];
        $blob = implode(' ', $fields);

        // العبارة كاملة متجاورة
        $phrasePositions = [];
        foreach ($fields as $field) {
            $pos = mb_strpos($field, $normalized);
            if ($pos !== false) {
                $phrasePositions[] = $pos;
            }
        }
        if (! empty($phrasePositions)) {
            $best = min($phrasePositions);

            return [$best === 0 ? 1 : 2, $best, 0, $itemDate ?? ''];
        }

        // fallback: كل الكلمات بأي ترتيب — رتّب حسب عدد الكلمات المطابقة ثم حداثة السجل
        $tokens = $this->searchTokens($search);
        if (count($tokens) > 1) {
            $hits = 0;
            foreach ($tokens as $token) {
                if (str_contains($blob, $token)) {
                    $hits++;
                }
            }
            if ($hits > 0) {
                return [3, 0, -$hits, $itemDate ?? ''];
            }
        }

        return [4, PHP_INT_MAX, PHP_INT_MAX, $itemDate ?? ''];
    }

    /**
     * تحويل صف حرفة خام إلى صف عرض جاهز للجدول/التصدير.
     */
    public function craftDisplayRow(array $data, int $id, ?string $date): array
    {
        return [
            'source'      => 'crafts',
            'craft_type'  => ((string) ($data['detailed_type'] ?? '')) ?: '—',
            'id'          => $id,
            'file_number' => $this->craftFileNumber($data) ?: '—',
            'owner'       => $this->craftOwnerName($data) ?: '—',
            'status'      => $this->craftStatus($data) ?: '—',
            'file_status' => ((string) ($data['debt_2017'] ?? '')) ?: '—',
            'address'     => $data['address'] ?? '—',
            'summary'     => $this->craftSummary($data) ?: '—',
            'date'        => $date,
        ];
    }

    /**
     * الملخص المختصر: المبلغ + أول ترخيص + آخر رخصة فقط.
     */
    public function craftSummary(array $data): string
    {
        $lastLicense = $data['last_license_year'] ?? null;
        if (! empty($lastLicense)) {
            // القيمة مخزّنة كتاريخ كامل "2019-11-17 00:00:00"، نأخذ الجزء التاريخ فقط
            $lastLicense = substr((string) $lastLicense, 0, 10);
        }

        $parts = [
            'المبلغ'    => $data['balance'] ?? null,
            'أول ترخيص' => $data['first_license_year'] ?? null,
            'آخر رخصة'  => $lastLicense,
        ];

        $parts = array_filter($parts, fn ($value) => $value !== null && $value !== '');

        return collect($parts)
            ->map(fn ($value, $label) => "{$label}: {$value}")
            ->implode(' | ');
    }

    public function resolveCraftColumn(string $column): ?string
    {
        return match ($column) {
            'file_number' => 'file_no',
            'owner'       => 'full_name',
            'status'      => 'craft_status',
            'address'     => 'address',
            default       => $column,
        };
    }
}
