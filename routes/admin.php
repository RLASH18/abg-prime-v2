<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . UserRole::Admin->value])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('inventory', InventoryController::class);
        Route::resource('supplier', SupplierController::class);

        require __DIR__ . '/settings.php';
    });
