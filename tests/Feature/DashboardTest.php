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

it('renders the outstanding dues pie chart from real balances', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Alpha Pharma', 'opening_balance' => 600]);
    Distributor::factory()->create(['name' => 'Beta Traders', 'opening_balance' => 400]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Outstanding Dues by Distributor')
        ->assertSee('Pie chart of outstanding dues by distributor')
        ->assertSee('Alpha Pharma')
        ->assertSee('Beta Traders')
        ->assertSee('60.0%')
        ->assertSee('40.0%');
});

it('groups the smaller distributors into an others slice', function () {
    pharmacyUser();

    foreach (range(1, 9) as $amount) {
        Distributor::factory()->create([
            'name' => 'Distributor '.$amount,
            'opening_balance' => $amount * 100,
        ]);
    }

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Other distributors')
        ->assertSee('Pie chart of outstanding dues by distributor');
});

it('shows a settled empty state on the dues chart when nothing is owed', function () {
    pharmacyUser();
    Distributor::factory()->create(['opening_balance' => 0]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('No outstanding dues.')
        ->assertDontSee('Pie chart of outstanding dues by distributor');
});

it('renders a full circle when a single distributor owes everything', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Sole Supplier', 'opening_balance' => 500]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Pie chart of outstanding dues by distributor')
        ->assertSee('Sole Supplier')
        ->assertSee('100.0%');
});

it('only plots distributors that actually owe money', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Advance Payer', 'opening_balance' => -150]);
    Distributor::factory()->create(['name' => 'Owes Money', 'opening_balance' => 250]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Pie chart of outstanding dues by distributor')
        ->assertSee('Owes Money')
        ->assertSee('100.0%')
        ->assertDontSee('No outstanding dues.');
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
