<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Contenedor principal con transparencia y sombra -->
            <div class="bg-white border border-line shadow-lg rounded-lg p-4 sm:p-6 lg:p-8">

                <!-- Título de la sección con ícono y color de fondo -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                    <h1 class="text-display-sm text-carbon font-bold">Gestión de Pedidos</h1>
                    <p class="text-body-md text-muted">{{ $orders->count() }} pedidos</p>
                </div>

                <!-- Tabla de pedidos -->
                <div class="overflow-x-auto">
                    <table class="w-full bg-white bg-opacity-95 rounded-lg shadow-lg mt-4">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md whitespace-nowrap">ID</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md whitespace-nowrap">Usuario</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md whitespace-nowrap">Total</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md whitespace-nowrap">Estado</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md">Dirección</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md whitespace-nowrap">Teléfono</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md">Indicaciones</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left text-body-md whitespace-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr class="hover:bg-paper transition duration-150">
                                    <td class="px-4 py-3 border-b border-line text-carbon whitespace-nowrap">#{{ $order->id }}</td>
                                    <td class="px-4 py-3 border-b border-line text-carbon whitespace-nowrap">{{ $order->user->name }}</td>
                                    <td class="px-4 py-3 border-b border-line text-carbon whitespace-nowrap price-mono">${{ number_format($order->total_price, 2) }}</td>
                                    <td class="px-4 py-3 border-b border-line whitespace-nowrap"><x-status-badge type="info">{{ ucfirst($order->status) }}</x-status-badge></td>
                                    <td class="px-4 py-3 border-b border-line text-carbon max-w-[12rem] truncate" title="@if ($order->address){{ $order->address->neighborhood }}, {{ $order->address->street }} {{ $order->address->number }}@elseif ($order->shipping_address){{ $order->shipping_address }}@else Sin dirección @endif">
                                        @if ($order->address)
                                            {{ $order->address->neighborhood }}, {{ $order->address->street }} {{ $order->address->number }}
                                        @elseif ($order->shipping_address)
                                            {{ $order->shipping_address }}
                                        @else
                                            <span class="text-muted">Sin dirección</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b border-line text-carbon whitespace-nowrap">
                                        {{ $order->address?->contact_phone ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-line text-carbon max-w-[12rem] truncate" title="{{ $order->address?->additional_instructions ?? '—' }}">
                                        {{ $order->address?->additional_instructions ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-line">
                                        <div class="flex items-center gap-2">
                                            <!-- Botón de Ver con ícono -->
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 p-2 min-h-[44px] text-info hover:text-info font-semibold" aria-label="Ver pedido #{{ $order->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M12 4.5a7.5 7.5 0 1 1-7.5 7.5A7.5 7.5 0 0 1 12 4.5m0-1.5a9 9 0 1 0 9 9 9 9 0 0 0-9-9z"/>
                                                    <path d="M12 9a3 3 0 1 1-3 3 3 3 0 0 1 3-3m0-1.5a4.5 4.5 0 1 0 4.5 4.5 4.5 4.5 0 0 0-4.5-4.5z"/>
                                                </svg>
                                                <span>Ver</span>
                                            </a>

                                            <!-- Botón de Eliminar con ícono -->
                                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="flex items-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Eliminar este pedido?')" class="inline-flex items-center gap-1 p-2 min-h-[44px] text-error hover:text-red-700 font-semibold" aria-label="Eliminar pedido #{{ $order->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>Eliminar</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <p class="text-body-lg text-carbon font-semibold">Sin pedidos por aquí</p>
                                        <p class="text-body-md text-muted mt-1">Cuando lleguen nuevos pedidos aparecerán en esta tabla.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
