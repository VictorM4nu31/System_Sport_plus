<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Detalles del Pedido -->
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Detalles del Pedido #{{ $order->id }}</h3>
                <div class="space-y-2">
                    <p><strong class="text-gray-700">Usuario:</strong> {{ $order->user->name }}</p>
                    <p><strong class="text-gray-700">Total:</strong> ${{ $order->total_price }}</p>
                    <p><strong class="text-gray-700">Estado:</strong> {{ ucfirst($order->status) }}</p>
                </div>

                <!-- Tabla de Productos -->
                <h4 class="text-lg font-semibold mt-6 text-gray-800">Productos</h4>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b bg-gray-100 text-left text-sm font-semibold text-gray-700">Producto</th>
                                <th class="px-4 py-3 border-b bg-gray-100 text-left text-sm font-semibold text-gray-700">Cantidad</th>
                                <th class="px-4 py-3 border-b bg-gray-100 text-left text-sm font-semibold text-gray-700">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr>
                                    <td class="px-4 py-3 border-b border-gray-300 text-gray-600">{{ $item->product->name }}</td>
                                    <td class="px-4 py-3 border-b border-gray-300 text-gray-600">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 border-b border-gray-300 text-gray-600">${{ $item->price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Formulario para actualizar el estado del pedido -->
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="mt-6">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 font-medium">Estado del Pedido</label>
                        <select id="status" name="status" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="pendiente" {{ $order->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en proceso" {{ $order->status == 'en proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="completado" {{ $order->status == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ $order->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200">
                        Actualizar Estado
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
