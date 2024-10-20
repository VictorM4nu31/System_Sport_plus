<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Productos Disponibles</h1>

        <!-- Filtro de Productos -->
        <form method="GET" action="{{ route('usuario.products.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Filtrar por categoría -->
                <div>
                    <label for="category_id" class="block text-gray-700">Categoría</label>
                    <select id="category_id" name="category_id" class="block w-full mt-1">
                        <option value="">Todas las Categorías</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtrar por precio mínimo -->
                <div>
                    <label for="min_price" class="block text-gray-700">Precio Mínimo</label>
                    <input type="number" id="min_price" name="min_price" class="block w-full mt-1">
                </div>

                <!-- Filtrar por precio máximo -->
                <div>
                    <label for="max_price" class="block text-gray-700">Precio Máximo</label>
                    <input type="number" id="max_price" name="max_price" class="block w-full mt-1">
                </div>
            </div>

            <!-- Botón de Filtrar -->
            <div class="mb-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Filtrar</button>
            </div>
        </form>

        <!-- Listado de Productos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white p-4 shadow-sm rounded-lg">
                    <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover mb-4">
                    <h2 class="text-lg font-semibold">{{ $product->name }}</h2>
                    <p class="text-gray-600">${{ number_format($product->price, 2) }}</p>
                    <a href="#" class="text-blue-500">Agregar al carrito</a>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
