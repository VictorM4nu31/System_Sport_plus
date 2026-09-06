<?php

use App\Exceptions\OrderProcessingException;
use App\Exceptions\PaymentProcessingException;
use App\Exceptions\StockManagementException;
use App\Http\Middleware\EnhancedAuthMiddleware;
use App\Http\Middleware\InputSanitizationMiddleware;
use App\Http\Middleware\TrackAnalytics;
use App\Services\ErrorHandlingService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Stripe envía el webhook sin token CSRF
        $middleware->preventRequestForgery(except: [
            'stripe/*',
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'enhanced.auth' => EnhancedAuthMiddleware::class,
            'input.sanitization' => InputSanitizationMiddleware::class,
            'track.analytics' => TrackAnalytics::class,
        ]);

        // Apply input sanitization and analytics tracking to all web routes
        $middleware->web(append: [
            InputSanitizationMiddleware::class,
            TrackAnalytics::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (OrderProcessingException $e, $request) {
            if ($request->expectsJson()) {
                return ErrorHandlingService::jsonErrorResponse(
                    $e->getMessage(),
                    500,
                    ['order_data' => $e->getOrderData()]
                );
            }

            return ErrorHandlingService::redirectErrorResponse(
                'usuario.cart.index',
                $e->getMessage()
            );
        });

        $exceptions->render(function (PaymentProcessingException $e, $request) {
            if ($request->expectsJson()) {
                return ErrorHandlingService::jsonErrorResponse(
                    $e->getMessage(),
                    500,
                    ['payment_data' => $e->getPaymentData()]
                );
            }

            return ErrorHandlingService::redirectErrorResponse(
                'usuario.cart.index',
                $e->getMessage()
            );
        });

        $exceptions->render(function (StockManagementException $e, $request) {
            if ($request->expectsJson()) {
                return ErrorHandlingService::jsonErrorResponse(
                    $e->getMessage(),
                    400,
                    ['stock_data' => $e->getStockData()]
                );
            }

            return ErrorHandlingService::redirectErrorResponse(
                'usuario.cart.index',
                $e->getMessage()
            );
        });
    })->create();
