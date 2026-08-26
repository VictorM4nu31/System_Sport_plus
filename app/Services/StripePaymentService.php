<?php

namespace App\Services;

use App\Contracts\OrderProcessingInterface;
use App\Contracts\PaymentServiceInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Webhook;

class StripePaymentService implements PaymentServiceInterface
{
    protected OrderProcessingInterface $orderService;

    public function __construct(OrderProcessingInterface $orderService)
    {
        $this->orderService = $orderService;
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Create a payment intent for the given order data
     */
    public function createPaymentIntent(array $orderData): PaymentIntent
    {
        try {
            $amount = isset($orderData['amount']) ? $orderData['amount'] : $this->convertToStripeAmount($orderData['total'] ?? 0);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $orderData['currency'] ?? config('stripe.currency', 'mxn'),
                'metadata' => array_merge([
                    'user_id' => $orderData['user_id'] ?? null,
                    'order_reference' => $orderData['reference'] ?? null,
                ], $orderData['metadata'] ?? []),
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'receipt_email' => $orderData['customer_email'] ?? null,
            ]);

            Log::channel('payments')->info('Payment intent created', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'user_id' => $orderData['user_id'] ?? null,
            ]);

            return $paymentIntent;
        } catch (Exception $e) {
            Log::channel('payments')->error('Failed to create payment intent', [
                'error' => $e->getMessage(),
                'order_data' => $orderData,
            ]);
            throw $e;
        }
    }

    /**
     * Confirm a payment using the payment intent ID
     */
    public function confirmPayment(string $paymentIntentId): bool
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                Log::channel('payments')->info('Payment confirmed successfully', [
                    'payment_intent_id' => $paymentIntentId,
                    'amount' => $paymentIntent->amount,
                ]);

                return true;
            }

            Log::channel('payments')->warning('Payment confirmation failed', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $paymentIntent->status,
            ]);

            return false;
        } catch (Exception $e) {
            Log::channel('payments')->error('Error confirming payment', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Retrieve a payment intent by ID
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            Log::channel('payments')->info('Payment intent retrieved', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $paymentIntent->status,
            ]);

            return $paymentIntent;
        } catch (Exception $e) {
            Log::channel('payments')->error('Error retrieving payment intent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle incoming webhook requests from Stripe
     */
    public function handleWebhook(Request $request): void
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );

            Log::channel('payments')->info('Webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            // Handle different event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    $this->handlePaymentCanceled($event->data->object);
                    break;

                default:
                    Log::channel('payments')->info('Unhandled webhook event type', [
                        'event_type' => $event->type,
                    ]);
            }
        } catch (SignatureVerificationException $e) {
            Log::channel('payments')->error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (Exception $e) {
            Log::channel('payments')->error('Webhook processing failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process a refund for a payment
     *
     * @param  int|null  $amount  Amount in cents, null for full refund
     */
    public function refundPayment(string $paymentIntentId, ?int $amount = null): Refund
    {
        try {
            $refundData = ['payment_intent' => $paymentIntentId];

            if ($amount !== null) {
                $refundData['amount'] = $amount;
            }

            $refund = Refund::create($refundData);

            Log::channel('payments')->info('Refund processed', [
                'refund_id' => $refund->id,
                'payment_intent_id' => $paymentIntentId,
                'amount' => $refund->amount,
            ]);

            return $refund;
        } catch (Exception $e) {
            Log::channel('payments')->error('Refund processing failed', [
                'payment_intent_id' => $paymentIntentId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Convert amount to Stripe format (cents)
     */
    private function convertToStripeAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Handle successful payment webhook
     */
    private function handlePaymentSucceeded(PaymentIntent $paymentIntent): void
    {
        Log::channel('payments')->info('Payment succeeded webhook processed', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'metadata' => $paymentIntent->metadata->toArray(),
        ]);

        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();

        if (! $order) {
            Log::channel('payments')->warning('Order not found for succeeded payment intent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return;
        }

        // Idempotency: never reprocess an order already paid.
        if ($order->payment_status === PaymentStatus::PAID->value) {
            Log::channel('payments')->info('Payment success event already processed, skipping order', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return;
        }

        try {
            DB::transaction(function () use ($order, $paymentIntent) {
                if (! $this->orderService->confirmStockReservations($order)) {
                    Log::channel('payments')->error('Failed to confirm stock reservations after payment success', [
                        'order_id' => $order->id,
                        'payment_intent_id' => $paymentIntent->id,
                    ]);

                    $order->update([
                        'status' => OrderStatus::FAILED,
                        'payment_status' => PaymentStatus::FAILED,
                    ]);

                    return;
                }

                $order->update([
                    'status' => OrderStatus::PAID,
                    'payment_status' => PaymentStatus::PAID,
                ]);
            });

            Log::channel('payments')->info('Order marked as paid from webhook', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        } catch (Exception $e) {
            Log::channel('payments')->error('Error processing payment success webhook', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return;
        }
    }

    /**
     * Handle failed payment webhook
     */
    private function handlePaymentFailed(PaymentIntent $paymentIntent): void
    {
        Log::channel('payments')->warning('Payment failed webhook processed', [
            'payment_intent_id' => $paymentIntent->id,
            'last_payment_error' => $paymentIntent->last_payment_error,
            'metadata' => $paymentIntent->metadata->toArray(),
        ]);

        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();

        if (! $order) {
            Log::channel('payments')->warning('Order not found for failed payment intent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return;
        }

        if ($order->payment_status === PaymentStatus::PAID->value) {
            Log::channel('payments')->info('Order already paid, ignoring failed event', [
                'order_id' => $order->id,
            ]);

            return;
        }

        // Release stock reservations so the inventory is not kept locked.
        $this->orderService->releaseStock($order);

        $order->update([
            'status' => OrderStatus::FAILED,
            'payment_status' => PaymentStatus::FAILED,
        ]);

        Log::channel('payments')->info('Order marked as failed from webhook', [
            'order_id' => $order->id,
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    /**
     * Handle canceled payment webhook
     */
    private function handlePaymentCanceled(PaymentIntent $paymentIntent): void
    {
        Log::channel('payments')->info('Payment canceled webhook processed', [
            'payment_intent_id' => $paymentIntent->id,
            'metadata' => $paymentIntent->metadata->toArray(),
        ]);

        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();

        if (! $order) {
            Log::channel('payments')->warning('Order not found for canceled payment intent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return;
        }

        if (in_array($order->payment_status, [PaymentStatus::PAID->value, PaymentStatus::COMPLETED->value], true)) {
            return;
        }

        $this->orderService->releaseStock($order);

        $order->update([
            'status' => OrderStatus::FAILED,
            'payment_status' => PaymentStatus::FAILED,
        ]);

        Log::channel('payments')->info('Order marked as failed from canceled webhook', [
            'order_id' => $order->id,
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }
}
