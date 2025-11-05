<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal con transparencia y sombra -->
            <div class="bg-white bg-opacity-95 shadow-xl rounded-lg p-8">

                <!-- Contenedor para el logo centrado -->
                <div class="flex justify-center mb-4">
                    <img src="/img/logo.png" alt="Logo" class="w-24 h-24 rounded-full border-4 border-[#801336]">
                </div>

                <!-- Título centrado -->
                <h3 class="text-display-sm font-bold text-center text-primary mb-6">Gestión de Productos Deportivos</h3>

                <!-- Botón de agregar producto centrado -->
                <div class="flex justify-center mb-6">
                    <a href="{{ route('admin.products.create') }}"
                       class="px-6 py-3 bg-primary text-white rounded-md font-semibold hover:bg-primary-700 transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary text-body-md">
                        + Agregar Producto
                    </a>
                </div>

                <!-- Filtros de búsqueda -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h4 class="text-heading-lg font-semibold text-primary mb-4">Filtros de Búsqueda</h4>
                    <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Búsqueda por nombre -->
                        <div>
                            <label class="block text-primary font-medium mb-1 text-body-md">Nombre</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar producto..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                        </div>

                        <!-- Filtro por marca -->
                        <div>
                            <label class="block text-primary font-medium mb-1 text-body-md">Marca</label>
                            <select name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                                <option value="">Todas las marcas</option>
                                @foreach(['Nike', 'Adidas', 'Puma', 'Under Armour', 'Reebok', 'New Balance', 'Converse', 'Vans', 'Wilson', 'Spalding'] as $brand)
                                    <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por deporte -->
                        <div>
                            <label class="block text-primary font-medium mb-1 text-body-md">Deporte</label>
                            <select name="sport_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#801336] focus:border-[#801336]">
                                <option value="">Todos los deportes</option>
                                @foreach(['Fútbol', 'Basketball', 'Running', 'Tenis', 'Volleyball', 'Baseball', 'Natación', 'Ciclismo', 'Fitness', 'Casual'] as $sport)
                                    <option value="{{ $sport }}" {{ request('sport_type') == $sport ? 'selected' : '' }}>{{ $sport }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary text-body-md font-medium">
                                Filtrar
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 text-body-md font-medium">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Botones de gestión -->
                <div class="flex justify-center space-x-4 mb-6">
                    <a href="{{ route('admin.categories.index') }}"
                       class="px-5 py-2 bg-primary text-white rounded-md font-semibold hover:bg-primary-700 transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary text-body-md">
                        Gestionar Categorías
                    </a>
                    <a href="{{ route('admin.orders.index') }}"
                       class="px-5 py-2 bg-primary text-white rounded-md font-semibold hover:bg-primary-700 transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary text-body-md">
                        Gestionar Órdenes
                    </a>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-[#801336] text-white p-4 rounded-lg text-center">
                        <h5 class="text-heading-lg font-semibold">Total Productos</h5>
                        <p class="text-display-md font-bold">{{ $products->count() }}</p>
                    </div>
                    <div class="bg-success text-white p-4 rounded-lg text-center">
                        <h5 class="text-heading-lg font-semibold">En Stock</h5>
                        <p class="text-display-md font-bold">{{ $products->where('stock', '>', 0)->count() }}</p>
                    </div>
                    <div class="bg-red-600 text-white p-4 rounded-lg text-center">
                        <h5 class="text-heading-lg font-semibold">Sin Stock</h5>
                        <p class="text-display-md font-bold">{{ $products->where('stock', '<=', 0)->count() }}</p>
                    </div>
                    <div class="bg-yellow-600 text-white p-4 rounded-lg text-center">
                        <h5 class="text-heading-lg font-semibold">Destacados</h5>
                        <p class="text-display-md font-bold">{{ $products->where('is_featured', true)->count() }}</p>
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Imagen</th>
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Producto</th>
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Marca/Modelo</th>
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Precio</th>
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Stock</th>
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Deporte</th>
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Estado</th>
                                <th class="px-4 py-3 text-left text-body-md font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50 transition duration-150 border-b">
                                    <!-- Imagen -->
                                    <td class="px-4 py-3">
                                        @if($product->image)
                                            <img src="{{ asset('storage/products/' . $product->image) }}"
                                                 class="h-16 w-16 object-cover rounded-md shadow-sm"
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="h-16 w-16 bg-gray-200 rounded-md flex items-center justify-center">
                                                <span class="text-gray-400 text-body-sm">Sin imagen</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Producto -->
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-semibold text-primary text-body-md">{{ $product->name }}</p>
                                            @if($product->sku)
                                                <p class="text-body-sm text-gray-500">SKU: {{ $product->sku }}</p>
                                            @endif
                                            @if($product->is_featured)
                                                <span class="message-warning">
                                                    ⭐ Destacado
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Marca/Modelo -->
                                    <td class="px-4 py-3">
                                        <div>
                                            @if($product->brand)
                                                <p class="font-medium text-gray-800">{{ $product->brand }}</p>
                                            @endif
                                            @if($product->model)
                                                <p class="text-body-sm text-gray-600">{{ $product->model }}</p>
                                            @endif
                                            @if($product->gender)
                                                <span class="inline-block bg-blue-100 text-blue-800 text-body-sm px-2 py-1 rounded-full mt-1">
                                                    {{ ucfirst($product->gender) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Precio -->
                                    <td class="px-4 py-3">
                                        <span class="font-bold text-primary text-body-md">${{ number_format($product->price, 2) }}</span>
                                    </td>

                                    <!-- Stock -->
                                    <td class="px-4 py-3">
                                        <span class="font-semibold {{ $product->stock <= 0 ? 'text-error' : ($product->stock <= 10 ? 'text-warning-dark' : 'text-success-dark') }}">
                                            {{ $product->stock }}
                                        </span>
                                        @if($product->stock <= 0)
                                            <p class="text-body-sm text-error">Sin stock</p>
                                        @elseif($product->stock <= 10)
                                            <p class="text-body-sm text-warning-dark">Stock bajo</p>
                                        @endif
                                    </td>

                                    <!-- Deporte -->
                                    <td class="px-4 py-3">
                                        @if($product->sport_type)
                                            <span class="message-success">
                                                {{ $product->sport_type }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Estado -->
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col space-y-1">
                                            @if($product->sizes && count($product->sizes) > 0)
                                                <span class="text-body-sm text-gray-600">{{ count($product->sizes) }} tallas</span>
                                            @endif
                                            @if($product->colors && count($product->colors) > 0)
                                                <span class="text-body-sm text-gray-600">{{ count($product->colors) }} colores</span>
                                            @endif
                                            @if($product->isSyncedWithStripe())
                                                <span class="message-success">Stripe ✓</span>
                                            @else
                                                <span class="message-warning">Sin Stripe</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-2">
                                            <!-- Ver -->
                                            <a href="{{ route('admin.products.show', $product->id) }}"
                                               class="text-primary-lighter hover:text-blue-800 p-1 rounded" title="Ver detalles">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <!-- Editar -->
                                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                               class="text-[#801336] hover:text-[#9b1a3e] p-1 rounded" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            <!-- Sincronizar con Stripe -->
                                            @if(!$product->isSyncedWithStripe())
                                            <form action="{{ route('admin.products.sync-stripe', $product->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="text-primary-lighter hover:text-blue-800 p-1 rounded"
                                                        title="Sincronizar con Stripe">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif

                                            <!-- Eliminar -->
                                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="message-error"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-heading-sm font-medium">No hay productos registrados</p>
                                            <p class="text-body-sm">Comienza agregando tu primer producto deportivo</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
