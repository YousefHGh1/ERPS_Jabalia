{{-- تبويبات المديرين: مدير الدائرة، مدير القسم، مدير الشعبة --}}
@props(['active' => 'department']) {{-- department|section|branch --}}

<nav class="nav nav-pills nav-pills-custom mb-5 flex-nowrap overflow-auto" role="tablist">
    <a href="{{ route('departmentdirector.index') }}" class="nav-link py-4 px-6 {{ $active === 'department' ? 'active' : '' }}">
        <i class="fas fa-building ml-2"></i>
        <span>{{ __('department director') }}</span>
    </a>
    <a href="{{ route('sectiondirector.index') }}" class="nav-link py-4 px-6 {{ $active === 'section' ? 'active' : '' }}">
        <i class="fas fa-th-list ml-2"></i>
        <span>{{ __('section director') }}</span>
    </a>
    <a href="{{ route('branchdirector.index') }}" class="nav-link py-4 px-6 {{ $active === 'branch' ? 'active' : '' }}">
        <i class="fas fa-code-branch ml-2"></i>
        <span>{{ __('branch director') }}</span>
    </a>
</nav>
