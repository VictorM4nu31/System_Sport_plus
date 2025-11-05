<x-slot name="header">
    <h2 class="text-display-sm text-primary font-bold">
        {{ __('Dashboard') }}
    </h2>
    <p class="text-body-lg text-primary-light font-regular mt-2">Bienvenido, {{ auth()->user()->name }}. Este es tu panel de trabajador.</p>
</x-slot>
