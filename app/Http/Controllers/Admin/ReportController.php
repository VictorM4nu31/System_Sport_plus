<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class ReportController extends Controller
{
    public function salesReport()
{
    // Obtener los datos de ventas
    $sales = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as total_sales')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();

    return view('admin.reports.sales', compact('sales'));
}

}
