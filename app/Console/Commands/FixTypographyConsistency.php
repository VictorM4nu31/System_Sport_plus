<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixTypographyConsistency extends Command
{
    protected $signature = 'typography:fix {--dry-run : Show what would be changed without making changes} {--file= : Fix specific file only}';
    protected $description = 'Fix typography consistency issues found in audit';

    private $replacements = [
        // Replace old Tailwind size classes with our typography system
        'text-xs' => 'text-body-xs',
        'text-sm' => 'text-body-sm',
        'text-base' => 'text-body-lg',
        'text-lg' => 'text-heading-sm',
        'text-xl' => 'text-heading-md',
        'text-2xl' => 'text-heading-lg',
        'text-3xl' => 'text-heading-xl',
        'text-4xl' => 'text-display-sm',
        'text-5xl' => 'text-display-md',
        'text-6xl' => 'text-display-lg',

        // Replace old font weight classes
        'font-normal' => 'font-regular',
        'font-thin' => 'font-light',
        'font-extralight' => 'font-light',
        'font-extrabold' => 'font-bold',
        'font-black' => 'font-bold',

        // Replace old color classes with semantic ones
        'text-red-600' => 'text-error',
        'text-red-500' => 'text-error',
        'text-red-700' => 'text-error-dark',
        'text-green-600' => 'text-success',
        'text-green-500' => 'text-success',
        'text-green-700' => 'text-success-dark',
        'text-yellow-600' => 'text-warning',
        'text-yellow-500' => 'text-warning',
        'text-orange-600' => 'text-warning',
        'text-blue-600' => 'text-primary-lighter',
        'text-blue-500' => 'text-primary-lighter',
        'text-indigo-600' => 'text-primary',
        'text-purple-600' => 'text-primary-light',
        'text-pink-600' => 'text-primary-lighter',
    ];

    private $messageReplacements = [
        // Replace alert classes with semantic message classes
        'class="alert alert-success"' => 'class="alert-success"',
        'class="alert alert-warning"' => 'class="alert-warning"',
        'class="alert alert-danger"' => 'class="alert-error"',
        'class="alert alert-info"' => 'class="alert-info"',

        // Replace basic error styling with semantic classes
        'class="text-red-600"' => 'class="text-error"',
        'class="text-green-600"' => 'class="text-success"',
        'class="text-yellow-600"' => 'class="text-warning"',
    ];

    private $fixedFiles = [];
    private $totalChanges = 0;

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $specificFile = $this->option('file');

        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no files will be modified');
        }

        $this->info('Starting Typography Consistency Fix...');

        if ($specificFile) {
            $this->fixFile($specificFile, $isDryRun);
        } else {
            $this->fixAllFiles($isDryRun);
        }

        $this->generateSummary();

        return 0;
    }

    private function fixAllFiles($isDryRun)
    {
        // Fix admin interface files
        $this->fixAdminFiles($isDryRun);

        // Fix user interface files
        $this->fixUserFiles($isDryRun);

        // Fix shared component files
        $this->fixSharedFiles($isDryRun);
    }

    private function fixAdminFiles($isDryRun)
    {
        $this->info('Fixing Admin Interface files...');

        $adminFiles = [
            'resources/views/admin/dashboard.blade.php',
            'resources/views/admin/products/index.blade.php',
            'resources/views/admin/products/create.blade.php',
            'resources/views/admin/products/edit.blade.php',
            'resources/views/admin/products/show.blade.php',
        ];

        foreach ($adminFiles as $file) {
            if (File::exists($file)) {
                $this->fixFile($file, $isDryRun);
            }
        }
    }

    private function fixUserFiles($isDryRun)
    {
        $this->info('Fixing User Interface files...');

        $userFiles = [
            'resources/views/usuario/products/partials/product-card.blade.php',
            'resources/views/usuario/addresses/index.blade.php',
            'resources/views/usuario/addresses/create.blade.php',
            'resources/views/usuario/addresses/edit.blade.php',
        ];

        foreach ($userFiles as $file) {
            if (File::exists($file)) {
                $this->fixFile($file, $isDryRun);
            }
        }
    }

    private function fixSharedFiles($isDryRun)
    {
        $this->info('Fixing Shared Component files...');

        $sharedFiles = [
            'resources/views/components/banner.blade.php',
            'resources/views/components/button.blade.php',
            'resources/views/components/checkbox.blade.php',
            'resources/views/components/confirmation-modal.blade.php',
            'resources/views/components/danger-button.blade.php',
            'resources/views/components/dialog-modal.blade.php',
            'resources/views/components/dropdown-link.blade.php',
            'resources/views/components/input-error.blade.php',
            'resources/views/components/input-label.blade.php',
            'resources/views/components/label.blade.php',
            'resources/views/components/nav-link.blade.php',
            'resources/views/components/primary-button.blade.php',
            'resources/views/components/responsive-nav-link.blade.php',
            'resources/views/components/secondary-button.blade.php',
            'resources/views/components/section-title.blade.php',
            'resources/views/components/switchable-team.blade.php',
            'resources/views/components/validation-errors.blade.php',
            'resources/views/components/welcome.blade.php',
        ];

        foreach ($sharedFiles as $file) {
            if (File::exists($file)) {
                $this->fixFile($file, $isDryRun);
            }
        }
    }

    private function fixFile($filePath, $isDryRun)
    {
        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return;
        }

        $originalContent = File::get($filePath);
        $content = $originalContent;
        $changes = 0;

        // Apply typography class replacements
        foreach ($this->replacements as $old => $new) {
            $pattern = '/\b' . preg_quote($old, '/') . '\b/';
            $newContent = preg_replace($pattern, $new, $content);
            if ($newContent !== $content) {
                $changes += substr_count($content, $old);
                $content = $newContent;
            }
        }

        // Apply message class replacements
        foreach ($this->messageReplacements as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $changes++;
            }
        }

        // Fix specific accessibility issues
        $content = $this->fixAccessibilityIssues($content, $changes);

        // Fix semantic message usage
        $content = $this->fixSemanticMessages($content, $changes);

        if ($content !== $originalContent) {
            $this->fixedFiles[] = [
                'file' => $filePath,
                'changes' => $changes
            ];

            $this->totalChanges += $changes;

            if (!$isDryRun) {
                File::put($filePath, $content);
                $this->info("✅ Fixed {$filePath} ({$changes} changes)");
            } else {
                $this->comment("Would fix {$filePath} ({$changes} changes)");
            }
        }
    }

    private function fixAccessibilityIssues($content, &$changes)
    {
        // Fix contrast issues for success and warning colors
        $accessibilityFixes = [
            // Use darker success color for better contrast
            'text-success' => 'text-success-dark',
            // Use darker warning color for better contrast
            'text-warning' => 'text-warning-dark',
            // Ensure body-xs is not used for important content
            'text-body-xs' => 'text-body-sm',
        ];

        foreach ($accessibilityFixes as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $changes++;
            }
        }

        return $content;
    }

    private function fixSemanticMessages($content, &$changes)
    {
        // Fix error messages to use semantic classes
        $patterns = [
            // Error messages
            '/class="[^"]*text-red-[0-9]+[^"]*"/' => 'class="message-error"',
            // Success messages
            '/class="[^"]*text-green-[0-9]+[^"]*"/' => 'class="message-success"',
            // Warning messages
            '/class="[^"]*text-yellow-[0-9]+[^"]*"/' => 'class="message-warning"',
            '/class="[^"]*text-orange-[0-9]+[^"]*"/' => 'class="message-warning"',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== $content) {
                $changes++;
                $content = $newContent;
            }
        }

        return $content;
    }

    private function generateSummary()
    {
        $this->info("\n" . str_repeat('=', 60));
        $this->info('TYPOGRAPHY CONSISTENCY FIX SUMMARY');
        $this->info(str_repeat('=', 60));

        if (empty($this->fixedFiles)) {
            $this->info('No files needed fixing - typography is already consistent!');
        } else {
            $this->info("Fixed {$this->totalChanges} typography issues in " . count($this->fixedFiles) . " files:");

            foreach ($this->fixedFiles as $file) {
                $this->line("  - {$file['file']}: {$file['changes']} changes");
            }
        }

        $this->info(str_repeat('=', 60));

        if (!$this->option('dry-run') && !empty($this->fixedFiles)) {
            $this->info("\nNext steps:");
            $this->info("1. Run 'php artisan typography:audit' to verify fixes");
            $this->info("2. Run 'php artisan accessibility:audit' to check accessibility improvements");
            $this->info("3. Test the application to ensure everything works correctly");
        }
    }
}
