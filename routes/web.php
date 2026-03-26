<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth callbacks
Route::prefix('auth')->group(function () {
    Route::get('/callback', [\App\Modules\Identity\Http\Controllers\AuthCallbackController::class, 'handle'])->name('auth.callback');
    Route::post('/logout', [\App\Modules\Identity\Http\Controllers\AuthCallbackController::class, 'logout'])->name('auth.logout');
});
