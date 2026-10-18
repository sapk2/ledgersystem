<?php

use App\Http\Controllers\SuperAdmin\AuditLogController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\PharmacyController;
use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Http\Controllers\SuperAdmin\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('pharmacies', PharmacyController::class);

Route::patch('pharmacies/{pharmacy}/status', [PharmacyController::class, 'updateStatus'])
    ->name('pharmacies.status');

Route::put('pharmacies/{pharmacy}/password', [PharmacyController::class, 'updatePassword'])
    ->name('pharmacies.password');

Route::get('users', [UsersController::class, 'index'])->name('users.index');
Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update');
Route::patch('users/{user}/status', [UsersController::class, 'updateStatus'])->name('users.status');
Route::put('users/{user}/password', [UsersController::class, 'updatePassword'])->name('users.password');

Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
