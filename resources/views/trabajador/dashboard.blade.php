<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
    <p>Bienvenido, {{ auth()->user()->name }}. Este es tu panel de trabajador.</p>
</x-slot>
