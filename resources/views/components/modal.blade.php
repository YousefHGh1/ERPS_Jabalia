@props([
    'id' => null,
    'title' => null,
    'size' => 'md',
    'centered' => false,
    'scrollable' => false,
    'backdrop' => true,
    'module' => null,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'modal-sm',
        'md' => '',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
        'fullscreen' => 'modal-fullscreen',
        default => '',
    };

    // هوية القسم للون التلقائي
    $theme = module_theme($module);
    $moduleVars = '--module-accent: ' . $theme['color'] . ';'
        . ' --module-accent-rgb: ' . $theme['rgb'] . ';'
        . ' --module-accent-soft: ' . $theme['soft'] . ';'
        . ' --module-accent-strong: ' . $theme['strong'] . ';';
@endphp

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    {{ $attributes }}
>
    <div class="modal-dialog {{ $sizeClasses }} {{ $centered ? 'modal-dialog-centered' : '' }} {{ $scrollable ? 'modal-dialog-scrollable' : '' }}">
        <div class="modal-content" style="{{ $moduleVars }}">
            @if($title || $slot->exists('header'))
                <div class="modal-header">
                    @if($title)
                        <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    @endif

                    @if($slot->exists('header'))
                        {{ $header }}
                    @endif

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endif

            <div class="modal-body">
                {{ $slot }}
            </div>

            @if($slot->exists('footer'))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
