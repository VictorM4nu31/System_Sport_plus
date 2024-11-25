<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Navegación con Migas de Pan -->
        <nav class="text-sm text-gray-600 mb-4">
            <a href="{{ route('usuario.products.index') }}" class="text-blue-600 hover:underline">Inicio</a> >
            <a href="#" class="text-blue-600 hover:underline">{{ $product->category->name }}</a> >
            <span>{{ $product->name }}</span>
        </nav>

        <!-- Contenedor principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Imagen del Producto -->
            <div>
                <img src="{{ '/storage/' . $product->image }}" alt="{{ $product->name }}"
                    class="w-full h-auto rounded-lg shadow-md">
            </div>

            <!-- Información del Producto -->
            <div>
                <!-- Título del Producto -->
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>

                <!-- Calificaciones y Ventas -->
                <div class="flex items-center mb-4">
                    <!-- Estrellas promedio -->
                    @if ($product->average_rating > 0)
                        <div class="flex space-x-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 @if ($i <= $product->average_rating) text-yellow-400 @else text-gray-300 @endif"
                                    fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-600">({{ $product->reviews->count() }} reseñas)</span>
                    @else
                        <p class="text-gray-600">Sin calificaciones aún</p>
                    @endif
                </div>


                <!-- Precio -->
                <div class="mb-4">
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($product->price, 2) }}</p>
                </div>

                <!-- Indicador de Stock -->
                <p class="mb-4">
                    <span class="font-semibold text-gray-800">Stock:</span>
                    <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock > 0 ? 'Disponible' : 'Agotado' }}
                    </span>
                </p>

                <!-- Formulario de Carrito -->
                @if ($product->stock > 0)
                    <form action="{{ route('usuario.cart.add', $product->id) }}" method="POST"
                        class="flex items-center space-x-4">
                        @csrf
                        <!-- Selector de Cantidad -->
                        <div class="flex items-center">
                            <button type="button" class="px-3 py-1 border border-gray-300 rounded-l-md bg-gray-100"
                                onclick="updateQuantity(-1)">-</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1"
                                max="{{ $product->stock }}" class="w-16 text-center border-t border-b border-gray-300">
                            <button type="button" class="px-3 py-1 border border-gray-300 rounded-r-md bg-gray-100"
                                onclick="updateQuantity(1)">+</button>
                        </div>

                        <!-- Botón -->
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors">
                            Agregar al carrito
                        </button>
                    </form>
                @else
                    <p class="text-red-600 font-semibold">No disponible</p>
                @endif
            </div>
        </div>

        <!-- Descripción -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Descripción</h2>
            <p class="text-gray-700 leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </p>
        </div>

        <!-- Sección de Reseñas -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Reseñas</h2>

            <!-- Formulario para agregar reseñas -->
            <form action="{{ route('usuario.reviews.store', $product->id) }}" method="POST"
                class="mb-6 bg-gray-50 p-4 rounded-lg shadow">
                @csrf
                <label for="rating" class="block text-gray-700 font-semibold">Tu calificación:</label>
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
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-2 mt-4">
                    Enviar reseña
                </button>
            </form>

            <!-- Mostrar reseñas -->
<div class="space-y-6">
    @forelse ($reviews as $review)
        <div class="border-b pb-4">
            <div class="flex items-center space-x-2 mb-2">
                <!-- Estrellas -->
                <div class="flex space-x-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 @if ($i <= $review->rating) text-yellow-400 @else text-gray-300 @endif"
                            fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                        </svg>
                    @endfor
                </div>
                <span class="text-sm text-gray-500">{{ $review->user->name }}</span>
            </div>
            <p class="text-gray-700">{{ $review->review }}</p>
        </div>
    @empty
        <p class="text-gray-600">No hay reseñas aún. Sé el primero en escribir una.</p>
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
