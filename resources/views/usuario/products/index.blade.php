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
        <div class="mt-8 flex justify-center" data-catalogo-pagination>
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif

        <!-- Barra de comparación (aparece al seleccionar productos) -->
        <div class="fixed bottom-4 left-1/2 z-40 hidden -translate-x-1/2 items-center gap-3 rounded-full border border-carbon bg-carbon px-5 py-3 text-white shadow-lg" data-compare-bar role="status">
            <span class="text-sm font-medium" data-compare-count>0 seleccionados</span>
            <button type="button" class="focus-volt rounded-full bg-volt px-4 py-1.5 text-sm font-bold text-carbon" data-compare-open>
                Comparar
            </button>
            <button type="button" class="text-sm text-white/70 hover:text-white underline" data-compare-clear>
                Limpiar
            </button>
        </div>

        <!-- Diálogo de vista rápida -->
        <dialog data-quickview-dialog class="w-[min(92vw,28rem)] rounded-lg p-0 shadow-lg" aria-labelledby="qv-titulo">
            <div class="p-6" data-quickview-body>
                <p class="text-sm text-gray-500">Cargando…</p>
            </div>
            <form method="dialog" class="border-t border-line p-4 text-right">
                <button class="btn-ghost focus-volt text-sm" value="cerrar">Cerrar</button>
            </form>
        </dialog>

        <!-- Diálogo del comparador -->
        <dialog data-compare-dialog class="w-[min(94vw,44rem)] rounded-lg p-0 shadow-lg" aria-labelledby="cmp-titulo">
            <div class="p-6">
                <h2 id="cmp-titulo" class="text-lg font-bold text-gray-900 mb-4">Comparar productos</h2>
                <div class="overflow-x-auto" data-compare-body></div>
            </div>
            <form method="dialog" class="border-t border-line p-4 text-right">
                <button class="btn-ghost focus-volt text-sm" value="cerrar">Cerrar</button>
            </form>
        </dialog>
    </div>

    <script>
        // Vista rápida + comparador (mejora progresiva sobre los enlaces al detalle).
        (() => {
            if (window.__tiendaComparadorInit) {
                return;
            }
            window.__tiendaComparadorInit = true;

            const esc = (v) => String(v ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
            const fichaUrl = (id) => `/productos/${encodeURIComponent(id)}/ficha`;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const qvDialog = document.querySelector('[data-quickview-dialog]');
            const qvBody = document.querySelector('[data-quickview-body]');
            const cmpDialog = document.querySelector('[data-compare-dialog]');
            const cmpBody = document.querySelector('[data-compare-body]');
            const bar = document.querySelector('[data-compare-bar]');
            const count = document.querySelector('[data-compare-count]');
            const seleccionados = new Map();

            async function ficha(id) {
                const r = await fetch(fichaUrl(id), { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!r.ok) {
                    throw new Error('No se pudo cargar la ficha');
                }
                return r.json();
            }

            function stockBadge(stock) {
                if (stock <= 0) {
                    return '<span class="badge-stock-out">Agotado</span>';
                }
                return stock <= 10
                    ? `<span class="badge-stock-low">¡Últimas ${stock}!</span>`
                    : '<span class="badge-stock-ok">En stock</span>';
            }

            document.addEventListener('click', async (event) => {
                const qv = event.target.closest('[data-quickview]');
                if (qv && qvDialog) {
                    event.preventDefault();
                    qvBody.innerHTML = '<p class="text-sm text-gray-500">Cargando…</p>';
                    qvDialog.showModal();
                    try {
                        const p = await ficha(qv.dataset.quickview);
                        qvBody.innerHTML = `
                            <div class="flex gap-2 mb-3">${p.is_featured ? '<span class="rounded-full bg-yellow-500 px-2 py-1 text-xs font-bold text-white">⭐ Destacado</span>' : ''}${stockBadge(p.stock)}</div>
                            <img src="${esc(p.image_url)}" alt="${esc(p.name)}" class="mb-3 h-52 w-full rounded-md object-cover" loading="lazy">
                            <p class="text-xs text-gray-500">${esc(p.brand ?? '')}${p.sport_type ? ` · ${esc(p.sport_type)}` : ''}</p>
                            <h2 id="qv-titulo" class="text-lg font-bold text-gray-900">${esc(p.name)}</h2>
                            <p class="price-mono mt-1 text-xl font-bold text-gray-900">${esc(p.formatted_price)}</p>
                            <p class="mt-2 text-sm text-gray-600">${esc((p.description ?? '').slice(0, 160))}${(p.description ?? '').length > 160 ? '…' : ''}</p>
                            <div class="mt-3 flex gap-2">
                                <a href="${esc(p.url)}" class="btn-carbon focus-volt flex-1 text-center text-sm no-underline">Ver detalle</a>
                            </div>
                            ${p.stock > 0 ? `<form action="/carrito/${p.id}/agregar" method="POST" class="mt-2">
                                <input type="hidden" name="_token" value="${esc(csrf)}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-volt focus-volt w-full text-sm">Agregar al carrito</button>
                            </form>` : '<p class="mt-2 text-sm font-semibold text-error">Sin stock por ahora</p>'}`;
                    } catch (e) {
                        qvBody.innerHTML = '<p class="text-sm text-error">No se pudo cargar. <a class="underline" href="' + esc(qv.href) + '">Abrir el detalle</a></p>';
                    }
                }
            });

            function pintarBarra() {
                const n = seleccionados.size;
                count.textContent = `${n} seleccionado${n === 1 ? '' : 's'}`;
                bar.classList.toggle('hidden', n === 0);
                bar.classList.toggle('flex', n > 0);
            }

            document.addEventListener('change', (event) => {
                const box = event.target.closest('[data-compare]');
                if (!box) {
                    return;
                }
                if (box.checked && seleccionados.size >= 3 && !seleccionados.has(box.dataset.compare)) {
                    box.checked = false;
                    count.textContent = 'Máximo 3 para comparar';
                    return;
                }
                if (box.checked) {
                    seleccionados.set(box.dataset.compare, box.dataset.name ?? '');
                } else {
                    seleccionados.delete(box.dataset.compare);
                }
                pintarBarra();
            });

            document.querySelector('[data-compare-clear]')?.addEventListener('click', () => {
                seleccionados.clear();
                document.querySelectorAll('[data-compare]').forEach((b) => { b.checked = false; });
                pintarBarra();
            });

            document.querySelector('[data-compare-open]')?.addEventListener('click', async () => {
                if (seleccionados.size === 0 || !cmpDialog) {
                    return;
                }
                cmpBody.innerHTML = '<p class="text-sm text-gray-500">Cargando comparación…</p>';
                cmpDialog.showModal();
                try {
                    const fichas = await Promise.all([...seleccionados.keys()].map(ficha));
                    const fila = (titulo, fn) => `<tr class="border-t border-line"><th class="px-3 py-2 text-left text-sm font-semibold text-gray-700">${titulo}</th>${fichas.map((p) => `<td class="px-3 py-2 text-sm text-gray-800">${fn(p)}</td>`).join('')}</tr>`;
                    cmpBody.innerHTML = `<table class="min-w-full">
                        <thead><tr><th class="px-3 py-2"></th>${fichas.map((p) => `<th class="px-3 py-2 text-left"><img src="${esc(p.image_url)}" alt="${esc(p.name)}" class="mb-2 h-24 w-full rounded object-cover" loading="lazy"><a class="text-sm font-bold text-gray-900 hover:underline" href="${esc(p.url)}">${esc(p.name)}</a></th>`).join('')}</tr></thead>
                        <tbody>
                            ${fila('Precio', (p) => `<span class="price-mono font-bold">${esc(p.formatted_price)}</span>`)}
                            ${fila('Marca', (p) => esc(p.brand ?? '—'))}
                            ${fila('Deporte', (p) => esc(p.sport_type ?? '—'))}
                            ${fila('Género', (p) => esc(p.gender ?? '—'))}
                            ${fila('Material', (p) => esc(p.material ?? '—'))}
                            ${fila('Stock', (p) => esc(String(p.stock)))}
                            ${fila('Rating', (p) => esc(String(p.average_rating)) + ` (${p.reviews_count})`)}
                            ${fila('', (p) => `<a class="btn-carbon focus-volt inline-block text-sm no-underline" href="${esc(p.url)}">Elegir este</a>`)}
                        </tbody></table>`;
                } catch (e) {
                    cmpBody.innerHTML = '<p class="text-sm text-error">No se pudo cargar la comparación.</p>';
                }
            });
        })();
    </script>
</x-app-layout>
