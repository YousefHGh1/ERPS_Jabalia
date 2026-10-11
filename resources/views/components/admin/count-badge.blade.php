{{-- عرض عدد الأقسام/الشعب/الموظفين - قابل لإعادة الاستخدام --}}
@props(['count' => 0, 'label' => null, 'icon' => 'fa-th-list'])

<div class="d-flex align-items-center text-muted font-size-sm mt-2">
    <i class="fas {{ $icon }} ml-2" style="width: 1rem;"></i>
    <span>{{ (int) $count }} {{ $label }}</span>
</div>
