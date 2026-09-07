<x-app-layout>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6" data-catalogo
         data-catalogo-endpoint="{{ route('usuario.products.search') }}"
         data-catalogo-cart-base="{{ url('/carrito') }}">
        <!-- Título de la sección -->
        <div class="text-center mb-8">
            <h1 class="text-display-md text-primary mb-2">Tienda Deportiva</h1>
            <p class="text-body-lg text-primary-light">Encuentra el equipamiento perfecto para tu deporte favorito</p>
        </div>

        <!-- Filtros Avanzados (con búsqueda instantánea; sin JS el GET clásico sigue funcionando) -->
        <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-heading-md text-primary mb-4">Filtros de Búsqueda</h2>
            <p class="sr-only" aria-live="polite" data-catalogo-live></p>
            <form method="GET" action="{{ route('usuario.products.index') }}" data-catalogo-form>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Búsqueda por nombre -->
                    <div>
                        <label class="block text-primary text-body-md font-medium mb-1">Buscar producto</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nombre del producto..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                    </div>

                    <!-- Filtro por categoría -->
                    <div>
                        <label class="block text-primary text-body-md font-medium mb-1">Categoría</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                            <option value="">Todas las categorías</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por marca -->
                    <div>
                        <label class="block text-primary text-body-md font-medium mb-1">Marca</label>
                        <select name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                            <option value="">Todas las marcas</option>
                            @foreach(['Nike', 'Adidas', 'Puma', 'Under Armour', 'Reebok', 'New Balance', 'Converse', 'Vans', 'Wilson', 'Spalding'] as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por deporte -->
                    <div>
                        <label class="block text-primary text-body-md font-medium mb-1">Deporte</label>
                        <select name="sport_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                            <option value="">Todos los deportes</option>
                            @foreach(['Fútbol', 'Basketball', 'Running', 'Tenis', 'Volleyball', 'Baseball', 'Natación', 'Ciclismo', 'Fitness', 'Casual'] as $sport)
                                <option value="{{ $sport }}" {{ request('sport_type') == $sport ? 'selected' : '' }}>{{ $sport }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filtros adicionales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Filtro por género -->
                    <div>
                        <label class="block text-primary text-body-md font-medium mb-1">Género</label>
                        <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                            <option value="">Todos</option>
                            <option value="hombre" {{ request('gender') == 'hombre' ? 'selected' : '' }}>Hombre</option>
                            <option value="mujer" {{ request('gender') == 'mujer' ? 'selected' : '' }}>Mujer</option>
                            <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                        </select>
                    </div>

                    <!-- Filtro por rango de precio -->
                    <div>
                        <label class="block text-primary text-body-md font-medium mb-1">Precio máximo</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                               placeholder="Ej: 2000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                    </div>

                    <!-- Solo productos destacados -->
                    <div class="flex items-center">
                        <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }}
                               class="h-4 w-4 text-[#801336] focus:ring-[#801336] border-gray-300 rounded">
                        <label class="ml-2 block text-primary text-body-md font-medium">Solo productos destacados</label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex space-x-3">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary text-body-md font-medium btn-accessible">
                        Aplicar Filtros
                    </button>
                    <a href="{{ route('usuario.products.index') }}" data-catalogo-clear class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 text-body-md font-medium">
                        Limpiar Filtros
                    </a>
                </div>
            </form>
            <div class="mt-4 hidden flex-wrap gap-2" data-catalogo-chips aria-label="Filtros activos"></div>
        </div>

        <!-- Carrito y estadísticas -->
        <div class="flex justify-between items-center mb-6">
            <div class="text-primary">
                <p class="text-heading-sm" data-catalogo-count>{{ $products->total() }} productos encontrados</p>
            </div>
            <a href="{{ route('usuario.cart.index') }}"
               class="flex items-center bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-700 transition-colors text-body-md font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4z"></path>
                </svg>
                Carrito ({{ array_sum(array_column(session('cart', []), 'quantity')) }})
            </a>
        </div>

        <!-- Productos Destacados -->
        @if($products->where('is_featured', true)->count() > 0 && !request()->hasAny(['search', 'category_id', 'brand', 'sport_type', 'gender', 'max_price', 'featured']))
        <div class="mb-8">
            <h2 class="text-heading-lg text-primary mb-4">⭐ Productos Destacados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach ($products->where('is_featured', true)->take(4) as $product)
                    @include('usuario.products.partials.product-card', ['product' => $product, 'featured' => true])
                @endforeach
            </div>
        </div>
        @endif

        <!-- Listado de Productos (server-side; la capa instantánea lo reemplaza con JS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" data-catalogo-grid>
            @forelse ($products as $product)
                @include('usuario.products.partials.product-card', ['product' => $product])
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2-2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-heading-md text-gray-600 mb-2">No se encontraron productos</h3>
                    <p class="text-body-md text-gray-500">Intenta ajustar tus filtros de búsqueda</p>
                </div>
            @endforelse
        </div>

        <!-- Resultados instantáneos (solo con JS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 hidden" data-catalogo-instant aria-live="polite"></div>
        <div class="mt-8 flex justify-center">
            <button type="button" data-catalogo-more class="btn-ghost focus-volt hidden">Cargar más</button>
        </div>

        <!-- Paginación -->
        @if($products->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
