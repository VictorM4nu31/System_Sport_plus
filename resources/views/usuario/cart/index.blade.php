<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" data-checkout>
        <h1 class="text-2xl sm:text-3xl text-carbon font-semibold mb-4 sm:mb-6">Carrito de Compras</h1>

        @if (session('cart') && count(session('cart')) > 0)
        <!-- Stepper del checkout -->
        <ol class="mb-6 flex items-center gap-2 text-sm font-medium" aria-label="Progreso de compra" data-checkout-steps>
            <li class="flex items-center gap-2" data-step="1" aria-current="step">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-carbon text-white" data-step-dot>1</span>
                <span class="text-carbon">Carrito</span>
            </li>
            <li class="h-px w-8 bg-carbon/20" aria-hidden="true"></li>
            <li class="flex items-center gap-2" data-step="2">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-carbon/10 text-carbon" data-step-dot>2</span>
                <span class="text-carbon">Pago</span>
            </li>
            <li class="h-px w-8 bg-carbon/20" aria-hidden="true"></li>
            <li class="flex items-center gap-2" data-step="3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-carbon/10 text-carbon" data-step-dot>3</span>
                <span class="text-carbon">Confirmación</span>
            </li>
        </ol>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-lg rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-[#282E2E] text-white">
                        <th class="px-6 py-3 text-left text-sm font-semibold tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold tracking-wider">Precio</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('cart') as $id => $details)
                        <tr class="hover:bg-gray-100 transition-colors duration-200">
                            <td class="px-6 py-4 border-b text-gray-800">{{ $details['name'] }}</td>
                            <td class="px-6 py-4 border-b text-gray-600">${{ number_format($details['price'], 2) }}</td>
                            <td class="px-6 py-4 border-b text-gray-600">{{ $details['quantity'] }}</td>
                            <td class="px-6 py-4 border-b">
                                <form action="{{ route('usuario.cart.remove', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center text-error hover:text-error-dark font-semibold transition-colors duration-200">
                                        <!-- Ícono de eliminar (basura) -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd"/>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calcular el total del carrito -->
        @php
            $subtotal = $totals['subtotal'] ?? 0;
            $shippingCost = $totals['shipping'] ?? 0;
            $total = $totals['total'] ?? ($subtotal + $shippingCost);
        @endphp
        <div class="mt-6 space-y-3">
            <h2 class="text-lg sm:text-xl text-carbon font-semibold">Subotal: ${{ number_format($subtotal, 2) }}</h2>
            <h2 class="text-lg sm:text-xl text-carbon font-semibold">
                Costo de Envío:
                <span class="block sm:inline mt-1 sm:mt-0">
                    @if ($shippingCost > 0)
                        ${{ number_format($shippingCost, 2) }}
                    @else
                        Envío gratis
                    @endif
                </span>
            </h2>
            <h2 class="text-lg sm:text-xl text-carbon font-semibold pt-2 price-mono">Total: ${{ number_format($total, 2) }}</h2>

            <!-- Progreso a envío gratis (umbral: CartTotalsService::FREE_SHIPPING_THRESHOLD) -->
            @php
                $freeThreshold = \App\Services\CartTotalsService::FREE_SHIPPING_THRESHOLD;
                $faltante = max(0, $freeThreshold - $subtotal);
                $progreso = min(100, $subtotal > 0 ? ($subtotal / $freeThreshold) * 100 : 0);
            @endphp
            <div class="rounded-lg border border-line bg-white p-4" role="status">
                @if ($faltante > 0)
                    <p class="text-sm text-carbon">Te faltan <strong class="price-mono">${{ number_format($faltante, 2) }}</strong> para el envío gratis</p>
                @else
                    <p class="text-sm font-semibold text-carbon">Tienes envío gratis</p>
                @endif
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-carbon/10" role="progressbar"
                     aria-valuenow="{{ (int) $progreso }}" aria-valuemin="0" aria-valuemax="100"
                     aria-label="Progreso hacia envío gratis">
                    <div class="h-full rounded-full bg-volt transition-all duration-300" style="width: {{ $progreso }}%"></div>
                </div>
            </div>

            <a href="#paso-pago" class="btn-volt focus-volt inline-block no-underline">Continuar al pago</a>
        </div>

        <!-- Paso 2: pago con Stripe -->
        <div class="mt-8 max-w-md mx-auto" id="paso-pago">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-gray-800">Información de Pago</h3>
                    <!-- Cuenta regresiva de la reserva de stock (se activa al crear el PaymentIntent) -->
                    <p class="hidden items-center gap-2 rounded-full border border-carbon bg-volt px-3 py-1 text-sm font-bold text-carbon"
                       data-reserva-countdown role="status" aria-live="polite">
                        Stock apartado <span data-reserva-tiempo class="price-mono">--:--</span>
                    </p>
                </div>
                <p class="mb-4 hidden rounded-md bg-warning-light p-3 text-sm font-medium text-warning-dark" data-reserva-aviso role="alert"></p>

                <!-- Selección de dirección de envío -->
                @if(auth()->user()->addresses->count() > 0)
                <fieldset class="mb-4">
                    <legend class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección de Envío
                    </legend>
                    <div class="space-y-2">
                        @foreach(auth()->user()->addresses as $address)
                            <label class="focus-volt flex cursor-pointer items-start gap-3 rounded-md border p-3 transition-colors has-checked:border-carbon has-checked:bg-gray-50">
                                <input type="radio" name="shipping_address_id" value="{{ $address->id }}"
                                       {{ $address->is_default ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 accent-[#131417]">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-800">
                                        {{ $address->full_name }}
                                        @if($address->is_default)
                                            <span class="ml-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">Predeterminada</span>
                                        @endif
                                    </span>
                                    <span class="block text-sm text-gray-600">{{ $address->street }} {{ $address->number }}, {{ $address->neighborhood }}, {{ $address->municipality }}, {{ $address->state }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                @else
                <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                    <p class="text-sm">No tienes direcciones guardadas. <a href="{{ route('usuario.addresses.create') }}" class="underline">Agregar dirección</a></p>
                </div>
                @endif

                <!-- Información de facturación -->
                <div class="mb-4">
                    <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre en la tarjeta
                    </label>
                    <input type="text" id="billing_name" name="billing_name"
                           value="{{ auth()->user()->name }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <div class="mb-4">
                    <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" id="billing_email" name="billing_email"
                           value="{{ auth()->user()->email }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <!-- Stripe Elements Container -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Información de la Tarjeta
                    </label>
                    <div id="stripe-card-element" class="p-3 border border-gray-300 rounded-md">
                        <!-- Stripe Elements will create form elements here -->
                    </div>
                    <div id="stripe-card-errors" class="mt-2 text-error text-body-sm font-medium" role="alert"></div>
                </div>

                <!-- Botón de pago -->
                <div id="stripe-payment-container" class="w-full">
                    <button id="stripe-checkout-button"
                            class="w-full bg-primary hover:bg-primary-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 btn-accessible"
                            type="button">
                        Pagar ${{ number_format($total, 2) }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Paso 3: confirmación (oculto hasta el cobro exitoso) -->
        <div class="mt-8 hidden max-w-md mx-auto" data-pago-exito>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-volt">
                    <svg class="h-8 w-8 text-carbon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">¡Pago exitoso!</h3>
                <p class="mt-1 text-sm text-gray-600">Tu stock quedó confirmado. Así va tu pedido:</p>
                <ol class="mt-5 space-y-0 text-left" aria-label="Estado del pedido">
                    <li class="flex gap-3">
                        <span class="flex flex-col items-center" aria-hidden="true">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-carbon text-xs font-bold text-white">✓</span>
                            <span class="h-8 w-px bg-carbon"></span>
                        </span>
                        <span class="pb-4"><strong class="block text-sm text-gray-900">Pagado</strong><span class="text-sm text-gray-500">Cobro confirmado por Stripe</span></span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex flex-col items-center" aria-hidden="true">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-volt text-xs font-bold text-carbon">2</span>
                            <span class="h-8 w-px bg-gray-300"></span>
                        </span>
                        <span class="pb-4"><strong class="block text-sm text-gray-900">Preparando</strong><span class="text-sm text-gray-500">La tienda está alistando tus productos</span></span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-bold text-gray-500" aria-hidden="true">3</span>
                        <span><strong class="block text-sm text-gray-900">Listo</strong><span class="text-sm text-gray-500">Te avisaremos cuando esté en camino</span></span>
                    </li>
                </ol>
                <a href="{{ route('usuario.orders.history') }}" class="btn-carbon focus-volt mt-4 inline-block no-underline" data-ver-pedido>Ver mis pedidos ahora</a>
                <p class="mt-2 text-sm text-gray-500">Redirigiendo en <span data-redirect-cuenta class="price-mono">6</span>s…</p>
            </div>
        </div>

        <!-- Hidden cart data for JavaScript -->
        <script type="application/json" id="cart-data">
            @json(session('cart'))
        </script>
        @else
            <p class="text-carbon text-base sm:text-lg">No tienes productos en el carrito.</p>
        @endif
    </div>

    @if (session('cart') && count(session('cart')) > 0)
        <!-- Stripe JavaScript SDK -->
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            // Initialize Stripe
            const stripe = Stripe('{{ config('stripe.key') }}');
            const elements = stripe.elements();

            // Create card element
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#424770',
                        '::placeholder': {
                            color: '#aab7c4',
                        },
                    },
                    invalid: {
                        color: '#9e2146',
                    },
                },
            });

            // Mount card element
            cardElement.mount('#stripe-card-element');

            // Handle real-time validation errors from the card Element
            cardElement.on('change', function(event) {
                const displayError = document.getElementById('stripe-card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Handle form submission
            document.getElementById('stripe-checkout-button').addEventListener('click', async function(event) {
                event.preventDefault();

                const button = event.target;
                const originalText = button.textContent;

                // Disable button and show processing state
                button.disabled = true;
                button.textContent = 'Procesando...';

                try {
                    // Get billing details
                    const billingName = document.getElementById('billing_name').value;
                    const billingEmail = document.getElementById('billing_email').value;
                    const shippingAddressId = document.querySelector('[name="shipping_address_id"]:checked')?.value;

                    if (!billingName || !billingEmail) {
                        throw new Error('Por favor completa todos los campos requeridos');
                    }

                    if (!shippingAddressId) {
                        throw new Error('Por favor selecciona una dirección de envío');
                    }

                    // Create payment intent
                    const response = await fetch('{{ route('usuario.cart.create-payment-intent') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            cart: @json(session('cart')),
                            total: {{ $total }},
                            shipping_address_id: shippingAddressId
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Error al procesar el pago');
                    }

                    const { client_secret: clientSecret, order_id: orderId, reserva_expira_en: reservaExpiraEn } = await response.json();

                    // La reserva de stock ya corre: mostrar cuenta regresiva.
                    if (reservaExpiraEn) {
                        iniciarCuentaRegresiva(reservaExpiraEn);
                    }

                    // Confirm payment
                    const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: billingName,
                                email: billingEmail,
                            }
                        }
                    });

                    if (error) {
                        throw new Error(error.message);
                    }

                    if (paymentIntent.status === 'succeeded') {
                        // Confirm order on server
                        const confirmResponse = await fetch('{{ route('usuario.cart.confirm-order') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                order_id: orderId,
                                payment_intent_id: paymentIntent.id
                            })
                        });

                        if (confirmResponse.ok) {
                            // Paso 3: mostrar confirmación con timeline antes de redirigir.
                            mostrarExito('{{ route('usuario.orders.history') }}');
                        } else {
                            throw new Error('Error al confirmar la orden');
                        }
                    }

                } catch (error) {
                    // Show error message
                    showError(error.message);
                } finally {
                    // Re-enable button
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });

            // --- Cuenta regresiva de la reserva de stock ---
            let reservaInterval = null;
            let reservaPoller = null;

            function formatoTiempo(segundos) {
                const minutos = Math.floor(segundos / 60);
                const resto = segundos % 60;
                return `${String(minutos).padStart(2, '0')}:${String(resto).padStart(2, '0')}`;
            }

            function pintarTiempo(segundos) {
                const badge = document.querySelector('[data-reserva-countdown]');
                const tiempo = document.querySelector('[data-reserva-tiempo]');
                const aviso = document.querySelector('[data-reserva-aviso]');
                const boton = document.getElementById('stripe-checkout-button');
                if (!badge || !tiempo) {
                    return;
                }
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
                if (segundos <= 0) {
                    tiempo.textContent = '00:00';
                    if (aviso) {
                        aviso.textContent = 'Tu reserva expiró y el stock se liberó. Vuelve a intentarlo para apartar tus productos.';
                        aviso.classList.remove('hidden');
                    }
                    if (boton) {
                        boton.disabled = true;
                    }
                    window.clearInterval(reservaInterval);
                    return;
                }
                tiempo.textContent = formatoTiempo(segundos);
                if (segundos < 120 && aviso) {
                    aviso.textContent = 'Tu reserva vence en menos de 2 minutos. Completa el pago para no perder tu stock.';
                    aviso.classList.remove('hidden');
                }
            }

            function iniciarCuentaRegresiva(expiraEnIso) {
                window.clearInterval(reservaInterval);
                window.clearInterval(reservaPoller);
                const tick = () => {
                    const segundos = Math.max(0, Math.round((new Date(expiraEnIso).getTime() - Date.now()) / 1000));
                    pintarTiempo(segundos);
                };
                tick();
                reservaInterval = window.setInterval(tick, 1000);
                // Re-sincronizar con el servidor cada 15s (el webhook puede confirmar/liberar).
                reservaPoller = window.setInterval(async () => {
                    try {
                        const estado = await fetch('{{ route('usuario.cart.reserva-estado') }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!estado.ok) {
                            return;
                        }
                        const datos = await estado.json();
                        if (datos.tiene_reserva && datos.expira_en) {
                            window.clearInterval(reservaInterval);
                            iniciarCuentaRegresivaSinPoller(datos.expira_en);
                        } else {
                            pintarTiempo(0);
                        }
                    } catch (e) {
                        // Sin red: el reloj local sigue corriendo, no bloquear el pago.
                    }
                }, 15000);
            }

            function iniciarCuentaRegresivaSinPoller(expiraEnIso) {
                window.clearInterval(reservaInterval);
                const tick = () => {
                    const segundos = Math.max(0, Math.round((new Date(expiraEnIso).getTime() - Date.now()) / 1000));
                    pintarTiempo(segundos);
                };
                tick();
                reservaInterval = window.setInterval(tick, 1000);
            }

            function marcarPaso(numero) {
                document.querySelectorAll('[data-checkout-steps] [data-step]').forEach((item) => {
                    const punto = item.querySelector('[data-step-dot]');
                    const paso = Number(item.dataset.step);
                    const hecho = paso < numero;
                    const actual = paso === numero;
                    item.setAttribute('aria-current', actual ? 'step' : 'false');
                    if (!punto) {
                        return;
                    }
                    punto.className = 'flex h-7 w-7 items-center justify-center rounded-full ' +
                        (hecho ? 'bg-volt text-carbon' : (actual ? 'bg-carbon text-white' : 'bg-carbon/10 text-carbon'));
                    punto.textContent = hecho ? '✓' : String(paso);
                });
            }

            function mostrarExito(urlHistorial) {
                window.clearInterval(reservaInterval);
                window.clearInterval(reservaPoller);
                marcarPaso(3);
                document.querySelector('[data-pago-exito]')?.classList.remove('hidden');
                document.querySelector('[data-pago-exito]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const cuenta = document.querySelector('[data-redirect-cuenta]');
                let restantes = 6;
                const timer = window.setInterval(() => {
                    restantes -= 1;
                    if (cuenta) {
                        cuenta.textContent = String(Math.max(0, restantes));
                    }
                    if (restantes <= 0) {
                        window.clearInterval(timer);
                        window.location.href = urlHistorial;
                    }
                }, 1000);
            }

            // Al llegar al formulario de pago, marcar el paso 2.
            document.querySelector('#paso-pago')?.addEventListener('mouseenter', () => marcarPaso(2), { once: true });

            function showError(message) {
                // Remove existing error
                const existingError = document.getElementById('stripe-error-message');
                if (existingError) {
                    existingError.remove();
                }

                // Create error element
                const errorDiv = document.createElement('div');
                errorDiv.id = 'stripe-error-message';
                errorDiv.className = 'message-error mt-4';
                errorDiv.textContent = message;

                // Insert error message
                const container = document.getElementById('stripe-payment-container');
                container.parentNode.insertBefore(errorDiv, container);

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    if (errorDiv.parentNode) {
                        errorDiv.remove();
                    }
                }, 5000);
            }
        </script>
    @endif
</x-app-layout>
