<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl sm:text-3xl text-white font-semibold mb-4 sm:mb-6">Carrito de Compras</h1>
        <h1 class="text-sm sm:text-base text-white mb-6">(Compras menores a $300.00 se les cobra envio)</h1>

        @if (session('cart') && count(session('cart')) > 0)
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
            <h2 class="text-lg sm:text-xl text-white font-semibold">Subotal: ${{ number_format($subtotal, 2) }}</h2>
            <h2 class="text-lg sm:text-xl text-white font-semibold">
                Costo de Envío:
                <span class="block sm:inline mt-1 sm:mt-0">
                    @if ($shippingCost > 0)
                        ${{ number_format($shippingCost, 2) }}
                    @else
                        Envío gratis
                    @endif
                </span>
            </h2>
            <h2 class="text-lg sm:text-xl text-white font-semibold pt-2">Total: ${{ number_format($total, 2) }}</h2>
        </div>

        <!-- Formulario de pago con Stripe -->
        <div class="mt-8 max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información de Pago</h3>

                <!-- Selección de dirección de envío -->
                @if(auth()->user()->addresses->count() > 0)
                <div class="mb-4">
                    <label for="shipping_address_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección de Envío
                    </label>
                    <select name="shipping_address_id" id="shipping_address_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(auth()->user()->addresses as $address)
                            <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                {{ $address->full_name }} - {{ $address->street }} {{ $address->number }}, {{ $address->neighborhood }}, {{ $address->municipality }}, {{ $address->state }}
                            </option>
                        @endforeach
                    </select>
                </div>
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

        <!-- Hidden cart data for JavaScript -->
        <script type="application/json" id="cart-data">
            @json(session('cart'))
        </script>
        @else
            <p class="text-white text-base sm:text-lg">No tienes productos en el carrito.</p>
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
                    const shippingAddressId = document.getElementById('shipping_address_id')?.value;

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

                    const { client_secret: clientSecret, order_id: orderId } = await response.json();

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
                            // Redirect to order history
                            window.location.href = '{{ route('usuario.orders.history') }}';
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
