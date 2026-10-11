<?php
namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Services\HistoricalFilesQueryService;
use Illuminate\Http\Request;

/**
 * بحث وعرض الملفات التاريخية (هندسة وتنظيم) من جدول MASTER
 */
class LegacyEngineeringController extends Controller
{
    public function index(Request $request, HistoricalFilesQueryService $queryService)
    {
        $filters = $queryService->normalizeFilters($request->only(['type', 'search', 'file_number', 'owner_name', 'status']));
        $sources = $queryService->getIncludedSources($filters['type']);

        $query = MasterFile::query()->where('ISDELETE', 0);

        $search     = $filters['search'];
        $fileNo     = $filters['file_number'];
        $fileStatus = $filters['status'];
        $ownerName  = $filters['owner_name'];

        if ($sources === ['crafts']) {
            $query->whereRaw('1 = 0');
        }

        if ($search !== '') {
            $query->smartSearch($search)->orderBySearchRelevance($search);
        } else {
            $query->orderByDesc('ID');
        }

        if ($fileNo !== '') {
            $query->smartSearch('', $fileNo);
        }

        if ($ownerName !== '') {
            $query->smartSearch('', '', $ownerName);
        }

        if ($fileStatus !== '') {
            $query->smartSearch('', '', '', $fileStatus);
        }

        $files = $query->paginate(20)->withQueryString();

        return view('legacy-engineering.index', compact('files', 'filters'));
    }

    public function show(int $id)
    {
        $file = MasterFile::where('ID', $id)->where('ISDELETE', 0)->firstOrFail();

        return view('legacy-engineering.show', compact('file'));
    }
}
