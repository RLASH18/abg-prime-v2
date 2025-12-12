<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Inventory\DamagedItemController;
use App\Http\Controllers\Admin\Inventory\ItemController;
use App\Http\Controllers\Admin\Inventory\SupplierController;
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

        require __DIR__ . '/settings.php';
    });
