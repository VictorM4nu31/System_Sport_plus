<div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 relative group">
    <!-- Badge de producto destacado -->
    @if($product->is_featured ?? false)
        <div class="absolute top-2 left-2 z-10">
            <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                ⭐ Destacado
            </span>
        </div>
    @endif

    <!-- Badge de stock bajo -->
    @if($product->stock <= 10 && $product->stock > 0)
        <div class="absolute top-2 right-2 z-10">
            <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                ¡Últimas {{ $product->stock }}!
            </span>
        </div>
    @elseif($product->stock <= 0)
        <div class="absolute top-2 right-2 z-10">
            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Agotado
            </span>
        </div>
    @endif

    <!-- Imagen del producto -->
    <div class="relative overflow-hidden">
        <a href="{{ route('usuario.products.show', $product->id) }}">
            @if($product->image)
                <img src="{{ asset('storage/products/' . $product->image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
        </a>

        <!-- Botón de wishlist -->
        <form action="{{ route('usuario.wishlist.add', $product->id) }}" method="POST"
              class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            @csrf
            <button type="submit"
                    class="bg-white bg-opacity-80 hover:bg-opacity-100 p-2 rounded-full shadow-md transition-all duration-200"
                    title="Agregar a favoritos">
                <svg class="w-5 h-5 text-red-500 hover:text-red-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </button>
        </form>
    </div>

    <!-- Información del producto -->
    <div class="p-4">
        <!-- Marca y modelo -->
        @if($product->brand || $product->model)
        <div class="flex items-center space-x-2 mb-2">
            @if($product->brand)
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $product->brand }}</span>
            @endif
            @if($product->sport_type)
                <span class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded">{{ $product->sport_type }}</span>
            @endif
        </div>
        @endif

        <!-- Nombre del producto -->
        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
            <a href="{{ route('usuario.products.show', $product->id) }}" class="hover:text-[#801336] transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <!-- Precio -->
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl font-bold text-[#801336]">${{ number_format($product->price, 2) }}</span>
            @if($product->gender)
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ ucfirst($product->gender) }}</span>
            @endif
        </div>

        <!-- Variantes disponibles -->
        @if(($product->sizes && count($product->sizes) > 0) || ($product->colors && count($product->colors) > 0))
        <div class="mb-3 text-xs text-gray-600">
            @if($product->sizes && count($product->sizes) > 0)
                <span>{{ count($product->sizes) }} tallas</span>
            @endif
            @if($product->colors && count($product->colors) > 0)
                @if($product->sizes && count($product->sizes) > 0) • @endif
                <span>{{ count($product->colors) }} colores</span>
            @endif
        </div>
        @endif

        <!-- Rating (si existe) -->
        @if($product->reviews && $product->reviews->count() > 0)
        <div class="flex items-center mb-3">
            <div class="flex text-yellow-400 text-sm">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $product->average_rating)
                        ⭐
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <span class="text-xs text-gray-500 ml-2">({{ $product->reviews->count() }} reseñas)</span>
        </div>
        @endif

        <!-- Botón de agregar al carrito -->
        @if($product->stock > 0)
            <form action="{{ route('usuario.cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full bg-[#801336] text-white py-2 px-4 rounded-md hover:bg-[#9b1a3e] transition-colors duration-200 font-semibold">
                    Agregar al Carrito
                </button>
            </form>
        @else
            <button disabled
                    class="w-full bg-gray-400 text-white py-2 px-4 rounded-md cursor-not-allowed font-semibold">
                Sin Stock
            </button>
        @endif
    </div>
</div>
