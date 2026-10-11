<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use Illuminate\Http\Request;

class CachedController extends Controller
{
    /**
     * صفحة الموظفين مع الكاش
     */
    public function employees(Request $request)
    {
        $filters = [
            'department_id' => $request->get('department_id'),
            'section_id' => $request->get('section_id'),
            'status' => $request->get('status'),
            'search' => $request->get('search')
        ];

        $employees = CacheService::getEmployees($filters);
        $departments = \App\Models\Department::all();
        $sections = \App\Models\Section::all();

        return view('employees.index_cached', compact('employees', 'departments', 'sections'));
    }

    /**
     * صفحة الطلبات مع الكاش
     */
    public function requests(Request $request)
    {
        $filters = [
            'status_id' => $request->get('status_id'),
            'request_type_id' => $request->get('request_type_id'),
            'is_urgent' => $request->get('is_urgent'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to')
        ];

        $requests = CacheService::getRequests($filters);
        $stats = CacheService::getRequestStats();
        $requestTypes = \App\Models\RequestType::all();
        $requestStatuses = \App\Models\RequestStatus::all();

        return view('requests.index_cached', compact('requests', 'stats', 'requestTypes', 'requestStatuses'));
    }

    /**
     * صفحة الإحصائيات مع الكاش
     */
    public function dashboard()
    {
        $stats = CacheService::getRequestStats();
        $recentRequests = CacheService::getRequests(['limit' => 10]);
        $recentEmployees = CacheService::getEmployees(['limit' => 10]);

        return view('dashboard.cached', compact('stats', 'recentRequests', 'recentEmployees'));
    }

    /**
     * صفحة التقارير مع الكاش
     */
    public function reports(Request $request)
    {
        $type = $request->get('type', 'employees');
        
        switch ($type) {
            case 'employees':
                $data = CacheService::getEmployees();
                break;
            case 'requests':
                $data = CacheService::getRequests();
                break;
            case 'craft_licenses':
                $data = CacheService::getCraftLicenses();
                break;
            default:
                $data = [];
        }

        return view('reports.cached', ['type' => $type, 'data' => $data]);
    }

    /**
     * صفحة المكلفين مع الكاش
     */
    public function mokalafs(Request $request)
    {
        $filters = [
            'search' => $request->get('search')
        ];

        $mokalafs = CacheService::getMokalafs($filters);

        return view('mokalafs.index_cached', compact('mokalafs'));
    }
}
