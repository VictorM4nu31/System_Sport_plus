@props(['type' => 'info', 'dismissible' => false, 'icon' => true])

@php
    $classes = match($type) {
        'success' => 'message-success',
        'warning' => 'message-warning',
        'error' => 'message-error',
        default => 'message-info'
    };

    $iconSvg = match($type) {
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-4.5 0v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
        default => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />'
    };
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if($dismissible) x-data="{ show: true }" x-show="show" @endif>
    @if($icon)
        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            {!! $iconSvg !!}
        </svg>
    @endif

    <div class="flex-1">
        {{ $slot }}
    </div>

    @if($dismissible)
        <button type="button" class="flex-shrink-0 ml-2 opacity-70 hover:opacity-100 transition-opacity" @click="show = false">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
