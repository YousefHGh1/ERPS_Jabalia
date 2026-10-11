{{-- عرض معلومات المدير - قابل لإعادة الاستخدام --}}
@props(['name' => null, 'label' => __('Manager')])

<div class="d-flex align-items-center text-muted font-size-sm mt-2">
    <i class="fas fa-user-tie ml-2" style="width: 1rem;"></i>
    <span>{{ $label }}: {{ $name ?? __('vacant') }}</span>
</div>
