<x-app-layout>
    <div class="py-6" x-data="{ tab: 'laravel' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-line rounded-lg shadow-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-display-sm text-carbon">Monitoreo del sistema</h1>
                        <p class="text-body-md text-muted mt-1">Estado de salud, métricas y registros recientes.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button id="refresh-btn" type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 min-h-[44px] rounded-md border border-line text-carbon hover:bg-paper transition">
                            <span class="material-icons text-base">refresh</span>
                            <span>Actualizar</span>
                        </button>
                        <button id="health-check-btn" type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 min-h-[44px] rounded-md bg-carbon text-white hover:opacity-90 transition">
                            <span class="material-icons text-base">monitor_heart</span>
                            <span>Ejecutar revisión</span>
                        </button>
                    </div>
                </div>
            </div>

            @if (isset($error))
                <div class="bg-white border border-line rounded-lg shadow p-4 mb-6" role="alert">
                    <p class="text-body-md text-error">{{ $error }}</p>
                </div>
            @endif

            <div class="bg-white border border-line rounded-lg shadow-lg p-4 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h2 class="text-heading-lg text-carbon">Estado de salud</h2>
                    <span id="health-status-badge">
                        @if (isset($healthCheck))
                            @if ($healthCheck['status'] === 'healthy')
                                <x-status-badge type="success">Saludable</x-status-badge>
                            @else
                                <x-status-badge type="error">Con problemas</x-status-badge>
                            @endif
                        @else
                            <x-status-badge type="neutral">Desconocido</x-status-badge>
                        @endif
                    </span>
                </div>
                <div id="health-check-results">
                    @if (isset($healthCheck))
                        @if (! empty($healthCheck['alerts']))
                            <div class="rounded-md border border-line bg-paper p-4">
                                <h3 class="text-body-md font-semibold text-carbon">Alertas activas ({{ count($healthCheck['alerts']) }})</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                    @foreach ($healthCheck['alerts'] as $alert)
                                        <div class="rounded-md border border-line bg-white p-3">
                                            <p class="text-body-md text-carbon"><strong>{{ ucfirst($alert['category']) }}</strong>: {{ $alert['type'] }}</p>
                                            @if (! empty($alert['data']))
                                                <p class="mt-1 text-body-sm text-muted break-all"><small>{{ json_encode($alert['data']) }}</small></p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button id="clear-alerts-btn" type="button" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 min-h-[44px] rounded-md border border-line text-carbon hover:bg-white transition">
                                    <span class="material-icons text-base">close</span>
                                    <span>Limpiar alertas</span>
                                </button>
                            </div>
                        @else
                            <p class="text-body-md text-success">Todos los sistemas funcionan con normalidad.</p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-line rounded-lg shadow p-6 text-center">
                    <p class="text-body-md font-medium text-carbon">Pedidos hoy</p>
                    <p class="text-display-sm text-carbon mt-1" id="metric-pedidos">{{ $systemMetrics['orders_today'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-line rounded-lg shadow p-6 text-center">
                    <p class="text-body-md font-medium text-carbon">Ingresos hoy</p>
                    <p class="text-display-sm text-carbon mt-1" id="metric-ingresos">${{ number_format($systemMetrics['revenue_today'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-white border border-line rounded-lg shadow p-6 text-center">
                    <p class="text-body-md font-medium text-carbon">Stock bajo</p>
                    <p class="text-display-sm text-carbon mt-1" id="metric-stock">{{ $systemMetrics['low_stock_count'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-line rounded-lg shadow p-6 text-center">
                    <p class="text-body-md font-medium text-carbon">Usuarios activos</p>
                    <p class="text-display-sm text-carbon mt-1" id="metric-usuarios">{{ $systemMetrics['active_users_today'] ?? 0 }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <div class="bg-white border border-line rounded-lg shadow p-4 sm:p-6">
                    <h2 class="text-heading-lg text-carbon mb-3">Métricas del sistema</h2>
                    <div id="system-metrics" class="overflow-x-auto">
                        @if (isset($healthCheck['metrics']))
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-carbon text-white">
                                        <th scope="col" class="px-4 py-3 text-left text-body-sm font-semibold">Métrica</th>
                                        <th scope="col" class="px-4 py-3 text-right text-body-sm font-semibold">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($healthCheck['metrics'] as $metric => $value)
                                        <tr class="border-b border-line hover:bg-paper">
                                            <td class="px-4 py-3 text-body-md text-carbon">{{ ucwords(str_replace('_', ' ', $metric)) }}</td>
                                            <td class="px-4 py-3 text-body-md text-carbon text-right">
                                                @if (is_numeric($value))
                                                    {{ number_format($value, 2) }}
                                                    @if (str_contains($metric, 'percent'))
                                                        %
                                                    @elseif (str_contains($metric, 'time'))
                                                        ms
                                                    @elseif (str_contains($metric, 'gb'))
                                                        GB
                                                    @endif
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-body-md text-muted italic">Sin métricas disponibles.</p>
                        @endif
                    </div>
                </div>
                <div class="bg-white border border-line rounded-lg shadow p-4 sm:p-6">
                    <h2 class="text-heading-lg text-carbon mb-3">Rendimiento</h2>
                    <div id="performance-metrics" class="overflow-x-auto">
                        @if (isset($performanceMetrics))
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-carbon text-white">
                                        <th scope="col" class="px-4 py-3 text-left text-body-sm font-semibold">Métrica</th>
                                        <th scope="col" class="px-4 py-3 text-right text-body-sm font-semibold">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($performanceMetrics['memory_usage']))
                                        <tr class="border-b border-line">
                                            <td class="px-4 py-3 text-body-md">Memoria en uso</td>
                                            <td class="px-4 py-3 text-body-md text-right">{{ number_format($performanceMetrics['memory_usage']['current'] / 1024 / 1024, 2) }} MB</td>
                                        </tr>
                                        <tr class="border-b border-line">
                                            <td class="px-4 py-3 text-body-md">Pico de memoria</td>
                                            <td class="px-4 py-3 text-body-md text-right">{{ number_format($performanceMetrics['memory_usage']['peak'] / 1024 / 1024, 2) }} MB</td>
                                        </tr>
                                    @endif
                                    @if (isset($performanceMetrics['database']))
                                        <tr class="border-b border-line">
                                            <td class="px-4 py-3 text-body-md">Conexiones BD</td>
                                            <td class="px-4 py-3 text-body-md text-right">{{ $performanceMetrics['database']['active_connections'] }}</td>
                                        </tr>
                                        <tr class="border-b border-line">
                                            <td class="px-4 py-3 text-body-md">Consultas lentas</td>
                                            <td class="px-4 py-3 text-body-md text-right">{{ $performanceMetrics['database']['slow_queries'] }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @else
                            <p class="text-body-md text-muted italic">Sin métricas de rendimiento.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white border border-line rounded-lg shadow p-4 sm:p-6">
                <h2 class="text-heading-lg text-carbon mb-3">Registros recientes</h2>
                <div class="flex flex-wrap gap-2 mb-4" role="tablist" aria-label="Tipos de registro">
                    @foreach (['laravel' => 'Aplicación', 'payments' => 'Pagos', 'security' => 'Seguridad', 'orders' => 'Pedidos'] as $key => $label)
                        <button type="button" role="tab" :aria-selected="tab === '{{ $key }}'" @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'bg-carbon text-white' : 'border border-line text-carbon hover:bg-paper'" class="px-4 py-2 min-h-[44px] rounded-md text-body-md transition">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                @if (isset($recentLogs))
                    @foreach (['laravel', 'payments', 'security', 'orders'] as $logType)
                        <div x-show="tab === '{{ $logType }}'" x-cloak role="tabpanel">
                            <div class="max-h-72 overflow-y-auto rounded-md bg-paper border border-line p-4">
                                <pre class="text-xs break-all whitespace-pre-wrap m-0">@if (isset($recentLogs[$logType])){{ implode("\n", $recentLogs[$logType]) }}@else Sin entradas recientes @endif</pre>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-body-md text-muted italic">Sin registros disponibles.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const dinero = (v) => '$' + Number(v ?? 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function pintarBadge(estado) {
            const badge = document.getElementById('health-status-badge');
            if (!badge) {
                return;
            }
            const sano = estado === 'healthy';
            badge.innerHTML = `<span class="${sano ? 'badge-success' : 'badge-error'}">${sano ? 'Saludable' : 'Con problemas'}</span>`;
        }

        function pintarMetricas(m) {
            if (!m) {
                return;
            }
            const set = (id, valor) => { const el = document.getElementById(id); if (el) { el.textContent = valor; } };
            set('metric-pedidos', m.orders_today ?? 0);
            set('metric-ingresos', dinero(m.revenue_today));
            set('metric-stock', m.low_stock_count ?? 0);
            set('metric-usuarios', m.active_users_today ?? 0);
        }

        function refreshData() {
            fetch('/admin/monitoreo/estado-salud', { headers: { Accept: 'application/json' } })
                .then((response) => response.json())
                .then((data) => pintarBadge(data.status))
                .catch((error) => console.error('Error refreshing data:', error));

            fetch('/admin/monitoreo/metricas', { headers: { Accept: 'application/json' } })
                .then((response) => response.json())
                .then((data) => pintarMetricas(data.data))
                .catch((error) => console.error('Error refreshing metrics:', error));
        }

        document.getElementById('refresh-btn')?.addEventListener('click', refreshData);

        document.getElementById('health-check-btn')?.addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            fetch('/admin/monitoreo/ejecutar-revision', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                },
            })
                .then((response) => response.json())
                .then(() => window.location.reload())
                .catch((error) => console.error('Error running health check:', error))
                .finally(() => { btn.disabled = false; });
        });

        document.addEventListener('click', function (event) {
            if (event.target && event.target.id === 'clear-alerts-btn') {
                fetch('/admin/monitoreo/limpiar-alertas', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json',
                    },
                })
                    .then((response) => response.json())
                    .then(() => window.location.reload())
                    .catch((error) => console.error('Error clearing alerts:', error));
            }
        });
    });
    </script>
</x-app-layout>
