<x-app-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con transparencia y bordes redondeados -->
        <div class="bg-white border border-line shadow-lg rounded-lg p-4 sm:p-6 lg:p-8">
            <h1 class="text-display-sm text-carbon font-bold mb-6">Reporte de Ventas</h1>

            <!-- Formulario para filtrar por rango de fechas -->
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="mb-8 bg-paper border border-line p-4 rounded-lg shadow-inner">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="start_date" class="block text-carbon font-semibold">Fecha de Inicio</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="block w-full mt-1 px-4 py-2 bg-white border border-line rounded-md focus:ring-2 focus:ring-carbon focus:border-carbon shadow-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-carbon font-semibold">Fecha de Fin</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="block w-full mt-1 px-4 py-2 bg-white border border-line rounded-md focus:ring-2 focus:ring-carbon focus:border-carbon shadow-sm">
                    </div>
                    <div class="flex flex-wrap items-end gap-2 sm:col-span-2 lg:col-span-2">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] rounded-md bg-carbon text-white hover:opacity-90 transition focus:outline-none focus:ring-2 focus:ring-carbon w-full sm:w-auto">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] rounded-md border border-line text-carbon hover:bg-white transition w-full sm:w-auto">Limpiar</a>
                    </div>
                </div>
            </form>

            <!-- Mostrar los resultados del reporte -->
            @if ($sales->isEmpty())
                <div class="py-12 text-center">
                    <p class="text-body-lg text-carbon font-semibold">Sin ventas en este rango</p>
                    <p class="text-body-md text-muted mt-1">Ajusta las fechas o revisa los pedidos recientes.</p>
                </div>
            @else
                <h2 class="text-heading-lg text-primary mb-4">Ventas por Fecha</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-left whitespace-nowrap">Fecha</th>
                                <th scope="col" class="px-4 py-3 border-b-2 border-line text-right whitespace-nowrap">Total de Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr class="hover:bg-paper transition duration-150">
                                    <td class="px-4 py-3 border-b border-line text-carbon whitespace-nowrap">{{ $sale->date }}</td>
                                    <td class="px-4 py-3 border-b border-line text-carbon text-right price-mono">${{ number_format($sale->total_sales, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="px-4 py-3 text-right font-semibold text-carbon">Total</td>
                                <td class="px-4 py-3 text-right font-semibold text-carbon price-mono">${{ number_format($sales->sum('total_sales'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <!-- Productos más vendidos -->
            <h2 class="text-heading-lg text-primary mt-8 mb-4">Top 5 Productos Más Vendidos</h2>
            @if ($topProducts->isEmpty())
                <div class="py-12 text-center">
                    <p class="text-body-lg text-carbon font-semibold">Sin productos vendidos</p>
                    <p class="text-body-md text-muted mt-1">Prueba con otro rango de fechas.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th class="px-6 py-3 border-b-2 border-line text-left">Producto</th>
                                <th class="px-6 py-3 border-b-2 border-line text-left">Cantidad Vendida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topProducts as $item)
                                <tr class="hover:bg-paper transition duration-150">
                                    <td class="px-6 py-3 border-b border-line text-carbon">{{ $item->product->name }}</td>
                                    <td class="px-6 py-3 border-b border-line text-carbon">{{ $item->total_quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
