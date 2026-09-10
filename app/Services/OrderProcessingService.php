<?php

namespace App\Services;

use App\Contracts\OrderProcessingInterface;
use App\Contracts\StockManagementInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class OrderProcessingService implements OrderProcessingInterface
{
    protected StockManagementInterface $stockService;

    public function __construct(StockManagementInterface $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Process a complete order from cart data
     *
     * @throws \Exception
     */
    public function processOrder(array $cartData, User $user, array $paymentData): Order
    {
        // Validate input data
        if (! $this->validateOrderData($cartData)) {
            throw new InvalidArgumentException('Invalid cart data provided');
        }

        // Validate stock availability using the stock service
        $stockValidation = $this->validateStockAvailability($cartData);
        if (! empty($stockValidation['errors'])) {
            throw new InvalidArgumentException('Stock validation failed: '.implode(', ', $stockValidation['errors']));
        }

        return DB::transaction(function () use ($cartData, $user, $paymentData) {
            // Reserve stock first using the stock service
            if (! $this->reserveStock($cartData, $user->id)) {
                throw new \Exception('Failed to reserve stock for order');
            }

            try {
                // Calculate total
                $totalPrice = $this->calculateOrderTotal($cartData);

                // Determine shipping address
                $shippingAddressId = $this->determineShippingAddress($user, $paymentData);

                // Create the order
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_price' => $totalPrice,
                    'status' => OrderStatus::PENDING->value,
                    'payment_status' => PaymentStatus::PAID->value,
                    'shipping_address' => $paymentData['shipping_address'] ?? null, // Mantener para compatibilidad
                    'shipping_address_id' => $shippingAddressId,
                    'payment_intent_id' => $paymentData['payment_intent_id'] ?? null,
                    'notes' => $paymentData['notes'] ?? null,
                ]);

                // Create order items
                foreach ($cartData as $productId => $details) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                    ]);
                }

                // Log successful order creation
                Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'total_price' => $totalPrice,
                ]);

                return $order;

            } catch (\Exception $e) {
                // Release stock if order creation fails
                $this->releaseStockFromCartData($cartData, $user->id);
                throw $e;
            }
        });
    }

    /**
     * Validate order data before processing
     */
    public function validateOrderData(array $cartData): bool
    {
        if (empty($cartData)) {
            return false;
        }

        foreach ($cartData as $productId => $details) {
            // Validate required fields
            if (! isset($details['quantity']) || ! isset($details['price'])) {
                return false;
            }

            // Validate data types
            if (! is_numeric($details['quantity']) || ! is_numeric($details['price'])) {
                return false;
            }

            // Validate positive values
            if ($details['quantity'] <= 0 || $details['price'] < 0) {
                return false;
            }

            // Validate product exists
            if (! Product::find($productId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the total price for an order
     */
    public function calculateOrderTotal(array $cartData): float
    {
        $total = 0;

        foreach ($cartData as $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return round($total, 2);
    }

    /**
     * Reserve stock for products in the cart
     */
    public function reserveStock(array $cartData, int $userId): bool
    {
        foreach ($cartData as $productId => $details) {
            if (! $this->stockService->reserveStock($productId, $details['quantity'], $userId)) {
                // If any reservation fails, release all previous reservations
                $this->releaseStockFromCartData($cartData, $userId);

                return false;
            }
        }

        return true;
    }

    /**
     * Release reserved stock for an order
     */
    public function releaseStock(Order $order): void
    {
        foreach ($order->orderItems as $orderItem) {
            $this->stockService->releaseReservation(
                $orderItem->product_id,
                $orderItem->quantity,
                $order->user_id
            );
        }

        Log::info('Stock released for order', ['order_id' => $order->id]);
    }

    /**
     * Release stock from cart data (used in rollback scenarios)
     */
    private function releaseStockFromCartData(array $cartData, int $userId): void
    {
        foreach ($cartData as $productId => $details) {
            $this->stockService->releaseReservation($productId, $details['quantity'], $userId);
        }
    }

    /**
     * Validate stock availability for cart items
     */
    public function validateStockAvailability(array $cartData): array
    {
        $errors = [];
        $warnings = [];

        foreach ($cartData as $productId => $details) {
            $product = Product::find($productId);

            if (! $product) {
                $errors[] = "Product with ID {$productId} not found";

                continue;
            }

            // Use the stock service to check available stock (considering reservations)
            $availableStock = $this->stockService->getAvailableStock($productId);

            if ($availableStock < $details['quantity']) {
                $errors[] = "Insufficient stock for product '{$product->name}'. Available: {$availableStock}, Requested: {$details['quantity']}";
            } elseif ($availableStock < ($details['quantity'] * 2)) {
                $warnings[] = "Low stock for product '{$product->name}'. Available: {$availableStock}";
            }
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'valid' => empty($errors),
        ];
    }

    /**
     * Confirm stock reservations after successful payment
     */
    public function confirmStockReservations(Order $order): bool
    {
        foreach ($order->orderItems as $orderItem) {
            if (! $this->stockService->confirmReservation(
                $orderItem->product_id,
                $orderItem->quantity,
                $order->user_id
            )) {
                Log::error('Failed to confirm stock reservation', [
                    'order_id' => $order->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $orderItem->quantity,
                ]);

                return false;
            }
        }

        Log::info('Stock reservations confirmed for order', ['order_id' => $order->id]);

        return true;
    }

    /**
     * Determine which shipping address to use for the order
     */
    private function determineShippingAddress(User $user, array $paymentData): ?int
    {
        // If a specific address ID is provided, validate it belongs to the user
        if (isset($paymentData['shipping_address_id'])) {
            $address = $user->addresses()->find($paymentData['shipping_address_id']);
            if ($address) {
                return $address->id;
            }
        }

        // Fall back to user's default address
        $defaultAddress = $user->defaultAddress;
        if ($defaultAddress) {
            return $defaultAddress->id;
        }

        // If no default address, use the first available address
        $firstAddress = $user->addresses()->first();
        if ($firstAddress) {
            return $firstAddress->id;
        }

        // No address found
        return null;
    }
}
