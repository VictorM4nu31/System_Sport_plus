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

        $orders = Order::with('user', 'address')->orderBy('created_at', 'desc')->paginate(15);

        Log::channel('audit')->info('Admin viewed orders list', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'orders_count' => $orders->total(),
            'action' => 'admin.pedidos.index',
            'timestamp' => now(),
        ]);

        return view('admin.orders.index', compact('orders'));
    }

    // Mostrar detalles de un pedido
    public function show(Order $pedido)
    {
        Gate::authorize('view', $pedido);

        Log::channel('audit')->info('Admin viewed order details', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $pedido->id,
            'order_user_id' => $pedido->user_id,
            'action' => 'admin.pedidos.show',
            'timestamp' => now(),
        ]);

        return view('admin.orders.show', ['order' => $pedido]);
    }

    // Actualizar el estado de un pedido
    public function updateStatus(Request $request, Order $pedido)
    {
        Gate::authorize('updateStatus', $pedido);

        ValidationService::validateRequest($request, ValidationService::orderStatusRules());

        $oldStatus = $pedido->status;
        $pedido->update(['status' => $request->status]);

        Log::channel('audit')->info('Order status updated by admin', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $pedido->id,
            'order_user_id' => $pedido->user_id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'action' => 'admin.pedidos.actualizar-estado',
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.pedidos.index')->with('success', 'Estado del pedido actualizado.');
    }

    // Eliminar un pedido
    public function destroy(Order $pedido)
    {
        Gate::authorize('delete', $pedido);

        $orderData = $pedido->toArray();
        $orderId = $pedido->id;
        $pedido->delete();

        Log::channel('audit')->info('Order deleted by admin', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $orderId,
            'order_data' => $orderData,
            'action' => 'admin.pedidos.destroy',
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.pedidos.index')->with('success', 'Pedido eliminado.');
    }

    public function workerIndex()
    {
        Gate::authorize('viewAny', Order::class);

        // Mostrar pedidos pagados que están pendientes de aceptación por el trabajador
        // Excluir pedidos ya rechazados o confirmados
        $orders = Order::whereIn('status', ['paid', 'pendiente'])
            ->where('payment_status', 'paid')
            ->with('user', 'orderItems.product')
            ->orderBy('created_at', 'desc')
            ->get();

        // Segunda columna del kanban: aceptados hoy (contexto, sin paginación pesada).
        $acceptedToday = Order::where('status', 'confirmed')
            ->whereDate('updated_at', today())
            ->with('user', 'orderItems.product')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('trabajador.orders.index', compact('orders', 'acceptedToday'));
    }

    /**
     * Búsqueda rápida de pedidos para la palette del trabajador (JSON).
     */
    public function buscarPedidos(Request $request)
    {
        Gate::authorize('viewAny', Order::class);

        $query = trim((string) $request->query('q', ''));

        $orders = Order::with('user')
            ->when($query !== '', function ($q) use ($query) {
                if (ctype_digit($query)) {
                    $q->where('id', (int) $query);
                } else {
                    $q->whereHas('user', fn ($u) => $u->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($query).'%']));
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(fn (Order $order): array => [
                'id' => $order->id,
                'cliente' => $order->user?->name ?? '—',
                'total' => (float) $order->total_price,
                'formatted_total' => '$'.number_format((float) $order->total_price, 2),
                'status' => $order->status,
                'created_at' => $order->created_at?->format('d/m H:i'),
                'url' => route('trabajador.pedidos.ver', $order->id),
            ]);

        return response()->json(['data' => $orders]);
    }

    public function workerShow(Order $pedido)
    {
        $pedido->load(['user', 'orderItems.product', 'shippingAddress']);
        Gate::authorize('updateStatus', $pedido);

        // Verificar que el pedido esté en estado válido para el trabajador
        if (! in_array($pedido->status, ['paid', 'pendiente']) || $pedido->payment_status !== 'paid') {
            return redirect()->route('trabajador.pedidos.indice')
                ->with('error', 'Este pedido no está disponible para revisión.');
        }

        Log::channel('audit')->info('Worker viewed order details', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $pedido->id,
            'order_user_id' => $pedido->user_id,
            'action' => 'trabajador.pedidos.ver',
            'timestamp' => now(),
        ]);

        return view('trabajador.orders.show', ['order' => $pedido]);
    }

    public function acceptOrder(Order $pedido)
    {
        Gate::authorize('updateStatus', $pedido);

        // Verificar que el pedido esté pagado y pendiente de aceptación
        if (! in_array($pedido->status, ['paid', 'pendiente']) || $pedido->payment_status !== 'paid') {
            return redirect()->route('trabajador.pedidos.indice')
                ->with('error', 'Este pedido no puede ser aceptado.');
        }

        $pedido->update(['status' => 'confirmed']); // Cambiar a confirmed cuando el trabajador acepta

        Log::channel('audit')->info('Order accepted by worker', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $pedido->id,
            'order_user_id' => $pedido->user_id,
            'action' => 'trabajador.pedidos.aceptar',
            'timestamp' => now(),
        ]);

        // El kanban opera con fetch + UI optimista: responder JSON sin cambiar la lógica.
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Pedido aceptado con éxito.',
                'order_id' => $pedido->id,
                'status' => $pedido->status,
            ]);
        }

        return redirect()->route('trabajador.pedidos.indice')->with('success', 'Pedido aceptado con éxito.');
    }

    public function rejectOrder(Request $request, Order $pedido)
    {
        Gate::authorize('updateStatus', $pedido);

        // Verificar que el pedido esté pagado y pendiente de aceptación
        if (! in_array($pedido->status, ['paid', 'pendiente']) || $pedido->payment_status !== 'paid') {
            return redirect()->route('trabajador.pedidos.indice')
                ->with('error', 'Este pedido no puede ser rechazado.');
        }

        // Validar que se proporcione una razón de rechazo
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ], [
            'rejection_reason.required' => 'Debe proporcionar una razón para el rechazo.',
            'rejection_reason.min' => 'La razón debe tener al menos 10 caracteres.',
            'rejection_reason.max' => 'La razón no puede exceder 500 caracteres.',
        ]);

        $pedido->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
        ]);

        Log::channel('audit')->info('Order rejected by worker', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $pedido->id,
            'order_user_id' => $pedido->user_id,
            'rejection_reason' => $request->rejection_reason,
            'action' => 'trabajador.pedidos.rechazar',
            'timestamp' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Pedido rechazado correctamente.',
                'order_id' => $pedido->id,
                'status' => $pedido->status,
            ]);
        }

        return redirect()->route('trabajador.pedidos.indice')->with('success', 'Pedido rechazado correctamente.');
    }
}
