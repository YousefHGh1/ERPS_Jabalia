<?php
namespace App\Http\Controllers;

use App\Models\HistoricalCraft;
use App\Services\HistoricalFilesQueryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HistoricalFilesController extends Controller
{
    private const PER_PAGE = 15;

    /**
     * أسماء حقول البحث الموحدة (تُستخدم في الـ blade والتحكم).
     */
    private const FILTER_KEYS = [
        'search',
        'file_number',
        'owner_name',
        'status',
        'column',
        'date_from',
        'date_to',
    ];

    public function index(Request $request, HistoricalFilesQueryService $queryService)
    {
        $filters = $queryService->normalizeFilters($request->only(self::FILTER_KEYS));
        $hasSearch = $queryService->hasActiveFilters($filters);

        $results = new LengthAwarePaginator([], 0, self::PER_PAGE, 1, [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]);

        if ($hasSearch) {
            $results = $this->paginateFiltered($request, $queryService, $filters);
        }

        return view('historical-files.index', [
            'results'   => $results,
            'filters'   => $filters,
            'hasSearch' => $hasSearch,
        ]);
    }

    /**
     * التصفية الدقيقة بعد التضييق الأولي + الترتيب بالقرب من البحث + الترقيم.
     *
     * @return LengthAwarePaginator
     */
    protected function paginateFiltered(Request $request, HistoricalFilesQueryService $queryService, array $filters): LengthAwarePaginator
    {
        $rows = collect();

        HistoricalCraft::query()
            ->prefiltered($filters)
            ->orderByDesc('created_at')
            ->cursor()
            ->each(function ($item) use ($queryService, $filters, $rows) {
                $data = (array) ($item->data ?? []);
                $itemDate = $item->created_at?->toDateString();

                if (! $queryService->craftMatchesFilters($data, $filters, $itemDate)) {
                    return;
                }

                $rows->push([
                    'row'      => $queryService->craftDisplayRow($data, $item->id, $item->created_at?->format('Y-m-d')),
                    'relev'    => $queryService->craftRelevance($data, (string) ($filters['search'] ?? ''), $itemDate),
                    'fallback' => $item->id,
                ]);
            });

        $sorted = $rows->sort(function ($a, $b) {
            foreach ([0, 1, 2] as $i) {
                if ($a['relev'][$i] !== $b['relev'][$i]) {
                    return $a['relev'][$i] <=> $b['relev'][$i];
                }
            }
            if ($a['relev'][3] !== $b['relev'][3]) {
                return $b['relev'][3] <=> $a['relev'][3];
            }

            return $b['fallback'] <=> $a['fallback'];
        })->map(fn ($entry) => $entry['row'])->values();

        $currentPage = max(1, (int) $request->query('page', 1));

        return new LengthAwarePaginator(
            $sorted->forPage($currentPage, self::PER_PAGE)->values(),
            $sorted->count(),
            self::PER_PAGE,
            $currentPage,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    public function export(Request $request, HistoricalFilesQueryService $queryService)
    {
        $filters = $queryService->normalizeFilters($request->only(self::FILTER_KEYS));

        $rows = [];

        HistoricalCraft::query()
            ->prefiltered($filters)
            ->orderByDesc('created_at')
            ->cursor()
            ->each(function ($item) use ($queryService, $filters, &$rows) {
                $data = (array) ($item->data ?? []);
                $itemDate = $item->created_at?->toDateString();

                if (! $queryService->craftMatchesFilters($data, $filters, $itemDate)) {
                    return;
                }

                $display = $queryService->craftDisplayRow($data, $item->id, $item->created_at?->format('Y-m-d'));

                $rows[] = [
                    'نوع الحرفة' => $display['craft_type'],
                    'رقم الملف' => $display['file_number'],
                    'المالك'    => $display['owner'],
                    'الحالة'    => $display['status'],
                    'حالة الملف' => $display['file_status'],
                    'العنوان'   => $display['address'],
                    'الملخص'    => $display['summary'],
                    'التاريخ'   => $display['date'],
                ];
            });

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['نوع الحرفة', 'رقم الملف', 'المالك', 'الحالة', 'حالة الملف', 'العنوان', 'الملخص', 'التاريخ']], null, 'A1');
        $sheet->fromArray($rows, null, 'A2');

        $fileName = 'historical_files_' . now()->format('Ymd_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'historical_files');
        $path     = $tempFile . '.xlsx';
        rename($tempFile, $path);

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * رقم الملف الحقيقي في البيانات المستوردة هو file_no.
     * أي مفاتيح بديلة (لو انضافت مصادر بيانات تانية بمفاتيح مختلفة) بتضل مدعومة كـ fallback.
     */
    private function craftFileNumber(array $data): string
    {
        return (string) ($data['file_no'] ?? $data['file_number'] ?? $data['File Number'] ?? $data['رقم الملف'] ?? '');
    }

    /**
     * full_name موجود جاهز بالبيانات المستوردة، وإذا مش موجود بنركّبه من الاسم الرباعي
     * بنفس منطق الـ blade (first_name + father_name + grand_name + family_name).
     */
    private function craftOwnerName(array $data): string
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
     * (كتير سجلات الـ craft_status عندها فاضي والحالة الفعلية موجودة بس جوا note،
     * زي "الحرفة مغلقة" أو "الحرفة متوقفة").
     */
    private function craftStatus(array $data): string
    {
        if (! empty($data['craft_status'])) {
            return (string) $data['craft_status'];
        }

        if (! empty($data['note'])) {
            // بعض قيم note فيها كشيدة (ـ) للتزيين، منشيلها قبل ما نعرضها كحالة
            return trim(str_replace("\u{0640}", '', (string) $data['note']));
        }

        return (string) ($data['status'] ?? $data['Status'] ?? $data['الحالة'] ?? '');
    }

    /**
     * نوع الحرفة، بيحل محل "المصدر" الثابت بالعرض.
     */
    private function craftDetailedType(array $data): string
    {
        return (string) ($data['detailed_type'] ?? '');
    }

    /**
     * عمود "حالة الملف" بالعرض يعرض قيمة debt_2017 من ملف JSON.
     */
    private function craftFileStatus(array $data): string
    {
        return (string) ($data['debt_2017'] ?? '');
    }

    /**
     * الملخص المختصر: المبلغ + أول ترخيص + آخر رخصة فقط.
     */
    private function craftSummary(array $data): string
    {
        $lastLicense = $data['last_license_year'] ?? null;
        if (! empty($lastLicense)) {
            // القيمة مخزّنة كتاريخ كامل "2019-11-17 00:00:00"، بناخد الجزء التاريخ بس
            $lastLicense = substr((string) $lastLicense, 0, 10);
        }

        $parts = [
            'المبلغ'      => $data['balance'] ?? null,
            'أول ترخيص'   => $data['first_license_year'] ?? null,
            'آخر رخصة'    => $lastLicense,
        ];

        $parts = array_filter($parts, fn ($value) => $value !== null && $value !== '');

        return collect($parts)
            ->map(fn ($value, $label) => "{$label}: {$value}")
            ->implode(' | ');
    }

    private function resolveCraftColumn(string $column): ?string
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
