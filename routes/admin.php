<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\{
    AdminController,
    OrderController,
    BillingController,
    DeliveryController,
};
use App\Http\Controllers\Admin\Inventory\{
    SupplierController,
    ItemController,
    DamagedItemController
};
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . UserRole::Admin->value])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::prefix('inventory')->group(function () {
            Route::resources([
                'suppliers' => SupplierController::class,
                'items' => ItemController::class
            ]);

            Route::resource('damaged-items', DamagedItemController::class)
                ->only(['index', 'store', 'destroy']);
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{id}', 'show')->name('show');
                Route::patch('{id}/status', 'updateStatus')->name('updateStatus');
            });
        });

        Route::prefix('billings')->name('billings.')->group(function () {
            Route::controller(BillingController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{id}', 'show')->name('show');
            });
        });

        Route::prefix('deliveries')->name('deliveries.')->group(function () {
            Route::controller(DeliveryController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{id}', 'show')->name('show');
                Route::patch('{id}/status', 'updateStatus')->name('updateStatus');
            });
        });

        require __DIR__ . '/settings.php';
    });
