<?php

use App\Models\Distributor;

it('requires authentication', function () {
    $this->get(route('distributors.index'))->assertRedirect(route('login'));
});

it('shows the A–Z index grouped by first letter', function () {
    $user = pharmacyUser();

    Distributor::factory()->create(['name' => 'Parat Pharma']);
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);

    $this->actingAs($user)
        ->get(route('distributors.index'))
        ->assertSuccessful()
        ->assertSee('Parat Pharma')
        ->assertSee('Apex Medicine Center');
});

it('embeds all distributors for client-side letter filtering', function () {
    $user = pharmacyUser();

    Distributor::factory()->create(['name' => 'Parat Pharma']);
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);

    $this->actingAs($user)
        ->get(route('distributors.index', ['letter' => 'P']))
        ->assertSuccessful()
        ->assertSee('Parat Pharma')
        ->assertSee('Apex Medicine Center');
});

it('embeds distributors for live client-side search', function () {
    $user = pharmacyUser();

    Distributor::factory()->create(['name' => 'Parat Pharma']);
    Distributor::factory()->create(['name' => 'City Drug House']);

    $this->actingAs($user)
        ->get(route('distributors.index', ['q' => 'Parat']))
        ->assertSuccessful()
        ->assertSee('Parat Pharma')
        ->assertSee('City Drug House');
});

it('creates a distributor and redirects to the ledger', function () {
    $user = pharmacyUser();

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
    $user = pharmacyUser();
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

it('heads the printed ledger with the pharmacy and distributor details', function () {
    $user = pharmacyUser();
    $user->pharmacy->update([
        'name' => 'Prapti Pharma',
        'address' => 'Koteshwor, Kathmandu',
        'phone' => '01-4334455',
    ]);

    $distributor = Distributor::factory()->create([
        'name' => 'Life Line Medicine Distributors',
        'city' => 'Chitwan',
        'phone' => '9812345678',
        'pan_no' => '600112233',
    ]);

    $distributor->purchases()->create([
        'bill_no' => 'BL-300',
        'bill_date' => '2026-01-15',
        'total_amount' => 500,
    ])->ledgerEntry()->create([
        'distributor_id' => $distributor->id,
        'entry_date' => '2026-01-15',
        'voucher_no' => 'BL-300',
        'description' => 'Purchase Bill BL-300',
        'type' => 'PURCHASE',
        'dr_amount' => 500,
        'cr_amount' => 0,
    ]);

    $this->actingAs($user)
        ->get(route('distributors.ledger', $distributor))
        ->assertSuccessful()
        ->assertSeeInOrder(['Prapti Pharma', 'Koteshwor, Kathmandu', '01-4334455', 'Distributor', 'Life Line Medicine Distributors'])
        ->assertSee('Ledger Period:')
        ->assertSee('Opening Balance:')
        ->assertSee('Prepared By')
        ->assertSee('Authorised Signatory')
        ->assertSee('Closing Balance c/d');
});

it('destroys a distributor with all history', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->delete(route('distributors.destroy', $distributor))
        ->assertRedirect(route('distributors.index'));

    expect(Distributor::find($distributor->id))->toBeNull();
});
