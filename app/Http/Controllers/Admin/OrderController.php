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
        // Mostrar pedidos pagados que están pendientes de aceptación por el trabajador
        // Excluir pedidos ya rechazados o confirmados
        $orders = Order::whereIn('status', ['paid', 'pendiente'])
                      ->where('payment_status', 'paid')
                      ->with('user', 'orderItems.product')
                      ->orderBy('created_at', 'desc')
                      ->get();
        return view('trabajador.orders.index', compact('orders'));
    }

    public function workerShow($id)
    {
        $order = Order::with(['user', 'orderItems.product', 'shippingAddress'])
                     ->findOrFail($id);

        // Verificar que el pedido esté en estado válido para el trabajador
        if (!in_array($order->status, ['paid', 'pendiente']) || $order->payment_status !== 'paid') {
            return redirect()->route('trabajador.orders.index')
                           ->with('error', 'Este pedido no está disponible para revisión.');
        }

        Log::channel('audit')->info('Worker viewed order details', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'action' => 'trabajador.orders.show',
            'timestamp' => now(),
        ]);

        return view('trabajador.orders.show', compact('order'));
    }

    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);

        // Verificar que el pedido esté pagado y pendiente de aceptación
        if (!in_array($order->status, ['paid', 'pendiente']) || $order->payment_status !== 'paid') {
            return redirect()->route('trabajador.orders.index')
                           ->with('error', 'Este pedido no puede ser aceptado.');
        }

        $order->update(['status' => 'confirmed']); // Cambiar a confirmed cuando el trabajador acepta

        Log::channel('audit')->info('Order accepted by worker', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'action' => 'trabajador.orders.accept',
            'timestamp' => now(),
        ]);

        return redirect()->route('trabajador.orders.index')->with('success', 'Pedido aceptado con éxito.');
    }

    public function rejectOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Verificar que el pedido esté pagado y pendiente de aceptación
        if (!in_array($order->status, ['paid', 'pendiente']) || $order->payment_status !== 'paid') {
            return redirect()->route('trabajador.orders.index')
                           ->with('error', 'Este pedido no puede ser rechazado.');
        }

        // Validar que se proporcione una razón de rechazo
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ], [
            'rejection_reason.required' => 'Debe proporcionar una razón para el rechazo.',
            'rejection_reason.min' => 'La razón debe tener al menos 10 caracteres.',
            'rejection_reason.max' => 'La razón no puede exceder 500 caracteres.'
        ]);

        $order->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => Auth::id()
        ]);

        Log::channel('audit')->info('Order rejected by worker', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'rejection_reason' => $request->rejection_reason,
            'action' => 'trabajador.orders.reject',
            'timestamp' => now(),
        ]);

        return redirect()->route('trabajador.orders.index')->with('success', 'Pedido rechazado correctamente.');
    }
}
