<?php

namespace App\Services;

use App\Models\SiteAnalytics;
use App\Models\ProductView;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Registrar visita a una página
     */
    public function trackPageView(Request $request, $userId = null)
    {
        try {
            // Obtener session ID de forma segura
            $sessionId = null;
            if ($request->hasSession()) {
                try {
                    $sessionId = $request->session()->getId();
                } catch (\Exception $e) {
                    // Si no se puede obtener la sesión, continuar sin ella
                    $sessionId = null;
                }
            }

            SiteAnalytics::create([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'page_url' => $request->fullUrl(),
                'referrer' => $request->header('referer'),
                'user_id' => $userId,
                'session_id' => $sessionId,
                'device_info' => $this->getDeviceInfo($request),
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::error('Error tracking page view: ' . $e->getMessage());
        }
    }

    /**
     * Registrar vista de producto
     */
    public function trackProductView($productId, Request $request, $userId = null)
    {
        try {
            // Obtener session ID de forma segura
            $sessionId = null;
            if ($request->hasSession()) {
                try {
                    $sessionId = $request->session()->getId();
                } catch (\Exception $e) {
                    $sessionId = null;
                }
            }

            ProductView::updateOrCreate([
                'product_id' => $productId,
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
            ], [
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking product view: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function getDashboardStats()
    {
        try {
            return [
                'visitors' => $this->getVisitorStats(),
                'sales' => $this->getSalesStats(),
                'products' => $this->getProductStats(),
                'orders' => $this->getOrderStats(),
                'charts' => $this->getChartData(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats: ' . $e->getMessage());

            // Retornar datos básicos sin errores
            return [
                'visitors' => [
                    'today' => 0,
                    'yesterday' => 0,
                    'this_week' => 0,
                    'this_month' => 0,
                    'unique_today' => 0,
                    'unique_this_month' => 0,
                    'total_pages_viewed' => 0,
                ],
                'sales' => [
                    'today_sales' => 0,
                    'this_week_sales' => 0,
                    'this_month_sales' => 0,
                    'total_sales' => 0,
                    'orders_today' => 0,
                    'orders_this_month' => 0,
                ],
                'products' => [
                    'total_products' => Product::count(),
                    'products_in_stock' => Product::where('stock', '>', 0)->count(),
                    'products_out_of_stock' => Product::where('stock', '<=', 0)->count(),
                    'featured_products' => Product::where('is_featured', true)->count(),
                    'most_viewed_today' => collect(),
                    'most_viewed_week' => collect(),
                    'low_stock_products' => Product::where('stock', '<=', 10)->where('stock', '>', 0)->count(),
                ],
                'orders' => [
                    'pending_orders' => 0,
                    'processing_orders' => 0,
                    'completed_orders' => 0,
                    'cancelled_orders' => 0,
                    'average_order_value' => 0,
                ],
                'charts' => [
                    'daily_visitors' => [],
                    'daily_sales' => [],
                    'top_products' => [],
                    'sales_by_category' => [],
                    'visitor_sources' => [],
                    'device_breakdown' => [],
                ],
            ];
        }
    }

    /**
     * Estadísticas de visitantes
     */
    private function getVisitorStats()
    {
        try {
            return [
                'today' => SiteAnalytics::today()->count(),
                'yesterday' => SiteAnalytics::whereDate('created_at', Carbon::yesterday())->count(),
                'this_week' => SiteAnalytics::thisWeek()->count(),
                'this_month' => SiteAnalytics::thisMonth()->count(),
                'unique_today' => SiteAnalytics::today()->distinct('ip_address')->count(),
                'unique_this_month' => SiteAnalytics::thisMonth()->distinct('ip_address')->count(),
                'total_pages_viewed' => SiteAnalytics::count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting visitor stats: ' . $e->getMessage());
            return [
                'today' => 0,
                'yesterday' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'unique_today' => 0,
                'unique_this_month' => 0,
                'total_pages_viewed' => 0,
            ];
        }
    }

    /**
     * Estadísticas de ventas
     */
    private function getSalesStats()
    {
        try {
            return [
                'today_sales' => Order::whereDate('created_at', today())
                                     ->where('status', 'completado')
                                     ->sum('total_price') ?? 0,
                'this_week_sales' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                         ->where('status', 'completado')
                                         ->sum('total_price') ?? 0,
                'this_month_sales' => Order::whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->where('status', 'completado')
                                          ->sum('total_price') ?? 0,
                'total_sales' => Order::where('status', 'completado')->sum('total_price') ?? 0,
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'orders_this_month' => Order::whereMonth('created_at', now()->month)
                                           ->whereYear('created_at', now()->year)
                                           ->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting sales stats: ' . $e->getMessage());
            return [
                'today_sales' => 0,
                'this_week_sales' => 0,
                'this_month_sales' => 0,
                'total_sales' => 0,
                'orders_today' => 0,
                'orders_this_month' => 0,
            ];
        }
    }

    /**
     * Estadísticas de productos
     */
    private function getProductStats()
    {
        return [
            'total_products' => Product::count(),
            'products_in_stock' => Product::where('stock', '>', 0)->count(),
            'products_out_of_stock' => Product::where('stock', '<=', 0)->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'most_viewed_today' => $this->getMostViewedProducts(1),
            'most_viewed_week' => $this->getMostViewedProducts(7),
            'low_stock_products' => Product::where('stock', '<=', 10)->where('stock', '>', 0)->count(),
        ];
    }

    /**
     * Estadísticas de órdenes
     */
    private function getOrderStats()
    {
        try {
            return [
                'pending_orders' => Order::where('status', 'pendiente')->count(),
                'processing_orders' => Order::where('status', 'en proceso')->count(),
                'completed_orders' => Order::where('status', 'completado')->count(),
                'cancelled_orders' => Order::where('status', 'cancelado')->count(),
                'average_order_value' => Order::where('status', 'completado')->avg('total_price') ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting order stats: ' . $e->getMessage());
            return [
                'pending_orders' => 0,
                'processing_orders' => 0,
                'completed_orders' => 0,
                'cancelled_orders' => 0,
                'average_order_value' => 0,
            ];
        }
    }

    /**
     * Datos para gráficas
     */
    private function getChartData()
    {
        try {
            return [
                'daily_visitors' => $this->getDailyVisitors(),
                'daily_sales' => $this->getDailySales(),
                'top_products' => $this->getTopProducts(),
                'sales_by_category' => $this->getSalesByCategory(),
                'visitor_sources' => $this->getVisitorSources(),
                'device_breakdown' => $this->getDeviceBreakdown(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting chart data: ' . $e->getMessage());
            return [
                'daily_visitors' => collect(),
                'daily_sales' => collect(),
                'top_products' => collect(),
                'sales_by_category' => collect(),
                'visitor_sources' => collect(),
                'device_breakdown' => collect(),
            ];
        }
    }

    /**
     * Visitantes diarios (últimos 30 días)
     */
    private function getDailyVisitors()
    {
        try {
            $data = SiteAnalytics::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total_visits'),
                    DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $data->isEmpty() ? collect() : $data;
        } catch (\Exception $e) {
            Log::error('Error getting daily visitors: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Ventas diarias (últimos 30 días)
     */
    private function getDailySales()
    {
        try {
            $data = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_price) as total_sales'),
                    DB::raw('COUNT(*) as total_orders')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->where('status', 'completado')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $data->isEmpty() ? collect() : $data;
        } catch (\Exception $e) {
            Log::error('Error getting daily sales: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Productos más vendidos
     */
    private function getTopProducts()
    {
        try {
            $data = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completado')
                ->select(
                    'products.name',
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_sold', 'desc')
                ->limit(10)
                ->get();

            return $data->isEmpty() ? collect() : $data;
        } catch (\Exception $e) {
            Log::error('Error getting top products: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Ventas por categoría
     */
    private function getSalesByCategory()
    {
        try {
            $data = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completado')
                ->select(
                    'categories.name',
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_sales'),
                    DB::raw('SUM(order_items.quantity) as total_quantity')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('total_sales', 'desc')
                ->get();

            return $data->isEmpty() ? collect() : $data;
        } catch (\Exception $e) {
            Log::error('Error getting sales by category: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Fuentes de visitantes
     */
    private function getVisitorSources()
    {
        try {
            $data = SiteAnalytics::select(
                    DB::raw('CASE
                        WHEN referrer IS NULL OR referrer = "" THEN "Directo"
                        WHEN referrer LIKE "%google%" THEN "Google"
                        WHEN referrer LIKE "%facebook%" THEN "Facebook"
                        WHEN referrer LIKE "%instagram%" THEN "Instagram"
                        WHEN referrer LIKE "%twitter%" THEN "Twitter"
                        ELSE "Otros"
                    END as source'),
                    DB::raw('COUNT(*) as visits')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('source')
                ->orderBy('visits', 'desc')
                ->get();

            return $data->isEmpty() ? collect() : $data;
        } catch (\Exception $e) {
            Log::error('Error getting visitor sources: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Desglose por dispositivos
     */
    private function getDeviceBreakdown()
    {
        try {
            $data = SiteAnalytics::select(
                    DB::raw('CASE
                        WHEN user_agent LIKE "%Mobile%" THEN "Móvil"
                        WHEN user_agent LIKE "%Tablet%" THEN "Tablet"
                        ELSE "Escritorio"
                    END as device_type'),
                    DB::raw('COUNT(*) as visits')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('device_type')
                ->get();

            return $data->isEmpty() ? collect() : $data;
        } catch (\Exception $e) {
            Log::error('Error getting device breakdown: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Productos más vistos
     */
    private function getMostViewedProducts($days = 7)
    {
        return ProductView::with('product')
            ->select('product_id', DB::raw('COUNT(*) as views'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('product_id')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Obtener información del dispositivo
     */
    private function getDeviceInfo(Request $request)
    {
        $userAgent = $request->userAgent();

        return [
            'is_mobile' => $request->header('sec-ch-ua-mobile') === '?1',
            'platform' => $this->getPlatform($userAgent),
            'browser' => $this->getBrowser($userAgent),
        ];
    }

    /**
     * Detectar plataforma
     */
    private function getPlatform($userAgent)
    {
        if (stripos($userAgent, 'windows') !== false) return 'Windows';
        if (stripos($userAgent, 'mac') !== false) return 'Mac';
        if (stripos($userAgent, 'linux') !== false) return 'Linux';
        if (stripos($userAgent, 'android') !== false) return 'Android';
        if (stripos($userAgent, 'iphone') !== false) return 'iPhone';
        if (stripos($userAgent, 'ipad') !== false) return 'iPad';

        return 'Unknown';
    }

    /**
     * Detectar navegador
     */
    private function getBrowser($userAgent)
    {
        if (stripos($userAgent, 'chrome') !== false) return 'Chrome';
        if (stripos($userAgent, 'firefox') !== false) return 'Firefox';
        if (stripos($userAgent, 'safari') !== false) return 'Safari';
        if (stripos($userAgent, 'edge') !== false) return 'Edge';
        if (stripos($userAgent, 'opera') !== false) return 'Opera';

        return 'Unknown';
    }
}
