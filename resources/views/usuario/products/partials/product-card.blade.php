<article class="cs-product-tile relative flex h-full flex-col overflow-hidden group">
    @if($product->is_featured ?? false)
        <div class="absolute left-3 top-3 z-10">
            <span class="rounded-md border border-ink bg-volt px-2 py-1 text-body-sm font-bold text-ink">Destacado</span>
        </div>
    @endif

    @if($product->stock <= 10 && $product->stock > 0)
        <div class="absolute right-3 top-3 z-10"><span class="badge-stock-low">Últimas {{ $product->stock }}</span></div>
    @elseif($product->stock <= 0)
        <div class="absolute right-3 top-3 z-10"><span class="badge-stock-out">Agotado</span></div>
    @endif

    <div class="relative overflow-hidden bg-elevated">
        <a href="{{ route('usuario.products.show', $product->id) }}" class="block">
            @if($product->image)
                <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <div class="flex aspect-[4/3] w-full items-center justify-center bg-elevated">
                    <span class="material-icons text-5xl text-muted" aria-hidden="true">image</span>
                </div>
            @endif
        </a>
        <form action="{{ route('usuario.wishlist.add', $product->id) }}" method="POST" class="absolute right-3 top-14 md:opacity-0 md:transition md:duration-200 md:group-hover:opacity-100 focus-within:opacity-100">
            @csrf
            <button type="submit" class="cs-focus flex h-11 w-11 items-center justify-center rounded-full bg-white/90 text-error shadow-sm transition hover:bg-white" title="Agregar a favoritos" aria-label="Agregar {{ $product->name }} a favoritos">
                <span class="material-icons text-[20px]" aria-hidden="true">favorite_border</span>
            </button>
        </form>
    </div>

    <div class="flex flex-1 flex-col gap-3 p-5">
        @if($product->brand || $product->sport_type)
            <div class="flex flex-wrap items-center gap-2">
                @if($product->brand)
                    <span class="cs-eyebrow rounded bg-paper px-2 py-1 text-ink">{{ $product->brand }}</span>
                @endif
                @if($product->sport_type)
                    <span class="cs-eyebrow rounded border border-line px-2 py-1 text-muted">{{ $product->sport_type }}</span>
                @endif
            </div>
        @endif

        <h3 class="line-clamp-2 min-h-[3.5rem] text-heading-sm font-bold text-ink">
            <a href="{{ route('usuario.products.show', $product->id) }}" class="hover:underline">{{ $product->name }}</a>
        </h3>

        <div class="flex items-center justify-between gap-3">
            <span class="price-mono text-heading-md font-bold text-ink">${{ number_format($product->price, 2) }}</span>
            @if($product->gender)
                <span class="text-body-sm text-muted">{{ ucfirst($product->gender) }}</span>
            @endif
        </div>

        @if(($product->sizes && count($product->sizes) > 0) || ($product->colors && count($product->colors) > 0))
            <p class="text-body-sm text-muted">
                @if($product->sizes && count($product->sizes) > 0)
                    {{ count($product->sizes) }} tallas
                @endif
                @if($product->colors && count($product->colors) > 0)
                    {{ $product->sizes && count($product->sizes) > 0 ? ' · ' : '' }}{{ count($product->colors) }} colores
                @endif
            </p>
        @endif

        @if($product->reviews && $product->reviews->count() > 0)
            <div class="flex items-center gap-2 text-sm" aria-label="{{ $product->average_rating }} de 5 estrellas">
                <span class="text-warning">★★★★★</span><span class="text-body-sm text-muted">{{ $product->reviews->count() }} reseñas</span>
            </div>
        @endif

        <div class="mt-auto flex flex-col gap-2 pt-2">
            @if($product->stock > 0)
                <form action="{{ route('usuario.cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="cs-button-signal cs-focus w-full text-body-md">Agregar al carrito</button>
                </form>
            @else
                <button disabled class="min-h-[44px] w-full rounded-md bg-elevated px-4 py-2 text-body-md font-semibold text-muted">Sin stock</button>
            @endif
            <div class="flex items-center gap-2">
                <a href="{{ route('usuario.products.show', $product->id) }}" data-quickview="{{ $product->id }}" class="cs-button-secondary cs-focus flex-1 text-sm no-underline">Vista rápida</a>
                <label class="cs-focus flex min-h-[44px] cursor-pointer items-center justify-center gap-1 rounded-md border border-line px-3 text-sm font-medium text-ink hover:border-ink">
                    <input type="checkbox" data-compare="{{ $product->id }}" data-name="{{ $product->name }}" class="h-4 w-4 accent-[#111315]">
                    <span>Comparar</span>
                </label>
            </div>
        </div>
    </div>
</article>
