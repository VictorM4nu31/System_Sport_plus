<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Detalles del Pedido #{{ $order->id }}</h1>

        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2">Producto</th>
                    <th class="px-6 py-3 border-b-2">Cantidad</th>
                    <th class="px-6 py-3 border-b-2">Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td class="px-6 py-3 border-b">{{ $item->product->name }}</td>
                        <td class="px-6 py-3 border-b">{{ $item->quantity }}</td>
                        <td class="px-6 py-3 border-b">${{ number_format($item->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            <h2 class="text-xl font-semibold">Total: ${{ number_format($order->total_price, 2) }}</h2>
        </div>
    </div>
</x-app-layout>
