<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold text-white mb-6">Pedidos Pendientes</h1>
        @if ($orders->count())
            <table class="min-w-full bg-white">
                <thead>
                <tr class="bg-[#801336] text-white">
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">ID</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Usuario</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Total</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Estado</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Acciones</th>
                            </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-6 py-3 border-b">{{ $order->id }}</td>
                            <td class="px-6 py-3 border-b">${{ number_format($order->total_price, 2) }}</td>
                            <td class="px-6 py-3 border-b">
                                <form action="{{ route('trabajador.orders.accept', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-blue-600">Aceptar Pedido</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-white">No hay pedidos pendientes para aceptar.</p>
        @endif
    </div>
</x-app-layout>
