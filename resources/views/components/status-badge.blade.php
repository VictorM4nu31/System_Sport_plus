@props(['type' => 'info'])

@php
    $classes = match($type) {
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'error' => 'badge-error',
        default => 'badge-info'
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
