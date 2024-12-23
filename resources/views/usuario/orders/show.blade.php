<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-xl sm:text-2xl text-white font-semibold mb-4 sm:mb-6">Detalles del Pedido #{{ $order->id }}</h1>

        <!-- Tabla responsiva con scroll horizontal en móviles -->
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($order->orderItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $item->product->name }}</td>
                            <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $item->quantity }}</td>
                            <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">${{ number_format($item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 sm:mt-6 space-y-2 sm:space-y-3">
            <h2 class="text-lg sm:text-xl text-white font-semibold">Subtotal: ${{ number_format($item->price, 2) }}</h2>
            <h2 class="text-lg sm:text-xl text-white font-semibold">
                Costo de Envío:
                @if ($item->price > 300)
                <span class="text-green-400">Envío gratis</span>
                @else
                <span class="text-white">$200.00</span>
                @endif
            </h2>
            <h2 class="text-lg sm:text-xl text-white font-semibold">Total: ${{ number_format($order->total_price + $order->shipping_cost, 2) }}</h2>
        </div>
    </div>
</x-app-layout>
