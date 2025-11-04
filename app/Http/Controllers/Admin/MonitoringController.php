<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    protected MonitoringService $monitoring;

    public function __construct(MonitoringService $monitoring)
    {
        $this->monitoring = $monitoring;
    }

    /**
     * Display the monitoring dashboard
     */
    public function dashboard()
    {
        try {
            $healthCheck = $this->monitoring->performHealthCheck();
            $systemMetrics = $this->monitoring->getSystemMetrics();
            $recentAlerts = Cache::get('system_alerts', []);

            // Get recent log entries
            $recentLogs = $this->getRecentLogEntries();

            // Get performance metrics
            $performanceMetrics = $this->getPerformanceMetrics();

            return view('admin.monitoring.dashboard', compact(
                'healthCheck',
                'systemMetrics',
                'recentAlerts',
                'recentLogs',
                'performanceMetrics'
            ));

        } catch (\Exception $e) {
            Log::error('Monitoring dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('admin.monitoring.dashboard')->with('error', 'Unable to load monitoring data');
        }
    }

    /**
     * Get system health status (API endpoint)
     */
    public function healthStatus()
    {
        try {
            $healthCheck = $this->monitoring->performHealthCheck();
            return response()->json($healthCheck);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Health check failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system metrics (API endpoint)
     */
    public function metrics()
    {
        try {
            $metrics = $this->monitoring->getSystemMetrics();
            return response()->json([
                'status' => 'success',
                'data' => $metrics,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent alerts (API endpoint)
     */
    public function alerts()
    {
        try {
            $alerts = Cache::get('system_alerts', []);

            return response()->json([
                'status' => 'success',
                'data' => $alerts,
                'count' => count($alerts),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve alerts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear system alerts
     */
    public function clearAlerts()
    {
        try {
            Cache::forget('system_alerts');

            Log::channel('audit')->info('System alerts cleared', [
                'user_id' => Auth::id(),
                'ip_address' => request()->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Alerts cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear alerts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run manual health check
     */
    public function runHealthCheck()
    {
        try {
            $results = $this->monitoring->performHealthCheck();

            Log::channel('audit')->info('Manual health check performed', [
                'user_id' => Auth::id(),
                'results' => $results
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Health check completed',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Health check failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get log entries for monitoring
     */
    public function logs(Request $request)
    {
        try {
            $logType = $request->get('type', 'laravel');
            $lines = $request->get('lines', 100);

            $logFile = match ($logType) {
                'payments' => storage_path('logs/payments.log'),
                'security' => storage_path('logs/security.log'),
                'orders' => storage_path('logs/orders.log'),
                'audit' => storage_path('logs/audit.log'),
                default => storage_path('logs/laravel.log')
            };

            if (!file_exists($logFile)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Log file not found'
                ], 404);
            }

            $logContent = $this->getTailOfFile($logFile, $lines);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'type' => $logType,
                    'content' => $logContent,
                    'lines' => count(explode("\n", $logContent))
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance metrics over time
     */
    public function performanceHistory(Request $request)
    {
        try {
            $hours = $request->get('hours', 24);
            $startTime = now()->subHours($hours);

            // Get payment metrics
            $paymentMetrics = [];
            for ($i = 0; $i < $hours; $i++) {
                $hour = $startTime->copy()->addHours($i);
                $key = "payment_metrics:" . $hour->format('Y-m-d-H');
                $metrics = Cache::get($key, [
                    'attempts' => 0,
                    'successes' => 0,
                    'failures' => 0,
                    'total_amount' => 0
                ]);

                $paymentMetrics[] = [
                    'hour' => $hour->format('H:00'),
                    'date' => $hour->format('Y-m-d'),
                    'metrics' => $metrics
                ];
            }

            // Get order metrics
            $orderMetrics = DB::table('orders')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(CASE WHEN payment_status = "completed" THEN 1 ELSE 0 END) as completed_orders'),
                    DB::raw('SUM(CASE WHEN payment_status = "completed" THEN total_price ELSE 0 END) as revenue')
                )
                ->where('created_at', '>=', $startTime)
                ->groupBy('date', 'hour')
                ->orderBy('date')
                ->orderBy('hour')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'payment_metrics' => $paymentMetrics,
                    'order_metrics' => $orderMetrics,
                    'period' => [
                        'start' => $startTime->toISOString(),
                        'end' => now()->toISOString(),
                        'hours' => $hours
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve performance history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent log entries for dashboard
     */
    protected function getRecentLogEntries(): array
    {
        $logTypes = ['laravel', 'payments', 'security', 'orders'];
        $recentLogs = [];

        foreach ($logTypes as $type) {
            $logFile = storage_path("logs/{$type}.log");

            if (file_exists($logFile)) {
                $content = $this->getTailOfFile($logFile, 10);
                $lines = array_filter(explode("\n", $content));

                $recentLogs[$type] = array_slice($lines, -5); // Last 5 entries
            } else {
                $recentLogs[$type] = [];
            }
        }

        return $recentLogs;
    }

    /**
     * Get performance metrics for dashboard
     */
    protected function getPerformanceMetrics(): array
    {
        try {
            return [
                'response_times' => [
                    'avg_web' => $this->getAverageResponseTime('web'),
                    'avg_api' => $this->getAverageResponseTime('api'),
                ],
                'database' => [
                    'active_connections' => $this->getActiveConnections(),
                    'slow_queries' => $this->getSlowQueryCount(),
                ],
                'memory_usage' => [
                    'current' => memory_get_usage(true),
                    'peak' => memory_get_peak_usage(true),
                ],
                'cache_stats' => $this->getCacheStats(),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get performance metrics', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get tail of a file (last N lines)
     */
    protected function getTailOfFile(string $file, int $lines): string
    {
        $handle = fopen($file, 'r');
        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = [];

        while ($linecounter > 0) {
            $t = " ";
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines - $linecounter - 1] = fgets($handle);
            if ($beginning) break;
        }
        fclose($handle);

        return implode("", array_reverse($text));
    }

    /**
     * Get average response time for route type
     */
    protected function getAverageResponseTime(string $type): float
    {
        // This would typically come from application performance monitoring
        // For now, return a placeholder value
        return rand(100, 500) / 100; // Random value between 1-5 seconds
    }

    /**
     * Get active database connections
     */
    protected function getActiveConnections(): int
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get slow query count
     */
    protected function getSlowQueryCount(): int
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Slow_queries'");
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache statistics
     */
    protected function getCacheStats(): array
    {
        try {
            // This would depend on your cache driver
            return [
                'hits' => 0,
                'misses' => 0,
                'hit_ratio' => 0
            ];
        } catch (\Exception $e) {
            return [
                'hits' => 0,
                'misses' => 0,
                'hit_ratio' => 0
            ];
        }
    }
}
