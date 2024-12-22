<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Flowbite -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Flowbite -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" rel="stylesheet">

</head>
<body class="font-sans antialiased">
    <div class="flex">
        @include('layouts.navigation')

        <!-- Contenido principal con margen izquierdo -->
        <div class="flex-1 overflow-y-auto p-6 bg-[#ECF0F1] ml-64">
            <header class="bg-[#282E2E] flex justify-between items-center p-4 shadow-md mb-4 rounded-lg bg-opacity-90">
                <!-- Título en blanco -->
                <div class="text-xl font-semibold text-white">@yield('title', 'Bienvenido')</div>

                <!-- Settings Dropdown -->
                <div class="flex items-center space-x-4">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-1 px-3 py-2 text-sm font-medium text-white hover:text-white transition ease-in-out duration-150 focus:outline-none">
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

            <div class="relative min-h-screen bg-gradient-to-b from-[#801336] via-[#801336] to-[#801336] overflow-hidden">
                <div class="absolute inset-0 -z-10">
                    <!-- Círculos grandes -->
                    <div class="absolute w-96 h-96 bg-[#EE4E50] opacity-40 rounded-full blur-3xl top-10 left-20"></div>
                    <div class="absolute w-72 h-72 bg-[#C72C41] opacity-30 rounded-full blur-2xl bottom-20 right-10"></div>
                    <div class="absolute w-80 h-80 bg-[#FF8B9B] opacity-30 rounded-full blur-3xl bottom-40 left-40"></div>
                    <div class="absolute w-48 h-48 bg-[#2D132C] opacity-25 rounded-full blur-2xl top-60 left-1/3"></div>

                    <!-- Nuevos círculos añadidos -->
                    <div class="absolute w-64 h-64 bg-[#C72C41] opacity-20 rounded-full blur-2xl top-80 left-5"></div>
                    <div class="absolute w-40 h-40 bg-[#EE4E50] opacity-25 rounded-full blur-2xl bottom-10 left-3/4"></div>
                    <div class="absolute w-56 h-56 bg-[#FF8B9B] opacity-35 rounded-full blur-3xl top-1/4 right-1/4"></div>
                    <div class="absolute w-32 h-32 bg-[#2D132C] opacity-20 rounded-full blur-2xl bottom-40 right-1/5"></div>
                    <div class="absolute w-20 h-20 bg-[#C72C41] opacity-30 rounded-full blur-2xl top-1/2 left-1/5"></div>
                </div>

                <div class="absolute inset-0 overflow-hidden">
                    <!-- Primer círculo en movimiento -->
                    <div class="absolute w-24 h-24 bg-[#EE4E50] rounded-full opacity-70 animate-bounce-1"></div>
                    <div class="absolute w-16 h-16 bg-[#C72C41] rounded-full opacity-70 animate-bounce-2"></div>
                    <div class="absolute w-20 h-20 bg-[#801336] rounded-full opacity-70 animate-bounce-3"></div>
                    <div class="absolute w-32 h-32 bg-[#2D132C] rounded-full opacity-60 animate-bounce-4"></div>
                    <div class="absolute w-12 h-12 bg-[#C72C41] rounded-full opacity-60 animate-bounce-5"></div>
                    <div class="absolute w-10 h-10 bg-[#EE4E50] rounded-full opacity-70 animate-bounce-6"></div>
                    <div class="absolute w-28 h-28 bg-[#801336] rounded-full opacity-60 animate-bounce-7"></div>
                </div>

                <div class="relative z-10">
                    <main>
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos personalizados para animaciones -->
    <style>
        /* Efectos de desenfoque */
        .blur-2xl {
            filter: blur(40px);
        }
        .blur-3xl {
            filter: blur(60px);
        }

        /* Animación de rebote de los círculos */
        @keyframes bounce {
            0% { transform: translateY(100vh); }
            100% { transform: translateY(-100vh); }
        }

        /* Diferentes velocidades y posiciones para los círculos animados */
        .animate-bounce-1 { animation: bounce 10s linear infinite; left: 10%; top: 50%; }
        .animate-bounce-2 { animation: bounce 12s linear infinite; left: 30%; top: 60%; }
        .animate-bounce-3 { animation: bounce 8s linear infinite; left: 70%; top: 40%; }
        .animate-bounce-4 { animation: bounce 15s linear infinite; left: 50%; top: 70%; }
        .animate-bounce-5 { animation: bounce 18s linear infinite; left: 15%; top: 30%; }
        .animate-bounce-6 { animation: bounce 7s linear infinite; left: 80%; top: 20%; }
        .animate-bounce-7 { animation: bounce 9s linear infinite; left: 40%; top: 10%; }
    </style>
</body>
</html>
