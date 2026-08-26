@props(['on', 'type' => 'success'])

<div x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    style="display: none;"
    {{ $attributes->merge(['class' => 'text-body-sm font-medium ' . ($type === 'success' ? 'text-success' : ($type === 'error' ? 'text-error' : ($type === 'warning' ? 'text-warning' : 'text-primary')))]) }}>
    {{ $slot->isEmpty() ? 'Saved.' : $slot }}
</div>
