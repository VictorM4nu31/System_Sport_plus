<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header con información básica -->
        <div class="bg-white bg-opacity-95 shadow-xl rounded-lg p-6 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                <div>
                    <h1 class="text-heading-lg text-primary font-bold">Detalles del Pedido #{{ $order->id }}</h1>
                    <p class="text-body-md text-primary-light mt-2">
                        Fecha: {{ $order->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="mt-4 sm:mt-0 text-left sm:text-right">
                    <div class="text-heading-md text-primary font-bold mb-2">
                        Total: ${{ number_format($order->total_price, 2) }}
                    </div>
                    @switch($order->status)
                        @case('pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-yellow-100 text-yellow-800">
                                Pendiente de Pago
                            </span>
                            @break
                        @case('paid')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-line text-carbon">
                                Pagado - Esperando Confirmación
                            </span>
                            @break
                        @case('confirmed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-success-100 text-success-dark">
                                ✓ Confirmado
                            </span>
                            @break
                        @case('rejected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-red-100 text-red-800">
                                ✗ Rechazado
                            </span>
                            @break
                        @case('en proceso')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-primary-100 text-primary">
                                En Proceso
                            </span>
                            @break
                        @case('completado')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-success-100 text-success">
                                Completado
                            </span>
                            @break
                        @case('cancelado')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-error-100 text-error">
                                Cancelado
                            </span>
                            @break
                        @default
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-body-sm font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($order->status) }}
                            </span>
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Información de Rechazo (si aplica) -->
        @if($order->status === 'rejected' && $order->rejection_reason)
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-heading-sm text-red-800 font-semibold mb-2">Pedido Rechazado</h3>
                        <p class="text-body-md text-red-700 mb-2">{{ $order->rejection_reason }}</p>
                        @if($order->rejected_at)
                            <p class="text-body-sm text-red-600">
                                Rechazado el: {{ $order->rejected_at_formatted }}
                                @if($order->rejectedBy)
                                    por {{ $order->rejectedBy->name }}
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Dirección de Envío -->
        @if($order->shippingAddress)
            <div class="bg-white bg-opacity-95 shadow-xl rounded-lg p-6 mb-6">
                <h3 class="text-heading-md text-primary font-semibold mb-4">Dirección de Envío</h3>
                <div class="space-y-2">
                    <p class="text-body-md"><span class="font-medium">Nombre:</span> {{ $order->shippingAddress->full_name }}</p>
                    <p class="text-body-md"><span class="font-medium">Teléfono:</span> {{ $order->shippingAddress->phone }}</p>
                    <p class="text-body-md"><span class="font-medium">Dirección:</span> {{ $order->shippingAddress->street_address }}</p>
                    <p class="text-body-md"><span class="font-medium">Ciudad:</span> {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}</p>
                    <p class="text-body-md"><span class="font-medium">CP:</span> {{ $order->shippingAddress->postal_code }}</p>
                    @if($order->shippingAddress->delivery_instructions)
                        <p class="text-body-md"><span class="font-medium">Instrucciones:</span> {{ $order->shippingAddress->delivery_instructions }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Productos del Pedido -->
        <div class="bg-white bg-opacity-95 shadow-xl rounded-lg p-6 mb-6">
            <h3 class="text-heading-md text-primary font-semibold mb-6">Productos</h3>

            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow-sm border border-gray-200">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-body-md font-semibold">Producto</th>
                            <th class="px-6 py-4 text-left text-body-md font-semibold">Cantidad</th>
                            <th class="px-6 py-4 text-left text-body-md font-semibold">Precio Unit.</th>
                            <th class="px-6 py-4 text-left text-body-md font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->orderItems as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        @if($item->product->image)
                                            <img src="{{ asset('storage/products/' . $item->product->image) }}"
                                                 alt="{{ $item->product->name }}"
                                                 loading="lazy"
                                                 class="w-16 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-body-md font-medium text-gray-900">{{ $item->product->name }}</p>
                                            @if($item->product->brand)
                                                <p class="text-body-sm text-gray-500">{{ $item->product->brand }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-body-md text-gray-900">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-body-md text-gray-900">${{ number_format($item->price, 2) }}</td>
                                <td class="px-6 py-4 text-body-md font-medium text-gray-900">${{ number_format($item->quantity * $item->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-heading-md font-semibold text-primary">Total:</td>
                            <td class="px-6 py-4 text-heading-md font-bold text-primary">${{ number_format($order->total_price, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Notas del Pedido -->
        @if($order->notes)
            <div class="bg-white bg-opacity-95 shadow-xl rounded-lg p-6 mb-6">
                <h3 class="text-heading-md text-primary font-semibold mb-4">Notas del Pedido</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-body-md text-gray-700">{{ $order->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Botón de regreso -->
        <div class="flex justify-start">
            <a href="{{ route('usuario.orders.index') }}"
               class="focus-volt flex items-center text-carbon hover:underline text-body-md font-medium space-x-2 transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06l7.5-7.5a.75.75 0 111.06 1.06L9.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5z" clip-rule="evenodd" />
                </svg>
                <span>Volver a Mis Pedidos</span>
            </a>
        </div>
    </div>
</x-app-layout>
