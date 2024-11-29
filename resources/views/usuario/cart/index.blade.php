<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-3xl text-white font-semibold mb-6">Carrito de Compras</h1>

        @if (session('cart') && count(session('cart')) > 0)
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
                                <button type="submit" class="flex items-center text-red-500 hover:text-red-700 font-semibold transition-colors duration-200">
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


            <!-- Calcular el total del carrito -->
            @php
                $total = array_sum(
                    array_map(function ($details) {
                        return $details['price'] * $details['quantity'];
                    }, session('cart')),
                );
            @endphp

            <div class="mt-6">
                <h2 class="text-xl text-white font-semibold">Total: ${{ number_format($total, 2) }}</h2>
            </div>

            <!-- Contenedor del botón de PayPal -->
            <div class="mt-6" id="paypal-button-container"></div>
        @else
            <p class="text-white text-lg">No tienes productos en el carrito.</p>
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
