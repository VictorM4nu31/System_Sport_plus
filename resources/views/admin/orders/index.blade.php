<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Gestión de Pedidos</h3>

                <!-- Contenedor de desplazamiento para la tabla -->
                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">ID</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Usuario</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Total</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Estado</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Acciones</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Código postal</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Estado</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Municipio</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Colonia</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Calle</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Número</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Nº interior/depto</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Teléfono de contacto</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Indicaciones adicionales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->id }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->user->name }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">${{ number_format($order->total_price, 2) }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ ucfirst($order->status) }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">Ver</a> |
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->postal_code ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->state ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->municipality ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->neighborhood ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->street ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->number ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->interior_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->contact_phone ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 border-b border-gray-300">{{ $order->address->additional_instructions ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
