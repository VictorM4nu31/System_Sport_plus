<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Título de la lista de deseos -->
        <h1 class="text-display-sm text-carbon mb-2">Lista de Deseos</h1>
        <p class="text-body-md text-muted mb-6">Guarda tus favoritos y muévelos al carrito cuando estés listo.</p>

        @if (count($wishlist) > 0)
            <!-- Tarjetas de la lista de deseos con datos vivos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($wishlist as $id => $details)
                    @php
                        $bajoPrecio = ($details['saved_price'] ?? $details['price']) > $details['price'];
                        $diferencia = ($details['saved_price'] ?? $details['price']) - $details['price'];
                    @endphp
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden relative">
                        <div class="absolute top-2 left-2 z-10 flex gap-2">
                            @if ($bajoPrecio)
                                <span class="rounded-full bg-volt px-2 py-1 text-xs font-bold text-carbon price-mono">Bajó ${{ number_format($diferencia, 2) }}</span>
                            @endif
                            @if ($details['stock'] <= 0)
                                <span class="badge-stock-out">Agotado</span>
                            @elseif ($details['stock'] <= 10)
                                <span class="badge-stock-low">¡Últimas {{ $details['stock'] }}!</span>
                            @else
                                <span class="badge-stock-ok">En stock</span>
                            @endif
                        </div>
                        <a href="{{ route('usuario.products.show', $id) }}">
                            @if ($details['image'])
                                <img src="{{ asset('storage/products/' . $details['image']) }}"
                                     alt="{{ $details['name'] }}" loading="lazy"
                                     class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
                            @else
                                <img src="{{ asset('img/logo.png') }}"
                                     alt="{{ $details['name'] }}" loading="lazy"
                                     class="w-full h-48 object-contain bg-gray-100 p-4">
                            @endif
                        </a>
                        <div class="p-4">
                            @if ($details['brand'])
                                <p class="text-xs text-gray-500">{{ $details['brand'] }}</p>
                            @endif
                            <h3 class="text-heading-sm text-gray-900 truncate">
                                <a href="{{ route('usuario.products.show', $id) }}" class="hover:underline">{{ $details['name'] }}</a>
                            </h3>
                            <p class="mt-1 flex items-baseline gap-2">
                                <span class="price-mono text-lg font-bold text-gray-900">${{ number_format($details['price'], 2) }}</span>
                                @if ($bajoPrecio)
                                    <s class="price-mono text-sm text-gray-400">${{ number_format($details['saved_price'], 2) }}</s>
                                @endif
                            </p>
                            <div class="mt-3 space-y-2">
                                @if ($details['stock'] > 0)
                                    <form action="{{ route('usuario.wishlist.move', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-carbon focus-volt w-full text-sm">
                                            Mover al carrito
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="w-full rounded-md bg-gray-300 py-2 px-4 text-sm font-semibold text-gray-600 cursor-not-allowed">
                                        Sin stock
                                    </button>
                                @endif
                                <div class="flex gap-2">
                                    <a href="{{ route('usuario.products.show', $id) }}" class="btn-ghost focus-volt flex-1 text-center text-sm no-underline">
                                        Ver
                                    </a>
                                    <form action="{{ route('usuario.wishlist.remove', $id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full rounded-md border border-line py-2 px-4 text-sm font-semibold text-error hover:border-error transition-colors">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <h3 class="text-heading-md text-gray-600 mb-2">No tienes productos en la lista de deseos.</h3>
                <p class="text-body-md text-gray-500 mb-4">Explora el catálogo y guarda tus favoritos.</p>
                <a href="{{ route('usuario.products.index') }}" class="btn-carbon focus-volt inline-block no-underline">Ver catálogo</a>
            </div>
        @endif
    </div>
</x-app-layout>
