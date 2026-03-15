<?php

use App\Http\Controllers\Api\PosDeviceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.secret'])
    ->prefix('pos')
    ->name('pos.')
    ->group(function () {

        // Look up item info by item_code (read-only, no stock change)
        Route::get('/item/{code}', [PosDeviceController::class, 'lookup'])
            ->name('lookup');

        // Scan item by item_code (adjusts stock)
        Route::post('/scan', [PosDeviceController::class, 'scan'])
            ->name('scan');
    });
