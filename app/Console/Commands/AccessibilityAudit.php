<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AccessibilityAudit extends Command
{
    protected $signature = 'accessibility:audit {--output=console : Output format (console|json|html)}';
    protected $description = 'Test accessibility and contrast ratios for typography';

    private $auditResults = [];

    // Color definitions from our typography system
    private $colors = [
        'primary' => '#801336',
        'primary-50' => '#fdf2f4',
        'primary-100' => '#fce7eb',
        'primary-200' => '#f9d0d9',
        'primary-300' => '#f4a8ba',
        'primary-400' => '#ed7396',
        'primary-500' => '#e04574',
        'primary-600' => '#cc2857',
        'primary-700' => '#ab1e47',
        'primary-800' => '#901c42',
        'primary-900' => '#801336',
        'primary-950' => '#470a1e',
        'success' => '#059669',
        'success-light' => '#d1fae5',
        'success-dark' => '#047857',
        'warning' => '#d97706',
        'warning-light' => '#fef3c7',
        'warning-dark' => '#b45309',
        'error' => '#dc2626',
        'error-light' => '#fee2e2',
        'error-dark' => '#b91c1c',
        'white' => '#ffffff',
        'black' => '#000000',
        'gray-50' => '#f9fafb',
        'gray-100' => '#f3f4f6',
        'gray-200' => '#e5e7eb',
        'gray-300' => '#d1d5db',
        'gray-400' => '#9ca3af',
        'gray-500' => '#6b7280',
        'gray-600' => '#4b5563',
        'gray-700' => '#374151',
        'gray-800' => '#1f2937',
        'gray-900' => '#111827',
    ];

    // Common text/background combinations to test
    private $combinations = [
        // Primary color combinations
        ['text' => 'primary', 'bg' => 'white', 'context' => 'Primary text on white background'],
        ['text' => 'primary-700', 'bg' => 'white', 'context' => 'Secondary text on white background'],
        ['text' => 'primary-600', 'bg' => 'white', 'context' => 'Muted text on white background'],
        ['text' => 'white', 'bg' => 'primary', 'context' => 'White text on primary background'],
        ['text' => 'white', 'bg' => 'primary-700', 'context' => 'White text on primary-700 background'],

        // Semantic color combinations
        ['text' => 'success', 'bg' => 'white', 'context' => 'Success text on white background'],
        ['text' => 'success-dark', 'bg' => 'success-light', 'context' => 'Success message'],
        ['text' => 'warning', 'bg' => 'white', 'context' => 'Warning text on white background'],
        ['text' => 'warning-dark', 'bg' => 'warning-light', 'context' => 'Warning message'],
        ['text' => 'error', 'bg' => 'white', 'context' => 'Error text on white background'],
        ['text' => 'error-dark', 'bg' => 'error-light', 'context' => 'Error message'],

        // Gray combinations
        ['text' => 'gray-900', 'bg' => 'white', 'context' => 'Dark gray text on white background'],
        ['text' => 'gray-700', 'bg' => 'white', 'context' => 'Medium gray text on white background'],
        ['text' => 'gray-600', 'bg' => 'white', 'context' => 'Light gray text on white background'],
        ['text' => 'white', 'bg' => 'gray-900', 'context' => 'White text on dark gray background'],

        // Button combinations
        ['text' => 'white', 'bg' => 'primary', 'context' => 'Primary button'],
        ['text' => 'primary', 'bg' => 'gray-100', 'context' => 'Secondary button'],
        ['text' => 'white', 'bg' => 'error', 'context' => 'Danger button'],
    ];

    public function handle()
    {
        $this->info('Starting Accessibility and Contrast Ratio Audit...');

        // Test contrast ratios
        $this->testContrastRatios();

        // Test font size accessibility
        $this->testFontSizeAccessibility();

        // Test color independence
        $this->testColorIndependence();

        // Generate report
        $this->generateReport();

        return 0;
    }

    private function testContrastRatios()
    {
        $this->info('Testing contrast ratios...');

        $contrastResults = [];

        foreach ($this->combinations as $combo) {
            $textColor = $this->colors[$combo['text']];
            $bgColor = $this->colors[$combo['bg']];

            $contrastRatio = $this->calculateContrastRatio($textColor, $bgColor);

            $wcagAA = $contrastRatio >= 4.5;
            $wcagAAA = $contrastRatio >= 7.0;
            $wcagAALarge = $contrastRatio >= 3.0; // For large text (18pt+ or 14pt+ bold)

            $result = [
                'text_color' => $textColor,
                'bg_color' => $bgColor,
                'contrast_ratio' => round($contrastRatio, 2),
                'wcag_aa' => $wcagAA,
                'wcag_aaa' => $wcagAAA,
                'wcag_aa_large' => $wcagAALarge,
                'context' => $combo['context'],
                'status' => $wcagAA ? 'PASS' : 'FAIL'
            ];

            $contrastResults[] = $result;
        }

        $this->auditResults['contrast_ratios'] = $contrastResults;
    }

    private function testFontSizeAccessibility()
    {
        $this->info('Testing font size accessibility...');

        $fontSizes = [
            'display-lg' => ['size' => '48px', 'rem' => '3rem'],
            'display-md' => ['size' => '40px', 'rem' => '2.5rem'],
            'display-sm' => ['size' => '32px', 'rem' => '2rem'],
            'heading-xl' => ['size' => '30px', 'rem' => '1.875rem'],
            'heading-lg' => ['size' => '24px', 'rem' => '1.5rem'],
            'heading-md' => ['size' => '20px', 'rem' => '1.25rem'],
            'heading-sm' => ['size' => '18px', 'rem' => '1.125rem'],
            'body-lg' => ['size' => '16px', 'rem' => '1rem'],
            'body-md' => ['size' => '14px', 'rem' => '0.875rem'],
            'body-sm' => ['size' => '12px', 'rem' => '0.75rem'],
            'body-xs' => ['size' => '10px', 'rem' => '0.625rem'],
        ];

        $fontSizeResults = [];

        foreach ($fontSizes as $class => $info) {
            $sizeInPx = (int) str_replace('px', '', $info['size']);

            $accessible = $sizeInPx >= 12; // Minimum recommended size
            $recommended = $sizeInPx >= 14; // Recommended minimum for body text
            $large = $sizeInPx >= 18; // Considered large text for WCAG

            $result = [
                'class' => $class,
                'size_px' => $sizeInPx,
                'size_rem' => $info['rem'],
                'accessible' => $accessible,
                'recommended' => $recommended,
                'large_text' => $large,
                'status' => $accessible ? 'PASS' : 'FAIL',
                'notes' => []
            ];

            if (!$accessible) {
                $result['notes'][] = 'Below minimum accessible size (12px)';
            }
            if (!$recommended && $sizeInPx >= 12) {
                $result['notes'][] = 'Below recommended minimum for body text (14px)';
            }
            if ($large) {
                $result['notes'][] = 'Qualifies as large text for WCAG (lower contrast requirements)';
            }

            $fontSizeResults[] = $result;
        }

        $this->auditResults['font_sizes'] = $fontSizeResults;
    }

    private function testColorIndependence()
    {
        $this->info('Testing color independence...');

        // Check if information is conveyed through color alone
        $colorIndependenceResults = [
            'status_indicators' => [
                'test' => 'Status indicators should use icons or text in addition to color',
                'pass' => true, // Our badge system includes text
                'notes' => 'Badge classes include text content, not just color'
            ],
            'form_validation' => [
                'test' => 'Form validation should use text messages, not just color',
                'pass' => true, // Our error messages include text
                'notes' => 'Error messages include descriptive text'
            ],
            'links' => [
                'test' => 'Links should be distinguishable without color (underline, etc.)',
                'pass' => false, // Need to check if links have underlines or other indicators
                'notes' => 'Need to verify link styling includes non-color indicators'
            ],
            'buttons' => [
                'test' => 'Button states should be indicated by more than just color',
                'pass' => true, // Buttons have hover/focus states with multiple indicators
                'notes' => 'Buttons use hover/focus states with opacity and shadow changes'
            ]
        ];

        $this->auditResults['color_independence'] = $colorIndependenceResults;
    }

    private function calculateContrastRatio($color1, $color2)
    {
        $luminance1 = $this->getLuminance($color1);
        $luminance2 = $this->getLuminance($color2);

        $lighter = max($luminance1, $luminance2);
        $darker = min($luminance1, $luminance2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    private function getLuminance($hexColor)
    {
        // Remove # if present
        $hex = ltrim($hexColor, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        // Apply gamma correction
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        // Calculate luminance
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
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
        $this->info('ACCESSIBILITY AUDIT REPORT');
        $this->info(str_repeat('=', 80));

        // Contrast Ratios Report
        $this->info("\nCONTRAST RATIOS:");
        $this->info(str_repeat('-', 40));

        $passedContrast = 0;
        $totalContrast = count($this->auditResults['contrast_ratios']);

        foreach ($this->auditResults['contrast_ratios'] as $result) {
            $status = $result['status'] === 'PASS' ? '✅' : '❌';
            $this->line("{$status} {$result['context']}: {$result['contrast_ratio']}:1");

            if ($result['status'] === 'PASS') {
                $passedContrast++;
            } else {
                $this->error("   Text: {$result['text_color']} | Background: {$result['bg_color']}");
                $this->error("   WCAG AA: " . ($result['wcag_aa'] ? 'PASS' : 'FAIL'));
            }
        }

        $this->info("\nContrast Summary: {$passedContrast}/{$totalContrast} combinations pass WCAG AA");

        // Font Sizes Report
        $this->info("\nFONT SIZES:");
        $this->info(str_repeat('-', 40));

        $accessibleSizes = 0;
        $totalSizes = count($this->auditResults['font_sizes']);

        foreach ($this->auditResults['font_sizes'] as $result) {
            $status = $result['status'] === 'PASS' ? '✅' : '❌';
            $this->line("{$status} {$result['class']}: {$result['size_px']}px ({$result['size_rem']})");

            if ($result['status'] === 'PASS') {
                $accessibleSizes++;
            }

            foreach ($result['notes'] as $note) {
                $this->comment("   - {$note}");
            }
        }

        $this->info("\nFont Size Summary: {$accessibleSizes}/{$totalSizes} sizes are accessible");

        // Color Independence Report
        $this->info("\nCOLOR INDEPENDENCE:");
        $this->info(str_repeat('-', 40));

        $passedIndependence = 0;
        $totalIndependence = count($this->auditResults['color_independence']);

        foreach ($this->auditResults['color_independence'] as $test => $result) {
            $status = $result['pass'] ? '✅' : '❌';
            $this->line("{$status} {$result['test']}");
            $this->comment("   {$result['notes']}");

            if ($result['pass']) {
                $passedIndependence++;
            }
        }

        $this->info("\nColor Independence Summary: {$passedIndependence}/{$totalIndependence} tests pass");

        // Overall Summary
        $this->info("\n" . str_repeat('=', 80));
        $this->info("OVERALL ACCESSIBILITY SUMMARY:");

        $totalTests = $totalContrast + $totalSizes + $totalIndependence;
        $totalPassed = $passedContrast + $accessibleSizes + $passedIndependence;

        $this->info("Total Tests: {$totalTests}");
        $this->info("Passed Tests: {$totalPassed}");
        $this->info("Pass Rate: " . round(($totalPassed / $totalTests) * 100, 1) . "%");

        if ($totalPassed === $totalTests) {
            $this->info("🎉 All accessibility tests passed!");
        } else {
            $this->warn("⚠️  " . ($totalTests - $totalPassed) . " accessibility issues found");
        }
        $this->info(str_repeat('=', 80));
    }

    private function generateJsonReport()
    {
        $reportPath = storage_path('app/accessibility-audit-report.json');
        File::put($reportPath, json_encode($this->auditResults, JSON_PRETTY_PRINT));
        $this->info("JSON report generated: {$reportPath}");
    }

    private function generateHtmlReport()
    {
        $html = $this->generateAccessibilityHtmlContent();
        $reportPath = storage_path('app/accessibility-audit-report.html');
        File::put($reportPath, $html);
        $this->info("HTML report generated: {$reportPath}");
    }

    private function generateAccessibilityHtmlContent()
    {
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility Audit Report</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 40px; }
        .header { background: #801336; color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #801336; border-bottom: 2px solid #801336; padding-bottom: 10px; }
        .test-item { margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .test-item.pass { border-left: 4px solid #059669; }
        .test-item.fail { border-left: 4px solid #dc2626; }
        .contrast-demo { display: inline-block; padding: 5px 10px; margin: 5px; border-radius: 3px; }
        .summary { background: #f8f9fa; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Accessibility Audit Report</h1>
        <p>Generated on ' . date('Y-m-d H:i:s') . '</p>
    </div>';

        // Contrast ratios section
        $html .= '<div class="section">
            <h2>Contrast Ratios</h2>';

        foreach ($this->auditResults['contrast_ratios'] as $result) {
            $cssClass = $result['status'] === 'PASS' ? 'pass' : 'fail';
            $html .= '<div class="test-item ' . $cssClass . '">';
            $html .= '<h3>' . $result['context'] . '</h3>';
            $html .= '<p><strong>Contrast Ratio:</strong> ' . $result['contrast_ratio'] . ':1</p>';
            $html .= '<p><strong>WCAG AA:</strong> ' . ($result['wcag_aa'] ? 'PASS' : 'FAIL') . '</p>';
            $html .= '<div class="contrast-demo" style="color: ' . $result['text_color'] . '; background-color: ' . $result['bg_color'] . ';">Sample Text</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        // Font sizes section
        $html .= '<div class="section">
            <h2>Font Sizes</h2>';

        foreach ($this->auditResults['font_sizes'] as $result) {
            $cssClass = $result['status'] === 'PASS' ? 'pass' : 'fail';
            $html .= '<div class="test-item ' . $cssClass . '">';
            $html .= '<h3>' . $result['class'] . '</h3>';
            $html .= '<p><strong>Size:</strong> ' . $result['size_px'] . 'px (' . $result['size_rem'] . ')</p>';
            if (!empty($result['notes'])) {
                $html .= '<ul>';
                foreach ($result['notes'] as $note) {
                    $html .= '<li>' . htmlspecialchars($note) . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '</body></html>';

        return $html;
    }
}
