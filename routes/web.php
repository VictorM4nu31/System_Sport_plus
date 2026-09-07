<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\UserOrderHistoryController;
use App\Http\Controllers\User\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'welcome'])->name('welcome');

// Dashboard redirect based on user role
Route::get('/dashboard', [DashboardRedirectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

// Registration routes
Route::middleware('guest')->controller(RegisteredUserController::class)->group(function () {
    Route::get('/register', 'create')->name('register');
    Route::post('/register', 'store');
});

// User routes
Route::middleware(['auth', 'role:usuario'])->group(function () {
    Route::get('/productos', [UserProductController::class, 'index'])->name('usuario.products.index');
    Route::get('/productos/search', [UserProductController::class, 'search'])->name('usuario.products.search');
    Route::get('/productos/{id}/ficha', [UserProductController::class, 'ficha'])->name('usuario.products.ficha');
    Route::get('/productos/{id}', [UserProductController::class, 'show'])->name('usuario.products.show');

    // Cart routes
    Route::controller(CartController::class)->group(function () {
        Route::get('/carrito', 'index')->name('usuario.cart.index');
        Route::post('/carrito/{id}/agregar', 'add')->name('usuario.cart.add');
        Route::post('/carrito/{id}/eliminar', 'remove')->name('usuario.cart.remove');

        // Stripe payment routes
        Route::post('/carrito/create-payment-intent', 'createPaymentIntent')->name('usuario.cart.create-payment-intent');
        Route::post('/carrito/confirm-order', 'confirmOrder')->name('usuario.cart.confirm-order');
        Route::get('/carrito/reserva-estado', 'reservaEstado')->name('usuario.cart.reserva-estado');
    });

    // Order routes
    Route::controller(UserOrderController::class)->group(function () {
        Route::get('/pedidos', 'index')->name('usuario.orders.index');
        Route::get('/pedidos/{id}', 'show')->name('usuario.orders.show');
    });

    // Order history routes
    Route::controller(UserOrderHistoryController::class)->prefix('historial-pedidos')->group(function () {
        Route::get('/', 'history')->name('usuario.orders.history');
        Route::get('/{id}', 'show')->name('usuario.orders.history.show');
    });

    // Wishlist routes
    Route::controller(WishlistController::class)->prefix('lista-deseos')->group(function () {
        Route::get('/', 'index')->name('usuario.wishlist.index');
        Route::post('/{id}/agregar', 'add')->name('usuario.wishlist.add');
        Route::post('/{id}/eliminar', 'remove')->name('usuario.wishlist.remove');
        Route::post('/{id}/mover', 'moveToCart')->name('usuario.wishlist.move');
    });

    // Review routes
    Route::controller(ReviewController::class)->prefix('productos')->group(function () {
        Route::post('/{id}/reseñas', 'store')->name('usuario.reviews.store'); // Ruta para guardar reseñas
        Route::get('/{id}/reseñas', 'index')->name('usuario.reviews.index'); // Ruta para mostrar reseñas de un producto
    });
});

// Admin routes
Route::middleware(['auth', 'role:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
        Route::resource('workers', WorkerController::class)->except(['show']);
        Route::resource('products', ProductController::class);
        Route::post('/products/{id}/sync-stripe', [ProductController::class, 'syncWithStripe'])->name('products.sync-stripe');
        Route::get('/products/{id}/stripe-info', [ProductController::class, 'stripeInfo'])->name('products.stripe-info');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');

        // Monitoring routes
        Route::controller(MonitoringController::class)->prefix('monitoring')->group(function () {
            Route::get('/dashboard', 'dashboard')->name('monitoring.dashboard');
            Route::get('/health-status', 'healthStatus')->name('monitoring.health-status');
            Route::get('/metrics', 'metrics')->name('monitoring.metrics');
            Route::get('/alerts', 'alerts')->name('monitoring.alerts');
            Route::post('/clear-alerts', 'clearAlerts')->name('monitoring.clear-alerts');
            Route::post('/run-health-check', 'runHealthCheck')->name('monitoring.run-health-check');
            Route::get('/logs', 'logs')->name('monitoring.logs');
            Route::get('/performance-history', 'performanceHistory')->name('monitoring.performance-history');
        });
    });

// Worker routes
Route::middleware(['auth', 'role:trabajador'])
    ->prefix('trabajador')
    ->name('trabajador.')
    ->group(function () {
        Route::controller(OrderController::class)->prefix('pedidos')->group(function () {
            Route::get('/', 'workerIndex')->name('orders.index');
            Route::get('/{id}', 'workerShow')->name('orders.show');
            Route::patch('/{id}/aceptar', 'acceptOrder')->name('orders.accept');
            Route::patch('/{id}/rechazar', 'rejectOrder')->name('orders.reject');
        });
        Route::get('/reportes', [ReportController::class, 'workerSalesReport'])->name('reports.sales');
    });

// Address management routes (moved inside user middleware group)
Route::middleware(['auth', 'role:usuario'])->group(function () {
    Route::controller(AddressController::class)->prefix('direcciones')->group(function () {
        Route::get('/', 'index')->name('usuario.addresses.index');
        Route::get('/crear', 'create')->name('usuario.addresses.create');
        Route::post('/', 'store')->name('usuario.addresses.store');
        Route::get('/{address}/editar', 'edit')->name('usuario.addresses.edit');
        Route::put('/{address}', 'update')->name('usuario.addresses.update');
        Route::delete('/{address}', 'destroy')->name('usuario.addresses.destroy');
        Route::patch('/{address}/default', 'setDefault')->name('usuario.addresses.setDefault');
    });

    // Dashboard del usuario
    Route::get('/usuario/dashboard', [UserOrderController::class, 'dashboard'])->name('usuario.dashboard');
});

Route::get('/api/address/{postalCode}', [ProfileController::class, 'getAddressByPostalCode']);

// Stripe webhook route (no authentication required)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

require __DIR__.'/auth.php';
