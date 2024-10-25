<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold mb-6">Reporte de Ventas</h1>

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
    </div>
</x-app-layout>
