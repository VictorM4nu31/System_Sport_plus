<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupSecurityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup-security {--days=90 : Number of days to keep security logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old security and audit log files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $logPaths = [
            storage_path('logs/security-*.log'),
            storage_path('logs/audit-*.log'),
        ];

        $deletedFiles = 0;

        foreach ($logPaths as $pattern) {
            $files = glob($pattern);

            foreach ($files as $file) {
                $fileDate = Carbon::createFromTimestamp(filemtime($file));

                if ($fileDate->lt($cutoffDate)) {
                    if (File::delete($file)) {
                        $deletedFiles++;
                        $this->info("Deleted: " . basename($file));
                    } else {
                        $this->error("Failed to delete: " . basename($file));
                    }
                }
            }
        }

        Log::channel('security')->info('Security logs cleanup completed', [
            'deleted_files' => $deletedFiles,
            'cutoff_date' => $cutoffDate->toDateString(),
            'retention_days' => $days,
            'timestamp' => now(),
        ]);

        $this->info("Cleanup completed. Deleted {$deletedFiles} old log files.");

        return Command::SUCCESS;
    }
}
