<?php

use App\Services\StripeConfigService;

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Keys (Secure Configuration)
    |--------------------------------------------------------------------------
    |
    | The Stripe keys are loaded from encrypted configuration for security.
    | Fallback to environment variables if encrypted config is not available.
    |
    */

    'key' => app(StripeConfigService::class)->getPublishableKey(),

    'secret' => app(StripeConfigService::class)->getSecretKey(),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhook Secret (Secure Configuration)
    |--------------------------------------------------------------------------
    |
    | This is the webhook endpoint secret from your Stripe dashboard.
    | It's loaded from encrypted configuration for security.
    |
    */

    'webhook' => [
        'secret' => app(StripeConfigService::class)->getWebhookSecret(),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via Stripe.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'mxn'),

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | This array contains the payment methods that are supported by your
    | application. You can add or remove payment methods as needed.
    |
    */

    'payment_methods' => [
        'card',
        // Add other payment methods as needed
        // 'ideal', 'sepa_debit', 'bancontact', etc.
    ],

    /*
    |--------------------------------------------------------------------------
    | Logger
    |--------------------------------------------------------------------------
    |
    | This setting allows you to configure the logging channel that will be
    | used to log Stripe related events and errors.
    |
    */

    'logger' => env('STRIPE_LOGGER', 'payments'),

    /*
    |--------------------------------------------------------------------------
    | Auto Sync Configuration
    |--------------------------------------------------------------------------
    |
    | Automatically sync products with Stripe when created/updated/deleted
    |
    */

    'auto_sync' => env('STRIPE_AUTO_SYNC', true),

];
