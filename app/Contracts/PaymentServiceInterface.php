<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Refund;

interface PaymentServiceInterface
{
    /**
     * Create a payment intent for the given order data
     *
     * @param array $orderData
     * @return PaymentIntent
     */
    public function createPaymentIntent(array $orderData): PaymentIntent;

    /**
     * Confirm a payment using the payment intent ID
     *
     * @param string $paymentIntentId
     * @return bool
     */
    public function confirmPayment(string $paymentIntentId): bool;

    /**
     * Retrieve a payment intent by ID
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent;

    /**
     * Handle incoming webhook requests from Stripe
     *
     * @param Request $request
     * @return void
     */
    public function handleWebhook(Request $request): void;

    /**
     * Process a refund for a payment
     *
     * @param string $paymentIntentId
     * @param int|null $amount Amount in cents, null for full refund
     * @return Refund
     */
    public function refundPayment(string $paymentIntentId, ?int $amount = null): Refund;
}
