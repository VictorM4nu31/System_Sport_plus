<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Productos Disponibles</h1>

        <!-- Ícono del Carrito con Cantidad de Artículos -->
        <div class="mb-6">
            <a href="{{ route('usuario.cart.index') }}" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-700">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l1.38-8H6.62M7 13h10l1 6H6l1-6zM16 17h-8m1 0a1 1 0 011 1v2a1 1 0 001 1h4a1 1 0 001-1v-2a1 1 0 011-1z"/>
                </svg>
                <span class="ml-2 text-gray-700">{{ array_sum(array_column(session('cart', []), 'quantity')) }} artículos</span>
            </a>
        </div>

        <!-- Listado de Productos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white p-4 shadow-sm rounded-lg">
                    <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover mb-4">
                    <h2 class="text-lg font-semibold">{{ $product->name }}</h2>
                    <p class="text-gray-600">${{ number_format($product->price, 2) }}</p>

                    <!-- Formulario para seleccionar cantidad y agregar al carrito -->
                    <form action="{{ route('usuario.cart.add', $product->id) }}" method="POST">
                        @csrf
                        <label for="quantity" class="block text-gray-700 mt-2">Cantidad:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" class="block w-full mb-4">

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                            Agregar al carrito
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
