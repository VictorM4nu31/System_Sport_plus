@extends('layouts.admin')

@section('title', 'System Monitoring Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">System Monitoring Dashboard</h1>
                <div>
                    <button id="refresh-btn" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button id="health-check-btn" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-heartbeat"></i> Run Health Check
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> {{ $error }}
        </div>
    @endif

    <!-- System Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">System Health Status</h5>
                    <span id="health-status-badge" class="badge badge-lg">
                        @if(isset($healthCheck))
                            @if($healthCheck['status'] === 'healthy')
                                <span class="badge bg-success">Healthy</span>
                            @else
                                <span class="badge bg-danger">Unhealthy</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <div id="health-check-results">
                        @if(isset($healthCheck))
                            @if(!empty($healthCheck['alerts']))
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Active Alerts ({{ count($healthCheck['alerts']) }})</h6>
                                    <div class="row">
                                        @foreach($healthCheck['alerts'] as $alert)
                                            <div class="col-md-6 mb-2">
                                                <div class="alert alert-{{ $alert['severity'] === 'critical' ? 'danger' : ($alert['severity'] === 'warning' ? 'warning' : 'info') }} alert-sm">
                                                    <strong>{{ ucfirst($alert['category']) }}</strong>: {{ $alert['type'] }}
                                                    @if(!empty($alert['data']))
                                                        <br><small>{{ json_encode($alert['data']) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button id="clear-alerts-btn" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-times"></i> Clear Alerts
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> All systems are operating normally
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Overview -->
    <div class="row mb-4">
        @if(isset($systemMetrics))
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Orders Today</h5>
                        <h2 class="text-primary">{{ $systemMetrics['orders_today'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">Revenue Today</h5>
                        <h2 class="text-success">${{ number_format(($systemMetrics['revenue_today'] ?? 0) / 100, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Low Stock Items</h5>
                        <h2 class="text-warning">{{ $systemMetrics['low_stock_count'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">Active Users</h5>
                        <h2 class="text-info">{{ $systemMetrics['active_users_today'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Detailed Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">System Metrics</h5>
                </div>
                <div class="card-body">
                    <div id="system-metrics">
                        @if(isset($healthCheck['metrics']))
                            <table class="table table-sm">
                                @foreach($healthCheck['metrics'] as $metric => $value)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $metric)) }}</td>
                                        <td class="text-end">
                                            @if(is_numeric($value))
                                                {{ number_format($value, 2) }}
                                                @if(str_contains($metric, 'percent'))
                                                    %
                                                @elseif(str_contains($metric, 'time'))
                                                    ms
                                                @elseif(str_contains($metric, 'gb'))
                                                    GB
                                                @endif
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div id="performance-metrics">
                        @if(isset($performanceMetrics))
                            <table class="table table-sm">
                                @if(isset($performanceMetrics['memory_usage']))
                                    <tr>
                                        <td>Memory Usage</td>
                                        <td class="text-end">{{ number_format($performanceMetrics['memory_usage']['current'] / 1024 / 1024, 2) }} MB</td>
                                    </tr>
                                    <tr>
                                        <td>Peak Memory</td>
                                        <td class="text-end">{{ number_format($performanceMetrics['memory_usage']['peak'] / 1024 / 1024, 2) }} MB</td>
                                    </tr>
                                @endif
                                @if(isset($performanceMetrics['database']))
                                    <tr>
                                        <td>DB Connections</td>
                                        <td class="text-end">{{ $performanceMetrics['database']['active_connections'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Slow Queries</td>
                                        <td class="text-end">{{ $performanceMetrics['database']['slow_queries'] }}</td>
                                    </tr>
                                @endif
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Log Entries</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="logTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="laravel-tab" data-bs-toggle="tab" data-bs-target="#laravel" type="button" role="tab">Application</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Payments</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">Security</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">Orders</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="logTabContent">
                        @if(isset($recentLogs))
                            @foreach(['laravel', 'payments', 'security', 'orders'] as $logType)
                                <div class="tab-pane fade {{ $logType === 'laravel' ? 'show active' : '' }}" id="{{ $logType }}" role="tabpanel">
                                    <div class="log-container" style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                                        <pre style="font-size: 12px; margin: 0;">@if(isset($recentLogs[$logType])){{ implode("\n", $recentLogs[$logType]) }}@else
No recent entries@endif</pre>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh and AJAX functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let autoRefresh = true;
    let refreshInterval;

    // Auto-refresh every 30 seconds
    function startAutoRefresh() {
        refreshInterval = setInterval(function() {
            if (autoRefresh) {
                refreshData();
            }
        }, 30000);
    }

    // Refresh data via AJAX
    function refreshData() {
        fetch('/admin/monitoring/health-status')
            .then(response => response.json())
            .then(data => {
                updateHealthStatus(data);
            })
            .catch(error => {
                console.error('Error refreshing data:', error);
            });

        fetch('/admin/monitoring/metrics')
            .then(response => response.json())
            .then(data => {
                updateMetrics(data.data);
            })
            .catch(error => {
                console.error('Error refreshing metrics:', error);
            });
    }

    // Update health status display
    function updateHealthStatus(data) {
        const badge = document.getElementById('health-status-badge');
        const results = document.getElementById('health-check-results');

        if (data.status === 'healthy') {
            badge.innerHTML = '<span class="badge bg-success">Healthy</span>';
            results.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> All systems are operating normally</div>';
        } else {
            badge.innerHTML = '<span class="badge bg-danger">Unhealthy</span>';
            let alertsHtml = '<div class="alert alert-warning"><h6><i class="fas fa-exclamation-triangle"></i> Active Alerts (' + data.alerts.length + ')</h6><div class="row">';

            data.alerts.forEach(alert => {
                const severity = alert.severity === 'critical' ? 'danger' : (alert.severity === 'warning' ? 'warning' : 'info');
                alertsHtml += '<div class="col-md-6 mb-2"><div class="alert alert-' + severity + ' alert-sm">';
                alertsHtml += '<strong>' + alert.category.charAt(0).toUpperCase() + alert.category.slice(1) + '</strong>: ' + alert.type;
                if (alert.data && Object.keys(alert.data).length > 0) {
                    alertsHtml += '<br><small>' + JSON.stringify(alert.data) + '</small>';
                }
                alertsHtml += '</div></div>';
            });

            alertsHtml += '</div><button id="clear-alerts-btn" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i> Clear Alerts</button></div>';
            results.innerHTML = alertsHtml;
        }
    }

    // Update metrics display
    function updateMetrics(metrics) {
        const metricsContainer = document.getElementById('system-metrics');
        if (metricsContainer && metrics) {
            let html = '<table class="table table-sm">';
            Object.entries(metrics).forEach(([key, value]) => {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                let displayValue = value;

                if (typeof value === 'number') {
                    displayValue = value.toLocaleString();
                    if (key.includes('percent')) displayValue += '%';
                    else if (key.includes('time')) displayValue += 'ms';
                    else if (key.includes('gb')) displayValue += 'GB';
                }

                html += '<tr><td>' + label + '</td><td class="text-end">' + displayValue + '</td></tr>';
            });
            html += '</table>';
            metricsContainer.innerHTML = html;
        }
    }

    // Manual refresh button
    document.getElementById('refresh-btn').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        refreshData();
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
        }, 1000);
    });

    // Manual health check button
    document.getElementById('health-check-btn').addEventListener('click', function() {
        const btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running...';
        btn.disabled = true;

        fetch('/admin/monitoring/run-health-check', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateHealthStatus(data.data);
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = '<i class="fas fa-check"></i> Health check completed successfully <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
            } else {
                console.error('Health check failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error running health check:', error);
        })
        .finally(() => {
            btn.innerHTML = '<i class="fas fa-heartbeat"></i> Run Health Check';
            btn.disabled = false;
        });
    });

    // Clear alerts functionality
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'clear-alerts-btn') {
            fetch('/admin/monitoring/clear-alerts', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    refreshData();
                }
            })
            .catch(error => {
                console.error('Error clearing alerts:', error);
            });
        }
    });

    // Start auto-refresh
    startAutoRefresh();
});
</script>
@endsection
