<?php

namespace App\Http\Controllers\User;

use App\Contracts\OrderProcessingInterface;
use App\Contracts\PaymentServiceInterface;
use App\Contracts\StockManagementInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockReservation;
use App\Services\CartTotalsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $orderProcessingService;

    protected $stockService;

    protected $paymentService;

    protected $totalsService;

    public function __construct(
        OrderProcessingInterface $orderProcessingService,
        StockManagementInterface $stockService,
        PaymentServiceInterface $paymentService,
        CartTotalsService $totalsService
    ) {
        $this->orderProcessingService = $orderProcessingService;
        $this->stockService = $stockService;
        $this->paymentService = $paymentService;
        $this->totalsService = $totalsService;
    }

    // Mostrar el carrito de compras
    public function index()
    {
        $cart = session()->get('cart', []);

        // Validate stock availability for cart items
        $stockValidation = $this->validateCartStock($cart);

        // Rebuild cart with real prices from the database (never trust session prices)
        $cart = $this->rebuildCartFromDatabase($cart);
        $totals = $this->totalsService->calculate($cart);

        return view('usuario.cart.index', compact('cart', 'stockValidation', 'totals'));
    }

    // Agregar productos al carrito
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);  // Obtener la cantidad seleccionada

        // Check stock availability before adding to cart
        $availableStock = $this->stockService->getAvailableStock($id);

        $cart = session()->get('cart', []);
        $currentCartQuantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $totalRequestedQuantity = $currentCartQuantity + $quantity;

        if ($totalRequestedQuantity > $availableStock) {
            return redirect()->route('usuario.products.index')
                ->with('error', "Stock insuficiente para {$product->name}. Disponible: {$availableStock}, Solicitado: {$totalRequestedQuantity}");
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;  // Incrementar la cantidad si ya está en el carrito
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('usuario.products.index')->with('success', 'Producto agregado al carrito.');
    }

    // Eliminar productos del carrito
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('usuario.cart.index')->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * Validate stock availability for all items in cart
     */
    private function validateCartStock(array $cart): array
    {
        $stockValidation = [
            'errors' => [],
            'warnings' => [],
            'valid' => true,
        ];

        foreach ($cart as $productId => $details) {
            $availableStock = $this->stockService->getAvailableStock($productId);
            $product = Product::find($productId);

            if (! $product) {
                $stockValidation['errors'][] = "Producto con ID {$productId} no encontrado";
                $stockValidation['valid'] = false;

                continue;
            }

            if ($availableStock < $details['quantity']) {
                $stockValidation['errors'][] = "Stock insuficiente para {$product->name}. Disponible: {$availableStock}, En carrito: {$details['quantity']}";
                $stockValidation['valid'] = false;
            } elseif ($availableStock < ($details['quantity'] * 2)) {
                $stockValidation['warnings'][] = "Stock bajo para {$product->name}. Disponible: {$availableStock}";
            }
        }

        return $stockValidation;
    }

    /**
     * Rebuild the cart using prices currently stored in the database.
     *
     * Session carts may be stale (price changed) and must never be trusted when
     * computing what the customer is charged.
     *
     * @param  array  $cart  Carrito en formato [productId => ['price', 'quantity']]
     * @return array<string, array{name: string, price: float, quantity: int}>
     */
    private function rebuildCartFromDatabase(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        $rebuilt = [];

        foreach ($cart as $productId => $details) {
            $product = $products->get($productId);

            if (! $product) {
                continue;
            }

            $quantity = (int) $details['quantity'];

            if ($quantity < 1) {
                continue;
            }

            $rebuilt[$productId] = [
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => $quantity,
            ];
        }

        return $rebuilt;
    }

    /**
     * Show checkout page with address selection
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('usuario.cart.index')->with('error', 'No tienes productos en el carrito.');
        }

        // Validate stock before proceeding to checkout
        $stockValidation = $this->validateCartStock($cart);
        if (! $stockValidation['valid']) {
            return redirect()->route('usuario.cart.index')
                ->with('error', 'Algunos productos en tu carrito no tienen stock suficiente.');
        }

        // Get user addresses
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        if ($addresses->isEmpty()) {
            return redirect()->route('usuario.addresses.create')
                ->with('error', 'Debes agregar una dirección de envío antes de realizar un pedido.');
        }

        // Calculate total
        $total = $this->orderProcessingService->calculateOrderTotal($cart);

        return view('usuario.cart.checkout', compact('cart', 'addresses', 'total'));
    }

    /**
     * Create Stripe Payment Intent for cart checkout
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
        ]);

        $shippingAddressId = $request->input('shipping_address_id');

        // Verify the address belongs to the authenticated user
        $address = Auth::user()->addresses()->findOrFail($shippingAddressId);

        try {
            // Rebuild the cart from the session using real prices from the database.
            // The client-supplied `total` and `cart` are intentionally ignored so a
            // malicious user cannot tamper with the amount to be charged.
            $cart = $this->rebuildCartFromDatabase(session()->get('cart', []));

            if (empty($cart)) {
                return response()->json([
                    'message' => 'No tienes productos en el carrito.',
                ], 400);
            }

            // Validate stock availability
            $stockValidation = $this->validateCartStock($cart);
            if (! $stockValidation['valid']) {
                return response()->json([
                    'message' => 'Algunos productos no tienen stock suficiente',
                    'errors' => $stockValidation['errors'],
                ], 400);
            }

            // Reserve stock for the cart items
            foreach ($cart as $productId => $details) {
                $reserved = $this->stockService->reserveStock($productId, $details['quantity'], Auth::id());
                if (! $reserved) {
                    return response()->json([
                        'message' => "No se pudo reservar stock para el producto ID: {$productId}",
                    ], 400);
                }
            }

            // Compute the total server-side (subtotal + shipping), never from the client
            $totals = $this->totalsService->calculate($cart);
            $total = $totals['total'];

            // Create a pending order
            $orderData = [
                'user_id' => Auth::id(),
                'total_price' => $total,
                'status' => OrderStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
                'shipping_address_id' => $shippingAddressId,
                'notes' => 'Orden creada para pago con Stripe',
            ];

            $order = Order::create($orderData);

            // Create order items with prices from the database
            foreach ($cart as $productId => $details) {
                $order->orderItems()->create([
                    'product_id' => $productId,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);
            }

            // Create payment intent with Stripe
            $paymentIntent = $this->paymentService->createPaymentIntent([
                'amount' => (int) round($total * 100), // Convertir a centavos (entero, como espera Stripe)
                'currency' => config('stripe.currency', 'mxn'),
                'order_id' => $order->id,
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ],
            ]);

            // Update order with payment intent ID
            $order->update(['payment_intent_id' => $paymentIntent->id]);

            Log::channel('payments')->info('Payment intent created', [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $total,
            ]);

            // La reserva creada al cobrar expira (por defecto 15 min).
            // Exponer su vencimiento permite al checkout mostrar la cuenta
            // regresiva sin cambiar ninguna regla de negocio.
            $reservaExpiraEn = StockReservation::where('user_id', Auth::id())
                ->active()
                ->min('expires_at');

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'order_id' => $order->id,
                'reserva_expira_en' => $reservaExpiraEn,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create payment intent', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error al crear la intención de pago: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Estado de la reserva activa del usuario (solo lectura, para el countdown).
     */
    public function reservaEstado()
    {
        $expiraEn = StockReservation::where('user_id', Auth::id())
            ->active()
            ->min('expires_at');

        if (! $expiraEn) {
            return response()->json([
                'tiene_reserva' => false,
                'expira_en' => null,
                'segundos' => 0,
            ]);
        }

        return response()->json([
            'tiene_reserva' => true,
            'expira_en' => $expiraEn,
            'segundos' => max(0, now()->diffInSeconds($expiraEn, false)),
        ]);
    }

    /**
     * Confirm order after successful Stripe payment
     */
    public function confirmOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_intent_id' => 'required|string',
        ]);

        $orderId = $request->input('order_id');
        $paymentIntentId = $request->input('payment_intent_id');

        try {
            $order = Order::where('id', $orderId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Idempotencia: doble clic o reintento devuelven éxito sin
            // reconfirmar reservas ni decrementar stock dos veces.
            if ($order->payment_status === PaymentStatus::PAID->value
                && $order->payment_intent_id === $paymentIntentId) {
                return response()->json([
                    'message' => 'Orden confirmada exitosamente',
                    'order_id' => $order->id,
                ]);
            }

            // Verify payment with Stripe
            $paymentIntent = $this->paymentService->retrievePaymentIntent($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                DB::transaction(function () use ($order, $paymentIntentId) {
                    // Update order status - paid but pending worker acceptance
                    $order->update([
                        'status' => OrderStatus::PAID,
                        'payment_status' => PaymentStatus::PAID,
                        'payment_intent_id' => $paymentIntentId,
                    ]);

                    // Confirm stock reservations (convert to actual stock reduction)
                    foreach ($order->orderItems as $item) {
                        $this->stockService->confirmReservation($item->product_id, $item->quantity, Auth::id());
                    }
                });

                // Clear cart session
                session()->forget('cart');

                Log::channel('orders')->info('Order confirmed after payment', [
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'payment_intent_id' => $paymentIntentId,
                ]);

                return response()->json([
                    'message' => 'Orden confirmada exitosamente',
                    'order_id' => $order->id,
                ]);
            } else {
                // Payment failed, release stock reservations
                foreach ($order->orderItems as $item) {
                    $this->stockService->releaseReservation($item->product_id, $item->quantity, Auth::id());
                }

                $order->update([
                    'status' => OrderStatus::FAILED,
                    'payment_status' => PaymentStatus::FAILED,
                ]);

                return response()->json([
                    'message' => 'El pago no fue exitoso',
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Failed to confirm order', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error al confirmar la orden: '.$e->getMessage(),
            ], 500);
        }
    }
}
