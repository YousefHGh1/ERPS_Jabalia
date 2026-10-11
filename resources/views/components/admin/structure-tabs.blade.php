{{-- تبويبات الهيكل الإداري: الدوائر، الأقسام، الشعب --}}
@props(['active' => 'departments']) {{-- departments|sections|branches|chart|builder --}}

<nav class="nav nav-pills nav-pills-custom mb-5 flex-nowrap overflow-auto" role="tablist">
    <a href="{{ route('departments.index') }}" class="nav-link py-4 px-6 {{ $active === 'departments' ? 'active' : '' }}">
        <i class="fas fa-building ml-2"></i>
        <span>{{ __('departments') }}</span>
    </a>
    <a href="{{ route('sections.index') }}" class="nav-link py-4 px-6 {{ $active === 'sections' ? 'active' : '' }}">
        <i class="fas fa-th-list ml-2"></i>
        <span>{{ __('sections') }}</span>
    </a>
    <a href="{{ route('branches.index') }}" class="nav-link py-4 px-6 {{ $active === 'branches' ? 'active' : '' }}">
        <i class="fas fa-code-branch ml-2"></i>
        <span>{{ __('branches') }}</span>
    </a>
    @can('index departments')
    <a href="{{ route('structure.org-chart') }}" class="nav-link py-4 px-6 {{ $active === 'chart' ? 'active' : '' }}">
        <i class="fas fa-sitemap ml-2"></i>
        <span>{{ __('org_chart') }}</span>
    </a>
    <a href="{{ route('structure.org-chart.builder') }}" class="nav-link py-4 px-6 {{ $active === 'builder' ? 'active' : '' }}">
        <i class="fas fa-project-diagram ml-2"></i>
        <span>{{ __('manual_chart') }}</span>
    </a>
    @endcan
</nav>
