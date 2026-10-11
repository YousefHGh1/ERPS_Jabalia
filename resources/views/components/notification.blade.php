@props([
    'type' => 'info',
    'message' => null,
    'title' => null,
    'duration' => 3000,
    'position' => 'toast-top-center',
])

@php
    $typeConfig = [
        'success' => [
            'class' => 'toast-success',
            'icon' => 'fa-check-circle',
            'title' => __('Success')
        ],
        'error' => [
            'class' => 'toast-error',
            'icon' => 'fa-times-circle',
            'title' => __('Error')
        ],
        'warning' => [
            'class' => 'toast-warning',
            'icon' => 'fa-exclamation-triangle',
            'title' => __('Warning')
        ],
        'info' => [
            'class' => 'toast-info',
            'icon' => 'fa-info-circle',
            'title' => __('Info')
        ]
    ];

    if (array_key_exists($type, $typeConfig)) {
        $config = $typeConfig[$type];
    } else {
        $config = $typeConfig['info'];
    }
    
    $displayTitle = isset($title) && $title ? $title : $config['title'];
@endphp

@push('scripts')
<script>
    @if($message)
    $(document).ready(function() {
        toastr.{{ $type }}(
            '{{ $message }}',
            '{{ $displayTitle }}',
            {
                closeButton: true,
                progressBar: true,
                timeOut: {{ $duration }},
                positionClass: '{{ $position }}',
                preventDuplicates: true,
                newestOnTop: true
            }
        );
    });
    @endif
</script>
@endpush
