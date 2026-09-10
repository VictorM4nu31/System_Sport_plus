@php
    $user = auth()->user();
    $isCustomer = $user->hasRole('usuario');
    $isWorker = $user->hasRole('trabajador');
    $isAdmin = $user->hasRole('administrador');
    $links = $isAdmin
        ? [
            ['label' => 'Resumen', 'icon' => 'dashboard', 'url' => route('admin.panel'), 'active' => request()->routeIs('admin.panel')],
            ['label' => 'Pedidos', 'icon' => 'shopping_cart', 'url' => route('admin.pedidos.index'), 'active' => request()->routeIs('admin.pedidos.*')],
            ['label' => 'Productos', 'icon' => 'inventory_2', 'url' => route('admin.productos.index'), 'active' => request()->routeIs('admin.productos.*')],
            ['label' => 'Categorías', 'icon' => 'category', 'url' => route('admin.categorias.index'), 'active' => request()->routeIs('admin.categorias.*')],
            ['label' => 'Trabajadores', 'icon' => 'group', 'url' => route('admin.trabajadores.index'), 'active' => request()->routeIs('admin.trabajadores.*')],
            ['label' => 'Reportes', 'icon' => 'monitoring', 'url' => route('admin.reportes.ventas'), 'active' => request()->routeIs('admin.reportes.*')],
            ['label' => 'Monitoreo', 'icon' => 'monitor_heart', 'url' => route('admin.monitoreo.panel'), 'active' => request()->routeIs('admin.monitoreo.*')],
        ]
        : ($isWorker
            ? [
                ['label' => 'Resumen', 'icon' => 'dashboard', 'url' => route('trabajador.panel'), 'active' => request()->routeIs('trabajador.panel')],
                ['label' => 'Cola de pedidos', 'icon' => 'orders', 'url' => route('trabajador.pedidos.indice'), 'active' => request()->routeIs('trabajador.pedidos.*')],
                ['label' => 'Reportes', 'icon' => 'bar_chart', 'url' => route('trabajador.reportes.ventas'), 'active' => request()->routeIs('trabajador.reportes.*')],
            ]
            : [
                ['label' => 'Inicio', 'icon' => 'home', 'url' => route('usuario.panel'), 'active' => request()->routeIs('usuario.panel')],
                ['label' => 'Explorar', 'icon' => 'explore', 'url' => route('usuario.productos.indice'), 'active' => request()->routeIs('usuario.productos.*')],
                ['label' => 'Carrito', 'icon' => 'shopping_cart', 'url' => route('usuario.carrito.indice'), 'active' => request()->routeIs('usuario.carrito.*')],
                ['label' => 'Pedidos', 'icon' => 'receipt_long', 'url' => route('usuario.pedidos.historial'), 'active' => request()->routeIs('usuario.pedidos.*')],
                ['label' => 'Wishlist', 'icon' => 'favorite', 'url' => route('usuario.deseos.indice'), 'active' => request()->routeIs('usuario.deseos.*')],
                ['label' => 'Direcciones', 'icon' => 'location_on', 'url' => route('usuario.direcciones.indice'), 'active' => request()->routeIs('usuario.direcciones.*')],
            ]);
@endphp

<nav x-data="{ open: false }" aria-label="Navegación principal">
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-white/10 bg-ink p-6 text-white md:flex">
        <a href="{{ $isCustomer ? route('usuario.panel') : ($isWorker ? route('trabajador.panel') : route('admin.panel')) }}" class="flex items-center gap-3 text-white no-underline">
            <span class="flex h-10 w-10 items-center justify-center rounded-md bg-volt font-black text-ink">CS</span>
            <span class="font-display text-lg font-extrabold tracking-tight">Campos Sport</span>
        </a>
        <div class="mt-14">
            <p class="cs-eyebrow text-white/45">{{ $isAdmin ? 'Control' : ($isWorker ? 'Operación' : 'Tu espacio') }}</p>
            <div class="mt-4 flex flex-col gap-1">
                @foreach($links as $link)
                    <a href="{{ $link['url'] }}" @class(['cs-focus flex min-h-[44px] items-center gap-3 rounded-md px-3 py-2.5 text-sm no-underline transition-colors', 'bg-volt font-bold text-ink' => $link['active'], 'text-white/65 hover:bg-white/10 hover:text-white' => ! $link['active']]) @if($link['active']) aria-current="page" @endif>
                        <span class="material-icons text-[20px]" aria-hidden="true">{{ $link['icon'] }}</span>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="mt-auto border-t border-white/10 pt-5">
            <p class="truncate text-sm font-semibold">{{ $user->name }}</p>
            <p class="mt-1 truncate text-xs text-white/45">{{ $isAdmin ? 'Administrador' : ($isWorker ? 'Trabajador' : 'Cliente') }}</p>
        </div>
    </aside>

    <div class="flex items-center justify-between border-b border-line bg-ink px-4 py-3 text-white md:hidden">
        <a href="{{ $isCustomer ? route('usuario.panel') : ($isWorker ? route('trabajador.panel') : route('admin.panel')) }}" class="flex items-center gap-2 font-display font-extrabold text-white no-underline"><span class="flex h-8 w-8 items-center justify-center rounded bg-volt text-xs font-black text-ink">CS</span> Campos Sport</a>
        <button type="button" @click="open = !open" class="cs-focus flex h-11 w-11 items-center justify-center rounded-md text-white" aria-label="Abrir navegación" :aria-expanded="open">
            <span class="material-icons" x-text="open ? 'close' : 'menu'" aria-hidden="true"></span>
        </button>
    </div>
    <div x-show="open" x-cloak class="border-b border-line bg-ink px-4 pb-4 text-white md:hidden">
        <div class="flex flex-col gap-1">
            @foreach($links as $link)
                <a href="{{ $link['url'] }}" class="flex min-h-[44px] items-center gap-3 rounded-md px-3 py-2 text-sm text-white/80 hover:bg-white/10"><span class="material-icons text-[20px]" aria-hidden="true">{{ $link['icon'] }}</span>{{ $link['label'] }}</a>
            @endforeach
        </div>
    </div>

    <div class="cs-mobile-nav fixed inset-x-0 bottom-0 z-30 grid grid-cols-5 border-t border-line bg-white/95 px-2 pt-2 backdrop-blur md:hidden">
        @foreach(array_slice($links, 0, 5) as $link)
            <a href="{{ $link['url'] }}" @class(['flex min-h-[52px] flex-col items-center justify-center gap-0.5 text-[10px] font-semibold no-underline', 'text-ink' => $link['active'], 'text-muted' => ! $link['active']])>
                <span class="material-icons text-[21px]" aria-hidden="true">{{ $link['icon'] }}</span>
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
