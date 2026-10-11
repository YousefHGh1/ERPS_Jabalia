<?php

namespace App\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HomeDashboardComposer
{
    /**
     * Bind dashboard visibility flags to the home view.
     * Permission checks are done once here instead of repeated @can in Blade.
     * Keys match the permissions used on the dashboard (spaces as underscores).
     */
    public function compose(View $view): void
    {
        $view->with('dashboardSections', $this->buildSections());
    }

    /**
     * @return array<string, bool>
     */
    protected function buildSections(): array
    {
        $user = Auth::user();
        if ($user === null) {
            return [];
        }

        $sections = [];
        $permissions = [
            'index_employees'         => 'index employees',
            'index_zk'                => 'index zk',
            'index_employee_holiday'  => 'index employee_holiday',
            'index_EmployeePermissions' => 'index EmployeePermissions',
            'index_jobs'              => 'index jobs',
            'index_vehicle'           => 'index vehicle',
            'index_departments'       => 'index departments',
            'index_sections'          => 'index sections',
            'index_engineerings'      => 'index engineerings',
            'export_engineerings'     => 'export engineerings',
            'index_craft_licenses'    => 'index craft_licenses',
            'export_craft_licenses'   => 'export craft_licenses',
            'index_requests'          => 'index requests',
            'index_reservations'      => 'index reservations',
            'index_notices'           => 'index notices',
            'index_imprisonment_orders' => 'index imprisonment_orders',
            'create_craft_licenses'   => 'create craft_licenses',
            'index_employee_reports'  => 'index employee_reports',
            'store_requests'          => 'store requests',
        ];

        foreach ($permissions as $key => $permission) {
            $sections[$key] = $user->can($permission);
        }

        return $sections;
    }
}
