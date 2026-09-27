<?php

use App\Models\Distributor;

it('renders the dashboard with aging buckets', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $purchase = $distributor->purchases()->create([
        'bill_no' => 'BL-001',
        'bill_date' => '2026-01-15',
        'total_amount' => 1000,
    ]);

    $purchase->ledgerEntry()->create([
        'distributor_id' => $distributor->id,
        'entry_date' => '2026-01-15',
        'voucher_no' => 'BL-001',
        'description' => 'Purchase Bill BL-001',
        'type' => 'PURCHASE',
        'dr_amount' => 1000,
        'cr_amount' => 0,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Aging Analysis')
        ->assertSee('Outstanding (Due)');
});

it('offers the billing page directly from the dashboard', function () {
    pharmacyUser();

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('New Bill')
        ->assertSee('All Bills')
        ->assertSee('Record Payment')
        ->assertSee(route('purchases.create'), escape: false)
        ->assertSee(route('purchases.index'), escape: false);
});

it('shows a first-bill call to action when nothing has been billed', function () {
    pharmacyUser();

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Create your first bill');
});

it('shows the pharmacy name and today in the dashboard header', function () {
    $user = pharmacyUser();
    $user->pharmacy->update(['name' => 'Sunrise Pharmacy']);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Sunrise Pharmacy')
        ->assertSee(now()->format('l, j F Y'));
});

it('embeds distributor search data on the dashboard', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create([
        'name' => 'Acme Pharma',
        'city' => 'Kathmandu',
        'phone' => '9811234567',
        'pan_no' => '601234567',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Search Distributor Ledger')
        ->assertSee('dashboardLedgerSearch')
        ->assertSee('Acme Pharma')
        ->assertSee('601234567')
        ->assertSee('ledger-summary');
});

it('renders a ledger summary fragment for a distributor', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $purchase = $distributor->purchases()->create([
        'bill_no' => 'BL-001',
        'bill_date' => now()->toDateString(),
        'total_amount' => 1000,
    ]);

    $purchase->ledgerEntry()->create([
        'distributor_id' => $distributor->id,
        'entry_date' => now()->toDateString(),
        'voucher_no' => 'BL-001',
        'description' => 'Purchase Bill BL-001',
        'type' => 'PURCHASE',
        'dr_amount' => 1000,
        'cr_amount' => 0,
    ]);

    $this->actingAs($user)
        ->get(route('distributors.ledger-summary', $distributor))
        ->assertSuccessful()
        ->assertSee($distributor->name)
        ->assertSee('Outstanding Balance')
        ->assertSee('Ageing Due')
        ->assertSee('BL-001');
});
