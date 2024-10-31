<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Mostrar el reporte de ventas
    public function salesReport(Request $request)
    {
        // Obtener el rango de fechas desde el formulario o usar el último mes por defecto
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // Obtener las ventas agrupadas por fecha dentro del rango seleccionado
        $sales = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as total_sales')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Obtener los productos más vendidos dentro del rango seleccionado
        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->take(5) // Limitar a los 5 productos más vendidos
            ->with('product') // Traer los detalles del producto
            ->get();

        return view('admin.reports.sales', compact('sales', 'topProducts', 'startDate', 'endDate'));
    }

    public function workerSalesReport()
    {
        // Genera el reporte de ventas, por ejemplo, con los pedidos completados
        $completedOrders = Order::where('status', 'completado')->get();
        return view('trabajador.reports.sales', compact('completedOrders'));
    }
}
