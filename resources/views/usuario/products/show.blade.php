<x-app-layout>
    <div class="mx-auto max-w-7xl py-6 sm:py-10">
        <nav class="mb-8 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-muted" aria-label="Migas de pan">
            <a href="{{ route('usuario.products.index') }}" class="cs-focus rounded text-ink hover:underline">Explorar</a>
            <span aria-hidden="true">/</span>
            <span>{{ $product->category->name }}</span>
            <span aria-hidden="true">/</span>
            <span class="truncate">{{ $product->name }}</span>
        </nav>

        @if(session('success'))
            <x-alert type="success" dismissible="true" class="mb-6">
                {{ session('success') }}
            </x-alert>
        @endif
        @if(session('error'))
            <x-alert type="error" dismissible="true" class="mb-6">
                {{ session('error') }}
            </x-alert>
        @endif

        <section class="grid overflow-hidden rounded-[14px] border border-line bg-white lg:grid-cols-[1.1fr_.9fr]">
            <div class="relative bg-elevated">
                @if ($product->image)
                    <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="aspect-square h-full w-full object-cover">
                @else
                    <div class="flex aspect-square h-full items-center justify-center"><span class="material-icons text-8xl text-muted" aria-hidden="true">image</span></div>
                @endif
                @if($product->is_featured)
                    <span class="absolute left-5 top-5 rounded-md border border-ink bg-volt px-3 py-1.5 text-xs font-bold text-ink">Selección Campos</span>
                @endif
            </div>
            <div class="flex flex-col gap-6 p-6 sm:p-10">
                <div>
                    <p class="cs-eyebrow">{{ $product->brand ?: $product->sport_type ?: 'Equipamiento deportivo' }}</p>
                    <h1 class="cs-display mt-3 text-4xl sm:text-5xl">{{ $product->name }}</h1>
                    @if($product->model)<p class="mt-2 text-sm text-muted">Modelo {{ $product->model }}</p>@endif
                </div>

                <div class="flex flex-wrap items-center gap-3 border-y border-line py-4">
                    @if ($product->average_rating > 0)
                        <span class="text-warning" aria-hidden="true">★★★★★</span>
                        <span class="text-sm font-semibold text-ink">{{ number_format($product->average_rating, 1) }}</span>
                        <span class="text-sm text-muted">{{ $product->reviews->count() }} reseñas</span>
                    @else
                        <span class="text-sm text-muted">Aún no hay reseñas</span>
                    @endif
                </div>

                <div>
                    <p class="price-mono text-3xl font-bold text-ink">MX${{ number_format($product->price, 2) }}</p>
                    <p class="mt-2 flex items-center gap-2 text-sm font-semibold {{ $product->stock > 0 ? 'text-success' : 'text-error' }}">
                        <span class="h-2 w-2 rounded-full bg-current" aria-hidden="true"></span>
                        {{ $product->stock > 0 ? 'Disponible para envío' : 'Agotado por ahora' }}
                        @if($product->stock > 0 && $product->stock <= 10)<span class="font-normal text-muted">· Últimas {{ $product->stock }}</span>@endif
                    </p>
                </div>

                <p class="text-sm leading-7 text-muted">{!! nl2br(e($product->description)) !!}</p>

                @if(($product->sizes && count($product->sizes) > 0) || ($product->colors && count($product->colors) > 0))
                    <div class="grid gap-3 border-t border-line pt-5 sm:grid-cols-2">
                        @if($product->sizes && count($product->sizes) > 0)
                            <div><p class="cs-eyebrow mb-2">Tallas disponibles</p><p class="text-sm font-semibold text-ink">{{ implode(' · ', $product->sizes) }}</p></div>
                        @endif
                        @if($product->colors && count($product->colors) > 0)
                            <div><p class="cs-eyebrow mb-2">Colores disponibles</p><p class="text-sm font-semibold text-ink">{{ implode(' · ', $product->colors) }}</p></div>
                        @endif
                    </div>
                @endif

                @if ($product->stock > 0)
                    <form action="{{ route('usuario.cart.add', $product->id) }}" method="POST" class="mt-auto flex flex-col gap-3 sm:flex-row">
                        @csrf
                        <div class="flex h-12 items-center rounded-md border border-line">
                            <button type="button" class="cs-focus h-12 w-11 text-lg text-muted hover:text-ink" onclick="updateQuantity(-1)" aria-label="Reducir cantidad">−</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="h-12 w-12 border-0 bg-transparent text-center font-bold text-ink focus:ring-0" aria-label="Cantidad">
                            <button type="button" class="cs-focus h-12 w-11 text-lg text-muted hover:text-ink" onclick="updateQuantity(1)" aria-label="Aumentar cantidad">+</button>
                        </div>
                        <button type="submit" class="cs-button-signal cs-focus flex-1">Agregar al carrito <span aria-hidden="true">→</span></button>
                    </form>
                @else
                    <p class="rounded-md bg-elevated p-4 text-sm font-semibold text-muted">Te avisaremos cuando vuelva a estar disponible.</p>
                @endif
            </div>
        </section>

        <section class="mt-16 grid gap-8 lg:grid-cols-[.75fr_1.25fr]">
            <div>
                <p class="cs-eyebrow">Experiencias reales</p>
                <h2 class="cs-display mt-3 text-4xl">Lo que dice la comunidad.</h2>
                <p class="mt-4 text-sm leading-6 text-muted">Tu experiencia puede ayudar a otra persona a elegir mejor.</p>
            </div>
            <div>
                <form action="{{ route('usuario.reviews.store', $product->id) }}" method="POST" class="cs-surface p-5 sm:p-6">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-[.35fr_1fr]">
                        <div><label for="rating" class="cs-eyebrow mb-2 block text-ink">Tu calificación</label><select name="rating" id="rating" class="cs-input cs-focus"><option value="5">5 · Excelente</option><option value="4">4 · Muy bueno</option><option value="3">3 · Bueno</option><option value="2">2 · Regular</option><option value="1">1 · Malo</option></select></div>
                        <div><label for="review" class="cs-eyebrow mb-2 block text-ink">Tu experiencia</label><textarea name="review" id="review" rows="3" class="cs-input cs-focus" placeholder="¿Cómo te funcionó?"></textarea></div>
                    </div>
                    <button type="submit" class="cs-button-primary cs-focus mt-4">Publicar reseña</button>
                </form>
                <div class="mt-6 divide-y divide-line">
                    @forelse ($reviews as $review)
                        <article class="py-5 first:pt-0">
                            <div class="flex flex-wrap items-center justify-between gap-2"><span class="text-warning" aria-label="{{ $review->rating }} de 5 estrellas">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span><span class="text-sm font-semibold text-ink">{{ $review->user->name }}</span></div>
                            <p class="mt-3 text-sm leading-6 text-muted">{{ $review->review }}</p>
                        </article>
                    @empty
                        <p class="py-6 text-sm text-muted">Todavía no hay reseñas. Sé la primera persona en compartir su experiencia.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
    <script>
        function updateQuantity(delta) {
            const input = document.getElementById('quantity');
            const value = Number(input.value) + delta;
            if (value >= Number(input.min) && value <= Number(input.max)) input.value = value;
        }
    </script>
</x-app-layout>
