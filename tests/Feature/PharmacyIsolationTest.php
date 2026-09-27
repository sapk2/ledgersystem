<?php

use App\Models\Distributor;
use App\Models\DistributorLedger;
use App\Models\Medicine;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\User;

it('does not show another pharmacy distributors', function () {
    $user = pharmacyUser();

    Distributor::factory()->create(['name' => 'Mine Pharma']);
    Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => otherPharmacy()->id,
        'name' => 'Their Pharma',
    ]);

    $this->get(route('distributors.index'))
        ->assertSuccessful()
        ->assertSee('Mine Pharma')
        ->assertDontSee('Their Pharma');

    expect(Distributor::count())->toBe(1);
});

it('cannot open the ledger of another pharmacy distributor', function () {
    pharmacyUser();

    $foreign = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => otherPharmacy()->id,
        'name' => 'Their Pharma',
    ]);

    $this->get(route('distributors.ledger', $foreign))->assertNotFound();
});

it('cannot update or delete another pharmacy distributor', function () {
    pharmacyUser();

    $foreign = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => otherPharmacy()->id,
        'name' => 'Their Pharma',
    ]);

    $this->delete(route('distributors.destroy', $foreign))->assertNotFound();
    $this->put(route('distributors.update', $foreign), [
        'name' => 'Hijacked',
    ])->assertNotFound();

    expect($foreign->refresh()->name)->toBe('Their Pharma');
});

it('rejects a purchase bill for another pharmacy distributor', function () {
    pharmacyUser();

    $foreign = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => otherPharmacy()->id,
        'name' => 'Their Pharma',
    ]);

    $this->post(route('purchases.store'), [
        'distributor_id' => $foreign->id,
        'bill_no' => 'HACK-001',
        'bill_date' => '2026-03-10',
        'items' => [
            ['medicine_name' => 'Paracetamol 500mg', 'quantity' => 1, 'rate' => 10],
        ],
    ])->assertSessionHasErrors('distributor_id');

    expect(Purchase::withoutGlobalScopes()->where('bill_no', 'HACK-001')->exists())->toBeFalse();
});

it('rejects a payment for another pharmacy distributor', function () {
    pharmacyUser();

    $foreign = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => otherPharmacy()->id,
        'name' => 'Their Pharma',
    ]);

    $this->post(route('payments.store'), [
        'distributor_id' => $foreign->id,
        'amount' => 500,
        'payment_date' => '2026-03-10',
    ])->assertSessionHasErrors('distributor_id');

    expect(Payment::withoutGlobalScopes()->count())->toBe(0);
});

it('allows the same bill number in different pharmacies', function () {
    $user = pharmacyUser();
    Distributor::factory()->create();

    $this->post(route('purchases.store'), [
        'distributor_id' => Distributor::first()->id,
        'bill_no' => 'SHARED-001',
        'bill_date' => '2026-03-10',
        'items' => [
            ['medicine_name' => 'Paracetamol 500mg', 'quantity' => 1, 'rate' => 10],
        ],
    ])->assertSessionHasNoErrors();

    $other = otherPharmacy();
    $otherUser = User::factory()->create(['pharmacy_id' => $other->id]);
    $otherDistributor = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => $other->id,
        'name' => 'Other Distributor',
    ]);

    $this->actingAs($otherUser)
        ->post(route('purchases.store'), [
            'distributor_id' => $otherDistributor->id,
            'bill_no' => 'SHARED-001',
            'bill_date' => '2026-03-10',
            'items' => [
                ['medicine_name' => 'Paracetamol 500mg', 'quantity' => 1, 'rate' => 10],
            ],
        ])->assertSessionHasNoErrors();

    expect(Purchase::withoutGlobalScopes()->where('bill_no', 'SHARED-001')->count())->toBe(2);
});

it('keeps medicines separate per pharmacy', function () {
    $user = pharmacyUser();
    $other = otherPharmacy();

    Medicine::factory()->create([
        'pharmacy_id' => $user->pharmacy_id,
        'name' => 'Paracetamol 500mg',
    ]);

    Medicine::factory()->create([
        'pharmacy_id' => $other->id,
        'name' => 'Paracetamol 500mg',
    ]);

    // Both rows exist, but the signed-in pharmacy only sees its own catalogue.
    expect(Medicine::withoutGlobalScopes()->count())->toBe(2)
        ->and(Medicine::count())->toBe(1);

    $this->actingAs(User::factory()->create(['pharmacy_id' => $other->id]))
        ->get(route('medicines.index'))
        ->assertSuccessful();
});

it('keeps the dashboard totals limited to the own pharmacy', function () {
    $user = pharmacyUser();

    $mine = Distributor::factory()->create(['opening_balance' => 500]);
    $mine->purchases()->create([
        'bill_no' => 'MINE-1',
        'bill_date' => '2026-01-10',
        'total_amount' => 1000,
    ]);

    $other = otherPharmacy();
    $theirDistributor = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => $other->id,
        'name' => 'Their Pharma',
        'opening_balance' => 9999,
    ]);
    $theirDistributor->purchases()->create([
        'pharmacy_id' => $other->id,
        'bill_no' => 'THEIRS-1',
        'bill_date' => '2026-01-10',
        'total_amount' => 7777,
    ]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('MINE-1')
        ->assertDontSee('THEIRS-1');
});

it('keeps ledger entries out of reach of another pharmacy', function () {
    $user = pharmacyUser();

    $theirDistributor = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => otherPharmacy()->id,
        'name' => 'Their Pharma',
    ]);

    DistributorLedger::withoutGlobalScopes()->create([
        'pharmacy_id' => $theirDistributor->pharmacy_id,
        'distributor_id' => $theirDistributor->id,
        'entry_date' => '2026-01-01',
        'voucher_no' => 'SECRET-1',
        'description' => 'Secret entry',
        'type' => 'PURCHASE',
        'dr_amount' => 500,
        'cr_amount' => 0,
    ]);

    expect(DistributorLedger::count())->toBe(0);
    expect(DistributorLedger::withoutGlobalScopes()->count())->toBe(1);
});

it('cannot view a purchase invoice from another pharmacy', function () {
    pharmacyUser();

    $other = otherPharmacy();
    $theirDistributor = Distributor::withoutGlobalScopes()->create([
        'pharmacy_id' => $other->id,
        'name' => 'Their Pharma',
    ]);

    $theirPurchase = $theirDistributor->purchases()->create([
        'pharmacy_id' => $other->id,
        'bill_no' => 'THEIRS-9',
        'bill_date' => '2026-01-10',
        'total_amount' => 100,
    ]);

    $this->get(route('purchases.show', $theirPurchase))->assertNotFound();
});
