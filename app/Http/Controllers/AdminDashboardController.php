<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * عرض صفحة الشؤون الإدارية
     */
    public function administrative()
    {
        $data = $this->dashboardService->getAdministrativeDashboardData();

        return view('admin.administrative', $data);
    }

    /**
     * عرض صفحة الحرف والصناعات
     */
    public function crafts()
    {
        $data = $this->dashboardService->getCraftsDashboardData();

        return view('admin.crafts', $data);
    }

    /**
     * عرض صفحة الهندسة والتنظيم
     */
    public function engineering()
    {
        $data = $this->dashboardService->getEngineeringDashboardData();

        return view('admin.engineering', $data);
    }

    /**
     * مسح كاش Dashboard
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        
        if ($type === 'all') {
            $this->dashboardService->clearDashboardCache();
            $message = 'تم مسح جميع كاشات Dashboard بنجاح';
        } else {
            $this->dashboardService->clearSpecificCache($type);
            $message = "تم مسح كاش {$type} بنجاح";
        }

        return redirect()->back()->with('success', $message);
    }
}
