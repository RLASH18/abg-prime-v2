<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\Auth\OAuthController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::controller(OAuthController::class)->group(function () {
    Route::get('oauth/redirect/{provider}', 'redirect')->name('oauth.redirect');
    Route::get('oauth/{provider}/callback', 'callback')->name('oauth.callback');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/customer.php';
