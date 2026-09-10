<x-app-layout>
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white bg-opacity-95 shadow-lg rounded-lg overflow-hidden">

            <!-- Header -->
            <div class="bg-carbon text-white p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-display-sm font-bold truncate">{{ $product->name }}</h1>
                        @if($product->sku)
                            <p class="text-gray-200 mt-1">SKU: {{ $product->sku }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('admin.productos.edit', $product->id) }}"
                           class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] bg-white text-carbon rounded-md hover:bg-gray-100 font-semibold w-full sm:w-auto">
                            Editar Producto
                        </a>
                        <a href="{{ route('admin.productos.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] bg-gray-600 text-white rounded-md hover:bg-gray-700 font-semibold w-full sm:w-auto">
                            Volver al Listado
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- Imagen del Producto -->
                    <div class="space-y-4">
                        @if($product->image)
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/products/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-square bg-gray-200 rounded-lg flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p>Sin imagen</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Información del Producto -->
                    <div class="space-y-6">

                        <!-- Información Básica -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h2 class="text-heading-md font-semibold text-carbon mb-4">Información Básica</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Precio</label>
                                    <p class="text-heading-lg font-bold text-carbon">${{ number_format($product->price, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Stock</label>
                                    <p class="text-heading-sm font-semibold {{ $product->stock <= 0 ? 'text-error' : ($product->stock <= 10 ? 'text-warning-dark' : 'text-success-dark') }}">
                                        {{ $product->stock }} unidades
                                    </p>
                                </div>
                                @if($product->brand)
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Marca</label>
                                    <p class="text-heading-sm">{{ $product->brand }}</p>
                                </div>
                                @endif
                                @if($product->model)
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Modelo</label>
                                    <p class="text-heading-sm">{{ $product->model }}</p>
                                </div>
                                @endif
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Categoría</label>
                                    <p class="text-heading-sm">{{ $product->category->name ?? 'Sin categoría' }}</p>
                                </div>
                                @if($product->sport_type)
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Tipo de Deporte</label>
                                    <span class="message-success">
                                        {{ $product->sport_type }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Características -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h2 class="text-heading-md font-semibold text-carbon mb-4">Características</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if($product->gender)
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Género</label>
                                    <span class="inline-block bg-line text-carbon px-3 py-1 rounded-full text-body-sm font-medium">
                                        {{ ucfirst($product->gender) }}
                                    </span>
                                </div>
                                @endif
                                @if($product->material)
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Material</label>
                                    <p class="text-heading-sm">{{ $product->material }}</p>
                                </div>
                                @endif
                                @if($product->weight)
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Peso</label>
                                    <p class="text-heading-sm">{{ $product->weight }} kg</p>
                                </div>
                                @endif
                                <div>
                                    <label class="block text-body-sm font-medium text-gray-600">Estado</label>
                                    @if($product->is_featured)
                                        <span class="message-warning">
                                            ⭐ Producto Destacado
                                        </span>
                                    @else
                                        <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-body-sm font-medium">
                                            Producto Regular
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Variantes -->
                        @if(($product->sizes && count($product->sizes) > 0) || ($product->colors && count($product->colors) > 0))
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h2 class="text-heading-md font-semibold text-carbon mb-4">Variantes Disponibles</h2>

                            @if($product->sizes && count($product->sizes) > 0)
                            <div class="mb-4">
                                <label class="block text-body-sm font-medium text-gray-600 mb-2">Tallas</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($product->sizes as $size)
                                        <span class="inline-block bg-carbon text-white px-3 py-1 rounded-md text-body-sm font-medium">
                                            {{ $size }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($product->colors && count($product->colors) > 0)
                            <div>
                                <label class="block text-body-sm font-medium text-gray-600 mb-2">Colores</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($product->colors as $color)
                                        <span class="inline-block bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-body-sm font-medium">
                                            {{ $color }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-heading-md font-semibold text-carbon mb-4">Descripción del Producto</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                </div>

                <!-- Especificaciones Técnicas -->
                @if($product->specifications && count($product->specifications) > 0)
                <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-heading-md font-semibold text-carbon mb-4">Especificaciones Técnicas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($product->specifications as $key => $value)
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-600">{{ $key }}:</span>
                                <span class="text-gray-800">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reseñas -->
                @if($product->reviews && $product->reviews->count() > 0)
                <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-heading-md font-semibold text-carbon mb-4">
                        Reseñas del Producto
                        <span class="text-body-sm font-regular text-gray-600">
                            ({{ $product->reviews->count() }} reseñas - Promedio: {{ $product->average_rating }}/5)
                        </span>
                    </h2>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($product->reviews->take(5) as $review)
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">{{ $review->user->name ?? 'Usuario' }}</span>
                                        <div class="message-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    ⭐
                                                @else
                                                    ☆
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-body-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>
                                <p class="text-gray-700">{{ $review->review }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Información de Stripe -->
                <div class="mt-8 bg-paper p-6 rounded-lg border border-line">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-heading-md font-semibold text-carbon">Integración con Stripe</h2>
                        @if(!$product->isSyncedWithStripe())
                            <form action="{{ route('admin.productos.sincronizar-stripe', $product->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-700 font-semibold btn-accessible">
                                    Sincronizar con Stripe
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-600">Estado de Sincronización</label>
                            @if($product->isSyncedWithStripe())
                                <span class="message-success">
                                    ✅ Sincronizado
                                </span>
                            @else
                                <span class="message-warning">
                                    ⚠️ No sincronizado
                                </span>
                            @endif
                        </div>

                        @if($product->stripe_product_id)
                        <div>
                            <label class="block font-medium text-gray-600">ID del Producto en Stripe</label>
                            <p class="text-gray-800 font-mono text-body-sm break-all">{{ $product->stripe_product_id }}</p>
                        </div>
                        @endif

                        @if($product->stripe_price_id)
                        <div>
                            <label class="block font-medium text-gray-600">ID del Precio en Stripe</label>
                            <p class="text-gray-800 font-mono text-body-sm break-all">{{ $product->stripe_price_id }}</p>
                        </div>
                        @endif

                        @if($product->isSyncedWithStripe())
                        <div class="md:col-span-2">
                            <button onclick="loadStripeInfo({{ $product->id }})"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-body-sm">
                                Ver Información Completa de Stripe
                            </button>
                            <div id="stripe-info-{{ $product->id }}" class="mt-4 hidden">
                                <!-- La información se cargará aquí via AJAX -->
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Información de Auditoría -->
                <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-heading-md font-semibold text-carbon mb-4">Información del Sistema</h2>
                    <div class="grid grid-cols-2 gap-4 text-body-sm">
                        <div>
                            <label class="block font-medium text-gray-600">Fecha de Creación</label>
                            <p class="text-gray-800">{{ $product->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div>
                            <label class="block font-medium text-gray-600">Última Actualización</label>
                            <p class="text-gray-800">{{ $product->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadStripeInfo(productId) {
            const container = document.getElementById(`stripe-info-${productId}`);

            if (container.classList.contains('hidden')) {
                // Mostrar loading
                container.innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-carbon mx-auto"></div><p class="mt-2 text-gray-600">Cargando información de Stripe...</p></div>';
                container.classList.remove('hidden');

                // Cargar información de Stripe
                fetch(`/admin/productos/${productId}/info-stripe`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let html = '<div class="bg-white p-4 rounded-lg border">';
                            html += '<h4 class="font-semibold text-gray-800 mb-3">Información del Producto en Stripe</h4>';

                            if (data.stripe_product) {
                                html += '<div class="grid grid-cols-2 gap-4 text-body-sm">';
                                html += `<div><strong>Nombre:</strong> ${data.stripe_product.name}</div>`;
                                html += `<div><strong>Activo:</strong> ${data.stripe_product.active ? 'Sí' : 'No'}</div>`;
                                html += `<div><strong>Creado:</strong> ${new Date(data.stripe_product.created * 1000).toLocaleDateString()}</div>`;
                                html += `<div><strong>Actualizado:</strong> ${new Date(data.stripe_product.updated * 1000).toLocaleDateString()}</div>`;
                                html += '</div>';

                                if (data.stripe_product.metadata) {
                                    html += '<div class="mt-4"><strong>Metadatos:</strong>';
                                    html += '<div class="bg-gray-50 p-2 rounded mt-2 text-body-sm">';
                                    html += '<pre>' + JSON.stringify(data.stripe_product.metadata, null, 2) + '</pre>';
                                    html += '</div></div>';
                                }
                            }

                            if (data.stripe_price) {
                                html += '<div class="mt-4 pt-4 border-t">';
                                html += '<h5 class="font-semibold text-gray-800 mb-2">Información del Precio</h5>';
                                html += '<div class="grid grid-cols-2 gap-4 text-body-sm">';
                                html += `<div><strong>Precio:</strong> $${(data.stripe_price.unit_amount / 100).toFixed(2)} ${data.stripe_price.currency.toUpperCase()}</div>`;
                                html += `<div><strong>Activo:</strong> ${data.stripe_price.active ? 'Sí' : 'No'}</div>`;
                                html += '</div></div>';
                            }

                            html += '</div>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = `<div class="bg-red-50 border border-red-200 p-4 rounded-lg"><p class="text-error">Error: ${data.error}</p></div>`;
                        }
                    })
                    .catch(error => {
                        container.innerHTML = `<div class="bg-red-50 border border-red-200 p-4 rounded-lg"><p class="text-error">Error al cargar información: ${error.message}</p></div>`;
                    });
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
