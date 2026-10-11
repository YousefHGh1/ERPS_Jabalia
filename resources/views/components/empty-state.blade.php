@props([
    'icon' => 'fa-inbox',
    'title' => 'لا توجد بيانات',
    'message' => 'لم يتم العثور على أي سجلات',
    'action' => null,
])

<div class="view-empty-state">
    <div class="empty-icon">
        <i class="fas {{ $icon }}"></i>
    </div>
    <strong class="d-block mb-1">{{ $title }}</strong>
    <span class="d-block mb-4">{{ $message }}</span>
    @if($action)
        <div>{{ $action }}</div>
    @endif
    {{ $slot }}
</div>
