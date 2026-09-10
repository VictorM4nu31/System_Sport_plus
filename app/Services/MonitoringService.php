<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MonitoringService
{
    protected array $alerts = [];

    protected array $metrics = [];

    /**
     * Check system health and generate alerts if needed
     */
    public function performHealthCheck(): array
    {
        $this->checkDatabaseHealth();
        $this->checkPaymentSystemHealth();
        $this->checkStockLevels();
        $this->checkQueueHealth();
        $this->checkStorageHealth();
        $this->checkErrorRates();

        $this->processAlerts();

        return [
            'status' => empty($this->alerts) ? 'healthy' : 'unhealthy',
            'alerts' => $this->alerts,
            'metrics' => $this->metrics,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Check database connectivity and performance
     */
    protected function checkDatabaseHealth(): void
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $responseTime = (microtime(true) - $start) * 1000;

            $this->metrics['database_response_time'] = round($responseTime, 2);

            if ($responseTime > 1000) { // 1 second
                $this->addAlert('database', 'slow_response', [
                    'response_time' => $responseTime,
                    'threshold' => 1000,
                ]);
            }

            // Check for long-running queries (sintaxis según el motor).
            $longQueries = $this->countLongRunningQueries();

            if ($longQueries > 0) {
                $this->addAlert('database', 'long_running_queries', [
                    'count' => $longQueries,
                ]);
            }

        } catch (Exception $e) {
            $this->addAlert('database', 'connection_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Contar consultas de larga duración según el motor de BD.
     */
    protected function countLongRunningQueries(): int
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $result = DB::select("
                SELECT COUNT(*) as count
                FROM pg_stat_activity
                WHERE state <> 'idle'
                AND pid <> pg_backend_pid()
                AND now() - query_start > interval '30 seconds'
            ");

            return (int) ($result[0]->count ?? 0);
        }

        if ($driver === 'mysql') {
            $result = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.processlist
                WHERE command != 'Sleep' AND time > 30
            ");

            return (int) ($result[0]->count ?? 0);
        }

        return 0;
    }

    /**
     * Check payment system health
     */
    protected function checkPaymentSystemHealth(): void
    {
        try {
            // Check recent payment failures
            $recentFailures = DB::table('orders')
                ->where('payment_status', 'failed')
                ->where('created_at', '>=', now()->subHours(1))
                ->count();

            $this->metrics['payment_failures_last_hour'] = $recentFailures;

            if ($recentFailures > 5) {
                $this->addAlert('payments', 'high_failure_rate', [
                    'failures' => $recentFailures,
                    'threshold' => 5,
                ]);
            }

            // Check payment processing time (sintaxis según el motor).
            $avgProcessingTime = $this->averageProcessingSeconds();

            $this->metrics['avg_payment_processing_time'] = round($avgProcessingTime ?? 0, 2);

            if ($avgProcessingTime > 30) { // 30 seconds
                $this->addAlert('payments', 'slow_processing', [
                    'avg_time' => $avgProcessingTime,
                    'threshold' => 30,
                ]);
            }

        } catch (Exception $e) {
            $this->addAlert('payments', 'monitoring_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Promedio en segundos entre creación y actualización del pedido,
     * según el motor de BD. Null si no hay pedidos completados.
     */
    protected function averageProcessingSeconds(): ?float
    {
        $query = DB::table('orders')
            ->whereIn('payment_status', ['paid', 'completed'])
            ->where('created_at', '>=', now()->subDay());

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $value = $query->avg(DB::raw('EXTRACT(EPOCH FROM (updated_at - created_at))'));

            return $value === null ? null : (float) $value;
        }

        if ($driver === 'mysql') {
            $value = $query->avg(DB::raw('TIMESTAMPDIFF(SECOND, created_at, updated_at)'));

            return $value === null ? null : (float) $value;
        }

        return null;
    }

    /**
     * Check stock levels and alert for low stock
     */
    protected function checkStockLevels(): void
    {
        try {
            $lowStockProducts = Product::where('stock', '<=', 5)
                ->where('stock', '>', 0)
                ->get();

            $outOfStockProducts = Product::where('stock', 0)->count();

            $this->metrics['low_stock_products'] = $lowStockProducts->count();
            $this->metrics['out_of_stock_products'] = $outOfStockProducts;

            if ($lowStockProducts->count() > 0) {
                $this->addAlert('inventory', 'low_stock', [
                    'products' => $lowStockProducts->pluck('name', 'id')->toArray(),
                    'count' => $lowStockProducts->count(),
                ]);
            }

            if ($outOfStockProducts > 10) {
                $this->addAlert('inventory', 'high_out_of_stock', [
                    'count' => $outOfStockProducts,
                    'threshold' => 10,
                ]);
            }

        } catch (Exception $e) {
            $this->addAlert('inventory', 'monitoring_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check queue health
     */
    protected function checkQueueHealth(): void
    {
        try {
            // Check failed jobs
            $failedJobs = DB::table('failed_jobs')
                ->where('failed_at', '>=', now()->subHour())
                ->count();

            $this->metrics['failed_jobs_last_hour'] = $failedJobs;

            if ($failedJobs > 10) {
                $this->addAlert('queue', 'high_failure_rate', [
                    'failed_jobs' => $failedJobs,
                    'threshold' => 10,
                ]);
            }

            // Check queue size
            $queueSize = DB::table('jobs')->count();
            $this->metrics['queue_size'] = $queueSize;

            if ($queueSize > 1000) {
                $this->addAlert('queue', 'high_queue_size', [
                    'size' => $queueSize,
                    'threshold' => 1000,
                ]);
            }

        } catch (Exception $e) {
            $this->addAlert('queue', 'monitoring_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check storage health
     */
    protected function checkStorageHealth(): void
    {
        try {
            $storagePath = storage_path();
            $freeBytes = disk_free_space($storagePath);
            $totalBytes = disk_total_space($storagePath);
            $usedPercent = (($totalBytes - $freeBytes) / $totalBytes) * 100;

            $this->metrics['storage_used_percent'] = round($usedPercent, 2);
            $this->metrics['storage_free_gb'] = round($freeBytes / (1024 ** 3), 2);

            if ($usedPercent > 85) {
                $this->addAlert('storage', 'high_usage', [
                    'used_percent' => $usedPercent,
                    'free_gb' => round($freeBytes / (1024 ** 3), 2),
                    'threshold' => 85,
                ]);
            }

        } catch (Exception $e) {
            $this->addAlert('storage', 'monitoring_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check error rates in logs
     */
    protected function checkErrorRates(): void
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (file_exists($logPath)) {
                $logContent = file_get_contents($logPath);
                $lines = explode("\n", $logContent);

                $recentErrors = 0;
                $cutoffTime = now()->subHour();

                foreach (array_reverse($lines) as $line) {
                    if (empty($line)) {
                        continue;
                    }

                    // Parse log timestamp
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        $logTime = Carbon::createFromFormat('Y-m-d H:i:s', $matches[1]);

                        if ($logTime < $cutoffTime) {
                            break; // Stop checking older logs
                        }

                        if (strpos($line, '.ERROR:') !== false) {
                            $recentErrors++;
                        }
                    }
                }

                $this->metrics['errors_last_hour'] = $recentErrors;

                if ($recentErrors > 50) {
                    $this->addAlert('application', 'high_error_rate', [
                        'errors' => $recentErrors,
                        'threshold' => 50,
                    ]);
                }
            }

        } catch (Exception $e) {
            $this->addAlert('application', 'log_monitoring_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Add an alert to the alerts array
     */
    protected function addAlert(string $category, string $type, array $data): void
    {
        $this->alerts[] = [
            'category' => $category,
            'type' => $type,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'severity' => $this->getAlertSeverity($category, $type),
        ];
    }

    /**
     * Get alert severity level
     */
    protected function getAlertSeverity(string $category, string $type): string
    {
        $criticalAlerts = [
            'database.connection_failed',
            'payments.high_failure_rate',
            'storage.high_usage',
        ];

        $warningAlerts = [
            'database.slow_response',
            'payments.slow_processing',
            'inventory.low_stock',
            'queue.high_failure_rate',
        ];

        $alertKey = "{$category}.{$type}";

        if (in_array($alertKey, $criticalAlerts)) {
            return 'critical';
        } elseif (in_array($alertKey, $warningAlerts)) {
            return 'warning';
        }

        return 'info';
    }

    /**
     * Process alerts and send notifications if needed
     */
    protected function processAlerts(): void
    {
        if (empty($this->alerts)) {
            return;
        }

        // Log all alerts
        foreach ($this->alerts as $alert) {
            Log::channel('audit')->warning('System alert generated', $alert);
        }

        // Send email notifications for critical alerts
        $criticalAlerts = array_filter($this->alerts, function ($alert) {
            return $alert['severity'] === 'critical';
        });

        if (! empty($criticalAlerts)) {
            $this->sendCriticalAlertEmail($criticalAlerts);
        }

        // Cache alerts for dashboard display
        Cache::put('system_alerts', $this->alerts, now()->addMinutes(30));
    }

    /**
     * Send email notification for critical alerts
     */
    protected function sendCriticalAlertEmail(array $alerts): void
    {
        try {
            $adminEmail = config('app.admin_email', 'admin@example.com');

            if ($adminEmail && $adminEmail !== 'admin@example.com') {
                Mail::raw($this->formatAlertEmail($alerts), function ($message) use ($adminEmail) {
                    $message->to($adminEmail)
                        ->subject('Critical System Alert - '.config('app.name'));
                });
            }
        } catch (Exception $e) {
            Log::error('Failed to send alert email', [
                'error' => $e->getMessage(),
                'alerts' => $alerts,
            ]);
        }
    }

    /**
     * Format alerts for email notification
     */
    protected function formatAlertEmail(array $alerts): string
    {
        $message = "Critical system alerts detected:\n\n";

        foreach ($alerts as $alert) {
            $message .= "Category: {$alert['category']}\n";
            $message .= "Type: {$alert['type']}\n";
            $message .= "Severity: {$alert['severity']}\n";
            $message .= "Time: {$alert['timestamp']}\n";
            $message .= 'Details: '.json_encode($alert['data'], JSON_PRETTY_PRINT)."\n";
            $message .= str_repeat('-', 50)."\n";
        }

        $message .= "\nPlease check the system immediately.\n";
        $message .= 'Dashboard: '.config('app.url')."/admin/monitoring\n";

        return $message;
    }

    /**
     * Get cached system metrics
     */
    public function getSystemMetrics(): array
    {
        return Cache::remember('system_metrics', now()->addMinutes(5), function () {
            return [
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'orders_this_week' => Order::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])->count(),
                'revenue_today' => Order::whereDate('created_at', today())
                    ->whereIn('payment_status', ['paid', 'completed'])
                    ->sum('total_price'),
                'revenue_this_week' => Order::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])->whereIn('payment_status', ['paid', 'completed'])->sum('total_price'),
                'active_users_today' => DB::table('sessions')
                    ->where('last_activity', '>=', now()->subDay()->timestamp)
                    ->whereNotNull('user_id')
                    ->distinct('user_id')
                    ->count(),
                'low_stock_count' => Product::where('stock', '<=', 5)->count(),
                'out_of_stock_count' => Product::where('stock', 0)->count(),
            ];
        });
    }

    /**
     * Log payment event for monitoring
     */
    public function logPaymentEvent(string $event, array $data): void
    {
        Log::channel('payments')->info("Payment event: {$event}", array_merge($data, [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]));

        // Track payment metrics
        $this->trackPaymentMetrics($event, $data);
    }

    /**
     * Track payment metrics for monitoring
     */
    protected function trackPaymentMetrics(string $event, array $data): void
    {
        $key = 'payment_metrics:'.now()->format('Y-m-d-H');

        $metrics = Cache::get($key, [
            'attempts' => 0,
            'successes' => 0,
            'failures' => 0,
            'total_amount' => 0,
        ]);

        switch ($event) {
            case 'payment_attempt':
                $metrics['attempts']++;
                break;
            case 'payment_success':
                $metrics['successes']++;
                $metrics['total_amount'] += $data['amount'] ?? 0;
                break;
            case 'payment_failed':
                $metrics['failures']++;
                break;
        }

        Cache::put($key, $metrics, now()->addHours(25));
    }

    /**
     * Log stock event for monitoring
     */
    public function logStockEvent(string $event, array $data): void
    {
        Log::channel('audit')->info("Stock event: {$event}", array_merge($data, [
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString(),
        ]));
    }

    /**
     * Log security event for monitoring
     */
    public function logSecurityEvent(string $event, array $data): void
    {
        Log::channel('security')->warning("Security event: {$event}", array_merge($data, [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]));

        // Track security metrics
        $this->trackSecurityMetrics($event);
    }

    /**
     * Track security metrics
     */
    protected function trackSecurityMetrics(string $event): void
    {
        $key = 'security_metrics:'.now()->format('Y-m-d');

        $metrics = Cache::get($key, []);
        $metrics[$event] = ($metrics[$event] ?? 0) + 1;

        Cache::put($key, $metrics, now()->addDays(2));
    }
}
