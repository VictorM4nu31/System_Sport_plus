@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-body-md font-medium text-success']) }} role="status">
        {{ $status }}
    </div>
@endif
