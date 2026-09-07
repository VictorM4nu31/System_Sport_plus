<x-app-layout>
    <div class="py-6" data-kanban
         data-accept-url-base="{{ url('/trabajador/pedidos') }}"
         data-csrf="{{ csrf_token() }}">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-display-sm text-carbon font-bold">Cola de pedidos</h1>
                    <p class="text-body-md text-muted mt-1">
                        {{ $orders->count() }} por aceptar · {{ $acceptedToday->count() }} aceptados hoy.
                        Arrastra a Aceptados o usa los botones (Ctrl+K para buscar).
                    </p>
                </div>
                <p class="sr-only" aria-live="polite" data-kanban-live></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Columna: por aceptar -->
                <section aria-labelledby="kanban-pendientes" class="rounded-lg border border-line bg-white p-4">
                    <h2 id="kanban-pendientes" class="text-heading-md text-carbon mb-4">
                        Por aceptar <span class="price-mono rounded-full bg-volt px-2 py-0.5 text-sm font-bold">{{ $orders->count() }}</span>
                    </h2>
                    @if ($orders->count())
                        <div class="space-y-3" data-cola="pendientes">
                            @foreach ($orders as $order)
                                @php
                                    // diffInMinutes en Carbon 3 devuelve diferencia con signo: usar valor absoluto entero.
                                    $minutosCola = $order->created_at ? (int) abs(now()->diffInMinutes($order->created_at)) : 0;
                                    $slaClase = $minutosCola > 240 ? 'badge-stock-out' : ($minutosCola > 60 ? 'badge-stock-low' : 'badge-stock-ok');
                                    $slaTexto = $minutosCola < 1 ? 'ahora mismo' : ($minutosCola < 60 ? "hace {$minutosCola} min" : 'hace '.intdiv($minutosCola, 60).' h '.($minutosCola % 60).' min');
                                @endphp
                                <article class="rounded-md border border-line bg-paper p-4 shadow-sm"
                                         draggable="true" data-pedido="{{ $order->id }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="font-bold text-carbon price-mono">#{{ $order->id }}</p>
                                            <p class="text-sm font-medium text-carbon">{{ $order->user->name }}</p>
                                            <p class="text-xs text-muted">{{ $order->user->email }}</p>
                                        </div>
                                        <span class="{{ $slaClase }}">{{ $slaTexto }}</span>
                                    </div>
                                    <div class="mt-2 space-y-1 text-sm text-carbon">
                                        @foreach($order->orderItems->take(2) as $item)
                                            <p>{{ $item->quantity }}x {{ $item->product->name }}</p>
                                        @endforeach
                                        @if($order->orderItems->count() > 2)
                                            <p class="text-muted">+{{ $order->orderItems->count() - 2 }} más…</p>
                                        @endif
                                    </div>
                                    <p class="price-mono mt-2 font-bold text-carbon">${{ number_format($order->total_price, 2) }}</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <a href="{{ route('trabajador.orders.show', $order->id) }}"
                                           class="focus-volt rounded-md border border-line px-3 py-1.5 text-sm font-medium text-carbon hover:border-carbon no-underline">Ver</a>
                                        <form action="{{ route('trabajador.orders.accept', $order->id) }}" method="POST" class="inline" data-aceptar="{{ $order->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-carbon focus-volt px-3 py-1.5 text-sm">Aceptar</button>
                                        </form>
                                        <a href="{{ route('trabajador.orders.show', $order->id) }}#rechazar"
                                           data-rechazar="{{ $order->id }}"
                                           class="focus-volt rounded-md border border-line px-3 py-1.5 text-sm font-semibold text-error hover:border-error no-underline">Rechazar</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted italic">No hay pedidos pagados pendientes de aceptación.</p>
                    @endif
                </section>

                <!-- Columna: aceptados hoy (zona de drop) -->
                <section aria-labelledby="kanban-aceptados" class="rounded-lg border border-line bg-white p-4">
                    <h2 id="kanban-aceptados" class="text-heading-md text-carbon mb-4">
                        Aceptados hoy <span class="price-mono rounded-full bg-carbon px-2 py-0.5 text-sm font-bold text-white">{{ $acceptedToday->count() }}</span>
                    </h2>
                    <p class="mb-3 rounded-md border border-dashed border-line p-3 text-center text-sm text-muted" data-drop-hint>
                        Suelta aquí para aceptar
                    </p>
                    <div class="space-y-3" data-cola="aceptados" aria-live="polite">
                        @forelse ($acceptedToday as $order)
                            <article class="rounded-md border border-line bg-paper p-4 opacity-90">
                                <p class="font-bold text-carbon price-mono">#{{ $order->id }}</p>
                                <p class="text-sm font-medium text-carbon">{{ $order->user->name }}</p>
                                <p class="price-mono mt-1 text-sm font-bold text-carbon">${{ number_format($order->total_price, 2) }}</p>
                                <a href="{{ route('trabajador.orders.show', $order->id) }}" class="mt-2 inline-block text-sm font-medium text-carbon underline">Ver</a>
                            </article>
                        @empty
                            <p class="text-muted italic" data-aceptados-vacio>Aún no aceptas pedidos hoy.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <!-- Diálogo de rechazo con motivo -->
        <dialog data-rechazo-dialog class="w-[min(92vw,26rem)] rounded-lg p-0 shadow-lg" aria-labelledby="rechazo-titulo">
            <form data-rechazo-form class="p-6">
                <h2 id="rechazo-titulo" class="text-lg font-bold text-gray-900 mb-1">Rechazar pedido <span data-rechazo-id class="price-mono"></span></h2>
                <p class="text-sm text-gray-500 mb-4">El cliente verá este motivo. Mínimo 10 caracteres.</p>
                <label for="rechazo-motivo" class="block text-sm font-medium text-gray-700 mb-2">Motivo del rechazo *</label>
                <textarea id="rechazo-motivo" name="rejection_reason" rows="4" required minlength="10" maxlength="500"
                          class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-carbon"
                          placeholder="Explique el motivo por el cual rechaza este pedido..."></textarea>
                <p class="mt-1 hidden text-sm text-error" data-rechazo-error role="alert"></p>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" class="btn-ghost focus-volt text-sm" data-rechazo-cancelar>Cancelar</button>
                    <button type="submit" class="focus-volt rounded-md bg-error px-4 py-2 text-sm font-semibold text-white hover:bg-error-dark">Rechazar pedido</button>
                </div>
            </form>
        </dialog>
    </div>

    <script>
        // Kanban del trabajador: drag & drop + UI optimista con rollback.
        // Sin JS, los formularios Aceptar y enlaces Ver/Rechazar siguen funcionando.
        (() => {
            const root = document.querySelector('[data-kanban]');
            if (!root || root.dataset.kanbanInit === '1') {
                return;
            }
            root.dataset.kanbanInit = '1';

            const csrf = root.dataset.csrf;
            const base = root.dataset.acceptUrlBase;
            const live = root.querySelector('[data-kanban-live]');
            const pendientes = root.querySelector('[data-cola="pendientes"]');
            const aceptados = root.querySelector('[data-cola="aceptados"]');
            const hint = root.querySelector('[data-drop-hint]');
            const dialogo = root.querySelector('[data-rechazo-dialog]');
            const formRechazo = root.querySelector('[data-rechazo-form]');
            const motivo = root.querySelector('#rechazo-motivo');
            const errorRechazo = root.querySelector('[data-rechazo-error]');
            let rechazoId = null;
            let arrastrado = null;

            const anunciar = (msg) => { if (live) { live.textContent = msg; } };
            const headers = () => ({
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            });

            async function aceptar(id, tarjeta) {
                // UI optimista: mover de inmediato, revertir si falla.
                const siguiente = tarjeta.nextElementSibling;
                aceptados.prepend(tarjeta);
                tarjeta.setAttribute('draggable', 'false');
                tarjeta.querySelectorAll('button, a').forEach((el) => { el.style.pointerEvents = 'none'; });
                anunciar(`Aceptando pedido ${id}…`);
                try {
                    const r = await fetch(`${base}/${id}/aceptar`, { method: 'PATCH', headers: headers(), body: '{}' });
                    if (!r.ok) {
                        throw new Error(`HTTP ${r.status}`);
                    }
                    tarjeta.querySelector('.mt-3')?.remove();
                    const ok = document.createElement('p');
                    ok.className = 'mt-3 text-sm font-semibold text-success';
                    ok.textContent = '✓ Aceptado';
                    tarjeta.appendChild(ok);
                    document.querySelector('[data-aceptados-vacio]')?.remove();
                    anunciar(`Pedido ${id} aceptado.`);
                } catch (e) {
                    pendientes.insertBefore(tarjeta, siguiente);
                    tarjeta.setAttribute('draggable', 'true');
                    tarjeta.querySelectorAll('button, a').forEach((el) => { el.style.pointerEvents = ''; });
                    anunciar(`No se pudo aceptar el pedido ${id}. Inténtalo de nuevo.`);
                }
            }

            // Formularios Aceptar (también funcionan sin JS por POST clásico).
            root.addEventListener('submit', (event) => {
                const form = event.target.closest('[data-aceptar]');
                if (!form) {
                    return;
                }
                event.preventDefault();
                const tarjeta = form.closest('[data-pedido]');
                aceptar(form.dataset.aceptar, tarjeta);
            });

            // Drag & drop hacia Aceptados.
            root.addEventListener('dragstart', (event) => {
                const tarjeta = event.target.closest('[data-pedido]');
                if (!tarjeta || tarjeta.getAttribute('draggable') !== 'true') {
                    return;
                }
                arrastrado = tarjeta;
                event.dataTransfer.effectAllowed = 'move';
                hint?.classList.add('bg-volt', 'text-carbon', 'font-semibold');
            });
            root.addEventListener('dragend', () => {
                arrastrado = null;
                hint?.classList.remove('bg-volt', 'text-carbon', 'font-semibold');
            });
            aceptados.addEventListener('dragover', (event) => { event.preventDefault(); });
            aceptados.addEventListener('drop', (event) => {
                event.preventDefault();
                if (arrastrado) {
                    aceptar(arrastrado.dataset.pedido, arrastrado);
                }
            });

            // Rechazo con motivo (fallback sin JS: enlace al detalle).
            root.addEventListener('click', (event) => {
                const link = event.target.closest('[data-rechazar]');
                if (!link) {
                    return;
                }
                event.preventDefault();
                rechazoId = link.dataset.rechazar;
                root.querySelector('[data-rechazo-id]').textContent = `#${rechazoId}`;
                errorRechazo.classList.add('hidden');
                formRechazo.reset();
                dialogo.showModal();
                motivo.focus();
            });
            root.querySelector('[data-rechazo-cancelar]')?.addEventListener('click', () => dialogo.close());

            formRechazo.addEventListener('submit', async (event) => {
                event.preventDefault();
                const razon = motivo.value.trim();
                if (razon.length < 10) {
                    errorRechazo.textContent = 'La razón debe tener al menos 10 caracteres.';
                    errorRechazo.classList.remove('hidden');
                    return;
                }
                try {
                    const r = await fetch(`${base}/${rechazoId}/rechazar`, {
                        method: 'PATCH',
                        headers: headers(),
                        body: JSON.stringify({ rejection_reason: razon }),
                    });
                    if (!r.ok) {
                        const datos = await r.json().catch(() => ({}));
                        throw new Error(datos.message ?? `HTTP ${r.status}`);
                    }
                    document.querySelector(`[data-pedido="${rechazoId}"]`)?.remove();
                    dialogo.close();
                    anunciar(`Pedido ${rechazoId} rechazado.`);
                } catch (e) {
                    errorRechazo.textContent = e.message || 'No se pudo rechazar. Inténtalo de nuevo.';
                    errorRechazo.classList.remove('hidden');
                }
            });
        })();
    </script>
</x-app-layout>
