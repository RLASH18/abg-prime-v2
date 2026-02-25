<?php

use App\Http\Controllers\Api\RfidController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.secret'])
    ->prefix('rfid')
    ->name('rfid.')
    ->group(function () {

        // Look up item info by item_code (read-only, no stock change)
        Route::get('/item/{code}', [RfidController::class, 'lookup'])
            ->name('lookup');

        // Scan item by item_code (adjusts stock)
        Route::post('/scan', [RfidController::class, 'scan'])
            ->name('scan');
    });
