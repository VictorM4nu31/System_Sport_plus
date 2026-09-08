<x-app-layout>
    @section('title', 'Tu espacio')
    <div class="mx-auto max-w-7xl py-6">
        <section class="relative overflow-hidden rounded-[14px] bg-ink px-6 py-10 text-white sm:px-10 sm:py-14">
            <div class="absolute right-0 top-0 h-full w-1/2 bg-cover bg-center opacity-35" style="background-image: url('{{ asset('img/running.png') }}');"></div>
            <div class="relative max-w-xl">
                <p class="cs-eyebrow text-volt">Tu próxima sesión</p>
                <h1 class="cs-display mt-4 text-5xl text-white sm:text-6xl">Hola, {{ auth()->user()->name }}.</h1>
                <p class="mt-5 max-w-md text-sm leading-6 text-white/65">Explora equipamiento, retoma tus favoritos y mantén tus pedidos bajo control.</p>
                <a href="{{ route('usuario.products.index') }}" class="cs-button-signal cs-focus mt-8 no-underline">Explorar equipamiento <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <section class="mt-10">
            <div class="flex items-end justify-between gap-4"><div><p class="cs-eyebrow">Atajos</p><h2 class="cs-display mt-2 text-3xl">Tu centro de movimiento.</h2></div></div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['label' => 'Explorar catálogo', 'hint' => 'Encuentra tu próximo equipo', 'icon' => 'explore', 'url' => route('usuario.products.index'), 'signal' => true],
                    ['label' => 'Mis pedidos', 'hint' => 'Sigue lo que ya compraste', 'icon' => 'receipt_long', 'url' => route('usuario.orders.history'), 'signal' => false],
                    ['label' => 'Lista de deseos', 'hint' => 'Lo que quieres probar', 'icon' => 'favorite', 'url' => route('usuario.wishlist.index'), 'signal' => false],
                    ['label' => 'Direcciones', 'hint' => 'Entrega sin fricción', 'icon' => 'location_on', 'url' => route('usuario.addresses.index'), 'signal' => false],
                ] as $item)
                    <a href="{{ $item['url'] }}" @class(['cs-focus group rounded-[14px] border p-5 no-underline transition', 'border-ink bg-volt text-ink' => $item['signal'], 'cs-surface hover:border-ink' => ! $item['signal']])>
                        <span class="material-icons text-2xl" aria-hidden="true">{{ $item['icon'] }}</span>
                        <span class="mt-8 block font-display text-xl font-bold">{{ $item['label'] }}</span>
                        <span class="mt-1 block text-sm {{ $item['signal'] ? 'text-ink/65' : 'text-muted' }}">{{ $item['hint'] }}</span>
                        <span class="mt-5 block text-xl" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
