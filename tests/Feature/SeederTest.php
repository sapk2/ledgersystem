<?php

use App\Models\Distributor;
use App\Models\Medicine;
use App\Models\Payment;
use App\Models\Pharmacy;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('seeder creates isolated, usable, per-pharmacy data', function () {
    $this->seed(DatabaseSeeder::class);

    $pharmacyUsers = User::where('role', User::ROLE_USER)->get();
    $admins = User::where('role', User::ROLE_ADMIN)->get();

    expect($admins)->toHaveCount(1)
        ->and($admins->first()->pharmacy_id)->toBeNull()
        ->and($admins->first()->email_verified_at)->not->toBeNull()
        ->and($pharmacyUsers)->toHaveCount(2)
        ->and(User::whereNull('email_verified_at')->count())->toBe(0)
        ->and(Distributor::withoutGlobalScopes()->whereNull('pharmacy_id')->count())->toBe(0)
        ->and(Medicine::withoutGlobalScopes()->whereNull('pharmacy_id')->count())->toBe(0)
        ->and(Purchase::withoutGlobalScopes()->whereNull('pharmacy_id')->count())->toBe(0)
        ->and(Payment::withoutGlobalScopes()->whereNull('pharmacy_id')->count())->toBe(0);

    foreach ($pharmacyUsers as $user) {
        expect($user->pharmacy_id)->not->toBeNull()
            ->and($user->pharmacy)->not->toBeNull();
    }

    // Each pharmacy only sees its own catalogue and distributors.
    $first = $pharmacyUsers->first();
    $this->actingAs($first)->get(route('distributors.index'))->assertSuccessful();

    $firstDistributorIds = Distributor::withoutGlobalScopes()->where('pharmacy_id', $first->pharmacy_id)->pluck('id');
    expect($firstDistributorIds)->not->toBeEmpty();
});
