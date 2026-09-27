<?php

use App\Models\Distributor;

it('shows the pharmacy settings page', function () {
    $user = pharmacyUser();
    $user->pharmacy->update(['name' => 'Green Cross Pharmacy']);

    $this->get(route('pharmacy.settings.edit'))
        ->assertSuccessful()
        ->assertSee('Pharmacy Details')
        ->assertSee('Green Cross Pharmacy');
});

it('a pharmacy user can update their own details', function () {
    $user = pharmacyUser();
    $pharmacy = $user->pharmacy;

    $this->put(route('pharmacy.settings.update'), [
        'name' => 'Updated Pharmacy',
        'owner_name' => 'Updated Owner',
        'phone' => '9800000000',
        'address' => 'New Address',
        'city' => 'Butwal',
        'district' => 'Rupandehi',
        'province' => 'Lumbini',
        'country' => 'Nepal',
    ])->assertRedirect();

    expect($pharmacy->refresh()->name)->toBe('Updated Pharmacy')
        ->and($pharmacy->owner_name)->toBe('Updated Owner')
        ->and($pharmacy->city)->toBe('Butwal');
});

it('the pharmacy login email is managed by the super admin only', function () {
    $user = pharmacyUser();
    $originalEmail = $user->pharmacy->email;

    $this->put(route('pharmacy.settings.update'), [
        'name' => 'Updated Pharmacy',
        'email' => 'hijack@example.com',
    ])->assertSessionHasNoErrors();

    expect($user->pharmacy->refresh()->email)->toBe($originalEmail)
        ->and($user->pharmacy->email)->not->toBe('hijack@example.com')
        ->and($user->refresh()->email)->not->toBe('hijack@example.com');
});

it('printed documents use the logged in pharmacy details', function () {
    $user = pharmacyUser();
    $user->pharmacy->update([
        'name' => 'Sunrise Pharmacy',
        'address' => 'Baglung Road',
        'city' => 'Pokhara',
        'phone' => '9841122334',
    ]);

    $distributor = Distributor::factory()->create(['name' => 'Sunrise Distributor']);

    $this->get(route('distributors.ledger', $distributor))
        ->assertSuccessful()
        ->assertSeeInOrder(['Sunrise Pharmacy', 'Baglung Road', 'Pokhara', '9841122334']);
});

it('printed purchase invoices use the logged in pharmacy details', function () {
    $user = pharmacyUser();
    $user->pharmacy->update([
        'name' => 'Sunrise Pharmacy',
        'address' => 'Baglung Road',
        'city' => 'Pokhara',
    ]);

    $distributor = Distributor::factory()->create();
    $purchase = $distributor->purchases()->create([
        'bill_no' => 'SR-1',
        'bill_date' => '2026-02-02',
        'total_amount' => 100,
    ]);

    $this->get(route('purchases.show', $purchase))
        ->assertSuccessful()
        ->assertSee('Sunrise Pharmacy')
        ->assertSee('Pokhara');
});
