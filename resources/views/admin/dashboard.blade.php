<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white bg-opacity-95 shadow-lg rounded-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-display-sm font-bold text-primary">Dashboard Administrativo</h1>
                        <p class="text-body-md text-primary-600 mt-1">Bienvenido, {{ auth()->user()->name }}. Aquí tienes el resumen de tu tienda deportiva.</p>
                    </div>
                    <div class="sm:text-right shrink-0">
                        <p class="text-body-sm text-gray-500">{{ now()->format('d/m/Y H:i') }}</p>
                        <p class="text-body-sm text-gray-500">{{ now()->translatedFormat('l') }}</p>
                    </div>
                </div>
            </div>

            @if(!isset($stats) || empty($stats))
                <!-- Mensaje cuando no hay datos -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                        <svg class="h-6 w-6 text-warning-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="message-warning">Dashboard en Configuración</h3>
                    <p class="message-warning">
                        El sistema de analíticas se está configurando. Los datos aparecerán una vez que haya actividad en la tienda.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <a href="{{ route('admin.products.index') }}"
                           class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-700 font-semibold text-body-md">
                            Gestionar Productos
                        </a>
                        <a href="{{ route('admin.orders.index') }}"
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-semibold text-body-md">
                            Ver pedidos
                        </a>
                    </div>
                </div>
            @else

            <!-- Estadísticas Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Visitantes Hoy -->
                <div class="bg-white border border-line text-carbon rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-muted text-body-md font-medium">Visitantes Hoy</p>
                            <p class="text-display-sm font-bold price-mono">{{ number_format($stats['visitors']['today'] ?? 0) }}</p>
                            <p class="text-muted text-body-sm mt-1">
                                Únicos: {{ number_format($stats['visitors']['unique_today'] ?? 0) }}
                            </p>
                        </div>
                        <div class="bg-paper border border-line rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ventas del Mes -->
                <div class="bg-white border border-line text-carbon rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-success text-body-md font-semibold">Ventas del Mes</p>
                            <p class="text-display-sm font-bold price-mono">${{ number_format($stats['sales']['this_month_sales'] ?? 0, 2) }}</p>
                            <p class="text-muted text-body-sm mt-1 font-medium">
                                {{ $stats['sales']['orders_this_month'] ?? 0 }} órdenes
                            </p>
                        </div>
                        <div class="bg-success-light rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Productos Totales -->
                <div class="bg-white border border-line text-carbon rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-muted text-body-md font-medium">Productos Totales</p>
                            <p class="text-display-sm font-bold price-mono">{{ number_format($stats['products']['total_products'] ?? 0) }}</p>
                            <p class="text-muted text-body-sm mt-1">
                                En stock: {{ $stats['products']['products_in_stock'] ?? 0 }}
                            </p>
                        </div>
                        <div class="bg-volt rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM6 9a1 1 0 112 0 1 1 0 01-2 0zm6 0a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Órdenes Pendientes -->
                <div class="bg-white border border-line text-carbon rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-warning-dark text-body-md font-semibold">Órdenes Pendientes</p>
                            <p class="text-display-sm font-bold price-mono">{{ number_format($stats['orders']['pending_orders'] ?? 0) }}</p>
                            <p class="text-muted text-body-sm mt-1 font-medium">
                                En proceso: {{ $stats['orders']['processing_orders'] ?? 0 }}
                            </p>
                        </div>
                        <div class="bg-warning-light rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Gráfica de Visitantes -->
                <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-primary mb-4">Visitantes Diarios (Últimos 30 días)</h3>
                    <div class="h-64">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>

                <!-- Gráfica de Ventas -->
                <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-primary mb-4">Ventas Diarias (Últimos 30 días)</h3>
                    <div class="h-64">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Productos y Categorías -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Top Productos -->
                <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-primary mb-4">Productos Más Vendidos</h3>
                    <div class="h-64">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>

                <!-- Ventas por Categoría -->
                <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-primary mb-4">Ventas por Categoría</h3>
                    <div class="h-64">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Fuentes de Tráfico -->
                <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-primary mb-4">Fuentes de Tráfico</h3>
                    <div class="h-48">
                        <canvas id="sourcesChart"></canvas>
                    </div>
                </div>

                <!-- Dispositivos -->
                <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-primary mb-4">Dispositivos</h3>
                    <div class="h-48">
                        <canvas id="devicesChart"></canvas>
                    </div>
                </div>

                <!-- Productos Más Vistos -->
                <div class="bg-white bg-opacity-95 rounded-lg shadow-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-primary mb-4">Productos Más Vistos (Esta Semana)</h3>
                    <div class="space-y-3">
                        @if(isset($stats['products']['most_viewed_week']) && $stats['products']['most_viewed_week']->count() > 0)
                            @foreach($stats['products']['most_viewed_week'] as $item)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-body-md font-medium text-gray-800">{{ $item->product->name ?? 'Producto eliminado' }}</span>
                                    <span class="text-body-md text-primary font-semibold">{{ $item->views }} vistas</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-500 text-body-md">No hay datos de vistas aún</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Alertas y Notificaciones -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Productos con Stock Bajo -->
                @if(isset($stats['products']['low_stock_products']) && $stats['products']['low_stock_products'] > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-warning-dark mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <h3 class="message-warning">Alerta de Stock</h3>
                    </div>
                    <p class="message-warning">
                        Tienes {{ $stats['products']['low_stock_products'] }} productos con stock bajo (≤10 unidades).
                    </p>
                    <a href="{{ route('admin.products.index') }}" class="message-warning">
                        Ver productos →
                    </a>
                </div>
                @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-success-dark mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <h3 class="message-success">Inventario Saludable</h3>
                    </div>
                    <p class="text-body-md text-success-dark-dark">
                        Todos los productos tienen stock adecuado.
                    </p>
                </div>
                @endif

                <!-- Resumen Rápido -->
                <div class="bg-[#801336] rounded-lg p-6">
                    <h3 class="text-heading-lg font-semibold text-white mb-4">Resumen Rápido</h3>
                    <div class="space-y-2 text-body-md">
                        <div class="flex justify-between">
                            <span class="text-white">Valor promedio de orden:</span>
                            <span class="font-semibold text-white">${{ number_format($stats['orders']['average_order_value'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white">Productos destacados:</span>
                            <span class="font-semibold text-white">{{ $stats['products']['featured_products'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white">Visitantes únicos del mes:</span>
                            <span class="font-semibold text-white">{{ number_format($stats['visitors']['unique_this_month'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white">Total páginas vistas:</span>
                            <span class="font-semibold text-white">{{ number_format($stats['visitors']['total_pages_viewed'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para las gráficas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuración común para las gráficas
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
        Chart.defaults.color = '#6B7280';

        // Colores del tema
        const colors = {
            primary: '#801336',
            secondary: '#9b1a3e',
            success: '#10B981',
            warning: '#F59E0B',
            danger: '#EF4444',
            info: '#3B82F6',
        };

        // Función para mostrar mensaje de error
        function showChartError(chartId, message) {
            const chartElement = document.getElementById(chartId);
            if (chartElement) {
                chartElement.parentElement.innerHTML = `<div class="flex items-center justify-center h-64 text-gray-500"><p>${message}</p></div>`;
            }
        }

        // Función para cargar gráfica con manejo de errores
        function loadChart(type, chartId, chartConfig) {
            fetch(`/admin/dashboard/chart-data?type=${type}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data || (Array.isArray(data) && data.length === 0)) {
                        showChartError(chartId, `No hay datos de ${type} disponibles`);
                        return;
                    }

                    const ctx = document.getElementById(chartId);
                    if (ctx) {
                        new Chart(ctx.getContext('2d'), chartConfig(data));
                    }
                })
                .catch(error => {
                    console.error(`Error loading ${type} chart:`, error);
                    showChartError(chartId, `Error cargando datos de ${type}`);
                });
        }

        // Cargar todas las gráficas
        document.addEventListener('DOMContentLoaded', function() {
            // Solo cargar gráficas si existen los elementos
            if (document.getElementById('visitorsChart')) {
                loadChart('visitors', 'visitorsChart', (data) => ({
                    type: 'line',
                    data: {
                        labels: data.map(item => new Date(item.date).toLocaleDateString()),
                        datasets: [{
                            label: 'Visitantes Únicos',
                            data: data.map(item => item.unique_visitors || 0),
                            borderColor: colors.primary,
                            backgroundColor: colors.primary + '20',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: { y: { beginAtZero: true } }
                    }
                }));
            }

            if (document.getElementById('salesChart')) {
                loadChart('sales', 'salesChart', (data) => ({
                    type: 'bar',
                    data: {
                        labels: data.map(item => new Date(item.date).toLocaleDateString()),
                        datasets: [{
                            label: 'Ventas ($)',
                            data: data.map(item => parseFloat(item.total_sales || 0)),
                            backgroundColor: colors.success,
                            borderColor: colors.success,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                }));
            }

            // Cargar otras gráficas solo si hay datos
            ['products', 'categories', 'sources', 'devices'].forEach(type => {
                const chartId = type + 'Chart';
                if (document.getElementById(chartId)) {
                    loadChart(type, chartId, (data) => ({
                        type: 'doughnut',
                        data: {
                            labels: data.map(item => item.name || item.source || item.device_type || 'Sin nombre'),
                            datasets: [{
                                data: data.map(item => parseFloat(item.total_sales || item.visits || item.total_sold || 0)),
                                backgroundColor: [colors.primary, colors.info, colors.success, colors.warning, colors.danger]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    }));
                }
            });
        });
    </script>
            @endif
        </div>
    </div>
</x-app-layout>

