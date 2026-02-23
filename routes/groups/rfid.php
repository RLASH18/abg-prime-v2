<?php

use App\Http\Controllers\Api\RfidController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.secret'])
    ->prefix('rfid')
    ->name('rfid.')
    ->group(function () {

        // Scan item by item_code
        Route::post('/scan', [RfidController::class, 'scan'])
            ->name('scan');
    });
