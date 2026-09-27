<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PharmacySettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'pharmacy'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pharmacy/settings', [PharmacySettingsController::class, 'edit'])->name('pharmacy.settings.edit');
    Route::put('/pharmacy/settings', [PharmacySettingsController::class, 'update'])->name('pharmacy.settings.update');

    Route::get('/distributors/{distributor}/ledger', [DistributorController::class, 'show'])
        ->name('distributors.ledger');

    Route::get('/distributors/{distributor}/ledger-summary', [DistributorController::class, 'ledgerSummary'])
        ->name('distributors.ledger-summary');

    Route::resource('distributors', DistributorController::class)
        ->except(['show']);

    Route::resource('purchases', PurchaseController::class)
        ->except(['edit', 'update']);

    Route::resource('payments', PaymentController::class)
        ->except(['show', 'edit', 'update']);

    Route::resource('medicines', MedicineController::class)
        ->only(['index', 'create', 'store']);
});

Route::middleware(['auth', 'pharmacy'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
