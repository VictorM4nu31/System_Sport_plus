<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-4 sm:p-6">
                <!-- Detalles del Pedido -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h1 class="text-display-sm text-primary">Detalles del Pedido #{{ $order->id }}</h1>
                    <x-status-badge type="info">{{ ucfirst($order->status) }}</x-status-badge>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-body-sm text-muted">Usuario</dt>
                        <dd class="text-body-md text-carbon">{{ $order->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-body-sm text-muted">Total</dt>
                        <dd class="text-body-md text-carbon price-mono">${{ number_format($order->total_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-body-sm text-muted">Fecha</dt>
                        <dd class="text-body-md text-carbon">{{ $order->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-body-sm text-muted">Dirección</dt>
                        <dd class="text-body-md text-carbon">@if ($order->address){{ $order->address->neighborhood }}, {{ $order->address->street }} {{ $order->address->number }}@elseif ($order->shipping_address){{ $order->shipping_address }}@else — @endif</dd>
                    </div>
                </dl>

                <!-- Tabla de Productos -->
                <h2 class="text-heading-lg text-primary mt-6">Productos</h2>
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th scope="col" class="px-4 py-3 text-left text-body-md font-semibold whitespace-nowrap">Producto</th>
                                <th scope="col" class="px-4 py-3 text-left text-body-md font-semibold whitespace-nowrap">Cantidad</th>
                                <th scope="col" class="px-4 py-3 text-right text-body-md font-semibold whitespace-nowrap">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr class="border-b border-line">
                                    <td class="px-4 py-3 text-carbon">{{ $item->product->name }}</td>
                                    <td class="px-4 py-3 text-carbon">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-carbon text-right price-mono">${{ number_format($item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right font-semibold text-carbon">Total</td>
                                <td class="px-4 py-3 text-right font-semibold text-carbon price-mono">${{ number_format($order->total_price, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Formulario para actualizar el estado del pedido -->
                <form method="POST" action="{{ route('admin.pedidos.actualizar-estado', $order->id) }}" class="mt-6">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 font-medium">Estado del Pedido</label>
                        <select id="status" name="status" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-carbon focus:ring focus:ring-line">
                            @foreach (['pending' => 'Pendiente de pago', 'paid' => 'Pagado', 'confirmed' => 'Confirmado', 'rejected' => 'Rechazado', 'failed' => 'Fallido', 'cancelado' => 'Cancelado', 'completado' => 'Completado'] as $value => $label)
                                <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col-reverse sm:flex-row gap-3">
                        <a href="{{ route('admin.pedidos.index') }}" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] rounded-md border border-line text-carbon hover:bg-paper transition w-full sm:w-auto">Volver</a>
                        <button type="submit" class="px-4 py-2 min-h-[44px] bg-primary text-white rounded hover:bg-primary-700 transition duration-200 btn-accessible w-full sm:w-auto">
                            Actualizar Estado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
