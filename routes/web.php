<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController; //nuevo
use App\Http\Controllers\Admin\WorkerController; //nuevo
use App\Http\Controllers\Admin\ProductController; //nuevo   

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Restringir el Registro Solo a Usuarios
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');
//fin del cambio
//Definir las Rutas para el Administrador
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/admin/workers/create', [WorkerController::class, 'create'])->name('admin.workers.create');
    Route::post('/admin/workers', [WorkerController::class, 'store'])->name('admin.workers.store');
    Route::get('/admin/workers', [WorkerController::class, 'index'])->name('admin.workers.index');
    Route::get('/admin/workers/{id}/edit', [WorkerController::class, 'edit'])->name('admin.workers.edit');
    Route::patch('/admin/workers/{id}', [WorkerController::class, 'update'])->name('admin.workers.update');
    Route::delete('/admin/workers/{id}', [WorkerController::class, 'destroy'])->name('admin.workers.destroy');
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::patch('/admin/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    // Otras rutas de administrador...
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
//fin del cambio

require __DIR__ . '/auth.php';
