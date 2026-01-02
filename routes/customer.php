<?php

use App\Enums\UserRole;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HomepageController;
use App\Http\Controllers\Customer\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . UserRole::Customer->value])
    ->name('customer.')
    ->group(function () {

        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::controller(HomepageController::class)->group(function () {
                Route::get('/', 'index')->name('index');

                Route::get('/products/{product}', 'show')->name('products.show');
            });
        });

        Route::resource('carts', CartController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::delete('/carts-clear', [CartController::class, 'clear'])->name('carts.clear');

        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::controller(CheckoutController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/process', 'process')->name('process');
            });
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
            });
        });

        require __DIR__ . '/settings.php';
    });
