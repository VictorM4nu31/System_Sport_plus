<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Contracts\OrderProcessingInterface;
use App\Contracts\StockManagementInterface;
use App\Contracts\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected $orderProcessingService;
    protected $stockService;
    protected $paymentService;

    public function __construct(
        OrderProcessingInterface $orderProcessingService,
        StockManagementInterface $stockService,
        PaymentServiceInterface $paymentService
    ) {
        $this->orderProcessingService = $orderProcessingService;
        $this->stockService = $stockService;
        $this->paymentService = $paymentService;
    }
    // Mostrar el carrito de compras
    public function index()
    {
        $cart = session()->get('cart', []);

        // Validate stock availability for cart items
        $stockValidation = $this->validateCartStock($cart);

        return view('usuario.cart.index', compact('cart', 'stockValidation'));
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

    // Procesar el pedido después del pago exitoso
    public function processOrder(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('usuario.cart.index')->with('error', 'No tienes productos en el carrito.');
        }

        // Validate that user has at least one address
        if (!Auth::user()->addresses()->exists()) {
            return redirect()->route('usuario.addresses.create')
                ->with('error', 'Debes agregar una dirección de envío antes de realizar un pedido.');
        }

        try {
            $paymentData = [
                'payment_status' => 'pagado',
                'shipping_address' => $request->shipping_address ?? 'No definida', // Mantener para compatibilidad
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

            Log::channel('audit')->info('Order processed via cart', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'order_id' => $order->id,
                'total_price' => $order->total_price,
                'payment_status' => $order->payment_status,
                'action' => 'cart.processOrder',
                'timestamp' => now(),
            ]);

            return redirect()->route('usuario.orders.history')->with('success', '¡Pedido procesado con éxito!');

        } catch (\InvalidArgumentException $e) {
            Log::warning('Order processing failed - validation error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('usuario.cart.index')->with('error', 'Error en los datos del pedido: ' . $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Order processing failed - system error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('usuario.cart.index')->with('error', 'Error al procesar el pedido. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Validate stock availability for all items in cart
     */
    private function validateCartStock(array $cart): array
    {
        $stockValidation = [
            'errors' => [],
            'warnings' => [],
            'valid' => true
        ];

        foreach ($cart as $productId => $details) {
            $availableStock = $this->stockService->getAvailableStock($productId);
            $product = Product::find($productId);

            if (!$product) {
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
        if (!$stockValidation['valid']) {
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
            'cart' => 'required|array',
            'total' => 'required|numeric|min:0.01',
            'shipping_address_id' => 'required|exists:addresses,id'
        ]);

        $cart = $request->input('cart');
        $total = $request->input('total');
        $shippingAddressId = $request->input('shipping_address_id');

        // Verify the address belongs to the authenticated user
        $address = Auth::user()->addresses()->findOrFail($shippingAddressId);

        try {
            // Validate stock availability
            $stockValidation = $this->validateCartStock($cart);
            if (!$stockValidation['valid']) {
                return response()->json([
                    'message' => 'Algunos productos no tienen stock suficiente',
                    'errors' => $stockValidation['errors']
                ], 400);
            }

            // Reserve stock for the cart items
            foreach ($cart as $productId => $details) {
                $reserved = $this->stockService->reserveStock($productId, $details['quantity'], Auth::id());
                if (!$reserved) {
                    return response()->json([
                        'message' => "No se pudo reservar stock para el producto ID: {$productId}"
                    ], 400);
                }
            }

            // Create a pending order
            $orderData = [
                'user_id' => Auth::id(),
                'total_price' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address_id' => $shippingAddressId,
                'notes' => 'Orden creada para pago con Stripe'
            ];

            $order = Order::create($orderData);

            // Create order items
            foreach ($cart as $productId => $details) {
                $order->orderItems()->create([
                    'product_id' => $productId,
                    'quantity' => $details['quantity'],
                    'price' => $details['price']
                ]);
            }

            // Create payment intent with Stripe
            $paymentIntent = $this->paymentService->createPaymentIntent([
                'amount' => $total * 100, // Convert to cents
                'currency' => 'mxn',
                'order_id' => $order->id,
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => Auth::id()
                ]
            ]);

            // Update order with payment intent ID
            $order->update(['payment_intent_id' => $paymentIntent->id]);

            Log::channel('payments')->info('Payment intent created', [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $total
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create payment intent', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'cart' => $cart
            ]);

            return response()->json([
                'message' => 'Error al crear la intención de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm order after successful Stripe payment
     */
    public function confirmOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_intent_id' => 'required|string'
        ]);

        $orderId = $request->input('order_id');
        $paymentIntentId = $request->input('payment_intent_id');

        try {
            $order = Order::where('id', $orderId)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

            // Verify payment with Stripe
            $paymentIntent = $this->paymentService->retrievePaymentIntent($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                DB::transaction(function () use ($order, $paymentIntentId) {
                    // Update order status - paid but pending worker acceptance
                    $order->update([
                        'status' => 'paid',
                        'payment_status' => 'paid',
                        'payment_intent_id' => $paymentIntentId
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
                    'payment_intent_id' => $paymentIntentId
                ]);

                return response()->json([
                    'message' => 'Orden confirmada exitosamente',
                    'order_id' => $order->id
                ]);
            } else {
                // Payment failed, release stock reservations
                foreach ($order->orderItems as $item) {
                    $this->stockService->releaseReservation($item->product_id, $item->quantity, Auth::id());
                }

                $order->update([
                    'status' => 'failed',
                    'payment_status' => 'failed'
                ]);

                return response()->json([
                    'message' => 'El pago no fue exitoso'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Failed to confirm order', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al confirmar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

}
