<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentServiceInterface;
use App\Contracts\OrderProcessingInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected PaymentServiceInterface $paymentService;
    protected OrderProcessingInterface $orderService;

    public function __construct(
        PaymentServiceInterface $paymentService,
        OrderProcessingInterface $orderService
    ) {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleStripe(Request $request)
    {
        try {
            // Let the payment service handle webhook validation and processing
            $this->paymentService->handleWebhook($request);

            // Extract event data for additional processing
            $payload = json_decode($request->getContent(), true);
            $event = $payload['type'] ?? null;
            $paymentIntent = $payload['data']['object'] ?? null;

            if ($event === 'payment_intent.succeeded' && $paymentIntent) {
                $this->handlePaymentSuccess($paymentIntent);
            } elseif ($event === 'payment_intent.payment_failed' && $paymentIntent) {
                $this->handlePaymentFailure($paymentIntent);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }

    /**
     * Handle successful payment - confirm stock reservations
     */
    private function handlePaymentSuccess(array $paymentIntent): void
    {
        $paymentIntentId = $paymentIntent['id'] ?? null;

        if (!$paymentIntentId) {
            Log::error('Payment intent ID missing in webhook');
            return;
        }

        // Find the order by payment intent ID
        $order = Order::where('payment_intent_id', $paymentIntentId)->first();

        if (!$order) {
            Log::warning('Order not found for payment intent', [
                'payment_intent_id' => $paymentIntentId
            ]);
            return;
        }

        // Confirm stock reservations
        if ($this->orderService->confirmStockReservations($order)) {
            // Update order status to confirmed
            $order->update([
                'status' => 'confirmado',
                'payment_status' => 'pagado'
            ]);

            Log::info('Payment success processed and stock confirmed', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntentId
            ]);
        } else {
            Log::error('Failed to confirm stock reservations after payment success', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntentId
            ]);

            // Mark order as having issues
            $order->update([
                'status' => 'problema_stock',
                'payment_status' => 'pagado'
            ]);
        }
    }

    /**
     * Handle failed payment - release stock reservations
     */
    private function handlePaymentFailure(array $paymentIntent): void
    {
        $paymentIntentId = $paymentIntent['id'] ?? null;

        if (!$paymentIntentId) {
            Log::error('Payment intent ID missing in failed payment webhook');
            return;
        }

        // Find the order by payment intent ID
        $order = Order::where('payment_intent_id', $paymentIntentId)->first();

        if (!$order) {
            Log::warning('Order not found for failed payment intent', [
                'payment_intent_id' => $paymentIntentId
            ]);
            return;
        }

        // Release stock reservations
        $this->orderService->releaseStock($order);

        // Update order status
        $order->update([
            'status' => 'pago_fallido',
            'payment_status' => 'fallido'
        ]);

        Log::info('Payment failure processed and stock released', [
            'order_id' => $order->id,
            'payment_intent_id' => $paymentIntentId
        ]);
    }
}
