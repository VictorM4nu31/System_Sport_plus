<x-app-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con transparencia y bordes redondeados -->
        <div class="bg-white border border-line shadow-lg rounded-lg p-4 sm:p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h1 class="text-display-sm text-carbon font-bold">Reporte de Ventas</h1>
                <p class="text-body-md text-muted">{{ $completedOrders->count() }} pedidos completados</p>
            </div>

            <!-- Mostrar pedidos completados -->
            @if ($completedOrders->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md font-semibold whitespace-nowrap">Pedido</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-right text-body-md font-semibold whitespace-nowrap">Total</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md font-semibold whitespace-nowrap">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedOrders as $order)
                                <tr class="hover:bg-paper transition duration-150">
                                    <td class="px-4 py-3 border-b border-line text-carbon text-body-md whitespace-nowrap">#{{ $order->id }}</td>
                                    <td class="px-4 py-3 border-b border-line text-carbon text-body-md font-semibold text-right whitespace-nowrap price-mono">${{ number_format($order->total_price, 2) }}</td>
                                    <td class="px-4 py-3 border-b border-line whitespace-nowrap"><x-status-badge type="success">{{ ucfirst($order->status) }}</x-status-badge></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <p class="text-body-lg text-carbon font-semibold">Sin pedidos completados</p>
                    <p class="text-body-md text-muted mt-1">Cuando completes pedidos aparecerán aquí.</p>
                    <a href="{{ route('trabajador.pedidos.indice') }}" class="inline-flex items-center justify-center mt-4 px-4 py-2 min-h-[44px] rounded-md bg-carbon text-white text-body-md">Ir a la cola</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
