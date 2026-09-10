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

// PWA: manifest y service worker con cabeceras correctas.
// El checkout y las rutas sensibles nunca se cachean (ver public/sw.js).
Route::get('/manifest.webmanifest', function () {
    return response()->file(public_path('manifest.webmanifest'), [
        'Content-Type' => 'application/manifest+json',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('pwa.manifest');

Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'), [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'no-cache',
        'Service-Worker-Allowed' => '/',
    ]);
})->name('pwa.sw');

// Dashboard redirect based on user role
Route::get('/dashboard', [DashboardRedirectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/perfil', 'edit')->name('perfil.editar');
        Route::patch('/perfil', 'update')->name('perfil.actualizar');
        Route::delete('/perfil', 'destroy')->name('perfil.eliminar');
    });
});

// Registration routes
Route::middleware('guest')->controller(RegisteredUserController::class)->group(function () {
    Route::get('/register', 'create')->name('register');
    Route::post('/register', 'store');
});

// User routes
Route::middleware(['auth', 'verified', 'role:usuario'])->group(function () {
    Route::get('/productos', [UserProductController::class, 'index'])->name('usuario.productos.indice');
    Route::get('/productos/buscar', [UserProductController::class, 'search'])->name('usuario.productos.buscar');
    Route::get('/productos/{producto}/ficha', [UserProductController::class, 'ficha'])->name('usuario.productos.ficha');
    Route::get('/productos/{producto}', [UserProductController::class, 'show'])->name('usuario.productos.ver');

    // Cart routes
    Route::controller(CartController::class)->group(function () {
        Route::get('/carrito', 'index')->name('usuario.carrito.indice');
        Route::post('/carrito/{producto}/agregar', 'add')->name('usuario.carrito.agregar');
        Route::post('/carrito/{id}/eliminar', 'remove')->name('usuario.carrito.eliminar');

        // Stripe payment routes
        Route::post('/carrito/crear-intencion-pago', 'createPaymentIntent')->name('usuario.carrito.crear-intencion-pago');
        Route::post('/carrito/confirmar-pedido', 'confirmOrder')->name('usuario.carrito.confirmar-pedido');
        Route::get('/carrito/estado-reserva', 'reservaEstado')->name('usuario.carrito.estado-reserva');
    });

    // Order routes
    Route::controller(UserOrderController::class)->group(function () {
        Route::get('/pedidos', 'index')->name('usuario.pedidos.indice');
        Route::get('/pedidos/{pedido}', 'show')->name('usuario.pedidos.ver');
        Route::post('/pedidos/{pedido}/cancelar', 'cancel')->name('usuario.pedidos.cancelar');
    });

    // Order history routes
    Route::controller(UserOrderHistoryController::class)->prefix('historial-pedidos')->group(function () {
        Route::get('/', 'history')->name('usuario.pedidos.historial');
        Route::get('/{pedido}', 'show')->name('usuario.pedidos.historial.ver');
    });

    // Wishlist routes
    Route::controller(WishlistController::class)->prefix('lista-deseos')->group(function () {
        Route::get('/', 'index')->name('usuario.deseos.indice');
        Route::post('/{producto}/agregar', 'add')->name('usuario.deseos.agregar');
        Route::post('/{id}/eliminar', 'remove')->name('usuario.deseos.eliminar');
        Route::post('/{producto}/mover', 'moveToCart')->name('usuario.deseos.mover');
    });

    // Review routes
    Route::controller(ReviewController::class)->prefix('productos')->group(function () {
        Route::post('/{producto}/reseñas', 'store')->name('usuario.resenas.guardar'); // Ruta para guardar reseñas
        Route::get('/{producto}/reseñas', 'index')->name('usuario.resenas.indice'); // Ruta para mostrar reseñas de un producto
    });
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/panel', [DashboardController::class, 'index'])->name('panel');
        Route::get('/panel/datos-grafica', [DashboardController::class, 'chartData'])->name('panel.datos-grafica');
        Route::resource('trabajadores', WorkerController::class)
            ->parameters(['trabajadores' => 'trabajador'])
            ->names('trabajadores')
            ->except(['show']);
        Route::resource('productos', ProductController::class)
            ->parameters(['productos' => 'producto'])
            ->names('productos');
        Route::post('/productos/{producto}/sincronizar-stripe', [ProductController::class, 'syncWithStripe'])->name('productos.sincronizar-stripe');
        Route::get('/productos/{producto}/info-stripe', [ProductController::class, 'stripeInfo'])->name('productos.info-stripe');
        Route::resource('categorias', CategoryController::class)
            ->parameters(['categorias' => 'categoria'])
            ->names('categorias')
            ->except(['show']);
        Route::resource('pedidos', OrderController::class)
            ->parameters(['pedidos' => 'pedido'])
            ->names('pedidos')
            ->only(['index', 'show', 'destroy']);
        Route::patch('/pedidos/{pedido}/estado', [OrderController::class, 'updateStatus'])->name('pedidos.actualizar-estado');
        Route::get('reportes/ventas', [ReportController::class, 'salesReport'])->name('reportes.ventas');

        // Monitoring routes
        Route::controller(MonitoringController::class)->prefix('monitoreo')->middleware('can:access-monitoring')->group(function () {
            Route::get('/panel', 'dashboard')->name('monitoreo.panel');
            Route::get('/estado-salud', 'healthStatus')->name('monitoreo.estado-salud');
            Route::get('/metricas', 'metrics')->name('monitoreo.metricas');
            Route::get('/alertas', 'alerts')->name('monitoreo.alertas');
            Route::post('/limpiar-alertas', 'clearAlerts')->name('monitoreo.limpiar-alertas');
            Route::post('/ejecutar-revision', 'runHealthCheck')->name('monitoreo.ejecutar-revision');
            Route::get('/registros', 'logs')->name('monitoreo.registros');
            Route::get('/historial-rendimiento', 'performanceHistory')->name('monitoreo.historial-rendimiento');
        });
    });

// Worker routes
Route::middleware(['auth', 'verified', 'role:trabajador'])
    ->prefix('trabajador')
    ->name('trabajador.')
    ->group(function () {
        Route::get('/panel', function () {
            return view('trabajador.dashboard');
        })->name('panel');
        Route::controller(OrderController::class)->prefix('pedidos')->group(function () {
            Route::get('/', 'workerIndex')->name('pedidos.indice');
            Route::get('/buscar', 'buscarPedidos')->name('pedidos.buscar');
            Route::get('/{pedido}', 'workerShow')->name('pedidos.ver');
            Route::patch('/{pedido}/aceptar', 'acceptOrder')->name('pedidos.aceptar');
            Route::patch('/{pedido}/rechazar', 'rejectOrder')->name('pedidos.rechazar');
        });
        Route::get('/reportes', [ReportController::class, 'workerSalesReport'])->name('reportes.ventas');
    });

// Address management routes (moved inside user middleware group)
Route::middleware(['auth', 'verified', 'role:usuario'])->group(function () {
    Route::controller(AddressController::class)->prefix('direcciones')->group(function () {
        Route::get('/', 'index')->name('usuario.direcciones.indice');
        Route::get('/crear', 'create')->name('usuario.direcciones.crear');
        Route::post('/', 'store')->name('usuario.direcciones.guardar');
        Route::get('/{direccion}/editar', 'edit')->name('usuario.direcciones.editar');
        Route::put('/{direccion}', 'update')->name('usuario.direcciones.actualizar');
        Route::delete('/{direccion}', 'destroy')->name('usuario.direcciones.eliminar');
        Route::patch('/{direccion}/predeterminada', 'setDefault')->name('usuario.direcciones.predeterminada');
    });

    // Dashboard del usuario
    Route::get('/usuario/panel', [UserOrderController::class, 'dashboard'])->name('usuario.panel');
});

Route::get('/api/address/{postalCode}', [ProfileController::class, 'getAddressByPostalCode'])
    ->middleware(['auth', 'throttle:30,1'])
    ->where('postalCode', '[0-9]+');

// Stripe webhook route (no authentication required)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

require __DIR__.'/auth.php';
