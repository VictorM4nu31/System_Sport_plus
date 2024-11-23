<x-app-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Contenedor principal con transparencia y bordes redondeados -->
        <div class="bg-[#DDEAF2] bg-opacity-70 shadow-2xl rounded-lg p-8">
            <h1 class="text-3xl font-bold mb-6 text-[#476D9D]">Reporte de Ventas</h1>

            <!-- Formulario para filtrar por rango de fechas -->
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="mb-8 bg-[#B5DFF5] bg-opacity-60 p-4 rounded-lg shadow-inner">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1">
                        <label for="start_date" class="block text-[#476D9D] font-semibold">Fecha de Inicio</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="block w-full mt-1 bg-white border border-[#85C7E6] rounded-md focus:ring-[#85C7E6] focus:border-[#85C7E6] shadow-sm">
                    </div>
                    <div class="flex-1">
                        <label for="end_date" class="block text-[#476D9D] font-semibold">Fecha de Fin</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="block w-full mt-1 bg-white border border-[#85C7E6] rounded-md focus:ring-[#85C7E6] focus:border-[#85C7E6] shadow-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-2 bg-[#85C7E6] text-white font-semibold rounded-md hover:bg-[#476D9D] transition duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#85C7E6]">
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Mostrar los resultados del reporte -->
            @if ($sales->isEmpty())
                <p class="text-[#476D9D] italic">No se encontraron ventas en este rango de fechas.</p>
            @else
                <h2 class="text-2xl font-semibold mb-4 text-[#476D9D]">Ventas por Fecha</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-[#476D9D] text-white">
                                <th class="px-6 py-3 border-b-2 border-[#85C7E6] text-left">Fecha</th>
                                <th class="px-6 py-3 border-b-2 border-[#85C7E6] text-left">Total de Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr class="hover:bg-[#E7E3C4] transition duration-150">
                                    <td class="px-6 py-3 border-b border-[#B5DFF5] text-[#476D9D]">{{ $sale->date }}</td>
                                    <td class="px-6 py-3 border-b border-[#B5DFF5] text-[#476D9D]">${{ number_format($sale->total_sales, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Productos más vendidos -->
            <h2 class="text-2xl font-semibold mt-8 mb-4 text-[#476D9D]">Top 5 Productos Más Vendidos</h2>
            @if ($topProducts->isEmpty())
                <p class="text-[#476D9D] italic">No se encontraron productos vendidos en este rango de fechas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white bg-opacity-90 rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-[#476D9D] text-white">
                                <th class="px-6 py-3 border-b-2 border-[#85C7E6] text-left">Producto</th>
                                <th class="px-6 py-3 border-b-2 border-[#85C7E6] text-left">Cantidad Vendida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topProducts as $item)
                                <tr class="hover:bg-[#E7E3C4] transition duration-150">
                                    <td class="px-6 py-3 border-b border-[#B5DFF5] text-[#476D9D]">{{ $item->product->name }}</td>
                                    <td class="px-6 py-3 border-b border-[#B5DFF5] text-[#476D9D]">{{ $item->total_quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
