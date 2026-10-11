{{-- دليل بسيط للألوان والخطوط المعتمدة في النظام --}}
<div class="container-fluid my-4">
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 py-4">
            <h3 class="card-title mb-0 font-weight-bolder text-dark">
                {{ __('دليل الواجهة (Style Guide)') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="font-weight-bold text-dark mb-3">{{ __('الأزرار الأساسية') }}</h5>
                    <button class="btn btn-primary mr-2 mb-2">{{ __('أساسي (Primary)') }}</button>
                    <button class="btn btn-success mr-2 mb-2">{{ __('نجاح (Success)') }}</button>
                    <button class="btn btn-danger mr-2 mb-2">{{ __('تحذير/حذف (Danger)') }}</button>
                    <button class="btn btn-light-primary mr-2 mb-2">{{ __('ثانوي (Light Primary)') }}</button>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="font-weight-bold text-dark mb-3">{{ __('الشارات (Badges)') }}</h5>
                    <span class="badge badge-primary mr-1 mb-1">{{ __('معلومة') }}</span>
                    <span class="badge badge-success mr-1 mb-1">{{ __('ناجح') }}</span>
                    <span class="badge badge-warning mr-1 mb-1">{{ __('تحذير') }}</span>
                    <span class="badge badge-danger mr-1 mb-1">{{ __('خطر') }}</span>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="font-weight-bold text-dark mb-3">{{ __('أنماط الجداول') }}</h5>
                    <p class="text-muted mb-2" style="font-size: 0.875rem;">
                        {{ __('استخدم الجداول من نوع') }} <code>table-head-custom table-vertical-center</code>
                        {{ __('مع ألوان متناسقة للعناوين وخط بحجم موحّد لصفوف البيانات.') }}
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                        {{ __('يفضل استخدام') }} <code>table-responsive</code> {{ __('لضمان عمل الجداول على الشاشات الصغيرة.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

