<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAnalyticsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:report {--period=week : Period for the report (day, week, month)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate analytics report for the specified period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->option('period');
        $analyticsService = app(\App\Services\AnalyticsService::class);

        $this->info("Generando reporte de analíticas para: {$period}");
        $this->newLine();

        $stats = $analyticsService->getDashboardStats();

        // Mostrar estadísticas principales
        $this->info('📊 ESTADÍSTICAS PRINCIPALES');
        $this->line('─────────────────────────────');

        switch ($period) {
            case 'day':
                $this->line("Visitantes hoy: {$stats['visitors']['today']}");
                $this->line("Visitantes únicos hoy: {$stats['visitors']['unique_today']}");
                $this->line("Ventas hoy: $" . number_format($stats['sales']['today_sales'], 2));
                $this->line("Órdenes hoy: {$stats['sales']['orders_today']}");
                break;

            case 'week':
                $this->line("Visitantes esta semana: {$stats['visitors']['this_week']}");
                $this->line("Ventas esta semana: $" . number_format($stats['sales']['this_week_sales'], 2));
                break;

            case 'month':
            default:
                $this->line("Visitantes este mes: {$stats['visitors']['this_month']}");
                $this->line("Visitantes únicos este mes: {$stats['visitors']['unique_this_month']}");
                $this->line("Ventas este mes: $" . number_format($stats['sales']['this_month_sales'], 2));
                $this->line("Órdenes este mes: {$stats['sales']['orders_this_month']}");
                break;
        }

        $this->newLine();

        // Productos
        $this->info('🏪 PRODUCTOS');
        $this->line('─────────────────────────────');
        $this->line("Total productos: {$stats['products']['total_products']}");
        $this->line("En stock: {$stats['products']['products_in_stock']}");
        $this->line("Sin stock: {$stats['products']['products_out_of_stock']}");
        $this->line("Stock bajo: {$stats['products']['low_stock_products']}");
        $this->line("Destacados: {$stats['products']['featured_products']}");

        $this->newLine();

        // Órdenes
        $this->info('📦 ÓRDENES');
        $this->line('─────────────────────────────');
        $this->line("Pendientes: {$stats['orders']['pending_orders']}");
        $this->line("En proceso: {$stats['orders']['processing_orders']}");
        $this->line("Completadas: {$stats['orders']['completed_orders']}");
        $this->line("Canceladas: {$stats['orders']['cancelled_orders']}");
        $this->line("Valor promedio: $" . number_format($stats['orders']['average_order_value'], 2));

        $this->newLine();

        // Top productos más vistos
        if (!empty($stats['products']['most_viewed_week'])) {
            $this->info('👁️ PRODUCTOS MÁS VISTOS (Esta semana)');
            $this->line('─────────────────────────────');
            foreach ($stats['products']['most_viewed_week'] as $item) {
                $productName = $item->product->name ?? 'Producto eliminado';
                $this->line("• {$productName}: {$item->views} vistas");
            }
        }

        $this->newLine();
        $this->info('✅ Reporte generado exitosamente');
    }
}
