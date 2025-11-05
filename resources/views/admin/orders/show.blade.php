<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Detalles del Pedido -->
                <h3 class="text-heading-md text-primary mb-4">Detalles del Pedido #{{ $order->id }}</h3>
                <div class="space-y-2">
                    <p><strong class="text-gray-700">Usuario:</strong> {{ $order->user->name }}</p>
                    <p><strong class="text-gray-700">Total:</strong> ${{ $order->total_price }}</p>
                    <p><strong class="text-gray-700">Estado:</strong> {{ ucfirst($order->status) }}</p>
                </div>

                <!-- Tabla de Productos -->
                <h4 class="text-heading-md text-primary mt-6">Productos</h4>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b bg-primary-50 text-left text-body-md font-semibold text-primary">Producto</th>
                                <th class="px-4 py-3 border-b bg-primary-50 text-left text-body-md font-semibold text-primary">Cantidad</th>
                                <th class="px-4 py-3 border-b bg-primary-50 text-left text-body-md font-semibold text-primary">Precio</th>
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
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-primary text-white rounded hover:bg-primary-700 transition duration-200 btn-accessible">
                        Actualizar Estado
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
