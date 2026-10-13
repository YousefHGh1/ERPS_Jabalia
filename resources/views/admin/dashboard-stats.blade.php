{{-- Administrative Stats Cards (modernized to match the live admin dashboards) --}}
<div class="row mb-5">
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('employees')"
            :value="$stats['employees_count'] ?? 0"
            icon="fa-users"
            color="primary"
            :link="route('employees.index')"
            :linkText="__('View All')"
        />
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('Today Attendance')"
            :value="$stats['today_attendance'] ?? 0"
            icon="fa-fingerprint"
            color="success"
            :link="route('zk')"
            :linkText="__('View Attendance')"
        />
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('Pending Holidays')"
            :value="$stats['pending_holidays'] ?? 0"
            icon="fa-calendar-check"
            color="warning"
            :link="route('EmployeeHolidays.index')"
            :linkText="__('View Requests')"
        />
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('Pending Permissions')"
            :value="$stats['pending_permissions'] ?? 0"
            icon="fa-clock"
            color="info"
            :link="route('EmployeePermissions.index')"
            :linkText="__('View Requests')"
        />
    </div>
</div>

<div class="row mb-5">
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('jobs')"
            :value="$stats['jobs_count'] ?? 0"
            icon="fa-briefcase"
            color="danger"
            :link="route('jobs.index')"
            :linkText="__('View All')"
        />
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('vehicles')"
            :value="$stats['vehicles_count'] ?? 0"
            icon="fa-car"
            color="dark"
            :link="route('vehicle.index')"
            :linkText="__('View All')"
        />
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('departments')"
            :value="$stats['departments_count'] ?? 0"
            icon="fa-building"
            color="info"
            :link="route('departments.index')"
            :linkText="__('View All')"
        />
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <x-admin.stat-card
            :title="__('sections')"
            :value="$stats['sections_count'] ?? 0"
            icon="fa-sitemap"
            color="primary"
            :link="route('sections.index')"
            :linkText="__('View All')"
        />
    </div>
</div>

{{-- Cache Management --}}
<div class="row">
    <div class="col-12">
        <div class="card card-custom shadow-sm gutter-b">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title">
                    <span class="card-label font-weight-bolder text-dark">
                        <i class="fa fa-broom text-danger mr-2"></i>{{ __('Cache Management') }}
                    </span>
                </h3>
            </div>
            <div class="card-body pt-2">
                <form action="{{ route('admin.clear_cache') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">{{ __('Cache Type') }}</label>
                        <select name="type" class="form-control">
                            <option value="all">{{ __('All Caches') }}</option>
                            <option value="administrative">{{ __('Administrative Affairs') }}</option>
                            <option value="crafts">{{ __('Crafts and Industries') }}</option>
                            <option value="engineering">{{ __('Engineering and Organization') }}</option>
                            <option value="public_office">{{ __('Public Office') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger font-weight-bold">
                        <i class="fa fa-trash"></i> {{ __('Clear Cache') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

