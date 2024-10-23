<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Mis Pedidos</h1>

        @if ($orders->count())
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2">Pedido</th>
                        <th class="px-6 py-3 border-b-2">Total</th>
                        <th class="px-6 py-3 border-b-2">Estado</th>
                        <th class="px-6 py-3 border-b-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-6 py-3 border-b">{{ $order->id }}</td>
                            <td class="px-6 py-3 border-b">${{ number_format($order->total_price, 2) }}</td>
                            <td class="px-6 py-3 border-b">{{ ucfirst($order->status) }}</td>
                            <td class="px-6 py-3 border-b">
                                <a href="{{ route('usuario.orders.show', $order->id) }}" class="text-blue-600">Ver detalles</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No has realizado ningún pedido.</p>
        @endif
    </div>
</x-app-layout>
