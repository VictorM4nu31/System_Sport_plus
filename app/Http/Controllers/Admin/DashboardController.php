<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Mostrar el dashboard principal del administrador
     */
    public function index()
    {
        // Verificar que el usuario sea administrador
        Gate::authorize('viewAny', \App\Models\Product::class);

        $stats = $this->analyticsService->getDashboardStats();

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * API endpoint para datos de gráficas (AJAX)
     */
    public function chartData(Request $request)
    {
        Gate::authorize('viewAny', \App\Models\Product::class);

        $type = $request->get('type', 'visitors');
        $stats = $this->analyticsService->getDashboardStats();

        switch ($type) {
            case 'visitors':
                return response()->json($stats['charts']['daily_visitors']);
            case 'sales':
                return response()->json($stats['charts']['daily_sales']);
            case 'products':
                return response()->json($stats['charts']['top_products']);
            case 'categories':
                return response()->json($stats['charts']['sales_by_category']);
            case 'sources':
                return response()->json($stats['charts']['visitor_sources']);
            case 'devices':
                return response()->json($stats['charts']['device_breakdown']);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }
}
