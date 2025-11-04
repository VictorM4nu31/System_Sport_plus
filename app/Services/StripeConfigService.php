<?php

namespace App\Services;

class StripeConfigService
{
    /**
     * Get the Stripe publishable key based on environment
     */
    public function getPublishableKey(): string
    {
        return config('app.env') === 'production'
            ? env('STRIPE_KEY')
            : env('STRIPE_TEST_KEY', env('STRIPE_KEY'));
    }

    /**
     * Get the Stripe secret key based on environment
     */
    public function getSecretKey(): string
    {
        return config('app.env') === 'production'
            ? env('STRIPE_SECRET')
            : env('STRIPE_TEST_SECRET', env('STRIPE_SECRET'));
    }

    /**
     * Get the Stripe webhook secret based on environment
     */
    public function getWebhookSecret(): string
    {
        return config('app.env') === 'production'
            ? env('STRIPE_WEBHOOK_SECRET')
            : env('STRIPE_TEST_WEBHOOK_SECRET', env('STRIPE_WEBHOOK_SECRET'));
    }
}
