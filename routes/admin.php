<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\{
    AdminController,
    OrderController,
    BillingController,
    DeliveryController,
    ReportController,
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

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        // Inventory
        Route::prefix('inventory')->group(function () {
            Route::resources([
                'suppliers' => SupplierController::class,
                'items' => ItemController::class
            ]);

            Route::resource('damaged-items', DamagedItemController::class)
                ->only(['index', 'store', 'destroy']);
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
                Route::patch('/{id}/status', 'updateStatus')->name('updateStatus');
            });
        });

        // Billings
        Route::prefix('billings')->name('billings.')->group(function () {
            Route::controller(BillingController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
            });
        });

        // Deliveries
        Route::prefix('deliveries')->name('deliveries.')->group(function () {
            Route::controller(DeliveryController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
                Route::patch('/{id}/status', 'updateStatus')->name('updateStatus');
            });
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::controller(ReportController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sales', 'sales')->name('sales');
                Route::get('/inventory', 'inventory')->name('inventory');
                Route::get('/orders', 'orders')->name('orders');
                Route::get('/billing', 'billing')->name('billing');
                Route::get('/delivery', 'delivery')->name('delivery');
            });
        });

        require __DIR__ . '/settings.php';
    });
