<?php

namespace App\Providers;

use App\Contracts\OrderProcessingInterface;
use App\Contracts\PaymentServiceInterface;
use App\Contracts\StockManagementInterface;
use App\Models\Order;
use App\Services\OrderProcessingService;
use App\Services\StockManagementService;
use App\Services\StripePaymentService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the payment service interface to the Stripe implementation
        $this->app->bind(
            PaymentServiceInterface::class,
            StripePaymentService::class
        );

        // Bind the order processing service interface
        $this->app->bind(
            OrderProcessingInterface::class,
            OrderProcessingService::class
        );

        // Bind the stock management service interface
        $this->app->bind(
            StockManagementInterface::class,
            StockManagementService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // El parámetro {pedido} de /admin/pedidos resuelve al modelo Order.
        Route::model('pedido', Order::class);

        // Environment validation temporarily disabled during development
        // Will be re-enabled after completing Stripe integration
        // $this->validateRequiredEnvironmentVariables();
    }

    /**
     * Validate that required environment variables are set
     */
    private function validateRequiredEnvironmentVariables(): void
    {
        $requiredVars = [
            'APP_KEY',
            'DB_CONNECTION',
            'DB_HOST',
            'DB_DATABASE',
        ];

        // Add payment-related variables only in production
        if (app()->environment('production')) {
            $requiredVars = array_merge($requiredVars, [
                'STRIPE_KEY',
                'STRIPE_SECRET',
                'STRIPE_WEBHOOK_SECRET',
            ]);
        }

        $missingVars = [];
        foreach ($requiredVars as $var) {
            $value = env($var);
            if ($value === null || $value === '') {
                $missingVars[] = $var;
            }
        }

        if (! empty($missingVars)) {
            throw new \RuntimeException(
                'Missing required environment variables: '.implode(', ', $missingVars).
                '. Please check your .env file and ensure all required variables are set.'
            );
        }
    }
}
