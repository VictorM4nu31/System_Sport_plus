<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ErrorHandlingService
{
    /**
     * Handle and log application errors consistently
     */
    public static function handleError(
        Throwable $exception,
        string $context = 'general',
        array $additionalData = [],
        string $userMessage = 'Ha ocurrido un error. Por favor, inténtalo de nuevo.'
    ): array {
        $errorData = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'timestamp' => now(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'user_email' => Auth::check() ? Auth::user()->email : 'guest',
        ];

        // Merge additional data
        $errorData = array_merge($errorData, $additionalData);

        // Log based on exception type
        if ($exception instanceof ValidationException) {
            Log::channel('orders')->warning('Validation error', $errorData);
        } else {
            Log::channel('orders')->error('Application error', $errorData);
        }

        return [
            'error' => $userMessage,
            'code' => $exception->getCode() ?: 500,
            'logged' => true
        ];
    }

    /**
     * Handle payment-specific errors
     */
    public static function handlePaymentError(
        Throwable $exception,
        array $paymentData = [],
        string $userMessage = 'Error al procesar el pago. Por favor, inténtalo de nuevo.'
    ): array {
        $errorData = [
            'payment_data' => $paymentData,
            'payment_intent_id' => $paymentData['payment_intent_id'] ?? null,
            'amount' => $paymentData['amount'] ?? null,
        ];

        Log::channel('payments')->error('Payment processing error', array_merge([
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => 'payment_processing',
            'timestamp' => now(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'user_email' => Auth::check() ? Auth::user()->email : 'guest',
        ], $errorData));

        return [
            'error' => $userMessage,
            'code' => 500,
            'logged' => true
        ];
    }

    /**
     * Handle order-specific errors
     */
    public static function handleOrderError(
        Throwable $exception,
        array $orderData = [],
        string $userMessage = 'Error al procesar el pedido. Por favor, inténtalo de nuevo.'
    ): array {
        $errorData = [
            'order_data' => $orderData,
            'cart_items' => $orderData['cart'] ?? null,
            'total_amount' => $orderData['total_price'] ?? null,
        ];

        Log::channel('orders')->error('Order processing error', array_merge([
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => 'order_processing',
            'timestamp' => now(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'user_email' => Auth::check() ? Auth::user()->email : 'guest',
        ], $errorData));

        return [
            'error' => $userMessage,
            'code' => 500,
            'logged' => true
        ];
    }

    /**
     * Handle stock-related errors
     */
    public static function handleStockError(
        Throwable $exception,
        array $stockData = [],
        string $userMessage = 'Error en la gestión de inventario. Por favor, inténtalo de nuevo.'
    ): array {
        $errorData = [
            'stock_data' => $stockData,
            'product_id' => $stockData['product_id'] ?? null,
            'requested_quantity' => $stockData['quantity'] ?? null,
            'available_stock' => $stockData['available_stock'] ?? null,
        ];

        Log::channel('orders')->error('Stock management error', array_merge([
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => 'stock_management',
            'timestamp' => now(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'user_email' => Auth::check() ? Auth::user()->email : 'guest',
        ], $errorData));

        return [
            'error' => $userMessage,
            'code' => 400,
            'logged' => true
        ];
    }

    /**
     * Create consistent JSON error response
     */
    public static function jsonErrorResponse(
        string $message,
        int $code = 500,
        array $additionalData = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'error' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if (!empty($additionalData)) {
            $response = array_merge($response, $additionalData);
        }

        return response()->json($response, $code);
    }

    /**
     * Create consistent redirect error response
     */
    public static function redirectErrorResponse(
        string $route,
        string $message,
        array $additionalData = []
    ): RedirectResponse {
        $redirect = redirect()->route($route)->with('error', $message);

        foreach ($additionalData as $key => $value) {
            $redirect->with($key, $value);
        }

        return $redirect;
    }

    /**
     * Log successful operations for audit trail
     */
    public static function logSuccess(
        string $action,
        array $data = [],
        string $channel = 'audit'
    ): void {
        Log::channel($channel)->info("Successful operation: {$action}", array_merge([
            'action' => $action,
            'timestamp' => now(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'user_email' => Auth::check() ? Auth::user()->email : 'guest',
        ], $data));
    }

    /**
     * Validate and sanitize input data
     */
    public static function sanitizeInput(array $data, array $allowedFields): array
    {
        $sanitized = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $value = $data[$field];

                // Basic sanitization
                if (is_string($value)) {
                    $value = trim($value);
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }

                $sanitized[$field] = $value;
            }
        }

        return $sanitized;
    }
}
