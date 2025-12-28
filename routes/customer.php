<?php

use App\Enums\UserRole;
use App\Http\Controllers\Customer\HomepageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:' . UserRole::Customer->value])
    ->name('customer.')
    ->group(function () {

        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::controller(HomepageController::class)->group(function () {
                Route::get('/', 'homepage')->name('index');

                Route::get('/items/{item}', 'show')->name('items.show');
            });
        });

        require __DIR__ . '/settings.php';
    });
