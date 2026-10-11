<!-- Administrative Stats Cards -->
<div class="row">
    <x-stats-card 
        title="إجمالي الموظفين" 
        :value="$stats['employees_count']" 
        icon="fa-users" 
        color="primary" 
    />
    
    <x-stats-card 
        title="الحضور اليوم" 
        :value="$stats['today_attendance']" 
        icon="fa-user-check" 
        color="success" 
    />
    
    <x-stats-card 
        title="إجازات معلقة" 
        :value="$stats['pending_holidays']" 
        icon="fa-calendar-times" 
        color="warning" 
    />
    
    <x-stats-card 
        title="تصاريح معلقة" 
        :value="$stats['pending_permissions']" 
        icon="fa-clock" 
        color="info" 
    />
</div>

<div class="row mt-3">
    <x-stats-card 
        title="الوظائف" 
        :value="$stats['jobs_count']" 
        icon="fa-briefcase" 
        color="secondary" 
    />
    
    <x-stats-card 
        title="المركبات" 
        :value="$stats['vehicles_count']" 
        icon="fa-car" 
        color="dark" 
    />
    
    <x-stats-card 
        title="الإدارات" 
        :value="$stats['departments_count']" 
        icon="fa-building" 
        color="info" 
    />
    
    <x-stats-card 
        title="الأقسام" 
        :value="$stats['sections_count']" 
        icon="fa-sitemap" 
        color="primary" 
    />
</div>

<!-- Cache Management -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cog"></i>
                    إدارة الكاش
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.clear_cache') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="type">اختر نوع الكاش:</label>
                        <select name="type" id="type" class="form-control">
                            <option value="all">جميع الكاشات</option>
                            <option value="administrative">الشؤون الإدارية</option>
                            <option value="crafts">الحرف والصناعات</option>
                            <option value="engineering">الهندسة والتنظيم</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-trash"></i>
                        مسح الكاش
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
