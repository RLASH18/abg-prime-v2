<?php

use App\Enums\UserRole;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HomepageController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PaymentCallbackController;
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

        // Resource routes
        Route::resource('carts', CartController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Custom cart routes
        Route::controller(CartController::class)->group(function () {
            Route::prefix('carts')->name('carts.')->group(function () {
                Route::delete('/clear', 'clear')->name('clear');

                Route::patch('/{id}/toggle-selection', 'toggleSelection')->name('toggle-selection');

                Route::post('/toggle-all-selection', 'toggleAllSelection')->name('toggle-all-selection');
            });
        });

        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::controller(CheckoutController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/process', 'process')->name('process');
            });
        });

        Route::get('/checkout/success', [PaymentCallbackController::class, 'success'])
            ->name('checkout.success');

        Route::get('/checkout/failed', [PaymentCallbackController::class, 'failed'])
            ->name('checkout.failed');

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
            });
        });

        require __DIR__ . '/settings.php';
    });
