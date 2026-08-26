<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Contracts\OrderProcessingInterface;
use App\Services\ValidationService;
use App\Services\ErrorHandlingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderProcessingService;

    public function __construct(OrderProcessingInterface $orderProcessingService)
    {
        // Middleware is handled by routes in Laravel 11
        $this->orderProcessingService = $orderProcessingService;
    }
    public function dashboard()
    {
        $address = Auth::user()->defaultAddress; // Obtener la dirección por defecto del usuario autenticado
        $addresses = Auth::user()->addresses; // Obtener todas las direcciones del usuario
        return view('usuario.dashboard', compact('address', 'addresses'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (count($cart) == 0) {
            return response()->json(['error' => 'El carrito está vacío.'], 400);
        }

        // Validate that user has at least one address
        if (!Auth::user()->addresses()->exists()) {
            return response()->json(['error' => 'Debes agregar una dirección de envío antes de realizar un pedido.'], 400);
        }

        try {
            $paymentData = [
                'payment_status' => 'pagado',
                'shipping_address' => $request->shipping_address ?? null, // Mantener para compatibilidad
                'shipping_address_id' => $request->shipping_address_id ?? null,
                'payment_intent_id' => $request->payment_intent_id ?? null,
                'notes' => $request->notes ?? null,
            ];

            // Use the unified order processing service with atomic transactions
            $order = $this->orderProcessingService->processOrder(
                $cart,
                Auth::user(),
                $paymentData
            );

            // Clear cart after successful order creation
            session()->forget('cart');

            Log::channel('audit')->info('Order created', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'order_id' => $order->id,
                'total_price' => $order->total_price,
                'payment_status' => $order->payment_status,
                'action' => 'orders.store',
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => 'Pedido creado con éxito.',
                'order_id' => $order->id
            ], 200);

        } catch (\InvalidArgumentException $e) {
            $errorResponse = ErrorHandlingService::handleOrderError(
                $e,
                ['cart' => $cart, 'payment_data' => $paymentData ?? []],
                'Error en los datos del pedido: ' . $e->getMessage()
            );

            return ErrorHandlingService::jsonErrorResponse($errorResponse['error'], 400);

        } catch (\Exception $e) {
            $errorResponse = ErrorHandlingService::handleOrderError(
                $e,
                ['cart' => $cart, 'payment_data' => $paymentData ?? []],
                'Error al procesar el pedido. Por favor, inténtalo de nuevo.'
            );

            return ErrorHandlingService::jsonErrorResponse($errorResponse['error'], 500);
        }
    }

    // Mostrar los pedidos del usuario
    public function index()
    {
        Gate::authorize('viewAny', Order::class);

        $orders = Auth::user()->orders; // Obtener los pedidos del usuario autenticado
        return view('usuario.orders.index', compact('orders'));
    }

    // Mostrar los detalles de un pedido específico
    public function show($id)
    {
        $order = Order::findOrFail($id);
        Gate::authorize('view', $order);

        Log::channel('audit')->info('Order viewed', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'order_id' => $order->id,
            'action' => 'orders.show',
            'timestamp' => now(),
        ]);

        return view('usuario.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Validar el estado del pedido
        ValidationService::validateRequest($request, ValidationService::orderStatusRules());

        // Encontrar el pedido y actualizar su estado
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Estado del pedido actualizado con éxito.');
    }

    /**
     * Cancel an order and restore stock atomically
     */
    public function cancel(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Verify that the order belongs to the user
            if ($order->user_id !== Auth::id()) {
                return response()->json(['error' => 'No tienes acceso a este pedido.'], 403);
            }

            // Only allow cancellation of pending orders
            if ($order->status !== 'pendiente') {
                return response()->json(['error' => 'Solo se pueden cancelar pedidos pendientes.'], 400);
            }

            // Use atomic transaction to cancel order and restore stock
            DB::transaction(function () use ($order) {
                // Restore stock
                $this->orderProcessingService->releaseStock($order);

                // Update order status
                $order->status = 'cancelado';
                $order->save();
            });

            Log::channel('audit')->info('Order cancelled', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'order_id' => $order->id,
                'total_price' => $order->total_price,
                'action' => 'orders.cancel',
                'timestamp' => now(),
            ]);

            return response()->json(['success' => 'Pedido cancelado con éxito.'], 200);

        } catch (\Exception $e) {
            $errorResponse = ErrorHandlingService::handleOrderError(
                $e,
                ['order_id' => $id, 'action' => 'cancel'],
                'Error al cancelar el pedido.'
            );

            return ErrorHandlingService::jsonErrorResponse($errorResponse['error'], 500);
        }
    }
}
