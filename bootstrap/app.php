<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'enhanced.auth' => \App\Http\Middleware\EnhancedAuthMiddleware::class,
            'input.sanitization' => \App\Http\Middleware\InputSanitizationMiddleware::class,
            'track.analytics' => \App\Http\Middleware\TrackAnalytics::class,
        ]);

        // Apply input sanitization and analytics tracking to all web routes
        $middleware->web(append: [
            \App\Http\Middleware\InputSanitizationMiddleware::class,
            \App\Http\Middleware\TrackAnalytics::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\App\Exceptions\OrderProcessingException $e, $request) {
            if ($request->expectsJson()) {
                return \App\Services\ErrorHandlingService::jsonErrorResponse(
                    $e->getMessage(),
                    500,
                    ['order_data' => $e->getOrderData()]
                );
            }
            return \App\Services\ErrorHandlingService::redirectErrorResponse(
                'usuario.cart.index',
                $e->getMessage()
            );
        });

        $exceptions->render(function (\App\Exceptions\PaymentProcessingException $e, $request) {
            if ($request->expectsJson()) {
                return \App\Services\ErrorHandlingService::jsonErrorResponse(
                    $e->getMessage(),
                    500,
                    ['payment_data' => $e->getPaymentData()]
                );
            }
            return \App\Services\ErrorHandlingService::redirectErrorResponse(
                'usuario.cart.index',
                $e->getMessage()
            );
        });

        $exceptions->render(function (\App\Exceptions\StockManagementException $e, $request) {
            if ($request->expectsJson()) {
                return \App\Services\ErrorHandlingService::jsonErrorResponse(
                    $e->getMessage(),
                    400,
                    ['stock_data' => $e->getStockData()]
                );
            }
            return \App\Services\ErrorHandlingService::redirectErrorResponse(
                'usuario.cart.index',
                $e->getMessage()
            );
        });
    })->create();
