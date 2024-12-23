<x-app-layout>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Título de la sección -->
        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-6">Productos Disponibles</h1>

        <!-- Formulario de Búsqueda y Filtrado -->
        <form method="GET" action="{{ route('usuario.products.index') }}">
            <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4 mb-6">
                <!-- Campo de búsqueda -->
                <input type="text"
                       name="search"
                       placeholder="Buscar productos"
                       value="{{ request('search') }}"
                       class="w-full sm:w-1/2 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" />

                <!-- Campo de selección de categoría -->
                <select name="category_id"
                        class="w-full sm:w-1/3 border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las Categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Botón de Filtrar -->
                <button type="submit"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-200">
                    Filtrar
                </button>
            </div>
        </form>

        <!-- Ícono del Carrito -->
        <div class="mb-6 flex items-center space-x-2">
            <a href="{{ route('usuario.cart.index') }}" class="flex items-center">
                <span class="material-icons text-white">shopping_cart</span>
                <span class="ml-2 text-base sm:text-lg font-semibold text-white">
                    {{ array_sum(array_column(session('cart', []), 'quantity')) }} artículos
                </span>
            </a>
        </div>

        <!-- Listado de Productos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach ($products as $product)
                <a href="{{ route('usuario.products.show', $product->id) }}"
                   class="bg-white p-4 sm:p-6 shadow-lg rounded-lg relative transition-transform hover:scale-105 duration-200 block">
                    <!-- Imagen del Producto -->
                    <img src="{{ '/storage/' . $product->image }}"
                         alt="{{ $product->name }}"
                         class="w-full h-32 sm:h-40 object-cover rounded-md mb-4">

                    <!-- Nombre y Precio del Producto -->
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">{{ $product->name }}</h2>
                    <p class="text-gray-600 mt-2 mb-4">${{ number_format($product->price, 2) }}</p>

                    <!-- Ícono del Corazón -->
                    <form action="{{ route('usuario.wishlist.add', $product->id) }}" method="POST"
                          class="absolute top-2 right-2 sm:top-4 sm:right-4">
                        @csrf
                        <button type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="currentColor"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 class="w-6 h-6 text-red-500 hover:text-red-600 transition-colors duration-200">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </button>
                    </form>
                </a>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
