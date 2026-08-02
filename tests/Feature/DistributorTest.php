<?php

use App\Models\Distributor;
use App\Models\User;

it('requires authentication', function () {
    $this->get(route('distributors.index'))->assertRedirect(route('login'));
});

it('shows the A–Z index grouped by first letter', function () {
    $user = User::factory()->create();

    Distributor::factory()->create(['name' => 'Parat Pharma']);
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);

    $this->actingAs($user)
        ->get(route('distributors.index'))
        ->assertSuccessful()
        ->assertSee('Parat Pharma')
        ->assertSee('Apex Medicine Center');
});

it('embeds all distributors for client-side letter filtering', function () {
    $user = User::factory()->create();

    Distributor::factory()->create(['name' => 'Parat Pharma']);
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);

    $this->actingAs($user)
        ->get(route('distributors.index', ['letter' => 'P']))
        ->assertSuccessful()
        ->assertSee('Parat Pharma')
        ->assertSee('Apex Medicine Center');
});

it('embeds distributors for live client-side search', function () {
    $user = User::factory()->create();

    Distributor::factory()->create(['name' => 'Parat Pharma']);
    Distributor::factory()->create(['name' => 'City Drug House']);

    $this->actingAs($user)
        ->get(route('distributors.index', ['q' => 'Parat']))
        ->assertSuccessful()
        ->assertSee('Parat Pharma')
        ->assertSee('City Drug House');
});

it('creates a distributor and redirects to the ledger', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('distributors.store'), [
            'name' => 'New Distributor',
            'city' => 'Kathmandu',
            'opening_balance' => 1250.50,
        ])
        ->assertRedirect();

    $distributor = Distributor::where('name', 'New Distributor')->firstOrFail();

    expect($distributor->opening_balance)->toBe('1250.50');

    $this->get(route('distributors.ledger', $distributor))
        ->assertSuccessful()
        ->assertSee('New Distributor')
        ->assertSee('1,250.50');
});

it('shows the ledger with entries and running balance', function () {
    $user = User::factory()->create();
    $distributor = Distributor::factory()->create(['opening_balance' => 1000]);

    $purchase = $distributor->purchases()->create([
        'bill_no' => 'BL-001',
        'bill_date' => '2026-01-15',
        'total_amount' => 500,
    ]);

    $purchase->ledgerEntry()->create([
        'distributor_id' => $distributor->id,
        'entry_date' => '2026-01-15',
        'voucher_no' => 'BL-001',
        'description' => 'Purchase Bill BL-001',
        'type' => 'PURCHASE',
        'dr_amount' => 500,
        'cr_amount' => 0,
    ]);

    $this->actingAs($user)
        ->get(route('distributors.ledger', $distributor))
        ->assertSuccessful()
        ->assertSee('Distributor Ledger')
        ->assertSee('BL-001')
        ->assertSee('To Balance b/d')
        ->assertSee('1,000.00')
        ->assertSee('1,500.00');
});

it('destroys a distributor with all history', function () {
    $user = User::factory()->create();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->delete(route('distributors.destroy', $distributor))
        ->assertRedirect(route('distributors.index'));

    expect(Distributor::find($distributor->id))->toBeNull();
});
