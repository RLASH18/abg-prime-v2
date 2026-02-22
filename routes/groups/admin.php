<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\Inventory\DamagedItemController;
use App\Http\Controllers\Admin\Inventory\ItemController;
use App\Http\Controllers\Admin\Inventory\ItemForecastController;
use App\Http\Controllers\Admin\Inventory\SupplierController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . UserRole::Admin->value])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->name('dashboard');

        // Inventory
        Route::prefix('inventory')->group(function () {
            Route::resources([
                'suppliers' => SupplierController::class,
                'items' => ItemController::class
            ]);

            Route::resource('damaged-items', DamagedItemController::class)
                ->only(['index', 'store', 'destroy']);

            // AI Forecast for a specific item
            Route::post('/items/{item}/forecast', [ItemForecastController::class, 'generate'])
                ->name('items.forecast');
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

        // Chats
        Route::prefix('chats')->name('chats.')->group(function () {
            Route::controller(ChatController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/unread/count', 'unreadCount')->name('unread.count');
                Route::get('/{conversation}', 'show')->name('show');
                Route::post('/{conversation}', 'store')->name('store');
                Route::post('/{conversation}/read', 'markAsRead')->name('read');
            });
        });

        require __DIR__ . '/../settings.php';
    });
