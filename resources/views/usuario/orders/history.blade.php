<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-xl sm:text-2xl text-carbon font-semibold mb-4 sm:mb-6">Historial de Pedidos</h1>

        @if ($orders->count())
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Pedido</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $order->id }}</td>
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">${{ number_format($order->total_price, 2) }}</td>
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm"><x-status-badge type="info">{{ ucfirst($order->status) }}</x-status-badge></td>
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('usuario.pedidos.ver', $order->id) }}"
                                       class="text-info hover:underline transition-colors duration-200">
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 bg-white rounded-lg shadow text-center">
                <p class="text-body-lg text-carbon font-semibold">Aún no tienes historial</p>
                <p class="text-body-md text-muted mt-1">Explora el catálogo y haz tu primer pedido.</p>
                <a href="{{ route('usuario.productos.indice') }}" class="inline-flex items-center justify-center mt-4 px-4 py-2 min-h-[44px] rounded-md bg-carbon text-white text-body-md">Ver catálogo</a>
            </div>
        @endif
    </div>
</x-app-layout>
