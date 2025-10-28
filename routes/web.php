<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\EventController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::get('/balance', [BalanceController::class, 'index']);
        Route::post('/event', [EventController::class, 'index']);
        Route::post('/reset', [BalanceController::class, 'reset']);
    });