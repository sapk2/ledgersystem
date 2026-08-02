<?php

use App\Models\Distributor;
use App\Models\User;

it('renders the dashboard with aging buckets', function () {
    $user = User::factory()->create();
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

it('embeds distributor search data on the dashboard', function () {
    $user = User::factory()->create();
    $distributor = Distributor::factory()->create(['name' => 'Acme Pharma', 'city' => 'Kathmandu']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Search Distributor Ledger')
        ->assertSee('dashboardLedgerSearch')
        ->assertSee('Acme Pharma')
        ->assertSee('ledger-summary');
});

it('renders a ledger summary fragment for a distributor', function () {
    $user = User::factory()->create();
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
