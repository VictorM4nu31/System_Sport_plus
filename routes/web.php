<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\User\AddressController;

use App\Http\Controllers\Admin\{
    WorkerController, ProductController, CategoryController,
    OrderController, ReportController
};
use App\Http\Controllers\User\{
    ProductController as UserProductController,
    CartController, OrderController as UserOrderController,
    UserOrderHistoryController, WishlistController, ReviewController
};
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('/productos/{id}', [ProductController::class, 'show'])->name('usuario.products.show');


    // Cart routes
    Route::controller(CartController::class)->group(function () {
        Route::get('/carrito', 'index')->name('usuario.cart.index');
        Route::post('/carrito/{id}/agregar', 'add')->name('usuario.cart.add');
        Route::post('/carrito/{id}/eliminar', 'remove')->name('usuario.cart.remove');
        Route::post('/carrito/procesar-pedido', 'processOrder')->name('usuario.cart.processOrder');
    });

    // Order routes
    Route::controller(UserOrderController::class)->group(function () {
        Route::get('/pedidos', 'index')->name('usuario.orders.index');
        Route::get('/pedidos/{id}', 'show')->name('usuario.orders.show');
    });

    // Order history routes
    Route::controller(UserOrderHistoryController::class)->prefix('historial-pedidos')->group(function () {
        Route::get('/', 'history')->name('usuario.orders.history');
        Route::get('/{id}', 'show')->name('usuario.orders.show');
    });

    // Wishlist routes
    Route::controller(WishlistController::class)->prefix('lista-deseos')->group(function () {
        Route::get('/', 'index')->name('usuario.wishlist.index');
        Route::post('/{id}/agregar', 'add')->name('usuario.wishlist.add');
        Route::post('/{id}/eliminar', 'remove')->name('usuario.wishlist.remove');
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
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        Route::resource('workers', WorkerController::class)->except(['show']);
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    });

// Worker routes
Route::middleware(['auth', 'role:trabajador'])
    ->prefix('trabajador')
    ->name('trabajador.')
    ->group(function () {
        Route::controller(OrderController::class)->prefix('pedidos')->group(function () {
            Route::get('/', 'workerIndex')->name('orders.index');
            Route::patch('/{id}/aceptar', 'acceptOrder')->name('orders.accept');
        });
        Route::get('/reportes', [ReportController::class, 'workerSalesReport'])->name('reports.sales');
    });


    // Ruta para la dirección del pedido
Route::middleware(['auth', 'role:usuario'])->group(function () {
    Route::get('/direccion', [OrderController::class, 'direccion'])->name('usuario.orders.direccion');
    Route::post('/direccion', [AddressController::class, 'store'])->name('usuario.orders.direccion');

});

Route::middleware(['auth', 'role:usuario'])->group(function () {
    // Dashboard del usuario
    Route::get('/usuario/dashboard', [App\Http\Controllers\User\OrderController::class, 'index'])->name('usuario.dashboard');
});



Route::get('/api/address/{postalCode}', [ProfileController::class, 'getAddressByPostalCode']);


require __DIR__ . '/auth.php';
