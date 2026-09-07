<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="stripe-key" content="{{ config('stripe.key') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Optimized Font Loading -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="preload" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"></noscript>
    <!-- Scripts (Vite build único; sin CDN duplicados) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="flex flex-col md:flex-row">
        @include('layouts.navigation')

        <!-- Contenido principal con margen izquierdo responsivo -->
        <div class="flex-1 overflow-y-auto p-6 bg-[#ECF0F1] md:ml-64">
            <header class="bg-[#282E2E] flex justify-between items-center p-4 shadow-md mb-4 rounded-lg bg-opacity-90">
                <!-- Título en blanco -->
                <div class="text-heading-lg text-white">@yield('title', 'Bienvenido')</div>

                <!-- Settings Dropdown -->
                <div class="flex items-center space-x-4">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-1 px-3 py-2 text-body-md font-medium text-white hover:text-white transition ease-in-out duration-150 focus:outline-none">
                                <span>{{ Auth::user()->name ?? 'Administrador' }}</span>
                                <svg class="fill-current h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Opción de Perfil -->
                            <x-dropdown-link :href="route('profile.edit')">
                                <span class="flex items-center space-x-2">
                                    <span class="material-icons">person</span>
                                    <span>{{ __('Profile') }}</span>
                                </span>
                            </x-dropdown-link>

                            <!-- Opción de Cerrar Sesión -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                    <span class="flex items-center space-x-2">
                                        <span class="material-icons">logout</span>
                                        <span>{{ __('Log Out') }}</span>
                                    </span>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <div class="relative min-h-screen overflow-hidden">
                <div class="relative z-10">
                    <main>
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
