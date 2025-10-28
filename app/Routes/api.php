<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/balance', [BalanceController::class, 'index']);
Route::post('/reset', [BalanceController::class, 'reset']);
Route::post('/event', [EventController::class, 'index']);