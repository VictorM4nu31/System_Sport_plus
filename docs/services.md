# Services Documentation

This document describes the core services implemented in the e-commerce system and how to use them.

## Overview

The system uses a service-oriented architecture to encapsulate business logic and provide reusable components. All services implement interfaces to ensure consistency and testability.

## Payment Services

### StripePaymentService

Handles all payment processing through Stripe's API.

**Interface:** `App\Contracts\PaymentServiceInterface`

**Location:** `app/Services/StripePaymentService.php`

#### Methods

##### createPaymentIntent(array $orderData): PaymentIntent

Creates a new payment intent for processing a payment.

```php
use App\Services\StripePaymentService;

$paymentService = app(StripePaymentService::class);

$paymentIntent = $paymentService->createPaymentIntent([
    'amount' => 2999, // Amount in cents
    'currency' => 'usd',
    'order_items' => [
        ['product_id' => 1, 'quantity' => 2, 'price' => 1499]
    ],
    'customer_id' => auth()->id(),
    'metadata' => [
        'order_type' => 'online',
        'source' => 'web'
    ]
]);

echo $paymentIntent->client_secret; // Use in frontend
```

##### confirmPayment(string $paymentIntentId): bool

Confirms a payment after successful payment method collection.

```php
$success = $paymentService->confirmPayment('pi_1234567890');

if ($success) {
    // Payment confirmed, create order
    $order = $orderService->createFromPaymentIntent('pi_1234567890');
}
```

##### handleWebhook(Request $request): void

Processes incoming Stripe webhooks.

```php
// In WebhookController
public function handle(Request $request)
{
    try {
        $paymentService->handleWebhook($request);
        return response('OK', 200);
    } catch (\Exception $e) {
        Log::error('Webhook processing failed: ' . $e->getMessage());
        return response('Error', 400);
    }
}
```

##### refundPayment(string $paymentIntentId, int $amount = null): Refund

Processes a refund for a completed payment.

```php
// Full refund
$refund = $paymentService->refundPayment('pi_1234567890');

// Partial refund
$refund = $paymentService->refundPayment('pi_1234567890', 1000); // $10.00
```

#### Configuration

Configure the service in `config/stripe.php`:

```php
return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],
    'currency' => env('CASHIER_CURRENCY', 'usd'),
    'logger' => env('STRIPE_LOGGER', 'payments'),
];
```

## Order Processing Services

### OrderProcessingService

Provides unified order creation and management functionality.

**Interface:** `App\Contracts\OrderProcessingInterface`

**Location:** `app/Services/OrderProcessingService.php`

#### Methods

##### processOrder(array $cartData, User $user, array $paymentData): Order

Creates a new order with validation and stock management.

```php
use App\Services\OrderProcessingService;

$orderService = app(OrderProcessingService::class);

$order = $orderService->processOrder(
    cartData: [
        'items' => [
            ['product_id' => 1, 'quantity' => 2, 'price' => 1499]
        ],
        'shipping_address_id' => 1,
        'notes' => 'Handle with care'
    ],
    user: auth()->user(),
    paymentData: [
        'payment_intent_id' => 'pi_1234567890',
        'payment_method' => 'stripe'
    ]
);
```

##### validateOrderData(array $cartData): bool

Validates order data before processing.

```php
$isValid = $orderService->validateOrderData([
    'items' => [
        ['product_id' => 1, 'quantity' => 2, 'price' => 1499]
    ],
    'shipping_address_id' => 1
]);

if (!$isValid) {
    // Handle validation errors
    $errors = $orderService->getValidationErrors();
}
```

##### calculateOrderTotal(array $cartData): float

Calculates the total price for an order.

```php
$total = $orderService->calculateOrderTotal([
    'items' => [
        ['product_id' => 1, 'quantity' => 2, 'price' => 1499],
        ['product_id' => 2, 'quantity' => 1, 'price' => 999]
    ],
    'shipping_cost' => 500,
    'tax_rate' => 0.08
]);
```

##### reserveStock(array $cartData): bool

Reserves stock for order items.

```php
$reserved = $orderService->reserveStock([
    'items' => [
        ['product_id' => 1, 'quantity' => 2],
        ['product_id' => 2, 'quantity' => 1]
    ]
]);

if (!$reserved) {
    // Handle insufficient stock
    $stockErrors = $orderService->getStockErrors();
}
```

##### releaseStock(Order $order): void

Releases stock reservations for a cancelled order.

```php
$orderService->releaseStock($order);
```

#### Error Handling

The service provides detailed error information:

```php
try {
    $order = $orderService->processOrder($cartData, $user, $paymentData);
} catch (InsufficientStockException $e) {
    return response()->json([
        'error' => 'Insufficient stock',
        'details' => $e->getStockDetails()
    ], 400);
} catch (ValidationException $e) {
    return response()->json([
        'error' => 'Validation failed',
        'details' => $e->getErrors()
    ], 422);
}
```

## Stock Management Services

### StockManagementService

Manages inventory tracking and stock reservations.

**Interface:** `App\Contracts\StockManagementInterface`

**Location:** `app/Services/StockManagementService.php`

#### Methods

##### checkAvailability(int $productId, int $quantity): bool

Checks if sufficient stock is available.

```php
use App\Services\StockManagementService;

$stockService = app(StockManagementService::class);

$available = $stockService->checkAvailability(1, 5);

if (!$available) {
    $currentStock = $stockService->getCurrentStock(1);
    // Show "Only {$currentStock} items available" message
}
```

##### reserveStock(int $productId, int $quantity): bool

Creates a temporary stock reservation.

```php
$reserved = $stockService->reserveStock(1, 2);

if ($reserved) {
    $reservationId = $stockService->getLastReservationId();
    // Store reservation ID for later confirmation or release
}
```

##### confirmReservation(int $productId, int $quantity): void

Confirms a stock reservation (permanently reduces stock).

```php
// After successful payment
$stockService->confirmReservation(1, 2);
```

##### releaseReservation(int $productId, int $quantity): void

Releases a stock reservation (returns stock to available pool).

```php
// If payment fails or user cancels
$stockService->releaseReservation(1, 2);
```

##### updateStock(int $productId, int $newStock): void

Updates the stock level for a product (admin function).

```php
// Restock inventory
$stockService->updateStock(1, 100);
```

#### Batch Operations

The service supports batch operations for multiple products:

```php
// Check multiple products
$availability = $stockService->checkBatchAvailability([
    ['product_id' => 1, 'quantity' => 2],
    ['product_id' => 2, 'quantity' => 1]
]);

// Reserve multiple products
$reserved = $stockService->reserveBatchStock([
    ['product_id' => 1, 'quantity' => 2],
    ['product_id' => 2, 'quantity' => 1]
]);
```

#### Automatic Cleanup

Stock reservations automatically expire after 15 minutes. A scheduled job cleans up expired reservations:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('stock:cleanup-reservations')->everyMinute();
}
```

## Security Services

### SecurityLoggingService

Handles security event logging and monitoring.

**Location:** `app/Services/SecurityLoggingService.php`

#### Methods

##### logSecurityEvent(string $event, array $context = []): void

Logs a security-related event.

```php
use App\Services\SecurityLoggingService;

$securityLogger = app(SecurityLoggingService::class);

$securityLogger->logSecurityEvent('unauthorized_access_attempt', [
    'user_id' => auth()->id(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'requested_resource' => request()->fullUrl(),
    'method' => request()->method()
]);
```

##### logPaymentEvent(string $event, array $paymentData): void

Logs payment-related security events.

```php
$securityLogger->logPaymentEvent('payment_attempt', [
    'payment_intent_id' => 'pi_1234567890',
    'amount' => 2999,
    'currency' => 'usd',
    'user_id' => auth()->id(),
    'ip_address' => request()->ip()
]);
```

##### logAuthenticationEvent(string $event, User $user = null): void

Logs authentication events.

```php
// Successful login
$securityLogger->logAuthenticationEvent('login_success', $user);

// Failed login attempt
$securityLogger->logAuthenticationEvent('login_failed', null, [
    'email' => $request->email,
    'ip_address' => $request->ip()
]);
```

#### Event Types

The service recognizes these event types:

- `login_success` / `login_failed`
- `logout`
- `password_change`
- `unauthorized_access_attempt`
- `payment_attempt` / `payment_success` / `payment_failed`
- `order_created` / `order_cancelled`
- `admin_action`
- `suspicious_activity`

## Validation Services

### ValidationService

Provides centralized validation logic for the application.

**Location:** `app/Services/ValidationService.php`

#### Methods

##### validateOrderItems(array $items): array

Validates order items and returns validation results.

```php
use App\Services\ValidationService;

$validator = app(ValidationService::class);

$result = $validator->validateOrderItems([
    ['product_id' => 1, 'quantity' => 2, 'price' => 1499],
    ['product_id' => 2, 'quantity' => 0, 'price' => 999] // Invalid quantity
]);

if (!$result['valid']) {
    foreach ($result['errors'] as $error) {
        echo $error['message'];
    }
}
```

##### validateAddress(array $addressData): array

Validates address information.

```php
$result = $validator->validateAddress([
    'full_name' => 'John Doe',
    'street' => '123 Main St',
    'city' => 'Anytown',
    'state' => 'CA',
    'postal_code' => '12345',
    'contact_phone' => '+1234567890'
]);
```

##### sanitizeInput(array $data, array $rules): array

Sanitizes input data according to specified rules.

```php
$sanitized = $validator->sanitizeInput([
    'name' => '<script>alert("xss")</script>John Doe',
    'email' => '  JOHN@EXAMPLE.COM  ',
    'phone' => '(123) 456-7890'
], [
    'name' => 'strip_tags|trim',
    'email' => 'lowercase|trim',
    'phone' => 'phone_format'
]);
```

## Error Handling Services

### ErrorHandlingService

Provides centralized error handling and logging.

**Location:** `app/Services/ErrorHandlingService.php`

#### Methods

##### handleException(\Throwable $exception, array $context = []): void

Handles and logs exceptions with appropriate context.

```php
use App\Services\ErrorHandlingService;

$errorHandler = app(ErrorHandlingService::class);

try {
    // Some risky operation
    $result = $paymentService->processPayment($data);
} catch (\Exception $e) {
    $errorHandler->handleException($e, [
        'user_id' => auth()->id(),
        'payment_data' => $data,
        'request_id' => request()->header('X-Request-ID')
    ]);
    
    // Return appropriate error response
    return response()->json(['error' => 'Payment processing failed'], 500);
}
```

##### logError(string $message, array $context = []): void

Logs error messages with context.

```php
$errorHandler->logError('Stock reservation failed', [
    'product_id' => 1,
    'requested_quantity' => 5,
    'available_stock' => 2
]);
```

##### formatErrorResponse(\Throwable $exception): array

Formats exceptions into consistent API error responses.

```php
public function handleApiException(\Throwable $e)
{
    $response = $errorHandler->formatErrorResponse($e);
    
    return response()->json($response, $response['status_code']);
}
```

## Service Registration

All services are registered in the `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
public function register()
{
    $this->app->bind(PaymentServiceInterface::class, StripePaymentService::class);
    $this->app->bind(OrderProcessingInterface::class, OrderProcessingService::class);
    $this->app->bind(StockManagementInterface::class, StockManagementService::class);
    
    $this->app->singleton(SecurityLoggingService::class);
    $this->app->singleton(ValidationService::class);
    $this->app->singleton(ErrorHandlingService::class);
}
```

## Testing Services

### Unit Testing

Each service includes comprehensive unit tests:

```php
// tests/Unit/Services/StripePaymentServiceTest.php
class StripePaymentServiceTest extends TestCase
{
    public function test_creates_payment_intent()
    {
        $service = new StripePaymentService();
        
        $intent = $service->createPaymentIntent([
            'amount' => 2999,
            'currency' => 'usd'
        ]);
        
        $this->assertInstanceOf(PaymentIntent::class, $intent);
        $this->assertEquals(2999, $intent->amount);
    }
}
```

### Integration Testing

Integration tests verify service interactions:

```php
// tests/Feature/OrderProcessingTest.php
class OrderProcessingTest extends TestCase
{
    public function test_complete_order_flow()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        
        $order = $this->orderService->processOrder([
            'items' => [['product_id' => $product->id, 'quantity' => 2]]
        ], $user, ['payment_intent_id' => 'pi_test']);
        
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertEquals(8, $product->fresh()->stock);
    }
}
```

## Performance Considerations

### Caching

Services implement caching where appropriate:

```php
// Cache stock levels for 5 minutes
$stock = Cache::remember("stock.{$productId}", 300, function () use ($productId) {
    return Product::find($productId)->stock;
});
```

### Database Optimization

- Use database transactions for atomic operations
- Implement proper indexing for frequently queried fields
- Use eager loading to prevent N+1 queries

### Queue Jobs

Long-running operations are queued:

```php
// Queue webhook processing
ProcessStripeWebhook::dispatch($webhookData);

// Queue stock cleanup
CleanupExpiredReservations::dispatch();
```

## Monitoring and Logging

All services include comprehensive logging:

```php
// Payment service logging
Log::channel('payments')->info('Payment intent created', [
    'payment_intent_id' => $intent->id,
    'amount' => $intent->amount,
    'user_id' => auth()->id()
]);

// Stock service logging
Log::channel('stock')->warning('Low stock alert', [
    'product_id' => $productId,
    'current_stock' => $currentStock,
    'threshold' => $lowStockThreshold
]);
```

## Best Practices

1. **Always use interfaces** for service dependencies
2. **Implement proper error handling** with specific exception types
3. **Log all important operations** with sufficient context
4. **Use database transactions** for operations that modify multiple records
5. **Validate all input data** before processing
6. **Cache expensive operations** where appropriate
7. **Write comprehensive tests** for all service methods
8. **Follow single responsibility principle** - each service should have one clear purpose
