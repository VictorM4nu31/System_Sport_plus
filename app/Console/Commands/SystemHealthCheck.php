<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonitoringService;
use Illuminate\Support\Facades\Log;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:health-check
                           {--alert : Send alerts for critical issues}
                           {--detailed : Show detailed output}';

    /**
     * The console command description.
     */
    protected $description = 'Perform comprehensive system health check and monitoring';

    /**
     * Execute the console command.
     */
    public function handle(MonitoringService $monitoring): int
    {
        $this->info('Starting system health check...');

        try {
            $results = $monitoring->performHealthCheck();

            if ($this->option('detailed')) {
                $this->displayDetailedResults($results);
            } else {
                $this->displaySummaryResults($results);
            }

            // Log the health check results
            Log::channel('audit')->info('System health check completed', [
                'status' => $results['status'],
                'alert_count' => count($results['alerts']),
                'metrics' => $results['metrics']
            ]);

            return $results['status'] === 'healthy' ? 0 : 1;

        } catch (\Exception $e) {
            $this->error('Health check failed: ' . $e->getMessage());
            Log::error('System health check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Display detailed health check results
     */
    protected function displayDetailedResults(array $results): void
    {
        $this->line('');
        $this->info('=== SYSTEM HEALTH CHECK RESULTS ===');
        $this->line('');

        // Overall status
        if ($results['status'] === 'healthy') {
            $this->info('✅ Overall Status: HEALTHY');
        } else {
            $this->error('❌ Overall Status: UNHEALTHY');
        }

        $this->line('');

        // Display metrics
        if (!empty($results['metrics'])) {
            $this->info('📊 System Metrics:');
            foreach ($results['metrics'] as $metric => $value) {
                $this->line("   {$metric}: {$value}");
            }
            $this->line('');
        }

        // Display alerts
        if (!empty($results['alerts'])) {
            $this->error('🚨 Active Alerts:');
            foreach ($results['alerts'] as $alert) {
                $severity = strtoupper($alert['severity']);
                $icon = $this->getSeverityIcon($alert['severity']);

                $this->line("   {$icon} [{$severity}] {$alert['category']}.{$alert['type']}");

                if (!empty($alert['data'])) {
                    foreach ($alert['data'] as $key => $value) {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }
                        $this->line("      {$key}: {$value}");
                    }
                }
                $this->line('');
            }
        } else {
            $this->info('✅ No active alerts');
        }

        $this->line('Timestamp: ' . $results['timestamp']);
    }

    /**
     * Display summary health check results
     */
    protected function displaySummaryResults(array $results): void
    {
        if ($results['status'] === 'healthy') {
            $this->info('✅ System is healthy');
        } else {
            $this->error('❌ System has issues');

            $criticalCount = count(array_filter($results['alerts'], function ($alert) {
                return $alert['severity'] === 'critical';
            }));

            $warningCount = count(array_filter($results['alerts'], function ($alert) {
                return $alert['severity'] === 'warning';
            }));

            if ($criticalCount > 0) {
                $this->error("   {$criticalCount} critical alert(s)");
            }

            if ($warningCount > 0) {
                $this->warn("   {$warningCount} warning(s)");
            }
        }
    }

    /**
     * Get icon for alert severity
     */
    protected function getSeverityIcon(string $severity): string
    {
        return match ($severity) {
            'critical' => '🔴',
            'warning' => '🟡',
            'info' => '🔵',
            default => '⚪'
        };
    }
}
