<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// System Monitoring and Maintenance Commands
Schedule::command('system:health-check')->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/health-check.log'));

Schedule::command('monitoring:cleanup')->daily()
    ->at('02:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/cleanup.log'));

Schedule::command('stock:cleanup-reservations')->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Log rotation and cleanup
Schedule::command('queue:prune-failed --hours=168')->weekly(); // Keep failed jobs for 1 week

// Performance monitoring
Schedule::call(function () {
    $monitoring = app(\App\Services\MonitoringService::class);
    $metrics = $monitoring->getSystemMetrics();

    // Log daily metrics summary
    \Illuminate\Support\Facades\Log::channel('audit')->info('Daily metrics summary', $metrics);
})->dailyAt('23:59');

// Security log analysis
Schedule::call(function () {
    $monitoring = app(\App\Services\MonitoringService::class);

    // Check for suspicious activity patterns
    $securityEvents = \Illuminate\Support\Facades\Cache::get('security_metrics:' . now()->format('Y-m-d'), []);

    if (!empty($securityEvents)) {
        $monitoring->logSecurityEvent('daily_security_summary', $securityEvents);
    }
})->dailyAt('00:30');
