<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Gestión de Pedidos</h3>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">ID</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Usuario</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Total</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Estado</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $order->id }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $order->user->name }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $order->total_price }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">{{ ucfirst($order->status) }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">Ver</a> |
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
