<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Carrito de Compras</h1>

        @if (session('cart') && count(session('cart')) > 0)
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2">Producto</th>
                        <th class="px-6 py-3 border-b-2">Precio</th>
                        <th class="px-6 py-3 border-b-2">Cantidad</th>
                        <th class="px-6 py-3 border-b-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('cart') as $id => $details)
                        <tr>
                            <td class="px-6 py-3 border-b">{{ $details['name'] }}</td>
                            <td class="px-6 py-3 border-b">${{ number_format($details['price'], 2) }}</td>
                            <td class="px-6 py-3 border-b">{{ $details['quantity'] }}</td>
                            <td class="px-6 py-3 border-b">
                                <form action="{{ route('usuario.cart.remove', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Calcular el total del carrito -->
            @php
                $total = array_sum(
                    array_map(function ($details) {
                        return $details['price'] * $details['quantity'];
                    }, session('cart')),
                );
            @endphp

            <div class="mt-6">
                <h2 class="text-xl font-semibold">Total: ${{ number_format($total, 2) }}</h2>
            </div>

            <!-- Contenedor del botón de PayPal -->
            <div class="mt-6" id="paypal-button-container"></div>
        @else
            <p>No tienes productos en el carrito.</p>
        @endif
    </div>

    <!-- Cargar el SDK de PayPal solo si hay productos en el carrito -->
    @if (session('cart') && count(session('cart')) > 0)
        <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency=USD"></script>

        <script>
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '{{ $total }}'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Enviar los datos del pedido al backend para guardar el pedido
                        return fetch('{{ route('usuario.cart.processOrder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                total: '{{ $total }}',
                                cart: @json(session('cart'))
                            })
                        }).then(function(res) {
                            if (res.ok) {
                                // Redirigir al historial de pedidos
                                window.location.href = '{{ route('usuario.orders.history') }}';
                            } else {
                                alert('Hubo un problema al procesar el pedido.');
                            }
                        });
                    });
                },
                onError: function(err) {
                    console.error(err);
                    alert('Hubo un error al procesar el pago.');
                }
            }).render('#paypal-button-container');
        </script>
    @endif
</x-app-layout>
