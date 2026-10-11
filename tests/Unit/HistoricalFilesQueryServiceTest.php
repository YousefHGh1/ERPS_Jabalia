<?php
namespace Tests\Unit;

use App\Services\HistoricalFilesQueryService;
use PHPUnit\Framework\TestCase;

class HistoricalFilesQueryServiceTest extends TestCase
{
    public function test_it_normalizes_type_and_filters(): void
    {
        $service = new HistoricalFilesQueryService();

        $filters = [
            'type'        => 'ALL',
            'search'      => '   هندسة   ',
            'file_number' => ' 123 ',
            'owner_name'  => '  Ahmad  ',
            'status'      => '  خالص  ',
        ];

        $normalized = $service->normalizeFilters($filters);

        $this->assertSame('all', $normalized['type']);
        $this->assertSame('هندسة', $normalized['search']);
        $this->assertSame('123', $normalized['file_number']);
        $this->assertSame('Ahmad', $normalized['owner_name']);
        $this->assertSame('خالص', $normalized['status']);
    }

    public function test_it_keeps_the_requested_sources_only(): void
    {
        $service = new HistoricalFilesQueryService();

        $this->assertSame(['crafts', 'engineering'], $service->getIncludedSources('all'));
        $this->assertSame(['crafts'], $service->getIncludedSources('crafts'));
        $this->assertSame(['engineering'], $service->getIncludedSources('engineering'));
    }

    public function test_it_normalizes_column_and_date_filters(): void
    {
        $service = new HistoricalFilesQueryService();

        $filters = [
            'column'    => '  owner  ',
            'date_from' => ' 2024-01-01 ',
            'date_to'   => ' 2024-12-31 ',
        ];

        $normalized = $service->normalizeFilters($filters);

        $this->assertSame('owner', $normalized['column']);
        $this->assertSame('2024-01-01', $normalized['date_from']);
        $this->assertSame('2024-12-31', $normalized['date_to']);
    }

    public function test_it_matches_normalized_arabic_with_extended_rules(): void
    {
        $service = new HistoricalFilesQueryService();

        // تطابق التشكيل
        $this->assertTrue($service->containsNormalized('مُحَمَّدٌ خَالِدٌ', 'محمد خالد'));
        // تطابق الهمزات والياء المقصورة
        $this->assertTrue($service->containsNormalized('إبراهيم موسى', 'ابراهيم موسي'));
        // تطابق الهمزة على نبرة والواو
        $this->assertTrue($service->containsNormalized('مؤمن فائز', 'مومن فايز'));
        // fallback للكلمات بأي ترتيب
        $this->assertTrue($service->containsNormalized('أحمد محمد سالم عثمان', 'عثمان أحمد'));
        // أرقام عربية ومشرقية
        $this->assertTrue($service->containsNormalized('شارع ١٢ رقم ٣٤', 'شارع 12 رقم 34'));
    }

    public function test_it_ranks_exact_phrase_above_token_fallback(): void
    {
        $service = new HistoricalFilesQueryService();

        $dataExact = [
            'file_no'       => '100',
            'full_name'     => 'يوسف خضر علي',
            'detailed_type' => 'تجارة',
            'address'       => 'غزة',
        ];

        $dataTokens = [
            'file_no'       => '101',
            'full_name'     => 'علي خضر محمد يوسف',
            'detailed_type' => 'تجارة',
            'address'       => 'غزة',
        ];

        $relevExact  = $service->craftRelevance($dataExact, 'يوسف خضر');
        $relevTokens = $service->craftRelevance($dataTokens, 'يوسف خضر');

        // العبارة الكاملة من البداية تأخذ tier 1، بينما تطابق الكلمات المتفرقة يأخذ tier 3
        $this->assertSame(1, $relevExact[0]);
        $this->assertSame(3, $relevTokens[0]);
        $this->assertLessThan($relevTokens[0], $relevExact[0]);
    }
}

