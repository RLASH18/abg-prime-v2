<?php

use App\Enums\UserRole;
use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . UserRole::Customer->value])
    ->name('customer.')
    ->group(function () {

        Route::get('homepage', [CustomerController::class, 'homepage'])->name('homepage');

        require __DIR__ . '/settings.php';
    });
