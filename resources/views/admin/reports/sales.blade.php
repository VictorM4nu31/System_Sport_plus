<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Reporte de Ventas</h1>

        <!-- Formulario para filtrar por rango de fechas -->
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="mb-6">
            <div class="flex space-x-4">
                <div>
                    <label for="start_date" class="block text-gray-700">Fecha de Inicio</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="block w-full mt-1">
                </div>
                <div>
                    <label for="end_date" class="block text-gray-700">Fecha de Fin</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="block w-full mt-1">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Filtrar</button>
                </div>
            </div>
        </form>

        <!-- Mostrar los resultados del reporte -->
        @if ($sales->isEmpty())
            <p>No se encontraron ventas en este rango de fechas.</p>
        @else
            <h2 class="text-xl font-semibold mb-4">Ventas por Fecha</h2>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2">Fecha</th>
                        <th class="px-6 py-3 border-b-2">Total de Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td class="px-6 py-3 border-b">{{ $sale->date }}</td>
                            <td class="px-6 py-3 border-b">${{ number_format($sale->total_sales, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Productos más vendidos -->
        <h2 class="text-xl font-semibold mt-6 mb-4">Top 5 Productos Más Vendidos</h2>
        @if ($topProducts->isEmpty())
            <p>No se encontraron productos vendidos en este rango de fechas.</p>
        @else
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2">Producto</th>
                        <th class="px-6 py-3 border-b-2">Cantidad Vendida</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topProducts as $item)
                        <tr>
                            <td class="px-6 py-3 border-b">{{ $item->product->name }}</td>
                            <td class="px-6 py-3 border-b">{{ $item->total_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
