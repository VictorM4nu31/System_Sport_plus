<x-app-layout>
    @section('title', 'Dashboard de Usuario')

    <!-- Menú de navegación para el usuario -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor con fondo blanco semitransparente -->
            <div class="bg-white bg-opacity-90 overflow-hidden shadow-lg sm:rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-display-sm text-primary mb-2">Dashboard de Usuario</h2>
                    <p class="text-body-md text-primary-light">Bienvenido, {{ auth()->user()->name }}. Este es tu panel de usuario.</p>
                </div>

                <h3 class="text-heading-lg text-primary mb-4">Opciones de Usuario</h3>
                <nav class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                    <!-- Botón Carrito de Compras -->
                    <a href="{{ route('usuario.cart.index') }}" class="px-6 py-3 bg-[#059669] hover:bg-[#047857] text-white rounded-lg flex items-center space-x-2 transition duration-200 shadow-md">
                        <span class="material-icons">shopping_cart</span>
                        <span class="text-body-md font-medium">Carrito de Compras</span>
                    </a>

                    <!-- Botón Mis Pedidos -->
                    <a href="{{ route('usuario.orders.index') }}" class="px-6 py-3 bg-primary hover:bg-primary-700 text-white rounded-lg flex items-center space-x-2 transition duration-200 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25V4.5M9.75 4.5h4.5m-6.75 9h9m-9 3h9m-9 3h9m-9-9h9M5.25 4.5h13.5a.75.75 0 01.75.75v14.25a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V5.25a.75.75 0 01.75-.75z" />
                        </svg>
                        <span class="text-body-md font-medium">Mis Pedidos</span>
                    </a>

                    <!-- Botón Mi historial de Pedidos -->
                    <a href="{{ route('usuario.orders.history') }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center space-x-2 transition duration-200 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M12 3.75a8.25 8.25 0 11-8.25 8.25A8.25 8.25 0 0112 3.75z" />
                        </svg>
                        <span class="text-body-md font-medium">Mi historial de Pedidos</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Aquí renderizamos las diferentes vistas dependiendo de la ruta -->
    @yield('content')
</x-app-layout>
