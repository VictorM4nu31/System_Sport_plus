<x-app-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con transparencia y bordes redondeados -->
        <div class="bg-white border border-line shadow-xl rounded-lg p-8">
            <h1 class="text-display-sm text-carbon font-bold mb-6">Reporte de Ventas</h1>

            <!-- Formulario para filtrar por rango de fechas -->
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="mb-8 bg-paper border border-line p-4 rounded-lg shadow-inner">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1">
                        <label for="start_date" class="block text-carbon font-semibold">Fecha de Inicio</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="block w-full mt-1 bg-white border border-line rounded-md focus:ring-carbon focus:border-carbon shadow-sm">
                    </div>
                    <div class="flex-1">
                        <label for="end_date" class="block text-carbon font-semibold">Fecha de Fin</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="block w-full mt-1 bg-white border border-line rounded-md focus:ring-carbon focus:border-carbon shadow-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-carbon focus-volt">
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Mostrar los resultados del reporte -->
            @if ($sales->isEmpty())
                <p class="text-carbon italic">No se encontraron ventas en este rango de fechas.</p>
            @else
                <h2 class="text-heading-lg text-primary mb-4">Ventas por Fecha</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-carbon text-white">
                                <th class="px-6 py-3 border-b-2 border-line text-left">Fecha</th>
                                <th class="px-6 py-3 border-b-2 border-line text-left">Total de Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr class="hover:bg-paper transition duration-150">
                                    <td class="px-6 py-3 border-b border-line text-carbon">{{ $sale->date }}</td>
                                    <td class="px-6 py-3 border-b border-line text-carbon">${{ number_format($sale->total_sales, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Productos más vendidos -->
            <h2 class="text-heading-lg text-primary mt-8 mb-4">Top 5 Productos Más Vendidos</h2>
            @if ($topProducts->isEmpty())
                <p class="text-carbon italic">No se encontraron productos vendidos en este rango de fechas.</p>
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
