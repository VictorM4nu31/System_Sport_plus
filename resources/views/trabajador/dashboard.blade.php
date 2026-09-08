<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-line rounded-lg shadow-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-display-sm font-bold text-primary">Panel de trabajador</h1>
                        <p class="text-body-md text-primary-light mt-1">Bienvenido, {{ auth()->user()->name }}. Atiende la cola de pedidos.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('trabajador.orders.index') }}" class="bg-white border border-line rounded-lg shadow p-6 hover:bg-paper transition">
                    <p class="text-body-md font-semibold text-carbon">Cola de pedidos</p>
                    <p class="text-body-sm text-muted mt-1">Ver, aceptar o rechazar pedidos.</p>
                </a>
                <a href="{{ route('trabajador.reports.sales') }}" class="bg-white border border-line rounded-lg shadow p-6 hover:bg-paper transition">
                    <p class="text-body-md font-semibold text-carbon">Reporte de ventas</p>
                    <p class="text-body-sm text-muted mt-1">Revisar pedidos completados.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
