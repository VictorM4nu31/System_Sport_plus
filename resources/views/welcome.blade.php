<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111315">
    <meta name="description" content="Campos Sport: equipamiento deportivo para tu próxima sesión.">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <title>Campos Sport · Equipo para tu próxima sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-ink">
    <header class="relative z-20 border-b border-white/10 bg-ink text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 lg:px-8">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 font-display text-lg font-extrabold tracking-tight text-white no-underline">
                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-volt text-sm font-black text-ink">CS</span>
                Campos Sport
            </a>
            <nav class="flex items-center gap-2 sm:gap-5" aria-label="Acceso">
                @auth
                    <a href="{{ url('/dashboard') }}" class="cs-focus rounded-md px-3 py-2 text-sm font-semibold text-white/80 hover:text-white">Ir a mi espacio</a>
                @else
                    <a href="{{ route('login') }}" class="cs-focus rounded-md px-3 py-2 text-sm font-semibold text-white/80 hover:text-white">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="cs-button-signal cs-focus">Crear cuenta</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="relative isolate overflow-hidden bg-ink text-white">
            <div class="absolute inset-0 -z-10 bg-cover bg-center opacity-45" style="background-image: url('{{ asset('img/banner1.jpg') }}');"></div>
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-ink via-ink/80 to-ink/20"></div>
            <div class="mx-auto grid min-h-[620px] max-w-7xl items-end gap-12 px-5 pb-16 pt-24 lg:grid-cols-[1.1fr_.9fr] lg:px-8 lg:pb-24">
                <div>
                    <p class="cs-eyebrow text-volt">Equipamiento deportivo con intención</p>
                    <h1 class="cs-display mt-5 max-w-3xl text-6xl text-white sm:text-7xl lg:text-8xl">Tu próxima sesión merece mejor equipo.</h1>
                    <p class="mt-7 max-w-xl text-base leading-7 text-white/70 sm:text-lg">Encuentra lo que necesitas para correr, entrenar y competir. Menos ruido, mejores decisiones.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('usuario.products.index') }}" class="cs-button-signal cs-focus no-underline">Explorar equipamiento <span aria-hidden="true">→</span></a>
                        @else
                            <a href="{{ route('register') }}" class="cs-button-signal cs-focus no-underline">Empezar a explorar <span aria-hidden="true">→</span></a>
                            <a href="#misiones" class="cs-button-secondary cs-focus border-white/30 text-white hover:border-white hover:bg-white/10 no-underline">Ver cómo funciona</a>
                        @endauth
                    </div>
                </div>
                <div class="hidden justify-self-end lg:block">
                    <div class="w-72 border-l-2 border-volt pl-6">
                        <p class="cs-eyebrow text-white/50">La idea</p>
                        <p class="mt-4 font-display text-3xl font-bold leading-tight">No vendemos categorías. Preparamos movimientos.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="misiones" class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div>
                    <p class="cs-eyebrow">Empieza por lo que vas a hacer</p>
                    <h2 class="cs-display mt-3 text-4xl sm:text-5xl">Elige tu misión.</h2>
                </div>
                <p class="max-w-md text-sm leading-6 text-muted">Una forma más directa de llegar al equipo correcto. La búsqueda detallada estará esperándote dentro.</p>
            </div>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                @foreach([
                    ['title' => 'Corre más lejos', 'text' => 'Running y recuperación', 'image' => 'running.png', 'sport' => 'Running'],
                    ['title' => 'Entrena con foco', 'text' => 'Fitness y fuerza', 'image' => 'yoga.png', 'sport' => 'Fitness'],
                    ['title' => 'Juega tu partido', 'text' => 'Accesorios y cancha', 'image' => 'accesorios.png', 'sport' => 'Basketball'],
                ] as $mission)
                    <a href="{{ auth()->check() ? route('usuario.products.index', ['sport_type' => $mission['sport']]) : route('register') }}" class="group relative min-h-[270px] overflow-hidden rounded-[14px] bg-ink no-underline">
                        <img src="{{ asset('img/'.$mission['image']) }}" alt="{{ $mission['title'] }}" class="absolute inset-0 h-full w-full object-cover opacity-70 transition duration-500 group-hover:scale-105 group-hover:opacity-85">
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/20 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                            <p class="cs-eyebrow text-volt">{{ $mission['text'] }}</p>
                            <h3 class="mt-2 font-display text-3xl font-bold">{{ $mission['title'] }} <span aria-hidden="true">↗</span></h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="border-y border-line bg-white">
            <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
                <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                    <div>
                        <p class="cs-eyebrow">Selección de la casa</p>
                        <h2 class="cs-display mt-3 text-4xl sm:text-5xl">Lo que está moviendo a la comunidad.</h2>
                    </div>
                    @auth
                        <a href="{{ route('usuario.products.index') }}" class="cs-button-secondary cs-focus no-underline">Ver catálogo completo <span aria-hidden="true">→</span></a>
                    @else
                        <a href="{{ route('login') }}" class="cs-button-secondary cs-focus no-underline">Entrar al catálogo <span aria-hidden="true">→</span></a>
                    @endauth
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse($products->take(4) as $product)
                        <article class="cs-product-tile overflow-hidden">
                            <div class="aspect-square bg-elevated">
                                <img src="{{ $product->image ? asset('storage/products/'.$product->image) : asset('img/logo.png') }}" alt="{{ $product->name }}" class="h-full w-full object-cover" loading="lazy">
                            </div>
                            <div class="flex flex-col gap-2 p-4">
                                <p class="cs-eyebrow">{{ $product->brand ?: $product->sport_type ?: 'Campos Sport' }}</p>
                                <h3 class="line-clamp-2 min-h-[3rem] font-display text-lg font-bold">{{ $product->name }}</h3>
                                <p class="price-mono text-lg font-bold">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="text-muted">La selección estará disponible muy pronto.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-ink px-5 py-8 text-white lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-3 text-sm text-white/50 sm:flex-row">
            <span class="font-display font-bold text-white">Campos Sport</span>
            <span>Equipamiento para tu próxima sesión · {{ now()->year }}</span>
        </div>
    </footer>

    <script>
        if ('serviceWorker' in navigator && !window.__swInit) {
            window.__swInit = true;
            window.addEventListener('load', () => navigator.serviceWorker.register('{{ route('pwa.sw') }}', { scope: '/' }).catch(() => {}));
        }
    </script>
</body>
</html>
