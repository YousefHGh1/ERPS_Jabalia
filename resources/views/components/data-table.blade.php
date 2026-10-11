@props([
    'headers' => [],
    'rows' => null,
    'empty' => true,
    'emptyIcon' => 'fa-inbox',
    'emptyTitle' => 'لا توجد بيانات',
    'emptyMessage' => 'لم يتم العثور على أي سجلات',
    'hover' => true,
    'striped' => false,
    'bordered' => false,
    'responsive' => true,
    'compact' => false,
])

@php
    $tableClasses = collect();
    $tableClasses->push('table');
    if ($hover) $tableClasses->push('table-hover');
    if ($striped) $tableClasses->push('table-striped');
    if ($bordered) $tableClasses->push('table-bordered');
    if ($compact) $tableClasses->push('table-sm');
    $tableClass = $tableClasses->implode(' ');
@endphp

@if($responsive)
<div class="table-responsive">
@endif
    <table class="{{ $tableClass }}">
        <thead class="table-light">
            <tr>
                @foreach($headers as $header)
                    <th class="text-uppercase font-weight-bolder font-size-sm">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if($rows && $rows->count() > 0)
                @foreach($rows as $row)
                    <tr>
                        @foreach($headers as $key => $header)
                            <td>{{ $row[$key] ?? $row[strtolower(str_replace(' ', '_', $header))] ?? '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @elseif($empty)
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center py-10">
                        <x-empty-state 
                            :icon="$emptyIcon" 
                            :title="$emptyTitle" 
                            :message="$emptyMessage" 
                        />
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
@if($responsive)
</div>
@endif
