<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupMonitoringData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:cleanup
                           {--days=30 : Number of days to keep data}
                           {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old monitoring data, logs, and cache entries';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning up monitoring data older than {$days} days...");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No data will be actually deleted');
        }

        $this->line('');

        try {
            $this->cleanupFailedJobs($cutoffDate, $dryRun);
            $this->cleanupOldSessions($cutoffDate, $dryRun);
            $this->cleanupCacheEntries($dryRun);
            $this->cleanupLogFiles($days, $dryRun);
            $this->cleanupTempFiles($dryRun);

            $this->line('');
            $this->info('✅ Monitoring data cleanup completed successfully');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Clean up old failed jobs
     */
    protected function cleanupFailedJobs(Carbon $cutoffDate, bool $dryRun): void
    {
        $count = DB::table('failed_jobs')
            ->where('failed_at', '<', $cutoffDate)
            ->count();

        if ($count > 0) {
            $this->line("🗑️  Failed jobs to clean up: {$count}");

            if (!$dryRun) {
                DB::table('failed_jobs')
                    ->where('failed_at', '<', $cutoffDate)
                    ->delete();
                $this->info("   ✅ Deleted {$count} old failed jobs");
            }
        } else {
            $this->line("✅ No old failed jobs to clean up");
        }
    }

    /**
     * Clean up old sessions
     */
    protected function cleanupOldSessions(Carbon $cutoffDate, bool $dryRun): void
    {
        $cutoffTimestamp = $cutoffDate->timestamp;

        $count = DB::table('sessions')
            ->where('last_activity', '<', $cutoffTimestamp)
            ->count();

        if ($count > 0) {
            $this->line("🗑️  Old sessions to clean up: {$count}");

            if (!$dryRun) {
                DB::table('sessions')
                    ->where('last_activity', '<', $cutoffTimestamp)
                    ->delete();
                $this->info("   ✅ Deleted {$count} old sessions");
            }
        } else {
            $this->line("✅ No old sessions to clean up");
        }
    }

    /**
     * Clean up cache entries
     */
    protected function cleanupCacheEntries(bool $dryRun): void
    {
        $patterns = [
            'system_alerts',
            'system_metrics',
            'payment_metrics:*',
            'security_metrics:*',
            'stock_*'
        ];

        $this->line("🗑️  Cleaning up cache entries...");

        if (!$dryRun) {
            foreach ($patterns as $pattern) {
                if (str_contains($pattern, '*')) {
                    // For wildcard patterns, we need to use Redis directly
                    try {
                        $keys = Cache::getRedis()->keys($pattern);
                        if (!empty($keys)) {
                            Cache::getRedis()->del($keys);
                            $this->info("   ✅ Cleared cache pattern: {$pattern}");
                        }
                    } catch (\Exception $e) {
                        // Fallback for non-Redis cache drivers
                        $this->warn("   ⚠️  Could not clear pattern {$pattern}: " . $e->getMessage());
                    }
                } else {
                    Cache::forget($pattern);
                    $this->info("   ✅ Cleared cache key: {$pattern}");
                }
            }
        } else {
            foreach ($patterns as $pattern) {
                $this->line("   Would clear cache: {$pattern}");
            }
        }
    }

    /**
     * Clean up old log files
     */
    protected function cleanupLogFiles(int $days, bool $dryRun): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = Carbon::now()->subDays($days);

        if (!is_dir($logPath)) {
            $this->warn("Log directory not found: {$logPath}");
            return;
        }

        $files = glob($logPath . '/*.log*');
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $fileTime = Carbon::createFromTimestamp(filemtime($file));

            if ($fileTime->lt($cutoffDate)) {
                $fileSize = filesize($file);
                $totalSize += $fileSize;

                if (!$dryRun) {
                    unlink($file);
                }

                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $sizeFormatted = $this->formatBytes($totalSize);
            $this->line("🗑️  Old log files to clean up: {$deletedCount} files ({$sizeFormatted})");

            if (!$dryRun) {
                $this->info("   ✅ Deleted {$deletedCount} old log files");
            }
        } else {
            $this->line("✅ No old log files to clean up");
        }
    }

    /**
     * Clean up temporary files
     */
    protected function cleanupTempFiles(bool $dryRun): void
    {
        $tempPaths = [
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views')
        ];

        $totalFiles = 0;
        $totalSize = 0;

        foreach ($tempPaths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $files = glob($path . '/*');

            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileAge = time() - filemtime($file);

                    // Delete files older than 24 hours
                    if ($fileAge > 86400) {
                        $fileSize = filesize($file);
                        $totalSize += $fileSize;
                        $totalFiles++;

                        if (!$dryRun) {
                            unlink($file);
                        }
                    }
                }
            }
        }

        if ($totalFiles > 0) {
            $sizeFormatted = $this->formatBytes($totalSize);
            $this->line("🗑️  Temporary files to clean up: {$totalFiles} files ({$sizeFormatted})");

            if (!$dryRun) {
                $this->info("   ✅ Deleted {$totalFiles} temporary files");
            }
        } else {
            $this->line("✅ No temporary files to clean up");
        }
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
