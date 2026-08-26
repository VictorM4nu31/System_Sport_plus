<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TypographyAudit extends Command
{
    protected $signature = 'typography:audit {--output=console : Output format (console|json|html)}';
    protected $description = 'Audit all views for typography consistency';

    private $auditResults = [];
    private $expectedTypographyClasses = [
        'display' => ['text-display-lg', 'text-display-md', 'text-display-sm'],
        'heading' => ['text-heading-xl', 'text-heading-lg', 'text-heading-md', 'text-heading-sm'],
        'body' => ['text-body-lg', 'text-body-md', 'text-body-sm', 'text-body-xs'],
        'colors' => ['text-primary', 'text-primary-light', 'text-primary-lighter', 'text-primary-lightest'],
        'semantic' => ['text-success', 'text-warning', 'text-error'],
        'weights' => ['font-light', 'font-regular', 'font-medium', 'font-semibold', 'font-bold']
    ];

    private $inconsistentPatterns = [
        // Old Tailwind size classes that should be replaced
        'text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl', 'text-3xl', 'text-4xl', 'text-5xl', 'text-6xl',
        // Old color classes that should use our system
        'text-red-', 'text-green-', 'text-yellow-', 'text-orange-', 'text-blue-', 'text-indigo-', 'text-purple-', 'text-pink-',
        // Inline styles that should use classes
        'style="font-size:', 'style="color:', 'style="font-weight:',
        // Non-standard font weights
        'font-thin', 'font-extralight', 'font-normal', 'font-extrabold', 'font-black'
    ];

    public function handle()
    {
        $this->info('Starting Typography Consistency Audit...');

        // Audit different interface types
        $this->auditAdminInterface();
        $this->auditUserInterface();
        $this->auditWorkerInterface();
        $this->auditSharedComponents();

        // Generate report
        $this->generateReport();

        return 0;
    }

    private function auditAdminInterface()
    {
        $this->info('Auditing Admin Interface...');

        $adminViews = [
            'resources/views/admin/dashboard.blade.php',
            'resources/views/admin/products/index.blade.php',
            'resources/views/admin/products/create.blade.php',
            'resources/views/admin/products/edit.blade.php',
            'resources/views/admin/products/show.blade.php',
            'resources/views/admin/workers/index.blade.php',
        ];

        foreach ($adminViews as $view) {
            if (File::exists($view)) {
                $this->auditView($view, 'admin');
            }
        }
    }

    private function auditUserInterface()
    {
        $this->info('Auditing User Interface...');

        $userViews = [
            'resources/views/usuario/dashboard.blade.php',
            'resources/views/usuario/products/index.blade.php',
            'resources/views/usuario/products/partials/product-card.blade.php',
            'resources/views/usuario/addresses/index.blade.php',
            'resources/views/usuario/addresses/create.blade.php',
            'resources/views/usuario/addresses/edit.blade.php',
        ];

        foreach ($userViews as $view) {
            if (File::exists($view)) {
                $this->auditView($view, 'user');
            }
        }
    }

    private function auditWorkerInterface()
    {
        $this->info('Auditing Worker Interface...');

        // Check if worker views exist
        $workerViewsPath = 'resources/views/worker';
        if (File::exists($workerViewsPath)) {
            $workerViews = File::allFiles($workerViewsPath);
            foreach ($workerViews as $view) {
                $this->auditView($view->getPathname(), 'worker');
            }
        } else {
            $this->auditResults['worker']['status'] = 'No worker interface found';
        }
    }

    private function auditSharedComponents()
    {
        $this->info('Auditing Shared Components...');

        $sharedViews = [
            'resources/views/layouts/app.blade.php',
            'resources/views/layouts/navigation.blade.php',
            'resources/views/components',
        ];

        foreach ($sharedViews as $view) {
            if (File::exists($view)) {
                if (File::isDirectory($view)) {
                    $files = File::allFiles($view);
                    foreach ($files as $file) {
                        $this->auditView($file->getPathname(), 'shared');
                    }
                } else {
                    $this->auditView($view, 'shared');
                }
            }
        }
    }

    private function auditView($viewPath, $interface)
    {
        if (!File::exists($viewPath)) {
            return;
        }

        $content = File::get($viewPath);
        $relativePath = str_replace(base_path() . '/', '', $viewPath);

        $issues = [];
        $goodPractices = [];

        // Check for inconsistent patterns
        foreach ($this->inconsistentPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                $issues[] = "Found inconsistent pattern: {$pattern}";
            }
        }

        // Check for proper typography class usage
        foreach ($this->expectedTypographyClasses as $category => $classes) {
            foreach ($classes as $class) {
                if (strpos($content, $class) !== false) {
                    $goodPractices[] = "Uses proper {$category} class: {$class}";
                }
            }
        }

        // Check for specific requirements based on interface type
        $this->checkInterfaceSpecificRequirements($content, $interface, $issues, $goodPractices);

        // Store results
        $this->auditResults[$interface]['views'][$relativePath] = [
            'issues' => $issues,
            'good_practices' => $goodPractices,
            'issue_count' => count($issues),
            'good_practice_count' => count($goodPractices)
        ];
    }

    private function checkInterfaceSpecificRequirements($content, $interface, &$issues, &$goodPractices)
    {
        switch ($interface) {
            case 'admin':
                // Check for dashboard titles (should be text-display-sm, font-bold, text-primary)
                if (preg_match('/dashboard|título|title/i', $content)) {
                    if (!preg_match('/text-display-sm|text-display-md/', $content)) {
                        $issues[] = 'Dashboard titles should use display typography classes';
                    }
                    if (preg_match('/text-display-sm.*text-primary|text-primary.*text-display-sm/', $content)) {
                        $goodPractices[] = 'Dashboard title uses proper display typography with primary color';
                    }
                }

                // Check for table headers
                if (preg_match('/<th|table.*header/i', $content)) {
                    if (preg_match('/text-body-md.*font-semibold/', $content)) {
                        $goodPractices[] = 'Table headers use proper typography';
                    }
                }
                break;

            case 'user':
                // Check for product names (should be text-heading-sm, font-semibold, text-primary)
                if (preg_match('/product.*name|nombre.*producto/i', $content)) {
                    if (preg_match('/text-heading-sm.*text-primary/', $content)) {
                        $goodPractices[] = 'Product names use proper heading typography';
                    }
                }

                // Check for product prices (should be text-heading-md, font-bold, text-primary)
                if (preg_match('/price|precio/i', $content)) {
                    if (preg_match('/text-heading-md.*font-bold/', $content)) {
                        $goodPractices[] = 'Product prices use proper typography';
                    }
                }
                break;

            case 'worker':
                // Check for status indicators
                if (preg_match('/status|estado/i', $content)) {
                    if (preg_match('/badge-success|badge-warning|badge-error/', $content)) {
                        $goodPractices[] = 'Status indicators use proper badge classes';
                    }
                }
                break;
        }

        // Check for semantic message usage across all interfaces
        if (preg_match('/alert|message|notification/i', $content)) {
            if (preg_match('/message-success|message-warning|message-error|alert-success|alert-warning|alert-error/', $content)) {
                $goodPractices[] = 'Uses proper semantic message classes';
            } else {
                $issues[] = 'Messages should use semantic message classes';
            }
        }
    }

    private function generateReport()
    {
        $outputFormat = $this->option('output');

        switch ($outputFormat) {
            case 'json':
                $this->generateJsonReport();
                break;
            case 'html':
                $this->generateHtmlReport();
                break;
            default:
                $this->generateConsoleReport();
                break;
        }
    }

    private function generateConsoleReport()
    {
        $this->info("\n" . str_repeat('=', 80));
        $this->info('TYPOGRAPHY CONSISTENCY AUDIT REPORT');
        $this->info(str_repeat('=', 80));

        $totalIssues = 0;
        $totalGoodPractices = 0;

        foreach ($this->auditResults as $interface => $data) {
            $this->info("\n{$interface} INTERFACE:");
            $this->info(str_repeat('-', 40));

            if (isset($data['status'])) {
                $this->warn($data['status']);
                continue;
            }

            $interfaceIssues = 0;
            $interfaceGoodPractices = 0;

            foreach ($data['views'] as $view => $results) {
                $issueCount = $results['issue_count'];
                $goodPracticeCount = $results['good_practice_count'];

                $interfaceIssues += $issueCount;
                $interfaceGoodPractices += $goodPracticeCount;

                if ($issueCount > 0) {
                    $this->error("  ❌ {$view} ({$issueCount} issues)");
                    foreach ($results['issues'] as $issue) {
                        $this->line("     - {$issue}");
                    }
                } else {
                    $this->info("  ✅ {$view} (No issues)");
                }

                if ($goodPracticeCount > 0) {
                    $this->comment("     Good practices: {$goodPracticeCount}");
                }
            }

            $totalIssues += $interfaceIssues;
            $totalGoodPractices += $interfaceGoodPractices;

            $this->info("Interface Summary: {$interfaceIssues} issues, {$interfaceGoodPractices} good practices");
        }

        $this->info("\n" . str_repeat('=', 80));
        $this->info("OVERALL SUMMARY:");
        $this->info("Total Issues: {$totalIssues}");
        $this->info("Total Good Practices: {$totalGoodPractices}");

        if ($totalIssues === 0) {
            $this->info("🎉 All views are typography consistent!");
        } else {
            $this->warn("⚠️  {$totalIssues} typography consistency issues found");
        }
        $this->info(str_repeat('=', 80));
    }

    private function generateJsonReport()
    {
        $reportPath = storage_path('app/typography-audit-report.json');
        File::put($reportPath, json_encode($this->auditResults, JSON_PRETTY_PRINT));
        $this->info("JSON report generated: {$reportPath}");
    }

    private function generateHtmlReport()
    {
        $html = $this->generateHtmlContent();
        $reportPath = storage_path('app/typography-audit-report.html');
        File::put($reportPath, $html);
        $this->info("HTML report generated: {$reportPath}");
    }

    private function generateHtmlContent()
    {
        $totalIssues = 0;
        $totalGoodPractices = 0;

        foreach ($this->auditResults as $interface => $data) {
            if (isset($data['views'])) {
                foreach ($data['views'] as $view => $results) {
                    $totalIssues += $results['issue_count'];
                    $totalGoodPractices += $results['good_practice_count'];
                }
            }
        }

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typography Consistency Audit Report</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 40px; }
        .header { background: #801336; color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .summary { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .interface { margin-bottom: 30px; }
        .interface h2 { color: #801336; border-bottom: 2px solid #801336; padding-bottom: 10px; }
        .view { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .view.has-issues { border-left: 4px solid #dc2626; }
        .view.no-issues { border-left: 4px solid #059669; }
        .issues { color: #dc2626; }
        .good-practices { color: #059669; }
        ul { margin: 10px 0; }
        li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Typography Consistency Audit Report</h1>
        <p>Generated on ' . date('Y-m-d H:i:s') . '</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <p><strong>Total Issues:</strong> ' . $totalIssues . '</p>
        <p><strong>Total Good Practices:</strong> ' . $totalGoodPractices . '</p>
    </div>';

        foreach ($this->auditResults as $interface => $data) {
            $html .= '<div class="interface">';
            $html .= '<h2>' . ucfirst($interface) . ' Interface</h2>';

            if (isset($data['status'])) {
                $html .= '<p><em>' . $data['status'] . '</em></p>';
            } else {
                foreach ($data['views'] as $view => $results) {
                    $hasIssues = $results['issue_count'] > 0;
                    $cssClass = $hasIssues ? 'has-issues' : 'no-issues';

                    $html .= '<div class="view ' . $cssClass . '">';
                    $html .= '<h3>' . $view . '</h3>';

                    if ($hasIssues) {
                        $html .= '<div class="issues">';
                        $html .= '<h4>Issues (' . $results['issue_count'] . '):</h4>';
                        $html .= '<ul>';
                        foreach ($results['issues'] as $issue) {
                            $html .= '<li>' . htmlspecialchars($issue) . '</li>';
                        }
                        $html .= '</ul></div>';
                    }

                    if ($results['good_practice_count'] > 0) {
                        $html .= '<div class="good-practices">';
                        $html .= '<h4>Good Practices (' . $results['good_practice_count'] . '):</h4>';
                        $html .= '<ul>';
                        foreach ($results['good_practices'] as $practice) {
                            $html .= '<li>' . htmlspecialchars($practice) . '</li>';
                        }
                        $html .= '</ul></div>';
                    }

                    $html .= '</div>';
                }
            }

            $html .= '</div>';
        }

        $html .= '</body></html>';

        return $html;
    }
}
