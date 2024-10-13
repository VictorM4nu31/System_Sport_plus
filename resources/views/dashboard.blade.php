<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 text-gray-900 dark:text-gray-100">
        @if (auth()->user()->hasRole('administrador'))
            @include('admin.dashboard')
        @elseif(auth()->user()->hasRole('trabajador'))
            @include('trabajador.dashboard')
        @elseif(auth()->user()->hasRole('usuario'))
            @include('usuario.dashboard')
        @else
            <p>Acceso denegado.</p>
        @endif
    </div>
</x-app-layout>
