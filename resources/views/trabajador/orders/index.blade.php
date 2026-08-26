<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor principal con transparencia y sombra -->
            <div class="bg-[#FFFFFF] bg-opacity-50 shadow-xl rounded-lg p-8">

                <!-- Título de la sección -->
                <div class="flex flex-col items-center text-center mb-6">
                    <h1 class="text-display-sm text-primary font-bold">Pedidos Pagados - Pendientes de Aceptación</h1>
                </div>

                <!-- Tabla de pedidos -->
                @if ($orders->count())
                    <div class="overflow-x-auto">
                        <table class="w-full bg-white bg-opacity-95 rounded-lg shadow-lg mt-4">
                            <thead>
                                <tr class="bg-[#801336] text-white">
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-body-md font-semibold">ID</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-body-md font-semibold">Cliente</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-body-md font-semibold">Productos</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-body-md font-semibold">Total</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-body-md font-semibold">Estado</th>
                                    <th class="px-6 py-3 border-b-2 border-[#801336] text-left text-body-md font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr class="hover:bg-[#E7E3C4] transition duration-150">
                                        <td class="px-6 py-3 border-b border-[#801336] text-primary text-body-md font-regular">{{ $order->id }}</td>
                                        <td class="px-6 py-3 border-b border-[#801336] text-primary text-body-md font-regular">
                                            <div>
                                                <p class="font-medium">{{ $order->user->name }}</p>
                                                <p class="text-body-sm text-primary-light">{{ $order->user->email }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 border-b border-[#801336] text-body-md">
                                            <div class="space-y-1">
                                                @foreach($order->orderItems->take(2) as $item)
                                                    <p class="text-body-sm">{{ $item->quantity }}x {{ $item->product->name }}</p>
                                                @endforeach
                                                @if($order->orderItems->count() > 2)
                                                    <p class="text-body-sm text-primary-light">+{{ $order->orderItems->count() - 2 }} más...</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 border-b border-[#801336] text-primary text-body-md font-semibold">${{ number_format($order->total_price, 2) }}</td>
                                        <td class="px-6 py-3 border-b border-[#801336]">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-body-sm font-medium bg-success-100 text-success-dark">
                                                ✓ Pagado
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 border-b border-[#801336]">
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('trabajador.orders.show', $order->id) }}"
                                                   class="flex items-center text-primary hover:text-primary-700 text-body-sm font-medium space-x-1 transition duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                        <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Ver</span>
                                                </a>

                                                <form action="{{ route('trabajador.orders.accept', $order->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="flex items-center text-success hover:text-success-dark text-body-sm font-medium space-x-1 transition duration-150"
                                                            onclick="return confirm('¿Estás seguro de que quieres aceptar este pedido?')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>Aceptar</span>
                                                    </button>
                                                </form>

                                                <button onclick="openRejectModal({{ $order->id }})"
                                                        class="flex items-center text-error hover:text-error-dark text-body-sm font-medium space-x-1 transition duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Rechazar</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-primary-light text-body-lg font-regular italic">No hay pedidos pagados pendientes de aceptación.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Rechazo -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Rechazar Pedido</h3>
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

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-150">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-error text-white rounded-md hover:bg-error-dark transition duration-150">
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

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
