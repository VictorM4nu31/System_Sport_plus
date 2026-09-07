<x-app-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con transparencia y bordes redondeados -->
        <div class="bg-white border border-line shadow-xl rounded-lg p-8">
            <h1 class="text-display-sm text-primary font-bold mb-6">Reporte de Ventas</h1>

            <!-- Mostrar pedidos completados -->
            @if ($completedOrders->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th class="px-6 py-3 border-b-2 border-line text-left text-body-md font-semibold">Pedido</th>
                                <th class="px-6 py-3 border-b-2 border-line text-left text-body-md font-semibold">Total</th>
                                <th class="px-6 py-3 border-b-2 border-line text-left text-body-md font-semibold">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedOrders as $order)
                                <tr class="hover:bg-paper transition duration-150">
                                    <td class="px-6 py-3 border-b border-line text-primary text-body-md font-regular">{{ $order->id }}</td>
                                    <td class="px-6 py-3 border-b border-line text-primary text-body-md font-semibold">${{ number_format($order->total_price, 2) }}</td>
                                    <td class="px-6 py-3 border-b border-line text-success text-body-md font-medium">{{ ucfirst($order->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-primary-light text-body-lg font-regular italic">No hay pedidos completados para mostrar.</p>
            @endif
        </div>
    </div>
</x-app-layout>
