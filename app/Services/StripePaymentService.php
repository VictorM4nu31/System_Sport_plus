<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Exception;

class StripePaymentService implements PaymentServiceInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Create a payment intent for the given order data
     *
     * @param array $orderData
     * @return PaymentIntent
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
     *
     * @param string $paymentIntentId
     * @return bool
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
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
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
     *
     * @param Request $request
     * @return void
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
     * @param string $paymentIntentId
     * @param int|null $amount Amount in cents, null for full refund
     * @return Refund
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
     *
     * @param float $amount
     * @return int
     */
    private function convertToStripeAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Handle successful payment webhook
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    private function handlePaymentSucceeded(PaymentIntent $paymentIntent): void
    {
        Log::channel('payments')->info('Payment succeeded webhook processed', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'metadata' => $paymentIntent->metadata->toArray(),
        ]);

        // Here you would typically update the order status in the database
        // This will be implemented in the OrderProcessingService
    }

    /**
     * Handle failed payment webhook
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    private function handlePaymentFailed(PaymentIntent $paymentIntent): void
    {
        Log::channel('payments')->warning('Payment failed webhook processed', [
            'payment_intent_id' => $paymentIntent->id,
            'last_payment_error' => $paymentIntent->last_payment_error,
            'metadata' => $paymentIntent->metadata->toArray(),
        ]);

        // Here you would typically handle the failed payment
        // This will be implemented in the OrderProcessingService
    }

    /**
     * Handle canceled payment webhook
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    private function handlePaymentCanceled(PaymentIntent $paymentIntent): void
    {
        Log::channel('payments')->info('Payment canceled webhook processed', [
            'payment_intent_id' => $paymentIntent->id,
            'metadata' => $paymentIntent->metadata->toArray(),
        ]);

        // Here you would typically handle the canceled payment
        // This will be implemented in the OrderProcessingService
    }
}
