<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="stripe-key" content="{{ config('stripe.key') }}">
    <meta name="theme-color" content="#131417">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <title>Campos Sport · Tu próxima sesión</title>

    <!-- Optimized Font Loading -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=archivo:700,800,900&display=swap" rel="stylesheet" />
    <link rel="preload" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"></noscript>
    <!-- Scripts (Vite build único; sin CDN duplicados) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <div class="min-h-screen md:flex">
        @include('layouts.navigation')

        <div class="min-w-0 flex-1 bg-paper md:ml-64">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-paper/90 px-4 py-4 backdrop-blur sm:px-8 lg:px-10">
                <div>
                    <p class="cs-eyebrow">Campos Sport</p>
                    <div class="mt-1 font-display text-xl font-extrabold tracking-tight text-ink">{{ $header ?? '' }}@yield('title')</div>
                </div>

                <!-- Settings Dropdown -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Command palette (Ctrl/⌘ + K) -->
                    <button type="button" data-palette-open
                            class="cs-focus flex items-center gap-2 rounded-md border border-line px-3 py-2 text-sm text-muted hover:border-ink hover:text-ink transition-colors"
                            aria-label="Búsqueda rápida">
                        <kbd class="rounded border border-line bg-white px-1.5 py-0.5 text-[10px] font-bold text-muted" aria-hidden="true">⌘K</kbd>
                        <span class="hidden sm:inline">Buscar…</span>
                    </button>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="cs-focus flex items-center gap-1 rounded-md px-3 py-2 text-body-md font-medium text-ink hover:bg-white transition ease-in-out duration-150">
                                <span class="max-w-[12rem] truncate">{{ Auth::user()->name ?? 'Administrador' }}</span>
                                <svg class="fill-current h-4 w-4 text-muted" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Opción de Perfil -->
                            <x-dropdown-link :href="route('profile.edit')">
                                <span class="flex items-center space-x-2">
                                    <span class="material-icons">person</span>
                                    <span>{{ __('Profile') }}</span>
                                </span>
                            </x-dropdown-link>

                            <!-- Opción de Cerrar Sesión -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                    <span class="flex items-center space-x-2">
                                        <span class="material-icons">logout</span>
                                        <span>{{ __('Log Out') }}</span>
                                    </span>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <main id="contenido" class="px-4 pb-24 pt-4 sm:px-8 sm:pb-10 lg:px-10">
                    {{ $slot }}
                </main>
        </div>
    </div>

    <!-- Command palette global (roles: acciones + búsqueda viva de pedidos en ops) -->
    @php
        $paletteActions = [];
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('usuario')) {
                $paletteActions = [
                    ['titulo' => 'Ir al catálogo', 'url' => route('usuario.products.index')],
                    ['titulo' => 'Ir al carrito', 'url' => route('usuario.cart.index')],
                    ['titulo' => 'Mis pedidos', 'url' => route('usuario.orders.index')],
                    ['titulo' => 'Historial de pedidos', 'url' => route('usuario.orders.history')],
                    ['titulo' => 'Lista de deseos', 'url' => route('usuario.wishlist.index')],
                    ['titulo' => 'Mis direcciones', 'url' => route('usuario.addresses.index')],
                ];
            } elseif ($user->hasRole('trabajador')) {
                $paletteActions = [
                    ['titulo' => 'Cola de pedidos', 'url' => route('trabajador.orders.index')],
                    ['titulo' => 'Reporte de ventas', 'url' => route('trabajador.reports.sales')],
                ];
            } elseif ($user->hasRole('administrador')) {
                $paletteActions = [
                    ['titulo' => 'Panel admin', 'url' => route('dashboard')],
                    ['titulo' => 'Productos', 'url' => route('admin.products.index')],
                    ['titulo' => 'Categorías', 'url' => route('admin.categories.index')],
                    ['titulo' => 'Pedidos', 'url' => route('admin.orders.index')],
                    ['titulo' => 'Reportes', 'url' => route('admin.reports.sales')],
                    ['titulo' => 'Monitoreo', 'url' => route('admin.monitoring.dashboard')],
                ];
            }
        }
    @endphp
    <dialog data-palette class="w-[min(92vw,32rem)] rounded-lg p-0 shadow-lg" aria-label="Búsqueda rápida">
        <div class="p-4" data-palette-root
             data-actions='@json($paletteActions)'
             data-search-url="{{ auth()->check() && auth()->user()->hasRole('trabajador') ? route('trabajador.orders.search') : '' }}">
            <input type="search" data-palette-input placeholder="Buscar pedidos, páginas, acciones… (Esc para cerrar)"
                   class="w-full rounded-md border border-line px-3 py-2 focus:outline-none focus:ring-2 focus:ring-carbon"
                   aria-label="Buscar" autocomplete="off">
            <ul class="mt-2 max-h-72 overflow-y-auto" data-palette-list role="listbox" aria-label="Resultados"></ul>
            <p class="mt-2 text-xs text-gray-500">↑↓ navegar · Enter abrir · Escribe #123 para ir al pedido directo</p>
        </div>
    </dialog>
    <script>
        // Registro del Service Worker (solo navegadores compatibles; nunca interfiere con el pago).
        if ('serviceWorker' in navigator && !window.__swInit) {
            window.__swInit = true;
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ route('pwa.sw') }}', { scope: '/' }).catch(() => {});
            });
        }
    </script>
    <script>
        // Palette ⌘K/Ctrl+K: acciones por rol + búsqueda viva + recientes.
        (() => {
            const root = document.querySelector('[data-palette-root]');
            const dialog = document.querySelector('[data-palette]');
            if (!root || !dialog || window.__paletteInit) {
                return;
            }
            window.__paletteInit = true;

            const input = root.querySelector('[data-palette-input]');
            const list = root.querySelector('[data-palette-list]');
            const actions = JSON.parse(root.dataset.actions || '[]');
            const searchUrl = root.dataset.searchUrl || '';
            const RECENT_KEY = 'tienda-palette-recientes';
            const recientes = () => { try { return JSON.parse(localStorage.getItem(RECENT_KEY) || '[]'); } catch { return []; } };
            const guardarReciente = (item) => {
                try {
                    const actual = [item, ...recientes().filter((r) => r.url !== item.url)].slice(0, 5);
                    localStorage.setItem(RECENT_KEY, JSON.stringify(actual));
                } catch { /* almacenamiento no disponible: la palette sigue funcionando */ }
            };
            let resultados = [];
            let activo = 0;
            let debounce = null;

            const esc = (v) => String(v ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;').replaceAll('"', '&quot;');

            function pintar() {
                list.innerHTML = resultados.map((r, i) => `
                    <li role="option" aria-selected="${i === activo}">
                        <a href="${esc(r.url)}" data-palette-go="${i}"
                           class="block rounded-md px-3 py-2 text-sm ${i === activo ? 'bg-carbon text-white' : 'text-carbon hover:bg-gray-100'} no-underline">
                            <span class="font-semibold">${esc(r.titulo)}</span>
                            ${r.detalle ? `<span class="ml-2 text-xs ${i === activo ? 'text-white/70' : 'text-gray-500'}">${esc(r.detalle)}</span>` : ''}
                        </a>
                    </li>`).join('') || '<li class="px-3 py-2 text-sm text-gray-500">Sin resultados</li>';
                list.querySelectorAll('[data-palette-go]').forEach((a) => {
                    a.addEventListener('click', () => guardarReciente(resultados[Number(a.dataset.paletteGo)]));
                });
            }

            function ir(i) {
                const r = resultados[i];
                if (!r) {
                    return;
                }
                guardarReciente(r);
                window.location.href = r.url;
            }

            async function buscar(q) {
                const query = q.trim();
                const base = actions
                    .filter((a) => a.titulo.toLowerCase().includes(query.toLowerCase()))
                    .map((a) => ({ ...a, detalle: 'Acción' }));

                // Atajo #123: ir directo al pedido (trabajador).
                const directo = query.match(/^#(\d+)$/);
                if (directo && searchUrl) {
                    const id = directo[1];
                    resultados = [{ titulo: `Abrir pedido #${id}`, detalle: 'Directo', url: `${searchUrl.replace(/\/buscar$/, '')}/${id}` }, ...base];
                    activo = 0;
                    pintar();
                    return;
                }

                if (searchUrl && query.length >= 2 && !directo) {
                    try {
                        const r = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (r.ok) {
                            const datos = await r.json();
                            const pedidos = (datos.data || []).map((p) => ({
                                titulo: `Pedido #${p.id} — ${p.cliente}`,
                                detalle: `${p.formatted_total} · ${p.status}`,
                                url: p.url,
                            }));
                            resultados = [...pedidos, ...base];
                            activo = 0;
                            pintar();
                            return;
                        }
                    } catch { /* sin red: mostrar acciones locales */ }
                }

                if (query === '') {
                    resultados = [...recientes(), ...base].filter((r, i, arr) => arr.findIndex((x) => x.url === r.url) === i);
                } else {
                    resultados = base;
                }
                activo = 0;
                pintar();
            }

            function abrir() {
                if (dialog.open) {
                    return;
                }
                dialog.showModal();
                input.value = '';
                buscar('');
                window.setTimeout(() => input.focus(), 0);
            }

            document.querySelector('[data-palette-open]')?.addEventListener('click', abrir);
            document.addEventListener('keydown', (event) => {
                if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
                    event.preventDefault();
                    if (dialog.open) {
                        dialog.close();
                    } else {
                        abrir();
                    }
                }
            });
            input.addEventListener('input', () => {
                window.clearTimeout(debounce);
                debounce = window.setTimeout(() => buscar(input.value), 180);
            });
            input.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    activo = Math.min(resultados.length - 1, activo + 1);
                    pintar();
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    activo = Math.max(0, activo - 1);
                    pintar();
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    ir(activo);
                }
            });
            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) {
                    dialog.close();
                }
            });
        })();
    </script>

</body>
</html>
