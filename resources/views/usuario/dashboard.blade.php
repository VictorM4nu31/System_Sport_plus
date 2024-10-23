<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
    <p>Bienvenido, {{ auth()->user()->name }}. Este es tu panel de usuario.</p>
</x-slot>

<!-- Menú de navegación para el usuario -->
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Opciones de Usuario</h3>
            <nav class="flex space-x-4">
                <a href="{{ route('usuario.cart.index') }}" class="px-4 py-2 bg-green-600 text-white rounded">Carrito de Compras</a>
                <a href="{{ route('usuario.orders.index') }}" class="px-4 py-2 bg-yellow-600 text-white rounded">Mis Pedidos</a>
            </nav>
        </div>
    </div>
</div>

<!-- Aquí renderizamos las diferentes vistas dependiendo de la ruta -->
@yield('content')
