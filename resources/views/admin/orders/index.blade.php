<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal con transparencia y sombra -->
            <div class="bg-[#FFFFFF] bg-opacity-50 shadow-xl rounded-lg p-8">

                <!-- Título de la sección con ícono y color de fondo -->
                <div class="flex flex-col items-center text-center mb-6">
                    <img src="../img/logo.png" alt="Logo" class="w-24 h-24 mb-4 rounded-full border-4 border-[#801336]" />
                    <h3 class="text-3xl font-bold text-[#FFFFFF]">Gestión de Pedidos</h3>
                </div>

                <!-- Tabla de pedidos -->
                <div class="overflow-x-auto">
                    <table class="w-full bg-white bg-opacity-95 rounded-lg shadow-lg mt-4">
                        <thead>
                            <tr class="bg-[#801336] text-white">
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">ID</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Usuario</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Total</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Estado</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Dirección</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Teléfono</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Indicaciones</th>
                                <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-sm">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr class="hover:bg-[#E7E3C4] transition duration-150">
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $order->id }}</td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $order->user->name }}</td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ $order->total_price }}</td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">{{ ucfirst($order->status) }}</td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">
                                        {{ $order->address->neighborhood }}, {{ $order->address->street }} {{ $order->address->number }}
                                    </td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">
                                        {{ $order->address->contact_phone }}
                                    </td>
                                    <td class="px-6 py-3 border-b border-[#801336] text-[#801336]">
                                        {{ $order->address->additional_instructions }}
                                    </td>
                                    <td class="px-6 py-3 border-b border-[#801336]">
                                        <div class="flex items-center gap-4">
                                            <!-- Botón de Ver con ícono -->
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center text-[#6A92C7] hover:text-[#6A92C7] font-semibold space-x-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M12 4.5a7.5 7.5 0 1 1-7.5 7.5A7.5 7.5 0 0 1 12 4.5m0-1.5a9 9 0 1 0 9 9 9 9 0 0 0-9-9z"/>
                                                    <path d="M12 9a3 3 0 1 1-3 3 3 3 0 0 1 3-3m0-1.5a4.5 4.5 0 1 0 4.5 4.5 4.5 4.5 0 0 0-4.5-4.5z"/>
                                                </svg>
                                                <span>Ver</span>
                                            </a>

                                            <!-- Botón de Eliminar con ícono -->
                                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="flex items-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex items-center text-[#C5283D] hover:text-red-700 font-semibold space-x-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>Eliminar</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
