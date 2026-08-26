<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <x-alert type="success" dismissible="true" class="mb-6">
                {{ session('success') }}
            </x-alert>
        @endif

        <h1 class="text-xl sm:text-2xl text-white font-semibold mb-4 sm:mb-6">Mis Pedidos</h1>

        @if ($orders->count())
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Pedido</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">{{ $order->id }}</td>
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">${{ number_format($order->total_price, 2) }}</td>
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pendiente de Pago
                                            </span>
                                            @break
                                        @case('paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Pagado - Esperando Confirmación
                                            </span>
                                            @break
                                        @case('confirmed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-dark">
                                                Confirmado
                                            </span>
                                            @break
                                        @case('en proceso')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary">
                                                En Proceso
                                            </span>
                                            @break
                                        @case('completado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success">
                                                Completado
                                            </span>
                                            @break
                                        @case('cancelado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-error-100 text-error">
                                                Cancelado
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Rechazado
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('usuario.orders.show', $order->id) }}"
                                       class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-white text-sm sm:text-base">No has realizado ningún pedido.</p>
        @endif
    </div>
</x-app-layout>
