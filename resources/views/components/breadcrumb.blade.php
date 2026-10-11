@props(['items' => []])

@if (count($items) > 0)
<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb breadcrumb-dot bg-transparent mb-0 py-1 px-0">
        @foreach ($items as $index => $item)
            <li class="breadcrumb-item {{ $loop->last ? 'active text-dark font-weight-bold' : '' }}">
                @if (($item['url'] ?? null) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="text-hover-primary">{{ $item['label'] }}</a>
                @else
                    <span>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
