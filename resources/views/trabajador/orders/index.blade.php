<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal con transparencia y sombra -->
            <div class="bg-[#FFFFFF] bg-opacity-50 shadow-xl rounded-lg p-8">

                <!-- Título de la sección -->
                <div class="flex flex-col items-center text-center mb-6">
                    <h1 class="text-3xl font-bold text-[#801336]">Pedidos Pendientes</h1>
                </div>

                <!-- Tabla de pedidos -->
                @if ($orders->count())
                    <div class="overflow-x-auto">
                        <table class="w-full bg-white bg-opacity-95 rounded-lg shadow-lg mt-4">
                            <thead>
                                <tr class="bg-[#801336] text-white">
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">ID</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Usuario</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Total</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr class="hover:bg-[#E7E3C4] transition duration-150">
                                        <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $order->id }}</td>
                                        <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $order->user->name }}</td>
                                        <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">${{ number_format($order->total_price, 2) }}</td>
                                        <td class="px-6 py-3 border-b border-[#801336]">
                                            <form action="{{ route('trabajador.orders.accept', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="flex items-center text-[#6A92C7] hover:text-[#6A92C7] font-semibold space-x-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path d="M12 4.5a7.5 7.5 0 1 1-7.5 7.5A7.5 7.5 0 0 1 12 4.5m0-1.5a9 9 0 1 0 9 9 9 9 0 0 0-9-9z"/>
                                                        <path d="M12 9a3 3 0 1 1-3 3 3 3 0 0 1 3-3m0-1.5a4.5 4.5 0 1 0 4.5 4.5 4.5 4.5 0 0 0-4.5-4.5z"/>
                                                    </svg>
                                                    <span>Aceptar Pedido</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-[#801336] italic">No hay pedidos pendientes para aceptar.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
