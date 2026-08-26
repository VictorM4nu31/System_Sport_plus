<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:py-10 px-4 sm:px-6">
        <!-- Navegación con Migas de Pan -->
        <nav class="text-sm text-gray-500 mb-4 sm:mb-8">
            <a href="{{ route('usuario.products.index') }}" class="text-white hover:underline">Inicio</a>
            <span class="mx-2 text-gray-400">></span>
            <a href="#" class="text-white hover:underline">{{ $product->category->name }}</a>
            <span class="mx-2 text-gray-400">></span>
            <span class="text-white">{{ $product->name }}</span>
        </nav>

        <div class="max-w-5xl mx-auto bg-white p-4 sm:p-6 rounded-lg shadow-lg">
            <div class="flex flex-col md:flex-row gap-4 md:gap-8">
                <!-- Imagen del Producto -->
                <div class="w-full md:w-1/2">
                    <div class="aspect-square overflow-hidden rounded-lg bg-gray-100">
                        <img
                            src="{{ '/storage/' . $product->image }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-contain hover:scale-105 transition-transform duration-300"
                        >
                    </div>
                </div>
                <!-- Información del Producto -->
                <div class="w-full md:w-1/2 space-y-4">
                    <h1 class="text-display-md text-primary font-bold">{{ $product->name }}</h1>
                    <!-- Descripción -->
                    <p class="text-body-md text-primary-light leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </p>
                    <!-- Calificaciones y Ventas -->
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                        @if ($product->average_rating > 0)
                            <div class="flex space-x-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="{{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }} h-6 w-6"
                                        fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-3 text-gray-600">({{ $product->reviews->count() }} reseñas)</span>
                        @else
                            <p class="text-gray-600">Sin calificaciones aún</p>
                        @endif
                    </div>
                    <!-- Precio -->
                    <div class="text-heading-lg text-primary font-bold">
                        MX${{ number_format($product->price, 2) }}
                    </div>
                    <!-- Indicador de Stock -->
                    <p class="mb-6">
                        <span class="font-semibold text-primary">Stock:</span>
                        <span class="{{ $product->stock > 0 ? 'text-success' : 'text-error' }}">
                            {{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}
                        </span>
                    </p>
                    <!-- Formulario de Carrito -->
                    @if ($product->stock > 0)
                        <form action="{{ route('usuario.cart.add', $product->id) }}" method="POST"
                              class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                            @csrf
                            <!-- Selector de Cantidad -->
                            <div class="flex items-center border rounded-lg overflow-hidden shadow-sm w-full sm:w-auto">
                                <!-- Botón de Restar -->
                                <button type="button"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900 focus:outline-none transition-all"
                                        onclick="updateQuantity(-1)">
                                    -
                                </button>
                                <!-- Campo de Entrada -->
                                <input type="number" name="quantity" id="quantity" value="1" min="1"
                                       max="{{ $product->stock }}"
                                       class="w-16 text-center border-t border-b border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all">
                                <!-- Botón de Sumar -->
                                <button type="button"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900 focus:outline-none transition-all"
                                        onclick="updateQuantity(1)">
                                    +
                                </button>
                            </div>
                            <!-- Botón -->
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors btn-accessible">
                                Agregar al carrito
                            </button>
                        </form>
                    @else
                        <p class="text-error font-semibold">No disponible</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Sección de Reseñas -->
        <div class="mt-8 sm:mt-12">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-6">
                <h2 class="text-xl sm:text-2xl text-white font-bold">Reseñas del artículo</h2>
                <span class="text-gray-400 text-base sm:text-lg">({{ $product->reviews->count() }} reseñas)</span>
            </div>

            <!-- Formulario para agregar reseñas -->
            <form action="{{ route('usuario.reviews.store', $product->id) }}" method="POST"
                  class="mb-6 sm:mb-8 bg-white p-4 sm:p-6 rounded-lg shadow space-y-4">
                @csrf
                <label for="rating" class="block text-gray-700 font-semibold mb-2">Tu calificación:</label>
                <select name="rating" id="rating" class="border border-gray-300 rounded-lg p-2 w-full">
                    <option value="5">5 - Excelente</option>
                    <option value="4">4 - Bueno</option>
                    <option value="3">3 - Regular</option>
                    <option value="2">2 - Malo</option>
                    <option value="1">1 - Muy malo</option>
                </select>
                <label for="review" class="block text-gray-700 font-semibold mt-4">Tu reseña:</label>
                <textarea name="review" id="review" rows="3" class="border border-gray-300 rounded-lg p-2 w-full"></textarea>
                <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors btn-accessible">
                    Enviar reseña
                </button>
            </form>
            <!-- Mostrar reseñas -->
            <div class="space-y-4 sm:space-y-6">
                @forelse ($reviews as $review)
                    <div class="border-b pb-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <!-- Estrellas -->
                            <div class="flex space-x-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} h-5 w-5"
                                        fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm text-white">{{ $review->user->name }}</span>
                        </div>
                        <p class="text-white">{{ $review->review }}</p>
                    </div>
                @empty
                    <p class="text-white">No hay reseñas aún. Sé el primero en escribir una.</p>
                @endforelse
            </div>
        </div>
    </div>
    <script>
        function updateQuantity(delta) {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const max = parseInt(input.max);
            const newValue = currentValue + delta;
            if (newValue >= 1 && newValue <= max) {
                input.value = newValue;
            }
        }
    </script>
</x-app-layout>
