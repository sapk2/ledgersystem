<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('pharmacies', PharmacyController::class)->except(['destroy']);

Route::patch('pharmacies/{pharmacy}/status', [PharmacyController::class, 'updateStatus'])
    ->name('pharmacies.status');

Route::put('pharmacies/{pharmacy}/password', [PharmacyController::class, 'updatePassword'])
    ->name('pharmacies.password');
