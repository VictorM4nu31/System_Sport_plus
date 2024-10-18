<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Detalles del Pedido #{{ $order->id }}</h3>

                <p><strong>Usuario:</strong> {{ $order->user->name }}</p>
                <p><strong>Total:</strong> ${{ $order->total_price }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($order->status) }}</p>

                <h4 class="text-lg font-semibold mt-4">Productos</h4>
                <table class="min-w-full bg-white mt-4">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Producto</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Cantidad</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $item->product->name }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">{{ $item->quantity }}</td>
                                <td class="px-6 py-3 border-b border-gray-300">${{ $item->price }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Formulario para actualizar el estado del pedido -->
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="mt-6">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700">Estado del Pedido</label>
                        <select id="status" name="status" required class="mt-1 block w-full">
                            <option value="pendiente" {{ $order->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en proceso" {{ $order->status == 'en proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="completado" {{ $order->status == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ $order->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Actualizar Estado</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
