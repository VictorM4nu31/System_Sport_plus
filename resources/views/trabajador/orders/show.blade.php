<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal -->
            <div class="bg-white bg-opacity-95 shadow-lg rounded-lg p-4 sm:p-6 lg:p-8">

                <!-- Header con información básica -->
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
                    <div class="min-w-0">
                        <h1 class="text-display-sm text-primary font-bold">Detalles del Pedido #{{ $order->id }}</h1>
                        <p class="text-body-md text-primary-light mt-2">
                            Fecha: {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="sm:text-right shrink-0">
                        <div class="text-heading-lg text-primary font-bold price-mono">
                            Total: ${{ number_format($order->total_price, 2) }}
                        </div>
                        <x-status-badge type="info" class="mt-2">{{ ucfirst($order->status) }}</x-status-badge>
                    </div>
                </div>

                <!-- Información del Cliente -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8 mb-8">
                    <!-- Cliente -->
                    <div class="bg-paper border border-line rounded-lg p-4 sm:p-6">
                        <h2 class="text-heading-md text-primary font-semibold mb-4">Información del Cliente</h2>
                        <div class="flex flex-col gap-2">
                            <p class="text-body-md"><span class="font-medium">Nombre:</span> {{ $order->user->name }}</p>
                            <p class="text-body-md break-all"><span class="font-medium">Email:</span> {{ $order->user->email }}</p>
                            <p class="text-body-md"><span class="font-medium">ID Cliente:</span> #{{ $order->user->id }}</p>
                        </div>
                    </div>

                    <!-- Dirección de Envío -->
                    <div class="bg-paper border border-line rounded-lg p-4 sm:p-6">
                        <h3 class="text-heading-md text-primary font-semibold mb-4">Dirección de Envío</h3>
                        @if($order->shippingAddress)
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
                        @else
                            <p class="text-body-md text-error">No se encontró dirección de envío</p>
                        @endif
                    </div>
                </div>

                <!-- Productos del Pedido -->
                <div class="mb-8">
                    <h3 class="text-heading-md text-primary font-semibold mb-6">Productos Solicitados</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full bg-white rounded-lg shadow-sm border border-gray-200">
                            <thead class="bg-carbon text-white">
                                <tr>
                                    <th scope="col" class="px-4 sm:px-6 py-4 text-left text-body-md font-semibold whitespace-nowrap">Producto</th>
                                    <th scope="col" class="px-4 sm:px-6 py-4 text-left text-body-md font-semibold whitespace-nowrap">Cantidad</th>
                                    <th scope="col" class="px-4 sm:px-6 py-4 text-right text-body-md font-semibold whitespace-nowrap">Precio Unit.</th>
                                    <th scope="col" class="px-4 sm:px-6 py-4 text-right text-body-md font-semibold whitespace-nowrap">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($order->orderItems as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                @if($item->product->image)
                                                    <img src="{{ asset('storage/products/' . $item->product->image) }}"
                                                         alt="{{ $item->product->name }}"
                                                         loading="lazy"
                                                         class="w-12 h-12 sm:w-16 sm:h-16 object-cover rounded-lg shrink-0">
                                                @else
                                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-200 rounded-lg flex items-center justify-center shrink-0">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="text-body-md font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                                    @if($item->product->brand)
                                                        <p class="text-body-sm text-gray-500">{{ $item->product->brand }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 text-body-md text-gray-900 whitespace-nowrap">{{ $item->quantity }}</td>
                                        <td class="px-4 sm:px-6 py-4 text-body-md text-gray-900 text-right whitespace-nowrap price-mono">${{ number_format($item->price, 2) }}</td>
                                        <td class="px-4 sm:px-6 py-4 text-body-md font-medium text-gray-900 text-right whitespace-nowrap price-mono">${{ number_format($item->quantity * $item->price, 2) }}</td>
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
                <div class="mb-8">
                    <h3 class="text-heading-md text-primary font-semibold mb-4">Notas del Pedido</h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-body-md text-gray-700">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Acciones -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('trabajador.orders.index') }}"
                       class="inline-flex items-center justify-center gap-2 text-primary hover:text-primary-700 text-body-md font-medium px-4 py-2 min-h-[44px] rounded-md border border-line sm:border-0 transition duration-150 w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06l7.5-7.5a.75.75 0 111.06 1.06L9.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5z" clip-rule="evenodd" />
                        </svg>
                        <span>Volver a Pedidos</span>
                    </a>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="openRejectModal({{ $order->id }})"
                                class="inline-flex items-center justify-center bg-error hover:bg-error-dark text-white px-6 py-3 min-h-[44px] rounded-lg font-semibold text-body-md gap-2 transition duration-200 shadow-lg w-full sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                            </svg>
                            <span>Rechazar Pedido</span>
                        </button>

                        <form action="{{ route('trabajador.orders.accept', $order->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center justify-center bg-success hover:bg-success-dark text-white px-6 py-3 min-h-[44px] rounded-lg font-semibold text-body-md gap-2 transition duration-200 shadow-lg btn-accessible w-full sm:w-auto"
                                    onclick="return confirm('¿Estás seguro de que quieres aceptar este pedido?')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                                <span>Aceptar Pedido</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rechazo -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" role="dialog" aria-modal="true" aria-labelledby="reject-modal-title">
        <div class="relative top-20 mx-auto p-5 border w-[min(92vw,26rem)] shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="reject-modal-title" class="text-lg font-medium text-gray-900">Rechazar Pedido #{{ $order->id }}</h3>
                    <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Motivo del rechazo *
                        </label>
                        <textarea
                            id="rejection_reason"
                            name="rejection_reason"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Explique el motivo por el cual rechaza este pedido..."
                            required
                            minlength="10"
                            maxlength="500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Mínimo 10 caracteres, máximo 500</p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 min-h-[44px] bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-150 w-full sm:w-auto">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 min-h-[44px] bg-error text-white rounded-md hover:bg-error-dark transition duration-150 w-full sm:w-auto">
                            Rechazar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(orderId) {
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectForm').action = `/trabajador/pedidos/${orderId}/rechazar`;
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejection_reason').value = '';
        }

        // Cerrar modal al hacer clic fuera de él o con Escape
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
