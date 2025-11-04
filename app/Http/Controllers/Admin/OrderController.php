<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by routes in Laravel 11
    }

    // Mostrar todos los pedidos
    public function index()
    {
        Gate::authorize('viewAny', Order::class);

        $orders = Order::with('user', 'address')->get();

        Log::channel('audit')->info('Admin viewed orders list', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'orders_count' => $orders->count(),
            'action' => 'admin.orders.index',
            'timestamp' => now(),
        ]);

        return view('admin.orders.index', compact('orders'));
    }

    // Mostrar detalles de un pedido
    public function show($id)
    {
        $order = Order::findOrFail($id);
        Gate::authorize('view', $order);

        Log::channel('audit')->info('Admin viewed order details', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'action' => 'admin.orders.show',
            'timestamp' => now(),
        ]);

        return view('admin.orders.show', compact('order'));
    }

    // Actualizar el estado de un pedido
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        Gate::authorize('updateStatus', $order);

        ValidationService::validateRequest($request, ValidationService::orderStatusRules());

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        Log::channel('audit')->info('Order status updated by admin', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'action' => 'admin.orders.updateStatus',
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Estado del pedido actualizado.');
    }

    // Eliminar un pedido
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        Gate::authorize('delete', $order);

        $orderData = $order->toArray();
        $order->delete();

        Log::channel('audit')->info('Order deleted by admin', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $id,
            'order_data' => $orderData,
            'action' => 'admin.orders.destroy',
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Pedido eliminado.');
    }

    public function workerIndex()
    {
        $orders = Order::where('status', 'pendiente')->get(); // Solo mostrar pedidos pendientes
        return view('trabajador.orders.index', compact('orders'));
    }

    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'en proceso']); // Actualiza el estado a "en proceso" cuando el trabajador lo acepta
        return redirect()->route('trabajador.orders.index')->with('success', 'Pedido aceptado con éxito.');
    }
}
