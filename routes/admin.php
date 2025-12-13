<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\Inventory\DamagedItemController;
use App\Http\Controllers\Admin\Inventory\ItemController;
use App\Http\Controllers\Admin\Inventory\SupplierController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . UserRole::Admin->value])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::prefix('inventory')->group(function () {
            Route::resource('suppliers', SupplierController::class);
            Route::resource('items', ItemController::class);
            Route::resource('damaged-items', DamagedItemController::class)->only(['index', 'store', 'destroy']);
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        });

        Route::prefix('billings')->name('billings.')->group(function () {
            Route::get('/', [BillingController::class, 'index'])->name('index');
            Route::get('/{id}', [BillingController::class, 'show'])->name('show');
        });

        require __DIR__ . '/settings.php';
    });
