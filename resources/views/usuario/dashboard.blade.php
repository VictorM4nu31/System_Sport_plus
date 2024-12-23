<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
    <p>Bienvenido, {{ auth()->user()->name }}. Este es tu panel de usuario.</p>
</x-slot>

<!-- Menú de navegación para el usuario -->
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Contenedor con fondo blanco semitransparente -->
        <div class="bg-white bg-opacity-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Opciones de Usuario</h3>
            <nav class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                <!-- Botón Carrito de Compras -->
                <a href="{{ route('usuario.cart.index') }}" class="px-4 py-2 bg-green-600 text-white rounded flex items-center space-x-2">
                    <span class="material-icons">shopping_cart</span>
                    <span>Carrito de Compras</span>
                </a>

                <!-- Botón Mis Pedidos -->
                <a href="{{ route('usuario.orders.index') }}" class="px-4 py-2 bg-yellow-600 text-white rounded flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25V4.5M9.75 4.5h4.5m-6.75 9h9m-9 3h9m-9 3h9m-9-9h9M5.25 4.5h13.5a.75.75 0 01.75.75v14.25a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V5.25a.75.75 0 01.75-.75z" />
                    </svg>
                    <span>Mis Pedidos</span>
                </a>

                <!-- Botón Mi historial de Pedidos -->
                <a href="{{ route('usuario.orders.history') }}" class="px-4 py-2 bg-yellow-900 text-white rounded flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M12 3.75a8.25 8.25 0 11-8.25 8.25A8.25 8.25 0 0112 3.75z" />
                    </svg>
                    <span>Mi historial de Pedidos</span>
                </a>
            </nav>
        </div>
    </div>
</div>

<!-- Aquí renderizamos las diferentes vistas dependiendo de la ruta -->
@yield('content')
