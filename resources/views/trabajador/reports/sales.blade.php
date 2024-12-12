<x-app-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con transparencia y bordes redondeados -->
        <div class="bg-[#DDEAF2] bg-opacity-70 shadow-2xl rounded-lg p-8">
            <h1 class="text-3xl font-bold mb-6 text-[#476D9D]">Reporte de Ventas</h1>

            <!-- Mostrar pedidos completados -->
            @if ($completedOrders->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-[#476D9D] text-white">
                                <th class="px-6 py-3 border-b-2 border-[#85C7E6] text-left">Pedido</th>
                                <th class="px-6 py-3 border-b-2 border-[#85C7E6] text-left">Total</th>
                                <th class="px-6 py-3 border-b-2 border-[#85C7E6] text-left">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedOrders as $order)
                                <tr class="hover:bg-[#E7E3C4] transition duration-150">
                                    <td class="px-6 py-3 border-b border-[#B5DFF5] text-[#476D9D]">{{ $order->id }}</td>
                                    <td class="px-6 py-3 border-b border-[#B5DFF5] text-[#476D9D]">${{ number_format($order->total_price, 2) }}</td>
                                    <td class="px-6 py-3 border-b border-[#B5DFF5] text-[#476D9D]">{{ ucfirst($order->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-[#476D9D] italic">No hay pedidos completados para mostrar.</p>
            @endif
        </div>
    </div>
</x-app-layout>
